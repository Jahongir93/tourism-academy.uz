@extends('layouts.dashboard-new')

@section('title', 'Rolni tahrirlash')

@section('content')
<div class="container-fluid px-4">
    <div class="mb-4">
        <h1 class="h3 mb-0">Rolni tahrirlash: {{ $role->name }}</h1>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.settings.roles.update', $role) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="name" class="form-label">Rol nomi <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $role->name) }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Ruxsatlar</label>
                    <div class="mb-2">
                        <button type="button" class="btn btn-sm btn-outline-primary" id="selectAll">
                            <i class="fas fa-check-square"></i> Barchasini tanlash
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="deselectAll">
                            <i class="fas fa-square"></i> Barchasini olib tashlash
                        </button>
                    </div>
                    <div class="card">
                        <div class="card-body" style="max-height: 500px; overflow-y: auto;">
                            @foreach($permissions as $group => $groupPermissions)
                                <div class="mb-4">
                                    <h6 class="text-uppercase text-muted mb-2">{{ $group }}</h6>
                                    <div class="row">
                                        @foreach($groupPermissions as $permission)
                                            <div class="col-md-4 col-lg-3">
                                                <div class="form-check">
                                                    <input class="form-check-input permission-checkbox" type="checkbox" name="permissions[]" value="{{ $permission->name }}" id="perm_{{ $permission->id }}" {{ in_array($permission->name, old('permissions', $rolePermissions)) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="perm_{{ $permission->id }}">
                                                        {{ $permission->name }}
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <hr>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Saqlash
                    </button>
                    <a href="{{ route('admin.settings.roles.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Bekor qilish
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('selectAll').addEventListener('click', function() {
        document.querySelectorAll('.permission-checkbox').forEach(cb => cb.checked = true);
    });

    document.getElementById('deselectAll').addEventListener('click', function() {
        document.querySelectorAll('.permission-checkbox').forEach(cb => cb.checked = false);
    });
</script>
@endpush
@endsection
