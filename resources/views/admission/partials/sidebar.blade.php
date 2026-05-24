<div class="bg-white rounded-lg shadow-sm p-4 mb-4">
    <h5 class="text-success mb-3">
        <i class="fas fa-graduation-cap me-2"></i> Onlayn Qabul
    </h5>

    <ul class="nav nav-pills flex-column">
        <li class="nav-item mb-2">
            <a href="{{ route('admission.applications') }}"
               class="nav-link {{ request()->routeIs('admission.applications') ? 'active bg-success' : 'text-dark' }}">
                <i class="fas fa-list me-2"></i> Arizalar ro'yxati
                @php
                    $pendingCount = \App\Models\AdmissionApplication::where('status', 'pending')->count();
                @endphp
                @if($pendingCount > 0)
                    <span class="badge bg-danger float-end">{{ $pendingCount }}</span>
                @endif
            </a>
        </li>

        <li class="nav-item mb-2">
            <a href="{{ route('admission.statistics') }}"
               class="nav-link {{ request()->routeIs('admission.statistics') ? 'active bg-success' : 'text-dark' }}">
                <i class="fas fa-chart-bar me-2"></i> Statistika
            </a>
        </li>

        <li class="nav-item mb-2">
            <a href="{{ route('admission.settings') }}"
               class="nav-link {{ request()->routeIs('admission.settings') ? 'active bg-success' : 'text-dark' }}">
                <i class="fas fa-cog me-2"></i> Sozlamalar
            </a>
        </li>

        <li class="nav-item mb-2">
            <a href="{{ route('admission.forms') }}"
               class="nav-link {{ request()->routeIs('admission.forms') ? 'active bg-success' : 'text-dark' }}">
                <i class="fas fa-edit me-2"></i> Forma tahrirlash
            </a>
        </li>

        <li class="nav-item mb-2">
            <a href="{{ route('admission.export') }}?format=excel"
               class="nav-link text-dark">
                <i class="fas fa-download me-2"></i> Excel yuklash
            </a>
        </li>
    </ul>
</div>

<div class="bg-white rounded-lg shadow-sm p-4">
    <h6 class="text-muted mb-3">Tezkor statistika</h6>

    @php
        $todayCount = \App\Models\AdmissionApplication::whereDate('applied_at', today())->count();
        $weekCount = \App\Models\AdmissionApplication::where('applied_at', '>=', now()->subWeek())->count();
        $totalCount = \App\Models\AdmissionApplication::count();
    @endphp

    <div class="d-flex justify-content-between mb-2">
        <span class="text-muted">Bugun:</span>
        <span class="badge bg-info">{{ $todayCount }}</span>
    </div>

    <div class="d-flex justify-content-between mb-2">
        <span class="text-muted">Shu hafta:</span>
        <span class="badge bg-primary">{{ $weekCount }}</span>
    </div>

    <div class="d-flex justify-content-between">
        <span class="text-muted">Jami:</span>
        <span class="badge bg-success">{{ $totalCount }}</span>
    </div>
</div>