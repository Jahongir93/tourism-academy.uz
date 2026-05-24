@extends('layouts.dashboard-new')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="card border-0 shadow-sm mb-4 bg-gradient-primary text-white">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">
                        <i class="fas fa-table me-2"></i>
                        {{ $groupSubject->subject->name }}
                    </h4>
                    <p class="mb-0 opacity-75">
                        <i class="fas fa-users me-2"></i>{{ $groupSubject->group->name }}
                        <span class="ms-3"><i class="fas fa-door-open me-1"></i>{{ $groupSubject->room ?? 'N/A' }}</span>
                        <span class="ms-3"><i class="fas fa-calendar me-1"></i>{{ $groupSubject->semester }}-semestr</span>
                    </p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('teacher.grades.create', $groupSubject->id) }}" class="btn btn-success btn-sm">
                        <i class="fas fa-plus me-1"></i>Baho qo'shish
                    </a>
                    <a href="{{ route('teacher.grades.index') }}" class="btn btn-light btn-sm">
                        <i class="fas fa-arrow-left me-1"></i>Orqaga
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3">
                            <i class="fas fa-users fa-lg text-primary"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1 small">Talabalar</h6>
                            <h3 class="mb-0 fw-bold">{{ $overallStats['total_students'] }}</h3>
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
                            <i class="fas fa-chalkboard-teacher fa-lg text-success"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1 small">Darslar</h6>
                            <h3 class="mb-0 fw-bold">{{ $overallStats['total_entries'] }}</h3>
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
                            <i class="fas fa-star fa-lg text-warning"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1 small">O'rtacha ball</h6>
                            <h3 class="mb-0 fw-bold">{{ number_format($overallStats['avg_score'], 1) }}</h3>
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
                            <i class="fas fa-percentage fa-lg text-info"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1 small">Davomat</h6>
                            <h3 class="mb-0 fw-bold">{{ number_format($overallStats['avg_attendance'], 1) }}%</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Grades Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-list me-2"></i>
                    Talabalar baholar jadvali
                </h5>
                <div class="input-group" style="max-width: 300px;">
                    <span class="input-group-text bg-white">
                        <i class="fas fa-search text-muted"></i>
                    </span>
                    <input type="text"
                           class="form-control border-start-0"
                           id="searchInput"
                           placeholder="Talaba qidirish...">
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            @if($studentsData->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="gradesTable">
                    <thead class="bg-light sticky-top">
                        <tr>
                            <th class="px-4" rowspan="2" style="vertical-align: middle;">#</th>
                            <th rowspan="2" style="vertical-align: middle; min-width: 200px;">F.I.Sh</th>
                            <th rowspan="2" style="vertical-align: middle;">Talaba ID</th>
                            <th colspan="3" class="text-center border-bottom">Nazorat turlari</th>
                            <th rowspan="2" class="text-center" style="vertical-align: middle; background-color: #fffaeb;">
                                <strong>Umumiy o'rtacha</strong>
                            </th>
                            <th rowspan="2" class="text-center" style="vertical-align: middle;">Davomat</th>
                            <th rowspan="2" class="text-center px-4" style="vertical-align: middle;">Harakat</th>
                        </tr>
                        <tr>
                            <th class="text-center" style="background-color: #e8f4fd;">Joriy</th>
                            <th class="text-center" style="background-color: #fff4e6;">Oraliq</th>
                            <th class="text-center" style="background-color: #ffebee;">Yakuniy</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($studentsData as $index => $studentData)
                        <tr class="student-row">
                            <td class="px-4">{{ $index + 1 }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle me-2" style="width: 36px; height: 36px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 0.8rem;">
                                        {{ strtoupper(substr($studentData['student']->user->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $studentData['student']->user->name }}</div>
                                        <small class="text-muted">{{ $studentData['student']->user->email }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-secondary">{{ $studentData['student']->student_id }}</span>
                            </td>
                            <!-- Joriy -->
                            <td class="text-center" style="background-color: #f8fbff;">
                                @if($studentData['joriy_avg'])
                                <span class="badge bg-info fs-6">{{ number_format($studentData['joriy_avg'], 1) }}</span>
                                <small class="d-block text-muted mt-1">({{ $studentData['joriy_count'] }})</small>
                                @else
                                <span class="text-muted">-</span>
                                @endif
                            </td>
                            <!-- Oraliq -->
                            <td class="text-center" style="background-color: #fffbf5;">
                                @if($studentData['oraliq_avg'])
                                <span class="badge bg-warning fs-6">{{ number_format($studentData['oraliq_avg'], 1) }}</span>
                                <small class="d-block text-muted mt-1">({{ $studentData['oraliq_count'] }})</small>
                                @else
                                <span class="text-muted">-</span>
                                @endif
                            </td>
                            <!-- Yakuniy -->
                            <td class="text-center" style="background-color: #fffafa;">
                                @if($studentData['yakuniy_avg'])
                                <span class="badge bg-danger fs-6">{{ number_format($studentData['yakuniy_avg'], 1) }}</span>
                                <small class="d-block text-muted mt-1">({{ $studentData['yakuniy_count'] }})</small>
                                @else
                                <span class="text-muted">-</span>
                                @endif
                            </td>
                            <!-- Total Average -->
                            <td class="text-center" style="background-color: #fffaeb;">
                                @php
                                    $totalAvg = $studentData['total_avg'];
                                @endphp
                                <span class="badge bg-{{ $totalAvg >= 86 ? 'success' : ($totalAvg >= 71 ? 'primary' : ($totalAvg >= 56 ? 'warning' : 'danger')) }} fs-5">
                                    {{ number_format($totalAvg, 1) }}
                                </span>
                            </td>
                            <!-- Attendance -->
                            <td class="text-center">
                                <div class="d-flex align-items-center justify-content-center">
                                    <small class="me-2">{{ $studentData['attendance_rate'] }}%</small>
                                    <div class="progress" style="width: 60px; height: 8px;">
                                        <div class="progress-bar bg-{{ $studentData['attendance_rate'] >= 80 ? 'success' : ($studentData['attendance_rate'] >= 60 ? 'warning' : 'danger') }}"
                                             style="width: {{ $studentData['attendance_rate'] }}%">
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <!-- Actions -->
                            <td class="text-center px-4">
                                <a href="{{ route('teacher.grades.student', [$groupSubject->id, $studentData['student']->id]) }}"
                                   class="btn btn-sm btn-outline-primary"
                                   title="Batafsil">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center py-5">
                <i class="fas fa-users-slash fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Bu guruhda talabalar yo'q</h5>
            </div>
            @endif
        </div>
    </div>
</div>

<style>
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
.table thead.sticky-top {
    position: sticky;
    top: 0;
    z-index: 10;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const rows = document.querySelectorAll('.student-row');

    searchInput.addEventListener('keyup', function() {
        const searchTerm = this.value.toLowerCase();

        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchTerm) ? '' : 'none';
        });
    });
});
</script>
@endsection
