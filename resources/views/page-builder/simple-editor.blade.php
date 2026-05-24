@extends('layouts.app')

@section('content')
<div class="page-builder-container">
    <!-- Top Toolbar -->
    <div class="pb-toolbar bg-white border-bottom p-3">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-auto">
                    <a href="{{ route('page-builder.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>
                <div class="col">
                    <input type="text" id="page-title" class="form-control form-control-lg border-0" 
                           placeholder="Page Title" value="{{ $page->title ?? '' }}">
                </div>
                <div class="col-auto">
                    <button class="btn btn-primary" onclick="savePage()">
                        <i class="fas fa-save"></i> Save
                    </button>
                    <button class="btn btn-success" onclick="publishPage()">
                        <i class="fas fa-globe"></i> Publish
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex" style="height: calc(100vh - 130px);">
        <!-- Left Sidebar - Elements -->
        <div class="pb-sidebar bg-light border-end" style="width: 300px; overflow-y: auto;">
            <div class="p-3">
                <h5>Elements</h5>
                
                <div class="element-category mb-3">
                    <h6 class="text-muted">Basic</h6>
                    <div class="row g-2">
                        <div class="col-6">
                            <div class="element-item" draggable="true" data-element="text">
                                <i class="fas fa-font"></i>
                                <span>Text</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="element-item" draggable="true" data-element="heading">
                                <i class="fas fa-heading"></i>
                                <span>Heading</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="element-item" draggable="true" data-element="button">
                                <i class="fas fa-square"></i>
                                <span>Button</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="element-item" draggable="true" data-element="image">
                                <i class="fas fa-image"></i>
                                <span>Image</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="element-item" draggable="true" data-element="divider">
                                <i class="fas fa-minus"></i>
                                <span>Divider</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="element-item" draggable="true" data-element="spacer">
                                <i class="fas fa-arrows-alt-v"></i>
                                <span>Spacer</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="element-category mb-3">
                    <h6 class="text-muted">Layout</h6>
                    <div class="row g-2">
                        <div class="col-12">
                            <div class="element-item" draggable="true" data-element="row">
                                <i class="fas fa-columns"></i>
                                <span>Row/Columns</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Canvas Area -->
        <div class="pb-canvas flex-fill bg-white p-4" style="overflow-y: auto;">
            <div class="container">
                <div id="canvas-content" class="min-vh-100">
                    @if($page && $page->sections)
                        @foreach($page->sections as $section)
                        <div class="pb-section" data-section-id="{{ $section->id }}">
                            <div class="row">
                                @foreach($section->columns as $column)
                                <div class="col-md-{{ $column->width }}">
                                    @foreach($column->elements as $element)
                                    <div class="pb-element-wrapper">
                                        {!! renderElement($element) !!}
                                    </div>
                                    @endforeach
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    @else
                    <div class="empty-state text-center py-5">
                        <i class="fas fa-plus-circle fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Drag elements here to start building</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Sidebar - Properties -->
        <div class="pb-properties bg-light border-start" style="width: 300px; display: none;">
            <div class="p-3">
                <h5>Properties</h5>
                <div id="properties-content">
                    <!-- Dynamic properties will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.page-builder-container {
    height: 100vh;
    overflow: hidden;
}

.element-item {
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    padding: 10px;
    text-align: center;
    cursor: move;
    transition: all 0.2s;
}

.element-item:hover {
    background: #007bff;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.element-item i {
    display: block;
    font-size: 20px;
    margin-bottom: 5px;
}

.element-item span {
    font-size: 12px;
}

.pb-section {
    border: 2px dashed transparent;
    padding: 20px;
    margin-bottom: 20px;
    min-height: 100px;
    transition: all 0.2s;
}

.pb-section:hover {
    border-color: #007bff;
    background: rgba(0,123,255,0.05);
}

.pb-element-wrapper {
    position: relative;
    padding: 10px;
    margin-bottom: 10px;
    border: 1px solid transparent;
    cursor: move;
}

.pb-element-wrapper:hover {
    border-color: #28a745;
    background: rgba(40,167,69,0.05);
}

.pb-element-wrapper:hover::before {
    content: attr(data-element-type);
    position: absolute;
    top: -20px;
    left: 0;
    background: #28a745;
    color: white;
    padding: 2px 8px;
    font-size: 11px;
    border-radius: 3px;
}

