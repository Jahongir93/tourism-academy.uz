@extends('layouts.dashboard-new')

@section('title', 'Mening fanlarim')
@section('page-title', 'Mening fanlarim')

@section('content')
<div class="container-fluid px-4 py-3">
    <!-- Header -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1"><i class="fas fa-book text-primary me-2"></i>Mening fanlarim</h4>
                            <p class="text-muted mb-0">{{ auth()->user()->name }} - {{ $teacher ? ($teacher->position ?? 'O\'qituvchi') : 'O\'qituvchi' }}</p>
                        </div>
                        <div>
                            <a href="{{ route('teacher.dashboard') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Orqaga
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics -->
    <div class="row mb-3">
        <div class="col-md-4 mb-2">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3">
                            <i class="fas fa-book-open fa-2x text-primary"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1 small">Jami fanlar</h6>
                            <h3 class="mb-0 fw-bold">{{ $totalSubjects }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-2">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-success bg-opacity-10 p-3 me-3">
                            <i class="fas fa-users fa-2x text-success"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1 small">Jami guruhlar</h6>
                            <h3 class="mb-0 fw-bold">{{ $totalGroups }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-2">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-info bg-opacity-10 p-3 me-3">
                            <i class="fas fa-user-graduate fa-2x text-info"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1 small">Jami talabalar</h6>
                            <h3 class="mb-0 fw-bold">{{ $totalStudents }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Subjects List -->
    <div class="row">
        @forelse($subjects as $subjectData)
            <div class="col-lg-6 mb-3">
                <div class="card border-0 shadow-sm h-100 subject-card">
                    <div class="card-body p-4">
                        <!-- Subject Header -->
                        <div class="d-flex align-items-start mb-3">
                            <div class="subject-icon me-3">
                                <i class="fas fa-graduation-cap fa-2x text-primary"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="mb-1 fw-bold">{{ $subjectData['subject']->name }}</h5>
                                <p class="text-muted mb-0">
                                    <small>
                                        <i class="fas fa-barcode me-1"></i>
                                        {{ $subjectData['subject']->code ?? 'N/A' }}
                                    </small>
                                </p>
                            </div>
                            <div>
                                <span class="badge bg-primary fs-6">
                                    {{ $subjectData['subject']->credits ?? 4 }} kredit
                                </span>
                            </div>
                        </div>

                        <!-- Statistics -->
                        <div class="row mb-3">
                            <div class="col-6">
                                <div class="stat-box">
                                    <i class="fas fa-users text-success me-2"></i>
                                    <strong>{{ $subjectData['groups_count'] }}</strong> guruh
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stat-box">
                                    <i class="fas fa-user-graduate text-info me-2"></i>
                                    <strong>{{ $subjectData['total_students'] }}</strong> talaba
                                </div>
                            </div>
                        </div>

                        <!-- Groups List -->
                        <div class="groups-list">
                            <h6 class="mb-2 text-muted small">Guruhlar:</h6>
                            @foreach($subjectData['groups'] as $groupData)
                                <div class="group-item mb-2">
                                    <div class="d-flex justify-content-between align-items-center p-2 rounded bg-light">
                                        <div class="d-flex align-items-center">
                                            <span class="badge bg-primary me-2">{{ $groupData['group']->name }}</span>
                                            <small class="text-muted">
                                                <i class="fas fa-door-open me-1"></i>{{ $groupData['room'] ?? 'N/A' }}
                                            </small>
                                            <small class="text-muted ms-2">
                                                <i class="fas fa-calendar me-1"></i>{{ $groupData['semester'] }}-sem
                                            </small>
                                        </div>
                                        <div>
                                            <small class="text-muted me-2">
                                                <i class="fas fa-users"></i> {{ $groupData['students_count'] }}
                                            </small>
                                            <a href="{{ route('teacher.subjects.show', $groupData['id']) }}"
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Actions -->
                        <div class="d-flex gap-2 mt-3">
                            <a href="{{ route('teacher.topics.subject', $subjectData['subject']->id) }}" class="btn btn-sm btn-outline-primary flex-fill">
                                <i class="fas fa-list-ol me-1"></i>Mavzular
                            </a>
                            <a href="{{ route('teacher.journal.index', ['subject_id' => $subjectData['subject']->id]) }}" class="btn btn-sm btn-outline-success flex-fill">
                                <i class="fas fa-book-open me-1"></i>Jurnal
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-book-open fa-4x text-muted mb-3"></i>
                        <h5 class="text-muted">Fanlar topilmadi</h5>
                        <p class="text-muted mb-0">Sizga hali fanlar biriktirilmagan. Administrator bilan bog'laning.</p>
                    </div>
                </div>
            </div>
        @endforelse
    </div>
</div>

<style>
.subject-card {
    transition: transform 0.2s, box-shadow 0.2s;
}

.subject-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.5rem 1.5rem rgba(0,0,0,0.15) !important;
}

.subject-icon {
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 15px;
    color: white;
}

.stat-box {
    padding: 8px;
    background-color: #f8f9fa;
    border-radius: 8px;
    text-align: center;
}

.groups-list {
    max-height: 250px;
    overflow-y: auto;
}

.groups-list::-webkit-scrollbar {
    width: 6px;
}

.groups-list::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.groups-list::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 10px;
}

.groups-list::-webkit-scrollbar-thumb:hover {
    background: #555;
}

.group-item {
    transition: all 0.2s;
}

.group-item:hover {
    transform: translateX(5px);
}
</style>
@endsection
