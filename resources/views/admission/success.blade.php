<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ariza qabul qilindi - Tourism Academy</title>

    <!-- Google Fonts -->
    <link href="{{ asset('vendor/fonts/inter.css') }}" rel="stylesheet">

    <!-- Tailwind CSS -->
    <script src="{{ asset('vendor/tailwind/tailwind.min.js') }}"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.min.css') }}">

    <style>
        * {
            font-family: 'Inter', sans-serif;
        }

        :root {
            --primary: #0066CC;
            --primary-dark: #0052A3;
            --success-green: #16a085;
            --dark-green: #0d4f3c;
            --light-green: #e8f5f0;
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
        }

        .success-animation {
            animation: scaleIn 0.5s ease-out;
        }

        @keyframes scaleIn {
            from {
                transform: scale(0);
                opacity: 0;
            }
            to {
                transform: scale(1);
                opacity: 1;
            }
        }

        .pulse-animation {
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
        }

        .copy-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 102, 204, 0.3);
        }

        .copy-btn:active {
            transform: translateY(0);
        }

        .confetti {
            position: fixed;
            width: 10px;
            height: 10px;
            background: var(--primary);
            animation: fall 3s linear infinite;
            z-index: 1000;
        }

        @keyframes fall {
            to {
                transform: translateY(100vh) rotate(360deg);
                opacity: 0;
            }
        }
    </style>
