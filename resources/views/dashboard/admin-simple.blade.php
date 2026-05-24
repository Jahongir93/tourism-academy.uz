@extends('layouts.dashboard-new')

@section('title', 'Bosh sahifa — HEMIS')
@section('page-title', 'Bosh sahifa')

@section('content')

{{-- ── Welcome Banner ── --}}
<div class="mb-5" style="
    background: linear-gradient(135deg, var(--c-sidebar) 0%, #1E293B 60%, #0F3460 100%);
    border-radius: 20px;
    padding: 32px 36px;
    position: relative;
    overflow: hidden;
">
    <div style="position:absolute;right:-40px;top:-40px;width:200px;height:200px;border-radius:50%;background:rgba(79,70,229,.15)"></div>
    <div style="position:absolute;right:60px;bottom:-60px;width:140px;height:140px;border-radius:50%;background:rgba(124,58,237,.12)"></div>
    <div style="position:relative;z-index:1">
        <div style="font-size:13px;color:rgba(255,255,255,.5);margin-bottom:6px;font-weight:500;letter-spacing:.05em;text-transform:uppercase">
            {{ now()->locale('uz')->isoFormat('dddd, D MMMM Y') }}
        </div>
        <h2 style="color:#fff;font-size:24px;font-weight:800;margin:0 0 6px">
            Xush kelibsiz, {{ auth()->user()->name ?? 'Admin' }} 👋
        </h2>
        <p style="color:rgba(255,255,255,.55);margin:0;font-size:14px">
            Tourism Academy HEMIS tizimiga kirish muvaffaqiyatli amalga oshirildi
        </p>
        <div class="d-flex gap-3 mt-4 flex-wrap">
            <a href="{{ route('students.create') }}" class="btn btn-sm"
               style="background:rgba(255,255,255,.12);color:#fff;border:1px solid rgba(255,255,255,.2);backdrop-filter:blur(6px)">
                <i class="fas fa-user-plus me-1"></i> Yangi talaba
            </a>
            <a href="{{ route('employees.create') }}" class="btn btn-sm"
               style="background:rgba(255,255,255,.12);color:#fff;border:1px solid rgba(255,255,255,.2);backdrop-filter:blur(6px)">
                <i class="fas fa-user-tie me-1"></i> Yangi xodim
            </a>
            <a href="{{ route('hemis.sync.index') }}" class="btn btn-sm"
               style="background:rgba(79,70,229,.5);color:#fff;border:1px solid rgba(79,70,229,.4)">
                <i class="fas fa-sync-alt me-1"></i> HEMIS Sinxron
            </a>
        </div>
    </div>
</div>

{{-- ── Main Stat Cards ── --}}
<div class="row g-4 mb-4">
    <div class="col-6 col-xl-3">
        <a href="{{ route('students.index') }}" class="text-decoration-none d-block">
            <div class="stat-card anim-fade-up" style="color:var(--c-sky)">
                <div class="stat-card-icon" style="background:rgba(14,165,233,.12);color:var(--c-sky)">
                    <i class="fas fa-user-graduate"></i>
                </div>
                <div class="stat-card-value" style="color:var(--c-text)">{{ number_format($stats['total_students'] ?? 0) }}</div>
                <div class="stat-card-label">Talabalar</div>
                <div class="stat-card-delta up"><i class="fas fa-arrow-up fa-xs"></i> Joriy semestr</div>
            </div>
        </a>
    </div>
    <div class="col-6 col-xl-3 delay-1">
        <a href="{{ route('employees.teachers') }}" class="text-decoration-none d-block">
            <div class="stat-card anim-fade-up" style="color:var(--c-violet)">
                <div class="stat-card-icon" style="background:rgba(124,58,237,.12);color:var(--c-violet)">
                    <i class="fas fa-chalkboard-teacher"></i>
                </div>
                <div class="stat-card-value" style="color:var(--c-text)">{{ number_format($stats['total_teachers'] ?? 0) }}</div>
                <div class="stat-card-label">O'qituvchilar</div>
                <div class="stat-card-delta up"><i class="fas fa-arrow-up fa-xs"></i> Faol holatda</div>
            </div>
        </a>
    </div>
    <div class="col-6 col-xl-3 delay-2">
        <a href="{{ route('student-contingent.groups.index') }}" class="text-decoration-none d-block">
            <div class="stat-card anim-fade-up" style="color:var(--c-emerald)">
                <div class="stat-card-icon" style="background:rgba(16,185,129,.12);color:var(--c-emerald)">
                    <i class="fas fa-layer-group"></i>
                </div>
                <div class="stat-card-value" style="color:var(--c-text)">{{ number_format($stats['total_groups'] ?? 0) }}</div>
                <div class="stat-card-label">Guruhlar</div>
                <div class="stat-card-delta up"><i class="fas fa-arrow-up fa-xs"></i> Barcha kurslar</div>
            </div>
        </a>
    </div>
    <div class="col-6 col-xl-3 delay-3">
        <a href="{{ route('structure.faculties.index') }}" class="text-decoration-none d-block">
            <div class="stat-card anim-fade-up" style="color:var(--c-teal)">
                <div class="stat-card-icon" style="background:rgba(20,184,166,.12);color:var(--c-teal)">
                    <i class="fas fa-university"></i>
                </div>
                <div class="stat-card-value" style="color:var(--c-text)">{{ number_format($stats['total_faculties'] ?? 0) }}</div>
                <div class="stat-card-label">Fakultetlar</div>
                <div class="stat-card-delta up"><i class="fas fa-arrow-up fa-xs"></i> Tashkiliy tuzilma</div>
            </div>
        </a>
    </div>
</div>

{{-- ── Secondary stats ── --}}
<div class="row g-4 mb-4">
    <div class="col-6 col-xl-3">
        <a href="{{ route('structure.academic.subjects.index') }}" class="text-decoration-none d-block">
            <div class="stat-card anim-fade-up delay-1" style="color:var(--c-cyan)">
                <div class="stat-card-icon" style="background:rgba(6,182,212,.12);color:var(--c-cyan)">
                    <i class="fas fa-book-open"></i>
                </div>
                <div class="stat-card-value" style="color:var(--c-text)">{{ number_format($stats['total_subjects'] ?? 0) }}</div>
                <div class="stat-card-label">Fanlar</div>
            </div>
        </a>
    </div>
    <div class="col-6 col-xl-3">
        <div class="stat-card anim-fade-up delay-2" style="color:var(--c-amber)">
            <div class="stat-card-icon" style="background:rgba(245,158,11,.12);color:var(--c-amber)">
                <i class="fas fa-bookmark"></i>
            </div>
            <div class="stat-card-value" style="color:var(--c-text)">{{ number_format($stats['total_specialties'] ?? 0) }}</div>
            <div class="stat-card-label">Mutaxassisliklar</div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <a href="{{ route('gpa.index') }}" class="text-decoration-none d-block">
            <div class="stat-card anim-fade-up delay-3" style="color:var(--c-indigo,var(--c-primary))">
                <div class="stat-card-icon" style="background:rgba(79,70,229,.12);color:var(--c-primary)">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="stat-card-value" style="color:var(--c-text)">{{ number_format($stats['average_gpa'] ?? 0, 1) }}</div>
                <div class="stat-card-label">O'rtacha GPA</div>
            </div>
        </a>
    </div>
    <div class="col-6 col-xl-3">
        <a href="{{ route('academic.debt.index') }}" class="text-decoration-none d-block">
            <div class="stat-card anim-fade-up delay-4" style="color:var(--c-rose)">
                <div class="stat-card-icon" style="background:rgba(244,63,94,.12);color:var(--c-rose)">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="stat-card-value" style="color:var(--c-text)">{{ number_format($stats['students_with_debts'] ?? 0) }}</div>
                <div class="stat-card-label">Akademik qarzdorlar</div>
                @if(($stats['students_with_debts'] ?? 0) > 0)
                <div class="stat-card-delta down"><i class="fas fa-exclamation-circle fa-xs"></i> Diqqat talab etadi</div>
                @endif
            </div>
        </a>
    </div>
</div>

{{-- ── HEMIS Module Cards ── --}}
<div class="card mb-4 anim-fade-up">
    <div class="card-header d-flex align-items-center gap-2">
        <span style="width:28px;height:28px;border-radius:7px;background:linear-gradient(135deg,var(--c-primary),var(--c-violet));display:inline-flex;align-items:center;justify-content:center;color:#fff;font-size:12px">
            <i class="fas fa-graduation-cap"></i>
        </span>
        <span style="font-weight:700">HEMIS Integratsiyasi va Akademik Tizim</span>
    </div>
    <div class="card-body">
        <div class="row g-3">
            @php
            $hemisModules = [
                ['route'=>'hemis.sync.index',   'icon'=>'fa-sync-alt',           'color'=>'var(--c-primary)', 'rgb'=>'79,70,229',   'title'=>'HEMIS Sinxron',      'sub'=>'Talabalar va baholarni sinxronlash'],
                ['route'=>'gpa.index',           'icon'=>'fa-chart-bar',          'color'=>'var(--c-emerald)', 'rgb'=>'16,185,129',  'title'=>'GPA Kalkulyator',    'sub'=>"O'rtacha ball: ".number_format($stats['average_gpa']??0,2).' · '.$stats['total_grades']??0 .' ta baho'],
                ['route'=>'vedomost.index',      'icon'=>'fa-file-alt',           'color'=>'var(--c-amber)',   'rgb'=>'245,158,11',  'title'=>'Vedomost',           'sub'=>'Baholar jadvali va transkrip'],
                ['route'=>'academic.debt.index', 'icon'=>'fa-exclamation-circle', 'color'=>'var(--c-rose)',    'rgb'=>'244,63,94',   'title'=>'Akademik Qarzdorlik','sub'=>'Qarzdorlar: '.($stats['students_with_debts']??0)],
            ];
            @endphp
            @foreach($hemisModules as $m)
            <div class="col-6 col-md-3">
                <a href="{{ route($m['route']) }}" class="text-decoration-none d-block h-100">
                    <div style="
                        border:1.5px solid rgba({{ $m['rgb'] }},.15);
                        border-radius:14px;
                        padding:20px 16px;
                        background:rgba({{ $m['rgb'] }},.04);
                        transition:all .2s;
                        height:100%;
                    " onmouseover="this.style.background='rgba({{ $m['rgb'] }},.09)';this.style.transform='translateY(-2px)'"
                       onmouseout="this.style.background='rgba({{ $m['rgb'] }},.04)';this.style.transform='translateY(0)'">
                        <div style="width:44px;height:44px;border-radius:10px;background:rgba({{ $m['rgb'] }},.15);display:flex;align-items:center;justify-content:center;color:{{ $m['color'] }};font-size:18px;margin-bottom:14px">
                            <i class="fas {{ $m['icon'] }}"></i>
                        </div>
                        <div style="font-size:14px;font-weight:700;color:var(--c-text);margin-bottom:4px">{{ $m['title'] }}</div>
                        <div style="font-size:11px;color:var(--c-text-3);line-height:1.4">{{ $m['sub'] }}</div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- ── Chart + Quick Actions ── --}}
