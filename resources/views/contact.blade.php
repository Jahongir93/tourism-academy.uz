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

@section('title', $getContent('contact_page_title', 'Aloqa') . ' - Tourism Academy')
@section('description', $getContent('contact_page_description', "Biz bilan bog'laning"))

@section('content')
<style>
    /* Hero Section */
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
        position: relative;
        z-index: 2;
        line-height: 1.2;
    }

    .contact-hero p {
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

    /* Contact Info Cards */
    .info-section {
        background: linear-gradient(135deg, #1e3a5f 0%, #0d1b2a 100%);
        padding: 60px 0;
        position: relative;
    }

    .info-card {
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.15);
        border-radius: 20px;
        padding: 30px;
        text-align: center;
        backdrop-filter: blur(10px);
        transition: all 0.3s ease;
        height: 100%;
    }

    .info-card:hover {
        transform: translateY(-5px);
        background: rgba(255, 255, 255, 0.15);
    }

    .info-icon {
        width: 70px;
        height: 70px;
        background: linear-gradient(135deg, #3b82f6, #8b5cf6);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        font-size: 1.6rem;
        color: white;
        box-shadow: 0 10px 30px rgba(59, 130, 246, 0.3);
    }

    .info-card h5 {
        color: white;
        font-weight: 700;
        margin-bottom: 10px;
    }

    .info-card p {
        color: #cbd5e1;
        margin: 0;
        line-height: 1.7;
    }

    /* Form Section */
    .form-section {
        padding: 80px 0;
        background: #f8fafc;
    }

    .form-card {
        background: white;
        border-radius: 24px;
        padding: 40px;
        box-shadow: 0 15px 50px rgba(0, 0, 0, 0.08);
        height: 100%;
    }

    .form-card h3 {
        font-size: 1.6rem;
        font-weight: 700;
        color: #1e3a5f;
        margin-bottom: 10px;
    }

    .form-card h3 i {
        color: #3b82f6;
        margin-right: 10px;
    }

    .form-card > p {
        color: #64748b;
        margin-bottom: 30px;
    }

    .form-label {
        color: #1e3a5f;
        font-weight: 600;
        margin-bottom: 8px;
    }

    .form-control {
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        padding: 14px 18px;
        font-size: 1rem;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
    }

    .form-control::placeholder {
        color: #94a3b8;
    }

    .submit-btn {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
        border: none;
        padding: 16px 40px;
        border-radius: 50px;
        font-weight: 700;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.3s ease;
        width: 100%;
    }

    .submit-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 35px rgba(59, 130, 246, 0.35);
    }

    .submit-btn i {
        margin-right: 8px;
    }

    /* Map Card */
    .map-card {
        background: white;
        border-radius: 24px;
        padding: 40px;
        box-shadow: 0 15px 50px rgba(0, 0, 0, 0.08);
        height: 100%;
    }

    .map-card h3 {
        font-size: 1.6rem;
        font-weight: 700;
        color: #1e3a5f;
        margin-bottom: 25px;
    }

    .map-card h3 i {
        color: #3b82f6;
        margin-right: 10px;
    }

    .map-wrapper {
        border-radius: 16px;
        overflow: hidden;
        height: 350px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
    }

    .map-wrapper iframe {
        width: 100%;
        height: 100%;
        border: none;
    }

    /* Social Section */
    .social-section {
        background: linear-gradient(135deg, #1e3a5f 0%, #0d1b2a 100%);
        padding: 80px 0;
        position: relative;
        overflow: hidden;
    }

    .social-section::before {
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

    .social-content {
        position: relative;
        z-index: 2;
        text-align: center;
    }

    .social-title {
        color: white;
        font-size: 2rem;
        font-weight: 800;
        margin-bottom: 15px;
    }

    .social-subtitle {
        color: #cbd5e1;
        font-size: 1.1rem;
        margin-bottom: 30px;
    }

    .social-links {
        display: flex;
        justify-content: center;
        gap: 20px;
        flex-wrap: wrap;
    }

    .social-link {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.5rem;
        text-decoration: none;
        transition: all 0.3s ease;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
    }

    .social-link:hover {
        transform: translateY(-5px) scale(1.1);
        color: white;
    }

    .social-link.facebook { background: #1877f2; }
    .social-link.instagram { background: linear-gradient(45deg, #f09433, #e6683c, #dc2743, #cc2366, #bc1888); }
    .social-link.telegram { background: #0088cc; }
    .social-link.youtube { background: #ff0000; }
    .social-link.linkedin { background: #0077b5; }

    /* Success Alert */
    .alert-success {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
        border: none;
        border-radius: 12px;
        padding: 15px 20px;
        margin-bottom: 25px;
    }

    /* Responsive */
    @media (max-width: 992px) {
        .contact-hero h1 {
            font-size: 2.3rem;
        }
    }

    @media (max-width: 768px) {
        .contact-hero {
            padding-top: 120px;
            min-height: 45vh;
        }
        .contact-hero h1 {
            font-size: 1.8rem;
        }
        .contact-hero p {
            font-size: 1rem;
        }
        .form-card, .map-card {
            padding: 25px;
        }
        .social-title {
            font-size: 1.6rem;
        }
    }
</style>

<!-- Hero Section -->
<section class="contact-hero">
    <div class="container">
        <div data-aos="fade-up">
            <div class="hero-badge">
                <i class="fas fa-phone-alt"></i>
                {{ $getContent('contact_hero_badge', 'ALOQA') }}
            </div>
            <h1>{{ $getContent('contact_hero_title', "Biz bilan bog'laning") }}</h1>
            <p>{{ $getContent('contact_hero_subtitle', "Savollaringiz bo'lsa, biz bilan bog'laning. Jamoamiz sizga yordam berishga tayyor!") }}</p>
            <div class="hero-breadcrumb">
                <a href="{{ route('home') }}">{{ $getContent('contact_breadcrumb_home', 'Bosh sahifa') }}</a>
                <span>/</span>
                <span class="current">{{ $getContent('contact_breadcrumb_contact', 'Aloqa') }}</span>
            </div>
        </div>
    </div>
</section>

<!-- Contact Info Cards -->
<section class="info-section">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="100">
                <div class="info-card">
                    <div class="info-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <h5>{{ $getContent('contact_address_label', 'Manzil') }}</h5>
                    <p>{{ $getContent('contact_address', "Samarqand sh., Universitet xiyoboni 15") }}</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="200">
                <div class="info-card">
                    <div class="info-icon">
                        <i class="fas fa-phone"></i>
                    </div>
                    <h5>{{ $getContent('contact_phone_label', 'Telefon') }}</h5>
                    <p>{!! nl2br(e($getContent('contact_phone', "+998 90 123-45-67"))) !!}</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="300">
                <div class="info-card">
                    <div class="info-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <h5>{{ $getContent('contact_email_label', 'Email') }}</h5>
                    <p>{!! nl2br(e($getContent('contact_email', "info@tourism.uz"))) !!}</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="400">
                <div class="info-card">
                    <div class="info-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h5>{{ $getContent('contact_hours_label', 'Ish vaqti') }}</h5>
                    <p>{!! nl2br(e($getContent('contact_hours', "Dushanba-Juma: 9:00 - 18:00"))) !!}</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Form Section -->
<section class="form-section">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-6" data-aos="fade-up">
                <div class="form-card">
                    <h3><i class="fas fa-paper-plane"></i>{{ $getContent('contact_form_title', 'Xabar yuborish') }}</h3>
                    <p>{{ $getContent('contact_form_description', "Savollaringiz yoki takliflaringiz bo'lsa, bizga xabar yuboring") }}</p>

                    @if(session('success'))
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                        </div>
                    @endif

                    <form action="{{ route('contact.submit') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label class="form-label">{{ $getContent('contact_form_name', 'Ismingiz') }} *</label>
                            <input type="text" name="name" class="form-control" placeholder="{{ $getContent('contact_form_name_placeholder', 'Ismingizni kiriting') }}" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">{{ $getContent('contact_form_email', 'Email') }} *</label>
                            <input type="email" name="email" class="form-control" placeholder="{{ $getContent('contact_form_email_placeholder', 'Email manzilingiz') }}" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">{{ $getContent('contact_form_subject', 'Mavzu') }} *</label>
                            <input type="text" name="subject" class="form-control" placeholder="{{ $getContent('contact_form_subject_placeholder', 'Xabar mavzusi') }}" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">{{ $getContent('contact_form_message', 'Xabar') }} *</label>
                            <textarea name="message" rows="5" class="form-control" placeholder="{{ $getContent('contact_form_message_placeholder', 'Xabaringizni yozing...') }}" required></textarea>
                        </div>
                        <button type="submit" class="submit-btn">
                            <i class="fas fa-paper-plane"></i>{{ $getContent('contact_form_submit', 'Yuborish') }}
                        </button>
                    </form>
                </div>
            </div>

            <div class="col-lg-6" data-aos="fade-up" data-aos-delay="200">
                <div class="map-card">
                    <h3><i class="fas fa-map-marked-alt"></i>{{ $getContent('contact_map_title', 'Bizning joylashuvimiz') }}</h3>
                    <div class="map-wrapper">
                        @php
                            $mapEmbed = $getContent('contact_map_embed', '');
                        @endphp
                        @if($mapEmbed)
                            {!! $mapEmbed !!}
                        @else
                            <iframe
                                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3067.5!2d66.9597!3d39.6547!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMznCsDM5JzE3LjAiTiA2NsKwNTcnMzQuOSJF!5e0!3m2!1sen!2s!4v1234567890"
                                allowfullscreen=""
                                loading="lazy"
                                referrerpolicy="no-referrer-when-downgrade">
                            </iframe>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Social Media Section -->
<section class="social-section">
    <div class="container">
        <div class="social-content" data-aos="fade-up">
            <h2 class="social-title">{{ $getContent('contact_social_title', 'Ijtimoiy tarmoqlarda') }}</h2>
            <p class="social-subtitle">{{ $getContent('contact_social_subtitle', "Bizni ijtimoiy tarmoqlarda kuzatib boring") }}</p>
            <div class="social-links">
                @php
                    $socialLinks = [
                        'facebook' => ['icon' => 'fab fa-facebook-f', 'class' => 'facebook'],
                        'instagram' => ['icon' => 'fab fa-instagram', 'class' => 'instagram'],
                        'telegram' => ['icon' => 'fab fa-telegram-plane', 'class' => 'telegram'],
                        'youtube' => ['icon' => 'fab fa-youtube', 'class' => 'youtube'],
                        'linkedin' => ['icon' => 'fab fa-linkedin-in', 'class' => 'linkedin'],
                    ];
                @endphp
                @foreach($socialLinks as $network => $data)
                    @php $link = $getContent("contact_social_{$network}", ''); @endphp
                    @if($link)
                    <a href="{{ $link }}" target="_blank" class="social-link {{ $data['class'] }}">
                        <i class="{{ $data['icon'] }}"></i>
                    </a>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
</section>
@endsection
