<x-app-layout>
    <!-- Removed app header slot to prevent double headers -->
    <div class="flex flex-col h-screen">
        <!-- Fixed Top Bar with chat title -->
        <div class="bg-white py-3 flex justify-between items-center sticky top-0 z-30 border-b shadow-sm">
            <div class="flex items-center">
                <a href="{{ route('chat.index') }}" class="flex items-center text-blue-600 hover:text-blue-800 text-sm font-medium pl-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Conversations
                </a>
            </div>

            <h2 class="font-semibold text-base sm:text-lg text-gray-800 truncate max-w-[50%] px-2">
                {{ $conversation->user1_id === auth()->id() ? $conversation->user2->name : $conversation->user1->name }}
            </h2>

            <div class="pr-3">
                <form action="{{ route('chat.conversation.destroy', $conversation) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="flex items-center text-red-600 hover:text-red-800 text-sm font-medium">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>

        <!-- Scrollable Messages with optimized spacing -->
        <div id="chat-messages" class="flex-1 overflow-y-auto py-3 space-y-3 px-2 sm:px-4 mt-1 mb-4">
            @foreach($messages as $message)
                <div class="flex {{ $message->user_id === auth()->id() ? 'justify-end' : 'justify-start' }}">
                    <div class="max-w-[85%] px-4 py-3 rounded-2xl {{ $message->user_id === auth()->id() ? 'bg-blue-100 rounded-tr-none' : 'bg-gray-300 rounded-tl-none' }}">
                        @if($message->attachment)
                            <div class="mb-2 rounded-lg overflow-hidden">
                                @php $fileType = $message->file_type; @endphp
                                <a href="{{ Storage::url($message->attachment) }}" target="_blank" class="block">
                                    @if($fileType === 'image')
                                        <img src="{{ Storage::url($message->attachment) }}" alt="Attachment" class="max-w-full h-auto max-h-52 object-cover">
                                    @elseif($fileType === 'video')
                                        <video controls class="max-w-full h-auto max-h-52 object-cover">
                                            <source src="{{ Storage::url($message->attachment) }}" type="video/mp4">
                                            Your browser does not support the video tag.
                                        </video>
                                    @else
                                        <div class="flex items-center p-3 bg-gray-100 rounded-lg">
                                            <svg class="w-8 h-8 text-gray-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            <div>
                                                <p class="text-sm font-medium truncate">{{ basename($message->attachment) }}</p>
                                                <p class="text-xs text-gray-500">{{ Str::upper(pathinfo($message->attachment, PATHINFO_EXTENSION)) }} file</p>
                                            </div>
                                        </div>
                                    @endif
                                </a>
                            </div>
                        @endif
                        @if($message->body)
                            <p class="text-sm sm:text-base">{{ $message->body }}</p>
                        @endif
                        <p class="text-xs text-gray-500 mt-2 text-right">
                            {{ $message->created_at->format('h:i A') }}
                        </p>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Fixed Input Area with enhanced styling -->
        <div class="bg-white py-3 sticky bottom-0 z-30 border-t shadow-sm">
            <div id="attachment-preview" class="mb-2 px-3"></div>
            <form id="message-form" action="{{ route('chat.store', $conversation) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="flex items-center gap-2 px-3">
                    <label for="attachment" class="cursor-pointer bg-gray-100 hover:bg-gray-200 rounded-full w-10 h-10 flex items-center justify-center transition flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                        </svg>
                        <input type="file" name="attachment" id="attachment" class="hidden" accept="image/*,video/*,audio/*,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document">
                    </label>

                    <div class="flex-1 relative">
                        <input type="text" name="body" placeholder="Type a message..."
                               class="w-full border rounded-full px-4 py-3 text-sm sm:text-base focus:outline-none focus:ring-2 focus:ring-blue-300">
                        <button type="submit"
                                class="absolute right-3 top-1/2 transform -translate-y-1/2 bg-blue-600 hover:bg-blue-700 text-white rounded-full w-8 h-8 flex items-center justify-center transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7" />
                            </svg>
                        </button>
                    </div>
                </div>
            </form>

            <div id="message-warning" class="text-red-600 text-sm px-3 py-1 hidden">
                Message cannot be empty. Please type a message or attach a file.
            </div>
        </div>
    </div>

    <script>
        // Auto-scroll to bottom on initial load
        const chatMessages = document.getElementById('chat-messages');
        chatMessages.scrollTop = chatMessages.scrollHeight;

        // Simple polling for new messages
        let lastMessageId = {{ $messages->last()->id ?? 0 }};
        let isPollingActive = true;

        function pollMessages() {
            if (!isPollingActive) return;

            fetch("{{ route('chat.poll', $conversation) }}?last_message_id=" + lastMessageId)
                .then(response => response.json())
                .then(messages => {
                    if (messages.length > 0) {
                        messages.forEach(msg => {
                            if (msg.id > lastMessageId) {
                                addMessageToChat(msg, false);
                                lastMessageId = msg.id;
                            }
                        });
                        chatMessages.scrollTop = chatMessages.scrollHeight;
                    }
                    setTimeout(pollMessages, 3000);
                })
                .catch(error => {
                    console.error('Polling error:', error);
                    setTimeout(pollMessages, 5000);
                });
        }

        // Add message to chat UI
        function addMessageToChat(message, isCurrentUser = true) {
            let attachmentHtml = '';
            if (message.attachment) {
                const fileName = message.attachment.split('/').pop();
                const fileExt = fileName.split('.').pop().toLowerCase();

                // Determine file type
                let fileType = 'file';
                const imageTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                const videoTypes = ['mp4', 'mov', 'avi'];

                if (imageTypes.includes(fileExt)) fileType = 'image';
                if (videoTypes.includes(fileExt)) fileType = 'video';

                if (fileType === 'image') {
                    attachmentHtml = `
                        <div class="mb-2 rounded-lg overflow-hidden">
                            <a href="/storage/${message.attachment}" target="_blank">
                                <img src="/storage/${message.attachment}" alt="Attachment" class="max-w-full h-auto max-h-52 object-cover">
                            </a>
                        </div>
                    `;
                }
                else if (fileType === 'video') {
                    attachmentHtml = `
                        <div class="mb-2 rounded-lg overflow-hidden">
                            <video controls class="max-w-full h-auto max-h-52 object-cover">
                                <source src="/storage/${message.attachment}" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                        </div>
                    `;
                }
                else {
                    attachmentHtml = `
                        <div class="mb-2">
                            <a href="/storage/${message.attachment}" target="_blank" class="flex items-center p-3 bg-gray-100 rounded-lg">
                                <svg class="w-8 h-8 text-gray-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <div>
                                    <p class="text-sm font-medium truncate">${fileName}</p>
                                    <p class="text-xs text-gray-500">${fileExt.toUpperCase()} file</p>
                                </div>
                            </a>
                        </div>
                    `;
                }
            }

            const bodyHtml = message.body ?
                `<p class="text-sm sm:text-base">${message.body}</p>` : '';

            const timestamp = isCurrentUser ?
                'Just now' :
                new Date(message.created_at).toLocaleTimeString([], { hour: '2-digit', minute:'2-digit' });

            const messageElement = `
                <div class="flex ${isCurrentUser ? 'justify-end' : 'justify-start'}">
                    <div class="max-w-[85%] px-4 py-3 rounded-2xl ${isCurrentUser ? 'bg-blue-100 rounded-tr-none' : 'bg-gray-100 rounded-tl-none'}">
                        ${attachmentHtml}
                        ${bodyHtml}
                        <p class="text-xs text-gray-500 mt-2 text-right">
                            ${timestamp}
                        </p>
                    </div>
                </div>
            `;

            chatMessages.innerHTML += messageElement;
        }

        // Message submission handler
        document.getElementById('message-form').addEventListener('submit', function(e) {
            e.preventDefault(); // Prevent default form submission

            const formData = new FormData(this);
            const warningDiv = document.getElementById('message-warning');
            const hasBody = formData.get('body') && formData.get('body').trim() !== '';
            const hasAttachment = formData.get('attachment') && formData.get('attachment').name;

            if (!hasBody && !hasAttachment) {
                e.preventDefault();
                warningDiv.classList.remove('hidden');
                setTimeout(() => warningDiv.classList.add('hidden'), 3000);
                return;
            } else {
                warningDiv.classList.add('hidden');
            }

            // Create optimistic UI update
            const tempMessage = {
            id: Date.now(), // Temporary ID
            body: formData.get('body'),
            attachment: null,
            user_id: {{ auth()->id() }},
            created_at: new Date().toISOString(),
            };

            // Handle file preview for optimistic update
            const attachmentInput = document.getElementById('attachment');
            if (attachmentInput.files.length > 0) {
            const file = attachmentInput.files[0];
            tempMessage.body = formData.get('body') || 'Sending attachment...';

            // Create a temporary object URL for preview
            if (file.type.startsWith('image/')) {
                tempMessage.attachment = URL.createObjectURL(file);
            }
            }

            addMessageToChat(tempMessage, true);
            chatMessages.scrollTop = chatMessages.scrollHeight;

            // Disable input during submission
            const input = this.querySelector('input[name="body"]');
            const submitBtn = this.querySelector('button[type="submit"]');
            input.disabled = true;
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-50');

            // Send to server
            fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
            })
            .then(response => response.json())
            .then(actualMessage => {
            // Replace temporary message with actual one
            const lastMessage = chatMessages.lastElementChild;
            if (lastMessage) {
                lastMessage.remove();
            }
            addMessageToChat(actualMessage, true);
            chatMessages.scrollTop = chatMessages.scrollHeight;
            lastMessageId = Math.max(lastMessageId, actualMessage.id);

            // Reset form
            this.reset();
            })
            .catch(error => {
            console.error('Error:', error);
            alert('Error sending message');
            // Remove optimistic message on error
            const lastMessage = chatMessages.lastElementChild;
            if (lastMessage) {
                lastMessage.remove();
            }
            })
            .finally(() => {
            input.disabled = false;
            submitBtn.disabled = false;
            submitBtn.classList.remove('opacity-50');
            input.focus();
            });
        });

        // Attachment preview logic
        const attachmentInput = document.getElementById('attachment');
        const previewDiv = document.getElementById('attachment-preview');

        attachmentInput.addEventListener('change', function() {
            previewDiv.innerHTML = '';
            if (this.files && this.files[0]) {
                const file = this.files[0];
                const previewWrapper = document.createElement('div');
                previewWrapper.className = "relative inline-block";

                // Remove button
                const removeBtn = document.createElement('button');
                removeBtn.type = "button";
                removeBtn.innerHTML = "&times;";
                removeBtn.className = "absolute top-1 right-1 bg-white rounded-full border border-gray-300 text-gray-600 w-6 h-6 flex items-center justify-center shadow hover:bg-red-100";
                removeBtn.onclick = function() {
                    attachmentInput.value = "";
                    previewDiv.innerHTML = "";
                };

                if (file.type.startsWith('image/')) {
                    const img = document.createElement('img');
                    img.src = URL.createObjectURL(file);
                    img.className = "max-w-xs max-h-40 rounded-lg border";
                    previewWrapper.appendChild(img);
                } else if (file.type.startsWith('video/')) {
                    const video = document.createElement('video');
                    video.src = URL.createObjectURL(file);
                    video.controls = true;
                    video.className = "max-w-xs max-h-40 rounded-lg border";
                    previewWrapper.appendChild(video);
                } else {
                    const fileInfo = document.createElement('div');
                    fileInfo.className = "p-2 bg-gray-100 rounded";
                    fileInfo.textContent = file.name;
                    previewWrapper.appendChild(fileInfo);
                }
                previewWrapper.appendChild(removeBtn);
                previewDiv.appendChild(previewWrapper);
            }
        });

        // Clear preview after sending
        document.getElementById('message-form').addEventListener('submit', function() {
            previewDiv.innerHTML = '';
        });

        // Start polling when page is visible
        document.addEventListener('visibilitychange', () => {
            isPollingActive = !document.hidden;
            if (isPollingActive) pollMessages();
        });

        // Initial polling start
        pollMessages();
    </script>
</x-app-layout>
