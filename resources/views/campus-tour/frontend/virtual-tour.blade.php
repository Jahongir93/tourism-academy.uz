@extends('layouts.public')

@section('title', '360° Virtual Tur - Tourism Academy')

@push('styles')
<style>
    .vt-hero {
        background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
        padding: 140px 0 60px;
        margin-top: -80px;
    }
    .panorama-card {
        transition: all 0.3s ease;
        overflow: hidden;
        border-radius: 16px;
        background: white;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    }
    .panorama-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.12);
    }
    .panorama-thumbnail {
        height: 220px;
        background-size: cover;
        background-position: center;
        position: relative;
    }
    .panorama-thumbnail::after {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(to top, rgba(0,0,0,0.6) 0%, transparent 60%);
    }
    .panorama-thumbnail .play-icon {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 70px;
        height: 70px;
        background: rgba(255,255,255,0.95);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10;
        transition: all 0.3s ease;
        box-shadow: 0 4px 20px rgba(0,0,0,0.2);
    }
    .panorama-card:hover .play-icon {
        transform: translate(-50%, -50%) scale(1.1);
    }
    .panorama-thumbnail .badges {
        position: absolute;
        top: 12px;
        right: 12px;
        z-index: 10;
        display: flex;
        gap: 8px;
    }
    .filter-btn {
        padding: 8px 20px;
        border-radius: 50px;
        border: 1px solid #e5e7eb;
        background: white;
        font-size: 14px;
        font-weight: 500;
        transition: all 0.2s ease;
        text-decoration: none;
        color: #374151;
        display: inline-block;
    }
    .filter-btn:hover {
        border-color: #3b82f6;
        color: #3b82f6;
    }
    .filter-btn.active {
        background: #3b82f6;
        border-color: #3b82f6;
        color: white;
    }
    .vt-section {
        padding: 60px 0;
    }
    .step-card {
        text-align: center;
        padding: 30px 20px;
    }
    .step-number {
        width: 60px;
        height: 60px;
        background: #eff6ff;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        font-size: 24px;
        font-weight: 700;
        color: #3b82f6;
    }
</style>
@endpush

@section('content')
<!-- Hero -->
<section class="vt-hero">
    <div class="container">
        <nav class="mb-3" style="font-size: 14px;">
            <a href="{{ route('home') }}" style="color: rgba(255,255,255,0.7); text-decoration: none;">Bosh sahifa</a>
            <span style="color: rgba(255,255,255,0.5); margin: 0 8px;">/</span>
            <a href="{{ route('campus-tour.public.index') }}" style="color: rgba(255,255,255,0.7); text-decoration: none;">Kampus Turi</a>
            <span style="color: rgba(255,255,255,0.5); margin: 0 8px;">/</span>
            <span style="color: white;">Virtual Tur</span>
        </nav>
        <h1 style="font-size: 2.5rem; font-weight: 700; color: white; margin-bottom: 16px;">
            <i class="fas fa-vr-cardboard me-3"></i>360° Virtual Tur
        </h1>
        <p style="color: rgba(255,255,255,0.9); font-size: 1.1rem; max-width: 600px;">
            Akademiya binolari ichini 360 daraja formatda ko'ring. Panoramalarni tanlang va virtual sayohatga chiqing.
        </p>
    </div>
</section>

<!-- Filters -->
<section style="background: white; border-bottom: 1px solid #e5e7eb; padding: 16px 0; position: sticky; top: 80px; z-index: 40;">
    <div class="container">
        <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 12px;">
            <span style="color: #6b7280; font-weight: 500; margin-right: 8px;">Filter:</span>
            <a href="{{ route('campus-tour.public.virtual-tour') }}"
               class="filter-btn {{ !request('building') ? 'active' : '' }}">
                Barchasi
            </a>
            @foreach($buildings as $building)
                <a href="{{ route('campus-tour.public.virtual-tour', ['building' => $building->id]) }}"
                   class="filter-btn {{ request('building') == $building->id ? 'active' : '' }}">
                    <i class="fas {{ $building->marker_icon }} me-1" style="color: {{ request('building') == $building->id ? 'white' : $building->color }}"></i>
                    {{ $building->title }}
                </a>
            @endforeach
        </div>
    </div>
</section>

