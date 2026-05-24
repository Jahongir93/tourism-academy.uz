@extends('layouts.dashboard-new')

@section('title', 'Moliya hisoboti')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h4 mb-0">Moliya hisoboti</h2>
        <div>
            <button class="btn btn-success btn-sm" onclick="exportToExcel()">
                <i class="fas fa-file-excel"></i> Excel
            </button>
            <button class="btn btn-danger btn-sm" onclick="exportToPDF()">
                <i class="fas fa-file-pdf"></i> PDF
            </button>
            <button class="btn btn-primary btn-sm" onclick="window.print()">
                <i class="fas fa-print"></i> Chop etish
            </button>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('reports.finance') }}" class="row">
                <div class="col-md-3">
                    <label>Boshlanish sanasi</label>
                    <input type="date" name="start_date" class="form-control form-control-sm" value="{{ request('start_date', now()->startOfMonth()->format('Y-m-d')) }}">
                </div>
                <div class="col-md-3">
                    <label>Tugash sanasi</label>
                    <input type="date" name="end_date" class="form-control form-control-sm" value="{{ request('end_date', now()->format('Y-m-d')) }}">
                </div>
                <div class="col-md-2">
                    <label>To'lov usuli</label>
                    <select name="payment_method" class="form-control form-control-sm">
                        <option value="">Barchasi</option>
                        <option value="cash" {{ request('payment_method') == 'cash' ? 'selected' : '' }}>Naqd</option>
                        <option value="card" {{ request('payment_method') == 'card' ? 'selected' : '' }}>Karta</option>
                        <option value="transfer" {{ request('payment_method') == 'transfer' ? 'selected' : '' }}>O'tkazma</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label>Status</label>
                    <select name="status" class="form-control form-control-sm">
                        <option value="">Barchasi</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Tasdiqlangan</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Kutilmoqda</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rad etilgan</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label>&nbsp;</label>
                    <div>
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="fas fa-filter"></i> Filtr
                        </button>
                        <a href="{{ route('reports.finance') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-redo"></i> Tozalash
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-left-success shadow">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Jami daromad</div>
                    <div class="h5 mb-0">{{ number_format($totalIncome, 0) }} so'm</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-primary shadow">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">To'lovlar soni</div>
                    <div class="h5 mb-0">{{ $payments->total() }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-info shadow">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">O'rtacha to'lov</div>
                    <div class="h5 mb-0">{{ number_format($averagePayment, 0) }} so'm</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-warning shadow">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Qarzlar</div>
                    <div class="h5 mb-0">{{ number_format($totalDebt, 0) }} so'm</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payments Table -->
    <div class="card shadow">
        <div class="card-header">
            <h6 class="m-0 font-weight-bold text-primary">To'lovlar ro'yxati</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm table-bordered" id="paymentsTable">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>Sana</th>
                            <th>Talaba</th>
                            <th>Shartnoma</th>
                            <th>Summa</th>
                            <th>To'lov usuli</th>
                            <th>Status</th>
                            <th>Izoh</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($payments as $index => $payment)
                        <tr>
                            <td>{{ $payments->firstItem() + $index }}</td>
                            <td>{{ $payment->payment_date }}</td>
                            <td>{{ $payment->student_name ?? 'N/A' }}</td>
                            <td>{{ $payment->contract_number ?? 'N/A' }}</td>
                            <td class="font-weight-bold">{{ number_format($payment->amount, 0) }} so'm</td>
                            <td>
                                @if($payment->payment_method == 'cash')
                                    <span class="badge badge-success">Naqd</span>
                                @elseif($payment->payment_method == 'card')
                                    <span class="badge badge-info">Karta</span>
                                @else
                                    <span class="badge badge-primary">O'tkazma</span>
                                @endif
                            </td>
                            <td>
                                @if($payment->status == 'completed')
                                    <span class="badge badge-success">Tasdiqlangan</span>
                                @elseif($payment->status == 'pending')
                                    <span class="badge badge-warning">Kutilmoqda</span>
                                @else
                                    <span class="badge badge-danger">Rad etilgan</span>
                                @endif
                            </td>
                            <td>{{ $payment->notes ?? '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="font-weight-bold">
                            <td colspan="4" class="text-right">Jami:</td>
                            <td>{{ number_format($totalIncome, 0) }} so'm</td>
                            <td colspan="3"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="mt-3">
                {{ $payments->appends(request()->query())->links() }}
            </div>
        </div>
    </div>

    <!-- Payment Methods Breakdown -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">To'lov usullari bo'yicha</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Usul</th>
                                <th class="text-right">Soni</th>
                                <th class="text-right">Summa</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($paymentsByMethod as $method)
                            <tr>
                                <td>
                                    @if($method->payment_method == 'cash')
                                        Naqd pul
                                    @elseif($method->payment_method == 'card')
                                        Plastik karta
                                    @else
                                        Bank o'tkazmasi
                                    @endif
                                </td>
                                <td class="text-right">{{ $method->count }}</td>
                                <td class="text-right font-weight-bold">{{ number_format($method->total, 0) }} so'm</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Status bo'yicha</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Status</th>
                                <th class="text-right">Soni</th>
                                <th class="text-right">Summa</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($paymentsByStatus as $status)
                            <tr>
                                <td>
                                    @if($status->status == 'completed')
                                        Tasdiqlangan
                                    @elseif($status->status == 'pending')
                                        Kutilmoqda
                                    @else
                                        Rad etilgan
                                    @endif
                                </td>
                                <td class="text-right">{{ $status->count }}</td>
                                <td class="text-right font-weight-bold">{{ number_format($status->total, 0) }} so'm</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function exportToExcel() {
    alert('Excel export funksiyasi ishlab chiqilmoqda...');
    // Implementation pending
}

function exportToPDF() {
    alert('PDF export funksiyasi ishlab chiqilmoqda...');
    // Implementation pending
}
</script>

<style>
@media print {
    .btn, .card-header, nav, form { display: none !important; }
    .card { border: none !important; box-shadow: none !important; }
}
</style>
@endpush
@endsection
