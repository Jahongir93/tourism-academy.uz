@extends('layouts.dashboard-new')

@section('content')
<div class="chat-page-container">
    <div class="row g-0 h-100">
        <!-- Members Sidebar (Hidden on mobile) -->
        <div class="col-lg-3 d-none d-lg-block members-sidebar">
            <div class="sidebar-header">
                <h6 class="mb-0">
                    <i class="fas fa-users me-2"></i>A'zolar ({{ $members->count() }})
                </h6>
            </div>
            <div class="members-list">
                @foreach($members as $member)
                <div class="member-item">
                    <div class="member-avatar {{ $member->pivot->last_seen_at && $member->pivot->last_seen_at->diffInMinutes(now()) < 5 ? 'online' : '' }}">
                        {{ strtoupper(substr($member->name, 0, 1)) }}
                    </div>
                    <div class="member-info">
                        <div class="member-name">
                            {{ $member->name }}
                            @if($member->pivot->role === 'admin')
                            <span class="badge bg-warning ms-1">Admin</span>
                            @elseif($member->pivot->role === 'moderator')
                            <span class="badge bg-info ms-1">Mod</span>
                            @endif
                        </div>
                        <div class="member-status">
                            @if($member->pivot->last_seen_at && $member->pivot->last_seen_at->diffInMinutes(now()) < 5)
                            <span class="text-success"><i class="fas fa-circle me-1" style="font-size: 8px;"></i>Online</span>
                            @else
                            <span class="text-muted">{{ $member->pivot->last_seen_at ? $member->pivot->last_seen_at->diffForHumans() : 'Noma\'lum' }}</span>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Chat Main Area -->
        <div class="col-12 col-lg-9 chat-main">
            <!-- Chat Header -->
            <div class="chat-header">
                <div class="d-flex align-items-center">
                    <a href="{{ route('chat.index') }}" class="btn btn-link text-white me-2 d-lg-none">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <a href="{{ route('chat.index') }}" class="btn btn-link text-white me-3 d-none d-lg-inline-block">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <div class="room-avatar">
                        <i class="fas {{ $room->icon ?? 'fa-comments' }}"></i>
                    </div>
                    <div class="room-info ms-3">
                        <h5 class="room-name mb-0">{{ $room->name }}</h5>
                        <small class="room-members">
                            <i class="fas fa-users me-1"></i>{{ $members->count() }} a'zo
                            <span class="mx-1">|</span>
                            <span class="online-count">
                                {{ $members->filter(fn($m) => $m->pivot->last_seen_at && $m->pivot->last_seen_at->diffInMinutes(now()) < 5)->count() }} online
                            </span>
                        </small>
                    </div>
                </div>
                <div class="header-actions">
                    <button class="btn btn-link text-white" onclick="toggleMembers()" title="A'zolar">
                        <i class="fas fa-users"></i>
                    </button>
                    <button class="btn btn-link text-white" onclick="leaveRoom()" title="Xonadan chiqish">
                        <i class="fas fa-sign-out-alt"></i>
                    </button>
                </div>
            </div>

            <!-- Messages Container -->
            <div class="messages-container" id="messagesContainer">
                @if($messages->isEmpty())
                <div class="empty-messages">
                    <div class="empty-icon">
                        <i class="fas fa-comments"></i>
                    </div>
                    <h5>Hali xabarlar yo'q</h5>
                    <p class="text-muted">Birinchi bo'lib xabar yuboring!</p>
                </div>
                @else
                    @php
                        $previousDate = null;
                    @endphp
                    @foreach($messages as $message)
                        @php
                            $currentDate = $message->created_at->format('Y-m-d');
                            $showDateDivider = $previousDate !== $currentDate;
                            $previousDate = $currentDate;
                        @endphp

                        @if($showDateDivider)
                        <div class="date-divider">
                            <span>{{ $message->created_at->isToday() ? 'Bugun' : ($message->created_at->isYesterday() ? 'Kecha' : $message->created_at->format('d M Y')) }}</span>
                        </div>
                        @endif

                        <div class="message-wrapper {{ $message->user_id == auth()->id() ? 'own' : '' }}" data-message-id="{{ $message->id }}">
                            @if($message->user_id != auth()->id())
                            <div class="message-avatar">
                                {{ strtoupper(substr($message->user->name, 0, 1)) }}
                            </div>
                            @endif
                            <div class="message-bubble {{ $message->user_id == auth()->id() ? 'own' : 'other' }}">
                                @if($message->user_id != auth()->id())
                                <div class="message-sender">{{ $message->user->name }}</div>
                                @endif

                                @if($message->parent)
                                <div class="reply-preview">
                                    <div class="reply-sender">{{ $message->parent->user->name }}</div>
                                    <div class="reply-text">{{ Str::limit($message->parent->message, 50) }}</div>
                                </div>
                                @endif

                                <div class="message-text">{{ $message->message }}</div>
                                <div class="message-meta">
                                    <span class="message-time">{{ $message->created_at->format('H:i') }}</span>
                                    @if($message->user_id == auth()->id())
                                    <i class="fas fa-check-double ms-1 {{ $message->is_read ? 'text-info' : '' }}"></i>
                                    @endif
                                </div>

                                <!-- Reactions -->
                                @if($message->reactions && $message->reactions->count() > 0)
                                <div class="message-reactions">
                                    @foreach($message->getReactionSummary() as $emoji => $data)
                                    <button class="reaction-btn {{ $data['reacted'] ? 'active' : '' }}"
                                            onclick="toggleReaction({{ $message->id }}, '{{ $emoji }}')">
                                        {{ $emoji }} {{ $data['count'] }}
                                    </button>
                                    @endforeach
                                </div>
                                @endif
                            </div>

                            <!-- Message Actions -->
                            <div class="message-actions">
                                <button onclick="replyToMessage({{ $message->id }}, '{{ addslashes($message->user->name) }}', '{{ addslashes(Str::limit($message->message, 30)) }}')" title="Javob berish">
                                    <i class="fas fa-reply"></i>
                                </button>
                                <button onclick="showReactionPicker({{ $message->id }})" title="Reaksiya">
                                    <i class="far fa-smile"></i>
                                </button>
                                @if($message->user_id == auth()->id() || $room->isModerator())
                                <button onclick="deleteMessage({{ $message->id }})" title="O'chirish" class="text-danger">
                                    <i class="fas fa-trash"></i>
                                </button>
                                @endif
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>

            <!-- Reply Preview -->
            <div class="reply-indicator" id="replyIndicator" style="display: none;">
                <div class="reply-content">
                    <i class="fas fa-reply me-2"></i>
                    <span id="replyToName"></span>: <span id="replyToText"></span>
                </div>
                <button onclick="cancelReply()" class="cancel-reply">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- Message Input -->
            <div class="message-input-area">
                <button class="attach-btn" onclick="toggleAttachMenu()">
                    <i class="fas fa-plus"></i>
                </button>
                <div class="input-wrapper">
                    <input type="text"
                           id="messageInput"
                           class="form-control"
                           placeholder="Xabar yozing..."
                           autocomplete="off"
                           onkeypress="if(event.key === 'Enter' && !event.shiftKey) { sendMessage(); event.preventDefault(); }">
                    <button class="emoji-btn" onclick="toggleEmojiPicker()">
                        <i class="far fa-smile"></i>
                    </button>
                </div>
                <button class="send-btn" id="sendBtn" onclick="sendMessage()">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Members Panel -->
    <div class="mobile-members-panel" id="mobileMembersPanel">
        <div class="panel-header">
            <h6 class="mb-0">A'zolar ({{ $members->count() }})</h6>
            <button onclick="toggleMembers()" class="btn btn-link text-dark">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="members-list">
            @foreach($members as $member)
            <div class="member-item">
                <div class="member-avatar {{ $member->pivot->last_seen_at && $member->pivot->last_seen_at->diffInMinutes(now()) < 5 ? 'online' : '' }}">
                    {{ strtoupper(substr($member->name, 0, 1)) }}
                </div>
                <div class="member-info">
                    <div class="member-name">{{ $member->name }}</div>
                    <div class="member-status">
                        @if($member->pivot->last_seen_at && $member->pivot->last_seen_at->diffInMinutes(now()) < 5)
                        <span class="text-success">Online</span>
                        @else
                        <span class="text-muted">{{ $member->pivot->last_seen_at ? $member->pivot->last_seen_at->diffForHumans() : '' }}</span>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Reaction Picker -->
    <div class="reaction-picker" id="reactionPicker" style="display: none;">
        <button onclick="addReaction('like')">Like</button>
        <button onclick="addReaction('heart')">Heart</button>
        <button onclick="addReaction('laugh')">Laugh</button>
        <button onclick="addReaction('wow')">Wow</button>
        <button onclick="addReaction('sad')">Sad</button>
        <button onclick="addReaction('angry')">Angry</button>
    </div>
