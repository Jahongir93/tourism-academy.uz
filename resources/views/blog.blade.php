@extends(\App\Helpers\TemplateHelper::getLayout())

@php
    use App\Models\CmsContent;

    // Get blog page content from CMS
    $blogContents = CmsContent::where('section', 'blog')->get()->keyBy('key');

    // Get current language
    $lang = app()->getLocale() ?? 'uz';
    $langField = 'value_' . $lang;

    // Helper function to get content
    $getContent = function($key, $default = '') use ($blogContents, $langField) {
        $content = $blogContents->get($key);
        return $content ? ($content->$langField ?? $content->value_uz ?? $default) : $default;
    };

    // Helper function to get localized field from model
    $getLocalized = function($model, $field, $default = '') use ($lang) {
        $localizedField = $field . '_' . $lang;
        $fallbackField = $field . '_uz';
        return $model->$localizedField ?? $model->$fallbackField ?? $default;
    };
@endphp

@section('title', $getContent('blog_page_title', 'Blog') . ' - Tourism Academy')
@section('description', $getContent('blog_page_description', 'Yangiliklar, maqolalar va tadbirlar'))

@section('content')
<style>
    /* Hero Section */
    .blog-hero {
        min-height: 55vh;
        padding-top: 140px;
        padding-bottom: 80px;
        background: linear-gradient(135deg, #1e3a5f 0%, #0d1b2a 50%, #1b263b 100%);
        display: flex;
        align-items: center;
        position: relative;
        overflow: hidden;
    }

    .blog-hero::before {
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

    .blog-hero h1 {
        color: #ffffff;
        font-size: 3rem;
        font-weight: 800;
        margin-bottom: 20px;
        text-shadow: 0 4px 30px rgba(0, 0, 0, 0.3);
        position: relative;
        z-index: 2;
        line-height: 1.2;
    }

    .blog-hero p {
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

    /* Blog Section */
    .blog-section {
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

    /* Blog Cards */
    .blog-card {
        background: white;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
        transition: all 0.4s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .blog-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 50px rgba(59, 130, 246, 0.15);
    }

    .blog-image {
        height: 220px;
        background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
        overflow: hidden;
        position: relative;
    }

    .blog-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .blog-card:hover .blog-image img {
        transform: scale(1.1);
    }

    .blog-image-placeholder {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.1), rgba(139, 92, 246, 0.1));
    }

    .blog-image-placeholder i {
        font-size: 4rem;
        color: #3b82f6;
        opacity: 0.3;
    }

    .blog-category-tag {
        position: absolute;
        top: 15px;
        left: 15px;
        padding: 6px 16px;
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: white;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 700;
        z-index: 1;
        box-shadow: 0 2px 8px rgba(59, 130, 246, 0.3);
    }

    .blog-content {
        padding: 25px;
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .blog-meta {
        display: flex;
        gap: 15px;
        margin-bottom: 15px;
    }

    .blog-meta span {
        display: flex;
        align-items: center;
        gap: 6px;
        color: #64748b;
        font-size: 0.85rem;
    }

    .blog-meta i {
        color: #3b82f6;
    }

    .blog-title {
        font-size: 1.2rem;
        font-weight: 700;
        color: #1e3a5f;
        margin-bottom: 12px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        transition: color 0.3s;
    }

    .blog-card:hover .blog-title {
        color: #3b82f6;
    }

    .blog-excerpt {
        color: #64748b;
        font-size: 0.95rem;
        line-height: 1.7;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
        margin-bottom: 20px;
        flex: 1;
    }

    .blog-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
        padding: 12px 24px;
        border-radius: 50px;
        font-size: 0.9rem;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
        align-self: flex-start;
    }

    .blog-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(59, 130, 246, 0.35);
        color: white;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 80px 20px;
        background: white;
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.06);
    }

    .empty-state-icon {
        width: 120px;
        height: 120px;
        background: rgba(59, 130, 246, 0.1);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 25px;
    }

    .empty-state-icon i {
        font-size: 3rem;
        color: #3b82f6;
    }

    .empty-state h4 {
        color: #1e3a5f;
        font-weight: 700;
        margin-bottom: 10px;
    }

    .empty-state p {
        color: #64748b;
    }

    /* Pagination */
    .pagination {
        gap: 8px;
        justify-content: center;
    }

    .page-link {
        border-radius: 10px !important;
        border: none;
        color: #1e3a5f;
        padding: 12px 18px;
        font-weight: 600;
        background: white;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }

    .page-item.active .page-link {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
    }

    .page-link:hover {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
    }

    /* Newsletter Section */
    .newsletter-section {
        background: linear-gradient(135deg, #1e3a5f 0%, #0d1b2a 100%);
        padding: 80px 0;
        position: relative;
        overflow: hidden;
    }

    .newsletter-section::before {
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

    .newsletter-content {
        position: relative;
        z-index: 2;
        text-align: center;
    }

    .newsletter-title {
        color: white;
        font-size: 2rem;
        font-weight: 800;
        margin-bottom: 15px;
    }

    .newsletter-text {
        color: #cbd5e1;
        font-size: 1.1rem;
        max-width: 500px;
        margin: 0 auto 30px;
    }

    .newsletter-form {
        display: flex;
        gap: 15px;
        max-width: 500px;
        margin: 0 auto;
        flex-wrap: wrap;
        justify-content: center;
    }

    .newsletter-input {
        flex: 1;
        min-width: 250px;
        padding: 15px 25px;
        border-radius: 50px;
        border: 2px solid rgba(255, 255, 255, 0.2);
        background: rgba(255, 255, 255, 0.1);
        color: white;
        font-size: 1rem;
    }

    .newsletter-input::placeholder {
        color: rgba(255, 255, 255, 0.5);
    }

    .newsletter-input:focus {
        outline: none;
        border-color: #3b82f6;
        background: rgba(255, 255, 255, 0.15);
    }

    .newsletter-btn {
        padding: 15px 35px;
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
        border: none;
        border-radius: 50px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s;
    }

    .newsletter-btn:hover {
        transform: scale(1.05);
        box-shadow: 0 10px 30px rgba(59, 130, 246, 0.4);
    }

    /* Responsive */
    @media (max-width: 992px) {
        .blog-hero h1 {
            font-size: 2.3rem;
        }
        .section-title {
            font-size: 2rem;
        }
    }

    @media (max-width: 768px) {
        .blog-hero {
            padding-top: 120px;
            min-height: 45vh;
        }
        .blog-hero h1 {
            font-size: 1.8rem;
        }
        .blog-hero p {
            font-size: 1rem;
        }
        .newsletter-title {
            font-size: 1.6rem;
        }
        .newsletter-form {
            flex-direction: column;
        }
        .newsletter-input {
            width: 100%;
        }
    }
</style>

<!-- Hero Section -->
<section class="blog-hero">
    <div class="container">
        <div data-aos="fade-up">
            <div class="hero-badge">
                <i class="fas fa-newspaper"></i>
                {{ $getContent('blog_hero_badge', 'YANGILIKLAR') }}
            </div>
            <h1>{{ $getContent('blog_hero_title', 'Blog va yangiliklar') }}</h1>
            <p>{{ $getContent('blog_hero_subtitle', "So'nggi yangiliklar, maqolalar va tadbirlar haqida ma'lumotlar. Doimo xabardor bo'ling!") }}</p>
            <div class="hero-breadcrumb">
                <a href="{{ route('home') }}">{{ $getContent('blog_breadcrumb_home', 'Bosh sahifa') }}</a>
                <span>/</span>
                <span class="current">{{ $getContent('blog_breadcrumb_blog', 'Blog') }}</span>
            </div>
        </div>
    </div>
</section>

<!-- Blog Section -->
<section class="blog-section">
    <div class="container">
        <div class="section-header" data-aos="fade-up">
            <div class="section-badge">
                <i class="fas fa-rss"></i>
                {{ $getContent('blog_section_badge', 'Maqolalar') }}
            </div>
            <h2 class="section-title">{{ $getContent('blog_section_title', "So'nggi yangiliklar") }}</h2>
            <p class="section-subtitle">{{ $getContent('blog_section_subtitle', "Eng so'nggi yangiliklar va maqolalar bilan tanishing") }}</p>
        </div>

        @if($posts->count() > 0)
            <div class="row g-4">
                @foreach($posts as $post)
                    <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                        <div class="blog-card">
                            <div class="blog-image">
                                @if($post->featured_image)
                                    <img src="{{ asset($post->featured_image) }}" alt="{{ $getLocalized($post, 'title') }}">
                                @else
                                    <div class="blog-image-placeholder">
                                        <i class="fas fa-newspaper"></i>
                                    </div>
                                @endif
                                @if($post->category)
                                    <span class="blog-category-tag">{{ $post->category->name ?? $post->category }}</span>
                                @endif
                            </div>
                            <div class="blog-content">
                                <div class="blog-meta">
                                    <span><i class="far fa-calendar"></i> {{ $post->published_at ? $post->published_at->format('d.m.Y') : '-' }}</span>
                                    @if($post->author)
                                    <span><i class="far fa-user"></i> {{ $post->author->name ?? '' }}</span>
                                    @endif
                                </div>
                                <h4 class="blog-title">{{ $getLocalized($post, 'title') }}</h4>
                                <p class="blog-excerpt">
                                    @php
                                        $excerpt = $getLocalized($post, 'excerpt');
                                        $content = $getLocalized($post, 'content');
                                    @endphp
                                    @if($excerpt)
                                        {{ Str::limit($excerpt, 120) }}
                                    @elseif($content)
                                        {{ Str::limit(strip_tags($content), 120) }}
                                    @endif
                                </p>
                                <a href="{{ route('news.show', $post->id) }}" class="blog-btn">
                                    <i class="fas fa-arrow-right"></i>{{ $getContent('btn_read_more', 'Batafsil') }}
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-5" data-aos="fade-up">
                {{ $posts->links() }}
            </div>
        @else
            <div class="empty-state" data-aos="fade-up">
                <div class="empty-state-icon">
                    <i class="fas fa-inbox"></i>
                </div>
                <h4>{{ $getContent('blog_empty_title', 'Hozircha blog yozuvlari mavjud emas') }}</h4>
                <p>{{ $getContent('blog_empty_subtitle', 'Tez orada qiziqarli maqolalar bilan tanishing') }}</p>
            </div>
        @endif
    </div>
</section>

<!-- Newsletter Section -->
<section class="newsletter-section">
    <div class="container">
        <div class="newsletter-content" data-aos="fade-up">
            <h2 class="newsletter-title">{{ $getContent('blog_newsletter_title', "Yangiliklardan xabardor bo'ling") }}</h2>
            <p class="newsletter-text">{{ $getContent('blog_newsletter_text', "Email manzilingizni qoldiring va eng so'nggi yangiliklar haqida birinchilardan bo'lib xabar toping") }}</p>
            <form class="newsletter-form" id="newsletterForm">
                <input type="email" class="newsletter-input" placeholder="{{ $getContent('blog_newsletter_placeholder', 'Email manzilingiz') }}" required>
                <button type="submit" class="newsletter-btn">
                    <i class="fas fa-paper-plane me-2"></i>{{ $getContent('blog_newsletter_button', "Obuna bo'lish") }}
                </button>
            </form>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const newsletterForm = document.getElementById('newsletterForm');
    if (newsletterForm) {
        newsletterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const email = this.querySelector('input[type="email"]').value;
            if (email) {
                alert('{{ $getContent("blog_newsletter_success", "Rahmat! Siz muvaffaqiyatli obuna bo'ldingiz.") }}');
                this.querySelector('input').value = '';
            }
        });
    }
});
</script>
@endsection
