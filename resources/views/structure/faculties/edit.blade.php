@extends('layouts.dashboard-new')

@section('title', $faculty->name_uz . ' - Tahrirlash')

@section('page-title', 'Fakultetni tahrirlash')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0">{{ $faculty->name_uz }} - Tahrirlash</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('structure.faculties.update', $faculty) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <!-- Basic Information -->
                            <div class="col-md-6">
                                <h6 class="mb-3 text-muted">Asosiy ma'lumotlar</h6>
                                
                                <div class="mb-3">
                                    <label for="code" class="form-label">Fakultet kodi <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('code') is-invalid @enderror" 
                                           id="code" name="code" value="{{ old('code', $faculty->code) }}" required>
                                    @error('code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="name_uz" class="form-label">Nomi (O'zbekcha) <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name_uz') is-invalid @enderror" 
                                           id="name_uz" name="name_uz" value="{{ old('name_uz', $faculty->name_uz) }}" required>
                                    @error('name_uz')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="name_ru" class="form-label">Nomi (Ruscha)</label>
                                    <input type="text" class="form-control @error('name_ru') is-invalid @enderror" 
                                           id="name_ru" name="name_ru" value="{{ old('name_ru', $faculty->name_ru) }}">
                                    @error('name_ru')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="name_en" class="form-label">Nomi (Inglizcha)</label>
                                    <input type="text" class="form-control @error('name_en') is-invalid @enderror" 
                                           id="name_en" name="name_en" value="{{ old('name_en', $faculty->name_en) }}">
                                    @error('name_en')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="abbreviation" class="form-label">Qisqartma</label>
                                    <input type="text" class="form-control @error('abbreviation') is-invalid @enderror" 
                                           id="abbreviation" name="abbreviation" value="{{ old('abbreviation', $faculty->abbreviation) }}"
                                           placeholder="Masalan: IT, MAT">
                                    @error('abbreviation')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="dean_user_id" class="form-label">Dekan</label>
                                    <select class="form-select @error('dean_user_id') is-invalid @enderror" 
                                            id="dean_user_id" name="dean_user_id">
                                        <option value="">Tanlanmagan</option>
                                        @foreach($deans as $dean)
                                            <option value="{{ $dean->id }}" {{ old('dean_user_id', $faculty->dean_user_id) == $dean->id ? 'selected' : '' }}>
                                                {{ $dean->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('dean_user_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="is_active" class="form-label">Holati</label>
                                    <select class="form-select @error('is_active') is-invalid @enderror" 
                                            id="is_active" name="is_active">
                                        <option value="1" {{ old('is_active', $faculty->is_active) ? 'selected' : '' }}>Faol</option>
                                        <option value="0" {{ !old('is_active', $faculty->is_active) ? 'selected' : '' }}>Nofaol</option>
                                    </select>
                                    @error('is_active')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Contact Information -->
                            <div class="col-md-6">
                                <h6 class="mb-3 text-muted">Aloqa ma'lumotlari</h6>
                                
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Telefon</label>
                                    <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                           id="phone" name="phone" value="{{ old('phone', $faculty->phone) }}"
                                           placeholder="+998 XX XXX XX XX">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                           id="email" name="email" value="{{ old('email', $faculty->email) }}"
                                           placeholder="fakultet@univ.uz">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="room" class="form-label">Xona</label>
                                    <input type="text" class="form-control @error('room') is-invalid @enderror" 
                                           id="room" name="room" value="{{ old('room', $faculty->room) }}"
                                           placeholder="A-201">
                                    @error('room')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="website" class="form-label">Veb-sayt</label>
                                    <input type="url" class="form-control @error('website') is-invalid @enderror" 
                                           id="website" name="website" value="{{ old('website', $faculty->website) }}"
                                           placeholder="https://fakultet.univ.uz">
                                    @error('website')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="established_date" class="form-label">Tashkil etilgan sana</label>
                                    <input type="date" class="form-control @error('established_date') is-invalid @enderror" 
                                           id="established_date" name="established_date" 
                                           value="{{ old('established_date', optional($faculty->established_date)->format('Y-m-d')) }}">
                                    @error('established_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Capacity Information -->
                        <h6 class="mb-3 text-muted">Sig'im ma'lumotlari</h6>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="student_capacity" class="form-label">Talabalar sig'imi</label>
                                    <input type="number" class="form-control @error('student_capacity') is-invalid @enderror" 
                                           id="student_capacity" name="student_capacity" 
                                           value="{{ old('student_capacity', $faculty->student_capacity) }}" min="0">
                                    @error('student_capacity')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="teacher_capacity" class="form-label">O'qituvchilar sig'imi</label>
                                    <input type="number" class="form-control @error('teacher_capacity') is-invalid @enderror" 
                                           id="teacher_capacity" name="teacher_capacity" 
                                           value="{{ old('teacher_capacity', $faculty->teacher_capacity) }}" min="0">
                                    @error('teacher_capacity')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="state_funded_places" class="form-label">Grant o'rinlari</label>
                                    <input type="number" class="form-control @error('state_funded_places') is-invalid @enderror" 
                                           id="state_funded_places" name="state_funded_places" 
                                           value="{{ old('state_funded_places', $faculty->state_funded_places) }}" min="0">
                                    @error('state_funded_places')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="contract_places" class="form-label">Kontrakt o'rinlari</label>
                                    <input type="number" class="form-control @error('contract_places') is-invalid @enderror" 
                                           id="contract_places" name="contract_places" 
                                           value="{{ old('contract_places', $faculty->contract_places) }}" min="0">
                                    @error('contract_places')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save"></i> Yangilash
                            </button>
                            <a href="{{ route('structure.faculties.show', $faculty) }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Bekor qilish
                            </a>
                            
                            @if(!$faculty->departments()->exists() && !$faculty->specialties()->exists())
                            <button type="button" class="btn btn-danger float-end" 
                                    onclick="if(confirm('Fakultetni o\'chirishni xohlaysizmi?')) document.getElementById('delete-form').submit();">
                                <i class="fas fa-trash"></i> O'chirish
                            </button>
                            @endif
                        </div>
                    </form>

                    @if(!$faculty->departments()->exists() && !$faculty->specialties()->exists())
                    <form id="delete-form" action="{{ route('structure.faculties.destroy', $faculty) }}" method="POST" class="d-none">
                        @csrf
                        @method('DELETE')
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection