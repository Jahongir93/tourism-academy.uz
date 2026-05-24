@extends('layouts.dashboard-new')

@section('title', 'Shartnomalar')
@section('page-title', 'Xodimlar shartnomalari')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm bg-gradient-primary text-white">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1"><i class="fas fa-file-contract me-2"></i>Xodimlar shartnomalari</h4>
                            <p class="mb-0 opacity-75">Shartnomalar muddati va holatini kuzatish</p>
                        </div>
                        <a href="{{ route('hr.employees.index') }}" class="btn btn-light">
                            <i class="fas fa-arrow-left me-1"></i> Xodimlar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Shartnomalar jadvali -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="border-0">Xodim</th>
                            <th class="border-0">Bo'lim</th>
                            <th class="border-0">Shartnoma muddati</th>
                            <th class="border-0">Qolgan kun</th>
                            <th class="border-0">Status</th>
                            <th class="border-0 text-end">Amallar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($employees as $employee)
                        @php
                            $daysRemaining = $employee->contract_end_date ? now()->diffInDays($employee->contract_end_date, false) : null;
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
                            <td>{{ $employee->contract_end_date?->format('d.m.Y') ?? '-' }}</td>
                            <td>
                                @if($daysRemaining !== null)
                                    @if($daysRemaining < 0)
                                        <span class="badge bg-danger">Muddati o'tgan ({{ abs($daysRemaining) }} kun)</span>
                                    @elseif($daysRemaining <= 30)
                                        <span class="badge bg-warning">{{ $daysRemaining }} kun qoldi</span>
                                    @elseif($daysRemaining <= 90)
                                        <span class="badge bg-info">{{ $daysRemaining }} kun qoldi</span>
                                    @else
                                        <span class="badge bg-success">{{ $daysRemaining }} kun qoldi</span>
                                    @endif
                                @else
                                    <span class="badge bg-secondary">Muddatsiz</span>
                                @endif
                            </td>
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
                            <td colspan="6" class="text-center py-5">
                                <i class="fas fa-file-contract fa-3x text-muted mb-3 d-block opacity-50"></i>
                                <p class="text-muted mb-0">Shartnomali xodimlar topilmadi</p>
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
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
.avatar-sm {
    width: 40px;
    height: 40px;
}
</style>
@endsection
