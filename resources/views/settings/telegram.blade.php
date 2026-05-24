@extends('layouts.dashboard-new')

@section('title', 'Telegram Bot Sozlamalari')
@section('page-title', 'Telegram Bot Sozlamalari')

@section('content')
<div class="container-fluid px-0">
    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fab fa-telegram me-2"></i>Telegram Bot Sozlamalari</h5>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('settings.integrations.telegram.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3 form-check form-switch">
                            <input type="checkbox" class="form-check-input" id="telegram_bot_enabled" name="telegram_bot_enabled" value="1" {{ $settings->where('key', 'telegram_bot_enabled')->first()?->value ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="telegram_bot_enabled">Telegram Bot Yoqilgan</label>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Bot Token <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="telegram_bot_token" value="{{ $settings->where('key', 'telegram_bot_token')->first()?->value ?? '' }}" placeholder="1234567890:ABCdefGHIjklMNOpqrsTUVwxyz">
                            <small class="text-muted">BotFather'dan olingan token</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Bot Username</label>
                            <div class="input-group">
                                <span class="input-group-text">@</span>
                                <input type="text" class="form-control" name="telegram_bot_username" value="{{ $settings->where('key', 'telegram_bot_username')->first()?->value ?? '' }}" placeholder="mybotusername">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Webhook URL</label>
                            <input type="url" class="form-control" name="telegram_webhook_url" value="{{ $settings->where('key', 'telegram_webhook_url')->first()?->value ?? '' }}" placeholder="https://yourdomain.com/api/telegram/webhook">
                        </div>

                        <div class="mb-3 form-check form-switch">
                            <input type="checkbox" class="form-check-input" id="telegram_notifications_enabled" name="telegram_notifications_enabled" value="1" {{ $settings->where('key', 'telegram_notifications_enabled')->first()?->value ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="telegram_notifications_enabled">Bildirishnomalar Yoqilgan</label>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Saqlash</button>
                            <a href="{{ route('settings.integrations') }}" class="btn btn-secondary"><i class="fas fa-arrow-left me-2"></i>Orqaga</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-question-circle me-2"></i>Yordam</h5>
                </div>
                <div class="card-body">
                    <h6 class="fw-semibold mb-3">Bot qanday yaratiladi?</h6>
                    <ol class="small">
                        <li class="mb-2">Telegram'da @BotFather'ni oching</li>
                        <li class="mb-2">/newbot buyrug'ini yuboring</li>
                        <li class="mb-2">Bot nomi va username'ni kiriting</li>
                        <li class="mb-2">Token'ni nusxalang</li>
                        <li class="mb-2">Bu sahifaga token'ni kiriting</li>
                    </ol>

                    <hr class="my-3">

                    <h6 class="fw-semibold mb-3">Bildirishnomalar</h6>
                    <ul class="list-unstyled small">
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Yangi baholar</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Davomat o'zgarishlari</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Yangi e'lonlar</li>
                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Muddat eslatmalari</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
