@extends('layouts.dashboard-new')

@section('title', $classroom->exists ? 'Xonani tahrirlash' : 'Yangi xona')
@section('page-title', $classroom->exists ? 'Xonani tahrirlash' : 'Yangi xona')

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h1 class="page-header-title">{{ $classroom->exists ? 'Xonani tahrirlash' : 'Yangi xona qo\'shish' }}</h1>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('classrooms.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left"></i> Orqaga</a>
    </div>
</div>

<form action="{{ $classroom->exists ? route('classrooms.update', $classroom) : route('classrooms.store') }}" method="POST">
    @csrf
    @if($classroom->exists) @method('PUT') @endif

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header"><i class="fas fa-door-open" style="color:var(--c-primary)"></i> Xona ma'lumotlari</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label">Xona nomi <span class="required">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', $classroom->name) }}" placeholder="masalan: 101-xona" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Kodi</label>
                            <input type="text" name="code" class="form-control @error('code') is-invalid @enderror"
                                   value="{{ old('code', $classroom->code) }}" placeholder="R-101">
                            @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Qavat</label>
                            <input type="number" name="floor" class="form-control" value="{{ old('floor', $classroom->floor) }}" min="0" max="50">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Sig'im (o'rin)</label>
                            <input type="number" name="capacity" class="form-control" value="{{ old('capacity', $classroom->capacity) }}" min="0" max="1000">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Turi</label>
                            <select name="type" class="form-select">
                                @foreach(['lecture'=>'Ma\'ruza','practice'=>'Amaliy','lab'=>'Laboratoriya','seminar'=>'Seminar','other'=>'Boshqa'] as $k=>$v)
                                    <option value="{{ $k }}" {{ old('type', $classroom->type)==$k?'selected':'' }}>{{ $v }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Izoh</label>
                            <textarea name="notes" rows="2" class="form-control">{{ old('notes', $classroom->notes) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card mb-3">
                <div class="card-header"><i class="fas fa-tools" style="color:var(--c-teal)"></i> Jihozlar</div>
                <div class="card-body d-grid gap-2">
                    <label class="d-flex align-items-center gap-2">
                        <input type="checkbox" name="has_projector" value="1" {{ old('has_projector', $classroom->has_projector) ? 'checked' : '' }}> Proyektor
                    </label>
                    <label class="d-flex align-items-center gap-2">
                        <input type="checkbox" name="has_computer" value="1" {{ old('has_computer', $classroom->has_computer) ? 'checked' : '' }}> Kompyuter
                    </label>
                    <label class="d-flex align-items-center gap-2">
                        <input type="checkbox" name="has_whiteboard" value="1" {{ old('has_whiteboard', $classroom->has_whiteboard) ? 'checked' : '' }}> Doska
                    </label>
                    <hr style="margin:6px 0">
                    <label class="d-flex align-items-center gap-2">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $classroom->is_active ?? true) ? 'checked' : '' }}> Faol (jadvalda ko'rinsin)
                    </label>
                </div>
            </div>
            <div class="card">
                <div class="card-body d-grid gap-2">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Saqlash</button>
                    <a href="{{ route('classrooms.index') }}" class="btn btn-outline-secondary">Bekor qilish</a>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
