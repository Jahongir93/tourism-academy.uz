@extends('layouts.dashboard-new')

@section('title', "O'quv materiallari — LMS")
@section('page-title', "O'quv materiallari")

@section('styles')
<style>
.lms-mat-card {
    background:var(--c-surface);border:1px solid var(--c-border);border-radius:12px;
    overflow:hidden;transition:all .2s;display:flex;flex-direction:column;
}
.lms-mat-card:hover { transform:translateY(-2px);box-shadow:0 6px 18px rgba(0,0,0,.08);border-color:var(--c-teal); }
.lms-mat-top { height:4px; }
.lms-file-icon {
    width:52px;height:52px;border-radius:12px;display:flex;align-items:center;
    justify-content:center;font-size:22px;flex-shrink:0;
}
</style>
@endsection

@section('content')

{{-- Stats --}}
<div class="row g-4 mb-4">
    <div class="col-6 col-xl-3">
        <div class="stat-card" style="color:var(--c-teal)">
            <div class="stat-card-icon" style="background:rgba(20,184,166,.12);color:var(--c-teal)">
                <i class="fas fa-book"></i>
            </div>
            <div class="stat-card-value" style="color:var(--c-text)">{{ $materials->total() ?? 0 }}</div>
            <div class="stat-card-label">Jami materiallar</div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card" style="color:var(--c-rose)">
            <div class="stat-card-icon" style="background:rgba(244,63,94,.12);color:var(--c-rose)">
                <i class="fas fa-file-pdf"></i>
            </div>
            <div class="stat-card-value" style="color:var(--c-text)">{{ $materials->where('material_type','pdf')->count() ?? 0 }}</div>
            <div class="stat-card-label">PDF fayllar</div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card" style="color:var(--c-sky)">
            <div class="stat-card-icon" style="background:rgba(14,165,233,.12);color:var(--c-sky)">
                <i class="fas fa-file-word"></i>
            </div>
            <div class="stat-card-value" style="color:var(--c-text)">{{ $materials->where('material_type','document')->count() ?? 0 }}</div>
            <div class="stat-card-label">Hujjatlar</div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card" style="color:var(--c-amber)">
            <div class="stat-card-icon" style="background:rgba(245,158,11,.12);color:var(--c-amber)">
                <i class="fas fa-download"></i>
            </div>
            <div class="stat-card-value" style="color:var(--c-text)">{{ number_format($materials->sum('download_count') ?? 0) }}</div>
            <div class="stat-card-label">Yuklab olishlar</div>
        </div>
    </div>
</div>

