@extends('layouts.dashboard-new')

@section('title', 'Chat - Xabarlar')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="card shadow-lg border-0">
        <div class="card-header bg-gradient-primary text-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="mb-0"><i class="fas fa-comments me-2"></i>Chat - Xabarlar</h4>
                <button class="btn btn-sm btn-light" onclick="location.reload()">
                    <i class="fas fa-sync-alt"></i> Yangilash
                </button>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="row g-0" style="height: 75vh;">
                <!-- USERS LIST -->
                <div class="col-md-4 col-lg-3 border-end bg-light">
                    <div class="p-3 border-bottom bg-white">
                        <input type="text" id="searchInput" class="form-control" placeholder="🔍 Qidirish...">
                    </div>
                    <div id="usersList" class="overflow-auto" style="height: calc(75vh - 70px);">
                        <div class="text-center py-5">
                            <div class="spinner-border text-primary"></div>
                            <p class="mt-2 text-muted">Yuklanmoqda...</p>
                        </div>
                    </div>
                </div>

                <!-- CHAT AREA -->
                <div class="col-md-8 col-lg-9 d-flex flex-column">
                    <!-- Chat Header -->
                    <div id="chatHeader" class="p-3 border-bottom bg-white d-none">
                        <div class="d-flex align-items-center">
                            <div class="chat-avatar me-3" id="chatAvatar">A</div>
                            <div>
                                <h6 class="mb-0" id="chatUserName">Foydalanuvchi</h6>
                                <small class="text-muted">Onlayn</small>
                            </div>
                        </div>
                    </div>

                    <!-- Messages -->
                    <div id="messagesArea" class="flex-grow-1 overflow-auto p-4" style="background: #f5f7fa;">
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-comments fa-4x mb-3 opacity-25"></i>
                            <h5>Suhbatni boshlash uchun foydalanuvchini tanlang</h5>
                        </div>
                    </div>

                    <!-- Message Input -->
                    <div id="messageInput" class="p-3 border-top bg-white d-none">
                        <form id="messageForm" onsubmit="sendMessage(event)">
                            <div class="input-group">
                                <input type="text" id="messageText" class="form-control"
                                       placeholder="Xabar yozing..." required>
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="fas fa-paper-plane me-1"></i> Yuborish
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* User Item Styles */
.user-item {
    padding: 15px;
    cursor: pointer;
    border-bottom: 1px solid #e0e0e0;
    transition: all 0.2s;
    background: white;
}
.user-item:hover {
    background: #f8f9fa;
}
.user-item.active {
    background: #e3f2fd;
    border-left: 4px solid #2196F3;
}
.user-avatar {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 18px;
    flex-shrink: 0;
}
.chat-avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 20px;
}

/* Message Styles */
.message {
    display: flex;
    margin-bottom: 15px;
    animation: fadeIn 0.3s;
}
.message.sent {
    justify-content: flex-end;
}
.message-bubble {
    max-width: 70%;
    padding: 12px 16px;
    border-radius: 18px;
    word-wrap: break-word;
    position: relative;
}
.message.sent .message-bubble {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-bottom-right-radius: 4px;
}
.message.received .message-bubble {
    background: white;
    color: #333;
    border: 1px solid #e0e0e0;
    border-bottom-left-radius: 4px;
}
.message-time {
    font-size: 11px;
    opacity: 0.7;
    margin-top: 4px;
}
.message.sent .message-time {
    text-align: right;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Loading */
.loading-msg {
    text-align: center;
    padding: 20px;
    color: #999;
}

/* Error */
.error-msg {
    background: #ffebee;
    color: #c62828;
    padding: 12px;
    border-radius: 8px;
    margin: 10px;
    text-align: center;
}
</style>

<script>
// Global Variables
let users = [];
let currentConversationId = null;
let currentUserId = null;
let currentUserName = '';
const authUserId = {{ auth()->id() ?? 0 }};
const csrfToken = '{{ csrf_token() }}';

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    console.log('Chat initialized');
    loadUsers();

    // Search functionality
    document.getElementById('searchInput').addEventListener('input', function(e) {
        const query = e.target.value.toLowerCase();
        document.querySelectorAll('.user-item').forEach(item => {
            const name = item.dataset.name.toLowerCase();
            item.style.display = name.includes(query) ? 'flex' : 'none';
        });
    });
});

