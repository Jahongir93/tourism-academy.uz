@extends(\App\Helpers\TemplateHelper::getLayout())

@section('title', 'Qidiruv: ' . $query)

@push('styles')
<style>
    .search-hero {
        background: linear-gradient(135deg, #4338CA 0%, #3730A3 100%);
        padding: 140px 0 60px;
        margin-top: -80px;
    }
    .search-input-wrapper {
        position: relative;
        max-width: 600px;
        margin: 0 auto;
    }
    .search-input-large {
        width: 100%;
        padding: 16px 24px 16px 50px;
        font-size: 18px;
        border: none;
        border-radius: 12px;
        background: white;
        box-shadow: 0 4px 20px rgba(0,0,0,0.15);
    }
    .search-input-large:focus {
        outline: none;
        box-shadow: 0 4px 25px rgba(0,0,0,0.2);
    }
    .search-icon-large {
        position: absolute;
        left: 18px;
        top: 50%;
        transform: translateY(-50%);
        color: #6b7280;
        font-size: 18px;
    }
    .search-section {
        padding: 60px 0;
        background: #f8fafc;
        min-height: 400px;
    }
    .filter-tabs {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        margin-bottom: 32px;
    }
    .filter-tab {
        padding: 10px 20px;
        border-radius: 50px;
        border: 1px solid #e5e7eb;
        background: white;
        color: #374151;
        font-size: 14px;
        font-weight: 500;
        text-decoration: none;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .filter-tab:hover {
        border-color: #4338CA;
        color: #4338CA;
    }
    .filter-tab.active {
        background: #4338CA;
        border-color: #4338CA;
        color: white;
    }
    .filter-tab .count {
        background: rgba(0,0,0,0.1);
        padding: 2px 8px;
        border-radius: 20px;
        font-size: 12px;
    }
    .filter-tab.active .count {
        background: rgba(255,255,255,0.2);
    }
    .result-card {
        background: white;
        border-radius: 16px;
        overflow: hidden;
        margin-bottom: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        transition: all 0.3s ease;
    }
    .result-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.1);
    }
    .result-card a {
        display: flex;
        text-decoration: none;
        color: inherit;
    }
    .result-image {
        width: 200px;
        min-height: 150px;
        background: #e5e7eb;
        flex-shrink: 0;
        background-size: cover;
        background-position: center;
    }
    .result-content {
        padding: 20px;
        flex: 1;
    }
    .result-type {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
        margin-bottom: 10px;
    }
    .result-type.news {
        background: #fef3c7;
        color: #b45309;
    }
    .result-type.event {
        background: #dbeafe;
        color: #1d4ed8;
    }
    .result-type.teacher {
        background: #d1fae5;
        color: #047857;
    }
    .result-type.page {
        background: #e0e7ff;
        color: #4338ca;
    }
    .result-title {
        font-size: 18px;
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 8px;
        line-height: 1.4;
    }
    .result-excerpt {
        color: #6b7280;
        font-size: 14px;
        line-height: 1.6;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .result-meta {
        display: flex;
        align-items: center;
        gap: 16px;
        margin-top: 12px;
        font-size: 13px;
        color: #9ca3af;
    }
    .result-meta i {
        margin-right: 4px;
    }
    .no-results {
        text-align: center;
        padding: 60px 20px;
    }
    .no-results i {
        font-size: 64px;
        color: #d1d5db;
        margin-bottom: 20px;
    }
    .no-results h3 {
        font-size: 20px;
        color: #6b7280;
        margin-bottom: 8px;
    }
    .no-results p {
        color: #9ca3af;
    }
    .section-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .section-title i {
        color: #4338CA;
    }
    @media (max-width: 768px) {
        .result-card a {
            flex-direction: column;
        }
        .result-image {
            width: 100%;
            height: 180px;
        }
        .search-input-large {
            font-size: 16px;
            padding: 14px 20px 14px 45px;
        }
    }
</style>
@endpush

@section('content')
<!-- Search Hero -->
<section class="search-hero">
    <div class="container">
        <div class="text-center mb-4">
            <h1 style="color: white; font-size: 2rem; font-weight: 700; margin-bottom: 16px;">Qidiruv</h1>
            <p style="color: rgba(255,255,255,0.8);">Yangiliklar, tadbirlar va o'qituvchilar bo'yicha qidiring</p>
        </div>
        <form action="{{ route('search') }}" method="GET" class="search-input-wrapper">
            <i class="fas fa-search search-icon-large"></i>
            <input type="text" name="q" class="search-input-large" placeholder="Qidirish..." value="{{ $query }}" autofocus>
            <input type="hidden" name="type" value="{{ $type }}">
        </form>
    </div>
</section>

<!-- Results Section -->
<section class="search-section">
    <div class="container">
        @if(strlen($query) >= 2)
            <!-- Filter Tabs -->
            <div class="filter-tabs">
                <a href="{{ route('search', ['q' => $query, 'type' => 'all']) }}"
                   class="filter-tab {{ $type === 'all' ? 'active' : '' }}">
                    <i class="fas fa-th-large"></i>
                    Barchasi
                    <span class="count">{{ $totalCount }}</span>
                </a>
                <a href="{{ route('search', ['q' => $query, 'type' => 'news']) }}"
                   class="filter-tab {{ $type === 'news' ? 'active' : '' }}">
                    <i class="fas fa-newspaper"></i>
                    Yangiliklar
                    <span class="count">{{ $results['news']->count() }}</span>
                </a>
                <a href="{{ route('search', ['q' => $query, 'type' => 'events']) }}"
                   class="filter-tab {{ $type === 'events' ? 'active' : '' }}">
                    <i class="fas fa-calendar-alt"></i>
                    Tadbirlar
                    <span class="count">{{ $results['events']->count() }}</span>
                </a>
                <a href="{{ route('search', ['q' => $query, 'type' => 'teachers']) }}"
                   class="filter-tab {{ $type === 'teachers' ? 'active' : '' }}">
                    <i class="fas fa-user-tie"></i>
                    O'qituvchilar
                    <span class="count">{{ $results['teachers']->count() }}</span>
                </a>
                <a href="{{ route('search', ['q' => $query, 'type' => 'pages']) }}"
                   class="filter-tab {{ $type === 'pages' ? 'active' : '' }}">
                    <i class="fas fa-file-alt"></i>
                    Sahifalar
                    <span class="count">{{ $results['pages']->count() }}</span>
                </a>
            </div>

            @if($totalCount > 0)
                <p style="color: #6b7280; margin-bottom: 24px;">
                    <strong>"{{ $query }}"</strong> bo'yicha {{ $totalCount }} ta natija topildi
                </p>

                <!-- News Results -->
                @if($results['news']->count() > 0 && ($type === 'all' || $type === 'news'))
                    <h2 class="section-title"><i class="fas fa-newspaper"></i> Yangiliklar</h2>
                    @foreach($results['news'] as $news)
                        <div class="result-card">
                            <a href="{{ route('news.show', $news->slug) }}">
                                <div class="result-image" style="background-image: url('{{ $news->featured_image ? asset($news->featured_image) : asset('images/ext/placeholder.jpg') }}')"></div>
                                <div class="result-content">
                                    <span class="result-type news">
                                        <i class="fas fa-newspaper"></i> Yangilik
                                    </span>
                                    <h3 class="result-title">{{ $news->title_uz }}</h3>
                                    <p class="result-excerpt">{{ Str::limit(strip_tags($news->content_uz), 150) }}</p>
                                    <div class="result-meta">
                                        <span><i class="fas fa-calendar"></i> {{ $news->published_at ? $news->published_at->format('d.m.Y') : $news->created_at->format('d.m.Y') }}</span>
                                        <span><i class="fas fa-eye"></i> {{ $news->views_count ?? 0 }}</span>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                @endif

                <!-- Events Results -->
                @if($results['events']->count() > 0 && ($type === 'all' || $type === 'events'))
                    <h2 class="section-title"><i class="fas fa-calendar-alt"></i> Tadbirlar</h2>
                    @foreach($results['events'] as $event)
                        <div class="result-card">
                            <a href="{{ route('news.show', $event->slug ?? $event->id) }}">
                                <div class="result-image" style="background-image: url('{{ $event->featured_image ? asset('storage/' . $event->featured_image) : asset('images/ext/placeholder.jpg') }}')"></div>
                                <div class="result-content">
                                    <span class="result-type event">
                                        <i class="fas fa-calendar-alt"></i> Tadbir
                                    </span>
                                    <h3 class="result-title">{{ $event->title_uz }}</h3>
                                    <p class="result-excerpt">{{ Str::limit(strip_tags($event->description_uz ?? ''), 150) }}</p>
                                    <div class="result-meta">
                                        @if($event->start_date)
                                            <span><i class="fas fa-clock"></i> {{ $event->start_date->format('d.m.Y H:i') }}</span>
                                        @endif
                                        @if($event->location)
                                            <span><i class="fas fa-map-marker-alt"></i> {{ $event->location }}</span>
                                        @endif
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                @endif

                <!-- Teachers Results -->
                @if($results['teachers']->count() > 0 && ($type === 'all' || $type === 'teachers'))
                    <h2 class="section-title"><i class="fas fa-user-tie"></i> O'qituvchilar</h2>
                    <div class="row">
                        @foreach($results['teachers'] as $teacher)
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="result-card">
                                    <a href="{{ route('teachers') }}#teacher-{{ $teacher->id }}" style="flex-direction: column;">
                                        <div class="result-image" style="height: 200px; background-image: url('{{ $teacher->photo_url ?: asset('images/ext/placeholder.jpg') . urlencode(mb_substr($teacher->first_name, 0, 1) . mb_substr($teacher->last_name, 0, 1)) }}')"></div>
                                        <div class="result-content text-center">
                                            <span class="result-type teacher">
                                                <i class="fas fa-user-tie"></i> O'qituvchi
                                            </span>
                                            <h3 class="result-title">{{ $teacher->full_name }}</h3>
                                            @if($teacher->position)
                                                <p class="result-excerpt">{{ $teacher->position }}</p>
                                            @endif
                                            @if($teacher->department)
                                                <div class="result-meta justify-content-center">
                                                    <span><i class="fas fa-building"></i> {{ $teacher->department->name ?? '' }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                <!-- Pages Results -->
                @if($results['pages']->count() > 0 && ($type === 'all' || $type === 'pages'))
                    <h2 class="section-title"><i class="fas fa-file-alt"></i> Sahifalar</h2>
                    @foreach($results['pages'] as $page)
                        @php
                            $pageContent = $page->content_uz ?? '';
                            if (is_array($pageContent)) {
                                $pageContent = json_encode($pageContent);
                            }
                        @endphp
                        <div class="result-card">
                            <a href="{{ url($page->slug) }}">
                                <div class="result-content">
                                    <span class="result-type page">
                                        <i class="fas fa-file-alt"></i> Sahifa
                                    </span>
                                    <h3 class="result-title">{{ $page->title_uz }}</h3>
                                    <p class="result-excerpt">{{ Str::limit(strip_tags($pageContent), 150) }}</p>
                                </div>
                            </a>
                        </div>
                    @endforeach
                @endif

            @else
                <!-- No Results -->
                <div class="no-results">
                    <i class="fas fa-search"></i>
                    <h3>Natija topilmadi</h3>
                    <p>"{{ $query }}" bo'yicha hech narsa topilmadi. Boshqa so'z bilan qidirib ko'ring.</p>
                </div>
            @endif
        @else
            <!-- Empty Query -->
            <div class="no-results">
                <i class="fas fa-keyboard"></i>
                <h3>Qidiruv so'zini kiriting</h3>
                <p>Kamida 2 ta belgi kiriting</p>
            </div>
        @endif
    </div>
</section>
@endsection
