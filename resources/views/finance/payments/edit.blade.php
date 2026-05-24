@extends('layouts.dashboard-new')

@section('title', 'To\'lovni tahrirlash')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">To'lovni tahrirlash #{{ $payment->id }}</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('finance.payments.update', $payment) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label">Talaba</label>
                            <input type="text" class="form-control" value="{{ $payment->student->user->name ?? 'N/A' }}" disabled>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Summa <span class="text-danger">*</span></label>
                            <input type="number" name="amount" class="form-control" value="{{ old('amount', $payment->amount) }}" required min="0" step="0.01">
                            @error('amount')
                            <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">To'lov sanasi <span class="text-danger">*</span></label>
                            <input type="date" name="payment_date" class="form-control" value="{{ old('payment_date', $payment->payment_date->format('Y-m-d')) }}" required>
                            @error('payment_date')
                            <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">To'lov usuli <span class="text-danger">*</span></label>
                            <select name="payment_method" class="form-control" required>
                                <option value="bank" {{ old('payment_method', $payment->payment_method) == 'bank' ? 'selected' : '' }}>Bank o'tkazmasi</option>
                                <option value="cash" {{ old('payment_method', $payment->payment_method) == 'cash' ? 'selected' : '' }}>Naqd pul</option>
                                <option value="card" {{ old('payment_method', $payment->payment_method) == 'card' ? 'selected' : '' }}>Plastik karta</option>
                                <option value="online" {{ old('payment_method', $payment->payment_method) == 'online' ? 'selected' : '' }}>Onlayn to'lov</option>
                            </select>
                            @error('payment_method')
                            <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Kvitansiya raqami</label>
                            <input type="text" name="receipt_number" class="form-control" value="{{ old('receipt_number', $payment->receipt_number) }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Holat <span class="text-danger">*</span></label>
                            <select name="status" class="form-control" required>
                                <option value="completed" {{ old('status', $payment->status) == 'completed' ? 'selected' : '' }}>To'langan</option>
                                <option value="pending" {{ old('status', $payment->status) == 'pending' ? 'selected' : '' }}>Kutilmoqda</option>
                                <option value="cancelled" {{ old('status', $payment->status) == 'cancelled' ? 'selected' : '' }}>Bekor qilingan</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Izoh</label>
                            <textarea name="notes" class="form-control" rows="3">{{ old('notes', $payment->notes) }}</textarea>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('finance.payments.show', $payment) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Orqaga
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Yangilash
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
