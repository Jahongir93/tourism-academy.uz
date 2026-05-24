@extends('layouts.dashboard-new')

@section('title', 'Guruhni tahrirlash')
@section('page-title', 'Guruhni tahrirlash')

@section('styles')
<style>
    select.form-control {
        background-color: white;
        color: #2c3e50;
    }
    select.form-control option {
        background-color: white;
        color: #2c3e50;
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header pb-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Guruhni tahrirlash: {{ $group->name }}</h6>
                        <a href="{{ route('student-contingent.groups.show', $group) }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Orqaga
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('student-contingent.groups.update', $group) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name" class="form-control-label">Guruh nomi *</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                           id="name" name="name" value="{{ old('name', $group->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="faculty_id" class="form-control-label">Fakultet *</label>
                                    <select class="form-control @error('faculty_id') is-invalid @enderror"
                                            id="faculty_id" name="faculty_id" required>
                                        <option value="">Fakultetni tanlang</option>
                                        @foreach($faculties as $faculty)
                                            <option value="{{ $faculty->id }}" {{ old('faculty_id', $group->faculty_id) == $faculty->id ? 'selected' : '' }}>
                                                {{ $faculty->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('faculty_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="department_id" class="form-control-label">Kafedra</label>
                                    <select class="form-control @error('department_id') is-invalid @enderror"
                                            id="department_id" name="department_id">
                                        <option value="">Kafedrani tanlang</option>
                                        @foreach($departments as $department)
                                            <option value="{{ $department->id }}" {{ old('department_id', $group->department_id) == $department->id ? 'selected' : '' }}>
                                                {{ $department->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('department_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="specialty_id" class="form-control-label">Mutaxassislik</label>
                                    <select class="form-control @error('specialty_id') is-invalid @enderror"
                                            id="specialty_id" name="specialty_id">
                                        <option value="">Mutaxassislikni tanlang</option>
                                        @foreach($specialties as $specialty)
                                            <option value="{{ $specialty->id }}" {{ old('specialty_id', $group->specialty_id) == $specialty->id ? 'selected' : '' }}>
                                                {{ $specialty->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('specialty_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="course" class="form-control-label">Kurs *</label>
                                    <select class="form-control @error('course') is-invalid @enderror"
                                            id="course" name="course" required>
                                        <option value="">Kursni tanlang</option>
                                        @for($i = 1; $i <= 6; $i++)
                                            <option value="{{ $i }}" {{ old('course', $group->course) == $i ? 'selected' : '' }}>
                                                {{ $i }}-kurs
                                            </option>
                                        @endfor
                                    </select>
                                    @error('course')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="semester" class="form-control-label">Semestr *</label>
                                    <select class="form-control @error('semester') is-invalid @enderror"
                                            id="semester" name="semester" required>
                                        <option value="">Semestrni tanlang</option>
                                        @for($i = 1; $i <= 12; $i++)
                                            <option value="{{ $i }}" {{ old('semester', $group->semester) == $i ? 'selected' : '' }}>
                                                {{ $i }}-semestr
                                            </option>
                                        @endfor
                                    </select>
                                    @error('semester')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="academic_year" class="form-control-label">O'quv yili *</label>
                                    <input type="text" class="form-control @error('academic_year') is-invalid @enderror"
                                           id="academic_year" name="academic_year" value="{{ old('academic_year', $group->academic_year) }}"
                                           placeholder="2024-2025" required>
                                    @error('academic_year')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="max_students" class="form-control-label">Maksimal talabalar soni *</label>
                                    <input type="number" class="form-control @error('max_students') is-invalid @enderror"
                                           id="max_students" name="max_students" value="{{ old('max_students', $group->max_students) }}"
                                           min="1" max="100" required>
                                    @error('max_students')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="education_type" class="form-control-label">Ta'lim turi</label>
                                    <select class="form-control @error('education_type') is-invalid @enderror"
                                            id="education_type" name="education_type">
                                        <option value="">Tanlang</option>
                                        <option value="bakalavr" {{ old('education_type', $group->education_type) == 'bakalavr' ? 'selected' : '' }}>Bakalavr</option>
                                        <option value="magistr" {{ old('education_type', $group->education_type) == 'magistr' ? 'selected' : '' }}>Magistr</option>
                                        <option value="doktorantura" {{ old('education_type', $group->education_type) == 'doktorantura' ? 'selected' : '' }}>Doktorantura</option>
                                    </select>
                                    @error('education_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="education_form" class="form-control-label">Ta'lim shakli</label>
                                    <select class="form-control @error('education_form') is-invalid @enderror"
                                            id="education_form" name="education_form">
                                        <option value="">Tanlang</option>
                                        <option value="kunduzgi" {{ old('education_form', $group->education_form) == 'kunduzgi' ? 'selected' : '' }}>Kunduzgi</option>
                                        <option value="kechki" {{ old('education_form', $group->education_form) == 'kechki' ? 'selected' : '' }}>Kechki</option>
                                        <option value="sirtqi" {{ old('education_form', $group->education_form) == 'sirtqi' ? 'selected' : '' }}>Sirtqi</option>
                                    </select>
                                    @error('education_form')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="language" class="form-control-label">Ta'lim tili *</label>
                                    <select class="form-control @error('language') is-invalid @enderror"
                                            id="language" name="language" required>
                                        <option value="uz" {{ old('language', $group->language) == 'uz' ? 'selected' : '' }}>O'zbek</option>
                                        <option value="ru" {{ old('language', $group->language) == 'ru' ? 'selected' : '' }}>Rus</option>
                                        <option value="en" {{ old('language', $group->language) == 'en' ? 'selected' : '' }}>Ingliz</option>
                                    </select>
                                    @error('language')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="curator_id" class="form-control-label">Kurator</label>
                                    <select class="form-control @error('curator_id') is-invalid @enderror"
                                            id="curator_id" name="curator_id">
                                        <option value="">Kuratorni tanlang</option>
                                        @foreach($curators as $curator)
                                            <option value="{{ $curator->id }}" {{ old('curator_id', $group->curator_id) == $curator->id ? 'selected' : '' }}>
                                                {{ $curator->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('curator_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="monitor_student_id" class="form-control-label">Guruh boshlig'i</label>
                                    <select class="form-control @error('monitor_student_id') is-invalid @enderror"
                                            id="monitor_student_id" name="monitor_student_id">
                                        <option value="">Guruh boshlig'ini tanlang</option>
                                        @foreach($students as $student)
                                            <option value="{{ $student->id }}" {{ old('monitor_student_id', $group->monitor_student_id) == $student->id ? 'selected' : '' }}>
                                                {{ $student->student_id }} - {{ $student->full_name_latin }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('monitor_student_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="is_active" class="form-control-label">Holat</label>
                                    <select class="form-control @error('is_active') is-invalid @enderror"
                                            id="is_active" name="is_active">
                                        <option value="1" {{ old('is_active', $group->is_active) == 1 ? 'selected' : '' }}>Faol</option>
                                        <option value="0" {{ old('is_active', $group->is_active) == 0 ? 'selected' : '' }}>Nofaol</option>
                                    </select>
                                    @error('is_active')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="description" class="form-control-label">Tavsif</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror"
                                              id="description" name="description" rows="3">{{ old('description', $group->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <a href="{{ route('student-contingent.groups.show', $group) }}" class="btn btn-secondary me-2">Bekor qilish</a>
                            <button type="submit" class="btn btn-primary">Saqlash</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection