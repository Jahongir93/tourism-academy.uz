@extends('layouts.dashboard-new')

@section('title', 'Yillar taqqoslashi')

@section('page-title', 'Statistik taqqoslash')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="mb-4">
        <h2 class="h4 mb-2">Yillar taqqoslashi</h2>
        <p class="text-muted">Turli davr ko'rsatkichlarini solishtirish</p>
    </div>

    <!-- Year Selection -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('statistics.comparison') }}">
                <div class="row align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">1-yil</label>
                        <select name="year1" class="form-control">
                            @for($y = now()->year; $y >= now()->year - 10; $y--)
                                <option value="{{ $y }}" {{ $year1 == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">2-yil</label>
                        <select name="year2" class="form-control">
                            @for($y = now()->year; $y >= now()->year - 10; $y--)
                                <option value="{{ $y }}" {{ $year2 == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search"></i> Taqqoslash
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Comparison Cards -->
    <div class="row mb-4">
        <!-- Enrollments Comparison -->
        <div class="col-md-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Talabalar qabuli</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <h3 class="text-primary">{{ $enrollments['year1'] }}</h3>
                            <p class="text-muted mb-0">{{ $year1 }}</p>
                        </div>
                        <div class="col-6">
                            <h3 class="text-info">{{ $enrollments['year2'] }}</h3>
                            <p class="text-muted mb-0">{{ $year2 }}</p>
                        </div>
                    </div>
                    <div class="mt-3 text-center">
                        @php
                            $diff = $enrollments['year1'] - $enrollments['year2'];
                            $percent = $enrollments['year2'] > 0 ? ($diff / $enrollments['year2']) * 100 : 0;
                        @endphp
                        @if($diff > 0)
                            <span class="badge badge-success">
                                <i class="fas fa-arrow-up"></i> +{{ $diff }} ta (+{{ number_format($percent, 1) }}%)
                            </span>
                        @elseif($diff < 0)
                            <span class="badge badge-danger">
                                <i class="fas fa-arrow-down"></i> {{ $diff }} ta ({{ number_format($percent, 1) }}%)
                            </span>
                        @else
                            <span class="badge badge-secondary">O'zgarmagan</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Income Comparison -->
        <div class="col-md-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">Daromad</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <h3 class="text-success">{{ number_format($income['year1'], 0) }}</h3>
                            <p class="text-muted mb-0">{{ $year1 }} yil</p>
                        </div>
                        <div class="col-6">
                            <h3 class="text-info">{{ number_format($income['year2'], 0) }}</h3>
                            <p class="text-muted mb-0">{{ $year2 }} yil</p>
                        </div>
                    </div>
                    <div class="mt-3 text-center">
                        @php
                            $diff = $income['year1'] - $income['year2'];
                            $percent = $income['year2'] > 0 ? ($diff / $income['year2']) * 100 : 0;
                        @endphp
                        @if($diff > 0)
                            <span class="badge badge-success">
                                <i class="fas fa-arrow-up"></i> +{{ number_format($diff, 0) }} so'm (+{{ number_format($percent, 1) }}%)
                            </span>
                        @elseif($diff < 0)
                            <span class="badge badge-danger">
                                <i class="fas fa-arrow-down"></i> {{ number_format($diff, 0) }} so'm ({{ number_format($percent, 1) }}%)
                            </span>
                        @else
                            <span class="badge badge-secondary">O'zgarmagan</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Comparison Chart -->
    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Oylik daromad taqqoslashi</h6>
        </div>
        <div class="card-body">
            <canvas id="comparisonChart" height="100"></canvas>
        </div>
    </div>
</div>

@push('scripts')
<script src="{{ asset('vendor/chartjs/chart.umd.min.js') }}"></script>
<script>
const ctx = document.getElementById('comparisonChart');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: {!! json_encode(array_column($monthlyComparison, 'month')) !!},
        datasets: [
            {
                label: '{{ $year1 }}',
                data: {!! json_encode(array_column($monthlyComparison, 'year1')) !!},
                borderColor: 'rgb(78, 115, 223)',
                backgroundColor: 'rgba(78, 115, 223, 0.1)',
                tension: 0.3,
                fill: true
            },
            {
                label: '{{ $year2 }}',
                data: {!! json_encode(array_column($monthlyComparison, 'year2')) !!},
                borderColor: 'rgb(54, 185, 204)',
                backgroundColor: 'rgba(54, 185, 204, 0.1)',
                tension: 0.3,
                fill: true
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                display: true,
                position: 'top'
            }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
</script>
@endpush
@endsection
