@extends('layouts.dashboard-new')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="card border-0 shadow-sm mb-4 bg-gradient-primary text-white">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">
                        <i class="fas fa-clipboard-list me-2"></i>
                        Dars tafsilotlari
                    </h4>
                    <p class="mb-0 opacity-75">
                        <i class="fas fa-calendar me-2"></i>{{ $journalEntry->created_at->format('d.m.Y H:i') }}
                    </p>
                </div>
                <div>
                    <a href="{{ route('teacher.attendance.journal', $journalEntry->subject_id) }}" class="btn btn-light btn-sm">
                        <i class="fas fa-arrow-left me-1"></i>Orqaga
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Left Column: Lesson Info -->
        <div class="col-lg-4 mb-4">
            <!-- Lesson Details Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Dars ma'lumotlari
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Subject -->
                    <div class="mb-3 pb-3 border-bottom">
                        <small class="text-muted d-block mb-1">Fan</small>
                        <h6 class="mb-0">{{ $journalEntry->subject->name }}</h6>
                    </div>

                    <!-- Group -->
                    <div class="mb-3 pb-3 border-bottom">
                        <small class="text-muted d-block mb-1">Guruh</small>
                        <h6 class="mb-0">{{ $journalEntry->group->name }}</h6>
                        <small class="text-muted">{{ $journalEntry->group->specialty->name ?? '' }}</small>
                    </div>

                    <!-- Topic -->
                    <div class="mb-3 pb-3 border-bottom">
                        <small class="text-muted d-block mb-1">Mavzu</small>
                        <h6 class="mb-0">{{ $journalEntry->grades->first()->topic ?? 'Mavzu ko\'rsatilmagan' }}</h6>
                    </div>

                    <!-- Lesson Type -->
                    <div class="mb-3 pb-3 border-bottom">
                        <small class="text-muted d-block mb-1">Nazorat turi</small>
                        @php
                            $gradeType = $journalEntry->grades->first()->grade_type ?? 'joriy';
                            $badgeClass = $gradeType == 'joriy' ? 'bg-info' : ($gradeType == 'oraliq' ? 'bg-warning' : 'bg-danger');
                            $typeLabel = $gradeType == 'joriy' ? 'Joriy nazorat' : ($gradeType == 'oraliq' ? 'Oraliq nazorat' : 'Yakuniy nazorat');
                        @endphp
                        <span class="badge {{ $badgeClass }}">{{ $typeLabel }}</span>
                    </div>

                    <!-- Date -->
                    <div class="mb-0">
                        <small class="text-muted d-block mb-1">Sana</small>
                        <h6 class="mb-0">
                            <i class="fas fa-calendar-alt text-primary me-1"></i>
                            {{ $journalEntry->created_at->format('d.m.Y') }}
                        </h6>
                        <small class="text-muted">{{ $journalEntry->created_at->format('H:i') }}</small>
                    </div>
                </div>
            </div>

            <!-- Statistics Card -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-bar me-2"></i>
                        Statistika
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Total Students -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <small class="text-muted d-block">Jami talabalar</small>
                            <h4 class="mb-0">{{ $journalEntry->group->students()->where('status', 'active')->count() }}</h4>
                        </div>
                        <div class="rounded-circle bg-primary bg-opacity-10 p-3">
                            <i class="fas fa-users fa-lg text-primary"></i>
                        </div>
                    </div>

                    <!-- Present Students -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <small class="text-muted d-block">Ishtirok etdi</small>
                            <h4 class="mb-0 text-success">{{ $journalEntry->grades->count() }}</h4>
                        </div>
                        <div class="rounded-circle bg-success bg-opacity-10 p-3">
                            <i class="fas fa-check-circle fa-lg text-success"></i>
                        </div>
                    </div>

                    <!-- Absent Students -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <small class="text-muted d-block">Kelmadi</small>
                            <h4 class="mb-0 text-danger">
                                {{ $journalEntry->group->students()->where('status', 'active')->count() - $journalEntry->grades->count() }}
                            </h4>
                        </div>
                        <div class="rounded-circle bg-danger bg-opacity-10 p-3">
                            <i class="fas fa-times-circle fa-lg text-danger"></i>
                        </div>
                    </div>

                    <!-- Average Score -->
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted d-block">O'rtacha ball</small>
                            @php
                                $avgScore = $journalEntry->grades->whereNotNull('score')->avg('score');
                            @endphp
                            <h4 class="mb-0 text-warning">
                                {{ $avgScore ? number_format($avgScore, 1) : '0.0' }}
                            </h4>
                        </div>
                        <div class="rounded-circle bg-warning bg-opacity-10 p-3">
                            <i class="fas fa-star fa-lg text-warning"></i>
                        </div>
                    </div>

                    <!-- Attendance Rate Progress -->
                    <div class="mt-3 pt-3 border-top">
                        @php
                            $totalStudents = $journalEntry->group->students()->where('status', 'active')->count();
                            $presentStudents = $journalEntry->grades->count();
                            $attendanceRate = $totalStudents > 0 ? round(($presentStudents / $totalStudents) * 100, 1) : 0;
                        @endphp
                        <small class="text-muted d-block mb-2">Davomat foizi</small>
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar bg-{{ $attendanceRate >= 80 ? 'success' : ($attendanceRate >= 60 ? 'warning' : 'danger') }}"
                                 role="progressbar"
                                 style="width: {{ $attendanceRate }}%"
                                 aria-valuenow="{{ $attendanceRate }}"
                                 aria-valuemin="0"
                                 aria-valuemax="100">
                                <strong>{{ $attendanceRate }}%</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Students List -->
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-list me-2"></i>
                        Talabalar ro'yxati
                    </h5>
                </div>
                <div class="card-body p-0">
                    @if($journalEntry->grades->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="px-4">#</th>
                                    <th>F.I.Sh</th>
                                    <th>Talaba ID</th>
                                    <th>Holat</th>
                                    <th class="text-center">Ball</th>
                                    <th>Baho</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($journalEntry->grades as $index => $grade)
                                <tr>
                                    <td class="px-4">{{ $index + 1 }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-circle me-2" style="width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">
                                                {{ strtoupper(substr($grade->student->user->name, 0, 2)) }}
                                            </div>
                                            <div>
                                                <div class="fw-bold">{{ $grade->student->user->name }}</div>
                                                <small class="text-muted">{{ $grade->student->user->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $grade->student->student_id }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-success">
                                            <i class="fas fa-check me-1"></i>Ishtirok etdi
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        @if($grade->score !== null)
                                        <span class="badge bg-{{ $grade->score >= 86 ? 'success' : ($grade->score >= 71 ? 'primary' : ($grade->score >= 56 ? 'warning' : 'danger')) }} fs-6">
                                            {{ number_format($grade->score, 1) }}
                                        </span>
                                        @else
                                        <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($grade->score !== null)
                                            @if($grade->score >= 86)
                                                <span class="badge bg-success">A (5)</span>
                                            @elseif($grade->score >= 71)
                                                <span class="badge bg-primary">B (4)</span>
                                            @elseif($grade->score >= 56)
                                                <span class="badge bg-warning">C (3)</span>
                                            @else
                                                <span class="badge bg-danger">F (2)</span>
                                            @endif
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Score Distribution -->
                    <div class="p-4 border-top bg-light">
                        <h6 class="mb-3">
                            <i class="fas fa-chart-pie me-2"></i>
                            Baholar taqsimoti
                        </h6>
                        <div class="row text-center">
                            @php
                                $scoreA = $journalEntry->grades->where('score', '>=', 86)->count();
                                $scoreB = $journalEntry->grades->whereBetween('score', [71, 85])->count();
                                $scoreC = $journalEntry->grades->whereBetween('score', [56, 70])->count();
                                $scoreF = $journalEntry->grades->where('score', '<', 56)->where('score', '!=', null)->count();
                            @endphp
                            <div class="col-3">
                                <div class="p-3 bg-white rounded shadow-sm">
                                    <div class="h1 mb-2 text-success">{{ $scoreA }}</div>
                                    <small class="text-muted">A (5)</small>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="p-3 bg-white rounded shadow-sm">
                                    <div class="h1 mb-2 text-primary">{{ $scoreB }}</div>
                                    <small class="text-muted">B (4)</small>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="p-3 bg-white rounded shadow-sm">
                                    <div class="h1 mb-2 text-warning">{{ $scoreC }}</div>
                                    <small class="text-muted">C (3)</small>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="p-3 bg-white rounded shadow-sm">
                                    <div class="h1 mb-2 text-danger">{{ $scoreF }}</div>
                                    <small class="text-muted">F (2)</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="text-center py-5">
                        <i class="fas fa-users-slash fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Hech kim ishtirok etmadi</h5>
                    </div>
                    @endif
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
@endsection
