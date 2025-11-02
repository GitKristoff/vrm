document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('ai-chat-form');
    const messageInput = document.getElementById('ai-message');
    const imageInput = document.getElementById('ai-image');
    const messagesEl = document.getElementById('ai-messages');
    const sendBtn = document.getElementById('ai-send-btn');
    const preview = document.getElementById('ai-preview');
    const previewImg = document.getElementById('ai-preview-img');
    const removeImgBtn = document.getElementById('ai-remove-img');

    // Loading overlay element and helpers (must exist in the DOM)
    const loadingOverlay = document.getElementById('ai-loading-overlay');
    function showLoading() {
        if (loadingOverlay) loadingOverlay.classList.add('active');
        if (sendBtn) sendBtn.disabled = true;
        if (messageInput) messageInput.disabled = true;
        if (imageInput) imageInput.disabled = true;
    }
    function hideLoading() {
        if (loadingOverlay) loadingOverlay.classList.remove('active');
        if (sendBtn) sendBtn.disabled = false;
        if (messageInput) messageInput.disabled = false;
        if (imageInput) imageInput.disabled = false;
    }

    function appendMessage(role, text, extra = {}) {
        const wrapper = document.createElement('div');
        wrapper.className = role === 'user' ? 'text-right mb-2' : 'text-left mb-2';
        const bubble = document.createElement('div');
        // add a stable class so CSS can control wrapping/width
        bubble.className = (role === 'user'
            ? 'message-bubble inline-block bg-indigo-100 text-gray-900 text-sm sm:text-base'
            : 'message-bubble inline-block bg-gray-100 text-gray-900 text-sm sm:text-base') + ' p-2 rounded';

        bubble.innerText = text;
        wrapper.appendChild(bubble);

        if (extra.image_url) {
            const img = document.createElement('img');
            img.src = extra.image_url;
            // responsive image sizing
            img.className = 'mt-2 rounded border';
            img.style.maxHeight = '220px';
            img.style.objectFit = 'cover';
            wrapper.appendChild(img);
        }

        messagesEl.appendChild(wrapper);
        messagesEl.scrollTop = messagesEl.scrollHeight;
    }

    imageInput?.addEventListener('change', function () {
        const file = imageInput.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = function (e) {
            previewImg.src = e.target.result;
            preview.classList.remove('hidden');
        };
        reader.readAsDataURL(file);
    });

    removeImgBtn?.addEventListener('click', function (e) {
        e.preventDefault();
        imageInput.value = '';
        previewImg.src = '';
        preview.classList.add('hidden');
    });

    form?.addEventListener('submit', async function (e) {
        e.preventDefault();
        if (!messageInput && !imageInput) return;

        const userText = messageInput?.value?.trim() || '';
        appendMessage('user', userText, imageInput?.files?.length ? { image_url: previewImg?.src } : {});

        showLoading();

        const fd = new FormData();
        const tokenEl = document.querySelector('input[name="_token"]');
        if (tokenEl) fd.append('_token', tokenEl.value);
        fd.append('message', userText);
        if (imageInput?.files?.length) fd.append('image', imageInput.files[0]);
        const petIdEl = document.getElementById('ai-pet-id');
        if (petIdEl) fd.append('pet_id', petIdEl.value);

        try {
            const url = window.AI_CHAT_URL || '/ai-chat';
            const res = await fetch(url, {
                method: 'POST',
                body: fd,
                credentials: 'same-origin',
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });

            const data = await res.json();
            if (res.ok) {
                appendMessage('assistant', data.reply || 'No response from AI.', { image_url: data.image_url });
            } else {
                appendMessage('assistant', data.error || 'Error communicating with AI.');
                console.error('AI error', data);
            }
        } catch (err) {
            appendMessage('assistant', 'Network error. Try again later.');
            console.error(err);
        } finally {
            // --- Reliable clear/reset of the input and preview ---
            if (messageInput) {
                // textarea / input case
                if ('value' in messageInput) {
                    messageInput.value = '';
                    // trigger input event in case autosize libraries are used
                    messageInput.dispatchEvent(new Event('input', { bubbles: true }));
                } else {
                    // contenteditable fallback
                    messageInput.textContent = '';
                }
                // restore focus so the owner can type again immediately
                messageInput.focus();
            }

            if (imageInput) {
                imageInput.value = '';
                imageInput.disabled = false;
            }

            if (previewImg) {
                previewImg.src = '';
            }
            preview?.classList.add('hidden');

            hideLoading();
        }
    });
});
