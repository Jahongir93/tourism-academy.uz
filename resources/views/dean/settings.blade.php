@extends('layouts.dashboard-new')

@section('title', 'Sozlamalar')
@section('page-title', 'Dekanat Sozlamalari')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm bg-gradient-secondary text-white">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1"><i class="fas fa-cog me-2"></i>Dekanat Sozlamalari</h4>
                            <p class="mb-0 opacity-75">{{ $faculty?->name ?? 'Fakultet' }}</p>
                        </div>
                        <a href="{{ route('dean.dashboard') }}" class="btn btn-light">
                            <i class="fas fa-arrow-left me-1"></i> Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-bell text-primary me-2"></i>Bildirishnomalar</h5>
                </div>
                <div class="card-body">
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="notifyNewStudent" checked>
                        <label class="form-check-label" for="notifyNewStudent">Yangi talaba qo'shilganda</label>
                    </div>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="notifyLowGpa" checked>
                        <label class="form-check-label" for="notifyLowGpa">Past GPA haqida ogohlantirish</label>
                    </div>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="notifyAbsence" checked>
                        <label class="form-check-label" for="notifyAbsence">Ko'p davomatsizlik haqida</label>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-chart-line text-success me-2"></i>Chegaralar</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Minimal GPA (ogohlantirish)</label>
                        <input type="number" class="form-control" value="2.5" step="0.1" min="0" max="5">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Maksimal davomatsizlik (kun)</label>
                        <input type="number" class="form-control" value="10" min="0">
                    </div>
                </div>
            </div>
        </div>
    </div>

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

<style>.bg-gradient-secondary { background: linear-gradient(135deg, #6c757d 0%, #495057 100%); }</style>
@endsection
