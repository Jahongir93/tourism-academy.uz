@extends('layouts.frontend')

@section('title', 'Virtual Kampus Turi - Tourism Academy')

@push('styles')
<style>
    .hero-section {
        background: linear-gradient(135deg, #0066CC 0%, #004d99 50%, #003366 100%);
        position: relative;
        overflow: hidden;
    }
    .hero-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Ccircle cx='50' cy='50' r='40' fill='none' stroke='rgba(255,255,255,0.1)' stroke-width='0.5'/%3E%3C/svg%3E") repeat;
        opacity: 0.3;
    }
    .feature-card {
        transition: all 0.3s ease;
        border: 1px solid rgba(0,0,0,0.08);
    }
    .feature-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.1);
    }
    .feature-icon {
        width: 80px;
        height: 80px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 20px;
        font-size: 2rem;
    }
    .panorama-preview {
        height: 200px;
        background-size: cover;
        background-position: center;
        border-radius: 12px;
        position: relative;
        overflow: hidden;
    }
    .panorama-preview::after {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(to top, rgba(0,0,0,0.5), transparent);
    }
    .panorama-preview .play-btn {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 60px;
        height: 60px;
        background: rgba(255,255,255,0.9);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10;
        transition: all 0.3s ease;
    }
    .panorama-preview:hover .play-btn {
        transform: translate(-50%, -50%) scale(1.1);
        background: white;
    }
</style>
@endpush

@section('content')
<!-- Hero Section -->
<section class="hero-section py-20 lg:py-28">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="text-center text-white">
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-6">
                Virtual Kampus Turi
            </h1>
            <p class="text-xl md:text-2xl text-blue-100 mb-8 max-w-3xl mx-auto">
                Tourism Academy kampusini 360° formatda ko'ring, interaktiv xarita orqali tanishing va bizga qanday yetib kelishni bilib oling
            </p>
            <div class="flex flex-wrap justify-center gap-4">
                <a href="{{ route('campus-tour.public.virtual-tour') }}" class="inline-flex items-center bg-white text-blue-600 px-8 py-4 rounded-xl font-semibold hover:bg-blue-50 transition btn-hover">
                    <i class="fas fa-vr-cardboard mr-3 text-xl"></i>
                    360° Virtual Tur
                </a>
                <a href="{{ route('campus-tour.public.map') }}" class="inline-flex items-center bg-blue-500 bg-opacity-30 text-white px-8 py-4 rounded-xl font-semibold hover:bg-opacity-50 transition border border-white/30">
                    <i class="fas fa-map-marked-alt mr-3 text-xl"></i>
                    Interaktiv Xarita
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-800 mb-4">Kampusni O'rganing</h2>
            <p class="text-gray-600 max-w-2xl mx-auto">
                Akademiyamiz hududini turli yo'llar bilan tanib chiqing
            </p>
        </div>

        <div class="grid md:grid-cols-3 gap-8">
            <!-- Virtual Tour -->
            <a href="{{ route('campus-tour.public.virtual-tour') }}" class="feature-card bg-white rounded-2xl p-8 text-center">
                <div class="feature-icon bg-blue-100 text-blue-600 mx-auto mb-6">
                    <i class="fas fa-vr-cardboard"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-3">360° Virtual Tur</h3>
                <p class="text-gray-600 mb-4">
                    Binolar ichini 360 daraja formatda ko'ring. Xuddi o'zingiz bo'lgandek his qiling.
                </p>
                <span class="text-blue-600 font-medium">
                    Boshlanish <i class="fas fa-arrow-right ml-2"></i>
                </span>
            </a>

            <!-- Interactive Map -->
            <a href="{{ route('campus-tour.public.map') }}" class="feature-card bg-white rounded-2xl p-8 text-center">
                <div class="feature-icon bg-green-100 text-green-600 mx-auto mb-6">
                    <i class="fas fa-map-marked-alt"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-3">Interaktiv Xarita</h3>
                <p class="text-gray-600 mb-4">
                    Kampus xaritasida binolar, kutubxona, sport zali va boshqa joylarni toping.
                </p>
                <span class="text-green-600 font-medium">
                    Ko'rish <i class="fas fa-arrow-right ml-2"></i>
                </span>
            </a>

            <!-- Directions -->
            <a href="{{ route('campus-tour.public.directions') }}" class="feature-card bg-white rounded-2xl p-8 text-center">
                <div class="feature-icon bg-orange-100 text-orange-600 mx-auto mb-6">
                    <i class="fas fa-route"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-3">Transport Yo'nalishlari</h3>
                <p class="text-gray-600 mb-4">
                    Bizga qanday yetib kelish mumkinligini bilib oling. Avtobus, taksi va boshqa yo'llar.
                </p>
                <span class="text-orange-600 font-medium">
                    Yo'nalishlar <i class="fas fa-arrow-right ml-2"></i>
                </span>
            </a>
        </div>
    </div>
