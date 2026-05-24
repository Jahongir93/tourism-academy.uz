@extends('layouts.dashboard-new')

@section('title', $department->name_uz . ' - Tahrirlash')

@section('page-title', 'Kafedrani tahrirlash')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0">{{ $department->name_uz }} - Tahrirlash</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('structure.departments.update', $department) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <!-- Basic Information -->
                            <div class="col-md-6">
                                <h6 class="mb-3 text-muted">Asosiy ma'lumotlar</h6>
                                
                                <div class="mb-3">
                                    <label for="faculty_id" class="form-label">Fakultet <span class="text-danger">*</span></label>
                                    <select class="form-select @error('faculty_id') is-invalid @enderror" 
                                            id="faculty_id" name="faculty_id" required>
                                        <option value="">Tanlang...</option>
                                        @foreach($faculties as $faculty)
                                            <option value="{{ $faculty->id }}" 
                                                {{ old('faculty_id', $department->faculty_id) == $faculty->id ? 'selected' : '' }}>
                                                {{ $faculty->name_uz }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('faculty_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="code" class="form-label">Kafedra kodi <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('code') is-invalid @enderror" 
                                           id="code" name="code" value="{{ old('code', $department->code) }}" required>
                                    @error('code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="name_uz" class="form-label">Nomi (O'zbekcha) <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name_uz') is-invalid @enderror" 
                                           id="name_uz" name="name_uz" value="{{ old('name_uz', $department->name_uz) }}" required>
                                    @error('name_uz')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="name_ru" class="form-label">Nomi (Ruscha)</label>
                                    <input type="text" class="form-control @error('name_ru') is-invalid @enderror" 
                                           id="name_ru" name="name_ru" value="{{ old('name_ru', $department->name_ru) }}">
                                    @error('name_ru')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="name_en" class="form-label">Nomi (Inglizcha)</label>
                                    <input type="text" class="form-control @error('name_en') is-invalid @enderror" 
                                           id="name_en" name="name_en" value="{{ old('name_en', $department->name_en) }}">
                                    @error('name_en')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="short_name" class="form-label">Qisqa nomi</label>
                                    <input type="text" class="form-control @error('short_name') is-invalid @enderror" 
                                           id="short_name" name="short_name" value="{{ old('short_name', $department->short_name) }}">
                                    @error('short_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="is_active" class="form-label">Holati</label>
                                    <select class="form-select @error('is_active') is-invalid @enderror" 
                                            id="is_active" name="is_active">
                                        <option value="1" {{ old('is_active', $department->is_active) ? 'selected' : '' }}>Faol</option>
                                        <option value="0" {{ !old('is_active', $department->is_active) ? 'selected' : '' }}>Nofaol</option>
                                    </select>
                                    @error('is_active')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Additional Information -->
                            <div class="col-md-6">
                                <h6 class="mb-3 text-muted">Qo'shimcha ma'lumotlar</h6>
                                
                                <div class="mb-3">
                                    <label for="type" class="form-label">Kafedra turi</label>
                                    <select class="form-select @error('type') is-invalid @enderror" 
                                            id="type" name="type">
                                        <option value="">Tanlang...</option>
                                        <option value="umumkasbiy" {{ old('type', $department->type) == 'umumkasbiy' ? 'selected' : '' }}>Umumkasbiy</option>
                                        <option value="ixtisoslik" {{ old('type', $department->type) == 'ixtisoslik' ? 'selected' : '' }}>Ixtisoslik</option>
                                        <option value="umumtalim" {{ old('type', $department->type) == 'umumtalim' ? 'selected' : '' }}>Umumta'lim</option>
                                    </select>
                                    @error('type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="head_id" class="form-label">Kafedra mudiri</label>
                                    <select class="form-select @error('head_id') is-invalid @enderror" 
                                            id="head_id" name="head_id">
                                        <option value="">Tanlanmagan</option>
                                        @foreach($heads as $head)
                                            <option value="{{ $head->id }}" {{ old('head_id', $department->head_id) == $head->id ? 'selected' : '' }}>
                                                {{ $head->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('head_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="room_number" class="form-label">Xona raqami</label>
                                    <input type="text" class="form-control @error('room_number') is-invalid @enderror" 
                                           id="room_number" name="room_number" value="{{ old('room_number', $department->room_number) }}"
                                           placeholder="A-301">
                                    @error('room_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="phone" class="form-label">Telefon</label>
                                    <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                           id="phone" name="phone" value="{{ old('phone', $department->phone) }}"
                                           placeholder="+998 XX XXX XX XX">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                           id="email" name="email" value="{{ old('email', $department->email) }}"
                                           placeholder="kafedra@univ.uz">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="established_date" class="form-label">Tashkil etilgan sana</label>
                                            <input type="date" class="form-control @error('established_date') is-invalid @enderror" 
                                                   id="established_date" name="established_date" 
                                                   value="{{ old('established_date', optional($department->established_date)->format('Y-m-d')) }}">
                                            @error('established_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="staff_capacity" class="form-label">Xodimlar sig'imi</label>
                                            <input type="number" class="form-control @error('staff_capacity') is-invalid @enderror" 
                                                   id="staff_capacity" name="staff_capacity" 
                                                   value="{{ old('staff_capacity', $department->staff_capacity) }}" min="0">
                                            @error('staff_capacity')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save"></i> Yangilash
                            </button>
                            <a href="{{ route('structure.departments.show', $department) }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Bekor qilish
                            </a>
                            
                            @if(!$department->specialties()->exists())
                            <button type="button" class="btn btn-danger float-end" 
                                    onclick="if(confirm('Kafedrani o\'chirishni xohlaysizmi?')) document.getElementById('delete-form').submit();">
                                <i class="fas fa-trash"></i> O'chirish
                            </button>
                            @endif
                        </div>
                    </form>

                    @if(!$department->specialties()->exists())
                    <form id="delete-form" action="{{ route('structure.departments.destroy', $department) }}" method="POST" class="d-none">
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