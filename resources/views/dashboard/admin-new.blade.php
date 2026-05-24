@extends('layouts.dashboard-new')

@section('title', 'Admin Dashboard — HEMIS')
@section('page-title', 'Bosh sahifa')

@section('content')

{{-- ── Stat Cards ── --}}
<div class="row g-4 mb-4">
    <div class="col-6 col-xl-3">
        <div class="stat-card" style="color:var(--c-primary)">
            <div class="stat-card-icon" style="background:rgba(79,70,229,.1);color:var(--c-primary)">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-card-value" style="color:var(--c-text)">{{ $stats['total_users'] ?? 0 }}</div>
            <div class="stat-card-label">Foydalanuvchilar</div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <a href="{{ route('students.index') }}" class="text-decoration-none">
            <div class="stat-card" style="color:var(--c-sky)">
                <div class="stat-card-icon" style="background:rgba(14,165,233,.1);color:var(--c-sky)">
                    <i class="fas fa-user-graduate"></i>
                </div>
                <div class="stat-card-value" style="color:var(--c-text)">{{ $stats['total_students'] ?? 0 }}</div>
                <div class="stat-card-label">Talabalar</div>
            </div>
        </a>
    </div>
    <div class="col-6 col-xl-3">
        <a href="{{ route('employees.teachers') }}" class="text-decoration-none">
            <div class="stat-card" style="color:var(--c-violet)">
                <div class="stat-card-icon" style="background:rgba(124,58,237,.1);color:var(--c-violet)">
                    <i class="fas fa-chalkboard-teacher"></i>
                </div>
                <div class="stat-card-value" style="color:var(--c-text)">
                    {{ \App\Models\Employee::where('employee_type','teacher')->count() }}
                </div>
                <div class="stat-card-label">O'qituvchilar</div>
            </div>
        </a>
    </div>
    <div class="col-6 col-xl-3">
        <a href="{{ route('employees.index') }}" class="text-decoration-none">
            <div class="stat-card" style="color:var(--c-emerald)">
                <div class="stat-card-icon" style="background:rgba(16,185,129,.1);color:var(--c-emerald)">
                    <i class="fas fa-id-badge"></i>
                </div>
                <div class="stat-card-value" style="color:var(--c-text)">
                    {{ \App\Models\Employee::count() }}
                </div>
                <div class="stat-card-label">Barcha xodimlar</div>
            </div>
        </a>
    </div>
</div>

{{-- ── Quick actions + Recent activity ── --}}
<div class="row g-4 mb-4">
    {{-- Quick Actions --}}
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="fas fa-bolt" style="color:var(--c-amber)"></i>
                <span>Tezkor amallar</span>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-6">
                        <a href="{{ route('students.create') }}"
                           class="d-flex align-items-center gap-3 p-3 rounded text-decoration-none"
                           style="background:rgba(14,165,233,.07);border:1px solid rgba(14,165,233,.15);transition:all .18s">
                            <div style="width:38px;height:38px;border-radius:8px;background:rgba(14,165,233,.15);display:flex;align-items:center;justify-content:center;color:var(--c-sky);flex-shrink:0">
                                <i class="fas fa-user-plus"></i>
                            </div>
                            <span style="font-size:13px;font-weight:600;color:var(--c-text)">Yangi talaba</span>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('employees.create') }}"
                           class="d-flex align-items-center gap-3 p-3 rounded text-decoration-none"
                           style="background:rgba(16,185,129,.07);border:1px solid rgba(16,185,129,.15);transition:all .18s">
                            <div style="width:38px;height:38px;border-radius:8px;background:rgba(16,185,129,.15);display:flex;align-items:center;justify-content:center;color:var(--c-emerald);flex-shrink:0">
                                <i class="fas fa-user-plus"></i>
                            </div>
                            <span style="font-size:13px;font-weight:600;color:var(--c-text)">Yangi xodim</span>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('structure.faculties.index') }}"
                           class="d-flex align-items-center gap-3 p-3 rounded text-decoration-none"
                           style="background:rgba(124,58,237,.07);border:1px solid rgba(124,58,237,.15);transition:all .18s">
                            <div style="width:38px;height:38px;border-radius:8px;background:rgba(124,58,237,.15);display:flex;align-items:center;justify-content:center;color:var(--c-violet);flex-shrink:0">
                                <i class="fas fa-university"></i>
                            </div>
                            <span style="font-size:13px;font-weight:600;color:var(--c-text)">Fakultetlar</span>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('admin.settings.users.index') }}"
                           class="d-flex align-items-center gap-3 p-3 rounded text-decoration-none"
                           style="background:rgba(100,116,139,.07);border:1px solid rgba(100,116,139,.15);transition:all .18s">
                            <div style="width:38px;height:38px;border-radius:8px;background:rgba(100,116,139,.15);display:flex;align-items:center;justify-content:center;color:var(--c-text-2);flex-shrink:0">
                                <i class="fas fa-users-cog"></i>
                            </div>
                            <span style="font-size:13px;font-weight:600;color:var(--c-text)">Foydalanuvchilar</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Activity --}}
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="fas fa-clock" style="color:var(--c-sky)"></i>
                <span>So'nggi faoliyat</span>
            </div>
            <div class="card-body p-0">
                @if(isset($recent_activities) && count($recent_activities))
                    <ul class="list-group list-group-flush">
                        @foreach($recent_activities as $activity)
                        <li class="list-group-item d-flex align-items-start gap-3 border-0 px-4 py-3"
                            style="border-bottom:1px solid var(--c-border)!important">
                            <div style="width:8px;height:8px;border-radius:50%;background:var(--c-primary);margin-top:6px;flex-shrink:0"></div>
                            <div>
                                <div style="font-size:13px;color:var(--c-text)">{{ $activity->description }}</div>
                                <div style="font-size:11px;color:var(--c-text-3);margin-top:2px">
                                    {{ $activity->created_at->diffForHumans() }}
                                </div>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                @else
                    <div class="empty-state py-4">
                        <div class="empty-state-icon" style="font-size:32px"><i class="fas fa-inbox"></i></div>
                        <div class="empty-state-sub">Faoliyat mavjud emas</div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- ── Stats grids ── --}}
