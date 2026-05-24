@extends('layouts.auth')

@section('title', 'Parolni tiklash - HEMIS')
@section('subtitle', 'Parolni tiklash')

@section('content')
    <form action="{{ route('password.email') }}" method="POST">
        @csrf
        
        <div class="text-center mb-6">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-r from-purple-600 to-indigo-600 rounded-full mb-4">
                <i class="fas fa-key text-2xl text-white"></i>
            </div>
            <h2 class="text-2xl font-bold text-gray-800 mb-2">Parolni unutdingizmi?</h2>
            <p class="text-gray-600 text-sm">Xavotir olmang! Email manzilingizni kiriting va biz sizga parolni tiklash havolasini yuboramiz.</p>
        </div>

        <!-- Email Input -->
        <div class="form-input mb-6">
            <input 
                id="email" 
                name="email" 
                type="email" 
                required
                placeholder="Email manzilingiz"
                value="{{ old('email') }}"
                autocomplete="email"
                autofocus>
            <i class="fas fa-envelope"></i>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn-primary mb-4">
            <i class="fas fa-paper-plane mr-2"></i>
            Tiklash havolasini yuborish
        </button>

        <!-- Back to Login -->
        <div class="text-center">
            <a href="{{ route('login') }}" class="link-hover text-sm">
                <i class="fas fa-arrow-left mr-1"></i>
                Kirish sahifasiga qaytish
            </a>
        </div>
    </form>

    <!-- Success Message Animation -->
    @if (session('status'))
        <div class="mt-6 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded-lg" 
             x-data="{ show: true }"
             x-show="show"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform translate-x-10"
             x-transition:enter-end="opacity-100 transform translate-x-0"
             x-init="setTimeout(() => show = false, 10000)">
            <div class="flex">
                <i class="fas fa-check-circle mr-3 mt-0.5"></i>
                <div>
                    <p class="font-semibold">Muvaffaqiyatli!</p>
                    <p class="text-sm">{{ session('status') }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Additional JavaScript for animations -->
    <script>
        // Email input animation
        const emailInput = document.getElementById('email');
        const emailIcon = emailInput.nextElementSibling;
        
        emailInput.addEventListener('focus', function() {
            this.parentElement.classList.add('focused');
            emailIcon.classList.add('text-indigo-600');
        });
        
        emailInput.addEventListener('blur', function() {
            if (!this.value) {
                this.parentElement.classList.remove('focused');
                emailIcon.classList.remove('text-indigo-600');
            }
        });
        
        // Form submit animation
        const form = document.querySelector('form');
        form.addEventListener('submit', function(e) {
            const button = this.querySelector('button[type="submit"]');
            button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Yuborilmoqda...';
            button.disabled = true;
        });
        
        // Auto-focus effect
        window.addEventListener('load', function() {
            emailInput.focus();
        });
    </script>

    <style>
        /* Page specific animations */
        @keyframes float-icon {
            0%, 100% {
                transform: translateY(0) rotate(0deg);
            }
            25% {
                transform: translateY(-5px) rotate(-5deg);
            }
            75% {
                transform: translateY(5px) rotate(5deg);
            }
        }
        
        .form-input.focused i {
            animation: float-icon 2s ease-in-out infinite;
        }
        
        /* Gradient text */
        .gradient-text {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
    </style>
@endsection