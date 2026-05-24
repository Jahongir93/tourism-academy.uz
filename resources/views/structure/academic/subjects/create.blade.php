@extends('layouts.dashboard-new')

@section('title', "Yangi fan qo'shish")
@section('page-title', "Yangi fan qo'shish")

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Fan ma'lumotlari</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('structure.academic.subjects.store') }}" method="POST">
                        @csrf
                        
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Fan kodi <span class="text-danger">*</span></label>
                                <input type="text" name="code" class="form-control @error('code') is-invalid @enderror" 
                                       value="{{ old('code') }}" required placeholder="CS101">
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-8">
                                <label class="form-label">Fan nomi (O'zbekcha) <span class="text-danger">*</span></label>
                                <input type="text" name="name_uz" class="form-control @error('name_uz') is-invalid @enderror" 
                                       value="{{ old('name_uz') }}" required>
                                @error('name_uz')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Fan nomi (Ruscha)</label>
                                <input type="text" name="name_ru" class="form-control @error('name_ru') is-invalid @enderror" 
                                       value="{{ old('name_ru') }}">
                                @error('name_ru')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Fan nomi (Inglizcha)</label>
                                <input type="text" name="name_en" class="form-control @error('name_en') is-invalid @enderror" 
                                       value="{{ old('name_en') }}">
                                @error('name_en')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Fan turi <span class="text-danger">*</span></label>
                                <select name="subject_type" class="form-select @error('subject_type') is-invalid @enderror" required>
                                    <option value="">Tanlang</option>
                                    <option value="majburiy" {{ old('subject_type') == 'majburiy' ? 'selected' : '' }}>Majburiy</option>
                                    <option value="tanlov" {{ old('subject_type') == 'tanlov' ? 'selected' : '' }}>Tanlov</option>
                                    <option value="umumkasbiy" {{ old('subject_type') == 'umumkasbiy' ? 'selected' : '' }}>Umumkasbiy</option>
                                    <option value="mutaxassislik" {{ old('subject_type') == 'mutaxassislik' ? 'selected' : '' }}>Mutaxassislik</option>
                                </select>
                                @error('subject_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-8">
                                <label class="form-label">Kafedra</label>
                                <select name="department_id" class="form-select @error('department_id') is-invalid @enderror">
                                    <option value="">Kafedrani tanlang</option>
                                    @foreach($departments as $department)
                                        <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                            {{ $department->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('department_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Kreditlar <span class="text-danger">*</span></label>
                                <input type="number" name="credits" class="form-control @error('credits') is-invalid @enderror" 
                                       value="{{ old('credits', 3) }}" min="1" max="10" required>
                                @error('credits')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Jami soatlar <span class="text-danger">*</span></label>
                                <input type="number" name="total_hours" class="form-control @error('total_hours') is-invalid @enderror" 
                                       value="{{ old('total_hours', 90) }}" min="30" max="300" required>
                                @error('total_hours')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Hisoblangan soatlar</label>
                                <div class="form-control-plaintext">
                                    <span id="calculated-hours" class="badge bg-info">0 soat</span>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Oldindan talab qilinadigan fanlar</label>
                            <select name="prerequisites[]" class="form-select @error('prerequisites') is-invalid @enderror" multiple size="4">
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}" {{ in_array($subject->id, old('prerequisites', [])) ? 'selected' : '' }}>
                                        {{ $subject->code }} - {{ $subject->name_uz }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Bir nechta fanni tanlash uchun Ctrl tugmasini bosib turing</small>
                            @error('prerequisites')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Fan tavsifi</label>
                            <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Fan maqsadlari</label>
                            <textarea name="objectives" class="form-control @error('objectives') is-invalid @enderror" rows="3">{{ old('objectives') }}</textarea>
                            @error('objectives')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Kutilayotgan natijalar</label>
                            <textarea name="outcomes" class="form-control @error('outcomes') is-invalid @enderror" rows="3">{{ old('outcomes') }}</textarea>
                            @error('outcomes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('structure.academic.subjects.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Bekor qilish
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Saqlash
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Yordam</h5>
                </div>
                <div class="card-body">
                    <h6>Fan kodi</h6>
                    <p class="text-muted small">Fan kodini qisqa va tushunarli qilib kiriting. Masalan: MAT101, CS202</p>
                    
                    <h6 class="mt-3">Kredit va soatlar</h6>
                    <p class="text-muted small">
                        1 kredit = 30 soat o'quv yuklamasi<br>
                        Masalan: 3 kredit = 90 soat
                    </p>
                    
                    <h6 class="mt-3">Fan turlari</h6>
                    <ul class="text-muted small">
                        <li><strong>Majburiy</strong> - Barcha talabalar uchun majburiy</li>
                        <li><strong>Tanlov</strong> - Talabalar tanlay oladigan fanlar</li>
                        <li><strong>Umumkasbiy</strong> - Kasbiy tayyorgarlik fanlari</li>
                        <li><strong>Mutaxassislik</strong> - Mutaxassislik fanlari</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const creditsInput = document.querySelector('input[name="credits"]');
    const hoursInput = document.querySelector('input[name="total_hours"]');
    const calculatedHours = document.getElementById('calculated-hours');
    
    function updateCalculatedHours() {
        const credits = parseInt(creditsInput.value) || 0;
        const expectedHours = credits * 30;
        calculatedHours.textContent = expectedHours + ' soat';
        
        const actualHours = parseInt(hoursInput.value) || 0;
        if (actualHours !== expectedHours && actualHours > 0) {
            calculatedHours.classList.remove('bg-info');
            calculatedHours.classList.add('bg-warning');
        } else {
            calculatedHours.classList.remove('bg-warning');
            calculatedHours.classList.add('bg-info');
        }
    }
    
    creditsInput.addEventListener('input', updateCalculatedHours);
    hoursInput.addEventListener('input', updateCalculatedHours);
    updateCalculatedHours();
});
</script>
@endpush
@endsection