@extends('layouts.dashboard-new')

@section('title', 'Binoni tahrirlash')
@section('page-title', 'Binoni Tahrirlash')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="{{ route('campus-tour.dashboard') }}">Kampus Turi</a></li>
                <li class="breadcrumb-item"><a href="{{ route('campus-tour.buildings.index') }}">Binolar</a></li>
                <li class="breadcrumb-item active">Tahrirlash</li>
            </ol>
        </nav>
        <h1 class="h3 mb-0">
            <i class="fas fa-edit text-success me-2"></i>
            Binoni tahrirlash
        </h1>
    </div>

    <form action="{{ route('campus-tour.buildings.update', $building) }}" method="POST" enctype="multipart/form-data">
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
                        <!-- Titles -->
                        <div class="mb-3">
                            <label class="form-label">Nomi (O'zbekcha) <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                                   value="{{ old('title', $building->title) }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nomi (Ruscha)</label>
                                <input type="text" name="title_ru" class="form-control" value="{{ old('title_ru', $building->title_ru) }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nomi (Inglizcha)</label>
                                <input type="text" name="title_en" class="form-control" value="{{ old('title_en', $building->title_en) }}">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Qisqa tavsif</label>
                            <input type="text" name="short_description" class="form-control" value="{{ old('short_description', $building->short_description) }}"
                                   maxlength="500">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">To'liq tavsif (O'zbekcha)</label>
                            <textarea name="description" class="form-control" rows="4">{{ old('description', $building->description) }}</textarea>
                        </div>

                        @if($building->image)
                            <div class="mb-3">
                                <label class="form-label">Joriy rasm</label>
                                <div>
                                    <img src="{{ $building->image_url }}" class="rounded" style="max-height: 150px;">
                                </div>
                            </div>
                        @endif

                        <div class="mb-3">
                            <label class="form-label">Yangi rasm</label>
                            <input type="file" name="image" class="form-control" accept="image/*">
                        </div>
                    </div>
                </div>

                <!-- Location -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0"><i class="fas fa-map-marker-alt me-2"></i>Joylashuv</h5>
                    </div>
                    <div class="card-body">
                        <h6 class="mb-3">Rasm asosidagi xarita uchun (X,Y %)</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">X koordinatasi (%)</label>
                                <input type="number" name="marker_x" class="form-control" value="{{ old('marker_x', $building->marker_x) }}"
                                       min="0" max="100" step="0.01">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Y koordinatasi (%)</label>
                                <input type="number" name="marker_y" class="form-control" value="{{ old('marker_y', $building->marker_y) }}"
                                       min="0" max="100" step="0.01">
                            </div>
                        </div>

                        <hr>

                        <h6 class="mb-3">OSM/Google Maps uchun (Lat, Lng)</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Kenglik (Latitude)</label>
                                <input type="number" name="latitude" class="form-control" value="{{ old('latitude', $building->latitude) }}"
                                       step="0.00000001">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Uzunlik (Longitude)</label>
                                <input type="number" name="longitude" class="form-control" value="{{ old('longitude', $building->longitude) }}"
                                       step="0.00000001">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Info -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0"><i class="fas fa-phone me-2"></i>Aloqa ma'lumotlari</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Ish vaqti</label>
                                <input type="text" name="working_hours" class="form-control" value="{{ old('working_hours', $building->working_hours) }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Telefon</label>
                                <input type="text" name="phone" class="form-control" value="{{ old('phone', $building->phone) }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email', $building->email) }}">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Qavatlar soni</label>
                            <input type="number" name="floor_count" class="form-control" value="{{ old('floor_count', $building->floor_count) }}"
                                   min="1" max="100" style="width: 120px;">
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
                            <label class="form-label">Bog'langan 360° panorama</label>
                            <select name="panorama_id" class="form-select">
                                <option value="">Tanlanmagan</option>
                                @foreach($panoramas as $panorama)
                                    <option value="{{ $panorama->id }}" {{ old('panorama_id', $building->panorama_id) == $panorama->id ? 'selected' : '' }}>
                                        {{ $panorama->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Marker ikonkasi</label>
                            <select name="icon" class="form-select">
                                <option value="fa-building" {{ $building->icon == 'fa-building' ? 'selected' : '' }}>Building</option>
                                <option value="fa-university" {{ $building->icon == 'fa-university' ? 'selected' : '' }}>University</option>
                                <option value="fa-book" {{ $building->icon == 'fa-book' ? 'selected' : '' }}>Library</option>
                                <option value="fa-flask" {{ $building->icon == 'fa-flask' ? 'selected' : '' }}>Laboratory</option>
                                <option value="fa-utensils" {{ $building->icon == 'fa-utensils' ? 'selected' : '' }}>Canteen</option>
                                <option value="fa-bed" {{ $building->icon == 'fa-bed' ? 'selected' : '' }}>Dormitory</option>
                                <option value="fa-futbol" {{ $building->icon == 'fa-futbol' ? 'selected' : '' }}>Sports</option>
                                <option value="fa-parking" {{ $building->icon == 'fa-parking' ? 'selected' : '' }}>Parking</option>
                                <option value="fa-hospital" {{ $building->icon == 'fa-hospital' ? 'selected' : '' }}>Medical</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Marker rangi</label>
                            <input type="color" name="color" class="form-control form-control-color" value="{{ old('color', $building->color) }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tartib raqami</label>
                            <input type="number" name="order" class="form-control" value="{{ old('order', $building->order) }}" min="0">
                        </div>

                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active"
                                   value="1" {{ old('is_active', $building->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Faol</label>
                        </div>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-success btn-lg">
                        <i class="fas fa-save me-2"></i>Saqlash
                    </button>
                    <a href="{{ route('campus-tour.buildings.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-2"></i>Bekor qilish
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
