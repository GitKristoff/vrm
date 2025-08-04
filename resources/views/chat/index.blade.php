<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Conversations') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <a href="{{ route('chat.create') }}" class="inline-block bg-blue-600 text-white px-4 py-2 rounded mb-4">
                        Start New Conversation
                    </a>

                    @if($conversations->count())
                        <div class="space-y-4">
                            @foreach($conversations as $conversation)
                                @php
                                    $otherUser = $conversation->user1_id === auth()->id()
                                        ? $conversation->user2
                                        : $conversation->user1;
                                @endphp
                                <div class="flex items-center justify-between p-4 border rounded-lg bg-white shadow-sm hover:shadow-md transition">
                                    <a href="{{ route('chat.show', $conversation) }}" class="flex items-center group">
                                        @if($otherUser->profile_picture)
                                            <img src="{{ asset('storage/' . $otherUser->profile_picture) }}" class="h-12 w-12 rounded-full object-cover border" alt="Profile">
                                        @else
                                            <div class="h-12 w-12 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 font-bold text-lg shadow">
                                                {{ strtoupper(substr($otherUser->name, 0, 2)) }}
                                            </div>
                                        @endif
                                        <div class="ml-4">
                                            <h3 class="font-semibold text-gray-800 group-hover:text-blue-700 transition">{{ $otherUser->name }}</h3>
                                            @if($conversation->messages->first())
                                                <p class="text-sm text-gray-500 truncate max-w-md mt-1">
                                                    {{ $conversation->messages->first()->body }}
                                                </p>
                                            @endif
                                        </div>
                                    </a>
                                    <form action="{{ route('chat.conversation.destroy', $conversation) }}" method="POST" class="ml-4">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 rounded-full bg-red-50 hover:bg-red-100 text-red-600 hover:text-red-800 transition" title="Delete Conversation">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No conversations</h3>
                            <p class="mt-1 text-sm text-gray-500">Start a new conversation with a vet or pet owner.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
