@extends(\App\Helpers\TemplateHelper::getLayout())

@php
    use App\Models\CmsContent;

    // Get current language
    $lang = app()->getLocale() ?? 'uz';
    $langField = 'value_' . $lang;

    // Translations
    $translations = [
        'uz' => [
            'page_title' => "Yo'nalishlar",
            'hero_badge' => "HOSPITALITY MANAGEMENT",
            'hero_title' => "Hospitality Management Program",
            'hero_subtitle' => "Innovative, Alternative, and Sustainable Trends in Tourism",
            'hero_subtitle_long' => "Karyerangizni yangi bosqichga olib chiqing! UN Tourism va Les Roches hamkorligidagi blended learning dasturi orqali xalqaro darajadagi bilim va ko'nikmalarni egallang.",
            'btn_register' => "Ro'yxatdan o'tish",
            'btn_learn_more' => "Batafsil",
            'stat_modules' => "Modullar",
            'stat_months' => "Oylik dastur",
            'stat_certification' => "Sertifikat",
            'breadcrumb_home' => "Bosh sahifa",
            'breadcrumb_programs' => "Yo'nalishlar",
            'training_date' => "O'quv sanasi",
            'objective' => "Maqsad",
            'subjects' => "Fanlar",
            'from' => "dan",
            'to' => "gacha",
            'what_you_get' => "Nimani olasiz?",
            'what_you_get_subtitle' => "Hospitality Management dasturi orqali quyidagi bilim va ko'nikmalarni egallaysiz",
            // Module titles
            'module1_title' => "Innovatsion, Alternativ va Barqaror Turizm Trendlari",
            'module2_title' => "Tadbirkorlik, Biznes Modellashtirish va Inqiroz Boshqaruvi",
            'module3_title' => "Loyiha Boshqaruvi va Xalqaro HR",
            'module4_title' => "Ilg'or Moliya va Byudjetlashtirish",
            'module5_title' => "Daromadlarni Boshqarish",
            'module6_title' => "Mehmondo'stlik Ko'chmas Mulk va Investitsiyalar",
            'module7_title' => "Tadbirlarni Boshqarish",
            'module8_title' => "Marketing Strategiyalari va Ma'lumotlar Tahlili",
        ],
        'en' => [
            'page_title' => "Programs",
            'hero_badge' => "HOSPITALITY MANAGEMENT",
            'hero_title' => "Hospitality Management Program",
            'hero_subtitle' => "Innovative, Alternative, and Sustainable Trends in Tourism",
            'hero_subtitle_long' => "Take your career to the next level! Gain world-class knowledge and skills through our blended learning program in partnership with UN Tourism and Les Roches.",
            'btn_register' => "Register Now",
            'btn_learn_more' => "Learn More",
            'stat_modules' => "Modules",
            'stat_months' => "Month Program",
            'stat_certification' => "Certificate",
            'breadcrumb_home' => "Home",
            'breadcrumb_programs' => "Programs",
            'training_date' => "Training date",
            'objective' => "Objective",
            'subjects' => "Subjects",
            'from' => "From",
            'to' => "to",
            'what_you_get' => "What you get?",
            'what_you_get_subtitle' => "Gain valuable skills and knowledge through our Hospitality Management program",
            'module1_title' => "Innovative, Alternative & Sustainable Trends in Tourism",
            'module2_title' => "Entrepreneurship, Business Modeling & Crisis Management",
            'module3_title' => "Project Management & International HR",
            'module4_title' => "Advanced Finance and Budgeting",
            'module5_title' => "Revenue Management",
            'module6_title' => "Hospitality Real Estate & Investment",
            'module7_title' => "Event Management",
            'module8_title' => "Marketing Strategies & Data Analytics for Hotel Management",
        ],
        'ru' => [
            'page_title' => "Направления",
            'hero_badge' => "HOSPITALITY MANAGEMENT",
            'hero_title' => "Программа Hospitality Management",
            'hero_subtitle' => "Инновационные, Альтернативные и Устойчивые Тренды в Туризме",
            'hero_subtitle_long' => "Поднимите свою карьеру на новый уровень! Получите знания и навыки мирового класса через программу смешанного обучения в партнерстве с UN Tourism и Les Roches.",
            'btn_register' => "Регистрация",
            'btn_learn_more' => "Подробнее",
            'stat_modules' => "Модулей",
            'stat_months' => "Месяцев",
            'stat_certification' => "Сертификат",
            'breadcrumb_home' => "Главная",
            'breadcrumb_programs' => "Направления",
            'training_date' => "Дата обучения",
            'objective' => "Цель",
            'subjects' => "Предметы",
            'from' => "С",
            'to' => "по",
            'what_you_get' => "Что вы получите?",
            'what_you_get_subtitle' => "Получите ценные навыки и знания через нашу программу Hospitality Management",
            'module1_title' => "Инновационные, Альтернативные и Устойчивые Тренды в Туризме",
            'module2_title' => "Предпринимательство, Бизнес-моделирование и Кризис-менеджмент",
            'module3_title' => "Управление Проектами и Международный HR",
            'module4_title' => "Продвинутые Финансы и Бюджетирование",
            'module5_title' => "Управление Доходами",
            'module6_title' => "Недвижимость и Инвестиции в Гостеприимство",
            'module7_title' => "Управление Мероприятиями",
            'module8_title' => "Маркетинговые Стратегии и Аналитика Данных",
        ]
    ];

    $t = $translations[$lang] ?? $translations['en'];
