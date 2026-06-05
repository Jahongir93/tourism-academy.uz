@extends(\App\Helpers\TemplateHelper::getLayout())

@php
    use App\Models\CmsContent;

    // Get contact page content from CMS
    $contactContents = CmsContent::where('section', 'contact')->get()->keyBy('key');

    // Get current language
    $lang = app()->getLocale() ?? 'uz';
    $langField = 'value_' . $lang;

    // Helper function to get content
    $getContent = function($key, $default = '') use ($contactContents, $langField) {
        $content = $contactContents->get($key);
        return $content ? ($content->$langField ?? $content->value_uz ?? $default) : $default;
    };
@endphp

@section('title', $getContent('contact_page_title', "Bog'lanish") . ' - Tourism Academy Samarkand')

@section('styles')
<style>
    /* Contact Hero Section - Blue Design */
    .contact-hero {
        min-height: 55vh;
        padding-top: 140px;
        padding-bottom: 80px;
        background: linear-gradient(135deg, #1e3a5f 0%, #0d1b2a 50%, #1b263b 100%);
        display: flex;
        align-items: center;
        position: relative;
        overflow: hidden;
    }

    .contact-hero::before {
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

    .contact-hero .container {
        position: relative;
        z-index: 2;
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

    .contact-hero h1 {
        color: #ffffff;
        font-size: 3rem;
        font-weight: 800;
        margin-bottom: 20px;
        text-shadow: 0 4px 30px rgba(0, 0, 0, 0.3);
        line-height: 1.2;
    }

    .contact-hero p {
        color: #cbd5e1;
        font-size: 1.2rem;
        max-width: 700px;
        line-height: 1.7;
        margin-bottom: 0;
    }

    .hero-breadcrumb {
        margin-top: 30px;
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

    /* Contact Section */
    .contact-section {
        padding: 80px 0;
    }

    .contact-grid {
        display: grid;
        grid-template-columns: 1fr 2fr;
        gap: 40px;
    }

    /* Info Cards */
    .contact-info-card {
        background: #fff;
        border-radius: 16px;
        padding: 30px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    }

    .info-item {
        display: flex;
        align-items: flex-start;
        gap: 15px;
        padding: 20px 0;
        border-bottom: 1px solid #f0f0f0;
    }

    .info-item:last-child {
        border-bottom: none;
    }

    .info-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .info-icon.green { background: #E8F5E9; }
    .info-icon.blue { background: #E3F2FD; }
    .info-icon.orange { background: #FFE0B2; }
    .info-icon.purple { background: #F3E5F5; }

    .info-icon i {
        font-size: 1.2rem;
        color: #1a1a2e;
    }

    .info-content h4 {
        font-size: 1rem;
        font-weight: 600;
        color: #1a1a2e;
        margin-bottom: 5px;
    }

    .info-content p {
        color: #6b7280;
        font-size: 0.9rem;
        margin: 0;
        line-height: 1.6;
    }

    /* Working Hours */
    .working-hours {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 20px;
        margin-top: 20px;
    }

    .working-hours h5 {
        font-size: 1rem;
        font-weight: 600;
        color: #1a1a2e;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .working-hours h5 i {
        color: #C8E637;
    }

    .hours-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .hours-list li {
        display: flex;
        justify-content: space-between;
        padding: 8px 0;
        font-size: 0.9rem;
        color: #6b7280;
    }

    .hours-list li span:last-child {
        font-weight: 500;
        color: #1a1a2e;
    }

    .hours-list li.closed span:last-child {
        color: #ef4444;
    }

    /* Social Links */
    .social-section {
        margin-top: 25px;
        padding-top: 20px;
        border-top: 1px solid #f0f0f0;
    }

    .social-section h5 {
        font-size: 1rem;
        font-weight: 600;
        color: #1a1a2e;
        margin-bottom: 15px;
    }

    .social-links {
        display: flex;
        gap: 10px;
    }

    .social-link {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background: rgba(200, 230, 55, 0.15);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #1a1a2e;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .social-link:hover {
        background: #C8E637;
        transform: translateY(-3px);
    }

    /* Contact Form */
    .contact-form-card {
        background: #fff;
        border-radius: 16px;
        padding: 40px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    }

    .form-header {
        margin-bottom: 30px;
    }

    .form-header h3 {
        font-size: 1.5rem;
        font-weight: 700;
        color: #1a1a2e;
        margin-bottom: 10px;
    }

    .form-header p {
        color: #6b7280;
        font-size: 0.95rem;
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }

    .form-group {
        margin-bottom: 0;
    }

    .form-group.full-width {
        grid-column: 1 / -1;
    }

    .form-label {
        display: block;
        font-size: 0.9rem;
        font-weight: 500;
        color: #1a1a2e;
        margin-bottom: 8px;
    }

    .form-control {
        width: 100%;
        padding: 12px 16px;
        border: 2px solid #e5e7eb;
        border-radius: 10px;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        background: #fff;
        color: #1a1a2e;
    }

    .form-control:focus {
        outline: none;
        border-color: #C8E637;
        box-shadow: 0 0 0 3px rgba(200, 230, 55, 0.2);
    }

    .form-control::placeholder {
        color: #9ca3af;
    }

    textarea.form-control {
        resize: vertical;
        min-height: 120px;
    }

    .form-select {
        width: 100%;
        padding: 12px 16px;
        border: 2px solid #e5e7eb;
        border-radius: 10px;
        font-size: 0.95rem;
        background: #fff;
        color: #1a1a2e;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .form-select:focus {
        outline: none;
        border-color: #C8E637;
        box-shadow: 0 0 0 3px rgba(200, 230, 55, 0.2);
    }

    .submit-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        width: 100%;
        padding: 14px 30px;
        background: #1a1a2e;
        color: #fff;
        border: none;
        border-radius: 10px;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .submit-btn:hover {
        background: #C8E637;
        color: #1a1a2e;
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(200, 230, 55, 0.4);
    }

    /* Map Section */
    .map-section {
        padding: 0 0 80px;
    }

    .map-container {
        background: #fff;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        height: 400px;
    }

    .map-container iframe {
        width: 100%;
        height: 100%;
        border: none;
    }

    .map-placeholder {
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, #f8f9fa, #e9ecef);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        color: #6b7280;
    }

    .map-placeholder i {
        font-size: 3rem;
        color: #C8E637;
        margin-bottom: 15px;
    }

    /* FAQ Section */
    .faq-section {
        background: #f8f9fa;
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

    .faq-grid {
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
        border-color: #C8E637;
        box-shadow: 0 6px 18px rgba(200, 230, 55, 0.18);
        transform: translateY(-1px);
    }

    .faq-item.active {
        border-color: #C8E637;
        box-shadow: 0 10px 28px rgba(200, 230, 55, 0.22);
    }

    .faq-question {
        width: 100%;
        padding: 24px 28px;
        background: transparent;
        border: none;
        text-align: left;
        font-size: 1.02rem;
        font-weight: 600;
        color: #1a1a2e;
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 18px;
        transition: background 0.25s ease;
    }

    .faq-question:hover {
        background: rgba(200, 230, 55, 0.06);
    }

    .faq-question span {
        flex: 1;
        line-height: 1.55;
        padding-right: 4px;
    }

    .faq-question i {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background: rgba(200, 230, 55, 0.18);
        color: #1a1a2e;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.85rem;
        flex-shrink: 0;
        transition: background 0.3s ease, color 0.3s ease, transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .faq-item.active .faq-question i,
    .faq-question.active i {
        background: #C8E637;
        color: #1a1a2e;
        transform: rotate(180deg);
    }

    .faq-answer {
        max-height: 0;
        overflow: hidden;
        padding: 0;
        color: #4b5563;
        line-height: 1.75;
        font-size: 0.95rem;
        transition: max-height 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .faq-item.active .faq-answer {
        max-height: 1500px;
    }

    .faq-answer-inner {
        padding: 4px 28px 26px;
    }

    /* Alert */
    .alert-success {
        background: rgba(200, 230, 55, 0.15);
        border: 1px solid #C8E637;
        color: #1a1a2e;
        padding: 15px 20px;
        border-radius: 10px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .alert-success i {
        color: #C8E637;
    }

    /* Responsive */
    @media (max-width: 991px) {
        .contact-hero h1 { font-size: 2.3rem; }
        .contact-hero p { font-size: 1rem; }
        .contact-grid { grid-template-columns: 1fr; }
        .form-grid { grid-template-columns: 1fr; }
    }

    @media (max-width: 768px) {
        .contact-hero {
            padding-top: 120px;
            min-height: 45vh;
        }
        .contact-hero h1 { font-size: 1.8rem; }
        .contact-hero p { font-size: 0.95rem; }
        .hero-badge {
            padding: 10px 20px;
            font-size: 0.8rem;
        }
    }

    @media (max-width: 575px) {
        .contact-hero {
            min-height: 40vh;
            padding-top: 110px;
            padding-bottom: 60px;
        }
        .contact-hero h1 { font-size: 1.6rem; }
        .section-title { font-size: 1.8rem; }
        .contact-form-card { padding: 25px; }
    }
</style>
@endsection

@section('content')
<!-- Hero Section -->
<section class="contact-hero">
    <div class="container">
        <div data-aos="fade-up">
            <div class="hero-badge">
                <i class="fas fa-envelope"></i>
                {{ $getContent('contact_hero_badge', "BOG'LANISH") }}
            </div>
            <h1>{{ $getContent('contact_hero_title', "Biz Bilan Bog'laning") }}</h1>
            <p>{{ $getContent('contact_hero_subtitle', 'Savollaringiz bormi? Biz doimo aloqadamiz va sizga yordam berishga tayyormiz!') }}</p>
            <div class="hero-breadcrumb">
                <a href="{{ route('home') }}">{{ $getContent('contact_breadcrumb_home', 'Bosh sahifa') }}</a>
                <span>/</span>
                <span class="current">{{ $getContent('contact_breadcrumb_contact', "Bog'lanish") }}</span>
            </div>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section class="contact-section">
    <div class="container">
        <div class="contact-grid">
            <!-- Contact Info -->
            <div class="contact-info-card" data-aos="fade-right">
                <div class="info-item">
                    <div class="info-icon green">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <div class="info-content">
                        <h4>{{ $getContent('contact_address_title', 'Manzil') }}</h4>
                        <p>{!! $getContent('contact_address_text', "Samarqand shahar,<br>Istiqlol ko'chasi, 47") !!}</p>
                    </div>
                </div>

                <div class="info-item">
                    <div class="info-icon blue">
                        <i class="fas fa-phone"></i>
                    </div>
                    <div class="info-content">
                        <h4>{{ $getContent('contact_phone_title', 'Telefon') }}</h4>
                        <p>{!! $getContent('contact_phone_text', '+998 66 233 XX XX<br>+998 66 233 XX XX') !!}</p>
                    </div>
                </div>

                <div class="info-item">
                    <div class="info-icon orange">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div class="info-content">
                        <h4>{{ $getContent('contact_email_title', 'Email') }}</h4>
                        <p>{!! $getContent('contact_email_text', 'info@tourism.uz<br>admin@tourism.uz') !!}</p>
                    </div>
                </div>

                <div class="working-hours">
                    <h5><i class="far fa-clock"></i> {{ $getContent('contact_hours_title', 'Ish vaqti') }}</h5>
                    <ul class="hours-list">
                        <li>
                            <span>{{ $getContent('contact_hours_weekdays', 'Dushanba - Juma') }}</span>
                            <span>09:00 - 18:00</span>
                        </li>
                        <li>
                            <span>{{ $getContent('contact_hours_saturday', 'Shanba') }}</span>
                            <span>09:00 - 14:00</span>
                        </li>
                        <li class="closed">
                            <span>{{ $getContent('contact_hours_sunday', 'Yakshanba') }}</span>
                            <span>{{ $getContent('contact_hours_closed', 'Dam olish') }}</span>
                        </li>
                    </ul>
                </div>

                <div class="social-section">
                    <h5>{{ $getContent('contact_social_title', 'Ijtimoiy tarmoqlar') }}</h5>
                    <div class="social-links">
                        <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-telegram-plane"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
            </div>

            <!-- Contact Form -->
            <div class="contact-form-card" data-aos="fade-left">
                <div class="form-header">
                    <h3>{{ $getContent('contact_form_title', 'Xabar yuborish') }}</h3>
                    <p>{{ $getContent('contact_form_subtitle', "Formani to'ldiring va biz siz bilan tez orada bog'lanamiz") }}</p>
                </div>

                @if(session('success'))
                <div class="alert-success">
                    <i class="fas fa-check-circle"></i>
                    <span>{{ session('success') }}</span>
                </div>
                @endif

                <form action="{{ route('contact.submit') }}" method="POST">
                    @csrf
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">{{ $getContent('contact_form_name', 'Ismingiz') }} *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                   name="name" value="{{ old('name') }}" placeholder="{{ $getContent('contact_form_name_placeholder', 'Ismingizni kiriting') }}" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">{{ $getContent('contact_form_email', 'Email') }} *</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                   name="email" value="{{ old('email') }}" placeholder="email@example.com" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">{{ $getContent('contact_form_phone', 'Telefon') }}</label>
                            <input type="tel" class="form-control" name="phone" value="{{ old('phone') }}"
                                   placeholder="+998 XX XXX XX XX">
                        </div>

                        <div class="form-group">
                            <label class="form-label">{{ $getContent('contact_form_subject', 'Mavzu') }} *</label>
                            <select class="form-select @error('subject') is-invalid @enderror" name="subject" required>
                                <option value="">{{ $getContent('contact_form_select', 'Tanlang...') }}</option>
                                <option value="umumiy">{{ $getContent('contact_form_subject_general', 'Umumiy savol') }}</option>
                                <option value="qabul">{{ $getContent('contact_form_subject_admission', 'Qabul masalalari') }}</option>
                                <option value="talim">{{ $getContent('contact_form_subject_education', "Ta'lim jarayoni") }}</option>
                                <option value="hamkorlik">{{ $getContent('contact_form_subject_partnership', 'Hamkorlik') }}</option>
                                <option value="boshqa">{{ $getContent('contact_form_subject_other', 'Boshqa') }}</option>
                            </select>
                        </div>

                        <div class="form-group full-width">
                            <label class="form-label">{{ $getContent('contact_form_message', 'Xabar matni') }} *</label>
                            <textarea class="form-control @error('message') is-invalid @enderror"
                                      name="message" rows="5" placeholder="{{ $getContent('contact_form_message_placeholder', 'Xabaringizni yozing...') }}" required>{{ old('message') }}</textarea>
                        </div>

                        <div class="form-group full-width">
                            <button type="submit" class="submit-btn">
                                <i class="fas fa-paper-plane"></i>
                                <span>{{ $getContent('contact_form_submit', 'Xabar yuborish') }}</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Map Section -->
<section class="map-section">
    <div class="container">
        @php
            // Xarita: CMS'dan boshqariladi (embed URL yoki to'liq iframe paste qilingan bo'lishi mumkin)
            $mapDefault = 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3067.8897645692387!2d66.95746731534896!3d39.65480397946463!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3f4d191960077df7%3A0x487736c5d08182a7!2sSamarkand%20Institute%20of%20Economics%20and%20Service!5e0!3m2!1sen!2s!4v1703000000000!5m2!1sen!2s';
            $mapRaw = $getContent('contact_map_embed', $mapDefault) ?: $mapDefault;
            // Agar to'liq <iframe ...> paste qilingan bo'lsa — src ni ajratib olamiz
            if (preg_match('/src=["\\\']([^"\\\']+)["\\\']/', $mapRaw, $mm)) {
                $mapSrc = $mm[1];
            } else {
                $mapSrc = trim($mapRaw);
            }
        @endphp
        <div class="map-container" data-aos="zoom-in">
            <iframe
                src="{{ $mapSrc }}"
                width="100%"
                height="100%"
                style="border:0;"
                allowfullscreen=""
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade">
            </iframe>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="faq-section">
    <div class="container">
        <div class="section-header" data-aos="fade-up">
            <span class="section-badge">{{ $getContent('contact_faq_badge', 'FAQ') }}</span>
            <h2 class="section-title">{{ $getContent('contact_faq_title', "Ko'p Beriladigan Savollar") }}</h2>
        </div>

        <div class="faq-grid">
            <div class="faq-item active" data-aos="fade-up" data-aos-delay="0">
                <button class="faq-question active" type="button">
                    <span>{{ $getContent('contact_faq1_question', 'Qabul jarayoni qanday amalga oshiriladi?') }}</span>
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div class="faq-answer">
                    <div class="faq-answer-inner">
                        {{ $getContent('contact_faq1_answer', "Qabul jarayoni har yili iyul-avgust oylarida amalga oshiriladi. Abituriyentlar dtm.uz sayti orqali ro'yxatdan o'tadilar va test sinovlarida qatnashadilar.") }}
                    </div>
                </div>
            </div>

            <div class="faq-item" data-aos="fade-up" data-aos-delay="100">
                <button class="faq-question" type="button">
                    <span>{{ $getContent('contact_faq2_question', "Kontrakt to'lovi qancha?") }}</span>
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div class="faq-answer">
                    <div class="faq-answer-inner">
                        {{ $getContent('contact_faq2_answer', "Kontrakt to'lovi yo'nalishga qarab farq qiladi. Aniq ma'lumot uchun qabul komissiyasiga murojaat qiling yoki rasmiy veb-saytimizni tekshiring.") }}
                    </div>
                </div>
            </div>

            <div class="faq-item" data-aos="fade-up" data-aos-delay="200">
                <button class="faq-question" type="button">
                    <span>{{ $getContent('contact_faq3_question', 'Talabalar turar joyi bormi?') }}</span>
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div class="faq-answer">
                    <div class="faq-answer-inner">
                        {{ $getContent('contact_faq3_answer', "Ha, akademiyamizda talabalar uchun zamonaviy talabalar turar joyi mavjud. Barcha qulayliklar bilan jihozlangan.") }}
                    </div>
                </div>
            </div>

            <div class="faq-item" data-aos="fade-up" data-aos-delay="300">
                <button class="faq-question" type="button">
                    <span>{{ $getContent('contact_faq4_question', 'Xalqaro dasturlar bormi?') }}</span>
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div class="faq-answer">
                    <div class="faq-answer-inner">
                        {{ $getContent('contact_faq4_answer', "Ha, akademiyamiz UN Tourism bilan hamkorlikda xalqaro sertifikat dasturlarini taklif etadi. Shuningdek, xorijiy universitetlar bilan almashinuv dasturlari mavjud.") }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    (function initContactFaqAccordion() {
        function setOpen(item, open) {
            const ans = item.querySelector('.faq-answer');
            const btn = item.querySelector('.faq-question');
            if (!ans) return;
            if (open) {
                item.classList.add('active');
                if (btn) btn.classList.add('active');
                ans.style.maxHeight = ans.scrollHeight + 'px';
            } else {
                item.classList.remove('active');
                if (btn) btn.classList.remove('active');
                ans.style.maxHeight = '0px';
            }
        }

        function bind() {
            const items = document.querySelectorAll('.faq-section .faq-item');
            if (!items.length) return;

            items.forEach(item => {
                const button = item.querySelector('.faq-question');
                if (!button) return;

                if (item.classList.contains('active')) {
                    requestAnimationFrame(() => setOpen(item, true));
                }

                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
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
            document.addEventListener('DOMContentLoaded', bind);
        } else {
            bind();
        }
    })();
</script>
@endpush
