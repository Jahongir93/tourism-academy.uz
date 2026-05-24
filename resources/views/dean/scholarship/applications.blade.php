@extends('layouts.dashboard-new')

@section('title', 'Stipendiya arizalari')
@section('page-title', 'Stipendiya arizalari')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm bg-gradient-info text-white">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1"><i class="fas fa-file-signature me-2"></i>Stipendiya arizalari</h4>
                            <p class="mb-0 opacity-75">{{ $faculty?->name ?? 'Fakultet' }}</p>
                        </div>
                        <a href="{{ route('dean.scholarship.index') }}" class="btn btn-light">
                            <i class="fas fa-arrow-left me-1"></i> Stipendiatlar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body text-center py-5">
            <i class="fas fa-file-signature fa-4x text-muted mb-4 opacity-50"></i>
            <h4 class="text-muted">Stipendiya arizalari moduli</h4>
            <p class="text-muted mb-4">Bu modul tez orada ishga tushiriladi.</p>
            <a href="{{ route('dean.dashboard') }}" class="btn btn-primary">
                <i class="fas fa-home me-1"></i> Dashboardga qaytish
            </a>
        </div>
    </div>
</div>

<style>.bg-gradient-info { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }</style>
@endsection
