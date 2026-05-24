@extends('layouts.dashboard-new')

@section('title', "Fanlar katalogi — O'quv jarayoni — HEMIS")
@section('page-title', "Fanlar ro'yxati")

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
                <i class="fas fa-book"></i>
            </div>
            <div class="stat-card-value" style="color:var(--c-text)">{{ $statistics['total_subjects'] ?? 0 }}</div>
            <div class="stat-card-label">Jami fanlar</div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card" style="color:var(--c-sky)">
            <div class="stat-card-icon" style="background:rgba(14,165,233,.12);color:var(--c-sky)">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-card-value" style="color:var(--c-text)">{{ $statistics['majburiy'] ?? 0 }}</div>
            <div class="stat-card-label">Majburiy</div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card" style="color:var(--c-violet)">
            <div class="stat-card-icon" style="background:rgba(124,58,237,.12);color:var(--c-violet)">
                <i class="fas fa-list"></i>
            </div>
            <div class="stat-card-value" style="color:var(--c-text)">{{ $statistics['tanlov'] ?? 0 }}</div>
            <div class="stat-card-label">Tanlov</div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card" style="color:var(--c-amber)">
            <div class="stat-card-icon" style="background:rgba(245,158,11,.12);color:var(--c-amber)">
                <i class="fas fa-toggle-on"></i>
            </div>
            <div class="stat-card-value" style="color:var(--c-text)">{{ $statistics['active'] ?? 0 }}</div>
            <div class="stat-card-label">Faol fanlar</div>
        </div>
    </div>
</div>

