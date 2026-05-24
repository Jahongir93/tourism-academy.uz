<!-- Support Chat Widget -->
<div id="supportChatWidget" class="support-chat-widget" style="position: fixed !important; bottom: 24px !important; right: 24px !important; z-index: 999999 !important; display: block !important; visibility: visible !important;">
    <!-- Chat Toggle Button -->
    <button class="support-chat-toggle" id="supportChatToggle" title="Yordam" style="width: 60px !important; height: 60px !important; display: flex !important; visibility: visible !important; opacity: 1 !important;">
        <i class="fas fa-comment-dots" id="chatIconDefault"></i>
        <i class="fas fa-times" id="chatIconClose" style="display: none;"></i>
        <span class="support-chat-badge" id="supportChatBadge" style="display: none;">0</span>
    </button>

    <!-- Chat Popup -->
    <div class="support-chat-popup" id="supportChatPopup">
        <!-- Header -->
        <div class="support-chat-header">
            <div class="support-chat-header-info">
                <div class="support-chat-avatar">
                    <i class="fas fa-headset"></i>
                </div>
                <div>
                    <div class="support-chat-title">Qo'llab-quvvatlash</div>
                    <div class="support-chat-subtitle">
                        <span class="support-online-dot"></span>
                        Onlayn
                    </div>
                </div>
            </div>
            <button class="support-chat-minimize" id="supportChatMinimize">
                <i class="fas fa-minus"></i>
            </button>
        </div>

        <!-- Guest Info Form (shown for non-logged in users on first message) -->
        <div class="support-chat-guest-form" id="supportGuestForm" style="display: none;">
            <p>Sizga javob berish uchun quyidagi ma'lumotlarni kiriting:</p>
            <input type="text" id="guestName" class="support-chat-input-field" placeholder="Ism va familiyangiz *" required>
            <input type="tel" id="guestPhone" class="support-chat-input-field" placeholder="Telefon raqamingiz *" required pattern="[0-9+\s\-()]+">
            <button class="support-chat-start-btn" onclick="submitGuestInfo()">
                <i class="fas fa-arrow-right"></i> Davom etish
            </button>
        </div>

        <!-- Messages Container -->
        <div class="support-chat-messages" id="supportChatMessages">
            <!-- Welcome message -->
            <div class="support-chat-welcome">
                <div class="support-chat-welcome-icon">
                    <i class="fas fa-hand-wave"></i>
                </div>
                <h4>Assalomu alaykum!</h4>
                <p>Turizm Akademiyasi qo'llab-quvvatlash xizmatiga xush kelibsiz. Savolingizni yozing.</p>
            </div>
        </div>

        <!-- Input Area -->
        <div class="support-chat-input-area" id="supportChatInputArea">
            <input type="text" id="supportChatInput" class="support-chat-input" placeholder="Xabar yozing..." autocomplete="off">
            <button class="support-chat-send" id="supportChatSend">
                <i class="fas fa-paper-plane"></i>
            </button>
        </div>

        <!-- Footer -->
        <div class="support-chat-footer">
            Turizm Akademiyasi
        </div>
    </div>
</div>

<style>
/* Support Chat Widget Styles */
.support-chat-widget {
    position: fixed !important;
    bottom: 24px !important;
    right: 24px !important;
    z-index: 99999 !important;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
    display: block !important;
    visibility: visible !important;
}

