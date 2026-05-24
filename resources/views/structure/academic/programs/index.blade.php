@extends('layouts.dashboard-new')

@section('title', "Ta'lim yo'nalishlari — HEMIS")
@section('page-title', "Ta'lim yo'nalishlari")

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
                <i class="fas fa-graduation-cap"></i>
            </div>
            <div class="stat-card-value" style="color:var(--c-text)">{{ $statistics['total_programs'] ?? 0 }}</div>
            <div class="stat-card-label">Jami yo'nalishlar</div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card" style="color:var(--c-sky)">
            <div class="stat-card-icon" style="background:rgba(14,165,233,.12);color:var(--c-sky)">
                <i class="fas fa-user-graduate"></i>
            </div>
            <div class="stat-card-value" style="color:var(--c-text)">{{ $statistics['bakalavriat'] ?? 0 }}</div>
            <div class="stat-card-label">Bakalavriat</div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card" style="color:var(--c-violet)">
            <div class="stat-card-icon" style="background:rgba(124,58,237,.12);color:var(--c-violet)">
                <i class="fas fa-user-tie"></i>
            </div>
            <div class="stat-card-value" style="color:var(--c-text)">{{ $statistics['magistratura'] ?? 0 }}</div>
            <div class="stat-card-label">Magistratura</div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card" style="color:var(--c-amber)">
            <div class="stat-card-icon" style="background:rgba(245,158,11,.12);color:var(--c-amber)">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-card-value" style="color:var(--c-text)">{{ $statistics['active'] ?? 0 }}</div>
            <div class="stat-card-label">Faol yo'nalishlar</div>
        </div>
    </div>
</div>

