@extends('layouts.dashboard-new')

@section('title', 'Tashkiliy tuzilma — HEMIS')
@section('page-title', 'Universitet tashkiliy tuzilmasi')

@section('styles')
<style>
    /* Container */
    .org-container {
        background: var(--c-surface);
        border-radius: 14px;
        padding: 28px;
        border: 1px solid var(--c-border);
        margin: 0 0 24px;
    }

    /* View Toggle */
    .view-controls {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 28px;
        padding-bottom: 18px;
        border-bottom: 1px solid var(--c-border);
    }

    .view-toggle {
        display: flex;
        gap: 6px;
        background: var(--c-bg);
        padding: 4px;
        border-radius: 10px;
        border: 1px solid var(--c-border);
    }

    .view-toggle button {
        padding: 8px 18px;
        border: none;
        background: transparent;
        color: var(--c-text-2);
        border-radius: 7px;
        cursor: pointer;
        font-weight: 600;
        font-size: 13px;
        transition: all 0.2s;
    }

    .view-toggle button.active {
        background: var(--c-emerald);
        color: white;
    }

    /* Chart View */
    .chart-view {
        display: block;
        padding: 40px 20px;
        overflow-x: auto;
        background: var(--c-bg);
        border-radius: 10px;
        min-height: 600px;
        border: 1px solid var(--c-border);
    }

    .chart-view.hidden { display: none; }

    .org-chart {
        width: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        min-width: 1100px;
    }

    .tree {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .level {
        display: flex;
        justify-content: center;
        position: relative;
        width: 100%;
        margin: 36px 0;
    }

    /* Nodes */
    .node {
        background: var(--c-surface);
        border: 2px solid var(--c-border);
        border-radius: 10px;
        padding: 18px 20px;
        min-width: 180px;
        text-align: center;
        position: relative;
        cursor: pointer;
        transition: all 0.2s;
        box-shadow: 0 2px 8px rgba(0,0,0,.06);
        margin: 0 12px;
    }

    .node:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 16px rgba(0,0,0,.1);
        border-color: var(--c-emerald);
    }

    .node.rector {
        background: var(--c-emerald);
        color: white;
        border-color: var(--c-emerald);
        min-width: 280px;
        box-shadow: 0 4px 16px rgba(16,185,129,.3);
    }

    .node.rector:hover {
        box-shadow: 0 8px 24px rgba(16,185,129,.4);
        transform: translateY(-3px);
    }

    .node.prorector {
        background: rgba(20,184,166,.12);
        border-color: var(--c-teal);
        min-width: 200px;
    }

    .node.prorector:hover {
        background: rgba(20,184,166,.2);
    }

    .node.faculty {
        border-color: var(--c-emerald);
        background: var(--c-surface);
    }

    .node.department {
        background: rgba(16,185,129,.06);
        border-color: rgba(16,185,129,.3);
        min-width: 160px;
    }

    .node.center {
        background: rgba(14,165,233,.08);
        border-color: rgba(14,165,233,.3);
    }

    /* Node Content */
    .node-icon {
        font-size: 22px;
        margin-bottom: 8px;
        opacity: 0.85;
    }

    .node-title {
        font-size: 13px;
        font-weight: 700;
        margin-bottom: 4px;
        text-transform: uppercase;
        letter-spacing: 0.4px;
    }

    .node-subtitle {
        font-size: 12px;
        opacity: 0.85;
        margin-bottom: 4px;
    }

    .node-info {
        font-size: 11px;
        opacity: 0.75;
    }

    /* Connectors */
    .v-connector {
        width: 2px;
        background: var(--c-border);
        position: absolute;
        left: 50%;
        transform: translateX(-50%);
    }

    .v-connector::after {
        content: '';
        position: absolute;
        bottom: -6px;
        left: 50%;
        transform: translateX(-50%);
        width: 0;
        height: 0;
        border-left: 5px solid transparent;
        border-right: 5px solid transparent;
        border-top: 5px solid var(--c-border);
    }

    .h-connector {
        position: absolute;
        height: 2px;
        background: var(--c-border);
        top: -20px;
        z-index: -1;
    }

    .branch {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .branch-group {
        display: flex;
        gap: 28px;
        position: relative;
        padding-top: 40px;
    }

    .branch-group::before {
        content: '';
        position: absolute;
        top: 0;
        left: 10%;
        right: 10%;
        height: 2px;
        background: var(--c-border);
    }

    .branch-item { position: relative; }

    .branch-item::before {
        content: '';
        position: absolute;
        top: -40px;
        left: 50%;
        width: 2px;
        height: 40px;
        background: var(--c-border);
        transform: translateX(-50%);
    }

    .branch-label {
        padding: 6px 18px;
        margin-bottom: 16px;
        font-weight: 700;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        border-radius: 6px;
    }

    /* List View */
    .list-view { display: none; padding: 16px; }
    .list-view.active { display: block; }

    .list-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 18px;
    }

    .list-card {
        background: var(--c-surface);
        border: 1px solid var(--c-border);
        border-radius: 12px;
        padding: 18px;
        transition: all 0.2s;
    }

    .list-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(0,0,0,.08);
        border-color: var(--c-emerald);
    }

    .list-header {
        display: flex;
        align-items: center;
        gap: 12px;
        padding-bottom: 14px;
        border-bottom: 1px solid var(--c-border);
        margin-bottom: 14px;
    }

    .list-icon {
        width: 44px;
        height: 44px;
        background: rgba(16,185,129,.12);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--c-emerald);
        font-size: 18px;
    }

    .list-title {
        font-size: 16px;
        font-weight: 700;
        color: var(--c-text);
    }

    .list-item {
        padding: 9px 12px;
        background: var(--c-bg);
        border-radius: 8px;
        margin-bottom: 6px;
        cursor: pointer;
        transition: all 0.2s;
        border: 1px solid transparent;
    }

    .list-item:hover {
        border-color: var(--c-border);
        background: var(--c-surface);
        transform: translateX(4px);
    }

    .list-item-title {
        font-weight: 600;
        color: var(--c-text);
        font-size: 13px;
        margin-bottom: 2px;
    }

    .list-item-info {
        font-size: 12px;
        color: var(--c-text-3);
    }

    @media (max-width: 768px) {
        .org-chart { min-width: auto; }
        .node { min-width: 130px; padding: 12px 10px; margin: 0 5px; }
        .branch-group { flex-direction: column; gap: 16px; }
    }
