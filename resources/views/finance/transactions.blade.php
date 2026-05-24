@extends('layouts.dashboard-new')

@section('title', 'Moliyaviy tranzaksiyalar')

@section('page-title', 'Tranzaksiyalar')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="mb-4">
        <h2 class="h4 mb-0">Moliyaviy tranzaksiyalar</h2>
        <p class="text-muted small">Barcha daromad va xarajatlar ro'yxati</p>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('finance.transactions') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Turi</label>
                        <select name="type" class="form-select">
                            <option value="">Barchasi</option>
                            <option value="income" {{ request('type') == 'income' ? 'selected' : '' }}>Daromad</option>
                            <option value="expense" {{ request('type') == 'expense' ? 'selected' : '' }}>Xarajat</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Kategoriya</label>
                        <select name="category" class="form-select">
                            <option value="">Barchasi</option>
                            <option value="tuition" {{ request('category') == 'tuition' ? 'selected' : '' }}>Kontrakt to'lovi</option>
                            <option value="scholarship" {{ request('category') == 'scholarship' ? 'selected' : '' }}>Stipendiya</option>
                            <option value="grant" {{ request('category') == 'grant' ? 'selected' : '' }}>Grant</option>
                            <option value="salary" {{ request('category') == 'salary' ? 'selected' : '' }}>Ish haqi</option>
                            <option value="utility" {{ request('category') == 'utility' ? 'selected' : '' }}>Kommunal</option>
                            <option value="other" {{ request('category') == 'other' ? 'selected' : '' }}>Boshqa</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Boshlanish</label>
                        <input type="date" name="date_from" class="form-select" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Tugash</label>
                        <input type="date" name="date_to" class="form-select" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-filter"></i> Filtr
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-left-success shadow">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Jami daromad</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                        {{ number_format($transactions->where('type', 'income')->sum('amount'), 0) }} so'm
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-left-danger shadow">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Jami xarajat</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                        {{ number_format($transactions->where('type', 'expense')->sum('amount'), 0) }} so'm
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-left-info shadow">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Sof foyda</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                        {{ number_format($transactions->where('type', 'income')->sum('amount') - $transactions->where('type', 'expense')->sum('amount'), 0) }} so'm
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Transactions Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Raqam</th>
                            <th>Sana</th>
                            <th>Turi</th>
                            <th>Kategoriya</th>
                            <th>Tavsif</th>
                            <th>Summa</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $transaction)
                        <tr>
                            <td>
                                <code>{{ $transaction->transaction_number }}</code>
                            </td>
                            <td>{{ $transaction->transaction_date->format('d.m.Y') }}</td>
                            <td>
                                @if($transaction->type === 'income')
                                    <span class="badge badge-success">{{ $transaction->type_label }}</span>
                                @else
                                    <span class="badge badge-danger">{{ $transaction->type_label }}</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-info">{{ $transaction->category_label }}</span>
                            </td>
                            <td>
                                {{ Str::limit($transaction->description, 50) }}
                                @if($transaction->student)
                                <br><small class="text-muted">Talaba: {{ $transaction->student->user->name ?? 'N/A' }}</small>
                                @endif
                            </td>
                            <td class="font-weight-bold {{ $transaction->type === 'income' ? 'text-success' : 'text-danger' }}">
                                {{ $transaction->type === 'income' ? '+' : '-' }}{{ number_format($transaction->amount, 0) }} so'm
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                <i class="fas fa-exchange-alt fa-3x mb-3 d-block"></i>
                                Tranzaksiyalar topilmadi
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $transactions->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
