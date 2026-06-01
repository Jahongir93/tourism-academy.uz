@extends('layouts.dashboard-new')

@section('title', 'PR Dashboard')
@section('page-title', 'PR Dashboard - Kontentni Boshqarish')

@section('content')
<div class="container-fluid">
    <!-- Statistikalar -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-newspaper fa-2x text-primary"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h3 class="mb-0 fw-bold">{{ number_format($stats['total_news']) }}</h3>
                            <p class="text-muted mb-0">Jami yangiliklar</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-success bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-check-circle fa-2x text-success"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h3 class="mb-0 fw-bold">{{ number_format($stats['published_news']) }}</h3>
                            <p class="text-muted mb-0">Nashr etilgan</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-warning bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-edit fa-2x text-warning"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h3 class="mb-0 fw-bold">{{ number_format($stats['draft_news']) }}</h3>
                            <p class="text-muted mb-0">Qoralama</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-info bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-calendar-alt fa-2x text-info"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h3 class="mb-0 fw-bold">{{ number_format($stats['upcoming_events']) }}</h3>
                            <p class="text-muted mb-0">Yaqinlashayotgan tadbirlar</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- CMS Moduli - Yangiliklar -->
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-newspaper text-primary me-2"></i>So'nggi Yangiliklar</h5>
                    <a href="{{ route('cms.news.index') }}" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-plus me-1"></i>Yangi yangilik
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Sarlavha</th>
                                    <th>Kategoriya</th>
                                    <th>Status</th>
                                    <th>Sana</th>
                                    <th>Amallar</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentNews as $news)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($news->image)
                                            <img src="{{ asset($news->image) }}" alt="{{ $news->title }}" class="rounded me-2" style="width: 50px; height: 50px; object-fit: cover;">
                                            @endif
                                            <div>
                                                <h6 class="mb-0">{{ Str::limit($news->title, 50) }}</h6>
                                                <small class="text-muted">{{ Str::limit($news->excerpt, 60) }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($news->category)
                                        <span class="badge bg-secondary">{{ $news->category->name }}</span>
                                        @else
                                        <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($news->status === 'published')
                                        <span class="badge bg-success"><i class="fas fa-check me-1"></i>Nashr etilgan</span>
                                        @elseif($news->status === 'draft')
                                        <span class="badge bg-warning"><i class="fas fa-edit me-1"></i>Qoralama</span>
                                        @else
                                        <span class="badge bg-secondary">{{ $news->status }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $news->created_at->format('d.m.Y') }}</td>
                                    <td>
                                        <a href="{{ route('cms.news.edit', $news->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                        <p>Hali yangilik yo'q</p>
                                        <a href="{{ route('cms.news.create') }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-plus me-1"></i>Birinchi yangilikning yaratish
                                        </a>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Yaqinlashayotgan Tadbirlar -->
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-calendar-alt text-info me-2"></i>Tadbirlar</h5>
                    <a href="{{ route('cms.events.index') }}" class="btn btn-sm btn-outline-info">
                        <i class="fas fa-plus me-1"></i>Yangi
                    </a>
                </div>
                <div class="card-body">
                    @forelse($upcomingEvents as $event)
                    <div class="mb-3 pb-3 border-bottom">
                        <div class="d-flex align-items-start">
                            <div class="flex-shrink-0">
                                <div class="bg-info bg-opacity-10 rounded p-2 text-center" style="min-width: 60px;">
                                    <div class="text-info fw-bold" style="font-size: 1.5rem;">{{ $event->start_date->format('d') }}</div>
                                    <div class="text-muted small">{{ $event->start_date->format('M') }}</div>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1">{{ Str::limit($event->title, 40) }}</h6>
                                <p class="text-muted small mb-1">
                                    <i class="fas fa-clock me-1"></i>{{ $event->start_date->format('H:i') }}
                                </p>
                                @if($event->location)
                                <p class="text-muted small mb-0">
                                    <i class="fas fa-map-marker-alt me-1"></i>{{ Str::limit($event->location, 30) }}
                                </p>
                                @endif
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-calendar-times fa-2x mb-2"></i>
                        <p class="small mb-0">Yaqinlashayotgan tadbirlar yo'q</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Yangiliklar Statistikasi -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-chart-line text-primary me-2"></i>Oylik Yangiliklar Statistikasi</h5>
                </div>
                <div class="card-body">
                    <canvas id="newsStatsChart" height="200"></canvas>
                </div>
            </div>
        </div>

        <!-- Media va Kontent Statistikasi -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-photo-video text-success me-2"></i>Media Statistikasi</h5>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="fw-medium">Jami Media Fayllar</span>
                            <span class="badge bg-primary">{{ number_format($stats['total_media']) }}</span>
                        </div>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar bg-primary" role="progressbar" style="width: 100%"></div>
                        </div>
                    </div>
                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="fw-medium">Jami Yangiliklar</span>
                            <span class="badge bg-success">{{ number_format($stats['total_news']) }}</span>
                        </div>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $stats['total_news'] > 0 ? 100 : 0 }}%"></div>
                        </div>
                    </div>
                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="fw-medium">Jami Tadbirlar</span>
                            <span class="badge bg-info">{{ number_format($stats['total_events']) }}</span>
                        </div>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar bg-info" role="progressbar" style="width: {{ $stats['total_events'] > 0 ? 100 : 0 }}%"></div>
                        </div>
                    </div>
                    <div class="text-center mt-4">
                        <a href="{{ route('cms.media.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-images me-1"></i>Media Kutubxona
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tezkor Havolalar -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-link text-primary me-2"></i>Tezkor Havolalar</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('cms.news.create') }}" class="btn btn-outline-primary w-100 py-3">
                                <i class="fas fa-plus-circle fa-2x mb-2 d-block"></i>
                                Yangi Yangilik
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('cms.events.create') }}" class="btn btn-outline-info w-100 py-3">
                                <i class="fas fa-calendar-plus fa-2x mb-2 d-block"></i>
                                Yangi Tadbir
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('cms.media.index') }}" class="btn btn-outline-success w-100 py-3">
                                <i class="fas fa-photo-video fa-2x mb-2 d-block"></i>
                                Media Kutubxona
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('cms.news.categories') }}" class="btn btn-outline-warning w-100 py-3">
                                <i class="fas fa-tags fa-2x mb-2 d-block"></i>
                                Kategoriyalar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="{{ asset('vendor/chartjs/chart.umd.min.js') }}"></script>
<script>
// Yangiliklar statistikasi grafigi
const newsCtx = document.getElementById('newsStatsChart');
if (newsCtx) {
    const newsStats = @json($newsStats);

    new Chart(newsCtx, {
        type: 'line',
        data: {
            labels: newsStats.map(item => item.month),
            datasets: [{
                label: 'Yangiliklar soni',
                data: newsStats.map(item => item.count),
                borderColor: 'rgb(13, 110, 253)',
                backgroundColor: 'rgba(13, 110, 253, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
}
</script>
@endpush
@endsection