@endphp

@section('title', $t['page_title'] . ' - Tourism Academy Samarkand')

@section('content')
<style>
    /* Hero Section - Premium Academic Design */
    .programs-hero {
        min-height: 80vh;
        padding-top: 100px;
        padding-bottom: 80px;
        background: linear-gradient(135deg, #0A1F44 0%, #0D2B5A 50%, #1a365d 100%);
        display: flex;
        align-items: center;
        position: relative;
        overflow: hidden;
    }

    /* Background Image Overlay */
    .programs-hero::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background:
            linear-gradient(135deg, rgba(10, 31, 68, 0.92) 0%, rgba(13, 43, 90, 0.85) 50%, rgba(26, 54, 93, 0.8) 100%),
            url('/images/hero-bg.jpg') center/cover no-repeat;
        pointer-events: none;
    }

    /* Decorative Elements */
    .programs-hero::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background:
            radial-gradient(ellipse at 20% 30%, rgba(47, 91, 255, 0.2) 0%, transparent 50%),
            radial-gradient(ellipse at 80% 70%, rgba(47, 128, 237, 0.15) 0%, transparent 50%),
            radial-gradient(circle at 90% 20%, rgba(255, 255, 255, 0.05) 0%, transparent 30%);
        pointer-events: none;
    }

    .programs-hero .container {
        position: relative;
        z-index: 2;
    }

    .hero-content {
        max-width: 700px;
    }

    /* Category Badge */
    .hero-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: rgba(47, 91, 255, 0.25);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        color: white;
        padding: 8px 18px;
        border-radius: 50px;
        font-weight: 600;
        font-size: 12px;
        margin-bottom: 24px;
        letter-spacing: 1.5px;
        text-transform: uppercase;
        border: 1px solid rgba(255, 255, 255, 0.15);
    }

    .hero-badge i {
        font-size: 11px;
    }

    /* Main Title */
    .programs-hero h1 {
        color: #ffffff;
        font-size: 3.2rem;
        font-weight: 700;
        margin-bottom: 24px;
        line-height: 1.15;
        max-width: 650px;
        text-shadow: 0 2px 20px rgba(0, 0, 0, 0.3);
    }

    /* Glassmorphism Subtitle Block */
    .hero-subtitle-card {
        background: rgba(255, 255, 255, 0.12);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.18);
        border-radius: 16px;
        padding: 16px 24px;
        margin-bottom: 32px;
        max-width: 580px;
    }

    .hero-subtitle-card p {
        color: rgba(255, 255, 255, 0.95);
        font-size: 15px;
        line-height: 1.6;
        margin: 0;
    }

    /* CTA Buttons */
    .hero-buttons {
        display: flex;
        align-items: center;
        gap: 16px;
        flex-wrap: wrap;
        margin-bottom: 40px;
    }

    .btn-hero-primary {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        background: linear-gradient(135deg, #2F5BFF 0%, #1e40af 100%);
        color: white;
        padding: 14px 28px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 15px;
        text-decoration: none;
        transition: all 0.3s ease;
        box-shadow: 0 4px 20px rgba(47, 91, 255, 0.4);
        border: none;
    }

    .btn-hero-primary:hover {
        background: linear-gradient(135deg, #1e40af 0%, #1e3a8a 100%);
        transform: translateY(-2px);
        box-shadow: 0 6px 25px rgba(47, 91, 255, 0.5);
        color: white;
    }

    .btn-hero-primary i {
        font-size: 14px;
    }

    .btn-hero-secondary {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        background: transparent;
        color: white;
        padding: 13px 26px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 15px;
        text-decoration: none;
        transition: all 0.3s ease;
        border: 2px solid rgba(255, 255, 255, 0.4);
    }

    .btn-hero-secondary:hover {
        background: rgba(255, 255, 255, 0.1);
        border-color: rgba(255, 255, 255, 0.6);
        color: white;
    }

    .btn-hero-secondary i {
        font-size: 12px;
        transition: transform 0.3s ease;
    }

    .btn-hero-secondary:hover i {
        transform: translateX(4px);
    }

    /* Hero Breadcrumb */
    .hero-breadcrumb {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .hero-breadcrumb a {
        color: rgba(255, 255, 255, 0.7);
        text-decoration: none;
        font-size: 14px;
        transition: color 0.3s;
    }

    .hero-breadcrumb a:hover {
        color: #fff;
    }

    .hero-breadcrumb .separator {
        color: rgba(255, 255, 255, 0.4);
        font-size: 12px;
    }

    .hero-breadcrumb .current {
        color: rgba(255, 255, 255, 0.95);
        font-weight: 500;
        font-size: 14px;
    }

    /* Hero Stats (Optional) */
    .hero-stats {
        display: flex;
        gap: 40px;
        margin-top: 50px;
        padding-top: 30px;
        border-top: 1px solid rgba(255, 255, 255, 0.15);
    }

    .hero-stat {
        text-align: left;
    }

    .hero-stat-number {
        font-size: 2rem;
        font-weight: 700;
        color: #fff;
        line-height: 1;
        margin-bottom: 6px;
    }

    .hero-stat-label {
        font-size: 13px;
        color: rgba(255, 255, 255, 0.7);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Right Side Decoration */
    .hero-decoration {
        position: absolute;
        right: 0;
        top: 50%;
        transform: translateY(-50%);
        width: 45%;
        height: 80%;
        background:
            radial-gradient(circle at center, rgba(47, 91, 255, 0.1) 0%, transparent 70%);
        pointer-events: none;
        z-index: 1;
    }

    .hero-decoration::before {
        content: '';
        position: absolute;
        right: 10%;
        top: 20%;
        width: 300px;
        height: 300px;
        border: 2px solid rgba(255, 255, 255, 0.08);
        border-radius: 50%;
    }

    .hero-decoration::after {
        content: '';
        position: absolute;
        right: 5%;
        bottom: 15%;
        width: 200px;
        height: 200px;
        border: 2px solid rgba(255, 255, 255, 0.05);
        border-radius: 50%;
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

    .section-title {
        font-size: 2.2rem;
        font-weight: 700;
        color: #0A1F44;
        margin-bottom: 10px;
    }

    .section-subtitle {
        color: #667085;
        font-size: 1.05rem;
        max-width: 600px;
        margin: 0 auto;
    }

    /* Accordion Styles */
    .program-accordion {
        max-width: 900px;
        margin: 0 auto;
    }

    .accordion-item {
        background: #ffffff;
        border-radius: 12px;
        margin-bottom: 16px;
        border: 1px solid #E4E7EC;
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .accordion-item:hover {
        border-color: #2F5BFF;
        box-shadow: 0 4px 20px rgba(47, 91, 255, 0.1);
    }

    .accordion-item.active {
        border-color: #2F5BFF;
        box-shadow: 0 8px 30px rgba(47, 91, 255, 0.15);
    }

    .accordion-header {
        padding: 20px 25px;
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #ffffff;
        transition: all 0.3s ease;
    }

    .accordion-header:hover {
        background: #f8fafc;
    }

    .accordion-header-left {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .accordion-number {
        width: 36px;
        height: 36px;
        background: #2F5BFF;
        color: #ffffff;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.95rem;
    }

    .accordion-title {
        font-size: 1.05rem;
        font-weight: 600;
        color: #0A1F44;
        margin: 0;
    }

    .accordion-date {
        font-size: 0.85rem;
        color: #667085;
        margin-top: 4px;
    }

    .accordion-icon {
        width: 32px;
        height: 32px;
        background: #f0f4ff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #2F5BFF;
        transition: all 0.3s ease;
    }

    .accordion-item.active .accordion-icon {
        background: #2F5BFF;
        color: #ffffff;
        transform: rotate(180deg);
    }

    .accordion-content {
        display: none;
        padding: 0 25px 25px;
        border-top: 1px solid #E4E7EC;
    }

    .accordion-item.active .accordion-content {
        display: block;
    }

    .accordion-objective {
        background: #f0f4ff;
        border-radius: 10px;
        padding: 20px;
        margin: 20px 0;
    }

    .accordion-objective h4 {
        color: #2F5BFF;
        font-size: 0.9rem;
        font-weight: 600;
        margin-bottom: 10px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .accordion-objective p {
        color: #4b5563;
        font-size: 0.95rem;
        line-height: 1.7;
        margin: 0;
    }

    .accordion-subjects h4 {
        color: #0A1F44;
        font-size: 0.9rem;
        font-weight: 600;
        margin-bottom: 15px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .subjects-list {
        list-style: none;
        padding: 0;
        margin: 0;
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 10px;
    }

    .subjects-list li {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        padding: 10px 15px;
        background: #f8fafc;
        border-radius: 8px;
        font-size: 0.9rem;
        color: #374151;
    }

    .subjects-list li i {
        color: #2F5BFF;
        margin-top: 2px;
    }

    /* What You Get Section */
    .what-you-get-section {
        padding: 80px 0;
        background: #ffffff;
    }

    .benefits-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 24px;
        max-width: 1000px;
        margin: 0 auto;
    }

    .benefit-card {
        background: #ffffff;
        border: 2px solid #E4E7EC;
        border-radius: 16px;
        padding: 28px;
        display: flex;
        gap: 20px;
        transition: all 0.3s ease;
        position: relative;
    }

    .benefit-card:hover {
        border-color: #2F5BFF;
        box-shadow: 0 8px 30px rgba(47, 91, 255, 0.1);
        transform: translateY(-4px);
    }

    .benefit-content {
        flex: 1;
    }

    .benefit-title {
        font-size: 1.05rem;
        font-weight: 700;
        color: #0A1F44;
        margin-bottom: 12px;
    }

    .benefit-text {
        color: #667085;
        font-size: 0.9rem;
        line-height: 1.6;
        margin: 0;
    }

    .benefit-text li {
        margin-bottom: 6px;
        list-style: none;
        position: relative;
        padding-left: 16px;
    }

    .benefit-text li::before {
        content: '';
        position: absolute;
        left: 0;
        top: 8px;
        width: 6px;
        height: 6px;
        background: #2F5BFF;
        border-radius: 50%;
    }

    .benefit-icon {
        width: 50px;
        height: 50px;
        background: #f0f4ff;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #2F5BFF;
        font-size: 1.3rem;
        flex-shrink: 0;
    }

    /* Responsive */
    @media (max-width: 991px) {
        .programs-hero {
            min-height: 70vh;
            padding-top: 100px;
        }
        .programs-hero h1 { font-size: 2.5rem; }
        .hero-subtitle-card { max-width: 100%; }
        .hero-stats { gap: 30px; }
        .hero-stat-number { font-size: 1.75rem; }
        .hero-decoration { display: none; }
        .subjects-list { grid-template-columns: 1fr; }
        .benefits-grid { grid-template-columns: 1fr; }
    }

    @media (max-width: 768px) {
        .programs-hero {
            padding-top: 90px;
            padding-bottom: 60px;
            min-height: 65vh;
        }
        .programs-hero h1 { font-size: 2rem; }
        .hero-subtitle-card { padding: 14px 18px; }
        .hero-subtitle-card p { font-size: 14px; }
        .hero-buttons { flex-direction: column; align-items: flex-start; gap: 12px; }
        .btn-hero-primary, .btn-hero-secondary { width: 100%; justify-content: center; }
        .hero-stats { flex-wrap: wrap; gap: 20px; }
        .hero-stat { flex: 1 1 40%; }
        .section-title { font-size: 1.8rem; }
        .accordion-header { padding: 16px 20px; }
        .accordion-title { font-size: 0.95rem; }
        .benefit-card { flex-direction: column; text-align: center; }
        .benefit-icon { margin: 0 auto 15px; }
    }

    @media (max-width: 575px) {
        .programs-hero {
            min-height: auto;
            padding-top: 80px;
            padding-bottom: 50px;
        }
        .programs-hero h1 { font-size: 1.65rem; }
        .hero-badge { font-size: 10px; padding: 6px 14px; }
        .hero-subtitle-card p { font-size: 13px; }
        .hero-stats { gap: 15px; padding-top: 20px; margin-top: 30px; }
        .hero-stat-number { font-size: 1.5rem; }
        .hero-stat-label { font-size: 11px; }
        .accordion-header-left { flex-direction: column; align-items: flex-start; gap: 10px; }
        .accordion-number { width: 30px; height: 30px; font-size: 0.85rem; }
    }
</style>

<!-- Hero Section -->
<section class="programs-hero">
    <!-- Right Side Decoration -->
    <div class="hero-decoration"></div>

    <div class="container">
        <div class="hero-content" data-aos="fade-up">
            <!-- Category Badge -->
            <div class="hero-badge">
                <i class="fas fa-graduation-cap"></i>
                {{ $t['hero_badge'] }}
            </div>

            <!-- Main Title -->
            <h1>{{ $t['hero_title'] }}</h1>

            <!-- Glassmorphism Subtitle Card -->
            <div class="hero-subtitle-card">
                <p>{{ $t['hero_subtitle_long'] ?? $t['hero_subtitle'] }}</p>
            </div>

            <!-- CTA Buttons -->
            <div class="hero-buttons">
                <a href="{{ route('admission.apply') }}" class="btn-hero-primary">
                    <i class="fas fa-user-plus"></i>
                    {{ $t['btn_register'] ?? "Ro'yxatdan o'tish" }}
                </a>
                <a href="#programs" class="btn-hero-secondary">
                    {{ $t['btn_learn_more'] ?? "Batafsil" }}
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>

            <!-- Breadcrumb -->
            <div class="hero-breadcrumb">
                <a href="{{ route('home') }}">
                    <i class="fas fa-home me-1"></i>{{ $t['breadcrumb_home'] }}
                </a>
                <span class="separator">/</span>
                <span class="current">{{ $t['breadcrumb_programs'] }}</span>
            </div>

            <!-- Stats Section -->
            <div class="hero-stats">
                <div class="hero-stat">
                    <div class="hero-stat-number">8</div>
                    <div class="hero-stat-label">{{ $t['stat_modules'] ?? 'Modullar' }}</div>
                </div>
                <div class="hero-stat">
                    <div class="hero-stat-number">5+</div>
                    <div class="hero-stat-label">{{ $t['stat_months'] ?? 'Oylik dastur' }}</div>
                </div>
                <div class="hero-stat">
                    <div class="hero-stat-number">UN</div>
                    <div class="hero-stat-label">{{ $t['stat_certification'] ?? 'Sertifikat' }}</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Programs Accordion Section -->
<section class="programs-section" id="programs">
    <div class="container">
        <div class="section-header" data-aos="fade-up">
            <h2 class="section-title">{{ $t['hero_title'] }}</h2>
            <p class="section-subtitle">{{ $t['hero_subtitle'] }}</p>
        </div>

        <div class="program-accordion">
            <!-- Module 1 -->
            <div class="accordion-item" data-aos="fade-up" data-aos-delay="0">
                <div class="accordion-header">
                    <div class="accordion-header-left">
                        <div class="accordion-number">1</div>
                        <div>
                            <h3 class="accordion-title">{{ $t['module1_title'] }}</h3>
                            <div class="accordion-date">{{ $t['training_date'] }}: April 21 - May 23</div>
                        </div>
                    </div>
                    <div class="accordion-icon">
                        <i class="fas fa-chevron-down"></i>
                    </div>
                </div>
                <div class="accordion-content">
                    <div class="accordion-objective">
                        <h4>{{ $t['objective'] }}</h4>
                        <p>Gain a comprehensive understanding of sustainability principles and innovative practices in the hospitality industry, and their impact on operational efficiency, marketing performance, customer experience, and compliance with environmental regulations.</p>
                    </div>
                    <div class="accordion-subjects">
                        <h4>{{ $t['subjects'] }}</h4>
                        <ul class="subjects-list">
                            <li><i class="fas fa-check-circle"></i> Principles of Sustainability in Hospitality</li>
                            <li><i class="fas fa-check-circle"></i> Managing Change & Digital Transformation</li>
                            <li><i class="fas fa-check-circle"></i> Innovative, Alternative & Sustainable Trends in Tourism</li>
                            <li><i class="fas fa-check-circle"></i> Latest Trends in Tourism</li>
                            <li><i class="fas fa-check-circle"></i> Integration of Sustainability and Innovation in Hospitality Operations</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Module 2 -->
            <div class="accordion-item" data-aos="fade-up" data-aos-delay="50">
                <div class="accordion-header">
                    <div class="accordion-header-left">
                        <div class="accordion-number">2</div>
                        <div>
                            <h3 class="accordion-title">{{ $t['module2_title'] }}</h3>
                            <div class="accordion-date">{{ $t['training_date'] }}: May 26 - June 27</div>
                        </div>
                    </div>
                    <div class="accordion-icon">
                        <i class="fas fa-chevron-down"></i>
                    </div>
                </div>
                <div class="accordion-content">
                    <div class="accordion-objective">
                        <h4>{{ $t['objective'] }}</h4>
                        <p>The objective of the Entrepreneurship, Business Modeling & Crisis Management module is to equip students with the knowledge and skills necessary to develop and manage successful SME businesses in the hospitality industry. Additionally, students will learn crisis management strategies to ensure business continuity and protect assets and reputation.</p>
                    </div>
                    <div class="accordion-subjects">
                        <h4>{{ $t['subjects'] }}</h4>
                        <ul class="subjects-list">
                            <li><i class="fas fa-check-circle"></i> SME Business Planning</li>
                            <li><i class="fas fa-check-circle"></i> SME Business Management</li>
                            <li><i class="fas fa-check-circle"></i> Maximizing Return on Investment</li>
                            <li><i class="fas fa-check-circle"></i> Advanced Finance & Budgeting</li>
                            <li><i class="fas fa-check-circle"></i> Strategic Management and Business Modeling</li>
                            <li><i class="fas fa-check-circle"></i> Crisis Management</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Module 3 -->
            <div class="accordion-item" data-aos="fade-up" data-aos-delay="100">
                <div class="accordion-header">
                    <div class="accordion-header-left">
                        <div class="accordion-number">3</div>
                        <div>
                            <h3 class="accordion-title">{{ $t['module3_title'] }}</h3>
                            <div class="accordion-date">{{ $t['training_date'] }}: June 30 - July 31</div>
                        </div>
                    </div>
                    <div class="accordion-icon">
                        <i class="fas fa-chevron-down"></i>
                    </div>
                </div>
                <div class="accordion-content">
                    <div class="accordion-objective">
                        <h4>{{ $t['objective'] }}</h4>
                        <p>The objective of the Project Management & International HR module is to provide students with the knowledge and skills to effectively manage projects and human resources in the hospitality industry. Students will learn project management principles and techniques, as well as the practices and challenges of managing an international workforce.</p>
                    </div>
                    <div class="accordion-subjects">
                        <h4>{{ $t['subjects'] }}</h4>
                        <ul class="subjects-list">
                            <li><i class="fas fa-check-circle"></i> Aligning Human Capital Strategy</li>
                            <li><i class="fas fa-check-circle"></i> Project Management in Resort Properties</li>
                            <li><i class="fas fa-check-circle"></i> Project Management</li>
                            <li><i class="fas fa-check-circle"></i> Human Resources Budgeting and Control</li>
                            <li><i class="fas fa-check-circle"></i> International Human Resource Management</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Module 4 -->
            <div class="accordion-item" data-aos="fade-up" data-aos-delay="150">
                <div class="accordion-header">
                    <div class="accordion-header-left">
                        <div class="accordion-number">4</div>
                        <div>
                            <h3 class="accordion-title">{{ $t['module4_title'] }}</h3>
                            <div class="accordion-date">{{ $t['training_date'] }}: August 4 - September 5</div>
                        </div>
                    </div>
                    <div class="accordion-icon">
                        <i class="fas fa-chevron-down"></i>
                    </div>
                </div>
                <div class="accordion-content">
                    <div class="accordion-objective">
                        <h4>{{ $t['objective'] }}</h4>
                        <p>The objective of the Advanced Finance and Budgeting module is to equip students with advanced financial planning and budgeting techniques applicable to the hospitality industry. Students will learn to analyze and estimate revenues and expenses, apply forecasting models, and utilize financial indicators to make informed managerial decisions.</p>
                    </div>
                    <div class="accordion-subjects">
                        <h4>{{ $t['subjects'] }}</h4>
                        <ul class="subjects-list">
                            <li><i class="fas fa-check-circle"></i> Advanced Planning Techniques and Tools</li>
                            <li><i class="fas fa-check-circle"></i> Performance Estimation and Risk</li>
                            <li><i class="fas fa-check-circle"></i> Financial Analysis and Reporting</li>
                            <li><i class="fas fa-check-circle"></i> Cost Control and Revenue Management</li>
                            <li><i class="fas fa-check-circle"></i> Capital Budgeting and Financial Forecasting</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Module 5 -->
            <div class="accordion-item" data-aos="fade-up" data-aos-delay="200">
                <div class="accordion-header">
                    <div class="accordion-header-left">
                        <div class="accordion-number">5</div>
                        <div>
                            <h3 class="accordion-title">{{ $t['module5_title'] }}</h3>
                            <div class="accordion-date">{{ $t['training_date'] }}: October 13 - November 14</div>
                        </div>
                    </div>
                    <div class="accordion-icon">
                        <i class="fas fa-chevron-down"></i>
                    </div>
                </div>
                <div class="accordion-content">
                    <div class="accordion-objective">
                        <h4>{{ $t['objective'] }}</h4>
                        <p>The Revenue Management module focuses on equipping students with the knowledge and skills necessary to design and implement effective revenue and pricing strategies in the hospitality industry. The module covers topics such as pricing and rate management, inventory management, demand forecasting, distribution channels, revenue analysis, and the use of technology and data-driven approaches to optimize revenue and profitability.</p>
                    </div>
                    <div class="accordion-subjects">
                        <h4>{{ $t['subjects'] }}</h4>
                        <ul class="subjects-list">
                            <li><i class="fas fa-check-circle"></i> Revenue & Pricing Management</li>
                            <li><i class="fas fa-check-circle"></i> Hospitality Revenue Management</li>
                            <li><i class="fas fa-check-circle"></i> Revenue and Asset Management for Hotels</li>
                            <li><i class="fas fa-check-circle"></i> Demand Forecasting</li>
                            <li><i class="fas fa-check-circle"></i> Distribution Channels Management</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Module 6 -->
            <div class="accordion-item" data-aos="fade-up" data-aos-delay="250">
                <div class="accordion-header">
                    <div class="accordion-header-left">
                        <div class="accordion-number">6</div>
                        <div>
                            <h3 class="accordion-title">{{ $t['module6_title'] }}</h3>
                            <div class="accordion-date">{{ $t['training_date'] }}: November 17 - December 19</div>
                        </div>
                    </div>
                    <div class="accordion-icon">
                        <i class="fas fa-chevron-down"></i>
                    </div>
                </div>
                <div class="accordion-content">
                    <div class="accordion-objective">
                        <h4>{{ $t['objective'] }}</h4>
                        <p>This module equips students with the knowledge and skills needed to navigate the international hospitality and real estate industries successfully. They will gain a deep understanding of financial analysis, investment strategies, hotel development, asset management, effective communication, teamwork, and continuous professional development.</p>
                    </div>
                    <div class="accordion-subjects">
                        <h4>{{ $t['subjects'] }}</h4>
                        <ul class="subjects-list">
                            <li><i class="fas fa-check-circle"></i> International Hospitality and Real Estate Industries</li>
                            <li><i class="fas fa-check-circle"></i> Financial Analysis and Investment Strategies</li>
                            <li><i class="fas fa-check-circle"></i> Hotel Development and Asset Management</li>
                            <li><i class="fas fa-check-circle"></i> Communication and Interpersonal Skills</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Module 7 -->
            <div class="accordion-item" data-aos="fade-up" data-aos-delay="300">
                <div class="accordion-header">
                    <div class="accordion-header-left">
                        <div class="accordion-number">7</div>
                        <div>
                            <h3 class="accordion-title">{{ $t['module7_title'] }}</h3>
                            <div class="accordion-date">{{ $t['training_date'] }}: January 5 - January 30, 2026</div>
                        </div>
                    </div>
                    <div class="accordion-icon">
                        <i class="fas fa-chevron-down"></i>
                    </div>
                </div>
                <div class="accordion-content">
                    <div class="accordion-objective">
                        <h4>{{ $t['objective'] }}</h4>
                        <p>This module equips students with the necessary knowledge and skills to plan, manage, and execute successful events in the hospitality industry. Students will learn event design, logistics, marketing, and risk management strategies, as well as the protocols and etiquettes associated with different types of events.</p>
                    </div>
                    <div class="accordion-subjects">
                        <h4>{{ $t['subjects'] }}</h4>
                        <ul class="subjects-list">
                            <li><i class="fas fa-check-circle"></i> Event Design and Management</li>
                            <li><i class="fas fa-check-circle"></i> Luxury Event Management and Communication</li>
                            <li><i class="fas fa-check-circle"></i> Protocol & Etiquette</li>
                            <li><i class="fas fa-check-circle"></i> Budgeting and Logistical Planning</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Module 8 -->
            <div class="accordion-item" data-aos="fade-up" data-aos-delay="350">
                <div class="accordion-header">
                    <div class="accordion-header-left">
                        <div class="accordion-number">8</div>
                        <div>
                            <h3 class="accordion-title">{{ $t['module8_title'] }}</h3>
                            <div class="accordion-date">{{ $t['training_date'] }}: September 8 - October 10</div>
                        </div>
                    </div>
                    <div class="accordion-icon">
                        <i class="fas fa-chevron-down"></i>
                    </div>
                </div>
                <div class="accordion-content">
                    <div class="accordion-objective">
                        <h4>{{ $t['objective'] }}</h4>
                        <p>The Marketing Strategies & Data Analytics for Hotel Management module aims to provide students with a comprehensive understanding of marketing strategies, digital marketing, data analytics, and experiential marketing in the hotel management industry.</p>
                    </div>
                    <div class="accordion-subjects">
                        <h4>{{ $t['subjects'] }}</h4>
                        <ul class="subjects-list">
                            <li><i class="fas fa-check-circle"></i> Digital Marketing and Sales</li>
                            <li><i class="fas fa-check-circle"></i> Marketing Strategies for Hotel Management</li>
                            <li><i class="fas fa-check-circle"></i> Data Analytics for Business Optimization</li>
                            <li><i class="fas fa-check-circle"></i> Experiential Marketing</li>
                            <li><i class="fas fa-check-circle"></i> Global Strategic Marketing</li>
                            <li><i class="fas fa-check-circle"></i> Customer Relationship Management</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- What You Get Section -->
<section class="what-you-get-section">
    <div class="container">
        <div class="section-header" data-aos="fade-up">
            <h2 class="section-title">{{ $t['what_you_get'] }}</h2>
            <p class="section-subtitle">{{ $t['what_you_get_subtitle'] }}</p>
        </div>

        <div class="benefits-grid">
            <!-- Customer Service Excellence -->
            <div class="benefit-card" data-aos="fade-up" data-aos-delay="0">
                <div class="benefit-content">
                    <h4 class="benefit-title">Customer Service Excellence</h4>
                    <ul class="benefit-text">
                        <li>Ability to deliver high-quality service and ensure customer satisfaction</li>
                        <li>Understanding how to handle complaints and improve guest experiences</li>
                    </ul>
                </div>
                <div class="benefit-icon">
                    <i class="fas fa-star"></i>
                </div>
            </div>

            <!-- Food and Beverage Management -->
            <div class="benefit-card" data-aos="fade-up" data-aos-delay="50">
                <div class="benefit-content">
                    <h4 class="benefit-title">Food and Beverage Management</h4>
                    <ul class="benefit-text">
                        <li>Basics of menu planning, food safety standards, and catering operations</li>
                        <li>Understanding cost control and quality assurance in food services</li>
                    </ul>
                </div>
                <div class="benefit-icon">
                    <i class="fas fa-utensils"></i>
                </div>
            </div>

            <!-- Operational Management Skills -->
            <div class="benefit-card" data-aos="fade-up" data-aos-delay="100">
                <div class="benefit-content">
                    <h4 class="benefit-title">Operational Management Skills</h4>
                    <ul class="benefit-text">
                        <li>Proficiency in managing daily operations such as reservations, housekeeping, and front office tasks</li>
                        <li>Familiarity with hospitality software for bookings, inventory, and billing</li>
                    </ul>
                </div>
                <div class="benefit-icon">
                    <i class="fas fa-cogs"></i>
                </div>
            </div>

            <!-- Tourism Integration -->
            <div class="benefit-card" data-aos="fade-up" data-aos-delay="150">
                <div class="benefit-content">
                    <h4 class="benefit-title">Tourism Integration</h4>
                    <ul class="benefit-text">
                        <li>Knowledge of how hospitality contributes to the tourism industry</li>
                        <li>Familiarity with the roles of travel agencies and online platforms</li>
                    </ul>
                </div>
                <div class="benefit-icon">
                    <i class="fas fa-globe"></i>
                </div>
            </div>

            <!-- Event Planning and Coordination -->
            <div class="benefit-card" data-aos="fade-up" data-aos-delay="200">
                <div class="benefit-content">
                    <h4 class="benefit-title">Event Planning and Coordination</h4>
                    <ul class="benefit-text">
                        <li>Skills in organizing and managing events, meetings, and conferences</li>
                        <li>Budgeting and logistical planning expertise</li>
                    </ul>
                </div>
                <div class="benefit-icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
            </div>

            <!-- Team Management -->
            <div class="benefit-card" data-aos="fade-up" data-aos-delay="250">
                <div class="benefit-content">
                    <h4 class="benefit-title">Team Management</h4>
                    <ul class="benefit-text">
                        <li>Skills in supervising and motivating hospitality staff</li>
                        <li>Understanding team dynamics and conflict resolution</li>
                    </ul>
                </div>
                <div class="benefit-icon">
                    <i class="fas fa-users"></i>
                </div>
            </div>

            <!-- Hospitality Trends -->
            <div class="benefit-card" data-aos="fade-up" data-aos-delay="300">
                <div class="benefit-content">
                    <h4 class="benefit-title">Hospitality Trends</h4>
                    <ul class="benefit-text">
                        <li>Awareness of current trends such as sustainable tourism, digitalization, and luxury services</li>
                        <li>Insights into changing consumer preferences in the hospitality sector</li>
                    </ul>
                </div>
                <div class="benefit-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
            </div>

            <!-- Marketing Skills -->
            <div class="benefit-card" data-aos="fade-up" data-aos-delay="350">
                <div class="benefit-content">
                    <h4 class="benefit-title">Marketing Skills</h4>
                    <ul class="benefit-text">
                        <li>Knowledge of promoting hospitality services through traditional and digital marketing</li>
                        <li>Understanding customer loyalty programs and branding strategies</li>
                    </ul>
                </div>
                <div class="benefit-icon">
                    <i class="fas fa-bullhorn"></i>
                </div>
            </div>

            <!-- Networking -->
            <div class="benefit-card" data-aos="fade-up" data-aos-delay="400">
                <div class="benefit-content">
                    <h4 class="benefit-title">Networking</h4>
                    <ul class="benefit-text">
                        <li>Opportunities to connect with industry professionals and peers</li>
                        <li>Fostering valuable professional relationships</li>
                    </ul>
                </div>
                <div class="benefit-icon">
                    <i class="fas fa-handshake"></i>
                </div>
            </div>

            <!-- Additional Features -->
            <div class="benefit-card" data-aos="fade-up" data-aos-delay="450">
                <div class="benefit-content">
                    <h4 class="benefit-title">Additional Features</h4>
                    <ul class="benefit-text">
                        <li>Access to internship opportunities at renowned hospitality establishments</li>
                        <li>Exposure to UN Tourism projects and partnerships, creating pathways for global career prospects</li>
                    </ul>
                </div>
                <div class="benefit-icon">
                    <i class="fas fa-plus-circle"></i>
                </div>
            </div>

            <!-- Improved Employability -->
            <div class="benefit-card" data-aos="fade-up" data-aos-delay="500">
                <div class="benefit-content">
                    <h4 class="benefit-title">Improved Employability</h4>
                    <ul class="benefit-text">
                        <li>Qualification opens doors to roles such as front office manager, guest service supervisor, or event coordinator</li>
                        <li>Boosted credibility and improved chances of securing managerial positions</li>
                    </ul>
                </div>
                <div class="benefit-icon">
                    <i class="fas fa-briefcase"></i>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Accordion functionality
    const accordionHeaders = document.querySelectorAll('.accordion-header');

    accordionHeaders.forEach(function(header) {
        header.addEventListener('click', function() {
            const item = this.parentElement;
            const isActive = item.classList.contains('active');

            // Close all accordions
            document.querySelectorAll('.accordion-item').forEach(function(acc) {
                acc.classList.remove('active');
            });

            // Open clicked one if it wasn't active
            if (!isActive) {
                item.classList.add('active');
            }
        });
    });

    // Open first accordion by default
    const firstAccordion = document.querySelector('.accordion-item');
    if (firstAccordion) {
        firstAccordion.classList.add('active');
    }
});
</script>

@endsection
