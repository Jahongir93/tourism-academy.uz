@extends('layouts.dashboard-new')

@section('content')
<style>
    :root {
        --primary-blue: #0066CC;
        --primary-dark-blue: #0052A3;
        --secondary-blue: #3b82f6;
        --light-blue: #dbeafe;
        --lighter-blue: #eff6ff;
    }

    .stats-container {
        background: var(--lighter-blue);
        min-height: 100vh;
        padding: 1.5rem 0;
    }

    .stats-header {
        background: linear-gradient(135deg, var(--primary-blue), var(--primary-dark-blue));
        color: white;
        padding: 1.25rem;
        border-radius: 12px;
        margin-bottom: 1.5rem;
    }

    .stat-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 4px rgba(0, 102, 204, 0.1);
        padding: 1rem;
        height: 100%;
        transition: all 0.3s ease;
        text-align: center;
    }

    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 8px rgba(0, 102, 204, 0.15);
    }

    .stat-icon {
        width: 45px;
        height: 45px;
        background: var(--light-blue);
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: var(--primary-blue);
        font-size: 1.2rem;
        margin-bottom: 0.5rem;
    }

    .stat-number {
        font-size: 1.75rem;
        font-weight: bold;
        color: var(--primary-blue);
        line-height: 1.2;
    }

    .stat-label {
        color: #6b7280;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .chart-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 4px rgba(0, 102, 204, 0.1);
        height: 100%;
    }

    .chart-header {
        padding: 0.75rem 1rem;
        border-bottom: 1px solid var(--light-blue);
        font-weight: 600;
        color: #374151;
        font-size: 0.95rem;
    }

    .chart-header i {
        color: var(--primary-blue);
    }

    .chart-body {
        padding: 1rem;
    }

    .chart-scroll {
        max-height: 280px;
        overflow-y: auto;
    }

    .progress-bar-custom {
        background-color: var(--light-blue);
        height: 20px;
        border-radius: 10px;
        overflow: hidden;
        margin-bottom: 0.25rem;
    }

    .progress-fill {
        background: linear-gradient(90deg, var(--secondary-blue), var(--primary-blue));
        height: 100%;
        display: flex;
        align-items: center;
        padding-left: 8px;
        color: white;
        font-weight: 500;
        font-size: 0.75rem;
        transition: width 0.5s ease;
    }

    .list-item {
        padding: 0.5rem 0.75rem;
        border-bottom: 1px solid var(--light-blue);
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 0.9rem;
    }

    .list-item:last-child {
        border-bottom: none;
    }

    .list-item:hover {
        background: var(--lighter-blue);
    }

    .badge-custom {
        background: var(--light-blue);
        color: var(--primary-dark-blue);
        padding: 0.2rem 0.6rem;
        border-radius: 15px;
        font-weight: 500;
        font-size: 0.8rem;
    }

    /* Tab styles */
    .chart-tabs {
        display: flex;
        gap: 0.5rem;
        padding: 0.75rem 1rem;
        border-bottom: 1px solid var(--light-blue);
        flex-wrap: wrap;
    }

    .chart-tab {
        padding: 0.4rem 0.75rem;
        border-radius: 6px;
        background: var(--lighter-blue);
        color: #374151;
        cursor: pointer;
        font-size: 0.85rem;
        transition: all 0.2s;
        border: none;
    }

    .chart-tab:hover {
        background: var(--light-blue);
    }

    .chart-tab.active {
        background: var(--primary-blue);
        color: white;
    }

    .chart-pane {
        display: none;
    }

    .chart-pane.active {
        display: block;
    }

    /* Custom scrollbar */
    .chart-scroll::-webkit-scrollbar {
        width: 6px;
    }

    .chart-scroll::-webkit-scrollbar-track {
        background: var(--lighter-blue);
        border-radius: 3px;
    }

    .chart-scroll::-webkit-scrollbar-thumb {
        background: var(--secondary-blue);
        border-radius: 3px;
    }

    @media print {
        .stats-header button, .stats-header a {
            display: none;
        }
    }
</style>

