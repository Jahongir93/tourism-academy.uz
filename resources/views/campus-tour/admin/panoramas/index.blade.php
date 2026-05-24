@extends('layouts.dashboard-new')

@section('title', '360° Panoramalar')
@section('page-title', '360° Panoramalar')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('campus-tour.dashboard') }}">Kampus Turi</a></li>
                    <li class="breadcrumb-item active">Panoramalar</li>
                </ol>
            </nav>
            <h1 class="h3 mb-0">
                <i class="fas fa-vr-cardboard text-primary me-2"></i>
                360° Panoramalar
            </h1>
        </div>
        <a href="{{ route('campus-tour.panoramas.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Yangi panorama
        </a>
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Qidirish..."
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">Barcha holatlar</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Faol</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Nofaol</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="building_id" class="form-select">
                        <option value="">Barcha binolar</option>
                        @foreach($buildings as $building)
                            <option value="{{ $building->id }}" {{ request('building_id') == $building->id ? 'selected' : '' }}>
                                {{ $building->title }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-outline-primary w-100">
                        <i class="fas fa-search me-1"></i> Qidirish
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Panoramas Grid -->
    @if($panoramas->count() > 0)
        <div class="row g-4">
            @foreach($panoramas as $panorama)
                <div class="col-md-6 col-lg-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="position-relative">
                            @if($panorama->thumbnail_path || $panorama->image_path)
                                <img src="{{ $panorama->thumbnail_url }}" alt="{{ $panorama->title }}"
                                     class="card-img-top" style="height: 200px; object-fit: cover;">
                            @else
                                <div class="bg-secondary d-flex align-items-center justify-content-center" style="height: 200px;">
                                    <i class="fas fa-image fa-3x text-white-50"></i>
                                </div>
                            @endif
                            <div class="position-absolute top-0 end-0 p-2">
                                @if($panorama->is_active)
                                    <span class="badge bg-success">Faol</span>
                                @else
                                    <span class="badge bg-secondary">Nofaol</span>
                                @endif
                                @if($panorama->is_featured)
                                    <span class="badge bg-warning"><i class="fas fa-star"></i></span>
                                @endif
                            </div>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title mb-2">{{ $panorama->title }}</h5>
                            @if($panorama->building)
                                <p class="text-muted small mb-2">
                                    <i class="fas fa-building me-1"></i>{{ $panorama->building->title }}
                                </p>
                            @endif
                            @if($panorama->description)
                                <p class="card-text small text-muted">{{ Str::limit($panorama->description, 80) }}</p>
                            @endif
                        </div>
                        <div class="card-footer bg-white border-0 pt-0">
                            <div class="btn-group w-100">
                                <a href="{{ route('campus-tour.panoramas.preview', $panorama) }}" class="btn btn-sm btn-outline-primary" title="Ko'rish">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('campus-tour.panoramas.edit', $panorama) }}" class="btn btn-sm btn-outline-secondary" title="Tahrirlash">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('campus-tour.panoramas.destroy', $panorama) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Haqiqatan ham o\'chirmoqchimisiz?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="O'chirish">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $panoramas->links() }}
        </div>
    @else
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="fas fa-vr-cardboard fa-4x text-muted mb-3"></i>
                <h5>Hali panoramalar yo'q</h5>
                <p class="text-muted">Birinchi 360° panoramani qo'shing</p>
                <a href="{{ route('campus-tour.panoramas.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Panorama qo'shish
                </a>
            </div>
        </div>
    @endif
</div>
@endsection
