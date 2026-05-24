@extends('layouts.dashboard-new')

@section('title', 'Yangi to\'lov')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Yangi to'lov qo'shish</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('finance.payments.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Talaba <span class="text-danger">*</span></label>
                            <select name="student_id" class="form-control" required>
                                <option value="">Talabani tanlang</option>
                                @foreach(\App\Models\Student::with('user')->where('status', 'active')->get() as $s)
                                <option value="{{ $s->id }}" {{ old('student_id', $student->id ?? '') == $s->id ? 'selected' : '' }}>
                                    {{ $s->user->name ?? 'N/A' }} ({{ $s->student_id }})
                                </option>
                                @endforeach
                            </select>
                            @error('student_id')
                            <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Shartnoma</label>
                            <select name="contract_id" class="form-control">
                                <option value="">Tanlanmagan</option>
                                @foreach($contracts as $contract)
                                <option value="{{ $contract->id }}">
                                    {{ $contract->contract_number }} - Qoldiq: {{ number_format($contract->remaining_amount, 0) }} so'm
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Summa <span class="text-danger">*</span></label>
                            <input type="number" name="amount" class="form-control" value="{{ old('amount') }}" required min="0" step="0.01">
                            @error('amount')
                            <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">To'lov sanasi <span class="text-danger">*</span></label>
                            <input type="date" name="payment_date" class="form-control" value="{{ old('payment_date', date('Y-m-d')) }}" required>
                            @error('payment_date')
                            <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">To'lov usuli <span class="text-danger">*</span></label>
                            <select name="payment_method" class="form-control" required>
                                <option value="bank" {{ old('payment_method') == 'bank' ? 'selected' : '' }}>Bank o'tkazmasi</option>
                                <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Naqd pul</option>
                                <option value="card" {{ old('payment_method') == 'card' ? 'selected' : '' }}>Plastik karta</option>
                                <option value="online" {{ old('payment_method') == 'online' ? 'selected' : '' }}>Onlayn to'lov</option>
                            </select>
                            @error('payment_method')
                            <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Kvitansiya raqami</label>
                            <input type="text" name="receipt_number" class="form-control" value="{{ old('receipt_number') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Izoh</label>
                            <textarea name="notes" class="form-control" rows="3">{{ old('notes') }}</textarea>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('finance.payments.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Orqaga
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Saqlash
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