<div class="stats-container">
    <div class="container-fluid">
        <div class="stats-header">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h4 class="mb-0"><i class="fas fa-chart-line me-2"></i> Qabul Statistikasi</h4>
                    <small class="opacity-90">Online qabul jarayoni tahlili</small>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-light" onclick="window.print()">
                        <i class="fas fa-print me-1"></i> Chop etish
                    </button>
                    <a href="{{ route('admission.export') }}" class="btn btn-sm btn-light">
                        <i class="fas fa-download me-1"></i> Eksport
                    </a>
                </div>
            </div>
        </div>

        <!-- Summary Stats - Compact Row -->
        <div class="row g-2 mb-3">
            <div class="col-6 col-md-3 col-lg">
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-users"></i></div>
                    <div class="stat-number" id="totalApplications">{{ array_sum(array_column($byStatus->toArray(), 'count')) }}</div>
                    <div class="stat-label">Jami</div>
                </div>
            </div>
            <div class="col-6 col-md-3 col-lg">
                <div class="stat-card">
                    <div class="stat-icon" style="background: #fef3c7; color: #d97706;"><i class="fas fa-clock"></i></div>
                    <div class="stat-number" style="color: #d97706;">{{ $byStatus->where('status', 'pending')->first()->count ?? 0 }}</div>
                    <div class="stat-label">Kutilmoqda</div>
                </div>
            </div>
            <div class="col-6 col-md-3 col-lg">
                <div class="stat-card">
                    <div class="stat-icon" style="background: #dbeafe; color: #3b82f6;"><i class="fas fa-search"></i></div>
                    <div class="stat-number" style="color: #3b82f6;">{{ $byStatus->where('status', 'reviewing')->first()->count ?? 0 }}</div>
                    <div class="stat-label">Ko'rilmoqda</div>
                </div>
            </div>
            <div class="col-6 col-md-3 col-lg">
                <div class="stat-card">
                    <div class="stat-icon" style="background: #d1fae5; color: #10b981;"><i class="fas fa-check-circle"></i></div>
                    <div class="stat-number" style="color: #10b981;">{{ $byStatus->where('status', 'accepted')->first()->count ?? 0 }}</div>
                    <div class="stat-label">Qabul</div>
                </div>
            </div>
            <div class="col-6 col-md-3 col-lg">
                <div class="stat-card">
                    <div class="stat-icon" style="background: #fee2e2; color: #ef4444;"><i class="fas fa-times-circle"></i></div>
                    <div class="stat-number" style="color: #ef4444;">{{ $byStatus->where('status', 'rejected')->first()->count ?? 0 }}</div>
                    <div class="stat-label">Rad</div>
                </div>
            </div>
        </div>

        <!-- Main Charts Row -->
        <div class="row g-3 mb-3">
            <!-- Status & Daily Charts with Tabs -->
            <div class="col-lg-6">
                <div class="chart-card">
                    <div class="chart-tabs">
                        <button class="chart-tab active" onclick="switchChartTab(this, 'statusPane')">
                            <i class="fas fa-pie-chart me-1"></i> Holat
                        </button>
                        <button class="chart-tab" onclick="switchChartTab(this, 'dailyPane')">
                            <i class="fas fa-chart-line me-1"></i> Kunlik
                        </button>
                    </div>
                    <div class="chart-body">
                        <div class="chart-pane active" id="statusPane">
                            <canvas id="statusChart" height="250"></canvas>
                        </div>
                        <div class="chart-pane" id="dailyPane">
                            <canvas id="dailyChart" height="250"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Faculty & Region with Tabs -->
            <div class="col-lg-6">
                <div class="chart-card">
                    <div class="chart-tabs">
                        <button class="chart-tab active" onclick="switchChartTab(this, 'facultyPane')">
                            <i class="fas fa-building me-1"></i> Fakultetlar
                        </button>
                        <button class="chart-tab" onclick="switchChartTab(this, 'regionPane')">
                            <i class="fas fa-map-marked-alt me-1"></i> Viloyatlar
                        </button>
                    </div>
                    <div class="chart-body">
                        <div class="chart-pane active chart-scroll" id="facultyPane">
                            @php $facultyTotal = $byFaculty->sum('count'); @endphp
                            @foreach($byFaculty as $item)
                                @if($item->faculty && $facultyTotal > 0)
                                    <div class="mb-2">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <small class="text-truncate" style="max-width: 70%;">{{ $item->faculty->name_uz }}</small>
                                            <span class="badge-custom">{{ $item->count }}</span>
                                        </div>
                                        <div class="progress-bar-custom">
                                            <div class="progress-fill" style="width: {{ ($item->count / $facultyTotal) * 100 }}%">
                                                {{ round(($item->count / $facultyTotal) * 100, 1) }}%
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                            @if($byFaculty->isEmpty())
                                <div class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-2x mb-2"></i>
                                    <p class="mb-0">Ma'lumot mavjud emas</p>
                                </div>
                            @endif
                        </div>
                        <div class="chart-pane chart-scroll" id="regionPane">
                            @foreach($byRegion->take(15) as $item)
                                <div class="list-item">
                                    <span><i class="fas fa-map-pin me-2" style="color: var(--primary-blue);"></i> {{ $item->region ?: 'Noma\'lum' }}</span>
                                    <span class="badge-custom">{{ $item->count }}</span>
                                </div>
                            @endforeach
                            @if($byRegion->isEmpty())
                                <div class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-2x mb-2"></i>
                                    <p class="mb-0">Ma'lumot mavjud emas</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Age & Education Charts Row -->
        <div class="row g-3">
            <div class="col-lg-6">
                <div class="chart-card">
                    <div class="chart-header">
                        <i class="fas fa-birthday-cake me-2"></i> Yosh guruhlari
                    </div>
                    <div class="chart-body">
                        <canvas id="ageChart" height="200"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="chart-card">
                    <div class="chart-header">
                        <i class="fas fa-graduation-cap me-2"></i> Ta'lim turi
                    </div>
                    <div class="chart-body">
                        <canvas id="educationChart" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Tab switching
