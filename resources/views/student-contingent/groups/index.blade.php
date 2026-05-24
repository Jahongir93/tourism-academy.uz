@extends('layouts.dashboard-new')

@section('title', 'Talabalar guruhlari')
@section('page-title', 'Talabalar guruhlari')

@section('content')

{{-- ── Stat Cards ── --}}
<div class="row g-4 mb-4">
    <div class="col-6 col-xl-3">
        <div class="stat-card" style="color:var(--c-sky)">
            <div class="stat-card-icon" style="background:rgba(14,165,233,.12);color:var(--c-sky)">
                <i class="fas fa-layer-group"></i>
            </div>
            <div class="stat-card-value" style="color:var(--c-text)">{{ $statistics['total_groups'] }}</div>
            <div class="stat-card-label">Jami guruhlar</div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card" style="color:var(--c-emerald)">
            <div class="stat-card-icon" style="background:rgba(16,185,129,.12);color:var(--c-emerald)">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-card-value" style="color:var(--c-text)">{{ $statistics['active_groups'] }}</div>
            <div class="stat-card-label">Faol guruhlar</div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card" style="color:var(--c-violet)">
            <div class="stat-card-icon" style="background:rgba(124,58,237,.12);color:var(--c-violet)">
                <i class="fas fa-user-graduate"></i>
            </div>
            <div class="stat-card-value" style="color:var(--c-text)">{{ number_format($statistics['total_students']) }}</div>
            <div class="stat-card-label">Jami talabalar</div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card" style="color:var(--c-amber)">
            <div class="stat-card-icon" style="background:rgba(245,158,11,.12);color:var(--c-amber)">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-card-value" style="color:var(--c-text)">{{ number_format($statistics['max_capacity']) }}</div>
            <div class="stat-card-label">Umumiy sig'im</div>
        </div>
    </div>
</div>

