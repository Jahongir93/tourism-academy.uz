@extends('layouts.dashboard-new')

@section('title', 'CMS — Sozlamalar')
@section('page-title', 'CMS Sozlamalari')

@section('styles')
<style>
.settings-nav a { display:flex;align-items:center;gap:8px;padding:10px 14px;border-radius:8px;font-size:13px;color:var(--c-text-2);text-decoration:none;transition:all .12s; }
.settings-nav a:hover,
.settings-nav a.active { background:rgba(124,58,237,.08);color:var(--c-violet); }
.cache-item { border:1px solid var(--c-border);border-radius:10px;padding:16px;text-align:center;transition:all .2s; }
</style>
@endsection

@section('content')

<x-lms-alerts />

<div class="row g-4">
    {{-- Sidebar nav --}}
    <div class="col-lg-3">
        <div class="card">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="fas fa-cog" style="color:var(--c-violet)"></i>
                <span>Sozlamalar</span>
            </div>
            <div class="card-body p-2 settings-nav">
                <a href="#general" class="active"><i class="fas fa-sliders-h" style="color:var(--c-violet)"></i>Umumiy</a>
                <a href="#seo"><i class="fas fa-search" style="color:var(--c-sky)"></i>SEO</a>
                <a href="#email"><i class="fas fa-envelope" style="color:var(--c-teal)"></i>Email</a>
                <a href="#social"><i class="fas fa-share-alt" style="color:var(--c-rose)"></i>Ijtimoiy tarmoqlar</a>
                <a href="#cache"><i class="fas fa-broom" style="color:var(--c-amber)"></i>Cache tozalash</a>
                <a href="#backup"><i class="fas fa-database" style="color:var(--c-emerald)"></i>Backup</a>
            </div>
        </div>
    </div>

    {{-- Main content --}}
    <div class="col-lg-9">
        {{-- Umumiy --}}
        <div class="card mb-4" id="general">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="fas fa-sliders-h" style="color:var(--c-violet)"></i>
                <span>Umumiy sozlamalar</span>
            </div>
            <div class="card-body">
                <form>
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label" style="font-size:13px">Sayt nomi</label>
                            <input type="text" class="form-control" value="Tourism va Service fakulteti">
                        </div>
                        <div class="col-12">
                            <label class="form-label" style="font-size:13px">Sayt tavsifi</label>
                            <textarea class="form-control" rows="3">Tourism va Service fakultetining rasmiy veb-sayti</textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" style="font-size:13px">Administrator emaili</label>
                            <input type="email" class="form-control" value="admin@tourism.uz">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" style="font-size:13px">Vaqt zonasi</label>
                            <select class="form-select">
                                <option value="Asia/Tashkent" selected>Asia/Tashkent (UTC+5)</option>
                                <option value="UTC">UTC</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" style="font-size:13px">Til</label>
                            <select class="form-select">
                                <option value="uz" selected>O'zbek</option>
                                <option value="ru">Русский</option>
                                <option value="en">English</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="maintenance" checked>
                                <label class="form-check-label" for="maintenance" style="font-size:13px">Sayt faol holati</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-sm" style="background:var(--c-violet);color:#fff">
                                <i class="fas fa-save me-1"></i>Saqlash
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Cache --}}
        <div class="card" id="cache">
            <div class="card-header d-flex align-items-center justify-content-between gap-2">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-broom" style="color:var(--c-amber)"></i>
                    <span>Cache tozalash</span>
                </div>
            </div>
            <div class="card-body">
                <p style="font-size:13px;color:var(--c-text-2);margin-bottom:20px">
                    Saytda o'zgarishlar kuchga kirmasa yoki eski ma'lumotlar ko'rinsa — cache ni tozalang.
                </p>
                <div class="row g-3 mb-4">
                    @php
                    $cacheItems = [
                        ['config','fa-sliders-h','var(--c-violet)','Config','config:clear'],
                        ['cache','fa-database','var(--c-sky)','Cache','cache:clear'],
                        ['route','fa-route','var(--c-emerald)','Route','route:clear'],
                        ['view','fa-eye','var(--c-violet)','View','view:clear'],
                    ];
                    @endphp
                    @foreach($cacheItems as [$key,$icon,$color,$label,$cmd])
                    <div class="col-sm-6 col-lg-3">
                        <div class="cache-item" id="item-{{ $key }}">
                            <i class="fas {{ $icon }} fa-2x mb-2" style="color:{{ $color }}"></i>
                            <div style="font-size:13px;font-weight:600;color:var(--c-text)">{{ $label }}</div>
                            <div style="font-size:11px;color:var(--c-text-3);margin-bottom:8px">{{ $cmd }}</div>
                            <div id="status-{{ $key }}"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <button id="btn-clear-cache" class="btn" style="background:var(--c-amber);color:#fff;padding:10px 32px" onclick="clearAllCache()">
                    <i class="fas fa-broom me-2"></i>Barchasini tozalash
                </button>
                <div id="cache-result" class="mt-3" style="display:none"></div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function clearAllCache() {
    const btn  = document.getElementById('btn-clear-cache');
    const result = document.getElementById('cache-result');
    const keys = ['config','cache','route','view'];

    keys.forEach(k => {
        document.getElementById('status-' + k).innerHTML = '<span class="spinner-border spinner-border-sm text-secondary"></span>';
        document.getElementById('item-' + k).style.borderColor = '';
    });

    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Tozalanmoqda...';
    result.style.display = 'none';

    fetch('{{ route('cms.settings.clear-cache') }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json', 'Content-Type': 'application/json' },
        body: JSON.stringify({})
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            let allOk = true;
            keys.forEach(k => {
                const item   = document.getElementById('item-' + k);
                const status = document.getElementById('status-' + k);
                const res    = data.results[k];
                if (res && res.ok) {
                    item.style.borderColor = 'var(--c-emerald)';
                    item.style.background  = 'rgba(16,185,129,.05)';
                    status.innerHTML = '<span style="font-size:11px;font-weight:600;padding:2px 8px;border-radius:20px;background:rgba(16,185,129,.12);color:var(--c-emerald)"><i class="fas fa-check me-1"></i>OK</span>';
                } else {
                    item.style.borderColor = 'var(--c-rose)';
                    item.style.background  = 'rgba(244,63,94,.05)';
                    status.innerHTML = '<span style="font-size:11px;font-weight:600;padding:2px 8px;border-radius:20px;background:rgba(244,63,94,.12);color:var(--c-rose)"><i class="fas fa-times me-1"></i>Xato</span>';
                    allOk = false;
                }
            });
            result.style.display = 'block';
            result.innerHTML = allOk
                ? '<div class="alert alert-success mb-0 py-2" style="font-size:13px"><i class="fas fa-check-circle me-2"></i>Barcha cache muvaffaqiyatli tozalandi!</div>'
                : '<div class="alert alert-warning mb-0 py-2" style="font-size:13px"><i class="fas fa-exclamation-triangle me-2"></i>Ba\'zi cache tozalanmadi.</div>';
        } else {
            result.style.display = 'block';
            result.innerHTML = '<div class="alert alert-danger mb-0 py-2" style="font-size:13px"><i class="fas fa-times-circle me-2"></i>Server xatosi yuz berdi.</div>';
        }
    })
    .catch(() => {
        result.style.display = 'block';
        result.innerHTML = '<div class="alert alert-danger mb-0 py-2" style="font-size:13px"><i class="fas fa-times-circle me-2"></i>Ulanishda xato.</div>';
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-broom me-2"></i>Barchasini tozalash';
    });
}
</script>
@endpush
