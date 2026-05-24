@extends('layouts.dashboard-new')

@section('title', 'Xodimlar hisoboti')
@section('page-title', 'Xodimlar hisoboti')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm bg-gradient-primary text-white">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1"><i class="fas fa-chart-pie me-2"></i>Xodimlar hisoboti</h4>
                            <p class="mb-0 opacity-75">Xodimlar bo'yicha umumiy statistika va tahlil</p>
                        </div>
                        <a href="{{ route('hr.dashboard') }}" class="btn btn-light">
                            <i class="fas fa-arrow-left me-1"></i> Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistika -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="bg-primary bg-opacity-10 rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                        <i class="fas fa-users fa-2x text-primary"></i>
                    </div>
                    <h3 class="mb-0 fw-bold">{{ number_format($stats['total']) }}</h3>
                    <p class="text-muted mb-0">Jami xodimlar</p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="bg-success bg-opacity-10 rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                        <i class="fas fa-user-check fa-2x text-success"></i>
                    </div>
                    <h3 class="mb-0 fw-bold text-success">{{ number_format($stats['active']) }}</h3>
                    <p class="text-muted mb-0">Faol xodimlar</p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="bg-warning bg-opacity-10 rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                        <i class="fas fa-user-clock fa-2x text-warning"></i>
                    </div>
                    <h3 class="mb-0 fw-bold text-warning">{{ number_format($stats['inactive']) }}</h3>
                    <p class="text-muted mb-0">Nofaol xodimlar</p>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="bg-danger bg-opacity-10 rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                        <i class="fas fa-user-times fa-2x text-danger"></i>
                    </div>
                    <h3 class="mb-0 fw-bold text-danger">{{ number_format($stats['terminated']) }}</h3>
                    <p class="text-muted mb-0">Ishdan bo'shatilganlar</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Bo'limlar bo'yicha -->
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-building text-primary me-2"></i>Bo'limlar bo'yicha xodimlar</h5>
                </div>
                <div class="card-body">
                    <canvas id="departmentChart" height="300"></canvas>
                </div>
            </div>
        </div>

        <!-- Foizlar -->
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-chart-pie text-info me-2"></i>Status bo'yicha</h5>
                </div>
                <div class="card-body">
                    <canvas id="statusChart" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Bo'limlar ro'yxati -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0"><i class="fas fa-list me-2"></i>Bo'limlar statistikasi</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="border-0">#</th>
                            <th class="border-0">Bo'lim nomi</th>
                            <th class="border-0">Xodimlar soni</th>
                            <th class="border-0">Foiz</th>
                            <th class="border-0">Progress</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($byDepartment as $index => $dept)
                        @php
                            $totalEmployees = $stats['total'] ?: 1;
                            $percentage = ($dept->employees_count / $totalEmployees) * 100;
                        @endphp
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td class="fw-medium">{{ $dept->name }}</td>
                            <td>{{ $dept->employees_count }} ta</td>
                            <td>{{ number_format($percentage, 1) }}%</td>
                            <td style="width: 40%">
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-primary" style="width: {{ $percentage }}%"></div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">
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

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Bo'limlar grafigi
    const deptCtx = document.getElementById('departmentChart');
    if (deptCtx) {
        const deptData = @json($byDepartment);
        new Chart(deptCtx, {
            type: 'bar',
            data: {
                labels: deptData.map(d => d.name),
                datasets: [{
                    label: 'Xodimlar soni',
                    data: deptData.map(d => d.employees_count),
                    backgroundColor: 'rgba(102, 126, 234, 0.7)',
                    borderColor: 'rgba(102, 126, 234, 1)',
                    borderWidth: 1,
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    }

    // Status grafigi
    const statusCtx = document.getElementById('statusChart');
    if (statusCtx) {
        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: ['Faol', 'Nofaol', 'Ishdan bo\'shatilgan'],
                datasets: [{
                    data: [{{ $stats['active'] }}, {{ $stats['inactive'] }}, {{ $stats['terminated'] }}],
                    backgroundColor: [
                        'rgba(25, 135, 84, 0.8)',
                        'rgba(255, 193, 7, 0.8)',
                        'rgba(220, 53, 69, 0.8)'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }
});
</script>
@endpush

<style>
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
</style>
@endsection
