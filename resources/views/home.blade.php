@extends(\App\Helpers\TemplateHelper::getLayout())

@php
    use App\Models\CmsContent;

    // Get homepage content from CMS
    $homepageContents = CmsContent::where('section', 'homepage')->get()->keyBy('key');

    // Get current language
    $lang = app()->getLocale() ?? 'uz';
    $langField = 'value_' . $lang;

    // Helper function to get content
    $getContent = function($key, $default = '') use ($homepageContents, $langField) {
        $content = $homepageContents->get($key);
        return $content ? ($content->$langField ?? $content->value_uz ?? $default) : $default;
    };

    // Helper function to get image URL
    $getImage = function($key, $default = '') use ($homepageContents) {
        $content = $homepageContents->get($key);
        if ($content && $content->value_uz) {
            $val = trim(ltrim($content->value_uz, '/'));
            if ($val === '') return $default;
            if (Str::startsWith($val, 'http')) return $val;
            // New format: uploads/homepage/... served directly from public/
            if (Str::startsWith($val, 'uploads/')) return asset($val);
            // Old format: cms/homepage/... served via storage symlink
            return asset('storage/' . $val);
        }
        return $default;
    };

    // Default hero images
    $defaultSlideImages = [
        1 => 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?w=1920',
        2 => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=1920',
        3 => 'https://images.unsplash.com/photo-1469474968028-56623f02e42e?w=1920',
    ];

    // Get slide images from CMS or use defaults
    $slide1Image = $getImage('slide1_image', $defaultSlideImages[1]);
    $slide2Image = $getImage('slide2_image', $defaultSlideImages[2]);
    $slide3Image = $getImage('slide3_image', $defaultSlideImages[3]);

    // Hero fallback image (for non-slider section)
    $heroImageUrl = $slide1Image;
@endphp

@section('title', 'Tourism va Service fakulteti - Bosh sahifa')
@section('description', 'Xalqaro turizm va mehmondo\'stlik bo\'yicha zamonaviy ta\'lim markazi')

