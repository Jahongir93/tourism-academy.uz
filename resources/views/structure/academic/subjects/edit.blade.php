@extends('layouts.dashboard-new')

@section('title', "Fanni tahrirlash - " . $subject->name_uz)
@section('page-title', "Fanni tahrirlash")

@section('content')
<div class="container-fluid">
    <!-- Breadcrumb -->
    <div class="row mb-3">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('structure.academic.subjects.index') }}">Fanlar</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('structure.academic.subjects.show', $subject) }}">{{ $subject->name_uz }}</a></li>
                    <li class="breadcrumb-item active">Tahrirlash</li>
                </ol>
            </nav>
        </div>
    </div>

    <form action="{{ route('structure.academic.subjects.update', $subject) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="row">
            <!-- Basic Information -->
            <div class="col-md-6">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Asosiy ma'lumotlar</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Fan kodi <span class="text-danger">*</span></label>
                            <input type="text" name="code" class="form-control @error('code') is-invalid @enderror" 
                                   value="{{ old('code', $subject->code) }}" required>
                            @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Fan nomi (uz) <span class="text-danger">*</span></label>
                            <input type="text" name="name_uz" class="form-control @error('name_uz') is-invalid @enderror" 
                                   value="{{ old('name_uz', $subject->name_uz) }}" required>
                            @error('name_uz')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Fan nomi (ru)</label>
                            <input type="text" name="name_ru" class="form-control @error('name_ru') is-invalid @enderror" 
                                   value="{{ old('name_ru', $subject->name_ru) }}">
                            @error('name_ru')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Fan nomi (en)</label>
                            <input type="text" name="name_en" class="form-control @error('name_en') is-invalid @enderror" 
                                   value="{{ old('name_en', $subject->name_en) }}">
                            @error('name_en')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Fan turi <span class="text-danger">*</span></label>
                            <select name="subject_type" class="form-select @error('subject_type') is-invalid @enderror" required>
                                <option value="majburiy" {{ old('subject_type', $subject->subject_type) == 'majburiy' ? 'selected' : '' }}>Majburiy</option>
                                <option value="tanlov" {{ old('subject_type', $subject->subject_type) == 'tanlov' ? 'selected' : '' }}>Tanlov</option>
                                <option value="umumkasbiy" {{ old('subject_type', $subject->subject_type) == 'umumkasbiy' ? 'selected' : '' }}>Umumkasbiy</option>
                                <option value="mutaxassislik" {{ old('subject_type', $subject->subject_type) == 'mutaxassislik' ? 'selected' : '' }}>Mutaxassislik</option>
                            </select>
                            @error('subject_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Kategoriya</label>
                            <select name="category" class="form-select @error('category') is-invalid @enderror">
                                <option value="general" {{ old('category', $subject->category) == 'general' ? 'selected' : '' }}>Umumiy</option>
                                <option value="major" {{ old('category', $subject->category) == 'major' ? 'selected' : '' }}>Asosiy</option>
                                <option value="elective" {{ old('category', $subject->category) == 'elective' ? 'selected' : '' }}>Tanlov</option>
                            </select>
                            @error('category')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Academic Information -->
            <div class="col-md-6">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">Ta'lim ma'lumotlari</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Kafedra</label>
                            <select name="department_id" class="form-select @error('department_id') is-invalid @enderror">
                                <option value="">Kafedrani tanlang</option>
                                @foreach($departments as $department)
                                    <option value="{{ $department->id }}" {{ old('department_id', $subject->department_id) == $department->id ? 'selected' : '' }}>
                                        {{ $department->name_uz }}
                                    </option>
                                @endforeach
                            </select>
                            @error('department_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Kredit <span class="text-danger">*</span></label>
                            <input type="number" name="credits" class="form-control @error('credits') is-invalid @enderror" 
                                   value="{{ old('credits', $subject->credits) }}" min="1" max="10" required>
                            @error('credits')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Jami soat <span class="text-danger">*</span></label>
                            <input type="number" name="total_hours" class="form-control @error('total_hours') is-invalid @enderror" 
                                   value="{{ old('total_hours', $subject->total_hours) }}" min="30" max="300" required>
                            @error('total_hours')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" name="active" class="form-check-input" id="active" 
                                       value="1" {{ old('active', $subject->active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="active">
                                    Faol holat
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Description and Details -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">Qo'shimcha ma'lumotlar</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Tavsif</label>
                        <textarea name="description" class="form-control @error('description') is-invalid @enderror" 
                                  rows="3">{{ old('description', $subject->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Maqsadlar</label>
                        <textarea name="objectives" class="form-control @error('objectives') is-invalid @enderror" 
                                  rows="4">{{ old('objectives', $subject->objectives) }}</textarea>
                        @error('objectives')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Kutilayotgan natijalar</label>
                        <textarea name="outcomes" class="form-control @error('outcomes') is-invalid @enderror" 
                                  rows="4">{{ old('outcomes', $subject->outcomes) }}</textarea>
                        @error('outcomes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Prerequisites -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-warning">
                <h5 class="mb-0">Oldindan o'rganilishi kerak bo'lgan fanlar</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Fanlarni tanlang</label>
                    <select name="prerequisites[]" class="form-select @error('prerequisites') is-invalid @enderror" multiple size="6">
                        @foreach($subjects as $prereqSubject)
                            @if($prereqSubject->id != $subject->id)
                                <option value="{{ $prereqSubject->id }}" 
                                    {{ (collect(old('prerequisites', $subject->prerequisiteSubjects ? $subject->prerequisiteSubjects->pluck('id')->toArray() : []))->contains($prereqSubject->id)) ? 'selected' : '' }}>
                                    {{ $prereqSubject->code }} - {{ $prereqSubject->name_uz }}
                                </option>
                            @endif
                        @endforeach
                    </select>
                    <small class="text-muted">Bir nechta fanni tanlash uchun Ctrl tugmasini bosib turing</small>
                    @error('prerequisites')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
        
        <!-- Actions -->
        <div class="text-end">
            <a href="{{ route('structure.academic.subjects.show', $subject) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Bekor qilish
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Saqlash
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-calculate hours based on credits
    const creditsInput = document.querySelector('input[name="credits"]');
    const hoursInput = document.querySelector('input[name="total_hours"]');
    
    creditsInput.addEventListener('change', function() {
        const credits = parseInt(this.value) || 0;
        const suggestedHours = credits * 30; // 1 kredit = 30 soat
        
        if (!hoursInput.value || confirm(`${credits} kredit uchun ${suggestedHours} soat tavsiya etiladi. Qo'llashni xohlaysizmi?`)) {
            hoursInput.value = suggestedHours;
        }
    });
});
</script>
@endpush
@endsection