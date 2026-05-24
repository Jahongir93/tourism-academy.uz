@extends(\App\Helpers\TemplateHelper::getLayout())

@section('title', 'O\'zbekiston Respublikasi Davlat Ramzlari')

@push('styles')
<style>
    .symbol-hero {
        background: linear-gradient(135deg, #0099B5 0%, #1EB53A 100%);
        padding: 140px 0 60px;
        margin-top: -80px;
        position: relative;
        overflow: hidden;
    }
    .symbol-hero::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    }
    .symbol-card {
        background: white;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        margin-bottom: 40px;
    }
    .symbol-header {
        padding: 30px;
        text-align: center;
    }
    .flag-visual {
        max-width: 400px;
        margin: 0 auto;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0,0,0,0.2);
    }
    .flag-stripe {
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .flag-stripe.blue { background: #0099B5; }
    .flag-stripe.white { background: #FFFFFF; }
    .flag-stripe.green { background: #1EB53A; }
    .flag-stripe.red-line { height: 4px; background: #CE1126; }
    .emblem-image {
        width: 200px;
        height: 200px;
        margin: 0 auto;
        background: linear-gradient(135deg, #f8f9fa, #e9ecef);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .color-swatch {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        flex-shrink: 0;
    }
    .anthem-verse {
        background: linear-gradient(135deg, #f8fafc, #f1f5f9);
        border-radius: 16px;
        padding: 24px;
        margin-bottom: 20px;
        border-left: 4px solid #0099B5;
    }
    .anthem-chorus {
        background: linear-gradient(135deg, #ecfdf5, #d1fae5);
        border-radius: 16px;
        padding: 24px;
        margin-bottom: 20px;
        border-left: 4px solid #1EB53A;
    }
    .anthem-text {
        font-size: 1.1rem;
        line-height: 2;
        font-style: italic;
        color: #374151;
        white-space: pre-line;
    }
    .element-item {
        display: flex;
        align-items: flex-start;
        gap: 16px;
        padding: 16px;
        background: #f8fafc;
        border-radius: 12px;
        margin-bottom: 12px;
    }
    .element-icon {
        width: 48px;
        height: 48px;
        background: linear-gradient(135deg, #0099B5, #1EB53A);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        flex-shrink: 0;
    }
    .section-title {
        font-size: 1.75rem;
        font-weight: 700;
        color: #1f2937;
        position: relative;
        display: inline-block;
        padding-bottom: 12px;
    }
    .section-title::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 60px;
        height: 4px;
        background: linear-gradient(90deg, #0099B5, #1EB53A);
        border-radius: 2px;
    }
    .info-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        background: rgba(0, 153, 181, 0.1);
        color: #0099B5;
        border-radius: 50px;
        font-size: 14px;
        font-weight: 500;
    }
    .symbol-section {
        padding: 60px 0;
    }
</style>
@endpush

@section('content')
<!-- Hero Section -->
<section class="symbol-hero">
    <div class="container position-relative" style="z-index: 10;">
        <nav class="mb-4" style="font-size: 14px;">
            <a href="{{ route('home') }}" style="color: rgba(255,255,255,0.7); text-decoration: none;">Bosh sahifa</a>
            <span style="color: rgba(255,255,255,0.5); margin: 0 8px;">/</span>
            <span style="color: white;">Davlat ramzlari</span>
        </nav>
        <div class="text-center">
            <h1 style="font-size: 2.5rem; font-weight: 700; color: white; margin-bottom: 16px;">
                O'zbekiston Respublikasi
            </h1>
            <p style="font-size: 1.25rem; color: rgba(255,255,255,0.9);">Davlat Ramzlari</p>
        </div>
    </div>
</section>

<!-- Main Content -->
<section class="symbol-section" style="background: #f8fafc;">
    <div class="container">

        <!-- Flag Section -->
        <div class="mb-5">
            <div class="text-center mb-4">
                <h2 class="section-title">Davlat Bayrog'i</h2>
            </div>

            <div class="symbol-card">
                <div class="symbol-header" style="background: linear-gradient(135deg, #e0f2fe, #dcfce7);">
                    <div class="info-badge mb-3">
                        <i class="fas fa-calendar-alt"></i>
                        Qabul qilingan: {{ $flag['adopted'] }}
                    </div>

                    <!-- Flag Visual -->
                    @if(!empty($flag['image']))
                        <div class="flag-visual">
                            <img src="{{ str_starts_with($flag['image'], 'http') ? $flag['image'] : asset($flag['image']) }}"
                                 alt="O'zbekiston Davlat Bayrog'i" style="width:100%; display:block; border-radius:8px;">
                        </div>
                    @else
                        <div class="flag-visual">
                            <div class="flag-stripe blue" style="height: 60px; position: relative;">
                                <div style="position: absolute; left: 20px; top: 50%; transform: translateY(-50%); display: flex; align-items: center;">
                                    <div style="width: 30px; height: 30px; border-radius: 50%; background: white; box-shadow: 8px 0 0 0 #0099B5;"></div>
                                    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 4px; margin-left: 10px;">
                                        @for($i = 0; $i < 12; $i++)
                                            <div style="color: white; font-size: 10px;">&#9733;</div>
                                        @endfor
                                    </div>
                                </div>
                            </div>
                            <div class="flag-stripe red-line"></div>
                            <div class="flag-stripe white" style="height: 60px;"></div>
                            <div class="flag-stripe red-line"></div>
                            <div class="flag-stripe green" style="height: 60px;"></div>
                        </div>
                    @endif
                </div>

                <div class="p-4">
                    <h3 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 20px;">Ranglar ma'nosi</h3>
                    <div class="row g-3">
                        @foreach($flag['colors'] as $color)
                        <div class="col-md-6">
                            <div class="element-item">
                                <div class="color-swatch" style="background: {{ $color['hex'] }}; {{ $color['hex'] === '#FFFFFF' ? 'border: 2px solid #e5e7eb;' : '' }}"></div>
                                <div>
                                    <h4 style="font-weight: 600; color: #1f2937; margin-bottom: 4px;">{{ $color['name'] }}</h4>
                                    <p style="color: #6b7280; font-size: 14px; margin: 0;">{{ $color['meaning'] }}</p>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <h3 style="font-size: 1.25rem; font-weight: 600; margin: 30px 0 20px;">Elementlar</h3>
                    <div class="row g-3">
                        @foreach($flag['elements'] as $element)
                        <div class="col-md-6">
                            <div class="element-item">
                                <div class="element-icon">
                                    <i class="fas fa-star"></i>
                                </div>
                                <div class="d-flex align-items-center">
                                    <p style="color: #374151; margin: 0;">{{ $element }}</p>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Emblem Section -->
        <div class="mb-5">
            <div class="text-center mb-4">
                <h2 class="section-title">Davlat Gerbi</h2>
            </div>

            <div class="symbol-card">
                <div class="symbol-header" style="background: linear-gradient(135deg, #fef3c7, #fef9c3);">
                    <div class="info-badge mb-3">
                        <i class="fas fa-calendar-alt"></i>
                        Qabul qilingan: {{ $emblem['adopted'] }}
                    </div>

                    <div class="emblem-image">
                        @if(!empty($emblem['image']))
                            <img src="{{ str_starts_with($emblem['image'], 'http') ? $emblem['image'] : asset($emblem['image']) }}"
                                 alt="O'zbekiston Gerbi" style="max-width: 180px; max-height: 180px; object-fit: contain;">
                        @else
                            <img src="{{ asset('images/gerb.png') }}" alt="O'zbekiston Gerbi"
                                 style="max-width: 180px; max-height: 180px; object-fit: contain;"
                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                            <span style="font-size: 80px; display: none;">🇺🇿</span>
                        @endif
                    </div>
                </div>

                <div class="p-4">
                    <h3 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 20px;">Gerb elementlari</h3>
                    <div class="row g-3">
                        @foreach($emblem['elements'] as $element)
                        <div class="col-md-6">
                            <div class="element-item">
                                <div class="element-icon">
                                    @if(str_contains($element['name'], 'Humo'))
                                        <i class="fas fa-dove"></i>
                                    @elseif(str_contains($element['name'], 'Quyosh'))
                                        <i class="fas fa-sun"></i>
                                    @elseif(str_contains($element['name'], 'Daryo'))
                                        <i class="fas fa-water"></i>
                                    @elseif(str_contains($element['name'], 'Bug\'doy'))
                                        <i class="fas fa-seedling"></i>
                                    @elseif(str_contains($element['name'], 'Paxta'))
                                        <i class="fas fa-cloud"></i>
                                    @else
                                        <i class="fas fa-star"></i>
                                    @endif
                                </div>
                                <div>
                                    <h4 style="font-weight: 600; color: #1f2937; margin-bottom: 4px;">{{ $element['name'] }}</h4>
                                    <p style="color: #6b7280; font-size: 14px; margin: 0;">{{ $element['meaning'] }}</p>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Anthem Section -->
        <div>
            <div class="text-center mb-4">
                <h2 class="section-title">Davlat Madhiyasi</h2>
            </div>

            <div class="symbol-card">
                <div class="symbol-header" style="background: linear-gradient(135deg, #e0e7ff, #ede9fe);">
                    <div class="d-flex flex-wrap justify-content-center gap-3 mb-3">
                        <div class="info-badge">
                            <i class="fas fa-calendar-alt"></i>
                            {{ $anthem['adopted'] }}
                        </div>
                        <div class="info-badge">
                            <i class="fas fa-pen-fancy"></i>
                            So'zi: {{ $anthem['author_lyrics'] }}
                        </div>
                        <div class="info-badge">
                            <i class="fas fa-music"></i>
                            Musiqasi: {{ $anthem['author_music'] }}
                        </div>
                    </div>
                    <h3 style="font-size: 1.5rem; font-weight: 700; color: #1f2937;">{{ $anthem['title'] }}</h3>
                </div>

                <div class="p-4">
                    @foreach($anthem['verses'] as $index => $section)
                    <div class="mb-5">
                        <h4 style="font-size: 1.1rem; font-weight: 600; color: #374151; margin-bottom: 16px;">
                            <i class="fas fa-music me-2" style="color: #3b82f6;"></i>
                            {{ $index + 1 }}-band
                        </h4>

                        <div class="anthem-verse">
                            <p class="anthem-text">{{ $section['verse'] }}</p>
                        </div>

                        <h4 style="font-size: 1.1rem; font-weight: 600; color: #374151; margin-bottom: 16px;">
                            <i class="fas fa-redo me-2" style="color: #22c55e;"></i>
                            Naqorat
                        </h4>

                        <div class="anthem-chorus">
                            <p class="anthem-text">{{ $section['chorus'] }}</p>
                        </div>
                    </div>
                    @endforeach

                    <!-- Audio Player -->
                    @if(!empty($anthem['audio']))
                        <div class="p-4 rounded-3" style="background: linear-gradient(135deg, #eff6ff, #ecfdf5);">
                            <h4 style="font-size: 1.1rem; font-weight: 600; color: #1f2937; margin-bottom: 12px; text-align:center;">
                                <i class="fas fa-music me-2" style="color:#3b82f6;"></i>Madhiyani tinglang
                            </h4>
                            <audio controls class="w-100" style="border-radius:8px;">
                                <source src="{{ str_starts_with($anthem['audio'], 'http') ? $anthem['audio'] : asset($anthem['audio']) }}">
                                Brauzeringiz audio fayllarni qo'llab-quvvatlamaydi.
                            </audio>
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>
</section>

<!-- Additional Info -->
@if(!empty($footer['title']) || !empty($footer['text']))
<section class="py-5" style="background: white;">
    <div class="container text-center">
        @if(!empty($footer['title']))
            <h3 style="font-size: 1.5rem; font-weight: 700; color: #1f2937; margin-bottom: 16px;">{{ $footer['title'] }}</h3>
        @endif
        @if(!empty($footer['text']))
            <p style="color: #6b7280; line-height: 1.8; max-width: 700px; margin: 0 auto;">{{ $footer['text'] }}</p>
        @endif
    </div>
</section>
@endif
@endsection
