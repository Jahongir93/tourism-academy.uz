@extends('layouts.dashboard-new')

@section('title', 'O\'qituvchilar')
@section('page-title', 'O\'qituvchilar ro\'yxati')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm bg-gradient-info text-white">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1"><i class="fas fa-chalkboard-teacher me-2"></i>O'qituvchilar</h4>
                            <p class="mb-0 opacity-75">{{ $faculty?->name ?? 'Fakultet' }} o'qituvchilari</p>
                        </div>
                        <a href="{{ route('dean.dashboard') }}" class="btn btn-light">
                            <i class="fas fa-arrow-left me-1"></i> Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtrlar -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('dean.teachers.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Qidiruv</label>
                    <input type="text" name="search" class="form-control" placeholder="Ism, familiya..."
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Kafedra</label>
                    <select name="department_id" class="form-select">
                        <option value="">Barchasi</option>
                        @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                            {{ $dept->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-info me-2">
                        <i class="fas fa-search me-1"></i> Qidirish
                    </button>
                    <a href="{{ route('dean.teachers.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-redo"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- O'qituvchilar jadvali -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="border-0">O'qituvchi</th>
                            <th class="border-0">Kafedra</th>
                            <th class="border-0">Lavozim</th>
                            <th class="border-0">Telefon</th>
                            <th class="border-0">Email</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($teachers as $teacher)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm bg-info bg-opacity-10 rounded-circle me-3 d-flex align-items-center justify-content-center">
                                        <i class="fas fa-user-tie text-info"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ $teacher->last_name }} {{ $teacher->first_name }}</h6>
                                        <small class="text-muted">{{ $teacher->employee_code }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $teacher->employmentDetail?->department?->name ?? '-' }}</td>
                            <td>{{ $teacher->employmentDetail?->position?->name ?? $teacher->employee_type ?? '-' }}</td>
                            <td>{{ $teacher->phone ?? '-' }}</td>
                            <td>{{ $teacher->email ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <i class="fas fa-chalkboard-teacher fa-3x text-muted mb-3 d-block opacity-50"></i>
                                <p class="text-muted mb-0">O'qituvchilar topilmadi</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($teachers->hasPages())
        <div class="card-footer bg-white">
            {{ $teachers->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>

<style>
.bg-gradient-info { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
.avatar-sm { width: 40px; height: 40px; }
</style>
@endsection
