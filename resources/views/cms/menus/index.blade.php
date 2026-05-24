@extends('layouts.dashboard-new')

@section('title', 'CMS — Menyular')
@section('page-title', 'Menyu boshqaruvi')

@section('styles')
<style>
.menu-card { border:1px solid var(--c-border);border-radius:12px;overflow:hidden;background:var(--c-bg);transition:all .15s; }
.menu-card:hover { border-color:var(--c-emerald);box-shadow:0 4px 16px rgba(16,185,129,.1); }
.action-btn { width:30px;height:30px;border-radius:7px;border:1px solid var(--c-border);background:var(--c-bg);display:inline-flex;align-items:center;justify-content:center;font-size:12px;cursor:pointer;transition:all .12s;text-decoration:none;color:var(--c-text-2); }
.action-btn:hover { border-color:var(--c-teal);color:var(--c-teal);background:rgba(20,184,166,.07); }
.action-btn.danger:hover { border-color:var(--c-rose);color:var(--c-rose);background:rgba(244,63,94,.07); }
</style>
@endsection

@section('content')

<x-lms-alerts />

<div class="card mb-4">
    <div class="card-header d-flex align-items-center justify-content-between gap-3">
        <div class="d-flex align-items-center gap-2">
            <i class="fas fa-bars" style="color:var(--c-emerald)"></i>
            <span>Navigatsiya menyulari</span>
        </div>
        <a href="{{ route('cms.menus.create') }}" class="btn btn-sm" style="background:var(--c-emerald);color:#fff">
            <i class="fas fa-plus me-1"></i>Yangi menyu
        </a>
    </div>
</div>

<div class="row g-3">
    @forelse($menus ?? [] as $menu)
    <div class="col-lg-4 col-md-6">
        <div class="menu-card">
            <div class="d-flex align-items-center justify-content-between p-3"
                 style="border-bottom:1px solid var(--c-border)">
                <div class="d-flex align-items-center gap-2">
                    <div style="width:32px;height:32px;border-radius:8px;background:rgba(16,185,129,.1);display:flex;align-items:center;justify-content:center">
                        <i class="fas fa-bars" style="color:var(--c-emerald);font-size:12px"></i>
                    </div>
                    <span style="font-size:14px;font-weight:700;color:var(--c-text)">{{ $menu->name }}</span>
                </div>
                <span style="font-size:11px;font-weight:600;padding:3px 10px;border-radius:20px;
                    background:{{ $menu->is_active ? 'rgba(16,185,129,.12)' : 'rgba(100,116,139,.12)' }};
                    color:{{ $menu->is_active ? 'var(--c-emerald)' : 'var(--c-text-3)' }}">
                    {{ $menu->is_active ? 'Faol' : 'Nofaol' }}
                </span>
            </div>
            <div class="p-3">
                <div class="d-flex gap-3 mb-3" style="font-size:12px;color:var(--c-text-2)">
                    <span><i class="fas fa-map-marker-alt me-1" style="color:var(--c-sky)"></i>{{ ucfirst($menu->location) }}</span>
                    <span><i class="fas fa-list me-1" style="color:var(--c-violet)"></i>{{ $menu->menuItems->count() }} ta element</span>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('cms.menus.edit', $menu) }}" class="action-btn" title="Tahrirlash" style="flex:1;border-radius:8px;width:auto;height:30px;font-size:12px">
                        <i class="fas fa-pen me-1"></i><span>Tahrirlash</span>
                    </a>
                    <form action="{{ route('cms.menus.destroy', $menu) }}" method="POST" class="d-inline"
                          onsubmit="return confirm('Menyuni o\'chirishni tasdiqlaysizmi?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="action-btn danger" title="O'chirish">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-bars fa-3x mb-3" style="color:var(--c-border)"></i>
                <div style="font-size:15px;font-weight:600;color:var(--c-text-2);margin-bottom:4px">Menyular topilmadi</div>
                <div style="font-size:13px;color:var(--c-text-3)">Yangi menyu yaratish uchun yuqoridagi tugmani bosing</div>
            </div>
        </div>
    </div>
    @endforelse
</div>

@endsection