</head>
<body>
    <div class="min-h-screen flex items-center justify-center p-4">
        <!-- Confetti -->
        <div class="confetti" style="left: 10%; animation-delay: 0s; background: #16a085;"></div>
        <div class="confetti" style="left: 20%; animation-delay: 0.5s; background: #48c9b0;"></div>
        <div class="confetti" style="left: 30%; animation-delay: 1s; background: #0d4f3c;"></div>
        <div class="confetti" style="left: 40%; animation-delay: 1.5s; background: #16a085;"></div>
        <div class="confetti" style="left: 50%; animation-delay: 0.3s; background: #48c9b0;"></div>
        <div class="confetti" style="left: 60%; animation-delay: 0.8s; background: #0d4f3c;"></div>
        <div class="confetti" style="left: 70%; animation-delay: 1.2s; background: #16a085;"></div>
        <div class="confetti" style="left: 80%; animation-delay: 0.2s; background: #48c9b0;"></div>
        <div class="confetti" style="left: 90%; animation-delay: 0.7s; background: #0d4f3c;"></div>

        <div class="w-full max-w-2xl">
            <div class="bg-white rounded-lg shadow-xl p-8 text-center success-animation">
                <!-- Success Icon -->
                <div class="mb-6">
                    <div class="inline-flex items-center justify-center w-32 h-32 bg-green-100 rounded-full">
                        <i class="fas fa-check-circle text-6xl text-green-600"></i>
                    </div>
                </div>

                <!-- Success Message -->
                <h1 class="text-3xl font-bold text-gray-800 mb-4">Tabriklaymiz!</h1>
                <p class="text-xl text-gray-600 mb-8">Arizangiz muvaffaqiyatli qabul qilindi</p>

                <!-- Application Details -->
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-8 mb-8 border-2 border-blue-200">
                    <div class="mb-6">
                        <p class="text-sm text-gray-600 mb-2 font-medium">Ariza raqami</p>
                        <div class="flex items-center justify-center gap-3 flex-wrap">
                            <p id="applicationNumber" class="text-3xl md:text-4xl font-bold text-blue-600 tracking-wider">{{ $application->application_number }}</p>
                            <button onclick="copyApplicationNumber()" class="copy-btn inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg font-semibold transition-all duration-200">
                                <i class="fas fa-copy"></i>
                                <span id="copyText">Nusxa olish</span>
                            </button>
                        </div>
                    </div>

                    <div class="grid md:grid-cols-2 gap-4 text-left mt-6 pt-6 border-t border-blue-200">
                        <div class="bg-white rounded-lg p-4">
                            <p class="text-sm text-gray-600 mb-1">F.I.O:</p>
                            <p class="font-semibold text-gray-800">{{ $application->first_name }} {{ $application->last_name }}</p>
                        </div>
                        <div class="bg-white rounded-lg p-4">
                            <p class="text-sm text-gray-600 mb-1">Email:</p>
                            <p class="font-semibold text-gray-800">{{ $application->email }}</p>
                        </div>
                        @if($application->form_version == 2)
                        <div class="bg-white rounded-lg p-4 md:col-span-2">
                            <p class="text-sm text-gray-600 mb-2">Tanlangan modullar:</p>
                            <div class="flex flex-wrap gap-2">
                                @if(isset($application->form_data['selectedModules']))
                                    @php
                                        $modules = is_string($application->form_data['selectedModules'])
                                            ? json_decode($application->form_data['selectedModules'], true)
                                            : $application->form_data['selectedModules'];
                                    @endphp
                                    @foreach($modules as $module)
                                        <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm font-medium">{{ $module }}</span>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                        @else
                        <div class="bg-white rounded-lg p-4">
                            <p class="text-sm text-gray-600 mb-1">Fakultet:</p>
                            <p class="font-semibold text-gray-800">{{ $application->faculty->name_uz ?? 'N/A' }}</p>
                        </div>
                        <div class="bg-white rounded-lg p-4">
                            <p class="text-sm text-gray-600 mb-1">Yo'nalish:</p>
                            <p class="font-semibold text-gray-800">{{ $application->specialty->name_uz ?? 'N/A' }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Important Notice -->
                <div class="bg-gradient-to-r from-yellow-50 to-orange-50 border-2 border-yellow-300 rounded-xl p-6 mb-8 shadow-lg">
                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-yellow-400 rounded-full flex items-center justify-center pulse-animation">
                                <i class="fas fa-exclamation-triangle text-white text-xl"></i>
                            </div>
                        </div>
                        <div class="flex-1 text-left">
                            <h3 class="font-bold text-gray-800 mb-2 text-lg">⚠️ Muhim eslatma!</h3>
                            <ul class="space-y-2 text-sm text-gray-700">
                                <li class="flex items-start">
                                    <i class="fas fa-check-circle text-yellow-600 mt-1 mr-2"></i>
                                    <span><strong>Ariza raqamingizni saqlang!</strong> Bu raqam orqali ariza holatini kuzatishingiz mumkin.</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-check-circle text-yellow-600 mt-1 mr-2"></i>
                                    <span>Ariza raqamini yozib qo'ying yoki screenshot oling.</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-check-circle text-yellow-600 mt-1 mr-2"></i>
                                    <span>Emailingizni tekshiring - tasdiqlash xabari yuboriladi.</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Next Steps -->
                <div class="text-left mb-8">
                    <h3 class="font-semibold text-gray-800 mb-3">Keyingi qadamlar:</h3>
                    <ul class="space-y-2">
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                            <span class="text-gray-700">Arizangiz 3-5 ish kuni ichida ko'rib chiqiladi</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                            <span class="text-gray-700">Natija haqida SMS yoki email orqali xabardor qilinasiz</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                            <span class="text-gray-700">Ariza holatini veb-sayt orqali kuzatishingiz mumkin</span>
                        </li>
                    </ul>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col md:flex-row gap-4 justify-center">
                    <button onclick="window.print()" class="px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">
                        <i class="fas fa-print mr-2"></i>
                        Chop etish
                    </button>
                    <a href="{{ route('admission.check-status') }}" class="px-6 py-3 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition">
                        <i class="fas fa-search mr-2"></i>
                        Ariza holati
                    </a>
                    <a href="{{ route('home') }}" class="px-6 py-3 bg-gradient-to-r from-green-700 to-green-600 text-white rounded-lg hover:from-green-800 hover:to-green-700 transition">
                        <i class="fas fa-home mr-2"></i>
                        Bosh sahifa
                    </a>
                </div>
            </div>

            <!-- Contact Info -->
            <div class="mt-8 text-center text-gray-600">
                <p class="mb-2">Savollar bo'lsa, biz bilan bog'laning:</p>
                <p>
                    <i class="fas fa-phone mr-2"></i>
                    <a href="tel:+998662330841" class="font-semibold hover:text-green-600">+998 66 233-08-41</a>
                </p>
                <p>
                    <i class="fas fa-envelope mr-2"></i>
                    <a href="mailto:admission@tourism.uz" class="font-semibold hover:text-green-600">admission@tourism.uz</a>
                </p>
            </div>
        </div>
    </div>

    <script>
        // Save application number to localStorage
        localStorage.setItem('lastApplicationNumber', '{{ $application->application_number }}');

        // Copy application number to clipboard
        function copyApplicationNumber() {
            const appNumber = document.getElementById('applicationNumber').textContent;
            const copyText = document.getElementById('copyText');

            navigator.clipboard.writeText(appNumber).then(function() {
                // Change button text temporarily
                copyText.textContent = '✓ Nusxa olindi!';
                copyText.parentElement.classList.add('bg-green-600');
                copyText.parentElement.classList.remove('bg-blue-600');

                // Reset after 2 seconds
                setTimeout(function() {
                    copyText.textContent = 'Nusxa olish';
                    copyText.parentElement.classList.remove('bg-green-600');
                    copyText.parentElement.classList.add('bg-blue-600');
                }, 2000);
            }).catch(function(err) {
                alert('Nusxa olishda xatolik: ' + err);
            });
        }

        // Create more confetti
        function createConfetti() {
            const colors = ['#0066CC', '#48c9b0', '#0052A3', '#16a085'];
            for (let i = 0; i < 30; i++) {
                const confetti = document.createElement('div');
                confetti.className = 'confetti';
                confetti.style.left = Math.random() * 100 + '%';
                confetti.style.animationDelay = Math.random() * 3 + 's';
                confetti.style.background = colors[Math.floor(Math.random() * colors.length)];
                document.body.appendChild(confetti);

                // Remove after animation
                setTimeout(() => confetti.remove(), 3000);
            }
        }

        // Trigger confetti on load
        window.addEventListener('load', createConfetti);
    </script>
</body>
</html>