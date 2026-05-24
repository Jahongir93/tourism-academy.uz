@extends('layouts.dashboard-new')

@section('title', 'Xodimlar ro\'yxati')
@section('page-title', 'Xodimlar ro\'yxati')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm bg-gradient-primary text-white">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1"><i class="fas fa-users me-2"></i>Xodimlar boshqaruvi</h4>
                            <p class="mb-0 opacity-75">Barcha xodimlar ro'yxati va boshqaruvi</p>
                        </div>
                        <a href="{{ route('hr.employees.create') }}" class="btn btn-light">
                            <i class="fas fa-plus me-1"></i> Yangi xodim
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Filtrlar -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('hr.employees.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Qidiruv</label>
                    <input type="text" name="search" class="form-control"
                           placeholder="Ism, familiya, ID yoki email..."
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Bo'lim</label>
                    <select name="department_id" class="form-select">
                        <option value="">Barchasi</option>
                        @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                            {{ $dept->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">Barchasi</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Faol</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Nofaol</option>
                        <option value="terminated" {{ request('status') == 'terminated' ? 'selected' : '' }}>Ishdan bo'shatilgan</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-search me-1"></i> Qidirish
                    </button>
                    <a href="{{ route('hr.employees.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-redo me-1"></i> Tozalash
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Xodimlar jadvali -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="border-0">ID</th>
                            <th class="border-0">Xodim</th>
                            <th class="border-0">Bo'lim</th>
                            <th class="border-0">Lavozim</th>
                            <th class="border-0">Telefon</th>
                            <th class="border-0">Status</th>
                            <th class="border-0 text-end">Amallar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($employees as $employee)
                        <tr>
                            <td>
                                <span class="badge bg-light text-dark">{{ $employee->employee_id }}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm bg-primary bg-opacity-10 rounded-circle me-3 d-flex align-items-center justify-content-center">
                                        <span class="text-primary fw-bold">
                                            {{ strtoupper(substr($employee->first_name, 0, 1)) }}{{ strtoupper(substr($employee->last_name, 0, 1)) }}
                                        </span>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ $employee->last_name }} {{ $employee->first_name }}</h6>
                                        <small class="text-muted">{{ $employee->email }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $employee->department?->name ?? '-' }}</td>
                            <td>{{ $employee->position ?? '-' }}</td>
                            <td>{{ $employee->phone ?? '-' }}</td>
                            <td>
                                @if($employee->status == 'active')
                                    <span class="badge bg-success">Faol</span>
                                @elseif($employee->status == 'inactive')
                                    <span class="badge bg-warning">Nofaol</span>
                                @else
                                    <span class="badge bg-danger">Ishdan bo'shatilgan</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('hr.employees.show', $employee) }}" class="btn btn-sm btn-outline-info" title="Ko'rish">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('hr.employees.edit', $employee) }}" class="btn btn-sm btn-outline-primary" title="Tahrirlash">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <i class="fas fa-users fa-3x text-muted mb-3 d-block opacity-50"></i>
                                <p class="text-muted mb-0">Xodimlar topilmadi</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($employees->hasPages())
        <div class="card-footer bg-white">
            {{ $employees->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>

<style>
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
.avatar-sm {
    width: 40px;
    height: 40px;
}
</style>
@endsection
