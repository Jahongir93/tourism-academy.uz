@extends('layouts.dashboard-new')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="card border-0 shadow-sm mb-4 bg-gradient-primary text-white">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">
                        <i class="fas fa-clipboard-check me-2"></i>
                        Davomat va jurnal
                    </h4>
                    <p class="mb-0 opacity-75">O'z fanlaringiz bo'yicha davomat va baholar jurnali</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Groups List -->
    <div class="row">
        @forelse($groupsData as $data)
        <div class="col-lg-6 col-xl-4 mb-4">
            <div class="card border-0 shadow-sm h-100 hover-lift">
                <div class="card-body">
                    <!-- Subject Name -->
                    <div class="d-flex align-items-start mb-3">
                        <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3">
                            <i class="fas fa-book fa-lg text-primary"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h5 class="mb-1">{{ $data['subject']->name }}</h5>
                            <p class="text-muted mb-0 small">
                                <i class="fas fa-users me-1"></i>
                                {{ $data['group']->name }}
                            </p>
                        </div>
                    </div>

                    <!-- Info -->
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <div class="bg-light rounded p-2 text-center">
                                <small class="text-muted d-block">Talabalar</small>
                                <strong class="text-primary">{{ $data['total_students'] }}</strong>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="bg-light rounded p-2 text-center">
                                <small class="text-muted d-block">Darslar</small>
                                <strong class="text-success">{{ $data['total_entries'] }}</strong>
                            </div>
                        </div>
                    </div>

                    <!-- Room and Semester -->
                    <div class="mb-3">
                        <span class="badge bg-secondary me-2">
                            <i class="fas fa-door-open me-1"></i>{{ $data['room'] ?? 'Xona ko\'rsatilmagan' }}
                        </span>
                        <span class="badge bg-info">
                            <i class="fas fa-calendar me-1"></i>{{ $data['semester'] }}-semestr
                        </span>
                    </div>

                    <!-- Attendance Rate -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <small class="text-muted">Davomat ko'rsatkichi</small>
                            <small class="fw-bold">{{ $data['attendance_rate'] }}%</small>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-{{ $data['attendance_rate'] >= 80 ? 'success' : ($data['attendance_rate'] >= 60 ? 'warning' : 'danger') }}"
                                 role="progressbar"
                                 style="width: {{ $data['attendance_rate'] }}%"
                                 aria-valuenow="{{ $data['attendance_rate'] }}"
                                 aria-valuemin="0"
                                 aria-valuemax="100">
                            </div>
                        </div>
                    </div>

                    <!-- Last Entry Date -->
                    @if($data['last_entry_date'])
                    <p class="text-muted small mb-3">
                        <i class="fas fa-clock me-1"></i>
                        So'ngi dars: {{ $data['last_entry_date']->format('d.m.Y H:i') }}
                    </p>
                    @else
                    <p class="text-muted small mb-3">
                        <i class="fas fa-info-circle me-1"></i>
                        Hali dars o'tilmagan
                    </p>
                    @endif

                    <!-- Actions -->
                    <div class="d-flex gap-2">
                        <a href="{{ route('teacher.attendance.journal', $data['id']) }}"
                           class="btn btn-outline-primary btn-sm flex-grow-1">
                            <i class="fas fa-book me-1"></i>Jurnal
                        </a>
                        <a href="{{ route('teacher.attendance.create', $data['id']) }}"
                           class="btn btn-primary btn-sm flex-grow-1">
                            <i class="fas fa-plus me-1"></i>Davomat qilish
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="fas fa-clipboard fa-3x text-muted mb-3"></i>
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
</style>
@endsection
