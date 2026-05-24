@extends('layouts.dashboard-new')

@section('title', "Ta'lim yo'nalishini tahrirlash")
@section('page-title', "Ta'lim yo'nalishini tahrirlash")

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('structure.academic.programs.index') }}">Ta'lim yo'nalishlari</a></li>
                    <li class="breadcrumb-item active">Tahrirlash</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0">{{ $program->name_uz }} - tahrirlash</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('structure.academic.programs.update', $program) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <!-- Basic Information -->
                    <div class="col-md-6">
                        <h6 class="text-primary mb-3">Asosiy ma'lumotlar</h6>
                        
                        <div class="mb-3">
                            <label class="form-label">Yo'nalish kodi <span class="text-danger">*</span></label>
                            <input type="text" name="code" class="form-control @error('code') is-invalid @enderror" 
                                   value="{{ old('code', $program->code) }}" required>
                            @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Yo'nalish nomi (uz) <span class="text-danger">*</span></label>
                            <input type="text" name="name_uz" class="form-control @error('name_uz') is-invalid @enderror" 
                                   value="{{ old('name_uz', $program->name_uz) }}" required>
                            @error('name_uz')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Yo'nalish nomi (ru)</label>
                            <input type="text" name="name_ru" class="form-control @error('name_ru') is-invalid @enderror" 
                                   value="{{ old('name_ru', $program->name_ru) }}">
                            @error('name_ru')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Yo'nalish nomi (en)</label>
                            <input type="text" name="name_en" class="form-control @error('name_en') is-invalid @enderror" 
                                   value="{{ old('name_en', $program->name_en) }}">
                            @error('name_en')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Tavsif</label>
                            <textarea name="description" class="form-control @error('description') is-invalid @enderror" 
                                      rows="3">{{ old('description', $program->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <!-- Academic Information -->
                    <div class="col-md-6">
                        <h6 class="text-primary mb-3">Ta'lim ma'lumotlari</h6>
                        
                        <div class="mb-3">
                            <label class="form-label">Fakultet <span class="text-danger">*</span></label>
                            <select name="faculty_id" class="form-select @error('faculty_id') is-invalid @enderror" required>
                                <option value="">Fakultetni tanlang</option>
                                @foreach($faculties as $faculty)
                                    <option value="{{ $faculty->id }}" {{ old('faculty_id', $program->faculty_id) == $faculty->id ? 'selected' : '' }}>
                                        {{ $faculty->name_uz }}
                                    </option>
                                @endforeach
                            </select>
                            @error('faculty_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Kafedra</label>
                            <select name="department_id" class="form-select @error('department_id') is-invalid @enderror">
                                <option value="">Kafedrani tanlang</option>
                                @foreach($departments as $department)
                                    <option value="{{ $department->id }}" {{ old('department_id', $program->department_id) == $department->id ? 'selected' : '' }}>
                                        {{ $department->name_uz }}
                                    </option>
                                @endforeach
                            </select>
                            @error('department_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Daraja <span class="text-danger">*</span></label>
                            <select name="degree" class="form-select @error('degree') is-invalid @enderror" required>
                                <option value="bachelor" {{ old('degree', $program->degree) == 'bachelor' ? 'selected' : '' }}>Bakalavr</option>
                                <option value="master" {{ old('degree', $program->degree) == 'master' ? 'selected' : '' }}>Magistr</option>
                                <option value="phd" {{ old('degree', $program->degree) == 'phd' ? 'selected' : '' }}>PhD</option>
                                <option value="dsc" {{ old('degree', $program->degree) == 'dsc' ? 'selected' : '' }}>DSc</option>
                            </select>
                            @error('degree')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Ta'lim shakli <span class="text-danger">*</span></label>
                            <select name="education_form" class="form-select @error('education_form') is-invalid @enderror" required>
                                <option value="kunduzgi" {{ old('education_form', $program->education_form) == 'kunduzgi' ? 'selected' : '' }}>Kunduzgi</option>
                                <option value="kechki" {{ old('education_form', $program->education_form) == 'kechki' ? 'selected' : '' }}>Kechki</option>
                                <option value="sirtqi" {{ old('education_form', $program->education_form) == 'sirtqi' ? 'selected' : '' }}>Sirtqi</option>
                            </select>
                            @error('education_form')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Ta'lim tili <span class="text-danger">*</span></label>
                            <select name="language" class="form-select @error('language') is-invalid @enderror" required>
                                <option value="uz" {{ old('language', $program->language) == 'uz' ? 'selected' : '' }}>O'zbek</option>
                                <option value="ru" {{ old('language', $program->language) == 'ru' ? 'selected' : '' }}>Rus</option>
                                <option value="en" {{ old('language', $program->language) == 'en' ? 'selected' : '' }}>Ingliz</option>
                            </select>
                            @error('language')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">O'quv muddati (yil) <span class="text-danger">*</span></label>
                            <input type="number" name="duration" class="form-control @error('duration') is-invalid @enderror" 
                                   value="{{ old('duration', $program->duration) }}" min="1" max="7" required>
                            @error('duration')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Jami kredit <span class="text-danger">*</span></label>
                            <input type="number" name="total_credits" class="form-control @error('total_credits') is-invalid @enderror" 
                                   value="{{ old('total_credits', $program->total_credits) }}" min="60" max="300" required>
                            @error('total_credits')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" name="is_active" class="form-check-input" id="is_active" 
                                       value="1" {{ old('is_active', $program->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Faol holat
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="text-end">
                    <a href="{{ route('structure.academic.programs.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Bekor qilish
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Saqlash
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Update departments based on selected faculty
    const facultySelect = document.querySelector('select[name="faculty_id"]');
    const departmentSelect = document.querySelector('select[name="department_id"]');
    
    facultySelect.addEventListener('change', function() {
        const facultyId = this.value;
        
        if (facultyId) {
            fetch(`/api/faculties/${facultyId}/departments`)
                .then(response => response.json())
                .then(data => {
                    departmentSelect.innerHTML = '<option value="">Kafedrani tanlang</option>';
                    data.forEach(department => {
                        const option = new Option(department.name_uz, department.id);
                        departmentSelect.add(option);
                    });
                })
                .catch(error => console.error('Error loading departments:', error));
        } else {
            departmentSelect.innerHTML = '<option value="">Kafedrani tanlang</option>';
        }
    });
});
</script>
@endpush
@endsection