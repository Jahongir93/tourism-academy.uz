@extends('layouts.dashboard-new')

@section('title', 'To\'lov ma\'lumotlari')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">To'lov #{{ $payment->id }}</h5>
                    <div>
                        @if($payment->status !== 'cancelled')
                        <a href="{{ route('finance.payments.edit', $payment) }}" class="btn btn-sm btn-warning">
                            <i class="fas fa-edit"></i> Tahrirlash
                        </a>
                        @endif
                        <a href="{{ route('finance.payments.index') }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left"></i> Orqaga
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-muted">Talaba</h6>
                            <p class="mb-1"><strong>{{ $payment->student->user->name ?? 'N/A' }}</strong></p>
                            <p class="text-muted small mb-0">ID: {{ $payment->student->student_id ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6 text-end">
                            <h6 class="text-muted">Holat</h6>
                            @if($payment->status === 'completed')
                                <span class="badge badge-success fs-6">To'langan</span>
                            @elseif($payment->status === 'pending')
                                <span class="badge badge-warning fs-6">Kutilmoqda</span>
                            @else
                                <span class="badge badge-danger fs-6">Bekor qilingan</span>
                            @endif
                        </div>
                    </div>

                    <hr>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6 class="text-muted">Summa</h6>
                            <h3 class="text-primary mb-0">{{ number_format($payment->amount, 0, '.', ' ') }} so'm</h3>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">To'lov sanasi</h6>
                            <p class="mb-0">{{ $payment->payment_date->format('d.m.Y') }}</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6 class="text-muted">To'lov usuli</h6>
                            <span class="badge badge-info">{{ $payment->payment_method_label }}</span>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Kvitansiya raqami</h6>
                            <p class="mb-0">{{ $payment->receipt_number ?? 'N/A' }}</p>
                        </div>
                    </div>

                    @if($payment->contract)
                    <div class="row mb-3">
                        <div class="col-12">
                            <h6 class="text-muted">Shartnoma</h6>
                            <p class="mb-0">{{ $payment->contract->contract_number }}</p>
                        </div>
                    </div>
                    @endif

                    @if($payment->notes)
                    <div class="row mb-3">
                        <div class="col-12">
                            <h6 class="text-muted">Izoh</h6>
                            <p class="mb-0">{{ $payment->notes }}</p>
                        </div>
                    </div>
                    @endif

                    <hr>

                    <div class="row">
                        <div class="col-md-6">
                            <p class="text-muted small mb-0">Yaratilgan: {{ $payment->created_at->format('d.m.Y H:i') }}</p>
                        </div>
                        <div class="col-md-6 text-end">
                            <p class="text-muted small mb-0">Yangilangan: {{ $payment->updated_at->format('d.m.Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
