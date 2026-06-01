@extends('layouts.dashboard-new')

@section('title', 'Vizual tahrirlash - ' . $page->title_uz)
@section('page-title', 'Vizual sahifa yaratish')

@push('styles')
<link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/all.min.css') }}">
<link rel="stylesheet" href="{{ asset('vendor/animatecss/animate.min.css') }}">
<style>
    .builder-header {
        background: #fff;
        border-bottom: 1px solid #dee2e6;
        padding: 10px 20px;
        position: sticky;
        top: 0;
        z-index: 100;
    }
    
    .builder-container {
        display: flex;
        height: calc(100vh - 120px);
        background: #f5f5f5;
    }
    
    .builder-sidebar {
        width: 300px;
        background: #fff;
        border-right: 1px solid #dee2e6;
        overflow-y: auto;
    }
    
    .builder-canvas {
        flex: 1;
        background: #f8f9fa;
        overflow-y: auto;
        position: relative;
    }
    
    .style-panel {
        width: 350px;
        background: #fff;
        border-left: 1px solid #dee2e6;
        padding: 20px;
        overflow-y: auto;
        display: none;
        position: relative;
    }
    
    .style-panel.active {
        display: block;
    }
    
    .close-panel {
        position: absolute;
        top: 10px;
        right: 10px;
        cursor: pointer;
        font-size: 1.2rem;
    }
    
    #canvas {
        background: #fff;
        margin: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        transition: all 0.3s;
    }
    
    #dropZone {
        min-height: 500px;
        padding: 30px;
        border: 2px dashed transparent;
        transition: all 0.3s;
    }
    
    #dropZone.drag-over {
        background: #e7f3ff;
        border-color: #007bff;
    }
    
    #dropZone.empty {
        border-color: #dee2e6;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #6c757d;
    }
    
    .element-item {
        padding: 12px;
        margin: 8px;
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        cursor: move;
        transition: all 0.3s;
        text-align: center;
    }
    
    .element-item:hover {
        background: #007bff;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,123,255,0.3);
    }
    
    .element-item i {
        display: block;
        font-size: 1.5rem;
        margin-bottom: 5px;
    }
    
    .builder-element {
        position: relative;
        margin: 15px 0;
        padding: 20px;
        background: #fff;
        border: 2px solid transparent;
        border-radius: 6px;
        cursor: move;
        transition: all 0.3s;
    }
    
    .builder-element:hover {
        border-color: #dee2e6;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    
    .builder-element.selected {
        border-color: #007bff;
        box-shadow: 0 0 0 4px rgba(0,123,255,0.1);
    }
    
    .element-toolbar {
        position: absolute;
        top: -40px;
        right: 0;
        background: #333;
        border-radius: 6px;
        padding: 5px;
        display: none;
        z-index: 100;
        box-shadow: 0 2px 8px rgba(0,0,0,0.2);
    }
    
    .builder-element:hover .element-toolbar,
    .builder-element.selected .element-toolbar {
        display: flex;
        gap: 5px;
    }
    
    .btn-element-action {
        background: transparent;
        border: none;
        color: white;
        padding: 5px 10px;
        cursor: pointer;
        transition: background 0.3s;
        border-radius: 4px;
    }
    
    .btn-element-action:hover {
        background: rgba(255,255,255,0.2);
    }
    
    .btn-element-action.text-danger {
        color: #ff6b6b;
    }
    
    .element-handle {
        position: absolute;
        left: -12px;
        top: 50%;
        transform: translateY(-50%);
        width: 24px;
        height: 40px;
        background: #007bff;
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        cursor: grab;
        opacity: 0;
        transition: opacity 0.3s;
    }
    
    .builder-element:hover .element-handle {
        opacity: 1;
    }
    
    .dragging {
        opacity: 0.5;
    }
    
    .dragging-placeholder {
        border: 3px dashed #007bff;
        background: #e7f3ff;
        height: 100px;
        margin: 15px 0;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .column-dropzone {
        min-height: 150px;
        border: 2px dashed #dee2e6;
        padding: 20px;
        border-radius: 6px;
        background: #fafafa;
        position: relative;
    }
    
    .column-dropzone:hover {
        border-color: #007bff;
        background: #f0f8ff;
    }
    
    .column-placeholder {
        color: #adb5bd;
        text-align: center;
        padding: 40px;
    }
    
    .device-mobile #canvas {
        max-width: 375px;
        margin: 20px auto;
    }
    
    .device-tablet #canvas {
        max-width: 768px;
        margin: 20px auto;
    }
    
    .tab-content {
        padding: 15px;
    }
    
    .style-group {
        margin-bottom: 20px;
    }
    
    .style-group label {
        display: block;
        margin-bottom: 5px;
        font-weight: 500;
        color: #495057;
    }
    
    .editable-text,
    .editable-heading,
    .editable-button {
        outline: none;
    }
    
    .editable-text:focus,
    .editable-heading:focus,
    .editable-button:focus {
        box-shadow: 0 0 0 3px rgba(0,123,255,0.2);
        border-radius: 4px;
    }
    
    .countdown-timer {
        display: flex;
        gap: 20px;
        justify-content: center;
        padding: 20px;
    }
    
    .countdown-item {
        text-align: center;
        background: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        min-width: 80px;
    }
    
    .countdown-value {
        display: block;
        font-size: 2.5em;
        font-weight: bold;
        color: #007bff;
    }
    
    .countdown-label {
        display: block;
        font-size: 0.9em;
        color: #6c757d;
        margin-top: 5px;
    }
    
    .social-icons {
        display: flex;
        gap: 15px;
        justify-content: center;
        padding: 20px;
    }
    
    .social-icon {
        font-size: 2em;
        color: #6c757d;
        transition: all 0.3s;
    }
    
    .social-icon:hover {
        color: #007bff;
        transform: translateY(-3px);
    }
    
    .progress-wrapper {
        padding: 20px;
    }
    
    .progress-label {
        display: block;
        margin-bottom: 10px;
        font-weight: 500;
    }
    
    .chart-container {
        padding: 20px;
        background: #fff;
    }
    
    .video-wrapper {
        position: relative;
        padding-bottom: 56.25%;
        height: 0;
        overflow: hidden;
    }
    
    .video-wrapper iframe {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
    }
    
    .element-divider {
        margin: 20px 0;
        border: none;
        border-top: 2px solid #dee2e6;
    }
    
    .element-spacer {
        display: block;
    }
    
    .element-icon {
        text-align: center;
        padding: 20px;
    }
