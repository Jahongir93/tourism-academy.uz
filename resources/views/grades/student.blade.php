@extends('layouts.dashboard-new')

@section('title', 'Mening baholarim')
@section('page-title', 'Mening baholarim')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-1">Mening baholarim</h3>
            <p class="text-muted mb-0">{{ $student->full_name }} - Barcha baholar va statistika</p>
        </div>
        <a href="{{ route('student.dashboard') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Orqaga
        </a>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="card border-left-primary h-100">
                <div class="card-body">
                    <div class="text-center">
                        <div class="text-primary mb-2">
                            <i class="fas fa-chart-line fa-2x"></i>
                        </div>
                        <h3 class="mb-1">{{ $statistics['gpa'] }}</h3>
                        <small class="text-muted">GPA (4.0)</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="card border-left-info h-100">
                <div class="card-body">
                    <div class="text-center">
                        <div class="text-info mb-2">
                            <i class="fas fa-star fa-2x"></i>
                        </div>
                        <h3 class="mb-1">{{ $statistics['average_score'] }}</h3>
                        <small class="text-muted">O'rtacha ball</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="card border-left-success h-100">
                <div class="card-body">
                    <div class="text-center">
                        <div class="text-success mb-2">
                            <i class="fas fa-trophy fa-2x"></i>
                        </div>
                        <h3 class="mb-1">{{ $statistics['excellent_count'] }}</h3>
                        <small class="text-muted">A'lo (86-100)</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="card border-left-primary h-100">
                <div class="card-body">
                    <div class="text-center">
                        <div class="text-primary mb-2">
                            <i class="fas fa-thumbs-up fa-2x"></i>
                        </div>
                        <h3 class="mb-1">{{ $statistics['good_count'] }}</h3>
                        <small class="text-muted">Yaxshi (71-85)</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="card border-left-warning h-100">
                <div class="card-body">
                    <div class="text-center">
                        <div class="text-warning mb-2">
                            <i class="fas fa-check fa-2x"></i>
                        </div>
                        <h3 class="mb-1">{{ $statistics['satisfactory_count'] }}</h3>
                        <small class="text-muted">Qoniqarli (56-70)</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="card border-left-danger h-100">
                <div class="card-body">
                    <div class="text-center">
                        <div class="text-danger mb-2">
                            <i class="fas fa-times fa-2x"></i>
                        </div>
                        <h3 class="mb-1">{{ $statistics['unsatisfactory_count'] }}</h3>
                        <small class="text-muted">Qoniqarsiz (<56)</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Grades by Subject -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">
                <i class="fas fa-graduation-cap me-2"></i>Fanlar bo'yicha baholar
            </h5>
        </div>
        <div class="card-body">
            @forelse($gradesBySubject as $subjectData)
            <div class="subject-grade-card mb-4 p-3 border rounded">
                <div class="row align-items-center">
                    <div class="col-md-4">
                        <h5 class="mb-2">
                            <i class="fas fa-book text-primary me-2"></i>
                            {{ $subjectData['subject']->name ?? 'N/A' }}
                        </h5>
                        <small class="text-muted">
                            Jami baholar: {{ $subjectData['total_count'] }}
                        </small>
                    </div>
                    <div class="col-md-8">
                        <div class="row text-center">
                            <div class="col-3">
                                <div class="mb-1">
                                    <small class="text-muted d-block">Joriy nazorat</small>
                                    <h4 class="mb-0 {{ $subjectData['joriy'] >= 86 ? 'text-success' : ($subjectData['joriy'] >= 71 ? 'text-primary' : ($subjectData['joriy'] >= 56 ? 'text-warning' : 'text-danger')) }}">
                                        {{ $subjectData['joriy'] ? number_format($subjectData['joriy'], 1) : '-' }}
                                    </h4>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="mb-1">
                                    <small class="text-muted d-block">Oraliq nazorat</small>
                                    <h4 class="mb-0 {{ $subjectData['oraliq'] >= 86 ? 'text-success' : ($subjectData['oraliq'] >= 71 ? 'text-primary' : ($subjectData['oraliq'] >= 56 ? 'text-warning' : 'text-danger')) }}">
                                        {{ $subjectData['oraliq'] ? number_format($subjectData['oraliq'], 1) : '-' }}
                                    </h4>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="mb-1">
                                    <small class="text-muted d-block">Yakuniy nazorat</small>
                                    <h4 class="mb-0 {{ $subjectData['yakuniy'] >= 86 ? 'text-success' : ($subjectData['yakuniy'] >= 71 ? 'text-primary' : ($subjectData['yakuniy'] >= 56 ? 'text-warning' : 'text-danger')) }}">
                                        {{ $subjectData['yakuniy'] ? number_format($subjectData['yakuniy'], 1) : '-' }}
                                    </h4>
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="mb-1">
                                    <small class="text-muted d-block">O'rtacha</small>
                                    <h4 class="mb-0 fw-bold {{ $subjectData['average'] >= 86 ? 'text-success' : ($subjectData['average'] >= 71 ? 'text-primary' : ($subjectData['average'] >= 56 ? 'text-warning' : 'text-danger')) }}">
                                        {{ number_format($subjectData['average'], 1) }}
                                    </h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-5">
                <i class="fas fa-chart-bar fa-4x text-muted mb-3"></i>
                <h5 class="text-muted">Baholar mavjud emas</h5>
                <p class="text-muted">Hozircha sizga baho qo'yilmagan.</p>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Detailed Grades List -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-list me-2"></i>Barcha baholar (batafsil)
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Fan</th>
                            <th>Nazorat turi</th>
                            <th class="text-center">Ball</th>
                            <th class="text-center">Maksimal</th>
                            <th class="text-center">Foiz</th>
                            <th>Baholangan sana</th>
                            <th>Izoh</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($grades as $index => $grade)
                        <tr>
                            <td>{{ $grades->firstItem() + $index }}</td>
                            <td>
                                <strong>{{ $grade->journalEntry->subject->name ?? 'N/A' }}</strong>
                            </td>
                            <td>
                                <span class="badge bg-{{ $grade->grade_type == 'joriy' ? 'info' : ($grade->grade_type == 'oraliq' ? 'warning' : 'success') }}">
                                    @if($grade->grade_type == 'joriy')
                                        Joriy nazorat
                                    @elseif($grade->grade_type == 'oraliq')
                                        Oraliq nazorat
                                    @else
                                        Yakuniy nazorat
                                    @endif
                                </span>
                            </td>
                            <td class="text-center">
                                <h5 class="mb-0 {{ $grade->score >= 86 ? 'text-success' : ($grade->score >= 71 ? 'text-primary' : ($grade->score >= 56 ? 'text-warning' : 'text-danger')) }}">
                                    {{ number_format($grade->score, 1) }}
                                </h5>
                            </td>
                            <td class="text-center">
                                <span class="text-muted">{{ $grade->max_score }}</span>
                            </td>
                            <td class="text-center">
                                @php
                                    $percentage = ($grade->score / $grade->max_score) * 100;
                                @endphp
                                <div class="progress" style="height: 20px;">
                                    <div class="progress-bar {{ $percentage >= 86 ? 'bg-success' : ($percentage >= 71 ? 'bg-primary' : ($percentage >= 56 ? 'bg-warning' : 'bg-danger')) }}"
                                         role="progressbar"
                                         style="width: {{ $percentage }}%"
                                         aria-valuenow="{{ $percentage }}"
                                         aria-valuemin="0"
                                         aria-valuemax="100">
                                        {{ number_format($percentage, 0) }}%
                                    </div>
                                </div>
                            </td>
                            <td>
                                <small>{{ \Carbon\Carbon::parse($grade->graded_date)->translatedFormat('d F Y') }}</small>
                            </td>
                            <td>
                                @if($grade->notes)
                                    <small class="text-muted">{{ Str::limit($grade->notes, 50) }}</small>
                                @else
                                    <small class="text-muted">-</small>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                Baholar topilmadi
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($grades->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $grades->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<style>
.border-left-primary {
    border-left: 4px solid #4e73df !important;
}
.border-left-success {
    border-left: 4px solid #1cc88a !important;
}
.border-left-info {
    border-left: 4px solid #36b9cc !important;
}
.border-left-warning {
    border-left: 4px solid #f6c23e !important;
}
.border-left-danger {
    border-left: 4px solid #e74a3b !important;
}
.subject-grade-card {
    transition: all 0.3s ease;
    background: #f8f9fc;
}
.subject-grade-card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    transform: translateY(-2px);
}
</style>
@endsection
