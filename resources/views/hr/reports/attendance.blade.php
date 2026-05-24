@extends('layouts.dashboard-new')

@section('title', 'Davomat hisoboti')
@section('page-title', 'Davomat hisoboti')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm bg-gradient-primary text-white">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1"><i class="fas fa-chart-bar me-2"></i>Davomat hisoboti</h4>
                            <p class="mb-0 opacity-75">Xodimlar davomati bo'yicha batafsil hisobot</p>
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
            <i class="fas fa-chart-bar fa-4x text-muted mb-4 opacity-50"></i>
            <h4 class="text-muted">Davomat hisoboti</h4>
            <p class="text-muted mb-4">Bu modul tez orada ishga tushiriladi. Davomat bo'yicha batafsil hisobotlar qo'shiladi.</p>
            <a href="{{ route('hr.dashboard') }}" class="btn btn-primary">
                <i class="fas fa-home me-1"></i> Dashboardga qaytish
            </a>
        </div>
    </div>
</div>

<style>
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
</style>
@endsection
