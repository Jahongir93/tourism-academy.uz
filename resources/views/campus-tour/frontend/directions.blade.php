@extends('layouts.frontend')

@section('title', 'Transport Yo\'nalishlari - Tourism Academy')

@push('styles')
<style>
    .route-card {
        background: white;
        border-radius: 16px;
        overflow: hidden;
        transition: all 0.3s ease;
        border: 1px solid #f0f0f0;
    }
    .route-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 30px rgba(0,0,0,0.1);
    }
    .route-header {
        padding: 20px;
        position: relative;
    }
    .route-type-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        border-radius: 50px;
        font-size: 13px;
        font-weight: 500;
        color: white;
    }
    .route-points {
        display: flex;
        align-items: center;
        gap: 12px;
        margin: 16px 0;
    }
    .route-point {
        flex: 1;
        padding: 12px;
        background: #f8f9fa;
        border-radius: 10px;
    }
    .route-point-label {
        font-size: 11px;
        text-transform: uppercase;
        color: #888;
        margin-bottom: 4px;
    }
    .route-point-value {
        font-weight: 600;
        color: #333;
    }
    .route-arrow {
        color: #ccc;
        font-size: 18px;
    }
    .route-meta {
        display: flex;
        gap: 20px;
        padding: 16px 20px;
        background: #f8f9fa;
        border-top: 1px solid #f0f0f0;
    }
    .route-meta-item {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
        color: #666;
    }
    .route-meta-item i {
        width: 16px;
        text-align: center;
    }
    .route-price {
        font-weight: 600;
        color: #22c55e;
    }
    .route-price.free {
        color: #0066CC;
    }
    .route-expand {
        padding: 0 20px 20px;
        display: none;
    }
    .route-expand.active {
        display: block;
    }
    .directions-list {
        padding: 16px;
        background: #f8f9fa;
        border-radius: 12px;
    }
    .direction-step {
        display: flex;
        gap: 12px;
        padding: 12px 0;
        border-bottom: 1px dashed #e5e7eb;
    }
    .direction-step:last-child {
        border-bottom: none;
    }
    .step-number {
        width: 28px;
        height: 28px;
        background: #0066CC;
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        font-weight: 600;
        flex-shrink: 0;
    }
    .map-embed {
        width: 100%;
        height: 300px;
        border-radius: 12px;
        overflow: hidden;
        margin-top: 16px;
    }
    .map-embed iframe {
        width: 100%;
        height: 100%;
        border: none;
    }
    .filter-tabs {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        padding: 16px 0;
    }
    .filter-tab {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 10px 20px;
        border-radius: 50px;
        font-size: 14px;
        font-weight: 500;
        border: 1px solid #e5e7eb;
        background: white;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .filter-tab:hover {
        border-color: #0066CC;
        color: #0066CC;
    }
    .filter-tab.active {
        background: #0066CC;
        border-color: #0066CC;
        color: white;
    }
    .filter-tab.active i {
        color: white;
    }
</style>
@endpush

@section('content')
<!-- Hero -->
<section class="bg-gradient-to-r from-orange-500 to-amber-600 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="text-orange-100 text-sm mb-4">
            <a href="{{ route('home') }}" class="hover:text-white">Bosh sahifa</a>
            <span class="mx-2">/</span>
            <a href="{{ route('campus-tour.public.index') }}" class="hover:text-white">Kampus Turi</a>
            <span class="mx-2">/</span>
            <span class="text-white">Yo'nalishlar</span>
        </nav>
        <h1 class="text-3xl md:text-4xl font-bold text-white mb-4">
            <i class="fas fa-route mr-3"></i>Transport Yo'nalishlari
        </h1>
        <p class="text-orange-100 text-lg max-w-2xl">
            Tourism Academy kampusiga turli transport vositalari orqali qanday yetib kelish mumkinligini bilib oling.
        </p>
    </div>
</section>

<!-- Filters -->
<section class="bg-white border-b sticky top-16 z-40">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="filter-tabs">
            <a href="{{ route('campus-tour.public.directions') }}"
               class="filter-tab {{ !request('type') ? 'active' : '' }}">
                Barchasi
            </a>
            @foreach($types as $key => $type)
                <a href="{{ route('campus-tour.public.directions', ['type' => $key]) }}"
                   class="filter-tab {{ request('type') === $key ? 'active' : '' }}">
                    <i class="fas {{ $type['icon'] }}" style="color: {{ request('type') === $key ? 'white' : $type['color'] }}"></i>
                    {{ $type['label'] }}
                </a>
            @endforeach
        </div>
    </div>
</section>

<!-- Routes List -->
<section class="py-12 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @if($routes->count() > 0)
            <div class="grid gap-6">
                @foreach($routes as $route)
                    <div class="route-card" data-route-id="{{ $route->id }}">
                        <div class="route-header">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <span class="route-type-badge" style="background-color: {{ $route->type_color }}">
                                        <i class="fas {{ $route->type_icon }}"></i>
                                        {{ $route->type_label }}
                                    </span>
                                </div>
                                @if($route->is_active)
                                    <span class="text-green-500 text-sm font-medium">
                                        <i class="fas fa-check-circle mr-1"></i>Faol
                                    </span>
                                @endif
                            </div>

                            <h3 class="text-xl font-bold text-gray-800 mb-2">{{ $route->title }}</h3>

                            @if($route->description)
                                <p class="text-gray-600 mb-4">{{ $route->description }}</p>
                            @endif

                            <div class="route-points">
                                <div class="route-point">
                                    <div class="route-point-label">Boshlang'ich nuqta</div>
                                    <div class="route-point-value">{{ $route->start_point }}</div>
                                </div>
                                <i class="fas fa-arrow-right route-arrow"></i>
                                <div class="route-point">
                                    <div class="route-point-label">Oxirgi nuqta</div>
                                    <div class="route-point-value">{{ $route->end_point }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="route-meta">
                            @if($route->duration)
                                <div class="route-meta-item">
                                    <i class="fas fa-clock text-blue-500"></i>
                                    <span>{{ $route->duration }}</span>
                                </div>
                            @endif
                            @if($route->distance)
                                <div class="route-meta-item">
                                    <i class="fas fa-road text-orange-500"></i>
                                    <span>{{ $route->distance }} km</span>
                                </div>
                            @endif
                            <div class="route-meta-item">
                                <i class="fas fa-wallet text-green-500"></i>
                                <span class="route-price {{ $route->price == 0 ? 'free' : '' }}">
                                    {{ $route->formatted_price }}
                                </span>
                            </div>
                            @if($route->directions || $route->map_embed_url)
                                <button class="route-meta-item ml-auto text-blue-600 hover:text-blue-700 expand-btn">
                                    <i class="fas fa-chevron-down"></i>
                                    <span>Batafsil</span>
                                </button>
                            @endif
                        </div>

                        @if($route->directions || $route->map_embed_url)
                            <div class="route-expand">
                                @if($route->directions)
                                    <div class="directions-list">
                                        <h4 class="font-semibold text-gray-800 mb-3">
                                            <i class="fas fa-list-ol mr-2 text-blue-500"></i>Yo'l-yo'riq
                                        </h4>
                                        @php
                                            $steps = preg_split('/\r\n|\r|\n/', $route->directions);
                                        @endphp
                                        @foreach($steps as $index => $step)
                                            @if(trim($step))
                                                <div class="direction-step">
                                                    <div class="step-number">{{ $index + 1 }}</div>
                                                    <div class="text-gray-700">{{ trim(preg_replace('/^\d+[.)]\s*/', '', $step)) }}</div>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                @endif

                                @if($route->map_embed_url)
                                    <div class="map-embed">
                                        <iframe src="{{ $route->map_embed_url }}" allowfullscreen loading="lazy"></iframe>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            <div class="mt-10">
                {{ $routes->links() }}
            </div>
        @else
            <div class="text-center py-16">
                <i class="fas fa-route text-6xl text-gray-300 mb-6"></i>
                <h3 class="text-xl font-semibold text-gray-600 mb-2">Yo'nalishlar topilmadi</h3>
                <p class="text-gray-500 mb-6">
                    @if(request('type'))
                        Bu turdagi yo'nalishlar hali qo'shilmagan
                    @else
                        Hali yo'nalishlar qo'shilmagan
                    @endif
                </p>
                <a href="{{ route('campus-tour.public.index') }}" class="inline-flex items-center text-blue-600 font-medium hover:text-blue-700">
                    <i class="fas fa-arrow-left mr-2"></i>Orqaga qaytish
                </a>
            </div>
        @endif
    </div>
</section>

<!-- Quick Info -->
<section class="py-12 bg-white">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-2xl font-bold text-gray-800 text-center mb-10">Foydali Ma'lumotlar</h2>

        <div class="grid md:grid-cols-3 gap-8">
            <div class="text-center">
                <div class="w-16 h-16 bg-blue-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-map-marked-alt text-blue-600 text-2xl"></i>
                </div>
                <h3 class="font-semibold text-gray-800 mb-2">Manzil</h3>
                <p class="text-gray-600 text-sm">Samarqand sh., Universitet xiyoboni 15</p>
            </div>
            <div class="text-center">
                <div class="w-16 h-16 bg-green-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-clock text-green-600 text-2xl"></i>
                </div>
                <h3 class="font-semibold text-gray-800 mb-2">Ish vaqti</h3>
                <p class="text-gray-600 text-sm">Dushanba - Shanba: 09:00 - 18:00</p>
            </div>
            <div class="text-center">
                <div class="w-16 h-16 bg-orange-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-phone-alt text-orange-600 text-2xl"></i>
                </div>
                <h3 class="font-semibold text-gray-800 mb-2">Bog'lanish</h3>
                <p class="text-gray-600 text-sm">
                    <a href="tel:+998901234567" class="text-blue-600 hover:text-blue-700">+998 90 123-45-67</a>
                </p>
            </div>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="py-12 bg-gradient-to-r from-blue-600 to-blue-800">
    <div class="max-w-4xl mx-auto px-4 text-center text-white">
        <h2 class="text-2xl font-bold mb-4">Yo'l topa olmadingizmi?</h2>
        <p class="text-blue-100 mb-6">Bizga qo'ng'iroq qiling, biz sizga yordam beramiz!</p>
        <a href="tel:+998901234567" class="inline-flex items-center bg-white text-blue-600 px-6 py-3 rounded-lg font-semibold hover:bg-blue-50 transition">
            <i class="fas fa-phone-alt mr-2"></i>+998 90 123-45-67
        </a>
    </div>
</section>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Expand/collapse functionality
    document.querySelectorAll('.expand-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const card = this.closest('.route-card');
            const expandSection = card.querySelector('.route-expand');
            const icon = this.querySelector('i');

            if (expandSection.classList.contains('active')) {
                expandSection.classList.remove('active');
                icon.classList.remove('fa-chevron-up');
                icon.classList.add('fa-chevron-down');
            } else {
                expandSection.classList.add('active');
                icon.classList.remove('fa-chevron-down');
                icon.classList.add('fa-chevron-up');
            }
        });
    });
});
</script>
@endpush