</style>
@endpush

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="page-id" content="{{ $page->id }}">

<!-- Builder Header -->
<div class="builder-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h5 class="mb-0">{{ $page->title_uz }}</h5>
            <small class="text-muted">Vizual tahrirlash rejimi</small>
        </div>
        
        <div class="d-flex gap-2 align-items-center">
            <!-- Device Preview -->
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-outline-secondary btn-sm active" data-device="desktop">
                    <i class="fas fa-desktop"></i>
                </button>
                <button type="button" class="btn btn-outline-secondary btn-sm" data-device="tablet">
                    <i class="fas fa-tablet-alt"></i>
                </button>
                <button type="button" class="btn btn-outline-secondary btn-sm" data-device="mobile">
                    <i class="fas fa-mobile-alt"></i>
                </button>
            </div>
            
            <!-- Actions -->
            <div class="btn-group ms-3" role="group">
                <button type="button" class="btn btn-outline-secondary btn-sm" id="undoBtn" disabled>
                    <i class="fas fa-undo"></i> Bekor
                </button>
                <button type="button" class="btn btn-outline-secondary btn-sm" id="redoBtn" disabled>
                    <i class="fas fa-redo"></i> Qayta
                </button>
            </div>
            
            <button type="button" class="btn btn-outline-danger btn-sm ms-2" id="clearBtn">
                <i class="fas fa-trash"></i> Tozalash
            </button>
            
            <button type="button" class="btn btn-info btn-sm ms-2" id="previewBtn">
                <i class="fas fa-eye"></i> Ko'rish
            </button>
            
            <button type="button" class="btn btn-primary btn-sm ms-2" id="saveBtn">
                <i class="fas fa-save"></i> Saqlash
            </button>
            
            <a href="{{ route('cms.pages.edit', $page) }}" class="btn btn-secondary btn-sm ms-2">
                <i class="fas fa-arrow-left"></i> Orqaga
            </a>
        </div>
    </div>
