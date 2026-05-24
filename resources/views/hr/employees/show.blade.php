@extends('layouts.dashboard-new')

@section('title', 'Xodim ma\'lumotlari')
@section('page-title', 'Xodim ma\'lumotlari')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm bg-gradient-primary text-white">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <div class="avatar-lg bg-white bg-opacity-25 rounded-circle me-4 d-flex align-items-center justify-content-center">
                                <span class="text-white fw-bold fs-3">
                                    {{ strtoupper(substr($employee->first_name, 0, 1)) }}{{ strtoupper(substr($employee->last_name, 0, 1)) }}
                                </span>
                            </div>
                            <div>
                                <h4 class="mb-1">{{ $employee->last_name }} {{ $employee->first_name }}</h4>
                                <p class="mb-0 opacity-75">{{ $employee->employee_id }} | {{ $employee->position ?? 'Lavozim ko\'rsatilmagan' }}</p>
                            </div>
                        </div>
                        <div>
                            <a href="{{ route('hr.employees.edit', $employee) }}" class="btn btn-light me-2">
                                <i class="fas fa-edit me-1"></i> Tahrirlash
                            </a>
                            <a href="{{ route('hr.employees.index') }}" class="btn btn-outline-light">
                                <i class="fas fa-arrow-left me-1"></i> Orqaga
                            </a>
                        </div>
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

    <div class="row">
        <!-- Asosiy ma'lumotlar -->
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-user text-primary me-2"></i>Shaxsiy ma'lumotlar</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <td class="text-muted" style="width: 40%">Xodim ID:</td>
                            <td class="fw-medium">{{ $employee->employee_id }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Ism:</td>
                            <td class="fw-medium">{{ $employee->first_name }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Familiya:</td>
                            <td class="fw-medium">{{ $employee->last_name }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Tug'ilgan sana:</td>
                            <td class="fw-medium">{{ $employee->birth_date?->format('d.m.Y') ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Manzil:</td>
                            <td class="fw-medium">{{ $employee->address ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Aloqa ma'lumotlari -->
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-phone text-success me-2"></i>Aloqa</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <td class="text-muted" style="width: 40%">Email:</td>
                            <td class="fw-medium">
                                <a href="mailto:{{ $employee->email }}">{{ $employee->email }}</a>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Telefon:</td>
                            <td class="fw-medium">
                                @if($employee->phone)
                                <a href="tel:{{ $employee->phone }}">{{ $employee->phone }}</a>
                                @else
                                -
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Ish ma'lumotlari -->
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-briefcase text-info me-2"></i>Ish ma'lumotlari</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <td class="text-muted" style="width: 40%">Bo'lim:</td>
                            <td class="fw-medium">{{ $employee->department?->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Lavozim:</td>
                            <td class="fw-medium">{{ $employee->position ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Ishga qabul:</td>
                            <td class="fw-medium">{{ $employee->hire_date?->format('d.m.Y') ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Status:</td>
                            <td>
                                @if($employee->status == 'active')
                                    <span class="badge bg-success">Faol</span>
                                @elseif($employee->status == 'inactive')
                                    <span class="badge bg-warning">Nofaol</span>
                                @else
                                    <span class="badge bg-danger">Ishdan bo'shatilgan</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Maosh:</td>
                            <td class="fw-medium">{{ $employee->salary ? number_format($employee->salary, 0, '.', ' ') . ' so\'m' : '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- So'nggi davomat -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-calendar-check text-warning me-2"></i>So'nggi davomat</h5>
                    <a href="{{ route('hr.attendance.index') }}" class="btn btn-sm btn-outline-warning">
                        <i class="fas fa-list me-1"></i> Barchasi
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="border-0">Sana</th>
                                    <th class="border-0">Kelish</th>
                                    <th class="border-0">Ketish</th>
                                    <th class="border-0">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($employee->attendances ?? [] as $attendance)
                                <tr>
                                    <td>{{ $attendance->date?->format('d.m.Y') }}</td>
                                    <td>{{ $attendance->check_in?->format('H:i') ?? '-' }}</td>
                                    <td>{{ $attendance->check_out?->format('H:i') ?? '-' }}</td>
                                    <td>
                                        @if($attendance->status == 'present')
                                            <span class="badge bg-success">Keldi</span>
                                        @elseif($attendance->status == 'absent')
                                            <span class="badge bg-danger">Kelmadi</span>
                                        @else
                                            <span class="badge bg-warning">Kech qoldi</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">
                                        Davomat ma'lumotlari yo'q
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ta'til arizalari -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-file-alt text-danger me-2"></i>Ta'til arizalari</h5>
                    <a href="{{ route('hr.leave.requests') }}" class="btn btn-sm btn-outline-danger">
                        <i class="fas fa-list me-1"></i> Barchasi
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="border-0">Turi</th>
                                    <th class="border-0">Muddat</th>
                                    <th class="border-0">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($employee->leaveRequests ?? [] as $leave)
                                <tr>
                                    <td>{{ $leave->leaveType?->name ?? 'Ta\'til' }}</td>
                                    <td>{{ $leave->start_date?->format('d.m') }} - {{ $leave->end_date?->format('d.m.Y') }}</td>
                                    <td>
                                        @if($leave->status == 'approved')
                                            <span class="badge bg-success">Tasdiqlangan</span>
                                        @elseif($leave->status == 'rejected')
                                            <span class="badge bg-danger">Rad etilgan</span>
                                        @else
                                            <span class="badge bg-warning">Kutilmoqda</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center py-4 text-muted">
                                        Ta'til arizalari yo'q
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
.avatar-lg {
    width: 80px;
    height: 80px;
}
</style>
@endsection
