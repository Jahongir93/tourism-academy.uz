@extends(\App\Helpers\TemplateHelper::getLayout())

@php
    $lang = app()->getLocale() ?? 'uz';
    $titleRaw = $lang == 'uz' ? $article->title_uz : ($lang == 'ru' ? ($article->title_ru ?? $article->title_uz) : ($article->title_en ?? $article->title_uz));
    $contentRaw = $lang == 'uz' ? $article->content_uz : ($lang == 'ru' ? ($article->content_ru ?? $article->content_uz) : ($article->content_en ?? $article->content_uz));
    $excerptRaw = $lang == 'uz' ? $article->excerpt_uz : ($lang == 'ru' ? ($article->excerpt_ru ?? $article->excerpt_uz) : ($article->excerpt_en ?? $article->excerpt_uz));

    $title = html_entity_decode($titleRaw, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $content = html_entity_decode($contentRaw, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $excerpt = $excerptRaw ? html_entity_decode($excerptRaw, ENT_QUOTES | ENT_HTML5, 'UTF-8') : null;
@endphp

@section('title', $title . ' - Yangiliklar')
@section('description', $excerpt ?? Str::limit(strip_tags($content), 160))

@section('content')
<style>
    /* News Article Page Styles */
    .news-article-hero {
        background: linear-gradient(135deg, #1e3a5f 0%, #0d1b2a 50%, #1b263b 100%);
        min-height: 450px;
        padding-top: 140px;
        padding-bottom: 80px;
        position: relative;
        overflow: hidden;
    }

    .news-article-hero::before {
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

    .news-article-hero::after {
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

    .category-badge {
        background: linear-gradient(135deg, #3b82f6, #8b5cf6);
        color: white;
        padding: 8px 20px;
        border-radius: 30px;
        font-size: 0.85rem;
        font-weight: 600;
        display: inline-block;
        margin-bottom: 20px;
        box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
    }

    .article-title {
        color: #ffffff;
        font-size: 2.5rem;
        font-weight: 800;
        line-height: 1.3;
        margin-bottom: 20px;
        text-shadow: 0 2px 20px rgba(0, 0, 0, 0.3);
    }

    @media (max-width: 768px) {
        .article-title {
            font-size: 1.75rem;
        }
        .news-article-hero {
            padding-top: 120px;
            min-height: 380px;
        }
    }

    .article-excerpt {
        color: #cbd5e1;
        font-size: 1.15rem;
        line-height: 1.7;
        max-width: 800px;
    }

    /* Main Content Area */
    .article-main {
        background: #f8fafc;
        padding: 0 0 60px 0;
    }

    .article-card {
        background: #ffffff;
        border-radius: 24px;
        box-shadow: 0 10px 60px rgba(0, 0, 0, 0.08);
        margin-top: -60px;
        position: relative;
        z-index: 3;
        overflow: hidden;
    }

    .article-meta-bar {
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

    .meta-item.featured i {
        color: #f59e0b;
    }

    .article-body-wrapper {
        padding: 40px;
    }

    @media (max-width: 768px) {
        .article-body-wrapper {
            padding: 25px;
        }
        .article-meta-bar {
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

    /* Article Content Typography */
    .article-body {
        color: #334155;
        font-size: 1.1rem;
        line-height: 1.9;
    }

    .article-body h1, .article-body h2, .article-body h3,
    .article-body h4, .article-body h5, .article-body h6 {
        color: #1e293b;
        font-weight: 700;
        margin-top: 35px;
        margin-bottom: 20px;
    }

    .article-body h2 {
        font-size: 1.75rem;
        padding-bottom: 10px;
        border-bottom: 3px solid #3b82f6;
        display: inline-block;
    }

    .article-body h3 {
        font-size: 1.4rem;
        color: #334155;
    }

    .article-body p {
        margin-bottom: 20px;
        color: #475569;
    }

    .article-body img {
        max-width: 100%;
        height: auto;
        border-radius: 12px;
        margin: 25px 0;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    }

    .article-body ul, .article-body ol {
        margin-bottom: 20px;
        padding-left: 25px;
        color: #475569;
    }

    .article-body li {
        margin-bottom: 10px;
    }

    .article-body blockquote {
        background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
        border-left: 5px solid #3b82f6;
        padding: 25px 30px;
        margin: 30px 0;
        border-radius: 0 16px 16px 0;
        font-style: italic;
        color: #1e40af;
        font-size: 1.1rem;
    }

    .article-body table {
        width: 100%;
        border-collapse: collapse;
        margin: 25px 0;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }

    .article-body table th {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: white;
        padding: 15px;
        text-align: left;
        font-weight: 600;
    }

    .article-body table td {
        padding: 15px;
        border-bottom: 1px solid #e2e8f0;
        color: #475569;
    }

    .article-body table tr:nth-child(even) {
        background: #f8fafc;
    }

    .article-body a {
        color: #3b82f6;
        text-decoration: none;
        border-bottom: 2px solid transparent;
        transition: all 0.3s;
    }

    .article-body a:hover {
        color: #1d4ed8;
        border-bottom-color: #1d4ed8;
    }

    /* Tags Section */
    .tags-section {
        margin-top: 40px;
        padding-top: 30px;
        border-top: 2px solid #e2e8f0;
    }

    .tags-label {
        color: #64748b;
        font-weight: 600;
        margin-right: 15px;
    }

    .tag-link {
        display: inline-block;
        padding: 8px 18px;
        background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
        color: #2563eb;
        border-radius: 25px;
        font-size: 0.9rem;
        font-weight: 500;
        text-decoration: none;
        margin: 5px;
        transition: all 0.3s;
    }

    .tag-link:hover {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
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

    /* Related News Section */
    .related-section {
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        padding: 80px 0;
    }

    .section-title {
        color: #1e293b;
        font-size: 2rem;
        font-weight: 800;
        text-align: center;
        margin-bottom: 50px;
    }

    .section-title i {
        color: #3b82f6;
        margin-right: 10px;
    }

    .related-card {
        background: #ffffff;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        transition: all 0.4s ease;
        height: 100%;
    }

    .related-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 50px rgba(59, 130, 246, 0.2);
    }

    .related-card-image {
        width: 100%;
        height: 200px;
        object-fit: cover;
    }

    .related-card-body {
        padding: 25px;
    }

    .related-card-title {
        color: #1e293b;
        font-size: 1.1rem;
        font-weight: 700;
        line-height: 1.5;
        margin-bottom: 15px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .related-card-date {
        color: #64748b;
        font-size: 0.9rem;
    }

    .related-card-date i {
        color: #3b82f6;
        margin-right: 8px;
    }

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
<section class="news-article-hero">
    <div class="container">
        <div class="hero-content" data-aos="fade-up">
            <!-- Breadcrumb -->
            <nav class="breadcrumb-nav">
                <a href="{{ route('home') }}">Bosh sahifa</a>
                <span>/</span>
                <a href="{{ route('news') }}">Yangiliklar</a>
                <span>/</span>
                <span class="current">{{ Str::limit($title, 40) }}</span>
            </nav>

            @if($article->category)
                <div class="category-badge" data-aos="fade-up" data-aos-delay="100">
                    <i class="fas fa-folder me-2"></i>{{ $article->category->name_uz }}
                </div>
            @endif

            <h1 class="article-title" data-aos="fade-up" data-aos-delay="150">
                {{ $title }}
            </h1>

            @if($excerpt)
                <p class="article-excerpt" data-aos="fade-up" data-aos-delay="200">{{ $excerpt }}</p>
            @endif
        </div>
    </div>
</section>

<!-- Main Article Content -->
<section class="article-main">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <article class="article-card" data-aos="fade-up">
                    <!-- Meta Bar -->
                    <div class="article-meta-bar">
                        <div class="meta-item">
                            <i class="far fa-calendar-alt"></i>
                            <span>{{ $article->published_at ? $article->published_at->format('d.m.Y') : $article->created_at->format('d.m.Y') }}</span>
                        </div>
                        @if($article->author)
                        <div class="meta-item">
                            <i class="far fa-user"></i>
                            <span>{{ $article->author->name }}</span>
                        </div>
                        @endif
                        <div class="meta-item">
                            <i class="far fa-eye"></i>
                            <span>{{ number_format($article->views_count) }} ko'rish</span>
                        </div>
                        @if($article->is_featured)
                        <div class="meta-item featured">
                            <i class="fas fa-star"></i>
                            <span>Tanlangan</span>
                        </div>
                        @endif
                    </div>

                    <!-- Article Body -->
                    <div class="article-body-wrapper">
                        @if($article->featured_image)
                            <img src="{{ asset($article->featured_image) }}" alt="{{ $title }}" class="featured-image">
                        @endif

                        <div class="article-body">
                            {!! $content !!}
                        </div>

                        <!-- Tags -->
                        @if($article->tags && count($article->tags) > 0)
                            <div class="tags-section">
                                <span class="tags-label"><i class="fas fa-tags me-2"></i>Teglar:</span>
                                @foreach($article->tags as $tag)
                                    <a href="{{ route('news', ['tag' => $tag]) }}" class="tag-link">#{{ $tag }}</a>
                                @endforeach
                            </div>
                        @endif

                        <!-- Share -->
                        <div class="share-section">
                            <div class="share-label">
                                <i class="fas fa-share-alt"></i>
                                Yangilikni ulashish
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

<!-- Related News -->
@if($relatedNews && $relatedNews->count() > 0)
<section class="related-section">
    <div class="container">
        <h2 class="section-title" data-aos="fade-up">
            <i class="fas fa-newspaper"></i>O'xshash yangiliklar
        </h2>

        <div class="row g-4">
            @foreach($relatedNews as $related)
            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                <a href="{{ route('news.show', $related->slug ?? $related->id) }}" class="text-decoration-none">
                    <div class="related-card">
                        @if($related->featured_image)
                            <img src="{{ asset($related->featured_image) }}" alt="{{ $related->title_uz }}" class="related-card-image">
                        @else
                            <img src="{{ asset('images/ext/dddd05ac019ad8bf.jpg') }}"
                                 alt="News" class="related-card-image">
                        @endif
                        <div class="related-card-body">
                            <h5 class="related-card-title">
                                {{ html_entity_decode($related->title_uz, ENT_QUOTES | ENT_HTML5, 'UTF-8') }}
                            </h5>
                            <div class="related-card-date">
                                <i class="far fa-calendar-alt"></i>
                                {{ $related->published_at?->format('d.m.Y') ?? $related->created_at->format('d.m.Y') }}
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Back Button -->
<section class="back-section">
    <a href="{{ route('news') }}" class="back-btn">
        <i class="fas fa-arrow-left"></i>
        Barcha yangiliklarga qaytish
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
