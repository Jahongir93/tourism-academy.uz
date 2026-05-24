<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Server xatoligi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="max-w-2xl w-full">
            <!-- Error Card -->
            <div class="bg-white rounded-lg shadow-xl overflow-hidden">
                <!-- Header -->
                <div class="bg-gradient-to-r from-red-500 to-red-600 p-8 text-white text-center">
                    <div class="inline-flex items-center justify-center w-20 h-20 bg-white bg-opacity-20 rounded-full mb-4">
                        <i class="fas fa-exclamation-triangle text-4xl"></i>
                    </div>
                    <h1 class="text-4xl font-bold mb-2">500</h1>
                    <p class="text-xl">Server xatoligi</p>
                </div>

                <!-- Content -->
                <div class="p-8">
                    <p class="text-gray-700 text-lg mb-6 text-center">
                        Kechirasiz, serverda ichki xatolik yuz berdi. Iltimos, keyinroq qayta urinib ko'ring.
                    </p>

                    @if(isset($message) && config('app.debug'))
                    <!-- Error Details (only in debug mode) -->
                    <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-bug text-red-400"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">Xatolik tafsilotlari:</h3>
                                <div class="mt-2 text-sm text-red-700">
                                    <p class="font-mono">{{ $message }}</p>
                                    @if(isset($file))
                                        <p class="mt-2 text-xs">
                                            <strong>Fayl:</strong> {{ $file }}<br>
                                            <strong>Qator:</strong> {{ $line ?? 'N/A' }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Quick Help -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                        <h3 class="font-semibold text-blue-900 mb-2">
                            <i class="fas fa-lightbulb mr-2"></i>Nima qilish kerak?
                        </h3>
                        <ul class="text-sm text-blue-800 space-y-1">
                            <li><i class="fas fa-check mr-2"></i>Sahifani yangilab ko'ring</li>
                            <li><i class="fas fa-check mr-2"></i>Brauzer keshini tozalang</li>
                            <li><i class="fas fa-check mr-2"></i>Biroz kutib, qayta urinib ko'ring</li>
                            <li><i class="fas fa-check mr-2"></i>Muammo davom etsa, administratorga xabar bering</li>
                        </ul>
                    </div>

                    <!-- Actions -->
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <button onclick="window.location.reload()"
                                class="inline-flex items-center justify-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                            <i class="fas fa-redo mr-2"></i>Sahifani yangilash
                        </button>
                        <a href="{{ route('dashboard') }}"
                           class="inline-flex items-center justify-center px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition">
                            <i class="fas fa-home mr-2"></i>Bosh sahifaga qaytish
                        </a>
                    </div>

                    <!-- Support Info -->
                    <div class="mt-8 text-center text-sm text-gray-500">
                        <p>Yordam kerakmi? <a href="mailto:support@ziyopedia.uz" class="text-blue-600 hover:underline">support@ziyopedia.uz</a></p>
                    </div>
                </div>
            </div>

            <!-- Error ID (for support) -->
            <div class="mt-4 text-center text-sm text-gray-500">
                Error ID: {{ uniqid('ERR-') }} | {{ now()->format('Y-m-d H:i:s') }}
            </div>
        </div>
    </div>

    <script>
        // Auto-report error to backend (optional)
        if (window.fetch) {
            fetch('/api/log-frontend-error', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                body: JSON.stringify({
                    url: window.location.href,
                    userAgent: navigator.userAgent,
                    timestamp: new Date().toISOString(),
                    @if(isset($message))
                    message: '{{ addslashes($message) }}',
                    @endif
                })
            }).catch(() => {});
        }
    </script>
</body>
</html>
