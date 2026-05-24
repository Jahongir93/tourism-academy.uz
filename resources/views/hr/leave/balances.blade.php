@extends('layouts.dashboard-new')

@section('title', 'Ta\'til balanslari')
@section('page-title', 'Ta\'til balanslari')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm bg-gradient-warning text-white">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1"><i class="fas fa-balance-scale me-2"></i>Ta'til balanslari</h4>
                            <p class="mb-0 opacity-75">Xodimlar ta'til balanslari</p>
                        </div>
                        <a href="{{ route('hr.leave.requests') }}" class="btn btn-light">
                            <i class="fas fa-arrow-left me-1"></i> Arizalar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Balanslar jadvali -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="border-0">Xodim</th>
                            <th class="border-0">Bo'lim</th>
                            <th class="border-0 text-center">Yillik ta'til</th>
                            <th class="border-0 text-center">Ishlatilgan</th>
                            <th class="border-0 text-center">Qolgan</th>
                            <th class="border-0 text-center">Kasallik</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($employees as $employee)
                        @php
                            $totalLeave = 24; // Yillik ta'til kunlari
                            $usedLeave = 0; // Ishlatilgan kunlar
                            $remainingLeave = $totalLeave - $usedLeave;
                            $sickLeave = 10; // Kasallik ta'tili
                        @endphp
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm bg-primary bg-opacity-10 rounded-circle me-3 d-flex align-items-center justify-content-center">
                                        <i class="fas fa-user text-primary"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ $employee->last_name }} {{ $employee->first_name }}</h6>
                                        <small class="text-muted">{{ $employee->employee_id }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $employee->department?->name ?? '-' }}</td>
                            <td class="text-center">
                                <span class="badge bg-primary">{{ $totalLeave }} kun</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-warning">{{ $usedLeave }} kun</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-success">{{ $remainingLeave }} kun</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-info">{{ $sickLeave }} kun</span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
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
            {{ $employees->links() }}
        </div>
        @endif
    </div>
</div>

<style>
.bg-gradient-warning {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
}
.avatar-sm {
    width: 40px;
    height: 40px;
}
</style>
@endsection