.support-chat-toggle {
    width: 60px !important;
    height: 60px !important;
    border-radius: 50% !important;
    background: linear-gradient(135deg, #1EB53A 0%, #0099B5 100%) !important;
    border: none !important;
    color: white !important;
    font-size: 24px !important;
    cursor: pointer !important;
    box-shadow: 0 4px 20px rgba(30, 181, 58, 0.4) !important;
    transition: all 0.3s ease;
    position: relative !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    visibility: visible !important;
    opacity: 1 !important;
}

.support-chat-toggle:hover {
    transform: scale(1.1);
    box-shadow: 0 6px 25px rgba(30, 181, 58, 0.5);
}

.support-chat-toggle.active {
    background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
    box-shadow: 0 4px 20px rgba(220, 38, 38, 0.4);
}

.support-chat-badge {
    position: absolute;
    top: -5px;
    right: -5px;
    background: #dc2626;
    color: white;
    font-size: 12px;
    font-weight: 600;
    min-width: 22px;
    height: 22px;
    border-radius: 11px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid white;
}

.support-chat-popup {
    position: absolute;
    bottom: 75px;
    right: 0;
    width: 380px;
    max-width: calc(100vw - 48px);
    background: white;
    border-radius: 20px;
    box-shadow: 0 10px 50px rgba(0, 0, 0, 0.2);
    opacity: 0;
    visibility: hidden;
    transform: translateY(20px) scale(0.95);
    transition: all 0.3s ease;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    z-index: 99999;
    max-height: 550px;
}

.support-chat-popup.active {
    opacity: 1;
    visibility: visible;
    transform: translateY(0) scale(1);
}

.support-chat-header {
    background: linear-gradient(135deg, #1EB53A 0%, #0099B5 100%);
    padding: 16px 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    color: white;
}

.support-chat-header-info {
    display: flex;
    align-items: center;
    gap: 12px;
}

.support-chat-avatar {
    width: 45px;
    height: 45px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
}

.support-chat-title {
    font-size: 16px;
    font-weight: 600;
}

.support-chat-subtitle {
    font-size: 13px;
    opacity: 0.9;
    display: flex;
    align-items: center;
    gap: 6px;
}

.support-online-dot {
    width: 8px;
    height: 8px;
    background: #4ade80;
    border-radius: 50%;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

.support-chat-minimize {
    background: rgba(255, 255, 255, 0.2);
    border: none;
    color: white;
    width: 32px;
    height: 32px;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    justify-content: center;
}

.support-chat-minimize:hover {
    background: rgba(255, 255, 255, 0.3);
}

.support-chat-guest-form {
    padding: 20px;
    background: #f8fafc;
    border-bottom: 1px solid #e5e7eb;
}

.support-chat-guest-form p {
    font-size: 14px;
    color: #6b7280;
    margin-bottom: 12px;
}

.support-chat-input-field {
    width: 100%;
    padding: 12px 14px;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    font-size: 14px;
    margin-bottom: 10px;
    transition: all 0.2s ease;
}

.support-chat-input-field:focus {
    outline: none;
    border-color: #1EB53A;
    box-shadow: 0 0 0 3px rgba(30, 181, 58, 0.1);
}

.support-chat-start-btn {
    width: 100%;
    padding: 12px;
    background: linear-gradient(135deg, #1EB53A 0%, #0099B5 100%);
    border: none;
    border-radius: 10px;
    color: white;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: all 0.2s ease;
}

.support-chat-start-btn:hover {
    opacity: 0.9;
    transform: translateY(-1px);
}

.support-chat-messages {
    flex: 1;
    padding: 20px;
    overflow-y: auto;
    min-height: 300px;
    max-height: 350px;
    background: #f8fafc;
}

.support-chat-welcome {
    text-align: center;
    padding: 30px 20px;
}

.support-chat-welcome-icon {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, rgba(30, 181, 58, 0.1) 0%, rgba(0, 153, 181, 0.1) 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 16px;
    font-size: 28px;
    color: #1EB53A;
}

.support-chat-welcome h4 {
    font-size: 18px;
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 8px;
}

.support-chat-welcome p {
    font-size: 14px;
    color: #6b7280;
    line-height: 1.5;
}

.support-message {
    margin-bottom: 12px;
    display: flex;
    flex-direction: column;
}

.support-message.user {
    align-items: flex-end;
}

.support-message.admin {
    align-items: flex-start;
}

.support-message-bubble {
    max-width: 85%;
    padding: 12px 16px;
    border-radius: 16px;
    font-size: 14px;
    line-height: 1.5;
    word-wrap: break-word;
}

.support-message.user .support-message-bubble {
    background: linear-gradient(135deg, #1EB53A 0%, #0099B5 100%);
    color: white;
    border-bottom-right-radius: 4px;
}

.support-message.admin .support-message-bubble {
    background: white;
    color: #1f2937;
    border-bottom-left-radius: 4px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.support-message-time {
    font-size: 11px;
    color: #9ca3af;
    margin-top: 4px;
    padding: 0 4px;
}

.support-chat-input-area {
    padding: 16px;
    background: white;
    border-top: 1px solid #e5e7eb;
    display: flex;
    gap: 10px;
}

.support-chat-input {
    flex: 1;
    padding: 12px 16px;
    border: 1px solid #e5e7eb;
    border-radius: 25px;
    font-size: 14px;
    transition: all 0.2s ease;
}

.support-chat-input:focus {
    outline: none;
    border-color: #1EB53A;
}

.support-chat-send {
    width: 44px;
    height: 44px;
    border: none;
    background: linear-gradient(135deg, #1EB53A 0%, #0099B5 100%);
    color: white;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
}

.support-chat-send:hover {
    transform: scale(1.05);
}

.support-chat-send:disabled {
    opacity: 0.5;
    cursor: not-allowed;
    transform: none;
}

.support-chat-footer {
    padding: 10px;
    text-align: center;
    font-size: 11px;
    color: #9ca3af;
    background: #f8fafc;
    border-top: 1px solid #e5e7eb;
}

.support-chat-typing {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 8px 14px;
    background: white;
    border-radius: 16px;
    border-bottom-left-radius: 4px;
    width: fit-content;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.support-chat-typing span {
    width: 8px;
    height: 8px;
    background: #9ca3af;
    border-radius: 50%;
    animation: typing 1.4s infinite both;
}

.support-chat-typing span:nth-child(2) { animation-delay: 0.2s; }
.support-chat-typing span:nth-child(3) { animation-delay: 0.4s; }

@keyframes typing {
    0%, 60%, 100% { transform: translateY(0); }
    30% { transform: translateY(-4px); }
}

/* Mobile responsiveness */
@media (max-width: 480px) {
    .support-chat-widget {
        bottom: 16px;
        right: 16px;
    }

    .support-chat-toggle {
        width: 54px;
        height: 54px;
        font-size: 22px;
    }

    .support-chat-popup {
        width: calc(100vw - 32px);
        right: -8px;
        bottom: 70px;
        max-height: 70vh;
    }

    .support-chat-messages {
        min-height: 250px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const widget = document.getElementById('supportChatWidget');
    const toggle = document.getElementById('supportChatToggle');
    const popup = document.getElementById('supportChatPopup');
    const minimize = document.getElementById('supportChatMinimize');
    const messages = document.getElementById('supportChatMessages');
    const input = document.getElementById('supportChatInput');
    const sendBtn = document.getElementById('supportChatSend');
    const badge = document.getElementById('supportChatBadge');
    const guestForm = document.getElementById('supportGuestForm');
    const iconDefault = document.getElementById('chatIconDefault');
    const iconClose = document.getElementById('chatIconClose');

    const apiBaseUrl = '{{ url("/api/support-chat") }}';
    let sessionId = null;
    let isLoggedIn = {{ auth()->check() ? 'true' : 'false' }};
    let guestInfo = { name: null, phone: null };
    let guestInfoSubmitted = false;
    let lastMessageId = 0;
    let pollInterval = null;
    let pendingMessage = null; // Kutilayotgan xabar
    let sessionInitialized = false;

    console.log('Support Chat Widget loaded, API base:', apiBaseUrl);

    // Initialize session
    function initSession() {
        return fetch(apiBaseUrl + '/init', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'
            },
            credentials: 'same-origin'
        })
        .then(res => {
            if (!res.ok) throw new Error('Init failed: ' + res.status);
            return res.json();
        })
        .then(data => {
            console.log('Session init response:', data);
            if (data.success) {
                sessionId = data.session_id;
                sessionInitialized = true;
                if (data.has_history) {
                    loadMessages();
                }
                if (data.user) {
                    guestInfo = data.user;
                    guestInfoSubmitted = true;
                }
                return true;
            }
            return false;
        })
        .catch(err => {
            console.error('Init error:', err);
            return false;
        });
    }

    // Load messages
    function loadMessages() {
        if (!sessionId) return;

        fetch(`${apiBaseUrl}/messages?after=${lastMessageId}`)
        .then(res => res.json())
        .then(data => {
            if (data.success && data.messages.length > 0) {
                // Remove welcome message if exists
                const welcome = messages.querySelector('.support-chat-welcome');
                if (welcome) welcome.remove();

                data.messages.forEach(msg => {
                    appendMessage(msg);
                    lastMessageId = Math.max(lastMessageId, msg.id);
                });
                scrollToBottom();
            }
        })
        .catch(err => console.error('Load messages error:', err));
    }

    // Append message to chat
    function appendMessage(msg) {
        const msgDiv = document.createElement('div');
        msgDiv.className = `support-message ${msg.is_from_admin ? 'admin' : 'user'}`;
        msgDiv.innerHTML = `
            <div class="support-message-bubble">${escapeHtml(msg.message)}</div>
            <div class="support-message-time">${msg.created_at}</div>
        `;
        messages.appendChild(msgDiv);
    }

    // Send message
    async function sendMessage(messageText = null) {
        const text = messageText || input.value.trim();
        if (!text) return;

        // Check if guest needs to submit info first
        if (!isLoggedIn && !guestInfoSubmitted) {
            pendingMessage = text; // Xabarni saqlab qo'yamiz
            guestForm.style.display = 'block';
            return;
        }

        // Session init qilinmagan bo'lsa, avval init qilamiz
        if (!sessionInitialized) {
            console.log('Session not initialized, initializing...');
            const success = await initSession();
            if (!success) {
                console.error('Failed to initialize session');
                alert('Xatolik yuz berdi. Sahifani yangilang.');
                return;
            }
        }

        sendBtn.disabled = true;
        input.value = '';
        pendingMessage = null;

        console.log('Sending message:', text);

        fetch(apiBaseUrl + '/send', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'
            },
            credentials: 'same-origin',
            body: JSON.stringify({
                message: text,
                guest_name: guestInfo.name,
                guest_phone: guestInfo.phone
            })
        })
        .then(res => {
            console.log('Send response status:', res.status);
            if (!res.ok) throw new Error('Send failed: ' + res.status);
            return res.json();
        })
        .then(data => {
            console.log('Send response data:', data);
            if (data.success) {
                // Remove welcome message
                const welcome = messages.querySelector('.support-chat-welcome');
                if (welcome) welcome.remove();

                appendMessage(data.message);
                lastMessageId = Math.max(lastMessageId, data.message.id);

                // Auto-reply
                if (data.auto_reply) {
                    setTimeout(() => {
                        appendMessage(data.auto_reply);
                        lastMessageId = Math.max(lastMessageId, data.auto_reply.id);
                        scrollToBottom();
                    }, 500);
                }

                scrollToBottom();
            } else {
                console.error('Send failed:', data);
                input.value = text; // Xabarni qaytaramiz
            }
        })
        .catch(err => {
            console.error('Send error:', err);
            input.value = text; // Xatolik bo'lsa xabarni qaytaramiz
        })
        .finally(() => {
            sendBtn.disabled = false;
            input.focus();
        });
    }

    // Submit guest info
    window.submitGuestInfo = function() {
        const name = document.getElementById('guestName').value.trim();
        const phone = document.getElementById('guestPhone').value.trim();

        if (!name) {
            document.getElementById('guestName').focus();
            return;
        }

        if (!phone) {
            document.getElementById('guestPhone').focus();
            return;
        }

        guestInfo = { name, phone };
        guestInfoSubmitted = true;
        guestForm.style.display = 'none';

        // Send pending message
        if (pendingMessage) {
            sendMessage(pendingMessage);
        } else {
            sendMessage();
        }
    };

    // Check for new messages
    function checkUnread() {
        fetch(apiBaseUrl + '/unread')
        .then(res => res.json())
        .then(data => {
            if (data.success && data.count > 0) {
                badge.textContent = data.count;
                badge.style.display = 'flex';

                // If popup is open, load new messages
                if (popup.classList.contains('active')) {
                    loadMessages();
                }
            } else {
                badge.style.display = 'none';
            }
        })
        .catch(() => {});
    }

    // Scroll to bottom
    function scrollToBottom() {
        messages.scrollTop = messages.scrollHeight;
    }

    // Escape HTML
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Toggle popup
    toggle.addEventListener('click', async function() {
        const isActive = popup.classList.toggle('active');
        toggle.classList.toggle('active', isActive);
        iconDefault.style.display = isActive ? 'none' : 'block';
        iconClose.style.display = isActive ? 'block' : 'none';

        if (isActive) {
            if (!sessionInitialized) {
                console.log('Initializing session on popup open...');
                await initSession();
            }
            input.focus();
            badge.style.display = 'none';

            // Start polling
            if (!pollInterval) {
                pollInterval = setInterval(checkUnread, 5000);
            }
        }
    });

    // Minimize
    minimize.addEventListener('click', function() {
        popup.classList.remove('active');
        toggle.classList.remove('active');
        iconDefault.style.display = 'block';
        iconClose.style.display = 'none';
    });

    // Send on click
    sendBtn.addEventListener('click', sendMessage);

    // Send on Enter
    input.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            sendMessage();
        }
    });

    // Guest form Enter key
    document.getElementById('guestPhone').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            submitGuestInfo();
        }
    });

    // Initial unread check
    setTimeout(checkUnread, 3000);
});
</script>
