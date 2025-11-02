<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AIChatController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Request $request)
    {
        $request->validate([
            'message' => 'nullable|string',
            'image' => 'nullable|image|max:5120', // 5MB
            'pet_id' => 'nullable|exists:pets,id'
        ]);

        $imageUrl = null;
        if ($request->hasFile('image')) {
            // ensure storage link exists (run php artisan storage:link)
            $path = $request->file('image')->store('ai_images', 'public');
            $imageUrl = Storage::disk('public')->url($path);
        }

        $user = Auth::user();
        $userText = $request->input('message', '');

        $petInfo = '';
        if ($request->filled('pet_id')) {
            $pet = \App\Models\Pet::find($request->input('pet_id'));
            if ($pet) {
                $petInfo = "Pet: {$pet->name}, species: {$pet->species}, age: {$pet->age}.\n";
            }
        }

        // Build a short prompt. Keep it concise to control tokens/cost.
        $prompt = "You are a veterinary assistant. Provide concise first-aid suggestions with safety disclaimers. Do not provide definitive diagnoses. Recommend seeing a veterinarian when necessary.\n\n";
        $prompt .= $petInfo;
        if ($imageUrl) {
            // NOTE: If the model cannot analyze images, including the URL lets humans check it.
            // For image-capable models you might mention "analyze the image at: <url>"
            $prompt .= "Image URL: {$imageUrl}\n";
        }
        $prompt .= "Owner message: {$userText}\n\nPlease respond with short steps, possible causes, and urgency level (emergency/see soon/routine).";

        $apiKey = config('services.openai.key') ?? env('OPENAI_API_KEY');
        $model = config('services.openai.model') ?? env('OPENAI_CHAT_MODEL', 'gpt-4o-mini');

        try {
            $response = Http::withToken($apiKey)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => $model,
                    'messages' => [
                        ['role' => 'system', 'content' => "You are a friendly veterinary assistant. Keep answers concise and structured. Return three short sections: 1) Quick first-aid steps (bulleted), 2) Possible causes (brief), 3) Urgency (Emergency / See soon / Routine). Use a gentle tone and add appropriate emojis (e.g. ⚠️ 🩺 🐶 🐱 ✅) to make the reply clearer. Always include a short safety disclaimer advising to see a veterinarian when needed."],
                        ['role' => 'user', 'content' => $prompt],
                    ],
                    'max_tokens' => 500,    // increased to allow longer, richer replies (cost ↑)
                    'temperature' => 0.25,
                    'top_p' => 1,
                    'frequency_penalty' => 0.0,
                    'presence_penalty' => 0.0,
                ]);

            if ($response->successful()) {
                $json = $response->json();
                $reply = $json['choices'][0]['message']['content'] ?? 'No response from AI.';
                return response()->json(['reply' => $reply, 'image_url' => $imageUrl]);
            }

            // log for debugging
            Log::error('OpenAI error', ['status' => $response->status(), 'body' => $response->body()]);
            $err = $response->json();
            return response()->json(['error' => 'AI service error', 'detail' => $err], 500);
        } catch (\Exception $e) {
            Log::error('OpenAI exception', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['error' => 'Exception: ' . $e->getMessage()], 500);
        }
    }
}
