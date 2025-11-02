<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Conversation;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class ChatController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $userId = $user->id;

        $conversations = Conversation::where(function($q) use ($userId) {
                $q->where('user1_id', $userId)
                ->orWhere('user2_id', $userId);
            })
            ->where(function($q) use ($userId) {
                $q->whereNull('deleted_by')
                ->orWhereJsonDoesntContain('deleted_by', $userId);
            })
            ->with(['user1', 'user2', 'messages' => fn($q) => $q->latest()->limit(1)])
            ->get();

        return view('chat.index', compact('conversations'));
    }

    public function create()
    {
        $userId = Auth::id();

        // Get IDs of users that have active conversations with current user
        $excludedUserIds = Conversation::where(function($q) use ($userId) {
                $q->where('user1_id', $userId)
                ->orWhere('user2_id', $userId);
            })
            ->where(function($q) use ($userId) {
                $q->whereNull('deleted_by')
                ->orWhereJsonDoesntContain('deleted_by', $userId);
            })
            ->get()
            ->map(function($conversation) use ($userId) {
                return $conversation->user1_id == $userId
                    ? $conversation->user2_id
                    : $conversation->user1_id;
            })
            ->unique()
            ->toArray();

        // Exclude system admin users from selection
        $users = User::where('id', '!=', $userId)
            ->whereNotIn('id', $excludedUserIds)
            ->where('role', '!=', 'admin')
            ->get();

        return view('chat.create', compact('users'));
    }

    public function show(Conversation $conversation)
    {
        $this->authorize('view', $conversation);

         $messages = $conversation->messages()
        ->with('user')
        ->orderBy('created_at', 'asc')
        ->get();

        return view('chat.show', compact('conversation', 'messages'));
    }

    public function store(Request $request, Conversation $conversation)
    {
        $this->authorize('view', $conversation);

        $hasAttachment = $request->hasFile('attachment');
        $body = $request->input('body');

        // Custom validation
        if (!$hasAttachment && (is_null($body) || trim($body) === '')) {
            return response()->json([
                'error' => 'Message body is required unless you are sending an attachment.'
            ], 422);
        }

        try {
            $request->validate([
                'body' => 'nullable|string|max:1000',
                'attachment' => 'nullable|file|max:10240', // Max 10MB
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'errors' => $e->errors(),
            ], 422);
        }

        $data = [
            'user_id' => Auth::id(),
            'body' => $request->body,
        ];

        if ($request->hasFile('attachment')) {
            $path = $request->file('attachment')->store('attachments', 'public');
            $data['attachment'] = $path;
        }

        // Remove recipient from deleted_by if present
        $senderId = Auth::id();
        $recipientId = ($conversation->user1_id == $senderId) ? $conversation->user2_id : $conversation->user1_id;
        $deletedBy = $conversation->deleted_by ?? [];
        if (is_string($deletedBy)) {
            $deletedBy = json_decode($deletedBy, true) ?? [];
        }
        if (in_array($recipientId, $deletedBy)) {
            $deletedBy = array_diff($deletedBy, [$recipientId]);
            $conversation->deleted_by = array_values($deletedBy);
            $conversation->save();
        }

        // Only create one message
        $message = $conversation->messages()->create($data);

        return response()->json([
            'id' => $message->id,
            'body' => $message->body,
            'attachment' => $message->attachment,
            'user_id' => $message->user_id,
            'created_at' => $message->created_at->diffForHumans(),
        ], 201);
    }

    public function poll(Conversation $conversation)
    {
        $this->authorize('view', $conversation);
        $lastMessageId = request()->input('last_message_id', 0);

        $messages = $conversation->messages()
            ->where('id', '>', $lastMessageId)
            ->with('user')
            ->get()
            ->map(function ($message) {
                return [
                    'id' => $message->id,
                    'body' => $message->body,
                    'attachment' => $message->attachment,
                    'user_id' => $message->user_id,
                    'created_at' => $message->created_at->diffForHumans(),
                ];
            });

        return response()->json($messages);
    }

    public function storeConversation(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        $user1 = Auth::id();
        $user2 = $request->user_id;

        $conversation = Conversation::withTrashed()
            ->where(function($q) use ($user1, $user2) {
                $q->where('user1_id', $user1)->where('user2_id', $user2);
            })->orWhere(function($q) use ($user1, $user2) {
                $q->where('user1_id', $user2)->where('user2_id', $user1);
            })->first();

        if ($conversation) {
            // Restore if soft-deleted
            if ($conversation->trashed()) {
                $conversation->restore();
            }
            // Remove both users from deleted_by
            $deletedBy = $conversation->deleted_by ?? [];
            if (is_string($deletedBy)) {
                $deletedBy = json_decode($deletedBy, true) ?? [];
            }
            $wasDeletedForBoth = in_array($user1, $deletedBy) && in_array($user2, $deletedBy);

            $deletedBy = array_diff($deletedBy, [$user1, $user2]);
            $conversation->deleted_by = array_values($deletedBy);
            $conversation->save();

            // If both users had deleted, clear messages
            if ($wasDeletedForBoth) {
                $conversation->messages()->delete();
            }
        } else {
            // Create new conversation
            $conversation = Conversation::create([
                'user1_id' => $user1,
                'user2_id' => $user2,
                'deleted_by' => []
            ]);
        }

        return redirect()->route('chat.show', $conversation);
    }

   public function destroy(Conversation $conversation)
    {
        $this->authorize('delete', $conversation);

        // Ensure we always get an array
        $deletedBy = $conversation->deleted_by ?? [];
        if (is_string($deletedBy)) {
            $deletedBy = json_decode($deletedBy, true) ?? [];
        }

        $userId = Auth::id();

        if (!in_array($userId, $deletedBy)) {
            $deletedBy[] = $userId;
            $conversation->deleted_by = $deletedBy; // Array will be auto-cast to JSON
            $conversation->save();
        }

        return redirect()->route('chat.index')->with('success', 'Conversation removed from your view.');
    }
}
