@extends('layouts.dashboard-new')

@section('title', 'Telegram bot sozlamalari')
@section('page-title', 'Telegram bot sozlamalari')

@section('content')
<div class="container-fluid">
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fab fa-telegram text-primary me-2"></i>Bot sozlamalari</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('chat-admin.telegram-settings.update') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Bot Token</label>
                            <input type="text" name="bot_token" class="form-control @error('bot_token') is-invalid @enderror"
                                   value="{{ old('bot_token', $settings->bot_token) }}"
                                   placeholder="123456789:ABCdefGHIjklMNOpqrsTUVwxyz">
                            <small class="text-muted">@BotFather dan olingan token</small>
                            @error('bot_token')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Bot Username</label>
                            <div class="input-group">
                                <span class="input-group-text">@</span>
                                <input type="text" name="bot_username" class="form-control @error('bot_username') is-invalid @enderror"
                                       value="{{ old('bot_username', $settings->bot_username) }}"
                                       placeholder="your_bot">
                            </div>
                            @error('bot_username')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Webhook URL</label>
                            <input type="url" name="webhook_url" class="form-control @error('webhook_url') is-invalid @enderror"
                                   value="{{ old('webhook_url', $settings->webhook_url) }}"
                                   placeholder="https://yourdomain.com/api/telegram/webhook">
                            <small class="text-muted">Telegram xabarlarni yuborish uchun URL</small>
                            @error('webhook_url')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Salomlash xabari</label>
                            <textarea name="welcome_message" class="form-control @error('welcome_message') is-invalid @enderror"
                                      rows="4" placeholder="Assalomu alaykum! Sizga qanday yordam bera olishim mumkin?">{{ old('welcome_message', $settings->welcome_message) }}</textarea>
                            <small class="text-muted">Foydalanuvchi birinchi marta yozganida yuboriladigan xabar</small>
                            @error('welcome_message')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <div class="form-check form-switch">
                                <input type="hidden" name="is_active" value="0">
                                <input type="checkbox" name="is_active" value="1" class="form-check-input"
                                       id="is_active" {{ old('is_active', $settings->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">Bot faol</label>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('chat-admin.telegram') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Orqaga
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Saqlash
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-vial text-warning me-2"></i>Bot sinovi</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="testBotConnection()">
                            <i class="fas fa-robot me-1"></i> Bot holatini tekshirish
                        </button>
                        <button type="button" class="btn btn-outline-info btn-sm" onclick="checkWebhook()">
                            <i class="fas fa-link me-1"></i> Webhook ma'lumotlari
                        </button>
                        <button type="button" class="btn btn-outline-success btn-sm" onclick="setWebhook()">
                            <i class="fas fa-plug me-1"></i> Webhook o'rnatish
                        </button>
                        <button type="button" class="btn btn-outline-danger btn-sm" onclick="deleteWebhook()">
                            <i class="fas fa-trash me-1"></i> Webhook o'chirish
                        </button>
                    </div>

                    <div id="testResult" class="mt-3" style="display: none;">
                        <div class="alert alert-sm mb-0" role="alert" id="testResultContent"></div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle text-info me-2"></i>Qo'llanma</h5>
                </div>
                <div class="card-body">
                    <ol class="mb-0">
                        <li class="mb-2">Telegram da <strong>@BotFather</strong> ga yozing</li>
                        <li class="mb-2"><code>/newbot</code> buyrug'ini yuboring</li>
                        <li class="mb-2">Bot nomini va username ni kiriting</li>
                        <li class="mb-2">Olingan tokenni shu yerga kiriting</li>
                        <li class="mb-2">Webhook URL ni sozlang</li>
                        <li>Bot ni faollashtiring</li>
                    </ol>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-link text-success me-2"></i>Foydali havolalar</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <a href="https://t.me/BotFather" target="_blank" class="text-decoration-none">
                                <i class="fab fa-telegram me-1"></i> BotFather
                            </a>
                        </li>
                        <li>
                            <a href="https://core.telegram.org/bots/api" target="_blank" class="text-decoration-none">
                                <i class="fas fa-book me-1"></i> Telegram Bot API
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function showTestResult(message, type = 'info') {
    const resultDiv = document.getElementById('testResult');
    const contentDiv = document.getElementById('testResultContent');

    resultDiv.style.display = 'block';
    contentDiv.className = `alert alert-${type} alert-sm mb-0`;
    contentDiv.innerHTML = message;
}

async function testBotConnection() {
    showTestResult('<i class="fas fa-spinner fa-spin me-2"></i>Tekshirilmoqda...', 'info');

    try {
        const response = await fetch('/api/telegram/test-bot', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });

        const data = await response.json();

        if (data.success) {
            let message = '<i class="fas fa-check-circle me-2"></i><strong>Bot ishlayapti!</strong><br><small>';
            message += `Bot nomi: ${data.bot.first_name}<br>`;
            message += `Username: @${data.bot.username}</small>`;
            showTestResult(message, 'success');
        } else {
            showTestResult(`<i class="fas fa-times-circle me-2"></i><strong>Xato:</strong> ${data.error}`, 'danger');
        }
    } catch (error) {
        showTestResult(`<i class="fas fa-times-circle me-2"></i><strong>Xato:</strong> ${error.message}`, 'danger');
    }
}

async function checkWebhook() {
    showTestResult('<i class="fas fa-spinner fa-spin me-2"></i>Tekshirilmoqda...', 'info');

    try {
        const response = await fetch('/api/telegram/webhook-info', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });

        const data = await response.json();

        if (data.success) {
            const info = data.webhook_info;
            let message = '<i class="fas fa-info-circle me-2"></i><strong>Webhook ma\'lumotlari:</strong><br><small>';
            message += `URL: ${info.url || 'O\'rnatilmagan'}<br>`;
            message += `Pending updates: ${info.pending_update_count || 0}<br>`;
            if (info.last_error_message) {
                message += `<span class="text-danger">Oxirgi xato: ${info.last_error_message}</span>`;
            }
            message += '</small>';
            showTestResult(message, info.url ? 'success' : 'warning');
        } else {
            showTestResult(`<i class="fas fa-times-circle me-2"></i><strong>Xato:</strong> ${data.error}`, 'danger');
        }
    } catch (error) {
        showTestResult(`<i class="fas fa-times-circle me-2"></i><strong>Xato:</strong> ${error.message}`, 'danger');
    }
}

async function setWebhook() {
    if (!confirm('Webhook o\'rnatilsinmi?')) return;

    showTestResult('<i class="fas fa-spinner fa-spin me-2"></i>O\'rnatilmoqda...', 'info');

    try {
        const response = await fetch('/api/telegram/set-webhook', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });

        const data = await response.json();

        if (data.success) {
            showTestResult('<i class="fas fa-check-circle me-2"></i><strong>Webhook muvaffaqiyatli o\'rnatildi!</strong>', 'success');
        } else {
            showTestResult(`<i class="fas fa-times-circle me-2"></i><strong>Xato:</strong> ${data.error || data.description}`, 'danger');
        }
    } catch (error) {
        showTestResult(`<i class="fas fa-times-circle me-2"></i><strong>Xato:</strong> ${error.message}`, 'danger');
    }
}

async function deleteWebhook() {
    if (!confirm('Webhook o\'chirilsinmi?')) return;

    showTestResult('<i class="fas fa-spinner fa-spin me-2"></i>O\'chirilmoqda...', 'info');

    try {
        const response = await fetch('/api/telegram/delete-webhook', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });

        const data = await response.json();

        if (data.success) {
            showTestResult('<i class="fas fa-check-circle me-2"></i><strong>Webhook o\'chirildi!</strong>', 'success');
        } else {
            showTestResult(`<i class="fas fa-times-circle me-2"></i><strong>Xato:</strong> ${data.error || data.description}`, 'danger');
        }
    } catch (error) {
        showTestResult(`<i class="fas fa-times-circle me-2"></i><strong>Xato:</strong> ${error.message}`, 'danger');
    }
}
</script>
@endpush
