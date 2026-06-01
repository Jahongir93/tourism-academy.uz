@extends('layouts.dashboard-new')

@section('title', 'GPA statistikasi')
@section('page-title', 'GPA statistikasi')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm bg-gradient-success text-white">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1"><i class="fas fa-chart-bar me-2"></i>GPA statistikasi</h4>
                            <p class="mb-0 opacity-75">{{ $faculty?->name ?? 'Fakultet' }}</p>
                        </div>
                        <a href="{{ route('dean.grades.index') }}" class="btn btn-light">
                            <i class="fas fa-arrow-left me-1"></i> O'zlashtirish
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-chart-pie text-success me-2"></i>GPA taqsimoti</h5>
                </div>
                <div class="card-body">
                    <canvas id="gpaChart" height="300"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-list me-2"></i>Statistika</h5>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @forelse($gpaStats as $stat)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            {{ $stat->category }}
                            <span class="badge bg-primary">{{ $stat->count }} ta</span>
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
    const ctx = document.getElementById('gpaChart');
    if (ctx) {
        const data = @json($gpaStats);
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: data.map(d => d.category),
                datasets: [{
                    data: data.map(d => d.count),
                    backgroundColor: ['#198754', '#0d6efd', '#ffc107', '#dc3545']
                }]
            },
            options: { responsive: true, maintainAspectRatio: false }
        });
    }
});
</script>
@endpush

<style>.bg-gradient-success { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); }</style>
@endsection
