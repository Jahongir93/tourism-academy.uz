<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tourism Academy Samarkand - Bosh sahifa</title>

    <!-- Tailwind CSS -->
    <script src="{{ asset('vendor/tailwind/tailwind.min.js') }}"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.min.css') }}">

    <!-- Swiper CSS -->
    <link rel="stylesheet" href="{{ asset('vendor/swiper/swiper-bundle.min.css') }}" />

    <!-- Alpine.js -->
    <script defer src="{{ asset('vendor/alpine/alpine.min.js') }}"></script>

    <style>
        :root {
            --primary-green: #16a085;
            --dark-green: #0d4f3c;
            --light-green: #e8f5f0;
            --accent-green: #48c9b0;
        }

        .gradient-bg {
            background: linear-gradient(135deg, var(--dark-green), var(--primary-green));
        }

        .hover-lift {
            transition: all 0.3s ease;
        }

        .hover-lift:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }

        .section-title::after {
            content: '';
            display: block;
            width: 60px;
            height: 4px;
            background: var(--primary-green);
            margin: 15px auto 0;
            border-radius: 2px;
        }

        /* Hero Slider Animation */
        .hero-text {
            animation: fadeInUp 1s ease;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Smooth scroll */
        html {
            scroll-behavior: smooth;
        }

        .nav-link {
            position: relative;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: -5px;
            left: 50%;
            background: var(--accent-green);
            transition: all 0.3s ease;
        }

        .nav-link:hover::after {
            width: 100%;
            left: 0;
        }
    </style>
</head>
<body class="font-sans bg-gray-50">

    <!-- Header -->
    <header class="bg-white shadow-md fixed w-full top-0 z-50" x-data="{ mobileMenu: false }">
        <!-- Top Bar -->
        <div class="gradient-bg text-white py-2">
            <div class="container mx-auto px-4">
                <div class="flex justify-between items-center text-sm">
                    <div class="flex items-center space-x-4">
                        <span><i class="fas fa-phone mr-1"></i> +998 66 233-45-67</span>
                        <span class="hidden md:inline"><i class="fas fa-envelope mr-1"></i> info@tourism.uz</span>
                    </div>
                    <div class="flex items-center space-x-4">
                        <a href="{{ route('login') }}" class="hover:text-gray-200 transition">
                            <i class="fas fa-sign-in-alt mr-1"></i> Kirish
                        </a>
                        <a href="{{ route('student.register.form') }}" class="hover:text-gray-200 transition">
                            <i class="fas fa-user-plus mr-1"></i> Ro'yxatdan o'tish
                        </a>
                        <a href="{{ route('admission.apply') }}" class="bg-white text-green-600 px-3 py-1 rounded-full hover:bg-gray-100 transition">
                            <i class="fas fa-graduation-cap mr-1"></i> Online Admission
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Navigation -->
        <nav class="container mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="{{ route('home') }}" class="flex items-center space-x-3">
                        <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-full flex items-center justify-center">
                            <i class="fas fa-graduation-cap text-white text-xl"></i>
                        </div>
                        <div>
                            <h1 class="text-xl font-bold text-gray-800">Tourism Academy</h1>
                            <p class="text-xs text-gray-600">Samarkand</p>
                        </div>
                    </a>
                </div>

                <!-- Desktop Menu -->
                <div class="hidden lg:flex items-center space-x-8">
                    <a href="#home" class="nav-link text-gray-700 hover:text-green-600 transition">Bosh sahifa</a>
                    <div class="relative group">
                        <a href="#about" class="nav-link text-gray-700 hover:text-green-600 transition flex items-center">
                            Akademiya haqida
                            <i class="fas fa-chevron-down ml-1 text-xs"></i>
                        </a>
                        <div class="absolute left-0 mt-2 w-56 bg-white rounded-lg shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300">
                            <a href="#virtual-tour" class="block px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 hover:text-green-600">
                                <i class="fas fa-vr-cardboard mr-2"></i> Kampus virtual turi
                            </a>
                            <a href="#map" class="block px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 hover:text-green-600">
                                <i class="fas fa-map-marked-alt mr-2"></i> Interaktiv xarita
                            </a>
                        </div>
                    </div>
                    <a href="{{ route('faculties') }}" class="nav-link text-gray-700 hover:text-green-600 transition">Fakultetlar</a>
                    <a href="{{ route('departments') }}" class="nav-link text-gray-700 hover:text-green-600 transition">Kafedralar</a>
                    <div class="relative group">
                        <a href="#research" class="nav-link text-gray-700 hover:text-green-600 transition flex items-center">
                            Tadqiqotlar
                            <i class="fas fa-chevron-down ml-1 text-xs"></i>
                        </a>
                        <div class="absolute left-0 mt-2 w-56 bg-white rounded-lg shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300">
                            <a href="#library" class="block px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 hover:text-green-600">
                                <i class="fas fa-book mr-2"></i> Elektron kutubxona
                            </a>
                            <a href="#videos" class="block px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 hover:text-green-600">
                                <i class="fas fa-video mr-2"></i> Video darslar
                            </a>
                        </div>
                    </div>
                    <a href="#student-life" class="nav-link text-gray-700 hover:text-green-600 transition">Talabalar hayoti</a>
                    <a href="{{ route('news') }}" class="nav-link text-gray-700 hover:text-green-600 transition">Yangiliklar</a>
                    <a href="#events" class="nav-link text-gray-700 hover:text-green-600 transition">Tadbirlar</a>
                </div>

                <!-- Mobile Menu Button -->
                <button @click="mobileMenu = !mobileMenu" class="lg:hidden text-gray-700 focus:outline-none">
                    <i class="fas fa-bars text-2xl"></i>
                </button>
            </div>

            <!-- Mobile Menu -->
            <div x-show="mobileMenu" x-transition class="lg:hidden mt-4 pb-4">
                <a href="#home" class="block py-2 text-gray-700 hover:text-green-600">Bosh sahifa</a>
                <a href="#about" class="block py-2 text-gray-700 hover:text-green-600">Akademiya haqida</a>
                <a href="{{ route('faculties') }}" class="block py-2 text-gray-700 hover:text-green-600">Fakultetlar</a>
                <a href="{{ route('departments') }}" class="block py-2 text-gray-700 hover:text-green-600">Kafedralar</a>
                <a href="#research" class="block py-2 text-gray-700 hover:text-green-600">Tadqiqotlar</a>
                <a href="#student-life" class="block py-2 text-gray-700 hover:text-green-600">Talabalar hayoti</a>
                <a href="{{ route('news') }}" class="block py-2 text-gray-700 hover:text-green-600">Yangiliklar</a>
                <a href="#events" class="block py-2 text-gray-700 hover:text-green-600">Tadbirlar</a>
            </div>
        </nav>
    </header>

    <!-- Hero Slider Section -->
    <section id="home" class="mt-24">
        <div class="swiper heroSwiper">
            <div class="swiper-wrapper">
                <!-- Slide 1 -->
                <div class="swiper-slide relative">
                    <div class="h-[600px] bg-cover bg-center" style="background-image: url('{{ asset('images/ext/placeholder.jpg') }}');">
                        <div class="absolute inset-0 bg-black bg-opacity-50"></div>
                        <div class="relative h-full flex items-center">
                            <div class="container mx-auto px-4">
                                <div class="max-w-3xl text-white hero-text">
                                    <h2 class="text-5xl font-bold mb-4">Tourism Academy Samarkand</h2>
                                    <p class="text-xl mb-6">Zamonaviy ta'lim, global imkoniyatlar</p>
                                    <div class="flex flex-wrap gap-4">
                                        <a href="{{ route('admission.apply') }}" class="bg-green-500 hover:bg-green-600 text-white px-8 py-3 rounded-full transition">
                                            <i class="fas fa-user-graduate mr-2"></i> Hozir ariza topshiring
                                        </a>
                                        <a href="#virtual-tour" class="bg-transparent border-2 border-white text-white hover:bg-white hover:text-gray-900 px-8 py-3 rounded-full transition">
                                            <i class="fas fa-play-circle mr-2"></i> Virtual tur
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Slide 2 -->
                <div class="swiper-slide relative">
                    <div class="h-[600px] bg-cover bg-center" style="background-image: url('{{ asset('images/ext/placeholder.jpg') }}');">
                        <div class="absolute inset-0 bg-black bg-opacity-50"></div>
                        <div class="relative h-full flex items-center">
                            <div class="container mx-auto px-4">
                                <div class="max-w-3xl text-white hero-text">
                                    <h2 class="text-5xl font-bold mb-4">Xalqaro darajadagi ta'lim</h2>
                                    <p class="text-xl mb-6">15+ ta'lim yo'nalishlari, 120+ malakali o'qituvchilar</p>
                                    <a href="#programs" class="bg-green-500 hover:bg-green-600 text-white px-8 py-3 rounded-full inline-block transition">
                                        <i class="fas fa-book-open mr-2"></i> Dasturlarni ko'rish
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Slide 3 -->
                <div class="swiper-slide relative">
                    <div class="h-[600px] bg-cover bg-center" style="background-image: url('{{ asset('images/ext/placeholder.jpg') }}');">
                        <div class="absolute inset-0 bg-black bg-opacity-50"></div>
                        <div class="relative h-full flex items-center">
                            <div class="container mx-auto px-4">
                                <div class="max-w-3xl text-white hero-text">
                                    <h2 class="text-5xl font-bold mb-4">Kelajakni biz bilan quring</h2>
                                    <p class="text-xl mb-6">Zamonaviy kampus, boy talabalar hayoti</p>
                                    <a href="#student-life" class="bg-green-500 hover:bg-green-600 text-white px-8 py-3 rounded-full inline-block transition">
                                        <i class="fas fa-users mr-2"></i> Talabalar hayoti
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="swiper-pagination"></div>
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
        </div>
    </section>

    <!-- Statistics Section -->
    <section class="py-12 gradient-bg text-white">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                <div class="text-center">
                    <div class="text-4xl font-bold mb-2">{{ $stats['students'] }}+</div>
                    <div class="text-sm opacity-90">Talabalar</div>
                </div>
                <div class="text-center">
                    <div class="text-4xl font-bold mb-2">{{ $stats['teachers'] }}+</div>
                    <div class="text-sm opacity-90">O'qituvchilar</div>
                </div>
                <div class="text-center">
                    <div class="text-4xl font-bold mb-2">{{ $stats['programs'] }}+</div>
                    <div class="text-sm opacity-90">Dasturlar</div>
                </div>
                <div class="text-center">
                    <div class="text-4xl font-bold mb-2">{{ $stats['graduates'] }}+</div>
                    <div class="text-sm opacity-90">Bitiruvchilar</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Sections -->
    <section id="features" class="py-20 bg-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-bold text-gray-800 mb-4 section-title">Bizning xizmatlar</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">Tourism Academy Samarkand zamonaviy ta'lim va ilm-fan markazidir</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- 3D Virtual Tour -->
                <div class="text-center hover-lift bg-gray-50 rounded-xl p-8">
                    <div class="w-20 h-20 mx-auto mb-6 bg-gradient-to-br from-green-400 to-green-600 rounded-full flex items-center justify-center">
                        <i class="fas fa-vr-cardboard text-white text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">3D Virtual Tur</h3>
                    <p class="text-gray-600 mb-4">Kampusimizni virtual muhitda ko'ring va tanishing</p>
                    <a href="#virtual-tour" class="text-green-600 hover:text-green-700 font-medium">
                        Ko'rish <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>

                <!-- Education Programs -->
                <div class="text-center hover-lift bg-gray-50 rounded-xl p-8">
                    <div class="w-20 h-20 mx-auto mb-6 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center">
                        <i class="fas fa-graduation-cap text-white text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Ta'lim yo'nalishlari</h3>
                    <p class="text-gray-600 mb-4">15+ zamonaviy ta'lim dasturlari va yo'nalishlari</p>
                    <a href="#programs" class="text-green-600 hover:text-green-700 font-medium">
                        Batafsil <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>

                <!-- E-Library -->
                <div class="text-center hover-lift bg-gray-50 rounded-xl p-8">
                    <div class="w-20 h-20 mx-auto mb-6 bg-gradient-to-br from-purple-400 to-purple-600 rounded-full flex items-center justify-center">
                        <i class="fas fa-book text-white text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Elektron kutubxona</h3>
                    <p class="text-gray-600 mb-4">10,000+ elektron kitoblar va ilmiy maqolalar</p>
                    <a href="#library" class="text-green-600 hover:text-green-700 font-medium">
                        Kirish <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Additional Services -->
    <section class="py-20 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white rounded-lg p-6 hover-lift">
                    <i class="fas fa-home text-3xl text-green-500 mb-4"></i>
                    <h4 class="font-bold text-gray-800 mb-2">Kampus virtual turi</h4>
                    <p class="text-sm text-gray-600">360° ko'rinishda kampusni ko'ring</p>
                </div>

                <div class="bg-white rounded-lg p-6 hover-lift">
                    <i class="fas fa-map-marked-alt text-3xl text-blue-500 mb-4"></i>
                    <h4 class="font-bold text-gray-800 mb-2">Interaktiv xarita</h4>
                    <p class="text-sm text-gray-600">Kampus joylashuvi va yo'nalishlar</p>
                </div>

                <div class="bg-white rounded-lg p-6 hover-lift">
                    <i class="fas fa-microscope text-3xl text-purple-500 mb-4"></i>
                    <h4 class="font-bold text-gray-800 mb-2">Tadqiqotlar</h4>
                    <p class="text-sm text-gray-600">Ilmiy tadqiqot markazlari</p>
                </div>

                <div class="bg-white rounded-lg p-6 hover-lift">
                    <i class="fas fa-video text-3xl text-red-500 mb-4"></i>
                    <h4 class="font-bold text-gray-800 mb-2">Video darslar</h4>
                    <p class="text-sm text-gray-600">Online ta'lim resurslari</p>
                </div>

                <div class="bg-white rounded-lg p-6 hover-lift">
                    <i class="fas fa-users text-3xl text-orange-500 mb-4"></i>
                    <h4 class="font-bold text-gray-800 mb-2">Talabalar hayoti</h4>
                    <p class="text-sm text-gray-600">Klub va to'garaklar</p>
                </div>

                <div class="bg-white rounded-lg p-6 hover-lift">
                    <i class="fas fa-newspaper text-3xl text-teal-500 mb-4"></i>
                    <h4 class="font-bold text-gray-800 mb-2">Yangiliklar</h4>
                    <p class="text-sm text-gray-600">So'nggi yangiliklar va e'lonlar</p>
                </div>

                <div class="bg-white rounded-lg p-6 hover-lift">
                    <i class="fas fa-calendar-alt text-3xl text-pink-500 mb-4"></i>
                    <h4 class="font-bold text-gray-800 mb-2">Tadbirlar</h4>
                    <p class="text-sm text-gray-600">Konferensiya va seminarlar</p>
                </div>

                <div class="bg-white rounded-lg p-6 hover-lift">
                    <i class="fas fa-globe text-3xl text-indigo-500 mb-4"></i>
                    <h4 class="font-bold text-gray-800 mb-2">Xalqaro aloqalar</h4>
                    <p class="text-sm text-gray-600">Hamkorlik dasturlari</p>
                </div>
            </div>
        </div>
    </section>

    <!-- News Section -->
    <section id="news" class="py-20 bg-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-bold text-gray-800 mb-4 section-title">So'nggi yangiliklar</h2>
                <p class="text-gray-600 max-w-2xl mx-auto">Akademiyamiz hayotidan eng so'nggi xabarlar</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @foreach($news as $item)
                <article class="bg-white rounded-xl overflow-hidden shadow-lg hover-lift">
                    <div class="h-48 bg-cover bg-center" style="background-image: url('{{ $item['image'] }}');">
                        <div class="h-full bg-gradient-to-t from-black/50 to-transparent flex items-end p-4">
                            <span class="bg-green-500 text-white text-xs px-3 py-1 rounded-full">{{ $item['category'] }}</span>
                        </div>
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-2 line-clamp-2">{{ $item['title'] }}</h3>
                        <p class="text-gray-600 mb-4 line-clamp-3">{{ $item['description'] }}</p>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500">
                                <i class="far fa-calendar mr-1"></i> {{ $item['date']->format('d.m.Y') }}
                            </span>
                            <a href="{{ route('news.show', $item['id']) }}" class="text-green-600 hover:text-green-700 font-medium">
                                Batafsil <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </div>
                    </div>
                </article>
                @endforeach
            </div>

            <div class="text-center mt-12">
                <a href="{{ route('news') }}" class="bg-green-500 hover:bg-green-600 text-white px-8 py-3 rounded-full inline-block transition">
                    Barcha yangiliklar <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 gradient-bg">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-4xl font-bold text-white mb-4">Kelajagingizni biz bilan boshlang!</h2>
            <p class="text-xl text-white/90 mb-8 max-w-2xl mx-auto">
                Tourism Academy Samarkand - zamonaviy ta'lim, xalqaro imkoniyatlar va yorqin kelajak sari birinchi qadamingiz
            </p>
            <div class="flex flex-wrap gap-4 justify-center">
                <a href="{{ route('admission.apply') }}" class="bg-white text-green-600 hover:bg-gray-100 px-8 py-3 rounded-full font-medium transition">
                    <i class="fas fa-file-alt mr-2"></i> Ariza topshirish
                </a>
                <a href="{{ route('contact') }}" class="bg-transparent border-2 border-white text-white hover:bg-white hover:text-green-600 px-8 py-3 rounded-full font-medium transition">
                    <i class="fas fa-phone mr-2"></i> Bog'lanish
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white pt-12 pb-6">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-8">
                <!-- About -->
                <div>
                    <h4 class="text-xl font-bold mb-4">Tourism Academy</h4>
                    <p class="text-gray-400 mb-4">
                        Samarkand shahrida joylashgan zamonaviy oliy ta'lim muassasasi. Biz turizm sohasida yuqori malakali mutaxassislar tayyorlaymiz.
                    </p>
                    <div class="flex space-x-3">
                        <a href="#" class="w-10 h-10 bg-gray-800 hover:bg-green-600 rounded-full flex items-center justify-center transition">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-gray-800 hover:bg-green-600 rounded-full flex items-center justify-center transition">
                            <i class="fab fa-telegram-plane"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-gray-800 hover:bg-green-600 rounded-full flex items-center justify-center transition">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-gray-800 hover:bg-green-600 rounded-full flex items-center justify-center transition">
                            <i class="fab fa-youtube"></i>
                        </a>
                    </div>
                </div>

                <!-- Quick Links -->
                <div>
                    <h4 class="text-xl font-bold mb-4">Tez havolalar</h4>
                    <ul class="space-y-2">
                        <li><a href="#about" class="text-gray-400 hover:text-green-400 transition">Biz haqimizda</a></li>
                        <li><a href="{{ route('faculties') }}" class="text-gray-400 hover:text-green-400 transition">Fakultetlar</a></li>
                        <li><a href="#programs" class="text-gray-400 hover:text-green-400 transition">Dasturlar</a></li>
                        <li><a href="{{ route('admission.apply') }}" class="text-gray-400 hover:text-green-400 transition">Qabul</a></li>
                        <li><a href="#research" class="text-gray-400 hover:text-green-400 transition">Tadqiqotlar</a></li>
                        <li><a href="{{ route('news') }}" class="text-gray-400 hover:text-green-400 transition">Yangiliklar</a></li>
                    </ul>
                </div>

                <!-- Student Services -->
                <div>
                    <h4 class="text-xl font-bold mb-4">Talabalar uchun</h4>
                    <ul class="space-y-2">
                        <li><a href="#library" class="text-gray-400 hover:text-green-400 transition">Elektron kutubxona</a></li>
                        <li><a href="#student-life" class="text-gray-400 hover:text-green-400 transition">Talabalar hayoti</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-green-400 transition">Sport va madaniyat</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-green-400 transition">Yotoqxonalar</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-green-400 transition">Stipendiyalar</a></li>
                        <li><a href="#virtual-tour" class="text-gray-400 hover:text-green-400 transition">Virtual tur</a></li>
                    </ul>
                </div>

                <!-- Contact Info -->
                <div>
                    <h4 class="text-xl font-bold mb-4">Bog'lanish</h4>
                    <ul class="space-y-3">
                        <li class="flex items-start">
                            <i class="fas fa-map-marker-alt text-green-400 mt-1 mr-3"></i>
                            <span class="text-gray-400">Samarkand shahri, Universitet xiyoboni, 15</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-phone text-green-400 mr-3"></i>
                            <span class="text-gray-400">+998 66 233-45-67</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-envelope text-green-400 mr-3"></i>
                            <span class="text-gray-400">info@tourism.uz</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-clock text-green-400 mr-3"></i>
                            <span class="text-gray-400">Dush-Jum: 9:00 - 18:00</span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Bottom Bar -->
            <div class="border-t border-gray-800 pt-6">
                <div class="flex flex-col md:flex-row justify-between items-center">
                    <p class="text-gray-400 text-sm mb-4 md:mb-0">
                        © 2025 Tourism Academy Samarkand. Barcha huquqlar himoyalangan.
                    </p>
                    <div class="flex space-x-6">
                        <a href="#" class="text-gray-400 hover:text-green-400 text-sm transition">Maxfiylik siyosati</a>
                        <a href="#" class="text-gray-400 hover:text-green-400 text-sm transition">Foydalanish shartlari</a>
                        <a href="#" class="text-gray-400 hover:text-green-400 text-sm transition">Sayt xaritasi</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Back to Top Button -->
    <button onclick="window.scrollTo({top: 0, behavior: 'smooth'})"
            class="fixed bottom-8 right-8 w-12 h-12 bg-green-500 hover:bg-green-600 text-white rounded-full shadow-lg hidden transition"
            id="backToTop">
        <i class="fas fa-arrow-up"></i>
    </button>

    <!-- Swiper JS -->
    <script src="{{ asset('vendor/swiper/swiper-bundle.min.js') }}"></script>

    <script>
        // Hero Slider
        var swiper = new Swiper(".heroSwiper", {
            spaceBetween: 0,
            centeredSlides: true,
            autoplay: {
                delay: 5000,
                disableOnInteraction: false,
            },
            pagination: {
                el: ".swiper-pagination",
                clickable: true,
            },
            navigation: {
                nextEl: ".swiper-button-next",
                prevEl: ".swiper-button-prev",
            },
            loop: true,
        });

        // Back to Top Button
        window.addEventListener('scroll', function() {
            const backToTop = document.getElementById('backToTop');
            if (window.pageYOffset > 300) {
                backToTop.classList.remove('hidden');
            } else {
                backToTop.classList.add('hidden');
            }
        });

        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>
</body>
</html>