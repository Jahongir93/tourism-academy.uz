@extends('layouts.dashboard-new')

@section('title', 'Transport Yo\'nalishlari')
@section('page-title', 'Transport Yo\'nalishlari')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('campus-tour.dashboard') }}">Kampus Turi</a></li>
                    <li class="breadcrumb-item active">Yo'nalishlar</li>
                </ol>
            </nav>
            <h1 class="h3 mb-0">
                <i class="fas fa-route text-warning me-2"></i>
                Transport Yo'nalishlari
            </h1>
        </div>
        <a href="{{ route('campus-tour.routes.create') }}" class="btn btn-warning">
            <i class="fas fa-plus me-2"></i>Yangi yo'nalish
        </a>
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Qidirish..."
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="type" class="form-select">
                        <option value="">Barcha turlar</option>
                        @foreach($types as $key => $type)
                            <option value="{{ $key }}" {{ request('type') === $key ? 'selected' : '' }}>
                                {{ $type['label'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">Barcha holatlar</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Faol</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Nofaol</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-outline-warning w-100">
                        <i class="fas fa-search me-1"></i> Qidirish
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Routes Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            @if($routes->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th width="60">#</th>
                                <th>Yo'nalish</th>
                                <th>Turi</th>
                                <th>Boshlang'ich</th>
                                <th>Oxirgi</th>
                                <th>Davomiylik</th>
                                <th>Narxi</th>
                                <th width="100">Holat</th>
                                <th width="150">Amallar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($routes as $route)
                                <tr>
                                    <td>{{ $route->order }}</td>
                                    <td><strong>{{ $route->title }}</strong></td>
                                    <td>
                                        <span class="badge" style="background-color: {{ $route->type_color }}">
                                            <i class="fas {{ $route->type_icon }} me-1"></i>
                                            {{ $route->type_label }}
                                        </span>
                                    </td>
                                    <td>{{ $route->start_point }}</td>
                                    <td>{{ $route->end_point }}</td>
                                    <td>{{ $route->duration ?? '-' }}</td>
                                    <td>{{ $route->formatted_price }}</td>
                                    <td>
                                        @if($route->is_active)
                                            <span class="badge bg-success">Faol</span>
                                        @else
                                            <span class="badge bg-secondary">Nofaol</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('campus-tour.routes.edit', $route) }}" class="btn btn-outline-secondary" title="Tahrirlash">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('campus-tour.routes.destroy', $route) }}" method="POST" class="d-inline"
                                                  onsubmit="return confirm('O\'chirmoqchimisiz?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger" title="O'chirish">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-3">
                    {{ $routes->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-route fa-4x text-muted mb-3"></i>
                    <h5>Hali yo'nalishlar yo'q</h5>
                    <p class="text-muted">Birinchi transport yo'nalishini qo'shing</p>
                    <a href="{{ route('campus-tour.routes.create') }}" class="btn btn-warning">
                        <i class="fas fa-plus me-2"></i>Yo'nalish qo'shish
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
