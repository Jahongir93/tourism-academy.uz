@extends('layouts.dashboard-new')

@section('title', 'Real-time Chat')
@section('page-title', 'Real-time Suhbat')

@push('styles')
<style>
.chat-container {
    height: calc(100vh - 200px);
    display: flex;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 0 20px rgba(0,0,0,0.1);
}

.users-sidebar {
    width: 300px;
    background: white;
    border-right: 1px solid #e0e0e0;
    display: flex;
    flex-direction: column;
}

.users-header {
    padding: 20px;
    border-bottom: 1px solid #e0e0e0;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.users-search {
    padding: 15px;
    border-bottom: 1px solid #e0e0e0;
}

.users-search input {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 20px;
    outline: none;
}

.users-list {
    flex: 1;
    overflow-y: auto;
    padding: 10px;
}

.user-item {
    padding: 12px;
    margin-bottom: 8px;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s;
    display: flex;
    align-items: center;
    position: relative;
}

.user-item:hover {
    background: #f5f5f5;
}

.user-item.active {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
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
    margin-right: 12px;
    position: relative;
}

.online-indicator {
    width: 12px;
    height: 12px;
    background: #4caf50;
    border: 2px solid white;
    border-radius: 50%;
    position: absolute;
    bottom: 0;
    right: 0;
}

.user-info {
    flex: 1;
}

.user-name {
    font-weight: 600;
    margin-bottom: 2px;
}

.user-status {
    font-size: 12px;
    opacity: 0.7;
}

.chat-main {
    flex: 1;
    display: flex;
    flex-direction: column;
    background: #f8f9fa;
}

.chat-header {
    padding: 20px;
    background: white;
    border-bottom: 1px solid #e0e0e0;
    display: flex;
    align-items: center;
}

.chat-messages {
    flex: 1;
    overflow-y: auto;
    padding: 20px;
}

