@extends('layouts.dashboard-new')

@section('title', 'Moliya Sozlamalari')
@section('page-title', 'Moliya Sozlamalari')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm bg-gradient-warning text-white">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1"><i class="fas fa-coins me-2"></i>Moliya Sozlamalari</h4>
                            <p class="mb-0 opacity-75">To'lov kontrakt, valyuta va moliyaviy hisobotlar sozlamalari</p>
                        </div>
                        <a href="{{ route('settings.index') }}" class="btn btn-light">
                            <i class="fas fa-arrow-left me-1"></i> Orqaga
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <form action="{{ route('settings.finance.update') }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row">
            <!-- Valyuta Sozlamalari -->
            <div class="col-lg-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-dollar-sign text-success me-2"></i>Valyuta Sozlamalari</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Asosiy valyuta <span class="text-danger">*</span></label>
                            <select name="currency" class="form-select" required>
                                <option value="UZS" {{ old('currency', $settings->where('key', 'currency')->first()?->value ?? 'UZS') == 'UZS' ? 'selected' : '' }}>UZS - O'zbek so'mi</option>
                                <option value="USD" {{ old('currency', $settings->where('key', 'currency')->first()?->value ?? 'UZS') == 'USD' ? 'selected' : '' }}>USD - AQSH dollari</option>
                                <option value="EUR" {{ old('currency', $settings->where('key', 'currency')->first()?->value ?? 'UZS') == 'EUR' ? 'selected' : '' }}>EUR - Yevro</option>
                                <option value="RUB" {{ old('currency', $settings->where('key', 'currency')->first()?->value ?? 'UZS') == 'RUB' ? 'selected' : '' }}>RUB - Rus rubli</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Valyuta belgisi</label>
                            <input type="text" name="currency_symbol" class="form-control"
                                   value="{{ old('currency_symbol', $settings->where('key', 'currency_symbol')->first()?->value ?? 'so\'m') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Valyuta pozitsiyasi</label>
                            <select name="currency_position" class="form-select">
                                <option value="after" {{ old('currency_position', $settings->where('key', 'currency_position')->first()?->value ?? 'after') == 'after' ? 'selected' : '' }}>Keyin (1,000,000 so'm)</option>
                                <option value="before" {{ old('currency_position', $settings->where('key', 'currency_position')->first()?->value ?? 'after') == 'before' ? 'selected' : '' }}>Oldin ($1,000)</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Minglik ajratuvchi</label>
                            <select name="thousand_separator" class="form-select">
                                <option value="," {{ old('thousand_separator', $settings->where('key', 'thousand_separator')->first()?->value ?? ',') == ',' ? 'selected' : '' }}>Vergul (1,000,000)</option>
                                <option value=" " {{ old('thousand_separator', $settings->where('key', 'thousand_separator')->first()?->value ?? ',') == ' ' ? 'selected' : '' }}>Bo'shliq (1 000 000)</option>
                                <option value="." {{ old('thousand_separator', $settings->where('key', 'thousand_separator')->first()?->value ?? ',') == '.' ? 'selected' : '' }}>Nuqta (1.000.000)</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kontrakt To'lovi -->
            <div class="col-lg-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-file-contract text-primary me-2"></i>Kontrakt To'lovi</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Yillik kontrakt summasi (so'm)</label>
                            <input type="number" name="annual_contract_fee" class="form-control"
                                   value="{{ old('annual_contract_fee', $settings->where('key', 'annual_contract_fee')->first()?->value ?? '15000000') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Semestrlik to'lov (so'm)</label>
                            <input type="number" name="semester_fee" class="form-control"
                                   value="{{ old('semester_fee', $settings->where('key', 'semester_fee')->first()?->value ?? '7500000') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">To'lov muddati (kunlar)</label>
                            <input type="number" name="payment_deadline_days" class="form-control" min="1"
                                   value="{{ old('payment_deadline_days', $settings->where('key', 'payment_deadline_days')->first()?->value ?? '30') }}">
                            <small class="text-muted">Semestr boshlanganidan keyin</small>
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="partial_payment_allowed" id="partial_payment_allowed"
                                       {{ old('partial_payment_allowed', $settings->where('key', 'partial_payment_allowed')->first()?->value ?? '1') == '1' ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="partial_payment_allowed">Qisman to'lovga ruxsat berish</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Minimal qisman to'lov (%)</label>
                            <input type="number" name="min_partial_payment_percent" class="form-control" min="10" max="100"
                                   value="{{ old('min_partial_payment_percent', $settings->where('key', 'min_partial_payment_percent')->first()?->value ?? '25') }}">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Chegirmalar -->
            <div class="col-lg-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-percentage text-info me-2"></i>Chegirmalar</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="discount_enabled" id="discount_enabled"
                                       {{ old('discount_enabled', $settings->where('key', 'discount_enabled')->first()?->value ?? '1') == '1' ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="discount_enabled">Chegirmalar tizimini yoqish</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Imtiyozli talabalar chegirmasi (%)</label>
                            <input type="number" name="orphan_discount" class="form-control" min="0" max="100"
                                   value="{{ old('orphan_discount', $settings->where('key', 'orphan_discount')->first()?->value ?? '50') }}">
                            <small class="text-muted">Yetim va mehribonlik uyi tarbiyalanuvchilari</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">A'lo o'quvchilar chegirmasi (%)</label>
                            <input type="number" name="excellent_student_discount" class="form-control" min="0" max="100"
                                   value="{{ old('excellent_student_discount', $settings->where('key', 'excellent_student_discount')->first()?->value ?? '10') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Erta to'lov chegirmasi (%)</label>
                            <input type="number" name="early_payment_discount" class="form-control" min="0" max="100"
                                   value="{{ old('early_payment_discount', $settings->where('key', 'early_payment_discount')->first()?->value ?? '5') }}">
                            <small class="text-muted">Muddatidan oldin to'langan holatda</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Maksimal chegirma (%)</label>
                            <input type="number" name="max_discount" class="form-control" min="0" max="100"
                                   value="{{ old('max_discount', $settings->where('key', 'max_discount')->first()?->value ?? '50') }}">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Jarima va Kechikish -->
            <div class="col-lg-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-exclamation-triangle text-danger me-2"></i>Jarima va Kechikish</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="late_fee_enabled" id="late_fee_enabled"
                                       {{ old('late_fee_enabled', $settings->where('key', 'late_fee_enabled')->first()?->value ?? '1') == '1' ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="late_fee_enabled">Kechikish jarimalarini yoqish</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Kunlik jarima foizi (%)</label>
                            <input type="number" name="daily_late_fee_percent" class="form-control" min="0" max="10" step="0.01"
                                   value="{{ old('daily_late_fee_percent', $settings->where('key', 'daily_late_fee_percent')->first()?->value ?? '0.1') }}">
                            <small class="text-muted">Har bir kechikkan kun uchun</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Maksimal jarima foizi (%)</label>
                            <input type="number" name="max_late_fee_percent" class="form-control" min="0" max="100"
                                   value="{{ old('max_late_fee_percent', $settings->where('key', 'max_late_fee_percent')->first()?->value ?? '20') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Ogohlantirish kunlari</label>
                            <input type="number" name="payment_warning_days" class="form-control" min="1"
                                   value="{{ old('payment_warning_days', $settings->where('key', 'payment_warning_days')->first()?->value ?? '7') }}">
                            <small class="text-muted">Muddatidan necha kun oldin ogohlantirish yuborish</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- To'lov Usullari -->
            <div class="col-lg-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-credit-card text-secondary me-2"></i>To'lov Usullari</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="cash_payment" id="cash_payment"
                                       {{ old('cash_payment', $settings->where('key', 'cash_payment')->first()?->value ?? '1') == '1' ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="cash_payment">Naqd pul to'lovi</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="card_payment" id="card_payment"
                                       {{ old('card_payment', $settings->where('key', 'card_payment')->first()?->value ?? '1') == '1' ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="card_payment">Bank kartasi orqali</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="bank_transfer" id="bank_transfer"
                                       {{ old('bank_transfer', $settings->where('key', 'bank_transfer')->first()?->value ?? '1') == '1' ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="bank_transfer">Bank o'tkazmasi</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="online_payment" id="online_payment"
                                       {{ old('online_payment', $settings->where('key', 'online_payment')->first()?->value ?? '1') == '1' ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="online_payment">Onlayn to'lov (Payme, Click)</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bank Hisobi Ma'lumotlari -->
            <div class="col-lg-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-university text-warning me-2"></i>Bank Hisobi</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Bank nomi</label>
                            <input type="text" name="bank_name" class="form-control"
                                   value="{{ old('bank_name', $settings->where('key', 'bank_name')->first()?->value ?? '') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Hisob raqami</label>
                            <input type="text" name="bank_account" class="form-control"
                                   value="{{ old('bank_account', $settings->where('key', 'bank_account')->first()?->value ?? '') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">MFO</label>
                            <input type="text" name="bank_mfo" class="form-control"
                                   value="{{ old('bank_mfo', $settings->where('key', 'bank_mfo')->first()?->value ?? '') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">INN</label>
                            <input type="text" name="bank_inn" class="form-control"
                                   value="{{ old('bank_inn', $settings->where('key', 'bank_inn')->first()?->value ?? '') }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Submit -->
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-end">
                        <button type="reset" class="btn btn-outline-secondary me-2">
                            <i class="fas fa-undo me-1"></i> Bekor qilish
                        </button>
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-save me-1"></i> Saqlash
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
.bg-gradient-warning {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
}
</style>
@endsection
