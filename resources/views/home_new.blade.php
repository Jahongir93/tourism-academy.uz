@extends('layouts.frontend')

@section('title', 'Bosh sahifa - Tourism Academy')

@section('content')
<!-- Hero Section -->
<section class="relative h-screen flex items-center justify-center overflow-hidden">
    <!-- Background Image with Overlay -->
    <div class="absolute inset-0">
        <img src="{{ asset('images/ext/placeholder.jpg') }}" alt="University" class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-gradient-to-br from-black/70 via-emerald-900/50 to-teal-900/60"></div>
    </div>

    <!-- Content -->
    <div class="relative z-10 text-center text-white px-4 max-w-4xl mx-auto">
        <h1 class="text-5xl md:text-7xl font-bold mb-6 animate-fade-in">
            Innovatsion ta'lim markazi
        </h1>
        <p class="text-2xl md:text-3xl mb-8 text-emerald-100 animate-slide-up">
            Bilim — kelajak kaliti
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center animate-fade-in-delay">
            <a href="{{ route('admission.apply') }}" class="bg-gradient-to-r from-emerald-500 to-teal-600 text-white px-8 py-4 rounded-full text-lg font-semibold hover:from-emerald-600 hover:to-teal-700 transition btn-hover inline-flex items-center justify-center">
                <i class="fas fa-user-plus mr-2"></i>Qo'shilish
            </a>
            <a href="{{ route('login') }}" class="bg-white/20 backdrop-blur-sm text-white border-2 border-white px-8 py-4 rounded-full text-lg font-semibold hover:bg-white hover:text-emerald-700 transition btn-hover inline-flex items-center justify-center">
                <i class="fas fa-sign-in-alt mr-2"></i>Kirish
            </a>
        </div>
    </div>

    <!-- Scroll Indicator -->
    <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 animate-bounce">
        <a href="#news" class="text-white">
            <i class="fas fa-chevron-down text-3xl"></i>
        </a>
    </div>
</section>

<!-- News Section -->
<section id="news" class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-4xl font-bold gradient-text mb-4">So'nggi yangiliklar</h2>
            <p class="text-gray-600 text-lg">Universitetdagi eng so'nggi voqealar va yangiliklar</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- News Card 1 -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden card-hover">
                <img src="{{ asset('images/ext/763130cac43032c7.jpg') }}" alt="News" class="w-full h-48 object-cover">
                <div class="p-6">
                    <span class="text-emerald-600 text-sm font-semibold">15-Dekabr, 2024</span>
                    <h3 class="text-xl font-bold mt-2 mb-3">Xalqaro ilmiy konferensiya</h3>
                    <p class="text-gray-600 mb-4">Turizm sohasidagi innovatsiyalar mavzusida xalqaro konferensiya o'tkazildi.</p>
                    <a href="#" class="text-emerald-600 font-semibold hover:text-emerald-700 transition">
                        Batafsil <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>

            <!-- News Card 2 -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden card-hover">
                <img src="{{ asset('images/ext/555f626738a84761.jpg') }}" alt="News" class="w-full h-48 object-cover">
                <div class="p-6">
                    <span class="text-emerald-600 text-sm font-semibold">10-Dekabr, 2024</span>
                    <h3 class="text-xl font-bold mt-2 mb-3">Yangi o'quv korpusi ochildi</h3>
                    <p class="text-gray-600 mb-4">5000 talaba sig'imiga ega zamonaviy o'quv korpusi foydalanishga topshirildi.</p>
                    <a href="#" class="text-emerald-600 font-semibold hover:text-emerald-700 transition">
                        Batafsil <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>

            <!-- News Card 3 -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden card-hover">
                <img src="{{ asset('images/ext/f50a06dbfa3cdec0.jpg') }}" alt="News" class="w-full h-48 object-cover">
                <div class="p-6">
                    <span class="text-emerald-600 text-sm font-semibold">5-Dekabr, 2024</span>
                    <h3 class="text-xl font-bold mt-2 mb-3">Startup musobaqasi g'oliblari</h3>
                    <p class="text-gray-600 mb-4">Talabalar o'rtasidagi startup musobaqasi yakunlandi va g'oliblar aniqlandi.</p>
                    <a href="#" class="text-emerald-600 font-semibold hover:text-emerald-700 transition">
                        Batafsil <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Events Section -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-4xl font-bold gradient-text mb-4">Yaqin tadbirlar</h2>
            <p class="text-gray-600 text-lg">Bo'lib o'tadigan tadbirlar va voqealar</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Event 1 -->
            <div class="bg-gradient-to-br from-emerald-50 to-teal-50 rounded-2xl p-6 border-l-4 border-emerald-500 card-hover">
                <div class="flex items-start space-x-4">
                    <div class="bg-emerald-600 text-white rounded-xl p-3 text-center min-w-[60px]">
                        <div class="text-2xl font-bold">25</div>
                        <div class="text-xs">DEK</div>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-bold text-lg mb-2">Yangi yil bayrami</h4>
                        <p class="text-gray-600 text-sm mb-2">
                            <i class="fas fa-clock mr-1"></i>14:00
                            <i class="fas fa-map-marker-alt ml-3 mr-1"></i>Bosh bino
                        </p>
                    </div>
                </div>
            </div>

            <!-- Event 2 -->
            <div class="bg-gradient-to-br from-emerald-50 to-teal-50 rounded-2xl p-6 border-l-4 border-emerald-500 card-hover">
                <div class="flex items-start space-x-4">
                    <div class="bg-emerald-600 text-white rounded-xl p-3 text-center min-w-[60px]">
                        <div class="text-2xl font-bold">10</div>
                        <div class="text-xs">YAN</div>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-bold text-lg mb-2">Ochiq eshiklar kuni</h4>
                        <p class="text-gray-600 text-sm mb-2">
                            <i class="fas fa-clock mr-1"></i>10:00
                            <i class="fas fa-map-marker-alt ml-3 mr-1"></i>Barcha korpuslar
                        </p>
                    </div>
                </div>
            </div>

            <!-- Event 3 -->
            <div class="bg-gradient-to-br from-emerald-50 to-teal-50 rounded-2xl p-6 border-l-4 border-emerald-500 card-hover">
                <div class="flex items-start space-x-4">
                    <div class="bg-emerald-600 text-white rounded-xl p-3 text-center min-w-[60px]">
                        <div class="text-2xl font-bold">15</div>
                        <div class="text-xs">YAN</div>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-bold text-lg mb-2">Job Fair 2025</h4>
                        <p class="text-gray-600 text-sm mb-2">
                            <i class="fas fa-clock mr-1"></i>09:00
                            <i class="fas fa-map-marker-alt ml-3 mr-1"></i>Sport majmuasi
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center mt-8">
            <a href="{{ route('frontend.events') }}" class="bg-gradient-to-r from-emerald-500 to-teal-600 text-white px-8 py-3 rounded-full font-semibold hover:from-emerald-600 hover:to-teal-700 transition btn-hover inline-flex items-center">
                Barcha tadbirlar <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>
    </div>
