@extends('layouts.dashboard-new')

@section('title', 'Talaba Dashboard')
@section('page-title', 'Mening Dashboard')

@section('styles')
<style>
    :root {
        --primary: #4F46E5;
        --success: #10B981;
        --warning: #F59E0B;
        --danger: #EF4444;
        --info: #3B82F6;
    }

    .profile-header {
        background: linear-gradient(135deg, #667EEA 0%, #764BA2 100%);
        border-radius: 16px;
        padding: 24px;
        color: white;
        margin-bottom: 24px;
    }

    .profile-avatar {
        width: 72px;
        height: 72px;
        border-radius: 50%;
        border: 3px solid white;
        background: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        font-weight: bold;
        color: #667EEA;
    }

    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        border-left: 4px solid;
        box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        height: 100%;
    }

    .stat-card.primary { border-left-color: var(--primary); }
    .stat-card.success { border-left-color: var(--success); }
    .stat-card.warning { border-left-color: var(--warning); }
    .stat-card.info { border-left-color: var(--info); }

    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }

    .stat-icon.primary { background: #EEF2FF; color: var(--primary); }
    .stat-icon.success { background: #D1FAE5; color: var(--success); }
    .stat-icon.warning { background: #FEF3C7; color: var(--warning); }
    .stat-icon.info { background: #DBEAFE; color: var(--info); }

    .card-section {
        background: white;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        margin-bottom: 20px;
    }

    .card-section .card-header {
        padding: 16px 20px;
        border-bottom: 1px solid #E5E7EB;
        background: transparent;
    }

    .card-section .card-body {
        padding: 20px;
    }

    .schedule-item {
        display: flex;
        align-items: center;
        padding: 12px;
        border-radius: 8px;
        background: #F9FAFB;
        margin-bottom: 10px;
        border-left: 4px solid var(--primary);
    }

    .schedule-item:hover {
        background: #EEF2FF;
    }

    .schedule-time {
        min-width: 100px;
        font-weight: 600;
        color: var(--primary);
    }

    .subject-row {
        padding: 12px 0;
        border-bottom: 1px solid #F3F4F6;
    }

    .subject-row:last-child {
        border-bottom: none;
    }

    .teacher-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667EEA 0%, #764BA2 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: 600;
    }

    .grade-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 13px;
    }

    .grade-badge.excellent { background: #D1FAE5; color: #065F46; }
    .grade-badge.good { background: #DBEAFE; color: #1E40AF; }
    .grade-badge.satisfactory { background: #FEF3C7; color: #92400E; }
    .grade-badge.poor { background: #FEE2E2; color: #991B1B; }

    .quick-action {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 16px;
        border-radius: 12px;
        border: 2px solid #E5E7EB;
        background: white;
        transition: all 0.2s ease;
        text-decoration: none;
        color: #374151;
        height: 100%;
    }

    .quick-action:hover {
        border-color: var(--primary);
        background: #EEF2FF;
        color: var(--primary);
        transform: translateY(-2px);
    }

    .quick-action i {
        font-size: 24px;
        margin-bottom: 8px;
    }

    .assignment-card {
        padding: 12px;
        border-radius: 8px;
        margin-bottom: 10px;
        border-left: 4px solid;
    }

    .assignment-card.urgent {
        background: #FEF2F2;
        border-left-color: var(--danger);
    }

    .assignment-card.soon {
        background: #FFFBEB;
        border-left-color: var(--warning);
    }

    .assignment-card.normal {
        background: #EFF6FF;
        border-left-color: var(--info);
    }

    .empty-state {
        text-align: center;
        padding: 40px 20px;
        color: #9CA3AF;
    }

    .empty-state i {
        font-size: 48px;
        margin-bottom: 12px;
        opacity: 0.5;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Profil header -->
    <div class="profile-header">
        <div class="row align-items-center">
            <div class="col-auto">
                <div class="profile-avatar">
                    @if($student && $student->photo)
                        <img src="{{ asset('storage/' . $student->photo) }}" alt="Photo" class="rounded-circle" style="width: 100%; height: 100%; object-fit: cover;">
                    @else
                        {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                    @endif
                </div>
            </div>
            <div class="col">
                <h4 class="mb-1 fw-bold">{{ Auth::user()->name }}</h4>
                <div class="d-flex gap-3 flex-wrap small">
                    @if($student)
                    <span><i class="fas fa-id-card me-1"></i> {{ $student->student_id ?? 'N/A' }}</span>
                    @endif
                    @if($group)
                    <span><i class="fas fa-building me-1"></i> {{ $group->department->name ?? $group->specialty->name ?? '-' }}</span>
                    <span><i class="fas fa-layer-group me-1"></i> {{ $group->course ?? '-' }}-kurs</span>
                    <span><i class="fas fa-users me-1"></i> {{ $group->name ?? '-' }}</span>
                    @else
                    <span class="text-warning-subtle"><i class="fas fa-exclamation-triangle me-1"></i> Guruh biriktirilmagan</span>
                    @endif
                </div>
            </div>
            <div class="col-auto">
                <a href="{{ route('student.profile.index') }}" class="btn btn-light btn-sm">
                    <i class="fas fa-user-edit me-1"></i> Profil
                </a>
            </div>
        </div>
    </div>

    <!-- Statistika -->
    <div class="row mb-4">
        <div class="col-md-3 col-6 mb-3">
            <div class="stat-card primary">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted mb-1 small">O'rtacha ball</p>
                        <h3 class="mb-0 fw-bold">{{ number_format($gpa ?? 0, 1) }}</h3>
                        <small class="text-muted">100 ballik tizim</small>
                    </div>
                    <div class="stat-icon primary">
                        <i class="fas fa-chart-line"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="stat-card info">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted mb-1 small">Fanlar soni</p>
                        <h3 class="mb-0 fw-bold">{{ $total_courses ?? 0 }}</h3>
                        <small class="text-muted">Joriy semestr</small>
                    </div>
                    <div class="stat-icon info">
                        <i class="fas fa-book"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="stat-card warning">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted mb-1 small">Reyting</p>
                        <h3 class="mb-0 fw-bold">{{ $student_rank ?? '-' }}</h3>
                        <small class="text-muted">Guruhda</small>
                    </div>
                    <div class="stat-icon warning">
                        <i class="fas fa-trophy"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="stat-card success">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted mb-1 small">Davomat</p>
                        <h3 class="mb-0 fw-bold">{{ $attendance_percentage ?? 0 }}%</h3>
                        <small class="text-muted">{{ $attendance_present ?? 0 }}/{{ $attendance_total ?? 0 }} kun</small>
                    </div>
                    <div class="stat-icon success">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Chap ustun -->
        <div class="col-lg-8">
            <!-- Bugungi dars jadvali -->
            <div class="card-section">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold">
                        <i class="fas fa-calendar-day text-primary me-2"></i>
                        Bugungi darslar
                    </h6>
                    <span class="badge bg-primary">{{ now()->translatedFormat('l, d F') }}</span>
                </div>
                <div class="card-body">
                    @forelse($todaySchedule as $schedule)
                    <div class="schedule-item">
                        <div class="schedule-time">{{ $schedule['time'] }}</div>
                        <div class="flex-grow-1">
                            <div class="fw-semibold">{{ $schedule['subject'] }}</div>
                            <small class="text-muted">
                                <i class="fas fa-user-tie"></i> {{ $schedule['teacher'] }}
                                <span class="mx-2">|</span>
                                <i class="fas fa-door-open"></i> {{ $schedule['room'] }}
                            </small>
                        </div>
                        <span class="badge bg-{{ $schedule['type_color'] }}">{{ $schedule['type'] }}</span>
                    </div>
                    @empty
                    <div class="empty-state">
                        <i class="fas fa-calendar-times d-block"></i>
                        <p class="mb-0">Bugun darslar yo'q</p>
                    </div>
                    @endforelse

                    <div class="text-center mt-3">
                        <a href="{{ route('student.schedule') }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-calendar-alt me-1"></i> To'liq jadval
                        </a>
                    </div>
                </div>
            </div>

            <!-- Fanlar va o'qituvchilar -->
            <div class="card-section">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold">
                        <i class="fas fa-book-open text-primary me-2"></i>
                        Joriy semestr fanlari
                    </h6>
                    <span class="badge bg-secondary">{{ $groupSubjects->count() }} ta fan</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 40px">№</th>
                                    <th>Fan nomi</th>
                                    <th>O'qituvchi</th>
                                    <th class="text-center" style="width: 120px">Soatlar</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($groupSubjects as $index => $item)
                                <tr>
                                    <td class="text-muted">{{ $index + 1 }}</td>
                                    <td>
                                        <div class="fw-semibold">{{ $item['subject']->name_uz ?? $item['subject']->name ?? 'Noma\'lum' }}</div>
                                        @if($item['subject'] && $item['subject']->code)
                                        <small class="text-muted">{{ $item['subject']->code }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($item['teacher'])
                                        <div class="d-flex align-items-center">
                                            <div class="teacher-avatar me-2">
                                                {{ substr($item['teacher']->first_name ?? 'N', 0, 1) }}{{ substr($item['teacher']->last_name ?? '', 0, 1) }}
                                            </div>
                                            <span class="small">{{ $item['teacher_name'] }}</span>
                                        </div>
                                        @else
                                        <span class="badge bg-warning text-dark">Belgilanmagan</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($item['hours'])
                                        <div class="d-flex gap-1 justify-content-center">
                                            <span class="badge bg-info" title="Ma'ruza">{{ $item['hours']['lecture'] }}</span>
                                            <span class="badge bg-success" title="Amaliy">{{ $item['hours']['practice'] }}</span>
                                            <span class="badge bg-warning text-dark" title="Lab">{{ $item['hours']['lab'] }}</span>
                                        </div>
                                        @else
                                        <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">
                                        <i class="fas fa-book-open fa-2x mb-2 d-block opacity-50"></i>
                                        @if($group)
                                        Fanlar hali biriktirilmagan
                                        @else
                                        Guruh biriktirilmagan
                                        @endif
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Baholar -->
            @if($studentGrades->count() > 0)
            <div class="card-section">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold">
                        <i class="fas fa-star text-warning me-2"></i>
                        Baholarim
                    </h6>
                    <a href="{{ route('grades.all') }}" class="btn btn-sm btn-outline-primary">
                        Barchasini ko'rish
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Fan</th>
                                    <th class="text-center">Joriy</th>
                                    <th class="text-center">Oraliq</th>
                                    <th class="text-center">Yakuniy</th>
                                    <th class="text-center">Jami</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($studentGrades->take(5) as $gradeData)
                                @php
                                    $total = $gradeData['total_score'] ?? 0;
                                    $badgeClass = $total >= 86 ? 'excellent' : ($total >= 71 ? 'good' : ($total >= 56 ? 'satisfactory' : 'poor'));
                                @endphp
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $gradeData['subject']->name_uz ?? $gradeData['subject']->name ?? 'Noma\'lum' }}</div>
                                        <small class="text-muted">{{ $gradeData['teacher_name'] }}</small>
                                    </td>
                                    <td class="text-center">{{ $gradeData['current_score'] ?? '-' }}</td>
                                    <td class="text-center">{{ $gradeData['midterm_score'] ?? '-' }}</td>
                                    <td class="text-center">{{ $gradeData['final_score'] ?? '-' }}</td>
                                    <td class="text-center">
                                        <span class="grade-badge {{ $badgeClass }}">{{ $total }}</span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- O'ng ustun -->
        <div class="col-lg-4">
            <!-- Tezkor harakatlar -->
            <div class="card-section">
                <div class="card-header">
                    <h6 class="mb-0 fw-bold">
                        <i class="fas fa-bolt text-warning me-2"></i>
                        Tezkor harakatlar
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-6">
                            <a href="{{ route('student.schedule') }}" class="quick-action">
                                <i class="fas fa-calendar-alt text-primary"></i>
                                <span class="small fw-semibold">Dars jadvali</span>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('student.attendance.index') }}" class="quick-action">
                                <i class="fas fa-clipboard-check text-success"></i>
                                <span class="small fw-semibold">Davomat</span>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('grades.all') }}" class="quick-action">
                                <i class="fas fa-star text-warning"></i>
                                <span class="small fw-semibold">Baholar</span>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('student.assignments.index') }}" class="quick-action">
                                <i class="fas fa-tasks text-info"></i>
                                <span class="small fw-semibold">Topshiriqlar</span>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('student.documents.index') }}" class="quick-action">
                                <i class="fas fa-file-alt text-secondary"></i>
                                <span class="small fw-semibold">Hujjatlar</span>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('student.help') }}" class="quick-action">
                                <i class="fas fa-question-circle text-danger"></i>
                                <span class="small fw-semibold">Yordam</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Yaqinlashayotgan topshiriqlar -->
            <div class="card-section">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold">
                        <i class="fas fa-clock text-danger me-2"></i>
                        Yaqin muddatli topshiriqlar
                    </h6>
                    @if(count($upcomingAssignments) > 0)
                    <span class="badge bg-danger">{{ count($upcomingAssignments) }}</span>
                    @endif
                </div>
                <div class="card-body">
                    @forelse($upcomingAssignments as $assignment)
                    <div class="assignment-card {{ $assignment['urgency'] }}">
                        <div class="d-flex justify-content-between align-items-start mb-1">
                            <div class="fw-semibold small">{{ $assignment['title'] }}</div>
                            <span class="badge bg-{{ $assignment['badge_color'] }}">{{ $assignment['deadline_text'] }}</span>
                        </div>
                        <div class="small text-muted">
                            <i class="fas fa-book me-1"></i> {{ $assignment['subject'] }}
                        </div>
                        <div class="small text-muted">
                            <i class="fas fa-calendar me-1"></i> {{ $assignment['deadline'] }}
                        </div>
                        <div class="mt-2">
                            <a href="{{ route('student.assignments.show', $assignment['id']) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-eye me-1"></i> Ko'rish
                            </a>
                        </div>
                    </div>
                    @empty
                    <div class="empty-state py-3">
                        <i class="fas fa-check-circle d-block text-success" style="font-size: 32px;"></i>
                        <p class="mb-0 small">Barcha topshiriqlar bajarilgan</p>
                    </div>
                    @endforelse

                    @if(count($upcomingAssignments) > 0)
                    <div class="text-center mt-2">
                        <a href="{{ route('student.assignments.index') }}" class="btn btn-sm btn-outline-secondary">
                            Barchasini ko'rish
                        </a>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Guruh ma'lumotlari -->
            @if($group)
            <div class="card-section">
                <div class="card-header">
                    <h6 class="mb-0 fw-bold">
                        <i class="fas fa-users text-primary me-2"></i>
                        Guruh ma'lumotlari
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="small text-muted">Guruh nomi</div>
                        <div class="fw-semibold">{{ $group->name }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="small text-muted">Yo'nalish</div>
                        <div class="fw-semibold">{{ $group->specialty->name ?? $group->department->name ?? '-' }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="small text-muted">Kurs</div>
                        <div class="fw-semibold">{{ $group->course }}-kurs</div>
                    </div>
                    <div class="mb-3">
                        <div class="small text-muted">Talabalar soni</div>
                        <div class="fw-semibold">{{ $group->students_count ?? $group->students()->count() ?? '-' }} ta</div>
                    </div>
                    @if($group->curator)
                    <div>
                        <div class="small text-muted">Kurator</div>
                        <div class="fw-semibold">{{ $group->curator->full_name ?? '-' }}</div>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
