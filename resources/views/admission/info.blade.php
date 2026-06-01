<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Qabul haqida ma'lumot - Tourism Academy</title>

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
            background: linear-gradient(135deg, #f0f9f6 0%, #e8f5f0 100%);
        }

        .stat-card {
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(22, 160, 133, 0.2);
        }
    </style>
</head>
<body>
    <div class="min-h-screen py-8">
        <div class="container mx-auto px-4 max-w-7xl">
            <!-- Header -->
            <div class="text-center mb-10">
                <div class="flex justify-between items-center mb-4">
                    <a href="/local.uz/public/login" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-gray-700 transition">
                        <i class="fas fa-home mr-2"></i> Bosh sahifa
                    </a>
                    <div class="text-center flex-grow">
                        <h1 class="text-4xl font-bold text-gray-800 mb-4">Qabul - 2025/2026</h1>
                        <p class="text-xl text-gray-600">Tourism Academy Samarkand</p>
                    </div>
                    <div class="w-32"></div>
                </div>
            </div>

            <!-- Statistics -->
            <div class="grid md:grid-cols-4 gap-6 mb-10">
                <div class="stat-card bg-white rounded-lg p-6 text-center">
                    <div class="text-3xl font-bold text-green-600">{{ $statistics['total_applications'] }}</div>
                    <div class="text-gray-600 mt-2">Jami arizalar</div>
                    <i class="fas fa-file-alt text-4xl text-green-200 mt-3"></i>
                </div>

                <div class="stat-card bg-white rounded-lg p-6 text-center">
                    <div class="text-3xl font-bold text-yellow-600">{{ $statistics['pending'] }}</div>
                    <div class="text-gray-600 mt-2">Ko'rib chiqilmoqda</div>
                    <i class="fas fa-clock text-4xl text-yellow-200 mt-3"></i>
                </div>

                <div class="stat-card bg-white rounded-lg p-6 text-center">
                    <div class="text-3xl font-bold text-green-600">{{ $statistics['accepted'] }}</div>
                    <div class="text-gray-600 mt-2">Qabul qilingan</div>
                    <i class="fas fa-check-circle text-4xl text-green-200 mt-3"></i>
                </div>

                <div class="stat-card bg-white rounded-lg p-6 text-center">
                    <div class="text-3xl font-bold text-red-600">{{ $statistics['rejected'] }}</div>
                    <div class="text-gray-600 mt-2">Rad etilgan</div>
                    <i class="fas fa-times-circle text-4xl text-red-200 mt-3"></i>
                </div>
            </div>

            <!-- Main Content -->
            <div class="grid md:grid-cols-3 gap-8">
                <!-- Requirements Section -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">
                        <i class="fas fa-list text-green-600 mr-2"></i>
                        Qabul shartlari
                    </h2>
                    <ul class="space-y-3">
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                            <span class="text-gray-700">O'rta ta'lim yoki o'rta maxsus ta'lim haqida diplom</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                            <span class="text-gray-700">Pasport nusxasi</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                            <span class="text-gray-700">3x4 o'lchamdagi rasm (6 dona)</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                            <span class="text-gray-700">DTM test natijasi (majburiy emas)</span>
                        </li>
                    </ul>
                </div>

                <!-- Process Section -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">
                        <i class="fas fa-tasks text-green-600 mr-2"></i>
                        Qabul jarayoni
                    </h2>
                    <div class="space-y-4">
                        <div class="flex items-start">
                            <div class="bg-green-100 text-green-600 rounded-full w-8 h-8 flex items-center justify-center font-bold mr-3">1</div>
                            <div>
                                <div class="font-semibold text-gray-800">Onlayn ariza</div>
                                <div class="text-sm text-gray-600">Veb-sayt orqali ariza to'ldiring</div>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <div class="bg-green-100 text-green-600 rounded-full w-8 h-8 flex items-center justify-center font-bold mr-3">2</div>
                            <div>
                                <div class="font-semibold text-gray-800">Hujjatlar yuklash</div>
                                <div class="text-sm text-gray-600">Kerakli hujjatlarni yuklang</div>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <div class="bg-green-100 text-green-600 rounded-full w-8 h-8 flex items-center justify-center font-bold mr-3">3</div>
                            <div>
                                <div class="font-semibold text-gray-800">Ko'rib chiqish</div>
                                <div class="text-sm text-gray-600">Arizangiz 3-5 kun ichida ko'rib chiqiladi</div>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <div class="bg-green-100 text-green-600 rounded-full w-8 h-8 flex items-center justify-center font-bold mr-3">4</div>
                            <div>
                                <div class="font-semibold text-gray-800">Natija</div>
                                <div class="text-sm text-gray-600">SMS yoki email orqali xabardor qilinasiz</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Section -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">
                        <i class="fas fa-phone text-green-600 mr-2"></i>
                        Aloqa ma'lumotlari
                    </h2>
                    <div class="space-y-4">
                        <div>
                            <div class="text-sm text-gray-600">Qabul komissiyasi</div>
                            <div class="font-semibold text-gray-800">+998 66 233-08-41</div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-600">Email</div>
                            <div class="font-semibold text-gray-800">admission@tourism.uz</div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-600">Ish vaqti</div>
                            <div class="font-semibold text-gray-800">Dush-Jum: 9:00 - 18:00</div>
                            <div class="font-semibold text-gray-800">Shanba: 9:00 - 14:00</div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-600">Manzil</div>
                            <div class="font-semibold text-gray-800">Samarqand sh., Universitet xiyoboni 17</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Faculties Section -->
            <div class="mt-10">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Fakultetlar</h2>
                <div class="grid md:grid-cols-3 gap-6">
                    @foreach($faculties as $faculty)
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h3 class="font-semibold text-lg text-gray-800 mb-2">{{ $faculty->name_uz }}</h3>
                        <p class="text-gray-600 mb-4">Yo'nalishlar soni: {{ $faculty->specialties_count }}</p>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500">
                                <i class="fas fa-graduation-cap mr-1"></i>
                                {{ $faculty->student_capacity ?? 'N/A' }} o'rin
                            </span>
                            <a href="{{ route('admission.apply') }}" class="text-green-600 hover:text-green-700">
                                Ariza topshirish <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- CTA Section -->
            <div class="mt-12 bg-gradient-to-r from-green-700 to-green-600 rounded-lg p-8 text-center text-white">
                <h2 class="text-3xl font-bold mb-4">Kelajagingizni biz bilan quring!</h2>
                <p class="text-xl mb-6">Hoziroq ariza topshiring va o'z orzularingizga erishing</p>
                <div class="flex justify-center gap-4">
                    <a href="{{ route('admission.apply') }}" class="bg-white text-green-600 px-8 py-3 rounded-lg font-semibold hover:bg-green-50 transition">
                        <i class="fas fa-file-alt mr-2"></i> Ariza topshirish
                    </a>
                    <a href="{{ route('admission.check-status') }}" class="bg-green-800 text-white px-8 py-3 rounded-lg font-semibold hover:bg-green-900 transition">
                        <i class="fas fa-search mr-2"></i> Ariza holati
                    </a>
                </div>
            </div>

            <!-- Back to Home -->
            <div class="text-center mt-8">
                <a href="/local.uz/public/login" class="text-gray-600 hover:text-green-600 transition">
                    <i class="fas fa-home mr-2"></i> Bosh sahifaga qaytish
                </a>
            </div>
        </div>
    </div>
</body>
</html>