@extends('layouts.dashboard-new')

@section('title', 'Topshiriqlarim')
@section('page-title', 'Topshiriqlarim')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-1">Mening topshiriqlarim</h3>
            <p class="text-muted mb-0">Barcha topshiriqlar va ularning holati</p>
        </div>
        <a href="{{ route('student.dashboard') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Orqaga
        </a>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <select class="form-select" id="statusFilter">
                        <option value="">Barcha holat</option>
                        <option value="pending">Topshirilmagan</option>
                        <option value="submitted">Topshirilgan</option>
                        <option value="graded">Baholangan</option>
                        <option value="late">Kech topshirilgan</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" id="typeFilter">
                        <option value="">Barcha turlar</option>
                        <option value="lab">Laboratoriya</option>
                        <option value="homework">Uy vazifasi</option>
                        <option value="course_work">Kurs ishi</option>
                        <option value="independent">Mustaqil ish</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <input type="text" class="form-control" id="searchInput" placeholder="Topshiriq nomini qidirish...">
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-left-primary">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-tasks fa-2x text-primary"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="small text-muted">Jami topshiriqlar</div>
                            <h4 class="mb-0">{{ $assignments->total() }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-warning">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-clock fa-2x text-warning"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="small text-muted">Topshirilmagan</div>
                            <h4 class="mb-0">{{ $assignments->where('is_submitted', false)->where('is_overdue', false)->count() }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-success">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-check-circle fa-2x text-success"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="small text-muted">Topshirilgan</div>
                            <h4 class="mb-0">{{ $assignments->where('is_submitted', true)->count() }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-danger">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-triangle fa-2x text-danger"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="small text-muted">Muddati o'tgan</div>
                            <h4 class="mb-0">{{ $assignments->where('is_overdue', true)->count() }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Assignments List -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Topshiriqlar ro'yxati</h5>
        </div>
        <div class="card-body">
            @forelse($assignments as $assignment)
            <div class="assignment-item border rounded p-3 mb-3 @if($assignment->is_overdue) border-danger @elseif($assignment->is_submitted) border-success @else border-warning @endif">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h5 class="mb-2">
                            <i class="fas fa-file-alt text-primary me-2"></i>
                            {{ $assignment->title }}
                        </h5>
                        <div class="mb-2">
                            <span class="badge bg-light text-dark me-2">
                                <i class="fas fa-book"></i> {{ $assignment->subject->name ?? 'N/A' }}
                            </span>
                            <span class="badge bg-info">
                                @switch($assignment->type)
                                    @case('lab') Laboratoriya @break
                                    @case('homework') Uy vazifasi @break
                                    @case('course_work') Kurs ishi @break
                                    @case('independent') Mustaqil ish @break
                                    @default {{ $assignment->type }}
                                @endswitch
                            </span>
                        </div>
                        <small class="text-muted">
                            <i class="fas fa-user-tie"></i> {{ $assignment->teacher->user->name ?? 'N/A' }}
                        </small>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-2">
                            <small class="text-muted d-block">Muddat:</small>
                            <strong class="@if($assignment->is_overdue) text-danger @endif">
                                <i class="fas fa-calendar"></i>
                                {{ \Carbon\Carbon::parse($assignment->deadline)->translatedFormat('d F Y, H:i') }}
                            </strong>
                        </div>
                        @if($assignment->days_until >= 0)
                        <small class="text-muted">
                            <i class="fas fa-hourglass-half"></i>
                            {{ $assignment->days_until == 0 ? 'Bugun' : $assignment->days_until . ' kun qoldi' }}
                        </small>
                        @else
                        <small class="text-danger">
                            <i class="fas fa-exclamation-circle"></i>
                            {{ abs($assignment->days_until) }} kun kechikkan
                        </small>
                        @endif
                    </div>
                    <div class="col-md-3 text-end">
                        @if($assignment->is_submitted)
                            @if($assignment->submission->status == 'graded')
                            <div class="mb-2">
                                <span class="badge bg-success">
                                    <i class="fas fa-star"></i> {{ $assignment->submission->score }}/{{ $assignment->max_score }}
                                </span>
                            </div>
                            @else
                            <div class="mb-2">
                                <span class="badge bg-info">
                                    <i class="fas fa-check"></i> Topshirilgan
                                </span>
                            </div>
                            @endif
                            <a href="{{ route('student.assignments.show', $assignment->id) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-eye"></i> Ko'rish
                            </a>
                        @else
                            @if($assignment->is_overdue)
                            <div class="mb-2">
                                <span class="badge bg-danger">
                                    <i class="fas fa-times"></i> Muddati o'tgan
                                </span>
                            </div>
                            @else
                            <div class="mb-2">
                                <span class="badge bg-warning text-dark">
                                    <i class="fas fa-clock"></i> Kutilmoqda
                                </span>
                            </div>
                            @endif
                            <a href="{{ route('student.assignments.show', $assignment->id) }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-upload"></i> Topshirish
                            </a>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-5">
                <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                <h5 class="text-muted">Topshiriqlar mavjud emas</h5>
                <p class="text-muted">Hozircha sizga hech qanday topshiriq berilmagan.</p>
            </div>
            @endforelse

            <!-- Pagination -->
            @if($assignments->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $assignments->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<style>
.border-left-primary {
    border-left: 4px solid #4e73df !important;
}
.border-left-warning {
    border-left: 4px solid #f6c23e !important;
}
.border-left-success {
    border-left: 4px solid #1cc88a !important;
}
.border-left-danger {
    border-left: 4px solid #e74a3b !important;
}
.assignment-item {
    transition: all 0.3s ease;
}
.assignment-item:hover {
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}
</style>

@endsection