</section>

<!-- Featured Panoramas -->
@if($featuredPanoramas->count() > 0)
<section class="py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-3xl font-bold text-gray-800">Tanlangan Panoramalar</h2>
            <a href="{{ route('campus-tour.public.virtual-tour') }}" class="text-blue-600 font-medium hover:text-blue-700">
                Barchasi <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($featuredPanoramas as $panorama)
                <a href="{{ route('campus-tour.public.panorama', $panorama) }}" class="block group">
                    <div class="panorama-preview mb-4" style="background-image: url('{{ $panorama->thumbnail_url }}')">
                        <div class="play-btn">
                            <i class="fas fa-play text-blue-600 ml-1"></i>
                        </div>
                    </div>
                    <h3 class="font-semibold text-gray-800 group-hover:text-blue-600 transition">{{ $panorama->title }}</h3>
                    @if($panorama->building)
                        <p class="text-gray-500 text-sm">
                            <i class="fas fa-building mr-1"></i>{{ $panorama->building->title }}
                        </p>
                    @endif
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Buildings Section -->
@if($buildings->count() > 0)
<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-800 mb-4">Kampus Binolari</h2>
            <p class="text-gray-600">Asosiy binolar va ularning joylashuvi</p>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($buildings->take(8) as $building)
                <div class="bg-white rounded-xl p-6 shadow-sm hover:shadow-lg transition">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 rounded-lg flex items-center justify-center mr-4" style="background-color: {{ $building->color }}20;">
                            <i class="fas {{ $building->marker_icon }}" style="color: {{ $building->color }}"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-800">{{ $building->title }}</h3>
                            <span class="text-sm text-gray-500">{{ $building->category_label }}</span>
                        </div>
                    </div>
                    @if($building->description)
                        <p class="text-gray-600 text-sm">{{ Str::limit($building->description, 80) }}</p>
                    @endif
                    @if($building->panoramas_count > 0)
                        <div class="mt-4 pt-4 border-t">
                            <a href="{{ route('campus-tour.public.virtual-tour', ['building' => $building->id]) }}" class="text-blue-600 text-sm font-medium">
                                <i class="fas fa-vr-cardboard mr-1"></i>
                                {{ $building->panoramas_count }} ta panorama
                            </a>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        @if($buildings->count() > 8)
            <div class="text-center mt-8">
                <a href="{{ route('campus-tour.public.map') }}" class="inline-flex items-center bg-blue-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-blue-700 transition">
                    <i class="fas fa-map-marked-alt mr-2"></i>
                    Xaritada ko'rish
                </a>
            </div>
        @endif
    </div>
</section>
@endif

<!-- CTA Section -->
<section class="py-16 bg-gradient-to-r from-blue-600 to-blue-800 text-white">
    <div class="max-w-4xl mx-auto px-4 text-center">
        <i class="fas fa-university text-5xl mb-6 text-blue-200"></i>
        <h2 class="text-3xl font-bold mb-4">Bizga tashrif buyuring!</h2>
        <p class="text-xl text-blue-100 mb-8">
            Virtual turdan so'ng kampusni shaxsan ko'rishni xohlaysizmi?
            Biz sizni kutamiz!
        </p>
        <div class="flex flex-wrap justify-center gap-4">
            <a href="{{ route('campus-tour.public.directions') }}" class="inline-flex items-center bg-white text-blue-600 px-6 py-3 rounded-lg font-medium hover:bg-blue-50 transition">
                <i class="fas fa-directions mr-2"></i>
                Yo'l-yo'riq olish
            </a>
            <a href="{{ route('frontend.contacts') }}" class="inline-flex items-center border border-white text-white px-6 py-3 rounded-lg font-medium hover:bg-white/10 transition">
                <i class="fas fa-phone-alt mr-2"></i>
                Bog'lanish
            </a>
        </div>
    </div>
</section>
@endsection