{{-- ── Filters ── --}}
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('student-contingent.groups.index') }}">
            <div class="row g-2 align-items-end">
                <div class="col-12 col-md-3">
                    <div class="position-relative">
                        <i class="fas fa-search position-absolute" style="left:12px;top:50%;transform:translateY(-50%);color:var(--c-text-3);font-size:13px"></i>
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Guruh nomi..." class="form-control" style="padding-left:34px!important">
                    </div>
                </div>
                <div class="col-6 col-md-2">
                    <select name="faculty_id" class="form-select">
                        <option value="">Barcha fakultetlar</option>
                        @foreach($faculties as $faculty)
                            <option value="{{ $faculty->id }}" {{ request('faculty_id') == $faculty->id ? 'selected' : '' }}>
                                {{ $faculty->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-1">
                    <select name="course" class="form-select">
                        <option value="">Kurs</option>
                        @for($i = 1; $i <= 6; $i++)
                            <option value="{{ $i }}" {{ request('course') == $i ? 'selected' : '' }}>{{ $i }}-kurs</option>
                        @endfor
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <select name="academic_year" class="form-select">
                        <option value="">O'quv yili</option>
                        <option value="2024-2025" {{ request('academic_year') == '2024-2025' ? 'selected' : '' }}>2024-2025</option>
                        <option value="2023-2024" {{ request('academic_year') == '2023-2024' ? 'selected' : '' }}>2023-2024</option>
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <select name="is_active" class="form-select">
                        <option value="">Barcha holatlar</option>
                        <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>Faol</option>
                        <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>Nofaol</option>
                    </select>
                </div>
                <div class="col-12 col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-fill">
                        <i class="fas fa-search me-1"></i>Qidirish
                    </button>
                    <a href="{{ route('student-contingent.groups.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- ── Table ── --}}
<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-2">
            <i class="fas fa-layer-group" style="color:var(--c-sky)"></i>
            <span>Guruhlar ro'yxati</span>
        </div>
        <a href="{{ route('student-contingent.groups.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus me-1"></i>Yangi guruh
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Guruh nomi</th>
                        <th>Fakultet / Kafedra</th>
                        <th style="width:90px">Kurs</th>
                        <th style="width:200px">Talabalar</th>
                        <th style="width:100px">Holat</th>
                        <th style="width:60px"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($groups as $group)
                    <tr>
                        <td>
                            <a href="{{ route('student-contingent.groups.show', $group) }}"
                               class="text-decoration-none">
                                <div style="font-weight:600;color:var(--c-text)">{{ $group->name }}</div>
                                @if($group->code)
                                <div style="font-size:11px;color:var(--c-sky)">{{ $group->code }}</div>
                                @endif
                            </a>
                        </td>
                        <td>
                            <div style="font-size:13px;color:var(--c-text)">{{ $group->department->faculty->name ?? '—' }}</div>
                            <div style="font-size:11px;color:var(--c-text-3)">{{ $group->department->name ?? '' }}</div>
                        </td>
                        <td>
                            <span class="badge" style="background:rgba(14,165,233,.1);color:var(--c-sky)">
                                {{ $group->course }}-kurs
                            </span>
                            @if($group->education_type)
                            <div style="font-size:11px;color:var(--c-text-3);margin-top:2px">{{ $group->education_type }}</div>
                            @endif
                        </td>
                        <td>
                            @php
                                $currentStudents = $group->students_count ?? $group->students->count();
                                $maxStudents = 30;
                                $pct = $maxStudents > 0 ? min(($currentStudents / $maxStudents) * 100, 100) : 0;
                                $barColor = $pct >= 90 ? 'var(--c-rose)' : ($pct >= 70 ? 'var(--c-amber)' : 'var(--c-sky)');
                            @endphp
                            <div class="d-flex align-items-center gap-2">
                                <div style="flex:1;height:6px;border-radius:999px;background:var(--c-border)">
                                    <div style="width:{{ $pct }}%;height:100%;border-radius:999px;background:{{ $barColor }};transition:width .3s"></div>
                                </div>
                                <span style="font-size:12px;font-weight:600;color:var(--c-text);white-space:nowrap">
                                    {{ $currentStudents }} / {{ $maxStudents }}
                                </span>
                            </div>
                        </td>
                        <td>
                            <span class="badge" style="background:rgba(16,185,129,.1);color:var(--c-emerald)">Faol</span>
                        </td>
                        <td>
                            <div class="position-relative" x-data="{ open: false }">
                                <button @click="open = !open" type="button"
                                        class="action-btn" style="color:var(--c-text-2)">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <div x-show="open" @click.away="open = false" x-transition
                                     class="position-absolute end-0 mt-1 rounded-3 bg-white shadow-lg z-3"
                                     style="min-width:180px;border:1px solid var(--c-border);top:100%">
                                    <a href="{{ route('student-contingent.groups.show', $group) }}"
                                       class="dropdown-action-item">
                                        <i class="fas fa-eye fa-sm" style="color:var(--c-sky)"></i> Ko'rish
                                    </a>
                                    <a href="{{ route('student-contingent.groups.edit', $group) }}"
                                       class="dropdown-action-item">
                                        <i class="fas fa-edit fa-sm" style="color:var(--c-amber)"></i> Tahrirlash
                                    </a>
                                    @if(Route::has('student-contingent.groups.export'))
                                    <a href="{{ route('student-contingent.groups.export', $group) }}"
                                       class="dropdown-action-item">
                                        <i class="fas fa-download fa-sm" style="color:var(--c-emerald)"></i> Export
                                    </a>
                                    @endif
                                    <div style="height:1px;background:var(--c-border);margin:4px 0"></div>
                                    <form action="{{ route('student-contingent.groups.destroy', $group) }}" method="POST">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="dropdown-action-item w-100 text-start"
                                                style="color:var(--c-rose)"
                                                onclick="return confirm('Guruhni o\'chirishni xohlaysizmi?')">
                                            <i class="fas fa-trash fa-sm"></i> O'chirish
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6">
                            <div class="empty-state py-5">
                                <div class="empty-state-icon"><i class="fas fa-layer-group"></i></div>
                                <div class="empty-state-sub">Guruhlar topilmadi</div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($groups->hasPages())
        <div class="px-4 py-3" style="border-top:1px solid var(--c-border)">
            {{ $groups->links() }}
        </div>
        @endif
    </div>
</div>

<style>
.action-btn {
    display:inline-flex;align-items:center;justify-content:center;
    width:30px;height:30px;border-radius:7px;border:none;background:transparent;
    cursor:pointer;transition:all .15s;font-size:13px;padding:0;
}
.action-btn:hover { background:var(--c-bg); }
.dropdown-action-item {
    display:flex;align-items:center;gap:8px;padding:9px 14px;
    font-size:13px;color:var(--c-text-2);text-decoration:none;
    transition:all .15s;background:transparent;border:none;cursor:pointer;
}
.dropdown-action-item:hover { background:var(--c-bg);color:var(--c-text); }
</style>

@endsection
