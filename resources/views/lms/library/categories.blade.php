@extends('layouts.dashboard-new')

@section('title', 'Kutubxona kataloglari — LMS')
@section('page-title', 'Kutubxona kataloglari')

@section('content')

<x-lms-alerts />

{{-- Header --}}
<div class="card mb-4">
    <div class="card-body py-2">
        <div class="d-flex align-items-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('lms.library.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i>Kutubxona
                </a>
                <div style="width:1px;height:24px;background:var(--c-border)"></div>
                <div style="font-size:14px;font-weight:700;color:var(--c-text)">Kataloglarni boshqarish</div>
            </div>
            <button class="btn btn-sm" style="background:var(--c-teal);color:#fff"
                    data-bs-toggle="modal" data-bs-target="#createCategoryModal">
                <i class="fas fa-plus me-1"></i>Yangi katalog
            </button>
        </div>
    </div>
</div>

{{-- Table --}}
<div class="card">
    <div class="card-header d-flex align-items-center gap-2">
        <i class="fas fa-layer-group" style="color:var(--c-teal)"></i>
        <span>Kataloglar ro'yxati</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr style="font-size:12px;color:var(--c-text-3)">
                        <th>Nomi</th>
                        <th>Tavsif</th>
                        <th style="width:100px">Kitoblar</th>
                        <th style="width:80px">Holati</th>
                        <th style="width:100px">Amallar</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories->where('parent_id', null) as $category)
                    <tr>
                        <td>
                            @if($category->icon)
                            <i class="{{ $category->icon }} me-2" style="color:{{ $category->color ?? 'var(--c-teal)' }}"></i>
                            @endif
                            <strong style="font-size:13px;color:var(--c-text)">{{ $category->name_uz }}</strong>
                        </td>
                        <td style="font-size:12px;color:var(--c-text-3)">{{ $category->description }}</td>
                        <td>
                            <span class="badge" style="background:rgba(14,165,233,.12);color:var(--c-sky)">{{ $category->books_count }}</span>
                        </td>
                        <td>
                            @if($category->is_active)
                            <span class="badge" style="background:rgba(16,185,129,.12);color:var(--c-emerald);font-size:10px">Faol</span>
                            @else
                            <span class="badge" style="background:var(--c-bg);color:var(--c-text-3);border:1px solid var(--c-border);font-size:10px">Nofaol</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <button class="action-btn" style="background:rgba(245,158,11,.1);color:var(--c-amber)"
                                        onclick="editCategory({{ $category->id }},'{{ addslashes($category->name_uz) }}','{{ addslashes($category->name_ru) }}','{{ addslashes($category->name_en) }}','{{ addslashes($category->description) }}','{{ $category->icon }}','{{ $category->color }}',{{ $category->order_number }},{{ $category->is_active ? 'true' : 'false' }})">
                                    <i class="fas fa-edit"></i>
                                </button>
                                @if($category->books_count == 0 && $category->children->count() == 0)
                                <form action="{{ route('lms.library.categories.destroy', $category) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Katalogni o\'chirmoqchimisiz?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-btn" style="background:rgba(244,63,94,.1);color:var(--c-rose)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @foreach($category->children as $child)
                    <tr>
                        <td class="ps-5" style="font-size:13px;color:var(--c-text-2)">
                            <i class="fas fa-level-up-alt fa-rotate-90 me-2" style="color:var(--c-text-3);font-size:10px"></i>
                            {{ $child->name_uz }}
                        </td>
                        <td style="font-size:12px;color:var(--c-text-3)">{{ $child->description }}</td>
                        <td>
                            <span class="badge" style="background:rgba(14,165,233,.12);color:var(--c-sky)">{{ $child->books_count }}</span>
                        </td>
                        <td>
                            @if($child->is_active)
                            <span class="badge" style="background:rgba(16,185,129,.12);color:var(--c-emerald);font-size:10px">Faol</span>
                            @else
                            <span class="badge" style="background:var(--c-bg);color:var(--c-text-3);border:1px solid var(--c-border);font-size:10px">Nofaol</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <button class="action-btn" style="background:rgba(245,158,11,.1);color:var(--c-amber)"
                                        onclick="editCategory({{ $child->id }},'{{ addslashes($child->name_uz) }}','{{ addslashes($child->name_ru) }}','{{ addslashes($child->name_en) }}','{{ addslashes($child->description) }}','{{ $child->icon }}','{{ $child->color }}',{{ $child->order_number }},{{ $child->is_active ? 'true' : 'false' }},{{ $child->parent_id }})">
                                    <i class="fas fa-edit"></i>
                                </button>
                                @if($child->books_count == 0)
                                <form action="{{ route('lms.library.categories.destroy', $child) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Katalogni o\'chirmoqchimisiz?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-btn" style="background:rgba(244,63,94,.1);color:var(--c-rose)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5" style="color:var(--c-text-3);font-size:13px">
                            <i class="fas fa-layer-group mb-2" style="display:block;font-size:24px"></i>
                            Hozircha kataloglar mavjud emas
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Create Modal --}}
<div class="modal fade" id="createCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('lms.library.categories.store') }}" method="POST">
                @csrf
                <div class="modal-header" style="border-bottom:1px solid var(--c-border)">
                    <h5 class="modal-title d-flex align-items-center gap-2">
                        <i class="fas fa-folder-plus" style="color:var(--c-teal)"></i>
                        Yangi katalog yaratish
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label" style="font-size:13px;font-weight:600;color:var(--c-text-2)">Nomi (O'zbek) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name_uz" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-size:13px;font-weight:600;color:var(--c-text-2)">Nomi (Rus)</label>
                        <input type="text" class="form-control" name="name_ru">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-size:13px;font-weight:600;color:var(--c-text-2)">Nomi (Ingliz)</label>
                        <input type="text" class="form-control" name="name_en">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-size:13px;font-weight:600;color:var(--c-text-2)">Ota katalog</label>
                        <select class="form-select" name="parent_id">
                            <option value="">Asosiy katalog</option>
                            @foreach($categories->where('parent_id', null) as $parent)
                            <option value="{{ $parent->id }}">{{ $parent->name_uz }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-size:13px;font-weight:600;color:var(--c-text-2)">Tavsif</label>
                        <textarea class="form-control" name="description" rows="2"></textarea>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label" style="font-size:13px;font-weight:600;color:var(--c-text-2)">Ikonka (FontAwesome)</label>
                            <input type="text" class="form-control" name="icon" placeholder="fas fa-book">
                        </div>
                        <div class="col-6">
                            <label class="form-label" style="font-size:13px;font-weight:600;color:var(--c-text-2)">Rang</label>
                            <input type="color" class="form-control form-control-color w-100" name="color" value="#14b8a6">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-size:13px;font-weight:600;color:var(--c-text-2)">Tartib raqami</label>
                        <input type="number" class="form-control" name="order_number" value="0" min="0">
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" checked id="create_is_active">
                        <label class="form-check-label" for="create_is_active" style="font-size:13px">Faol holat</label>
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid var(--c-border)">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Bekor qilish</button>
                    <button type="submit" class="btn btn-sm" style="background:var(--c-teal);color:#fff">
                        <i class="fas fa-save me-1"></i>Saqlash
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Edit Modal --}}
<div class="modal fade" id="editCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editCategoryForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header" style="border-bottom:1px solid var(--c-border)">
                    <h5 class="modal-title d-flex align-items-center gap-2">
                        <i class="fas fa-folder-open" style="color:var(--c-amber)"></i>
                        Katalogni tahrirlash
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label" style="font-size:13px;font-weight:600;color:var(--c-text-2)">Nomi (O'zbek) <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_name_uz" name="name_uz" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-size:13px;font-weight:600;color:var(--c-text-2)">Nomi (Rus)</label>
                        <input type="text" class="form-control" id="edit_name_ru" name="name_ru">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-size:13px;font-weight:600;color:var(--c-text-2)">Nomi (Ingliz)</label>
                        <input type="text" class="form-control" id="edit_name_en" name="name_en">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-size:13px;font-weight:600;color:var(--c-text-2)">Ota katalog</label>
                        <select class="form-select" id="edit_parent_id" name="parent_id">
                            <option value="">Asosiy katalog</option>
                            @foreach($categories->where('parent_id', null) as $parent)
                            <option value="{{ $parent->id }}">{{ $parent->name_uz }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-size:13px;font-weight:600;color:var(--c-text-2)">Tavsif</label>
                        <textarea class="form-control" id="edit_description" name="description" rows="2"></textarea>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label" style="font-size:13px;font-weight:600;color:var(--c-text-2)">Ikonka</label>
                            <input type="text" class="form-control" id="edit_icon" name="icon">
                        </div>
                        <div class="col-6">
                            <label class="form-label" style="font-size:13px;font-weight:600;color:var(--c-text-2)">Rang</label>
                            <input type="color" class="form-control form-control-color w-100" id="edit_color" name="color">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-size:13px;font-weight:600;color:var(--c-text-2)">Tartib raqami</label>
                        <input type="number" class="form-control" id="edit_order_number" name="order_number" min="0">
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="edit_is_active" name="is_active" value="1">
                        <label class="form-check-label" for="edit_is_active" style="font-size:13px">Faol holat</label>
                    </div>
                </div>
                <div class="modal-footer" style="border-top:1px solid var(--c-border)">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Bekor qilish</button>
                    <button type="submit" class="btn btn-sm" style="background:var(--c-amber);color:#fff">
                        <i class="fas fa-save me-1"></i>Yangilash
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function editCategory(id, name_uz, name_ru, name_en, description, icon, color, order_number, is_active, parent_id) {
    const form = document.getElementById('editCategoryForm');
    form.action = '/lms/library/categories/' + id;
    document.getElementById('edit_name_uz').value = name_uz;
    document.getElementById('edit_name_ru').value = name_ru || '';
    document.getElementById('edit_name_en').value = name_en || '';
    document.getElementById('edit_description').value = description || '';
    document.getElementById('edit_icon').value = icon || '';
    document.getElementById('edit_color').value = color || '#14b8a6';
    document.getElementById('edit_order_number').value = order_number;
    document.getElementById('edit_is_active').checked = is_active;
    document.getElementById('edit_parent_id').value = parent_id || '';
    new bootstrap.Modal(document.getElementById('editCategoryModal')).show();
}
</script>
@endpush
