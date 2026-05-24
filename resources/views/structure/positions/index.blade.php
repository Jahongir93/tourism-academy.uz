@extends('layouts.dashboard-new')

@section('title', 'Lavozimlar — Tuzilma — HEMIS')
@section('page-title', 'Lavozimlar katalogi')

@section('styles')
<style>
.action-btn { display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;border-radius:7px;border:none;background:transparent;cursor:pointer;transition:all .15s;font-size:13px;padding:0;text-decoration:none; }
.action-btn:hover { background:var(--c-bg); }
</style>
@endsection

@section('content')

{{-- Stat cards --}}
<div class="row g-4 mb-4">
    <div class="col-6 col-xl-3">
        <div class="stat-card" style="color:var(--c-emerald)">
            <div class="stat-card-icon" style="background:rgba(16,185,129,.12);color:var(--c-emerald)">
                <i class="fas fa-id-badge"></i>
            </div>
            <div class="stat-card-value" style="color:var(--c-text)">{{ $statistics['total_positions'] ?? 0 }}</div>
            <div class="stat-card-label">Jami lavozimlar</div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card" style="color:var(--c-violet)">
            <div class="stat-card-icon" style="background:rgba(124,58,237,.12);color:var(--c-violet)">
                <i class="fas fa-crown"></i>
            </div>
            <div class="stat-card-value" style="color:var(--c-text)">{{ $statistics['leadership'] ?? 0 }}</div>
            <div class="stat-card-label">Rahbariyat</div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card" style="color:var(--c-sky)">
            <div class="stat-card-icon" style="background:rgba(14,165,233,.12);color:var(--c-sky)">
                <i class="fas fa-chalkboard-teacher"></i>
            </div>
            <div class="stat-card-value" style="color:var(--c-text)">{{ $statistics['academic'] ?? 0 }}</div>
            <div class="stat-card-label">Akademik</div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card" style="color:var(--c-amber)">
            <div class="stat-card-icon" style="background:rgba(245,158,11,.12);color:var(--c-amber)">
                <i class="fas fa-briefcase"></i>
            </div>
            <div class="stat-card-value" style="color:var(--c-text)">{{ $statistics['administrative'] ?? 0 }}</div>
            <div class="stat-card-label">Ma'muriy</div>
        </div>
    </div>
</div>

{{-- Filter --}}
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('structure.positions.index') }}">
            <div class="row g-2 align-items-end">
                <div class="col-12 col-md-3">
                    <label class="form-label mb-1" style="font-size:12px;font-weight:600;color:var(--c-text-2)">Kategoriya</label>
                    <select name="category" class="form-select">
                        <option value="">Barcha kategoriyalar</option>
                        <option value="leadership"     {{ request('category') == 'leadership'     ? 'selected' : '' }}>Rahbariyat</option>
                        <option value="academic"       {{ request('category') == 'academic'       ? 'selected' : '' }}>Akademik</option>
                        <option value="administrative" {{ request('category') == 'administrative' ? 'selected' : '' }}>Ma'muriy</option>
                        <option value="support"        {{ request('category') == 'support'        ? 'selected' : '' }}>Yordamchi</option>
                    </select>
                </div>
                <div class="col-12 col-md-5">
                    <label class="form-label mb-1" style="font-size:12px;font-weight:600;color:var(--c-text-2)">Qidirish</label>
                    <div class="position-relative">
                        <i class="fas fa-search position-absolute" style="left:12px;top:50%;transform:translateY(-50%);color:var(--c-text-3);font-size:13px"></i>
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Lavozim nomi yoki kodi..." class="form-control" style="padding-left:34px!important">
                    </div>
                </div>
                <div class="col-12 col-md-4 d-flex gap-2 align-self-end">
                    <button type="submit" class="btn btn-primary flex-fill">
                        <i class="fas fa-search me-1"></i>Qidirish
                    </button>
                    <a href="{{ route('structure.positions.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times"></i>
                    </a>
                    <a href="{{ route('structure.positions.hierarchy') }}" class="btn btn-outline-secondary" title="Ierarxiya">
                        <i class="fas fa-sitemap"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Table --}}
