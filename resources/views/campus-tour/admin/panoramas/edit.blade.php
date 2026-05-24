@extends('layouts.dashboard-new')

@section('title', 'Panoramani tahrirlash')
@section('page-title', 'Panoramani Tahrirlash')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="{{ route('campus-tour.dashboard') }}">Kampus Turi</a></li>
                <li class="breadcrumb-item"><a href="{{ route('campus-tour.panoramas.index') }}">Panoramalar</a></li>
                <li class="breadcrumb-item active">Tahrirlash</li>
            </ol>
        </nav>
        <h1 class="h3 mb-0">
            <i class="fas fa-edit text-primary me-2"></i>
            Panoramani tahrirlash
        </h1>
    </div>

    <form action="{{ route('campus-tour.panoramas.update', $panorama) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="row g-4">
            <!-- Main Content -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">Asosiy ma'lumotlar</h5>
                    </div>
                    <div class="card-body">
                        <!-- Titles -->
                        <div class="mb-3">
                            <label class="form-label">Nomi (O'zbekcha) <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                                   value="{{ old('title', $panorama->title) }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nomi (Ruscha)</label>
                                <input type="text" name="title_ru" class="form-control" value="{{ old('title_ru', $panorama->title_ru) }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nomi (Inglizcha)</label>
                                <input type="text" name="title_en" class="form-control" value="{{ old('title_en', $panorama->title_en) }}">
                            </div>
                        </div>

                        <!-- Descriptions -->
                        <div class="mb-3">
                            <label class="form-label">Tavsif (O'zbekcha)</label>
                            <textarea name="description" class="form-control" rows="3">{{ old('description', $panorama->description) }}</textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tavsif (Ruscha)</label>
                                <textarea name="description_ru" class="form-control" rows="3">{{ old('description_ru', $panorama->description_ru) }}</textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tavsif (Inglizcha)</label>
                                <textarea name="description_en" class="form-control" rows="3">{{ old('description_en', $panorama->description_en) }}</textarea>
                            </div>
                        </div>

                        <!-- Current Image -->
                        @if($panorama->image_path)
                            <div class="mb-3">
                                <label class="form-label">Joriy rasm</label>
                                <div class="border rounded p-2">
                                    <img src="{{ $panorama->image_url }}" class="img-fluid rounded" style="max-height: 200px;">
                                </div>
                            </div>
                        @endif

                        <!-- Image Upload -->
                        <div class="mb-3">
                            <label class="form-label">Yangi rasm (ixtiyoriy)</label>
                            <input type="file" name="image" class="form-control @error('image') is-invalid @enderror"
                                   accept="image/jpeg,image/jpg,image/png">
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                Equirectangular format (2:1 nisbat), JPEG/PNG, maksimum 20MB
                            </div>
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">Sozlamalar</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Bino</label>
                            <select name="building_id" class="form-select">
                                <option value="">Tanlanmagan</option>
                                @foreach($buildings as $building)
                                    <option value="{{ $building->id }}" {{ old('building_id', $panorama->building_id) == $building->id ? 'selected' : '' }}>
                                        {{ $building->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tartib raqami</label>
                            <input type="number" name="order" class="form-control" value="{{ old('order', $panorama->order) }}" min="0">
                        </div>

                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active"
                                   value="1" {{ old('is_active', $panorama->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Faol</label>
                        </div>

                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" name="is_featured" id="is_featured"
                                   value="1" {{ old('is_featured', $panorama->is_featured) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_featured">Tanlangan (Featured)</label>
                        </div>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-save me-2"></i>Saqlash
                    </button>
                    <a href="{{ route('campus-tour.panoramas.preview', $panorama) }}" class="btn btn-outline-info">
                        <i class="fas fa-eye me-2"></i>Ko'rish
                    </a>
                    <a href="{{ route('campus-tour.panoramas.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-2"></i>Bekor qilish
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