function switchChartTab(btn, paneId) {
    const parent = btn.closest('.chart-card');
    parent.querySelectorAll('.chart-tab').forEach(t => t.classList.remove('active'));
    parent.querySelectorAll('.chart-pane').forEach(p => p.classList.remove('active'));
    btn.classList.add('active');
    document.getElementById(paneId).classList.add('active');
}

document.addEventListener('DOMContentLoaded', function() {
    // Animate total number
    const totalEl = document.getElementById('totalApplications');
    const target = parseInt(totalEl.textContent);
    let current = 0;
    const increment = Math.max(1, target / 30);
    const timer = setInterval(() => {
        current += increment;
        if (current >= target) {
            current = target;
            clearInterval(timer);
        }
        totalEl.textContent = Math.floor(current);
    }, 30);

    // Chart.js defaults
    Chart.defaults.font.family = 'Inter, sans-serif';
    Chart.defaults.font.size = 12;

    // Status Chart (Doughnut)
    new Chart(document.getElementById('statusChart').getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: ['Kutilmoqda', 'Ko\'rilmoqda', 'Qabul', 'Rad', 'Kutish'],
            datasets: [{
                data: [
                    {{ $byStatus->where('status', 'pending')->first()->count ?? 0 }},
                    {{ $byStatus->where('status', 'reviewing')->first()->count ?? 0 }},
                    {{ $byStatus->where('status', 'accepted')->first()->count ?? 0 }},
                    {{ $byStatus->where('status', 'rejected')->first()->count ?? 0 }},
                    {{ $byStatus->where('status', 'waitlist')->first()->count ?? 0 }}
                ],
                backgroundColor: ['#fbbf24', '#60a5fa', '#10b981', '#ef4444', '#8b5cf6'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '60%',
            plugins: {
                legend: { position: 'right', labels: { boxWidth: 12, padding: 10 } }
            }
        }
    });

    // Daily Chart (Line)
    new Chart(document.getElementById('dailyChart').getContext('2d'), {
        type: 'line',
        data: {
            labels: {!! json_encode($dailyApplications->pluck('date')->map(fn($d) => \Carbon\Carbon::parse($d)->format('d.m'))) !!},
            datasets: [{
                label: 'Arizalar',
                data: {!! json_encode($dailyApplications->pluck('count')) !!},
                borderColor: '#0066CC',
                backgroundColor: 'rgba(0, 102, 204, 0.1)',
                tension: 0.4,
                fill: true,
                pointRadius: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1 } },
                x: { ticks: { maxRotation: 45, minRotation: 45 } }
            }
        }
    });

    // Age Chart (Bar)
    new Chart(document.getElementById('ageChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: {!! json_encode($byAge->pluck('age_group')) !!},
            datasets: [{
                label: 'Soni',
                data: {!! json_encode($byAge->pluck('count')) !!},
                backgroundColor: '#0066CC',
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { y: { beginAtZero: true } }
        }
    });

    // Education Chart (Pie)
    new Chart(document.getElementById('educationChart').getContext('2d'), {
        type: 'pie',
        data: {
            labels: {!! json_encode($byEducation->pluck('education_type')->map(function($type) {
                return ['school' => 'Maktab', 'college' => 'Kollej', 'lyceum' => 'Litsey', 'bachelor' => 'Bakalavr'][$type] ?? $type;
            })) !!},
            datasets: [{
                data: {!! json_encode($byEducation->pluck('count')) !!},
                backgroundColor: ['#0066CC', '#3b82f6', '#60a5fa', '#93c5fd'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'right', labels: { boxWidth: 12, padding: 10 } } }
        }
    });
});
</script>
@endsection
