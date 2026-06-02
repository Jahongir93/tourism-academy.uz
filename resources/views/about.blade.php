@extends(\App\Helpers\TemplateHelper::getLayout())

@php
    use App\Models\CmsContent;

    // Get about page content from CMS
    $aboutContents = CmsContent::where('section', 'about')->get()->keyBy('key');

    // Get current language
    $lang = app()->getLocale() ?? 'uz';
    $langField = 'value_' . $lang;

    // Helper function to get content
    $getContent = function($key, $default = '') use ($aboutContents, $langField) {
        $content = $aboutContents->get($key);
        return $content ? ($content->$langField ?? $content->value_uz ?? $default) : $default;
    };
@endphp

@section('title', $getContent('about_page_title', 'Biz haqimizda') . ' - Tourism Academy')
@section('description', $getContent('about_hero_description', "Samarqand Xalqaro Turizm va Mehmondo'stlik Akademiyasi haqida"))

@section('content')
<style>
    /* Hero Section */
    .about-hero {
        min-height: 55vh;
        padding-top: 140px;
        padding-bottom: 80px;
        background: linear-gradient(135deg, #1e3a5f 0%, #0d1b2a 50%, #1b263b 100%);
        display: flex;
        align-items: center;
        position: relative;
        overflow: hidden;
    }

    .about-hero::before {
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

    .about-hero::after {
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

    .about-hero h1 {
        color: #ffffff;
        font-size: 3.2rem;
        font-weight: 800;
        margin-bottom: 20px;
        text-shadow: 0 4px 30px rgba(0, 0, 0, 0.3);
        position: relative;
        z-index: 2;
        line-height: 1.2;
    }

    .about-hero p {
        color: #cbd5e1;
        font-size: 1.25rem;
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

    /* Section Styles */
    .about-section {
        padding: 80px 0;
    }

    .about-section.alt-bg {
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

    /* Mission & Vision Cards */
    .mv-card {
        background: white;
        border-radius: 24px;
        padding: 40px;
        box-shadow: 0 15px 50px rgba(0, 0, 0, 0.08);
        transition: all 0.4s ease;
        height: 100%;
        position: relative;
        overflow: hidden;
    }

    .mv-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 5px;
        height: 100%;
        background: linear-gradient(180deg, #3b82f6, #8b5cf6);
    }

    .mv-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 25px 60px rgba(59, 130, 246, 0.15);
    }

    .mv-icon {
        width: 80px;
        height: 80px;
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 25px;
        font-size: 2rem;
    }

    .mv-icon.mission {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
        box-shadow: 0 10px 30px rgba(59, 130, 246, 0.3);
    }

    .mv-icon.vision {
        background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
        color: white;
        box-shadow: 0 10px 30px rgba(139, 92, 246, 0.3);
    }

    .mv-card h3 {
        font-size: 1.6rem;
        font-weight: 700;
        color: #1e3a5f;
        margin-bottom: 15px;
    }

    .mv-card p {
        color: #64748b;
        line-height: 1.8;
        font-size: 1.05rem;
    }

    /* Values Cards */
    .value-card {
        background: white;
        border-radius: 20px;
        padding: 35px 25px;
        text-align: center;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.06);
        transition: all 0.4s ease;
        height: 100%;
        border: 2px solid transparent;
    }

    .value-card:hover {
        transform: translateY(-8px);
        border-color: #3b82f6;
        box-shadow: 0 20px 50px rgba(59, 130, 246, 0.12);
    }

    .value-icon {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        font-size: 2rem;
    }

    .value-icon.blue {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
        box-shadow: 0 10px 25px rgba(59, 130, 246, 0.25);
    }

    .value-icon.purple {
        background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
        color: white;
        box-shadow: 0 10px 25px rgba(139, 92, 246, 0.25);
    }

    .value-icon.green {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        box-shadow: 0 10px 25px rgba(16, 185, 129, 0.25);
    }

    .value-icon.orange {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: white;
        box-shadow: 0 10px 25px rgba(245, 158, 11, 0.25);
    }

    .value-card h4 {
        font-size: 1.2rem;
        font-weight: 700;
        color: #1e3a5f;
        margin-bottom: 12px;
    }

    .value-card p {
        color: #64748b;
        font-size: 0.95rem;
        line-height: 1.6;
    }

    /* Stats Section */
    .stats-section {
        background: linear-gradient(135deg, #1e3a5f 0%, #0d1b2a 100%);
        padding: 80px 0;
        position: relative;
        overflow: hidden;
    }

    .stats-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background:
            radial-gradient(ellipse at 20% 50%, rgba(59, 130, 246, 0.15) 0%, transparent 50%),
            radial-gradient(ellipse at 80% 50%, rgba(139, 92, 246, 0.1) 0%, transparent 50%);
    }

    .stat-item {
        text-align: center;
        position: relative;
        z-index: 2;
    }

    .stat-number {
        font-size: 4rem;
        font-weight: 800;
        background: linear-gradient(135deg, #3b82f6, #8b5cf6);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        margin-bottom: 10px;
        line-height: 1;
    }

    .stat-label {
        color: #cbd5e1;
        font-size: 1.1rem;
        font-weight: 600;
    }

    /* History Section */
    .history-content {
        display: flex;
        align-items: center;
        gap: 60px;
    }

    .history-image {
        flex: 1;
        border-radius: 24px;
        overflow: hidden;
        box-shadow: 0 25px 60px rgba(0, 0, 0, 0.15);
    }

    .history-image img {
        width: 100%;
        height: auto;
        display: block;
    }

    .history-text {
        flex: 1;
    }

    .history-text h3 {
        font-size: 2rem;
        font-weight: 800;
        color: #1e3a5f;
        margin-bottom: 25px;
    }

    .history-text p {
        color: #64748b;
        line-height: 1.8;
        margin-bottom: 20px;
        font-size: 1.05rem;
    }

    /* Team Cards */
    .team-card {
        background: white;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
        transition: all 0.4s ease;
        height: 100%;
    }

    .team-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 50px rgba(59, 130, 246, 0.15);
    }

    .team-image {
        height: 280px;
        background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .team-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .team-card:hover .team-image img {
        transform: scale(1.1);
    }

    .team-placeholder {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 3rem;
    }

    .team-info {
        padding: 25px;
        text-align: center;
    }

    .team-info h4 {
        font-size: 1.2rem;
        font-weight: 700;
        color: #1e3a5f;
        margin-bottom: 8px;
    }

    .team-info .role {
        color: #3b82f6;
        font-weight: 600;
        font-size: 0.95rem;
        margin-bottom: 10px;
    }

    .team-info p {
        color: #64748b;
        font-size: 0.9rem;
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
        .about-hero h1 {
            font-size: 2.5rem;
        }
        .section-title {
            font-size: 2rem;
        }
        .history-content {
            flex-direction: column;
        }
        .stat-number {
            font-size: 3rem;
        }
    }

    @media (max-width: 768px) {
        .about-hero {
            padding-top: 120px;
            min-height: 45vh;
        }
        .about-hero h1 {
            font-size: 2rem;
        }
        .about-hero p {
            font-size: 1rem;
        }
        .mv-card {
            padding: 30px;
        }
        .cta-title {
            font-size: 1.8rem;
        }
        .stat-number {
            font-size: 2.5rem;
        }
    }
</style>

<!-- Hero Section -->
<section class="about-hero">
    <div class="container">
        <div data-aos="fade-up">
            <div class="hero-badge">
                <i class="fas fa-university"></i>
                {{ $getContent('about_hero_badge', 'BIZ HAQIMIZDA') }}
            </div>
            <h1>{{ $getContent('about_hero_title', "Ta'lim sifatini oshirish yo'lida") }}</h1>
            <p>{{ $getContent('about_hero_subtitle', "Zamonaviy texnologiyalar va innovatsion yondashuvlar orqali ta'lim jarayonini yangi bosqichga olib chiqamiz") }}</p>
            <div class="hero-breadcrumb">
                <a href="{{ route('home') }}">{{ $getContent('about_breadcrumb_home', 'Bosh sahifa') }}</a>
                <span>/</span>
                <span class="current">{{ $getContent('about_breadcrumb_about', 'Biz haqimizda') }}</span>
            </div>
        </div>
    </div>
</section>

<!-- Mission & Vision Section -->
<section class="about-section">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-6" data-aos="fade-right">
                <div class="mv-card">
                    <div class="mv-icon mission">
                        <i class="fas fa-bullseye"></i>
                    </div>
                    <h3>{{ $getContent('about_mission_title', 'Bizning missiyamiz') }}</h3>
                    <p>{{ $getContent('about_mission_text', "Talabalar va o'qituvchilar uchun qulay, samarali va zamonaviy ta'lim muhitini yaratish.") }}</p>
                </div>
            </div>
            <div class="col-lg-6" data-aos="fade-left">
                <div class="mv-card">
                    <div class="mv-icon vision">
                        <i class="fas fa-eye"></i>
                    </div>
                    <h3>{{ $getContent('about_vision_title', "Bizning ko'zlagan maqsadimiz") }}</h3>
                    <p>{{ $getContent('about_vision_text', "O'zbekistonda ta'lim sohasida yetakchi raqamli platforma bo'lish.") }}</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Values Section -->
<section class="about-section alt-bg">
    <div class="container">
        <div class="section-header" data-aos="fade-up">
            <h2 class="section-title">{{ $getContent('about_values_title', 'Bizning qadriyatlarimiz') }}</h2>
        </div>

        <div class="row g-4">
            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="100">
                <div class="value-card">
                    <div class="value-icon blue">
                        <i class="fas fa-award"></i>
                    </div>
                    <h4>{{ $getContent('about_value1_title', 'Sifat') }}</h4>
                    <p>{{ $getContent('about_value1_text', 'Har bir loyihada eng yuqori sifat standartlariga rioya qilamiz') }}</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="200">
                <div class="value-card">
                    <div class="value-icon purple">
                        <i class="fas fa-lightbulb"></i>
                    </div>
                    <h4>{{ $getContent('about_value2_title', 'Innovatsiya') }}</h4>
                    <p>{{ $getContent('about_value2_text', "Doimiy ravishda yangi texnologiyalarni o'rganamiz va joriy etamiz") }}</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="300">
                <div class="value-card">
                    <div class="value-icon green">
                        <i class="fas fa-handshake"></i>
                    </div>
                    <h4>{{ $getContent('about_value3_title', 'Hamkorlik') }}</h4>
                    <p>{{ $getContent('about_value3_text', "Jamoaviy ish va o'zaro hurmat asosida muvaffaqiyatga erishamiz") }}</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="400">
                <div class="value-card">
                    <div class="value-icon orange">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h4>{{ $getContent('about_value4_title', 'Shaffoflik') }}</h4>
                    <p>{{ $getContent('about_value4_text', "Barcha jarayonlarda ochiqlik va halollikni ta'minlaymiz") }}</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="stats-section">
    <div class="container">
        <div class="section-header" data-aos="fade-up" style="margin-bottom: 40px;">
            <h2 class="section-title" style="color: white;">{{ $getContent('about_stats_title', 'Bizning yutuqlarimiz') }}</h2>
        </div>
        <div class="row g-4">
            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="100">
                <div class="stat-item">
                    <div class="stat-number">{{ $getContent('about_stat1_value', '5000+') }}</div>
                    <div class="stat-label">{{ $getContent('about_stat1_label', 'Faol talabalar') }}</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="200">
                <div class="stat-item">
                    <div class="stat-number">{{ $getContent('about_stat2_value', '200+') }}</div>
                    <div class="stat-label">{{ $getContent('about_stat2_label', "Malakali o'qituvchilar") }}</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="300">
                <div class="stat-item">
                    <div class="stat-number">{{ $getContent('about_stat3_value', '50+') }}</div>
                    <div class="stat-label">{{ $getContent('about_stat3_label', "Ta'lim dasturlari") }}</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="400">
                <div class="stat-item">
                    <div class="stat-number">{{ $getContent('about_stat4_value', '10+') }}</div>
                    <div class="stat-label">{{ $getContent('about_stat4_label', 'Yillik tajriba') }}</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- History Section -->
<section class="about-section">
    <div class="container">
        <div class="history-content">
            <div class="history-image" data-aos="fade-right">
                <img src="{{ asset('images/academy-building.jpg') }}" alt="Akademiya binosi" onerror="this.src='{{ asset('images/ext/eaf642e2041a4a7c.jpg') }}'">
            </div>
            <div class="history-text" data-aos="fade-left">
                <h3>{{ $getContent('about_history_title', 'Bizning tarix') }}</h3>
                <p>{{ $getContent('about_history_text', "Institutimiz ko'p yillik tajribaga ega bo'lib, minglab mutaxassislarni tayyorlagan. Biz doimiy ravishda rivojlanib, zamonaviy ta'lim standartlariga mos kelamiz. Bizning maqsadimiz - har bir talabaga sifatli ta'lim berish va ularning kelajagiga hissa qo'shish.") }}</p>
            </div>
        </div>
    </div>
</section>

<!-- Leadership Section -->
<section class="about-section alt-bg">
    <div class="container">
        <div class="section-header" data-aos="fade-up">
            <h2 class="section-title">{{ $getContent('about_team_title', 'Bizning rahbariyat') }}</h2>
            <p class="section-subtitle">{{ $getContent('about_team_subtitle', 'Tajribali va professional jamoa') }}</p>
        </div>

        <div class="row g-4 justify-content-center">
            @for($i = 1; $i <= 3; $i++)
            @php
                // Prefer about_mgmt* keys (managed by CMS), fall back to about_team* (legacy)
                $imageRaw = $getContent("about_mgmt{$i}_image", '') ?: $getContent("about_team{$i}_image", '');
                $imageRaw = trim(ltrim($imageRaw, '/'));
                if ($imageRaw === '') {
                    $imageUrl = '';
                } elseif (\Illuminate\Support\Str::startsWith($imageRaw, 'http')) {
                    $imageUrl = $imageRaw;
                } elseif (\Illuminate\Support\Str::startsWith($imageRaw, 'uploads/')) {
                    $imageUrl = asset($imageRaw);
                } else {
                    $imageUrl = asset('storage/' . $imageRaw);
                }
                $name = $getContent("about_mgmt{$i}_title", '') ?: $getContent("about_team{$i}_name", 'Rahbar ' . $i);
                $role = $getContent("about_mgmt{$i}_role", '') ?: $getContent("about_team{$i}_role", 'Lavozim');
                $desc = $getContent("about_mgmt{$i}_text", '') ?: $getContent("about_team{$i}_desc", '');
            @endphp
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="{{ $i * 150 }}">
                <div class="team-card">
                    <div class="team-image">
                        @if($imageUrl)
                            <img src="{{ $imageUrl }}" alt="{{ $name }}" loading="lazy">
                        @else
                            <div class="team-placeholder">
                                <i class="fas fa-user-tie"></i>
                            </div>
                        @endif
                    </div>
                    <div class="team-info">
                        <h4>{{ $name }}</h4>
                        <div class="role">{{ $role }}</div>
                        <p>{{ $desc }}</p>
                    </div>
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
            <h2 class="cta-title">{{ $getContent('about_cta_title', "Biz bilan bog'laning") }}</h2>
            <p class="cta-text">{{ $getContent('about_cta_text', "Savollaringiz bormi? Biz bilan bog'laning va batafsil ma'lumot oling.") }}</p>
            <a href="{{ route('contact') }}" class="cta-btn">
                <i class="fas fa-paper-plane"></i>
                {{ $getContent('about_cta_button', "Bog'lanish") }}
            </a>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animate stats on scroll
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

    document.querySelectorAll('.stat-item, .mv-card, .value-card, .team-card').forEach(item => {
        observer.observe(item);
    });
});
</script>
@endsection
