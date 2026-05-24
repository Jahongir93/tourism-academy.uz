@extends('layouts.dashboard-new')

@section('title', 'Xavfsizlik Sozlamalari')
@section('page-title', 'Xavfsizlik Sozlamalari')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm bg-gradient-danger text-white">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1"><i class="fas fa-shield-alt me-2"></i>Xavfsizlik Sozlamalari</h4>
                            <p class="mb-0 opacity-75">Parol siyosati, autentifikatsiya va xavfsizlik sozlamalari</p>
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

    <form action="{{ route('settings.security.update') }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row">
            <!-- Parol Siyosati -->
            <div class="col-lg-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-key text-primary me-2"></i>Parol Siyosati</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Minimal parol uzunligi</label>
                            <input type="number" name="min_password_length" class="form-control" min="6" max="32"
                                   value="{{ old('min_password_length', $settings->where('key', 'min_password_length')->first()?->value ?? '8') }}">
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="password_uppercase" id="password_uppercase"
                                       {{ old('password_uppercase', $settings->where('key', 'password_uppercase')->first()?->value ?? '1') == '1' ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="password_uppercase">Katta harf talab qilish (A-Z)</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="password_lowercase" id="password_lowercase"
                                       {{ old('password_lowercase', $settings->where('key', 'password_lowercase')->first()?->value ?? '1') == '1' ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="password_lowercase">Kichik harf talab qilish (a-z)</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="password_numbers" id="password_numbers"
                                       {{ old('password_numbers', $settings->where('key', 'password_numbers')->first()?->value ?? '1') == '1' ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="password_numbers">Raqam talab qilish (0-9)</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="password_special" id="password_special"
                                       {{ old('password_special', $settings->where('key', 'password_special')->first()?->value ?? '0') == '1' ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="password_special">Maxsus belgi talab qilish (!@#$%)</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Parol amal qilish muddati (kun)</label>
                            <input type="number" name="password_expiry_days" class="form-control" min="0"
                                   value="{{ old('password_expiry_days', $settings->where('key', 'password_expiry_days')->first()?->value ?? '90') }}">
                            <small class="text-muted">0 = cheksiz</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Parol tarixini saqlash</label>
                            <input type="number" name="password_history" class="form-control" min="0" max="24"
                                   value="{{ old('password_history', $settings->where('key', 'password_history')->first()?->value ?? '5') }}">
                            <small class="text-muted">So'nggi nechta parolni takrorlash mumkin emas</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Login Xavfsizligi -->
            <div class="col-lg-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-sign-in-alt text-success me-2"></i>Login Xavfsizligi</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Maksimal noto'g'ri urinishlar</label>
                            <input type="number" name="max_login_attempts" class="form-control" min="3" max="10"
                                   value="{{ old('max_login_attempts', $settings->where('key', 'max_login_attempts')->first()?->value ?? '5') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Bloklash davomiyligi (daqiqa)</label>
                            <input type="number" name="lockout_duration" class="form-control" min="1"
                                   value="{{ old('lockout_duration', $settings->where('key', 'lockout_duration')->first()?->value ?? '30') }}">
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="two_factor_auth" id="two_factor_auth"
                                       {{ old('two_factor_auth', $settings->where('key', 'two_factor_auth')->first()?->value ?? '0') == '1' ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="two_factor_auth">Ikki bosqichli autentifikatsiya</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="remember_me" id="remember_me"
                                       {{ old('remember_me', $settings->where('key', 'remember_me')->first()?->value ?? '1') == '1' ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="remember_me">"Meni eslab qol" funksiyasi</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">"Meni eslab qol" muddati (kun)</label>
                            <input type="number" name="remember_me_days" class="form-control" min="1" max="365"
                                   value="{{ old('remember_me_days', $settings->where('key', 'remember_me_days')->first()?->value ?? '30') }}">
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="login_notification" id="login_notification"
                                       {{ old('login_notification', $settings->where('key', 'login_notification')->first()?->value ?? '0') == '1' ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="login_notification">Yangi qurilmadan kirganda xabar yuborish</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sessiya Xavfsizligi -->
            <div class="col-lg-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-clock text-warning me-2"></i>Sessiya Xavfsizligi</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Sessiya muddati (daqiqa)</label>
                            <input type="number" name="session_timeout" class="form-control" min="5"
                                   value="{{ old('session_timeout', $settings->where('key', 'session_timeout')->first()?->value ?? '120') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Faoliyatsizlik vaqti (daqiqa)</label>
                            <input type="number" name="idle_timeout" class="form-control" min="5"
                                   value="{{ old('idle_timeout', $settings->where('key', 'idle_timeout')->first()?->value ?? '30') }}">
                            <small class="text-muted">Faol bo'lmaganda avtomatik chiqish</small>
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="single_session" id="single_session"
                                       {{ old('single_session', $settings->where('key', 'single_session')->first()?->value ?? '0') == '1' ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="single_session">Bitta qurilmadan kirish (boshqalarini chiqarish)</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="secure_session" id="secure_session"
                                       {{ old('secure_session', $settings->where('key', 'secure_session')->first()?->value ?? '1') == '1' ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="secure_session">HTTPS orqali sessiya (xavfsiz cookie)</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="regenerate_session" id="regenerate_session"
                                       {{ old('regenerate_session', $settings->where('key', 'regenerate_session')->first()?->value ?? '1') == '1' ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="regenerate_session">Logindan keyin sessiya ID yangilash</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CAPTCHA va Bot Himoyasi -->
            <div class="col-lg-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-robot text-info me-2"></i>CAPTCHA va Bot Himoyasi</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="captcha_enabled" id="captcha_enabled"
                                       {{ old('captcha_enabled', $settings->where('key', 'captcha_enabled')->first()?->value ?? '0') == '1' ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="captcha_enabled">CAPTCHA ni yoqish</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">CAPTCHA turi</label>
                            <select name="captcha_type" class="form-select">
                                <option value="recaptcha" {{ old('captcha_type', $settings->where('key', 'captcha_type')->first()?->value ?? 'recaptcha') == 'recaptcha' ? 'selected' : '' }}>Google reCAPTCHA</option>
                                <option value="hcaptcha" {{ old('captcha_type', $settings->where('key', 'captcha_type')->first()?->value ?? 'recaptcha') == 'hcaptcha' ? 'selected' : '' }}>hCaptcha</option>
                                <option value="simple" {{ old('captcha_type', $settings->where('key', 'captcha_type')->first()?->value ?? 'recaptcha') == 'simple' ? 'selected' : '' }}>Oddiy matematik</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">reCAPTCHA Site Key</label>
                            <input type="text" name="recaptcha_site_key" class="form-control"
                                   value="{{ old('recaptcha_site_key', $settings->where('key', 'recaptcha_site_key')->first()?->value ?? '') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">reCAPTCHA Secret Key</label>
                            <input type="password" name="recaptcha_secret_key" class="form-control"
                                   value="{{ old('recaptcha_secret_key', $settings->where('key', 'recaptcha_secret_key')->first()?->value ?? '') }}">
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="captcha_on_login" id="captcha_on_login"
                                       {{ old('captcha_on_login', $settings->where('key', 'captcha_on_login')->first()?->value ?? '1') == '1' ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="captcha_on_login">Login sahifasida</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="captcha_on_register" id="captcha_on_register"
                                       {{ old('captcha_on_register', $settings->where('key', 'captcha_on_register')->first()?->value ?? '1') == '1' ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="captcha_on_register">Ro'yxatdan o'tish sahifasida</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- IP Cheklash -->
            <div class="col-lg-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-network-wired text-danger me-2"></i>IP Cheklash</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="ip_restriction" id="ip_restriction"
                                       {{ old('ip_restriction', $settings->where('key', 'ip_restriction')->first()?->value ?? '0') == '1' ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="ip_restriction">IP cheklashni yoqish</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Ruxsat etilgan IP manzillar</label>
                            <textarea name="allowed_ips" class="form-control" rows="4" placeholder="Har bir qatorda bitta IP">{{ old('allowed_ips', $settings->where('key', 'allowed_ips')->first()?->value ?? '') }}</textarea>
                            <small class="text-muted">Masalan: 192.168.1.1, 10.0.0.0/24</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Bloklangan IP manzillar</label>
                            <textarea name="blocked_ips" class="form-control" rows="4" placeholder="Har bir qatorda bitta IP">{{ old('blocked_ips', $settings->where('key', 'blocked_ips')->first()?->value ?? '') }}</textarea>
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="admin_ip_restriction" id="admin_ip_restriction"
                                       {{ old('admin_ip_restriction', $settings->where('key', 'admin_ip_restriction')->first()?->value ?? '0') == '1' ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="admin_ip_restriction">Faqat admin panel uchun IP cheklash</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SSL va HTTPS -->
            <div class="col-lg-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-lock text-success me-2"></i>SSL va HTTPS</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="force_https" id="force_https"
                                       {{ old('force_https', $settings->where('key', 'force_https')->first()?->value ?? '0') == '1' ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="force_https">HTTPS ga majburiy yo'naltirish</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="hsts_enabled" id="hsts_enabled"
                                       {{ old('hsts_enabled', $settings->where('key', 'hsts_enabled')->first()?->value ?? '0') == '1' ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="hsts_enabled">HSTS (HTTP Strict Transport Security)</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="csrf_protection" id="csrf_protection"
                                       {{ old('csrf_protection', $settings->where('key', 'csrf_protection')->first()?->value ?? '1') == '1' ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="csrf_protection">CSRF himoyasi</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="xss_protection" id="xss_protection"
                                       {{ old('xss_protection', $settings->where('key', 'xss_protection')->first()?->value ?? '1') == '1' ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="xss_protection">XSS himoyasi</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Content Security Policy</label>
                            <input type="text" name="csp_policy" class="form-control"
                                   value="{{ old('csp_policy', $settings->where('key', 'csp_policy')->first()?->value ?? "default-src 'self'") }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bloklangan Foydalanuvchilar -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-user-slash text-danger me-2"></i>Bloklangan Foydalanuvchilar</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Foydalanuvchi</th>
                                        <th>Sabab</th>
                                        <th>Bloklangan sana</th>
                                        <th>Blok muddati</th>
                                        <th>Amallar</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($blockedUsers ?? [] as $index => $user)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $user->name ?? 'Noma\'lum' }}</td>
                                        <td>{{ $user->block_reason ?? '-' }}</td>
                                        <td>{{ $user->blocked_at ? \Carbon\Carbon::parse($user->blocked_at)->format('d.m.Y H:i') : '-' }}</td>
                                        <td>{{ $user->blocked_until ? \Carbon\Carbon::parse($user->blocked_until)->format('d.m.Y H:i') : 'Doimiy' }}</td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-outline-success" title="Blokdan chiqarish">
                                                <i class="fas fa-unlock"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">
                                            <i class="fas fa-user-check fa-2x mb-2 d-block text-success"></i>
                                            Bloklangan foydalanuvchilar yo'q
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

        <!-- Submit -->
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-end">
                        <button type="reset" class="btn btn-outline-secondary me-2">
                            <i class="fas fa-undo me-1"></i> Bekor qilish
                        </button>
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-save me-1"></i> Saqlash
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
.bg-gradient-danger {
    background: linear-gradient(135deg, #ff416c 0%, #ff4b2b 100%);
}
</style>
@endsection
