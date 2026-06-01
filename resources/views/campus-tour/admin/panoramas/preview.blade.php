@extends('layouts.dashboard-new')

@section('title', $panorama->title . ' - Preview')
@section('page-title', 'Panorama Ko\'rish')

@push('styles')
<link rel="stylesheet" href="{{ asset('vendor/pannellum/pannellum.css') }}"/>
<style>
    #panorama-viewer {
        width: 100%;
        height: 500px;
        border-radius: 0.5rem;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('campus-tour.dashboard') }}">Kampus Turi</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('campus-tour.panoramas.index') }}">Panoramalar</a></li>
                    <li class="breadcrumb-item active">Ko'rish</li>
                </ol>
            </nav>
            <h1 class="h3 mb-0">{{ $panorama->title }}</h1>
        </div>
        <div>
            <a href="{{ route('campus-tour.panoramas.edit', $panorama) }}" class="btn btn-outline-primary">
                <i class="fas fa-edit me-2"></i>Tahrirlash
            </a>
            <a href="{{ route('campus-tour.panoramas.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Orqaga
            </a>
        </div>
    </div>

    <!-- Panorama Viewer -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-0">
            <div id="panorama-viewer"></div>
        </div>
    </div>

    <!-- Info -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">Ma'lumotlar</h5>
                </div>
                <div class="card-body">
                    @if($panorama->description)
                        <p>{{ $panorama->description }}</p>
                    @endif

                    <table class="table table-sm">
                        <tr>
                            <th width="200">Holat:</th>
                            <td>
                                @if($panorama->is_active)
                                    <span class="badge bg-success">Faol</span>
                                @else
                                    <span class="badge bg-secondary">Nofaol</span>
                                @endif
                            </td>
                        </tr>
                        @if($panorama->building)
                            <tr>
                                <th>Bino:</th>
                                <td>{{ $panorama->building->title }}</td>
                            </tr>
                        @endif
                        <tr>
                            <th>Yaratilgan:</th>
                            <td>{{ $panorama->created_at->format('d.m.Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Yangilangan:</th>
                            <td>{{ $panorama->updated_at->format('d.m.Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="{{ asset('vendor/pannellum/pannellum.js') }}"></script>
<script>
pannellum.viewer('panorama-viewer', {
    type: 'equirectangular',
    panorama: '{{ $panorama->image_url }}',
    autoLoad: true,
    autoRotate: -2,
    compass: true,
    showFullscreenCtrl: true,
    showZoomCtrl: true,
    mouseZoom: true,
    hfov: 100,
    pitch: 0,
    yaw: 0
});
</script>
@endpush
@endsection
