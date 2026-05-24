<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ariza holati - Tourism Academy</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary-green: #16a085;
            --dark-green: #0d4f3c;
            --light-green: #e8f5f0;
        }

        body {
            background: linear-gradient(135deg, #f0f9f6 0%, #e8f5f0 100%);
            min-height: 100vh;
        }

        .status-timeline {
            position: relative;
            padding-left: 40px;
        }

        .status-timeline::before {
            content: '';
            position: absolute;
            left: 15px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #e5e7eb;
        }

        .status-item {
            position: relative;
            padding-bottom: 30px;
        }

        .status-item::before {
            content: '';
            position: absolute;
            left: -25px;
            top: 5px;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #e5e7eb;
            border: 2px solid white;
            box-shadow: 0 0 0 3px #f3f4f6;
        }

        .status-item.completed::before {
            background: var(--primary-green);
        }

        .status-item.current::before {
            background: #f59e0b;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% {
                box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.3);
            }
            50% {
                box-shadow: 0 0 0 6px rgba(245, 158, 11, 0.1);
            }
        }
    </style>
</head>
<body>
    <div class="min-h-screen py-8">
        <div class="container mx-auto px-4 max-w-4xl">
            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-800 mb-2">Ariza holati</h1>
                <p class="text-gray-600">Tourism Academy Samarkand</p>
            </div>

            <!-- Application Status Card -->
            <div class="bg-white rounded-lg shadow-lg p-8 mb-8">
                <!-- Status Badge -->
                <div class="text-center mb-8">
                    @php
                        $statusColors = [
                            'pending' => 'bg-yellow-100 text-yellow-800',
                            'reviewing' => 'bg-blue-100 text-blue-800',
                            'accepted' => 'bg-green-100 text-green-800',
                            'rejected' => 'bg-red-100 text-red-800',
                            'waitlist' => 'bg-gray-100 text-gray-800'
                        ];
                        $statusIcons = [
                            'pending' => 'fa-clock',
                            'reviewing' => 'fa-search',
                            'accepted' => 'fa-check-circle',
                            'rejected' => 'fa-times-circle',
                            'waitlist' => 'fa-list'
                        ];
                    @endphp
                    <span class="inline-flex items-center px-6 py-3 rounded-full text-lg font-semibold {{ $statusColors[$application->status] ?? 'bg-gray-100 text-gray-800' }}">
                        <i class="fas {{ $statusIcons[$application->status] ?? 'fa-question' }} mr-2"></i>
                        {{ $application->status_text }}
                    </span>
                </div>

                <!-- Application Info -->
                <div class="grid md:grid-cols-2 gap-6 mb-8">
                    <div class="space-y-4">
                        <div>
                            <p class="text-sm text-gray-600">Ariza raqami</p>
                            <p class="font-semibold text-gray-800">{{ $application->application_number }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">F.I.O</p>
                            <p class="font-semibold text-gray-800">{{ $application->full_name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Telefon</p>
                            <p class="font-semibold text-gray-800">{{ $application->phone }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Email</p>
                            <p class="font-semibold text-gray-800">{{ $application->email }}</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <p class="text-sm text-gray-600">Fakultet</p>
                            <p class="font-semibold text-gray-800">{{ $application->faculty->name_uz ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Yo'nalish</p>
                            <p class="font-semibold text-gray-800">{{ $application->specialty->name_uz ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Ta'lim shakli</p>
                            <p class="font-semibold text-gray-800">{{ $application->education_form_text }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Topshirilgan sana</p>
                            <p class="font-semibold text-gray-800">{{ $application->applied_at->format('d.m.Y H:i') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Status Timeline -->
                <div class="border-t pt-8">
                    <h3 class="font-semibold text-gray-800 mb-6">Ariza jarayoni</h3>
                    <div class="status-timeline">
                        <div class="status-item {{ in_array($application->status, ['pending', 'reviewing', 'accepted', 'rejected', 'waitlist']) ? 'completed' : '' }}">
                            <div class="font-semibold text-gray-800">Ariza qabul qilindi</div>
                            <div class="text-sm text-gray-600">{{ $application->applied_at->format('d.m.Y H:i') }}</div>
                        </div>

                        <div class="status-item {{ in_array($application->status, ['reviewing', 'accepted', 'rejected', 'waitlist']) ? 'completed' : '' }} {{ $application->status == 'pending' ? 'current' : '' }}">
                            <div class="font-semibold text-gray-800">Ko'rib chiqilmoqda</div>
                            <div class="text-sm text-gray-600">
                                @if($application->status != 'pending')
                                    Ko'rib chiqildi
                                @else
                                    Kutilmoqda...
                                @endif
                            </div>
                        </div>

                        <div class="status-item {{ in_array($application->status, ['accepted', 'rejected', 'waitlist']) ? 'completed' : '' }} {{ $application->status == 'reviewing' ? 'current' : '' }}">
                            <div class="font-semibold text-gray-800">Qaror</div>
                            <div class="text-sm text-gray-600">
                                @if(in_array($application->status, ['accepted', 'rejected', 'waitlist']))
                                    {{ $application->reviewed_at ? $application->reviewed_at->format('d.m.Y H:i') : 'N/A' }}
                                @else
                                    Kutilmoqda...
                                @endif
                            </div>
                        </div>

                        @if($application->status == 'accepted')
                        <div class="status-item completed current">
                            <div class="font-semibold text-green-600">Tabriklaymiz! Siz qabul qilindingiz</div>
                            <div class="text-sm text-gray-600">Keyingi qadamlar haqida tez orada xabardor qilamiz</div>
                        </div>
                        @elseif($application->status == 'rejected')
                        <div class="status-item completed">
                            <div class="font-semibold text-red-600">Afsuski, arizangiz rad etildi</div>
                            @if($application->notes)
                            <div class="text-sm text-gray-600 mt-2">Sabab: {{ $application->notes }}</div>
                            @endif
                        </div>
                        @elseif($application->status == 'waitlist')
                        <div class="status-item completed current">
                            <div class="font-semibold text-yellow-600">Kutish ro'yxatida</div>
                            <div class="text-sm text-gray-600">Joy bo'shaganda sizga xabar beramiz</div>
                        </div>
                        @endif
                    </div>
                </div>

                @if($application->notes && $application->status != 'rejected')
                <div class="border-t pt-6 mt-6">
                    <h3 class="font-semibold text-gray-800 mb-2">Izohlar</h3>
                    <p class="text-gray-700">{{ $application->notes }}</p>
                </div>
                @endif
            </div>

            <!-- Actions -->
            <div class="flex flex-col md:flex-row gap-4 justify-center">
                <button onclick="window.print()" class="px-6 py-3 bg-white text-gray-700 rounded-lg hover:bg-gray-50 transition border">
                    <i class="fas fa-print mr-2"></i>
                    Chop etish
                </button>
                <a href="{{ route('admission.check-status') }}" class="px-6 py-3 bg-white text-gray-700 rounded-lg hover:bg-gray-50 transition border text-center">
                    <i class="fas fa-search mr-2"></i>
                    Boshqa ariza
                </a>
                <a href="/local.uz/public/login" class="px-6 py-3 bg-gradient-to-r from-green-700 to-green-600 text-white rounded-lg hover:from-green-800 hover:to-green-700 transition text-center">
                    <i class="fas fa-home mr-2"></i>
                    Bosh sahifa
                </a>
            </div>

            <!-- Contact -->
            <div class="mt-8 text-center text-gray-600">
                <p class="mb-2">Savollar bo'lsa:</p>
                <p>
                    <i class="fas fa-phone mr-2"></i>
                    <a href="tel:+998662330841" class="font-semibold hover:text-green-600">+998 66 233-08-41</a>
                    <span class="mx-2">|</span>
                    <i class="fas fa-envelope mr-2"></i>
                    <a href="mailto:admission@tourism.uz" class="font-semibold hover:text-green-600">admission@tourism.uz</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>