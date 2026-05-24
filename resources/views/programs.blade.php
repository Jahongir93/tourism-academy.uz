@extends(\App\Helpers\TemplateHelper::getLayout())

@php
    use App\Models\CmsContent;

    // Get programs page content from CMS
    $programsContents = CmsContent::where('section', 'programs')->get()->keyBy('key');

    // Get current language
    $lang = app()->getLocale() ?? 'uz';
    $langField = 'value_' . $lang;

    // Helper function to get content
    $getContent = function($key, $default = '') use ($programsContents, $langField) {
        $content = $programsContents->get($key);
        return $content ? ($content->$langField ?? $content->value_uz ?? $default) : $default;
    };
@endphp

@section('title', $getContent('programs_page_title', "O'quv dasturlari") . ' - Tourism Academy')
@section('description', $getContent('programs_page_description', "Zamonaviy ta'lim dasturlari va professional kadrlar tayyorlash"))

@section('content')
<style>
    /* Hero Section */
    .programs-hero {
        min-height: 55vh;
        padding-top: 140px;
        padding-bottom: 80px;
        background: linear-gradient(135deg, #1e3a5f 0%, #0d1b2a 50%, #1b263b 100%);
        display: flex;
        align-items: center;
        position: relative;
        overflow: hidden;
    }

    .programs-hero::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background:
            radial-gradient(ellipse at 20% 30%, rgba(59, 130, 246, 0.2) 0%, transparent 50%),
            radial-gradient(ellipse at 80% 70%, rgba(147, 51, 234, 0.15) 0%, transparent 50%),
            radial-gradient(ellipse at 50% 50%, rgba(16, 185, 129, 0.1) 0%, transparent 60%);
        pointer-events: none;
    }

    .hero-badge {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        background: linear-gradient(135deg, #3b82f6, #8b5cf6);
        color: white;
        padding: 12px 28px;
        border-radius: 50px;
        font-weight: 700;
        font-size: 0.9rem;
        margin-bottom: 25px;
        box-shadow: 0 8px 25px rgba(59, 130, 246, 0.35);
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .programs-hero h1 {
        color: #ffffff;
        font-size: 3rem;
        font-weight: 800;
        margin-bottom: 20px;
        text-shadow: 0 4px 30px rgba(0, 0, 0, 0.3);
        position: relative;
        z-index: 2;
        line-height: 1.2;
    }

    .programs-hero p {
        color: #cbd5e1;
        font-size: 1.2rem;
        max-width: 700px;
        position: relative;
        z-index: 2;
        line-height: 1.7;
    }

    .hero-breadcrumb {
        margin-top: 30px;
        position: relative;
        z-index: 2;
    }

    .hero-breadcrumb a {
        color: rgba(255, 255, 255, 0.7);
        text-decoration: none;
        transition: color 0.3s;
    }

    .hero-breadcrumb a:hover {
        color: #3b82f6;
    }

    .hero-breadcrumb span {
        color: rgba(255, 255, 255, 0.5);
        margin: 0 12px;
    }

    .hero-breadcrumb .current {
        color: #3b82f6;
        font-weight: 600;
    }

    /* Stats Section */
    .stats-section {
        background: linear-gradient(135deg, #1e3a5f 0%, #0d1b2a 100%);
        padding: 60px 0;
        position: relative;
    }

    .stat-card {
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.15);
        border-radius: 20px;
        padding: 30px;
        text-align: center;
        backdrop-filter: blur(10px);
        transition: all 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        background: rgba(255, 255, 255, 0.15);
    }

    .stat-icon {
        width: 70px;
        height: 70px;
        background: linear-gradient(135deg, #3b82f6, #8b5cf6);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        font-size: 1.8rem;
        color: white;
        box-shadow: 0 10px 30px rgba(59, 130, 246, 0.3);
    }

    .stat-value {
        font-size: 2.5rem;
        font-weight: 800;
        color: white;
        margin-bottom: 5px;
    }

    .stat-label {
        color: #cbd5e1;
        font-size: 1rem;
    }

    /* Programs Section */
    .programs-section {
        padding: 80px 0;
        background: #f8fafc;
    }

    .section-header {
        text-align: center;
        margin-bottom: 50px;
    }

    .section-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: rgba(59, 130, 246, 0.1);
        color: #3b82f6;
        padding: 10px 24px;
        border-radius: 50px;
        font-weight: 700;
        font-size: 0.85rem;
        margin-bottom: 20px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .section-title {
        font-size: 2.5rem;
        font-weight: 800;
        color: #1e3a5f;
        margin-bottom: 15px;
    }

    .section-subtitle {
        color: #64748b;
        font-size: 1.1rem;
        max-width: 600px;
        margin: 0 auto;
        line-height: 1.7;
    }

    /* Program Cards */
    .program-card {
        background: white;
        border-radius: 20px;
        padding: 30px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.06);
        transition: all 0.4s ease;
        height: 100%;
        position: relative;
        overflow: hidden;
        border-left: 4px solid #3b82f6;
    }

    .program-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 50px rgba(59, 130, 246, 0.15);
    }

    .program-number {
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.3rem;
        font-weight: 700;
        margin-bottom: 20px;
        box-shadow: 0 5px 15px rgba(59, 130, 246, 0.3);
    }

    .program-card h4 {
        font-size: 1.3rem;
        font-weight: 700;
        color: #1e3a5f;
        margin-bottom: 15px;
    }

    .program-date {
        display: inline-block;
        background: rgba(59, 130, 246, 0.1);
        color: #3b82f6;
        padding: 8px 16px;
        border-radius: 50px;
        font-size: 0.85rem;
        font-weight: 600;
        margin-bottom: 15px;
    }

    .program-card p {
        color: #64748b;
        line-height: 1.7;
        margin-bottom: 20px;
    }

    .program-topics {
        background: #f8fafc;
        padding: 15px;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
    }

    .program-topics-label {
        color: #3b82f6;
        font-weight: 600;
        font-size: 0.9rem;
        margin-right: 10px;
    }

    .program-topics-badge {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: white;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    /* Benefits Section */
    .benefits-section {
        padding: 80px 0;
        background: white;
    }

    .benefit-card {
        background: #f8fafc;
        border-radius: 20px;
        padding: 35px 25px;
        text-align: center;
        transition: all 0.4s ease;
        height: 100%;
        border: 2px solid transparent;
    }

    .benefit-card:hover {
        transform: translateY(-8px);
        border-color: #3b82f6;
        background: white;
        box-shadow: 0 20px 50px rgba(59, 130, 246, 0.12);
    }

    .benefit-icon {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        font-size: 2rem;
    }

    .benefit-icon.blue {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
        box-shadow: 0 10px 25px rgba(59, 130, 246, 0.25);
    }

    .benefit-icon.purple {
        background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
        color: white;
        box-shadow: 0 10px 25px rgba(139, 92, 246, 0.25);
    }

    .benefit-icon.green {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        box-shadow: 0 10px 25px rgba(16, 185, 129, 0.25);
    }

    .benefit-card h5 {
        font-size: 1.1rem;
        font-weight: 700;
        color: #1e3a5f;
        margin-bottom: 12px;
    }

    .benefit-card p {
        color: #64748b;
        font-size: 0.95rem;
        line-height: 1.6;
    }

    /* CTA Section */
    .cta-section {
        background: linear-gradient(135deg, #1e3a5f 0%, #0d1b2a 100%);
        padding: 80px 0;
        position: relative;
        overflow: hidden;
    }

    .cta-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background:
            radial-gradient(ellipse at 30% 50%, rgba(59, 130, 246, 0.15) 0%, transparent 50%),
            radial-gradient(ellipse at 70% 50%, rgba(139, 92, 246, 0.1) 0%, transparent 50%);
    }

    .cta-content {
        position: relative;
        z-index: 2;
        text-align: center;
    }

    .cta-title {
        color: white;
        font-size: 2.5rem;
        font-weight: 800;
        margin-bottom: 20px;
    }

    .cta-text {
        color: #cbd5e1;
        font-size: 1.15rem;
        max-width: 600px;
        margin: 0 auto 35px;
    }

    .cta-btn {
        display: inline-flex;
        align-items: center;
        gap: 12px;
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
        padding: 18px 40px;
        border-radius: 50px;
        font-weight: 700;
        font-size: 1.1rem;
        text-decoration: none;
        box-shadow: 0 15px 40px rgba(59, 130, 246, 0.4);
        transition: all 0.3s ease;
    }

    .cta-btn:hover {
        transform: translateY(-5px) scale(1.02);
        box-shadow: 0 20px 50px rgba(59, 130, 246, 0.5);
        color: white;
    }

    /* Responsive */
    @media (max-width: 992px) {
        .programs-hero h1 {
            font-size: 2.3rem;
        }
        .section-title {
            font-size: 2rem;
        }
    }

    @media (max-width: 768px) {
        .programs-hero {
            padding-top: 120px;
            min-height: 45vh;
        }
        .programs-hero h1 {
            font-size: 1.8rem;
        }
        .programs-hero p {
            font-size: 1rem;
        }
        .stat-value {
            font-size: 2rem;
        }
        .cta-title {
            font-size: 1.8rem;
        }
    }
</style>

<!-- Hero Section -->
<section class="programs-hero">
    <div class="container">
        <div data-aos="fade-up">
            <div class="hero-badge">
                <i class="fas fa-graduation-cap"></i>
                {{ $getContent('programs_hero_badge', "O'QUV DASTURLARI") }}
            </div>
            <h1>{{ $getContent('programs_hero_title', "Professional ta'lim dasturlari") }}</h1>
            <p>{{ $getContent('programs_hero_subtitle', "Zamonaviy texnologiyalar va innovatsion yondashuvlar orqali professional kadrlar tayyorlash. Xalqaro standartlarga mos ta'lim dasturlari.") }}</p>
            <div class="hero-breadcrumb">
                <a href="{{ route('home') }}">{{ $getContent('programs_breadcrumb_home', 'Bosh sahifa') }}</a>
                <span>/</span>
                <span class="current">{{ $getContent('programs_breadcrumb_programs', "O'quv dasturlari") }}</span>
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="stats-section">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-book-open"></i>
                    </div>
                    <div class="stat-value">{{ $getContent('programs_stat1_value', '10+') }}</div>
                    <div class="stat-label">{{ $getContent('programs_stat1_label', "Ta'lim dasturlari") }}</div>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-value">{{ $getContent('programs_stat2_value', '500+') }}</div>
                    <div class="stat-label">{{ $getContent('programs_stat2_label', 'Faol talabalar') }}</div>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-certificate"></i>
                    </div>
                    <div class="stat-value">{{ $getContent('programs_stat3_value', '95%') }}</div>
                    <div class="stat-label">{{ $getContent('programs_stat3_label', 'Ishga joylashish') }}</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Programs Section -->
<section class="programs-section">
    <div class="container">
        <div class="section-header" data-aos="fade-up">
            <div class="section-badge">
                <i class="fas fa-list-alt"></i>
                {{ $getContent('programs_section_badge', 'Dasturlar') }}
            </div>
            <h2 class="section-title">{{ $getContent('programs_section_title', 'Bizning dasturlarimiz') }}</h2>
            <p class="section-subtitle">{{ $getContent('programs_section_subtitle', "Professional bilim va ko'nikmalarni rivojlantirish uchun maxsus ishlab chiqilgan dasturlar") }}</p>
        </div>

        <div class="row g-4">
            @for($i = 1; $i <= 6; $i++)
            @php
                $title = $getContent("program{$i}_title", '');
            @endphp
            @if($title)
            <div class="col-lg-6" data-aos="fade-up" data-aos-delay="{{ ($i % 2) * 100 + 100 }}">
                <div class="program-card">
                    <div class="program-number">{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}</div>
                    <h4>{{ $title }}</h4>
                    @php $dates = $getContent("program{$i}_dates", ''); @endphp
                    @if($dates)
                    <div class="program-date">
                        <i class="fas fa-calendar-alt me-2"></i>{{ $dates }}
                    </div>
                    @endif
                    <p>{{ $getContent("program{$i}_description", '') }}</p>
                    @php $topics = $getContent("program{$i}_topics", ''); @endphp
                    @if($topics)
                    <div class="program-topics">
                        <span class="program-topics-label"><i class="fas fa-book-open me-1"></i>{{ $getContent('programs_topics_label', 'Asosiy mavzular') }}</span>
                        <span class="program-topics-badge">{{ $topics }}</span>
                    </div>
                    @endif
                </div>
            </div>
            @endif
            @endfor
        </div>
    </div>
</section>

<!-- Benefits Section -->
<section class="benefits-section">
    <div class="container">
        <div class="section-header" data-aos="fade-up">
            <div class="section-badge">
                <i class="fas fa-star"></i>
                {{ $getContent('benefits_badge', 'Afzalliklar') }}
            </div>
            <h2 class="section-title">{{ $getContent('benefits_title', "Siz nimalarga ega bo'lasiz?") }}</h2>
            <p class="section-subtitle">{{ $getContent('benefits_subtitle', "Dasturlarni tugatganingizdan so'ng siz quyidagi ko'nikma va imkoniyatlarga ega bo'lasiz") }}</p>
        </div>

        <div class="row g-4">
            @php
                $benefitIcons = ['blue', 'purple', 'green', 'blue', 'purple', 'green'];
                $defaultBenefitIcons = ['fas fa-award', 'fas fa-certificate', 'fas fa-briefcase', 'fas fa-globe', 'fas fa-lightbulb', 'fas fa-users'];
            @endphp
            @for($i = 1; $i <= 6; $i++)
            @php
                $benefitTitle = $getContent("benefit{$i}_title", '');
            @endphp
            @if($benefitTitle)
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="{{ (($i - 1) % 3 + 1) * 100 }}">
                <div class="benefit-card">
                    <div class="benefit-icon {{ $benefitIcons[($i - 1) % 6] }}">
                        <i class="{{ $getContent("benefit{$i}_icon", $defaultBenefitIcons[($i - 1) % 6]) }}"></i>
                    </div>
                    <h5>{{ $benefitTitle }}</h5>
                    <p>{{ $getContent("benefit{$i}_text", '') }}</p>
                </div>
            </div>
            @endif
            @endfor
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section">
    <div class="container">
        <div class="cta-content" data-aos="fade-up">
            <h2 class="cta-title">{{ $getContent('programs_cta_title', "Hoziroq ro'yxatdan o'ting!") }}</h2>
            <p class="cta-text">{{ $getContent('programs_cta_text', "Ta'lim dasturlarimizga qo'shiling va professional karyerangizni boshlang.") }}</p>
            <a href="{{ route('contact') }}" class="cta-btn">
                <i class="fas fa-paper-plane"></i>
                {{ $getContent('programs_cta_button', "Bog'lanish") }}
            </a>
        </div>
    </div>
</section>
@endsection
