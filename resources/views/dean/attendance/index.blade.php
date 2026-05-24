@extends('layouts.dashboard-new')

@section('title', 'Davomat')
@section('page-title', 'Davomat nazorati')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm bg-gradient-danger text-white">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1"><i class="fas fa-clipboard-check me-2"></i>Davomat nazorati</h4>
                            <p class="mb-0 opacity-75">{{ $faculty?->name ?? 'Fakultet' }}</p>
                        </div>
                        <a href="{{ route('dean.dashboard') }}" class="btn btn-light">
                            <i class="fas fa-arrow-left me-1"></i> Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Guruhlar davomat statistikasi -->
    <div class="row">
        @forelse($groups as $group)
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-danger bg-opacity-10 rounded-circle p-3 me-3">
                            <i class="fas fa-users fa-lg text-danger"></i>
                        </div>
                        <div>
                            <h5 class="mb-0">{{ $group->name }}</h5>
                            <small class="text-muted">{{ $group->students_count }} talaba</small>
                        </div>
                    </div>
                    <div class="progress mb-2" style="height: 8px;">
                        <div class="progress-bar bg-success" style="width: 85%"></div>
                    </div>
                    <small class="text-muted">Davomat: 85%</small>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="fas fa-clipboard-check fa-4x text-muted mb-4 opacity-50"></i>
                    <h4 class="text-muted">Guruhlar topilmadi</h4>
                </div>
            </div>
        </div>
        @endforelse
    </div>
</div>

<style>.bg-gradient-danger { background: linear-gradient(135deg, #ff416c 0%, #ff4b2b 100%); }</style>
@endsection
