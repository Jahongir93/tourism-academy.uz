@extends('layouts.dashboard-new')

@section('title', 'Integratsiyalar')
@section('page-title', 'Integratsiyalar')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm bg-gradient-purple text-white">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1"><i class="fas fa-plug me-2"></i>Integratsiyalar</h4>
                            <p class="mb-0 opacity-75">Tashqi xizmatlar va API integratsiyalari</p>
                        </div>
                        <a href="{{ route('settings.index') }}" class="btn btn-light">
                            <i class="fas fa-arrow-left me-1"></i> Orqaga
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row">
        <!-- HEMIS Integration -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-graduation-cap me-2" style="color: #667eea;"></i>
                        HEMIS Integratsiyasi
                    </h5>
                    <span class="badge {{ isset($hemisEnabled) && $hemisEnabled ? 'bg-success' : 'bg-secondary' }}">
                        {{ isset($hemisEnabled) && $hemisEnabled ? 'Faol' : 'Nofaol' }}
                    </span>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">Oliy ta'lim axborot tizimi bilan integratsiya</p>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">API URL</label>
                        <input type="text" class="form-control" value="{{ $settings->where('key', 'hemis_api_url')->first()?->value ?? 'https://student.samtuit.uz/rest' }}" readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Holat</label>
                        <div class="d-flex align-items-center">
                            @if(isset($hemisConnected) && $hemisConnected)
                            <span class="badge bg-success me-2"><i class="fas fa-check me-1"></i>Ulangan</span>
                            <small class="text-muted">So'nggi sinxronlash: {{ $hemisLastSync ?? 'Hech qachon' }}</small>
                            @else
                            <span class="badge bg-warning me-2"><i class="fas fa-exclamation me-1"></i>Ulanmagan</span>
                            @endif
                        </div>
                    </div>

                    <h6 class="fw-semibold mb-2">Xususiyatlar:</h6>
                    <ul class="list-unstyled mb-3">
                        <li class="mb-1"><i class="fas fa-check text-success me-2"></i>Talabalarni sinxronlash</li>
                        <li class="mb-1"><i class="fas fa-check text-success me-2"></i>O'qituvchilarni sinxronlash</li>
                        <li class="mb-1"><i class="fas fa-check text-success me-2"></i>Fanlarni import qilish</li>
                        <li class="mb-1"><i class="fas fa-check text-success me-2"></i>Dars jadvali</li>
                    </ul>

                    <a href="{{ route('hemis.settings') ?? '#' }}" class="btn btn-primary w-100">
                        <i class="fas fa-cog me-1"></i> Sozlash
                    </a>
                </div>
            </div>
        </div>

        <!-- Telegram Bot -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fab fa-telegram me-2" style="color: #0088cc;"></i>
                        Telegram Bot
                    </h5>
                    <span class="badge {{ $settings->where('key', 'telegram_enabled')->first()?->value == '1' ? 'bg-success' : 'bg-secondary' }}">
                        {{ $settings->where('key', 'telegram_enabled')->first()?->value == '1' ? 'Faol' : 'Nofaol' }}
                    </span>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">Telegram orqali bildirishnomalar yuborish</p>

                    <form action="{{ route('settings.integrations.telegram.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Bot Token</label>
                            <input type="password" name="telegram_bot_token" class="form-control"
                                   value="{{ $settings->where('key', 'telegram_bot_token')->first()?->value ?? '' }}"
                                   placeholder="123456:ABC-DEF1234ghIkl-zyx57W2v1u123ew11">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Bot Username</label>
                            <input type="text" name="telegram_bot_username" class="form-control"
                                   value="{{ $settings->where('key', 'telegram_bot_username')->first()?->value ?? '' }}"
                                   placeholder="@hemis_bot">
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="telegram_enabled" id="telegram_enabled"
                                       {{ $settings->where('key', 'telegram_enabled')->first()?->value == '1' ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="telegram_enabled">Telegram botni yoqish</label>
                            </div>
                        </div>

                        <h6 class="fw-semibold mb-2">Bildirishnoma turlari:</h6>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="telegram_notify_grades" id="telegram_notify_grades" checked>
                                <label class="form-check-label" for="telegram_notify_grades">Baholar</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="telegram_notify_attendance" id="telegram_notify_attendance" checked>
                                <label class="form-check-label" for="telegram_notify_attendance">Davomat</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="telegram_notify_announcements" id="telegram_notify_announcements" checked>
                                <label class="form-check-label" for="telegram_notify_announcements">E'lonlar</label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-save me-1"></i> Saqlash
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- SMS/OTP Integration - Admin panelga yo'naltirish -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-sms me-2 text-success"></i>
                        SMS/OTP Xizmati
                    </h5>
                    <span class="badge bg-info">Admin Panel</span>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">SMS va OTP sozlamalari admin panelda boshqariladi</p>

                    <div class="alert alert-info mb-3">
                        <i class="fas fa-info-circle me-2"></i>
                        SMS va OTP sozlamalari markazlashtirilgan holda admin panelda saqlanadi.
                    </div>

                    <h6 class="fw-semibold mb-2">Mavjud xususiyatlar:</h6>
                    <ul class="list-unstyled mb-3">
                        <li class="mb-1"><i class="fas fa-check text-success me-2"></i>Eskiz SMS Gateway</li>
                        <li class="mb-1"><i class="fas fa-check text-success me-2"></i>PlayMobile SMS</li>
                        <li class="mb-1"><i class="fas fa-check text-success me-2"></i>OTP tasdiqlash</li>
                        <li class="mb-1"><i class="fas fa-check text-success me-2"></i>Email tasdiqlash</li>
                        <li class="mb-1"><i class="fas fa-check text-success me-2"></i>Test rejimi</li>
                    </ul>

                    @can('manage_settings')
                    <a href="{{ route('admin.settings.otp.index') }}" class="btn btn-success w-100">
                        <i class="fas fa-cog me-1"></i> OTP Sozlamalarini ochish
                    </a>
                    @else
                    <div class="alert alert-warning mb-0">
                        <i class="fas fa-lock me-2"></i>
                        OTP sozlamalarini ko'rish uchun admin huquqi kerak
                    </div>
                    @endcan
                </div>
            </div>
        </div>

        <!-- Payment Integrations -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="fas fa-credit-card me-2 text-warning"></i>
                        To'lov Tizimlari
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">Onlayn to'lovlarni qabul qilish</p>

                    <!-- Payme -->
                    <div class="border rounded p-3 mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="mb-0"><i class="fas fa-wallet text-primary me-2"></i>Payme</h6>
                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input" type="checkbox" id="payme_enabled"
                                       {{ $settings->where('key', 'payme_enabled')->first()?->value == '1' ? 'checked' : '' }}>
                            </div>
                        </div>
                        <div class="row g-2">
                            <div class="col-md-6">
                                <input type="text" class="form-control form-control-sm" placeholder="Merchant ID"
                                       value="{{ $settings->where('key', 'payme_merchant_id')->first()?->value ?? '' }}">
                            </div>
                            <div class="col-md-6">
                                <input type="password" class="form-control form-control-sm" placeholder="Secret Key"
                                       value="{{ $settings->where('key', 'payme_secret_key')->first()?->value ?? '' }}">
                            </div>
                        </div>
                    </div>

                    <!-- Click -->
                    <div class="border rounded p-3 mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="mb-0"><i class="fas fa-hand-pointer text-success me-2"></i>Click</h6>
                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input" type="checkbox" id="click_enabled"
                                       {{ $settings->where('key', 'click_enabled')->first()?->value == '1' ? 'checked' : '' }}>
                            </div>
                        </div>
                        <div class="row g-2">
                            <div class="col-md-6">
                                <input type="text" class="form-control form-control-sm" placeholder="Merchant ID"
                                       value="{{ $settings->where('key', 'click_merchant_id')->first()?->value ?? '' }}">
                            </div>
                            <div class="col-md-6">
                                <input type="password" class="form-control form-control-sm" placeholder="Service ID"
                                       value="{{ $settings->where('key', 'click_service_id')->first()?->value ?? '' }}">
                            </div>
                        </div>
                    </div>

                    <!-- Uzum -->
                    <div class="border rounded p-3 mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="mb-0"><i class="fas fa-mobile-alt text-info me-2"></i>Uzum Bank</h6>
                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input" type="checkbox" id="uzum_enabled"
                                       {{ $settings->where('key', 'uzum_enabled')->first()?->value == '1' ? 'checked' : '' }}>
                            </div>
                        </div>
                        <div class="row g-2">
                            <div class="col-md-6">
                                <input type="text" class="form-control form-control-sm" placeholder="Terminal ID"
                                       value="{{ $settings->where('key', 'uzum_terminal_id')->first()?->value ?? '' }}">
                            </div>
                            <div class="col-md-6">
                                <input type="password" class="form-control form-control-sm" placeholder="Secret Key"
                                       value="{{ $settings->where('key', 'uzum_secret_key')->first()?->value ?? '' }}">
                            </div>
                        </div>
                    </div>

                    <button type="button" class="btn btn-warning w-100">
                        <i class="fas fa-save me-1"></i> To'lov sozlamalarini saqlash
                    </button>
                </div>
            </div>
        </div>

        <!-- Google Services -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fab fa-google me-2" style="color: #4285f4;"></i>
                        Google Xizmatlari
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">Google API va xizmatlarini ulash</p>

                    <form action="#" method="POST" onsubmit="alert('Google sozlamalari hozircha mavjud emas'); return false;">
                        @csrf
                        @method('PUT')

                        <!-- Google Analytics -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Google Analytics ID</label>
                            <input type="text" name="google_analytics_id" class="form-control"
                                   value="{{ $settings->where('key', 'google_analytics_id')->first()?->value ?? '' }}"
                                   placeholder="G-XXXXXXXXXX">
                        </div>

                        <!-- Google OAuth -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">OAuth Client ID</label>
                            <input type="text" name="google_client_id" class="form-control"
                                   value="{{ $settings->where('key', 'google_client_id')->first()?->value ?? '' }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">OAuth Client Secret</label>
                            <input type="password" name="google_client_secret" class="form-control"
                                   value="{{ $settings->where('key', 'google_client_secret')->first()?->value ?? '' }}">
                        </div>

                        <!-- reCAPTCHA -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">reCAPTCHA Site Key</label>
                            <input type="text" name="recaptcha_site_key" class="form-control"
                                   value="{{ $settings->where('key', 'recaptcha_site_key')->first()?->value ?? '' }}">
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="google_login_enabled" id="google_login_enabled"
                                       {{ $settings->where('key', 'google_login_enabled')->first()?->value == '1' ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="google_login_enabled">Google orqali kirishni yoqish</label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-save me-1"></i> Saqlash
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- One ID Integration -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-id-card me-2 text-primary"></i>
                        One ID
                    </h5>
                    <span class="badge {{ $settings->where('key', 'oneid_enabled')->first()?->value == '1' ? 'bg-success' : 'bg-secondary' }}">
                        {{ $settings->where('key', 'oneid_enabled')->first()?->value == '1' ? 'Faol' : 'Nofaol' }}
                    </span>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">O'zbekiston yagona identifikatsiya tizimi</p>

                    <form action="#" method="POST" onsubmit="alert('OneID sozlamalari hozircha mavjud emas'); return false;">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Client ID</label>
                            <input type="text" name="oneid_client_id" class="form-control"
                                   value="{{ $settings->where('key', 'oneid_client_id')->first()?->value ?? '' }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Client Secret</label>
                            <input type="password" name="oneid_client_secret" class="form-control"
                                   value="{{ $settings->where('key', 'oneid_client_secret')->first()?->value ?? '' }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Redirect URI</label>
                            <input type="text" name="oneid_redirect_uri" class="form-control"
                                   value="{{ $settings->where('key', 'oneid_redirect_uri')->first()?->value ?? url('/auth/oneid/callback') }}" readonly>
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="oneid_enabled" id="oneid_enabled"
                                       {{ $settings->where('key', 'oneid_enabled')->first()?->value == '1' ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="oneid_enabled">One ID orqali kirishni yoqish</label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-save me-1"></i> Saqlash
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Webhook Endpoints -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-link text-info me-2"></i>Webhook Endpoints</h5>
                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addWebhookModal">
                        <i class="fas fa-plus me-1"></i> Yangi Webhook
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Nomi</th>
                                    <th>URL</th>
                                    <th>Hodisalar</th>
                                    <th>Holat</th>
                                    <th>Amallar</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($webhooks ?? [] as $webhook)
                                <tr>
                                    <td>{{ $webhook->name }}</td>
                                    <td><code>{{ $webhook->url }}</code></td>
                                    <td>
                                        @foreach($webhook->events ?? [] as $event)
                                        <span class="badge bg-secondary me-1">{{ $event }}</span>
                                        @endforeach
                                    </td>
                                    <td>
                                        @if($webhook->is_active)
                                        <span class="badge bg-success">Faol</span>
                                        @else
                                        <span class="badge bg-secondary">Nofaol</span>
                                        @endif
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary" title="Test yuborish">
                                            <i class="fas fa-paper-plane"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" title="O'chirish">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        <i class="fas fa-link fa-2x mb-2 d-block"></i>
                                        Webhook'lar topilmadi
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Add Webhook -->
<div class="modal fade" id="addWebhookModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-plus-circle me-2"></i>Yangi Webhook</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="#" method="POST" onsubmit="alert('Webhook funksiyasi hozircha mavjud emas'); return false;">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nomi <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">URL <span class="text-danger">*</span></label>
                        <input type="url" name="url" class="form-control" placeholder="https://example.com/webhook" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Hodisalar</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="events[]" value="student.created" id="event_student_created">
                            <label class="form-check-label" for="event_student_created">Yangi talaba</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="events[]" value="grade.updated" id="event_grade_updated">
                            <label class="form-check-label" for="event_grade_updated">Baho yangilandi</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="events[]" value="payment.received" id="event_payment_received">
                            <label class="form-check-label" for="event_payment_received">To'lov qabul qilindi</label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Secret Key</label>
                        <input type="text" name="secret" class="form-control" placeholder="Webhook imzolash uchun maxfiy kalit">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bekor qilish</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Saqlash</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.bg-gradient-purple {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
</style>

@endsection
