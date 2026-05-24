@extends('layouts.frontend')

@section('title', $panorama->title . ' - 360° Virtual Tur')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/pannellum@2.5.6/build/pannellum.css"/>
<style>
    #panorama-viewer {
        width: 100%;
        height: calc(100vh - 80px);
        min-height: 500px;
        background: #1a1a1a;
    }
    .panorama-info-overlay {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);
        padding: 60px 24px 24px;
        z-index: 100;
        pointer-events: none;
    }
    .panorama-info-overlay * {
        pointer-events: auto;
    }
    .panorama-controls {
        position: absolute;
        top: 20px;
        right: 20px;
        z-index: 100;
        display: flex;
        gap: 8px;
    }
    .control-btn {
        width: 44px;
        height: 44px;
        background: rgba(255,255,255,0.95);
        border: none;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        color: #333;
        cursor: pointer;
        transition: all 0.2s ease;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    .control-btn:hover {
        background: white;
        transform: scale(1.05);
    }
    .back-btn {
        position: absolute;
        top: 20px;
        left: 20px;
        z-index: 100;
        display: flex;
        align-items: center;
        gap: 8px;
        background: rgba(255,255,255,0.95);
        padding: 10px 20px;
        border-radius: 50px;
        font-weight: 500;
        color: #333;
        text-decoration: none;
        transition: all 0.2s ease;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    .back-btn:hover {
        background: white;
        transform: translateX(-3px);
    }
    .hotspot-tooltip {
        background: rgba(0,0,0,0.8);
        color: white;
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 14px;
        white-space: nowrap;
    }
    .pnlm-hotspot {
        width: 40px !important;
        height: 40px !important;
    }
    .pnlm-hotspot.pnlm-info {
        background: rgba(0,102,204,0.9) !important;
        border: 2px solid white !important;
        border-radius: 50% !important;
    }
    .pnlm-hotspot.pnlm-scene {
        background: rgba(255,152,0,0.9) !important;
        border: 2px solid white !important;
        border-radius: 50% !important;
    }
    .related-panoramas {
        position: absolute;
        bottom: 120px;
        left: 24px;
        z-index: 100;
        display: flex;
        gap: 12px;
    }
    .related-item {
        width: 120px;
        background: rgba(255,255,255,0.95);
        border-radius: 12px;
        overflow: hidden;
        text-decoration: none;
        transition: all 0.2s ease;
        box-shadow: 0 2px 10px rgba(0,0,0,0.2);
    }
    .related-item:hover {
        transform: scale(1.05);
    }
    .related-item img {
        width: 100%;
        height: 70px;
        object-fit: cover;
    }
    .related-item span {
        display: block;
        padding: 8px;
        font-size: 12px;
        font-weight: 500;
        color: #333;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    /* Mobile adjustments */
    @media (max-width: 768px) {
        .related-panoramas {
            display: none;
        }
        .panorama-info-overlay {
            padding: 40px 16px 16px;
        }
    }
</style>
@endpush

@section('content')
<div class="relative">
    <!-- Panorama Viewer -->
    <div id="panorama-viewer"></div>

    <!-- Back Button -->
    <a href="{{ route('campus-tour.public.virtual-tour') }}" class="back-btn">
        <i class="fas fa-arrow-left"></i>
        <span>Orqaga</span>
    </a>

    <!-- Controls -->
    <div class="panorama-controls">
        <button class="control-btn" id="zoomIn" title="Yaqinlashtirish">
            <i class="fas fa-search-plus"></i>
        </button>
        <button class="control-btn" id="zoomOut" title="Uzoqlashtirish">
            <i class="fas fa-search-minus"></i>
        </button>
        <button class="control-btn" id="fullscreen" title="To'liq ekran">
            <i class="fas fa-expand"></i>
        </button>
        <button class="control-btn" id="autoRotate" title="Avto aylanish">
            <i class="fas fa-sync-alt"></i>
        </button>
    </div>

    <!-- Related Panoramas -->
    @if($relatedPanoramas->count() > 0)
        <div class="related-panoramas">
            @foreach($relatedPanoramas as $related)
                <a href="{{ route('campus-tour.public.panorama', $related) }}" class="related-item">
                    <img src="{{ $related->thumbnail_url }}" alt="{{ $related->title }}">
                    <span>{{ $related->title }}</span>
                </a>
            @endforeach
        </div>
    @endif

    <!-- Info Overlay -->
    <div class="panorama-info-overlay">
        <div class="max-w-7xl mx-auto">
            <h1 class="text-2xl md:text-3xl font-bold text-white mb-2">{{ $panorama->title }}</h1>
            @if($panorama->building)
                <div class="flex items-center text-white/80 mb-3">
                    <i class="fas {{ $panorama->building->marker_icon }} mr-2"></i>
                    <span>{{ $panorama->building->title }}</span>
                </div>
            @endif
            @if($panorama->description)
                <p class="text-white/70 max-w-2xl">{{ $panorama->description }}</p>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/pannellum@2.5.6/build/pannellum.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Hotspots configuration
    const hotspots = @json($panorama->hotspots ?? []);

    // Build hotspots array for Pannellum
    const pannellumHotspots = hotspots.map(hotspot => {
        const config = {
            pitch: parseFloat(hotspot.pitch) || 0,
            yaw: parseFloat(hotspot.yaw) || 0,
            type: hotspot.type || 'info',
            text: hotspot.text || ''
        };

        if (hotspot.type === 'scene' && hotspot.scene_id) {
            config.sceneId = hotspot.scene_id;
        }

        if (hotspot.url) {
            config.URL = hotspot.url;
        }

        return config;
    });

    // Initialize Pannellum
    const viewer = pannellum.viewer('panorama-viewer', {
        type: 'equirectangular',
        panorama: '{{ $panorama->image_url }}',
        autoLoad: true,
        compass: true,
        showControls: false,
        mouseZoom: true,
        draggable: true,
        hfov: 110,
        minHfov: 50,
        maxHfov: 120,
        pitch: {{ $panorama->initial_pitch ?? 0 }},
        yaw: {{ $panorama->initial_yaw ?? 0 }},
        hotSpots: pannellumHotspots,
        hotSpotDebug: false,
        strings: {
            loadButtonLabel: "Yuklash",
            loadingLabel: "Yuklanmoqda...",
            bylineLabel: "Tourism Academy",
            noPanoramaError: "Panorama topilmadi.",
            fileAccessError: "Faylga kirish imkonsiz.",
            malformedURLError: "Noto'g'ri havola.",
            iOS8WebGLError: "WebGL qo'llab-quvvatlanmaydi.",
            genericWebGLError: "Brauzeringiz WebGL ni qo'llab-quvvatlamaydi."
        }
    });

    // Control buttons
    document.getElementById('zoomIn').addEventListener('click', function() {
        viewer.setHfov(viewer.getHfov() - 10);
    });

    document.getElementById('zoomOut').addEventListener('click', function() {
        viewer.setHfov(viewer.getHfov() + 10);
    });

    document.getElementById('fullscreen').addEventListener('click', function() {
        viewer.toggleFullscreen();
    });

    let autoRotating = false;
    document.getElementById('autoRotate').addEventListener('click', function() {
        autoRotating = !autoRotating;
        if (autoRotating) {
            viewer.startAutoRotate(2);
            this.classList.add('text-blue-600');
        } else {
            viewer.stopAutoRotate();
            this.classList.remove('text-blue-600');
        }
    });

    // Stop auto-rotate on user interaction
    viewer.on('mousedown', function() {
        if (autoRotating) {
            viewer.stopAutoRotate();
            autoRotating = false;
            document.getElementById('autoRotate').classList.remove('text-blue-600');
        }
    });

    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        switch(e.key) {
            case '+':
            case '=':
                viewer.setHfov(viewer.getHfov() - 10);
                break;
            case '-':
                viewer.setHfov(viewer.getHfov() + 10);
                break;
            case 'f':
            case 'F':
                viewer.toggleFullscreen();
                break;
            case 'r':
            case 'R':
                document.getElementById('autoRotate').click();
                break;
        }
    });
});
</script>
@endpush
