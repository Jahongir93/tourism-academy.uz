@extends('layouts.dashboard-new')

@section('title', 'Kampus Turi - Dashboard')
@section('page-title', 'Kampus Virtual Turi')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">
                <i class="fas fa-map-marked-alt text-primary me-2"></i>
                Interaktiv Xaritalar va Virtual Tur
            </h1>
            <p class="text-muted mb-0">Kampus virtual turi va yo'nalishlarni boshqaring</p>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-primary bg-opacity-10 rounded-3 p-3">
                                <i class="fas fa-vr-cardboard text-primary fa-2x"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h2 class="mb-0">{{ $panoramasCount }}</h2>
                            <p class="text-muted mb-0">360° Panoramalar</p>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0">
                    <a href="{{ route('campus-tour.panoramas.index') }}" class="text-primary text-decoration-none">
                        Barchasini ko'rish <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-success bg-opacity-10 rounded-3 p-3">
                                <i class="fas fa-building text-success fa-2x"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h2 class="mb-0">{{ $buildingsCount }}</h2>
                            <p class="text-muted mb-0">Binolar</p>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0">
                    <a href="{{ route('campus-tour.buildings.index') }}" class="text-success text-decoration-none">
                        Barchasini ko'rish <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-warning bg-opacity-10 rounded-3 p-3">
                                <i class="fas fa-route text-warning fa-2x"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h2 class="mb-0">{{ $routesCount }}</h2>
                            <p class="text-muted mb-0">Transport Yo'nalishlari</p>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0">
                    <a href="{{ route('campus-tour.routes.index') }}" class="text-warning text-decoration-none">
                        Barchasini ko'rish <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0"><i class="fas fa-bolt me-2"></i>Tezkor harakatlar</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <a href="{{ route('campus-tour.panoramas.create') }}" class="btn btn-outline-primary w-100 py-3">
                                <i class="fas fa-plus-circle d-block mb-2 fa-2x"></i>
                                Panorama qo'shish
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('campus-tour.buildings.create') }}" class="btn btn-outline-success w-100 py-3">
                                <i class="fas fa-plus-circle d-block mb-2 fa-2x"></i>
                                Bino qo'shish
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('campus-tour.routes.create') }}" class="btn btn-outline-warning w-100 py-3">
                                <i class="fas fa-plus-circle d-block mb-2 fa-2x"></i>
                                Yo'nalish qo'shish
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('campus-tour.map.index') }}" class="btn btn-outline-info w-100 py-3">
                                <i class="fas fa-map d-block mb-2 fa-2x"></i>
                                Xarita sozlamalari
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0"><i class="fas fa-external-link-alt me-2"></i>Frontend sahifalar</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span><i class="fas fa-home me-2 text-muted"></i> Kampus Turi Bosh sahifa</span>
                            <a href="{{ route('campus-tour.public.index') }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-external-link-alt"></i>
                            </a>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span><i class="fas fa-vr-cardboard me-2 text-muted"></i> 360° Virtual Tur</span>
                            <a href="{{ route('campus-tour.public.virtual-tour') }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-external-link-alt"></i>
                            </a>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span><i class="fas fa-map-marked-alt me-2 text-muted"></i> Interaktiv Xarita</span>
                            <a href="{{ route('campus-tour.public.map') }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-external-link-alt"></i>
                            </a>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span><i class="fas fa-route me-2 text-muted"></i> Qanday boriladi?</span>
                            <a href="{{ route('campus-tour.public.directions') }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                                <i class="fas fa-external-link-alt"></i>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