<!-- Panoramas Grid -->
<section class="vt-section" style="background: #f8fafc;">
    <div class="container">
        @if($panoramas->count() > 0)
            <div class="row g-4">
                @foreach($panoramas as $panorama)
                    <div class="col-md-6 col-lg-4">
                        <a href="{{ route('campus-tour.public.panorama', $panorama) }}" class="panorama-card d-block text-decoration-none">
                            <div class="panorama-thumbnail" style="background-image: url('{{ $panorama->thumbnail_url }}')">
                                <div class="play-icon">
                                    <i class="fas fa-play" style="color: #3b82f6; font-size: 20px; margin-left: 4px;"></i>
                                </div>
                                <div class="badges">
                                    @if($panorama->is_featured)
                                        <span style="background: #eab308; color: white; font-size: 12px; padding: 4px 12px; border-radius: 50px; font-weight: 500;">
                                            <i class="fas fa-star me-1"></i>Tanlangan
                                        </span>
                                    @endif
                                </div>
                                <div style="position: absolute; bottom: 0; left: 0; right: 0; padding: 16px; z-index: 10;">
                                    <div style="display: flex; align-items: center; color: rgba(255,255,255,0.8); font-size: 14px;">
                                        <i class="fas fa-eye me-2"></i>
                                        <span>360° ko'rinish</span>
                                    </div>
                                </div>
                            </div>
                            <div style="padding: 20px;">
                                <h3 style="font-size: 18px; font-weight: 600; color: #1f2937; margin-bottom: 8px;">{{ $panorama->title }}</h3>
                                @if($panorama->building)
                                    <div style="display: flex; align-items: center; color: #6b7280; font-size: 14px; margin-bottom: 12px;">
                                        <i class="fas {{ $panorama->building->marker_icon }} me-2" style="color: {{ $panorama->building->color }}"></i>
                                        {{ $panorama->building->title }}
                                    </div>
                                @endif
                                @if($panorama->description)
                                    <p style="color: #6b7280; font-size: 14px; margin: 0; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">{{ $panorama->description }}</p>
                                @endif
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>

            <div class="mt-5">
                {{ $panoramas->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-vr-cardboard" style="font-size: 64px; color: #d1d5db; margin-bottom: 24px;"></i>
                <h3 style="font-size: 20px; font-weight: 600; color: #6b7280; margin-bottom: 8px;">Panoramalar topilmadi</h3>
                <p style="color: #9ca3af; margin-bottom: 24px;">
                    @if(request('building'))
                        Bu bino uchun hali panoramalar qo'shilmagan
                    @else
                        Hali panoramalar qo'shilmagan
                    @endif
                </p>
                <a href="{{ route('campus-tour.public.index') }}" style="display: inline-flex; align-items: center; color: #3b82f6; font-weight: 500; text-decoration: none;">
                    <i class="fas fa-arrow-left me-2"></i>Orqaga qaytish
                </a>
            </div>
        @endif
    </div>
</section>

<!-- How It Works -->
<section class="vt-section" style="background: white;">
    <div class="container">
        <h2 style="font-size: 1.75rem; font-weight: 700; color: #1f2937; text-align: center; margin-bottom: 48px;">Qanday ishlaydi?</h2>

        <div class="row">
            <div class="col-md-4">
                <div class="step-card">
                    <div class="step-number">1</div>
                    <h3 style="font-weight: 600; color: #1f2937; margin-bottom: 8px;">Panoramani tanlang</h3>
                    <p style="color: #6b7280; font-size: 14px; margin: 0;">Ko'rmoqchi bo'lgan joyni tanlang</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="step-card">
                    <div class="step-number">2</div>
                    <h3 style="font-weight: 600; color: #1f2937; margin-bottom: 8px;">360° ko'ring</h3>
                    <p style="color: #6b7280; font-size: 14px; margin: 0;">Sichqoncha yoki barmog'ingiz bilan aylanib ko'ring</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="step-card">
                    <div class="step-number">3</div>
                    <h3 style="font-weight: 600; color: #1f2937; margin-bottom: 8px;">O'tishlar</h3>
                    <p style="color: #6b7280; font-size: 14px; margin: 0;">Hotspotlar orqali boshqa joylarga o'ting</p>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
