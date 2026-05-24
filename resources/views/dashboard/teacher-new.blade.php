@extends('layouts.dashboard-new')

@section('title', 'O\'qituvchi Dashboard')
@section('page-title', 'Bosh sahifa')

@section('content')
<div class="container-fluid px-4 py-3">
    @if(!$teacher)
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle me-2"></i>
            O'qituvchi profili topilmadi. Iltimos administrator bilan bog'laning.
        </div>
    @else
        <!-- Profile Header -->
        <div class="row mb-3">
            <div class="col-12">
                <div class="card border-0 bg-gradient-primary text-white shadow">
                    <div class="card-body p-4">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <div class="avatar-circle bg-white bg-opacity-25" style="width: 70px; height: 70px;">
                                    @if($teacher->user && $teacher->user->photo)
                                        <img src="{{ asset('storage/' . $teacher->user->photo) }}" alt="Photo" class="rounded-circle" style="width: 70px; height: 70px; object-fit: cover;">
                                    @else
                                        <div class="d-flex align-items-center justify-content-center h-100">
                                            <i class="fas fa-user-tie fa-2x text-white"></i>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col">
                                <h4 class="mb-1">{{ $teacher->user->name ?? $teacher->full_name ?? 'O\'qituvchi' }}</h4>
                                <p class="mb-0 opacity-75">
                                    <i class="fas fa-briefcase me-2"></i>{{ $teacher->position ?? 'O\'qituvchi' }}
                                    @if($teacher->department)
                                        <span class="mx-2">|</span>
                                        <i class="fas fa-building me-2"></i>{{ $teacher->department->name }}
                                    @endif
                                </p>
                            </div>
                            <div class="col-auto text-end">
                                <p class="mb-0 small opacity-75">Bugun</p>
                                <h5 class="mb-0">{{ \Carbon\Carbon::now()->format('d.m.Y') }}</h5>
                                <p class="mb-0 small">{{ \Carbon\Carbon::now()->translatedFormat('l') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-3">
            <div class="col-6 col-md-3 mb-2">
                <div class="card border-0 shadow-sm h-100 stat-card">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center">
                            <div class="stat-icon bg-primary bg-opacity-10">
                                <i class="fas fa-book fa-lg text-primary"></i>
                            </div>
                            <div class="ms-3">
                                <h6 class="text-muted mb-0 small">Fanlar</h6>
                                <h3 class="mb-0 fw-bold">{{ $subjects_count }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3 mb-2">
                <div class="card border-0 shadow-sm h-100 stat-card">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center">
                            <div class="stat-icon bg-success bg-opacity-10">
                                <i class="fas fa-users fa-lg text-success"></i>
                            </div>
                            <div class="ms-3">
                                <h6 class="text-muted mb-0 small">Guruhlar</h6>
                                <h3 class="mb-0 fw-bold">{{ $groups_count }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3 mb-2">
                <div class="card border-0 shadow-sm h-100 stat-card">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center">
                            <div class="stat-icon bg-info bg-opacity-10">
                                <i class="fas fa-user-graduate fa-lg text-info"></i>
                            </div>
                            <div class="ms-3">
                                <h6 class="text-muted mb-0 small">Talabalar</h6>
                                <h3 class="mb-0 fw-bold">{{ $total_students }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3 mb-2">
                <div class="card border-0 shadow-sm h-100 stat-card">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center">
                            <div class="stat-icon bg-warning bg-opacity-10">
                                <i class="fas fa-journal-whills fa-lg text-warning"></i>
                            </div>
                            <div class="ms-3">
                                <h6 class="text-muted mb-0 small">Jurnallar</h6>
                                <h3 class="mb-0 fw-bold">{{ $journals_count ?? 0 }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Left Column - Main Content -->
            <div class="col-lg-8">
                <!-- Today's Schedule -->
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white border-0 p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-calendar-day text-primary me-2"></i>Bugungi darslar
                            </h5>
                            <a href="{{ route('teacher.schedule') }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-calendar-alt me-1"></i>To'liq jadval
                            </a>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        @if($todaySchedule->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover mb-0 align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 120px;">Vaqt</th>
                                            <th>Fan</th>
                                            <th>Guruh</th>
                                            <th style="width: 80px;">Xona</th>
                                            <th style="width: 100px;">Harakat</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($todaySchedule as $schedule)
                                        <tr>
                                            <td>
                                                <i class="far fa-clock text-muted me-1"></i>
                                                @if(is_object($schedule) && isset($schedule->start_time))
                                                    {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} -
                                                    {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}
                                                @else
                                                    {{ $schedule['start_time'] ?? '09:00' }} - {{ $schedule['end_time'] ?? '10:30' }}
                                                @endif
                                            </td>
                                            <td>
                                                <strong>
                                                    @if(is_object($schedule) && isset($schedule->subject))
                                                        {{ $schedule->subject->name ?? $schedule->subject->name_uz ?? 'N/A' }}
                                                    @else
                                                        {{ $schedule['subject'] ?? 'N/A' }}
                                                    @endif
                                                </strong>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary">
                                                    @if(is_object($schedule) && isset($schedule->group))
                                                        {{ $schedule->group->name ?? 'N/A' }}
                                                    @else
                                                        {{ $schedule['group'] ?? 'N/A' }}
                                                    @endif
                                                </span>
                                            </td>
                                            <td>
                                                <i class="fas fa-door-open text-muted me-1"></i>
                                                @if(is_object($schedule) && isset($schedule->room))
                                                    {{ $schedule->room->name ?? 'N/A' }}
                                                @else
                                                    {{ $schedule['room'] ?? 'N/A' }}
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('teacher.attendance.index') }}" class="btn btn-sm btn-success">
                                                    <i class="fas fa-check"></i> Davomat
                                                </a>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                                <p class="text-muted mb-0">Bugun darslar yo'q</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- My Groups and Subjects -->
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white border-0 p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-layer-group text-success me-2"></i>Mening guruhlarim
                            </h5>
                            <a href="{{ route('teacher.subjects.index') }}" class="btn btn-sm btn-outline-success">
                                <i class="fas fa-list me-1"></i>Barcha fanlar
                            </a>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        @if(isset($subjectsByGroup) && count($subjectsByGroup) > 0)
                            <div class="accordion accordion-flush" id="groupsAccordion">
                                @foreach($subjectsByGroup as $index => $item)
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="heading{{ $loop->index }}">
                                        <button class="accordion-button {{ $index > 0 ? 'collapsed' : '' }}"
                                                type="button"
                                                data-bs-toggle="collapse"
                                                data-bs-target="#groupCollapse{{ $loop->index }}"
                                                aria-expanded="{{ $index == 0 ? 'true' : 'false' }}"
                                                aria-controls="groupCollapse{{ $loop->index }}">
                                            <span class="badge bg-primary me-2">{{ $item['group']['name'] }}</span>
                                            <span class="text-muted small me-2">{{ $item['group']['specialty'] }}</span>
                                            <span class="badge bg-secondary ms-auto me-2">{{ $item['group']['student_count'] }} talaba</span>
                                        </button>
                                    </h2>
                                    <div id="groupCollapse{{ $loop->index }}"
                                         class="accordion-collapse collapse {{ $index == 0 ? 'show' : '' }}"
                                         aria-labelledby="heading{{ $loop->index }}"
                                         data-bs-parent="#groupsAccordion">
                                        <div class="accordion-body p-2">
                                            <div class="list-group list-group-flush">
                                                @foreach($item['subjects'] as $subject)
                                                <div class="list-group-item d-flex justify-content-between align-items-center px-2">
                                                    <div>
                                                        <i class="fas fa-book-open text-primary me-2"></i>
                                                        {{ $subject['name'] }}
                                                        @if($subject['has_journal'])
                                                            <span class="badge bg-success ms-2">Jurnal mavjud</span>
                                                        @endif
                                                    </div>
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="{{ route('teacher.journal.index') }}" class="btn btn-outline-primary" title="Jurnal">
                                                            <i class="fas fa-book"></i>
                                                        </a>
                                                        <a href="{{ route('teacher.grades.index') }}" class="btn btn-outline-warning" title="Baholar">
                                                            <i class="fas fa-star"></i>
                                                        </a>
                                                        <a href="{{ route('teacher.attendance.index') }}" class="btn btn-outline-success" title="Davomat">
                                                            <i class="fas fa-check-circle"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @elseif(isset($teacherGroups) && count($teacherGroups) > 0)
                            <div class="list-group list-group-flush">
                                @foreach($teacherGroups as $group)
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="badge bg-primary me-2">{{ $group['name'] }}</span>
                                        <span class="text-muted small">{{ $group['specialty'] }}</span>
                                    </div>
                                    <span class="badge bg-secondary">{{ $group['student_count'] }} talaba</span>
                                </div>
                                @endforeach
                            </div>
                        @elseif(isset($teacherSubjects) && $teacherSubjects->count() > 0)
                            <!-- Show TeacherSubjects if no groups -->
                            <div class="list-group list-group-flush">
                                @foreach($teacherSubjects as $ts)
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="fas fa-book-open text-primary me-2"></i>
                                        {{ $ts->subject->name ?? $ts->subject->name_uz ?? 'N/A' }}
                                    </div>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('teacher.journal.index') }}" class="btn btn-outline-primary" title="Jurnal">
                                            <i class="fas fa-book"></i>
                                        </a>
                                        <a href="{{ route('teacher.grades.index') }}" class="btn btn-outline-warning" title="Baholar">
                                            <i class="fas fa-star"></i>
                                        </a>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-users-slash fa-2x text-muted mb-2"></i>
                                <p class="text-muted mb-0">Guruhlar biriktirilmagan</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- LMS Courses Section -->
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white border-0 p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-graduation-cap text-purple me-2"></i>LMS Kurslarim
                            </h5>
                            <div>
                                <a href="{{ route('lms.courses.index') }}" class="btn btn-sm btn-outline-secondary me-1">
                                    <i class="fas fa-list me-1"></i>Barchasi
                                </a>
                                <a href="{{ route('lms.courses.create') }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-plus me-1"></i>Yangi kurs
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-3">
                        <!-- Course Statistics -->
                        <div class="row mb-3">
                            <div class="col-4 text-center">
                                <h4 class="mb-0 text-primary">{{ $lmsCourseStats['total'] }}</h4>
                                <small class="text-muted">Jami kurslar</small>
                            </div>
                            <div class="col-4 text-center">
                                <h4 class="mb-0 text-success">{{ $lmsCourseStats['published'] }}</h4>
                                <small class="text-muted">Nashr qilingan</small>
                            </div>
                            <div class="col-4 text-center">
                                <h4 class="mb-0 text-info">{{ $lmsCourseStats['total_enrollments'] }}</h4>
                                <small class="text-muted">Talabalar</small>
                            </div>
                        </div>

                        @if($lmsCourses->count() > 0)
                            <div class="list-group list-group-flush">
                                @foreach($lmsCourses as $course)
                                <a href="{{ route('lms.courses.show', $course) }}" class="list-group-item list-group-item-action px-2">
                                    <div class="d-flex w-100 justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            @if($course->thumbnail)
                                                <img src="{{ asset('storage/' . $course->thumbnail) }}" alt="{{ $course->title }}" class="rounded me-2" style="width: 48px; height: 48px; object-fit: cover;">
                                            @else
                                                <div class="rounded me-2 d-flex align-items-center justify-content-center bg-light" style="width: 48px; height: 48px;">
                                                    <i class="fas fa-book-open text-muted"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <h6 class="mb-0">{{ Str::limit($course->title, 40) }}</h6>
                                                <small class="text-muted">{{ $course->subject->name ?? $course->subject->name_uz ?? 'N/A' }}</small>
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            @if($course->is_published)
                                                <span class="badge bg-success">Nashr</span>
                                            @else
                                                <span class="badge bg-warning">Qoralama</span>
                                            @endif
                                            <br>
                                            <small class="text-muted"><i class="fas fa-users me-1"></i>{{ $course->enrollment_count ?? 0 }}</small>
                                        </div>
                                    </div>
                                </a>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-chalkboard-teacher fa-3x text-muted mb-3"></i>
                                <p class="text-muted mb-2">Hali kurslar yaratilmagan</p>
                                <a href="{{ route('lms.courses.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-1"></i>Birinchi kurs yaratish
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Recent Grades -->
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white border-0 p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-star text-warning me-2"></i>So'nggi baholar
                            </h5>
                            <a href="{{ route('teacher.grades.index') }}" class="btn btn-sm btn-outline-warning">
                                <i class="fas fa-chart-bar me-1"></i>Barcha baholar
                            </a>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        @if($recentGrades->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover mb-0 align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Talaba</th>
                                            <th>Fan</th>
                                            <th>Guruh</th>
                                            <th style="width: 60px;">Ball</th>
                                            <th style="width: 90px;">Sana</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($recentGrades as $grade)
                                        <tr>
                                            <td>{{ $grade->student->user->name ?? $grade->student->full_name ?? 'N/A' }}</td>
                                            <td>{{ $grade->journalEntry->subject->name ?? $grade->journalEntry->subject->name_uz ?? 'N/A' }}</td>
                                            <td>
                                                <span class="badge bg-secondary">{{ $grade->journalEntry->group->name ?? 'N/A' }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $grade->score >= 86 ? 'success' : ($grade->score >= 71 ? 'primary' : ($grade->score >= 56 ? 'warning' : 'danger')) }}">
                                                    {{ number_format($grade->score, 0) }}
                                                </span>
                                            </td>
                                            <td>
                                                <small class="text-muted">{{ \Carbon\Carbon::parse($grade->graded_date)->format('d.m.Y') }}</small>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-star-half-alt fa-2x text-muted mb-2"></i>
                                <p class="text-muted mb-0">Hozircha baholar yo'q</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right Column - Sidebar -->
            <div class="col-lg-4">
                <!-- Quick Actions -->
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white border-0 p-3">
                        <h5 class="mb-0"><i class="fas fa-bolt text-warning me-2"></i>Tez harakatlar</h5>
                    </div>
                    <div class="card-body p-3">
                        <div class="d-grid gap-2">
                            <a href="{{ route('teacher.journal.index') }}" class="btn btn-primary">
                                <i class="fas fa-book me-2"></i>Jurnal
                            </a>
                            <a href="{{ route('teacher.attendance.index') }}" class="btn btn-outline-success">
                                <i class="fas fa-check-circle me-2"></i>Davomat kiritish
                            </a>
                            <a href="{{ route('teacher.grades.index') }}" class="btn btn-outline-warning">
                                <i class="fas fa-star me-2"></i>Baho qo'yish
                            </a>
                            <a href="{{ route('teacher.assignments.create') }}" class="btn btn-outline-info">
                                <i class="fas fa-tasks me-2"></i>Yangi topshiriq
                            </a>
                            <a href="{{ route('teacher.materials.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-folder-open me-2"></i>Materiallar
                            </a>
                            <a href="{{ route('lms.courses.create') }}" class="btn btn-purple">
                                <i class="fas fa-graduation-cap me-2"></i>Yangi LMS kurs
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Pending Submissions -->
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white border-0 p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-inbox text-danger me-2"></i>Tekshirish kerak
                            </h5>
                            <span class="badge bg-danger">{{ $pendingAssignments->count() }}</span>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        @if($pendingAssignments->count() > 0)
                            <div class="list-group list-group-flush">
                                @foreach($pendingAssignments->take(5) as $submission)
                                <a href="{{ route('teacher.assignments.grade', $submission->id) }}" class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1 text-truncate" style="max-width: 180px;">{{ $submission->student->user->name ?? $submission->student->full_name ?? 'N/A' }}</h6>
                                        <small class="text-muted">{{ \Carbon\Carbon::parse($submission->submitted_at)->diffForHumans() }}</small>
                                    </div>
                                    <p class="mb-1 small text-muted text-truncate">{{ $submission->assignment->title ?? 'N/A' }}</p>
                                    <small class="text-primary">
                                        <i class="fas fa-book me-1"></i>{{ $submission->assignment->subject->name ?? $submission->assignment->subject->name_uz ?? 'N/A' }}
                                    </small>
                                </a>
                                @endforeach
                            </div>
                            @if($pendingAssignments->count() > 5)
                            <div class="card-footer bg-light text-center p-2">
                                <a href="{{ route('teacher.assignments.pending') }}" class="btn btn-sm btn-link">
                                    Barchasini ko'rish ({{ $pendingAssignments->count() }})
                                </a>
                            </div>
                            @endif
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                                <p class="text-muted mb-0">Tekshirish kerak emas</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Upcoming Deadlines -->
                @if(isset($upcomingDeadlines) && count($upcomingDeadlines) > 0)
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white border-0 p-3">
                        <h5 class="mb-0">
                            <i class="fas fa-clock text-info me-2"></i>Yaqinlashayotgan muddatlar
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            @foreach($upcomingDeadlines as $deadline)
                            <a href="{{ route('teacher.assignments.show', $deadline['id']) }}" class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1 text-truncate" style="max-width: 200px;">{{ $deadline['title'] }}</h6>
                                        <small class="text-muted">{{ $deadline['subject'] }}</small>
                                    </div>
                                    <span class="badge bg-{{ $deadline['urgency'] }}">
                                        {{ $deadline['days_left'] }} kun
                                    </span>
                                </div>
                            </a>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

                <!-- Attendance Rate -->
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="mb-0"><i class="fas fa-chart-line me-2"></i>Davomat ko'rsatkichi</h6>
                            <span class="badge bg-{{ $attendance_rate >= 80 ? 'success' : ($attendance_rate >= 60 ? 'warning' : 'danger') }}">
                                {{ $attendance_rate }}%
                            </span>
                        </div>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar bg-{{ $attendance_rate >= 80 ? 'success' : ($attendance_rate >= 60 ? 'warning' : 'danger') }}"
                                 role="progressbar"
                                 style="width: {{ $attendance_rate }}%">
                            </div>
                        </div>
                        <small class="text-muted">So'nggi 30 kun davomida</small>
                    </div>
                </div>

                <!-- Workload -->
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body p-3">
                        <div class="row text-center">
                            <div class="col-6">
                                <h4 class="mb-0 text-primary">{{ $workload }}</h4>
                                <small class="text-muted">Soat/hafta</small>
                            </div>
                            <div class="col-6">
                                <h4 class="mb-0 text-success">{{ $journals_count ?? 0 }}</h4>
                                <small class="text-muted">Jurnallar</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<style>
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
.text-purple {
    color: #8b5cf6;
}
.btn-purple {
    background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
    border: none;
    color: #fff;
}
.btn-purple:hover {
    background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%);
    color: #fff;
}
.stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.stat-card:hover {
    transform: translateY(-2px);
    transition: transform 0.2s ease;
}
.avatar-circle {
    border-radius: 50%;
    overflow: hidden;
}

/* Accordion Styles - Fixed for Tailwind/Bootstrap conflict */
#groupsAccordion .accordion-item {
    border: none !important;
    border-bottom: 1px solid #e9ecef !important;
    background-color: #fff !important;
}
#groupsAccordion .accordion-button {
    padding: 12px 16px !important;
    font-size: 14px !important;
    background-color: #fff !important;
    color: #212529 !important;
}
#groupsAccordion .accordion-button:not(.collapsed) {
    background-color: rgba(13, 110, 253, 0.1) !important;
    color: #0d6efd !important;
    box-shadow: none !important;
}
#groupsAccordion .accordion-button:focus {
    box-shadow: none !important;
    border-color: rgba(0,0,0,.125) !important;
}
#groupsAccordion .accordion-button::after {
    flex-shrink: 0 !important;
    margin-left: 8px !important;
}
/* FIX: Override Tailwind's collapse class */
#groupsAccordion .accordion-collapse {
    visibility: visible !important;
}
#groupsAccordion .accordion-collapse:not(.show) {
    display: none !important;
}
#groupsAccordion .accordion-collapse.show {
    display: block !important;
}
#groupsAccordion .accordion-collapse.collapsing {
    display: block !important;
    height: 0;
    overflow: hidden;
    transition: height 0.35s ease !important;
}
#groupsAccordion .accordion-body {
    padding: 8px !important;
    background-color: #f8f9fa !important;
    color: #212529 !important;
}
#groupsAccordion .list-group-item {
    border: none !important;
    border-bottom: 1px solid #e9ecef !important;
    background: transparent !important;
    padding: 10px 8px !important;
    color: #212529 !important;
}
#groupsAccordion .list-group-item:last-child {
    border-bottom: none !important;
}
#groupsAccordion .badge {
    color: #fff !important;
}
#groupsAccordion .badge.bg-primary {
    background-color: #0d6efd !important;
}
#groupsAccordion .badge.bg-secondary {
    background-color: #6c757d !important;
}
#groupsAccordion .badge.bg-success {
    background-color: #198754 !important;
}
#groupsAccordion .text-muted {
    color: #6c757d !important;
}
#groupsAccordion .text-primary {
    color: #0d6efd !important;
}
#groupsAccordion .btn-outline-primary,
#groupsAccordion .btn-outline-warning,
#groupsAccordion .btn-outline-success {
    background-color: transparent !important;
}
</style>
@endsection
