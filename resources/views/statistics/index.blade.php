@extends('layouts.dashboard-new')

@section('title', 'Statistika')

@section('page-title', 'Statistika dashboard')

@section('content')
<div class="container-fluid">
    <!-- Summary Cards -->
    <div class="row mb-4">
        <!-- Students -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Jami talabalar</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['students']['total'] }}</div>
                            <div class="mt-2 text-xs">
                                <span class="text-success">Faol: {{ $stats['students']['active'] }}</span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-graduate fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Groups -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Guruhlar</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['groups']['total'] }}</div>
                            <div class="mt-2 text-xs">
                                <span class="text-success">Faol: {{ $stats['groups']['active'] }}</span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Teachers -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">O'qituvchilar</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['teachers']['total'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chalkboard-teacher fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Finance -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Oylik daromad</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($stats['finance']['total_income_month'], 0) }} so'm
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row mb-4">
        <!-- Enrollment Trend -->
        <div class="col-xl-8 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Talabalar qabul tendensiyasi (12 oy)</h6>
                </div>
                <div class="card-body">
                    <canvas id="enrollmentChart" height="100"></canvas>
                </div>
            </div>
        </div>

        <!-- Gender Distribution -->
        <div class="col-xl-4 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Jins bo'yicha taqsimot</h6>
                </div>
                <div class="card-body">
                    <canvas id="genderChart"></canvas>
                    <div class="mt-3">
                        @foreach($genderDistribution as $item)
                        <div class="mb-2">
                            <span class="badge badge-{{ $item->gender == 'male' ? 'primary' : 'danger' }}">
                                {{ $item->gender == 'male' ? 'Erkak' : 'Ayol' }}
                            </span>
                            <span class="float-right">{{ $item->total }} ta</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Students by Course -->
    <div class="row mb-4">
        <div class="col-xl-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Kurs bo'yicha talabalar</h6>
                </div>
                <div class="card-body">
                    <canvas id="courseChart" height="150"></canvas>
                </div>
            </div>
        </div>

        <!-- Top Groups -->
        <div class="col-xl-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Eng ko'p talabali guruhlar</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Guruh</th>
                                    <th class="text-right">Talabalar</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topGroups as $group)
                                <tr>
                                    <td>{{ $group->name }}</td>
                                    <td class="text-right"><strong>{{ $group->total }}</strong></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Links -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Batafsil statistika</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <a href="{{ route('statistics.finance') }}" class="btn btn-outline-success btn-block">
                                <i class="fas fa-chart-line"></i> Moliya statistikasi
                            </a>
                        </div>
                        <div class="col-md-4 mb-3">
                            <a href="{{ route('statistics.academic') }}" class="btn btn-outline-info btn-block">
                                <i class="fas fa-graduation-cap"></i> O'quv statistikasi
                            </a>
                        </div>
                        <div class="col-md-4 mb-3">
                            <a href="{{ route('statistics.comparison') }}" class="btn btn-outline-warning btn-block">
                                <i class="fas fa-balance-scale"></i> Taqqoslash
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Enrollment Trend Chart
const enrollmentCtx = document.getElementById('enrollmentChart');
new Chart(enrollmentCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode(array_column($enrollmentTrend, 'month')) !!},
        datasets: [{
            label: 'Yangi talabalar',
            data: {!! json_encode(array_column($enrollmentTrend, 'count')) !!},
            borderColor: 'rgb(78, 115, 223)',
            backgroundColor: 'rgba(78, 115, 223, 0.05)',
            tension: 0.3,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true
    }
});

// Gender Chart
const genderCtx = document.getElementById('genderChart');
new Chart(genderCtx, {
    type: 'doughnut',
    data: {
        labels: {!! json_encode($genderDistribution->pluck('gender')->map(function($g) { return $g == 'male' ? 'Erkak' : 'Ayol'; })->toArray()) !!},
        datasets: [{
            data: {!! json_encode($genderDistribution->pluck('total')->toArray()) !!},
            backgroundColor: ['rgba(78, 115, 223, 0.8)', 'rgba(231, 74, 59, 0.8)']
        }]
    }
});

// Course Chart
const courseCtx = document.getElementById('courseChart');
new Chart(courseCtx, {
    type: 'bar',
    data: {
        labels: {!! json_encode($studentsByCourse->pluck('course')->map(function($c) { return $c . '-kurs'; })->toArray()) !!},
        datasets: [{
            label: 'Talabalar soni',
            data: {!! json_encode($studentsByCourse->pluck('total')->toArray()) !!},
            backgroundColor: 'rgba(28, 200, 138, 0.8)'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true
    }
});
</script>
@endpush
@endsection
