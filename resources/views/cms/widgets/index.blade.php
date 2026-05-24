@extends('layouts.dashboard-new')

@section('title', 'CMS — Vijetlar')
@section('page-title', 'Vijetlar boshqaruvi')

@section('styles')
<style>
.widget-card { border:1px solid var(--c-border);border-radius:12px;padding:24px;text-align:center;background:var(--c-bg);transition:all .15s; }
.widget-card:hover { border-color:var(--c-violet);box-shadow:0 4px 16px rgba(124,58,237,.1); }
</style>
@endsection

@section('content')

<x-lms-alerts />

<div class="card mb-4">
    <div class="card-header d-flex align-items-center justify-content-between gap-3">
        <div class="d-flex align-items-center gap-2">
            <i class="fas fa-puzzle-piece" style="color:var(--c-violet)"></i>
            <span>Sayt vijetlari</span>
        </div>
        <button class="btn btn-sm" style="background:var(--c-violet);color:#fff">
            <i class="fas fa-plus me-1"></i>Yangi vijet
        </button>
    </div>
</div>

<div class="row g-3">
    @php
    $widgets = [
        ['fa-images','var(--c-sky)','rgba(14,165,233,.08)','Slayder','Rasmlar galereyasi'],
        ['fa-chart-bar','var(--c-emerald)','rgba(16,185,129,.08)','Statistika','Raqamlar va grafika'],
        ['fa-comments','var(--c-violet)','rgba(124,58,237,.08)','Fikrlar','Mijozlar fikrlari'],
        ['fa-users','var(--c-amber)','rgba(245,158,11,.08)','Jamoa','Xodimlar ro\'yxati'],
    ];
    @endphp
    @foreach($widgets as [$icon,$color,$bg,$label,$hint])
    <div class="col-lg-3 col-md-6">
        <div class="widget-card">
            <div style="width:56px;height:56px;border-radius:14px;background:{{ $bg }};display:flex;align-items:center;justify-content:center;margin:0 auto 12px">
                <i class="fas {{ $icon }} fa-xl" style="color:{{ $color }}"></i>
            </div>
            <div style="font-size:15px;font-weight:700;color:var(--c-text);margin-bottom:4px">{{ $label }}</div>
            <div style="font-size:12px;color:var(--c-text-3);margin-bottom:14px">{{ $hint }}</div>
            <button class="btn btn-sm" style="background:{{ $bg }};color:{{ $color }};border:1px solid {{ $color }}20;font-size:12px">
                <i class="fas fa-cog me-1"></i>Sozlash
            </button>
        </div>
    </div>
    @endforeach
</div>

@endsection
