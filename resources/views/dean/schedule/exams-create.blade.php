@extends('layouts.dashboard-new')

@section('title', 'Yangi imtihon')
@section('page-title', 'Yangi imtihon yaratish')

@section('content')
<div class="container-fluid">
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm bg-gradient-success text-white">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1"><i class="fas fa-plus-circle me-2"></i>Yangi imtihon yaratish</h4>
                            <p class="mb-0 opacity-75">{{ $faculty?->name ?? 'Fakultet' }}</p>
                        </div>
                        <a href="{{ route('dean.schedule.exams') }}" class="btn btn-light">
                            <i class="fas fa-arrow-left me-1"></i> Orqaga
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0"><i class="fas fa-file-alt text-success me-2"></i>Imtihon ma'lumotlari</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('dean.schedule.exams.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-8 mb-3">
                        <label class="form-label fw-semibold">Imtihon nomi <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                               value="{{ old('title') }}" placeholder="Masalan: Matematika yakuniy nazorat" required>
                        @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-semibold">Imtihon turi <span class="text-danger">*</span></label>
                        <select name="exam_type" class="form-select @error('exam_type') is-invalid @enderror" required>
                            <option value="">-- Turni tanlang --</option>
                            <option value="joriy" {{ old('exam_type') == 'joriy' ? 'selected' : '' }}>Joriy nazorat</option>
                            <option value="oraliq" {{ old('exam_type') == 'oraliq' ? 'selected' : '' }}>Oraliq nazorat</option>
                            <option value="yakuniy" {{ old('exam_type') == 'yakuniy' ? 'selected' : '' }}>Yakuniy nazorat</option>
                            <option value="practice" {{ old('exam_type') == 'practice' ? 'selected' : '' }}>Mashq test</option>
                        </select>
                        @error('exam_type')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Fan <span class="text-danger">*</span></label>
                        <select name="subject_id" class="form-select @error('subject_id') is-invalid @enderror" required>
                            <option value="">-- Fanni tanlang --</option>
                            @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
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
                        <select name="teacher_id" class="form-select @error('teacher_id') is-invalid @enderror">
                            <option value="">-- O'qituvchini tanlang --</option>
                            @foreach($teachers as $teacher)
                            <option value="{{ $teacher->id }}" {{ old('teacher_id') == $teacher->id ? 'selected' : '' }}>
                                {{ $teacher->last_name }} {{ $teacher->first_name }}
                            </option>
                            @endforeach
                        </select>
                        @error('teacher_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label fw-semibold">Guruhlar <span class="text-danger">*</span></label>
                        <div class="row">
                            @foreach($groups as $group)
                            <div class="col-md-3 col-sm-4 col-6 mb-2">
                                <div class="form-check">
                                    <input type="checkbox" name="group_ids[]" value="{{ $group->id }}"
                                           class="form-check-input" id="group_{{ $group->id }}"
                                           {{ in_array($group->id, old('group_ids', [])) ? 'checked' : '' }}>
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
                               value="{{ old('start_time') }}" required>
                        @error('start_time')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-semibold">Tugash vaqti <span class="text-danger">*</span></label>
                        <input type="datetime-local" name="end_time" class="form-control @error('end_time') is-invalid @enderror"
                               value="{{ old('end_time') }}" required>
                        @error('end_time')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-semibold">Davomiylik (daqiqa) <span class="text-danger">*</span></label>
                        <input type="number" name="duration_minutes" class="form-control @error('duration_minutes') is-invalid @enderror"
                               value="{{ old('duration_minutes', 60) }}" min="10" max="300" required>
                        @error('duration_minutes')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <hr class="my-4">
                <h6 class="fw-semibold mb-3"><i class="fas fa-star text-warning me-2"></i>Baholash</h6>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-semibold">Maksimal ball <span class="text-danger">*</span></label>
                        <input type="number" name="max_score" class="form-control @error('max_score') is-invalid @enderror"
                               value="{{ old('max_score', 100) }}" min="1" max="100" required>
                        @error('max_score')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-semibold">O'tish balli</label>
                        <input type="number" name="passing_score" class="form-control @error('passing_score') is-invalid @enderror"
                               value="{{ old('passing_score', 60) }}" min="1" max="100">
                        @error('passing_score')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-semibold">Maksimal urinishlar</label>
                        <input type="number" name="max_attempts" class="form-control @error('max_attempts') is-invalid @enderror"
                               value="{{ old('max_attempts', 1) }}" min="1" max="10">
                        @error('max_attempts')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Tavsif</label>
                    <textarea name="description" class="form-control" rows="3" placeholder="Imtihon haqida qo'shimcha ma'lumot...">{{ old('description') }}</textarea>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('dean.schedule.exams') }}" class="btn btn-secondary">
                        <i class="fas fa-times me-1"></i> Bekor qilish
                    </a>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save me-1"></i> Yaratish
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>.bg-gradient-success { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); }</style>
@endsection
