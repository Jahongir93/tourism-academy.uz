@extends('layouts.dashboard-new')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="card border-0 shadow-sm mb-4 bg-gradient-primary text-white">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">
                        <i class="fas fa-chart-bar me-2"></i>
                        Davomat hisoboti
                    </h4>
                    <p class="mb-0 opacity-75">Barcha guruhlar bo'yicha davomat statistikasi</p>
                </div>
                <div>
                    <a href="{{ route('teacher.attendance.index') }}" class="btn btn-light btn-sm">
                        <i class="fas fa-arrow-left me-1"></i>Orqaga
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3">
                            <i class="fas fa-book fa-lg text-primary"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1 small">Jami fanlar</h6>
                            <h3 class="mb-0 fw-bold">{{ $reportData->pluck('subject.id')->unique()->count() }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-success bg-opacity-10 p-3 me-3">
                            <i class="fas fa-users fa-lg text-success"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1 small">Jami guruhlar</h6>
                            <h3 class="mb-0 fw-bold">{{ $reportData->count() }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-info bg-opacity-10 p-3 me-3">
                            <i class="fas fa-chalkboard-teacher fa-lg text-info"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1 small">Jami darslar</h6>
                            <h3 class="mb-0 fw-bold">{{ $reportData->sum('total_entries') }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-warning bg-opacity-10 p-3 me-3">
                            <i class="fas fa-percentage fa-lg text-warning"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1 small">O'rtacha davomat</h6>
                            <h3 class="mb-0 fw-bold">{{ $reportData->count() > 0 ? number_format($reportData->avg('attendance_rate'), 1) : 0 }}%</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Report Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-table me-2 text-primary"></i>
                    Batafsil hisobot
                </h5>
                <div class="input-group" style="max-width: 300px;">
                    <span class="input-group-text bg-white">
                        <i class="fas fa-search text-muted"></i>
                    </span>
                    <input type="text"
                           class="form-control border-start-0"
                           id="searchInput"
                           placeholder="Qidirish...">
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            @if($reportData->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="reportTable">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4">#</th>
                            <th>Fan</th>
                            <th>Guruh</th>
                            <th class="text-center">Talabalar</th>
                            <th class="text-center">Darslar</th>
                            <th class="text-center">Qatnashuvlar</th>
                            <th class="text-center">Davomat</th>
                            <th class="text-center px-4">Holat</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reportData as $index => $data)
                        <tr class="report-row">
                            <td class="px-4">{{ $index + 1 }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-primary bg-opacity-10 p-2 me-2">
                                        <i class="fas fa-book text-primary small"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $data['subject']->name ?? 'N/A' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-secondary">{{ $data['group']->name ?? 'N/A' }}</span>
                            </td>
                            <td class="text-center">
                                <span class="fw-bold">{{ $data['total_students'] }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-info">{{ $data['total_entries'] }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-success">{{ $data['total_attendance'] }}</span>
                            </td>
                            <td class="text-center">
                                <div class="d-flex align-items-center justify-content-center">
                                    <div class="progress me-2" style="width: 60px; height: 8px;">
                                        <div class="progress-bar bg-{{ $data['attendance_rate'] >= 80 ? 'success' : ($data['attendance_rate'] >= 60 ? 'warning' : 'danger') }}"
                                             style="width: {{ $data['attendance_rate'] }}%">
                                        </div>
                                    </div>
                                    <span class="fw-bold">{{ $data['attendance_rate'] }}%</span>
                                </div>
                            </td>
                            <td class="text-center px-4">
                                @if($data['attendance_rate'] >= 80)
                                <span class="badge bg-success">
                                    <i class="fas fa-check me-1"></i>Yaxshi
                                </span>
                                @elseif($data['attendance_rate'] >= 60)
                                <span class="badge bg-warning">
                                    <i class="fas fa-exclamation me-1"></i>O'rta
                                </span>
                                @else
                                <span class="badge bg-danger">
                                    <i class="fas fa-times me-1"></i>Past
                                </span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center py-5">
                <i class="fas fa-chart-bar fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Ma'lumotlar topilmadi</h5>
                <p class="text-muted">Sizga hali fan va guruh biriktirilmagan yoki davomat belgilanmagan</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Legend -->
    <div class="card border-0 shadow-sm mt-4">
        <div class="card-body">
            <h6 class="mb-3"><i class="fas fa-info-circle me-2"></i>Davomat holati:</h6>
            <div class="d-flex flex-wrap gap-4">
                <div class="d-flex align-items-center">
                    <span class="badge bg-success me-2">Yaxshi</span>
                    <span class="text-muted small">80% va undan yuqori</span>
                </div>
                <div class="d-flex align-items-center">
                    <span class="badge bg-warning me-2">O'rta</span>
                    <span class="text-muted small">60% - 79%</span>
                </div>
                <div class="d-flex align-items-center">
                    <span class="badge bg-danger me-2">Past</span>
                    <span class="text-muted small">60% dan past</span>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const rows = document.querySelectorAll('.report-row');

    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();

            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });
    }
});
</script>
@endsection