{{-- Filter --}}
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('structure.academic.subjects.index') }}">
            <div class="row g-2 align-items-end">
                <div class="col-12 col-md-3">
                    <label class="form-label mb-1" style="font-size:12px;font-weight:600;color:var(--c-text-2)">Kafedra</label>
                    <select name="department_id" class="form-select">
                        <option value="">Barcha kafedralar</option>
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}" {{ request('department_id') == $department->id ? 'selected' : '' }}>
                                {{ $department->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-2">
                    <label class="form-label mb-1" style="font-size:12px;font-weight:600;color:var(--c-text-2)">Fan turi</label>
                    <select name="subject_type" class="form-select">
                        <option value="">Barcha turlar</option>
                        <option value="majburiy"      {{ request('subject_type') == 'majburiy'      ? 'selected' : '' }}>Majburiy</option>
                        <option value="tanlov"        {{ request('subject_type') == 'tanlov'        ? 'selected' : '' }}>Tanlov</option>
                        <option value="umumkasbiy"    {{ request('subject_type') == 'umumkasbiy'    ? 'selected' : '' }}>Umumkasbiy</option>
                        <option value="mutaxassislik" {{ request('subject_type') == 'mutaxassislik' ? 'selected' : '' }}>Mutaxassislik</option>
                    </select>
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label mb-1" style="font-size:12px;font-weight:600;color:var(--c-text-2)">Qidirish</label>
                    <div class="position-relative">
                        <i class="fas fa-search position-absolute" style="left:12px;top:50%;transform:translateY(-50%);color:var(--c-text-3);font-size:13px"></i>
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Fan kodi yoki nomi..." class="form-control" style="padding-left:34px!important">
                    </div>
                </div>
                <div class="col-12 col-md-3 d-flex gap-2 align-self-end">
                    <button type="submit" class="btn btn-primary flex-fill">
                        <i class="fas fa-search me-1"></i>Qidirish
                    </button>
                    <a href="{{ route('structure.academic.subjects.index') }}" class="btn btn-outline-secondary">
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
            <i class="fas fa-book-open" style="color:var(--c-emerald)"></i>
            <span>Fanlar katalogi</span>
            @if($subjects->total())
            <span class="badge" style="background:rgba(16,185,129,.12);color:var(--c-emerald);font-size:11px">
                {{ $subjects->total() }} ta
            </span>
            @endif
        </div>
        <a href="{{ route('structure.academic.subjects.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus me-1"></i>Yangi fan
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th style="width:90px">Kod</th>
                        <th>Fan nomi</th>
                        <th>Kafedra</th>
                        <th style="width:120px">Turi</th>
                        <th style="width:90px" class="text-center">Kreditlar</th>
                        <th style="width:90px" class="text-center">Soatlar</th>
                        <th style="width:90px" class="text-center">Bog'liqlik</th>
                        <th style="width:80px" class="text-center">Status</th>
                        <th style="width:110px" class="text-center">Amal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($subjects as $subject)
                    <tr>
                        <td>
                            <a href="{{ route('structure.academic.subjects.show', $subject) }}"
                               class="text-decoration-none fw-semibold" style="color:var(--c-emerald)">
                                {{ $subject->code }}
                            </a>
                        </td>
                        <td>
                            <div style="font-weight:600;color:var(--c-text)">{{ $subject->name_uz }}</div>
                            @if($subject->name_ru)
                                <div style="font-size:11px;color:var(--c-text-3)">{{ $subject->name_ru }}</div>
                            @endif
                        </td>
                        <td style="font-size:13px;color:var(--c-text-2)">{{ $subject->department->name ?? '—' }}</td>
                        <td>
                            @php
                                $typeColors = [
                                    'majburiy'       => ['rgba(16,185,129,.12)',  'var(--c-emerald)'],
                                    'tanlov'         => ['rgba(14,165,233,.12)',  'var(--c-sky)'],
                                    'umumkasbiy'     => ['rgba(20,184,166,.12)',  'var(--c-teal)'],
                                    'mutaxassislik'  => ['rgba(124,58,237,.12)', 'var(--c-violet)'],
                                ];
                                [$tbg, $tc] = $typeColors[$subject->subject_type] ?? ['rgba(148,163,184,.12)', 'var(--c-text-3)'];
                            @endphp
                            <span class="badge" style="background:{{ $tbg }};color:{{ $tc }};font-size:11px">
                                {{ $subject->subject_type_text ?? ucfirst($subject->subject_type) }}
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="badge" style="background:rgba(245,158,11,.12);color:var(--c-amber);font-size:11px">
                                {{ $subject->credits }} kr
                            </span>
                        </td>
                        <td class="text-center" style="font-size:13px;color:var(--c-text-2)">
                            {{ $subject->total_hours }}
                        </td>
                        <td class="text-center">
                            @if($subject->prerequisiteSubjects && $subject->prerequisiteSubjects->count() > 0)
                                <span class="badge" style="background:rgba(245,158,11,.12);color:var(--c-amber);font-size:11px">
                                    <i class="fas fa-link fa-xs"></i> {{ $subject->prerequisiteSubjects->count() }}
                                </span>
                            @else
                                <span style="color:var(--c-text-3)">—</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($subject->active)
                                <span class="badge" style="background:rgba(16,185,129,.12);color:var(--c-emerald)">Faol</span>
                            @else
                                <span class="badge" style="background:rgba(244,63,94,.12);color:var(--c-rose)">Nofaol</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex justify-content-center gap-1">
                                <a href="{{ route('structure.academic.subjects.show', $subject) }}" class="action-btn" title="Ko'rish" style="color:var(--c-sky)"><i class="fas fa-eye"></i></a>
                                <a href="{{ route('structure.academic.subjects.prerequisites', $subject) }}" class="action-btn" title="Bog'liqliklar" style="color:var(--c-teal)"><i class="fas fa-link"></i></a>
                                <a href="{{ route('subjects.topics.index', $subject) }}" class="action-btn" title="Mavzular" style="color:var(--c-violet)"><i class="fas fa-list"></i></a>
                                <a href="{{ route('structure.academic.subjects.edit', $subject) }}" class="action-btn" title="Tahrirlash" style="color:var(--c-amber)"><i class="fas fa-edit"></i></a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9">
                            <div class="empty-state py-5">
                                <div class="empty-state-icon"><i class="fas fa-book"></i></div>
                                <div class="empty-state-sub">Fanlar topilmadi</div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($subjects->hasPages())
        <div class="px-4 py-3" style="border-top:1px solid var(--c-border)">
            {{ $subjects->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>

{{-- Quick actions --}}
<div class="mt-3 d-flex align-items-center gap-2">
    <span style="font-size:12px;font-weight:600;color:var(--c-text-3)">Tezkor havolalar:</span>
    <a href="{{ route('structure.academic.hours.index') }}"
       class="btn btn-sm btn-outline-secondary" style="font-size:12px">
        <i class="fas fa-clock me-1" style="color:var(--c-emerald)"></i>Soatlar taqsimoti
    </a>
</div>

@endsection
