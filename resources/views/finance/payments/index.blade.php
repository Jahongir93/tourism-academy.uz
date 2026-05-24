@extends('layouts.dashboard-new')

@section('title', 'To\'lovlar ro\'yxati')

@section('page-title', 'To\'lovlar')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h4 mb-0">To'lovlar ro'yxati</h2>
            <p class="text-muted small">Talabalarning to'lovlarini boshqarish</p>
        </div>
        <a href="{{ route('finance.payments.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Yangi to'lov
        </a>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('finance.payments.index') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Holat</label>
                        <select name="status" class="form-select">
                            <option value="">Barchasi</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>To'langan</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Kutilmoqda</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Bekor qilingan</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">To'lov usuli</label>
                        <select name="payment_method" class="form-select">
                            <option value="">Barchasi</option>
                            <option value="cash" {{ request('payment_method') == 'cash' ? 'selected' : '' }}>Naqd</option>
                            <option value="bank" {{ request('payment_method') == 'bank' ? 'selected' : '' }}>Bank</option>
                            <option value="online" {{ request('payment_method') == 'online' ? 'selected' : '' }}>Online</option>
                            <option value="card" {{ request('payment_method') == 'card' ? 'selected' : '' }}>Karta</option>
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

    <!-- Payments Table -->
    <div class="card">
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
                            <th>Amallar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $payment)
                        <tr>
                            <td>{{ $payment->id }}</td>
                            <td>
                                <div>
                                    <strong>{{ $payment->student->user->name ?? 'N/A' }}</strong>
                                    <br>
                                    <small class="text-muted">ID: {{ $payment->student->student_id ?? 'N/A' }}</small>
                                </div>
                            </td>
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
                            <td>
                                <a href="{{ route('finance.payments.show', $payment) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if($payment->status !== 'cancelled')
                                <a href="{{ route('finance.payments.edit', $payment) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                To'lovlar topilmadi
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $payments->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
