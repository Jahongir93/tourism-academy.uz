@extends('layouts.dashboard-new')

@section('title', ($position->name_uz ?? $position->name) . ' - Tahrirlash')

@section('page-title', 'Lavozimni tahrirlash')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0">{{ $position->name_uz ?? $position->name }} - Tahrirlash</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('structure.positions.update', $position) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <!-- Basic Information -->
                            <div class="col-md-6">
                                <h6 class="mb-3 text-muted">Asosiy ma'lumotlar</h6>
                                
                                <div class="mb-3">
                                    <label for="code" class="form-label">Lavozim kodi <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('code') is-invalid @enderror" 
                                           id="code" name="code" value="{{ old('code', $position->code) }}" required
                                           placeholder="Masalan: RECTOR, DEAN, PROF">
                                    @error('code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="name_uz" class="form-label">Nomi (O'zbekcha) <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name_uz') is-invalid @enderror" 
                                           id="name_uz" name="name_uz" value="{{ old('name_uz', $position->name_uz) }}" required>
                                    @error('name_uz')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="name_ru" class="form-label">Nomi (Ruscha)</label>
                                    <input type="text" class="form-control @error('name_ru') is-invalid @enderror" 
                                           id="name_ru" name="name_ru" value="{{ old('name_ru', $position->name_ru) }}">
                                    @error('name_ru')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="name_en" class="form-label">Nomi (Inglizcha)</label>
                                    <input type="text" class="form-control @error('name_en') is-invalid @enderror" 
                                           id="name_en" name="name_en" value="{{ old('name_en', $position->name_en) }}">
                                    @error('name_en')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="category" class="form-label">Kategoriya <span class="text-danger">*</span></label>
                                    <select class="form-select @error('category') is-invalid @enderror" 
                                            id="category" name="category" required>
                                        <option value="">Tanlang...</option>
                                        <option value="leadership" {{ old('category', $position->category) == 'leadership' ? 'selected' : '' }}>Rahbariyat</option>
                                        <option value="academic" {{ old('category', $position->category) == 'academic' ? 'selected' : '' }}>Akademik</option>
                                        <option value="administrative" {{ old('category', $position->category) == 'administrative' ? 'selected' : '' }}>Ma'muriy</option>
                                        <option value="support" {{ old('category', $position->category) == 'support' ? 'selected' : '' }}>Yordamchi</option>
                                    </select>
                                    @error('category')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="level" class="form-label">Daraja <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control @error('level') is-invalid @enderror" 
                                                   id="level" name="level" value="{{ old('level', $position->level) }}" min="1" max="10" required>
                                            <small class="form-text text-muted">1 - eng yuqori, 10 - eng past</small>
                                            @error('level')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="salary_grade" class="form-label">Maosh darajasi</label>
                                            <input type="text" class="form-control @error('salary_grade') is-invalid @enderror" 
                                                   id="salary_grade" name="salary_grade" value="{{ old('salary_grade', $position->salary_grade) }}"
                                                   placeholder="A1, B2, C3">
                                            @error('salary_grade')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="is_active" class="form-label">Holati</label>
                                            <select class="form-select @error('is_active') is-invalid @enderror" 
                                                    id="is_active" name="is_active">
                                                <option value="1" {{ old('is_active', $position->is_active) ? 'selected' : '' }}>Faol</option>
                                                <option value="0" {{ !old('is_active', $position->is_active) ? 'selected' : '' }}>Nofaol</option>
                                            </select>
                                            @error('is_active')
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
                                        @if($position->requirements && count($position->requirements) > 0)
                                            @foreach($position->requirements as $requirement)
                                                @if($requirement)
                                                <div class="input-group mb-2">
                                                    <input type="text" class="form-control" name="requirements[]" 
                                                           value="{{ $requirement }}" placeholder="Talab kiriting...">
                                                    <button type="button" class="btn btn-danger" onclick="removeField(this)">
                                                        <i class="fas fa-minus"></i>
                                                    </button>
                                                </div>
                                                @endif
                                            @endforeach
                                        @endif
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
                                        @if($position->responsibilities && count($position->responsibilities) > 0)
                                            @foreach($position->responsibilities as $responsibility)
                                                @if($responsibility)
                                                <div class="input-group mb-2">
                                                    <input type="text" class="form-control" name="responsibilities[]" 
                                                           value="{{ $responsibility }}" placeholder="Vazifa kiriting...">
                                                    <button type="button" class="btn btn-danger" onclick="removeField(this)">
                                                        <i class="fas fa-minus"></i>
                                                    </button>
                                                </div>
                                                @endif
                                            @endforeach
                                        @endif
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
                                            <option value="{{ $pos->id }}" 
                                                {{ in_array($pos->id, $position->reportsTo->pluck('id')->toArray()) ? 'selected' : '' }}>
                                                {{ $pos->name_uz ?? $pos->name }} ({{ $pos->category }}, {{ $pos->level }}-daraja)
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
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save"></i> Yangilash
                            </button>
                            <a href="{{ route('structure.positions.show', $position) }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Bekor qilish
                            </a>
                            
                            @if(!$position->orgUnitPositions()->exists() && !$position->staffAllocations()->exists())
                            <button type="button" class="btn btn-danger float-end" 
                                    onclick="if(confirm('Lavozimni o\'chirishni xohlaysizmi?')) document.getElementById('delete-form').submit();">
                                <i class="fas fa-trash"></i> O'chirish
                            </button>
                            @endif
                        </div>
                    </form>

                    @if(!$position->orgUnitPositions()->exists() && !$position->staffAllocations()->exists())
                    <form id="delete-form" action="{{ route('structure.positions.destroy', $position) }}" method="POST" class="d-none">
                        @csrf
                        @method('DELETE')
                    </form>
                    @endif
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
    container.insertBefore(div, container.lastElementChild);
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
    container.insertBefore(div, container.lastElementChild);
}

function removeField(button) {
    button.parentElement.remove();
}
</script>
@endpush
@endsection