@extends('layouts.dashboard-new')

@section('title', 'Bosh sahifa tahrirlash - CMS')
@section('page-title', 'Bosh sahifa tahrirlash')

@section('styles')
<style>
    .content-card {
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        background: #fff;
        transition: all 0.3s;
        margin-bottom: 24px;
    }
    .content-card:hover {
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    }
    .section-header {
        background: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%);
        color: white;
        padding: 16px 20px;
        border-radius: 12px 12px 0 0;
        margin: -1px -1px 0 -1px;
    }
    .section-header.hero { background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); }
    .section-header.stats { background: linear-gradient(135deg, #065f46 0%, #047857 100%); }
    .section-header.quick { background: linear-gradient(135deg, #4338ca 0%, #4f46e5 100%); }
    .section-header.news { background: linear-gradient(135deg, #b45309 0%, #d97706 100%); }
    .section-header.events { background: linear-gradient(135deg, #9333ea 0%, #7c3aed 100%); }
    .section-header.academy { background: linear-gradient(135deg, #1e40af 0%, #1d4ed8 100%); }
    .section-header.faq { background: linear-gradient(135deg, #be185d 0%, #db2777 100%); }
    .section-header.partners { background: linear-gradient(135deg, #059669 0%, #10b981 100%); }
    .section-header.buttons { background: linear-gradient(135deg, #475569 0%, #64748b 100%); }
    .image-preview {
        max-height: 120px;
        border-radius: 8px;
        object-fit: cover;
    }

    /* Unified image upload component */
    .image-upload-wrapper {
        position: relative;
    }
    .image-preview-box {
        position: relative;
        border: 2px dashed #cbd5e1;
        border-radius: 10px;
        overflow: hidden;
        background: #f8fafc;
        margin-bottom: 8px;
        min-height: 110px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: border-color 0.2s, background 0.2s;
        cursor: pointer;
    }
    .image-preview-box:hover {
        border-color: #4f46e5;
        background: #f1f5ff;
    }
    .image-preview-box img {
        width: 100%;
        height: 100%;
        max-height: 180px;
        object-fit: cover;
        display: block;
    }
    .image-preview-box.partner-style { min-height: 70px; }
    .image-preview-box.partner-style img { max-height: 70px; object-fit: contain; padding: 6px; }
    .image-preview-box.slide-style { min-height: 140px; }
    .image-placeholder {
        color: #94a3b8;
        text-align: center;
        padding: 20px;
        font-size: 0.85rem;
    }
    .image-placeholder i {
        font-size: 1.8rem;
        margin-bottom: 6px;
        display: block;
    }
    .image-preview-box.has-new::after {
        content: 'Yangi rasm';
        position: absolute;
        top: 8px;
        right: 8px;
        background: #10b981;
        color: white;
        padding: 3px 10px;
        border-radius: 50px;
        font-size: 11px;
        font-weight: 600;
        box-shadow: 0 2px 8px rgba(16,185,129,0.4);
    }

    /* Per-input mini loader (overlay on preview while reading file) */
    .image-preview-box.loading::before {
        content: '';
        position: absolute;
        inset: 0;
        background: rgba(255,255,255,0.7);
        z-index: 1;
    }
    .image-preview-box.loading::after {
        content: '';
        position: absolute;
        top: 50%; left: 50%;
        transform: translate(-50%, -50%);
        width: 32px; height: 32px;
        border: 3px solid #cbd5e1;
        border-top-color: #4f46e5;
        border-radius: 50%;
        z-index: 2;
        animation: spin 0.7s linear infinite;
    }

    /* Full-screen submit loader */
    .upload-loader-overlay {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.75);
        backdrop-filter: blur(6px);
        -webkit-backdrop-filter: blur(6px);
        z-index: 99999;
        display: none;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        gap: 20px;
        color: #fff;
    }
    .upload-loader-overlay.active { display: flex; }
    .upload-loader-spinner {
        width: 72px;
        height: 72px;
        border: 6px solid rgba(255,255,255,0.18);
        border-top-color: #4f46e5;
        border-right-color: #7c3aed;
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
    }
    .upload-loader-text {
        font-size: 1.05rem;
        font-weight: 600;
        text-align: center;
    }
    .upload-loader-progress {
        font-size: 0.85rem;
        opacity: 0.75;
    }
    @keyframes spin { to { transform: rotate(360deg); } }
    .nav-pills .nav-link.active {
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
    }
    .info-box-card {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 16px;
    }
    .slide-card {
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
        background: #fafafa;
    }
    .slide-card:hover {
        border-color: #4f46e5;
        background: #fff;
    }
    .slide-number {
        width: 36px;
        height: 36px;
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
    }
    .lang-badge {
        display: inline-block;
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 600;
    }
    .lang-badge.uz { background: #dbeafe; color: #1e40af; }
    .lang-badge.en { background: #dcfce7; color: #166534; }
    .lang-badge.ru { background: #fce7f3; color: #9d174d; }
</style>
@endsection

@section('content')
@php
    $activeSection = request()->get('section', 'hero');
    $sectionTitles = [
        'hero' => ['title' => 'Hero Slider tahrirlash', 'icon' => 'fas fa-images', 'desc' => 'Bosh sahifa slayder rasmlar va matnlarini tahrirlang'],
        'stats' => ['title' => 'Statistika tahrirlash', 'icon' => 'fas fa-chart-bar', 'desc' => 'Statistika raqamlari va yorliqlarini tahrirlang'],
        'quick' => ['title' => 'Tezkor havolalar', 'icon' => 'fas fa-bolt', 'desc' => 'Tezkor havolalar matnlarini tahrirlang'],
        'all' => ['title' => 'Bosh sahifa tahrirlash', 'icon' => 'fas fa-home', 'desc' => 'Sayt bosh sahifasini uchta tilda tahrirlang'],
    ];
    $currentSection = $sectionTitles[$activeSection] ?? $sectionTitles['all'];
@endphp

    {{-- Header --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <h1 class="h3 mb-1 fw-bold">
                        <i class="{{ $currentSection['icon'] }} text-primary me-2"></i>{{ $currentSection['title'] }}
                    </h1>
                    <p class="text-muted mb-0">{{ $currentSection['desc'] }}</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('cms.dashboard') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Orqaga
                    </a>
                    <a href="{{ route('home') }}" target="_blank" class="btn btn-info">
                        <i class="fas fa-external-link-alt me-1"></i> Ko'rish
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif


    {{-- Navigation Tabs --}}
    <ul class="nav nav-pills mb-4" id="sectionTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link {{ $activeSection == 'hero' ? 'active' : '' }}" data-bs-toggle="pill" href="#heroSection">
                <i class="fas fa-images me-1"></i> Hero Slider
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $activeSection == 'stats' ? 'active' : '' }}" data-bs-toggle="pill" href="#statsSection">
                <i class="fas fa-chart-bar me-1"></i> Statistika
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $activeSection == 'quick' ? 'active' : '' }}" data-bs-toggle="pill" href="#quickSection">
                <i class="fas fa-bolt me-1"></i> Tezkor havolalar
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $activeSection == 'news' ? 'active' : '' }}" data-bs-toggle="pill" href="#newsSection">
                <i class="fas fa-newspaper me-1"></i> Yangiliklar
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $activeSection == 'events' ? 'active' : '' }}" data-bs-toggle="pill" href="#eventsSection">
                <i class="fas fa-calendar-alt me-1"></i> Tadbirlar
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $activeSection == 'academy' ? 'active' : '' }}" data-bs-toggle="pill" href="#academySection">
                <i class="fas fa-university me-1"></i> Akademiya hayoti
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $activeSection == 'faq' ? 'active' : '' }}" data-bs-toggle="pill" href="#faqSection">
                <i class="fas fa-question-circle me-1"></i> FAQ
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $activeSection == 'partners' ? 'active' : '' }}" data-bs-toggle="pill" href="#partnersSection">
                <i class="fas fa-handshake me-1"></i> Hamkorlar
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $activeSection == 'buttons' ? 'active' : '' }}" data-bs-toggle="pill" href="#buttonsSection">
                <i class="fas fa-mouse-pointer me-1"></i> Tugmalar
            </a>
        </li>
    </ul>

    <form id="homepageForm" action="{{ route('cms.homepage.update') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="tab-content">
            {{-- ==================== HERO SLIDER SECTION ==================== --}}
            <div class="tab-pane fade {{ $activeSection == 'hero' ? 'show active' : '' }}" id="heroSection">
                <div class="content-card">
                    <div class="section-header hero">
                        <h5 class="mb-0"><i class="fas fa-star me-2"></i>Hero Slider (3 ta slayd)</h5>
                    </div>
                    <div class="p-4">
                        @for($i = 1; $i <= 3; $i++)
                            @php
                                $badge = $contents->where('key', "slide{$i}_badge")->first();
                                $title = $contents->where('key', "slide{$i}_title")->first();
                                $subtitle = $contents->where('key', "slide{$i}_subtitle")->first();
                                $btn1 = $contents->where('key', "slide{$i}_btn1")->first();
                                $btn2 = $contents->where('key', "slide{$i}_btn2")->first();
                                $image = $contents->where('key', "slide{$i}_image")->first();
                            @endphp
                            <div class="slide-card">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="slide-number me-3">{{ $i }}</div>
                                    <h5 class="mb-0 fw-bold">Slayd {{ $i }}</h5>
                                </div>

                                <div class="row g-4">
                                    {{-- Badge --}}
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Badge matni</label>
                                        <div class="row g-2">
                                            <div class="col-12">
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text lang-badge uz">UZ</span>
                                                    <input type="text" name="contents[slide{{ $i }}_badge][value_uz]" value="{{ $badge->value_uz ?? '' }}" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text lang-badge en">EN</span>
                                                    <input type="text" name="contents[slide{{ $i }}_badge][value_en]" value="{{ $badge->value_en ?? '' }}" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text lang-badge ru">RU</span>
                                                    <input type="text" name="contents[slide{{ $i }}_badge][value_ru]" value="{{ $badge->value_ru ?? '' }}" class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                        <input type="hidden" name="contents[slide{{ $i }}_badge][type]" value="text">
                                    </div>

                                    {{-- Fon rasmi --}}
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Fon rasmi</label>
                                        @php
                                            $hasSlideImg = $image && $image->value_uz;
                                            $isSlideCustom = $hasSlideImg && !\Illuminate\Support\Str::startsWith($image->value_uz, 'http');
                                            if ($hasSlideImg) {
                                                if (\Illuminate\Support\Str::startsWith($image->value_uz, 'http')) $slideSrc = $image->value_uz;
                                                elseif (\Illuminate\Support\Str::startsWith($image->value_uz, 'uploads/')) $slideSrc = asset($image->value_uz);
                                                else $slideSrc = asset('storage/' . $image->value_uz);
                                            }
                                        @endphp
                                        <div class="image-upload-wrapper" data-image-key="slide{{ $i }}_image">
                                            <div class="image-preview-box slide-style" data-preview-target>
                                                @if($hasSlideImg)
                                                    <img src="{{ $slideSrc }}" alt="Slide {{ $i }}" data-current-image>
                                                @else
                                                    <div class="image-placeholder">
                                                        <i class="fas fa-image"></i>
                                                        <div>Rasm tanlang</div>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="d-flex gap-2 mt-2">
                                                <input type="file" class="form-control form-control-sm flex-grow-1" accept="image/*" data-image-input>
                                                <button type="button" class="btn btn-sm btn-danger image-delete-btn" data-image-delete style="{{ $isSlideCustom ? '' : 'display:none' }}" title="Rasmni o'chirish">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                            <small class="text-muted">Tavsiya: 1920x1080 piksel</small>
                                            <div class="image-status small mt-1"></div>
                                        </div>
                                    </div>

                                    {{-- Sarlavha --}}
                                    <div class="col-12">
                                        <label class="form-label fw-semibold">Sarlavha</label>
                                        <div class="row g-2">
                                            <div class="col-md-4">
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text lang-badge uz">UZ</span>
                                                    <input type="text" name="contents[slide{{ $i }}_title][value_uz]" value="{{ $title->value_uz ?? '' }}" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text lang-badge en">EN</span>
                                                    <input type="text" name="contents[slide{{ $i }}_title][value_en]" value="{{ $title->value_en ?? '' }}" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text lang-badge ru">RU</span>
                                                    <input type="text" name="contents[slide{{ $i }}_title][value_ru]" value="{{ $title->value_ru ?? '' }}" class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                        <input type="hidden" name="contents[slide{{ $i }}_title][type]" value="text">
                                    </div>

                                    {{-- Tavsif --}}
                                    <div class="col-12">
                                        <label class="form-label fw-semibold">Tavsif</label>
                                        <div class="row g-2">
                                            <div class="col-md-4">
                                                <textarea name="contents[slide{{ $i }}_subtitle][value_uz]" class="form-control form-control-sm" rows="2" placeholder="UZ">{{ $subtitle->value_uz ?? '' }}</textarea>
                                            </div>
                                            <div class="col-md-4">
                                                <textarea name="contents[slide{{ $i }}_subtitle][value_en]" class="form-control form-control-sm" rows="2" placeholder="EN">{{ $subtitle->value_en ?? '' }}</textarea>
                                            </div>
                                            <div class="col-md-4">
                                                <textarea name="contents[slide{{ $i }}_subtitle][value_ru]" class="form-control form-control-sm" rows="2" placeholder="RU">{{ $subtitle->value_ru ?? '' }}</textarea>
                                            </div>
                                        </div>
                                        <input type="hidden" name="contents[slide{{ $i }}_subtitle][type]" value="textarea">
                                    </div>

                                    {{-- Tugmalar --}}
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Tugma 1 (Asosiy)</label>
                                        <div class="row g-1">
                                            <div class="col-4"><input type="text" name="contents[slide{{ $i }}_btn1][value_uz]" value="{{ $btn1->value_uz ?? '' }}" class="form-control form-control-sm" placeholder="UZ"></div>
                                            <div class="col-4"><input type="text" name="contents[slide{{ $i }}_btn1][value_en]" value="{{ $btn1->value_en ?? '' }}" class="form-control form-control-sm" placeholder="EN"></div>
                                            <div class="col-4"><input type="text" name="contents[slide{{ $i }}_btn1][value_ru]" value="{{ $btn1->value_ru ?? '' }}" class="form-control form-control-sm" placeholder="RU"></div>
                                        </div>
                                        <input type="hidden" name="contents[slide{{ $i }}_btn1][type]" value="text">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Tugma 2 (Ikkilamchi)</label>
                                        <div class="row g-1">
                                            <div class="col-4"><input type="text" name="contents[slide{{ $i }}_btn2][value_uz]" value="{{ $btn2->value_uz ?? '' }}" class="form-control form-control-sm" placeholder="UZ"></div>
                                            <div class="col-4"><input type="text" name="contents[slide{{ $i }}_btn2][value_en]" value="{{ $btn2->value_en ?? '' }}" class="form-control form-control-sm" placeholder="EN"></div>
                                            <div class="col-4"><input type="text" name="contents[slide{{ $i }}_btn2][value_ru]" value="{{ $btn2->value_ru ?? '' }}" class="form-control form-control-sm" placeholder="RU"></div>
                                        </div>
                                        <input type="hidden" name="contents[slide{{ $i }}_btn2][type]" value="text">
                                    </div>
                                </div>
                            </div>
                        @endfor
                    </div>
                </div>
            </div>

            {{-- ==================== STATISTICS SECTION ==================== --}}
            <div class="tab-pane fade {{ $activeSection == 'stats' ? 'show active' : '' }}" id="statsSection">
                <div class="content-card">
                    <div class="section-header stats">
                        <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Statistika bo'limi</h5>
                    </div>
                    <div class="p-4">
                        @php
                            $statsLabel = $contents->where('key', 'stats_label')->first();
                            $statsTitle = $contents->where('key', 'stats_title')->first();
                        @endphp

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Bo'lim yorlig'i</label>
                                <div class="row g-1">
                                    <div class="col-4"><input type="text" name="contents[stats_label][value_uz]" value="{{ $statsLabel->value_uz ?? '' }}" class="form-control form-control-sm" placeholder="UZ"></div>
                                    <div class="col-4"><input type="text" name="contents[stats_label][value_en]" value="{{ $statsLabel->value_en ?? '' }}" class="form-control form-control-sm" placeholder="EN"></div>
                                    <div class="col-4"><input type="text" name="contents[stats_label][value_ru]" value="{{ $statsLabel->value_ru ?? '' }}" class="form-control form-control-sm" placeholder="RU"></div>
                                </div>
                                <input type="hidden" name="contents[stats_label][type]" value="text">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Bo'lim sarlavhasi</label>
                                <div class="row g-1">
                                    <div class="col-4"><input type="text" name="contents[stats_title][value_uz]" value="{{ $statsTitle->value_uz ?? '' }}" class="form-control form-control-sm" placeholder="UZ"></div>
                                    <div class="col-4"><input type="text" name="contents[stats_title][value_en]" value="{{ $statsTitle->value_en ?? '' }}" class="form-control form-control-sm" placeholder="EN"></div>
                                    <div class="col-4"><input type="text" name="contents[stats_title][value_ru]" value="{{ $statsTitle->value_ru ?? '' }}" class="form-control form-control-sm" placeholder="RU"></div>
                                </div>
                                <input type="hidden" name="contents[stats_title][type]" value="text">
                            </div>
                        </div>

                        <h6 class="fw-bold mb-3">Statistika ko'rsatkichlari (4 ta)</h6>
                        <div class="row g-4">
                            @for($i = 1; $i <= 4; $i++)
                                @php
                                    $statNumber = $contents->where('key', "stat{$i}_number")->first();
                                    $statLabel = $contents->where('key', "stat{$i}_label")->first();
                                @endphp
                                <div class="col-md-6 col-lg-3">
                                    <div class="info-box-card h-100">
                                        <h6 class="fw-bold mb-3"><span class="badge bg-success me-2">{{ $i }}</span> Ko'rsatkich</h6>

                                        <div class="mb-3">
                                            <label class="form-label small">Raqam</label>
                                            <input type="text" name="contents[stat{{ $i }}_number][value_uz]" value="{{ $statNumber->value_uz ?? '' }}" class="form-control form-control-sm" placeholder="1500+">
                                            <input type="hidden" name="contents[stat{{ $i }}_number][value_en]" value="{{ $statNumber->value_en ?? '' }}">
                                            <input type="hidden" name="contents[stat{{ $i }}_number][value_ru]" value="{{ $statNumber->value_ru ?? '' }}">
                                            <input type="hidden" name="contents[stat{{ $i }}_number][type]" value="text">
                                        </div>

                                        <div>
                                            <label class="form-label small">Yorliq</label>
                                            <input type="text" name="contents[stat{{ $i }}_label][value_uz]" value="{{ $statLabel->value_uz ?? '' }}" class="form-control form-control-sm mb-1" placeholder="UZ">
                                            <input type="text" name="contents[stat{{ $i }}_label][value_en]" value="{{ $statLabel->value_en ?? '' }}" class="form-control form-control-sm mb-1" placeholder="EN">
                                            <input type="text" name="contents[stat{{ $i }}_label][value_ru]" value="{{ $statLabel->value_ru ?? '' }}" class="form-control form-control-sm" placeholder="RU">
                                            <input type="hidden" name="contents[stat{{ $i }}_label][type]" value="text">
                                        </div>
                                    </div>
                                </div>
                            @endfor
                        </div>
                    </div>
                </div>
            </div>

            {{-- ==================== QUICK LINKS SECTION ==================== --}}
            <div class="tab-pane fade {{ $activeSection == 'quick' ? 'show active' : '' }}" id="quickSection">
                <div class="content-card">
                    <div class="section-header quick">
                        <h5 class="mb-0"><i class="fas fa-bolt me-2"></i>Tezkor havolalar (4 ta)</h5>
                    </div>
                    <div class="p-4">
                        <div class="row g-4">
                            @for($i = 1; $i <= 4; $i++)
                                @php
                                    $linkTitle = $contents->where('key', "quick_link{$i}_title")->first();
                                    $linkDesc = $contents->where('key', "quick_link{$i}_desc")->first();
                                @endphp
                                <div class="col-md-6">
                                    <div class="info-box-card h-100">
                                        <h6 class="fw-bold mb-3"><span class="badge bg-primary me-2">{{ $i }}</span> Havola</h6>

                                        <div class="mb-3">
                                            <label class="form-label small">Sarlavha</label>
                                            <input type="text" name="contents[quick_link{{ $i }}_title][value_uz]" value="{{ $linkTitle->value_uz ?? '' }}" class="form-control form-control-sm mb-1" placeholder="UZ">
                                            <input type="text" name="contents[quick_link{{ $i }}_title][value_en]" value="{{ $linkTitle->value_en ?? '' }}" class="form-control form-control-sm mb-1" placeholder="EN">
                                            <input type="text" name="contents[quick_link{{ $i }}_title][value_ru]" value="{{ $linkTitle->value_ru ?? '' }}" class="form-control form-control-sm" placeholder="RU">
                                            <input type="hidden" name="contents[quick_link{{ $i }}_title][type]" value="text">
                                        </div>

                                        <div>
                                            <label class="form-label small">Tavsif</label>
                                            <textarea name="contents[quick_link{{ $i }}_desc][value_uz]" class="form-control form-control-sm mb-1" rows="2" placeholder="UZ">{{ $linkDesc->value_uz ?? '' }}</textarea>
                                            <textarea name="contents[quick_link{{ $i }}_desc][value_en]" class="form-control form-control-sm mb-1" rows="2" placeholder="EN">{{ $linkDesc->value_en ?? '' }}</textarea>
                                            <textarea name="contents[quick_link{{ $i }}_desc][value_ru]" class="form-control form-control-sm" rows="2" placeholder="RU">{{ $linkDesc->value_ru ?? '' }}</textarea>
                                            <input type="hidden" name="contents[quick_link{{ $i }}_desc][type]" value="textarea">
                                        </div>
                                    </div>
                                </div>
                            @endfor
                        </div>
                    </div>
                </div>
            </div>

            {{-- ==================== NEWS SECTION ==================== --}}
            <div class="tab-pane fade {{ $activeSection == 'news' ? 'show active' : '' }}" id="newsSection">
                <div class="content-card">
                    <div class="section-header news">
                        <h5 class="mb-0"><i class="fas fa-newspaper me-2"></i>Yangiliklar bo'limi</h5>
                    </div>
                    <div class="p-4">
                        @php
                            $newsLabel = $contents->where('key', 'news_label')->first();
                            $newsTitle = $contents->where('key', 'news_title')->first();
                            $newsSubtitle = $contents->where('key', 'news_subtitle')->first();
                        @endphp

                        <div class="row g-4">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Bo'lim yorlig'i</label>
                                <input type="text" name="contents[news_label][value_uz]" value="{{ $newsLabel->value_uz ?? '' }}" class="form-control form-control-sm mb-1" placeholder="UZ">
                                <input type="text" name="contents[news_label][value_en]" value="{{ $newsLabel->value_en ?? '' }}" class="form-control form-control-sm mb-1" placeholder="EN">
                                <input type="text" name="contents[news_label][value_ru]" value="{{ $newsLabel->value_ru ?? '' }}" class="form-control form-control-sm" placeholder="RU">
                                <input type="hidden" name="contents[news_label][type]" value="text">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Bo'lim sarlavhasi</label>
                                <input type="text" name="contents[news_title][value_uz]" value="{{ $newsTitle->value_uz ?? '' }}" class="form-control form-control-sm mb-1" placeholder="UZ">
                                <input type="text" name="contents[news_title][value_en]" value="{{ $newsTitle->value_en ?? '' }}" class="form-control form-control-sm mb-1" placeholder="EN">
                                <input type="text" name="contents[news_title][value_ru]" value="{{ $newsTitle->value_ru ?? '' }}" class="form-control form-control-sm" placeholder="RU">
                                <input type="hidden" name="contents[news_title][type]" value="text">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Qo'shimcha matn</label>
                                <input type="text" name="contents[news_subtitle][value_uz]" value="{{ $newsSubtitle->value_uz ?? '' }}" class="form-control form-control-sm mb-1" placeholder="UZ">
                                <input type="text" name="contents[news_subtitle][value_en]" value="{{ $newsSubtitle->value_en ?? '' }}" class="form-control form-control-sm mb-1" placeholder="EN">
                                <input type="text" name="contents[news_subtitle][value_ru]" value="{{ $newsSubtitle->value_ru ?? '' }}" class="form-control form-control-sm" placeholder="RU">
                                <input type="hidden" name="contents[news_subtitle][type]" value="text">
                            </div>
                        </div>

                        <div class="alert alert-info mt-4">
                            <i class="fas fa-info-circle me-2"></i>
                            Yangiliklar ro'yxati <a href="{{ route('cms.news.index') }}" class="fw-bold">CMS > Yangiliklar</a> bo'limidan boshqariladi.
                        </div>
                    </div>
                </div>
            </div>

            {{-- ==================== EVENTS SECTION ==================== --}}
            <div class="tab-pane fade {{ $activeSection == 'events' ? 'show active' : '' }}" id="eventsSection">
                <div class="content-card">
                    <div class="section-header events">
                        <h5 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Tadbirlar bo'limi</h5>
                    </div>
                    <div class="p-4">
                        @php
                            $eventsLabel = $contents->where('key', 'events_label')->first();
                            $eventsTitle = $contents->where('key', 'events_title')->first();
                            $eventsSubtitle = $contents->where('key', 'events_subtitle')->first();
                        @endphp

                        <div class="row g-4">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Bo'lim yorlig'i</label>
                                <input type="text" name="contents[events_label][value_uz]" value="{{ $eventsLabel->value_uz ?? '' }}" class="form-control form-control-sm mb-1" placeholder="UZ">
                                <input type="text" name="contents[events_label][value_en]" value="{{ $eventsLabel->value_en ?? '' }}" class="form-control form-control-sm mb-1" placeholder="EN">
                                <input type="text" name="contents[events_label][value_ru]" value="{{ $eventsLabel->value_ru ?? '' }}" class="form-control form-control-sm" placeholder="RU">
                                <input type="hidden" name="contents[events_label][type]" value="text">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Bo'lim sarlavhasi</label>
                                <input type="text" name="contents[events_title][value_uz]" value="{{ $eventsTitle->value_uz ?? '' }}" class="form-control form-control-sm mb-1" placeholder="UZ">
                                <input type="text" name="contents[events_title][value_en]" value="{{ $eventsTitle->value_en ?? '' }}" class="form-control form-control-sm mb-1" placeholder="EN">
                                <input type="text" name="contents[events_title][value_ru]" value="{{ $eventsTitle->value_ru ?? '' }}" class="form-control form-control-sm" placeholder="RU">
                                <input type="hidden" name="contents[events_title][type]" value="text">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Qo'shimcha matn</label>
                                <input type="text" name="contents[events_subtitle][value_uz]" value="{{ $eventsSubtitle->value_uz ?? '' }}" class="form-control form-control-sm mb-1" placeholder="UZ">
                                <input type="text" name="contents[events_subtitle][value_en]" value="{{ $eventsSubtitle->value_en ?? '' }}" class="form-control form-control-sm mb-1" placeholder="EN">
                                <input type="text" name="contents[events_subtitle][value_ru]" value="{{ $eventsSubtitle->value_ru ?? '' }}" class="form-control form-control-sm" placeholder="RU">
                                <input type="hidden" name="contents[events_subtitle][type]" value="text">
                            </div>
                        </div>

                        <div class="alert alert-info mt-4">
                            <i class="fas fa-info-circle me-2"></i>
                            Tadbirlar ro'yxati <a href="{{ route('cms.events.index') }}" class="fw-bold">CMS > Tadbirlar</a> bo'limidan boshqariladi.
                        </div>
                    </div>
                </div>
            </div>

            {{-- ==================== ACADEMY LIFE (GALLERY) SECTION ==================== --}}
            <div class="tab-pane fade {{ $activeSection == 'academy' ? 'show active' : '' }}" id="academySection">
                <div class="content-card">
                    <div class="section-header academy">
                        <h5 class="mb-0"><i class="fas fa-university me-2"></i>Akademiya hayoti (Galereya)</h5>
                    </div>
                    <div class="p-4">
                        @php
                            $academyLabel = $contents->where('key', 'academy_label')->first();
                            $academyTitle = $contents->where('key', 'academy_title')->first();
                        @endphp

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Bo'lim yorlig'i</label>
                                <div class="row g-1">
                                    <div class="col-4"><input type="text" name="contents[academy_label][value_uz]" value="{{ $academyLabel->value_uz ?? '' }}" class="form-control form-control-sm" placeholder="UZ"></div>
                                    <div class="col-4"><input type="text" name="contents[academy_label][value_en]" value="{{ $academyLabel->value_en ?? '' }}" class="form-control form-control-sm" placeholder="EN"></div>
                                    <div class="col-4"><input type="text" name="contents[academy_label][value_ru]" value="{{ $academyLabel->value_ru ?? '' }}" class="form-control form-control-sm" placeholder="RU"></div>
                                </div>
                                <input type="hidden" name="contents[academy_label][type]" value="text">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Bo'lim sarlavhasi</label>
                                <div class="row g-1">
                                    <div class="col-4"><input type="text" name="contents[academy_title][value_uz]" value="{{ $academyTitle->value_uz ?? '' }}" class="form-control form-control-sm" placeholder="UZ"></div>
                                    <div class="col-4"><input type="text" name="contents[academy_title][value_en]" value="{{ $academyTitle->value_en ?? '' }}" class="form-control form-control-sm" placeholder="EN"></div>
                                    <div class="col-4"><input type="text" name="contents[academy_title][value_ru]" value="{{ $academyTitle->value_ru ?? '' }}" class="form-control form-control-sm" placeholder="RU"></div>
                                </div>
                                <input type="hidden" name="contents[academy_title][type]" value="text">
                            </div>
                        </div>

                        <h6 class="fw-bold mb-3">Galereya elementlari (4 ta)</h6>
                        <div class="row g-4">
                            @for($i = 1; $i <= 4; $i++)
                                @php
                                    $galleryTitle = $contents->where('key', "gallery_title_{$i}")->first();
                                    $galleryText = $contents->where('key', "gallery_text_{$i}")->first();
                                    $galleryImage = $contents->where('key', "gallery_image_{$i}")->first();
                                @endphp
                                <div class="col-md-4">
                                    <div class="info-box-card h-100">
                                        <h6 class="fw-bold mb-3"><span class="badge bg-info me-2">{{ $i }}</span> Element</h6>

                                        <div class="mb-3">
                                            @php
                                                $hasImage = $galleryImage && $galleryImage->value_uz;
                                                $isCustomUpload = $hasImage && !\Illuminate\Support\Str::startsWith($galleryImage->value_uz, 'http');
                                                if ($hasImage) {
                                                    if (\Illuminate\Support\Str::startsWith($galleryImage->value_uz, 'http')) $imgSrc = $galleryImage->value_uz;
                                                    elseif (\Illuminate\Support\Str::startsWith($galleryImage->value_uz, 'uploads/')) $imgSrc = asset($galleryImage->value_uz);
                                                    else $imgSrc = asset('storage/' . $galleryImage->value_uz);
                                                }
                                            @endphp
                                            <div class="image-upload-wrapper" data-image-key="gallery_image_{{ $i }}">
                                                <div class="image-preview-box" data-preview-target>
                                                    @if($hasImage)
                                                        <img src="{{ $imgSrc }}" alt="Gallery {{ $i }}" data-current-image>
                                                    @else
                                                        <div class="image-placeholder">
                                                            <i class="fas fa-image"></i>
                                                            <div>Rasm tanlang</div>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="d-flex gap-2 mt-2">
                                                    <input type="file" class="form-control form-control-sm flex-grow-1" accept="image/*" data-image-input>
                                                    <button type="button" class="btn btn-sm btn-danger image-delete-btn" data-image-delete style="{{ $isCustomUpload ? '' : 'display:none' }}" title="Rasmni o'chirish">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                                <div class="image-status small mt-1"></div>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label small">Sarlavha</label>
                                            <input type="text" name="contents[gallery_title_{{ $i }}][value_uz]" value="{{ $galleryTitle->value_uz ?? '' }}" class="form-control form-control-sm mb-1" placeholder="UZ">
                                            <input type="text" name="contents[gallery_title_{{ $i }}][value_en]" value="{{ $galleryTitle->value_en ?? '' }}" class="form-control form-control-sm mb-1" placeholder="EN">
                                            <input type="text" name="contents[gallery_title_{{ $i }}][value_ru]" value="{{ $galleryTitle->value_ru ?? '' }}" class="form-control form-control-sm" placeholder="RU">
                                            <input type="hidden" name="contents[gallery_title_{{ $i }}][type]" value="text">
                                        </div>

                                        <div>
                                            <label class="form-label small">Tavsif</label>
                                            <textarea name="contents[gallery_text_{{ $i }}][value_uz]" class="form-control form-control-sm mb-1" rows="2" placeholder="UZ">{{ $galleryText->value_uz ?? '' }}</textarea>
                                            <textarea name="contents[gallery_text_{{ $i }}][value_en]" class="form-control form-control-sm mb-1" rows="2" placeholder="EN">{{ $galleryText->value_en ?? '' }}</textarea>
                                            <textarea name="contents[gallery_text_{{ $i }}][value_ru]" class="form-control form-control-sm" rows="2" placeholder="RU">{{ $galleryText->value_ru ?? '' }}</textarea>
                                            <input type="hidden" name="contents[gallery_text_{{ $i }}][type]" value="textarea">
                                        </div>
                                    </div>
                                </div>
                            @endfor
                        </div>
                    </div>
                </div>
            </div>

            {{-- ==================== FAQ SECTION ==================== --}}
            <div class="tab-pane fade {{ $activeSection == 'faq' ? 'show active' : '' }}" id="faqSection">
                <div class="content-card">
                    <div class="section-header faq">
                        <h5 class="mb-0"><i class="fas fa-question-circle me-2"></i>Ko'p so'raladigan savollar (FAQ)</h5>
                    </div>
                    <div class="p-4">
                        @php
                            $faqLabel = $contents->where('key', 'faq_label')->first();
                            $faqTitle = $contents->where('key', 'faq_title')->first();
                            $faqSubtitle = $contents->where('key', 'faq_subtitle')->first();
                        @endphp

                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Bo'lim yorlig'i</label>
                                <input type="text" name="contents[faq_label][value_uz]" value="{{ $faqLabel->value_uz ?? '' }}" class="form-control form-control-sm mb-1" placeholder="UZ">
                                <input type="text" name="contents[faq_label][value_en]" value="{{ $faqLabel->value_en ?? '' }}" class="form-control form-control-sm mb-1" placeholder="EN">
                                <input type="text" name="contents[faq_label][value_ru]" value="{{ $faqLabel->value_ru ?? '' }}" class="form-control form-control-sm" placeholder="RU">
                                <input type="hidden" name="contents[faq_label][type]" value="text">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Bo'lim sarlavhasi</label>
                                <input type="text" name="contents[faq_title][value_uz]" value="{{ $faqTitle->value_uz ?? '' }}" class="form-control form-control-sm mb-1" placeholder="UZ">
                                <input type="text" name="contents[faq_title][value_en]" value="{{ $faqTitle->value_en ?? '' }}" class="form-control form-control-sm mb-1" placeholder="EN">
                                <input type="text" name="contents[faq_title][value_ru]" value="{{ $faqTitle->value_ru ?? '' }}" class="form-control form-control-sm" placeholder="RU">
                                <input type="hidden" name="contents[faq_title][type]" value="text">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Qo'shimcha matn</label>
                                <input type="text" name="contents[faq_subtitle][value_uz]" value="{{ $faqSubtitle->value_uz ?? '' }}" class="form-control form-control-sm mb-1" placeholder="UZ">
                                <input type="text" name="contents[faq_subtitle][value_en]" value="{{ $faqSubtitle->value_en ?? '' }}" class="form-control form-control-sm mb-1" placeholder="EN">
                                <input type="text" name="contents[faq_subtitle][value_ru]" value="{{ $faqSubtitle->value_ru ?? '' }}" class="form-control form-control-sm" placeholder="RU">
                                <input type="hidden" name="contents[faq_subtitle][type]" value="text">
                            </div>
                        </div>

                        <h6 class="fw-bold mb-3">FAQ elementlari (3 ta)</h6>
                        <div class="row g-4">
                            @for($i = 1; $i <= 3; $i++)
                                @php
                                    $faqQuestion = $contents->where('key', "faq{$i}_question")->first();
                                    $faqAnswer = $contents->where('key', "faq{$i}_answer")->first();
                                @endphp
                                <div class="col-12">
                                    <div class="info-box-card">
                                        <h6 class="fw-bold mb-3"><span class="badge bg-danger me-2">{{ $i }}</span> Savol-Javob</h6>

                                        <div class="row g-4">
                                            <div class="col-md-6">
                                                <label class="form-label small fw-semibold">Savol</label>
                                                <input type="text" name="contents[faq{{ $i }}_question][value_uz]" value="{{ $faqQuestion->value_uz ?? '' }}" class="form-control form-control-sm mb-1" placeholder="UZ">
                                                <input type="text" name="contents[faq{{ $i }}_question][value_en]" value="{{ $faqQuestion->value_en ?? '' }}" class="form-control form-control-sm mb-1" placeholder="EN">
                                                <input type="text" name="contents[faq{{ $i }}_question][value_ru]" value="{{ $faqQuestion->value_ru ?? '' }}" class="form-control form-control-sm" placeholder="RU">
                                                <input type="hidden" name="contents[faq{{ $i }}_question][type]" value="text">
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label small fw-semibold">Javob</label>
                                                <textarea name="contents[faq{{ $i }}_answer][value_uz]" class="form-control form-control-sm mb-1" rows="2" placeholder="UZ">{{ $faqAnswer->value_uz ?? '' }}</textarea>
                                                <textarea name="contents[faq{{ $i }}_answer][value_en]" class="form-control form-control-sm mb-1" rows="2" placeholder="EN">{{ $faqAnswer->value_en ?? '' }}</textarea>
                                                <textarea name="contents[faq{{ $i }}_answer][value_ru]" class="form-control form-control-sm" rows="2" placeholder="RU">{{ $faqAnswer->value_ru ?? '' }}</textarea>
                                                <input type="hidden" name="contents[faq{{ $i }}_answer][type]" value="textarea">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endfor
                        </div>
                    </div>
                </div>
            </div>

            {{-- ==================== PARTNERS SECTION ==================== --}}
            <div class="tab-pane fade {{ $activeSection == 'partners' ? 'show active' : '' }}" id="partnersSection">
                <div class="content-card">
                    <div class="section-header partners">
                        <h5 class="mb-0"><i class="fas fa-handshake me-2"></i>Hamkorlar bo'limi (6 ta)</h5>
                    </div>
                    <div class="p-4">
                        @php
                            $partnersLabel = $contents->where('key', 'partners_label')->first();
                        @endphp

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Bo'lim yorlig'i</label>
                                <div class="row g-1">
                                    <div class="col-4"><input type="text" name="contents[partners_label][value_uz]" value="{{ $partnersLabel->value_uz ?? '' }}" class="form-control form-control-sm" placeholder="UZ"></div>
                                    <div class="col-4"><input type="text" name="contents[partners_label][value_en]" value="{{ $partnersLabel->value_en ?? '' }}" class="form-control form-control-sm" placeholder="EN"></div>
                                    <div class="col-4"><input type="text" name="contents[partners_label][value_ru]" value="{{ $partnersLabel->value_ru ?? '' }}" class="form-control form-control-sm" placeholder="RU"></div>
                                </div>
                                <input type="hidden" name="contents[partners_label][type]" value="text">
                            </div>
                        </div>

                        <h6 class="fw-bold mb-3">Hamkor logolari va nomlari</h6>
                        <div class="row g-4">
                            @for($i = 1; $i <= 6; $i++)
                                @php
                                    $partnerLogo = $contents->where('key', "partner{$i}_logo")->first();
                                    $partnerName = $contents->where('key', "partner{$i}_name")->first();
                                @endphp
                                <div class="col-md-4">
                                    <div class="info-box-card h-100">
                                        <h6 class="fw-bold mb-3"><span class="badge bg-success me-2">{{ $i }}</span> Hamkor</h6>

                                        <div class="mb-3">
                                            @php
                                                $hasLogo = $partnerLogo && $partnerLogo->value_uz;
                                                $isLogoCustom = $hasLogo && !\Illuminate\Support\Str::startsWith($partnerLogo->value_uz, 'http');
                                                if ($hasLogo) {
                                                    if (\Illuminate\Support\Str::startsWith($partnerLogo->value_uz, 'http')) $logoSrc = $partnerLogo->value_uz;
                                                    elseif (\Illuminate\Support\Str::startsWith($partnerLogo->value_uz, 'uploads/')) $logoSrc = asset($partnerLogo->value_uz);
                                                    else $logoSrc = asset('storage/' . $partnerLogo->value_uz);
                                                }
                                            @endphp
                                            <div class="image-upload-wrapper" data-image-key="partner{{ $i }}_logo">
                                                <div class="image-preview-box partner-style" data-preview-target>
                                                    @if($hasLogo)
                                                        <img src="{{ $logoSrc }}" alt="Partner {{ $i }}" data-current-image>
                                                    @else
                                                        <div class="image-placeholder">
                                                            <i class="fas fa-building"></i>
                                                            <div>Logo tanlang</div>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="d-flex gap-2 mt-2">
                                                    <input type="file" class="form-control form-control-sm flex-grow-1" accept="image/*" data-image-input>
                                                    <button type="button" class="btn btn-sm btn-danger image-delete-btn" data-image-delete style="{{ $isLogoCustom ? '' : 'display:none' }}" title="Logoni o'chirish">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                                <small class="text-muted">PNG yoki SVG tavsiya (shaffof fon)</small>
                                                <div class="image-status small mt-1"></div>
                                            </div>
                                        </div>

                                        <div>
                                            <label class="form-label small">Hamkor nomi</label>
                                            <input type="text" name="contents[partner{{ $i }}_name][value_uz]" value="{{ $partnerName->value_uz ?? '' }}" class="form-control form-control-sm mb-1" placeholder="UZ - Hamkor nomi">
                                            <input type="text" name="contents[partner{{ $i }}_name][value_en]" value="{{ $partnerName->value_en ?? '' }}" class="form-control form-control-sm mb-1" placeholder="EN - Partner name">
                                            <input type="text" name="contents[partner{{ $i }}_name][value_ru]" value="{{ $partnerName->value_ru ?? '' }}" class="form-control form-control-sm" placeholder="RU - Название партнера">
                                            <input type="hidden" name="contents[partner{{ $i }}_name][type]" value="text">
                                        </div>
                                    </div>
                                </div>
                            @endfor
                        </div>
                    </div>
                </div>
            </div>

            {{-- ==================== BUTTONS SECTION ==================== --}}
            <div class="tab-pane fade {{ $activeSection == 'buttons' ? 'show active' : '' }}" id="buttonsSection">
                <div class="content-card">
                    <div class="section-header buttons">
                        <h5 class="mb-0"><i class="fas fa-mouse-pointer me-2"></i>Umumiy tugma matnlari</h5>
                    </div>
                    <div class="p-4">
                        @php
                            $btnLearnMore = $contents->where('key', 'btn_learn_more')->first();
                            $btnAllNews = $contents->where('key', 'btn_all_news')->first();
                            $btnReadMore = $contents->where('key', 'btn_read_more')->first();
                        @endphp

                        <div class="row g-4">
                            <div class="col-md-4">
                                <div class="info-box-card h-100">
                                    <label class="form-label fw-semibold">"Batafsil" tugmasi</label>
                                    <input type="text" name="contents[btn_learn_more][value_uz]" value="{{ $btnLearnMore->value_uz ?? '' }}" class="form-control form-control-sm mb-1" placeholder="UZ">
                                    <input type="text" name="contents[btn_learn_more][value_en]" value="{{ $btnLearnMore->value_en ?? '' }}" class="form-control form-control-sm mb-1" placeholder="EN">
                                    <input type="text" name="contents[btn_learn_more][value_ru]" value="{{ $btnLearnMore->value_ru ?? '' }}" class="form-control form-control-sm" placeholder="RU">
                                    <input type="hidden" name="contents[btn_learn_more][type]" value="text">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-box-card h-100">
                                    <label class="form-label fw-semibold">"Barcha yangiliklar" tugmasi</label>
                                    <input type="text" name="contents[btn_all_news][value_uz]" value="{{ $btnAllNews->value_uz ?? '' }}" class="form-control form-control-sm mb-1" placeholder="UZ">
                                    <input type="text" name="contents[btn_all_news][value_en]" value="{{ $btnAllNews->value_en ?? '' }}" class="form-control form-control-sm mb-1" placeholder="EN">
                                    <input type="text" name="contents[btn_all_news][value_ru]" value="{{ $btnAllNews->value_ru ?? '' }}" class="form-control form-control-sm" placeholder="RU">
                                    <input type="hidden" name="contents[btn_all_news][type]" value="text">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-box-card h-100">
                                    <label class="form-label fw-semibold">"Ko'proq o'qish" tugmasi</label>
                                    <input type="text" name="contents[btn_read_more][value_uz]" value="{{ $btnReadMore->value_uz ?? '' }}" class="form-control form-control-sm mb-1" placeholder="UZ">
                                    <input type="text" name="contents[btn_read_more][value_en]" value="{{ $btnReadMore->value_en ?? '' }}" class="form-control form-control-sm mb-1" placeholder="EN">
                                    <input type="text" name="contents[btn_read_more][value_ru]" value="{{ $btnReadMore->value_ru ?? '' }}" class="form-control form-control-sm" placeholder="RU">
                                    <input type="hidden" name="contents[btn_read_more][type]" value="text">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Submit --}}
        <div class="d-flex gap-2 justify-content-end mt-4">
            <a href="{{ route('cms.dashboard') }}" class="btn btn-outline-secondary btn-lg px-4">
                <i class="fas fa-times me-1"></i> Bekor qilish
            </a>
            <button type="submit" class="btn btn-primary btn-lg px-5" id="homepageSubmitBtn">
                <i class="fas fa-save me-1"></i> Saqlash
            </button>
        </div>
    </form>

{{-- Full-screen submit loader --}}
<div class="upload-loader-overlay" id="uploadLoaderOverlay" aria-hidden="true">
    <div class="upload-loader-spinner"></div>
    <div class="upload-loader-text" id="uploadLoaderText">Saqlanmoqda...</div>
    <div class="upload-loader-progress" id="uploadLoaderProgress">Iltimos kuting</div>
</div>

<script>
    (function initHomepageImageUploads() {
        const UPLOAD_URL = @json(route('cms.homepage.upload-image'));
        const DELETE_URL = @json(route('cms.homepage.delete-image'));
        const CSRF = @json(csrf_token());

        function showStatus(wrapper, type, message) {
            const status = wrapper.querySelector('.image-status');
            if (!status) return;
            const colors = { success: 'text-success', error: 'text-danger', loading: 'text-primary' };
            const icons = { success: 'check-circle', error: 'exclamation-circle', loading: 'spinner fa-spin' };
            status.className = 'image-status small mt-1 ' + (colors[type] || '');
            status.innerHTML = '<i class="fas fa-' + (icons[type] || 'info-circle') + ' me-1"></i>' + message;
            if (type === 'success') {
                setTimeout(() => { status.innerHTML = ''; }, 3000);
            }
        }

        function setPreviewImage(previewBox, url) {
            previewBox.innerHTML = '<img src="' + url + '" alt="Rasm" data-current-image>';
            previewBox.classList.add('has-new');
        }

        function setPreviewPlaceholder(previewBox, isPartner) {
            const icon = isPartner ? 'building' : 'image';
            const text = isPartner ? 'Logo tanlang' : 'Rasm tanlang';
            previewBox.innerHTML = '<div class="image-placeholder"><i class="fas fa-' + icon + '"></i><div>' + text + '</div></div>';
            previewBox.classList.remove('has-new');
        }

        async function uploadImage(wrapper, file) {
            const previewBox = wrapper.querySelector('[data-preview-target]');
            const deleteBtn = wrapper.querySelector('[data-image-delete]');
            const key = wrapper.dataset.imageKey;

            if (!file.type.startsWith('image/')) {
                showStatus(wrapper, 'error', 'Faqat rasm fayllari');
                return;
            }
            if (file.size > 20 * 1024 * 1024) {
                showStatus(wrapper, 'error', 'Rasm 20MB dan katta');
                return;
            }

            previewBox.classList.add('loading');
            showStatus(wrapper, 'loading', 'Yuklanmoqda...');

            const formData = new FormData();
            formData.append('image', file);
            formData.append('key', key);
            formData.append('_token', CSRF);

            try {
                const response = await fetch(UPLOAD_URL, {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                    credentials: 'same-origin',
                });

                let data;
                try { data = await response.json(); }
                catch (e) {
                    showStatus(wrapper, 'error', 'Server javobi noto\'g\'ri (HTTP ' + response.status + ')');
                    return;
                }

                if (!response.ok || !data.success) {
                    showStatus(wrapper, 'error', data.error || ('Xato (HTTP ' + response.status + ')'));
                    return;
                }

                setPreviewImage(previewBox, data.url);
                if (deleteBtn) deleteBtn.style.display = '';
                showStatus(wrapper, 'success', data.message || 'Saqlandi');
            } catch (err) {
                showStatus(wrapper, 'error', 'Tarmoq xatosi: ' + err.message);
            } finally {
                previewBox.classList.remove('loading');
            }
        }

        async function deleteImage(wrapper) {
            const previewBox = wrapper.querySelector('[data-preview-target]');
            const deleteBtn = wrapper.querySelector('[data-image-delete]');
            const fileInput = wrapper.querySelector('[data-image-input]');
            const key = wrapper.dataset.imageKey;
            const isPartner = previewBox.classList.contains('partner-style');

            if (!confirm('Rasmni butunlay o\'chirish?')) return;

            previewBox.classList.add('loading');
            showStatus(wrapper, 'loading', 'O\'chirilmoqda...');

            try {
                const formData = new URLSearchParams();
                formData.append('key', key);
                formData.append('_token', CSRF);

                const response = await fetch(DELETE_URL, {
                    method: 'POST',
                    body: formData,
                    headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                    credentials: 'same-origin',
                });

                const data = await response.json();
                if (!response.ok || !data.success) {
                    showStatus(wrapper, 'error', data.error || 'O\'chirib bo\'lmadi');
                    return;
                }

                setPreviewPlaceholder(previewBox, isPartner);
                if (deleteBtn) deleteBtn.style.display = 'none';
                if (fileInput) fileInput.value = '';
                showStatus(wrapper, 'success', 'O\'chirildi');
            } catch (err) {
                showStatus(wrapper, 'error', 'Tarmoq xatosi: ' + err.message);
            } finally {
                previewBox.classList.remove('loading');
            }
        }

        function bindWrappers() {
            document.querySelectorAll('.image-upload-wrapper[data-image-key]').forEach(wrapper => {
                if (wrapper.dataset.bound === '1') return;
                wrapper.dataset.bound = '1';

                const fileInput = wrapper.querySelector('[data-image-input]');
                const deleteBtn = wrapper.querySelector('[data-image-delete]');
                const previewBox = wrapper.querySelector('[data-preview-target]');

                if (previewBox && fileInput) {
                    previewBox.addEventListener('click', e => {
                        if (e.target.closest('button')) return;
                        fileInput.click();
                    });
                }

                if (fileInput) {
                    fileInput.addEventListener('change', () => {
                        const file = fileInput.files && fileInput.files[0];
                        if (file) uploadImage(wrapper, file);
                        fileInput.value = '';
                    });
                }

                if (deleteBtn) {
                    deleteBtn.addEventListener('click', () => deleteImage(wrapper));
                }
            });
        }

        function bindFormSubmit() {
            const form = document.getElementById('homepageForm');
            const overlay = document.getElementById('uploadLoaderOverlay');
            const text = document.getElementById('uploadLoaderText');
            const progress = document.getElementById('uploadLoaderProgress');
            const submitBtn = document.getElementById('homepageSubmitBtn');
            if (!form || !overlay) return;

            form.addEventListener('submit', () => {
                if (text) text.textContent = 'Matn ma\'lumotlari saqlanmoqda...';
                if (progress) progress.textContent = 'Iltimos kuting';
                overlay.classList.add('active');
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saqlanmoqda...';
                }
            });
        }

        function init() {
            bindWrappers();
            bindFormSubmit();
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', init);
        } else {
            init();
        }
    })();
</script>
@endsection
