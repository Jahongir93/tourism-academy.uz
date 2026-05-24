@extends('layouts.dashboard-new')

@section('title', 'Yangi lavozim - Tuzilma - HEMIS')

@section('page-title', 'Yangi lavozim yaratish')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Lavozim ma'lumotlari</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('structure.positions.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <!-- Basic Information -->
                            <div class="col-md-6">
                                <h6 class="mb-3 text-muted">Asosiy ma'lumotlar</h6>
                                
                                <div class="mb-3">
                                    <label for="code" class="form-label">Lavozim kodi <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('code') is-invalid @enderror" 
                                           id="code" name="code" value="{{ old('code') }}" required
                                           placeholder="Masalan: RECTOR, DEAN, PROF">
                                    @error('code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="name_uz" class="form-label">Nomi (O'zbekcha) <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name_uz') is-invalid @enderror" 
                                           id="name_uz" name="name_uz" value="{{ old('name_uz') }}" required>
                                    @error('name_uz')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="name_ru" class="form-label">Nomi (Ruscha)</label>
                                    <input type="text" class="form-control @error('name_ru') is-invalid @enderror" 
                                           id="name_ru" name="name_ru" value="{{ old('name_ru') }}">
                                    @error('name_ru')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="name_en" class="form-label">Nomi (Inglizcha)</label>
                                    <input type="text" class="form-control @error('name_en') is-invalid @enderror" 
                                           id="name_en" name="name_en" value="{{ old('name_en') }}">
                                    @error('name_en')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="category" class="form-label">Kategoriya <span class="text-danger">*</span></label>
                                    <select class="form-select @error('category') is-invalid @enderror" 
                                            id="category" name="category" required>
                                        <option value="">Tanlang...</option>
                                        <option value="leadership" {{ old('category') == 'leadership' ? 'selected' : '' }}>Rahbariyat</option>
                                        <option value="academic" {{ old('category') == 'academic' ? 'selected' : '' }}>Akademik</option>
                                        <option value="administrative" {{ old('category') == 'administrative' ? 'selected' : '' }}>Ma'muriy</option>
                                        <option value="support" {{ old('category') == 'support' ? 'selected' : '' }}>Yordamchi</option>
                                    </select>
                                    @error('category')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="level" class="form-label">Daraja <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control @error('level') is-invalid @enderror" 
                                                   id="level" name="level" value="{{ old('level', 1) }}" min="1" max="10" required>
                                            <small class="form-text text-muted">1 - eng yuqori, 10 - eng past</small>
                                            @error('level')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="salary_grade" class="form-label">Maosh darajasi</label>
                                            <input type="text" class="form-control @error('salary_grade') is-invalid @enderror" 
                                                   id="salary_grade" name="salary_grade" value="{{ old('salary_grade') }}"
                                                   placeholder="A1, B2, C3">
                                            @error('salary_grade')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Requirements and Responsibilities -->
                            <div class="col-md-6">
                                <h6 class="mb-3 text-muted">Talablar va vazifalar</h6>
                                
                                <div class="mb-3">
                                    <label for="requirements" class="form-label">Talablar</label>
                                    <div id="requirements-container">
                                        <div class="input-group mb-2">
                                            <input type="text" class="form-control" name="requirements[]" 
                                                   placeholder="Talab kiriting...">
                                            <button type="button" class="btn btn-success" onclick="addRequirement()">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="responsibilities" class="form-label">Vazifalar</label>
                                    <div id="responsibilities-container">
                                        <div class="input-group mb-2">
                                            <input type="text" class="form-control" name="responsibilities[]" 
                                                   placeholder="Vazifa kiriting...">
                                            <button type="button" class="btn btn-success" onclick="addResponsibility()">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="reports_to" class="form-label">Kimga bo'ysunadi</label>
                                    <select class="form-select @error('reports_to') is-invalid @enderror" 
                                            id="reports_to" name="reports_to[]" multiple>
                                        @foreach($positions as $pos)
                                            <option value="{{ $pos->id }}">
                                                {{ $pos->name_uz }} ({{ $pos->category }}, {{ $pos->level }}-daraja)
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="form-text text-muted">Bir nechta tanlash mumkin</small>
                                    @error('reports_to')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Saqlash
                            </button>
                            <a href="{{ route('structure.positions.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Bekor qilish
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function addRequirement() {
    const container = document.getElementById('requirements-container');
    const div = document.createElement('div');
    div.className = 'input-group mb-2';
    div.innerHTML = `
        <input type="text" class="form-control" name="requirements[]" placeholder="Talab kiriting...">
        <button type="button" class="btn btn-danger" onclick="removeField(this)">
            <i class="fas fa-minus"></i>
        </button>
    `;
    container.appendChild(div);
}

function addResponsibility() {
    const container = document.getElementById('responsibilities-container');
    const div = document.createElement('div');
    div.className = 'input-group mb-2';
    div.innerHTML = `
        <input type="text" class="form-control" name="responsibilities[]" placeholder="Vazifa kiriting...">
        <button type="button" class="btn btn-danger" onclick="removeField(this)">
            <i class="fas fa-minus"></i>
        </button>
    `;
    container.appendChild(div);
}

function removeField(button) {
    button.parentElement.remove();
}
</script>
@endpush
@endsection