</div>

<style>
.chat-page-container {
    height: calc(100vh - 70px);
    display: flex;
    flex-direction: column;
    background: #f0f2f5;
    margin: -1.5rem;
}

/* Members Sidebar */
.members-sidebar {
    background: white;
    border-right: 1px solid #e5e7eb;
    height: 100%;
    display: flex;
    flex-direction: column;
}

.sidebar-header {
    padding: 1rem 1.25rem;
    background: linear-gradient(135deg, #00b894 0%, #00cec9 100%);
    color: white;
    font-weight: 600;
}

.members-list {
    flex: 1;
    overflow-y: auto;
    padding: 0.5rem;
}

.member-item {
    display: flex;
    align-items: center;
    padding: 0.75rem;
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.2s ease;
}

.member-item:hover {
    background: #f8f9fa;
}

.member-avatar {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, #00b894 0%, #00cec9 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
    font-size: 0.9rem;
    position: relative;
    flex-shrink: 0;
}

.member-avatar.online::after {
    content: '';
    position: absolute;
    bottom: 2px;
    right: 2px;
    width: 10px;
    height: 10px;
    background: #10b981;
    border: 2px solid white;
    border-radius: 50%;
}

.member-info {
    margin-left: 0.75rem;
    min-width: 0;
}

.member-name {
    font-weight: 500;
    color: #1f2937;
    font-size: 0.9rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.member-status {
    font-size: 0.75rem;
}

/* Chat Main Area */
.chat-main {
    display: flex;
    flex-direction: column;
    height: 100%;
    background: #efeae2;
    background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23d5d0c7' fill-opacity='0.4'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
}

/* Chat Header */
.chat-header {
    padding: 0.75rem 1rem;
    background: linear-gradient(135deg, #00b894 0%, #00cec9 100%);
    color: white;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-shrink: 0;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.room-avatar {
    width: 45px;
    height: 45px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
}

.room-name {
    font-weight: 600;
    font-size: 1.1rem;
}

.room-members {
    opacity: 0.9;
    font-size: 0.8rem;
}

.header-actions .btn-link {
    font-size: 1.1rem;
    opacity: 0.9;
}

.header-actions .btn-link:hover {
    opacity: 1;
}

/* Messages Container */
.messages-container {
    flex: 1;
    overflow-y: auto;
    padding: 1rem;
    display: flex;
    flex-direction: column;
}

.empty-messages {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
}

.empty-icon {
    width: 80px;
    height: 80px;
    background: rgba(0, 184, 148, 0.1);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    color: #00b894;
    margin-bottom: 1rem;
}

/* Date Divider */
.date-divider {
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 1rem 0;
}

.date-divider span {
    background: rgba(0, 184, 148, 0.1);
    color: #00b894;
    padding: 0.35rem 1rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 500;
}

/* Message Wrapper */
.message-wrapper {
    display: flex;
    align-items: flex-end;
    margin-bottom: 0.5rem;
    position: relative;
    animation: messageSlide 0.3s ease;
}

@keyframes messageSlide {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.message-wrapper.own {
    justify-content: flex-end;
}

.message-wrapper .message-avatar {
    width: 32px;
    height: 32px;
    font-size: 0.75rem;
    margin-right: 0.5rem;
    flex-shrink: 0;
}

/* Message Bubble */
.message-bubble {
    max-width: 65%;
    padding: 0.6rem 0.9rem;
    border-radius: 12px;
    position: relative;
    word-wrap: break-word;
}

.message-bubble.own {
    background: linear-gradient(135deg, #00b894 0%, #00a884 100%);
    color: white;
    border-bottom-right-radius: 4px;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
}

.message-bubble.other {
    background: white;
    color: #1f2937;
    border-bottom-left-radius: 4px;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
}

.message-sender {
    font-size: 0.75rem;
    font-weight: 600;
    color: #00b894;
    margin-bottom: 0.25rem;
}

.reply-preview {
    background: rgba(0, 0, 0, 0.05);
    border-left: 3px solid #00b894;
    padding: 0.4rem 0.6rem;
    border-radius: 4px;
    margin-bottom: 0.4rem;
    font-size: 0.8rem;
}

.reply-sender {
    font-weight: 600;
    color: #00b894;
    font-size: 0.7rem;
}

.reply-text {
    color: #6b7280;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.message-text {
    font-size: 0.95rem;
    line-height: 1.4;
}

.message-meta {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    margin-top: 0.25rem;
    font-size: 0.7rem;
    opacity: 0.7;
}

.message-bubble.own .message-meta {
    color: rgba(255, 255, 255, 0.8);
}

/* Message Actions */
.message-actions {
    display: none;
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    background: white;
    border-radius: 20px;
    padding: 0.25rem 0.5rem;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.message-wrapper:hover .message-actions {
    display: flex;
    gap: 0.25rem;
}

.message-wrapper.own .message-actions {
    right: calc(65% + 10px);
}

.message-wrapper:not(.own) .message-actions {
    left: calc(32px + 65% + 10px);
}

.message-actions button {
    background: none;
    border: none;
    padding: 0.3rem 0.4rem;
    cursor: pointer;
    color: #6b7280;
    border-radius: 50%;
    transition: all 0.2s;
}

.message-actions button:hover {
    background: #f3f4f6;
    color: #00b894;
}

/* Reactions */
.message-reactions {
    display: flex;
    flex-wrap: wrap;
    gap: 0.25rem;
    margin-top: 0.4rem;
}

.reaction-btn {
    background: rgba(0, 0, 0, 0.05);
    border: none;
    padding: 0.2rem 0.5rem;
    border-radius: 10px;
    font-size: 0.75rem;
    cursor: pointer;
    transition: all 0.2s;
}

.reaction-btn.active {
    background: rgba(0, 184, 148, 0.2);
}

.reaction-btn:hover {
    background: rgba(0, 184, 148, 0.3);
}

/* Reply Indicator */
.reply-indicator {
    background: white;
    border-top: 1px solid #e5e7eb;
    padding: 0.5rem 1rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.reply-content {
    font-size: 0.85rem;
    color: #6b7280;
}

.reply-content i {
    color: #00b894;
}

.cancel-reply {
    background: none;
    border: none;
    color: #9ca3af;
    cursor: pointer;
}

.cancel-reply:hover {
    color: #ef4444;
}

/* Message Input Area */
.message-input-area {
    padding: 0.75rem 1rem;
    background: #f0f2f5;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    flex-shrink: 0;
}

.attach-btn {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    border: none;
    background: transparent;
    color: #6b7280;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
    transition: all 0.2s;
}

.attach-btn:hover {
    background: white;
    color: #00b894;
}

.input-wrapper {
    flex: 1;
    position: relative;
    display: flex;
    align-items: center;
    background: white;
    border-radius: 24px;
    padding: 0 0.5rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.input-wrapper .form-control {
    border: none;
    padding: 0.6rem 0.75rem;
    font-size: 0.95rem;
    background: transparent;
}

.input-wrapper .form-control:focus {
    outline: none;
    box-shadow: none;
}

.emoji-btn {
    background: none;
    border: none;
    color: #6b7280;
    cursor: pointer;
    padding: 0.5rem;
    font-size: 1.2rem;
}

.emoji-btn:hover {
    color: #00b894;
}

.send-btn {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    border: none;
    background: linear-gradient(135deg, #00b894 0%, #00cec9 100%);
    color: white;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    transition: all 0.2s;
    box-shadow: 0 2px 10px rgba(0, 184, 148, 0.3);
}

.send-btn:hover {
    transform: scale(1.1);
    box-shadow: 0 4px 15px rgba(0, 184, 148, 0.4);
}

/* Mobile Members Panel */
.mobile-members-panel {
    position: fixed;
    top: 0;
    right: -300px;
    width: 280px;
    height: 100%;
    background: white;
    box-shadow: -5px 0 20px rgba(0, 0, 0, 0.1);
    z-index: 1050;
    transition: right 0.3s ease;
    display: flex;
    flex-direction: column;
}

.mobile-members-panel.active {
    right: 0;
}

.panel-header {
    padding: 1rem;
    background: #f8f9fa;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

/* Reaction Picker */
.reaction-picker {
    position: fixed;
    background: white;
    border-radius: 30px;
    padding: 0.5rem;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
    display: flex;
    gap: 0.25rem;
    z-index: 1060;
}

.reaction-picker button {
    background: none;
    border: none;
    padding: 0.5rem;
    font-size: 1.2rem;
    cursor: pointer;
    border-radius: 50%;
    transition: all 0.2s;
}

.reaction-picker button:hover {
    background: #f3f4f6;
    transform: scale(1.2);
}

/* Scrollbar */
.messages-container::-webkit-scrollbar,
.members-list::-webkit-scrollbar {
    width: 6px;
}

.messages-container::-webkit-scrollbar-thumb,
.members-list::-webkit-scrollbar-thumb {
    background: #00b894;
    border-radius: 3px;
}

.messages-container::-webkit-scrollbar-track,
.members-list::-webkit-scrollbar-track {
    background: transparent;
}

/* Responsive */
@media (max-width: 991px) {
    .chat-page-container {
        height: calc(100vh - 60px);
    }
}
</style>

<script>
let replyToMessageId = null;
let pollingInterval = null;
const roomId = {{ $room->id }};

document.addEventListener('DOMContentLoaded', function() {
    scrollToBottom();
    startPolling();
});

function scrollToBottom() {
    const container = document.getElementById('messagesContainer');
    container.scrollTop = container.scrollHeight;
}

function sendMessage() {
    const input = document.getElementById('messageInput');
    const message = input.value.trim();

    if (!message) return;

    const sendBtn = document.getElementById('sendBtn');
    sendBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    sendBtn.disabled = true;

    fetch(`/chat/room/${roomId}/send`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            message: message,
            parent_id: replyToMessageId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            input.value = '';
            cancelReply();
            appendMessage(data.message);
            scrollToBottom();
        } else {
            alert(data.error || 'Xatolik yuz berdi');
        }
        sendBtn.innerHTML = '<i class="fas fa-paper-plane"></i>';
        sendBtn.disabled = false;
    })
    .catch(error => {
        console.error('Error:', error);
        sendBtn.innerHTML = '<i class="fas fa-paper-plane"></i>';
        sendBtn.disabled = false;
    });
}

function appendMessage(msg) {
    const container = document.getElementById('messagesContainer');
    const isOwn = msg.user_id == {{ auth()->id() }};
    const userName = msg.user ? msg.user.name : '{{ auth()->user()->name }}';
    const firstLetter = userName.charAt(0).toUpperCase();

    const wrapper = document.createElement('div');
    wrapper.className = `message-wrapper ${isOwn ? 'own' : ''}`;
    wrapper.dataset.messageId = msg.id;

    let avatarHtml = '';
    if (!isOwn) {
        avatarHtml = `<div class="message-avatar">${firstLetter}</div>`;
    }

    let senderHtml = '';
    if (!isOwn) {
        senderHtml = `<div class="message-sender">${userName}</div>`;
    }

    let replyHtml = '';
    if (msg.parent) {
        replyHtml = `
            <div class="reply-preview">
                <div class="reply-sender">${msg.parent.user.name}</div>
                <div class="reply-text">${msg.parent.message.substring(0, 50)}</div>
            </div>
        `;
    }

    const time = new Date().toLocaleTimeString('uz-UZ', { hour: '2-digit', minute: '2-digit' });

    wrapper.innerHTML = `
        ${avatarHtml}
        <div class="message-bubble ${isOwn ? 'own' : 'other'}">
            ${senderHtml}
            ${replyHtml}
            <div class="message-text">${msg.message}</div>
            <div class="message-meta">
                <span class="message-time">${time}</span>
                ${isOwn ? '<i class="fas fa-check-double ms-1"></i>' : ''}
            </div>
        </div>
        <div class="message-actions">
            <button onclick="replyToMessage(${msg.id}, '${userName}', '${msg.message.substring(0, 30)}')" title="Javob berish">
                <i class="fas fa-reply"></i>
            </button>
            ${isOwn ? `<button onclick="deleteMessage(${msg.id})" title="O'chirish" class="text-danger"><i class="fas fa-trash"></i></button>` : ''}
        </div>
    `;

    container.appendChild(wrapper);
}

function replyToMessage(messageId, senderName, messageText) {
    replyToMessageId = messageId;
    document.getElementById('replyToName').textContent = senderName;
    document.getElementById('replyToText').textContent = messageText;
    document.getElementById('replyIndicator').style.display = 'flex';
    document.getElementById('messageInput').focus();
}

function cancelReply() {
    replyToMessageId = null;
    document.getElementById('replyIndicator').style.display = 'none';
}

function deleteMessage(messageId) {
    if (!confirm("Xabarni o'chirmoqchimisiz?")) return;

    fetch(`/chat/message/${messageId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const msgEl = document.querySelector(`[data-message-id="${messageId}"]`);
            if (msgEl) {
                msgEl.style.animation = 'fadeOut 0.3s ease forwards';
                setTimeout(() => msgEl.remove(), 300);
            }
        } else {
            alert(data.error || 'Xatolik yuz berdi');
        }
    })
    .catch(error => console.error('Error:', error));
}

function toggleReaction(messageId, emoji) {
    fetch(`/chat/message/${messageId}/reaction`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ emoji: emoji })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Refresh messages to show updated reactions
            refreshMessages();
        }
    })
    .catch(error => console.error('Error:', error));
}

function toggleMembers() {
    const panel = document.getElementById('mobileMembersPanel');
    panel.classList.toggle('active');
}

function leaveRoom() {
    if (!confirm("Chat xonasidan chiqmoqchimisiz?")) return;

    fetch(`/chat/leave/${roomId}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.href = '{{ route("chat.index") }}';
        }
    })
    .catch(error => console.error('Error:', error));
}

function startPolling() {
    pollingInterval = setInterval(refreshMessages, 5000);
}

function refreshMessages() {
    // Simple refresh - in production, use websockets
    const container = document.getElementById('messagesContainer');
    const lastMessageId = container.querySelector('.message-wrapper:last-child')?.dataset.messageId || 0;

    // For now, we'll just keep the current messages
    // In a real app, fetch new messages and append them
}

function toggleAttachMenu() {
    alert('Fayl biriktirish tez orada!');
}

function toggleEmojiPicker() {
    alert('Emoji tanlash tez orada!');
}

// Cleanup on page leave
window.addEventListener('beforeunload', function() {
    if (pollingInterval) {
        clearInterval(pollingInterval);
    }
});

// Add fadeOut animation
const style = document.createElement('style');
style.textContent = `
    @keyframes fadeOut {
        from { opacity: 1; transform: translateX(0); }
        to { opacity: 0; transform: translateX(100px); }
    }
`;
document.head.appendChild(style);
</script>
@endsection