// Load Users
async function loadUsers() {
    try {
        const apiUrl = '/api/chat-users-api';
        console.log('Loading users from:', apiUrl);

        const response = await fetch(apiUrl, {
            method: 'GET',
            credentials: 'same-origin',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        console.log('Response status:', response.status);

        if (!response.ok) {
            const text = await response.text();
            console.error('Error response:', text.substring(0, 500));
            throw new Error(`HTTP ${response.status}`);
        }

        const data = await response.json();
        console.log('Users loaded:', data);

        if (data.success && data.users) {
            users = data.users;
            renderUsers();
        } else {
            throw new Error('Invalid response format');
        }

    } catch (error) {
        console.error('Load users error:', error);
        document.getElementById('usersList').innerHTML = `
            <div class="error-msg">
                <i class="fas fa-exclamation-triangle"></i>
                <div>Xatolik: ${error.message}</div>
                <button class="btn btn-sm btn-primary mt-2" onclick="loadUsers()">Qayta urinish</button>
            </div>
        `;
    }
}

// Render Users
function renderUsers() {
    const container = document.getElementById('usersList');

    if (users.length === 0) {
        container.innerHTML = '<div class="loading-msg">Foydalanuvchilar topilmadi</div>';
        return;
    }

    container.innerHTML = users.map(user => `
        <div class="user-item d-flex align-items-center"
             data-name="${user.name}"
             onclick="openChat(${user.id}, '${user.name.replace(/'/g, "\\'")}', '${user.avatar}')">
            <div class="user-avatar me-3">${user.avatar}</div>
            <div class="flex-grow-1">
                <div class="fw-bold">${escapeHtml(user.name)}</div>
                <small class="text-muted">${escapeHtml(user.email)}</small>
            </div>
        </div>
    `).join('');
}

// Open Chat
async function openChat(userId, userName, avatar) {
    console.log('Opening chat with user:', userId);

    currentUserId = userId;
    currentUserName = userName;

    // Update UI
    document.getElementById('chatUserName').textContent = userName;
    document.getElementById('chatAvatar').textContent = avatar;
    document.getElementById('chatHeader').classList.remove('d-none');
    document.getElementById('messageInput').classList.remove('d-none');

    // Highlight selected user
    document.querySelectorAll('.user-item').forEach(item => item.classList.remove('active'));
    event.currentTarget.classList.add('active');

    // Show loading
    document.getElementById('messagesArea').innerHTML = '<div class="loading-msg"><div class="spinner-border"></div></div>';

    try {
        // Create/get conversation
        const response = await fetch('/api/chat-create-conversation', {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ user_id: userId })
        });

        const data = await response.json();

        if (data.success) {
            currentConversationId = data.conversation_id;
            await loadMessages();
        } else {
            throw new Error(data.error || 'Failed to create conversation');
        }

    } catch (error) {
        console.error('Open chat error:', error);
        document.getElementById('messagesArea').innerHTML = `
            <div class="error-msg">Xatolik: ${error.message}</div>
        `;
    }
}

// Load Messages
async function loadMessages() {
    if (!currentConversationId) return;

    try {
        const response = await fetch(`/api/chat-messages-api?conversation_id=${currentConversationId}`, {
            credentials: 'same-origin',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        const data = await response.json();

        if (data.success) {
            renderMessages(data.messages);
        } else {
            throw new Error(data.error || 'Failed to load messages');
        }

    } catch (error) {
        console.error('Load messages error:', error);
        document.getElementById('messagesArea').innerHTML = `
            <div class="error-msg">Xatolik: ${error.message}</div>
        `;
    }
}

// Render Messages
function renderMessages(messages) {
    const container = document.getElementById('messagesArea');

    if (!messages || messages.length === 0) {
        container.innerHTML = '<div class="loading-msg">Xabarlar yo\'q. Birinchi xabarni yozing!</div>';
        return;
    }

    container.innerHTML = messages.map(msg => {
        const type = msg.is_mine ? 'sent' : 'received';
        return `
            <div class="message ${type}">
                <div class="message-bubble">
                    <div>${escapeHtml(msg.message)}</div>
                    <div class="message-time">${msg.created_at}</div>
                </div>
            </div>
        `;
    }).join('');

    // Scroll to bottom
    container.scrollTop = container.scrollHeight;
}

// Send Message
async function sendMessage(event) {
    event.preventDefault();

    const input = document.getElementById('messageText');
    const text = input.value.trim();

    if (!text || !currentConversationId) return;

    // Clear input immediately
    input.value = '';

    try {
        const response = await fetch('/api/chat-send-api', {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                conversation_id: currentConversationId,
                message: text
            })
        });

        const data = await response.json();

        if (data.success) {
            // Reload messages to show new one
            await loadMessages();
        } else {
            throw new Error(data.error || 'Failed to send message');
        }

    } catch (error) {
        console.error('Send message error:', error);
        alert('Xabar yuborishda xatolik: ' + error.message);
        input.value = text; // Restore text
    }
}

// Utility function
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Error logging
window.addEventListener('error', function(e) {
    if (e.message.includes('Chart is not defined')) {
        e.preventDefault();
        return;
    }

    fetch('/api/log-frontend-error', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            message: e.message,
            url: e.filename,
            line: e.lineno
        })
    }).catch(() => {});
});
</script>
@endsection
