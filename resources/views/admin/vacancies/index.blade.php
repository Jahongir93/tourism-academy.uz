@extends('layouts.dashboard-new')

@section('title', 'Vakansiyalar')
@section('page-title', 'Vakansiyalar')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('employees.index') }}">Xodimlar</a></li>
                    <li class="breadcrumb-item active">Vakansiyalar</li>
                </ol>
            </nav>
            <h1 class="h3 mb-0">
                <i class="fas fa-briefcase text-primary me-2"></i>
                Vakansiyalar
            </h1>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.vacancy-applications.index') }}" class="btn btn-outline-info">
                <i class="fas fa-users me-2"></i>Nomzodlar
            </a>
            <a href="{{ route('admin.vacancies.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Yangi vakansiya
            </a>
        </div>
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
                    <select name="status" class="form-select">
                        <option value="">Barcha holatlar</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Faol</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Nofaol</option>
                        <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Muddati tugagan</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="employment_type" class="form-select">
                        <option value="">Barcha turlar</option>
                        @foreach(\App\Models\Vacancy::EMPLOYMENT_TYPES as $key => $label)
                            <option value="{{ $key }}" {{ request('employment_type') === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-outline-primary w-100">
                        <i class="fas fa-search me-1"></i> Qidirish
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Vacancies Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            @if($vacancies->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Vakansiya</th>
                                <th>Bo'lim</th>
                                <th>Turi</th>
                                <th>Muddat</th>
                                <th>Arizalar</th>
                                <th width="100">Holat</th>
                                <th width="150">Amallar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($vacancies as $vacancy)
                                <tr>
                                    <td>
                                        <strong>{{ $vacancy->title }}</strong>
                                        @if($vacancy->is_featured)
                                            <span class="badge bg-warning text-dark ms-1"><i class="fas fa-star"></i></span>
                                        @endif
                                        @if($vacancy->positions_count > 1)
                                            <br><small class="text-muted">{{ $vacancy->positions_count }} ta o'rin</small>
                                        @endif
                                    </td>
                                    <td>{{ $vacancy->department ?? '-' }}</td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $vacancy->employment_type_label }}</span>
                                    </td>
                                    <td>
                                        @if($vacancy->deadline)
                                            @if($vacancy->is_expired)
                                                <span class="text-danger">
                                                    <i class="fas fa-exclamation-circle me-1"></i>
                                                    {{ $vacancy->deadline->format('d.m.Y') }}
                                                </span>
                                            @else
                                                {{ $vacancy->deadline->format('d.m.Y') }}
                                            @endif
                                        @else
                                            <span class="text-muted">Cheksiz</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.vacancy-applications.index', ['vacancy_id' => $vacancy->id]) }}" class="text-decoration-none">
                                            <span class="badge bg-primary">{{ $vacancy->applications_count }}</span>
                                            @if($vacancy->new_applications_count > 0)
                                                <span class="badge bg-danger">{{ $vacancy->new_applications_count }} yangi</span>
                                            @endif
                                        </a>
                                    </td>
                                    <td>
                                        @if($vacancy->is_active && !$vacancy->is_expired)
                                            <span class="badge bg-success">Faol</span>
                                        @elseif($vacancy->is_expired)
                                            <span class="badge bg-warning text-dark">Tugagan</span>
                                        @else
                                            <span class="badge bg-secondary">Nofaol</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('admin.vacancies.edit', $vacancy) }}" class="btn btn-outline-secondary" title="Tahrirlash">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.vacancies.toggle-status', $vacancy) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-outline-{{ $vacancy->is_active ? 'warning' : 'success' }}"
                                                        title="{{ $vacancy->is_active ? 'O\'chirish' : 'Yoqish' }}">
                                                    <i class="fas fa-{{ $vacancy->is_active ? 'pause' : 'play' }}"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.vacancies.destroy', $vacancy) }}" method="POST" class="d-inline"
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
                    {{ $vacancies->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-briefcase fa-4x text-muted mb-3"></i>
                    <h5>Hali vakansiyalar yo'q</h5>
                    <p class="text-muted">Birinchi vakansiyani qo'shing</p>
                    <a href="{{ route('admin.vacancies.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Vakansiya qo'shish
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
