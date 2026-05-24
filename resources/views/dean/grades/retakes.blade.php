@extends('layouts.dashboard-new')

@section('title', 'Qayta topshirish')
@section('page-title', 'Qayta topshirish')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm bg-gradient-warning text-white">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1"><i class="fas fa-redo me-2"></i>Qayta topshirish</h4>
                            <p class="mb-0 opacity-75">{{ $faculty?->name ?? 'Fakultet' }}</p>
                        </div>
                        <a href="{{ route('dean.grades.index') }}" class="btn btn-light">
                            <i class="fas fa-arrow-left me-1"></i> O'zlashtirish
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body text-center py-5">
            <i class="fas fa-redo fa-4x text-muted mb-4 opacity-50"></i>
            <h4 class="text-muted">Qayta topshirish moduli</h4>
            <p class="text-muted mb-4">Bu modul tez orada ishga tushiriladi.</p>
            <a href="{{ route('dean.dashboard') }}" class="btn btn-primary">
                <i class="fas fa-home me-1"></i> Dashboardga qaytish
            </a>
        </div>
    </div>
</div>

<style>.bg-gradient-warning { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }</style>
@endsection
