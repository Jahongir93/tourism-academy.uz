@extends('layouts.auth')

@section('title', 'Telefon raqamni tasdiqlash')
@section('subtitle', 'SMS orqali yuborilgan kodni kiriting')

@section('content')
<div x-data="otpVerification()">
    <!-- Phone Display -->
    <div class="phone-display">
        <div class="phone-icon">
            <i class="fas fa-mobile-alt"></i>
        </div>
        <div class="phone-info">
            <span class="phone-label">SMS yuborildi</span>
            <span class="phone-number">{{ substr(Auth::user()->phone, 0, 7) }}****{{ substr(Auth::user()->phone, -2) }}</span>
        </div>
    </div>

    <form action="{{ route('otp.verify') }}" method="POST" @submit="handleSubmit">
        @csrf

        <!-- OTP Input Fields -->
        <div class="otp-container">
            <div class="otp-inputs">
                @for($i = 0; $i < 6; $i++)
                <input type="text"
                       maxlength="1"
                       class="otp-input"
                       x-ref="otp{{ $i }}"
                       @input="handleInput($event, {{ $i }})"
                       @keydown="handleKeydown($event, {{ $i }})"
                       @paste="handlePaste($event)"
                       @focus="$event.target.select()"
                       inputmode="numeric"
                       pattern="[0-9]"
                       autocomplete="one-time-code">
                @endfor
            </div>
            <input type="hidden" name="otp" x-model="otpValue">
        </div>

        <!-- Timer -->
        <div class="timer-section">
            <div class="timer" x-show="timeLeft > 0">
                <i class="far fa-clock"></i>
                <span>Kod <span x-text="formattedTime"></span> ichida amal qiladi</span>
            </div>
            <div class="timer expired" x-show="timeLeft <= 0">
                <i class="fas fa-exclamation-circle"></i>
                <span>Kod muddati tugadi</span>
            </div>
        </div>

        <!-- Error Message -->
        @if($errors->any())
        <div class="error-message">
            <i class="fas fa-exclamation-triangle"></i>
            @foreach($errors->all() as $error)
                <span>{{ $error }}</span>
            @endforeach
        </div>
        @endif

        <!-- Submit Button -->
        <button type="submit" class="btn-primary" :disabled="!isComplete || isSubmitting">
            <template x-if="!isSubmitting">
                <span><i class="fas fa-check-circle"></i> Tasdiqlash</span>
            </template>
            <template x-if="isSubmitting">
                <span><i class="fas fa-spinner fa-spin"></i> Tekshirilmoqda...</span>
            </template>
        </button>
    </form>

    <!-- Resend Section -->
    <div class="resend-section">
        <p class="resend-text">Kod kelmadimi?</p>

        <form action="{{ route('otp.resend') }}" method="POST" x-show="canResend">
            @csrf
            <button type="submit" class="resend-btn">
                <i class="fas fa-redo"></i> Qayta yuborish
            </button>
        </form>

        <div class="resend-timer" x-show="!canResend">
            <span x-text="resendTimeFormatted"></span> dan keyin qayta yuborish mumkin
        </div>
    </div>

    <!-- Help Section -->
    <div class="help-section">
        <div class="help-item">
            <i class="fas fa-info-circle"></i>
            <span>Kod 5 daqiqa ichida amal qiladi</span>
        </div>
        <div class="help-item">
            <i class="fas fa-phone-alt"></i>
            <span>Muammo bo'lsa: <a href="tel:+998712345678">+998 71 234 56 78</a></span>
        </div>
    </div>

    <!-- Back Link -->
    <div class="back-link">
        <a href="{{ route('login') }}">
            <i class="fas fa-arrow-left"></i> Orqaga qaytish
        </a>
    </div>
</div>