</section>

<!-- Faculties Section -->
<section class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-4xl font-bold gradient-text mb-4">Fakultetlar</h2>
            <p class="text-gray-600 text-lg">Ta'lim yo'nalishlari va fakultetlar</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Faculty 1 -->
            <div class="bg-white rounded-2xl shadow-xl p-6 text-center card-hover">
                <div class="w-20 h-20 bg-gradient-to-br from-emerald-400 to-teal-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-plane text-white text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold mb-2">Turizm</h3>
                <p class="text-gray-600 text-sm mb-4">Xalqaro turizm va mehmondo'stlik</p>
                <a href="#" class="text-emerald-600 font-semibold hover:text-emerald-700">
                    Ko'proq o'qish <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>

            <!-- Faculty 2 -->
            <div class="bg-white rounded-2xl shadow-xl p-6 text-center card-hover">
                <div class="w-20 h-20 bg-gradient-to-br from-emerald-400 to-teal-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-chart-line text-white text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold mb-2">Iqtisodiyot</h3>
                <p class="text-gray-600 text-sm mb-4">Turizm iqtisodiyoti va boshqaruv</p>
                <a href="#" class="text-emerald-600 font-semibold hover:text-emerald-700">
                    Ko'proq o'qish <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>

            <!-- Faculty 3 -->
            <div class="bg-white rounded-2xl shadow-xl p-6 text-center card-hover">
                <div class="w-20 h-20 bg-gradient-to-br from-emerald-400 to-teal-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-globe text-white text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold mb-2">Xorijiy tillar</h3>
                <p class="text-gray-600 text-sm mb-4">Turizm uchun xorijiy tillar</p>
                <a href="#" class="text-emerald-600 font-semibold hover:text-emerald-700">
                    Ko'proq o'qish <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>

            <!-- Faculty 4 -->
            <div class="bg-white rounded-2xl shadow-xl p-6 text-center card-hover">
                <div class="w-20 h-20 bg-gradient-to-br from-emerald-400 to-teal-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-laptop-code text-white text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold mb-2">IT va innovatsiyalar</h3>
                <p class="text-gray-600 text-sm mb-4">Raqamli texnologiyalar</p>
                <a href="#" class="text-emerald-600 font-semibold hover:text-emerald-700">
                    Ko'proq o'qish <i class="fas fa-arrow-right ml-1"></i>
                </a>
        </div>
    </div>
