@extends('layouts.auth')

@section('title', __('auth.login'))

@section('content')
    <form action="{{ route('login') }}" method="POST">
        @csrf

        <div class="form-input">
            <input type="text" name="login" id="login" required
                placeholder="{{ __('auth.phone_or_email') }}"
                value="{{ old('login') }}">
            <i class="fas fa-user"></i>
        </div>

        <div class="form-input" style="position: relative;">
            <input type="password" name="password" id="password" required
                placeholder="{{ __('auth.password') }}">
            <i class="fas fa-lock"></i>
            <button type="button" class="password-toggle" onclick="togglePassword('password')" style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: #6b7280; padding: 0; width: 24px; height: 24px; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-eye" id="password-toggle-icon"></i>
            </button>
        </div>

        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem;">
            <label class="custom-checkbox">
                <input type="checkbox" name="remember">
                <span class="checkmark"></span>
                {{ __('auth.remember_me') }}
            </label>
            <a href="{{ route('password.request') }}" class="link-hover">
                {{ __('auth.forgot_password') }}
            </a>
        </div>

        <button type="submit" class="btn-primary">
            <i class="fas fa-sign-in-alt"></i>
            {{ __('auth.sign_in') }}
        </button>

        <div class="divider-solid"></div>

        <div style="text-align: center; margin-bottom: 1rem;">
            <span style="font-size: 0.8125rem; color: #6b7280;">{{ __('auth.dont_have_account') }}</span>
            <a href="{{ route('register') }}" class="link-hover" style="margin-left: 0.375rem;">
                {{ __('auth.register_now') }}
            </a>
        </div>

        <a href="{{ route('student.register.form') }}" class="btn-student">
            <i class="fas fa-user-graduate"></i>
            {{ __('auth.quick_register_student') }}
        </a>

        <div style="text-align:center; color:#6b7280; font-size:0.875rem; margin: 1rem 0 0.5rem; position:relative;">
            <span style="background:#fff; padding: 0 0.75rem; position:relative; z-index:1;">yoki ijtimoiy tarmoq orqali</span>
            <div style="position:absolute; top:50%; left:0; right:0; border-top:1px solid #e5e7eb; z-index:0;"></div>
        </div>

        @includeIf('auth.partials.social-buttons'){{-- Google/Facebook/Telegram buttons --}}

        <div class="divider"></div>

        <div>
            <p style="text-align: center; font-size: 0.8125rem; color: #6b7280; margin-bottom: 0.75rem; font-weight: 500;">{{ __('auth.other_services') }}</p>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.625rem;">
                <a href="{{ route('home') }}" class="service-btn">
                    <i class="fas fa-home"></i>
                    <p>{{ __('auth.home') }}</p>
                </a>
                <a href="{{ route('admission.apply') }}" class="service-btn">
                    <i class="fas fa-file-alt"></i>
                    <p>{{ __('auth.online_application') }}</p>
                </a>
            </div>
        </div>
    </form>

    <script>
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(inputId + '-toggle-icon');

            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>
@endsection
