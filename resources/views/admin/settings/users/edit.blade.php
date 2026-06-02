@extends('layouts.dashboard-new')

@section('title', 'Foydalanuvchini tahrirlash')

@section('content')
<div class="container-fluid px-4">
    <div class="mb-4">
        <h1 class="h3 mb-0">Foydalanuvchini tahrirlash</h1>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.settings.users.update', $user) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="name" class="form-label">Ism <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Yangi parol</label>
                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password">
                    <small class="form-text text-muted">Bo'sh qoldiring, agar parolni o'zgartirmoqchi bo'lmasangiz</small>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">Parolni tasdiqlash</label>
                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                </div>

                <div class="mb-3">
                    @php $currentRoles = old('roles', $user->roles->pluck('name')->toArray()); @endphp
                    <label class="form-label">Rollar <span class="text-danger">*</span> <small class="text-muted">(bir nechta tanlash mumkin)</small></label>
                    <div class="row g-2" style="border:1px solid var(--c-border);border-radius:var(--r-sm);padding:12px;">
                        @foreach($roles as $role)
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="roles[]"
                                           value="{{ $role->name }}" id="role_{{ $role->id }}"
                                           {{ collect($currentRoles)->contains($role->name) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="role_{{ $role->id }}">{{ $role->name }}</label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @error('roles')<div class="text-danger mt-1" style="font-size:13px">{{ $message }}</div>@enderror
                    @error('roles.*')<div class="text-danger mt-1" style="font-size:13px">{{ $message }}</div>@enderror
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Saqlash
                    </button>
                    <a href="{{ route('admin.settings.users.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Bekor qilish
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
