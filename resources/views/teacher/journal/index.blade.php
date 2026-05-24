@extends('layouts.dashboard-new')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="card border-0 shadow-sm mb-4 bg-gradient-primary text-white">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">
                        <i class="fas fa-book me-2"></i>
                        Jurnal
                    </h4>
                    <p class="mb-0 opacity-75">O'quv jurnallari va talabalar baholar jadvali</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Journals List -->
    <div class="row">
        @forelse($journalsData as $data)
        <div class="col-lg-6 col-xl-4 mb-4">
            <div class="card border-0 shadow-sm h-100 hover-lift">
                <div class="card-body">
                    @php
                        $subjectName = isset($data['subject']) ? ($data['subject']->name_uz ?? $data['subject']->name ?? 'N/A') : 'N/A';
                        $groupName = isset($data['group']) ? ($data['group']->name ?? 'N/A') : 'N/A';
                        $totalStudents = $data['total_students'] ?? 0;
                        $entriesCount = $data['entries_count'] ?? 0;
                        $room = $data['room'] ?? null;
                        $semester = $data['semester'] ?? 1;
                        $academicYear = $data['academic_year'] ?? null;
                        $avgScore = $data['avg_score'] ?? 0;
                    @endphp
                    <!-- Subject Name -->
                    <div class="d-flex align-items-start mb-3">
                        <div class="rounded-circle bg-indigo bg-opacity-10 p-3 me-3">
                            <i class="fas fa-book-open fa-lg text-indigo"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h5 class="mb-1">{{ $subjectName }}</h5>
                            <p class="text-muted mb-0 small">
                                <i class="fas fa-users me-1"></i>
                                {{ $groupName }}
                            </p>
                        </div>
                    </div>

                    <!-- Info -->
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <div class="bg-light rounded p-2 text-center">
                                <small class="text-muted d-block">Talabalar</small>
                                <strong class="text-primary">{{ $totalStudents }}</strong>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="bg-light rounded p-2 text-center">
                                <small class="text-muted d-block">Darslar</small>
                                <strong class="text-success">{{ $entriesCount }}</strong>
                            </div>
                        </div>
                    </div>

                    <!-- Room, Semester, Year -->
                    <div class="mb-3">
                        @if($room)
                        <span class="badge bg-secondary me-2">
                            <i class="fas fa-door-open me-1"></i>{{ $room }}
                        </span>
                        @endif
                        <span class="badge bg-info me-2">
                            <i class="fas fa-calendar me-1"></i>{{ $semester }}-semestr
                        </span>
                        @if($academicYear)
                        <span class="badge bg-primary">
                            {{ $academicYear->year ?? $academicYear->name ?? '' }}
                        </span>
                        @endif
                    </div>

                    <!-- Average Score -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <small class="text-muted">O'rtacha ball</small>
                            <h5 class="mb-0">
                                <span class="badge bg-{{ $avgScore >= 86 ? 'success' : ($avgScore >= 71 ? 'primary' : ($avgScore >= 56 ? 'warning' : 'secondary')) }}">
                                    {{ $avgScore > 0 ? number_format($avgScore, 1) : '0.0' }}
                                </span>
                            </h5>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-{{ $avgScore >= 86 ? 'success' : ($avgScore >= 71 ? 'primary' : ($avgScore >= 56 ? 'warning' : 'secondary')) }}"
                                 style="width: {{ $avgScore }}%">
                            </div>
                        </div>
                    </div>

                    <!-- Attendance Rate -->
                    @php $attendanceRate = $data['attendance_rate'] ?? 0; @endphp
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <small class="text-muted">Davomat</small>
                            <small class="fw-bold">{{ $attendanceRate }}%</small>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-{{ $attendanceRate >= 80 ? 'success' : ($attendanceRate >= 60 ? 'warning' : 'danger') }}"
                                 style="width: {{ $attendanceRate }}%">
                            </div>
                        </div>
                    </div>

                    <!-- Grade Distribution -->
                    @php
                        $joriyCount = $data['joriy_count'] ?? 0;
                        $oraliqCount = $data['oraliq_count'] ?? 0;
                        $yakuniyCount = $data['yakuniy_count'] ?? 0;
                        $journalId = $data['id'] ?? 0;
                        $journalType = $data['type'] ?? 'group_subject';
                    @endphp
                    <div class="mb-3 pb-3 border-bottom">
                        <small class="text-muted d-block mb-2">Nazorat turlari</small>
                        <div class="d-flex justify-content-between text-center">
                            <div>
                                <div class="badge bg-info mb-1">{{ $joriyCount }}</div>
                                <small class="d-block text-muted" style="font-size: 0.7rem;">Joriy</small>
                            </div>
                            <div>
                                <div class="badge bg-warning mb-1">{{ $oraliqCount }}</div>
                                <small class="d-block text-muted" style="font-size: 0.7rem;">Oraliq</small>
                            </div>
                            <div>
                                <div class="badge bg-danger mb-1">{{ $yakuniyCount }}</div>
                                <small class="d-block text-muted" style="font-size: 0.7rem;">Yakuniy</small>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    @if(in_array($journalType, ['group_subject', 'journal_entry']) && is_numeric($journalId))
                    <div class="d-flex gap-2">
                        <a href="{{ route('teacher.journal.show', $journalId) }}"
                           class="btn btn-primary btn-sm flex-grow-1">
                            <i class="fas fa-eye me-1"></i>Jurnalni ko'rish
                        </a>
                        <a href="{{ route('teacher.journal.export', [$journalId, 'pdf']) }}"
                           class="btn btn-outline-secondary btn-sm"
                           title="PDF yuklab olish">
                            <i class="fas fa-file-pdf"></i>
                        </a>
                    </div>
                    @else
                    <div class="d-flex gap-2">
                        <span class="btn btn-secondary btn-sm flex-grow-1 disabled">
                            <i class="fas fa-clock me-1"></i>Tez kunda
                        </span>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="fas fa-book fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Sizga hech qanday fan biriktirilmagan</h5>
                    <p class="text-muted">O'quv qismiga murojaat qiling</p>
                </div>
            </div>
        </div>
        @endforelse
    </div>
</div>

<style>
.hover-lift {
    transition: all 0.3s ease;
}
.hover-lift:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
.text-indigo {
    color: #6366f1;
}
.bg-indigo {
    background-color: #6366f1;
}
</style>
@endsection
