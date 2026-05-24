@extends('layouts.auth')

@section('title', __('auth.register_title'))
@section('subtitle', __('auth.register_subtitle'))

@section('content')
    <form action="{{ route('register') }}" method="POST" x-data="registrationForm()" @submit="handleSubmit">
        @csrf

        <!-- User Type Selection - Modern Card Style -->
        <div class="user-type-selector">
            <label class="user-type-card" :class="{ 'active': userType === 'uzbek' }">
                <input type="radio" name="user_type" value="uzbek" x-model="userType" class="hidden-radio">
                <div class="card-content">
                    <div class="card-icon">
                        <i class="fas fa-flag"></i>
                    </div>
                    <div class="card-text">
                        <span class="card-title">{{ __('auth.uzbek_citizen') }}</span>
                        <span class="card-desc">Telefon orqali</span>
                    </div>
                    <div class="check-mark">
                        <i class="fas fa-check"></i>
                    </div>
                </div>
            </label>

            <label class="user-type-card" :class="{ 'active': userType === 'foreign' }">
                <input type="radio" name="user_type" value="foreign" x-model="userType" class="hidden-radio">
                <div class="card-content">
                    <div class="card-icon foreign">
                        <i class="fas fa-globe"></i>
                    </div>
                    <div class="card-text">
                        <span class="card-title">{{ __('auth.foreign_citizen') }}</span>
                        <span class="card-desc">Email orqali</span>
                    </div>
                    <div class="check-mark">
                        <i class="fas fa-check"></i>
                    </div>
                </div>
            </label>
        </div>

        <!-- Full Name -->
        <div class="form-input">
            <input id="name" name="name" type="text" required
                   placeholder="{{ __('auth.full_name_placeholder') }}"
                   value="{{ old('name') }}"
                   minlength="3">
            <i class="fas fa-user"></i>
        </div>

        <!-- Phone Input (for Uzbek users) -->
        <div x-show="userType === 'uzbek'" x-transition class="form-input phone-input-group">
            <span class="phone-prefix">+998</span>
            <input id="phone_number" type="tel"
                   placeholder="90 123 45 67"
                   x-model="phoneNumber"
                   @input="formatPhone"
                   maxlength="12"
                   :required="userType === 'uzbek'">
            <input type="hidden" name="phone" :value="'+998' + phoneNumber.replace(/\s/g, '')">
            <i class="fas fa-mobile-alt" style="left: auto; right: 1rem;"></i>
        </div>

        <!-- Email Input (for Foreign users) -->
        <div x-show="userType === 'foreign'" x-transition class="form-input">
            <input id="email" name="email" type="email"
                   placeholder="{{ __('auth.email_placeholder') }}"
                   value="{{ old('email') }}"
                   :required="userType === 'foreign'"
                   :disabled="userType !== 'foreign'">
            <i class="fas fa-envelope"></i>
        </div>

        <!-- Role Selection -->
        <div class="form-input">
            <select name="role" required class="form-select">
                <option value="">{{ __('auth.user_type_placeholder') }}</option>
                <option value="Student" {{ old('role') === 'Student' ? 'selected' : '' }}>{{ __('auth.student') }}</option>
                <option value="Teacher" {{ old('role') === 'Teacher' ? 'selected' : '' }}>{{ __('auth.teacher') }}</option>
            </select>
            <i class="fas fa-user-tag"></i>
        </div>

        <!-- Password -->
        <div class="form-input password-input">
            <input id="password" name="password" type="password" required
                   placeholder="{{ __('auth.password_placeholder') }}"
                   minlength="8"
                   x-model="password">
            <i class="fas fa-lock"></i>
            <button type="button" class="password-toggle" @click="togglePassword('password')">
                <i class="fas" :class="showPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
            </button>
        </div>

        <!-- Password Strength Indicator -->
        <div class="password-strength" x-show="password.length > 0">
            <div class="strength-bar">
                <div class="strength-fill" :class="passwordStrengthClass" :style="'width: ' + passwordStrength + '%'"></div>
            </div>
            <span class="strength-text" :class="passwordStrengthClass" x-text="passwordStrengthText"></span>
        </div>

        <!-- Confirm Password -->
        <div class="form-input password-input">
            <input id="password_confirmation" name="password_confirmation" type="password" required
                   placeholder="{{ __('auth.confirm_password_placeholder') }}"
                   x-model="passwordConfirmation">
            <i class="fas fa-lock"></i>
            <button type="button" class="password-toggle" @click="togglePassword('password_confirmation')">
                <i class="fas" :class="showPasswordConfirmation ? 'fa-eye-slash' : 'fa-eye'"></i>
            </button>
        </div>

        <!-- Password Match Indicator -->
        <div x-show="passwordConfirmation.length > 0" class="password-match">
            <template x-if="password === passwordConfirmation">
                <span class="match-success"><i class="fas fa-check-circle"></i> Parollar mos keladi</span>
            </template>
            <template x-if="password !== passwordConfirmation">
                <span class="match-error"><i class="fas fa-times-circle"></i> Parollar mos kelmaydi</span>
            </template>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn-primary" :disabled="isSubmitting">
            <template x-if="!isSubmitting">
                <span><i class="fas fa-user-plus"></i> {{ __('auth.create_account') }}</span>
            </template>
            <template x-if="isSubmitting">
                <span><i class="fas fa-spinner fa-spin"></i> Yuklanmoqda...</span>
            </template>
        </button>

        <!-- Info Text -->
        <div class="info-text" x-show="userType === 'uzbek'">
            <i class="fas fa-info-circle"></i>
            Telefon raqamingizga SMS orqali tasdiqlash kodi yuboriladi
        </div>
        <div class="info-text" x-show="userType === 'foreign'">
            <i class="fas fa-info-circle"></i>
            Email manzilingizga tasdiqlash havolasi yuboriladi
        </div>

        <div class="divider"></div>

        <div style="text-align: center;">
            <span style="color: #6b7280; font-size: 0.875rem;">{{ __('auth.already_have_account') }}</span>
            <a href="{{ route('login') }}" class="link-hover" style="margin-left: 0.25rem;">
                {{ __('auth.login_now') }}
            </a>
        </div>
    </form>

    <div style="text-align:center; color:#6b7280; font-size:0.875rem; margin: 1rem 0 0.5rem; position:relative;">
        <span style="background:#fff; padding: 0 0.75rem; position:relative; z-index:1;">yoki ijtimoiy tarmoq orqali</span>
        <div style="position:absolute; top:50%; left:0; right:0; border-top:1px solid #e5e7eb; z-index:0;"></div>
    </div>
    @includeIf('auth.partials.social-buttons')

    <div style="text-align: center; margin-top: 0.75rem;">
        <a href="{{ route('home') }}" style="display: inline-flex; align-items: center; gap: 0.5rem; color: #6b7280; text-decoration: none; font-size: 0.875rem; transition: color 0.2s;">
            <i class="fas fa-home"></i>
            <span>{{ __('auth.back_to_home') }}</span>
        </a>
    </div>

    <style>
        .hidden-radio {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        .user-type-selector {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.75rem;
            margin-bottom: 1rem;
        }

        .user-type-card {
            background: #f8fafc;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            padding: 0.75rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .user-type-card:hover {
            border-color: #c7d2fe;
            background: #f0f5ff;
        }

        .user-type-card.active {
            border-color: #4338CA;
            background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%);
        }

        .card-content {
            display: flex;
            align-items: center;
            gap: 0.625rem;
        }

        .card-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: linear-gradient(135deg, #4338CA, #3730A3);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 0.875rem;
            flex-shrink: 0;
        }

        .card-icon.foreign {
            background: linear-gradient(135deg, #059669, #047857);
        }

        .card-text {
            flex: 1;
            min-width: 0;
        }

        .card-title {
            display: block;
            font-weight: 600;
            font-size: 0.8rem;
            color: #1f2937;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .card-desc {
            display: block;
            font-size: 0.7rem;
            color: #6b7280;
        }

        .check-mark {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            border: 2px solid #d1d5db;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
            flex-shrink: 0;
        }

        .check-mark i {
            font-size: 0.6rem;
            color: transparent;
        }

        .user-type-card.active .check-mark {
            background: linear-gradient(135deg, #4338CA, #3730A3);
            border-color: #4338CA;
        }

        .user-type-card.active .check-mark i {
            color: white;
        }

        /* Phone Input Group */
        .phone-input-group {
            display: flex;
            align-items: center;
            padding-left: 0 !important;
        }

        .phone-prefix {
            background: linear-gradient(135deg, #4338CA, #3730A3);
            color: white;
            padding: 0.65rem 0.75rem;
            font-weight: 600;
            font-size: 0.875rem;
            border-radius: 10px 0 0 10px;
            white-space: nowrap;
        }

        .phone-input-group input {
            border-radius: 0 10px 10px 0 !important;
            padding-left: 0.875rem !important;
            letter-spacing: 1px;
        }

        /* Form Select */
        .form-select {
            padding-left: 2.75rem;
            appearance: none;
            background-image: url('data:image/svg+xml;charset=UTF-8,%3csvg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"%3e%3cpolyline points="6 9 12 15 18 9"%3e%3c/polyline%3e%3c/svg%3e');
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            background-size: 1em;
        }

        /* Password Input */
        .password-input {
            position: relative;
        }

        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: #6b7280;
            padding: 0;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: color 0.2s;
        }

        .password-toggle:hover {
            color: #4338CA;
        }

        /* Password Strength */
        .password-strength {
            margin-bottom: 0.75rem;
            margin-top: -0.5rem;
        }

        .strength-bar {
            height: 4px;
            background: #e5e7eb;
            border-radius: 2px;
            overflow: hidden;
            margin-bottom: 0.25rem;
        }

        .strength-fill {
            height: 100%;
            border-radius: 2px;
            transition: all 0.3s;
        }

        .strength-fill.weak { background: #ef4444; }
        .strength-fill.medium { background: #f59e0b; }
        .strength-fill.strong { background: #10b981; }

        .strength-text {
            font-size: 0.7rem;
            font-weight: 500;
        }

        .strength-text.weak { color: #ef4444; }
        .strength-text.medium { color: #f59e0b; }
        .strength-text.strong { color: #10b981; }

        /* Password Match */
        .password-match {
            margin-bottom: 0.75rem;
            margin-top: -0.5rem;
            font-size: 0.75rem;
        }

        .match-success {
            color: #10b981;
        }

        .match-error {
            color: #ef4444;
        }

        /* Info Text */
        .info-text {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 8px;
            padding: 0.625rem 0.875rem;
            font-size: 0.75rem;
            color: #166534;
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .info-text i {
            color: #22c55e;
        }

        /* Button disabled state */
        .btn-primary:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none !important;
        }

        @media (max-width: 480px) {
            .user-type-selector {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <script>
        function registrationForm() {
            return {
                userType: '{{ old('user_type', 'uzbek') }}',
                phoneNumber: '{{ old('phone') ? preg_replace('/^\+998/', '', old('phone')) : '' }}',
                password: '',
                passwordConfirmation: '',
                showPassword: false,
                showPasswordConfirmation: false,
                isSubmitting: false,

                formatPhone() {
                    // Remove all non-digits
                    let value = this.phoneNumber.replace(/\D/g, '');

                    // Limit to 9 digits
                    if (value.length > 9) {
                        value = value.substring(0, 9);
                    }

                    // Format as XX XXX XX XX
                    let formatted = '';
                    if (value.length > 0) formatted += value.substring(0, 2);
                    if (value.length > 2) formatted += ' ' + value.substring(2, 5);
                    if (value.length > 5) formatted += ' ' + value.substring(5, 7);
                    if (value.length > 7) formatted += ' ' + value.substring(7, 9);

                    this.phoneNumber = formatted;
                },

                togglePassword(field) {
                    const input = document.getElementById(field);
                    if (field === 'password') {
                        this.showPassword = !this.showPassword;
                        input.type = this.showPassword ? 'text' : 'password';
                    } else {
                        this.showPasswordConfirmation = !this.showPasswordConfirmation;
                        input.type = this.showPasswordConfirmation ? 'text' : 'password';
                    }
                },

                get passwordStrength() {
                    const pass = this.password;
                    let strength = 0;

                    if (pass.length >= 8) strength += 25;
                    if (pass.match(/[a-z]/)) strength += 25;
                    if (pass.match(/[A-Z]/)) strength += 25;
                    if (pass.match(/[0-9]/)) strength += 15;
                    if (pass.match(/[^a-zA-Z0-9]/)) strength += 10;

                    return Math.min(strength, 100);
                },

                get passwordStrengthClass() {
                    if (this.passwordStrength < 40) return 'weak';
                    if (this.passwordStrength < 70) return 'medium';
                    return 'strong';
                },

                get passwordStrengthText() {
                    if (this.passwordStrength < 40) return 'Zaif parol';
                    if (this.passwordStrength < 70) return 'O\'rtacha parol';
                    return 'Kuchli parol';
                },

                handleSubmit(e) {
                    // Validate phone for Uzbek users
                    if (this.userType === 'uzbek') {
                        const digits = this.phoneNumber.replace(/\D/g, '');
                        if (digits.length !== 9) {
                            e.preventDefault();
                            alert('Iltimos, to\'liq telefon raqam kiriting (9 ta raqam)');
                            return false;
                        }
                    }

                    // Validate password match
                    if (this.password !== this.passwordConfirmation) {
                        e.preventDefault();
                        alert('Parollar mos kelmayapti');
                        return false;
                    }

                    this.isSubmitting = true;
                }
            }
        }
    </script>
@endsection
