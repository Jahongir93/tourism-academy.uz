@extends('layouts.dashboard-new')

@section('title', 'Ruxsatni tahrirlash')

@section('content')
<div class="container-fluid px-4">
    <div class="mb-4">
        <h1 class="h3 mb-0">Ruxsatni tahrirlash</h1>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.settings.permissions.update', $permission) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="name" class="form-label">Ruxsat nomi <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $permission->name) }}" required>
                    <small class="form-text text-muted">Masalan: view_dashboard, create_users, manage_students</small>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Saqlash
                    </button>
                    <a href="{{ route('admin.settings.permissions.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Bekor qilish
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
