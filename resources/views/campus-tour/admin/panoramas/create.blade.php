@extends('layouts.dashboard-new')

@section('title', 'Yangi Panorama')
@section('page-title', 'Yangi Panorama')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="{{ route('campus-tour.dashboard') }}">Kampus Turi</a></li>
                <li class="breadcrumb-item"><a href="{{ route('campus-tour.panoramas.index') }}">Panoramalar</a></li>
                <li class="breadcrumb-item active">Yangi</li>
            </ol>
        </nav>
        <h1 class="h3 mb-0">
            <i class="fas fa-plus-circle text-primary me-2"></i>
            Yangi 360° Panorama
        </h1>
    </div>

    <form action="{{ route('campus-tour.panoramas.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
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
                                   value="{{ old('title') }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nomi (Ruscha)</label>
                                <input type="text" name="title_ru" class="form-control" value="{{ old('title_ru') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nomi (Inglizcha)</label>
                                <input type="text" name="title_en" class="form-control" value="{{ old('title_en') }}">
                            </div>
                        </div>

                        <!-- Descriptions -->
                        <div class="mb-3">
                            <label class="form-label">Tavsif (O'zbekcha)</label>
                            <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tavsif (Ruscha)</label>
                                <textarea name="description_ru" class="form-control" rows="3">{{ old('description_ru') }}</textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tavsif (Inglizcha)</label>
                                <textarea name="description_en" class="form-control" rows="3">{{ old('description_en') }}</textarea>
                            </div>
                        </div>

                        <!-- Image Upload -->
                        <div class="mb-3">
                            <label class="form-label">360° Panorama rasmi <span class="text-danger">*</span></label>
                            <input type="file" name="image" class="form-control @error('image') is-invalid @enderror"
                                   accept="image/jpeg,image/jpg,image/png" required>
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                Equirectangular format (2:1 nisbat), JPEG/PNG, maksimum 20MB
                            </div>
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Preview -->
                        <div id="imagePreview" class="mb-3 d-none">
                            <label class="form-label">Oldindan ko'rish</label>
                            <div class="border rounded p-2">
                                <img id="previewImg" src="" class="img-fluid rounded" style="max-height: 300px;">
                            </div>
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
                                    <option value="{{ $building->id }}" {{ old('building_id') == $building->id ? 'selected' : '' }}>
                                        {{ $building->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tartib raqami</label>
                            <input type="number" name="order" class="form-control" value="{{ old('order', 0) }}" min="0">
                        </div>

                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active"
                                   value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Faol</label>
                        </div>

                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" name="is_featured" id="is_featured"
                                   value="1" {{ old('is_featured') ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_featured">Tanlangan (Featured)</label>
                        </div>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-save me-2"></i>Saqlash
                    </button>
                    <a href="{{ route('campus-tour.panoramas.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-2"></i>Bekor qilish
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.querySelector('input[name="image"]').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('previewImg').src = e.target.result;
            document.getElementById('imagePreview').classList.remove('d-none');
        };
        reader.readAsDataURL(file);
    }
});
</script>
@endpush
@endsection
