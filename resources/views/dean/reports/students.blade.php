@extends('layouts.dashboard-new')

@section('title', 'Talabalar hisoboti')
@section('page-title', 'Talabalar hisoboti')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm bg-gradient-primary text-white">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1"><i class="fas fa-chart-pie me-2"></i>Talabalar hisoboti</h4>
                            <p class="mb-0 opacity-75">{{ $faculty?->name ?? 'Fakultet' }}</p>
                        </div>
                        <a href="{{ route('dean.dashboard') }}" class="btn btn-light">
                            <i class="fas fa-arrow-left me-1"></i> Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistika -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100 bg-primary bg-opacity-10">
                <div class="card-body text-center">
                    <h3 class="mb-0 fw-bold text-primary">{{ $stats['total'] }}</h3>
                    <p class="text-muted mb-0">Jami talabalar</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100 bg-success bg-opacity-10">
                <div class="card-body text-center">
                    <h3 class="mb-0 fw-bold text-success">{{ $stats['active'] }}</h3>
                    <p class="text-muted mb-0">Faol</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100 bg-warning bg-opacity-10">
                <div class="card-body text-center">
                    <h3 class="mb-0 fw-bold text-warning">{{ $stats['academic_leave'] }}</h3>
                    <p class="text-muted mb-0">Akademik ta'til</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100 bg-danger bg-opacity-10">
                <div class="card-body text-center">
                    <h3 class="mb-0 fw-bold text-danger">{{ $stats['expelled'] }}</h3>
                    <p class="text-muted mb-0">Chetlatilgan</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Kurs bo'yicha -->
    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-chart-bar text-primary me-2"></i>Kurs bo'yicha taqsimot</h5>
                </div>
                <div class="card-body">
                    <canvas id="courseChart" height="250"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-list me-2"></i>Kurs statistikasi</h5>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @forelse($byCourse as $course)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            {{ $course->course }}-kurs
                            <span class="badge bg-primary">{{ $course->count }} ta</span>
                        </li>
                        @empty
                        <li class="list-group-item text-center text-muted">Ma'lumot yo'q</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="{{ asset('vendor/chartjs/chart.umd.min.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('courseChart');
    if (ctx) {
        const data = @json($byCourse);
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.map(d => d.course + '-kurs'),
                datasets: [{
                    label: 'Talabalar',
                    data: data.map(d => d.count),
                    backgroundColor: ['#0d6efd', '#198754', '#ffc107', '#dc3545'],
                    borderRadius: 8
                }]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }
        });
    }
});
</script>
@endpush

<style>.bg-gradient-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }</style>
@endsection
