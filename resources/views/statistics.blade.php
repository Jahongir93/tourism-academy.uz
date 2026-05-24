@extends(\App\Helpers\TemplateHelper::getLayout())

@php
    use App\Models\CmsContent;

    // Get statistics page content from CMS
    $statsContents = CmsContent::where('section', 'statistics')->get()->keyBy('key');

    // Get current language
    $lang = app()->getLocale() ?? 'uz';
    $langField = 'value_' . $lang;

    // Helper function to get content
    $getContent = function($key, $default = '') use ($statsContents, $langField) {
        $content = $statsContents->get($key);
        return $content ? ($content->$langField ?? $content->value_uz ?? $default) : $default;
    };

    // Calculate totals for summary
    $totalStudents = 0;
    for ($i = 1; $i <= 4; $i++) {
        $totalStudents += (int) $getContent("stats_age{$i}_value", 0);
    }
@endphp

@section('title', $getContent('stats_breadcrumb_current', 'Statistika') . ' - Tourism Academy')
@section('description', $getContent('stats_hero_subtitle', 'Akademiya haqida statistik ma\'lumotlar'))

@section('content')
<style>
    /* Hero Section */
    .stats-hero {
        min-height: 55vh;
        padding-top: 140px;
        padding-bottom: 80px;
        background: linear-gradient(135deg, #1e3a5f 0%, #0d1b2a 50%, #1b263b 100%);
        display: flex;
        align-items: center;
        position: relative;
        overflow: hidden;
    }

    .stats-hero::before {
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

    .stats-hero::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 120px;
        background: linear-gradient(to top, rgba(255,255,255,0.05), transparent);
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

    .stats-hero h1 {
        color: #ffffff;
        font-size: 3.2rem;
        font-weight: 800;
        margin-bottom: 20px;
        text-shadow: 0 4px 30px rgba(0, 0, 0, 0.3);
        position: relative;
        z-index: 2;
        line-height: 1.2;
    }

    .stats-hero p {
        color: #cbd5e1;
        font-size: 1.25rem;
        max-width: 650px;
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

    /* Summary Cards */
    .summary-section {
        margin-top: -60px;
        position: relative;
        z-index: 10;
        padding-bottom: 60px;
    }

    .summary-card {
        background: white;
        border-radius: 20px;
        padding: 30px;
        text-align: center;
        box-shadow: 0 15px 50px rgba(0, 0, 0, 0.1);
        transition: all 0.4s ease;
        border: 1px solid rgba(59, 130, 246, 0.1);
        height: 100%;
    }

    .summary-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 25px 60px rgba(59, 130, 246, 0.15);
    }

    .summary-icon {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        font-size: 2rem;
    }

    .summary-icon.blue {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
        box-shadow: 0 10px 30px rgba(59, 130, 246, 0.3);
    }

    .summary-icon.purple {
        background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
        color: white;
        box-shadow: 0 10px 30px rgba(139, 92, 246, 0.3);
    }

    .summary-icon.green {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        box-shadow: 0 10px 30px rgba(16, 185, 129, 0.3);
    }

    .summary-icon.orange {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: white;
        box-shadow: 0 10px 30px rgba(245, 158, 11, 0.3);
    }

    .summary-number {
        font-size: 3rem;
        font-weight: 800;
        color: #1e3a5f;
        margin-bottom: 5px;
        line-height: 1;
    }

    .summary-label {
        color: #64748b;
        font-size: 1rem;
        font-weight: 600;
    }

    /* Section Styles */
    .stats-section {
        padding: 80px 0;
    }

    .stats-section.alt-bg {
        background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);
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

    /* Age Statistics Cards */
    .age-card {
        background: white;
        border-radius: 20px;
        padding: 35px 25px;
        text-align: center;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
        transition: all 0.4s ease;
        border: 2px solid transparent;
        height: 100%;
        position: relative;
        overflow: hidden;
    }

    .age-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #3b82f6, #8b5cf6);
        transform: scaleX(0);
        transition: transform 0.4s ease;
    }

    .age-card:hover {
        transform: translateY(-10px);
        border-color: rgba(59, 130, 246, 0.2);
        box-shadow: 0 20px 50px rgba(59, 130, 246, 0.15);
    }

    .age-card:hover::before {
        transform: scaleX(1);
    }

    .age-icon {
        width: 90px;
        height: 90px;
        border-radius: 50%;
        background: linear-gradient(135deg, #1e3a5f 0%, #3b82f6 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 25px;
        font-size: 2.5rem;
        color: white;
        box-shadow: 0 15px 35px rgba(59, 130, 246, 0.25);
    }

    .age-value {
        font-size: 3.5rem;
        font-weight: 800;
        color: #1e3a5f;
        margin-bottom: 8px;
        line-height: 1;
    }

    .age-value span {
        font-size: 1.2rem;
        color: #64748b;
        font-weight: 600;
    }

    .age-label {
        color: #3b82f6;
        font-size: 1.2rem;
        font-weight: 700;
        margin-bottom: 10px;
    }

    .age-desc {
        color: #94a3b8;
        font-size: 0.9rem;
    }

    /* Region Statistics */
    .region-card {
        background: white;
        border-radius: 16px;
        padding: 25px;
        display: flex;
        align-items: center;
        gap: 20px;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.06);
        transition: all 0.3s ease;
        border: 1px solid #e2e8f0;
        height: 100%;
    }

    .region-card:hover {
        transform: translateX(8px);
        box-shadow: 0 15px 40px rgba(59, 130, 246, 0.12);
        border-color: #3b82f6;
    }

    .region-icon {
        width: 65px;
        height: 65px;
        border-radius: 16px;
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
        color: white;
        flex-shrink: 0;
        box-shadow: 0 8px 20px rgba(16, 185, 129, 0.25);
    }

    .region-info {
        flex: 1;
    }

    .region-value {
        font-size: 2rem;
        font-weight: 800;
        color: #1e3a5f;
        line-height: 1;
        margin-bottom: 5px;
    }

    .region-value span {
        font-size: 0.9rem;
        color: #64748b;
        font-weight: 600;
    }

    .region-name {
        color: #64748b;
        font-size: 1rem;
        font-weight: 600;
    }

    .region-bar {
        height: 6px;
        background: #e2e8f0;
        border-radius: 3px;
        margin-top: 10px;
        overflow: hidden;
    }

    .region-bar-fill {
        height: 100%;
        background: linear-gradient(90deg, #10b981, #3b82f6);
        border-radius: 3px;
        transition: width 1s ease;
    }

    /* Education Level Cards */
    .edu-card {
        background: white;
        border-radius: 24px;
        padding: 40px 30px;
        text-align: center;
        box-shadow: 0 15px 50px rgba(0, 0, 0, 0.08);
        transition: all 0.4s ease;
        height: 100%;
        position: relative;
        overflow: hidden;
    }

    .edu-card::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 5px;
        background: linear-gradient(90deg, #8b5cf6, #3b82f6, #10b981);
    }

    .edu-card:hover {
        transform: translateY(-12px);
        box-shadow: 0 25px 60px rgba(139, 92, 246, 0.18);
    }

    .edu-icon {
        width: 110px;
        height: 110px;
        border-radius: 50%;
        background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 30px;
        font-size: 3rem;
        color: white;
        box-shadow: 0 20px 40px rgba(139, 92, 246, 0.3);
        position: relative;
    }

    .edu-icon::after {
        content: '';
        position: absolute;
        inset: -8px;
        border-radius: 50%;
        border: 2px dashed rgba(139, 92, 246, 0.3);
    }

    .edu-value {
        font-size: 4rem;
        font-weight: 800;
        color: #1e3a5f;
        margin-bottom: 10px;
        line-height: 1;
    }

    .edu-title {
        font-size: 1.4rem;
        font-weight: 700;
        color: #8b5cf6;
        margin-bottom: 10px;
    }

    .edu-desc {
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
        .stats-hero h1 {
            font-size: 2.5rem;
        }
        .section-title {
            font-size: 2rem;
        }
        .summary-number {
            font-size: 2.5rem;
        }
    }

    @media (max-width: 768px) {
        .stats-hero {
            padding-top: 120px;
            min-height: 45vh;
        }
        .stats-hero h1 {
            font-size: 2rem;
        }
        .stats-hero p {
            font-size: 1rem;
        }
        .age-value, .edu-value {
            font-size: 2.5rem;
        }
        .summary-card {
            padding: 20px;
        }
        .summary-number {
            font-size: 2rem;
        }
        .cta-title {
            font-size: 1.8rem;
        }
    }
</style>

<!-- Hero Section -->
<section class="stats-hero">
    <div class="container">
        <div data-aos="fade-up">
            <div class="hero-badge">
                <i class="fas fa-chart-line"></i>
                {{ $getContent('stats_hero_badge', 'AKADEMIYA STATISTIKASI') }}
            </div>
            <h1>{{ $getContent('stats_hero_title', 'Talabalar Statistikasi') }}</h1>
            <p>{{ $getContent('stats_hero_subtitle', "Bizning akademiyamizdagi barcha talabalar haqida batafsil statistik ma'lumotlar.") }}</p>
            <div class="hero-breadcrumb">
                <a href="{{ route('home') }}">{{ $getContent('stats_breadcrumb_home', 'Bosh sahifa') }}</a>
                <span>/</span>
                <span class="current">{{ $getContent('stats_breadcrumb_current', 'Statistika') }}</span>
            </div>
        </div>
    </div>
</section>

<!-- Summary Cards -->
<section class="summary-section">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="100">
                <div class="summary-card">
                    <div class="summary-icon blue">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="summary-number">{{ $totalStudents }}</div>
                    <div class="summary-label">{{ $getContent('stats_summary_total_label', 'Jami talabalar soni') }}</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="200">
                <div class="summary-card">
                    <div class="summary-icon purple">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <div class="summary-number">{{ $getContent('stats_edu1_value', '180') }}</div>
                    <div class="summary-label">{{ $getContent('stats_summary_bachelor_label', 'Bakalavr talabalar') }}</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="300">
                <div class="summary-card">
                    <div class="summary-icon green">
                        <i class="fas fa-map-marked-alt"></i>
                    </div>
                    <div class="summary-number">{{ $getContent('stats_summary_regions_value', '14') }}</div>
                    <div class="summary-label">{{ $getContent('stats_summary_regions_label', 'Viloyatlardan') }}</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="400">
                <div class="summary-card">
                    <div class="summary-icon orange">
                        <i class="fas fa-award"></i>
                    </div>
                    <div class="summary-number">{{ $getContent('stats_edu2_value', '95') }}</div>
                    <div class="summary-label">{{ $getContent('stats_summary_master_label', 'Magistratura') }}</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Age Statistics Section -->
<section class="stats-section">
    <div class="container">
        <div class="section-header" data-aos="fade-up">
            <div class="section-badge">
                <i class="fas fa-birthday-cake"></i>
                {{ $getContent('stats_age_badge', 'Demografik ma\'lumotlar') }}
            </div>
            <h2 class="section-title">{{ $getContent('stats_age_title', "Yosh bo'yicha taqsimot") }}</h2>
            <p class="section-subtitle">{{ $getContent('stats_age_subtitle', "Akademiyamizdagi talabalarning yosh guruhlari bo'yicha taqsimoti.") }}</p>
        </div>

        <div class="row g-4">
            @for($i = 1; $i <= 4; $i++)
            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="{{ $i * 100 }}">
                <div class="age-card">
                    <div class="age-icon">
                        <i class="{{ $getContent("stats_age{$i}_icon", 'fas fa-user') }}"></i>
                    </div>
                    <div class="age-value">{{ $getContent("stats_age{$i}_value", '0') }} <span>{{ $getContent('stats_age_unit', 'nafar') }}</span></div>
                    <div class="age-label">{{ $getContent("stats_age{$i}_label", '') }}</div>
                    <div class="age-desc">{{ $getContent("stats_age{$i}_desc", '') }}</div>
                </div>
            </div>
            @endfor
        </div>
    </div>
</section>

<!-- Region Statistics Section -->
<section class="stats-section alt-bg">
    <div class="container">
        <div class="section-header" data-aos="fade-up">
            <div class="section-badge">
                <i class="fas fa-globe-asia"></i>
                {{ $getContent('stats_region_badge', 'Geografik taqsimot') }}
            </div>
            <h2 class="section-title">{{ $getContent('stats_region_title', "Hududlar bo'yicha") }}</h2>
            <p class="section-subtitle">{{ $getContent('stats_region_subtitle', "Talabalarimiz O'zbekistonning barcha hududlaridan kelishadi.") }}</p>
        </div>

        <div class="row g-4">
            @php
                $maxRegionValue = 0;
                for ($j = 1; $j <= 6; $j++) {
                    $val = (int) $getContent("stats_region{$j}_value", 0);
                    if ($val > $maxRegionValue) $maxRegionValue = $val;
                }
            @endphp
            @for($i = 1; $i <= 6; $i++)
            @php
                $regionValue = (int) $getContent("stats_region{$i}_value", 0);
                $barWidth = $maxRegionValue > 0 ? ($regionValue / $maxRegionValue) * 100 : 0;
            @endphp
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="{{ (($i - 1) % 3 + 1) * 100 }}">
                <div class="region-card">
                    <div class="region-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <div class="region-info">
                        <div class="region-value">{{ $getContent("stats_region{$i}_value", '0') }} <span>{{ $getContent('stats_region_unit', 'talaba') }}</span></div>
                        <div class="region-name">{{ $getContent("stats_region{$i}_label", 'Mintaqa') }} {{ $getContent('stats_region_suffix', 'viloyati') }}</div>
                        <div class="region-bar">
                            <div class="region-bar-fill" style="width: {{ $barWidth }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
            @endfor
        </div>
    </div>
</section>

<!-- Education Level Section -->
<section class="stats-section">
    <div class="container">
        <div class="section-header" data-aos="fade-up">
            <div class="section-badge">
                <i class="fas fa-university"></i>
                {{ $getContent('stats_edu_badge', 'Ta\'lim darajalari') }}
            </div>
            <h2 class="section-title">{{ $getContent('stats_edu_title', "Ta'lim bosqichlari bo'yicha") }}</h2>
            <p class="section-subtitle">{{ $getContent('stats_edu_subtitle', "Turli ta'lim darajalarida ta'lim olayotgan talabalar soni.") }}</p>
        </div>

        <div class="row g-4 justify-content-center">
            @for($i = 1; $i <= 3; $i++)
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="{{ $i * 150 }}">
                <div class="edu-card">
                    <div class="edu-icon">
                        <i class="{{ $getContent("stats_edu{$i}_icon", 'fas fa-graduation-cap') }}"></i>
                    </div>
                    <div class="edu-value">{{ $getContent("stats_edu{$i}_value", '0') }}</div>
                    <div class="edu-title">{{ $getContent("stats_edu{$i}_title", 'Ta\'lim dasturi') }}</div>
                    <div class="edu-desc">{{ $getContent("stats_edu{$i}_label", '') }}</div>
                </div>
            </div>
            @endfor
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section">
    <div class="container">
        <div class="cta-content" data-aos="fade-up">
            <h2 class="cta-title">{{ $getContent('stats_cta_title', 'Bizning akademiyamizga qo\'shiling!') }}</h2>
            <p class="cta-text">{{ $getContent('stats_cta_text', 'Siz ham bu statistikaning bir qismi bo\'lishni xohlaysizmi? Hoziroq qabul uchun ariza topshiring.') }}</p>
            <a href="{{ route('home') }}#admission" class="cta-btn">
                <i class="fas fa-paper-plane"></i>
                {{ $getContent('stats_cta_btn', 'Qabulga ariza topshirish') }}
            </a>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animate numbers on scroll
    const observerOptions = {
        threshold: 0.5
    };

    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animated');
            }
        });
    }, observerOptions);

    document.querySelectorAll('.summary-card, .age-card, .region-card, .edu-card').forEach(card => {
        observer.observe(card);
    });

    // Animate region bars
    const barObserver = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const bar = entry.target;
                const width = bar.style.width;
                bar.style.width = '0';
                setTimeout(() => {
                    bar.style.width = width;
                }, 100);
            }
        });
    }, { threshold: 0.3 });

    document.querySelectorAll('.region-bar-fill').forEach(bar => {
        barObserver.observe(bar);
    });
});
</script>
@endsection
