@extends('layouts.dashboard-new')

@section('title', 'Ruxsatlar boshqaruvi')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Ruxsatlar boshqaruvi</h1>
        <a href="{{ route('admin.settings.permissions.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Yangi ruxsat
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

    @foreach($permissions as $group => $groupPermissions)
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0 text-uppercase">{{ $group }}</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($groupPermissions as $permission)
                        <div class="col-md-4 col-lg-3 mb-3">
                            <div class="d-flex justify-content-between align-items-center p-2 border rounded">
                                <span>{{ $permission->name }}</span>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.settings.permissions.edit', $permission) }}" class="btn btn-warning btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.settings.permissions.destroy', $permission) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Rostdan ham o\'chirmoqchimisiz?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endforeach

    @if($permissions->count() == 0)
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> Hozircha ruxsatlar yo'q.
        </div>
    @endif
</div>
@endsection
