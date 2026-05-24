@extends('layouts.dashboard-new')

@section('title', 'O\'zlashtirish')
@section('page-title', 'O\'zlashtirish nazorati')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm bg-gradient-warning text-white">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1"><i class="fas fa-chart-line me-2"></i>O'zlashtirish nazorati</h4>
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

    <!-- Guruhlar bo'yicha -->
    <div class="row">
        @forelse($groups as $group)
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="bg-warning bg-opacity-10 rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                        <i class="fas fa-users fa-lg text-warning"></i>
                    </div>
                    <h5 class="mb-2">{{ $group->name }}</h5>
                    <p class="text-muted mb-0">Baholar mavjud emas</p>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="fas fa-chart-line fa-4x text-muted mb-4 opacity-50"></i>
                    <h4 class="text-muted">Guruhlar topilmadi</h4>
                </div>
            </div>
        </div>
        @endforelse
    </div>
</div>

<style>.bg-gradient-warning { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }</style>
@endsection