<div class="row g-4">
    {{-- Employee types --}}
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="fas fa-sitemap" style="color:var(--c-violet)"></i>
                <span>Xodimlar statistikasi</span>
            </div>
            <div class="card-body p-0">
                @php
                    $empTypes = [
                        ['label' => "O'qituvchilar",      'icon' => 'fa-chalkboard-teacher', 'color' => 'var(--c-violet)',  'val' => \App\Models\Employee::where('employee_type','teacher')->count()],
                        ['label' => "Ma'muriy xodimlar",  'icon' => 'fa-user-tie',           'color' => 'var(--c-sky)',     'val' => \App\Models\Employee::where('employee_type','admin')->count()],
                        ['label' => "Yordamchi xodimlar", 'icon' => 'fa-hands-helping',      'color' => 'var(--c-emerald)', 'val' => \App\Models\Employee::where('employee_type','support')->count()],
                        ['label' => "Ta'tilda",            'icon' => 'fa-umbrella-beach',     'color' => 'var(--c-amber)',   'val' => \App\Models\Employee::where('status','leave')->count()],
                    ];
                @endphp
                <ul class="list-group list-group-flush">
                    @foreach($empTypes as $i => $et)
                    <li class="list-group-item d-flex align-items-center justify-content-between px-4 py-3 border-0"
                        @if(!$loop->last) style="border-bottom:1px solid var(--c-border)!important" @endif>
                        <div class="d-flex align-items-center gap-3">
                            <div style="width:32px;height:32px;border-radius:8px;background:rgba(0,0,0,.04);display:flex;align-items:center;justify-content:center;color:{{ $et['color'] }}">
                                <i class="fas {{ $et['icon'] }} fa-sm"></i>
                            </div>
                            <span style="font-size:13px;color:var(--c-text-2)">{{ $et['label'] }}</span>
                        </div>
                        <span style="font-size:15px;font-weight:700;color:var(--c-text)">{{ $et['val'] }}</span>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    {{-- Faculty list --}}
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-university" style="color:var(--c-emerald)"></i>
                    <span>Fakultetlar bo'yicha</span>
                </div>
                <a href="{{ route('structure.faculties.index') }}" class="btn btn-sm btn-outline-secondary">
                    Barchasi <i class="fas fa-arrow-right fa-xs"></i>
                </a>
            </div>
            <div class="card-body p-0">
                @if(isset($faculties) && count($faculties))
                    <ul class="list-group list-group-flush">
                        @foreach($faculties->take(5) as $faculty)
                        <li class="list-group-item d-flex align-items-center justify-content-between px-4 py-3 border-0"
                            @if(!$loop->last) style="border-bottom:1px solid var(--c-border)!important" @endif>
                            <span style="font-size:13px;color:var(--c-text-2)">{{ $faculty->name_uz }}</span>
                            <span class="badge" style="background:rgba(16,185,129,.12);color:var(--c-emerald);font-size:11px">
                                {{ $faculty->departments->count() }} kafedra
                            </span>
                        </li>
                        @endforeach
                    </ul>
                @else
                    <div class="empty-state py-4">
                        <div class="empty-state-sub">Ma'lumot mavjud emas</div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection
