@extends('layouts.dashboard-new')

@section('title', 'Marker Muharriri')
@section('page-title', 'Marker Muharriri')

@push('styles')
<style>
    .map-editor-container {
        position: relative;
        width: 100%;
        overflow: hidden;
        border: 2px solid #dee2e6;
        border-radius: 8px;
        background: #f8f9fa;
    }
    .map-image {
        width: 100%;
        display: block;
        cursor: crosshair;
    }
    .map-marker {
        position: absolute;
        transform: translate(-50%, -100%);
        cursor: move;
        transition: transform 0.1s;
        z-index: 10;
    }
    .map-marker:hover {
        transform: translate(-50%, -100%) scale(1.2);
    }
    .map-marker i {
        font-size: 2rem;
        filter: drop-shadow(2px 2px 2px rgba(0,0,0,0.3));
    }
    .map-marker .marker-label {
        position: absolute;
        top: 100%;
        left: 50%;
        transform: translateX(-50%);
        white-space: nowrap;
        background: rgba(0,0,0,0.7);
        color: white;
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 12px;
        margin-top: 4px;
    }
    .building-item {
        cursor: pointer;
        transition: background-color 0.2s;
    }
    .building-item:hover {
        background-color: #f8f9fa;
    }
    .building-item.active {
        background-color: #e7f3ff;
        border-left: 3px solid #0d6efd;
    }
    .building-item.has-coordinates {
        border-left: 3px solid #198754;
    }
    .osm-map-container {
        height: 500px;
        border-radius: 8px;
        overflow: hidden;
    }