{{-- Filter --}}
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('lms.materials.index') }}">
            <div class="row g-2 align-items-end">
                <div class="col-12 col-md-4">
                    <label class="form-label mb-1" style="font-size:12px;font-weight:600;color:var(--c-text-2)">Qidirish</label>
                    <div class="position-relative">
                        <i class="fas fa-search position-absolute" style="left:12px;top:50%;transform:translateY(-50%);color:var(--c-text-3);font-size:13px"></i>
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Material nomi..." class="form-control" style="padding-left:34px!important">
                    </div>
                </div>
                <div class="col-12 col-md-2">
                    <label class="form-label mb-1" style="font-size:12px;font-weight:600;color:var(--c-text-2)">Fan</label>
                    <select name="subject_id" class="form-select">
                        <option value="">Barcha fanlar</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>{{ $subject->name_uz }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-2">
                    <label class="form-label mb-1" style="font-size:12px;font-weight:600;color:var(--c-text-2)">O'qituvchi</label>
                    <select name="teacher_id" class="form-select">
                        <option value="">Barchasi</option>
                        @foreach($teachers as $teacher)
                            <option value="{{ $teacher->id }}" {{ request('teacher_id') == $teacher->id ? 'selected' : '' }}>{{ $teacher->full_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-2">
                    <label class="form-label mb-1" style="font-size:12px;font-weight:600;color:var(--c-text-2)">Turi</label>
                    <select name="material_type" class="form-select">
                        <option value="">Barcha turlar</option>
                        <option value="presentation" {{ request('material_type') == 'presentation' ? 'selected' : '' }}>Prezentatsiya</option>
                        <option value="document"     {{ request('material_type') == 'document'     ? 'selected' : '' }}>Hujjat</option>
                        <option value="spreadsheet"  {{ request('material_type') == 'spreadsheet'  ? 'selected' : '' }}>Jadval</option>
                        <option value="pdf"          {{ request('material_type') == 'pdf'          ? 'selected' : '' }}>PDF</option>
                        <option value="other"        {{ request('material_type') == 'other'        ? 'selected' : '' }}>Boshqa</option>
                    </select>
                </div>
                <div class="col-12 col-md-2 d-flex gap-2 align-self-end">
                    <button type="submit" class="btn btn-primary flex-fill"><i class="fas fa-search me-1"></i>Qidirish</button>
                    <a href="{{ route('lms.materials.index') }}" class="btn btn-outline-secondary"><i class="fas fa-times"></i></a>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Header --}}
<div class="d-flex align-items-center justify-content-between mb-3">
    <div class="d-flex align-items-center gap-2">
        <i class="fas fa-book" style="color:var(--c-teal)"></i>
        <span style="font-weight:700;color:var(--c-text)">Materiallar katalogi</span>
        @if($materials->total())
        <span class="badge" style="background:rgba(20,184,166,.12);color:var(--c-teal);font-size:11px">{{ $materials->total() }} ta</span>
        @endif
    </div>
    @if(Auth::user()->hasRole('Teacher') || Auth::user()->hasRole('SuperAdmin') || Auth::user()->hasRole('admin'))
    <a href="{{ route('lms.materials.create') }}" class="btn btn-primary btn-sm">
        <i class="fas fa-plus me-1"></i>Yangi material
    </a>
    @endif
</div>

{{-- Cards --}}
<div class="row g-4">
    @forelse($materials as $material)
    @php
        $typeConfig = [
            'presentation' => ['fa-file-powerpoint','rgba(234,88,12,.12)','var(--c-amber)','from-orange-400 to-orange-600','#EA580C'],
            'document'     => ['fa-file-word','rgba(14,165,233,.12)','var(--c-sky)','from-sky-400 to-sky-600','#0EA5E9'],
            'spreadsheet'  => ['fa-file-excel','rgba(16,185,129,.12)','var(--c-emerald)','from-green-400 to-green-600','#10B981'],
            'pdf'          => ['fa-file-pdf','rgba(244,63,94,.12)','var(--c-rose)','from-rose-400 to-rose-600','#F43F5E'],
            'other'        => ['fa-file','rgba(148,163,184,.12)','var(--c-text-3)','from-gray-400 to-gray-500','#94A3B8'],
        ];
        [$icon,$ibg,$ic,$grad,$topColor] = $typeConfig[$material->material_type] ?? $typeConfig['other'];
    @endphp
    <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
        <div class="lms-mat-card">
            <div class="lms-mat-top" style="background:{{ $topColor }}"></div>
            <div class="p-4" style="flex:1;display:flex;flex-direction:column">
                {{-- Icon + week badge --}}
                <div class="d-flex align-items-start justify-content-between mb-3">
                    <div class="lms-file-icon" style="background:{{ $ibg }};color:{{ $ic }}">
                        <i class="fas {{ $icon }}"></i>
                    </div>
                    @if($material->week_number)
                    <span class="badge" style="background:rgba(14,165,233,.12);color:var(--c-sky);font-size:10px">
                        {{ $material->week_number }}-hafta
                    </span>
                    @endif
                </div>
                {{-- Title --}}
                <div style="font-weight:700;font-size:13px;color:var(--c-text);margin-bottom:6px;line-height:1.3;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;min-height:2.6em">
                    {{ $material->title }}
                </div>
                @if($material->description)
                <div style="font-size:12px;color:var(--c-text-3);margin-bottom:8px;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden">
                    {{ $material->description }}
                </div>
                @endif
                {{-- Info --}}
                <div style="background:var(--c-bg);border-radius:8px;padding:10px;margin-bottom:12px;font-size:12px">
                    <div class="d-flex align-items-center mb-1" style="color:var(--c-text-2)">
                        <i class="fas fa-graduation-cap me-2" style="color:var(--c-teal);width:14px"></i>
                        <span class="text-truncate">{{ $material->subject?->name_uz ?? 'Fan nomi' }}</span>
                    </div>
                    <div class="d-flex align-items-center mb-1" style="color:var(--c-text-2)">
                        <i class="fas fa-user-tie me-2" style="color:var(--c-sky);width:14px"></i>
                        <span class="text-truncate">{{ $material->teacher?->full_name ?? "O'qituvchi" }}</span>
                    </div>
                    <div class="d-flex align-items-center justify-content-between" style="border-top:1px solid var(--c-border);padding-top:6px;margin-top:6px;color:var(--c-text-3)">
                        <span><i class="fas fa-hdd me-1"></i>{{ $material->file_size_formatted ?? '0 KB' }}</span>
                        <span><i class="fas fa-download me-1"></i>{{ $material->download_count ?? 0 }}</span>
                        <span><i class="far fa-clock me-1"></i>{{ $material->created_at->diffForHumans() }}</span>
                    </div>
                </div>
                {{-- Actions --}}
                <div class="d-flex gap-2 mt-auto">
                    <a href="{{ route('lms.materials.show', $material) }}" class="btn btn-sm flex-fill"
                       style="background:var(--c-teal);color:#fff;font-size:12px">
                        <i class="fas fa-eye me-1"></i>Ko'rish
                    </a>
                    <a href="{{ route('lms.materials.download', $material) }}" class="btn btn-sm btn-outline-secondary"
                       title="Yuklab olish">
                        <i class="fas fa-download"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="empty-state py-5">
                    <div class="empty-state-icon"><i class="fas fa-book"></i></div>
                    <div class="empty-state-sub">Materiallar topilmadi</div>
                    @if(Auth::user()->hasRole('Teacher') || Auth::user()->hasRole('SuperAdmin') || Auth::user()->hasRole('admin'))
                    <div class="mt-3">
                        <a href="{{ route('lms.materials.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus me-1"></i>Material qo'shish
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endforelse
</div>

@if($materials->hasPages())
<div class="mt-4 px-1 py-3 d-flex align-items-center justify-content-between" style="border-top:1px solid var(--c-border)">
    <span style="font-size:13px;color:var(--c-text-2)">
        Jami <strong>{{ $materials->total() }}</strong> ta material ({{ $materials->firstItem() }}–{{ $materials->lastItem() }})
    </span>
    {{ $materials->withQueryString()->links() }}
</div>
@endif

@endsection
