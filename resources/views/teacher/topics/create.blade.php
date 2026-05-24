@extends('layouts.dashboard-new')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('teacher.topics.index') }}">Fanlar</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('teacher.topics.subject', $subject->id) }}">{{ $subject->name }}</a>
                    </li>
                    <li class="breadcrumb-item active">Yangi Mavzu</li>
                </ol>
            </nav>
            <h3>Yangi Mavzu Qo'shish</h3>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Mavzu Ma'lumotlari</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('teacher.topics.store', $subject->id) }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="title" class="form-label">Mavzu Nomi <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control @error('title') is-invalid @enderror"
                                   id="title"
                                   name="title"
                                   value="{{ old('title') }}"
                                   required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Qisqacha Tavsif</label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description"
                                      name="description"
                                      rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="content" class="form-label">To'liq Tarkib</label>
                            <textarea class="form-control @error('content') is-invalid @enderror"
                                      id="content"
                                      name="content"
                                      rows="10">{{ old('content') }}</textarea>
                            @error('content')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Mavzu mazmunini batafsil yozing</small>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="order" class="form-label">Tartib Raqami <span class="text-danger">*</span></label>
                                    <input type="number"
                                           class="form-control @error('order') is-invalid @enderror"
                                           id="order"
                                           name="order"
                                           value="{{ old('order', $maxOrder + 1) }}"
                                           min="0"
                                           required>
                                    @error('order')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="duration_hours" class="form-label">Davomiyligi (soatda)</label>
                                    <input type="number"
                                           class="form-control @error('duration_hours') is-invalid @enderror"
                                           id="duration_hours"
                                           name="duration_hours"
                                           value="{{ old('duration_hours') }}"
                                           step="0.5"
                                           min="0">
                                    @error('duration_hours')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('teacher.topics.subject', $subject->id) }}"
                               class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Orqaga
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Saqlash
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Yo'riqnoma</h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <i class="bi bi-info-circle text-primary"></i>
                            <strong>Mavzu nomi:</strong> Qisqa va aniq nom bering
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-info-circle text-primary"></i>
                            <strong>Tartib:</strong> Mavzuning ketma-ketligi
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-info-circle text-primary"></i>
                            <strong>Davomiyligi:</strong> Mavzuni o'qitish uchun kerak bo'ladigan vaqt
                        </li>
                        <li>
                            <i class="bi bi-info-circle text-primary"></i>
                            Mavzu yaratgandan keyin resurslar qo'shishingiz mumkin
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
