@extends('layouts.dashboard-new')

@section('title', 'Arizalar')
@section('page-title', 'Ishga qabul arizalari')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm bg-gradient-teal text-white">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1"><i class="fas fa-file-alt me-2"></i>Ishga qabul arizalari</h4>
                            <p class="mb-0 opacity-75">Ishga qabul arizalarini ko'rish va boshqarish</p>
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
            <i class="fas fa-file-alt fa-4x text-muted mb-4 opacity-50"></i>
            <h4 class="text-muted">Arizalar moduli</h4>
            <p class="text-muted mb-4">Bu modul tez orada ishga tushiriladi. Ishga qabul arizalarini boshqarish imkoniyati qo'shiladi.</p>
            <a href="{{ route('hr.dashboard') }}" class="btn btn-primary">
                <i class="fas fa-home me-1"></i> Dashboardga qaytish
            </a>
        </div>
    </div>
</div>

<style>
.bg-gradient-teal {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
}
</style>
@endsection
