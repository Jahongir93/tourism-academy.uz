@extends('layouts.dashboard-new')

@section('title', 'Xarita Sozlamalari')
@section('page-title', 'Xarita Sozlamalari')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('campus-tour.dashboard') }}">Kampus Turi</a></li>
                    <li class="breadcrumb-item active">Xarita</li>
                </ol>
            </nav>
            <h1 class="h3 mb-0">
                <i class="fas fa-map text-info me-2"></i>
                Interaktiv Xarita Sozlamalari
            </h1>
        </div>
        <a href="{{ route('campus-tour.map.editor') }}" class="btn btn-info">
            <i class="fas fa-edit me-2"></i>Marker muharriri
        </a>
    </div>

    <form action="{{ route('campus-tour.map.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="row g-4">
            <!-- Main Content -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">Xarita turi</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach(\App\Models\CampusTour\MapSettings::MAP_TYPES as $key => $label)
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="map_type" id="map_{{ $key }}"
                                               value="{{ $key }}" {{ $settings->map_type === $key ? 'checked' : '' }}>
                                        <label class="form-check-label" for="map_{{ $key }}">
                                            {{ $label }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Image-based Map -->
                <div class="card border-0 shadow-sm mb-4" id="imageMapSettings">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0"><i class="fas fa-image me-2"></i>Rasm asosidagi xarita</h5>
                    </div>
                    <div class="card-body">
                        @if($settings->base_image)
                            <div class="mb-3">
                                <label class="form-label">Joriy xarita rasmi</label>
                                <div class="border rounded p-2">
                                    <img src="{{ $settings->base_image_url }}" class="img-fluid rounded" style="max-height: 300px;">
                                </div>
                                <div class="form-text">
                                    O'lcham: {{ $settings->image_width }}x{{ $settings->image_height }} px
                                </div>
                            </div>
                        @endif

                        <div class="mb-3">
                            <label class="form-label">Yangi xarita rasmi yuklash</label>
                            <input type="file" name="base_image" class="form-control" accept="image/*">
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                Kampus xaritasi rasmi (PNG, JPEG), maksimum 10MB
                            </div>
                        </div>
                    </div>
                </div>

                <!-- OSM Settings -->
                <div class="card border-0 shadow-sm mb-4" id="osmSettings">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0"><i class="fas fa-globe me-2"></i>OpenStreetMap sozlamalari</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Markaziy nuqta - Kenglik</label>
                                <input type="number" name="center_lat" class="form-control" value="{{ $settings->center_lat }}"
                                       step="0.00000001" placeholder="39.6512345">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Markaziy nuqta - Uzunlik</label>
                                <input type="number" name="center_lng" class="form-control" value="{{ $settings->center_lng }}"
                                       step="0.00000001" placeholder="66.9612345">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Default zoom</label>
                                <input type="number" name="default_zoom" class="form-control" value="{{ $settings->default_zoom }}"
                                       min="1" max="22">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Minimum zoom</label>
                                <input type="number" name="min_zoom" class="form-control" value="{{ $settings->min_zoom }}"
                                       min="1" max="22">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Maximum zoom</label>
                                <input type="number" name="max_zoom" class="form-control" value="{{ $settings->max_zoom }}"
                                       min="1" max="22">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Custom Tile URL (ixtiyoriy)</label>
                            <input type="url" name="tile_url" class="form-control" value="{{ $settings->tile_url }}"
                                   placeholder="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">Binolar ({{ $buildings->count() }})</h5>
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush" style="max-height: 300px; overflow-y: auto;">
                            @forelse($buildings as $building)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="fas {{ $building->marker_icon }} me-2" style="color: {{ $building->color }}"></i>
                                        {{ $building->title }}
                                    </div>
                                    @if($building->hasCoordinates())
                                        <span class="badge bg-success"><i class="fas fa-check"></i></span>
                                    @else
                                        <span class="badge bg-warning">Koordinata yo'q</span>
                                    @endif
                                </li>
                            @empty
                                <li class="list-group-item text-center text-muted py-3">
                                    Binolar yo'q
                                </li>
                            @endforelse
                        </ul>
                    </div>
                    <div class="card-footer bg-white border-0">
                        <a href="{{ route('campus-tour.buildings.create') }}" class="btn btn-sm btn-outline-success w-100">
                            <i class="fas fa-plus me-1"></i>Bino qo'shish
                        </a>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active"
                                   value="1" {{ $settings->is_active ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Xarita faol</label>
                        </div>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-info btn-lg">
                        <i class="fas fa-save me-2"></i>Saqlash
                    </button>
                    <a href="{{ route('campus-tour.public.map') }}" target="_blank" class="btn btn-outline-secondary">
                        <i class="fas fa-external-link-alt me-2"></i>Xaritani ko'rish
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
