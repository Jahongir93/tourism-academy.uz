@extends(\App\Helpers\TemplateHelper::getLayout())

@section('title', 'Yangiliklar')

@section('content')
<style>
    /* Hero Section */
    .news-hero {
        min-height: 50vh;
        padding-top: 140px;
        padding-bottom: 60px;
        background: linear-gradient(135deg, #1e3a5f 0%, #0d1b2a 50%, #1b263b 100%);
        display: flex;
        align-items: center;
        position: relative;
        overflow: hidden;
    }

    .news-hero::before {
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

    .hero-badge {
        display: inline-block;
        background: linear-gradient(135deg, #3b82f6, #8b5cf6);
        color: white;
        padding: 10px 25px;
        border-radius: 30px;
        font-weight: 600;
        font-size: 0.9rem;
        margin-bottom: 20px;
        box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
    }

    .news-hero h1 {
        color: #ffffff;
        font-size: 2.8rem;
        font-weight: 800;
        margin-bottom: 15px;
        text-shadow: 0 2px 20px rgba(0, 0, 0, 0.3);
        position: relative;
        z-index: 2;
    }

    .news-hero p {
        color: #cbd5e1;
        font-size: 1.2rem;
        position: relative;
        z-index: 2;
    }

    @media (max-width: 768px) {
        .news-hero {
            padding-top: 120px;
            min-height: 40vh;
        }
        .news-hero h1 {
            font-size: 1.8rem;
        }
    }

    /* Category Filter */
    .category-section {
        background: #f8fafc;
        padding: 25px 0;
        border-bottom: 1px solid #e5e7eb;
    }

    .category-filter {
        display: flex;
        justify-content: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .category-btn {
        padding: 12px 28px;
        background: white;
        border: 2px solid #e5e7eb;
        border-radius: 30px;
        color: #6b7280;
        transition: all 0.3s;
        cursor: pointer;
        font-weight: 600;
        font-size: 0.95rem;
    }

    .category-btn:hover {
        border-color: #3b82f6;
        color: #1e3a5f;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.15);
    }

    .category-btn.active {
        background: linear-gradient(135deg, #1e3a5f 0%, #0d1b2a 100%);
        color: white;
        border-color: #1e3a5f;
    }

    /* News Cards */
    .news-card {
        background: #ffffff;
        border-radius: 16px;
        overflow: hidden;
        transition: all 0.4s;
        height: 100%;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        border: 1px solid #e5e7eb;
    }

    .news-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(26, 26, 46, 0.15);
    }

    .news-img-wrapper {
        position: relative;
        overflow: hidden;
        height: 220px;
        background: #f3f4f6;
    }

    .news-img-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s;
    }

    .news-card:hover .news-img-wrapper img {
        transform: scale(1.1);
    }

    .news-category-tag {
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

    .news-category-tag.event {
        background: #10b981;
        color: white;
    }

    .news-date-tag {
        position: absolute;
        bottom: 15px;
        right: 15px;
        padding: 6px 14px;
        background: rgba(26, 26, 46, 0.85);
        backdrop-filter: blur(10px);
        border-radius: 20px;
        font-size: 0.85rem;
        color: white;
        z-index: 1;
    }

    .news-content {
        padding: 25px;
    }

    .news-title {
        font-size: 1.2rem;
        font-weight: 700;
        margin-bottom: 12px;
        color: #1a1a2e;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        line-height: 1.4;
        transition: color 0.3s;
    }

    .news-card:hover .news-title {
        color: #3b82f6;
    }

    .news-excerpt {
        color: #6b7280;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
        margin-bottom: 20px;
        line-height: 1.7;
        font-size: 0.95rem;
    }

    .news-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 15px;
        border-top: 1px solid #f3f4f6;
    }

    .news-views {
        color: #9ca3af;
        font-size: 0.9rem;
    }

    .read-more-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        background: linear-gradient(135deg, #1e3a5f 0%, #0d1b2a 100%);
        border-radius: 25px;
        color: white;
        text-decoration: none;
        transition: all 0.3s;
        font-size: 0.9rem;
        font-weight: 600;
    }

    .read-more-btn:hover {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
        transform: translateX(5px);
        box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
    }

    /* Featured Card */
    .featured-card {
        border: 2px solid #3b82f6 !important;
        position: relative;
    }

    .featured-badge {
        position: absolute;
        top: -12px;
        right: 20px;
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: white;
        padding: 6px 14px;
        border-radius: 15px;
        font-size: 0.75rem;
        font-weight: 700;
        z-index: 10;
        box-shadow: 0 4px 10px rgba(245, 158, 11, 0.3);
    }

    /* Section Header */
    .section-badge {
        display: inline-block;
        background: rgba(59, 130, 246, 0.15);
        color: #1e3a5f;
        padding: 8px 20px;
        border-radius: 30px;
        font-weight: 600;
        font-size: 0.85rem;
        margin-bottom: 15px;
    }

    .section-title {
        color: #1e3a5f;
        font-size: 2rem;
        font-weight: 800;
        margin-bottom: 10px;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
    }

    .empty-state-icon {
        width: 120px;
        height: 120px;
        background: rgba(59, 130, 246, 0.15);
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

    /* Newsletter CTA */
    .newsletter-section {
        background: linear-gradient(135deg, #1e3a5f 0%, #0d1b2a 100%);
        padding: 60px 0;
    }

    .newsletter-box {
        background: rgba(59, 130, 246, 0.1);
        border-radius: 20px;
        padding: 40px;
        border: 1px solid rgba(59, 130, 246, 0.2);
    }

    .newsletter-title {
        color: #ffffff;
        font-size: 1.8rem;
        font-weight: 700;
        margin-bottom: 15px;
    }

    .newsletter-text {
        color: rgba(255, 255, 255, 0.7);
        margin-bottom: 25px;
    }

    .newsletter-form {
        display: flex;
        gap: 15px;
        max-width: 500px;
    }

    .newsletter-input {
        flex: 1;
        padding: 15px 25px;
        border: none;
        border-radius: 30px;
        font-size: 1rem;
        background: rgba(255, 255, 255, 0.1);
        color: white;
        border: 1px solid rgba(255, 255, 255, 0.2);
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
        padding: 15px 30px;
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
        border: none;
        border-radius: 30px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s;
    }

    .newsletter-btn:hover {
        transform: scale(1.05);
        box-shadow: 0 5px 20px rgba(59, 130, 246, 0.4);
    }

    /* Pagination */
    .pagination {
        gap: 8px;
    }

    .page-link {
        border-radius: 10px !important;
        border: none;
        color: #1e3a5f;
        padding: 12px 18px;
        font-weight: 600;
    }

    .page-item.active .page-link {
        background: linear-gradient(135deg, #1e3a5f 0%, #0d1b2a 100%);
        color: white;
    }

    .page-link:hover {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
    }

    @media (max-width: 768px) {
        .newsletter-form {
            flex-direction: column;
        }

        .category-btn {
            padding: 10px 20px;
            font-size: 0.85rem;
        }
    }
</style>

<!-- Hero Section -->
<section class="news-hero">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center">
                <span class="hero-badge" data-aos="fade-up">
                    <i class="fas fa-newspaper me-2"></i>Yangiliklar
                </span>
                <h1 data-aos="fade-up" data-aos-delay="100">
                    So'nggi Yangiliklar va Tadbirlar
                </h1>
                <p data-aos="fade-up" data-aos-delay="200">
                    Fakultet hayotidan eng dolzarb xabarlar va muhim tadbirlar
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Category Filter -->
<section class="category-section">
    <div class="container">
        <div class="category-filter" data-aos="fade-up">
            <button class="category-btn active" data-category="all">
                <i class="fas fa-th-large me-2"></i>Barchasi
            </button>
            <button class="category-btn" data-category="ilmiy">
                <i class="fas fa-flask me-2"></i>Ilmiy
            </button>
            <button class="category-btn" data-category="talim">
                <i class="fas fa-graduation-cap me-2"></i>Ta'lim
            </button>
            <button class="category-btn" data-category="madaniy">
                <i class="fas fa-theater-masks me-2"></i>Madaniy
            </button>
            <button class="category-btn" data-category="sport">
                <i class="fas fa-futbol me-2"></i>Sport
            </button>
        </div>
    </div>
</section>

<!-- Featured News -->
@if(isset($featuredNews) && $featuredNews->count() > 0)
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <span class="section-badge">
                <i class="fas fa-star me-2"></i>Muhim
            </span>
            <h2 class="section-title">Muhim Yangiliklar va Tadbirlar</h2>
        </div>

        <div class="row g-4 justify-content-center">
            @foreach($featuredNews as $featured)
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                <div class="news-card featured-card">
                    <span class="featured-badge">
                        <i class="fas fa-star me-1"></i>Muhim
                    </span>
                    <div class="news-img-wrapper">
                        @if($featured['image'] ?? false)
                            @if(($featured['type'] ?? 'news') === 'event')
                                <img src="{{ asset('storage/' . $featured['image']) }}"
                                     alt="{{ $featured['title'] }}"
                                     onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1540575467063-178a50c2df87?w=600';">
                            @else
                                <img src="{{ asset($featured['image']) }}"
                                     alt="{{ $featured['title'] }}"
                                     onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1523050854058-8df90110c9f1?w=600';">
                            @endif
                        @else
                            <img src="https://images.unsplash.com/photo-1523050854058-8df90110c9f1?w=600" alt="{{ $featured['title'] }}">
                        @endif

                        @if(($featured['type'] ?? 'news') === 'event')
                            <span class="news-category-tag event">
                                <i class="fas fa-calendar-alt me-1"></i>Tadbir
                            </span>
                        @else
                            <span class="news-category-tag">Yangilik</span>
                        @endif

                        <span class="news-date-tag">
                            <i class="far fa-calendar me-1"></i>{{ $featured['date']?->format('d.m.Y') }}
                        </span>
                    </div>
                    <div class="news-content">
                        <h4 class="news-title">{{ $featured['title'] }}</h4>
                        <p class="news-excerpt">{{ $featured['excerpt'] }}</p>
                        <a href="{{ $featured['url'] }}" class="read-more-btn">
                            Batafsil <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- All News -->
<section class="py-5" style="background: #f8fafc;">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <span class="section-badge">
                <i class="fas fa-rss me-2"></i>Barcha Yangiliklar
            </span>
            <h2 class="section-title">Yangiliklar Arxivi</h2>
        </div>

        <div class="row g-4 justify-content-center">
            @forelse($news as $item)
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="{{ ($loop->index % 3) * 100 }}">
                <div class="news-card" data-type="{{ $item['type'] ?? 'news' }}">
                    <div class="news-img-wrapper">
                        @if($item['image'] ?? false)
                            @if(($item['type'] ?? 'news') === 'event')
                                <img src="{{ asset('storage/' . $item['image']) }}"
                                     alt="{{ $item['title'] }}"
                                     onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1540575467063-178a50c2df87?w=600';">
                            @else
                                <img src="{{ asset($item['image']) }}"
                                     alt="{{ $item['title'] }}"
                                     onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1523050854058-8df90110c9f1?w=600';">
                            @endif
                        @else
                            <img src="https://images.unsplash.com/photo-1523050854058-8df90110c9f1?w=600" alt="{{ $item['title'] }}">
                        @endif

                        @if(($item['type'] ?? 'news') === 'event')
                            <span class="news-category-tag event">
                                <i class="fas fa-calendar-alt me-1"></i>{{ $item['category'] }}
                            </span>
                        @else
                            <span class="news-category-tag">{{ $item['category'] }}</span>
                        @endif

                        <span class="news-date-tag">
                            <i class="far fa-calendar me-1"></i>{{ $item['date']?->format('d.m.Y') }}
                        </span>
                    </div>
                    <div class="news-content">
                        <h4 class="news-title">{{ $item['title'] }}</h4>
                        <p class="news-excerpt">{{ $item['excerpt'] }}</p>
                        <div class="news-footer">
                            <span class="news-views">
                                <i class="fas fa-eye me-1"></i>{{ $item['views'] ?? 0 }} ko'rishlar
                            </span>
                            <a href="{{ $item['url'] }}" class="read-more-btn">
                                Batafsil <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="fas fa-newspaper"></i>
                    </div>
                    <h3 style="color: #1a1a2e; font-weight: 700; margin-bottom: 10px;">Yangiliklar topilmadi</h3>
                    <p style="color: #6b7280;">Tez orada yangi maqolalar va tadbirlar qo'shiladi</p>
                </div>
            </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($news->hasPages())
        <div class="d-flex justify-content-center mt-5" data-aos="fade-up">
            {{ $news->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
</section>

<!-- Newsletter Section -->
<section class="newsletter-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8" data-aos="fade-up">
                <div class="newsletter-box text-center">
                    <h3 class="newsletter-title">
                        <i class="fas fa-envelope me-2"></i>Yangiliklar Obunasi
                    </h3>
                    <p class="newsletter-text">
                        Eng so'nggi yangiliklar va tadbirlar haqida birinchilardan bo'lib xabardor bo'ling
                    </p>
                    <form class="newsletter-form mx-auto">
                        <input type="email" class="newsletter-input" placeholder="Email manzilingiz...">
                        <button type="submit" class="newsletter-btn">
                            <i class="fas fa-paper-plane me-2"></i>Obuna bo'lish
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const categoryBtns = document.querySelectorAll('.category-btn');
    const newsCards = document.querySelectorAll('.news-card');

    categoryBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            categoryBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            const category = this.dataset.category;

            newsCards.forEach(card => {
                const cardParent = card.closest('.col-lg-4');
                if (category === 'all') {
                    cardParent.style.display = 'block';
                    setTimeout(() => {
                        cardParent.style.opacity = '1';
                        cardParent.style.transform = 'translateY(0)';
                    }, 50);
                } else {
                    const cardType = card.dataset.type || 'news';
                    if (cardType.toLowerCase().includes(category)) {
                        cardParent.style.display = 'block';
                        setTimeout(() => {
                            cardParent.style.opacity = '1';
                            cardParent.style.transform = 'translateY(0)';
                        }, 50);
                    } else {
                        cardParent.style.opacity = '0';
                        cardParent.style.transform = 'translateY(20px)';
                        setTimeout(() => {
                            cardParent.style.display = 'none';
                        }, 300);
                    }
                }
            });
        });
    });

    // Newsletter form
    const newsletterForm = document.querySelector('.newsletter-form');
    if (newsletterForm) {
        newsletterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const email = this.querySelector('input').value;
            if (email) {
                alert('Rahmat! ' + email + ' manziliga yangiliklar yuboriladi.');
                this.querySelector('input').value = '';
            }
        });
    }
});
</script>
@endsection
