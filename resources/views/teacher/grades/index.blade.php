@extends('layouts.dashboard-new')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="card border-0 shadow-sm mb-4 bg-gradient-primary text-white">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">
                        <i class="fas fa-star me-2"></i>
                        Baholar
                    </h4>
                    <p class="mb-0 opacity-75">Talabalarning baholarini boshqarish va ko'rish</p>
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
                        <div class="rounded-circle bg-warning bg-opacity-10 p-3 me-3">
                            <i class="fas fa-graduation-cap fa-lg text-warning"></i>
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
                                <small class="text-muted d-block">Baholar</small>
                                <strong class="text-success">{{ $data['total_grades'] }}</strong>
                            </div>
                        </div>
                    </div>

                    <!-- Room and Semester -->
                    <div class="mb-3">
                        <span class="badge bg-secondary me-2">
                            <i class="fas fa-door-open me-1"></i>{{ $data['room'] ?? 'N/A' }}
                        </span>
                        <span class="badge bg-info">
                            <i class="fas fa-calendar me-1"></i>{{ $data['semester'] }}-semestr
                        </span>
                    </div>

                    <!-- Average Score -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <small class="text-muted">O'rtacha ball</small>
                            <h4 class="mb-0">
                                <span class="badge bg-{{ $data['avg_score'] >= 86 ? 'success' : ($data['avg_score'] >= 71 ? 'primary' : ($data['avg_score'] >= 56 ? 'warning' : 'danger')) }}">
                                    {{ $data['avg_score'] > 0 ? number_format($data['avg_score'], 1) : '0.0' }}
                                </span>
                            </h4>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-{{ $data['avg_score'] >= 86 ? 'success' : ($data['avg_score'] >= 71 ? 'primary' : ($data['avg_score'] >= 56 ? 'warning' : 'danger')) }}"
                                 role="progressbar"
                                 style="width: {{ $data['avg_score'] }}%"
                                 aria-valuenow="{{ $data['avg_score'] }}"
                                 aria-valuemin="0"
                                 aria-valuemax="100">
                            </div>
                        </div>
                    </div>

                    <!-- Grades Distribution -->
                    <div class="mb-3 pb-3 border-bottom">
                        <small class="text-muted d-block mb-2">Nazorat turlari</small>
                        <div class="d-flex justify-content-between text-center">
                            <div>
                                <div class="badge bg-info mb-1">{{ $data['joriy_count'] }}</div>
                                <small class="d-block text-muted" style="font-size: 0.7rem;">Joriy</small>
                            </div>
                            <div>
                                <div class="badge bg-warning mb-1">{{ $data['oraliq_count'] }}</div>
                                <small class="d-block text-muted" style="font-size: 0.7rem;">Oraliq</small>
                            </div>
                            <div>
                                <div class="badge bg-danger mb-1">{{ $data['yakuniy_count'] }}</div>
                                <small class="d-block text-muted" style="font-size: 0.7rem;">Yakuniy</small>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="d-flex gap-2">
                        <a href="{{ route('teacher.grades.show', $data['id']) }}"
                           class="btn btn-primary btn-sm flex-grow-1">
                            <i class="fas fa-table me-1"></i>Baholar jadvali
                        </a>
                        <a href="{{ route('teacher.grades.create', $data['id']) }}"
                           class="btn btn-success btn-sm"
                           title="Baho qo'shish">
                            <i class="fas fa-plus"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="fas fa-star fa-3x text-muted mb-3"></i>
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
