@extends('layouts.dashboard-new')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="card border-0 shadow-sm mb-4 bg-gradient-primary text-white">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center">
                <div class="flex-grow-1">
                    <h4 class="mb-1">{{ $assignment->title }}</h4>
                    <p class="mb-0 opacity-75">
                        <i class="fas fa-book me-2"></i>{{ $assignment->subject->name }}
                        <span class="ms-3"><i class="fas fa-calendar me-1"></i>{{ \Carbon\Carbon::parse($assignment->deadline)->format('d.m.Y H:i') }}</span>
                    </p>
                </div>
                <div>
                    <a href="{{ route('teacher.assignments.index') }}" class="btn btn-light btn-sm me-2">
                        <i class="fas fa-arrow-left me-1"></i>Orqaga
                    </a>
                    <a href="{{ route('teacher.assignments.edit', $assignment->id) }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-edit me-1"></i>Tahrirlash
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

    <div class="row">
        <!-- Left Column: Assignment Details -->
        <div class="col-lg-4 mb-4">
            <!-- Assignment Info -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Topshiriq ma'lumotlari
                    </h6>
                </div>
                <div class="card-body">
                    <!-- Description -->
                    <div class="mb-3 pb-3 border-bottom">
                        <small class="text-muted d-block mb-2">Tavsif</small>
                        <p class="mb-0">{{ $assignment->description }}</p>
                    </div>

                    <!-- File -->
                    @if($assignment->file_path)
                    <div class="mb-3 pb-3 border-bottom">
                        <small class="text-muted d-block mb-2">Biriktirma</small>
                        <a href="{{ Storage::url($assignment->file_path) }}"
                           target="_blank"
                           class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-download me-1"></i>Faylni yuklash
                        </a>
                    </div>
                    @endif

                    <!-- Max Score -->
                    <div class="mb-3 pb-3 border-bottom">
                        <small class="text-muted d-block mb-1">Maksimal ball</small>
                        <h4 class="mb-0 text-warning">{{ $assignment->max_score }}</h4>
                    </div>

                    <!-- Deadline -->
                    <div class="mb-0">
                        <small class="text-muted d-block mb-1">Muddat</small>
                        <h6 class="mb-0">
                            <i class="fas fa-calendar-alt text-danger me-1"></i>
                            {{ \Carbon\Carbon::parse($assignment->deadline)->format('d.m.Y H:i') }}
                        </h6>
                        @if($assignment->deadline < now())
                        <span class="badge bg-danger mt-2">Muddati o'tgan</span>
                        @else
                        <span class="badge bg-success mt-2">Faol</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Statistics -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0">
                        <i class="fas fa-chart-bar me-2"></i>
                        Statistika
                    </h6>
                </div>
                <div class="card-body">
                    <!-- Total Students -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <small class="text-muted d-block">Jami talabalar</small>
                            <h4 class="mb-0">{{ $statistics['total_students'] }}</h4>
                        </div>
                        <div class="rounded-circle bg-primary bg-opacity-10 p-3">
                            <i class="fas fa-users fa-lg text-primary"></i>
                        </div>
                    </div>

                    <!-- Submitted -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <small class="text-muted d-block">Topshirdi</small>
                            <h4 class="mb-0 text-success">{{ $statistics['submitted'] }}</h4>
                        </div>
                        <div class="rounded-circle bg-success bg-opacity-10 p-3">
                            <i class="fas fa-check-circle fa-lg text-success"></i>
                        </div>
                    </div>

                    <!-- Not Submitted -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <small class="text-muted d-block">Topshirmadi</small>
                            <h4 class="mb-0 text-danger">{{ $statistics['not_submitted'] }}</h4>
                        </div>
                        <div class="rounded-circle bg-danger bg-opacity-10 p-3">
                            <i class="fas fa-times-circle fa-lg text-danger"></i>
                        </div>
                    </div>

                    <!-- Pending -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <small class="text-muted d-block">Tekshirish kerak</small>
                            <h4 class="mb-0 text-warning">{{ $statistics['pending'] }}</h4>
                        </div>
                        <div class="rounded-circle bg-warning bg-opacity-10 p-3">
                            <i class="fas fa-clock fa-lg text-warning"></i>
                        </div>
                    </div>

                    <!-- Average Score -->
                    @if($statistics['avg_score'])
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted d-block">O'rtacha ball</small>
                            <h4 class="mb-0 text-info">{{ number_format($statistics['avg_score'], 1) }}</h4>
                        </div>
                        <div class="rounded-circle bg-info bg-opacity-10 p-3">
                            <i class="fas fa-star fa-lg text-info"></i>
                        </div>
                    </div>
                    @endif

                    <!-- Submission Rate -->
                    <div class="mt-3 pt-3 border-top">
                        <small class="text-muted d-block mb-2">Topshirish foizi</small>
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar bg-{{ $statistics['submission_rate'] >= 80 ? 'success' : ($statistics['submission_rate'] >= 50 ? 'warning' : 'danger') }}"
                                 style="width: {{ $statistics['submission_rate'] }}%">
                                <strong>{{ $statistics['submission_rate'] }}%</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Submissions -->
        <div class="col-lg-8 mb-4">
            <!-- Tabs -->
            <ul class="nav nav-tabs mb-3" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#submitted" type="button">
                        <i class="fas fa-check-circle me-1"></i>
                        Topshirilgan ({{ $submissions->count() }})
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#notSubmitted" type="button">
                        <i class="fas fa-times-circle me-1"></i>
                        Topshirmagan ({{ $notSubmittedStudents->count() }})
                    </button>
                </li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content">
                <!-- Submitted Tab -->
                <div class="tab-pane fade show active" id="submitted">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-0">
                            @if($submissions->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="px-4">#</th>
                                            <th>Talaba</th>
                                            <th>Guruh</th>
                                            <th>Topshirgan vaqti</th>
                                            <th class="text-center">Ball</th>
                                            <th class="text-center px-4">Harakat</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($submissions as $index => $submission)
                                        <tr>
                                            <td class="px-4">{{ $index + 1 }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-circle me-2" style="width: 36px; height: 36px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 0.8rem;">
                                                        {{ strtoupper(substr($submission->student->user->name, 0, 2)) }}
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold">{{ $submission->student->user->name }}</div>
                                                        <small class="text-muted">{{ $submission->student->student_id }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">{{ $submission->student->group->name }}</span>
                                            </td>
                                            <td>
                                                <div>{{ $submission->submitted_at ? $submission->submitted_at->format('d.m.Y') : '-' }}</div>
                                                <small class="text-muted">{{ $submission->submitted_at ? $submission->submitted_at->format('H:i') : '' }}</small>
                                            </td>
                                            <td class="text-center">
                                                @if($submission->score !== null)
                                                <span class="badge bg-{{ $submission->score >= 86 ? 'success' : ($submission->score >= 71 ? 'primary' : ($submission->score >= 56 ? 'warning' : 'danger')) }} fs-6">
                                                    {{ number_format($submission->score, 1) }}
                                                </span>
                                                @else
                                                <span class="badge bg-warning">Tekshirilmagan</span>
                                                @endif
                                            </td>
                                            <td class="text-center px-4">
                                                <a href="{{ route('teacher.assignments.grade', $submission->id) }}"
                                                   class="btn btn-sm btn-{{ $submission->score !== null ? 'outline-primary' : 'primary' }}">
                                                    <i class="fas fa-{{ $submission->score !== null ? 'edit' : 'star' }}"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @else
                            <div class="text-center py-5">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">Hali topshirilmagan</h5>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Not Submitted Tab -->
                <div class="tab-pane fade" id="notSubmitted">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-0">
                            @if($notSubmittedStudents->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="px-4">#</th>
                                            <th>Talaba</th>
                                            <th>Guruh</th>
                                            <th>Email</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($notSubmittedStudents as $index => $student)
                                        <tr>
                                            <td class="px-4">{{ $index + 1 }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-circle me-2" style="width: 36px; height: 36px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 0.8rem;">
                                                        {{ strtoupper(substr($student->user->name, 0, 2)) }}
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold">{{ $student->user->name }}</div>
                                                        <small class="text-muted">{{ $student->student_id }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">{{ $student->group->name }}</span>
                                            </td>
                                            <td>{{ $student->user->email }}</td>
                                            <td>
                                                <span class="badge bg-danger">
                                                    <i class="fas fa-times me-1"></i>Topshirilmagan
                                                </span>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @else
                            <div class="text-center py-5">
                                <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                                <h5 class="text-success">Barcha talabalar topshirdi!</h5>
                            </div>
                            @endif
                        </div>
                    </div>
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