<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-chart-area" style="color:var(--c-primary)"></i>
                    <span>Haftalik statistika</span>
                </div>
                <span style="font-size:11px;color:var(--c-text-3)">Joriy hafta</span>
            </div>
            <div class="card-body">
                <canvas id="weeklyChart" style="height:260px;max-height:260px"></canvas>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="fas fa-bolt" style="color:var(--c-amber)"></i>
                <span>Tezkor amallar</span>
            </div>
            <div class="card-body p-0">
                @php
                $qactions = [
                    ['route'=>'students.create',       'icon'=>'fa-user-plus',      'color'=>'var(--c-sky)',     'label'=>'Yangi talaba qo\'shish'],
                    ['route'=>'employees.create',      'icon'=>'fa-user-tie',       'color'=>'var(--c-violet)',  'label'=>'Yangi xodim qo\'shish'],
                    ['route'=>'journal.create',        'icon'=>'fa-book-medical',   'color'=>'var(--c-teal)',    'label'=>'Jurnal yaratish'],
                    ['route'=>'schedule.create',       'icon'=>'fa-calendar-plus',  'color'=>'var(--c-amber)',   'label'=>'Jadval qo\'shish'],
                    ['route'=>'cms.news.create',       'icon'=>'fa-newspaper',      'color'=>'var(--c-orange)',  'label'=>'Yangilik e\'lon qilish'],
                    ['route'=>'vedomost.create',       'icon'=>'fa-plus-circle',    'color'=>'var(--c-primary)', 'label'=>'Vedomost yaratish'],
                    ['route'=>'hemis.sync.index',      'icon'=>'fa-sync',           'color'=>'var(--c-emerald)', 'label'=>'HEMIS sinxronizatsiya'],
                    ['route'=>'hemis.settings',        'icon'=>'fa-cog',            'color'=>'var(--c-text-2)',  'label'=>'HEMIS sozlamalari'],
                ];
                @endphp
                <ul class="list-group list-group-flush">
                    @foreach($qactions as $qa)
                    @if(Route::has($qa['route']))
                    <li class="list-group-item border-0 p-0" style="border-bottom:1px solid var(--c-border)!important">
                        <a href="{{ route($qa['route']) }}"
                           class="d-flex align-items-center gap-3 px-4 py-3 text-decoration-none"
                           style="transition:all .15s;color:var(--c-text-2)"
                           onmouseover="this.style.background='var(--c-bg)';this.style.color='var(--c-text)'"
                           onmouseout="this.style.background='transparent';this.style.color='var(--c-text-2)'">
                            <i class="fas {{ $qa['icon'] }} fa-sm" style="color:{{ $qa['color'] }};width:16px;text-align:center"></i>
                            <span style="font-size:13px;font-weight:500">{{ $qa['label'] }}</span>
                        </a>
                    </li>
                    @endif
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>

