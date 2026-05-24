@extends('layouts.dashboard-new')

@section('title', 'Imtihon tahrirlash')
@section('page-title', 'Imtihon tahrirlash')

@section('content')
<div class="container-fluid">
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm bg-gradient-primary text-white">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1"><i class="fas fa-edit me-2"></i>{{ $exam->title }}</h4>
                            <p class="mb-0 opacity-75">{{ $exam->subject?->name_uz ?? '' }}</p>
                        </div>
                        <div>
                            <a href="{{ route('dean.schedule.exams.show', $exam) }}" class="btn btn-light me-2">
                                <i class="fas fa-eye me-1"></i> Ko'rish
                            </a>
                            <a href="{{ route('dean.schedule.exams') }}" class="btn btn-outline-light">
                                <i class="fas fa-arrow-left me-1"></i> Orqaga
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0"><i class="fas fa-file-alt text-primary me-2"></i>Imtihon ma'lumotlari</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('dean.schedule.exams.update', $exam) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Imtihon nomi <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                               value="{{ old('title', $exam->title) }}" required>
                        @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label fw-semibold">Imtihon turi <span class="text-danger">*</span></label>
                        <select name="exam_type" class="form-select @error('exam_type') is-invalid @enderror" required>
                            <option value="joriy" {{ old('exam_type', $exam->exam_type) == 'joriy' ? 'selected' : '' }}>Joriy nazorat</option>
                            <option value="oraliq" {{ old('exam_type', $exam->exam_type) == 'oraliq' ? 'selected' : '' }}>Oraliq nazorat</option>
                            <option value="yakuniy" {{ old('exam_type', $exam->exam_type) == 'yakuniy' ? 'selected' : '' }}>Yakuniy nazorat</option>
                            <option value="practice" {{ old('exam_type', $exam->exam_type) == 'practice' ? 'selected' : '' }}>Mashq test</option>
                        </select>
                        @error('exam_type')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label fw-semibold">Status</label>
                        <select name="status" class="form-select">
                            <option value="draft" {{ old('status', $exam->status) == 'draft' ? 'selected' : '' }}>Qoralama</option>
                            <option value="scheduled" {{ old('status', $exam->status) == 'scheduled' ? 'selected' : '' }}>Rejalashtirilgan</option>
                            <option value="active" {{ old('status', $exam->status) == 'active' ? 'selected' : '' }}>Faol</option>
                            <option value="completed" {{ old('status', $exam->status) == 'completed' ? 'selected' : '' }}>Yakunlangan</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Fan <span class="text-danger">*</span></label>
                        <select name="subject_id" class="form-select @error('subject_id') is-invalid @enderror" required>
                            @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}" {{ old('subject_id', $exam->subject_id) == $subject->id ? 'selected' : '' }}>
                                {{ $subject->name_uz }}
                            </option>
                            @endforeach
                        </select>
                        @error('subject_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">O'qituvchi</label>
                        <select name="teacher_id" class="form-select">
                            <option value="">-- O'qituvchini tanlang --</option>
                            @foreach($teachers as $teacher)
                            <option value="{{ $teacher->id }}" {{ old('teacher_id', $exam->teacher_id) == $teacher->id ? 'selected' : '' }}>
                                {{ $teacher->last_name }} {{ $teacher->first_name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label fw-semibold">Guruhlar <span class="text-danger">*</span></label>
                        <div class="row">
                            @foreach($groups as $group)
                            <div class="col-md-3 col-sm-4 col-6 mb-2">
                                <div class="form-check">
                                    <input type="checkbox" name="group_ids[]" value="{{ $group->id }}"
                                           class="form-check-input" id="group_{{ $group->id }}"
                                           {{ in_array($group->id, old('group_ids', $exam->group_ids ?? [])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="group_{{ $group->id }}">
                                        {{ $group->name }}
                                    </label>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @error('group_ids')
                        <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <hr class="my-4">
                <h6 class="fw-semibold mb-3"><i class="fas fa-clock text-primary me-2"></i>Vaqt sozlamalari</h6>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-semibold">Boshlanish vaqti <span class="text-danger">*</span></label>
                        <input type="datetime-local" name="start_time" class="form-control @error('start_time') is-invalid @enderror"
                               value="{{ old('start_time', $exam->start_time?->format('Y-m-d\TH:i')) }}" required>
                        @error('start_time')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-semibold">Tugash vaqti <span class="text-danger">*</span></label>
                        <input type="datetime-local" name="end_time" class="form-control @error('end_time') is-invalid @enderror"
                               value="{{ old('end_time', $exam->end_time?->format('Y-m-d\TH:i')) }}" required>
                        @error('end_time')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-semibold">Davomiylik (daqiqa) <span class="text-danger">*</span></label>
                        <input type="number" name="duration_minutes" class="form-control @error('duration_minutes') is-invalid @enderror"
                               value="{{ old('duration_minutes', $exam->duration_minutes) }}" min="10" max="300" required>
                        @error('duration_minutes')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <hr class="my-4">
                <h6 class="fw-semibold mb-3"><i class="fas fa-star text-warning me-2"></i>Baholash</h6>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-semibold">Maksimal ball</label>
                        <input type="number" name="max_score" class="form-control"
                               value="{{ old('max_score', $exam->max_score) }}" min="1" max="100">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-semibold">O'tish balli</label>
                        <input type="number" name="passing_score" class="form-control"
                               value="{{ old('passing_score', $exam->passing_score) }}" min="1" max="100">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-semibold">E'lon qilingan</label>
                        <div class="form-check form-switch mt-2">
                            <input type="checkbox" name="is_published" class="form-check-input" id="isPublished"
                                   {{ old('is_published', $exam->is_published) ? 'checked' : '' }}>
                            <label class="form-check-label" for="isPublished">Talabalarga ko'rsatish</label>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Tavsif</label>
                    <textarea name="description" class="form-control" rows="3">{{ old('description', $exam->description) }}</textarea>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('dean.schedule.exams') }}" class="btn btn-secondary">
                        <i class="fas fa-times me-1"></i> Bekor qilish
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Saqlash
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>.bg-gradient-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }</style>
@endsection
