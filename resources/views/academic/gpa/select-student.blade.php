@extends('layouts.dashboard-new')

@section('title', 'Talaba tanlash - GPA Kalkulyator')
@section('page-title', 'GPA Kalkulyator')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm bg-gradient-primary text-white">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1"><i class="fas fa-calculator me-2"></i>GPA Kalkulyator</h4>
                            <p class="mb-0 opacity-75">Talaba GPA sini ko'rish uchun talaba tanlang</p>
                        </div>
                        <div class="text-end">
                            <h2 class="mb-0">{{ $students->total() }}</h2>
                            <small class="opacity-75">Jami talabalar</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('gpa.index') }}">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Qidiruv</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input type="text" name="name" class="form-control"
                                   placeholder="Ism, familiya yoki ID"
                                   value="{{ request('name') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Fakultet</label>
                        <select name="faculty_id" class="form-select">
                            <option value="">Barcha fakultetlar</option>
                            @foreach($faculties ?? [] as $faculty)
                            <option value="{{ $faculty->id }}" {{ request('faculty_id') == $faculty->id ? 'selected' : '' }}>
                                {{ $faculty->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Guruh</label>
                        <select name="group_id" class="form-select">
                            <option value="">Barcha guruhlar</option>
                            @foreach($groups ?? [] as $group)
                            <option value="{{ $group->id }}" {{ request('group_id') == $group->id ? 'selected' : '' }}>
                                {{ $group->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-grow-1">
                                <i class="fas fa-search"></i>
                            </button>
                            <a href="{{ route('gpa.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-undo"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Students List -->
    <div class="row g-3">
        @forelse($students as $student)
        <div class="col-md-6 col-lg-4 col-xl-3">
            <div class="card h-100 border-0 shadow-sm student-card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        @if($student->photo)
                        <img src="{{ asset('storage/' . $student->photo) }}"
                             class="rounded-circle"
                             style="width: 50px; height: 50px; object-fit: cover;"
                             alt="{{ $student->full_name ?? $student->user->name ?? 'Student' }}">
                        @else
                        <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center"
                             style="width: 50px; height: 50px;">
                            <i class="fas fa-user-graduate fs-5 text-primary"></i>
                        </div>
                        @endif
                        <div class="ms-3 flex-grow-1">
                            <h6 class="mb-0 text-truncate" style="max-width: 150px;">
                                {{ $student->full_name ?? $student->user->name ?? 'Noma\'lum' }}
                            </h6>
                            <small class="text-muted">{{ $student->student_id_number ?? 'ID: ' . $student->id }}</small>
                        </div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <small class="text-muted d-block">Guruh</small>
                            <span class="badge bg-secondary">{{ $student->group->name ?? 'N/A' }}</span>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block">Kurs</small>
                            <span class="badge bg-info">{{ $student->course ?? $student->group->course ?? '-' }}-kurs</span>
                        </div>
                    </div>

                    @if($student->faculty)
                    <div class="mb-3">
                        <small class="text-muted d-block">Fakultet</small>
                        <small class="text-truncate d-block" style="max-width: 200px;">
                            {{ $student->faculty->name }}
                        </small>
                    </div>
                    @endif

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <small class="text-muted">GPA</small>
                            @php
                                $gpa = $student->cumulative_gpa ?? $student->gpa ?? 0;
                                $gpaClass = $gpa >= 3.5 ? 'bg-success' : ($gpa >= 3.0 ? 'bg-primary' : ($gpa >= 2.0 ? 'bg-warning' : 'bg-danger'));
                            @endphp
                            <h4 class="mb-0">
                                <span class="badge {{ $gpaClass }}">
                                    {{ number_format($gpa, 2) }}
                                </span>
                            </h4>
                        </div>
                        <div class="text-end">
                            @if($gpa >= 3.5)
                            <span class="badge bg-success-subtle text-success">A'lo</span>
                            @elseif($gpa >= 3.0)
                            <span class="badge bg-primary-subtle text-primary">Yaxshi</span>
                            @elseif($gpa >= 2.0)
                            <span class="badge bg-warning-subtle text-warning">Qoniqarli</span>
                            @else
                            <span class="badge bg-danger-subtle text-danger">Past</span>
                            @endif
                        </div>
                    </div>

                    <a href="{{ route('gpa.index', ['student_id' => $student->id]) }}" class="btn btn-primary btn-sm w-100">
                        <i class="fas fa-chart-line me-1"></i> GPA ko'rish
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="fas fa-user-graduate fs-1 text-muted mb-3"></i>
                    <h5 class="text-muted">Talabalar topilmadi</h5>
                    <p class="text-muted mb-3">Qidiruv yoki filtr parametrlarini o'zgartiring</p>
                    <a href="{{ route('gpa.index') }}" class="btn btn-outline-primary">
                        <i class="fas fa-undo me-1"></i> Filtrlarni tozalash
                    </a>
                </div>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($students->hasPages())
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-muted">
                            Ko'rsatilmoqda: {{ $students->firstItem() }}-{{ $students->lastItem() }} / {{ $students->total() }}
                        </div>
                        <div>
                            {{ $students->links() }}
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
.student-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.student-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15) !important;
}
.bg-success-subtle {
    background-color: rgba(25, 135, 84, 0.1);
}
.bg-primary-subtle {
    background-color: rgba(13, 110, 253, 0.1);
}
.bg-warning-subtle {
    background-color: rgba(255, 193, 7, 0.1);
}
.bg-danger-subtle {
    background-color: rgba(220, 53, 69, 0.1);
}
</style>
@endsection
