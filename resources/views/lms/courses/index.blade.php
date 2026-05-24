@extends('layouts.dashboard-new')

@section('title', 'Kurslar — LMS')
@section('page-title', 'Online kurslar')

@section('styles')
<style>
.lms-card {
    background:var(--c-surface);border:1px solid var(--c-border);border-radius:12px;
    overflow:hidden;transition:all .2s;
}
.lms-card:hover { transform:translateY(-3px);box-shadow:0 6px 20px rgba(0,0,0,.09);border-color:var(--c-teal); }
.lms-thumb {
    height:130px;overflow:hidden;display:flex;align-items:center;justify-content:center;
    background:linear-gradient(135deg,var(--c-teal),var(--c-emerald));position:relative;
}
.lms-thumb img { width:100%;height:100%;object-fit:cover; }
.lms-badge-overlay {
    position:absolute;padding:3px 8px;border-radius:5px;font-size:11px;font-weight:700;
    background:rgba(0,0,0,.55);backdrop-filter:blur(4px);color:#fff;
}
</style>
@endsection

@section('content')

{{-- Stats --}}
<div class="row g-4 mb-4">
    <div class="col-6 col-xl-3">
        <div class="stat-card" style="color:var(--c-teal)">
            <div class="stat-card-icon" style="background:rgba(20,184,166,.12);color:var(--c-teal)">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <div class="stat-card-value" style="color:var(--c-text)">{{ $courses->total() ?? 0 }}</div>
            <div class="stat-card-label">Jami kurslar</div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card" style="color:var(--c-emerald)">
            <div class="stat-card-icon" style="background:rgba(16,185,129,.12);color:var(--c-emerald)">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-card-value" style="color:var(--c-text)">{{ $courses->where('is_published', true)->count() ?? 0 }}</div>
            <div class="stat-card-label">Nashr qilingan</div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card" style="color:var(--c-sky)">
            <div class="stat-card-icon" style="background:rgba(14,165,233,.12);color:var(--c-sky)">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-card-value" style="color:var(--c-text)">{{ $courses->sum('enrollment_count') ?? 0 }}</div>
            <div class="stat-card-label">Jami talabalar</div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card" style="color:var(--c-amber)">
            <div class="stat-card-icon" style="background:rgba(245,158,11,.12);color:var(--c-amber)">
                <i class="fas fa-edit"></i>
            </div>
            <div class="stat-card-value" style="color:var(--c-text)">{{ $courses->where('is_published', false)->count() ?? 0 }}</div>
            <div class="stat-card-label">Qoralamalar</div>
        </div>
    </div>
</div>

{{-- Filter --}}
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('lms.courses.index') }}">
            <div class="row g-2 align-items-end">
                <div class="col-12 col-md-4">
                    <label class="form-label mb-1" style="font-size:12px;font-weight:600;color:var(--c-text-2)">Qidirish</label>
                    <div class="position-relative">
                        <i class="fas fa-search position-absolute" style="left:12px;top:50%;transform:translateY(-50%);color:var(--c-text-3);font-size:13px"></i>
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Kurs nomi..." class="form-control" style="padding-left:34px!important">
                    </div>
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label mb-1" style="font-size:12px;font-weight:600;color:var(--c-text-2)">Fan</label>
                    <select name="subject_id" class="form-select">
                        <option value="">Barcha fanlar</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>{{ $subject->name_uz }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label mb-1" style="font-size:12px;font-weight:600;color:var(--c-text-2)">Daraja</label>
                    <select name="level" class="form-select">
                        <option value="">Barchasi</option>
                        <option value="beginner"     {{ request('level') == 'beginner'     ? 'selected' : '' }}>Boshlang'ich</option>
                        <option value="intermediate" {{ request('level') == 'intermediate' ? 'selected' : '' }}>O'rta</option>
                        <option value="advanced"     {{ request('level') == 'advanced'     ? 'selected' : '' }}>Yuqori</option>
                    </select>
                </div>
                <div class="col-6 col-md-1">
                    <label class="form-label mb-1" style="font-size:12px;font-weight:600;color:var(--c-text-2)">Holat</label>
                    <select name="status" class="form-select">
                        <option value="">Barchasi</option>
                        <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Nashr</option>
                        <option value="draft"     {{ request('status') == 'draft'     ? 'selected' : '' }}>Qoralama</option>
                    </select>
                </div>
                <div class="col-12 col-md-2 d-flex gap-2 align-self-end">
                    <button type="submit" class="btn btn-primary flex-fill"><i class="fas fa-search me-1"></i>Qidirish</button>
                    <a href="{{ route('lms.courses.index') }}" class="btn btn-outline-secondary"><i class="fas fa-times"></i></a>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Courses grid --}}
<div class="d-flex align-items-center justify-content-between mb-3">
    <div class="d-flex align-items-center gap-2">
        <i class="fas fa-graduation-cap" style="color:var(--c-teal)"></i>
        <span style="font-weight:700;color:var(--c-text)">Kurslar ro'yxati</span>
        @if($courses->total())
        <span class="badge" style="background:rgba(20,184,166,.12);color:var(--c-teal);font-size:11px">{{ $courses->total() }} ta</span>
        @endif
    </div>
    @if(Auth::check() && (Auth::user()->hasRole('Teacher') || Auth::user()->hasRole('SuperAdmin') || Auth::user()->hasRole('admin')))
    <a href="{{ route('lms.courses.create') }}" class="btn btn-primary btn-sm">
        <i class="fas fa-plus me-1"></i>Yangi kurs
    </a>
    @endif
