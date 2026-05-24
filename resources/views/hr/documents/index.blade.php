@extends('layouts.dashboard-new')

@section('title', 'Hujjatlar')
@section('page-title', 'Hujjatlar boshqaruvi')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm bg-gradient-orange text-white">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1"><i class="fas fa-folder-open me-2"></i>Hujjatlar boshqaruvi</h4>
                            <p class="mb-0 opacity-75">HR hujjatlari va shablonlarni boshqarish</p>
                        </div>
                        <a href="{{ route('hr.dashboard') }}" class="btn btn-light">
                            <i class="fas fa-arrow-left me-1"></i> Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Hozircha bo'sh -->
    <div class="card border-0 shadow-sm">
        <div class="card-body text-center py-5">
            <i class="fas fa-folder-open fa-4x text-muted mb-4 opacity-50"></i>
            <h4 class="text-muted">Hujjatlar moduli</h4>
            <p class="text-muted mb-4">Bu modul tez orada ishga tushiriladi. HR hujjatlari va shablonlarni boshqarish imkoniyati qo'shiladi.</p>
            <a href="{{ route('hr.dashboard') }}" class="btn btn-primary">
                <i class="fas fa-home me-1"></i> Dashboardga qaytish
            </a>
        </div>
    </div>
</div>

<style>
.bg-gradient-orange {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
}
</style>
@endsection
