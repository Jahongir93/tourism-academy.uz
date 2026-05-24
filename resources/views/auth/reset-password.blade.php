@extends('layouts.auth')

@section('title', 'Yangi parol - HEMIS')
@section('subtitle', 'Yangi parol o\'rnatish')

@section('content')
    <form action="{{ route('password.update') }}" method="POST">
        @csrf
        
        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $token ?? '' }}">
        
        <div class="text-center mb-6">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-r from-purple-600 to-indigo-600 rounded-full mb-4">
                <i class="fas fa-lock-open text-2xl text-white"></i>
            </div>
            <h2 class="text-2xl font-bold text-gray-800 mb-2">Yangi parol o'rnatish</h2>
            <p class="text-gray-600 text-sm">Kuchli parol tanlang va uni xavfsiz joyda saqlang</p>
        </div>

        <!-- Email Input -->
        <div class="form-input mb-4">
            <input 
                id="email" 
                name="email" 
                type="email" 
                required
                placeholder="Email manzilingiz"
                value="{{ old('email', $email ?? request('email')) }}"
                autocomplete="email"
                readonly>
            <i class="fas fa-envelope"></i>
        </div>

        <!-- New Password Input -->
        <div class="form-input mb-4">
            <input 
                id="password" 
                name="password" 
                type="password" 
                required
                placeholder="Yangi parol"
                autocomplete="new-password"
                minlength="8">
            <i class="fas fa-lock"></i>
        </div>

        <!-- Password Strength Indicator -->
        <div class="mb-4">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs text-gray-600">Parol kuchi:</span>
                <span id="strength-text" class="text-xs font-semibold">Kuchsiz</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div id="strength-bar" class="h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
            </div>
        </div>

        <!-- Confirm Password Input -->
        <div class="form-input mb-6">
            <input 
                id="password_confirmation" 
                name="password_confirmation" 
                type="password" 
                required
                placeholder="Parolni tasdiqlang"
                autocomplete="new-password"
                minlength="8">
            <i class="fas fa-lock-check"></i>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn-primary mb-4">
            <i class="fas fa-save mr-2"></i>
            Parolni yangilash
        </button>

        <!-- Back to Login -->
        <div class="text-center">
            <a href="{{ route('login') }}" class="link-hover text-sm">
                <i class="fas fa-arrow-left mr-1"></i>
                Kirish sahifasiga qaytish
            </a>
        </div>
    </form>

    <!-- Additional JavaScript -->
    <script>
        // Password visibility toggle
        document.querySelectorAll('.form-input i.fa-lock, .form-input i.fa-lock-check').forEach(icon => {
            icon.style.cursor = 'pointer';
            icon.addEventListener('click', function() {
                const input = this.previousElementSibling;
                if (input.type === 'password') {
                    input.type = 'text';
                    this.classList.remove('fa-lock', 'fa-lock-check');
                    this.classList.add('fa-eye');
                } else {
                    input.type = 'password';
                    this.classList.remove('fa-eye');
                    this.classList.add(this.parentElement.querySelector('#password_confirmation') ? 'fa-lock-check' : 'fa-lock');
                }
            });
        });

        // Password strength checker
        const passwordInput = document.getElementById('password');
        const strengthBar = document.getElementById('strength-bar');
        const strengthText = document.getElementById('strength-text');
        
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            let strength = 0;
            
            // Check password strength
            if (password.length >= 8) strength++;
            if (password.length >= 12) strength++;
            if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            if (/[^a-zA-Z0-9]/.test(password)) strength++;
            
            // Update UI
            const percentage = (strength / 5) * 100;
            strengthBar.style.width = percentage + '%';
            
            if (strength <= 1) {
                strengthBar.className = 'h-2 rounded-full transition-all duration-300 bg-red-500';
                strengthText.textContent = 'Juda kuchsiz';
                strengthText.className = 'text-xs font-semibold text-red-500';
            } else if (strength === 2) {
                strengthBar.className = 'h-2 rounded-full transition-all duration-300 bg-orange-500';
                strengthText.textContent = 'Kuchsiz';
                strengthText.className = 'text-xs font-semibold text-orange-500';
            } else if (strength === 3) {
                strengthBar.className = 'h-2 rounded-full transition-all duration-300 bg-yellow-500';
                strengthText.textContent = 'O\'rtacha';
                strengthText.className = 'text-xs font-semibold text-yellow-500';
            } else if (strength === 4) {
                strengthBar.className = 'h-2 rounded-full transition-all duration-300 bg-blue-500';
                strengthText.textContent = 'Kuchli';
                strengthText.className = 'text-xs font-semibold text-blue-500';
            } else {
                strengthBar.className = 'h-2 rounded-full transition-all duration-300 bg-green-500';
                strengthText.textContent = 'Juda kuchli';
                strengthText.className = 'text-xs font-semibold text-green-500';
            }
        });

        // Password match checker
        const confirmInput = document.getElementById('password_confirmation');
        confirmInput.addEventListener('input', function() {
            if (this.value !== passwordInput.value) {
                this.setCustomValidity('Parollar mos kelmayapti');
                this.parentElement.classList.add('border-red-500');
            } else {
                this.setCustomValidity('');
                this.parentElement.classList.remove('border-red-500');
                this.parentElement.classList.add('border-green-500');
            }
        });

        // Form submit animation
        const form = document.querySelector('form');
        form.addEventListener('submit', function(e) {
            const button = this.querySelector('button[type="submit"]');
            button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Yangilanmoqda...';
            button.disabled = true;
        });
    </script>

    <style>
        /* Password match indicator */
        .form-input.border-red-500 input {
            border-color: #ef4444;
        }
        
        .form-input.border-green-500 input {
            border-color: #10b981;
        }
        
        /* Lock icon animation */
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
            20%, 40%, 60%, 80% { transform: translateX(5px); }
        }
        
        .form-input.border-red-500 i {
            animation: shake 0.5s;
            color: #ef4444;
        }
        
        .form-input.border-green-500 i {
            color: #10b981;
        }
    </style>
@endsection