{{-- Filter --}}
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('structure.academic.programs.index') }}">
            <div class="row g-2 align-items-end">
                <div class="col-12 col-md-3">
                    <label class="form-label mb-1" style="font-size:12px;font-weight:600;color:var(--c-text-2)">Fakultet</label>
                    <select name="faculty_id" class="form-select">
                        <option value="">Barcha fakultetlar</option>
                        @foreach($faculties as $faculty)
                            <option value="{{ $faculty->id }}" {{ request('faculty_id') == $faculty->id ? 'selected' : '' }}>
                                {{ $faculty->short_name ?? $faculty->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-2">
                    <label class="form-label mb-1" style="font-size:12px;font-weight:600;color:var(--c-text-2)">Ta'lim darajasi</label>
                    <select name="level" class="form-select">
                        <option value="">Barchasi</option>
                        <option value="bakalavriat"  {{ request('level') == 'bakalavriat'  ? 'selected' : '' }}>Bakalavriat</option>
                        <option value="magistratura" {{ request('level') == 'magistratura' ? 'selected' : '' }}>Magistratura</option>
                        <option value="doktorantura" {{ request('level') == 'doktorantura' ? 'selected' : '' }}>Doktorantura</option>
                    </select>
                </div>
                <div class="col-12 col-md-2">
                    <label class="form-label mb-1" style="font-size:12px;font-weight:600;color:var(--c-text-2)">Ta'lim shakli</label>
                    <select name="education_form" class="form-select">
                        <option value="">Barchasi</option>
                        <option value="kunduzgi" {{ request('education_form') == 'kunduzgi' ? 'selected' : '' }}>Kunduzgi</option>
                        <option value="kechki"   {{ request('education_form') == 'kechki'   ? 'selected' : '' }}>Kechki</option>
                        <option value="sirtqi"   {{ request('education_form') == 'sirtqi'   ? 'selected' : '' }}>Sirtqi</option>
                    </select>
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label mb-1" style="font-size:12px;font-weight:600;color:var(--c-text-2)">Qidirish</label>
                    <div class="position-relative">
                        <i class="fas fa-search position-absolute" style="left:12px;top:50%;transform:translateY(-50%);color:var(--c-text-3);font-size:13px"></i>
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Kod yoki yo'nalish nomi..." class="form-control" style="padding-left:34px!important">
                    </div>
                </div>
                <div class="col-12 col-md-2 d-flex gap-2 align-self-end">
                    <button type="submit" class="btn btn-primary flex-fill">
                        <i class="fas fa-search me-1"></i>Qidirish
                    </button>
                    <a href="{{ route('structure.academic.programs.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times"></i>
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
            <i class="fas fa-list-alt" style="color:var(--c-emerald)"></i>
            <span>Ta'lim yo'nalishlari ro'yxati</span>
            @if($programs->total())
            <span class="badge" style="background:rgba(16,185,129,.12);color:var(--c-emerald);font-size:11px">
                {{ $programs->total() }} ta
            </span>
            @endif
        </div>
        <a href="{{ route('structure.academic.programs.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus me-1"></i>Yangi yo'nalish
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th style="width:100px">Kod</th>
                        <th>Nomi</th>
                        <th style="width:130px">Fakultet</th>
                        <th style="width:110px" class="text-center">Daraja</th>
                        <th style="width:100px" class="text-center">Shakli</th>
                        <th style="width:100px" class="text-center">Davomiyligi</th>
                        <th style="width:90px" class="text-center">Kreditlar</th>
                        <th style="width:80px" class="text-center">Status</th>
                        <th style="width:90px" class="text-center">Amal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($programs as $program)
                    <tr>
                        <td>
                            <a href="{{ route('structure.academic.programs.show', $program) }}"
                               class="text-decoration-none fw-semibold" style="color:var(--c-emerald)">
                                {{ $program->code }}
                            </a>
                        </td>
                        <td>
                            <div style="font-weight:600;color:var(--c-text)">{{ $program->name_uz }}</div>
                            @if($program->name_ru)
                                <div style="font-size:11px;color:var(--c-text-3)">{{ $program->name_ru }}</div>
                            @endif
                        </td>
                        <td style="font-size:13px;color:var(--c-text-2)">
                            {{ $program->faculty->short_name ?? $program->faculty->name ?? '—' }}
                        </td>
                        <td class="text-center">
                            @php
                                $levelColors = [
                                    'bakalavriat'  => ['rgba(16,185,129,.12)',  'var(--c-emerald)'],
                                    'magistratura' => ['rgba(124,58,237,.12)', 'var(--c-violet)'],
                                    'doktorantura' => ['rgba(6,182,212,.12)',   'var(--c-cyan)'],
                                ];
                                [$lbg, $lc] = $levelColors[strtolower($program->level)] ?? ['rgba(148,163,184,.12)', 'var(--c-text-3)'];
                            @endphp
                            <span class="badge" style="background:{{ $lbg }};color:{{ $lc }};font-size:11px">
                                {{ ucfirst($program->level) }}
                            </span>
                        </td>
                        <td class="text-center">
                            @php
                                $formColors = [
                                    'kunduzgi' => ['rgba(14,165,233,.12)',  'var(--c-sky)'],
                                    'kechki'   => ['rgba(245,158,11,.12)',  'var(--c-amber)'],
                                    'sirtqi'   => ['rgba(20,184,166,.12)',  'var(--c-teal)'],
                                ];
                                [$fbg, $fc] = $formColors[strtolower($program->education_form)] ?? ['rgba(148,163,184,.12)', 'var(--c-text-3)'];
                            @endphp
                            <span class="badge" style="background:{{ $fbg }};color:{{ $fc }};font-size:11px">
                                {{ ucfirst($program->education_form) }}
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="badge" style="background:rgba(20,184,166,.12);color:var(--c-teal);font-size:11px">
                                {{ $program->duration_years }} yil
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="badge" style="background:rgba(245,158,11,.12);color:var(--c-amber);font-size:11px">
                                {{ $program->total_credits }} kr
                            </span>
                        </td>
                        <td class="text-center">
                            @if($program->active)
                                <span class="badge" style="background:rgba(16,185,129,.12);color:var(--c-emerald)">Faol</span>
                            @else
                                <span class="badge" style="background:rgba(244,63,94,.12);color:var(--c-rose)">Nofaol</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex justify-content-center gap-1">
                                <a href="{{ route('structure.academic.programs.show', $program) }}" class="action-btn" title="Ko'rish" style="color:var(--c-sky)"><i class="fas fa-eye"></i></a>
                                <a href="{{ route('structure.academic.programs.curriculum', $program) }}" class="action-btn" title="O'quv reja" style="color:var(--c-emerald)"><i class="fas fa-book"></i></a>
                                <a href="{{ route('structure.academic.programs.edit', $program) }}" class="action-btn" title="Tahrirlash" style="color:var(--c-amber)"><i class="fas fa-edit"></i></a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9">
                            <div class="empty-state py-5">
                                <div class="empty-state-icon"><i class="fas fa-graduation-cap"></i></div>
                                <div class="empty-state-sub">Ta'lim yo'nalishlari topilmadi</div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($programs->hasPages())
        <div class="px-4 py-3" style="border-top:1px solid var(--c-border)">
            {{ $programs->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>

{{-- Quick actions --}}
<div class="mt-3 d-flex align-items-center gap-2">
    <span style="font-size:12px;font-weight:600;color:var(--c-text-3)">Tezkor havolalar:</span>
    <a href="{{ route('structure.academic.curriculum.index') }}"
       class="btn btn-sm btn-outline-secondary" style="font-size:12px">
        <i class="fas fa-book-open me-1" style="color:var(--c-emerald)"></i>O'quv rejalar
    </a>
    <a href="{{ route('structure.academic.subjects.index') }}"
       class="btn btn-sm btn-outline-secondary" style="font-size:12px">
        <i class="fas fa-list-alt me-1" style="color:var(--c-violet)"></i>Fanlar ro'yxati
    </a>
</div>

@endsection
