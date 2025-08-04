<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Chat with {{ $conversation->user1_id === auth()->id() ? $conversation->user2->name : $conversation->user1->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200 flex flex-col h-[70vh]">
                    <!-- Back & Delete Button-->
                    <div class="flex justify-between items-center mb-4">
                        <a href="{{ route('chat.index') }}" class="inline-block text-blue-600 hover:underline">
                            ← Back to Conversations
                        </a>
                        <form action="{{ route('chat.conversation.destroy', $conversation) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800 px-4 py-2 rounded border border-red-600 hover:border-red-800">
                                Delete Conversation
                            </button>
                        </form>
                    </div>

                    <div id="chat-messages" class="flex-1 overflow-y-auto mb-4 space-y-4">
                        @foreach($messages as $message)
                            <div class="flex {{ $message->user_id === auth()->id() ? 'justify-end' : 'justify-start' }}">
                                <div class="max-w-xs md:max-w-md px-4 py-2 rounded-lg {{ $message->user_id === auth()->id() ? 'bg-blue-100' : 'bg-gray-100' }}">
                                    @if($message->attachment)
                                        <div class="mb-2">
                                            <a href="{{ Storage::url($message->attachment) }}" target="_blank" class="block">
                                                <img src="{{ Storage::url($message->attachment) }}" alt="Attachment" class="max-w-full h-auto rounded">
                                            </a>
                                        </div>
                                    @endif
                                    @if($message->body)
                                        <p class="text-sm">{{ $message->body }}</p>
                                    @endif
                                    <p class="text-xs text-gray-500 mt-1">
                                        {{ $message->created_at->diffForHumans() }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <form id="message-form" action="{{ route('chat.store', $conversation) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="flex space-x-2">

                            <div class="flex-1 flex items-center">
                                <input type="text" name="body" placeholder="Type your message..."
                                       class="flex-1 border rounded-full px-4 py-2 focus:outline-none">
                                <label for="attachment" class="cursor-pointer ml-2 text-gray-500 hover:text-blue-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                    </svg>
                                    <input type="file" name="attachment" id="attachment" class="hidden">
                                </label>
                            </div>

                            <button type="submit"
                                    class="bg-blue-600 text-white rounded-full w-12 h-12 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Auto-scroll to bottom
        const chatMessages = document.getElementById('chat-messages');
        chatMessages.scrollTop = chatMessages.scrollHeight;

        // Simple polling
        let lastMessageId = {{ $messages->last()->id ?? 0 }};

        function pollMessages() {
            fetch("{{ route('chat.poll', $conversation) }}?last_message_id=" + lastMessageId)
                .then(response => {
                    // Check if response is JSON
                    const contentType = response.headers.get('content-type');
                    if (!contentType || !contentType.includes('application/json')) {
                        return response.text().then(text => {
                            throw new Error(`Expected JSON but got: ${text}`);
                        });
                    }
                    return response.json();
                })
                .then(messages => {
                    messages.forEach(msg => {
                        // Only add new messages
                        if (msg.id > lastMessageId) {
                            const isCurrentUser = msg.user_id === {{ auth()->id() }};

                            // Handle attachments
                            const attachmentHtml = msg.attachment ?
                                `<div class="mb-2">
                                    <a href="/storage/${msg.attachment}" target="_blank">
                                        <img src="/storage/${msg.attachment}" alt="Attachment" class="max-w-full h-auto rounded">
                                    </a>
                                </div>` : '';

                            // Handle text
                            const bodyHtml = msg.body ?
                                `<p class="text-sm">${msg.body}</p>` : '';

                            const messageElement = `
                                <div class="flex ${isCurrentUser ? 'justify-end' : 'justify-start'}">
                                    <div class="max-w-xs md:max-w-md px-4 py-2 rounded-lg ${isCurrentUser ? 'bg-blue-100' : 'bg-gray-100'}">
                                        ${attachmentHtml}
                                        ${bodyHtml}
                                        <p class="text-xs text-gray-500 mt-1">
                                            ${msg.created_at}
                                        </p>
                                    </div>
                                </div>
                            `;

                            chatMessages.innerHTML += messageElement;
                            lastMessageId = msg.id;
                        }
                    });

                    if (messages.length > 0) {
                        chatMessages.scrollTop = chatMessages.scrollHeight;
                    }

                    setTimeout(pollMessages, 3000); // Poll every 3 seconds
                })
                .catch(error => {
                    console.error('Polling error:', error);
                    // Retry after 5 seconds on error
                    setTimeout(pollMessages, 5000);
                });
        }

        // Message submission
       document.getElementById('message-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const messageInput = this.querySelector('input[name="body"]');

            // Don't send empty messages
            if (!formData.get('body') && !formData.get('attachment')) {
                return;
            }

            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }).then(response => {
                // Check if response is JSON
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    return response.text().then(text => {
                        throw new Error(`Expected JSON but got: ${text}`);
                    });
                }
                return response.json();
            })
            .then(message => {
                this.reset();
                if (message.body || message.attachment) {
                    const isCurrentUser = message.user_id === {{ auth()->id() }};

                    // Handle attachments
                    const attachmentHtml = message.attachment ?
                        `<div class="mb-2">
                            <a href="/storage/${message.attachment}" target="_blank">
                                <img src="/storage/${message.attachment}" alt="Attachment" class="max-w-full h-auto rounded">
                            </a>
                        </div>` : '';

                    // Handle text
                    const bodyHtml = message.body ?
                        `<p class="text-sm">${message.body}</p>` : '';

                    const messageElement = `
                        <div class="flex ${isCurrentUser ? 'justify-end' : 'justify-start'}">
                            <div class="max-w-xs md:max-w-md px-4 py-2 rounded-lg ${isCurrentUser ? 'bg-blue-100' : 'bg-gray-100'}">
                                ${attachmentHtml}
                                ${bodyHtml}
                                <p class="text-xs text-gray-500 mt-1">
                                    ${message.created_at}
                                </p>
                            </div>
                        </div>
                    `;

                    chatMessages.innerHTML += messageElement;
                    chatMessages.scrollTop = chatMessages.scrollHeight;
                    lastMessageId = message.id;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error sending message: ' + error.message);
            });
        });
    </script>
</x-app-layout>
