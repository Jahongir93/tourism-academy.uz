@extends('layouts.dashboard-new')

@section('title', "Yangi ta'lim yo'nalishi")
@section('page-title', "Yangi ta'lim yo'nalishi")

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Ta'lim yo'nalishi ma'lumotlari</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('structure.academic.programs.store') }}" method="POST">
                        @csrf
                        
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Yo'nalish kodi <span class="text-danger">*</span></label>
                                <input type="text" name="code" class="form-control @error('code') is-invalid @enderror" 
                                       value="{{ old('code') }}" required placeholder="5330100">
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-8">
                                <label class="form-label">Yo'nalish nomi (O'zbekcha) <span class="text-danger">*</span></label>
                                <input type="text" name="name_uz" class="form-control @error('name_uz') is-invalid @enderror" 
                                       value="{{ old('name_uz') }}" required>
                                @error('name_uz')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Yo'nalish nomi (Ruscha)</label>
                                <input type="text" name="name_ru" class="form-control @error('name_ru') is-invalid @enderror" 
                                       value="{{ old('name_ru') }}">
                                @error('name_ru')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Yo'nalish nomi (Inglizcha)</label>
                                <input type="text" name="name_en" class="form-control @error('name_en') is-invalid @enderror" 
                                       value="{{ old('name_en') }}">
                                @error('name_en')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Fakultet <span class="text-danger">*</span></label>
                                <select name="faculty_id" class="form-select @error('faculty_id') is-invalid @enderror" required>
                                    <option value="">Fakultetni tanlang</option>
                                    @foreach($faculties as $faculty)
                                        <option value="{{ $faculty->id }}" {{ old('faculty_id') == $faculty->id ? 'selected' : '' }}>
                                            {{ $faculty->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('faculty_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
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
                                <label class="form-label">Ta'lim darajasi <span class="text-danger">*</span></label>
                                <select name="level" class="form-select @error('level') is-invalid @enderror" required>
                                    <option value="">Tanlang</option>
                                    <option value="bakalavriat" {{ old('level') == 'bakalavriat' ? 'selected' : '' }}>Bakalavriat</option>
                                    <option value="magistratura" {{ old('level') == 'magistratura' ? 'selected' : '' }}>Magistratura</option>
                                    <option value="doktorantura" {{ old('level') == 'doktorantura' ? 'selected' : '' }}>Doktorantura</option>
                                    <option value="ordinatura" {{ old('level') == 'ordinatura' ? 'selected' : '' }}>Ordinatura</option>
                                </select>
                                @error('level')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Ta'lim shakli <span class="text-danger">*</span></label>
                                <select name="education_form" class="form-select @error('education_form') is-invalid @enderror" required>
                                    <option value="">Tanlang</option>
                                    <option value="kunduzgi" {{ old('education_form') == 'kunduzgi' ? 'selected' : '' }}>Kunduzgi</option>
                                    <option value="kechki" {{ old('education_form') == 'kechki' ? 'selected' : '' }}>Kechki</option>
                                    <option value="sirtqi" {{ old('education_form') == 'sirtqi' ? 'selected' : '' }}>Sirtqi</option>
                                </select>
                                @error('education_form')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Ta'lim muddati (yil) <span class="text-danger">*</span></label>
                                <input type="number" name="duration_years" class="form-control @error('duration_years') is-invalid @enderror" 
                                       value="{{ old('duration_years', 4) }}" min="1" max="6" required>
                                @error('duration_years')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Jami kreditlar <span class="text-danger">*</span></label>
                                <input type="number" name="total_credits" class="form-control @error('total_credits') is-invalid @enderror" 
                                       value="{{ old('total_credits', 240) }}" min="60" max="300" required>
                                @error('total_credits')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-8">
                                <label class="form-label">Kvalifikatsiya</label>
                                <input type="text" name="qualification" class="form-control @error('qualification') is-invalid @enderror" 
                                       value="{{ old('qualification') }}" placeholder="Bakalavr">
                                @error('qualification')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tavsif</label>
                            <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="4">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('structure.academic.programs.index') }}" class="btn btn-secondary">
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
                    <h6>Yo'nalish kodi</h6>
                    <p class="text-muted small">Ta'lim yo'nalishining rasmiy kodi. Masalan: 5330100 - Informatika va axborot texnologiyalari</p>
                    
                    <h6 class="mt-3">Ta'lim darajasi</h6>
                    <ul class="text-muted small">
                        <li><strong>Bakalavriat</strong> - 4 yillik ta'lim</li>
                        <li><strong>Magistratura</strong> - 2 yillik ta'lim</li>
                        <li><strong>Doktorantura</strong> - 3 yillik ta'lim</li>
                    </ul>
                    
                    <h6 class="mt-3">Kreditlar</h6>
                    <p class="text-muted small">
                        1 kredit = 30 soat o'quv yuklamasi<br>
                        Bakalavriat: 240 kredit (4 yil)<br>
                        Magistratura: 120 kredit (2 yil)
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection