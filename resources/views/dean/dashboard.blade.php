@extends('layouts.dashboard-new')

@section('title', 'Dekanat Dashboard')
@section('page-title', 'Dekanat Boshqaruv Paneli')

@section('content')
<div class="container-fluid">
    <!-- Fakultet ma'lumoti -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm bg-gradient-success text-white">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1"><i class="fas fa-university me-2"></i>{{ $faculty?->name ?? 'Fakultet' }}</h4>
                            <p class="mb-0 opacity-75">Dekanat boshqaruv paneli - {{ now()->format('d.m.Y') }}</p>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-white text-success fs-6">{{ auth()->user()->name }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistikalar -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-user-graduate fa-2x text-primary"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h3 class="mb-0 fw-bold">{{ number_format($stats['total_students']) }}</h3>
                            <p class="text-muted mb-0">Jami talabalar</p>
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
                                <i class="fas fa-users fa-2x text-success"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h3 class="mb-0 fw-bold">{{ number_format($stats['total_groups']) }}</h3>
                            <p class="text-muted mb-0">Guruhlar soni</p>
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
                                <i class="fas fa-chalkboard-teacher fa-2x text-info"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h3 class="mb-0 fw-bold">{{ number_format($stats['total_teachers']) }}</h3>
                            <p class="text-muted mb-0">O'qituvchilar</p>
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
                                <i class="fas fa-building fa-2x text-warning"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h3 class="mb-0 fw-bold">{{ number_format($stats['total_departments']) }}</h3>
                            <p class="text-muted mb-0">Kafedralar</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Kurs bo'yicha talabalar -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-chart-bar text-primary me-2"></i>Kurs bo'yicha talabalar</h5>
                    <a href="{{ route('dean.students.index') }}" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-list me-1"></i>Barchasi
                    </a>
                </div>
                <div class="card-body">
                    <canvas id="courseChart" height="250"></canvas>
                </div>
            </div>
        </div>

        <!-- Guruhlar statistikasi -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-users text-success me-2"></i>Guruhlar bo'yicha</h5>
                    <a href="{{ route('dean.groups.index') }}" class="btn btn-sm btn-outline-success">
                        <i class="fas fa-list me-1"></i>Barchasi
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="border-0">Guruh</th>
                                    <th class="border-0 text-end">Talabalar</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($groupStats as $group)
                                <tr>
                                    <td>
                                        <span class="fw-medium">{{ $group->name }}</span>
                                    </td>
                                    <td class="text-end">
                                        <span class="badge bg-primary">{{ $group->students_count }} ta</span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="2" class="text-center py-4 text-muted">
                                        Ma'lumot topilmadi
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Kafedralar statistikasi -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-building text-warning me-2"></i>Kafedralar</h5>
                    <a href="{{ route('dean.departments.index') }}" class="btn btn-sm btn-outline-warning">
                        <i class="fas fa-list me-1"></i>Barchasi
                    </a>
                </div>
                <div class="card-body">
                    @forelse($departmentStats as $dept)
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="fw-medium">{{ $dept->name }}</span>
                                <span class="text-muted">{{ $dept->employees_count }} o'qituvchi</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                @php
                                    $maxCount = $departmentStats->max('employees_count') ?: 1;
                                    $percentage = ($dept->employees_count / $maxCount) * 100;
                                @endphp
                                <div class="progress-bar bg-warning" style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-building fa-3x mb-3 opacity-50"></i>
                        <p>Kafedralar mavjud emas</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- So'nggi qo'shilgan talabalar -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-user-plus text-info me-2"></i>So'nggi qo'shilgan talabalar</h5>
                    <a href="{{ route('dean.students.index') }}" class="btn btn-sm btn-outline-info">
                        <i class="fas fa-list me-1"></i>Barchasi
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="border-0">#</th>
                                    <th class="border-0">Talaba</th>
                                    <th class="border-0">Guruh</th>
                                    <th class="border-0 text-end">Kurs</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topStudents as $index => $student)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <span class="fw-medium">{{ $student->last_name }} {{ $student->first_name }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark">{{ $student->group?->name ?? '-' }}</span>
                                    </td>
                                    <td class="text-end">
                                        <span class="badge bg-primary">{{ $student->course }}-kurs</span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">
                                        Ma'lumot topilmadi
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tezkor havolalar -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-bolt text-primary me-2"></i>Tezkor havolalar</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-lg-2 col-md-4 col-sm-6">
                            <a href="{{ route('dean.students.index') }}" class="btn btn-outline-primary w-100 py-3">
                                <i class="fas fa-user-graduate fa-2x mb-2 d-block"></i>
                                Talabalar
                            </a>
                        </div>
                        <div class="col-lg-2 col-md-4 col-sm-6">
                            <a href="{{ route('dean.groups.index') }}" class="btn btn-outline-success w-100 py-3">
                                <i class="fas fa-users fa-2x mb-2 d-block"></i>
                                Guruhlar
                            </a>
                        </div>
                        <div class="col-lg-2 col-md-4 col-sm-6">
                            <a href="{{ route('dean.schedule.index') }}" class="btn btn-outline-info w-100 py-3">
                                <i class="fas fa-calendar-alt fa-2x mb-2 d-block"></i>
                                Dars jadvali
                            </a>
                        </div>
                        <div class="col-lg-2 col-md-4 col-sm-6">
                            <a href="{{ route('dean.grades.index') }}" class="btn btn-outline-warning w-100 py-3">
                                <i class="fas fa-chart-line fa-2x mb-2 d-block"></i>
                                O'zlashtirish
                            </a>
                        </div>
                        <div class="col-lg-2 col-md-4 col-sm-6">
                            <a href="{{ route('dean.attendance.index') }}" class="btn btn-outline-danger w-100 py-3">
                                <i class="fas fa-clipboard-check fa-2x mb-2 d-block"></i>
                                Davomat
                            </a>
                        </div>
                        <div class="col-lg-2 col-md-4 col-sm-6">
                            <a href="{{ route('dean.reports.students') }}" class="btn btn-outline-secondary w-100 py-3">
                                <i class="fas fa-chart-pie fa-2x mb-2 d-block"></i>
                                Hisobotlar
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
document.addEventListener('DOMContentLoaded', function() {
    // Kurs grafigi
    const ctx = document.getElementById('courseChart');
    if (ctx) {
        const courseData = @json($studentsByCourse);

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: courseData.map(item => item.course + '-kurs'),
                datasets: [{
                    label: 'Talabalar soni',
                    data: courseData.map(item => item.count),
                    backgroundColor: [
                        'rgba(13, 110, 253, 0.7)',
                        'rgba(25, 135, 84, 0.7)',
                        'rgba(255, 193, 7, 0.7)',
                        'rgba(220, 53, 69, 0.7)'
                    ],
                    borderColor: [
                        'rgba(13, 110, 253, 1)',
                        'rgba(25, 135, 84, 1)',
                        'rgba(255, 193, 7, 1)',
                        'rgba(220, 53, 69, 1)'
                    ],
                    borderWidth: 1,
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 10 }
                    }
                }
            }
        });
    }
});
</script>
@endpush

<style>
.bg-gradient-success {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
}
</style>
@endsection
