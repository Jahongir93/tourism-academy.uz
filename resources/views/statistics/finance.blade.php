@extends('layouts.dashboard-new')

@section('title', 'Moliya statistikasi')

@section('content')
<div class="container-fluid">
    <h2 class="h4 mb-4">Moliya statistikasi</h2>

    <!-- Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-left-primary shadow">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Shartnomalar</div>
                    <div class="h5 mb-0">{{ $outstandingContracts->total_contracts ?? 0 }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-success shadow">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">To'langan</div>
                    <div class="h5 mb-0">{{ number_format($outstandingContracts->paid_amount ?? 0, 0) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-warning shadow">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Qoldiq</div>
                    <div class="h5 mb-0">{{ number_format($outstandingContracts->remaining ?? 0, 0) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-info shadow">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Jami summa</div>
                    <div class="h5 mb-0">{{ number_format($outstandingContracts->total_amount ?? 0, 0) }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="row">
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Oylik daromad ({{ now()->year }})</h6>
                </div>
                <div class="card-body">
                    <canvas id="incomeChart" height="100"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">To'lov usullari</h6>
                </div>
                <div class="card-body">
                    <canvas id="methodsChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Paying Students -->
    <div class="card shadow">
        <div class="card-header">
            <h6 class="m-0 font-weight-bold text-primary">Eng ko'p to'lov qilgan talabalar (joriy oy)</h6>
        </div>
        <div class="card-body">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Talaba</th>
                        <th class="text-right">Summa</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($topPayingStudents as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->student->user->name ?? 'N/A' }}</td>
                        <td class="text-right font-weight-bold">{{ number_format($item->total_paid, 0) }} so'm</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Monthly Income Chart
new Chart(document.getElementById('incomeChart'), {
    type: 'bar',
    data: {
        labels: {!! json_encode(array_column($monthlyIncome, 'month')) !!},
        datasets: [{
            label: 'Daromad',
            data: {!! json_encode(array_column($monthlyIncome, 'income')) !!},
            backgroundColor: 'rgba(28, 200, 138, 0.8)'
        }]
    },
    options: { responsive: true, maintainAspectRatio: true }
});

// Payment Methods Chart
new Chart(document.getElementById('methodsChart'), {
    type: 'doughnut',
    data: {
        labels: {!! json_encode($paymentMethods->pluck('payment_method')->toArray()) !!},
        datasets: [{
            data: {!! json_encode($paymentMethods->pluck('total')->toArray()) !!},
            backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e']
        }]
    }
});
</script>
@endpush
@endsection