</style>
@endsection

@section('content')

{{-- Stat cards --}}
<div class="row g-4 mb-4">
    <div class="col-6 col-xl-2">
        <div class="stat-card" style="color:var(--c-emerald)">
            <div class="stat-card-icon" style="background:rgba(16,185,129,.12);color:var(--c-emerald)">
                <i class="fas fa-university"></i>
            </div>
            <div class="stat-card-value" style="color:var(--c-text)">{{ $statistics['faculties'] ?? 12 }}</div>
            <div class="stat-card-label">Fakultetlar</div>
        </div>
    </div>
    <div class="col-6 col-xl-2">
        <div class="stat-card" style="color:var(--c-teal)">
            <div class="stat-card-icon" style="background:rgba(20,184,166,.12);color:var(--c-teal)">
                <i class="fas fa-building"></i>
            </div>
            <div class="stat-card-value" style="color:var(--c-text)">{{ $statistics['departments'] ?? 48 }}</div>
            <div class="stat-card-label">Kafedralar</div>
        </div>
    </div>
    <div class="col-6 col-xl-2">
        <div class="stat-card" style="color:var(--c-sky)">
            <div class="stat-card-icon" style="background:rgba(14,165,233,.12);color:var(--c-sky)">
                <i class="fas fa-briefcase"></i>
            </div>
            <div class="stat-card-value" style="color:var(--c-text)">{{ $statistics['divisions'] ?? 24 }}</div>
            <div class="stat-card-label">Bo'limlar</div>
        </div>
    </div>
    <div class="col-6 col-xl-2">
        <div class="stat-card" style="color:var(--c-violet)">
            <div class="stat-card-icon" style="background:rgba(124,58,237,.12);color:var(--c-violet)">
                <i class="fas fa-flask"></i>
            </div>
            <div class="stat-card-value" style="color:var(--c-text)">{{ $statistics['centers'] ?? 8 }}</div>
            <div class="stat-card-label">Markazlar</div>
        </div>
    </div>
    <div class="col-6 col-xl-2">
        <div class="stat-card" style="color:var(--c-amber)">
            <div class="stat-card-icon" style="background:rgba(245,158,11,.12);color:var(--c-amber)">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-card-value" style="color:var(--c-text)">{{ number_format($statistics['staff'] ?? 1250) }}</div>
            <div class="stat-card-label">Xodimlar</div>
        </div>
    </div>
    <div class="col-6 col-xl-2">
        <div class="stat-card" style="color:var(--c-rose)">
            <div class="stat-card-icon" style="background:rgba(244,63,94,.12);color:var(--c-rose)">
                <i class="fas fa-user-tie"></i>
            </div>
            <div class="stat-card-value" style="color:var(--c-text)">{{ $statistics['leaders'] ?? 85 }}</div>
            <div class="stat-card-label">Rahbarlar</div>
        </div>
    </div>
</div>

{{-- Main Container --}}
<div class="org-container">
    <div class="view-controls">
        <div class="view-toggle">
            <button class="active" onclick="toggleView('chart')">
                <i class="fas fa-sitemap me-2"></i>Diagramma
            </button>
            <button onclick="toggleView('list')">
                <i class="fas fa-list me-2"></i>Ro'yxat
            </button>
        </div>
        <button class="btn btn-outline-secondary btn-sm" onclick="window.print()">
            <i class="fas fa-file-pdf me-1" style="color:var(--c-rose)"></i>Export PDF
        </button>
    </div>

    {{-- Chart View --}}
    <div class="chart-view" id="chartView">
        <div class="org-chart">
            <div class="tree">

                {{-- Level 1: Rector --}}
                <div class="level">
                    <div class="node rector">
                        <div class="node-icon"><i class="fas fa-crown"></i></div>
                        <div class="node-title">REKTOR</div>
                        <div class="node-subtitle">Prof. Ibrohim Abdullayev</div>
                        <div class="node-info"><i class="fas fa-phone me-1"></i>+998 71 227-1224</div>
                    </div>
                </div>

                <div class="v-connector" style="height:40px;top:-20px"></div>

                {{-- Level 2: Prorectors --}}
                <div class="level">
                    <div class="h-connector" style="left:15%;right:15%"></div>
                    <div class="branch-group">
                        <div class="branch-item">
                            <div class="node prorector">
                                <div class="node-icon" style="color:var(--c-teal)"><i class="fas fa-graduation-cap"></i></div>
                                <div class="node-title" style="color:var(--c-text)">O'QUV ISHLARI</div>
                                <div class="node-subtitle" style="color:var(--c-text-2)">Prorektor</div>
                                <div class="node-info" style="color:var(--c-text-3)">S.Karimov</div>
                            </div>
                        </div>
                        <div class="branch-item">
                            <div class="node prorector">
                                <div class="node-icon" style="color:var(--c-teal)"><i class="fas fa-microscope"></i></div>
                                <div class="node-title" style="color:var(--c-text)">ILMIY ISHLAR</div>
                                <div class="node-subtitle" style="color:var(--c-text-2)">Prorektor</div>
                                <div class="node-info" style="color:var(--c-text-3)">A.Rahimov</div>
                            </div>
                        </div>
                        <div class="branch-item">
                            <div class="node prorector">
                                <div class="node-icon" style="color:var(--c-teal)"><i class="fas fa-coins"></i></div>
                                <div class="node-title" style="color:var(--c-text)">MOLIYA</div>
                                <div class="node-subtitle" style="color:var(--c-text-2)">Prorektor</div>
                                <div class="node-info" style="color:var(--c-text-3)">M.Normatov</div>
                            </div>
                        </div>
                        <div class="branch-item">
                            <div class="node prorector">
                                <div class="node-icon" style="color:var(--c-teal)"><i class="fas fa-users"></i></div>
                                <div class="node-title" style="color:var(--c-text)">YOSHLAR</div>
                                <div class="node-subtitle" style="color:var(--c-text-2)">Prorektor</div>
                                <div class="node-info" style="color:var(--c-text-3)">G.Azimova</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Level 3: Divisions --}}
                <div class="level" style="margin-top:50px">
                    <div class="branch-group">

                        {{-- Faculties --}}
                        <div class="branch">
                            <div class="branch-label" style="background:rgba(16,185,129,.12);color:var(--c-emerald)">
                                Fakultetlar
                            </div>
                            <div style="display:flex;gap:12px;flex-wrap:wrap;justify-content:center">
                                <div class="node faculty">
                                    <div class="node-icon" style="color:var(--c-emerald)"><i class="fas fa-laptop"></i></div>
                                    <div class="node-title" style="color:var(--c-text)">IT</div>
                                    <div class="node-info" style="color:var(--c-text-3)">5 kafedra</div>
                                </div>
                                <div class="node faculty">
                                    <div class="node-icon" style="color:var(--c-emerald)"><i class="fas fa-calculator"></i></div>
                                    <div class="node-title" style="color:var(--c-text)">MATEMATIKA</div>
                                    <div class="node-info" style="color:var(--c-text-3)">4 kafedra</div>
                                </div>
                                <div class="node faculty">
                                    <div class="node-icon" style="color:var(--c-emerald)"><i class="fas fa-atom"></i></div>
                                    <div class="node-title" style="color:var(--c-text)">FIZIKA</div>
                                    <div class="node-info" style="color:var(--c-text-3)">6 kafedra</div>
                                </div>
                            </div>
                        </div>

                        {{-- Departments --}}
                        <div class="branch">
                            <div class="branch-label" style="background:rgba(20,184,166,.12);color:var(--c-teal)">
                                Bo'limlar
                            </div>
                            <div style="display:flex;gap:12px;flex-wrap:wrap;justify-content:center">
                                <div class="node department">
                                    <div class="node-icon" style="color:var(--c-teal)"><i class="fas fa-book"></i></div>
                                    <div class="node-title" style="color:var(--c-text)">O'QUV</div>
                                </div>
                                <div class="node department">
                                    <div class="node-icon" style="color:var(--c-teal)"><i class="fas fa-user-tie"></i></div>
                                    <div class="node-title" style="color:var(--c-text)">KADRLAR</div>
                                </div>
                                <div class="node department">
                                    <div class="node-icon" style="color:var(--c-teal)"><i class="fas fa-wallet"></i></div>
                                    <div class="node-title" style="color:var(--c-text)">MOLIYA</div>
                                </div>
                            </div>
                        </div>

                        {{-- Centers --}}
                        <div class="branch">
                            <div class="branch-label" style="background:rgba(14,165,233,.12);color:var(--c-sky)">
                                Markazlar
                            </div>
                            <div style="display:flex;gap:12px;flex-wrap:wrap;justify-content:center">
                                <div class="node center">
                                    <div class="node-icon" style="color:var(--c-sky)"><i class="fas fa-server"></i></div>
                                    <div class="node-title" style="color:var(--c-text)">IT</div>
                                </div>
                                <div class="node center">
                                    <div class="node-icon" style="color:var(--c-sky)"><i class="fas fa-globe"></i></div>
                                    <div class="node-title" style="color:var(--c-text)">TIL</div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- List View --}}
    <div class="list-view" id="listView">
        <div class="list-grid">
            <div class="list-card">
                <div class="list-header">
                    <div class="list-icon"><i class="fas fa-university"></i></div>
                    <div>
                        <div class="list-title">Fakultetlar</div>
                        <div style="color:var(--c-text-3);font-size:13px">12 ta fakultet</div>
                    </div>
                </div>
                <div>
                    <div class="list-item">
                        <div class="list-item-title">Axborot texnologiyalari</div>
                        <div class="list-item-info">Dekan: A.Karimov &bull; 5 kafedra</div>
                    </div>
                    <div class="list-item">
                        <div class="list-item-title">Matematika</div>
                        <div class="list-item-info">Dekan: B.Saidov &bull; 4 kafedra</div>
                    </div>
                    <div class="list-item">
                        <div class="list-item-title">Fizika</div>
                        <div class="list-item-info">Dekan: D.Alimov &bull; 6 kafedra</div>
                    </div>
                    <div class="list-item">
                        <div class="list-item-title">Iqtisodiyot</div>
                        <div class="list-item-info">Dekan: M.Toshev &bull; 5 kafedra</div>
                    </div>
                </div>
            </div>

            <div class="list-card">
                <div class="list-header">
                    <div class="list-icon" style="background:rgba(20,184,166,.12);color:var(--c-teal)">
                        <i class="fas fa-briefcase"></i>
                    </div>
                    <div>
                        <div class="list-title">Bo'limlar</div>
                        <div style="color:var(--c-text-3);font-size:13px">24 ta bo'lim</div>
                    </div>
                </div>
                <div>
                    <div class="list-item">
                        <div class="list-item-title">O'quv bo'limi</div>
                        <div class="list-item-info">Boshlig': S.Rahmonov</div>
                    </div>
                    <div class="list-item">
                        <div class="list-item-title">Kadrlar bo'limi</div>
                        <div class="list-item-info">Boshlig': N.Qosimova</div>
                    </div>
                    <div class="list-item">
                        <div class="list-item-title">Moliya bo'limi</div>
                        <div class="list-item-info">Boshlig': R.Tursunov</div>
                    </div>
                    <div class="list-item">
                        <div class="list-item-title">Xalqaro aloqalar</div>
                        <div class="list-item-info">Boshlig': K.Aliyev</div>
                    </div>
                </div>
            </div>

            <div class="list-card">
                <div class="list-header">
                    <div class="list-icon" style="background:rgba(14,165,233,.12);color:var(--c-sky)">
                        <i class="fas fa-flask"></i>
                    </div>
                    <div>
                        <div class="list-title">Markazlar</div>
                        <div style="color:var(--c-text-3);font-size:13px">8 ta markaz</div>
                    </div>
                </div>
                <div>
                    <div class="list-item">
                        <div class="list-item-title">IT markazi</div>
                        <div class="list-item-info">Direktor: J.Yuldoshev</div>
                    </div>
                    <div class="list-item">
                        <div class="list-item-title">Til o'rgatish markazi</div>
                        <div class="list-item-info">Direktor: N.Qobilova</div>
                    </div>
                    <div class="list-item">
                        <div class="list-item-title">Ilmiy tadqiqot markazi</div>
                        <div class="list-item-info">Direktor: O.Rahmonov</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleView(view) {
    const chartView = document.getElementById('chartView');
    const listView  = document.getElementById('listView');
    const buttons   = document.querySelectorAll('.view-toggle button');
    buttons.forEach(b => b.classList.remove('active'));
    if (view === 'chart') {
        chartView.classList.remove('hidden');
        listView.classList.remove('active');
        buttons[0].classList.add('active');
    } else {
        chartView.classList.add('hidden');
        listView.classList.add('active');
        buttons[1].classList.add('active');
    }
}

document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.node').forEach(node => {
        node.addEventListener('click', function () {
            this.style.animation = 'nodeClick 0.25s';
            setTimeout(() => { this.style.animation = ''; }, 250);
        });
    });
});
</script>

<style>
@keyframes nodeClick {
    0%   { transform: scale(1); }
    50%  { transform: scale(0.96); }
    100% { transform: scale(1); }
}
</style>

@endsection
