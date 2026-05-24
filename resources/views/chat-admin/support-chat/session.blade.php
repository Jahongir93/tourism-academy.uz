@extends('layouts.dashboard-new')

@section('title', 'Support Chat - ' . $sessionInfo['name'])
@section('page-title', 'Chat: ' . $sessionInfo['name'])

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Back button -->
        <div class="col-12 mb-3">
            <a href="{{ route('chat-admin.support-chat') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Orqaga
            </a>
        </div>

        <!-- Chat Area -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white py-3">
                    <div class="d-flex align-items-center">
                        <div class="avatar-circle bg-white text-primary me-3">
                            {{ strtoupper(substr($sessionInfo['name'], 0, 1)) }}
                        </div>
                        <div>
                            <h5 class="mb-0">{{ $sessionInfo['name'] }}</h5>
                            <small class="opacity-75"><i class="fas fa-phone me-1"></i>{{ $sessionInfo['phone'] ?: 'Telefon mavjud emas' }}</small>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <!-- Messages -->
                    <div class="chat-messages p-4" id="chatMessages" style="height: 500px; overflow-y: auto;">
                        @foreach($messages as $message)
                            <div class="message-item mb-3 {{ $message->is_from_admin ? 'text-end' : '' }}">
                                <div class="d-inline-block p-3 rounded-3 {{ $message->is_from_admin ? 'bg-primary text-white' : 'bg-light' }}" style="max-width: 75%;">
                                    <div class="message-text">{{ $message->message }}</div>
                                    <div class="message-time mt-1 {{ $message->is_from_admin ? 'text-white-50' : 'text-muted' }}" style="font-size: 0.75rem;">
                                        {{ $message->created_at->format('d.m.Y H:i') }}
                                        @if($message->is_from_admin && $message->admin)
                                            <span class="ms-1">- {{ $message->admin->name }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <!-- Reply Form -->
                <div class="card-footer bg-light">
                    <form action="{{ route('chat-admin.support-chat.send', $sessionId) }}" method="POST" id="replyForm">
                        @csrf
                        <div class="input-group">
                            <input type="text" name="message" class="form-control" placeholder="Javob yozing..." required autocomplete="off" id="messageInput">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i> Yuborish
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Session Info -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Sessiya ma'lumotlari</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td class="text-muted">Ism:</td>
                            <td class="fw-bold">{{ $sessionInfo['name'] }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Telefon:</td>
                            <td><a href="tel:{{ $sessionInfo['phone'] }}">{{ $sessionInfo['phone'] ?: '-' }}</a></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Boshlangan:</td>
                            <td>{{ $sessionInfo['started_at'] ? $sessionInfo['started_at']->format('d.m.Y H:i') : '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Jami xabarlar:</td>
                            <td>{{ $messages->count() }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Session ID:</td>
                            <td><small class="text-muted">{{ Str::limit($sessionId, 20) }}</small></td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Quick Replies -->
            <div class="card border-0 shadow-sm mt-3">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0"><i class="fas fa-bolt me-2"></i>Tezkor javoblar</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-outline-secondary btn-sm quick-reply" data-message="Assalomu alaykum! Sizga qanday yordam bera olaman?">
                            Salomlashish
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm quick-reply" data-message="Ma'lumot uchun rahmat. Tez orada javob beramiz.">
                            Qabul qilish
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm quick-reply" data-message="Iltimos, batafsil ma'lumot bering.">
                            Batafsil so'rash
                        </button>
                        <button type="button" class="btn btn-outline-secondary btn-sm quick-reply" data-message="Murojatingiz uchun rahmat. Yaxshi kun tilaymiz!">
                            Xayrlashish
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-circle {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 1.2rem;
}
.chat-messages {
    background: #f8f9fa;
}
.message-item .message-text {
    word-wrap: break-word;
}
</style>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Scroll to bottom
    const chatMessages = document.getElementById('chatMessages');
    chatMessages.scrollTop = chatMessages.scrollHeight;

    // Quick replies
    document.querySelectorAll('.quick-reply').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('messageInput').value = this.dataset.message;
            document.getElementById('messageInput').focus();
        });
    });

    // Auto-refresh messages every 10 seconds
    setInterval(function() {
        location.reload();
    }, 30000);
});
</script>
@endpush
@endsection
