@extends('layouts.dashboard-new')

@section('title', 'Davomat')
@section('page-title', 'Davomat nazorati')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm bg-gradient-success text-white">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1"><i class="fas fa-calendar-check me-2"></i>Davomat nazorati</h4>
                            <p class="mb-0 opacity-75">Xodimlar davomatini kuzatish va boshqarish</p>
                        </div>
                        <a href="{{ route('hr.dashboard') }}" class="btn btn-light">
                            <i class="fas fa-arrow-left me-1"></i> Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistika -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm h-100 bg-success bg-opacity-10">
                <div class="card-body text-center">
                    <i class="fas fa-user-check fa-2x text-success mb-2"></i>
                    <h3 class="mb-0 fw-bold text-success">{{ $stats['present'] }}</h3>
                    <p class="text-muted mb-0">Kelganlar</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm h-100 bg-danger bg-opacity-10">
                <div class="card-body text-center">
                    <i class="fas fa-user-times fa-2x text-danger mb-2"></i>
                    <h3 class="mb-0 fw-bold text-danger">{{ $stats['absent'] }}</h3>
                    <p class="text-muted mb-0">Kelmaganlar</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card border-0 shadow-sm h-100 bg-warning bg-opacity-10">
                <div class="card-body text-center">
                    <i class="fas fa-user-clock fa-2x text-warning mb-2"></i>
                    <h3 class="mb-0 fw-bold text-warning">{{ $stats['late'] }}</h3>
                    <p class="text-muted mb-0">Kech qolganlar</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Sana tanlash -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('hr.attendance.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Sana</label>
                    <input type="date" name="date" class="form-control" value="{{ $date }}">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-search me-1"></i> Ko'rish
                    </button>
                    <a href="{{ route('hr.attendance.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-calendar-day me-1"></i> Bugun
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Davomat jadvali -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0"><i class="fas fa-list me-2"></i>{{ \Carbon\Carbon::parse($date)->format('d.m.Y') }} - Davomat</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="border-0">Xodim</th>
                            <th class="border-0">Bo'lim</th>
                            <th class="border-0">Kelish vaqti</th>
                            <th class="border-0">Ketish vaqti</th>
                            <th class="border-0">Ishlagan soat</th>
                            <th class="border-0">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attendances as $attendance)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm bg-primary bg-opacity-10 rounded-circle me-3 d-flex align-items-center justify-content-center">
                                        <i class="fas fa-user text-primary"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ $attendance->employee?->last_name }} {{ $attendance->employee?->first_name }}</h6>
                                        <small class="text-muted">{{ $attendance->employee?->employee_id }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $attendance->employee?->department?->name ?? '-' }}</td>
                            <td>
                                @if($attendance->check_in)
                                    <span class="{{ $attendance->is_late ? 'text-warning' : 'text-success' }}">
                                        <i class="fas fa-sign-in-alt me-1"></i>{{ $attendance->check_in->format('H:i') }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($attendance->check_out)
                                    <span class="text-info">
                                        <i class="fas fa-sign-out-alt me-1"></i>{{ $attendance->check_out->format('H:i') }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($attendance->check_in && $attendance->check_out)
                                    {{ $attendance->check_in->diff($attendance->check_out)->format('%H:%I') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if($attendance->status == 'present')
                                    <span class="badge bg-success">Keldi</span>
                                @elseif($attendance->status == 'absent')
                                    <span class="badge bg-danger">Kelmadi</span>
                                @elseif($attendance->is_late)
                                    <span class="badge bg-warning">Kech qoldi</span>
                                @else
                                    <span class="badge bg-secondary">{{ $attendance->status }}</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="fas fa-calendar-check fa-3x text-muted mb-3 d-block opacity-50"></i>
                                <p class="text-muted mb-0">Bu sana uchun davomat ma'lumotlari topilmadi</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($attendances->hasPages())
        <div class="card-footer bg-white">
            {{ $attendances->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>

<style>
.bg-gradient-success {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
}
.avatar-sm {
    width: 40px;
    height: 40px;
}
</style>
@endsection
