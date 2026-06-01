@extends('layouts.dashboard-new')

@section('title', 'HR Dashboard')
@section('page-title', 'HR Boshqaruv Paneli')

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
                                <i class="fas fa-users fa-2x text-primary"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h3 class="mb-0 fw-bold">{{ number_format($stats['total_employees']) }}</h3>
                            <p class="text-muted mb-0">Jami xodimlar</p>
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
                                <i class="fas fa-user-plus fa-2x text-success"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h3 class="mb-0 fw-bold">{{ number_format($stats['new_employees_month']) }}</h3>
                            <p class="text-muted mb-0">Bu oy yangi xodimlar</p>
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
                                <i class="fas fa-clock fa-2x text-warning"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h3 class="mb-0 fw-bold">{{ number_format($stats['pending_leave_requests']) }}</h3>
                            <p class="text-muted mb-0">Kutilayotgan arizalar</p>
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
                                <i class="fas fa-calendar-check fa-2x text-info"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h3 class="mb-0 fw-bold">{{ number_format($stats['today_attendance']) }}</h3>
                            <p class="text-muted mb-0">Bugungi davomat</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Bo'limlar bo'yicha statistika -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-building text-primary me-2"></i>Bo'limlar bo'yicha xodimlar</h5>
                    <a href="{{ route('hr.reports.employees') }}" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-chart-bar me-1"></i>Batafsil
                    </a>
                </div>
                <div class="card-body">
                    @forelse($departmentStats as $dept)
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="fw-medium">{{ $dept->name }}</span>
                                <span class="text-muted">{{ $dept->employees_count }} ta</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                @php
                                    $maxCount = $departmentStats->max('employees_count') ?: 1;
                                    $percentage = ($dept->employees_count / $maxCount) * 100;
                                @endphp
                                <div class="progress-bar bg-primary" style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-building fa-3x mb-3 opacity-50"></i>
                        <p>Bo'limlar mavjud emas</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- So'nggi ishga qabul qilinganlar -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-user-tie text-success me-2"></i>So'nggi ishga qabul qilinganlar</h5>
                    <a href="{{ route('hr.employees.index') }}" class="btn btn-sm btn-outline-success">
                        <i class="fas fa-list me-1"></i>Barchasi
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <tbody>
                                @forelse($recentHires as $employee)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-light rounded-circle me-3 d-flex align-items-center justify-content-center">
                                                <i class="fas fa-user text-muted"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $employee->last_name }} {{ $employee->first_name }}</h6>
                                                <small class="text-muted">{{ $employee->employee_id }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark">{{ $employee->department?->name ?? 'N/A' }}</span>
                                    </td>
                                    <td class="text-end">
                                        <small class="text-muted">{{ $employee->created_at->format('d.m.Y') }}</small>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center py-4 text-muted">
                                        <i class="fas fa-inbox fa-2x mb-2 d-block opacity-50"></i>
                                        Hozircha ma'lumot yo'q
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
        <!-- Kutilayotgan ta'til arizalari -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-file-alt text-warning me-2"></i>Kutilayotgan ta'til arizalari</h5>
                    <a href="{{ route('hr.leave.requests') }}" class="btn btn-sm btn-outline-warning">
                        <i class="fas fa-list me-1"></i>Barchasi
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <tbody>
                                @forelse($pendingLeaves as $leave)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-warning bg-opacity-10 rounded-circle me-3 d-flex align-items-center justify-content-center">
                                                <i class="fas fa-calendar text-warning"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $leave->employee?->last_name }} {{ $leave->employee?->first_name }}</h6>
                                                <small class="text-muted">{{ $leave->leaveType?->name ?? 'Ta\'til' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <small>{{ $leave->start_date?->format('d.m') }} - {{ $leave->end_date?->format('d.m.Y') }}</small>
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('hr.leave.requests') }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center py-4 text-muted">
                                        <i class="fas fa-check-circle fa-2x mb-2 d-block text-success opacity-50"></i>
                                        Kutilayotgan arizalar yo'q
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Oylik davomat grafigi -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-chart-line text-info me-2"></i>Oylik davomat statistikasi</h5>
                    <a href="{{ route('hr.attendance.index') }}" class="btn btn-sm btn-outline-info">
                        <i class="fas fa-calendar me-1"></i>Davomat
                    </a>
                </div>
                <div class="card-body">
                    <canvas id="attendanceChart" height="200"></canvas>
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
                        <div class="col-lg-3 col-md-4 col-sm-6">
                            <a href="{{ route('hr.employees.create') }}" class="btn btn-outline-primary w-100 py-3">
                                <i class="fas fa-user-plus fa-2x mb-2 d-block"></i>
                                Yangi xodim qo'shish
                            </a>
                        </div>
                        <div class="col-lg-3 col-md-4 col-sm-6">
                            <a href="{{ route('hr.leave.requests', ['status' => 'pending']) }}" class="btn btn-outline-warning w-100 py-3">
                                <i class="fas fa-file-signature fa-2x mb-2 d-block"></i>
                                Arizalarni ko'rish
                            </a>
                        </div>
                        <div class="col-lg-3 col-md-4 col-sm-6">
                            <a href="{{ route('hr.attendance.index') }}" class="btn btn-outline-success w-100 py-3">
                                <i class="fas fa-clipboard-check fa-2x mb-2 d-block"></i>
                                Davomat nazorati
                            </a>
                        </div>
                        <div class="col-lg-3 col-md-4 col-sm-6">
                            <a href="{{ route('hr.reports.employees') }}" class="btn btn-outline-info w-100 py-3">
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
    const ctx = document.getElementById('attendanceChart');
    if (ctx) {
        const monthlyData = @json($monthlyAttendance);

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: monthlyData.map(item => item.date),
                datasets: [{
                    label: 'Kelganlar',
                    data: monthlyData.map(item => item.present),
                    backgroundColor: 'rgba(13, 110, 253, 0.7)',
                    borderColor: 'rgba(13, 110, 253, 1)',
                    borderWidth: 1,
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            display: true,
                            drawBorder: false
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }
});
</script>
@endpush

<style>
.avatar-sm {
    width: 40px;
    height: 40px;
}
</style>
@endsection