.message {
    margin-bottom: 15px;
    display: flex;
    animation: slideIn 0.3s ease-out;
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

.message.mine {
    justify-content: flex-end;
}

.message-bubble {
    max-width: 60%;
    padding: 12px 16px;
    border-radius: 18px;
    position: relative;
}

.message.mine .message-bubble {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-bottom-right-radius: 4px;
}

.message.other .message-bubble {
    background: white;
    border-bottom-left-radius: 4px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

.message-time {
    font-size: 11px;
    opacity: 0.7;
    margin-top: 4px;
}

.typing-indicator {
    padding: 10px 20px;
    color: #666;
    font-style: italic;
    font-size: 14px;
    display: none;
}

.typing-indicator.show {
    display: block;
}

.chat-input-container {
    padding: 20px;
    background: white;
    border-top: 1px solid #e0e0e0;
}

.chat-input-wrapper {
    display: flex;
    align-items: center;
    gap: 10px;
}

.chat-input {
    flex: 1;
    padding: 12px 20px;
    border: 2px solid #e0e0e0;
    border-radius: 25px;
    outline: none;
    font-size: 14px;
    transition: border 0.3s;
}

.chat-input:focus {
    border-color: #667eea;
}

.send-btn {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    color: white;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: transform 0.2s;
}

.send-btn:hover {
    transform: scale(1.1);
}

.send-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 100%;
    color: #999;
}

.empty-state i {
    font-size: 64px;
    margin-bottom: 20px;
}

.connection-status {
    padding: 8px 15px;
    border-radius: 20px;
    font-size: 12px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.connection-status.connected {
    background: #e8f5e9;
    color: #2e7d32;
}

.connection-status.disconnected {
    background: #ffebee;
    color: #c62828;
}

.connection-status.connecting {
    background: #fff3e0;
    color: #ef6c00;
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="mb-3">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Real-time Chat</h4>
            <div id="connectionStatus" class="connection-status connecting">
                <i class="fas fa-circle-notch fa-spin"></i>
                <span>Ulanmoqda...</span>
            </div>
        </div>
    </div>

    <div class="chat-container">
        <!-- Users Sidebar -->
        <div class="users-sidebar">
            <div class="users-header">
                <h5 class="mb-1">Suhbatlar</h5>
                <small>Onlayn foydalanuvchilar</small>
            </div>
            <div class="users-search">
                <input type="text" id="userSearch" placeholder="Qidirish...">
            </div>
            <div class="users-list" id="usersList">
                <div class="text-center text-muted py-4">
                    <i class="fas fa-spinner fa-spin"></i>
                    <p class="mt-2">Yuklanmoqda...</p>
                </div>
            </div>
        </div>

        <!-- Chat Main -->
        <div class="chat-main">
            <div id="chatEmpty" class="empty-state">
                <i class="fas fa-comments"></i>
                <h5>Suhbatni boshlash uchun foydalanuvchini tanlang</h5>
                <p class="text-muted">Chap tarafdan foydalanuvchini tanlang</p>
            </div>

            <div id="chatActive" style="display: none; height: 100%; display: flex; flex-direction: column;">
                <div class="chat-header">
                    <div class="user-avatar" id="activeUserAvatar"></div>
                    <div class="flex-grow-1">
                        <h6 class="mb-0" id="activeUserName"></h6>
                        <small class="text-muted" id="activeUserStatus">Onlayn</small>
                    </div>
                </div>

                <div class="chat-messages" id="chatMessages">
                    <!-- Messages will be appended here -->
                </div>

                <div class="typing-indicator" id="typingIndicator">
                    <span id="typingUserName"></span> yozmoqda...
                </div>

                <div class="chat-input-container">
                    <div class="chat-input-wrapper">
                        <input
                            type="text"
                            class="chat-input"
                            id="messageInput"
                            placeholder="Xabar yozing..."
                            autocomplete="off"
                        >
                        <button class="send-btn" id="sendBtn" disabled>
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- Pusher/Echo -->
<script src="https://cdn.jsdelivr.net/npm/pusher-js@8.3.0/dist/web/pusher.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.15.3/dist/echo.iife.js"></script>

<script>
// Configuration
const PUSHER_CONFIG = {
    key: '{{ env('PUSHER_APP_KEY', 'local-app-key') }}',
    cluster: '{{ env('PUSHER_APP_CLUSTER', 'mt1') }}',
    wsHost: '{{ env('PUSHER_HOST', '127.0.0.1') }}',
    wsPort: {{ env('PUSHER_PORT', 6001) }},
    wssPort: {{ env('PUSHER_PORT', 6001) }},
    forceTLS: {{ env('PUSHER_SCHEME', 'http') === 'https' ? 'true' : 'false' }},
    encrypted: {{ env('PUSHER_SCHEME', 'http') === 'https' ? 'true' : 'false' }},
    disableStats: true,
    enabledTransports: ['ws', 'wss'],
    authEndpoint: '/broadcasting/auth',
    auth: {
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    }
};

// State
let currentUser = {{ auth()->id() }};
let currentUserName = '{{ auth()->user()->name }}';
let activeConversationId = null;
let activeUserId = null;
let users = [];
let typingTimeout = null;
let echoInstance = null;

// Initialize Echo
function initEcho() {
    try {
        echoInstance = new Echo({
            broadcaster: 'pusher',
            ...PUSHER_CONFIG
        });

        // Connection status
        echoInstance.connector.pusher.connection.bind('connected', () => {
            updateConnectionStatus('connected', 'Ulangan');
            console.log('✅ Echo connected');
        });

        echoInstance.connector.pusher.connection.bind('disconnected', () => {
            updateConnectionStatus('disconnected', 'Uzilgan');
            console.log('❌ Echo disconnected');
        });

        echoInstance.connector.pusher.connection.bind('connecting', () => {
            updateConnectionStatus('connecting', 'Ulanmoqda...');
            console.log('🔄 Echo connecting...');
        });

        // Join presence channel for online users
        echoInstance.join('chat.online')
            .here((onlineUsers) => {
                console.log('Online users:', onlineUsers);
                updateOnlineUsers(onlineUsers);
            })
            .joining((user) => {
                console.log('User joined:', user);
                markUserOnline(user.id);
            })
            .leaving((user) => {
                console.log('User left:', user);
                markUserOffline(user.id);
            });

    } catch (error) {
        console.error('Echo initialization error:', error);
        updateConnectionStatus('disconnected', 'Xato: ' + error.message);
    }
}

// Update connection status
function updateConnectionStatus(status, text) {
    const statusEl = document.getElementById('connectionStatus');
    statusEl.className = `connection-status ${status}`;

    let icon = 'fa-circle-notch fa-spin';
    if (status === 'connected') icon = 'fa-check-circle';
    if (status === 'disconnected') icon = 'fa-times-circle';

    statusEl.innerHTML = `<i class="fas ${icon}"></i><span>${text}</span>`;
}

// Update online users
function updateOnlineUsers(onlineUsers) {
    // Mark users as online/offline
    onlineUsers.forEach(user => {
        markUserOnline(user.id);
    });
}

// Mark user as online
function markUserOnline(userId) {
    const userEl = document.querySelector(`[data-user-id="${userId}"]`);
    if (userEl) {
        const indicator = userEl.querySelector('.online-indicator');
        if (!indicator) {
            const avatar = userEl.querySelector('.user-avatar');
            avatar.insertAdjacentHTML('beforeend', '<span class="online-indicator"></span>');
        }
    }
}

// Mark user as offline
function markUserOffline(userId) {
    const userEl = document.querySelector(`[data-user-id="${userId}"]`);
    if (userEl) {
        const indicator = userEl.querySelector('.online-indicator');
        if (indicator) indicator.remove();
    }
}

// Load users
async function loadUsers() {
    try {
        const response = await fetch('/api/chat-users-api', {
            credentials: 'same-origin',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        const data = await response.json();
        if (data.success && data.users) {
            users = data.users;
            renderUsers();
        }
    } catch (error) {
        console.error('Load users error:', error);
    }
}

// Render users list
function renderUsers() {
    const container = document.getElementById('usersList');

    if (users.length === 0) {
        container.innerHTML = '<div class="text-center text-muted py-4">Foydalanuvchilar topilmadi</div>';
        return;
    }

    container.innerHTML = users.map(user => `
        <div class="user-item" data-user-id="${user.id}" onclick="selectUser(${user.id}, '${user.name}', '${user.avatar}')">
            <div class="user-avatar">
                ${user.avatar}
            </div>
            <div class="user-info">
                <div class="user-name">${escapeHtml(user.name)}</div>
                <div class="user-status">Suhbatni boshlash</div>
            </div>
        </div>
    `).join('');
}

// Select user and start conversation
async function selectUser(userId, userName, avatar) {
    try {
        // Create or get conversation
        const response = await fetch('/api/chat-create-conversation', {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ user_id: userId })
        });

        const data = await response.json();
        if (data.success) {
            activeConversationId = data.conversation_id;
            activeUserId = userId;

            // Update UI
            document.getElementById('chatEmpty').style.display = 'none';
            document.getElementById('chatActive').style.display = 'flex';
            document.getElementById('activeUserName').textContent = userName;
            document.getElementById('activeUserAvatar').textContent = avatar;
            document.getElementById('sendBtn').disabled = false;

            // Mark user as active
            document.querySelectorAll('.user-item').forEach(el => el.classList.remove('active'));
            document.querySelector(`[data-user-id="${userId}"]`).classList.add('active');

            // Load messages
            await loadMessages();

            // Subscribe to conversation channel
            subscribeToConversation(activeConversationId);
        }
    } catch (error) {
        console.error('Select user error:', error);
    }
}

// Subscribe to conversation
function subscribeToConversation(conversationId) {
    if (!echoInstance) return;

    // Leave previous channel
    if (window.currentChannel) {
        echoInstance.leave(`chat.conversation.${window.currentConversationId}`);
    }

    // Join new channel
    const channel = echoInstance.private(`chat.conversation.${conversationId}`);

    // Listen for new messages
    channel.listen('.message.new', (e) => {
        console.log('New message received:', e);
        if (e.user_id !== currentUser) {
            appendMessage(e, false);
            scrollToBottom();
        }
    });

    // Listen for typing
    channel.listen('.user.typing', (e) => {
        if (e.user_id !== currentUser) {
            showTypingIndicator(e.user_name, e.is_typing);
        }
    });

    window.currentChannel = channel;
    window.currentConversationId = conversationId;
}

// Load messages
async function loadMessages() {
    try {
        const response = await fetch(`/api/chat-messages-api?conversation_id=${activeConversationId}`, {
            credentials: 'same-origin',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        const data = await response.json();
        if (data.success && data.messages) {
            renderMessages(data.messages);
            scrollToBottom();
        }
    } catch (error) {
        console.error('Load messages error:', error);
    }
}

// Render messages
function renderMessages(messages) {
    const container = document.getElementById('chatMessages');
    container.innerHTML = messages.map(msg => createMessageHTML(msg)).join('');
}

// Create message HTML
function createMessageHTML(msg) {
    const isMine = msg.user_id === currentUser || msg.is_mine;
    const messageClass = isMine ? 'mine' : 'other';

    return `
        <div class="message ${messageClass}">
            <div class="message-bubble">
                <div>${escapeHtml(msg.message)}</div>
                <div class="message-time">${msg.formatted_time || msg.created_at}</div>
            </div>
        </div>
    `;
}

// Append new message
function appendMessage(msg, isMine) {
    const container = document.getElementById('chatMessages');
    container.insertAdjacentHTML('beforeend', createMessageHTML({...msg, is_mine: isMine}));
}

// Send message
async function sendMessage() {
    const input = document.getElementById('messageInput');
    const message = input.value.trim();

    if (!message || !activeConversationId) return;

    try {
        const response = await fetch('/api/chat-send-api', {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                conversation_id: activeConversationId,
                message: message
            })
        });

        const data = await response.json();
        if (data.success) {
            // Add to UI immediately
            appendMessage(data.message, true);
            input.value = '';
            scrollToBottom();

            // Stop typing indicator
            sendTypingIndicator(false);
        }
    } catch (error) {
        console.error('Send message error:', error);
    }
}

// Send typing indicator
async function sendTypingIndicator(isTyping) {
    if (!activeConversationId) return;

    try {
        await fetch('/api/chat-typing', {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                conversation_id: activeConversationId,
                is_typing: isTyping
            })
        });
    } catch (error) {
        console.error('Send typing error:', error);
    }
}

// Show typing indicator
function showTypingIndicator(userName, isTyping) {
    const indicator = document.getElementById('typingIndicator');
    const nameEl = document.getElementById('typingUserName');

    if (isTyping) {
        nameEl.textContent = userName;
        indicator.classList.add('show');

        // Auto-hide after 3 seconds
        clearTimeout(window.typingTimer);
        window.typingTimer = setTimeout(() => {
            indicator.classList.remove('show');
        }, 3000);
    } else {
        indicator.classList.remove('show');
    }
}

// Scroll to bottom
function scrollToBottom() {
    const container = document.getElementById('chatMessages');
    container.scrollTop = container.scrollHeight;
}

// Escape HTML
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Event Listeners
document.addEventListener('DOMContentLoaded', () => {
    // Initialize Echo
    initEcho();

    // Load users
    loadUsers();

    // Message input
    const input = document.getElementById('messageInput');
    const sendBtn = document.getElementById('sendBtn');

    input.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            sendMessage();
        }
    });

    input.addEventListener('input', () => {
        // Send typing indicator
        sendTypingIndicator(true);

        // Clear previous timeout
        clearTimeout(typingTimeout);

        // Set new timeout to stop typing
        typingTimeout = setTimeout(() => {
            sendTypingIndicator(false);
        }, 1000);
    });

    sendBtn.addEventListener('click', sendMessage);

    // User search
    document.getElementById('userSearch').addEventListener('input', (e) => {
        const query = e.target.value.toLowerCase();
        document.querySelectorAll('.user-item').forEach(item => {
            const name = item.querySelector('.user-name').textContent.toLowerCase();
            item.style.display = name.includes(query) ? 'flex' : 'none';
        });
    });
});

// Cleanup on page unload
window.addEventListener('beforeunload', () => {
    if (echoInstance) {
        echoInstance.disconnect();
    }
});
</script>
@endpush
@endsection
