@extends('layouts.dashboard-new')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="card border-0 shadow-sm mb-4 bg-gradient-primary text-white">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">
                        <i class="fas fa-tasks me-2"></i>
                        Topshiriqlar
                    </h4>
                    <p class="mb-0 opacity-75">Talabalar uchun topshiriqlarni boshqarish</p>
                </div>
                <div>
                    <a href="{{ route('teacher.assignments.pending') }}" class="btn btn-warning btn-sm me-2">
                        <i class="fas fa-clock me-1"></i>Tekshirish kerak
                    </a>
                    <a href="{{ route('teacher.assignments.create') }}" class="btn btn-light btn-sm">
                        <i class="fas fa-plus me-1"></i>Yangi topshiriq
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

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('teacher.assignments.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label small text-muted">Holati</label>
                    <select name="status" class="form-select">
                        <option value="">Barchasi</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Faol</option>
                        <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Muddati o'tgan</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small text-muted">Fan</label>
                    <select name="subject_id" class="form-select">
                        <option value="">Barcha fanlar</option>
                        @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                            {{ $subject->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-filter me-1"></i>Filter
                    </button>
                    <a href="{{ route('teacher.assignments.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-redo me-1"></i>Tozalash
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Assignments List -->
    @if($assignmentsData->count() > 0)
    <div class="row">
        @foreach($assignmentsData as $data)
        <div class="col-lg-6 col-xl-4 mb-4">
            <div class="card border-0 shadow-sm h-100 hover-lift">
                <div class="card-body">
                    <!-- Status Badge -->
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <span class="badge {{ $data['is_expired'] ? 'bg-danger' : 'bg-success' }}">
                            {{ $data['is_expired'] ? 'Muddati o\'tgan' : 'Faol' }}
                        </span>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-light" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item" href="{{ route('teacher.assignments.show', $data['assignment']->id) }}">
                                        <i class="fas fa-eye me-2"></i>Ko'rish
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('teacher.assignments.edit', $data['assignment']->id) }}">
                                        <i class="fas fa-edit me-2"></i>Tahrirlash
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item text-danger" href="#" onclick="deleteAssignment({{ $data['assignment']->id }})">
                                        <i class="fas fa-trash me-2"></i>O'chirish
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Title -->
                    <h5 class="mb-2">{{ $data['assignment']->title }}</h5>

                    <!-- Subject -->
                    <p class="text-muted small mb-3">
                        <i class="fas fa-book me-1"></i>
                        {{ $data['assignment']->subject->name }}
                    </p>

                    <!-- Description -->
                    <p class="text-muted small mb-3" style="max-height: 60px; overflow: hidden; text-overflow: ellipsis;">
                        {{ Str::limit($data['assignment']->description, 100) }}
                    </p>

                    <!-- Deadline -->
                    <div class="mb-3 pb-3 border-bottom">
                        <small class="text-muted d-block mb-1">Muddat</small>
                        <div class="d-flex align-items-center">
                            <i class="fas fa-calendar-alt text-primary me-2"></i>
                            <span class="fw-bold">{{ \Carbon\Carbon::parse($data['assignment']->deadline)->format('d.m.Y H:i') }}</span>
                        </div>
                    </div>

                    <!-- Statistics -->
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <div class="bg-light rounded p-2 text-center">
                                <small class="text-muted d-block">Talabalar</small>
                                <strong class="text-primary">{{ $data['total_students'] }}</strong>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="bg-light rounded p-2 text-center">
                                <small class="text-muted d-block">Topshirildi</small>
                                <strong class="text-success">{{ $data['total_submissions'] }}</strong>
                            </div>
                        </div>
                    </div>

                    <!-- Submission Progress -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <small class="text-muted">Topshirish foizi</small>
                            <small class="fw-bold">{{ $data['submission_rate'] }}%</small>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-{{ $data['submission_rate'] >= 80 ? 'success' : ($data['submission_rate'] >= 50 ? 'warning' : 'danger') }}"
                                 style="width: {{ $data['submission_rate'] }}%">
                            </div>
                        </div>
                    </div>

                    <!-- Grading Status -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <small class="text-muted">Tekshirish kerak:</small>
                        <span class="badge bg-warning">{{ $data['pending_submissions'] }}</span>
                    </div>

                    <!-- Actions -->
                    <a href="{{ route('teacher.assignments.show', $data['assignment']->id) }}"
                       class="btn btn-primary btn-sm w-100">
                        <i class="fas fa-eye me-1"></i>Batafsil ko'rish
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center">
        {{ $assignments->links() }}
    </div>
    @else
    <div class="card border-0 shadow-sm">
        <div class="card-body text-center py-5">
            <i class="fas fa-tasks fa-3x text-muted mb-3"></i>
            <h5 class="text-muted">Topshiriqlar yo'q</h5>
            <p class="text-muted mb-3">Yangi topshiriq yaratish uchun tugmani bosing</p>
            <a href="{{ route('teacher.assignments.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i>Yangi topshiriq
            </a>
        </div>
    </div>
    @endif
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

<script>
function deleteAssignment(id) {
    if (confirm('Bu topshiriqni o\'chirishni xohlaysizmi?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/teacher/assignments/${id}`;

        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        form.innerHTML = `
            <input type="hidden" name="_token" value="${csrfToken}">
            <input type="hidden" name="_method" value="DELETE">
        `;

        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endsection
