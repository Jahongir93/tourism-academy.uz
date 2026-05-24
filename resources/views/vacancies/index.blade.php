@extends(\App\Helpers\TemplateHelper::getLayout())

@section('title', 'Vakansiyalar - Tourism Academy')

@section('content')
<!-- Hero Section -->
<section class="page-hero" style="padding-top: 140px; margin-top: -80px;">
    <div class="container">
        <nav class="hero-breadcrumb">
            <a href="{{ route('home') }}">Bosh sahifa</a>
            <span>/</span>
            <span>Vakansiyalar</span>
        </nav>
        <h1 class="hero-title">
            <i class="fas fa-briefcase me-3"></i>Vakansiyalar
        </h1>
        <p class="hero-subtitle">
            Tourism Academy jamoasiga qo'shiling! Bizda ochiq ish o'rinlari bilan tanishing va o'z karyerangizni boshlang.
        </p>
    </div>
</section>

<!-- Main Content -->
<section class="py-5" style="background: #f8fafc;">
    <div class="container">
        <div class="row g-4">
            <!-- Filters Sidebar -->
            <div class="col-lg-3">
                <div class="filter-card">
                    <h5 class="filter-title">
                        <i class="fas fa-filter me-2"></i>Filtrlar
                    </h5>
                    <form method="GET" action="{{ route('vacancies.index') }}">
                        <!-- Search -->
                        <div class="mb-4">
                            <label class="form-label small text-muted">Qidirish</label>
                            <input type="text" name="q" class="form-control" placeholder="Vakansiya nomi..."
                                   value="{{ request('q') }}">
                        </div>

                        <!-- Employment Type -->
                        <div class="mb-4">
                            <label class="form-label small text-muted">Ish turi</label>
                            <select name="type" class="form-select">
                                <option value="">Barchasi</option>
                                @foreach($employmentTypes as $key => $label)
                                    <option value="{{ $key }}" {{ request('type') === $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Department -->
                        @if($departments->count() > 0)
                        <div class="mb-4">
                            <label class="form-label small text-muted">Bo'lim</label>
                            <select name="department" class="form-select">
                                <option value="">Barchasi</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept }}" {{ request('department') === $dept ? 'selected' : '' }}>
                                        {{ $dept }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @endif

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search me-2"></i>Qidirish
                        </button>

                        @if(request()->hasAny(['q', 'type', 'department']))
                            <a href="{{ route('vacancies.index') }}" class="btn btn-outline-secondary w-100 mt-2">
                                <i class="fas fa-times me-2"></i>Tozalash
                            </a>
                        @endif
                    </form>
                </div>
            </div>

            <!-- Vacancies Grid -->
            <div class="col-lg-9">
                @if($vacancies->count() > 0)
                    <div class="row g-4">
                        @foreach($vacancies as $vacancy)
                            <div class="col-md-6">
                                <div class="vacancy-card">
                                    @if($vacancy->is_featured)
                                        <span class="featured-badge">
                                            <i class="fas fa-star me-1"></i>Tanlangan
                                        </span>
                                    @endif
                                    <div class="vacancy-card-body">
                                        <span class="vacancy-type-badge {{ $vacancy->employment_type }}">
                                            {{ $vacancy->employment_type_label }}
                                        </span>

                                        <h3 class="vacancy-title">
                                            <a href="{{ route('vacancies.show', $vacancy) }}">
                                                {{ $vacancy->title }}
                                            </a>
                                        </h3>

                                        @if($vacancy->department)
                                            <p class="vacancy-department">
                                                <i class="fas fa-building me-1"></i>{{ $vacancy->department }}
                                            </p>
                                        @endif

                                        @if($vacancy->description)
                                            <p class="vacancy-excerpt">
                                                {{ Str::limit(strip_tags($vacancy->description), 120) }}
                                            </p>
                                        @endif

                                        <div class="vacancy-meta">
                                            @if($vacancy->salary_range)
                                                <div class="vacancy-meta-item">
                                                    <i class="fas fa-money-bill-wave text-success"></i>
                                                    <span>{{ $vacancy->salary_range }}</span>
                                                </div>
                                            @endif
                                            @if($vacancy->experience_required)
                                                <div class="vacancy-meta-item">
                                                    <i class="fas fa-user-clock text-info"></i>
                                                    <span>{{ $vacancy->experience_required }}</span>
                                                </div>
                                            @endif
                                            @if($vacancy->deadline)
                                                <div class="vacancy-meta-item">
                                                    <i class="fas fa-calendar-alt text-warning"></i>
                                                    <span>{{ $vacancy->deadline->format('d.m.Y') }} gacha</span>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="mt-4">
                                            <a href="{{ route('vacancies.show', $vacancy) }}" class="btn btn-outline-primary btn-sm">
                                                Batafsil <i class="fas fa-arrow-right ms-1"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-5">
                        {{ $vacancies->links() }}
                    </div>
                @else
                    <div class="empty-state">
                        <i class="fas fa-briefcase"></i>
                        <h3>Vakansiyalar topilmadi</h3>
                        <p>
                            @if(request()->hasAny(['q', 'type', 'department']))
                                Qidiruv natijasida vakansiyalar topilmadi
                            @else
                                Hozircha ochiq vakansiyalar yo'q
                            @endif
                        </p>
                        @if(request()->hasAny(['q', 'type', 'department']))
                            <a href="{{ route('vacancies.index') }}" class="btn btn-primary">
                                Barcha vakansiyalarni ko'rish
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section">
    <div class="container text-center">
        <h2>Mos vakansiya topmadingizmi?</h2>
        <p>Rezyumengizni bizga yuboring, biz sizga mos vakansiya paydo bo'lganda xabar beramiz!</p>
        <a href="mailto:hr@tourismacademy.uz" class="btn btn-light btn-lg">
            <i class="fas fa-envelope me-2"></i>hr@tourismacademy.uz
        </a>
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
    }

    /* Filter Card */
    .filter-card {
        background: white;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        position: sticky;
        top: 100px;
    }
    .filter-title {
        font-weight: 600;
        margin-bottom: 20px;
        padding-bottom: 16px;
        border-bottom: 1px solid #eee;
    }

    /* Vacancy Cards */
    .vacancy-card {
        background: white;
        border-radius: 16px;
        overflow: hidden;
        transition: all 0.3s ease;
        border: 1px solid #e5e7eb;
        height: 100%;
        position: relative;
    }
    .vacancy-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 30px rgba(0,0,0,0.1);
    }
    .vacancy-card-body {
        padding: 24px;
    }
    .featured-badge {
        position: absolute;
        top: 16px;
        right: 16px;
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: white;
        padding: 6px 12px;
        border-radius: 50px;
        font-size: 11px;
        font-weight: 600;
        z-index: 1;
    }
    .vacancy-type-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 6px 14px;
        border-radius: 50px;
        font-size: 12px;
        font-weight: 500;
    }
    .vacancy-type-badge.full_time { background: #dcfce7; color: #166534; }
    .vacancy-type-badge.part_time { background: #fef3c7; color: #92400e; }
    .vacancy-type-badge.contract { background: #dbeafe; color: #1e40af; }
    .vacancy-type-badge.internship { background: #f3e8ff; color: #7c3aed; }

    .vacancy-title {
        font-size: 1.25rem;
        font-weight: 600;
        margin: 16px 0 8px;
    }
    .vacancy-title a {
        color: #1a1a1a;
        text-decoration: none;
        transition: color 0.2s;
    }
    .vacancy-title a:hover {
        color: #0066CC;
    }
    .vacancy-department {
        color: #666;
        font-size: 14px;
        margin-bottom: 12px;
    }
    .vacancy-excerpt {
        color: #666;
        font-size: 14px;
        line-height: 1.6;
        margin-bottom: 0;
    }
    .vacancy-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 16px;
        margin-top: 16px;
        padding-top: 16px;
        border-top: 1px solid #f0f0f0;
    }
    .vacancy-meta-item {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 13px;
        color: #666;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 80px 20px;
        background: white;
        border-radius: 16px;
    }
    .empty-state i {
        font-size: 4rem;
        color: #d1d5db;
        margin-bottom: 24px;
    }
    .empty-state h3 {
        font-size: 1.5rem;
        font-weight: 600;
        color: #374151;
        margin-bottom: 12px;
    }
    .empty-state p {
        color: #6b7280;
        margin-bottom: 24px;
    }

    /* CTA Section */
    .cta-section {
        background: linear-gradient(135deg, #059669 0%, #10b981 100%);
        color: white;
        padding: 60px 0;
    }
    .cta-section h2 {
        font-size: 1.75rem;
        font-weight: 700;
        margin-bottom: 16px;
    }
    .cta-section p {
        color: rgba(255,255,255,0.9);
        margin-bottom: 24px;
        font-size: 1.1rem;
    }
    .cta-section .btn-light {
        background: white;
        color: #059669;
        border: none;
        font-weight: 600;
    }
    .cta-section .btn-light:hover {
        background: #f0fdf4;
    }

    @media (max-width: 991px) {
        .hero-title {
            font-size: 2rem;
        }
        .filter-card {
            position: static;
            margin-bottom: 24px;
        }
    }
    @media (max-width: 576px) {
        .hero-title {
            font-size: 1.75rem;
        }
        .vacancy-meta {
            flex-direction: column;
            gap: 8px;
        }
    }
</style>
@endsection
