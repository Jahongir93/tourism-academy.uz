@extends('layouts.dashboard-new')

@section('title', 'Grantni tahrirlash')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Grantni tahrirlash</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('finance.scholarships.update', $scholarship) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label">Grant nomi <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $scholarship->name) }}" required>
                            @error('name')
                            <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tavsif</label>
                            <textarea name="description" class="form-control" rows="3">{{ old('description', $scholarship->description) }}</textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Summa <span class="text-danger">*</span></label>
                                <input type="number" name="amount" class="form-control" value="{{ old('amount', $scholarship->amount) }}" required min="0" step="0.01">
                                @error('amount')
                                <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Turi <span class="text-danger">*</span></label>
                                <select name="type" class="form-control" required>
                                    <option value="monthly" {{ old('type', $scholarship->type) == 'monthly' ? 'selected' : '' }}>Oylik</option>
                                    <option value="one_time" {{ old('type', $scholarship->type) == 'one_time' ? 'selected' : '' }}>Bir martalik</option>
                                    <option value="annual" {{ old('type', $scholarship->type) == 'annual' ? 'selected' : '' }}>Yillik</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Kategoriya <span class="text-danger">*</span></label>
                                <select name="category" class="form-control" required>
                                    <option value="academic" {{ old('category', $scholarship->category) == 'academic' ? 'selected' : '' }}>Akademik</option>
                                    <option value="social" {{ old('category', $scholarship->category) == 'social' ? 'selected' : '' }}>Ijtimoiy</option>
                                    <option value="sport" {{ old('category', $scholarship->category) == 'sport' ? 'selected' : '' }}>Sport</option>
                                    <option value="cultural" {{ old('category', $scholarship->category) == 'cultural' ? 'selected' : '' }}>Madaniy</option>
                                    <option value="other" {{ old('category', $scholarship->category) == 'other' ? 'selected' : '' }}>Boshqa</option>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Maksimal oluvchilar soni</label>
                                <input type="number" name="max_recipients" class="form-control" value="{{ old('max_recipients', $scholarship->max_recipients) }}" min="1">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Boshlanish sanasi <span class="text-danger">*</span></label>
                                <input type="date" name="start_date" class="form-control" value="{{ old('start_date', $scholarship->start_date->format('Y-m-d')) }}" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tugash sanasi</label>
                                <input type="date" name="end_date" class="form-control" value="{{ old('end_date', $scholarship->end_date?->format('Y-m-d')) }}">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Munosiblik mezonlari</label>
                            <textarea name="eligibility_criteria" class="form-control" rows="3">{{ old('eligibility_criteria', $scholarship->eligibility_criteria) }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Holat <span class="text-danger">*</span></label>
                            <select name="status" class="form-control" required>
                                <option value="active" {{ old('status', $scholarship->status) == 'active' ? 'selected' : '' }}>Faol</option>
                                <option value="inactive" {{ old('status', $scholarship->status) == 'inactive' ? 'selected' : '' }}>Nofaol</option>
                                <option value="expired" {{ old('status', $scholarship->status) == 'expired' ? 'selected' : '' }}>Tugagan</option>
                            </select>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('finance.scholarships.show', $scholarship) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Orqaga
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Yangilash
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
