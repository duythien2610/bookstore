{{-- Chatbot Widget — Modtra Books AI Assistant --}}
<div id="chatbot-widget">
    {{-- Greeting Tooltip --}}
    <div class="chatbot-greeting" id="chatbot-greeting">
        <div class="chatbot-greeting-content">
            <span class="chatbot-greeting-wave">👋</span>
            <div>
                <strong>Chào bạn!</strong>
                <span>Cần tìm sách hay? Tôi giúp ngay!</span>
            </div>
            <button class="chatbot-greeting-close" id="chatbot-greeting-close" aria-label="Đóng">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" width="14" height="14"><path d="M18 6 6 18M6 6l12 12"/></svg>
            </button>
        </div>
    </div>

    {{-- Toggle Button --}}
    <button class="chatbot-toggle" id="chatbot-toggle" title="Trợ lý AI Modtra Books" aria-label="Mở trợ lý AI">
        <span class="chatbot-toggle-icon" id="chatbot-icon-open">
            {{-- Custom sparkle AI icon --}}
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" width="26" height="26">
                <path d="M12 3 13.5 8.5 19 10l-5.5 1.5L12 17l-1.5-5.5L5 10l5.5-1.5L12 3z" fill="currentColor" fill-opacity="0.3"/>
                <path d="M19 14l.75 2.25L22 17l-2.25.75L19 20l-.75-2.25L16 17l2.25-.75z" fill="currentColor" fill-opacity="0.5"/>
                <path d="M5 17l.5 1.5L7 19l-1.5.5L5 21l-.5-1.5L3 19l1.5-.5z" fill="currentColor" fill-opacity="0.5"/>
            </svg>
        </span>
        <span class="chatbot-toggle-icon hidden" id="chatbot-icon-close">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" width="24" height="24"><path d="M18 6 6 18M6 6l12 12"/></svg>
        </span>
        <span class="chatbot-pulse"></span>
        <span class="chatbot-pulse-2"></span>
        <span class="chatbot-status-dot"></span>
    </button>

    {{-- Chat Window --}}
    <div class="chatbot-window hidden" id="chatbot-window">
        {{-- Decorative gradient shape at header --}}
        <div class="chatbot-window-deco"></div>

        {{-- Header --}}
        <div class="chatbot-header">
            <div class="chatbot-header-info">
                <div class="chatbot-avatar-wrap">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" width="22" height="22">
                        <path d="M12 3 13.5 8.5 19 10l-5.5 1.5L12 17l-1.5-5.5L5 10l5.5-1.5L12 3z"/>
                    </svg>
                    <span class="chatbot-online-dot"></span>
                </div>
                <div>
                    <strong>Modtra AI</strong>
                    <small><span class="status-dot"></span> Đang hoạt động</small>
                </div>
            </div>
            <button class="chatbot-close" id="chatbot-close" title="Đóng">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" width="18" height="18"><path d="M18 6 6 18M6 6l12 12"/></svg>
            </button>
        </div>

        {{-- Messages --}}
        <div class="chatbot-messages" id="chatbot-messages">
            <div class="chatbot-msg bot">
                <div class="chatbot-msg-avatar">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" width="14" height="14"><path d="M12 3 13.5 8.5 19 10l-5.5 1.5L12 17l-1.5-5.5L5 10l5.5-1.5L12 3z" fill="currentColor"/></svg>
                </div>
                <div class="chatbot-msg-bubble">
                    Xin chào! 👋 Tôi là trợ lý AI của <strong>Modtra Books</strong>. Tôi có thể giúp bạn tìm sách, gợi ý sách hay hoặc trả lời câu hỏi. Bạn cần gì nào?
                    <div class="chatbot-quick-actions">
                        <button class="chatbot-quick-btn" data-text="Gợi ý sách hay cho tôi">📚 Gợi ý sách hay</button>
                        <button class="chatbot-quick-btn" data-text="Sách bán chạy nhất hiện nay">🔥 Bán chạy</button>
                        <button class="chatbot-quick-btn" data-text="Có chương trình khuyến mãi nào không?">🎁 Khuyến mãi</button>
                    </div>
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
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="18" height="18">
                    <path d="m22 2-7 20-4-9-9-4Z"/>
                    <path d="M22 2 11 13"/>
                </svg>
            </button>
        </div>
        <div class="chatbot-footer-badge">
            <svg viewBox="0 0 24 24" fill="currentColor" width="10" height="10"><path d="M12 2 13.5 8.5 19 10l-5.5 1.5L12 17l-1.5-5.5L5 10l5.5-1.5z"/></svg>
            Powered by Modtra AI
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
    const greeting  = document.getElementById('chatbot-greeting');
    const greetingClose = document.getElementById('chatbot-greeting-close');
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    let isOpen = false;
    let greetingShown = false;

    // Show greeting tooltip after 2s if chat not opened and not dismissed
    if (!sessionStorage.getItem('chatbot_greeting_dismissed')) {
        setTimeout(() => {
            if (!isOpen && greeting && !greetingShown) {
                greeting.classList.add('show');
                greetingShown = true;
                // Auto-hide after 8 seconds
                setTimeout(() => {
                    if (greeting.classList.contains('show') && !isOpen) {
                        greeting.classList.remove('show');
                    }
                }, 8000);
            }
        }, 2500);
    }

    if (greetingClose) {
        greetingClose.addEventListener('click', function(e) {
            e.stopPropagation();
            greeting.classList.remove('show');
            sessionStorage.setItem('chatbot_greeting_dismissed', '1');
        });
    }

    // Toggle chat window
    function toggleChat() {
        isOpen = !isOpen;
        window_.classList.toggle('hidden', !isOpen);
        iconOpen.classList.toggle('hidden', isOpen);
        iconClose.classList.toggle('hidden', !isOpen);
        if (greeting) greeting.classList.remove('show');
        if (isOpen) {
            sessionStorage.setItem('chatbot_greeting_dismissed', '1');
            input.focus();
            loadMessages();
            scrollToBottom();
        }
    }

    toggle.addEventListener('click', toggleChat);
    closeBtn.addEventListener('click', toggleChat);

    // Quick action buttons
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.chatbot-quick-btn');
        if (btn) {
            const text = btn.dataset.text;
            if (text && input) {
                input.value = text;
                sendMessage();
            }
        }
    });

    // Load chat history from DB
    function loadMessages() {
        fetch('{{ url("/chat/messages") }}')
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

        // Setup Timeout (15s)
        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), 15000);

        fetch('{{ url("/chatbot/send") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            signal: controller.signal,
            body: JSON.stringify({ message: text }),
        })
        .then(res => {
            clearTimeout(timeoutId);
            return res.json();
        })
        .then(data => {
            removeTyping(typingId);
            appendMessage('bot', data.reply || 'Xin lỗi, tôi chưa hiểu ý bạn. Bạn có thể nhắc lại không?');
        })
        .catch((err) => {
            clearTimeout(timeoutId);
            removeTyping(typingId);
            
            if (err.name === 'AbortError') {
                appendMessage('bot', 'Hệ thống đang bận xử lý (Timeout). Vui lòng thử lại sau giây lát!');
            } else {
                appendMessage('bot', 'Rất tiếc, đã có lỗi kết nối xảy ra. Bạn vui lòng kiểm tra mạng và thử lại nhé.');
            }
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
                <div class="chatbot-msg-avatar"><svg viewBox="0 0 24 24" fill="currentColor" width="14" height="14"><path d="M12 3 13.5 8.5 19 10l-5.5 1.5L12 17l-1.5-5.5L5 10l5.5-1.5z"/></svg></div>
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
            <div class="chatbot-msg-avatar"><svg viewBox="0 0 24 24" fill="currentColor" width="14" height="14"><path d="M12 3 13.5 8.5 19 10l-5.5 1.5L12 17l-1.5-5.5L5 10l5.5-1.5z"/></svg></div>
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
