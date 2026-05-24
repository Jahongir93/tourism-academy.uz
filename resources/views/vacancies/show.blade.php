@extends(\App\Helpers\TemplateHelper::getLayout())

@section('title', $vacancy->title . ' - Tourism Academy')

@section('content')
<!-- Hero Section -->
<section class="page-hero" style="padding-top: 140px; margin-top: -80px;">
    <div class="container">
        <nav class="hero-breadcrumb">
            <a href="{{ route('home') }}">Bosh sahifa</a>
            <span>/</span>
            <a href="{{ route('vacancies.index') }}">Vakansiyalar</a>
            <span>/</span>
            <span>{{ $vacancy->title }}</span>
        </nav>
        <div class="d-flex flex-wrap align-items-center gap-3 mb-3">
            <span class="vacancy-type-badge {{ $vacancy->employment_type }}">
                {{ $vacancy->employment_type_label }}
            </span>
            @if($vacancy->department)
                <span class="vacancy-dept-badge">
                    <i class="fas fa-building me-1"></i>{{ $vacancy->department }}
                </span>
            @endif
            @if($vacancy->is_featured)
                <span class="featured-badge">
                    <i class="fas fa-star me-1"></i>Tanlangan
                </span>
            @endif
        </div>
        <h1 class="hero-title">{{ $vacancy->title }}</h1>
        @if($vacancy->deadline)
            <p class="hero-subtitle">
                <i class="fas fa-clock me-2"></i>Ariza berish muddati: {{ $vacancy->deadline->format('d.m.Y') }}
            </p>
        @endif
    </div>
</section>

<!-- Content -->
<section class="py-5" style="background: #f8fafc;">
    <div class="container">
        <div class="row g-4">
            <!-- Main Content -->
            <div class="col-lg-8">
                <div class="vacancy-detail-card">
                    @if($vacancy->description)
                    <div class="vacancy-section">
                        <h3><i class="fas fa-info-circle me-2 text-primary"></i>Vakansiya haqida</h3>
                        <div class="text-muted">{!! nl2br(e($vacancy->description)) !!}</div>
                    </div>
                    @endif

                    @if($vacancy->responsibilities)
                    <div class="vacancy-section">
                        <h3><i class="fas fa-tasks me-2 text-success"></i>Vazifalar</h3>
                        <ul class="vacancy-list">
                            @foreach(preg_split('/\r\n|\r|\n/', $vacancy->responsibilities) as $item)
                                @if(trim($item))
                                    <li>{{ trim(preg_replace('/^[-•*]\s*/', '', $item)) }}</li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    @if($vacancy->requirements)
                    <div class="vacancy-section">
                        <h3><i class="fas fa-check-circle me-2 text-info"></i>Talablar</h3>
                        <ul class="vacancy-list">
                            @foreach(preg_split('/\r\n|\r|\n/', $vacancy->requirements) as $item)
                                @if(trim($item))
                                    <li>{{ trim(preg_replace('/^[-•*]\s*/', '', $item)) }}</li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    @if($vacancy->benefits)
                    <div class="vacancy-section">
                        <h3><i class="fas fa-gift me-2 text-warning"></i>Imtiyozlar</h3>
                        <ul class="vacancy-list">
                            @foreach(preg_split('/\r\n|\r|\n/', $vacancy->benefits) as $item)
                                @if(trim($item))
                                    <li>{{ trim(preg_replace('/^[-•*]\s*/', '', $item)) }}</li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </div>

                <!-- Related Vacancies -->
                @if($relatedVacancies->count() > 0)
                <div class="mt-5">
                    <h3 class="h5 mb-4">O'xshash vakansiyalar</h3>
                    <div class="row g-3">
                        @foreach($relatedVacancies as $related)
                            <div class="col-md-4">
                                <a href="{{ route('vacancies.show', $related) }}" class="text-decoration-none">
                                    <div class="related-card">
                                        <span class="badge bg-secondary mb-2">{{ $related->employment_type_label }}</span>
                                        <h6 class="text-dark mb-1">{{ $related->title }}</h6>
                                        <small class="text-muted">{{ $related->department }}</small>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <div class="apply-sidebar">
                    <!-- Apply Card -->
                    <div class="vacancy-detail-card mb-4">
                        <div class="vacancy-section">
                            <a href="{{ route('vacancies.apply', $vacancy) }}" class="btn btn-primary btn-lg w-100 mb-3">
                                <i class="fas fa-paper-plane me-2"></i>Ariza topshirish
                            </a>
                            <p class="text-muted text-center small mb-0">
                                <i class="fas fa-users me-1"></i>{{ $vacancy->applications_count }} ta ariza topshirilgan
                            </p>
                        </div>
                    </div>

                    <!-- Info Card -->
                    <div class="info-card">
                        @if($vacancy->salary_range)
                        <div class="info-item">
                            <div class="info-icon">
                                <i class="fas fa-money-bill-wave"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block">Maosh</small>
                                <strong>{{ $vacancy->salary_range }}</strong>
                            </div>
                        </div>
                        @endif

                        <div class="info-item">
                            <div class="info-icon">
                                <i class="fas fa-briefcase"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block">Ish turi</small>
                                <strong>{{ $vacancy->employment_type_label }}</strong>
                            </div>
                        </div>

                        @if($vacancy->experience_required)
                        <div class="info-item">
                            <div class="info-icon">
                                <i class="fas fa-user-clock"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block">Tajriba</small>
                                <strong>{{ $vacancy->experience_required }}</strong>
                            </div>
                        </div>
                        @endif

                        @if($vacancy->education_required)
                        <div class="info-item">
                            <div class="info-icon">
                                <i class="fas fa-graduation-cap"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block">Ma'lumot</small>
                                <strong>{{ $vacancy->education_required }}</strong>
                            </div>
                        </div>
                        @endif

                        @if($vacancy->positions_count > 1)
                        <div class="info-item">
                            <div class="info-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block">Ochiq o'rinlar</small>
                                <strong>{{ $vacancy->positions_count }} ta</strong>
                            </div>
                        </div>
                        @endif

                        @if($vacancy->deadline)
                        <div class="info-item">
                            <div class="info-icon">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block">Muddat</small>
                                <strong>{{ $vacancy->deadline->format('d.m.Y') }}</strong>
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- Share -->
                    <div class="mt-4 text-center">
                        <p class="text-muted small mb-2">Ulashish:</p>
                        <div class="d-flex justify-content-center gap-2">
                            <a href="https://t.me/share/url?url={{ urlencode(request()->url()) }}&text={{ urlencode($vacancy->title) }}"
                               target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="fab fa-telegram"></i>
                            </a>
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}"
                               target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlencode(request()->url()) }}&title={{ urlencode($vacancy->title) }}"
                               target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="fab fa-linkedin-in"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('styles')
