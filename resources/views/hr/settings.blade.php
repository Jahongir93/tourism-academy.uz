@extends('layouts.dashboard-new')

@section('title', 'HR Sozlamalari')
@section('page-title', 'HR Sozlamalari')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm bg-gradient-primary text-white">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1"><i class="fas fa-cog me-2"></i>HR Sozlamalari</h4>
                            <p class="mb-0 opacity-75">HR moduli sozlamalarini boshqarish</p>
                        </div>
                        <a href="{{ route('hr.dashboard') }}" class="btn btn-light">
                            <i class="fas fa-arrow-left me-1"></i> Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Ish vaqti sozlamalari -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-clock text-primary me-2"></i>Ish vaqti sozlamalari</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Ish boshlanish vaqti</label>
                        <input type="time" class="form-control" value="09:00">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Ish tugash vaqti</label>
                        <input type="time" class="form-control" value="18:00">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Kechikish chegarasi (daqiqa)</label>
                        <input type="number" class="form-control" value="15" min="0">
                    </div>
                    <div class="alert alert-info mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Bu sozlamalar davomat hisobotlarida ishlatiladi.
                    </div>
                </div>
            </div>
        </div>

        <!-- Ta'til sozlamalari -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-calendar-alt text-success me-2"></i>Ta'til sozlamalari</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Yillik ta'til kunlari</label>
                        <input type="number" class="form-control" value="24" min="0">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Kasallik ta'tili kunlari</label>
                        <input type="number" class="form-control" value="10" min="0">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Boshqa ta'til kunlari</label>
                        <input type="number" class="form-control" value="5" min="0">
                    </div>
                </div>
            </div>
        </div>

        <!-- Bildirishnoma sozlamalari -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-bell text-warning me-2"></i>Bildirishnoma sozlamalari</h5>
                </div>
                <div class="card-body">
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="notifyNewEmployee" checked>
                        <label class="form-check-label" for="notifyNewEmployee">Yangi xodim qo'shilganda xabar berish</label>
                    </div>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="notifyLeaveRequest" checked>
                        <label class="form-check-label" for="notifyLeaveRequest">Ta'til arizasi kelganda xabar berish</label>
                    </div>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="notifyContractExpiry" checked>
                        <label class="form-check-label" for="notifyContractExpiry">Shartnoma muddati tugashi haqida eslatish</label>
                    </div>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="notifyBirthday" checked>
                        <label class="form-check-label" for="notifyBirthday">Tug'ilgan kunlar haqida eslatish</label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hisobot sozlamalari -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-file-pdf text-danger me-2"></i>Hisobot sozlamalari</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Hisobot formati</label>
                        <select class="form-select">
                            <option value="pdf">PDF</option>
                            <option value="excel">Excel</option>
                            <option value="csv">CSV</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Avtomatik hisobot yuborish</label>
                        <select class="form-select">
                            <option value="none">O'chirilgan</option>
                            <option value="weekly">Haftalik</option>
                            <option value="monthly">Oylik</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Saqlash -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-end">
                    <button type="button" class="btn btn-outline-secondary me-2">
                        <i class="fas fa-undo me-1"></i> Bekor qilish
                    </button>
                    <button type="button" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Saqlash
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
</style>
@endsection
