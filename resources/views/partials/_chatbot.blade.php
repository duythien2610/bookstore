{{-- Chatbot Widget — Modtra Books AI Assistant --}}
<div id="chatbot-widget">
    {{-- Toggle Button 
        Biểu tượng CHATBOT AI--}}
    <!--Biểu tượng chatbot AI -->
    <button class="chatbot-toggle" id="chatbot-toggle" title="Trợ lý AI Modtra Books">
        <span class="material-icons chatbot-toggle-icon" id="chatbot-icon-open">smart_toy</span>
        <span class="material-icons chatbot-toggle-icon hidden" id="chatbot-icon-close">close</span>
        <span class="chatbot-pulse"></span>
    </button>

    {{-- Chat Window --}}
    <div class="chatbot-window hidden" id="chatbot-window">
        {{-- Header --}}
        <div class="chatbot-header">
            <div class="chatbot-header-info">
                <span class="material-icons">smart_toy</span>
                <div>
                    <strong>Modtra AI</strong>
                    <small>Trợ lý tư vấn sách</small>
                </div>
            </div>
            <button class="chatbot-close" id="chatbot-close" title="Đóng">
                <span class="material-icons">close</span>
            </button>
        </div>

        {{-- Messages --}}
        <div class="chatbot-messages" id="chatbot-messages">
            <div class="chatbot-msg bot">
                <div class="chatbot-msg-avatar">
                    <span class="material-icons">smart_toy</span>
                </div>
                <div class="chatbot-msg-bubble">
                    Xin chào! 👋 Tôi là trợ lý AI của <strong>Modtra Books</strong>. Tôi có thể giúp bạn tìm sách, gợi ý sách hay hoặc trả lời câu hỏi. Bạn cần gì nào?
                </div>
            </div>
        </div>

        {{-- Input --}}
        <div class="chatbot-input-area">
            <input type="text"
                   class="chatbot-input"
                   id="chatbot-input"
                   placeholder="Nhập câu hỏi về sách..."
                   maxlength="500"
                   autocomplete="off">
            <button class="chatbot-send" id="chatbot-send" title="Gửi">
                <span class="material-icons">send</span>
            </button>
        </div>
    </div>
</div>

<script>
    
document.addEventListener('DOMContentLoaded', function () {
    const toggle    = document.getElementById('chatbot-toggle');
    const window_   = document.getElementById('chatbot-window');
    const closeBtn  = document.getElementById('chatbot-close');
    const input     = document.getElementById('chatbot-input');
    const sendBtn   = document.getElementById('chatbot-send');
    const messages  = document.getElementById('chatbot-messages');
    const iconOpen  = document.getElementById('chatbot-icon-open');
    const iconClose = document.getElementById('chatbot-icon-close');
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    let isOpen = false;

    // Toggle chat window
    function toggleChat() {
        isOpen = !isOpen;
        window_.classList.toggle('hidden', !isOpen);
        iconOpen.classList.toggle('hidden', isOpen);
        iconClose.classList.toggle('hidden', !isOpen);
        if (isOpen) {
            input.focus();
            loadMessages();
            scrollToBottom();
        }
    }

    toggle.addEventListener('click', toggleChat);
    closeBtn.addEventListener('click', toggleChat);

    // Load chat history from DB
    function loadMessages() {
        fetch('{{ url("/chat/messege") }}')
            .then(res => res.json())
            .then(data => {
                // Clear current messages (except initial bot greeting if you want)
                // Or just append if not already loaded.
                // For simplicity, let's keep the greeting and append the rest.
                const historyIds = Array.from(messages.querySelectorAll('.chatbot-msg')).map(el => el.dataset.id);
                
                data.forEach(msg => {
                    // Avoid duplicating messages
                    if (!historyIds.includes(msg.id.toString())) {
                        appendMessage(msg.sender, msg.message, msg.id);
                    }
                });
            })
            .catch(err => console.error('Error loading messages:', err));
    }

    // Send message
    function sendMessage() {
        const text = input.value.trim();
        if (!text) return;

        appendMessage('user', text);
        input.value = '';
        input.disabled = true;
        sendBtn.disabled = true;

        // Show typing indicator
        const typingId = showTyping();

        fetch('{{ url("/chatbot/send") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ message: text }),
        })
        .then(res => res.json())
        .then(data => {
            removeTyping(typingId);
            appendMessage('bot', data.reply || 'Xin lỗi, không thể trả lời.');
        })
        .catch(() => {
            removeTyping(typingId);
            appendMessage('bot', 'Lỗi kết nối. Vui lòng thử lại.');
        })
        .finally(() => {
            input.disabled = false;
            sendBtn.disabled = false;
            input.focus();
        });
    }

    sendBtn.addEventListener('click', sendMessage);
    input.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') sendMessage();
    });

    // Append message bubble
    function appendMessage(role, text, id = null) {
        const wrap = document.createElement('div');
        wrap.className = 'chatbot-msg ' + role;
        if (id) wrap.dataset.id = id;

        if (role === 'bot') {
            wrap.innerHTML = `
                <div class="chatbot-msg-avatar"><span class="material-icons">smart_toy</span></div>
                <div class="chatbot-msg-bubble">${formatText(text)}</div>`;
        } else {
            wrap.innerHTML = `<div class="chatbot-msg-bubble">${escapeHtml(text)}</div>`;
        }

        messages.appendChild(wrap);
        scrollToBottom();
    }

    // Format bot text (basic markdown-like)
    function formatText(text) {
        return text
            .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
            .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
            .replace(/\*(.*?)\*/g, '<em>$1</em>')
            .replace(/\[(.*?)\]\((.*?)\)/g, '<a href="$2" target="_blank" style="color:var(--color-primary); text-decoration:underline; font-weight:bold;">$1</a>')
            .replace(/\n/g, '<br>');
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Typing indicator
    function showTyping() {
        const id = 'typing-' + Date.now();
        const el = document.createElement('div');
        el.className = 'chatbot-msg bot';
        el.id = id;
        el.innerHTML = `
            <div class="chatbot-msg-avatar"><span class="material-icons">smart_toy</span></div>
            <div class="chatbot-msg-bubble chatbot-typing">
                <span class="dot"></span><span class="dot"></span><span class="dot"></span>
            </div>`;
        messages.appendChild(el);
        scrollToBottom();
        return id;
    }

    function removeTyping(id) {
        const el = document.getElementById(id);
        if (el) el.remove();
    }

    function scrollToBottom() {
        messages.scrollTop = messages.scrollHeight;
    }
});
</script>
