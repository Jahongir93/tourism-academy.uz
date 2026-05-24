@extends(\App\Helpers\TemplateHelper::getLayout())

@section('title', 'Biz haqimizda - Tourism Academy Samarkand')

@section('styles')
<style>
    /* About Page Styles - PDF Design */
    .about-hero {
        background: linear-gradient(135deg, rgba(26, 26, 46, 0.9) 0%, rgba(26, 26, 46, 0.7) 100%),
                    url('{{ asset("images/about-hero.jpg") }}') center/cover no-repeat;
        min-height: 400px;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        padding: 80px 20px;
    }

    .about-hero-content {
        max-width: 800px;
    }

    .about-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #C8E637;
        color: #1a1a2e;
        padding: 8px 20px;
        border-radius: 30px;
        font-size: 0.85rem;
        font-weight: 600;
        margin-bottom: 20px;
    }

    .about-hero h1 {
        color: #fff;
        font-size: 3rem;
        font-weight: 700;
        margin-bottom: 20px;
        line-height: 1.2;
    }

    .about-hero p {
        color: rgba(255, 255, 255, 0.85);
        font-size: 1.1rem;
        line-height: 1.7;
    }

    /* Section Styles */
    .about-section {
        padding: 80px 0;
    }

    .section-header {
        text-align: center;
        margin-bottom: 50px;
    }

    .section-badge {
        display: inline-block;
        background: rgba(200, 230, 55, 0.15);
        color: #1a1a2e;
        padding: 6px 16px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
        margin-bottom: 15px;
    }

    .section-title {
        font-size: 2.2rem;
        font-weight: 700;
        color: #1a1a2e;
        margin-bottom: 15px;
    }

    .section-subtitle {
        color: #6b7280;
        font-size: 1rem;
        max-width: 600px;
        margin: 0 auto;
    }

    /* Mission & Vision Cards */
    .mv-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 30px;
    }

    .mv-card {
        background: #fff;
        border-radius: 16px;
        padding: 40px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .mv-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background: #C8E637;
    }

    .mv-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
    }

    .mv-card-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        background: rgba(200, 230, 55, 0.15);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 20px;
    }

    .mv-card-icon i {
        font-size: 1.5rem;
        color: #1a1a2e;
    }

    .mv-card h3 {
        font-size: 1.4rem;
        font-weight: 700;
        color: #1a1a2e;
        margin-bottom: 15px;
    }

    .mv-card p {
        color: #6b7280;
        line-height: 1.7;
    }

    /* Values Section */
    .values-section {
        background: #f8f9fa;
    }

    .values-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 25px;
    }

    .value-card {
        background: #fff;
        border-radius: 16px;
        padding: 30px;
        text-align: center;
        transition: all 0.3s ease;
        border: 2px solid transparent;
    }

    .value-card:hover {
        border-color: #C8E637;
        transform: translateY(-5px);
    }

    .value-icon {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        margin: 0 auto 20px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .value-icon.green { background: #E8F5E9; }
    .value-icon.yellow { background: #FFF9C4; }
    .value-icon.orange { background: #FFE0B2; }
    .value-icon.blue { background: #E3F2FD; }

    .value-icon i {
        font-size: 1.8rem;
        color: #1a1a2e;
    }

    .value-card h4 {
        font-size: 1.1rem;
        font-weight: 600;
        color: #1a1a2e;
        margin-bottom: 10px;
    }

    .value-card p {
        color: #6b7280;
        font-size: 0.9rem;
        line-height: 1.6;
    }

    /* History Section */
    .history-content {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 50px;
        align-items: center;
    }

    .history-image {
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
    }

    .history-image img {
        width: 100%;
        height: auto;
        display: block;
    }

    .history-text h3 {
        font-size: 1.8rem;
        font-weight: 700;
        color: #1a1a2e;
        margin-bottom: 20px;
    }

    .history-text p {
        color: #6b7280;
        line-height: 1.8;
        margin-bottom: 15px;
    }

    /* Stats Section */
    .stats-section {
        background: #1a1a2e;
        padding: 60px 0;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 30px;
    }

    .stat-item {
        text-align: center;
    }

    .stat-number {
        font-size: 3rem;
        font-weight: 700;
        color: #C8E637;
        margin-bottom: 10px;
    }

    .stat-label {
        color: rgba(255, 255, 255, 0.8);
        font-size: 0.95rem;
    }

    /* Team Section */
    .team-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 25px;
    }

    .team-card {
        background: #fff;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
    }

    .team-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
    }

    .team-card-image {
        height: 250px;
        background: #f0f0f0;
        overflow: hidden;
    }

    .team-card-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .team-card-info {
        padding: 20px;
        text-align: center;
    }

    .team-card-info h4 {
        font-size: 1.1rem;
        font-weight: 600;
        color: #1a1a2e;
        margin-bottom: 5px;
    }

    .team-card-info p {
        color: #6b7280;
        font-size: 0.9rem;
    }

    /* CTA Section */
    .cta-section {
        background: linear-gradient(135deg, #1a1a2e 0%, #2d2d4a 100%);
        padding: 80px 0;
        text-align: center;
    }

    .cta-content h2 {
        color: #fff;
        font-size: 2.2rem;
        font-weight: 700;
        margin-bottom: 15px;
    }

    .cta-content p {
        color: rgba(255, 255, 255, 0.8);
        font-size: 1.1rem;
        margin-bottom: 30px;
        max-width: 600px;
        margin-left: auto;
        margin-right: auto;
    }

    .btn-cta {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        background: #C8E637;
        color: #1a1a2e;
        padding: 14px 30px;
        border-radius: 8px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .btn-cta:hover {
        background: #b8d627;
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(200, 230, 55, 0.4);
        color: #1a1a2e;
    }

    /* Responsive */
    @media (max-width: 991px) {
        .about-hero h1 { font-size: 2.2rem; }
        .mv-grid, .values-grid, .team-grid, .stats-grid { grid-template-columns: repeat(2, 1fr); }
        .history-content { grid-template-columns: 1fr; }
    }

    @media (max-width: 575px) {
        .about-hero { min-height: 300px; padding: 60px 20px; }
        .about-hero h1 { font-size: 1.8rem; }
        .mv-grid, .values-grid, .team-grid, .stats-grid { grid-template-columns: 1fr; }
        .section-title { font-size: 1.8rem; }
    }
</style>
@endsection

@section('content')
<!-- Hero Section -->
<section class="about-hero">
    <div class="about-hero-content" data-aos="fade-up">
        <div class="about-badge">
            <i class="fas fa-university"></i>
            <span>Akademiya haqida</span>
        </div>
        <h1>International Academy of Tourism and Hospitality</h1>
        <p>UN Tourism bilan hamkorlikdagi Samarqand Xalqaro Turizm va Mehmondo'stlik Akademiyasi – zamonaviy ta'lim va yuqori malakali kadrlar tayyorlash markazi.</p>
    </div>
</section>

<!-- Mission & Vision Section -->
<section class="about-section">
    <div class="container">
        <div class="section-header" data-aos="fade-up">
            <span class="section-badge">Bizning yo'nalishimiz</span>
            <h2 class="section-title">Missiya va Vizyon</h2>
            <p class="section-subtitle">Akademiyamizning asosiy maqsadlari va kelajak rejalarimiz</p>
        </div>

        <div class="mv-grid">
            <div class="mv-card" data-aos="fade-right">
                <div class="mv-card-icon">
                    <i class="fas fa-bullseye"></i>
                </div>
                <h3>Bizning Missiyamiz</h3>
                <p>Xalqaro standartlarga mos keladigan, raqobatbardosh mutaxassislar tayyorlash va turizm sohasini rivojlantirishga hissa qo'shish. Innovatsion ta'lim metodlari va zamonaviy yondashuvlar orqali talabalarni kelajakka tayyorlash.</p>
            </div>

            <div class="mv-card" data-aos="fade-left">
                <div class="mv-card-icon">
                    <i class="fas fa-eye"></i>
                </div>
                <h3>Bizning Vizyonimiz</h3>
                <p>Markaziy Osiyoda turizm ta'limi sohasida yetakchi akademiyaga aylanish va xalqaro hamkorliklarni kengaytirish. Global miqyosda tan olingan mutaxassislar tayyorlash.</p>
            </div>
        </div>
    </div>
</section>

<!-- Values Section -->
<section class="about-section values-section">
    <div class="container">
        <div class="section-header" data-aos="fade-up">
            <span class="section-badge">Asosiy tamoyillar</span>
            <h2 class="section-title">Bizning Qadriyatlarimiz</h2>
            <p class="section-subtitle">Akademiyamiz faoliyatining asosiy yo'nalishlari</p>
        </div>

        <div class="values-grid">
            <div class="value-card" data-aos="fade-up" data-aos-delay="0">
                <div class="value-icon green">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <h4>Sifatli Ta'lim</h4>
                <p>Xalqaro standartlarga mos keladigan zamonaviy ta'lim dasturlari</p>
            </div>

            <div class="value-card" data-aos="fade-up" data-aos-delay="100">
                <div class="value-icon yellow">
                    <i class="fas fa-lightbulb"></i>
                </div>
                <h4>Innovatsiya</h4>
                <p>Yangi texnologiyalar va metodlarni ta'lim jarayoniga joriy etish</p>
            </div>

            <div class="value-card" data-aos="fade-up" data-aos-delay="200">
                <div class="value-icon orange">
                    <i class="fas fa-globe"></i>
                </div>
                <h4>Xalqaro Hamkorlik</h4>
                <p>Dunyo bo'ylab yetakchi universitetlar bilan hamkorlik</p>
            </div>

            <div class="value-card" data-aos="fade-up" data-aos-delay="300">
                <div class="value-icon blue">
                    <i class="fas fa-briefcase"></i>
                </div>
                <h4>Amaliyot</h4>
                <p>Nazariy bilimlarni amaliyotda qo'llash imkoniyati</p>
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="stats-section">
    <div class="container">
        <div class="stats-grid">
            <div class="stat-item" data-aos="fade-up" data-aos-delay="0">
                <div class="stat-number">15+</div>
                <div class="stat-label">Yillik Tajriba</div>
            </div>
            <div class="stat-item" data-aos="fade-up" data-aos-delay="100">
                <div class="stat-number">5000+</div>
                <div class="stat-label">Bitiruvchilar</div>
            </div>
            <div class="stat-item" data-aos="fade-up" data-aos-delay="200">
                <div class="stat-number">50+</div>
                <div class="stat-label">Professor-o'qituvchilar</div>
            </div>
            <div class="stat-item" data-aos="fade-up" data-aos-delay="300">
                <div class="stat-number">10+</div>
                <div class="stat-label">Xalqaro Hamkorlar</div>
            </div>
        </div>
    </div>
</section>

<!-- History Section -->
<section class="about-section">
    <div class="container">
        <div class="history-content">
            <div class="history-image" data-aos="fade-right">
                <img src="{{ asset('images/academy-building.jpg') }}" alt="Akademiya binosi" onerror="this.src='https://images.unsplash.com/photo-1562774053-701939374585?w=600'">
            </div>
            <div class="history-text" data-aos="fade-left">
                <h3>Akademiya Tarixi</h3>
                <p>Samarqand Xalqaro Turizm va Mehmondo'stlik Akademiyasi O'zbekiston hukumati va Birlashgan Millatlar Tashkilotining Jahon Turizm Tashkiloti (UNWTO) hamkorligida tashkil etilgan.</p>
                <p>Akademiya turizm sohasida yuqori malakali kadrlar tayyorlash, ilmiy-tadqiqot ishlarini olib borish va xalqaro hamkorlikni rivojlantirish maqsadida faoliyat yuritadi.</p>
                <p>Bugungi kunda akademiya talabalarni bakalavr, magistratura va doktorantura yo'nalishlari bo'yicha tayyorlamoqda. Shuningdek, qisqa muddatli malaka oshirish kurslari ham tashkil etilgan.</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section">
    <div class="container">
        <div class="cta-content" data-aos="fade-up">
            <h2>Bizga Qo'shiling!</h2>
            <p>Turizm sohasida karyerangizni boshlash uchun eng yaxshi tanlov. Hoziroq ro'yxatdan o'ting!</p>
            <a href="{{ route('contact') }}" class="btn-cta">
                <span>Bog'lanish</span>
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
</section>
@endsection