</div>

<div class="row g-4">
    @forelse($courses as $course)
    <div class="col-12 col-sm-6 col-lg-4 col-xl-3">
        <div class="lms-card">
            {{-- Thumbnail --}}
            <div class="lms-thumb">
                @if($course->thumbnail)
                    <img src="{{ asset('storage/' . $course->thumbnail) }}" alt="{{ $course->title }}"
                         onerror="this.style.display='none'">
                @else
                    <i class="fas fa-graduation-cap text-white" style="font-size:40px;opacity:.6"></i>
                @endif
                {{-- Status --}}
                <div class="lms-badge-overlay" style="bottom:8px;right:8px">
                    @if($course->is_published)
                        <i class="fas fa-check-circle me-1" style="color:#4ade80"></i>Nashr
                    @else
                        <i class="fas fa-edit me-1" style="color:#fbbf24"></i>Qoralama
                    @endif
                </div>
                {{-- Level --}}
                <div class="lms-badge-overlay" style="bottom:8px;left:8px">
                    @if($course->level == 'beginner')
                        <i class="fas fa-seedling me-1"></i>Boshlang'ich
                    @elseif($course->level == 'intermediate')
                        <i class="fas fa-chart-line me-1"></i>O'rta
                    @elseif($course->level == 'advanced')
                        <i class="fas fa-crown me-1"></i>Yuqori
                    @endif
                </div>
                {{-- Action buttons overlay --}}
                <div style="position:absolute;top:8px;right:8px;display:flex;gap:4px">
                    <a href="{{ route('lms.courses.show', $course) }}"
                       class="btn btn-sm" style="background:rgba(20,184,166,.9);color:#fff;padding:3px 8px;font-size:11px">
                        <i class="fas fa-eye"></i>
                    </a>
                    @if(Auth::check() && (Auth::user()->hasRole('admin') || Auth::user()->hasRole('SuperAdmin') || Auth::user()->id == $course->teacher_id))
                    <a href="{{ route('lms.courses.edit', $course) }}"
                       class="btn btn-sm" style="background:rgba(245,158,11,.9);color:#fff;padding:3px 8px;font-size:11px">
                        <i class="fas fa-edit"></i>
                    </a>
                    @endif
                </div>
            </div>
            {{-- Info --}}
            <div class="p-3">
                <div style="font-weight:700;font-size:13px;color:var(--c-text);margin-bottom:4px;line-height:1.3;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;min-height:2.6em">
                    {{ $course->title }}
                </div>
                <div style="font-size:12px;color:var(--c-text-3);margin-bottom:8px">
                    <i class="fas fa-user-tie me-1" style="color:var(--c-teal)"></i>{{ $course->teacher?->name ?? "O'qituvchi" }}
                </div>
                @if($course->subject)
                <span class="badge mb-2" style="background:rgba(14,165,233,.12);color:var(--c-sky);font-size:10px">
                    <i class="fas fa-book me-1"></i>{{ $course->subject->name_uz }}
                </span>
                @endif
                <div class="d-flex align-items-center justify-content-between" style="border-top:1px solid var(--c-border);padding-top:8px;margin-top:4px">
                    <span style="font-size:11px;color:var(--c-text-3)">
                        <i class="fas fa-users me-1" style="color:var(--c-teal)"></i>{{ $course->enrollment_count ?? 0 }}
                    </span>
                    <span style="font-size:11px;color:var(--c-text-3)">
                        <i class="fas fa-eye me-1" style="color:var(--c-sky)"></i>{{ $course->view_count ?? 0 }}
                    </span>
                    <span style="font-size:11px;color:var(--c-text-3)">
                        <i class="fas fa-clock me-1" style="color:var(--c-amber)"></i>{{ $course->duration_weeks ?? 0 }} hafta
                    </span>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="empty-state py-5">
                    <div class="empty-state-icon"><i class="fas fa-graduation-cap"></i></div>
                    <div class="empty-state-sub">Kurslar topilmadi</div>
                    @if(Auth::check() && (Auth::user()->hasRole('Teacher') || Auth::user()->hasRole('SuperAdmin') || Auth::user()->hasRole('admin')))
                    <div class="mt-3">
                        <a href="{{ route('lms.courses.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus me-1"></i>Birinchi kursni yaratish
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endforelse
</div>

@if($courses->hasPages())
<div class="mt-4">
    <div class="card">
        <div class="card-body py-3 d-flex align-items-center justify-content-between">
            <span style="font-size:13px;color:var(--c-text-2)">
                <i class="fas fa-graduation-cap me-1" style="color:var(--c-teal)"></i>
                {{ $courses->firstItem() }}–{{ $courses->lastItem() }} / {{ $courses->total() }} ta kurs
            </span>
            {{ $courses->withQueryString()->links() }}
        </div>
    </div>
</div>
@endif

@endsection