<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-2">
            <i class="fas fa-id-badge" style="color:var(--c-emerald)"></i>
            <span>Lavozimlar ro'yxati</span>
            @if($positions->total())
            <span class="badge" style="background:rgba(16,185,129,.12);color:var(--c-emerald);font-size:11px">
                {{ $positions->total() }} ta
            </span>
            @endif
        </div>
        <a href="{{ route('structure.positions.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus me-1"></i>Yangi lavozim
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th style="width:90px">Kod</th>
                        <th>Nomi</th>
                        <th style="width:130px">Kategoriya</th>
                        <th style="width:100px" class="text-center">Daraja</th>
                        <th style="width:120px" class="text-center">Maosh darajasi</th>
                        <th style="width:80px" class="text-center">Holat</th>
                        <th style="width:90px" class="text-center">Amal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($positions as $position)
                    <tr>
                        <td>
                            <span class="badge" style="background:rgba(16,185,129,.12);color:var(--c-emerald);font-weight:600">
                                {{ $position->code }}
                            </span>
                        </td>
                        <td>
                            <div style="font-weight:600;color:var(--c-text)">{{ $position->name_uz ?? $position->name }}</div>
                            @if($position->name_ru)
                                <div style="font-size:11px;color:var(--c-text-3)">{{ $position->name_ru }}</div>
                            @endif
                        </td>
                        <td>
                            @php
                                $catColors = [
                                    'leadership'     => ['rgba(124,58,237,.12)',  'var(--c-violet)'],
                                    'academic'       => ['rgba(14,165,233,.12)',  'var(--c-sky)'],
                                    'administrative' => ['rgba(245,158,11,.12)',  'var(--c-amber)'],
                                    'support'        => ['rgba(20,184,166,.12)',  'var(--c-teal)'],
                                ];
                                $catLabels = [
                                    'leadership'     => 'Rahbariyat',
                                    'academic'       => 'Akademik',
                                    'administrative' => "Ma'muriy",
                                    'support'        => 'Yordamchi',
                                ];
                                [$cbg, $cc] = $catColors[$position->category] ?? ['rgba(148,163,184,.12)', 'var(--c-text-3)'];
                                $catLabel = $catLabels[$position->category] ?? ucfirst($position->category ?? '—');
                            @endphp
                            <span class="badge" style="background:{{ $cbg }};color:{{ $cc }};font-size:11px">
                                {{ $catLabel }}
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="badge" style="background:rgba(16,185,129,.12);color:var(--c-emerald);font-size:11px">
                                {{ $position->level }}-daraja
                            </span>
                        </td>
                        <td class="text-center" style="font-size:13px;color:var(--c-text-2)">
                            {{ $position->salary_grade ?? '—' }}
                        </td>
                        <td class="text-center">
                            @if($position->is_active)
                                <span class="badge" style="background:rgba(16,185,129,.12);color:var(--c-emerald)">Faol</span>
                            @else
                                <span class="badge" style="background:rgba(244,63,94,.12);color:var(--c-rose)">Nofaol</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex justify-content-center gap-1">
                                <a href="{{ route('structure.positions.show', $position) }}" class="action-btn" title="Ko'rish" style="color:var(--c-sky)"><i class="fas fa-eye"></i></a>
                                <a href="{{ route('structure.positions.edit', $position) }}" class="action-btn" title="Tahrirlash" style="color:var(--c-amber)"><i class="fas fa-edit"></i></a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7">
                            <div class="empty-state py-5">
                                <div class="empty-state-icon"><i class="fas fa-id-badge"></i></div>
                                <div class="empty-state-sub">Lavozimlar topilmadi</div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($positions->hasPages())
        <div class="px-4 py-3" style="border-top:1px solid var(--c-border)">
            {{ $positions->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>

@endsection
