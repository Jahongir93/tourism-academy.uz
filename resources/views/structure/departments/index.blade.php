@extends('layouts.dashboard-new')

@section('title', 'Kafedralar — Tuzilma — HEMIS')
@section('page-title', 'Kafedralar boshqaruvi')

@section('styles')
<style>
.action-btn { display:inline-flex;align-items:center;justify-content:center;width:30px;height:30px;border-radius:7px;border:none;background:transparent;cursor:pointer;transition:all .15s;font-size:13px;padding:0;text-decoration:none; }
.action-btn:hover { background:var(--c-bg); }
</style>
@endsection

@section('content')

{{-- Filter --}}
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('structure.departments.index') }}">
            <div class="row g-2 align-items-end">
                <div class="col-12 col-md-3">
                    <select name="faculty_id" class="form-select">
                        <option value="">Barcha fakultetlar</option>
                        @foreach($faculties as $faculty)
                            <option value="{{ $faculty->id }}" {{ request('faculty_id') == $faculty->id ? 'selected' : '' }}>
                                {{ $faculty->name_uz }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-2">
                    <select name="type" class="form-select">
                        <option value="">Barcha turlar</option>
                        <option value="umumkasbiy"  {{ request('type') == 'umumkasbiy'  ? 'selected' : '' }}>Umumkasbiy</option>
                        <option value="ixtisoslik"  {{ request('type') == 'ixtisoslik'  ? 'selected' : '' }}>Ixtisoslik</option>
                        <option value="umumtalim"   {{ request('type') == 'umumtalim'   ? 'selected' : '' }}>Umumta'lim</option>
                    </select>
                </div>
                <div class="col-12 col-md-4">
                    <div class="position-relative">
                        <i class="fas fa-search position-absolute" style="left:12px;top:50%;transform:translateY(-50%);color:var(--c-text-3);font-size:13px"></i>
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Kafedra nomi yoki kodi..."
                               class="form-control" style="padding-left:34px!important">
                    </div>
                </div>
                <div class="col-12 col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-fill">
                        <i class="fas fa-search me-1"></i>Qidirish
                    </button>
                    <a href="{{ route('structure.departments.index') }}" class="btn btn-outline-secondary">
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
            <i class="fas fa-building" style="color:var(--c-teal)"></i>
            <span>Kafedralar ro'yxati</span>
            @if($departments->total())
            <span class="badge" style="background:rgba(20,184,166,.12);color:var(--c-teal);font-size:11px">
                {{ $departments->total() }} ta
            </span>
            @endif
        </div>
        <a href="{{ route('structure.departments.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus me-1"></i>Yangi kafedra
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th style="width:80px">Kod</th>
                        <th>Nomi</th>
                        <th>Fakultet</th>
                        <th style="width:120px">Turi</th>
                        <th>Mudiri</th>
                        <th style="width:80px">Xona</th>
                        <th style="width:80px">Holat</th>
                        <th style="width:100px" class="text-center">Amal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($departments as $department)
                    <tr>
                        <td>
                            <span class="badge" style="background:rgba(16,185,129,.12);color:var(--c-emerald);font-weight:600">
                                {{ $department->code }}
                            </span>
                        </td>
                        <td>
                            <div style="font-weight:600;color:var(--c-text)">{{ $department->name_uz }}</div>
                            @if($department->name_ru)
                                <div style="font-size:11px;color:var(--c-text-3)">{{ $department->name_ru }}</div>
                            @endif
                        </td>
                        <td style="font-size:13px;color:var(--c-text-2)">
                            {{ optional($department->faculty)->name_uz ?? '—' }}
                        </td>
                        <td>
                            @if($department->type)
                                @php
                                    $typeLabel = match($department->type) {
                                        'umumkasbiy' => 'Umumkasbiy',
                                        'ixtisoslik' => 'Ixtisoslik',
                                        'umumtalim'  => "Umumta'lim",
                                        default      => $department->type,
                                    };
                                @endphp
                                <span class="badge" style="background:rgba(20,184,166,.12);color:var(--c-teal);font-size:11px">{{ $typeLabel }}</span>
                            @else
                                <span style="color:var(--c-text-3)">—</span>
                            @endif
                        </td>
                        <td style="font-size:13px;color:var(--c-text-2)">
                            {{ optional($department->head)->name ?? '—' }}
                        </td>
                        <td style="font-size:13px;color:var(--c-text-2)">{{ $department->room_number ?? '—' }}</td>
                        <td>
                            @if($department->is_active)
                                <span class="badge" style="background:rgba(16,185,129,.12);color:var(--c-emerald)">Faol</span>
                            @else
                                <span class="badge" style="background:rgba(244,63,94,.12);color:var(--c-rose)">Nofaol</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex justify-content-center gap-1">
                                <a href="{{ route('structure.departments.show', $department) }}" class="action-btn" title="Ko'rish" style="color:var(--c-sky)"><i class="fas fa-eye"></i></a>
                                <a href="{{ route('structure.departments.edit', $department) }}" class="action-btn" title="Tahrirlash" style="color:var(--c-amber)"><i class="fas fa-edit"></i></a>
                                <a href="{{ route('structure.departments.staffing', $department) }}" class="action-btn" title="Shtatlar" style="color:var(--c-emerald)"><i class="fas fa-users"></i></a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8">
                            <div class="empty-state py-5">
                                <div class="empty-state-icon"><i class="fas fa-building"></i></div>
                                <div class="empty-state-sub">Kafedralar topilmadi</div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($departments->hasPages())
        <div class="px-4 py-3" style="border-top:1px solid var(--c-border)">
            {{ $departments->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>

@endsection
