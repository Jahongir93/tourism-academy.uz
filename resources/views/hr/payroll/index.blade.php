@extends('layouts.dashboard-new')

@section('title', 'Ish haqi')
@section('page-title', 'Ish haqi boshqaruvi')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm bg-gradient-info text-white">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1"><i class="fas fa-money-bill-wave me-2"></i>Ish haqi boshqaruvi</h4>
                            <p class="mb-0 opacity-75">Xodimlar ish haqini boshqarish va hisobotlar</p>
                        </div>
                        <a href="{{ route('hr.dashboard') }}" class="btn btn-light">
                            <i class="fas fa-arrow-left me-1"></i> Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Oy tanlash -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('hr.payroll.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Oy</label>
                    <input type="month" name="month" class="form-control" value="{{ $month }}">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-search me-1"></i> Ko'rish
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Ish haqi jadvali -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-list me-2"></i>
                {{ \Carbon\Carbon::parse($month)->format('F Y') }} - Ish haqi ro'yxati
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="border-0">Xodim</th>
                            <th class="border-0">Bo'lim</th>
                            <th class="border-0">Lavozim</th>
                            <th class="border-0 text-end">Asosiy maosh</th>
                            <th class="border-0 text-end">Qo'shimcha</th>
                            <th class="border-0 text-end">Ushlanmalar</th>
                            <th class="border-0 text-end">Jami</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($employees as $employee)
                        @php
                            $baseSalary = $employee->salary ?? 0;
                            $bonus = 0; // Qo'shimcha hisoblash logikasi
                            $deductions = 0; // Ushlanmalar logikasi
                            $netSalary = $baseSalary + $bonus - $deductions;
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
                            <td>{{ $employee->position ?? '-' }}</td>
                            <td class="text-end">{{ number_format($baseSalary, 0, '.', ' ') }} so'm</td>
                            <td class="text-end text-success">+{{ number_format($bonus, 0, '.', ' ') }} so'm</td>
                            <td class="text-end text-danger">-{{ number_format($deductions, 0, '.', ' ') }} so'm</td>
                            <td class="text-end fw-bold">{{ number_format($netSalary, 0, '.', ' ') }} so'm</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <i class="fas fa-money-bill-wave fa-3x text-muted mb-3 d-block opacity-50"></i>
                                <p class="text-muted mb-0">Xodimlar topilmadi</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if($employees->count() > 0)
                    <tfoot class="bg-light">
                        <tr>
                            <td colspan="3" class="fw-bold">Jami:</td>
                            <td class="text-end fw-bold">{{ number_format($employees->sum('salary'), 0, '.', ' ') }} so'm</td>
                            <td class="text-end fw-bold text-success">+0 so'm</td>
                            <td class="text-end fw-bold text-danger">-0 so'm</td>
                            <td class="text-end fw-bold">{{ number_format($employees->sum('salary'), 0, '.', ' ') }} so'm</td>
                        </tr>
                    </tfoot>
                    @endif
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
.bg-gradient-info {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
.avatar-sm {
    width: 40px;
    height: 40px;
}
</style>
@endsection