{{-- ── Recent users + System info ── --}}
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-user-clock" style="color:var(--c-sky)"></i>
                    <span>So'nggi ro'yxatdan o'tganlar</span>
                </div>
                <a href="{{ route('admin.settings.users.index') }}" class="btn btn-sm btn-outline-secondary">
                    Barchasi <i class="fas fa-arrow-right fa-xs ms-1"></i>
                </a>
            </div>
            <div class="card-body p-0">
                @if(isset($stats['recent_registrations']) && count($stats['recent_registrations']))
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Ism</th>
                                <th>Email</th>
                                <th>Roli</th>
                                <th>Sana</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($stats['recent_registrations'] as $u)
                            <tr>
                                <td style="color:var(--c-text-3)">#{{ $u->id }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div style="width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,var(--c-primary),var(--c-violet));display:flex;align-items:center;justify-content:center;color:#fff;font-size:11px;font-weight:700;flex-shrink:0">
                                            {{ strtoupper(substr($u->name,0,1)) }}
                                        </div>
                                        <span style="font-weight:600;color:var(--c-text)">{{ $u->name }}</span>
                                    </div>
                                </td>
                                <td>{{ $u->email }}</td>
                                <td>
                                    @foreach($u->getRoleNames() as $role)
                                    <span class="badge" style="background:rgba(79,70,229,.1);color:var(--c-primary);font-size:11px">{{ $role }}</span>
                                    @endforeach
                                </td>
                                <td style="color:var(--c-text-3)">{{ $u->created_at->format('d.m.Y H:i') }}</td>
                                <td><span class="badge" style="background:rgba(16,185,129,.1);color:var(--c-emerald);font-size:11px">Aktiv</span></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="empty-state py-5">
                    <div class="empty-state-icon"><i class="fas fa-inbox"></i></div>
                    <div class="empty-state-sub">Hozircha yangi foydalanuvchilar yo'q</div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="fas fa-server" style="color:var(--c-teal)"></i>
                <span>Tizim ma'lumotlari</span>
            </div>
            <div class="card-body">
                @php
                $sysItems = [
                    ['icon'=>'fa-book-open',    'color'=>'var(--c-cyan)',    'label'=>'Fanlar soni',           'val'=> number_format($stats['total_subjects']??0)],
                    ['icon'=>'fa-users',        'color'=>'var(--c-primary)', 'label'=>'Foydalanuvchilar',      'val'=> number_format($stats['total_users']??0)],
                    ['icon'=>'fa-clock',        'color'=>'var(--c-amber)',   'label'=>'Server vaqti',          'val'=> now()->format('H:i:s')],
                    ['icon'=>'fa-calendar-day', 'color'=>'var(--c-violet)',  'label'=>'Bugungi sana',          'val'=> now()->format('d M Y')],
                    ['icon'=>'fa-code-branch',  'color'=>'var(--c-emerald)', 'label'=>'Laravel versiyasi',     'val'=> app()->version()],
                    ['icon'=>'fa-php',          'color'=>'var(--c-sky)',     'label'=>'PHP versiyasi',          'val'=> PHP_VERSION],
                ];
                @endphp
                <div class="d-flex flex-column gap-3">
                    @foreach($sysItems as $s)
                    <div class="d-flex align-items-center justify-content-between py-2"
                         style="border-bottom:1px solid var(--c-border)">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fas {{ $s['icon'] }} fa-sm" style="color:{{ $s['color'] }};width:16px;text-align:center"></i>
                            <span style="font-size:13px;color:var(--c-text-2)">{{ $s['label'] }}</span>
                        </div>
                        <span style="font-size:13px;font-weight:700;color:var(--c-text)">{{ $s['val'] }}</span>
                    </div>
                    @endforeach
                </div>

                <div class="mt-4 p-3 rounded-3 text-center" style="background:var(--c-bg)">
                    <div style="font-size:11px;color:var(--c-text-3);margin-bottom:4px">Tizim holati</div>
                    <div class="d-flex align-items-center justify-content-center gap-2">
                        <div style="width:8px;height:8px;border-radius:50%;background:var(--c-emerald);animation:pulse 2s infinite"></div>
                        <span style="font-size:13px;font-weight:600;color:var(--c-emerald)">Barcha xizmatlar ishlayapti</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function() {
    const style = getComputedStyle(document.documentElement);
    const primary = style.getPropertyValue('--c-primary').trim() || '#4F46E5';
    const emerald = style.getPropertyValue('--c-emerald').trim() || '#10B981';
    const border  = style.getPropertyValue('--c-border').trim()  || '#E2E8F0';
    const text3   = style.getPropertyValue('--c-text-3').trim()  || '#94A3B8';

    const ctx = document.getElementById('weeklyChart');
    if (!ctx) return;

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Dushanba','Seshanba','Chorshanba','Payshanba','Juma','Shanba'],
            datasets: [{
                label: 'Talabalar davomati (%)',
                data: [85, 88, 82, 90, 92, 87],
                borderColor: primary,
                backgroundColor: primary + '18',
                borderWidth: 2.5,
                tension: 0.45,
                fill: true,
                pointBackgroundColor: primary,
                pointRadius: 4,
                pointHoverRadius: 6,
            }, {
                label: 'Darslar soni',
                data: [32, 35, 30, 38, 36, 28],
                borderColor: emerald,
                backgroundColor: emerald + '18',
                borderWidth: 2.5,
                tension: 0.45,
                fill: true,
                pointBackgroundColor: emerald,
                pointRadius: 4,
                pointHoverRadius: 6,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        font: { family: 'Inter', size: 12 },
                        color: text3,
                        padding: 20,
                        usePointStyle: true,
                        pointStyleWidth: 8,
                    }
                },
                tooltip: {
                    backgroundColor: '#0F172A',
                    titleColor: '#fff',
                    bodyColor: '#94A3B8',
                    borderColor: '#1E293B',
                    borderWidth: 1,
                    padding: 12,
                    titleFont: { family: 'Inter', weight: '600' },
                    bodyFont: { family: 'Inter' },
                    cornerRadius: 10,
                }
            },
            scales: {
                x: {
                    grid: { color: border, drawBorder: false },
                    ticks: { font: { family: 'Inter', size: 12 }, color: text3 },
                },
                y: {
                    beginAtZero: true,
                    grid: { color: border, drawBorder: false },
                    ticks: { font: { family: 'Inter', size: 12 }, color: text3 },
                }
            }
        }
    });

    // Pulse animation for status dot
    const style2 = document.createElement('style');
    style2.textContent = '@keyframes pulse{0%,100%{opacity:1}50%{opacity:.4}}';
    document.head.appendChild(style2);
})();
</script>
@endsection
