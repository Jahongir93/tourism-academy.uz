@extends('layouts.dashboard-new')

@section('title', 'Kafedralar')
@section('page-title', 'Kafedralar ro\'yxati')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm bg-gradient-warning text-white">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1"><i class="fas fa-building me-2"></i>Kafedralar</h4>
                            <p class="mb-0 opacity-75">{{ $faculty?->name ?? 'Fakultet' }} kafedralari</p>
                        </div>
                        <a href="{{ route('dean.dashboard') }}" class="btn btn-light">
                            <i class="fas fa-arrow-left me-1"></i> Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Kafedralar -->
    <div class="row">
        @forelse($departments as $dept)
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-warning bg-opacity-10 rounded-circle p-3 me-3">
                            <i class="fas fa-building fa-lg text-warning"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-bold">{{ $dept->name }}</h5>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">O'qituvchilar:</span>
                        <span class="badge bg-warning text-dark">{{ $dept->employees_count }} ta</span>
                    </div>
                </div>
                <div class="card-footer bg-white border-0">
                    <a href="{{ route('dean.teachers.index', ['department_id' => $dept->id]) }}" class="btn btn-outline-warning btn-sm w-100">
                        <i class="fas fa-users me-1"></i> O'qituvchilarni ko'rish
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="fas fa-building fa-4x text-muted mb-4 opacity-50"></i>
                    <h4 class="text-muted">Kafedralar topilmadi</h4>
                </div>
            </div>
        </div>
        @endforelse
    </div>

    @if($departments->hasPages())
    <div class="d-flex justify-content-center">
        {{ $departments->links() }}
    </div>
    @endif
</div>

<style>
.bg-gradient-warning { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
</style>
@endsection
