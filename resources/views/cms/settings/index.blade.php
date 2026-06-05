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
                <a href="#system"><i class="fas fa-server" style="color:var(--c-emerald)"></i>Tizim (Deploy)</a>
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

        {{-- ── Tizim boshqaruvi / Deploy ── --}}
        <div class="card mt-4" id="system">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="fas fa-server" style="color:var(--c-emerald)"></i>
                <span>Tizim boshqaruvi (Deploy)</span>
            </div>
            <div class="card-body">
                <p style="font-size:13px;color:var(--c-text-2);margin-bottom:16px">
                    Kod yangilangandan so'ng (GitHub'dan tortilganda) bu yerdan terminalga
                    kirmasdan barcha kerakli amallarni bajaring.
                </p>

                <div style="background:rgba(16,185,129,.06);border:1px solid rgba(16,185,129,.2);border-radius:10px;padding:16px;margin-bottom:18px">
                    <div style="font-size:13px;font-weight:700;color:var(--c-text);margin-bottom:4px">
                        <i class="fas fa-rocket me-1" style="color:var(--c-emerald)"></i> To'liq yangilash
                    </div>
                    <div style="font-size:12px;color:var(--c-text-3);margin-bottom:12px">
                        migrate → optimize:clear → config / route / view / event cache → storage:link → up
                    </div>
                    <button id="btn-deploy" class="btn" style="background:var(--c-emerald);color:#fff;padding:10px 32px" onclick="runDeploy()">
                        <i class="fas fa-rocket me-2"></i>To'liq yangilash (Deploy)
                    </button>
                </div>

                <div style="font-size:12px;font-weight:700;color:var(--c-text-3);text-transform:uppercase;letter-spacing:.5px;margin-bottom:10px">Alohida amallar</div>
                <div class="row g-2 mb-3">
                    @php
                    $sysTasks = [
                        ['migrate','fa-database','Migratsiya','var(--c-sky)'],
                        ['storage-link','fa-link','Storage link','var(--c-violet)'],
                        ['config-cache','fa-sliders-h','Config cache','var(--c-amber)'],
                        ['route-cache','fa-route','Route cache','var(--c-emerald)'],
                        ['view-cache','fa-eye','View cache','var(--c-sky)'],
                        ['event-cache','fa-bolt','Event cache','var(--c-amber)'],
                        ['seed-academic','fa-calendar','O\'quv yillari','var(--c-violet)'],
                        ['seed-teachers','fa-chalkboard-teacher','O\'qituvchilar','var(--c-emerald)'],
                        ['up','fa-play','Saytni yoqish','#10b981'],
                        ['down','fa-pause','Texnik rejim','#ef4444'],
                    ];
                    @endphp
                    @foreach($sysTasks as [$task,$icon,$label,$color])
                    <div class="col-6 col-md-3">
                        <button class="btn w-100" style="background:var(--c-bg);border:1px solid var(--c-border);color:var(--c-text);font-size:12px;padding:9px"
                                onclick="runTask('{{ $task }}', this)">
                            <i class="fas {{ $icon }} me-1" style="color:{{ $color }}"></i>{{ $label }}
                        </button>
                    </div>
                    @endforeach
                </div>

                <div id="system-result" class="mt-2" style="display:none"></div>
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

// ── Tizim boshqaruvi (deploy) ──
function renderSysResults(results) {
    const box = document.getElementById('system-result');
    box.style.display = 'block';
    let html = '<div style="border:1px solid var(--c-border);border-radius:10px;overflow:hidden">';
    results.forEach(r => {
        const ok = r.ok || r.skipped;
        const color = r.ok ? 'var(--c-emerald)' : (r.skipped ? 'var(--c-amber)' : 'var(--c-rose)');
        const icon = r.ok ? 'fa-check' : (r.skipped ? 'fa-forward' : 'fa-times');
        html += '<div style="display:flex;align-items:center;gap:10px;padding:8px 12px;border-bottom:1px solid var(--c-border);font-size:12.5px">'
             +  '<i class="fas ' + icon + '" style="color:' + color + ';width:16px"></i>'
             +  '<span style="font-weight:600;min-width:160px">' + (r.label || r.command) + '</span>'
             +  '<span style="color:var(--c-text-3);flex:1;white-space:pre-wrap;word-break:break-word">' + (r.output || '').substring(0, 300) + '</span>'
             +  '</div>';
    });
    html += '</div>';
    box.innerHTML = html;
}

function runDeploy() {
    if (!confirm("To'liq yangilash bajarilsinmi? (migrate + cache + storage:link)")) return;
    const btn = document.getElementById('btn-deploy');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Bajarilmoqda...';
    fetch('{{ route('cms.settings.system.deploy') }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
        renderSysResults(data.results || []);
    })
    .catch(() => {
        const box = document.getElementById('system-result');
        box.style.display = 'block';
        box.innerHTML = '<div class="alert alert-danger mb-0 py-2" style="font-size:13px">Ulanishda xato.</div>';
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-rocket me-2"></i>To\'liq yangilash (Deploy)';
    });
}

function runTask(task, el) {
    const orig = el.innerHTML;
    el.disabled = true;
    el.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
    fetch('{{ url('cms/settings/system/run') }}/' + task, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
        if (data.result) renderSysResults([data.result]);
    })
    .catch(() => {
        const box = document.getElementById('system-result');
        box.style.display = 'block';
        box.innerHTML = '<div class="alert alert-danger mb-0 py-2" style="font-size:13px">Ulanishda xato.</div>';
    })
    .finally(() => {
        el.disabled = false;
        el.innerHTML = orig;
    });
}
</script>
@endpush
