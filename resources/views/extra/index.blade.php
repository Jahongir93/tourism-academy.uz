@extends('layouts.dashboard-new')

@section('title', "Qo'shimcha imkoniyatlar")
@section('page-title', "Qo'shimcha imkoniyatlar")

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h1 class="page-header-title">Qo'shimcha imkoniyatlar</h1>
        <p class="page-header-sub">Moliya, statistika va hisobotlar modullari</p>
    </div>
</div>

<div class="row g-4">

    {{-- Moliya --}}
    @canany(['view_finance','manage_finance'])
    <div class="col-lg-4 col-md-6">
        <div class="extra-module-card" style="--accent:#10B981;--accent-bg:rgba(16,185,129,.08);--accent-border:rgba(16,185,129,.18);">
            <div class="emc-header">
                <div class="emc-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <div class="emc-title">Moliya</div>
                    <div class="emc-sub">To'lovlar va moliyaviy hisobotlar</div>
                </div>
            </div>
            <div class="emc-links">
                <a href="{{ route('finance.dashboard') }}" class="emc-link">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
                <a href="{{ route('finance.payments.index') }}" class="emc-link">
                    <i class="fas fa-money-bill-wave"></i> To'lovlar
                </a>
                <a href="{{ route('finance.scholarships.index') }}" class="emc-link">
                    <i class="fas fa-graduation-cap"></i> Grant / Stipendiya
                </a>
                <a href="{{ route('finance.transactions') }}" class="emc-link">
                    <i class="fas fa-exchange-alt"></i> Tranzaksiyalar
                </a>
                <a href="{{ route('finance.reports') }}" class="emc-link">
                    <i class="fas fa-chart-line"></i> Moliyaviy hisobotlar
                </a>
            </div>
            <a href="{{ route('finance.dashboard') }}" class="emc-btn">
                Moliyaga kirish <i class="fas fa-arrow-right ms-1"></i>
            </a>
        </div>
    </div>
    @endcanany

    {{-- Statistika --}}
    @can('view_statistics')
    <div class="col-lg-4 col-md-6">
        <div class="extra-module-card" style="--accent:#F43F5E;--accent-bg:rgba(244,63,94,.08);--accent-border:rgba(244,63,94,.18);">
            <div class="emc-header">
                <div class="emc-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <div>
                    <div class="emc-title">Statistika</div>
                    <div class="emc-sub">Tahliliy ko'rsatkichlar va grafiklar</div>
                </div>
            </div>
            <div class="emc-links">
                <a href="{{ route('statistics.index') }}" class="emc-link">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
                <a href="{{ route('statistics.finance') }}" class="emc-link">
                    <i class="fas fa-chart-line"></i> Moliya statistikasi
                </a>
                <a href="{{ route('statistics.academic') }}" class="emc-link">
                    <i class="fas fa-graduation-cap"></i> O'quv statistikasi
                </a>
                <a href="{{ route('statistics.comparison') }}" class="emc-link">
                    <i class="fas fa-balance-scale"></i> Taqqoslash
                </a>
            </div>
            <a href="{{ route('statistics.index') }}" class="emc-btn">
                Statistikaga kirish <i class="fas fa-arrow-right ms-1"></i>
            </a>
        </div>
    </div>
    @endcan

    {{-- Hisobotlar --}}
    @can('view_reports')
    <div class="col-lg-4 col-md-6">
        <div class="extra-module-card" style="--accent:#F59E0B;--accent-bg:rgba(245,158,11,.08);--accent-border:rgba(245,158,11,.18);">
            <div class="emc-header">
                <div class="emc-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 17v1a1 1 0 001 1h4a1 1 0 001-1v-1m3-2V8a2 2 0 00-2-2H8a2 2 0 00-2 2v7m3-2h6l2 2H7l2-2z"/>
                    </svg>
                </div>
                <div>
                    <div class="emc-title">Hisobotlar</div>
                    <div class="emc-sub">Batafsil hisobot va eksport</div>
                </div>
            </div>
            <div class="emc-links">
                <a href="{{ route('reports.index') }}" class="emc-link">
                    <i class="fas fa-home"></i> Bosh sahifa
                </a>
                <a href="{{ route('reports.students') }}" class="emc-link">
                    <i class="fas fa-user-graduate"></i> Talabalar hisoboti
                </a>
                <a href="{{ route('reports.finance') }}" class="emc-link">
                    <i class="fas fa-dollar-sign"></i> Moliya hisoboti
                </a>
                <a href="{{ route('reports.academic') }}" class="emc-link">
                    <i class="fas fa-book"></i> O'quv hisoboti
                </a>
                <a href="{{ route('reports.attendance') }}" class="emc-link">
                    <i class="fas fa-calendar-check"></i> Davomat hisoboti
                </a>
            </div>
            <a href="{{ route('reports.index') }}" class="emc-btn">
                Hisobotlarga kirish <i class="fas fa-arrow-right ms-1"></i>
            </a>
        </div>
    </div>
    @endcan

</div>
@endsection

@section('styles')
<style>
.extra-module-card {
    background: var(--c-bg-card);
    border: 1px solid var(--accent-border, var(--c-border));
    border-radius: var(--r-xl);
    overflow: hidden;
    box-shadow: var(--sh-sm);
    transition: var(--tr-slow);
    display: flex;
    flex-direction: column;
    height: 100%;
}
.extra-module-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--sh-lg);
    border-color: var(--accent);
}

.emc-header {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 22px 22px 16px;
    border-bottom: 1px solid var(--accent-border, var(--c-border));
    background: var(--accent-bg, var(--c-bg));
}
.emc-icon {
    width: 48px;
    height: 48px;
    border-radius: var(--r-md);
    background: var(--accent);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.emc-icon svg { width: 24px; height: 24px; }
.emc-title {
    font-size: 17px;
    font-weight: 700;
    color: var(--c-text);
    line-height: 1.2;
}
.emc-sub {
    font-size: 12px;
    color: var(--c-text-3);
    margin-top: 2px;
}

.emc-links {
    flex: 1;
    padding: 16px 22px;
    display: flex;
    flex-direction: column;
    gap: 4px;
}
.emc-link {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 9px 12px;
    border-radius: var(--r-sm);
    font-size: 13px;
    font-weight: 500;
    color: var(--c-text-2);
    text-decoration: none;
    transition: var(--tr-fast);
}
.emc-link i {
    width: 16px;
    text-align: center;
    font-size: 13px;
    color: var(--accent);
    flex-shrink: 0;
}
.emc-link:hover {
    background: var(--accent-bg, var(--c-bg));
    color: var(--accent);
    padding-left: 16px;
}

.emc-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 22px 22px;
    padding: 11px;
    background: var(--accent);
    color: #fff;
    border-radius: var(--r-sm);
    font-size: 13px;
    font-weight: 600;
    text-decoration: none;
    transition: var(--tr-fast);
}
.emc-btn:hover {
    filter: brightness(.9);
    color: #fff;
    box-shadow: 0 4px 14px rgba(0,0,0,.18);
}
</style>
@endsection
