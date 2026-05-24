@extends('layouts.dashboard-new')

@section('title', 'Yangi mavzu qo\'shish')
@section('page-title', 'Yangi mavzu qo\'shish')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2>Yangi mavzu qo'shish</h2>
                    <p class="text-muted">Fan: {{ $subject->name_uz ?? $subject->name }} ({{ $subject->code }})</p>
                </div>
                <a href="{{ route('subjects.topics.index', $subject) }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Orqaga
                </a>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('subjects.topics.store', $subject) }}" method="POST">
                @csrf

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="topic_number" class="form-label">Mavzu raqami <span class="text-danger">*</span></label>
                            <input type="number"
                                   class="form-control @error('topic_number') is-invalid @enderror"
                                   id="topic_number"
                                   name="topic_number"
                                   value="{{ old('topic_number', $nextTopicNumber) }}"
                                   required
                                   min="1">
                            @error('topic_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="topic_type" class="form-label">Mashg'ulot turi <span class="text-danger">*</span></label>
                            <select class="form-select @error('topic_type') is-invalid @enderror"
                                    id="topic_type"
                                    name="topic_type"
                                    required>
                                <option value="lecture" {{ old('topic_type') == 'lecture' ? 'selected' : '' }}>Ma'ruza</option>
                                <option value="practice" {{ old('topic_type') == 'practice' ? 'selected' : '' }}>Amaliyot</option>
                                <option value="lab" {{ old('topic_type') == 'lab' ? 'selected' : '' }}>Laboratoriya</option>
                                <option value="seminar" {{ old('topic_type') == 'seminar' ? 'selected' : '' }}>Seminar</option>
                                <option value="independent" {{ old('topic_type') == 'independent' ? 'selected' : '' }}>Mustaqil ta'lim</option>
                            </select>
                            @error('topic_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="title_uz" class="form-label">Mavzu nomi (O'zbekcha) <span class="text-danger">*</span></label>
                    <input type="text"
                           class="form-control @error('title_uz') is-invalid @enderror"
                           id="title_uz"
                           name="title_uz"
                           value="{{ old('title_uz') }}"
                           required>
                    @error('title_uz')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="title_ru" class="form-label">Mavzu nomi (Ruscha)</label>
                    <input type="text"
                           class="form-control @error('title_ru') is-invalid @enderror"
                           id="title_ru"
                           name="title_ru"
                           value="{{ old('title_ru') }}">
                    @error('title_ru')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="title_en" class="form-label">Mavzu nomi (Inglizcha)</label>
                    <input type="text"
                           class="form-control @error('title_en') is-invalid @enderror"
                           id="title_en"
                           name="title_en"
                           value="{{ old('title_en') }}">
                    @error('title_en')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="description_uz" class="form-label">Tavsif (O'zbekcha)</label>
                    <textarea class="form-control @error('description_uz') is-invalid @enderror"
                              id="description_uz"
                              name="description_uz"
                              rows="3">{{ old('description_uz') }}</textarea>
                    @error('description_uz')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="hours" class="form-label">Soat miqdori <span class="text-danger">*</span></label>
                            <input type="number"
                                   class="form-control @error('hours') is-invalid @enderror"
                                   id="hours"
                                   name="hours"
                                   value="{{ old('hours', 2) }}"
                                   required
                                   min="1"
                                   max="20">
                            @error('hours')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="week_number" class="form-label">Hafta raqami</label>
                            <input type="number"
                                   class="form-control @error('week_number') is-invalid @enderror"
                                   id="week_number"
                                   name="week_number"
                                   value="{{ old('week_number') }}"
                                   min="1"
                                   max="52">
                            @error('week_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="is_active" class="form-label">Holati</label>
                            <select class="form-select @error('is_active') is-invalid @enderror"
                                    id="is_active"
                                    name="is_active">
                                <option value="1" {{ old('is_active', 1) == 1 ? 'selected' : '' }}>Faol</option>
                                <option value="0" {{ old('is_active') == 0 ? 'selected' : '' }}>Nofaol</option>
                            </select>
                            @error('is_active')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="learning_outcomes" class="form-label">O'quv natijalari</label>
                    <textarea class="form-control @error('learning_outcomes') is-invalid @enderror"
                              id="learning_outcomes"
                              name="learning_outcomes"
                              rows="3">{{ old('learning_outcomes') }}</textarea>
                    <small class="text-muted">Har bir natijani yangi qatordan yozing</small>
                    @error('learning_outcomes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="keywords" class="form-label">Kalit so'zlar</label>
                    <input type="text"
                           class="form-control @error('keywords') is-invalid @enderror"
                           id="keywords"
                           name="keywords"
                           value="{{ old('keywords') }}"
                           placeholder="Vergul bilan ajrating">
                    @error('keywords')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="references" class="form-label">Adabiyotlar</label>
                    <textarea class="form-control @error('references') is-invalid @enderror"
                              id="references"
                              name="references"
                              rows="3">{{ old('references') }}</textarea>
                    <small class="text-muted">Har bir adabiyotni yangi qatordan yozing</small>
                    @error('references')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('subjects.topics.index', $subject) }}" class="btn btn-secondary">
                        Bekor qilish
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Saqlash
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