@section('styles')
<style>
    /* ============================================
       DESIGN 2 - COMPLETE STYLES
       ============================================ */

    :root {
        --primary-lime: #C8E637;
        --primary-dark: #1a1a2e;
        --text-dark: #1a1a1a;
        --text-gray: #6b7280;
        --text-light: #9ca3af;
        --bg-light: #f8f9fa;
        --card-green: #E8F5E9;
        --card-yellow: #FFF9C4;
        --card-orange: #FFE0B2;
        --card-blue: #E3F2FD;
    }

    /* Hero Slider - Full screen behind navbar */
    .hero-slider {
        position: relative;
        min-height: 100vh;
        margin-top: 0;
        overflow: hidden;
    }

    .hero-slide {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        opacity: 0;
        transition: opacity 1s ease-in-out;
        display: flex;
        align-items: center;
        padding-top: 110px;
    }

    .hero-slide.active {
        opacity: 1;
        z-index: 1;
    }

    .hero-slide-bg {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-size: cover;
        background-position: center;
        z-index: -1;
    }

    .hero-slide-bg::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, rgba(26, 26, 46, 0.9) 0%, rgba(26, 26, 46, 0.7) 100%);
    }

    .hero-slide-content {
        position: relative;
        z-index: 2;
    }

    /* Slider Navigation */
    .slider-nav {
        position: absolute;
        bottom: 40px;
        left: 50%;
        transform: translateX(-50%);
        display: flex;
        gap: 12px;
        z-index: 10;
    }

    .slider-dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.4);
        cursor: pointer;
        transition: all 0.3s ease;
        border: none;
    }

    .slider-dot.active {
        background: var(--primary-lime);
        transform: scale(1.2);
    }

    .slider-dot:hover {
        background: rgba(255, 255, 255, 0.7);
    }

    /* Slider Arrows */
    .slider-arrow {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        width: 50px;
        height: 50px;
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 1.2rem;
        cursor: pointer;
        transition: all 0.3s ease;
        z-index: 10;
    }

    .slider-arrow:hover {
        background: var(--primary-lime);
        color: var(--primary-dark);
        border-color: var(--primary-lime);
    }

    .slider-arrow.prev {
        left: 30px;
    }

    .slider-arrow.next {
        right: 30px;
    }

    /* Hero Section (fallback) */
    .hero-section {
        background: linear-gradient(135deg, rgba(30, 41, 59, 0.85) 0%, rgba(30, 41, 59, 0.7) 100%),
                    url('{!! $heroImageUrl !!}') center center / cover no-repeat;
        min-height: 600px;
        display: flex;
        align-items: center;
        position: relative;
        margin-top: -80px;
        padding-top: 80px;
    }

    .hero-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        padding: 8px 16px;
        border-radius: 20px;
        margin-bottom: 20px;
    }

    .hero-badge-icon {
        width: 20px;
        height: 20px;
        background: var(--primary-lime);
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .hero-badge-icon i {
        font-size: 10px;
        color: var(--primary-dark);
    }

    .hero-badge span {
        color: #fff;
        font-size: 0.75rem;
        font-weight: 600;
        letter-spacing: 1px;
        text-transform: uppercase;
    }

    .hero-title {
        color: #fff;
        font-size: 3.5rem;
        font-weight: 700;
        line-height: 1.15;
        margin-bottom: 20px;
        max-width: 600px;
    }

    .hero-subtitle {
        color: rgba(255, 255, 255, 0.85);
        font-size: 1.1rem;
        line-height: 1.7;
        margin-bottom: 32px;
        max-width: 500px;
    }

    .hero-buttons {
        display: flex;
        gap: 16px;
        flex-wrap: wrap;
    }

    .btn-lime {
        background: var(--primary-lime);
        color: var(--primary-dark);
        font-weight: 600;
        padding: 14px 28px;
        border-radius: 8px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
        border: 2px solid var(--primary-lime);
    }

    .btn-lime:hover {
        background: #b8d62f;
        border-color: #b8d62f;
        color: var(--primary-dark);
        transform: translateY(-2px);
    }

    .btn-outline-white {
        background: transparent;
        color: #fff;
        font-weight: 600;
        padding: 14px 28px;
        border-radius: 8px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
        border: 2px solid rgba(255, 255, 255, 0.5);
    }

    .btn-outline-white:hover {
        background: rgba(255, 255, 255, 0.1);
        border-color: #fff;
        color: #fff;
    }

    .btn-dark {
        background: var(--primary-dark);
        color: #fff;
        font-weight: 600;
        padding: 14px 28px;
        border-radius: 8px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
        border: 2px solid var(--primary-dark);
    }

    .btn-dark:hover {
        background: #2d2d44;
        color: #fff;
        transform: translateY(-2px);
    }

    /* Section Styles */
    .section-label {
        color: var(--text-gray);
        font-size: 0.85rem;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 8px;
    }

    .section-title {
        color: var(--text-dark);
        font-size: 2.25rem;
        font-weight: 700;
        margin-bottom: 16px;
    }

    .section-subtitle {
        color: var(--text-gray);
        font-size: 1rem;
        margin-bottom: 40px;
    }

    /* Statistics Section */
    .statistics-section {
        padding: 80px 0;
        background: #fff;
    }

    .stat-cards {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 24px;
    }

    .stat-card {
        padding: 32px 24px;
        border-radius: 16px;
        text-align: center;
        transition: all 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
    }

    .stat-card:nth-child(1) { background: var(--card-green); }
    .stat-card:nth-child(2) { background: var(--card-yellow); }
    .stat-card:nth-child(3) { background: var(--card-orange); }
    .stat-card:nth-child(4) { background: var(--card-blue); }

    .stat-number {
        font-size: 3rem;
        font-weight: 800;
        color: var(--text-dark);
        margin-bottom: 8px;
    }

    .stat-label {
        font-size: 0.9rem;
        color: var(--text-gray);
        line-height: 1.4;
    }

    /* Quick Links Section */
    .quick-links-section {
        padding: 0 0 80px;
        background: #fff;
    }

    .quick-links-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 24px;
        margin-bottom: 32px;
    }

    .quick-link-card {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 24px;
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        transition: all 0.3s ease;
        text-decoration: none;
        color: inherit;
        position: relative;
    }

    .quick-link-card:hover {
        border-color: var(--primary-lime);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
        transform: translateY(-4px);
        background: linear-gradient(135deg, #fff 0%, #f8fff0 100%);
    }

    .quick-link-card:hover .quick-link-icon {
        background: var(--primary-lime);
    }

    .quick-link-card:hover .quick-link-icon i {
        color: var(--primary-dark);
    }

    .quick-link-card:hover .quick-link-arrow {
        opacity: 1;
        transform: translateX(0);
    }

    .quick-link-arrow {
        width: 36px;
        height: 36px;
        background: var(--primary-lime);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-left: auto;
        opacity: 0;
        transform: translateX(-10px);
        transition: all 0.3s ease;
    }

    .quick-link-arrow i {
        font-size: 0.9rem;
        color: var(--primary-dark);
    }

    .quick-link-icon {
        width: 48px;
        height: 48px;
        background: var(--bg-light);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .quick-link-icon i {
        font-size: 1.25rem;
        color: var(--primary-dark);
    }

    .quick-link-content h4 {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--text-dark);
        margin-bottom: 8px;
    }

    .quick-link-content p {
        font-size: 0.9rem;
        color: var(--text-gray);
        line-height: 1.5;
        margin: 0;
    }

    /* News Section */
    .news-section {
        padding: 80px 0;
        background: var(--bg-light);
    }

    .news-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 24px;
        margin-bottom: 40px;
    }

    .news-card {
        background: #fff;
        border-radius: 16px;
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .news-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
    }

    .news-card-image {
        height: 200px;
        overflow: hidden;
    }

    .news-card-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .news-card:hover .news-card-image img {
        transform: scale(1.05);
    }

    .news-card-content {
        padding: 20px;
    }

    .news-card-badges {
        display: flex;
        gap: 8px;
        margin-bottom: 12px;
    }

    .badge-news {
        background: var(--primary-lime);
        color: var(--primary-dark);
        font-size: 0.7rem;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 4px;
    }

    .badge-category {
        background: var(--bg-light);
        color: var(--text-gray);
        font-size: 0.7rem;
        font-weight: 500;
        padding: 4px 10px;
        border-radius: 4px;
    }

    .news-card-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--text-dark);
        margin-bottom: 8px;
        line-height: 1.4;
    }

    .news-card-excerpt {
        font-size: 0.9rem;
        color: var(--text-gray);
        margin-bottom: 16px;
        line-height: 1.5;
    }

    .news-card-link {
        color: var(--text-dark);
        font-size: 0.9rem;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: color 0.3s ease;
    }

    .news-card-link:hover {
        color: var(--primary-dark);
    }

    /* Events Section */
    .events-section {
        padding: 80px 0;
        background: #fff;
    }

    .events-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 24px;
        margin-bottom: 40px;
    }

    .event-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .event-card:hover {
        border-color: var(--primary-lime);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
    }

    .event-card-image {
        height: 180px;
        overflow: hidden;
        position: relative;
    }

    .event-card-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .event-badge {
        position: absolute;
        top: 12px;
        left: 12px;
        background: var(--primary-lime);
        color: var(--primary-dark);
        font-size: 0.7rem;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 4px;
    }

    .event-card-content {
        padding: 20px;
    }

    .event-card-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--text-dark);
        margin-bottom: 8px;
    }

    .event-card-excerpt {
        font-size: 0.85rem;
        color: var(--text-gray);
        margin-bottom: 16px;
        line-height: 1.5;
    }

    .event-card-link {
        color: var(--text-dark);
        font-size: 0.85rem;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .event-card-link:hover {
        color: var(--primary-dark);
    }

    /* Academy Life Section */
    .academy-life-section {
        padding: 80px 0;
        background: var(--bg-light);
    }

    .academy-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 24px;
    }

    .academy-card {
        position: relative;
        border-radius: 16px;
        overflow: hidden;
        height: 280px;
    }

    .academy-card img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .academy-card:hover img {
        transform: scale(1.05);
    }

    .academy-card-overlay {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);
        padding: 24px;
        color: #fff;
    }

    .academy-card-title {
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 8px;
    }

    .academy-card-text {
        font-size: 0.85rem;
        opacity: 0.9;
        margin-bottom: 12px;
        line-height: 1.5;
    }

    .academy-card-link {
        color: var(--primary-lime);
        font-size: 0.85rem;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    /* FAQ Section */
    .faq-section {
        padding: 90px 0;
        background: var(--bg-light, #f8f9fa);
    }

    .faq-container {
        max-width: 820px;
        margin: 0 auto;
    }

    .faq-item {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        margin-bottom: 14px;
        overflow: hidden;
        transition: border-color 0.25s ease, box-shadow 0.25s ease, transform 0.25s ease;
    }

    .faq-item:hover {
        border-color: var(--primary-lime, #C8E637);
        box-shadow: 0 6px 18px rgba(200, 230, 55, 0.18);
        transform: translateY(-1px);
    }

    .faq-item.active {
        border-color: var(--primary-lime, #C8E637);
        box-shadow: 0 10px 28px rgba(200, 230, 55, 0.22);
    }

    .faq-question {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 18px;
        padding: 22px 26px;
        cursor: pointer;
        user-select: none;
        background: transparent;
        transition: background 0.25s ease;
    }

    .faq-question:hover {
        background: rgba(200, 230, 55, 0.06);
    }

    .faq-question h4 {
        flex: 1;
        font-size: 1rem;
        font-weight: 600;
        color: var(--text-dark, #1a1a1a);
        margin: 0;
        line-height: 1.5;
    }

    .faq-question i {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background: rgba(200, 230, 55, 0.18);
        color: var(--primary-dark, #1a1a2e);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.85rem;
        flex-shrink: 0;
        transition: background 0.3s ease, color 0.3s ease, transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .faq-item.active .faq-question i {
        background: var(--primary-lime, #C8E637);
        color: var(--primary-dark, #1a1a2e);
        transform: rotate(180deg);
    }

    .faq-answer {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .faq-item.active .faq-answer {
        max-height: 1500px;
    }

    .faq-answer p {
        padding: 4px 26px 24px;
        color: var(--text-gray, #6b7280);
        font-size: 0.95rem;
        line-height: 1.75;
        margin: 0;
    }

    @media (max-width: 576px) {
        .faq-section { padding: 60px 0; }
        .faq-question { padding: 18px 20px; gap: 12px; }
        .faq-question h4 { font-size: 0.95rem; }
        .faq-question i { width: 32px; height: 32px; font-size: 0.75rem; }
        .faq-answer p { padding: 2px 20px 20px; font-size: 0.9rem; }
    }

    /* Partners Section */
    .partners-section {
        padding: 60px 0;
        background: var(--bg-light);
    }

    .partners-grid {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 48px;
        flex-wrap: wrap;
    }

    .partner-logo {
        height: 40px;
        opacity: 0.6;
        transition: opacity 0.3s ease;
        filter: grayscale(100%);
    }

    .partner-logo:hover {
        opacity: 1;
        filter: grayscale(0%);
    }

    /* Responsive */
    @media (max-width: 1199px) {
        .stat-cards {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 991px) {
        .hero-title {
            font-size: 2.5rem;
        }

        .news-grid,
        .events-grid,
        .academy-grid {
            grid-template-columns: 1fr;
        }

        .quick-links-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 767px) {
        .hero-slider {
            min-height: 550px;
        }

        .hero-section {
            min-height: 500px;
        }

        .hero-title {
            font-size: 2rem;
        }

        .stat-cards {
            grid-template-columns: 1fr;
        }

        .section-title {
            font-size: 1.75rem;
        }

        .hero-buttons {
            flex-direction: column;
        }

        .btn-lime,
        .btn-outline-white,
        .btn-dark {
            width: 100%;
            justify-content: center;
        }

        .slider-arrow {
            width: 40px;
            height: 40px;
            font-size: 1rem;
        }

        .slider-arrow.prev {
            left: 15px;
        }

        .slider-arrow.next {
            right: 15px;
        }

        .slider-nav {
            bottom: 25px;
        }
    }
</style>
@endsection

@section('content')
<!-- Hero Slider -->
<section class="hero-slider">
    <!-- Slide 1 -->
    <div class="hero-slide active" data-slide="0">
        <div class="hero-slide-bg" style="background-image: url('{{ $slide1Image }}');"></div>
        <div class="container">
            <div class="row">
                <div class="col-lg-7 hero-slide-content">
                    <div class="hero-badge">
                        <div class="hero-badge-icon">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                        <span>{{ $getContent('slide1_badge', 'HOSPITALITY MANAGEMENT') }}</span>
                    </div>
                    <h1 class="hero-title">
                        {{ $getContent('slide1_title', "Xalqaro Turizm va Mehmondo'stlik Akademiyasi") }}
                    </h1>
                    <p class="hero-subtitle">
                        {{ $getContent('slide1_subtitle', "UN Tourism bilan hamkorlikda zamonaviy ta'lim. Xalqaro sertifikatlar va jahon miqyosidagi karyera imkoniyatlari.") }}
                    </p>
                    <div class="hero-buttons">
                        <a href="/register" class="btn-lime">
                            <i class="fas fa-user-plus"></i>
                            {{ $getContent('slide1_btn1', "Ro'yxatdan o'tish") }}
                        </a>
                        <a href="/about" class="btn-outline-white">
                            {{ $getContent('slide1_btn2', 'Batafsil') }}
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Slide 2 -->
    <div class="hero-slide" data-slide="1">
        <div class="hero-slide-bg" style="background-image: url('{{ $slide2Image }}');"></div>
        <div class="container">
            <div class="row">
                <div class="col-lg-7 hero-slide-content">
                    <div class="hero-badge">
                        <div class="hero-badge-icon">
                            <i class="fas fa-hotel"></i>
                        </div>
                        <span>{{ $getContent('slide2_badge', 'MEHMONXONA BIZNESI') }}</span>
                    </div>
                    <h1 class="hero-title">
                        {{ $getContent('slide2_title', 'Mehmonxona va Restoran Menejment') }}
                    </h1>
                    <p class="hero-subtitle">
                        {{ $getContent('slide2_subtitle', "5 yulduzli mehmonxonalarda amaliyot. Front Office, Housekeeping va F&B bo'limlari bo'yicha chuqur bilimlar.") }}
                    </p>
                    <div class="hero-buttons">
                        <a href="/programs" class="btn-lime">
                            <i class="fas fa-book-open"></i>
                            {{ $getContent('slide2_btn1', 'Dasturlar') }}
                        </a>
                        <a href="/contact" class="btn-outline-white">
                            {{ $getContent('slide2_btn2', "Bog'lanish") }}
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Slide 3 -->
    <div class="hero-slide" data-slide="2">
        <div class="hero-slide-bg" style="background-image: url('{{ $slide3Image }}');"></div>
        <div class="container">
            <div class="row">
                <div class="col-lg-7 hero-slide-content">
                    <div class="hero-badge">
                        <div class="hero-badge-icon">
                            <i class="fas fa-globe-americas"></i>
                        </div>
                        <span>{{ $getContent('slide3_badge', 'TURIZM MENEJMENT') }}</span>
                    </div>
                    <h1 class="hero-title">
                        {{ $getContent('slide3_title', 'Turizm va Ekskursiya Xizmatlari') }}
                    </h1>
                    <p class="hero-subtitle">
                        {{ $getContent('slide3_subtitle', "O'zbekiston va jahon bo'ylab turistik marshrutlar. Gid tayyorlash, destinatsiya boshqaruvi va turizm marketingi.") }}
                    </p>
                    <div class="hero-buttons">
                        <a href="/teachers" class="btn-lime">
                            <i class="fas fa-users"></i>
                            {{ $getContent('slide3_btn1', "O'qituvchilar") }}
                        </a>
                        <a href="/news" class="btn-outline-white">
                            {{ $getContent('slide3_btn2', 'Yangiliklar') }}
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Slider Navigation Arrows -->
    <button class="slider-arrow prev" onclick="changeSlide(-1)">
        <i class="fas fa-chevron-left"></i>
    </button>
    <button class="slider-arrow next" onclick="changeSlide(1)">
        <i class="fas fa-chevron-right"></i>
    </button>

    <!-- Slider Dots -->
    <div class="slider-nav">
        <button class="slider-dot active" onclick="goToSlide(0)"></button>
        <button class="slider-dot" onclick="goToSlide(1)"></button>
        <button class="slider-dot" onclick="goToSlide(2)"></button>
    </div>
</section>

<script>
    // Minimalistic Hero Slider
    let currentSlide = 0;
    const totalSlides = 3;
    let slideInterval;

    function showSlide(index) {
        const slides = document.querySelectorAll('.hero-slide');
        const dots = document.querySelectorAll('.slider-dot');

        if (index >= totalSlides) currentSlide = 0;
        if (index < 0) currentSlide = totalSlides - 1;

        slides.forEach((slide, i) => {
            slide.classList.remove('active');
            if (i === currentSlide) slide.classList.add('active');
        });

        dots.forEach((dot, i) => {
            dot.classList.remove('active');
            if (i === currentSlide) dot.classList.add('active');
        });
    }

    function changeSlide(direction) {
        currentSlide += direction;
        showSlide(currentSlide);
        resetInterval();
    }

    function goToSlide(index) {
        currentSlide = index;
        showSlide(currentSlide);
        resetInterval();
    }

    function resetInterval() {
        clearInterval(slideInterval);
        slideInterval = setInterval(() => {
            currentSlide++;
            showSlide(currentSlide);
        }, 6000);
    }

    // Auto-play slider
    document.addEventListener('DOMContentLoaded', function() {
        slideInterval = setInterval(() => {
            currentSlide++;
            showSlide(currentSlide);
        }, 6000);
    });
</script>

<!-- Statistics Section -->
<section class="statistics-section">
    <div class="container">
        <div class="text-center mb-5">
            <p class="section-label">{{ $getContent('stats_label', 'Statistics') }}</p>
            <h2 class="section-title">{{ $getContent('stats_title', 'Bizning yutuqlarimiz') }}</h2>
        </div>

        <div class="stat-cards">
            <div class="stat-card" data-aos="fade-up" data-aos-delay="100">
                <div class="stat-number">{{ $getContent('stat1_number', '150') }}+</div>
                <div class="stat-label">{{ $getContent('stat1_label', 'Successful Projects Delivered') }}</div>
            </div>
            <div class="stat-card" data-aos="fade-up" data-aos-delay="200">
                <div class="stat-number">{{ $getContent('stat2_number', '97') }}%</div>
                <div class="stat-label">{{ $getContent('stat2_label', 'Client Satisfaction Rate') }}</div>
            </div>
            <div class="stat-card" data-aos="fade-up" data-aos-delay="300">
                <div class="stat-number">{{ $getContent('stat3_number', '10') }}+</div>
                <div class="stat-label">{{ $getContent('stat3_label', 'Years of Industry Experience') }}</div>
            </div>
            <div class="stat-card" data-aos="fade-up" data-aos-delay="400">
                <div class="stat-number">{{ $getContent('stat4_number', '5') }}</div>
                <div class="stat-label">{{ $getContent('stat4_label', 'Continents Served Globally') }}</div>
            </div>
        </div>
    </div>
</section>

<!-- Quick Links Section -->
<section class="quick-links-section">
    <div class="container">
        <div class="quick-links-grid">
            <!-- HEMIS tizimi -->
            <a href="{{ route('login') }}" class="quick-link-card" data-aos="fade-up" data-aos-delay="100">
                <div class="quick-link-icon">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <div class="quick-link-content">
                    <h4>{{ $getContent('quick_link1_title', 'HEMIS tizimiga kirish') }}</h4>
                    <p>{{ $getContent('quick_link1_desc', "Talabalar uchun HEMIS tizimi. Baholar, jadval va o'quv ma'lumotlari.") }}</p>
                </div>
                <div class="quick-link-arrow">
                    <i class="fas fa-arrow-right"></i>
                </div>
            </a>

            <!-- Kutubxona -->
            <a href="{{ route('public.library') }}" class="quick-link-card" data-aos="fade-up" data-aos-delay="200">
                <div class="quick-link-icon">
                    <i class="fas fa-book-open"></i>
                </div>
                <div class="quick-link-content">
                    <h4>{{ $getContent('quick_link2_title', 'Elektron kutubxona') }}</h4>
                    <p>{{ $getContent('quick_link2_desc', "Kitoblar, darsliklar va ilmiy adabiyotlar. Bepul foydalaning.") }}</p>
                </div>
                <div class="quick-link-arrow">
                    <i class="fas fa-arrow-right"></i>
                </div>
            </a>

            <!-- Onlayn ariza -->
            <a href="{{ route('contact') }}" class="quick-link-card" data-aos="fade-up" data-aos-delay="300">
                <div class="quick-link-icon">
                    <i class="fas fa-file-alt"></i>
                </div>
                <div class="quick-link-content">
                    <h4>{{ $getContent('quick_link3_title', 'Onlayn ariza qoldirish') }}</h4>
                    <p>{{ $getContent('quick_link3_desc', "Qabul uchun ariza topshirish. Tez va qulay ro'yxatdan o'tish.") }}</p>
                </div>
                <div class="quick-link-arrow">
                    <i class="fas fa-arrow-right"></i>
                </div>
            </a>

            <!-- Virtual tur -->
            <a href="{{ route('campus-tour.public.virtual-tour') }}" class="quick-link-card" data-aos="fade-up" data-aos-delay="400">
                <div class="quick-link-icon">
                    <i class="fas fa-vr-cardboard"></i>
                </div>
                <div class="quick-link-content">
                    <h4>Virtual tur</h4>
                    <p>Akademiyamiz bilan 360° virtual sayohat. Bino va auditoriyalar.</p>
                </div>
                <div class="quick-link-arrow">
                    <i class="fas fa-arrow-right"></i>
                </div>
            </a>

            <!-- Davlat ramzlari -->
            <a href="{{ route('state-symbols') }}" class="quick-link-card" data-aos="fade-up" data-aos-delay="500">
                <div class="quick-link-icon">
                    <i class="fas fa-flag"></i>
                </div>
                <div class="quick-link-content">
                    <h4>Davlat ramzlari</h4>
                    <p>O'zbekiston Respublikasi bayrog'i, gerbi va madhiyasi.</p>
                </div>
                <div class="quick-link-arrow">
                    <i class="fas fa-arrow-right"></i>
                </div>
            </a>

            <!-- Onlayn Qabul -->
            <a href="{{ route('admission.apply') }}" class="quick-link-card" data-aos="fade-up" data-aos-delay="600" style="background: linear-gradient(135deg, #0066CC 0%, #0052A3 100%); color: white;">
                <div class="quick-link-icon" style="background: rgba(255, 255, 255, 0.2);">
                    <i class="fas fa-user-graduate" style="color: white;"></i>
                </div>
                <div class="quick-link-content">
                    <h4 style="color: white;">Onlayn Qabul</h4>
                    <p style="color: rgba(255, 255, 255, 0.9);">Tourism Academy 2025 dasturiga ariza topshiring.</p>
                </div>
                <div class="quick-link-arrow">
                    <i class="fas fa-arrow-right" style="color: white;"></i>
                </div>
            </a>
        </div>
    </div>
</section>

<!-- News Section -->
<section class="news-section">
    <div class="container">
        <div class="text-center mb-5">
            <p class="section-label">{{ $getContent('news_label', 'New Academy') }}</p>
            <h2 class="section-title">{{ $getContent('news_title', 'News from our academy') }}</h2>
            <p class="section-subtitle">{{ $getContent('news_subtitle', 'Latest events and news') }}</p>
        </div>

        <div class="news-grid">
            @forelse($latest_news ?? [] as $index => $news)
                <div class="news-card" data-aos="fade-up" data-aos-delay="{{ ($index + 1) * 100 }}">
                    <div class="news-card-image">
                        @if($news->featured_image)
                            <img src="{{ asset($news->featured_image) }}" alt="{{ $news->title_uz }}">
                        @else
                            <img src="https://images.unsplash.com/photo-1523050854058-8df90110c9f1?w=600" alt="{{ $news->title_uz }}">
                        @endif
                    </div>
                    <div class="news-card-content">
                        <div class="news-card-badges">
                            <span class="badge-news">News</span>
                            @if($news->category)
                                <span class="badge-category">{{ $news->category->name_uz ?? 'Academy' }}</span>
                            @endif
                        </div>
                        <h3 class="news-card-title">
                            {{ html_entity_decode($lang == 'uz' ? $news->title_uz : ($lang == 'ru' ? ($news->title_ru ?? $news->title_uz) : ($news->title_en ?? $news->title_uz)), ENT_QUOTES | ENT_HTML5, 'UTF-8') }}
                        </h3>
                        <p class="news-card-excerpt">
                            {{ Str::limit(strip_tags($news->content_uz), 100) }}
                        </p>
                        <a href="{{ route('news.show', $news->slug) }}" class="news-card-link">
                            {{ $getContent('btn_learn_more', 'Batafsil') }} <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            @empty
                @for($i = 0; $i < 4; $i++)
                <div class="news-card" data-aos="fade-up" data-aos-delay="{{ ($i + 1) * 100 }}">
                    <div class="news-card-image">
                        <img src="https://images.unsplash.com/photo-1523050854058-8df90110c9f1?w=600" alt="News">
                    </div>
                    <div class="news-card-content">
                        <div class="news-card-badges">
                            <span class="badge-news">News</span>
                            <span class="badge-category">Academy</span>
                        </div>
                        <h3 class="news-card-title">What is Lorem Ipsum?</h3>
                        <p class="news-card-excerpt">
                            Lorem Ipsum is simply dummy text of the printing and typesetting industry.
                        </p>
                        <a href="#" class="news-card-link">
                            {{ $getContent('btn_learn_more', 'Batafsil') }} <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
                @endfor
            @endforelse
        </div>

        <div class="text-center">
            <a href="{{ route('blog') }}" class="btn-dark">
                {{ $getContent('btn_all_news', 'Barcha yangiliklar') }}
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
</section>

<!-- Events Section -->
<section class="events-section">
    <div class="container">
        <div class="text-center mb-5">
            <p class="section-label">{{ $getContent('events_label', 'What is Lorem Ipsum') }}</p>
            <h2 class="section-title">{{ $getContent('events_title', 'Upcoming events') }}</h2>
            <p class="section-subtitle">{{ $getContent('events_subtitle', 'Lorem Ipsum is simply dummy text of the printing and typesetting industry.') }}</p>
        </div>

        <div class="events-grid">
            @forelse($upcoming_events ?? [] as $index => $event)
                <div class="event-card" data-aos="fade-up" data-aos-delay="{{ ($index + 1) * 100 }}">
                    <div class="event-card-image">
                        @if($event->featured_image)
                            @php
                                $evtImg = str_starts_with($event->featured_image, 'http')
                                    ? $event->featured_image
                                    : (str_starts_with($event->featured_image, 'images/')
                                        ? asset($event->featured_image)
                                        : asset('storage/' . $event->featured_image));
                            @endphp
                            <img src="{{ $evtImg }}" alt="{{ $event->title_uz }}">
                        @else
                            <img src="https://images.unsplash.com/photo-1540575467063-178a50c2df87?w=600" alt="{{ $event->title_uz }}">
                        @endif
                        <span class="event-badge">Events</span>
                    </div>
                    <div class="event-card-content">
                        <h3 class="event-card-title">
                            {{ html_entity_decode($lang == 'uz' ? $event->title_uz : ($lang == 'ru' ? ($event->title_ru ?? $event->title_uz) : ($event->title_en ?? $event->title_uz)), ENT_QUOTES | ENT_HTML5, 'UTF-8') }}
                        </h3>
                        <p class="event-card-excerpt">
                            {{ Str::limit(strip_tags($event->description_uz ?? ''), 80) }}
                        </p>
                        <a href="{{ route('news.show', $event->id) }}" class="event-card-link">
                            {{ $getContent('btn_learn_more', 'Batafsil') }} <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            @empty
                @for($i = 0; $i < 4; $i++)
                <div class="event-card" data-aos="fade-up" data-aos-delay="{{ ($i + 1) * 100 }}">
                    <div class="event-card-image">
                        <img src="https://images.unsplash.com/photo-1540575467063-178a50c2df87?w=600" alt="Event">
                        <span class="event-badge">Events</span>
                    </div>
                    <div class="event-card-content">
                        <h3 class="event-card-title">What is Lorem Ipsum?</h3>
                        <p class="event-card-excerpt">
                            Lorem Ipsum is simply dummy text of the printing and typesetting industry.
                        </p>
                        <a href="#" class="event-card-link">
                            {{ $getContent('btn_learn_more', 'Batafsil') }} <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
                @endfor
            @endforelse
        </div>

        <div class="text-center">
            <a href="#" class="btn-lime">
                {{ $getContent('btn_read_more', "Ko'proq o'qish") }}
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
</section>

<!-- Academy Life Section -->
<section class="academy-life-section">
    <div class="container">
        <div class="text-center mb-5">
            <p class="section-label">{{ $getContent('academy_label', 'What is Lorem Ipsum?') }}</p>
            <h2 class="section-title">{{ $getContent('academy_title', 'Academy life') }}</h2>
        </div>

        <div class="academy-grid">
            @php
                $galleryImages = [
                    'https://images.unsplash.com/photo-1562774053-701939374585?w=600',
                    'https://images.unsplash.com/photo-1521587760476-6c12a4b040da?w=600',
                    'https://images.unsplash.com/photo-1531482615713-2afd69097998?w=600',
                    'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=600'
                ];
            @endphp
            @foreach($galleryImages as $index => $img)
            <div class="academy-card" data-aos="fade-up" data-aos-delay="{{ ($index + 1) * 100 }}">
                @php
                    $galleryContent = $homepageContents->get("gallery_image_" . ($index + 1));
                    $rawValue = $galleryContent && $galleryContent->value_uz ? trim(ltrim($galleryContent->value_uz, '/')) : '';
                    if ($rawValue === '') {
                        $imgUrl = $img;
                    } elseif (Str::startsWith($rawValue, 'http')) {
                        $imgUrl = $rawValue;
                    } elseif (Str::startsWith($rawValue, 'uploads/')) {
                        $imgUrl = asset($rawValue);
                    } else {
                        $imgUrl = asset('storage/' . $rawValue);
                    }
                @endphp
                <img src="{{ $imgUrl }}" alt="Academy Life" loading="lazy" onerror="this.onerror=null;this.src='{{ $img }}';">
                <div class="academy-card-overlay">
                    <h3 class="academy-card-title">{{ $getContent("gallery_title_" . ($index + 1), 'What is Lorem Ipsum?') }}</h3>
                    <p class="academy-card-text">{{ $getContent("gallery_text_" . ($index + 1), 'Lorem Ipsum is simply dummy text of the printing and typesetting industry.') }}</p>
                    <a href="#" class="academy-card-link">
                        {{ $getContent('btn_learn_more', 'Batafsil') }} <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="faq-section">
    <div class="container">
        <div class="text-center mb-5">
            <p class="section-label">{{ $getContent('faq_label', 'Frequently asked questions') }}</p>
            <h2 class="section-title">{{ $getContent('faq_title', 'Your Questions, Answered') }}</h2>
            <p class="section-subtitle">{{ $getContent('faq_subtitle', 'Here are answers to some common questions about our services and process.') }}</p>
        </div>

        <div class="faq-container">
            <div class="faq-item active">
                <div class="faq-question">
                    <h4>{{ $getContent('faq1_question', 'What industries do you work with?') }}</h4>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    <p>{{ $getContent('faq1_answer', 'We partner with startups, small businesses, and enterprises across various industries like technology, healthcare, and e-commerce.') }}</p>
                </div>
            </div>
            <div class="faq-item">
                <div class="faq-question">
                    <h4>{{ $getContent('faq2_question', 'How long does a typical project take?') }}</h4>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    <p>{{ $getContent('faq2_answer', 'Project timelines vary depending on scope and complexity. Most projects are completed within 4-12 weeks.') }}</p>
                </div>
            </div>
            <div class="faq-item">
                <div class="faq-question">
                    <h4>{{ $getContent('faq3_question', 'Do you offer post launch support?') }}</h4>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="faq-answer">
                    <p>{{ $getContent('faq3_answer', 'Yes, we provide ongoing support and maintenance services to ensure your project runs smoothly after launch.') }}</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Partners Section -->
<section class="partners-section">
    <div class="container">
        <div class="text-center mb-4">
            <p class="section-label">{{ $getContent('partners_label', 'Our partners') }}</p>
        </div>

        <div class="partners-grid">
            @for($i = 1; $i <= 6; $i++)
                @php
                    $partnerLogo = $homepageContents->get("partner{$i}_logo");
                    $partnerName = $homepageContents->get("partner{$i}_name");
                    $rawLogo = $partnerLogo && $partnerLogo->value_uz ? trim(ltrim($partnerLogo->value_uz, '/')) : '';
                    if ($rawLogo === '') {
                        $logoUrl = null;
                    } elseif (Str::startsWith($rawLogo, 'http')) {
                        $logoUrl = $rawLogo;
                    } elseif (Str::startsWith($rawLogo, 'uploads/')) {
                        $logoUrl = asset($rawLogo);
                    } else {
                        $logoUrl = asset('storage/' . $rawLogo);
                    }
                    $name = $partnerName ? ($partnerName->$langField ?? $partnerName->value_uz ?? "Partner {$i}") : "Partner {$i}";
                    $partnerFallback = 'data:image/svg+xml;utf8,' . rawurlencode('<svg xmlns="http://www.w3.org/2000/svg" width="120" height="40"><rect width="120" height="40" fill="#e5e7eb"/><text x="60" y="24" font-family="Arial" font-size="11" fill="#9ca3af" text-anchor="middle">' . e(Str::limit($name, 15)) . '</text></svg>');
                @endphp
                @if($logoUrl)
                    <img src="{{ $logoUrl }}" alt="{{ $name }}" class="partner-logo" title="{{ $name }}" loading="lazy" onerror="this.onerror=null;this.src='{{ $partnerFallback }}';">
                @else
                    <div style="width: 120px; height: 40px; background: #e5e7eb; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #9ca3af; font-size: 0.8rem;" title="{{ $name }}">{{ Str::limit($name, 15) }}</div>
                @endif
            @endfor
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    (function initHomepageFaq() {
        function init() {
            const items = document.querySelectorAll('.faq-section .faq-item');
            if (!items.length) return;

            const setOpen = (item, open) => {
                const ans = item.querySelector('.faq-answer');
                if (!ans) return;
                if (open) {
                    item.classList.add('active');
                    ans.style.maxHeight = ans.scrollHeight + 'px';
                } else {
                    item.classList.remove('active');
                    ans.style.maxHeight = '0px';
                }
            };

            items.forEach(item => {
                const question = item.querySelector('.faq-question');
                if (!question) return;

                if (item.classList.contains('active')) {
                    requestAnimationFrame(() => setOpen(item, true));
                }

                question.addEventListener('click', e => {
                    e.preventDefault();
                    const wasActive = item.classList.contains('active');
                    items.forEach(other => setOpen(other, false));
                    if (!wasActive) setOpen(item, true);
                });
            });

            let resizeTimer;
            window.addEventListener('resize', () => {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(() => {
                    items.forEach(item => {
                        if (item.classList.contains('active')) {
                            const ans = item.querySelector('.faq-answer');
                            if (ans) ans.style.maxHeight = ans.scrollHeight + 'px';
                        }
                    });
                }, 150);
            });
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', init);
        } else {
            init();
        }
    })();
</script>
@endpush