.drag-over {
    background: rgba(0,123,255,0.1);
    border: 2px dashed #007bff;
}

.empty-state {
    border: 2px dashed #dee2e6;
    border-radius: 8px;
}
</style>

<script>
let pageId = {{ $page->id ?? 'null' }};
let sections = [];

// Initialize drag and drop
document.addEventListener('DOMContentLoaded', function() {
    initializeDragDrop();
});

function initializeDragDrop() {
    // Make elements draggable
    const elements = document.querySelectorAll('.element-item');
    elements.forEach(el => {
        el.addEventListener('dragstart', handleDragStart);
        el.addEventListener('dragend', handleDragEnd);
    });

    // Make canvas droppable
    const canvas = document.getElementById('canvas-content');
    canvas.addEventListener('dragover', handleDragOver);
    canvas.addEventListener('drop', handleDrop);
    canvas.addEventListener('dragleave', handleDragLeave);
}

let draggedElement = null;

function handleDragStart(e) {
    draggedElement = {
        type: e.target.dataset.element,
        isNew: true
    };
    e.target.style.opacity = '0.5';
}

function handleDragEnd(e) {
    e.target.style.opacity = '';
}

function handleDragOver(e) {
    e.preventDefault();
    e.currentTarget.classList.add('drag-over');
}

function handleDragLeave(e) {
    e.currentTarget.classList.remove('drag-over');
}

function handleDrop(e) {
    e.preventDefault();
    e.currentTarget.classList.remove('drag-over');
    
    if (draggedElement && draggedElement.isNew) {
        addElement(draggedElement.type);
    }
}

function addElement(type) {
    const canvas = document.getElementById('canvas-content');
    
    // Remove empty state if exists
    const emptyState = canvas.querySelector('.empty-state');
    if (emptyState) {
        emptyState.remove();
    }
    
    let elementHtml = '';
    
    switch(type) {
        case 'text':
            elementHtml = '<div class="pb-element-wrapper" data-element-type="Text"><p contenteditable="true">Click to edit text</p></div>';
            break;
        case 'heading':
            elementHtml = '<div class="pb-element-wrapper" data-element-type="Heading"><h2 contenteditable="true">Heading</h2></div>';
            break;
        case 'button':
            elementHtml = '<div class="pb-element-wrapper" data-element-type="Button"><button class="btn btn-primary">Button</button></div>';
            break;
        case 'image':
            elementHtml = '<div class="pb-element-wrapper" data-element-type="Image"><img src="https://via.placeholder.com/800x400" class="img-fluid"></div>';
            break;
        case 'divider':
            elementHtml = '<div class="pb-element-wrapper" data-element-type="Divider"><hr></div>';
            break;
        case 'spacer':
            elementHtml = '<div class="pb-element-wrapper" data-element-type="Spacer"><div style="height: 50px;"></div></div>';
            break;
        case 'row':
            elementHtml = `
                <div class="pb-section">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="drop-zone" style="min-height: 100px; border: 1px dashed #ccc; padding: 20px;">
                                Drop elements here
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="drop-zone" style="min-height: 100px; border: 1px dashed #ccc; padding: 20px;">
                                Drop elements here
                            </div>
                        </div>
                    </div>
                </div>
            `;
            break;
    }
    
    canvas.insertAdjacentHTML('beforeend', elementHtml);
}

function savePage() {
    const title = document.getElementById('page-title').value;
    const content = document.getElementById('canvas-content').innerHTML;
    
    const data = {
        title: title,
        content: content,
        _token: '{{ csrf_token() }}'
    };
    
    const url = pageId ? `/admin/page-builder/pages/${pageId}` : '/admin/page-builder/pages';
    const method = pageId ? 'PUT' : 'POST';
    
    fetch(url, {
        method: method,
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Page saved successfully!');
            if (!pageId && data.redirect) {
                window.location.href = data.redirect;
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error saving page');
    });
}

function publishPage() {
    if (!pageId) {
        alert('Please save the page first');
        return;
    }
    
    fetch(`/admin/page-builder/pages/${pageId}/publish`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Page published successfully!');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error publishing page');
    });
}
</script>
@endsection