</section>

<!-- Library Section -->
<section class="py-20 bg-gradient-to-br from-emerald-600 via-teal-600 to-green-700 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div>
                <h2 class="text-4xl font-bold mb-6">Elektron kutubxona</h2>
                <p class="text-emerald-100 text-lg mb-8">
                    Minglab kitoblar, ilmiy maqolalar, video darslar va o'quv qo'llanmalariga ega zamonaviy elektron kutubxona xizmatidan foydalaning.
                </p>
                <div class="grid grid-cols-3 gap-4 mb-8">
                    <div class="text-center">
                        <div class="text-3xl font-bold">10,000+</div>
                        <div class="text-emerald-200">Kitoblar</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold">5,000+</div>
                        <div class="text-emerald-200">Video darslar</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold">24/7</div>
                        <div class="text-emerald-200">Ochiq</div>
                    </div>
                </div>
                <a href="{{ route('public.library') }}" class="bg-white text-emerald-700 px-8 py-3 rounded-full font-semibold hover:bg-emerald-50 transition btn-hover inline-flex items-center">
                    Kutubxonaga o'tish <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Resource Card 1 -->
                <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6 text-center card-hover">
                    <i class="fas fa-book text-4xl mb-3"></i>
                    <h4 class="font-semibold">Kitoblar</h4>
                    <p class="text-emerald-100 text-sm mt-2">PDF formatda</p>
                </div>

                <!-- Resource Card 2 -->
                <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6 text-center card-hover">
                    <i class="fas fa-video text-4xl mb-3"></i>
                    <h4 class="font-semibold">Video darslar</h4>
                    <p class="text-emerald-100 text-sm mt-2">HD sifatda</p>
                </div>

                <!-- Resource Card 3 -->
                <div class="bg-white/10 backdrop-blur-sm rounded-2xl p-6 text-center card-hover">
                    <i class="fas fa-file-alt text-4xl mb-3"></i>
                    <h4 class="font-semibold">O'quv qo'llanmalar</h4>
                    <p class="text-emerald-100 text-sm mt-2">Barcha fanlar</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Statistics Section -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
            <!-- Stat 1 -->
            <div class="text-center">
                <div class="text-5xl font-bold gradient-text mb-2" data-counter="5000">0</div>
                <div class="text-gray-600 font-semibold">Talabalar</div>
            </div>

            <!-- Stat 2 -->
            <div class="text-center">
                <div class="text-5xl font-bold gradient-text mb-2" data-counter="300">0</div>
                <div class="text-gray-600 font-semibold">O'qituvchilar</div>
            </div>

            <!-- Stat 3 -->
            <div class="text-center">
                <div class="text-5xl font-bold gradient-text mb-2" data-counter="50">0</div>
                <div class="text-gray-600 font-semibold">Hamkorliklar</div>
            </div>

            <!-- Stat 4 -->
            <div class="text-center">
                <div class="text-5xl font-bold gradient-text mb-2" data-counter="20">0</div>
                <div class="text-gray-600 font-semibold">Yillik tajriba</div>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials Section -->
