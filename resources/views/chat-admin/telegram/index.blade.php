@extends('layouts.dashboard-new')

@section('title', 'Telegram Suhbatlari')
@section('page-title', 'Telegram Suhbatlari')

@section('content')
<div class="container-fluid px-0" style="height: calc(100vh - 140px);">
    <div class="row g-0 h-100">
        <!-- Left Sidebar - Conversations List -->
        <div class="col-md-4 border-end bg-white" style="height: 100%; overflow-y: auto;">
            <!-- Header -->
            <div class="p-3 border-bottom bg-light sticky-top">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="mb-0"><i class="fab fa-telegram text-primary me-2"></i>Suhbatlar</h5>
                    <a href="{{ route('chat-admin.telegram-settings') }}" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-cog"></i>
                    </a>
                </div>

                <!-- Stats -->
                <div class="d-flex gap-2 mb-2">
                    <div class="flex-fill text-center bg-white rounded p-2 small">
                        <div class="fw-bold text-primary">{{ $stats['conversations'] }}</div>
                        <div class="text-muted small">Suhbatlar</div>
                    </div>
                    <div class="flex-fill text-center bg-warning bg-opacity-10 rounded p-2 small">
                        <div class="fw-bold text-warning">{{ $stats['new'] }}</div>
                        <div class="text-muted small">Yangi</div>
                    </div>
                    <div class="flex-fill text-center bg-success bg-opacity-10 rounded p-2 small">
                        <div class="fw-bold text-success">{{ $stats['replied'] }}</div>
                        <div class="text-muted small">Javob</div>
                    </div>
                </div>

                <!-- Filter -->
                <form action="{{ route('chat-admin.telegram') }}" method="GET">
                    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">Barcha suhbatlar</option>
                        <option value="new" {{ $status == 'new' ? 'selected' : '' }}>Yangi xabarlar</option>
                        <option value="read" {{ $status == 'read' ? 'selected' : '' }}>O'qilgan</option>
                        <option value="replied" {{ $status == 'replied' ? 'selected' : '' }}>Javob berilgan</option>
                    </select>
                </form>
            </div>

            <!-- Conversations List -->
            <div class="conversations-list">
                @forelse($conversations as $conv)
                    <a href="?chat_id={{ $conv->telegram_chat_id }}&status={{ $status }}"
                       class="conversation-item d-block p-3 border-bottom text-decoration-none {{ $chatId == $conv->telegram_chat_id ? 'active' : '' }}">
                        <div class="d-flex align-items-start">
                            <div class="avatar me-3">
                                <div class="avatar-circle bg-primary text-white">
                                    {{ strtoupper(substr($conv->telegram_first_name ?? 'U', 0, 1)) }}
                                </div>
                            </div>
                            <div class="flex-grow-1 min-width-0">
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <h6 class="mb-0 text-truncate fw-semibold">
                                        {{ trim(($conv->telegram_first_name ?? '') . ' ' . ($conv->telegram_last_name ?? '')) }}
                                    </h6>
                                    <small class="text-muted ms-2 flex-shrink-0">
                                        {{ \Carbon\Carbon::parse($conv->last_message_at)->format('H:i') }}
                                    </small>
                                </div>
                                @if($conv->telegram_username)
                                    <div class="small text-muted mb-1">{{ '@' . $conv->telegram_username }}</div>
                                @endif
                                <div class="d-flex justify-content-between align-items-center">
                                    <p class="mb-0 text-muted small text-truncate">
                                        {{ Str::limit($conv->last_message, 40) }}
                                    </p>
                                    @if($conv->unread_count > 0)
                                        <span class="badge bg-warning rounded-pill ms-2">{{ $conv->unread_count }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="text-center py-5 text-muted">
                        <i class="fab fa-telegram fa-3x mb-3 d-block opacity-50"></i>
                        <p class="mb-0">Suhbatlar topilmadi</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Right Side - Chat Messages -->
        <div class="col-md-8 d-flex flex-column bg-light" style="height: 100%;">
            @if($chatId && $messages->isNotEmpty())
                <!-- Chat Header -->
                <div class="chat-header p-3 bg-white border-bottom">
                    <div class="d-flex align-items-center">
                        <div class="avatar me-3">
                            <div class="avatar-circle bg-primary text-white">
                                {{ strtoupper(substr($messages->first()->telegram_first_name ?? 'U', 0, 1)) }}
                            </div>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-semibold">
                                {{ trim(($messages->first()->telegram_first_name ?? '') . ' ' . ($messages->first()->telegram_last_name ?? '')) }}
                            </h6>
                            @if($messages->first()->telegram_username)
                                <small class="text-muted">{{ '@' . $messages->first()->telegram_username }}</small>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Messages Area -->
                <div class="chat-messages flex-grow-1 p-3" style="overflow-y: auto;" id="messagesContainer">
                    @foreach($messages as $message)
                        <div class="message-wrapper mb-3 {{ $message->direction === 'outgoing' ? 'text-end' : '' }}">
                            <div class="message {{ $message->direction === 'outgoing' ? 'message-sent' : 'message-received' }}">
                                @if($message->direction === 'incoming')
                                    <div class="message-content">
                                        {{ $message->message }}
                                    </div>
                                    <div class="message-time">
                                        {{ $message->created_at->format('H:i') }}
                                    </div>
                                @else
                                    <div class="message-content">
                                        <div class="small text-success fw-semibold mb-1">
                                            <i class="fas fa-user-shield"></i> {{ $message->repliedByUser?->name ?? 'Admin' }}
                                        </div>
                                        {{ $message->reply_message }}
                                    </div>
                                    <div class="message-time">
                                        {{ $message->replied_at?->format('H:i') ?? $message->created_at->format('H:i') }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Message Input -->
                <div class="chat-input p-3 bg-white border-top">
                    <form id="replyForm">
                        <input type="hidden" id="currentChatId" value="{{ $chatId }}">
                        <input type="hidden" id="currentMessageId" value="{{ $messages->where('direction', 'incoming')->last()?->id }}">
                        <div class="input-group">
                            <textarea class="form-control" name="reply_message" id="replyInput"
                                      rows="1" placeholder="Xabar yozing..."
                                      style="resize: none; max-height: 120px;"></textarea>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </div>
                    </form>
                </div>
            @else
                <div class="d-flex align-items-center justify-content-center h-100 text-muted">
                    <div class="text-center">
                        <i class="fab fa-telegram fa-4x mb-3 opacity-25"></i>
                        <p class="mb-0">Suhbatni tanlang</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
/* Conversations List */
.conversation-item {
    transition: background-color 0.2s;
    color: inherit;
}

.conversation-item:hover {
    background-color: #f8f9fa;
}

.conversation-item.active {
    background-color: #e3f2fd;
    border-left: 3px solid #2196F3;
}

.avatar-circle {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    font-weight: bold;
}

.min-width-0 {
    min-width: 0;
}

/* Messages */
.chat-messages {
    background: linear-gradient(to bottom, #f8f9fa 0%, #ffffff 100%);
}

.message {
    display: inline-block;
    max-width: 70%;
    padding: 10px 14px;
    border-radius: 12px;
    box-shadow: 0 1px 2px rgba(0,0,0,0.1);
    word-wrap: break-word;
}

.message-received {
    background: white;
    border: 1px solid #e0e0e0;
}

.message-sent {
    background: #dcf8c6;
    border: 1px solid #d4f1ba;
}

.message-content {
    margin-bottom: 4px;
    line-height: 1.4;
}

.message-time {
    font-size: 0.75rem;
    color: #6c757d;
    text-align: right;
}

/* Auto-resize textarea */
#replyInput {
    min-height: 42px;
}

/* Scrollbar styling */
.chat-messages::-webkit-scrollbar,
.conversations-list::-webkit-scrollbar {
    width: 6px;
}

.chat-messages::-webkit-scrollbar-track,
.conversations-list::-webkit-scrollbar-track {
    background: #f1f1f1;
}

.chat-messages::-webkit-scrollbar-thumb,
.conversations-list::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 3px;
}

.chat-messages::-webkit-scrollbar-thumb:hover,
.conversations-list::-webkit-scrollbar-thumb:hover {
    background: #555;
}
</style>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-scroll to bottom on page load
    const container = document.getElementById('messagesContainer');
    if (container) {
        container.scrollTop = container.scrollHeight;
    }

    // Auto-resize textarea
    const textarea = document.getElementById('replyInput');
    if (textarea) {
        textarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 120) + 'px';
        });

        // Submit on Enter (Shift+Enter for new line)
        textarea.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                document.getElementById('replyForm').dispatchEvent(new Event('submit'));
            }
        });
    }

    // Reply form submit
    const form = document.getElementById('replyForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            const messageId = document.getElementById('currentMessageId').value;
            const chatId = document.getElementById('currentChatId').value;
            const formData = new FormData(this);

            if (!messageId) {
                alert('Xatolik: Message ID topilmadi');
                return;
            }

            // Disable form
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

            fetch(`{{ url('chat-admin/telegram') }}/${messageId}/reply`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    // Reload page to show new message
                    window.location.href = `?chat_id=${chatId}&status={{ $status }}`;
                } else {
                    alert(data.message || 'Xatolik yuz berdi');
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }
            })
            .catch(err => {
                console.error(err);
                alert('Xatolik yuz berdi');
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            });
        });
    }
});
</script>
@endpush
@endsection
