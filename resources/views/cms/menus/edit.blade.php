@extends('layouts.dashboard-new')

@section('title', 'Menyu tahrirlash - ' . $menu->name)
@section('page-title', 'Menyu tahrirlash')

@section('styles')
<style>
    .menu-item-card {
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        background: #fff;
        transition: all 0.2s;
    }
    .menu-item-card:hover {
        border-color: #4f46e5;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }
    .menu-item-card.child-item {
        margin-left: 2rem;
        border-left: 3px solid #4f46e5;
    }
    .section-header {
        background: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%);
        color: white;
        padding: 15px 20px;
        border-radius: 12px 12px 0 0;
    }
    .content-card {
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        background: #fff;
    }
    .drag-handle {
        cursor: grab;
        color: #9ca3af;
    }
    .drag-handle:hover {
        color: #4f46e5;
    }
</style>
@endsection

@section('content')

    {{-- Header --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div>
                    <h1 class="h3 mb-1 fw-bold">
                        <i class="fas fa-bars text-primary me-2"></i>{{ $menu->name }}
                    </h1>
                    <p class="text-muted mb-0">Menyu elementlarini tahrirlash va boshqarish</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('cms.menus.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Orqaga
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        {{-- Menu Settings --}}
        <div class="col-lg-4 mb-4">
            <div class="content-card">
                <div class="section-header">
                    <h5 class="mb-0"><i class="fas fa-cog me-2"></i>Menyu sozlamalari</h5>
                </div>
                <div class="p-4">
                    <form action="{{ route('cms.menus.update', $menu) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Menyu nomi</label>
                            <input type="text" name="name" value="{{ $menu->name }}" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Joylashuv</label>
                            <select name="location" class="form-select">
                                <option value="header" {{ $menu->location == 'header' ? 'selected' : '' }}>Header</option>
                                <option value="footer" {{ $menu->location == 'footer' ? 'selected' : '' }}>Footer</option>
                                <option value="sidebar" {{ $menu->location == 'sidebar' ? 'selected' : '' }}>Sidebar</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1" {{ $menu->is_active ? 'checked' : '' }}>
                                <label class="form-check-label">Faol</label>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-save me-1"></i> Saqlash
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Menu Items --}}
        <div class="col-lg-8 mb-4">
            <div class="content-card">
                <div class="section-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-list me-2"></i>Menyu elementlari</h5>
                    <button type="button" class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#addItemModal">
                        <i class="fas fa-plus me-1"></i> Yangi element
                    </button>
                </div>
                <div class="p-4">
                    @php
                        $rootItems = $menu->menuItems->whereNull('parent_id')->sortBy('order_position');
                    @endphp

                    @if($rootItems->count() > 0)
                        @foreach($rootItems as $item)
                            <div class="menu-item-card p-3 mb-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-grip-vertical drag-handle me-3"></i>
                                        <div>
                                            <strong>{{ $item->title_uz }}</strong>
                                            @if($item->title_en)
                                                <small class="text-muted ms-2">EN: {{ $item->title_en }}</small>
                                            @endif
                                            @if($item->title_ru)
                                                <small class="text-muted ms-2">RU: {{ $item->title_ru }}</small>
                                            @endif
                                            <br>
                                            <small class="text-muted"><i class="fas fa-link me-1"></i>{{ $item->getRawOriginal('url') }}</small>
                                            @if($item->children->count() > 0)
                                                <span class="badge bg-info ms-2">{{ $item->children->count() }} submenyu</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="d-flex gap-1">
                                        <button type="button" class="btn btn-sm btn-outline-primary"
                                                onclick="editItem({{ $item->id }}, '{{ addslashes($item->title_uz) }}', '{{ addslashes($item->title_en ?? '') }}', '{{ addslashes($item->title_ru ?? '') }}', '{{ addslashes($item->getRawOriginal('url')) }}', '{{ $item->icon ?? '' }}', {{ $item->parent_id ?? 'null' }}, {{ $item->order_position }})"
                                                title="Tahrirlash">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="{{ route('cms.menus.items.destroy', [$menu, $item]) }}" method="POST" class="d-inline"
                                              onsubmit="return confirm('O\'chirmoqchimisiz?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="O'chirish">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            {{-- Children --}}
                            @foreach($item->children->sortBy('order_position') as $child)
                                <div class="menu-item-card child-item p-3 mb-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-grip-vertical drag-handle me-3"></i>
                                            <div>
                                                <i class="fas fa-level-up-alt fa-rotate-90 text-muted me-1"></i>
                                                <strong>{{ $child->title_uz }}</strong>
                                                @if($child->title_en)
                                                    <small class="text-muted ms-2">EN: {{ $child->title_en }}</small>
                                                @endif
                                                @if($child->title_ru)
                                                    <small class="text-muted ms-2">RU: {{ $child->title_ru }}</small>
                                                @endif
                                                <br>
                                                <small class="text-muted"><i class="fas fa-link me-1"></i>{{ $child->getRawOriginal('url') }}</small>
                                            </div>
                                        </div>
                                        <div class="d-flex gap-1">
                                            <button type="button" class="btn btn-sm btn-outline-primary"
                                                    onclick="editItem({{ $child->id }}, '{{ addslashes($child->title_uz) }}', '{{ addslashes($child->title_en ?? '') }}', '{{ addslashes($child->title_ru ?? '') }}', '{{ addslashes($child->getRawOriginal('url')) }}', '{{ $child->icon ?? '' }}', {{ $child->parent_id ?? 'null' }}, {{ $child->order_position }})"
                                                    title="Tahrirlash">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form action="{{ route('cms.menus.items.destroy', [$menu, $child]) }}" method="POST" class="d-inline"
                                                  onsubmit="return confirm('O\'chirmoqchimisiz?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="O'chirish">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endforeach
                    @else
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                            <p class="mb-0">Menyu elementlari yo'q.</p>
                            <p class="small">Yangi element qo'shish uchun yuqoridagi tugmani bosing.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

{{-- Add Item Modal --}}
<div class="modal fade" id="addItemModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('cms.menus.items.store', $menu) }}" method="POST">
                @csrf
                <div class="modal-header" style="background: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%); color: white;">
                    <h5 class="modal-title"><i class="fas fa-plus-circle me-2"></i>Yangi element qo'shish</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nomi (O'zbekcha) <span class="text-danger">*</span></label>
                        <input type="text" name="title_uz" class="form-control" required placeholder="Masalan: Biz haqimizda">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nomi (English)</label>
                        <input type="text" name="title_en" class="form-control" placeholder="About Us">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nomi (Русский)</label>
                        <input type="text" name="title_ru" class="form-control" placeholder="О нас">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">URL <span class="text-danger">*</span></label>
                        <input type="text" name="url" class="form-control" required placeholder="/about">
                        <small class="text-muted">Ichki: /about | Tashqi: https://... | Submenyu uchun: #</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Sahifa tanlash (ixtiyoriy)</label>
                        <select name="page_id" class="form-select">
                            <option value="">-- Tanlang --</option>
                            @foreach($pages as $page)
                                <option value="{{ $page->id }}">{{ $page->title_uz }} ({{ $page->slug }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Parent menyu</label>
                        <select name="parent_id" class="form-select">
                            <option value="">-- Asosiy menyu (parent yo'q) --</option>
                            @foreach($rootItems as $rootItem)
                                <option value="{{ $rootItem->id }}">{{ $rootItem->title_uz }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tartib raqami</label>
                        <input type="number" name="order_position" class="form-control" value="{{ $menu->menuItems->count() + 1 }}" min="1">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Icon (ixtiyoriy)</label>
                        <input type="text" name="icon" class="form-control" placeholder="fas fa-home">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bekor qilish</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-plus me-1"></i> Qo'shish</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Edit Item Modal --}}
<div class="modal fade" id="editItemModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editItemForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header" style="background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); color: white;">
                    <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Elementni tahrirlash</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nomi (O'zbekcha) <span class="text-danger">*</span></label>
                        <input type="text" name="title_uz" id="edit_title_uz" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nomi (English)</label>
                        <input type="text" name="title_en" id="edit_title_en" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nomi (Русский)</label>
                        <input type="text" name="title_ru" id="edit_title_ru" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">URL <span class="text-danger">*</span></label>
                        <input type="text" name="url" id="edit_url" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Parent menyu</label>
                        <select name="parent_id" id="edit_parent_id" class="form-select">
                            <option value="">-- Asosiy menyu (parent yo'q) --</option>
                            @foreach($rootItems as $rootItem)
                                <option value="{{ $rootItem->id }}">{{ $rootItem->title_uz }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tartib raqami</label>
                        <input type="number" name="order_position" id="edit_order" class="form-control" min="1">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Icon</label>
                        <input type="text" name="icon" id="edit_icon" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bekor qilish</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Saqlash</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function editItem(id, titleUz, titleEn, titleRu, url, icon, parentId, order) {
        document.getElementById('editItemForm').action = '{{ url("cms/menus/" . $menu->id . "/items") }}/' + id;
        document.getElementById('edit_title_uz').value = titleUz;
        document.getElementById('edit_title_en').value = titleEn;
        document.getElementById('edit_title_ru').value = titleRu;
        document.getElementById('edit_url').value = url;
        document.getElementById('edit_icon').value = icon || '';
        document.getElementById('edit_order').value = order;

        var parentSelect = document.getElementById('edit_parent_id');
        parentSelect.value = parentId || '';

        new bootstrap.Modal(document.getElementById('editItemModal')).show();
    }
</script>
@endsection
