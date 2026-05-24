@extends('layouts.dashboard-new')

@section('title', 'Vakansiyani tahrirlash')
@section('page-title', 'Vakansiyani tahrirlash')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="{{ route('employees.index') }}">Xodimlar</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.vacancies.index') }}">Vakansiyalar</a></li>
                <li class="breadcrumb-item active">Tahrirlash</li>
            </ol>
        </nav>
        <h1 class="h3 mb-0">
            <i class="fas fa-edit text-primary me-2"></i>
            {{ $vacancy->title }}
        </h1>
    </div>

    <form action="{{ route('admin.vacancies.update', $vacancy) }}" method="POST">
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
                        <!-- Title -->
                        <div class="mb-3">
                            <label class="form-label">Vakansiya nomi <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                                   value="{{ old('title', $vacancy->title) }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nomi (Ruscha)</label>
                                <input type="text" name="title_ru" class="form-control" value="{{ old('title_ru', $vacancy->title_ru) }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nomi (Inglizcha)</label>
                                <input type="text" name="title_en" class="form-control" value="{{ old('title_en', $vacancy->title_en) }}">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Bo'lim / Kafedra</label>
                            <input type="text" name="department" class="form-control" value="{{ old('department', $vacancy->department) }}">
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label class="form-label">Tavsif</label>
                            <textarea name="description" class="form-control" rows="4">{{ old('description', $vacancy->description) }}</textarea>
                        </div>

                        <!-- Requirements -->
                        <div class="mb-3">
                            <label class="form-label">Talablar</label>
                            <textarea name="requirements" class="form-control" rows="4">{{ old('requirements', $vacancy->requirements) }}</textarea>
                        </div>

                        <!-- Responsibilities -->
                        <div class="mb-3">
                            <label class="form-label">Vazifalar</label>
                            <textarea name="responsibilities" class="form-control" rows="4">{{ old('responsibilities', $vacancy->responsibilities) }}</textarea>
                        </div>

                        <!-- Benefits -->
                        <div class="mb-3">
                            <label class="form-label">Imtiyozlar</label>
                            <textarea name="benefits" class="form-control" rows="3">{{ old('benefits', $vacancy->benefits) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Stats -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-4">
                                <div class="h4 mb-0">{{ $vacancy->views_count }}</div>
                                <small class="text-muted">Ko'rishlar</small>
                            </div>
                            <div class="col-4">
                                <div class="h4 mb-0">{{ $vacancy->applications_count }}</div>
                                <small class="text-muted">Arizalar</small>
                            </div>
                            <div class="col-4">
                                <div class="h4 mb-0 {{ $vacancy->is_active ? 'text-success' : 'text-danger' }}">
                                    <i class="fas fa-circle"></i>
                                </div>
                                <small class="text-muted">{{ $vacancy->is_active ? 'Faol' : 'Nofaol' }}</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">Parametrlar</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Ish turi <span class="text-danger">*</span></label>
                            <select name="employment_type" class="form-select" required>
                                @foreach(\App\Models\Vacancy::EMPLOYMENT_TYPES as $key => $label)
                                    <option value="{{ $key }}" {{ old('employment_type', $vacancy->employment_type) == $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Maosh oralig'i</label>
                            <input type="text" name="salary_range" class="form-control" value="{{ old('salary_range', $vacancy->salary_range) }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tajriba talab</label>
                            <input type="text" name="experience_required" class="form-control" value="{{ old('experience_required', $vacancy->experience_required) }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Ma'lumot talab</label>
                            <input type="text" name="education_required" class="form-control" value="{{ old('education_required', $vacancy->education_required) }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Ariza berish muddati</label>
                            <input type="date" name="deadline" class="form-control" value="{{ old('deadline', $vacancy->deadline?->format('Y-m-d')) }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Ochiq o'rinlar soni</label>
                            <input type="number" name="positions_count" class="form-control" value="{{ old('positions_count', $vacancy->positions_count) }}" min="1">
                        </div>

                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active"
                                   value="1" {{ old('is_active', $vacancy->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Faol</label>
                        </div>

                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" name="is_featured" id="is_featured"
                                   value="1" {{ old('is_featured', $vacancy->is_featured) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_featured">Tanlangan (Featured)</label>
                        </div>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-save me-2"></i>Saqlash
                    </button>
                    <a href="{{ route('admin.vacancies.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-2"></i>Bekor qilish
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
