@extends('layouts.dashboard-new')

@section('title', 'Chat')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-gradient-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-comments me-2"></i>Chat</h5>
                </div>
                <div class="card-body p-0">
                    <div class="row g-0" style="height: 70vh;">
                        <!-- Users List -->
                        <div class="col-md-4 border-end">
                            <div class="p-3 border-bottom">
                                <input type="text" id="searchUsers" class="form-control" placeholder="Qidirish...">
                            </div>
                            <div id="usersList" class="overflow-auto" style="height: calc(70vh - 60px);">
                                <div class="text-center py-5">
                                    <div class="spinner-border text-primary" role="status"></div>
                                    <p class="mt-2 text-muted">Yuklanmoqda...</p>
                                </div>
                            </div>
                        </div>

                        <!-- Chat Area -->
                        <div class="col-md-8 d-flex flex-column">
                            <div id="chatHeader" class="p-3 border-bottom bg-light d-none">
                                <h6 class="mb-0" id="chatUserName"></h6>
                            </div>
                            <div id="messagesArea" class="flex-grow-1 overflow-auto p-3 bg-light" style="height: 100%;">
                                <div class="text-center text-muted py-5">
                                    <i class="fas fa-comments fa-3x mb-3"></i>
                                    <p>Foydalanuvchini tanlang</p>
                                </div>
                            </div>
                            <div id="messageInput" class="p-3 border-top d-none">
                                <div class="input-group">
                                    <input type="text" id="messageText" class="form-control" placeholder="Xabar yozing...">
                                    <button class="btn btn-primary" onclick="sendMsg()">
                                        <i class="fas fa-paper-plane"></i> Yuborish
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.user-item {
    padding: 12px 15px;
    cursor: pointer;
    border-bottom: 1px solid #f0f0f0;
    transition: background 0.2s;
}
.user-item:hover {
    background: #f8f9fa;
}
.user-item.active {
    background: #e3f2fd;
}
.user-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
}
.message {
    margin-bottom: 15px;
    max-width: 70%;
}
.message.sent {
    margin-left: auto;
    text-align: right;
}
.message-bubble {
    display: inline-block;
    padding: 10px 15px;
    border-radius: 18px;
    word-wrap: break-word;
}
.message.sent .message-bubble {
    background: #007bff;
    color: white;
}
.message.received .message-bubble {
    background: #e9ecef;
    color: #333;
}
.message-time {
    font-size: 11px;
    color: #999;
    margin-top: 4px;
}
</style>

<script>
let users = [];
let currentConversationId = null;
let currentUserId = null;
let authUserId = {{ auth()->id() }};

// Load users
function loadUsers() {
    console.log('Loading users...');
    fetch('/api/chat-users-api', {
        credentials: 'same-origin',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(r => {
        console.log('Status:', r.status);
        if (!r.ok) throw new Error('HTTP ' + r.status);
        return r.json();
    })
    .then(data => {
        console.log('Users loaded:', data.length);
        users = data || [];
        renderUsers();
    })
    .catch(e => {
        console.error('Error:', e);
        document.getElementById('usersList').innerHTML = '<div class="p-3 text-danger">Xatolik: ' + e.message + '</div>';
    });
}

// Render users
function renderUsers() {
    const container = document.getElementById('usersList');
    if (users.length === 0) {
        container.innerHTML = '<div class="p-3 text-muted">Foydalanuvchilar yo\'q</div>';
        return;
    }

    let html = '';
    users.forEach(user => {
        html += `
            <div class="user-item d-flex align-items-center" onclick="openChat(${user.id}, '${user.name.replace(/'/g, "\\'")}')">
                <div class="user-avatar me-3">${user.name.charAt(0).toUpperCase()}</div>
                <div>
                    <div class="fw-bold">${user.name}</div>
                    <small class="text-muted">${user.email || ''}</small>
                </div>
            </div>
        `;
    });
    container.innerHTML = html;
}

// Open chat
function openChat(userId, userName) {
    currentUserId = userId;
    document.getElementById('chatUserName').textContent = userName;
    document.getElementById('chatHeader').classList.remove('d-none');
    document.getElementById('messageInput').classList.remove('d-none');

    // Highlight selected user
    document.querySelectorAll('.user-item').forEach(item => item.classList.remove('active'));
    event.currentTarget.classList.add('active');

    // Create conversation
    fetch('/chat/create', {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ user_id: userId })
    })
    .then(r => r.json())
    .then(data => {
        currentConversationId = data.conversation_id;
        loadMessages();
    })
    .catch(e => console.error('Error:', e));
}

// Load messages
function loadMessages() {
    if (!currentConversationId) return;

    fetch('/chat/' + currentConversationId + '/messages', {
        credentials: 'same-origin',
        headers: { 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(messages => {
        renderMessages(messages);
    })
    .catch(e => console.error('Error:', e));
}

// Render messages
function renderMessages(messages) {
    const container = document.getElementById('messagesArea');

    if (!messages || messages.length === 0) {
        container.innerHTML = '<div class="text-center text-muted py-5">Xabarlar yo\'q</div>';
        return;
    }

    let html = '';
    messages.forEach(msg => {
        const type = msg.user_id == authUserId ? 'sent' : 'received';
        html += `
            <div class="message ${type}">
                <div class="message-bubble">${escapeHtml(msg.message)}</div>
                <div class="message-time">${msg.created_at}</div>
            </div>
        `;
    });

    container.innerHTML = html;
    container.scrollTop = container.scrollHeight;
}

// Send message
function sendMsg() {
    const input = document.getElementById('messageText');
    const text = input.value.trim();

    if (!text || !currentConversationId) return;

    input.value = '';

    fetch('/chat/' + currentConversationId + '/send', {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ message: text })
    })
    .then(r => r.json())
    .then(() => {
        loadMessages();
    })
    .catch(e => console.error('Error:', e));
}

// Search users
document.addEventListener('DOMContentLoaded', function() {
    loadUsers();

    document.getElementById('searchUsers').addEventListener('input', function(e) {
        const query = e.target.value.toLowerCase();
        document.querySelectorAll('.user-item').forEach(item => {
            const name = item.textContent.toLowerCase();
            item.style.display = name.includes(query) ? '' : 'none';
        });
    });

    document.getElementById('messageText').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') sendMsg();
    });
});

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
</script>
@endsection
