@extends(\App\Helpers\TemplateHelper::getLayout())

@php
    $locale = app()->getLocale();
    $titleField = 'title_' . $locale;
    $contentField = 'content_' . $locale;
    $descField = 'meta_description_' . $locale;

    $titleRaw = $page->$titleField ?? $page->title_uz;
    $contentRaw = $page->$contentField ?? $page->content_uz;
    $descriptionRaw = $page->$descField ?? $page->meta_description_uz;

    $title = html_entity_decode($titleRaw, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $content = is_array($contentRaw) ? json_encode($contentRaw) : html_entity_decode($contentRaw ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $description = $descriptionRaw ? html_entity_decode($descriptionRaw, ENT_QUOTES | ENT_HTML5, 'UTF-8') : null;
@endphp

@section('title', $title . ' - Tourism Academy')
@section('description', $description ?? Str::limit(strip_tags($content), 160))

@section('content')
<style>
    /* Page Hero Section */
    .page-article-hero {
        background: linear-gradient(135deg, #1e3a5f 0%, #0d1b2a 50%, #1b263b 100%);
        min-height: 400px;
        padding-top: 140px;
        padding-bottom: 80px;
        position: relative;
        overflow: hidden;
    }

    .page-article-hero::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background:
            radial-gradient(ellipse at 20% 30%, rgba(59, 130, 246, 0.15) 0%, transparent 50%),
            radial-gradient(ellipse at 80% 70%, rgba(147, 51, 234, 0.1) 0%, transparent 50%);
        pointer-events: none;
    }

    .page-article-hero::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 120px;
        background: linear-gradient(to top, #f8fafc, transparent);
        pointer-events: none;
    }

    .hero-content {
        position: relative;
        z-index: 2;
    }

    .breadcrumb-nav {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        border-radius: 50px;
        padding: 10px 20px;
        display: inline-flex;
        margin-bottom: 25px;
    }

    .breadcrumb-nav a {
        color: #93c5fd;
        text-decoration: none;
        transition: color 0.3s;
    }

    .breadcrumb-nav a:hover {
        color: #ffffff;
    }

    .breadcrumb-nav span {
        color: rgba(255, 255, 255, 0.5);
        margin: 0 10px;
    }

    .breadcrumb-nav .current {
        color: rgba(255, 255, 255, 0.7);
    }

    .page-title {
        color: #ffffff;
        font-size: 2.5rem;
        font-weight: 800;
        line-height: 1.3;
        margin-bottom: 20px;
        text-shadow: 0 2px 20px rgba(0, 0, 0, 0.3);
    }

    @media (max-width: 768px) {
        .page-title {
            font-size: 1.75rem;
        }
        .page-article-hero {
            padding-top: 120px;
            min-height: 350px;
        }
    }

    .page-excerpt {
        color: #cbd5e1;
        font-size: 1.15rem;
        line-height: 1.7;
        max-width: 800px;
    }

    /* Main Content Area */
    .page-main {
        background: #f8fafc;
        padding: 0 0 60px 0;
    }

    .page-card {
        background: #ffffff;
        border-radius: 24px;
        box-shadow: 0 10px 60px rgba(0, 0, 0, 0.08);
        margin-top: -60px;
        position: relative;
        z-index: 3;
        overflow: hidden;
    }

    .page-meta-bar {
        background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
        padding: 20px 40px;
        display: flex;
        flex-wrap: wrap;
        gap: 25px;
        border-bottom: 1px solid #e2e8f0;
    }

    .meta-item {
        display: flex;
        align-items: center;
        gap: 10px;
        color: #475569;
        font-size: 0.95rem;
    }

    .meta-item i {
        color: #3b82f6;
        font-size: 1.1rem;
    }

    .page-body-wrapper {
        padding: 40px;
    }

    @media (max-width: 768px) {
        .page-body-wrapper {
            padding: 25px;
        }
        .page-meta-bar {
            padding: 15px 25px;
        }
    }

    .featured-image {
        width: 100%;
        max-height: 500px;
        object-fit: cover;
        border-radius: 16px;
        margin-bottom: 35px;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
    }

    /* Page Content Typography */
    .page-body {
        color: #334155;
        font-size: 1.1rem;
        line-height: 1.9;
    }

    .page-body h1, .page-body h2, .page-body h3,
    .page-body h4, .page-body h5, .page-body h6 {
        color: #1e293b;
        font-weight: 700;
        margin-top: 35px;
        margin-bottom: 20px;
    }

    .page-body h2 {
        font-size: 1.75rem;
        padding-bottom: 10px;
        border-bottom: 3px solid #3b82f6;
        display: inline-block;
    }

    .page-body h3 {
        font-size: 1.4rem;
        color: #334155;
    }

    .page-body p {
        margin-bottom: 20px;
        color: #475569;
    }

    .page-body img {
        max-width: 100%;
        height: auto;
        border-radius: 12px;
        margin: 25px auto;
        display: block;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    }

    .page-body ul, .page-body ol {
        margin-bottom: 20px;
        padding-left: 25px;
        color: #475569;
    }

    .page-body li {
        margin-bottom: 10px;
    }

    .page-body blockquote {
        background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
        border-left: 5px solid #3b82f6;
        padding: 25px 30px;
        margin: 30px 0;
        border-radius: 0 16px 16px 0;
        font-style: italic;
        color: #1e40af;
        font-size: 1.1rem;
    }

    .page-body table {
        width: 100%;
        border-collapse: collapse;
        margin: 25px 0;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }

    .page-body table th {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: white;
        padding: 15px;
        text-align: left;
        font-weight: 600;
    }

    .page-body table td {
        padding: 15px;
        border-bottom: 1px solid #e2e8f0;
        color: #475569;
    }

    .page-body table tr:nth-child(even) {
        background: #f8fafc;
    }

    .page-body a {
        color: #3b82f6;
        text-decoration: none;
        border-bottom: 2px solid transparent;
        transition: all 0.3s;
    }

    .page-body a:hover {
        color: #1d4ed8;
        border-bottom-color: #1d4ed8;
    }

    .page-body pre, .page-body code {
        background: #f1f5f9;
        border-radius: 8px;
        font-family: 'Consolas', 'Monaco', monospace;
    }

    .page-body code {
        padding: 3px 8px;
        font-size: 0.9rem;
        color: #1e40af;
    }

    .page-body pre {
        padding: 20px;
        overflow-x: auto;
        margin: 25px 0;
    }

    .page-body pre code {
        padding: 0;
        background: none;
    }

    /* Share Section */
    .share-section {
        margin-top: 40px;
        padding: 30px;
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        border-radius: 16px;
    }

    .share-label {
        color: #475569;
        font-weight: 600;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .share-buttons {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }

    .share-btn {
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        color: white;
        text-decoration: none;
        transition: all 0.3s;
        font-size: 1.2rem;
        border: none;
        cursor: pointer;
    }

    .share-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        color: white;
    }

    .share-btn.telegram { background: linear-gradient(135deg, #0088cc, #006699); }
    .share-btn.facebook { background: linear-gradient(135deg, #1877f2, #0d65d9); }
    .share-btn.twitter { background: linear-gradient(135deg, #1da1f2, #0c85d0); }
    .share-btn.whatsapp { background: linear-gradient(135deg, #25d366, #128c7e); }
    .share-btn.copy { background: linear-gradient(135deg, #6366f1, #4f46e5); }

    /* Back Button Section */
    .back-section {
        background: linear-gradient(135deg, #1e3a5f 0%, #0d1b2a 100%);
        padding: 50px 0;
        text-align: center;
    }

    .back-btn {
        display: inline-flex;
        align-items: center;
        gap: 12px;
        padding: 16px 35px;
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
        text-decoration: none;
        border-radius: 50px;
        font-weight: 600;
        font-size: 1.05rem;
        transition: all 0.3s;
        box-shadow: 0 4px 20px rgba(59, 130, 246, 0.4);
    }

    .back-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 30px rgba(59, 130, 246, 0.5);
        color: white;
    }

    .back-btn i {
        transition: transform 0.3s;
    }

    .back-btn:hover i {
        transform: translateX(-5px);
    }

    /* Copy notification */
    .copy-notification {
        position: fixed;
        bottom: 30px;
        left: 50%;
        transform: translateX(-50%) translateY(100px);
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        padding: 15px 30px;
        border-radius: 50px;
        font-weight: 600;
        box-shadow: 0 10px 30px rgba(16, 185, 129, 0.4);
        transition: transform 0.3s ease;
        z-index: 1000;
    }

    .copy-notification.show {
        transform: translateX(-50%) translateY(0);
    }
</style>

<!-- Hero Section -->
<section class="page-article-hero">
    <div class="container">
        <div class="hero-content" data-aos="fade-up">
            <!-- Breadcrumb -->
            <nav class="breadcrumb-nav">
                <a href="{{ url('/') }}"><i class="fas fa-home me-1"></i> Bosh sahifa</a>
                <span>/</span>
                <span class="current">{{ Str::limit($title, 40) }}</span>
            </nav>

            <h1 class="page-title" data-aos="fade-up" data-aos-delay="100">
                {{ $title }}
            </h1>

            @if($description)
                <p class="page-excerpt" data-aos="fade-up" data-aos-delay="150">{{ $description }}</p>
            @endif
        </div>
    </div>
</section>

<!-- Main Content -->
<section class="page-main">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <article class="page-card" data-aos="fade-up">
                    <!-- Meta Bar -->
                    <div class="page-meta-bar">
                        <div class="meta-item">
                            <i class="far fa-calendar-alt"></i>
                            <span>{{ $page->updated_at->format('d.m.Y') }}</span>
                        </div>
                        <div class="meta-item">
                            <i class="far fa-eye"></i>
                            <span>{{ number_format($page->views_count ?? 0) }} ko'rish</span>
                        </div>
                        @if($page->show_in_menu)
                        <div class="meta-item">
                            <i class="fas fa-bars"></i>
                            <span>Menyuda</span>
                        </div>
                        @endif
                    </div>

                    <!-- Page Body -->
                    <div class="page-body-wrapper">
                        @if($page->featured_image)
                            <img src="{{ asset('storage/' . $page->featured_image) }}" alt="{{ $title }}" class="featured-image">
                        @endif

                        <div class="page-body">
                            {!! $content !!}
                        </div>

                        <!-- Share -->
                        <div class="share-section">
                            <div class="share-label">
                                <i class="fas fa-share-alt"></i>
                                Sahifani ulashish
                            </div>
                            <div class="share-buttons">
                                <a href="https://t.me/share/url?url={{ urlencode(request()->url()) }}&text={{ urlencode($title) }}"
                                   target="_blank" class="share-btn telegram" title="Telegram">
                                    <i class="fab fa-telegram-plane"></i>
                                </a>
                                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}"
                                   target="_blank" class="share-btn facebook" title="Facebook">
                                    <i class="fab fa-facebook-f"></i>
                                </a>
                                <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->url()) }}&text={{ urlencode($title) }}"
                                   target="_blank" class="share-btn twitter" title="Twitter">
                                    <i class="fab fa-twitter"></i>
                                </a>
                                <a href="https://wa.me/?text={{ urlencode($title . ' ' . request()->url()) }}"
                                   target="_blank" class="share-btn whatsapp" title="WhatsApp">
                                    <i class="fab fa-whatsapp"></i>
                                </a>
                                <button class="share-btn copy" onclick="copyLink()" title="Havolani nusxalash">
                                    <i class="fas fa-link"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </article>
            </div>
        </div>
    </div>
</section>

<!-- Back Button -->
<section class="back-section">
    <a href="{{ url('/') }}" class="back-btn">
        <i class="fas fa-arrow-left"></i>
        Bosh sahifaga qaytish
    </a>
</section>

<!-- Copy Notification -->
<div class="copy-notification" id="copyNotification">
    <i class="fas fa-check me-2"></i>Havola nusxalandi!
</div>

<script>
function copyLink() {
    navigator.clipboard.writeText(window.location.href).then(function() {
        const notification = document.getElementById('copyNotification');
        notification.classList.add('show');
        setTimeout(() => {
            notification.classList.remove('show');
        }, 2000);
    });
}
</script>
@endsection
