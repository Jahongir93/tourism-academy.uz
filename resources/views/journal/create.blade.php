@extends('layouts.dashboard-new')

@section('title', 'Yangi jurnal yaratish')
@section('page-title', 'Yangi jurnal')

@section('content')
<div class="container-fluid px-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1">
                        <i class="fas fa-book-open text-primary me-2"></i>
                        Yangi jurnal yaratish
                    </h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('journal.index') }}">Jurnallar</a></li>
                            <li class="breadcrumb-item active">Yangi jurnal</li>
                        </ol>
                    </nav>
                </div>
                <a href="{{ route('journal.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Orqaga
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-edit me-2"></i>
                        Jurnal ma'lumotlari
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('journal.store') }}" method="POST">
                        @csrf

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="subject_id" class="form-label">Fan <span class="text-danger">*</span></label>
                                <select name="subject_id" id="subject_id" class="form-select @error('subject_id') is-invalid @enderror" required>
                                    <option value="">Fanni tanlang</option>
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

                            <div class="col-md-6">
                                <label for="group_id" class="form-label">Guruh <span class="text-danger">*</span></label>
                                <select name="group_id" id="group_id" class="form-select @error('group_id') is-invalid @enderror" required>
                                    <option value="">Guruhni tanlang</option>
                                    @if($groups->isNotEmpty())
                                        <optgroup label="Mavjud guruhlar">
                                            @foreach($groups as $group)
                                                <option value="{{ $group->id }}" {{ old('group_id') == $group->id ? 'selected' : '' }}>
                                                    {{ $group->name }} ({{ $group->course }}-kurs)
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @endif
                                    @if($studentGroups->isNotEmpty())
                                        <optgroup label="Talaba guruhlari">
                                            @foreach($studentGroups as $studentGroup)
                                                <option value="{{ $studentGroup->id }}" {{ old('group_id') == $studentGroup->id ? 'selected' : '' }}>
                                                    {{ $studentGroup->name }}
                                                    @if($studentGroup->specialty)
                                                        - {{ $studentGroup->specialty->name_uz }}
                                                    @endif
                                                    ({{ $studentGroup->course }}-kurs)
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @endif
                                </select>
                                @error('group_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Guruh avtomatik ravishda jurnalga biriktiriladi
                                </small>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="teacher_id" class="form-label">O'qituvchi <span class="text-danger">*</span></label>
                                <select name="teacher_id" id="teacher_id" class="form-select @error('teacher_id') is-invalid @enderror" required>
                                    <option value="">O'qituvchini tanlang</option>
                                    @foreach($teachers as $teacher)
                                        <option value="{{ $teacher->id }}" {{ old('teacher_id') == $teacher->id ? 'selected' : '' }}>
                                            {{ $teacher->first_name }} {{ $teacher->last_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('teacher_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="academic_year_id" class="form-label">O'quv yili <span class="text-danger">*</span></label>
                                <select name="academic_year_id" id="academic_year_id" class="form-select @error('academic_year_id') is-invalid @enderror" required>
                                    <option value="">O'quv yilini tanlang</option>
                                    @foreach($academicYears as $year)
                                        <option value="{{ $year->id }}" {{ old('academic_year_id') == $year->id || $year->is_current ? 'selected' : '' }}>
                                            {{ $year->year }}
                                            @if($year->is_current)
                                                <span class="badge bg-success">Joriy</span>
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('academic_year_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="semester_id" class="form-label">Semestr <span class="text-danger">*</span></label>
                                <select name="semester_id" id="semester_id" class="form-select @error('semester_id') is-invalid @enderror" required>
                                    <option value="">Semestrni tanlang</option>
                                    <option value="1" {{ old('semester_id') == 1 ? 'selected' : '' }}>1-semestr</option>
                                    <option value="2" {{ old('semester_id') == 2 ? 'selected' : '' }}>2-semestr</option>
                                </select>
                                @error('semester_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-lightbulb me-2"></i>
                            <strong>Eslatma:</strong> Jurnal yaratilgandan keyin guruh va fan o'zgartirilishi mumkin emas. Faqat o'qituvchi va semestr ma'lumotlarini tahrirlash mumkin.
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('journal.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i> Bekor qilish
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Jurnal yaratish
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
