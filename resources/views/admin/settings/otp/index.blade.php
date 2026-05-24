@extends('layouts.dashboard-new')

@section('title', 'OTP Sozlamalari')

@section('content')
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">
                <i class="fas fa-shield-alt text-primary me-2"></i>OTP Sozlamalari
            </h1>
            <p class="text-muted mb-0">SMS va Email orqali tasdiqlash kodlari sozlamalari</p>
        </div>
        <a href="{{ route('admin.settings.users.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Sozlamalarga qaytish
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('admin.settings.otp.update') }}" method="POST" id="otpSettingsForm">
        @csrf
        @method('PUT')

        <div class="row">
            <!-- SMS Provider Settings -->
            <div class="col-lg-6">
                <div class="card mb-4 border-0 shadow-sm">
                    <div class="card-header bg-gradient" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <h5 class="mb-0 text-white">
                            <i class="fas fa-sms me-2"></i>SMS Provider Sozlamalari
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- SMS Enabled -->
                        <div class="form-check form-switch mb-4">
                            <input class="form-check-input" type="checkbox" id="sms_enabled" name="sms_enabled"
                                   {{ $settings->sms_enabled ? 'checked' : '' }} style="width: 50px; height: 25px;">
                            <label class="form-check-label fw-bold ms-2" for="sms_enabled">
                                SMS orqali tasdiqlash faol
                            </label>
                        </div>

                        <!-- SMS Provider Selection -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-server me-1 text-muted"></i>SMS Provider
                            </label>
                            <div class="row g-2">
                                @foreach($providers as $key => $provider)
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="sms_provider"
                                               id="provider_{{ $key }}" value="{{ $key }}"
                                               {{ ($settings->sms_provider ?? 'eskiz') == $key ? 'checked' : '' }}
                                               onchange="updateProviderUrl('{{ $key }}')">
                                        <label class="form-check-label" for="provider_{{ $key }}">
                                            <strong>{{ $provider['name'] }}</strong>
                                            <small class="d-block text-muted">{{ $provider['description'] }}</small>
                                        </label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- API URL -->
                        <div class="mb-3" id="customUrlDiv">
                            <label for="sms_api_url" class="form-label fw-semibold">
                                <i class="fas fa-link me-1 text-muted"></i>API URL
                            </label>
                            <input type="url" class="form-control" id="sms_api_url" name="sms_api_url"
                                   value="{{ $settings->sms_api_url }}" placeholder="https://api.example.com/sms">
                        </div>

                        <!-- API Key -->
                        <div class="mb-3">
                            <label for="sms_api_key" class="form-label fw-semibold">
                                <i class="fas fa-key me-1 text-muted"></i>API Key / Token
                            </label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="sms_api_key" name="sms_api_key"
                                       value="{{ $settings->sms_api_key }}" placeholder="API kalitini kiriting">
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('sms_api_key')">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <!-- API Secret (for some providers) -->
                        <div class="mb-3">
                            <label for="sms_api_secret" class="form-label fw-semibold">
                                <i class="fas fa-lock me-1 text-muted"></i>API Secret (ixtiyoriy)
                            </label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="sms_api_secret" name="sms_api_secret"
                                       value="{{ $settings->sms_api_secret }}" placeholder="API secretni kiriting">
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('sms_api_secret')">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Sender Name -->
                        <div class="mb-3">
                            <label for="sms_sender_name" class="form-label fw-semibold">
                                <i class="fas fa-user-tag me-1 text-muted"></i>Yuboruvchi nomi
                            </label>
                            <input type="text" class="form-control" id="sms_sender_name" name="sms_sender_name"
                                   value="{{ $settings->sms_sender_name ?? 'TourismAcad' }}" maxlength="11">
                            <small class="text-muted">Maksimum 11 ta belgi</small>
                        </div>

                        <!-- Eskiz Token Helper -->
                        <div class="card bg-light border-0 mb-3" id="eskizHelper" style="{{ ($settings->sms_provider ?? 'eskiz') !== 'eskiz' ? 'display:none' : '' }}">
                            <div class="card-body py-3">
                                <h6 class="mb-2"><i class="fas fa-info-circle text-info me-1"></i>Eskiz Token olish</h6>
                                <div class="row g-2">
                                    <div class="col-md-5">
                                        <input type="email" class="form-control form-control-sm" id="eskiz_email" placeholder="Eskiz email">
                                    </div>
                                    <div class="col-md-5">
                                        <input type="password" class="form-control form-control-sm" id="eskiz_password" placeholder="Eskiz parol">
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-info btn-sm w-100" onclick="getEskizToken()">
                                            <i class="fas fa-sync-alt"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Test SMS -->
                        <div class="card bg-warning bg-opacity-10 border-warning">
                            <div class="card-body py-3">
                                <h6 class="mb-2"><i class="fas fa-vial text-warning me-1"></i>Test SMS yuborish</h6>
                                <div class="input-group">
                                    <span class="input-group-text">+998</span>
                                    <input type="text" class="form-control" id="test_phone" placeholder="901234567" maxlength="9">
                                    <button type="button" class="btn btn-warning" onclick="sendTestSms()">
                                        <i class="fas fa-paper-plane me-1"></i>Test
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Email Settings -->
                <div class="card mb-4 border-0 shadow-sm">
                    <div class="card-header bg-gradient" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
                        <h5 class="mb-0 text-white">
                            <i class="fas fa-envelope me-2"></i>Email Sozlamalari
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Email Enabled -->
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="email_verification_enabled"
                                   name="email_verification_enabled"
                                   {{ $settings->email_verification_enabled ? 'checked' : '' }} style="width: 50px; height: 25px;">
                            <label class="form-check-label fw-bold ms-2" for="email_verification_enabled">
                                Email orqali tasdiqlash faol (Chet elliklar uchun)
                            </label>
                        </div>

                        <div class="alert alert-info mb-0">
                            <i class="fas fa-info-circle me-1"></i>
                            Email sozlamalari <code>.env</code> faylida MAIL_* o'zgaruvchilari orqali sozlanadi.
                        </div>
                    </div>
                </div>
            </div>

            <!-- OTP Configuration -->
            <div class="col-lg-6">
                <div class="card mb-4 border-0 shadow-sm">
                    <div class="card-header bg-gradient" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                        <h5 class="mb-0 text-white">
                            <i class="fas fa-cog me-2"></i>OTP Konfiguratsiyasi
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="otp_length" class="form-label fw-semibold">
                                    <i class="fas fa-ruler me-1 text-muted"></i>Kod uzunligi
                                </label>
                                <select class="form-select" id="otp_length" name="otp_length">
                                    @for($i = 4; $i <= 8; $i++)
                                        <option value="{{ $i }}" {{ ($settings->otp_length ?? 6) == $i ? 'selected' : '' }}>
                                            {{ $i }} ta raqam
                                        </option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="otp_expiry_minutes" class="form-label fw-semibold">
                                    <i class="fas fa-clock me-1 text-muted"></i>Amal qilish muddati
                                </label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="otp_expiry_minutes" name="otp_expiry_minutes"
                                           value="{{ $settings->otp_expiry_minutes ?? 5 }}" min="1" max="30">
                                    <span class="input-group-text">daqiqa</span>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="max_attempts" class="form-label fw-semibold">
                                    <i class="fas fa-redo me-1 text-muted"></i>Maksimum urinishlar
                                </label>
                                <input type="number" class="form-control" id="max_attempts" name="max_attempts"
                                       value="{{ $settings->max_attempts ?? 3 }}" min="1" max="10">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="resend_cooldown_seconds" class="form-label fw-semibold">
                                    <i class="fas fa-hourglass-half me-1 text-muted"></i>Qayta yuborish kutish
                                </label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="resend_cooldown_seconds" name="resend_cooldown_seconds"
                                           value="{{ $settings->resend_cooldown_seconds ?? 60 }}" min="30" max="300">
                                    <span class="input-group-text">soniya</span>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="max_otp_per_hour" class="form-label fw-semibold">
                                    <i class="fas fa-tachometer-alt me-1 text-muted"></i>Soatiga maksimum
                                </label>
                                <input type="number" class="form-control" id="max_otp_per_hour" name="max_otp_per_hour"
                                       value="{{ $settings->max_otp_per_hour ?? 5 }}" min="1" max="20">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="max_otp_per_day" class="form-label fw-semibold">
                                    <i class="fas fa-calendar-day me-1 text-muted"></i>Kuniga maksimum
                                </label>
                                <input type="number" class="form-control" id="max_otp_per_day" name="max_otp_per_day"
                                       value="{{ $settings->max_otp_per_day ?? 10 }}" min="1" max="50">
                            </div>
                        </div>

                        <!-- Test Mode -->
                        <div class="card bg-danger bg-opacity-10 border-danger mt-3">
                            <div class="card-body py-3">
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" id="is_test_mode" name="is_test_mode"
                                           {{ $settings->is_test_mode ? 'checked' : '' }} style="width: 50px; height: 25px;">
                                    <label class="form-check-label fw-bold ms-2 text-danger" for="is_test_mode">
                                        <i class="fas fa-flask me-1"></i>Test rejimi
                                    </label>
                                </div>
                                <small class="text-muted d-block mb-2">Test rejimida SMS yuborilmaydi, quyidagi kod ishlatiladi</small>
                                <input type="text" class="form-control form-control-sm" id="test_otp_code" name="test_otp_code"
                                       value="{{ $settings->test_otp_code }}" placeholder="123456" maxlength="8">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Message Templates -->
                <div class="card mb-4 border-0 shadow-sm">
                    <div class="card-header bg-gradient" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                        <h5 class="mb-0 text-white">
                            <i class="fas fa-file-alt me-2"></i>Xabar Shablonlari
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Nav tabs -->
                        <ul class="nav nav-tabs mb-3" role="tablist">
                            <li class="nav-item">
                                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#sms-templates">
                                    <i class="fas fa-sms me-1"></i>SMS
                                </button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#email-templates">
                                    <i class="fas fa-envelope me-1"></i>Email
                                </button>
                            </li>
                        </ul>

                        <div class="tab-content">
                            <!-- SMS Templates -->
                            <div class="tab-pane fade show active" id="sms-templates">
                                <div class="alert alert-secondary py-2 mb-3">
                                    <small><strong>O'zgaruvchilar:</strong> <code>{otp}</code> - tasdiqlash kodi, <code>{minutes}</code> - amal qilish muddati</small>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">
                                        <img src="{{ asset('assets/flags/uz.png') }}" width="20" class="me-1" alt="UZ">O'zbekcha
                                    </label>
                                    <textarea class="form-control" name="sms_template_uz" rows="2" required>{{ $settings->sms_template_uz ?? 'Tourism Academy Samarkand. Sizning tasdiqlash kodingiz: {otp}. Kod {minutes} daqiqa amal qiladi.' }}</textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">
                                        <img src="{{ asset('assets/flags/ru.png') }}" width="20" class="me-1" alt="RU">Ruscha
                                    </label>
                                    <textarea class="form-control" name="sms_template_ru" rows="2">{{ $settings->sms_template_ru }}</textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">
                                        <img src="{{ asset('assets/flags/en.png') }}" width="20" class="me-1" alt="EN">Inglizcha
                                    </label>
                                    <textarea class="form-control" name="sms_template_en" rows="2">{{ $settings->sms_template_en }}</textarea>
                                </div>
                            </div>

                            <!-- Email Templates -->
                            <div class="tab-pane fade" id="email-templates">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">
                                        <img src="{{ asset('assets/flags/uz.png') }}" width="20" class="me-1" alt="UZ">Mavzu (O'zbekcha)
                                    </label>
                                    <input type="text" class="form-control" name="email_subject_uz"
                                           value="{{ $settings->email_subject_uz ?? 'Tasdiqlash kodi - Tourism Academy' }}">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Email matni (O'zbekcha)</label>
                                    <textarea class="form-control" name="email_template_uz" rows="4">{{ $settings->email_template_uz ?? '<h2>Tasdiqlash kodi</h2><p>Sizning tasdiqlash kodingiz: <strong>{otp}</strong></p><p>Kod {minutes} daqiqa amal qiladi.</p>' }}</textarea>
                                </div>

                                <hr>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">
                                        <img src="{{ asset('assets/flags/ru.png') }}" width="20" class="me-1" alt="RU">Mavzu (Ruscha)
                                    </label>
                                    <input type="text" class="form-control" name="email_subject_ru"
                                           value="{{ $settings->email_subject_ru }}">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Email matni (Ruscha)</label>
                                    <textarea class="form-control" name="email_template_ru" rows="4">{{ $settings->email_template_ru }}</textarea>
                                </div>

                                <hr>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">
                                        <img src="{{ asset('assets/flags/en.png') }}" width="20" class="me-1" alt="EN">Mavzu (Inglizcha)
                                    </label>
                                    <input type="text" class="form-control" name="email_subject_en"
                                           value="{{ $settings->email_subject_en }}">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Email matni (Inglizcha)</label>
                                    <textarea class="form-control" name="email_template_en" rows="4">{{ $settings->email_template_en }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Save Button -->
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-info-circle text-muted me-1"></i>
                            <span class="text-muted">O'zgarishlar avtomatik saqlanmaydi</span>
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg px-5">
                            <i class="fas fa-save me-2"></i>Saqlash
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
const providers = @json($providers);

function updateProviderUrl(providerKey) {
    const urlInput = document.getElementById('sms_api_url');
    const customUrlDiv = document.getElementById('customUrlDiv');
    const eskizHelper = document.getElementById('eskizHelper');

    if (providerKey === 'custom') {
        urlInput.readOnly = false;
        urlInput.value = '';
        customUrlDiv.style.display = 'block';
    } else {
        urlInput.readOnly = true;
        urlInput.value = providers[providerKey]?.url || '';
    }

    eskizHelper.style.display = providerKey === 'eskiz' ? 'block' : 'none';
}

function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    const icon = input.nextElementSibling.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}

function sendTestSms() {
    const phone = document.getElementById('test_phone').value;
    if (!phone || phone.length !== 9) {
        alert("Iltimos, to'g'ri telefon raqam kiriting (9 ta raqam)");
        return;
    }

    const btn = event.target.closest('button');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

    fetch('{{ route("admin.settings.otp.test-sms") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ test_phone: '+998' + phone })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('success', data.message);
        } else {
            showToast('error', data.message);
        }
    })
    .catch(error => {
        showToast('error', 'Xatolik yuz berdi: ' + error.message);
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-paper-plane me-1"></i>Test';
    });
}