<section class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-4xl font-bold gradient-text mb-4">Muvaffaqiyat hikoyalari</h2>
            <p class="text-gray-600 text-lg">Bizning bitiruvchilarimiz fikrlari</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Testimonial 1 -->
            <div class="bg-white rounded-2xl shadow-xl p-6 card-hover">
                <div class="flex items-center mb-4">
                    <img src="{{ url('avatar') }}?name=Aziz+Karimov&background=10b981&color=fff" alt="Avatar" class="w-12 h-12 rounded-full mr-4">
                    <div>
                        <h4 class="font-bold">Aziz Karimov</h4>
                        <p class="text-gray-600 text-sm">Turizm, 2022</p>
                    </div>
                </div>
                <p class="text-gray-600 italic">"Tourism Academy menga katta imkoniyatlar eshigini ochdi. Hozir men xalqaro turizm kompaniyasida ishlayman."</p>
                <div class="flex text-yellow-400 mt-4">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                </div>
            </div>

            <!-- Testimonial 2 -->
            <div class="bg-white rounded-2xl shadow-xl p-6 card-hover">
                <div class="flex items-center mb-4">
                    <img src="{{ url('avatar') }}?name=Dilnoza+Saidova&background=10b981&color=fff" alt="Avatar" class="w-12 h-12 rounded-full mr-4">
                    <div>
                        <h4 class="font-bold">Dilnoza Saidova</h4>
                        <p class="text-gray-600 text-sm">Mehmondo'stlik, 2023</p>
                    </div>
                </div>
                <p class="text-gray-600 italic">"Professional o'qituvchilar va zamonaviy ta'lim metodlari tufayli men o'z kasbimning ustasiga aylandim."</p>
                <div class="flex text-yellow-400 mt-4">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                </div>
            </div>

            <!-- Testimonial 3 -->
            <div class="bg-white rounded-2xl shadow-xl p-6 card-hover">
                <div class="flex items-center mb-4">
                    <img src="{{ url('avatar') }}?name=Jasur+Toshev&background=10b981&color=fff" alt="Avatar" class="w-12 h-12 rounded-full mr-4">
                    <div>
                        <h4 class="font-bold">Jasur Toshev</h4>
                        <p class="text-gray-600 text-sm">IT, 2023</p>
                    </div>
                </div>
                <p class="text-gray-600 italic">"Universitetda olgan bilimlarim tufayli o'z startapimni yaratdim va muvaffaqiyatli yuritmoqdaman."</p>
                <div class="flex text-yellow-400 mt-4">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Gallery Section -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-4xl font-bold gradient-text mb-4">Foto galereya</h2>
            <p class="text-gray-600 text-lg">Universitet hayotidan lavhalar</p>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            <div class="col-span-2 row-span-2">
                <img src="{{ asset('images/ext/9229f87e63308cf0.jpg') }}" alt="Gallery" class="w-full h-full object-cover rounded-2xl shadow-xl hover:shadow-2xl transition">
            </div>
            <div>
                <img src="{{ asset('images/ext/92a755e08645cd71.jpg') }}" alt="Gallery" class="w-full h-full object-cover rounded-2xl shadow-xl hover:shadow-2xl transition">
            </div>
            <div>
                <img src="{{ asset('images/ext/2563486dc9f24b73.jpg') }}" alt="Gallery" class="w-full h-full object-cover rounded-2xl shadow-xl hover:shadow-2xl transition">
            </div>
            <div>
                <img src="{{ asset('images/ext/99562c0638126f8f.jpg') }}" alt="Gallery" class="w-full h-full object-cover rounded-2xl shadow-xl hover:shadow-2xl transition">
            </div>
            <div>
                <img src="{{ asset('images/ext/1928825cab4f7cdf.jpg') }}" alt="Gallery" class="w-full h-full object-cover rounded-2xl shadow-xl hover:shadow-2xl transition">
            </div>
            <div class="col-span-2">
                <img src="{{ asset('images/ext/eaf642e2041a4a7c.jpg') }}" alt="Gallery" class="w-full h-full object-cover rounded-2xl shadow-xl hover:shadow-2xl transition">
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-20 bg-gradient-to-r from-emerald-600 to-teal-600 text-white">
    <div class="max-w-4xl mx-auto text-center px-4">
        <h2 class="text-4xl font-bold mb-6">Kelajagingizni biz bilan quring!</h2>
        <p class="text-xl text-emerald-100 mb-8">2025-yil qabul jarayoni boshlandi. Hoziroq ariza topshiring!</p>
        <a href="{{ route('admission.apply') }}" class="bg-white text-emerald-700 px-12 py-4 rounded-full text-lg font-bold hover:bg-emerald-50 transition btn-hover inline-flex items-center">
            Online ariza topshirish <i class="fas fa-arrow-right ml-2"></i>
        </a>
    </div>
</section>
@endsection

@push('styles')
<style>
    @keyframes fade-in {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes slide-up {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-fade-in {
        animation: fade-in 1s ease-out;
    }

    .animate-slide-up {
        animation: slide-up 1s ease-out 0.3s both;
    }

    .animate-fade-in-delay {
        animation: fade-in 1s ease-out 0.6s both;
    }
</style>
@endpush

@push('scripts')
<script>
    // Counter animation
    const counters = document.querySelectorAll('[data-counter]');
    const speed = 200;

    const countUp = (counter) => {
        const target = +counter.getAttribute('data-counter');
        const increment = target / speed;

        const updateCounter = () => {
            const count = +counter.innerText;
            if (count < target) {
                counter.innerText = Math.ceil(count + increment);
                setTimeout(updateCounter, 10);
            } else {
                counter.innerText = target + '+';
            }
        };

        updateCounter();
    };

    // Intersection Observer for counter animation
    const observerOptions = {
        threshold: 0.5,
        rootMargin: '0px 0px -100px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                countUp(entry.target);
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    counters.forEach(counter => {
        observer.observe(counter);
    });
</script>
@endpush