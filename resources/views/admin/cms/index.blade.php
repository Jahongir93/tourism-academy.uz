@extends('layouts.dashboard-new')

@section('title', 'CMS Boshqaruv Paneli')
@section('page-title', 'CMS Boshqaruv Paneli')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-3"><i class="fas fa-edit"></i> CMS Boshqaruv Paneli</h1>
            <p class="text-muted">Sayt kontentini boshqarish va tahrirlash</p>
        </div>
    </div>

    <div class="row g-4">
        <!-- Header -->
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm hover-shadow">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-box bg-primary bg-opacity-10 text-primary rounded-circle p-3 me-3">
                            <i class="fas fa-heading fa-2x"></i>
                        </div>
                        <h5 class="card-title mb-0">Header</h5>
                    </div>
                    <p class="card-text text-muted">Sayt sarlavha qismini tahrirlash</p>
                    <a href="{{ route('admin.cms.edit', 'header') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-edit"></i> Tahrirlash
                    </a>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm hover-shadow">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-box bg-success bg-opacity-10 text-success rounded-circle p-3 me-3">
                            <i class="fas fa-shoe-prints fa-2x"></i>
                        </div>
                        <h5 class="card-title mb-0">Footer</h5>
                    </div>
                    <p class="card-text text-muted">Sayt pastki qismini tahrirlash</p>
                    <a href="{{ route('admin.cms.edit', 'footer') }}" class="btn btn-success btn-sm">
                        <i class="fas fa-edit"></i> Tahrirlash
                    </a>
                </div>
            </div>
        </div>

        <!-- Home - Quick Access -->
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm hover-shadow">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-box bg-info bg-opacity-10 text-info rounded-circle p-3 me-3">
                            <i class="fas fa-home fa-2x"></i>
                        </div>
                        <h5 class="card-title mb-0">Bosh sahifa - Quick Access</h5>
                    </div>
                    <p class="card-text text-muted">HEMIS va LMS bloklarini tahrirlash</p>
                    <a href="{{ route('admin.cms.edit', 'home_quick_access') }}" class="btn btn-info btn-sm">
                        <i class="fas fa-edit"></i> Tahrirlash
                    </a>
                </div>
            </div>
        </div>

        <!-- About - UN Tourism -->
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm hover-shadow">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-box bg-warning bg-opacity-10 text-warning rounded-circle p-3 me-3">
                            <i class="fas fa-certificate fa-2x"></i>
                        </div>
                        <h5 class="card-title mb-0">Biz haqimizda - UN Tourism</h5>
                    </div>
                    <p class="card-text text-muted">UN Tourism sertifikat bo'limini tahrirlash</p>
                    <a href="{{ route('admin.cms.edit', 'about_un_tourism') }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-edit"></i> Tahrirlash
                    </a>
                </div>
            </div>
        </div>

        <!-- Programs - Statistics -->
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm hover-shadow">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-box bg-danger bg-opacity-10 text-danger rounded-circle p-3 me-3">
                            <i class="fas fa-chart-bar fa-2x"></i>
                        </div>
                        <h5 class="card-title mb-0">Yo'nalishlar - Statistika</h5>
                    </div>
                    <p class="card-text text-muted">Dastur statistikasi bo'limini tahrirlash</p>
                    <a href="{{ route('admin.cms.edit', 'programs_stats') }}" class="btn btn-danger btn-sm">
                        <i class="fas fa-edit"></i> Tahrirlash
                    </a>
                </div>
            </div>
        </div>

        <!-- Statistics - Age -->
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm hover-shadow">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-box bg-secondary bg-opacity-10 text-secondary rounded-circle p-3 me-3">
                            <i class="fas fa-users fa-2x"></i>
                        </div>
                        <h5 class="card-title mb-0">Statistika - Yosh bo'yicha</h5>
                    </div>
                    <p class="card-text text-muted">Yosh statistikasi bo'limini tahrirlash</p>
                    <a href="{{ route('admin.cms.edit', 'statistics_age') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-edit"></i> Tahrirlash
                    </a>
                </div>
            </div>
        </div>

        <!-- Contacts -->
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm hover-shadow">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-box bg-dark bg-opacity-10 text-dark rounded-circle p-3 me-3">
                            <i class="fas fa-address-book fa-2x"></i>
                        </div>
                        <h5 class="card-title mb-0">Kontaktlar</h5>
                    </div>
                    <p class="card-text text-muted">Kontaktlar sahifasini tahrirlash</p>
                    <a href="{{ route('admin.cms.edit', 'contacts') }}" class="btn btn-dark btn-sm">
                        <i class="fas fa-edit"></i> Tahrirlash
                    </a>
                </div>
            </div>
        </div>

        <!-- Menu Management -->
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm hover-shadow border-primary">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-box bg-primary bg-opacity-10 text-primary rounded-circle p-3 me-3">
                            <i class="fas fa-bars fa-2x"></i>
                        </div>
                        <h5 class="card-title mb-0">Menyu boshqaruvi</h5>
                    </div>
                    <p class="card-text text-muted">Sayt menyusini sozlash va boshqarish</p>
                    <a href="{{ route('admin.menu.index') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-cog"></i> Boshqarish
                    </a>
                </div>
            </div>
        </div>

        <!-- Kampus Virtual Turi -->
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm hover-shadow border-info">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-box bg-info bg-opacity-10 text-info rounded-circle p-3 me-3">
                            <i class="fas fa-map-marked-alt fa-2x"></i>
                        </div>
                        <h5 class="card-title mb-0">Kampus Virtual Turi</h5>
                    </div>
                    <p class="card-text text-muted">360° panoramalar, xarita va yo'nalishlarni boshqarish</p>
                    <a href="{{ route('campus-tour.dashboard') }}" class="btn btn-info btn-sm">
                        <i class="fas fa-cog"></i> Boshqarish
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.hover-shadow {
    transition: all 0.3s ease;
}

.hover-shadow:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}

.icon-box {
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>
@endsection
