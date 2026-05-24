@extends('layouts.dashboard-new')

@section('title', 'Binolar')
@section('page-title', 'Kampus Binolari')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('campus-tour.dashboard') }}">Kampus Turi</a></li>
                    <li class="breadcrumb-item active">Binolar</li>
                </ol>
            </nav>
            <h1 class="h3 mb-0">
                <i class="fas fa-building text-success me-2"></i>
                Kampus Binolari
            </h1>
        </div>
        <a href="{{ route('campus-tour.buildings.create') }}" class="btn btn-success">
            <i class="fas fa-plus me-2"></i>Yangi bino
        </a>
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-6">
                    <input type="text" name="search" class="form-control" placeholder="Qidirish..."
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-4">
                    <select name="status" class="form-select">
                        <option value="">Barcha holatlar</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Faol</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Nofaol</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-outline-success w-100">
                        <i class="fas fa-search me-1"></i> Qidirish
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Buildings Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            @if($buildings->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th width="60">#</th>
                                <th width="80">Rasm</th>
                                <th>Nomi</th>
                                <th>Koordinatalar</th>
                                <th>360° Tur</th>
                                <th width="100">Holat</th>
                                <th width="150">Amallar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($buildings as $building)
                                <tr>
                                    <td>{{ $building->order }}</td>
                                    <td>
                                        @if($building->image)
                                            <img src="{{ $building->image_url }}" alt="{{ $building->title }}"
                                                 class="rounded" style="width: 50px; height: 50px; object-fit: cover;">
                                        @else
                                            <div class="bg-secondary rounded d-flex align-items-center justify-content-center"
                                                 style="width: 50px; height: 50px;">
                                                <i class="fas fa-building text-white-50"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <strong>{{ $building->title }}</strong>
                                        @if($building->short_description)
                                            <br><small class="text-muted">{{ Str::limit($building->short_description, 50) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($building->marker_x && $building->marker_y)
                                            <span class="badge bg-info">X: {{ $building->marker_x }}%, Y: {{ $building->marker_y }}%</span>
                                        @elseif($building->latitude && $building->longitude)
                                            <span class="badge bg-success">{{ $building->latitude }}, {{ $building->longitude }}</span>
                                        @else
                                            <span class="badge bg-secondary">Kiritilmagan</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($building->panorama)
                                            <a href="{{ route('campus-tour.panoramas.preview', $building->panorama) }}" class="text-primary">
                                                <i class="fas fa-vr-cardboard me-1"></i>{{ Str::limit($building->panorama->title, 20) }}
                                            </a>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($building->is_active)
                                            <span class="badge bg-success">Faol</span>
                                        @else
                                            <span class="badge bg-secondary">Nofaol</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('campus-tour.buildings.edit', $building) }}" class="btn btn-outline-secondary" title="Tahrirlash">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('campus-tour.buildings.destroy', $building) }}" method="POST" class="d-inline"
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
                    {{ $buildings->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-building fa-4x text-muted mb-3"></i>
                    <h5>Hali binolar yo'q</h5>
                    <p class="text-muted">Birinchi binoni qo'shing</p>
                    <a href="{{ route('campus-tour.buildings.create') }}" class="btn btn-success">
                        <i class="fas fa-plus me-2"></i>Bino qo'shish
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
