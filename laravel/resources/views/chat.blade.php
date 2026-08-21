<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Tuintek CRM Assistant</title>
    <style>
        body { font-family: system-ui, sans-serif; background: #0d1117; color: #e6edf3; margin: 0; }
        .chat-container { max-width: 700px; margin: 0 auto; height: 100vh; display: flex; flex-direction: column; }
        .chat-header { padding: 20px; border-bottom: 1px solid #30363d; font-weight: bold; font-size: 18px; display: flex; justify-content: space-between; align-items: center; }
        .chat-header button { background: none; border: 1px solid #30363d; color: #8b949e; padding: 6px 12px; border-radius: 6px; cursor: pointer; font-size: 12px; }
        .chat-messages { flex: 1; overflow-y: auto; padding: 20px; display: flex; flex-direction: column; gap: 12px; }
        .message { max-width: 75%; padding: 10px 14px; border-radius: 12px; line-height: 1.4; white-space: pre-wrap; }
        .message.user { align-self: flex-end; background: #1f6feb; color: white; }
        .message.assistant { align-self: flex-start; background: #21262d; color: #e6edf3; }
        .message.loading { align-self: flex-start; background: #21262d; color: #8b949e; font-style: italic; }
        .chat-input-area { display: flex; padding: 16px; border-top: 1px solid #30363d; gap: 10px; }
        .chat-input-area input { flex: 1; padding: 12px; border-radius: 8px; border: 1px solid #30363d; background: #0d1117; color: #e6edf3; font-size: 14px; }
        .chat-input-area button { padding: 12px 20px; border-radius: 8px; border: none; background: #1f6feb; color: white; cursor: pointer; font-size: 14px; }
        .chat-input-area button:disabled { opacity: 0.5; cursor: not-allowed; }
    </style>
</head>
<body>
    <div class="chat-container">
        <div class="chat-header">
            <span>Tuintek CRM Assistant</span>
            <button id="newChatButton">New chat</button>
        </div>
        <div class="chat-messages" id="chatMessages">
            <div class="message assistant">Hello! I'm the Tuintek CRM assistant. How can I help you manage your contacts, leads, or projects today?</div>
        </div>
        <div class="chat-input-area">
            <input type="text" id="messageInput" placeholder="Type a message..." autocomplete="off">
            <button id="sendButton">Send</button>
        </div>
    </div>

    <script>
        function generateUUID() {
    return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function (c) {
        const r = Math.random() * 16 | 0;
        const v = c === 'x' ? r : (r & 0x3 | 0x8);
        return v.toString(16);
    });
}
        const messagesEl = document.getElementById('chatMessages');
        const inputEl = document.getElementById('messageInput');
        const sendButton = document.getElementById('sendButton');
        const newChatButton = document.getElementById('newChatButton');
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        function getOrCreateSessionId() {
    let id = localStorage.getItem('tuintek_chat_session_id');
    if (!id) {
        id = generateUUID();
        localStorage.setItem('tuintek_chat_session_id', id);
    }
    return id;
}

        let sessionId = getOrCreateSessionId();

    newChatButton.addEventListener('click', () => {
    sessionId = generateUUID();
    localStorage.setItem('tuintek_chat_session_id', sessionId);
    messagesEl.innerHTML = '<div class="message assistant">Hello! I\'m the Tuintek CRM assistant. How can I help you manage your contacts, leads, or projects today?</div>';
});

        function addMessage(text, role) {
            const div = document.createElement('div');
            div.className = `message ${role}`;
            div.textContent = text;
            messagesEl.appendChild(div);
            messagesEl.scrollTop = messagesEl.scrollHeight;
            return div;
        }

        async function sendMessage() {
            const text = inputEl.value.trim();
            if (!text) return;

            addMessage(text, 'user');
            inputEl.value = '';
            sendButton.disabled = true;
            inputEl.disabled = true;

            const loadingEl = addMessage('Thinking...', 'loading');

            try {
                const res = await fetch('{{ route('chat.send') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ message: text, session_id: sessionId }),
                });

                const data = await res.json();
                loadingEl.remove();

                if (!res.ok) {
                    addMessage(data.error || 'Something went wrong.', 'assistant');
                } else {
                    addMessage(data.reply, 'assistant');
                }
            } catch (err) {
                loadingEl.remove();
                addMessage('Connection error. Please try again.', 'assistant');
            } finally {
                sendButton.disabled = false;
                inputEl.disabled = false;
                inputEl.focus();
            }
        }

        sendButton.addEventListener('click', sendMessage);
        inputEl.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') sendMessage();
        });
    </script>
</body>
</html>