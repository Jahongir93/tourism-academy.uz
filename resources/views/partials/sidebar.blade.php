{{-- Modern color-coded sidebar using design system tokens --}}
<style>
.sb { width: 100%; min-height: 100%; display: flex; flex-direction: column; background: #0F172A; color: #94A3B8; }
.sb-logo { padding: 20px 16px 18px; border-bottom: 1px solid rgba(255,255,255,.06); flex-shrink: 0; }
.sb-logo-inner { display: flex; align-items: center; gap: 12px; }
.sb-logo-icon { width: 40px; height: 40px; border-radius: 10px; background: linear-gradient(135deg,#4F46E5,#7C3AED); display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: 0 4px 12px rgba(79,70,229,.4); }
.sb-logo-icon svg { width: 22px; height: 22px; color: #fff; }
.sb-logo-title { font-size: 17px; font-weight: 700; color: #fff; line-height: 1.2; }
.sb-logo-sub { font-size: 11px; color: #64748B; margin-top: 1px; }

.sb-nav { flex: 1; padding: 12px 10px 16px; display: flex; flex-direction: column; gap: 2px; }

.sb-section-label { font-size: 10px; font-weight: 600; letter-spacing: .08em; text-transform: uppercase; color: #334155; padding: 12px 12px 4px; }

/* base nav item */
.sb-item { display: flex; align-items: center; justify-content: space-between; width: 100%; padding: 9px 12px; border-radius: 8px; font-size: 13.5px; font-weight: 500; color: #94A3B8; transition: all .18s ease; text-decoration: none; background: transparent; border: none; cursor: pointer; text-align: left; }
.sb-item:hover { background: rgba(255,255,255,.06); color: #E2E8F0; }
.sb-item.active { color: #fff; }

.sb-item-left { display: flex; align-items: center; gap: 10px; }
.sb-item-icon { width: 20px; height: 20px; flex-shrink: 0; display: flex; align-items: center; justify-content: center; font-size: 14px; }
.sb-item-icon svg { width: 18px; height: 18px; }

.sb-chevron { width: 14px; height: 14px; transition: transform .2s ease; flex-shrink: 0; opacity: .5; }
.sb-chevron.open { transform: rotate(180deg); }

/* sub-menu */
.sb-sub { display: none; padding-left: 30px; margin-top: 2px; display: flex; flex-direction: column; gap: 1px; }
.sb-sub.open { display: flex; }
.sb-sub-item { display: flex; align-items: center; gap: 8px; padding: 7px 12px; border-radius: 7px; font-size: 13px; color: #64748B; text-decoration: none; transition: all .15s ease; }
.sb-sub-item:hover { color: #CBD5E1; background: rgba(255,255,255,.05); }
.sb-sub-item.active { color: #fff; }
.sb-sub-item i, .sb-sub-item .fa, .sb-sub-item .fas, .sb-sub-item .fab { width: 14px; text-align: center; font-size: 12px; }

.sb-divider { height: 1px; background: rgba(255,255,255,.05); margin: 6px 12px; }

/* badge */
.sb-badge { display: inline-flex; align-items: center; justify-content: center; min-width: 18px; height: 18px; padding: 0 5px; border-radius: 999px; font-size: 10px; font-weight: 700; color: #fff; background: #EF4444; margin-left: 6px; }
.sb-badge-sm { font-size: 10px; color: #FBBF24; margin-left: 4px; }

/* module color theming — .m-{name} colors icon + active state */
.m-blue  .sb-item.active { background: rgba(14,165,233,.15); color: #38BDF8; }
.m-blue  .sb-item.active .sb-item-icon { color: #0EA5E9; }
.m-blue  .sb-sub-item.active { background: rgba(14,165,233,.12); color: #7DD3FC; }
.m-blue  .sb-item-icon { color: #0EA5E9; }

.m-violet .sb-item.active { background: rgba(124,58,237,.18); color: #A78BFA; }
.m-violet .sb-item.active .sb-item-icon { color: #7C3AED; }
.m-violet .sb-sub-item.active { background: rgba(124,58,237,.12); color: #C4B5FD; }
.m-violet .sb-item-icon { color: #7C3AED; }

.m-orange .sb-item.active { background: rgba(249,115,22,.15); color: #FB923C; }
.m-orange .sb-item.active .sb-item-icon { color: #F97316; }
.m-orange .sb-sub-item.active { background: rgba(249,115,22,.12); color: #FDBA74; }
.m-orange .sb-item-icon { color: #F97316; }

.m-emerald .sb-item.active { background: rgba(16,185,129,.15); color: #34D399; }
.m-emerald .sb-item.active .sb-item-icon { color: #10B981; }
.m-emerald .sb-sub-item.active { background: rgba(16,185,129,.12); color: #6EE7B7; }
.m-emerald .sb-item-icon { color: #10B981; }

.m-cyan .sb-item.active { background: rgba(6,182,212,.15); color: #22D3EE; }
.m-cyan .sb-item.active .sb-item-icon { color: #06B6D4; }
.m-cyan .sb-sub-item.active { background: rgba(6,182,212,.12); color: #67E8F9; }
.m-cyan .sb-item-icon { color: #06B6D4; }

.m-teal .sb-item.active { background: rgba(20,184,166,.15); color: #2DD4BF; }
.m-teal .sb-item.active .sb-item-icon { color: #14B8A6; }
.m-teal .sb-sub-item.active { background: rgba(20,184,166,.12); color: #5EEAD4; }
.m-teal .sb-item-icon { color: #14B8A6; }

.m-indigo .sb-item.active { background: rgba(79,70,229,.18); color: #818CF8; }
.m-indigo .sb-item.active .sb-item-icon { color: #6366F1; }
.m-indigo .sb-sub-item.active { background: rgba(79,70,229,.12); color: #A5B4FC; }
.m-indigo .sb-item-icon { color: #6366F1; }

.m-amber .sb-item.active { background: rgba(245,158,11,.15); color: #FCD34D; }
.m-amber .sb-item.active .sb-item-icon { color: #F59E0B; }
.m-amber .sb-sub-item.active { background: rgba(245,158,11,.12); color: #FDE68A; }
.m-amber .sb-item-icon { color: #F59E0B; }

.m-rose .sb-item.active { background: rgba(244,63,94,.15); color: #FB7185; }
.m-rose .sb-item.active .sb-item-icon { color: #F43F5E; }
.m-rose .sb-sub-item.active { background: rgba(244,63,94,.12); color: #FCA5A5; }
.m-rose .sb-item-icon { color: #F43F5E; }

.m-pink .sb-item.active { background: rgba(236,72,153,.15); color: #F472B6; }
.m-pink .sb-item.active .sb-item-icon { color: #EC4899; }
.m-pink .sb-sub-item.active { background: rgba(236,72,153,.12); color: #F9A8D4; }
.m-pink .sb-item-icon { color: #EC4899; }

.m-slate .sb-item.active { background: rgba(100,116,139,.2); color: #CBD5E1; }
.m-slate .sb-item.active .sb-item-icon { color: #94A3B8; }
.m-slate .sb-sub-item.active { background: rgba(100,116,139,.15); color: #CBD5E1; }
.m-slate .sb-item-icon { color: #94A3B8; }

.sb-footer { padding: 12px 16px; border-top: 1px solid rgba(255,255,255,.06); flex-shrink: 0; }
.sb-footer p { font-size: 11px; color: #334155; line-height: 1.5; }
</style>

@php
function sbActive(string ...$patterns): bool {
    foreach ($patterns as $p) {
        if (str_contains($p, '*') ? request()->routeIs($p) : request()->is(ltrim($p,'/'))) return true;
    }
    return false;
}
@endphp

<aside class="sb" id="main-sidebar">

    {{-- ── Logo ── --}}
    <div class="sb-logo">
        <div class="sb-logo-inner">
            <div class="sb-logo-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14v7"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l-9-5v7l9 5 9-5v-7z"/>
                </svg>
            </div>
            <div>
                <div class="sb-logo-title">HEMIS</div>
                <div class="sb-logo-sub">Tourism Academy</div>
            </div>
        </div>
    </div>

    {{-- ── Navigation ── --}}
    <nav class="sb-nav">

        {{-- Dashboard --}}
        <a href="{{ route('dashboard') }}"
           class="sb-item {{ sbActive('dashboard','*.dashboard') ? 'active' : '' }}"
           style="{{ sbActive('dashboard','*.dashboard') ? 'background:rgba(79,70,229,.18);color:#818CF8;' : '' }}">
            <span class="sb-item-left">
                <span class="sb-item-icon" style="color:#6366F1;">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                </span>
                Bosh sahifa
            </span>
        </a>

        <div class="sb-section-label">Talabalar va Xodimlar</div>

        {{-- Talabalar --}}
        @canany(['view_students','view_groups','manage_students'])
        @php $talOpen = sbActive('students.*','student-contingent.*'); @endphp
        <div class="m-blue" x-data="{ open: {{ $talOpen ? 'true' : 'false' }} }">
            <button @click="open = !open" class="sb-item {{ $talOpen ? 'active' : '' }}">
                <span class="sb-item-left">
                    <span class="sb-item-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </span>
                    Talabalar
                </span>
                <svg class="sb-chevron" :class="{ open: open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div class="sb-sub" :class="{ open: open }" x-show="open" x-transition>
                @can('view_students')
                <a href="{{ route('students.index') }}" class="sb-sub-item {{ sbActive('students.index') ? 'active' : '' }}">
                    <i class="fas fa-list"></i> Talabalar ro'yxati
                </a>
                @endcan
                @can('manage_students')
                <a href="{{ route('students.create') }}" class="sb-sub-item {{ sbActive('students.create') ? 'active' : '' }}">
                    <i class="fas fa-plus"></i> Yangi talaba
                </a>
                @endcan
                @can('view_groups')
                <a href="{{ route('student-contingent.groups.index') }}" class="sb-sub-item {{ sbActive('student-contingent.groups.*') ? 'active' : '' }}">
                    <i class="fas fa-layer-group"></i> Guruhlar
                </a>
                @endcan
            </div>
        </div>
        @endcanany

        {{-- Xodimlar --}}
        @canany(['view_employees','view_teachers','manage_employees'])
        @php $empOpen = sbActive('employees.*'); @endphp
        <div class="m-violet" x-data="{ open: {{ $empOpen ? 'true' : 'false' }} }">
            <button @click="open = !open" class="sb-item {{ $empOpen ? 'active' : '' }}">
                <span class="sb-item-left">
                    <span class="sb-item-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </span>
                    Xodimlar
                </span>
                <svg class="sb-chevron" :class="{ open: open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div class="sb-sub" x-show="open" x-transition>
                @can('view_employees')
                <a href="{{ route('employees.index') }}" class="sb-sub-item {{ sbActive('employees.index') ? 'active' : '' }}">
                    <i class="fas fa-users"></i> Barcha xodimlar
                </a>
                @endcan
                @canany(['view_employees','view_teachers'])
                <a href="{{ route('employees.teachers') }}" class="sb-sub-item {{ sbActive('employees.teachers') ? 'active' : '' }}">
                    <i class="fas fa-chalkboard-teacher"></i> O'qituvchilar
                </a>
                @endcanany
                @can('manage_employees')
                <a href="{{ route('employees.create') }}" class="sb-sub-item {{ sbActive('employees.create') ? 'active' : '' }}">
                    <i class="fas fa-user-plus"></i> Yangi xodim
                </a>
                @endcan
            </div>
        </div>
        @endcanany

        {{-- Vakansiyalar --}}
        @php $vacOpen = sbActive('admin.vacancies.*','admin.vacancy-applications.*'); @endphp
        <div class="m-orange" x-data="{ open: {{ $vacOpen ? 'true' : 'false' }} }">
            <button @click="open = !open" class="sb-item {{ $vacOpen ? 'active' : '' }}">
                <span class="sb-item-left">
                    <span class="sb-item-icon"><i class="fas fa-briefcase"></i></span>
                    Vakansiyalar
                </span>
                <svg class="sb-chevron" :class="{ open: open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div class="sb-sub" x-show="open" x-transition>
                <a href="{{ route('admin.vacancies.index') }}" class="sb-sub-item {{ sbActive('admin.vacancies.index') ? 'active' : '' }}">
                    <i class="fas fa-list"></i> Vakansiyalar
                </a>
                <a href="{{ route('admin.vacancy-applications.index') }}" class="sb-sub-item {{ sbActive('admin.vacancy-applications.*') ? 'active' : '' }}">
                    <i class="fas fa-users"></i> Nomzodlar
                </a>
                <a href="{{ route('admin.vacancies.create') }}" class="sb-sub-item {{ sbActive('admin.vacancies.create') ? 'active' : '' }}">
                    <i class="fas fa-plus"></i> Yangi vakansiya
                </a>
            </div>
        </div>

        <div class="sb-section-label">Tuzilma va Ta'lim</div>

        {{-- Tashkiliy tuzilma --}}
        @canany(['view_structure','manage_structure','view_employees'])
        @php $strOpen = sbActive('structure/faculties*','structure/departments*','structure/positions*','structure/chart*'); @endphp
        <div class="m-emerald" x-data="{ open: {{ $strOpen ? 'true' : 'false' }} }">
            <button @click="open = !open" class="sb-item {{ $strOpen ? 'active' : '' }}">
                <span class="sb-item-left">
                    <span class="sb-item-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </span>
                    Tashkiliy tuzilma
                </span>
                <svg class="sb-chevron" :class="{ open: open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div class="sb-sub" x-show="open" x-transition>
                <a href="{{ route('structure.faculties.index') }}" class="sb-sub-item {{ sbActive('structure.faculties.*') ? 'active' : '' }}">
                    <i class="fas fa-university"></i> Fakultetlar
                </a>
                <a href="{{ route('structure.departments.index') }}" class="sb-sub-item {{ sbActive('structure.departments.*') ? 'active' : '' }}">
                    <i class="fas fa-sitemap"></i> Kafedralar
                </a>
                <a href="{{ route('structure.positions.index') }}" class="sb-sub-item {{ sbActive('structure.positions.*') ? 'active' : '' }}">
                    <i class="fas fa-id-badge"></i> Lavozimlar
                </a>
                <a href="{{ route('structure.chart.index') }}" class="sb-sub-item {{ sbActive('structure.chart.*') ? 'active' : '' }}">
                    <i class="fas fa-project-diagram"></i> Tuzilma diagrammasi
                </a>
            </div>
        </div>
        @endcanany

        {{-- O'quv jarayoni --}}
        @canany(['view_curriculum','view_subjects','view_journal','view_schedule','view_attendance','view_grades','view_assignments'])
        @php $eduOpen = sbActive('structure/academic*','journal*','schedule*','attendance*','grades*'); @endphp
        <div class="m-cyan" x-data="{ open: {{ $eduOpen ? 'true' : 'false' }} }">
            <button @click="open = !open" class="sb-item {{ $eduOpen ? 'active' : '' }}">
                <span class="sb-item-left">
                    <span class="sb-item-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </span>
                    O'quv jarayoni
                </span>
                <svg class="sb-chevron" :class="{ open: open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div class="sb-sub" x-show="open" x-transition>
                @can('view_curriculum')
                <a href="{{ route('structure.academic.programs.index') }}" class="sb-sub-item {{ sbActive('structure.academic.programs.*') ? 'active' : '' }}">
                    <i class="fas fa-graduation-cap"></i> Ta'lim yo'nalishlari
                </a>
                @endcan
                @can('view_subjects')
                <a href="{{ route('structure.academic.subjects.index') }}" class="sb-sub-item {{ sbActive('structure.academic.subjects.*') ? 'active' : '' }}">
                    <i class="fas fa-book-open"></i> Fanlar
                </a>
                @endcan
                @can('view_curriculum')
                <a href="{{ route('structure.academic.curriculum.index') }}" class="sb-sub-item {{ sbActive('structure.academic.curriculum.*') ? 'active' : '' }}">
                    <i class="fas fa-calendar-alt"></i> O'quv rejalar
                </a>
                <a href="{{ route('structure.academic.hours.index') }}" class="sb-sub-item {{ sbActive('structure.academic.hours.*') ? 'active' : '' }}">
                    <i class="fas fa-clock"></i> Soat taqsimoti
                </a>
                @endcan
                <div style="height:1px;background:rgba(255,255,255,.05);margin:6px 0;"></div>
                @can('view_journal')
                <a href="{{ route('journal.index') }}" class="sb-sub-item {{ sbActive('journal.*') ? 'active' : '' }}">
                    <i class="fas fa-book"></i> Elektron jurnal
                </a>
                @endcan
                @can('view_schedule')
                <a href="{{ route('schedule.index') }}" class="sb-sub-item {{ sbActive('schedule.*') ? 'active' : '' }}">
                    <i class="fas fa-th"></i> Dars jadvali
                </a>
                @endcan
                @can('view_attendance')
                <a href="{{ route('attendance.all') }}" class="sb-sub-item {{ sbActive('attendance.*') ? 'active' : '' }}">
                    <i class="fas fa-user-check"></i> Davomat
                </a>
                @endcan
                @can('view_grades')
                <a href="{{ route('grades.all') }}" class="sb-sub-item {{ sbActive('grades.*') ? 'active' : '' }}">
                    <i class="fas fa-star-half-alt"></i> Baholar
                </a>
                @endcan
                @can('view_assignments')
                <a href="{{ route('assignments.index') }}" class="sb-sub-item {{ sbActive('assignments.*') ? 'active' : '' }}">
                    <i class="fas fa-tasks"></i> Topshiriqlar
                </a>
                @endcan
            </div>
        </div>
        @endcanany

        {{-- LMS --}}
        @can('view_lms')
        @php $lmsOpen = sbActive('lms*'); @endphp
        <div class="m-teal" x-data="{ open: {{ $lmsOpen ? 'true' : 'false' }} }">
            <button @click="open = !open" class="sb-item {{ $lmsOpen ? 'active' : '' }}">
                <span class="sb-item-left">
                    <span class="sb-item-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </span>
                    LMS Online Ta'lim
                </span>
                <svg class="sb-chevron" :class="{ open: open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div class="sb-sub" x-show="open" x-transition>
                <a href="{{ route('lms.dashboard') }}" class="sb-sub-item {{ sbActive('lms.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
                <a href="{{ route('lms.courses.index') }}" class="sb-sub-item {{ sbActive('lms.courses.*') ? 'active' : '' }}">
                    <i class="fas fa-book-reader"></i> Kurslar
                </a>
                <a href="{{ route('lms.materials.index') }}" class="sb-sub-item {{ sbActive('lms.materials.*') ? 'active' : '' }}">
                    <i class="fas fa-file-alt"></i> O'quv materiallari
                </a>
                <a href="{{ route('lms.videos.index') }}" class="sb-sub-item {{ sbActive('lms.videos.*') ? 'active' : '' }}">
                    <i class="fas fa-video"></i> Video darslar
                </a>
                <a href="{{ route('lms.tests.index') }}" class="sb-sub-item {{ sbActive('lms.tests.*') ? 'active' : '' }}">
                    <i class="fas fa-clipboard-list"></i> Interaktiv testlar
                </a>
                <a href="{{ route('lms.exams.index') }}" class="sb-sub-item {{ sbActive('lms.exams.*') ? 'active' : '' }}">
                    <i class="fas fa-file-signature"></i> Imtihonlar
                </a>
                <a href="{{ route('lms.forum.index') }}" class="sb-sub-item {{ sbActive('lms.forum.*') ? 'active' : '' }}">
                    <i class="fas fa-comments"></i> Forum
                </a>
                <a href="{{ route('lms.library.index') }}" class="sb-sub-item {{ sbActive('lms.library.*') ? 'active' : '' }}">
                    <i class="fas fa-book"></i> E-kutubxona
                </a>
                <a href="{{ route('lms.certificates.index') }}" class="sb-sub-item {{ sbActive('lms.certificates.*') ? 'active' : '' }}">
                    <i class="fas fa-certificate"></i> Sertifikatlar
                </a>
                <a href="{{ route('lms.progress') }}" class="sb-sub-item {{ sbActive('lms.progress') ? 'active' : '' }}">
                    <i class="fas fa-trophy"></i> Mening yutuqlarim
                </a>
            </div>
        </div>
        @endcan

        <div class="sb-section-label">Integratsiyalar</div>

        {{-- HEMIS & Akademik --}}
        @canany(['view_hemis','view_vedomost','manage_hemis'])
        @php $hemOpen = sbActive('hemis*','academic*'); @endphp
        <div class="m-indigo" x-data="{ open: {{ $hemOpen ? 'true' : 'false' }} }">
            <button @click="open = !open" class="sb-item {{ $hemOpen ? 'active' : '' }}">
                <span class="sb-item-left">
                    <span class="sb-item-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                        </svg>
                    </span>
                    HEMIS & Akademik
                </span>
                <svg class="sb-chevron" :class="{ open: open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div class="sb-sub" x-show="open" x-transition>
                @can('manage_hemis')
                <a href="{{ route('hemis.sync.index') }}" class="sb-sub-item {{ sbActive('hemis.sync.*') ? 'active' : '' }}">
                    <i class="fas fa-sync-alt"></i> HEMIS Sinxronizatsiya
                </a>
                <a href="{{ route('hemis.settings') }}" class="sb-sub-item {{ sbActive('hemis.settings') ? 'active' : '' }}">
                    <i class="fas fa-cog"></i> HEMIS Sozlamalari
                </a>
                @endcan
                @can('view_hemis')
                <a href="{{ route('gpa.index') }}" class="sb-sub-item {{ sbActive('gpa.*') ? 'active' : '' }}">
                    <i class="fas fa-chart-line"></i> GPA Kalkulyator
                </a>
                @endcan
                @can('view_vedomost')
                <a href="{{ route('vedomost.index') }}" class="sb-sub-item {{ sbActive('vedomost.*') ? 'active' : '' }}">
                    <i class="fas fa-file-alt"></i> Vedomost
                </a>
                @endcan
                @can('view_hemis')
                <a href="{{ route('academic.debt.index') }}" class="sb-sub-item {{ sbActive('academic.debt.*') ? 'active' : '' }}">
                    <i class="fas fa-exclamation-triangle"></i> Akademik Qarzdorlik
                </a>
                @endcan
            </div>
        </div>
        @endcanany

        <div class="sb-section-label">Kontent va Kampus</div>

        {{-- CMS --}}
        @canany(['view_cms','manage_cms'])
        @php $cmsOpen = sbActive('cms.*'); @endphp
        <div class="m-orange" x-data="{ open: {{ $cmsOpen ? 'true' : 'false' }} }">
            <button @click="open = !open" class="sb-item {{ $cmsOpen ? 'active' : '' }}">
                <span class="sb-item-left">
                    <span class="sb-item-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/>
                        </svg>
                    </span>
                    CMS — Sayt boshqaruvi
                </span>
                <svg class="sb-chevron" :class="{ open: open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div class="sb-sub" x-show="open" x-transition>
                <a href="{{ route('cms.dashboard') }}" class="sb-sub-item {{ sbActive('cms.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
                <a href="{{ route('cms.pages.index') }}" class="sb-sub-item {{ sbActive('cms.pages.*') ? 'active' : '' }}">
                    <i class="fas fa-file-alt"></i> Sahifalar
                </a>
                <a href="{{ route('cms.news.index') }}" class="sb-sub-item {{ sbActive('cms.news.*') ? 'active' : '' }}">
                    <i class="fas fa-newspaper"></i> Yangiliklar
                </a>
                <a href="{{ route('cms.events.index') }}" class="sb-sub-item {{ sbActive('cms.events.*') ? 'active' : '' }}">
                    <i class="fas fa-calendar-alt"></i> Tadbirlar
                </a>
                <a href="{{ route('cms.media.index') }}" class="sb-sub-item {{ sbActive('cms.media.*') ? 'active' : '' }}">
                    <i class="fas fa-photo-video"></i> Media kutubxona
                </a>
                <a href="{{ route('cms.widgets.index') }}" class="sb-sub-item {{ sbActive('cms.widgets.*') ? 'active' : '' }}">
                    <i class="fas fa-puzzle-piece"></i> Vijetlar
                </a>
                <a href="{{ route('cms.templates.index') }}" class="sb-sub-item {{ sbActive('cms.templates.*') ? 'active' : '' }}">
                    <i class="fas fa-palette"></i> Shablonlar
                </a>
                <a href="{{ route('cms.settings.index') }}" class="sb-sub-item {{ sbActive('cms.settings.*') ? 'active' : '' }}">
                    <i class="fas fa-cog"></i> Sozlamalar
                </a>
                <div style="height:1px;background:rgba(255,255,255,.05);margin:6px 0;"></div>
                <span style="font-size:10px;color:#475569;padding:2px 12px;text-transform:uppercase;letter-spacing:.06em;">Sayt dizayni</span>
                <a href="{{ route('cms.homepage.index') }}" class="sb-sub-item {{ sbActive('cms.homepage.*') && !request()->has('section') ? 'active' : '' }}">
                    <i class="fas fa-home"></i> Bosh sahifa
                </a>
                <a href="{{ route('cms.homepage.index', ['section' => 'hero']) }}" class="sb-sub-item {{ request()->get('section') == 'hero' ? 'active' : '' }}">
                    <i class="fas fa-images"></i> Hero Slider
                </a>
                <a href="{{ route('cms.themes.index') }}" class="sb-sub-item {{ sbActive('cms.themes.*') ? 'active' : '' }}">
                    <i class="fas fa-fill-drip"></i> Temalar
                </a>
                <a href="{{ route('cms.about.index') }}" class="sb-sub-item {{ sbActive('cms.about.*') ? 'active' : '' }}">
                    <i class="fas fa-info-circle"></i> Biz haqimizda
                </a>
                <a href="{{ route('cms.programs.index') }}" class="sb-sub-item {{ sbActive('cms.programs.*') ? 'active' : '' }}">
                    <i class="fas fa-graduation-cap"></i> Yo'nalishlar
                </a>
                <a href="{{ route('cms.statistics.index') }}" class="sb-sub-item {{ sbActive('cms.statistics.*') ? 'active' : '' }}">
                    <i class="fas fa-chart-bar"></i> Statistika
                </a>
                <a href="{{ route('cms.contact.index') }}" class="sb-sub-item {{ sbActive('cms.contact.*') ? 'active' : '' }}">
                    <i class="fas fa-phone"></i> Aloqa
                </a>
                <a href="{{ route('cms.teachers.index') }}" class="sb-sub-item {{ sbActive('cms.teachers.*') ? 'active' : '' }}">
                    <i class="fas fa-chalkboard-teacher"></i> O'qituvchilar
                </a>
                <a href="{{ route('cms.header-footer.header') }}" class="sb-sub-item {{ sbActive('cms.header-footer.header*') ? 'active' : '' }}">
                    <i class="fas fa-heading"></i> Header
                </a>
                <a href="{{ route('cms.header-footer.footer') }}" class="sb-sub-item {{ sbActive('cms.header-footer.footer*') ? 'active' : '' }}">
                    <i class="fas fa-ellipsis-h"></i> Footer
                </a>
                <a href="{{ route('cms.state-symbols.index') }}" class="sb-sub-item {{ sbActive('cms.state-symbols.*') ? 'active' : '' }}">
                    <i class="fas fa-flag"></i> Davlat ramzlari
                </a>
            </div>
        </div>
        @endcanany

        {{-- Kampus Virtual Turi --}}
        @php $campOpen = sbActive('campus-tour.*'); @endphp
        <div class="m-cyan" x-data="{ open: {{ $campOpen ? 'true' : 'false' }} }">
            <button @click="open = !open" class="sb-item {{ $campOpen ? 'active' : '' }}">
                <span class="sb-item-left">
                    <span class="sb-item-icon"><i class="fas fa-map-marked-alt"></i></span>
                    Kampus Turi
                </span>
                <svg class="sb-chevron" :class="{ open: open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div class="sb-sub" x-show="open" x-transition>
                <a href="{{ route('campus-tour.dashboard') }}" class="sb-sub-item {{ sbActive('campus-tour.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
                <a href="{{ route('campus-tour.panoramas.index') }}" class="sb-sub-item {{ sbActive('campus-tour.panoramas.*') ? 'active' : '' }}">
                    <i class="fas fa-vr-cardboard"></i> 360° Panoramalar
                </a>
                <a href="{{ route('campus-tour.buildings.index') }}" class="sb-sub-item {{ sbActive('campus-tour.buildings.*') ? 'active' : '' }}">
                    <i class="fas fa-building"></i> Binolar
                </a>
                <a href="{{ route('campus-tour.routes.index') }}" class="sb-sub-item {{ sbActive('campus-tour.routes.*') ? 'active' : '' }}">
                    <i class="fas fa-route"></i> Yo'nalishlar
                </a>
                <a href="{{ route('campus-tour.map.index') }}" class="sb-sub-item {{ sbActive('campus-tour.map.*') ? 'active' : '' }}">
                    <i class="fas fa-map"></i> Xarita sozlamalari
                </a>
            </div>
        </div>

        <div class="sb-section-label">Qabul</div>

        {{-- Onlayn Qabul --}}
        @canany(['view_admission','manage_admission'])
        @php
            $pendingCount = \App\Models\AdmissionApplication::where('status', 'pending')->count();
            $admOpen = sbActive('admission.*');
        @endphp
        <div class="m-amber" x-data="{ open: {{ $admOpen ? 'true' : 'false' }} }">
            <button @click="open = !open" class="sb-item {{ $admOpen ? 'active' : '' }}">
                <span class="sb-item-left">
                    <span class="sb-item-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </span>
                    Onlayn Qabul
                    @if($pendingCount > 0)
                    <span class="sb-badge">{{ $pendingCount }}</span>
                    @endif
                </span>
                <svg class="sb-chevron" :class="{ open: open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div class="sb-sub" x-show="open" x-transition>
                <a href="{{ route('admission.applications') }}" class="sb-sub-item {{ sbActive('admission.applications') ? 'active' : '' }}">
                    <i class="fas fa-inbox"></i> Kelib tushgan arizalar
                    @if($pendingCount > 0)<span class="sb-badge-sm">({{ $pendingCount }})</span>@endif
                </a>
                <a href="{{ route('admission.statistics') }}" class="sb-sub-item {{ sbActive('admission.statistics') ? 'active' : '' }}">
                    <i class="fas fa-chart-bar"></i> Statistika
                </a>
                @can('manage_admission')
                <a href="{{ route('admission.settings') }}" class="sb-sub-item {{ sbActive('admission.settings') ? 'active' : '' }}">
                    <i class="fas fa-cog"></i> Parametrlar
                </a>
                <a href="{{ route('admission.forms') }}" class="sb-sub-item {{ sbActive('admission.forms') ? 'active' : '' }}">
                    <i class="fas fa-edit"></i> Forma sozlamalari
                </a>
                @endcan
                <a href="{{ route('admission.export') }}" class="sb-sub-item {{ sbActive('admission.export') ? 'active' : '' }}">
                    <i class="fas fa-download"></i> Eksport
                </a>
            </div>
        </div>
        @endcanany

        <div class="sb-section-label">Tizim</div>

        {{-- Sozlamalar --}}
        @canany(['view_settings','manage_settings'])
        @php $setOpen = sbActive('settings.*','admin.settings.*'); @endphp
        <div class="m-slate" x-data="{ open: {{ $setOpen ? 'true' : 'false' }} }">
            <button @click="open = !open" class="sb-item {{ $setOpen ? 'active' : '' }}">
                <span class="sb-item-left">
                    <span class="sb-item-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </span>
                    Sozlamalar
                </span>
                <svg class="sb-chevron" :class="{ open: open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div class="sb-sub" x-show="open" x-transition>
                @can('manage_settings')
                <a href="{{ route('admin.settings.users.index') }}" class="sb-sub-item {{ sbActive('admin.settings.users.*') ? 'active' : '' }}">
                    <i class="fas fa-users"></i> Foydalanuvchilar
                </a>
                <a href="{{ route('admin.settings.roles.index') }}" class="sb-sub-item {{ sbActive('admin.settings.roles.*') ? 'active' : '' }}">
                    <i class="fas fa-user-tag"></i> Rollar
                </a>
                <a href="{{ route('admin.settings.permissions.index') }}" class="sb-sub-item {{ sbActive('admin.settings.permissions.*') ? 'active' : '' }}">
                    <i class="fas fa-key"></i> Ruxsatlar
                </a>
                <a href="{{ route('admin.settings.modules.index') }}" class="sb-sub-item {{ sbActive('admin.settings.modules.*') ? 'active' : '' }}">
                    <i class="fas fa-th-large"></i> Modul ko'rinishi
                </a>
                <a href="{{ route('admin.settings.otp.index') }}" class="sb-sub-item {{ sbActive('admin.settings.otp.*') ? 'active' : '' }}">
                    <i class="fas fa-mobile-alt"></i> OTP Sozlamalari
                </a>
                <div style="height:1px;background:rgba(255,255,255,.05);margin:6px 0;"></div>
                @endcan
                <a href="{{ route('settings.index') }}" class="sb-sub-item {{ sbActive('settings.index') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt"></i> Umumiy
                </a>
                <a href="{{ route('settings.general') }}" class="sb-sub-item {{ sbActive('settings.general') ? 'active' : '' }}">
                    <i class="fas fa-sliders-h"></i> Asosiy sozlamalar
                </a>
                <a href="{{ route('settings.academic') }}" class="sb-sub-item {{ sbActive('settings.academic') ? 'active' : '' }}">
                    <i class="fas fa-graduation-cap"></i> O'quv sozlamalari
                </a>
                <a href="{{ route('settings.finance') }}" class="sb-sub-item {{ sbActive('settings.finance') ? 'active' : '' }}">
                    <i class="fas fa-dollar-sign"></i> Moliya sozlamalari
                </a>
                <a href="{{ route('settings.system') }}" class="sb-sub-item {{ sbActive('settings.system') ? 'active' : '' }}">
                    <i class="fas fa-server"></i> Tizim sozlamalari
                </a>
                <a href="{{ route('settings.security') }}" class="sb-sub-item {{ sbActive('settings.security') ? 'active' : '' }}">
                    <i class="fas fa-shield-alt"></i> Xavfsizlik
                </a>
                <a href="{{ route('settings.integrations') }}" class="sb-sub-item {{ sbActive('settings.integrations*') ? 'active' : '' }}">
                    <i class="fas fa-plug"></i> Integratsiyalar
                </a>
            </div>
        </div>
        @endcanany

        {{-- Xabarnomalar --}}
        @if(auth()->user()->hasRole(['Marketing','SuperAdmin','Admin']))
        @php $notifOpen = sbActive('notifications.*'); @endphp
        <div class="m-pink" x-data="{ open: {{ $notifOpen ? 'true' : 'false' }} }">
            <button @click="open = !open" class="sb-item {{ $notifOpen ? 'active' : '' }}">
                <span class="sb-item-left">
                    <span class="sb-item-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                        </svg>
                    </span>
                    Xabarnomalar
                </span>
                <svg class="sb-chevron" :class="{ open: open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div class="sb-sub" x-show="open" x-transition>
                <a href="{{ route('notifications.sender') }}" class="sb-sub-item {{ sbActive('notifications.sender') ? 'active' : '' }}">
                    <i class="fas fa-paper-plane"></i> Xabar yuborish
                </a>
                <a href="{{ route('notifications.index') }}" class="sb-sub-item {{ sbActive('notifications.index') ? 'active' : '' }}">
                    <i class="fas fa-bell"></i> Mening xabarlarim
                </a>
            </div>
        </div>
        @endif

        {{-- Chat Admin --}}
        @if(auth()->user()->hasRole(['ChatAdmin','SuperAdmin']))
        @php
            $newTelegramCount    = \App\Models\TelegramMessage::where('status', 'new')->count();
            $pendingContactsCount = \App\Models\Chat\ChatContactRequest::where('status', 'pending')->count();
            $totalBadge          = $newTelegramCount + $pendingContactsCount;
            $chatOpen            = sbActive('chat-admin.*');
            $supportChatUnread   = \App\Models\SupportMessage::where('is_from_admin', false)->where('is_read', false)->count();
        @endphp
        <div class="m-pink" x-data="{ open: {{ $chatOpen ? 'true' : 'false' }} }">
            <button @click="open = !open" class="sb-item {{ $chatOpen ? 'active' : '' }}">
                <span class="sb-item-left">
                    <span class="sb-item-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                    </span>
                    Chat Admin
                    @if($totalBadge > 0)
                    <span class="sb-badge">{{ $totalBadge }}</span>
                    @endif
                </span>
                <svg class="sb-chevron" :class="{ open: open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div class="sb-sub" x-show="open" x-transition>
                <a href="{{ route('chat-admin.dashboard') }}" class="sb-sub-item {{ sbActive('chat-admin.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
                <a href="{{ route('chat-admin.support-chat') }}" class="sb-sub-item {{ sbActive('chat-admin.support-chat*') ? 'active' : '' }}">
                    <i class="fas fa-headset"></i> Support Chat
                    @if($supportChatUnread > 0)<span class="sb-badge-sm">({{ $supportChatUnread }})</span>@endif
                </a>
                <a href="{{ route('chat-admin.telegram') }}" class="sb-sub-item {{ sbActive('chat-admin.telegram') ? 'active' : '' }}">
                    <i class="fab fa-telegram"></i> Telegram xabarlari
                    @if($newTelegramCount > 0)<span class="sb-badge-sm">({{ $newTelegramCount }})</span>@endif
                </a>
                <a href="{{ route('chat-admin.contact-requests') }}" class="sb-sub-item {{ sbActive('chat-admin.contact-requests') ? 'active' : '' }}">
                    <i class="fas fa-user-clock"></i> Murojaatlar
                    @if($pendingContactsCount > 0)<span class="sb-badge-sm">({{ $pendingContactsCount }})</span>@endif
                </a>
                <a href="{{ route('chat-admin.telegram-settings') }}" class="sb-sub-item {{ sbActive('chat-admin.telegram-settings') ? 'active' : '' }}">
                    <i class="fas fa-robot"></i> Bot sozlamalari
                </a>
                <a href="{{ route('chat-admin.nicknames') }}" class="sb-sub-item {{ sbActive('chat-admin.nicknames') ? 'active' : '' }}">
                    <i class="fas fa-at"></i> Niknamlar
                </a>
            </div>
        </div>
        @endif

        <div class="sb-section-label">Qo'shimcha imkoniyatlar</div>

        {{-- Qo'shimcha imkoniyatlar sahifasi --}}
        <a href="{{ route('extra.index') }}"
           class="sb-item {{ sbActive('extra') ? 'active' : '' }}"
           style="{{ sbActive('extra') ? 'background:rgba(245,158,11,.15);color:#FCD34D;' : '' }}">
            <span class="sb-item-left">
                <span class="sb-item-icon" style="color:#F59E0B;">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                    </svg>
                </span>
                Qo'shimcha imkoniyatlar
            </span>
            <svg style="width:14px;height:14px;flex-shrink:0;opacity:.45" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </a>

        {{-- Profilni tahrirlash --}}
        <a href="{{ route('profile.edit') }}"
           class="sb-item {{ sbActive('profile/*','profile') ? 'active' : '' }}"
           style="{{ sbActive('profile/*','profile') ? 'background:rgba(79,70,229,.18);color:#818CF8;' : '' }}">
            <span class="sb-item-left">
                <span class="sb-item-icon" style="color:#6366F1;">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </span>
                Mening profilim
            </span>
            <svg style="width:14px;height:14px;flex-shrink:0;opacity:.45" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </a>

    </nav>

    {{-- ── Footer ── --}}
    <div class="sb-footer">
        <p>© {{ date('Y') }} HEMIS · Tourism Academy Samarkand</p>
    </div>

</aside>
