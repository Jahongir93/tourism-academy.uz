@extends('layouts.frontend')

@section('title', 'Interaktiv Xarita - Tourism Academy')

@push('styles')
@if($settings->map_type !== 'image')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endif
<style>
    .map-container {
        height: calc(100vh - 80px);
        min-height: 600px;
        position: relative;
        background: #f5f5f5;
    }
    .image-map {
        width: 100%;
        height: 100%;
        overflow: auto;
        position: relative;
    }
    .image-map img {
        max-width: none;
        display: block;
    }
    #leaflet-map {
        width: 100%;
        height: 100%;
    }
    .map-marker {
        position: absolute;
        transform: translate(-50%, -100%);
        cursor: pointer;
        transition: transform 0.2s ease;
        z-index: 10;
    }
    .map-marker:hover {
        transform: translate(-50%, -100%) scale(1.2);
    }
    .map-marker i {
        font-size: 2rem;
        filter: drop-shadow(2px 2px 3px rgba(0,0,0,0.3));
    }
    .building-sidebar {
        position: absolute;
        top: 0;
        right: 0;
        width: 380px;
        height: 100%;
        background: white;
        box-shadow: -4px 0 20px rgba(0,0,0,0.1);
        z-index: 1000;
        overflow-y: auto;
        transform: translateX(100%);
        transition: transform 0.3s ease;
    }
    .building-sidebar.active {
        transform: translateX(0);
    }
    .sidebar-toggle {
        position: absolute;
        top: 20px;
        right: 20px;
        z-index: 999;
        background: white;
        border: none;
        width: 50px;
        height: 50px;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        cursor: pointer;
        font-size: 20px;
        transition: all 0.2s ease;
    }
    .sidebar-toggle:hover {
        transform: scale(1.05);
    }
    .building-list-item {
        padding: 16px 20px;
        border-bottom: 1px solid #f0f0f0;
        cursor: pointer;
        transition: background 0.2s ease;
    }
    .building-list-item:hover {
        background: #f8f9fa;
    }
    .building-list-item.active {
        background: #e7f3ff;
        border-left: 3px solid #0066CC;
    }
    .building-detail {
        padding: 24px;
    }
    .back-to-list {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #666;
        font-size: 14px;
        cursor: pointer;
        margin-bottom: 16px;
        transition: color 0.2s ease;
    }
    .back-to-list:hover {
        color: #0066CC;
    }
    .category-filter {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        padding: 16px;
        border-bottom: 1px solid #f0f0f0;
    }
    .category-btn {
        padding: 6px 14px;
        border-radius: 50px;
        font-size: 13px;
        font-weight: 500;
        border: 1px solid #e5e7eb;
        background: white;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .category-btn:hover {
        border-color: #0066CC;
        color: #0066CC;
    }
    .category-btn.active {
        background: #0066CC;
        border-color: #0066CC;
        color: white;
    }
    .search-box {
        padding: 16px;
        border-bottom: 1px solid #f0f0f0;
    }
    .search-box input {
        width: 100%;
        padding: 12px 16px 12px 44px;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        font-size: 14px;
        transition: all 0.2s ease;
    }
    .search-box input:focus {
        outline: none;
        border-color: #0066CC;
        box-shadow: 0 0 0 3px rgba(0,102,204,0.1);
    }
    .search-icon {
        position: absolute;
        left: 32px;
        top: 50%;
        transform: translateY(-50%);
        color: #9ca3af;
    }
    /* Mobile responsive */
    @media (max-width: 768px) {
        .building-sidebar {
            width: 100%;
            height: 60%;
            bottom: 0;
            top: auto;
            transform: translateY(100%);
            border-radius: 20px 20px 0 0;
        }
        .building-sidebar.active {
            transform: translateY(0);
        }
    }
    /* Leaflet custom popup */
    .leaflet-popup-content-wrapper {
        border-radius: 12px;
        padding: 0;
    }
    .leaflet-popup-content {
        margin: 0;
        min-width: 200px;
    }
    .custom-popup {
        padding: 16px;
    }
    .custom-popup h4 {
        font-weight: 600;
        margin-bottom: 8px;
    }
    .custom-popup p {
        color: #666;
        font-size: 13px;
        margin-bottom: 12px;
    }
    .custom-popup a {
        display: inline-block;
        padding: 8px 16px;
        background: #0066CC;
        color: white;
        border-radius: 6px;
        text-decoration: none;
        font-size: 13px;
    }
</style>
@endpush

@section('content')
<div class="map-container">
    @if($settings->map_type === 'image')
        <!-- Image-based Map -->
        <div class="image-map" id="imageMap">
            @if($settings->base_image)
                <img src="{{ $settings->base_image_url }}" alt="Kampus xaritasi" id="mapImage">
                @foreach($buildings as $building)
                    @if($building->marker_x !== null && $building->marker_y !== null)
                        <div class="map-marker" data-id="{{ $building->id }}"
                             style="left: {{ $building->marker_x }}%; top: {{ $building->marker_y }}%;">
                            <i class="fas {{ $building->marker_icon }}" style="color: {{ $building->color }}"></i>
                        </div>
                    @endif
                @endforeach
            @else
                <div class="flex items-center justify-center h-full">
                    <div class="text-center">
                        <i class="fas fa-map text-6xl text-gray-300 mb-4"></i>
                        <p class="text-gray-500">Xarita hali sozlanmagan</p>
                    </div>
                </div>
            @endif
        </div>
    @else
        <!-- Leaflet Map -->
        <div id="leaflet-map"></div>
    @endif

    <!-- Sidebar Toggle -->
    <button class="sidebar-toggle" id="sidebarToggle">
        <i class="fas fa-list"></i>
    </button>

    <!-- Building Sidebar -->
    <div class="building-sidebar" id="buildingSidebar">
        <!-- Sidebar Header -->
        <div class="p-4 border-b bg-gray-50">
            <div class="flex justify-between items-center">
                <h3 class="font-bold text-lg">Binolar</h3>
                <button id="closeSidebar" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>

        <!-- Search -->
        <div class="search-box relative">
            <i class="fas fa-search search-icon"></i>
            <input type="text" id="buildingSearch" placeholder="Qidirish...">
        </div>

        <!-- Category Filter -->
        <div class="category-filter">
            <button class="category-btn active" data-category="all">Barchasi</button>
            @foreach(\App\Models\CampusTour\Building::CATEGORIES as $key => $label)
                <button class="category-btn" data-category="{{ $key }}">{{ $label }}</button>
            @endforeach
        </div>

        <!-- Building List -->
        <div id="buildingList">
            @foreach($buildings as $building)
                <div class="building-list-item" data-id="{{ $building->id }}" data-category="{{ $building->category }}">
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center mr-3" style="background-color: {{ $building->color }}20;">
                            <i class="fas {{ $building->marker_icon }}" style="color: {{ $building->color }}"></i>
                        </div>
                        <div class="flex-1">
                            <h4 class="font-medium text-gray-800">{{ $building->title }}</h4>
                            <span class="text-sm text-gray-500">{{ $building->category_label }}</span>
                        </div>
                        <i class="fas fa-chevron-right text-gray-300"></i>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Building Detail (Hidden by default) -->
        <div id="buildingDetail" style="display: none;">
            <div class="building-detail">
                <div class="back-to-list" id="backToList">
                    <i class="fas fa-arrow-left"></i>
                    <span>Orqaga</span>
                </div>
                <div id="detailContent"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@if($settings->map_type !== 'image')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
@endif
<script>
document.addEventListener('DOMContentLoaded', function() {
    const buildings = @json($buildings);
    const mapType = '{{ $settings->map_type }}';

    // Sidebar functionality
    const sidebar = document.getElementById('buildingSidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const closeSidebar = document.getElementById('closeSidebar');
    const buildingList = document.getElementById('buildingList');
    const buildingDetail = document.getElementById('buildingDetail');
    const detailContent = document.getElementById('detailContent');
    const backToList = document.getElementById('backToList');

    sidebarToggle.addEventListener('click', () => {
        sidebar.classList.toggle('active');
    });

    closeSidebar.addEventListener('click', () => {
        sidebar.classList.remove('active');
    });

    // Search functionality
    const searchInput = document.getElementById('buildingSearch');
    searchInput.addEventListener('input', filterBuildings);

    // Category filter
    document.querySelectorAll('.category-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.category-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            filterBuildings();
        });
    });

    function filterBuildings() {
        const searchTerm = searchInput.value.toLowerCase();
        const category = document.querySelector('.category-btn.active').dataset.category;

        document.querySelectorAll('.building-list-item').forEach(item => {
            const title = item.querySelector('h4').textContent.toLowerCase();
            const itemCategory = item.dataset.category;

            const matchesSearch = title.includes(searchTerm);
            const matchesCategory = category === 'all' || itemCategory === category;

            item.style.display = (matchesSearch && matchesCategory) ? 'block' : 'none';
        });
    }

    // Building item click
    document.querySelectorAll('.building-list-item').forEach(item => {
        item.addEventListener('click', function() {
            const buildingId = this.dataset.id;
            const building = buildings.find(b => b.id == buildingId);
            showBuildingDetail(building);
        });
    });

    // Back to list
    backToList.addEventListener('click', function() {
        buildingList.style.display = 'block';
        buildingDetail.style.display = 'none';
    });

    function showBuildingDetail(building) {
        buildingList.style.display = 'none';
        buildingDetail.style.display = 'block';

        let panoramaLinks = '';
        if (building.panoramas && building.panoramas.length > 0) {
            panoramaLinks = '<div class="mt-4"><h5 class="font-medium text-gray-700 mb-2">360° Panoramalar</h5><div class="space-y-2">';
            building.panoramas.forEach(p => {
                panoramaLinks += `<a href="/campus-tour/panorama/${p.id}" class="block p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                    <div class="flex items-center">
                        <i class="fas fa-vr-cardboard text-blue-600 mr-3"></i>
                        <span class="text-sm">${p.title}</span>
                    </div>
                </a>`;
            });
            panoramaLinks += '</div></div>';
        }

        detailContent.innerHTML = `
            <div class="flex items-center mb-4">
                <div class="w-14 h-14 rounded-xl flex items-center justify-center mr-4" style="background-color: ${building.color}20;">
                    <i class="fas ${building.marker_icon} text-2xl" style="color: ${building.color}"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-800">${building.title}</h3>
                    <span class="text-gray-500">${building.category_label || ''}</span>
                </div>
            </div>
            ${building.description ? `<p class="text-gray-600 mb-4">${building.description}</p>` : ''}
            ${building.floor_count ? `<div class="flex items-center text-gray-600 mb-2"><i class="fas fa-layer-group mr-2"></i><span>${building.floor_count} qavat</span></div>` : ''}
            ${building.working_hours ? `<div class="flex items-center text-gray-600 mb-2"><i class="fas fa-clock mr-2"></i><span>${building.working_hours}</span></div>` : ''}
            ${building.phone ? `<div class="flex items-center text-gray-600 mb-2"><i class="fas fa-phone mr-2"></i><a href="tel:${building.phone}" class="text-blue-600">${building.phone}</a></div>` : ''}
            ${panoramaLinks}
        `;

        sidebar.classList.add('active');

        // Highlight marker
        highlightMarker(building.id);
    }

    function highlightMarker(buildingId) {
        @if($settings->map_type === 'image')
        document.querySelectorAll('.map-marker').forEach(m => {
            m.style.transform = m.dataset.id == buildingId
                ? 'translate(-50%, -100%) scale(1.3)'
                : 'translate(-50%, -100%)';
        });
        @else
        // For Leaflet, handled separately
        @endif
    }

    // Map marker clicks
    @if($settings->map_type === 'image')
    document.querySelectorAll('.map-marker').forEach(marker => {
        marker.addEventListener('click', function() {
            const buildingId = this.dataset.id;
            const building = buildings.find(b => b.id == buildingId);
            showBuildingDetail(building);
        });
    });
    @else
    // Initialize Leaflet map
    const map = L.map('leaflet-map').setView([{{ $settings->center_lat ?? 39.65 }}, {{ $settings->center_lng ?? 66.96 }}], {{ $settings->default_zoom ?? 17 }});

    L.tileLayer('{{ $settings->tile_url ?? "https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png" }}', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    // Add markers
    buildings.forEach(building => {
        if (building.latitude && building.longitude) {
            const icon = L.divIcon({
                html: `<div style="width: 40px; height: 40px; background: ${building.color}; border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 3px solid white; box-shadow: 0 2px 6px rgba(0,0,0,0.3);"><i class="fas ${building.marker_icon} text-white"></i></div>`,
                className: 'custom-marker',
                iconSize: [40, 40],
                iconAnchor: [20, 40]
            });

            const marker = L.marker([building.latitude, building.longitude], { icon: icon }).addTo(map);

            marker.bindPopup(`
                <div class="custom-popup">
                    <h4>${building.title}</h4>
                    <p>${building.description || building.category_label}</p>
                    ${building.panoramas && building.panoramas.length > 0
                        ? `<a href="/campus-tour/panorama/${building.panoramas[0].id}"><i class="fas fa-vr-cardboard mr-1"></i>360° ko'rish</a>`
                        : ''}
                </div>
            `);

            marker.on('click', function() {
                showBuildingDetail(building);
            });
        }
    });
    @endif
});
</script>
@endpush
