<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ariza holatini tekshirish - Tourism Academy</title>

    <!-- Tailwind CSS -->
    <script src="{{ asset('vendor/tailwind/tailwind.min.js') }}"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.min.css') }}">

    <style>
        :root {
            --primary-green: #16a085;
            --dark-green: #0d4f3c;
            --light-green: #e8f5f0;
            --accent-green: #48c9b0;
        }

        body {
            background: linear-gradient(135deg, var(--dark-green) 0%, var(--primary-green) 100%);
            min-height: 100vh;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
        }

        .input-green {
            border: 2px solid #e5e7eb;
            transition: all 0.3s ease;
        }

        .input-green:focus {
            border-color: var(--primary-green);
            outline: none;
            box-shadow: 0 0 0 3px rgba(22, 160, 133, 0.1);
        }

        .btn-green {
            background: linear-gradient(135deg, var(--dark-green) 0%, var(--primary-green) 100%);
            color: white;
            transition: all 0.3s ease;
        }

        .btn-green:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(22, 160, 133, 0.3);
        }
    </style>
</head>
<body>
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="w-full max-w-md">
            <!-- Logo -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-24 h-24 bg-white rounded-full shadow-2xl mb-4">
                    <i class="fas fa-graduation-cap text-4xl text-green-600"></i>
                </div>
                <h1 class="text-3xl font-bold text-white mb-2">Ariza holati</h1>
                <p class="text-white/80">Tourism Academy Samarkand</p>
                <div class="mt-4">
                    <a href="/local.uz/public/login" class="inline-flex items-center px-4 py-2 bg-white/20 hover:bg-white/30 rounded-lg text-white transition">
                        <i class="fas fa-home mr-2"></i> Bosh sahifa
                    </a>
                </div>
            </div>

            <!-- Main Card -->
            <div class="glass-card p-8">
                @if(session('error'))
                    <div class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded">
                        <div class="flex">
                            <i class="fas fa-exclamation-circle mr-3"></i>
                            <p>{{ session('error') }}</p>
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('admission.check-status') }}">
                    @csrf

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Ariza raqami</label>
                        <div class="relative">
                            <input type="text" name="application_number" required
                                class="w-full px-4 py-3 pl-12 rounded-lg input-green"
                                placeholder="APP-2025-00001"
                                value="{{ old('application_number') }}">
                            <i class="fas fa-hashtag absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        </div>
                        @error('application_number')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Pasport (seriya va raqam)</label>
                        <div class="relative">
                            <input type="text" name="passport" required
                                class="w-full px-4 py-3 pl-12 rounded-lg input-green uppercase"
                                placeholder="AB1234567"
                                maxlength="9"
                                value="{{ old('passport') }}">
                            <i class="fas fa-id-card absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        </div>
                        @error('passport')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <button type="submit" class="w-full btn-green py-3 rounded-lg font-semibold">
                        <i class="fas fa-search mr-2"></i>
                        Tekshirish
                    </button>
                </form>

                <div class="mt-6 pt-6 border-t border-gray-200">
                    <p class="text-center text-sm text-gray-600">
                        Ariza raqamingizni unutdingizmi?
                    </p>
                    <p class="text-center text-sm text-gray-500 mt-2">
                        Qabul komissiyasiga murojaat qiling:
                        <a href="tel:+998662330841" class="text-green-600 font-semibold">+998 66 233-08-41</a>
                    </p>
                </div>
            </div>

            <!-- Links -->
            <div class="mt-8 flex justify-center space-x-6">
                <a href="{{ route('admission.apply') }}" class="text-white/80 hover:text-white transition">
                    <i class="fas fa-file-alt mr-2"></i>
                    Ariza topshirish
                </a>
                <a href="{{ route('admission.info') }}" class="text-white/80 hover:text-white transition">
                    <i class="fas fa-info-circle mr-2"></i>
                    Qabul haqida
                </a>
                <a href="/local.uz/public/login" class="text-white/80 hover:text-white transition">
                    <i class="fas fa-home mr-2"></i>
                    Bosh sahifa
                </a>
            </div>
        </div>
    </div>

    <script>
        // Auto-format passport input
        document.querySelector('input[name="passport"]').addEventListener('input', function(e) {
            this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
        });
    </script>
</body>
</html>