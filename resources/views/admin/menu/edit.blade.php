@extends('layouts.dashboard-new')

@section('title', 'Menyuni tahrirlash')
@section('page-title', 'Menyuni tahrirlash')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <a href="{{ route('admin.menu.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Orqaga
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Menyu ma'lumotlari</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.menu.update', $menu) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="label_uz" class="form-label">Nomi (O'zbekcha) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('label_uz') is-invalid @enderror"
                                   id="label_uz" name="label_uz" value="{{ old('label_uz', $menu->label_uz) }}" required>
                            @error('label_uz')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="label_ru" class="form-label">Nomi (Ruscha)</label>
                                <input type="text" class="form-control @error('label_ru') is-invalid @enderror"
                                       id="label_ru" name="label_ru" value="{{ old('label_ru', $menu->label_ru) }}">
                                @error('label_ru')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="label_en" class="form-label">Nomi (Inglizcha)</label>
                                <input type="text" class="form-control @error('label_en') is-invalid @enderror"
                                       id="label_en" name="label_en" value="{{ old('label_en', $menu->label_en) }}">
                                @error('label_en')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="url" class="form-label">URL <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('url') is-invalid @enderror"
                                   id="url" name="url" value="{{ old('url', $menu->url) }}"
                                   placeholder="/about yoki https://example.com" required>
                            <small class="text-muted">Ichki havola: /about, Tashqi havola: https://example.com</small>
                            @error('url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="icon" class="form-label">Ikon (FontAwesome)</label>
                                <input type="text" class="form-control @error('icon') is-invalid @enderror"
                                       id="icon" name="icon" value="{{ old('icon', $menu->icon) }}"
                                       placeholder="fas fa-home">
                                <small class="text-muted">Masalan: fas fa-home, fas fa-user</small>
                                @error('icon')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="parent_id" class="form-label">Yuqori menyu</label>
                                <select class="form-select @error('parent_id') is-invalid @enderror"
                                        id="parent_id" name="parent_id">
                                    <option value="">-- Asosiy menyu --</option>
                                    @foreach($parentMenuItems as $item)
                                        <option value="{{ $item->id }}" {{ old('parent_id', $menu->parent_id) == $item->id ? 'selected' : '' }}>
                                            {{ $item->label_uz }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Pastki menyu yaratish uchun yuqori menyuni tanlang</small>
                                @error('parent_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="order" class="form-label">Tartib raqami <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('order') is-invalid @enderror"
                                   id="order" name="order" value="{{ old('order', $menu->order) }}" min="0" required>
                            <small class="text-muted">Menyular tartib raqami bo'yicha saralanadi (kichik raqam birinchi)</small>
                            @error('order')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active"
                                       value="1" {{ old('is_active', $menu->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Faol
                                </label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="open_in_new_tab" name="open_in_new_tab"
                                       value="1" {{ old('open_in_new_tab', $menu->open_in_new_tab) ? 'checked' : '' }}>
                                <label class="form-check-label" for="open_in_new_tab">
                                    Yangi oynada ochish
                                </label>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Saqlash
                            </button>
                            <a href="{{ route('admin.menu.index') }}" class="btn btn-secondary">
                                Bekor qilish
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-warning text-dark">
                    <h6 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i>Diqqat</h6>
                </div>
                <div class="card-body">
                    <p class="small mb-0">
                        Menyuni o'zgartirish sayt navigatsiyasiga ta'sir qiladi. O'zgarishlarni saqlashdan oldin tekshiring.
                    </p>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Yordam</h6>
                </div>
                <div class="card-body">
                    <h6>FontAwesome ikonlar</h6>
                    <p class="small mb-3">
                        <a href="https://fontawesome.com/icons" target="_blank" class="text-decoration-none">
                            fontawesome.com/icons <i class="fas fa-external-link-alt small"></i>
                        </a>
                    </p>

                    <h6>Ikon misollari:</h6>
                    <ul class="small">
                        <li><code>fas fa-home</code> - <i class="fas fa-home"></i> Bosh sahifa</li>
                        <li><code>fas fa-info-circle</code> - <i class="fas fa-info-circle"></i> Ma'lumot</li>
                        <li><code>fas fa-envelope</code> - <i class="fas fa-envelope"></i> Aloqa</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