</style>
@if($settings->map_type === 'osm' || $settings->map_type === 'google')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endif
@endpush

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('campus-tour.dashboard') }}">Kampus Turi</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('campus-tour.map.index') }}">Xarita</a></li>
                    <li class="breadcrumb-item active">Marker muharriri</li>
                </ol>
            </nav>
            <h1 class="h3 mb-0">
                <i class="fas fa-map-marked-alt text-info me-2"></i>
                Marker Joylashuvi Muharriri
            </h1>
        </div>
        <div>
            <button type="button" class="btn btn-success" id="saveAllBtn">
                <i class="fas fa-save me-2"></i>Barchasini saqlash
            </button>
        </div>
    </div>

    <div class="row g-4">
        <!-- Map Area -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        @if($settings->map_type === 'image')
                            <i class="fas fa-image me-2"></i>Rasm asosidagi xarita
                        @else
                            <i class="fas fa-globe me-2"></i>OpenStreetMap
                        @endif
                    </h5>
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-outline-secondary" id="zoomIn">
                            <i class="fas fa-search-plus"></i>
                        </button>
                        <button type="button" class="btn btn-outline-secondary" id="zoomOut">
                            <i class="fas fa-search-minus"></i>
                        </button>
                        <button type="button" class="btn btn-outline-secondary" id="resetZoom">
                            <i class="fas fa-compress-arrows-alt"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($settings->map_type === 'image')
                        @if($settings->base_image)
                            <div class="map-editor-container" id="mapContainer">
                                <img src="{{ $settings->base_image_url }}" class="map-image" id="mapImage" alt="Kampus xaritasi">
                                @foreach($buildings as $building)
                                    @if($building->marker_x !== null && $building->marker_y !== null)
                                        <div class="map-marker" data-id="{{ $building->id }}"
                                             style="left: {{ $building->marker_x }}%; top: {{ $building->marker_y }}%;">
                                            <i class="fas {{ $building->marker_icon }}" style="color: {{ $building->color }}"></i>
                                            <span class="marker-label">{{ $building->title }}</span>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-image fa-4x text-muted mb-3"></i>
                                <h5>Xarita rasmi yuklanmagan</h5>
                                <p class="text-muted">Avval xarita sozlamalaridan rasm yuklang</p>
                                <a href="{{ route('campus-tour.map.index') }}" class="btn btn-info">
                                    <i class="fas fa-cog me-2"></i>Sozlamalarga o'tish
                                </a>
                            </div>
                        @endif
                    @else
                        <div class="osm-map-container" id="osmMap"></div>
                    @endif
                </div>
            </div>

            <div class="alert alert-info mt-3">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Qo'llanma:</strong>
                @if($settings->map_type === 'image')
                    O'ng tomondan binoni tanlang, so'ng xaritada kerakli joyga bosing.
                @else
                    O'ng tomondan binoni tanlang, so'ng xaritada kerakli joyga bosing yoki markerni sudrab olib boring.
                @endif
            </div>
        </div>

        <!-- Buildings List -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">
                        <i class="fas fa-building me-2"></i>Binolar ({{ $buildings->count() }})
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush" style="max-height: 500px; overflow-y: auto;">
                        @forelse($buildings as $building)
                            <div class="list-group-item building-item d-flex justify-content-between align-items-center {{ $building->hasCoordinates() ? 'has-coordinates' : '' }}"
                                 data-id="{{ $building->id }}"
                                 data-x="{{ $building->marker_x }}"
                                 data-y="{{ $building->marker_y }}"
                                 data-lat="{{ $building->latitude }}"
                                 data-lng="{{ $building->longitude }}">
                                <div class="d-flex align-items-center">
                                    <i class="fas {{ $building->marker_icon }} me-3" style="color: {{ $building->color }}; font-size: 1.25rem;"></i>
                                    <div>
                                        <strong>{{ $building->title }}</strong>
                                        <div class="small text-muted">{{ $building->category_label }}</div>
                                    </div>
                                </div>
                                <div>
                                    @if($building->hasCoordinates())
                                        <span class="badge bg-success"><i class="fas fa-check"></i></span>
                                    @else
                                        <span class="badge bg-warning text-dark">
                                            <i class="fas fa-exclamation"></i>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4">
                                <i class="fas fa-building fa-2x text-muted mb-2"></i>
                                <p class="text-muted mb-0">Binolar yo'q</p>
                                <a href="{{ route('campus-tour.buildings.create') }}" class="btn btn-sm btn-success mt-2">
                                    <i class="fas fa-plus me-1"></i>Bino qo'shish
                                </a>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Selected Building Info -->
            <div class="card border-0 shadow-sm mt-3" id="selectedBuildingCard" style="display: none;">
                <div class="card-header bg-info text-white py-3">
                    <h6 class="mb-0">
                        <i class="fas fa-map-marker-alt me-2"></i>Tanlangan bino
                    </h6>
                </div>
                <div class="card-body">
                    <div id="selectedBuildingInfo"></div>
                    <div class="mt-3">
                        <div class="row g-2">
                            @if($settings->map_type === 'image')
                                <div class="col-6">
                                    <label class="form-label small">X (%)</label>
                                    <input type="number" class="form-control form-control-sm" id="coordX" step="0.01" min="0" max="100">
                                </div>
                                <div class="col-6">
                                    <label class="form-label small">Y (%)</label>
                                    <input type="number" class="form-control form-control-sm" id="coordY" step="0.01" min="0" max="100">
                                </div>
                            @else
                                <div class="col-6">
                                    <label class="form-label small">Kenglik</label>
                                    <input type="number" class="form-control form-control-sm" id="coordLat" step="0.00000001">
                                </div>
                                <div class="col-6">
                                    <label class="form-label small">Uzunlik</label>
                                    <input type="number" class="form-control form-control-sm" id="coordLng" step="0.00000001">
                                </div>
                            @endif
                        </div>
                        <div class="d-grid gap-2 mt-3">
                            <button type="button" class="btn btn-success btn-sm" id="saveCoordinates">
                                <i class="fas fa-save me-1"></i>Koordinatalarni saqlash
                            </button>
                            <button type="button" class="btn btn-outline-danger btn-sm" id="removeMarker">
                                <i class="fas fa-trash me-1"></i>Markerni o'chirish
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@if($settings->map_type === 'osm' || $settings->map_type === 'google')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
@endif
<script>
document.addEventListener('DOMContentLoaded', function() {
    const mapType = '{{ $settings->map_type }}';
    let selectedBuildingId = null;
    let markers = {};
    let osmMap = null;

    // Buildings data
    const buildings = @json($buildings);

    @if($settings->map_type === 'image')
    // Image-based map
    const mapContainer = document.getElementById('mapContainer');
    const mapImage = document.getElementById('mapImage');

    if (mapContainer && mapImage) {
        // Click on map to set marker
        mapContainer.addEventListener('click', function(e) {
            if (!selectedBuildingId) {
                alert('Avval o\'ng tomondan binoni tanlang');
                return;
            }

            const rect = mapImage.getBoundingClientRect();
            const x = ((e.clientX - rect.left) / rect.width) * 100;
            const y = ((e.clientY - rect.top) / rect.height) * 100;

            updateMarkerPosition(selectedBuildingId, x, y);
            document.getElementById('coordX').value = x.toFixed(2);
            document.getElementById('coordY').value = y.toFixed(2);
        });

        // Make markers draggable
        document.querySelectorAll('.map-marker').forEach(marker => {
            marker.addEventListener('mousedown', startDrag);
        });

        function startDrag(e) {
            e.preventDefault();
            const marker = e.currentTarget;
            const buildingId = marker.dataset.id;
            selectBuilding(buildingId);

            function onMouseMove(e) {
                const rect = mapImage.getBoundingClientRect();
                const x = ((e.clientX - rect.left) / rect.width) * 100;
                const y = ((e.clientY - rect.top) / rect.height) * 100;

                if (x >= 0 && x <= 100 && y >= 0 && y <= 100) {
                    marker.style.left = x + '%';
                    marker.style.top = y + '%';
                    document.getElementById('coordX').value = x.toFixed(2);
                    document.getElementById('coordY').value = y.toFixed(2);
                }
            }

            function onMouseUp() {
                document.removeEventListener('mousemove', onMouseMove);
                document.removeEventListener('mouseup', onMouseUp);
            }

            document.addEventListener('mousemove', onMouseMove);
            document.addEventListener('mouseup', onMouseUp);
        }

        function updateMarkerPosition(buildingId, x, y) {
            let marker = document.querySelector(`.map-marker[data-id="${buildingId}"]`);
            const building = buildings.find(b => b.id == buildingId);

            if (!marker && building) {
                marker = document.createElement('div');
                marker.className = 'map-marker';
                marker.dataset.id = buildingId;
                marker.innerHTML = `
                    <i class="fas ${building.marker_icon}" style="color: ${building.color}"></i>
                    <span class="marker-label">${building.title}</span>
                `;
                marker.addEventListener('mousedown', startDrag);
                mapContainer.appendChild(marker);
            }

            if (marker) {
                marker.style.left = x + '%';
                marker.style.top = y + '%';
            }
        }
    }
    @else
    // OSM Map
    osmMap = L.map('osmMap').setView([{{ $settings->center_lat ?? 39.65 }}, {{ $settings->center_lng ?? 66.96 }}], {{ $settings->default_zoom ?? 17 }});

    L.tileLayer('{{ $settings->tile_url ?? "https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png" }}', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(osmMap);

    // Add existing markers
    buildings.forEach(building => {
        if (building.latitude && building.longitude) {
            const marker = L.marker([building.latitude, building.longitude], {
                draggable: true
            }).addTo(osmMap);

            marker.bindPopup(building.title);
            markers[building.id] = marker;

            marker.on('dragend', function(e) {
                const pos = e.target.getLatLng();
                selectBuilding(building.id);
                document.getElementById('coordLat').value = pos.lat.toFixed(8);
                document.getElementById('coordLng').value = pos.lng.toFixed(8);
            });

            marker.on('click', function() {
                selectBuilding(building.id);
            });
        }
    });

    // Click on map to add marker
    osmMap.on('click', function(e) {
        if (!selectedBuildingId) {
            alert('Avval o\'ng tomondan binoni tanlang');
            return;
        }

        const building = buildings.find(b => b.id == selectedBuildingId);
        if (!markers[selectedBuildingId]) {
            const marker = L.marker([e.latlng.lat, e.latlng.lng], {
                draggable: true
            }).addTo(osmMap);

            marker.bindPopup(building.title);
            markers[selectedBuildingId] = marker;

            marker.on('dragend', function(evt) {
                const pos = evt.target.getLatLng();
                document.getElementById('coordLat').value = pos.lat.toFixed(8);
                document.getElementById('coordLng').value = pos.lng.toFixed(8);
            });
        } else {
            markers[selectedBuildingId].setLatLng([e.latlng.lat, e.latlng.lng]);
        }

        document.getElementById('coordLat').value = e.latlng.lat.toFixed(8);
        document.getElementById('coordLng').value = e.latlng.lng.toFixed(8);
    });
    @endif

    // Building selection
    document.querySelectorAll('.building-item').forEach(item => {
        item.addEventListener('click', function() {
            selectBuilding(this.dataset.id);
        });
    });

    function selectBuilding(id) {
        selectedBuildingId = id;
        const building = buildings.find(b => b.id == id);

        document.querySelectorAll('.building-item').forEach(item => {
            item.classList.remove('active');
        });
        document.querySelector(`.building-item[data-id="${id}"]`).classList.add('active');

        document.getElementById('selectedBuildingCard').style.display = 'block';
        document.getElementById('selectedBuildingInfo').innerHTML = `
            <div class="d-flex align-items-center">
                <i class="fas ${building.marker_icon} me-3" style="color: ${building.color}; font-size: 1.5rem;"></i>
                <div>
                    <strong>${building.title}</strong>
                    <div class="small text-muted">${building.category_label}</div>
                </div>
            </div>
        `;

        @if($settings->map_type === 'image')
        document.getElementById('coordX').value = building.marker_x || '';
        document.getElementById('coordY').value = building.marker_y || '';
        @else
        document.getElementById('coordLat').value = building.latitude || '';
        document.getElementById('coordLng').value = building.longitude || '';
        @endif
    }

    // Save coordinates
    document.getElementById('saveCoordinates').addEventListener('click', function() {
        if (!selectedBuildingId) return;

        @if($settings->map_type === 'image')
        const data = {
            marker_x: document.getElementById('coordX').value,
            marker_y: document.getElementById('coordY').value
        };
        @else
        const data = {
            latitude: document.getElementById('coordLat').value,
            longitude: document.getElementById('coordLng').value
        };
        @endif

        saveBuilding(selectedBuildingId, data);
    });

    // Remove marker
    document.getElementById('removeMarker').addEventListener('click', function() {
        if (!selectedBuildingId) return;

        if (confirm('Markerni o\'chirmoqchimisiz?')) {
            @if($settings->map_type === 'image')
            const marker = document.querySelector(`.map-marker[data-id="${selectedBuildingId}"]`);
            if (marker) marker.remove();
            saveBuilding(selectedBuildingId, { marker_x: null, marker_y: null });
            @else
            if (markers[selectedBuildingId]) {
                osmMap.removeLayer(markers[selectedBuildingId]);
                delete markers[selectedBuildingId];
            }
            saveBuilding(selectedBuildingId, { latitude: null, longitude: null });
            @endif
        }
    });

    // Save All
    document.getElementById('saveAllBtn').addEventListener('click', function() {
        const promises = [];

        @if($settings->map_type === 'image')
        document.querySelectorAll('.map-marker').forEach(marker => {
            const id = marker.dataset.id;
            const x = parseFloat(marker.style.left);
            const y = parseFloat(marker.style.top);
            promises.push(saveBuilding(id, { marker_x: x, marker_y: y }, false));
        });
        @else
        Object.keys(markers).forEach(id => {
            const pos = markers[id].getLatLng();
            promises.push(saveBuilding(id, { latitude: pos.lat, longitude: pos.lng }, false));
        });
        @endif

        Promise.all(promises).then(() => {
            alert('Barcha markerlar saqlandi!');
            location.reload();
        });
    });

    function saveBuilding(id, data, showAlert = true) {
        return fetch(`/admin/campus-tour/buildings/${id}/marker`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(result => {
            if (showAlert) {
                if (result.success) {
                    alert('Saqlandi!');
                    // Update building item
                    const item = document.querySelector(`.building-item[data-id="${id}"]`);
                    if (item) {
                        item.classList.add('has-coordinates');
                        item.querySelector('.badge').className = 'badge bg-success';
                        item.querySelector('.badge').innerHTML = '<i class="fas fa-check"></i>';
                    }
                } else {
                    alert('Xatolik yuz berdi');
                }
            }
            return result;
        });
    }
});
</script>
@endpush
