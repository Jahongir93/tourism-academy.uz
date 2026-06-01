@extends('layouts.dashboard-new')

@section('title', 'Moliyaviy hisobotlar')

@section('page-title', 'Hisobotlar')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="mb-4">
        <h2 class="h4 mb-0">Moliyaviy hisobotlar</h2>
        <p class="text-muted small">Daromad va xarajatlar tahlili</p>
    </div>

    <!-- Date Range Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('finance.reports') }}">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">Boshlanish sanasi</label>
                        <input type="date" name="start_date" class="form-control" value="{{ $report['period']['start'] }}" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Tugash sanasi</label>
                        <input type="date" name="end_date" class="form-control" value="{{ $report['period']['end'] }}" required>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search"></i> Ko'rish
                        </button>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-success w-100" onclick="window.print()">
                            <i class="fas fa-print"></i> Chop etish
                        </button>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-info w-100">
                            <i class="fas fa-file-excel"></i> Excel
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-left-success shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Jami daromad</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($report['income']['total'], 0) }} so'm
                            </div>
                            <div class="text-xs text-muted mt-2">
                                Kontrakt: {{ number_format($report['income']['tuition'], 0) }} so'm
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-arrow-up fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-left-danger shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Jami xarajat</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($report['expenses']['total'], 0) }} so'm
                            </div>
                            <div class="text-xs text-muted mt-2">
                                Stipendiya: {{ number_format($report['expenses']['scholarships'], 0) }} so'm
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-arrow-down fa-2x text-danger"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-left-{{ $report['net_income'] >= 0 ? 'success' : 'danger' }} shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-{{ $report['net_income'] >= 0 ? 'success' : 'danger' }} text-uppercase mb-1">
                                Sof foyda
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($report['net_income'], 0) }} so'm
                            </div>
                            <div class="text-xs text-muted mt-2">
                                Davr: {{ \Carbon\Carbon::parse($report['period']['start'])->format('d.m.Y') }} -
                                {{ \Carbon\Carbon::parse($report['period']['end'])->format('d.m.Y') }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-{{ $report['net_income'] >= 0 ? 'success' : 'danger' }}"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Breakdown -->
    <div class="row">
        <!-- Income Breakdown -->
        <div class="col-md-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">Daromad tarkibi</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Kategoriya</th>
                                    <th class="text-right">Summa</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($incomeByCategory as $item)
                                <tr>
                                    <td>{{ ucfirst($item->category) }}</td>
                                    <td class="text-right font-weight-bold">{{ number_format($item->total, 0) }} so'm</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="2" class="text-center text-muted">Ma'lumot yo'q</td>
                                </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr class="font-weight-bold">
                                    <td>JAMI:</td>
                                    <td class="text-right">{{ number_format($incomeByCategory->sum('total'), 0) }} so'm</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Expense Breakdown -->
        <div class="col-md-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-danger">Xarajat tarkibi</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Kategoriya</th>
                                    <th class="text-right">Summa</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($expensesByCategory as $item)
                                <tr>
                                    <td>{{ ucfirst($item->category) }}</td>
                                    <td class="text-right font-weight-bold">{{ number_format($item->total, 0) }} so'm</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="2" class="text-center text-muted">Ma'lumot yo'q</td>
                                </tr>
                                @endforelse
                            </tbody>
                            <tfoot>
                                <tr class="font-weight-bold">
                                    <td>JAMI:</td>
                                    <td class="text-right">{{ number_format($expensesByCategory->sum('total'), 0) }} so'm</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Daromad vs Xarajat</h6>
        </div>
        <div class="card-body">
            <canvas id="incomeExpenseChart" height="100"></canvas>
        </div>
    </div>
</div>

@push('scripts')
<script src="{{ asset('vendor/chartjs/chart.umd.min.js') }}"></script>
<script>
const ctx = document.getElementById('incomeExpenseChart');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ['Daromad', 'Xarajat', 'Sof foyda'],
        datasets: [{
            label: 'Summa (so\'m)',
            data: [
                {{ $report['income']['total'] }},
                {{ $report['expenses']['total'] }},
                {{ $report['net_income'] }}
            ],
            backgroundColor: [
                'rgba(28, 200, 138, 0.8)',
                'rgba(231, 74, 59, 0.8)',
                'rgba(78, 115, 223, 0.8)'
            ],
            borderColor: [
                'rgba(28, 200, 138, 1)',
                'rgba(231, 74, 59, 1)',
                'rgba(78, 115, 223, 1)'
            ],
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
</script>
@endpush
@endsection