<style>
    .phone-display {
        display: flex;
        align-items: center;
        gap: 1rem;
        background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%);
        border: 2px solid #c7d2fe;
        border-radius: 16px;
        padding: 1rem 1.25rem;
        margin-bottom: 1.5rem;
    }

    .phone-icon {
        width: 48px;
        height: 48px;
        background: linear-gradient(135deg, #4338CA, #3730A3);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.25rem;
    }

    .phone-info {
        display: flex;
        flex-direction: column;
    }

    .phone-label {
        font-size: 0.75rem;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .phone-number {
        font-size: 1.1rem;
        font-weight: 700;
        color: #1f2937;
        letter-spacing: 1px;
    }

    /* OTP Container */
    .otp-container {
        margin-bottom: 1.25rem;
    }

    .otp-inputs {
        display: flex;
        gap: 0.5rem;
        justify-content: center;
    }

    .otp-input {
        width: 48px;
        height: 56px;
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        text-align: center;
        font-size: 1.5rem;
        font-weight: 700;
        color: #1f2937;
        transition: all 0.2s ease;
        background: white;
    }

    .otp-input:focus {
        outline: none;
        border-color: #4338CA;
        box-shadow: 0 0 0 4px rgba(67, 56, 202, 0.1);
    }

    .otp-input.filled {
        border-color: #10b981;
        background: #f0fdf4;
    }

    .otp-input.error {
        border-color: #ef4444;
        background: #fef2f2;
        animation: shake 0.5s ease;
    }

    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        20%, 60% { transform: translateX(-5px); }
        40%, 80% { transform: translateX(5px); }
    }

    /* Timer */
    .timer-section {
        text-align: center;
        margin-bottom: 1rem;
    }

    .timer {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.875rem;
        color: #059669;
        background: #f0fdf4;
        padding: 0.5rem 1rem;
        border-radius: 50px;
        border: 1px solid #bbf7d0;
    }

    .timer.expired {
        color: #dc2626;
        background: #fef2f2;
        border-color: #fecaca;
    }

    /* Error Message */
    .error-message {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        background: #fef2f2;
        border: 1px solid #fecaca;
        color: #dc2626;
        padding: 0.75rem 1rem;
        border-radius: 10px;
        margin-bottom: 1rem;
        font-size: 0.875rem;
    }

    /* Resend Section */
    .resend-section {
        text-align: center;
        margin-top: 1.5rem;
        padding-top: 1.5rem;
        border-top: 1px solid #e5e7eb;
    }

    .resend-text {
        color: #6b7280;
        font-size: 0.875rem;
        margin-bottom: 0.75rem;
    }

    .resend-btn {
        background: transparent;
        border: 2px solid #4338CA;
        color: #4338CA;
        padding: 0.625rem 1.5rem;
        border-radius: 50px;
        font-weight: 600;
        font-size: 0.875rem;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .resend-btn:hover {
        background: #4338CA;
        color: white;
    }

    .resend-timer {
        color: #9ca3af;
        font-size: 0.8rem;
    }

    /* Help Section */
    .help-section {
        margin-top: 1.5rem;
        background: #f9fafb;
        border-radius: 12px;
        padding: 1rem;
    }

    .help-item {
        display: flex;
        align-items: center;
        gap: 0.625rem;
        font-size: 0.8rem;
        color: #6b7280;
        padding: 0.375rem 0;
    }

    .help-item i {
        color: #9ca3af;
        width: 16px;
    }

    .help-item a {
        color: #4338CA;
        text-decoration: none;
        font-weight: 600;
    }

    /* Back Link */
    .back-link {
        text-align: center;
        margin-top: 1.25rem;
    }

    .back-link a {
        color: #6b7280;
        text-decoration: none;
        font-size: 0.875rem;
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        transition: color 0.2s;
    }

    .back-link a:hover {
        color: #4338CA;
    }

    /* Button States */
    .btn-primary:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none !important;
    }

    @media (max-width: 400px) {
        .otp-input {
            width: 42px;
            height: 50px;
            font-size: 1.25rem;
        }

        .otp-inputs {
            gap: 0.375rem;
        }
    }
</style>

<script>
function otpVerification() {
    return {
        otpValue: '',
        isSubmitting: false,
        timeLeft: 300, // 5 minutes in seconds
        resendTime: 60, // 1 minute cooldown
        canResend: false,

        init() {
            // Start countdown timer
            this.startTimer();
            this.startResendTimer();

            // Focus first input
            this.$nextTick(() => {
                this.$refs.otp0.focus();
            });
        },

        startTimer() {
            const interval = setInterval(() => {
                if (this.timeLeft > 0) {
                    this.timeLeft--;
                } else {
                    clearInterval(interval);
                }
            }, 1000);
        },

        startResendTimer() {
            const interval = setInterval(() => {
                if (this.resendTime > 0) {
                    this.resendTime--;
                } else {
                    this.canResend = true;
                    clearInterval(interval);
                }
            }, 1000);
        },

        get formattedTime() {
            const minutes = Math.floor(this.timeLeft / 60);
            const seconds = this.timeLeft % 60;
            return `${minutes}:${seconds.toString().padStart(2, '0')}`;
        },

        get resendTimeFormatted() {
            return `${this.resendTime} soniya`;
        },

        get isComplete() {
            return this.otpValue.length === 6;
        },

        handleInput(event, index) {
            const input = event.target;
            const value = input.value;

            // Only allow numbers
            if (!/^\d*$/.test(value)) {
                input.value = '';
                return;
            }

            // Update OTP value
            this.updateOtpValue();

            // Add filled class
            if (value) {
                input.classList.add('filled');
                // Move to next input
                if (index < 5) {
                    this.$refs[`otp${index + 1}`].focus();
                }
            } else {
                input.classList.remove('filled');
            }
        },

        handleKeydown(event, index) {
            const input = event.target;

            // Handle backspace
            if (event.key === 'Backspace') {
                if (!input.value && index > 0) {
                    this.$refs[`otp${index - 1}`].focus();
                }
                input.classList.remove('filled');
            }

            // Handle arrow keys
            if (event.key === 'ArrowLeft' && index > 0) {
                this.$refs[`otp${index - 1}`].focus();
            }
            if (event.key === 'ArrowRight' && index < 5) {
                this.$refs[`otp${index + 1}`].focus();
            }
        },

        handlePaste(event) {
            event.preventDefault();
            const pastedData = event.clipboardData.getData('text').replace(/\D/g, '').substring(0, 6);

            if (pastedData.length === 6) {
                for (let i = 0; i < 6; i++) {
                    this.$refs[`otp${i}`].value = pastedData[i];
                    this.$refs[`otp${i}`].classList.add('filled');
                }
                this.updateOtpValue();
                this.$refs.otp5.focus();
            }
        },

        updateOtpValue() {
            let otp = '';
            for (let i = 0; i < 6; i++) {
                otp += this.$refs[`otp${i}`].value || '';
            }
            this.otpValue = otp;
        },

        handleSubmit(event) {
            if (!this.isComplete) {
                event.preventDefault();
                // Shake animation for incomplete
                document.querySelectorAll('.otp-input').forEach(input => {
                    if (!input.value) {
                        input.classList.add('error');
                        setTimeout(() => input.classList.remove('error'), 500);
                    }
                });
                return false;
            }

            this.isSubmitting = true;
        }
    }
}
</script>
@endsection
