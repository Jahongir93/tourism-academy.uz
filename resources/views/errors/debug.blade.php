<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xatolik yuz berdi</title>
    <script src="{{ asset('vendor/tailwind/tailwind.min.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.min.css') }}">
    <style>
        .code-block {
            background: #1e1e1e;
            color: #d4d4d4;
            padding: 1rem;
            border-radius: 0.5rem;
            overflow-x: auto;
            font-family: 'Courier New', monospace;
            font-size: 0.875rem;
        }
        .error-line {
            background: #ff000020;
            border-left: 3px solid #ff0000;
            padding-left: 0.5rem;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="bg-red-600 text-white rounded-t-lg p-6">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-triangle text-4xl mr-4"></i>
                    <div>
                        <h1 class="text-3xl font-bold">Xatolik yuz berdi!</h1>
                        <p class="text-red-100 mt-1">{{ $errorDetails['exception'] }}</p>
                    </div>
                </div>
            </div>

            <!-- Error Message -->
            <div class="bg-white border-x border-gray-200 p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Xatolik xabari:</h2>
                <div class="bg-red-50 border-l-4 border-red-600 p-4 rounded">
                    <p class="text-red-800 font-medium text-lg">{{ $errorDetails['message'] }}</p>
                </div>
            </div>

            <!-- File and Line -->
            <div class="bg-white border-x border-gray-200 p-6 border-t border-gray-200">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-600 mb-1"><i class="fas fa-file mr-2"></i>Fayl:</p>
                        <p class="font-mono text-sm text-blue-900 break-all">{{ $errorDetails['file'] }}</p>
                    </div>
                    <div class="bg-green-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-600 mb-1"><i class="fas fa-hashtag mr-2"></i>Qator:</p>
                        <p class="font-mono text-2xl font-bold text-green-900">{{ $errorDetails['line'] }}</p>
                    </div>
                    <div class="bg-purple-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-600 mb-1"><i class="fas fa-code mr-2"></i>Kod:</p>
                        <p class="font-mono text-2xl font-bold text-purple-900">{{ $errorDetails['code'] ?: 'N/A' }}</p>
                    </div>
                </div>
            </div>

            <!-- Request Info -->
            <div class="bg-white border-x border-gray-200 p-6 border-t border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 mb-3"><i class="fas fa-info-circle mr-2"></i>So'rov ma'lumotlari:</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600">URL:</p>
                        <p class="font-mono text-sm text-gray-900 break-all">{{ $errorDetails['url'] }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Method:</p>
                        <p class="font-mono text-sm text-gray-900">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $errorDetails['method'] === 'GET' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                {{ $errorDetails['method'] }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Stack Trace -->
            <div class="bg-white border border-gray-200 rounded-b-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-3"><i class="fas fa-layer-group mr-2"></i>Stack Trace:</h3>
                <div class="code-block">
                    @foreach($errorDetails['trace'] as $index => $line)
                        <div class="mb-1 {{ $index === 0 ? 'error-line' : '' }}">{{ $line }}</div>
                    @endforeach
                </div>
            </div>

            <!-- Actions -->
            <div class="mt-6 flex gap-4">
                <button onclick="window.history.back()"
                        class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition">
                    <i class="fas fa-arrow-left mr-2"></i>Orqaga qaytish
                </button>
                <button onclick="window.location.reload()"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                    <i class="fas fa-redo mr-2"></i>Sahifani yangilash
                </button>
                <button onclick="copyError()"
                        class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition">
                    <i class="fas fa-copy mr-2"></i>Xatolikni nusxalash
                </button>
            </div>

            <!-- Debug Info -->
            <div class="mt-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <p class="text-sm text-yellow-800">
                    <i class="fas fa-info-circle mr-2"></i>
                    <strong>Eslatma:</strong> Bu batafsil xatolik sahifasi faqat development rejimida ko'rinadi.
                    Production rejimida foydalanuvchilarga sodda xatolik sahifasi ko'rsatiladi.
                </p>
            </div>
        </div>
    </div>

    <script>
        function copyError() {
            const errorText = `
Xatolik: {{ $errorDetails['message'] }}
Fayl: {{ $errorDetails['file'] }}
Qator: {{ $errorDetails['line'] }}
URL: {{ $errorDetails['url'] }}
Method: {{ $errorDetails['method'] }}

Stack Trace:
@foreach($errorDetails['trace'] as $line)
{{ $line }}
@endforeach
            `.trim();

            navigator.clipboard.writeText(errorText).then(() => {
                alert('Xatolik ma\'lumotlari nusxalandi!');
            });
        }
    </script>
</body>
</html>
