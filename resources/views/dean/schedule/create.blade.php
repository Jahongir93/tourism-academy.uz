@extends('layouts.dashboard-new')

@section('title', 'Yangi jadval')
@section('page-title', 'Yangi dars jadvali yaratish')

@section('content')
<div class="container-fluid">
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm bg-gradient-success text-white">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1"><i class="fas fa-plus-circle me-2"></i>Yangi jadval yaratish</h4>
                            <p class="mb-0 opacity-75">{{ $faculty?->name ?? 'Fakultet' }}</p>
                        </div>
                        <a href="{{ route('dean.schedule.index') }}" class="btn btn-light">
                            <i class="fas fa-arrow-left me-1"></i> Orqaga
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0"><i class="fas fa-calendar-plus text-success me-2"></i>Jadval ma'lumotlari</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('dean.schedule.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Guruh <span class="text-danger">*</span></label>
                        <select name="group_id" class="form-select @error('group_id') is-invalid @enderror" required>
                            <option value="">-- Guruhni tanlang --</option>
                            @foreach($groups as $group)
                            <option value="{{ $group->id }}" {{ old('group_id') == $group->id ? 'selected' : '' }}>
                                {{ $group->name }} ({{ $group->course }}-kurs)
                            </option>
                            @endforeach
                        </select>
                        @error('group_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label fw-semibold">O'quv yili</label>
                        <select name="academic_year_id" class="form-select @error('academic_year_id') is-invalid @enderror">
                            <option value="">-- Joriy yil --</option>
                            @foreach($academicYears as $year)
                            <option value="{{ $year->id }}" {{ old('academic_year_id') == $year->id || $year->is_current ? 'selected' : '' }}>
                                {{ $year->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('academic_year_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label fw-semibold">Semestr</label>
                        <select name="semester_id" class="form-select @error('semester_id') is-invalid @enderror">
                            <option value="1" {{ old('semester_id') == 1 ? 'selected' : '' }}>1-semestr</option>
                            <option value="2" {{ old('semester_id') == 2 ? 'selected' : '' }}>2-semestr</option>
                        </select>
                        @error('semester_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Jadval yaratilgandan so'ng, dars vaqtlarini qo'shish sahifasiga o'tasiz.
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('dean.schedule.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times me-1"></i> Bekor qilish
                    </a>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save me-1"></i> Yaratish va davom etish
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>.bg-gradient-success { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); }</style>
@endsection
