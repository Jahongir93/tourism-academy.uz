@extends('layouts.dashboard-new')

@section('title', 'Fakultetlar — Tuzilma — HEMIS')
@section('page-title', 'Fakultetlar boshqaruvi')

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
                <i class="fas fa-university"></i>
            </div>
            <div class="stat-card-value" style="color:var(--c-text)">{{ $statistics['total_faculties'] ?? 0 }}</div>
            <div class="stat-card-label">Fakultetlar</div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card" style="color:var(--c-teal)">
            <div class="stat-card-icon" style="background:rgba(20,184,166,.12);color:var(--c-teal)">
                <i class="fas fa-building"></i>
            </div>
            <div class="stat-card-value" style="color:var(--c-text)">{{ $statistics['total_departments'] ?? 0 }}</div>
            <div class="stat-card-label">Kafedralar</div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card" style="color:var(--c-sky)">
            <div class="stat-card-icon" style="background:rgba(14,165,233,.12);color:var(--c-sky)">
                <i class="fas fa-user-graduate"></i>
            </div>
            <div class="stat-card-value" style="color:var(--c-text)">{{ number_format($statistics['total_students'] ?? 0) }}</div>
            <div class="stat-card-label">Talabalar</div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card" style="color:var(--c-violet)">
            <div class="stat-card-icon" style="background:rgba(124,58,237,.12);color:var(--c-violet)">
                <i class="fas fa-chalkboard-teacher"></i>
            </div>
            <div class="stat-card-value" style="color:var(--c-text)">{{ number_format($statistics['total_teachers'] ?? 0) }}</div>
            <div class="stat-card-label">O'qituvchilar</div>
        </div>
    </div>
</div>

{{-- Filter --}}
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('structure.faculties.index') }}">
            <div class="row g-2 align-items-end">
                <div class="col-12 col-md-9">
                    <div class="position-relative">
                        <i class="fas fa-search position-absolute" style="left:12px;top:50%;transform:translateY(-50%);color:var(--c-text-3);font-size:13px"></i>
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Fakultet nomi, kodi yoki qisqartma bo'yicha..."
                               class="form-control" style="padding-left:34px!important">
                    </div>
                </div>
                <div class="col-12 col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-fill">
                        <i class="fas fa-search me-1"></i>Qidirish
                    </button>
                    <a href="{{ route('structure.faculties.index') }}" class="btn btn-outline-secondary">
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
            <i class="fas fa-university" style="color:var(--c-emerald)"></i>
            <span>Fakultetlar ro'yxati</span>
            @if($faculties->total())
            <span class="badge" style="background:rgba(16,185,129,.12);color:var(--c-emerald);font-size:11px">
                {{ $faculties->total() }} ta
            </span>
            @endif
        </div>
        <a href="{{ route('structure.faculties.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus me-1"></i>Yangi fakultet
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th style="width:80px">Kod</th>
                        <th>Nomi</th>
                        <th>Dekan</th>
                        <th class="text-center" style="width:100px">Kafedralar</th>
                        <th class="text-center" style="width:110px">Yo'nalishlar</th>
                        <th style="width:130px">Telefon</th>
                        <th style="width:80px">Holat</th>
                        <th style="width:120px" class="text-center">Amal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($faculties as $faculty)
                    <tr>
                        <td>
                            <span class="badge" style="background:rgba(16,185,129,.12);color:var(--c-emerald);font-weight:600">
                                {{ $faculty->code }}
                            </span>
                        </td>
                        <td>
                            <div style="font-weight:600;color:var(--c-text)">
                                {{ $faculty->name_uz }}
                                @if($faculty->abbreviation)
                                    <span style="font-size:12px;color:var(--c-primary);font-weight:400">({{ $faculty->abbreviation }})</span>
                                @endif
                            </div>
                            @if($faculty->name_ru)
                                <div style="font-size:11px;color:var(--c-text-3)">{{ $faculty->name_ru }}</div>
                            @endif
                        </td>
                        <td style="color:var(--c-text-2);font-size:13px">
                            {{ $faculty->dean?->name ?? '—' }}
                        </td>
                        <td class="text-center">
                            <span class="badge" style="background:rgba(20,184,166,.12);color:var(--c-teal);font-weight:700;font-size:13px">
                                {{ $faculty->departments->count() }}
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="badge" style="background:rgba(124,58,237,.12);color:var(--c-violet);font-weight:700;font-size:13px">
                                {{ $faculty->specialties->count() }}
                            </span>
                        </td>
                        <td style="font-size:13px;color:var(--c-text-2)">{{ $faculty->phone ?? '—' }}</td>
                        <td>
                            @if($faculty->is_active)
                                <span class="badge" style="background:rgba(16,185,129,.12);color:var(--c-emerald)">Faol</span>
                            @else
                                <span class="badge" style="background:rgba(244,63,94,.12);color:var(--c-rose)">Nofaol</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex justify-content-center gap-1">
                                <a href="{{ route('structure.faculties.show', $faculty) }}" class="action-btn" title="Ko'rish" style="color:var(--c-sky)"><i class="fas fa-eye"></i></a>
                                <a href="{{ route('structure.faculties.edit', $faculty) }}" class="action-btn" title="Tahrirlash" style="color:var(--c-amber)"><i class="fas fa-edit"></i></a>
                                <a href="{{ route('structure.faculties.departments', $faculty) }}" class="action-btn" title="Kafedralar" style="color:var(--c-teal)"><i class="fas fa-building"></i></a>
                                <a href="{{ route('structure.faculties.staffing', $faculty) }}" class="action-btn" title="Shtatlar" style="color:var(--c-emerald)"><i class="fas fa-users"></i></a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8">
                            <div class="empty-state py-5">
                                <div class="empty-state-icon"><i class="fas fa-university"></i></div>
                                <div class="empty-state-sub">Fakultetlar topilmadi</div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($faculties->hasPages())
        <div class="px-4 py-3" style="border-top:1px solid var(--c-border)">
            {{ $faculties->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>

@endsection