</div>

<!-- Builder Container -->
<div class="builder-container">
    <!-- Sidebar -->
    <div class="builder-sidebar">
        <ul class="nav nav-tabs" role="tablist">
            <li class="nav-item flex-fill">
                <a class="nav-link active" data-bs-toggle="tab" href="#elements">
                    <i class="fas fa-shapes"></i> Elementlar
                </a>
            </li>
            <li class="nav-item flex-fill">
                <a class="nav-link" data-bs-toggle="tab" href="#layouts">
                    <i class="fas fa-th-large"></i> Joylashuv
                </a>
            </li>
            <li class="nav-item flex-fill">
                <a class="nav-link" data-bs-toggle="tab" href="#blocks">
                    <i class="fas fa-cubes"></i> Bloklar
                </a>
            </li>
        </ul>
        
        <div class="tab-content">
            <!-- Elements Tab -->
            <div class="tab-pane fade show active" id="elements">
                <div class="p-3">
                    <h6 class="text-muted mb-3">Asosiy elementlar</h6>
                    
                    <div class="row g-2">
                        <div class="col-6">
                            <div class="element-item" data-element="text">
                                <i class="fas fa-align-left"></i>
                                <small>Matn</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="element-item" data-element="heading">
                                <i class="fas fa-heading"></i>
                                <small>Sarlavha</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="element-item" data-element="image">
                                <i class="fas fa-image"></i>
                                <small>Rasm</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="element-item" data-element="video">
                                <i class="fas fa-video"></i>
                                <small>Video</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="element-item" data-element="button">
                                <i class="fas fa-square"></i>
                                <small>Tugma</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="element-item" data-element="divider">
                                <i class="fas fa-minus"></i>
                                <small>Ajratuvchi</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="element-item" data-element="spacer">
                                <i class="fas fa-arrows-alt-v"></i>
                                <small>Bo'shliq</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="element-item" data-element="icon">
                                <i class="fas fa-icons"></i>
                                <small>Ikonka</small>
                            </div>
                        </div>
                    </div>
                    
                    <h6 class="text-muted mb-3 mt-4">Interaktiv elementlar</h6>
                    
                    <div class="row g-2">
                        <div class="col-6">
                            <div class="element-item" data-element="accordion">
                                <i class="fas fa-chevron-down"></i>
                                <small>Akkordeon</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="element-item" data-element="tabs">
                                <i class="fas fa-folder"></i>
                                <small>Tablar</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="element-item" data-element="progressbar">
                                <i class="fas fa-tasks"></i>
                                <small>Progress</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="element-item" data-element="countdown">
                                <i class="fas fa-clock"></i>
                                <small>Taymer</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="element-item" data-element="chart">
                                <i class="fas fa-chart-bar"></i>
                                <small>Grafik</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="element-item" data-element="social">
                                <i class="fas fa-share-alt"></i>
                                <small>Ijtimoiy</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="element-item" data-element="quote">
                                <i class="fas fa-quote-right"></i>
                                <small>Iqtibos</small>
                            </div>
                        </div>
                    </div>

                    <h6 class="text-muted mb-3 mt-4">Kengaytirilgan elementlar</h6>

                    <div class="row g-2">
                        <div class="col-6">
                            <div class="element-item" data-element="card">
                                <i class="fas fa-id-card"></i>
                                <small>Kartochka</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="element-item" data-element="testimonial">
                                <i class="fas fa-comment-dots"></i>
                                <small>Testimonial</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="element-item" data-element="team">
                                <i class="fas fa-user-tie"></i>
                                <small>Jamoa</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="element-item" data-element="pricing">
                                <i class="fas fa-tags"></i>
                                <small>Narx</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="element-item" data-element="gallery">
                                <i class="fas fa-images"></i>
                                <small>Galereya</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="element-item" data-element="faq">
                                <i class="fas fa-question-circle"></i>
                                <small>FAQ</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="element-item" data-element="contactform">
                                <i class="fas fa-envelope"></i>
                                <small>Forma</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="element-item" data-element="map">
                                <i class="fas fa-map-marker-alt"></i>
                                <small>Xarita</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="element-item" data-element="alert">
                                <i class="fas fa-exclamation-triangle"></i>
                                <small>Bildirishnoma</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="element-item" data-element="list">
                                <i class="fas fa-list"></i>
                                <small>Ro'yxat</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Layouts Tab -->
            <div class="tab-pane fade" id="layouts">
                <div class="p-3">
                    <h6 class="text-muted mb-3">Ustun joylashuvi</h6>

                    <div class="row g-2">
                        <div class="col-12">
                            <div class="element-item" data-element="row1">
                                <i class="fas fa-square"></i>
                                <small>1 ustun (100%)</small>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="element-item" data-element="row2">
                                <i class="fas fa-columns"></i>
                                <small>2 ustun (50/50)</small>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="element-item" data-element="row3">
                                <i class="fas fa-th"></i>
                                <small>3 ustun (33/33/33)</small>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="element-item" data-element="row4">
                                <i class="fas fa-th-large"></i>
                                <small>4 ustun (25/25/25/25)</small>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="element-item" data-element="row-sidebar-left">
                                <i class="fas fa-indent"></i>
                                <small>Sidebar chap (25/75)</small>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="element-item" data-element="row-sidebar-right">
                                <i class="fas fa-outdent"></i>
                                <small>Sidebar o'ng (75/25)</small>
                            </div>
                        </div>
                    </div>

                    <h6 class="text-muted mb-3 mt-4">Section</h6>
                    <div class="row g-2">
                        <div class="col-12">
                            <div class="element-item" data-element="section">
                                <i class="fas fa-layer-group"></i>
                                <small>Bo'lim (Section)</small>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="element-item" data-element="container">
                                <i class="fas fa-box"></i>
                                <small>Konteyner</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Blocks Tab -->
            <div class="tab-pane fade" id="blocks">
                <div class="p-3">
                    <h6 class="text-muted mb-3">Hero bloklari</h6>
                    <div class="row g-2">
                        <div class="col-12">
                            <div class="element-item block-item" data-block="hero-1">
                                <i class="fas fa-home"></i>
                                <small>Hero - Markaziy</small>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="element-item block-item" data-block="hero-2">
                                <i class="fas fa-image"></i>
                                <small>Hero - Rasmli</small>
                            </div>
                        </div>
                    </div>

                    <h6 class="text-muted mb-3 mt-4">Xizmatlar</h6>
                    <div class="row g-2">
                        <div class="col-12">
                            <div class="element-item block-item" data-block="services-1">
                                <i class="fas fa-cogs"></i>
                                <small>Xizmatlar 3 ustun</small>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="element-item block-item" data-block="features-1">
                                <i class="fas fa-star"></i>
                                <small>Xususiyatlar</small>
                            </div>
                        </div>
                    </div>

                    <h6 class="text-muted mb-3 mt-4">Aloqa</h6>
                    <div class="row g-2">
                        <div class="col-12">
                            <div class="element-item block-item" data-block="contact-1">
                                <i class="fas fa-envelope"></i>
                                <small>Aloqa formasi</small>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="element-item block-item" data-block="cta-1">
                                <i class="fas fa-bullhorn"></i>
                                <small>CTA blok</small>
                            </div>
                        </div>
                    </div>

                    <h6 class="text-muted mb-3 mt-4">Jamoa</h6>
                    <div class="row g-2">
                        <div class="col-12">
                            <div class="element-item block-item" data-block="team-1">
                                <i class="fas fa-users"></i>
                                <small>Jamoa - Grid</small>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="element-item block-item" data-block="testimonials-1">
                                <i class="fas fa-quote-left"></i>
                                <small>Testimoniallar</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Canvas -->
    <div class="builder-canvas">
        <div id="canvas" class="device-desktop">
            <div id="dropZone" class="empty">
                <div class="text-center">
                    <i class="fas fa-layer-group fa-3x mb-3"></i>
                    <h5>Elementlarni bu yerga tashlang</h5>
                    <p class="text-muted">Chap paneldan elementlarni tanlab, bu yerga sudrab keling</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Style Panel -->
    <div id="stylePanel" class="style-panel">
        <button class="close-panel">&times;</button>
        <h5 class="mb-3">Element sozlamalari</h5>
        <p class="text-muted small">Tanlangan: <span id="selectedElementType">-</span></p>
        
        <div class="style-group">
            <label>Orqa fon rangi</label>
            <input type="color" id="bgColor" class="form-control form-control-color">
        </div>
        
        <div class="style-group">
            <label>Matn rangi</label>
            <input type="color" id="textColor" class="form-control form-control-color">
        </div>
        
        <div class="style-group">
            <label>Shrift o'lchami</label>
            <input type="range" id="fontSize" class="form-range" min="12" max="72" value="16">
            <small class="text-muted"><span id="fontSizeValue">16</span>px</small>
        </div>
        
        <div class="style-group">
            <label>Padding</label>
            <input type="range" id="padding" class="form-range" min="0" max="100" value="20">
            <small class="text-muted"><span id="paddingValue">20</span>px</small>
        </div>
        
        <div class="style-group">
            <label>Margin</label>
            <input type="range" id="margin" class="form-range" min="0" max="100" value="10">
            <small class="text-muted"><span id="marginValue">10</span>px</small>
        </div>
        
        <div class="style-group">
            <label>Border Radius</label>
            <input type="range" id="borderRadius" class="form-range" min="0" max="50" value="0">
            <small class="text-muted"><span id="borderRadiusValue">0</span>px</small>
        </div>
        
        <div class="style-group">
            <label>Animatsiya</label>
            <select id="animation" class="form-select">
                <option value="">Animatsiyasiz</option>
                <option value="fadeIn">Fade In</option>
                <option value="fadeInUp">Fade In Up</option>
                <option value="fadeInDown">Fade In Down</option>
                <option value="slideInLeft">Slide In Left</option>
                <option value="slideInRight">Slide In Right</option>
                <option value="zoomIn">Zoom In</option>
                <option value="bounceIn">Bounce In</option>
            </select>
        </div>
        
        <div class="style-group">
            <label>Custom CSS</label>
            <textarea id="customCss" class="form-control" rows="4" placeholder="custom styles..."></textarea>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('vendor/sortablejs/Sortable.min.js') }}"></script>
<script src="{{ asset('vendor/chartjs/chart.umd.min.js') }}"></script>
<script src="/js/page-builder.js"></script>
<script>
    // Update range input values
    document.querySelectorAll('input[type="range"]').forEach(input => {
        const valueSpan = document.getElementById(input.id + 'Value');
        if (valueSpan) {
            input.addEventListener('input', () => {
                valueSpan.textContent = input.value;
            });
        }
    });
</script>
@endpush