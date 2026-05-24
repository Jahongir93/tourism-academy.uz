@extends('layouts.dashboard-new')

@section('title', 'Yo\'nalishni tahrirlash')
@section('page-title', 'Yo\'nalishni Tahrirlash')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="{{ route('campus-tour.dashboard') }}">Kampus Turi</a></li>
                <li class="breadcrumb-item"><a href="{{ route('campus-tour.routes.index') }}">Yo'nalishlar</a></li>
                <li class="breadcrumb-item active">Tahrirlash</li>
            </ol>
        </nav>
        <h1 class="h3 mb-0">
            <i class="fas fa-edit text-warning me-2"></i>
            Yo'nalishni tahrirlash
        </h1>
    </div>

    <form action="{{ route('campus-tour.routes.update', $route) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="row g-4">
            <!-- Main Content -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">Asosiy ma'lumotlar</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Yo'nalish nomi (O'zbekcha) <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                                   value="{{ old('title', $route->title) }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nomi (Ruscha)</label>
                                <input type="text" name="title_ru" class="form-control" value="{{ old('title_ru', $route->title_ru) }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nomi (Inglizcha)</label>
                                <input type="text" name="title_en" class="form-control" value="{{ old('title_en', $route->title_en) }}">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Boshlang'ich nuqta <span class="text-danger">*</span></label>
                                <input type="text" name="start_point" class="form-control" value="{{ old('start_point', $route->start_point) }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Oxirgi nuqta <span class="text-danger">*</span></label>
                                <input type="text" name="end_point" class="form-control" value="{{ old('end_point', $route->end_point) }}" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tavsif (O'zbekcha)</label>
                            <textarea name="description" class="form-control" rows="3">{{ old('description', $route->description) }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Yo'l-yo'riq</label>
                            <textarea name="directions" class="form-control" rows="5">{{ old('directions', $route->directions) }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Google Maps havolasi</label>
                            <input type="url" name="map_embed_url" class="form-control" value="{{ old('map_embed_url', $route->map_embed_url) }}">
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
                            <label class="form-label">Transport turi <span class="text-danger">*</span></label>
                            <select name="type" class="form-select" required>
                                @foreach($types as $key => $type)
                                    <option value="{{ $key }}" {{ old('type', $route->type) === $key ? 'selected' : '' }}>
                                        {{ $type['label'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-6 mb-3">
                                <label class="form-label">Davomiylik</label>
                                <input type="text" name="duration" class="form-control" value="{{ old('duration', $route->duration) }}">
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label">Masofa (km)</label>
                                <input type="number" name="distance" class="form-control" value="{{ old('distance', $route->distance) }}" step="0.1" min="0">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Narxi (so'm)</label>
                            <input type="number" name="price" class="form-control" value="{{ old('price', $route->price) }}" min="0">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Rang</label>
                            <input type="color" name="color" class="form-control form-control-color" value="{{ old('color', $route->color) }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tartib raqami</label>
                            <input type="number" name="order" class="form-control" value="{{ old('order', $route->order) }}" min="0">
                        </div>

                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active"
                                   value="1" {{ old('is_active', $route->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Faol</label>
                        </div>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-warning btn-lg">
                        <i class="fas fa-save me-2"></i>Saqlash
                    </button>
                    <a href="{{ route('campus-tour.routes.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-2"></i>Bekor qilish
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