<style>
    /* Hero Section */
    .page-hero {
        background: linear-gradient(135deg, #1a365d 0%, #2c5282 100%);
        color: white;
        padding: 60px 0;
        position: relative;
    }
    .page-hero::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        opacity: 0.5;
    }
    .hero-breadcrumb {
        margin-bottom: 20px;
        font-size: 14px;
        position: relative;
        z-index: 1;
    }
    .hero-breadcrumb a {
        color: rgba(255,255,255,0.8);
        text-decoration: none;
    }
    .hero-breadcrumb a:hover {
        color: white;
    }
    .hero-breadcrumb span {
        color: rgba(255,255,255,0.6);
        margin: 0 8px;
    }
    .hero-breadcrumb span:last-child {
        color: white;
        margin: 0;
    }
    .hero-title {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 16px;
        position: relative;
        z-index: 1;
    }
    .hero-subtitle {
        font-size: 1.1rem;
        color: rgba(255,255,255,0.9);
        max-width: 600px;
        position: relative;
        z-index: 1;
        margin: 0;
    }

    /* Badges */
    .vacancy-type-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 6px 14px;
        border-radius: 50px;
        font-size: 12px;
        font-weight: 500;
        position: relative;
        z-index: 1;
    }
    .vacancy-type-badge.full_time { background: rgba(255,255,255,0.2); color: white; }
    .vacancy-type-badge.part_time { background: rgba(255,255,255,0.2); color: white; }
    .vacancy-type-badge.contract { background: rgba(255,255,255,0.2); color: white; }
    .vacancy-type-badge.internship { background: rgba(255,255,255,0.2); color: white; }

    .vacancy-dept-badge {
        display: inline-flex;
        align-items: center;
        padding: 6px 14px;
        border-radius: 50px;
        font-size: 12px;
        background: rgba(255,255,255,0.15);
        color: white;
        position: relative;
        z-index: 1;
    }

    .featured-badge {
        display: inline-flex;
        align-items: center;
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: white;
        padding: 6px 14px;
        border-radius: 50px;
        font-size: 12px;
        font-weight: 600;
        position: relative;
        z-index: 1;
    }

    /* Vacancy Detail Card */
    .vacancy-detail-card {
        background: white;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    }
    .vacancy-section {
        padding: 24px;
        border-bottom: 1px solid #f0f0f0;
    }
    .vacancy-section:last-child {
        border-bottom: none;
    }
    .vacancy-section h3 {
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 16px;
        color: #1a1a1a;
    }
    .vacancy-list {
        padding-left: 0;
        list-style: none;
        margin-bottom: 0;
    }
    .vacancy-list li {
        position: relative;
        padding-left: 24px;
        margin-bottom: 10px;
        line-height: 1.6;
        color: #666;
    }
    .vacancy-list li:last-child {
        margin-bottom: 0;
    }
    .vacancy-list li::before {
        content: '';
        position: absolute;
        left: 0;
        top: 10px;
        width: 8px;
        height: 8px;
        background: #0066CC;
        border-radius: 50%;
    }

    /* Sidebar */
    .apply-sidebar {
        position: sticky;
        top: 100px;
    }
    .info-card {
        background: #f8fafc;
        border-radius: 16px;
        padding: 24px;
    }
    .info-item {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        margin-bottom: 16px;
    }
    .info-item:last-child {
        margin-bottom: 0;
    }
    .info-icon {
        width: 40px;
        height: 40px;
        background: white;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #0066CC;
        flex-shrink: 0;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    }

    /* Related Cards */
    .related-card {
        background: white;
        border-radius: 12px;
        padding: 16px;
        border: 1px solid #e5e7eb;
        transition: all 0.3s ease;
        height: 100%;
    }
    .related-card:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        transform: translateY(-2px);
    }

    @media (max-width: 991px) {
        .hero-title {
            font-size: 2rem;
        }
        .apply-sidebar {
            position: static;
        }
    }
    @media (max-width: 576px) {
        .hero-title {
            font-size: 1.75rem;
        }
    }
</style>
@endsection
