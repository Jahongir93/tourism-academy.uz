@extends('layouts.dashboard-new')

@section('title', 'Guruhlar')
@section('page-title', 'Guruhlar ro\'yxati')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm bg-gradient-success text-white">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1"><i class="fas fa-users me-2"></i>Guruhlar ro'yxati</h4>
                            <p class="mb-0 opacity-75">{{ $faculty?->name ?? 'Fakultet' }} guruhlari</p>
                        </div>
                        <a href="{{ route('dean.dashboard') }}" class="btn btn-light">
                            <i class="fas fa-arrow-left me-1"></i> Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtr -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('dean.groups.index') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Kurs</label>
                    <select name="course" class="form-select">
                        <option value="">Barchasi</option>
                        @for($i = 1; $i <= 4; $i++)
                        <option value="{{ $i }}" {{ request('course') == $i ? 'selected' : '' }}>{{ $i }}-kurs</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-filter me-1"></i> Filtrlash
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Guruhlar -->
    <div class="row">
        @forelse($groups as $group)
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-success bg-opacity-10 rounded-circle p-3 me-3">
                            <i class="fas fa-users fa-lg text-success"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-bold">{{ $group->name }}</h5>
                            <small class="text-muted">{{ $group->specialty?->name_uz ?? 'Yo\'nalish' }}</small>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Talabalar:</span>
                        <span class="fw-bold">{{ $group->students_count }} ta</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Kurs:</span>
                        <span class="badge bg-primary">{{ $group->course }}-kurs</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Kurator:</span>
                        <span class="fw-medium">{{ $group->curator?->last_name ?? 'Belgilanmagan' }}</span>
                    </div>
                </div>
                <div class="card-footer bg-white border-0">
                    <a href="{{ route('dean.students.index', ['group_id' => $group->id]) }}" class="btn btn-outline-success btn-sm w-100">
                        <i class="fas fa-eye me-1"></i> Talabalarni ko'rish
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="fas fa-users fa-4x text-muted mb-4 opacity-50"></i>
                    <h4 class="text-muted">Guruhlar topilmadi</h4>
                </div>
            </div>
        </div>
        @endforelse
    </div>

    @if($groups->hasPages())
    <div class="d-flex justify-content-center">
        {{ $groups->links() }}
    </div>
    @endif
</div>

<style>
.bg-gradient-success { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); }
</style>
@endsection