function getEskizToken() {
    const email = document.getElementById('eskiz_email').value;
    const password = document.getElementById('eskiz_password').value;

    if (!email || !password) {
        alert("Iltimos, email va parolni kiriting");
        return;
    }

    const btn = event.target.closest('button');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

    fetch('{{ route("admin.settings.otp.get-eskiz-token") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ email, password })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('sms_api_key').value = data.token;
            showToast('success', data.message);
        } else {
            showToast('error', data.message);
        }
    })
    .catch(error => {
        showToast('error', 'Xatolik yuz berdi: ' + error.message);
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-sync-alt"></i>';
    });
}

function showToast(type, message) {
    const toast = document.createElement('div');
    toast.className = `alert alert-${type === 'success' ? 'success' : 'danger'} position-fixed shadow-lg`;
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px; animation: slideIn 0.3s ease;';
    toast.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>
        ${message}
        <button type="button" class="btn-close float-end" onclick="this.parentElement.remove()"></button>
    `;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 5000);
}

// Initialize on load
document.addEventListener('DOMContentLoaded', function() {
    const selectedProvider = document.querySelector('input[name="sms_provider"]:checked');
    if (selectedProvider && selectedProvider.value !== 'custom') {
        document.getElementById('sms_api_url').readOnly = true;
    }
});
</script>

<style>
@keyframes slideIn {
    from { opacity: 0; transform: translateX(20px); }
    to { opacity: 1; transform: translateX(0); }
}

.form-check-input:checked {
    background-color: #667eea;
    border-color: #667eea;
}

.card-header.bg-gradient {
    border-bottom: none;
}

.nav-tabs .nav-link.active {
    font-weight: 600;
    color: #667eea;
    border-color: #667eea #667eea #fff;
}
</style>
@endsection
