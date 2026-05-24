@extends('layouts.dashboard-new')

@section('title', 'Rollar boshqaruvi')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Rollar boshqaruvi</h1>
        <a href="{{ route('admin.settings.roles.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Yangi rol
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        @foreach($roles as $role)
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title">{{ $role->name }}</h5>
                        <p class="text-muted small mb-3">
                            <i class="fas fa-key"></i> {{ $role->permissions->count() }} ta ruxsat
                        </p>

                        <div class="mb-3" style="max-height: 150px; overflow-y: auto;">
                            @if($role->permissions->count() > 0)
                                <div class="d-flex flex-wrap gap-1">
                                    @foreach($role->permissions->take(10) as $permission)
                                        <span class="badge bg-info text-dark">{{ $permission->name }}</span>
                                    @endforeach
                                    @if($role->permissions->count() > 10)
                                        <span class="badge bg-secondary">+{{ $role->permissions->count() - 10 }} yana...</span>
                                    @endif
                                </div>
                            @else
                                <span class="text-muted">Ruxsatlar yo'q</span>
                            @endif
                        </div>
                    </div>
                    <div class="card-footer bg-transparent">
                        <a href="{{ route('admin.settings.roles.edit', $role) }}" class="btn btn-sm btn-warning">
                            <i class="fas fa-edit"></i> Tahrirlash
                        </a>
                        @if(!in_array($role->name, ['superadmin', 'admin']))
                            <form action="{{ route('admin.settings.roles.destroy', $role) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Rostdan ham o\'chirmoqchimisiz?')">
                                    <i class="fas fa-trash"></i> O'chirish
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
