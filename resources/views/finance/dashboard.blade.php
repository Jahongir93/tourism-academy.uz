@extends('layouts.dashboard-new')

@section('title', 'Moliya boshqaruvi - Dashboard')

@section('page-title', 'Moliya boshqaruvi')

@section('content')
<div class="container-fluid">
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <!-- Total Income This Month -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Joriy oy daromadi
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($stats['total_income_this_month'], 0, '.', ' ') }} so'm
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Payments Count -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Joriy oy to'lovlar soni
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stats['total_payments_count'] }} ta
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-receipt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Contracts -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Faol kontr aktlar
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stats['total_contracts'] }} ta
                            </div>
                            <div class="text-xs text-muted mt-1">
                                Qolgan: {{ number_format($stats['total_remaining'], 0, '.', ' ') }} so'm
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-contract fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Scholarships -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Grant oluvchilar
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stats['scholarship_recipients'] }} ta
                            </div>
                            <div class="text-xs text-muted mt-1">
                                {{ $stats['active_scholarships'] }} ta faol grant
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-graduation-cap fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row mb-4">
        <!-- Monthly Income Trend -->
        <div class="col-xl-8 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Daromad tendensiyasi (so'nggi 6 oy)</h6>
                </div>
                <div class="card-body">
                    <canvas id="incomeChart" height="100"></canvas>
                </div>
            </div>
        </div>

        <!-- Payment Methods -->
        <div class="col-xl-4 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">To'lov usullari</h6>
                </div>
                <div class="card-body">
                    <canvas id="paymentMethodsChart"></canvas>
                    <div class="mt-3">
                        @foreach($paymentsByMethod as $method)
                        <div class="mb-2">
                            <span class="badge badge-primary">{{ ucfirst($method->payment_method) }}</span>
                            <span class="float-right">{{ $method->count }} ta / {{ number_format($method->total, 0) }} so'm</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Payments -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">So'nggi to'lovlar</h6>
                    <a href="{{ route('finance.payments.index') }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-list"></i> Barchasi
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Talaba</th>
                                    <th>Summa</th>
                                    <th>Sana</th>
                                    <th>Usul</th>
                                    <th>Holat</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentPayments as $payment)
                                <tr>
                                    <td>{{ $payment->id }}</td>
                                    <td>{{ $payment->student->user->name ?? 'N/A' }}</td>
                                    <td class="font-weight-bold">{{ number_format($payment->amount, 0, '.', ' ') }} so'm</td>
                                    <td>{{ $payment->payment_date->format('d.m.Y') }}</td>
                                    <td>
                                        <span class="badge badge-info">{{ $payment->payment_method_label }}</span>
                                    </td>
                                    <td>
                                        @if($payment->status === 'completed')
                                            <span class="badge badge-success">To'langan</span>
                                        @elseif($payment->status === 'pending')
                                            <span class="badge badge-warning">Kutilmoqda</span>
                                        @else
                                            <span class="badge badge-danger">Bekor qilingan</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">To'lovlar yo'q</td>
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

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Monthly Income Chart
const incomeCtx = document.getElementById('incomeChart');
new Chart(incomeCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode(array_column($monthlyIncome, 'month')) !!},
        datasets: [{
            label: 'Daromad (so\'m)',
            data: {!! json_encode(array_column($monthlyIncome, 'income')) !!},
            borderColor: 'rgb(78, 115, 223)',
            backgroundColor: 'rgba(78, 115, 223, 0.05)',
            tension: 0.3,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                display: true
            }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// Payment Methods Chart
const methodsCtx = document.getElementById('paymentMethodsChart');
new Chart(methodsCtx, {
    type: 'doughnut',
    data: {
        labels: {!! json_encode($paymentsByMethod->pluck('payment_method')->toArray()) !!},
        datasets: [{
            data: {!! json_encode($paymentsByMethod->pluck('count')->toArray()) !!},
            backgroundColor: [
                'rgba(78, 115, 223, 0.8)',
                'rgba(28, 200, 138, 0.8)',
                'rgba(54, 185, 204, 0.8)',
                'rgba(246, 194, 62, 0.8)'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});
</script>
@endpush
@endsection
