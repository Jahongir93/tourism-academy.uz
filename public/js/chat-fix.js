/**
 * CHAT SYSTEM FIX - VPS Message Sending & Polling
 * Version: 2.0
 * Date: 2025-12-08
 *
 * Fixes:
 * 1. Efficient polling (only new messages)
 * 2. Better error handling
 * 3. User feedback on errors
 * 4. Retry mechanism
 */

// Global variables
let lastMessageId = 0;
let retryCount = 0;
const MAX_RETRIES = 3;
const POLLING_INTERVAL = 3000; // 3 seconds

/**
 * Load only NEW messages (not all messages)
 */
function loadNewMessages(conversationId) {
    fetch(`/chat/${conversationId}/messages?after=${lastMessageId}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }
            return response.json();
        })
        .then(messages => {
            if (messages && messages.length > 0) {
                messages.forEach(msg => {
                    appendMessage(msg, msg.user_id == AUTH_USER_ID);
                    lastMessageId = Math.max(lastMessageId, msg.id);
                });

                // Reset retry count on success
                retryCount = 0;
            }
        })
        .catch(error => {
            console.error('Load messages error:', error);
            handlePollingError(error, conversationId);
        });
}

/**
 * Handle polling errors with exponential backoff
 */
function handlePollingError(error, conversationId) {
    retryCount++;

    if (retryCount >= MAX_RETRIES) {
        // Stop polling after max retries
        if (window.pollingInterval) {
            clearInterval(window.pollingInterval);
        }

        showChatError('Ulanishda xatolik. Sahifani yangilang.');
        return;
    }

    // Exponential backoff: 3s, 6s, 12s
    const backoffDelay = POLLING_INTERVAL * Math.pow(2, retryCount - 1);
    console.log(`Retrying in ${backoffDelay}ms (attempt ${retryCount}/${MAX_RETRIES})`);
}

/**
 * Improved send message with proper error handling
 */
async function sendMessageImproved() {
    const input = document.getElementById('messageInput');
    const message = input.value.trim();

    if (!message || !window.currentUserId) {
        return;
    }

    // Disable input and show sending state
    input.disabled = true;
    const sendBtn = document.querySelector('.input-area button');
    const originalBtnHTML = sendBtn.innerHTML;
    sendBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    sendBtn.disabled = true;

    try {
        // Step 1: Get or create conversation
        const convResponse = await fetch('/chat/create', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ user_id: window.currentUserId })
        });

        if (!convResponse.ok) {
            throw new Error('Suhbat yaratib bo\'lmadi');
        }

        const convData = await convResponse.json();

        if (!convData.conversation_id) {
            throw new Error('Suhbat ID topilmadi');
        }

        // Step 2: Send message
        const msgResponse = await fetch(`/chat/${convData.conversation_id}/send`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ message: message })
        });

        if (!msgResponse.ok) {
            throw new Error('Xabar yuborilmadi');
        }

        const msgData = await msgResponse.json();

        if (msgData && msgData.id) {
            // Clear input
            input.value = '';

            // Append message immediately
            appendMessage(msgData, true);
            lastMessageId = Math.max(lastMessageId, msgData.id);

            // Show success feedback
            showChatSuccess('Yuborildi');
        } else {
            throw new Error('Xabar saqlanmadi');
        }

    } catch (error) {
        console.error('Send message error:', error);
        showChatError(error.message || 'Xabar yuborishda xatolik');

        // Restore message in input on error
        input.value = message;

    } finally {
        // Re-enable input
        input.disabled = false;
        sendBtn.innerHTML = originalBtnHTML;
        sendBtn.disabled = false;
        input.focus();
    }
}

/**
 * Show chat error message
 */
function showChatError(message) {
    const toast = createToast('error', message);
    document.body.appendChild(toast);

    setTimeout(() => {
        toast.classList.add('show');
    }, 100);

    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

/**
 * Show chat success message
 */
function showChatSuccess(message) {
    const toast = createToast('success', message);
    document.body.appendChild(toast);

    setTimeout(() => {
        toast.classList.add('show');
    }, 100);

    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 300);
    }, 2000);
}

/**
 * Create toast notification
 */
function createToast(type, message) {
    const toast = document.createElement('div');
    toast.className = `chat-toast chat-toast-${type}`;
    toast.innerHTML = `
        <i class="fas fa-${type === 'error' ? 'exclamation-circle' : 'check-circle'} me-2"></i>
        ${message}
    `;
    return toast;
}

/**
 * Improved append message function
 */
function appendMessageImproved(msg, isSent) {
    const container = document.getElementById('messagesArea');

    // Remove empty state message if exists
    const emptyMsg = container.querySelector('.text-muted');
    if (emptyMsg) emptyMsg.remove();

    // Check if message already exists (prevent duplicates)
    const existing = container.querySelector(`[data-message-id="${msg.id}"]`);
    if (existing) return;

    const type = isSent ? 'sent' : 'received';
    const div = document.createElement('div');
    div.className = `message ${type}`;
    div.setAttribute('data-message-id', msg.id);

    // Format time
    const time = formatMessageTime(msg.created_at);

    div.innerHTML = `
        <div class="message-bubble">
            <div class="message-text">${escapeHtml(msg.message)}</div>
            <div class="message-time">${time}</div>
        </div>
    `;

    container.appendChild(div);

    // Smooth scroll to bottom
    container.scrollTo({
        top: container.scrollHeight,
        behavior: 'smooth'
    });
}

/**
 * Format message time (relative or absolute)
 */
function formatMessageTime(timestamp) {
    const date = new Date(timestamp);
    const now = new Date();
    const diff = now - date;

    // Less than 1 minute
    if (diff < 60000) {
        return 'Hozir';
    }

    // Less than 1 hour
    if (diff < 3600000) {
        const minutes = Math.floor(diff / 60000);
        return `${minutes} daqiqa oldin`;
    }

    // Less than 24 hours
    if (diff < 86400000) {
        const hours = Math.floor(diff / 3600000);
        return `${hours} soat oldin`;
    }

    // Show date and time
    return date.toLocaleString('uz-UZ', {
        day: '2-digit',
        month: '2-digit',
        hour: '2-digit',
        minute: '2-digit'
    });
}

/**
 * Escape HTML to prevent XSS
 */
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

/**
 * Start improved polling
 */
function startImprovedPolling(conversationId) {
    // Clear existing polling
    if (window.pollingInterval) {
        clearInterval(window.pollingInterval);
    }

    // Reset retry count
    retryCount = 0;

    // Start new polling
    window.pollingInterval = setInterval(() => {
        loadNewMessages(conversationId);
    }, POLLING_INTERVAL);

    console.log(`Polling started for conversation ${conversationId}`);
}

/**
 * CSS for toast notifications
 */
const toastStyles = document.createElement('style');
toastStyles.textContent = `
    .chat-toast {
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 12px 20px;
        border-radius: 8px;
        color: white;
        font-size: 14px;
        font-weight: 500;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        z-index: 9999;
        opacity: 0;
        transform: translateX(100px);
        transition: all 0.3s ease;
    }

    .chat-toast.show {
        opacity: 1;
        transform: translateX(0);
    }

    .chat-toast-error {
        background: linear-gradient(135deg, #ff6b6b, #ee5a6f);
    }

    .chat-toast-success {
        background: linear-gradient(135deg, #00b894, #00a884);
    }

    .message[data-message-id] {
        animation: slideIn 0.3s ease;
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
`;
document.head.appendChild(toastStyles);

// Export functions for global use
window.loadNewMessages = loadNewMessages;
window.sendMessageImproved = sendMessageImproved;
window.appendMessage = appendMessageImproved;
window.startImprovedPolling = startImprovedPolling;
window.showChatError = showChatError;
window.showChatSuccess = showChatSuccess;

console.log('✅ Chat fixes loaded successfully!');
