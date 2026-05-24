@extends('layouts.dashboard-new')

@section('title', 'Baholar - HEMIS')

@section('page-title', 'Baholar boshqaruvi')

@section('styles')
<style>
    :root {
        --primary-dark-green: #0d4f3c;
        --secondary-green: #16a085;
        --light-green: #e8f5f0;
        --accent-green: #48c9b0;
        --border-green: #c3e6d8;
        --text-dark: #2c3e50;
        --hover-green: #0a3d2e;
        --very-light-green: #f0f9f6;
    }

    .stat-card {
        transition: all 0.3s ease;
        border: 1px solid var(--border-green) !important;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(13, 79, 60, 0.2) !important;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4 p-4 rounded-lg" style="background: linear-gradient(135deg, var(--primary-dark-green) 0%, var(--secondary-green) 100%);">
        <div class="col-md-8">
            <h1 class="h2 text-white">Baholar boshqaruvi</h1>
            <p class="text-white opacity-90">
                Barcha fanlar bo'yicha baholar ma'lumotlari
            </p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('journal.index') }}" class="btn text-white"
               style="background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.3);"
               onmouseover="this.style.background='rgba(255,255,255,0.3)'"
               onmouseout="this.style.background='rgba(255,255,255,0.2)'">
                <i class="fas fa-book me-2"></i> Jurnallar
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm stat-card" style="background: linear-gradient(135deg, var(--primary-dark-green), var(--secondary-green));">
                <div class="card-body text-white">
                    <div class="d-flex align-items-center mb-2">
                        <div class="rounded-circle p-2" style="background: rgba(255,255,255,0.2);">
                            <i class="fas fa-star"></i>
                        </div>
                    </div>
                    <h3 class="mb-0">{{ number_format($statistics['total_grades']) }}</h3>
                    <p class="mb-0 opacity-90">Jami baholar</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm stat-card" style="background: linear-gradient(135deg, var(--secondary-green), var(--accent-green));">
                <div class="card-body text-white">
                    <div class="d-flex align-items-center mb-2">
                        <div class="rounded-circle p-2" style="background: rgba(255,255,255,0.2);">
                            <i class="fas fa-chart-line"></i>
                        </div>
                    </div>
                    <h3 class="mb-0">{{ number_format($statistics['average_score'], 1) }}</h3>
                    <p class="mb-0 opacity-90">O'rtacha ball</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="rounded-circle p-2" style="background: var(--light-green);">
                            <i class="fas fa-trophy" style="color: var(--primary-dark-green);"></i>
                        </div>
                    </div>
                    <h3 class="mb-0" style="color: var(--primary-dark-green);">{{ number_format($statistics['excellent_count']) }}</h3>
                    <p class="mb-0" style="color: #7f8c8d;">A'lo (86-100)</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm stat-card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="rounded-circle p-2" style="background: #fef0f0;">
                            <i class="fas fa-exclamation-triangle" style="color: #dc3545;"></i>
                        </div>
                    </div>
                    <h3 class="mb-0" style="color: #dc3545;">{{ number_format($statistics['unsatisfactory_count']) }}</h3>
                    <p class="mb-0" style="color: #7f8c8d;">Qoniqarsiz (<56)</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Grade Distribution -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm" style="border: 1px solid var(--border-green) !important;">
                <div class="card-header" style="background: var(--light-green); border-bottom: 2px solid var(--border-green);">
                    <h5 class="mb-0" style="color: var(--text-dark); font-weight: 600;">
                        <i class="fas fa-chart-pie" style="color: var(--secondary-green);"></i>
                        Baholar taqsimoti
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3">
                            <div class="p-3 rounded" style="background: linear-gradient(135deg, var(--very-light-green), white); border: 2px solid var(--border-green);">
                                <h6 style="color: var(--primary-dark-green); font-weight: 600;">A'lo (86-100)</h6>
                                <h3 style="color: var(--primary-dark-green);">{{ $statistics['excellent_count'] }}</h3>
                                <div class="progress" style="height: 8px; background: var(--light-green);">
                                    <div class="progress-bar" role="progressbar"
                                         style="width: {{ $statistics['total_grades'] > 0 ? round($statistics['excellent_count'] / $statistics['total_grades'] * 100) : 0 }}%; background: var(--primary-dark-green);"></div>
                                </div>
                                <small style="color: #7f8c8d;">
                                    {{ $statistics['total_grades'] > 0 ? round($statistics['excellent_count'] / $statistics['total_grades'] * 100, 1) : 0 }}%
                                </small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3 rounded" style="background: linear-gradient(135deg, var(--very-light-green), white); border: 2px solid var(--border-green);">
                                <h6 style="color: var(--secondary-green); font-weight: 600;">Yaxshi (71-85)</h6>
                                <h3 style="color: var(--secondary-green);">{{ $statistics['good_count'] }}</h3>
                                <div class="progress" style="height: 8px; background: var(--light-green);">
                                    <div class="progress-bar" role="progressbar"
                                         style="width: {{ $statistics['total_grades'] > 0 ? round($statistics['good_count'] / $statistics['total_grades'] * 100) : 0 }}%; background: var(--secondary-green);"></div>
                                </div>
                                <small style="color: #7f8c8d;">
                                    {{ $statistics['total_grades'] > 0 ? round($statistics['good_count'] / $statistics['total_grades'] * 100, 1) : 0 }}%
                                </small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3 rounded" style="background: linear-gradient(135deg, #fff3cd, white); border: 2px solid #ffc107;">
                                <h6 style="color: #f39c12; font-weight: 600;">Qoniqarli (56-70)</h6>
                                <h3 style="color: #f39c12;">{{ $statistics['satisfactory_count'] }}</h3>
                                <div class="progress" style="height: 8px; background: #fff3cd;">
                                    <div class="progress-bar" role="progressbar"
                                         style="width: {{ $statistics['total_grades'] > 0 ? round($statistics['satisfactory_count'] / $statistics['total_grades'] * 100) : 0 }}%; background: #f39c12;"></div>
                                </div>
                                <small style="color: #7f8c8d;">
                                    {{ $statistics['total_grades'] > 0 ? round($statistics['satisfactory_count'] / $statistics['total_grades'] * 100, 1) : 0 }}%
                                </small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="p-3 rounded" style="background: linear-gradient(135deg, #fef0f0, white); border: 2px solid #dc3545;">
                                <h6 style="color: #dc3545; font-weight: 600;">Qoniqarsiz (<56)</h6>
                                <h3 style="color: #dc3545;">{{ $statistics['unsatisfactory_count'] }}</h3>
                                <div class="progress" style="height: 8px; background: #fef0f0;">
                                    <div class="progress-bar" role="progressbar"
                                         style="width: {{ $statistics['total_grades'] > 0 ? round($statistics['unsatisfactory_count'] / $statistics['total_grades'] * 100) : 0 }}%; background: #dc3545;"></div>
                                </div>
                                <small style="color: #7f8c8d;">
                                    {{ $statistics['total_grades'] > 0 ? round($statistics['unsatisfactory_count'] / $statistics['total_grades'] * 100, 1) : 0 }}%
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Grades Table -->
    <div class="card border-0 shadow-sm" style="border: 1px solid var(--border-green) !important;">
        <div class="card-header" style="background: var(--light-green); border-bottom: 2px solid var(--border-green);">
            <h5 class="mb-0" style="color: var(--text-dark); font-weight: 600;">
                <i class="fas fa-list" style="color: var(--secondary-green);"></i>
                So'nggi baholar
            </h5>
        </div>
        <div class="card-body">
            @if($grades->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead style="background: var(--very-light-green);">
                            <tr>
                                <th style="color: var(--text-dark); font-weight: 600;">Fan</th>
                                <th style="color: var(--text-dark); font-weight: 600;">Guruh</th>
                                <th style="color: var(--text-dark); font-weight: 600;">Talaba</th>
                                <th style="color: var(--text-dark); font-weight: 600;">Baho turi</th>
                                <th style="color: var(--text-dark); font-weight: 600;">Ball</th>
                                <th style="color: var(--text-dark); font-weight: 600;">Baho</th>
                                <th style="color: var(--text-dark); font-weight: 600;">Baholandi</th>
                                <th style="color: var(--text-dark); font-weight: 600;">Harakatlar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($grades as $grade)
                            <tr onmouseover="this.style.background='var(--very-light-green)'" onmouseout="this.style.background='white'">
                                <td style="color: var(--text-dark); font-weight: 600;">{{ $grade->journalEntry->subject->name ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge" style="background: var(--light-green); color: var(--primary-dark-green);">
                                        {{ $grade->journalEntry->group->name ?? 'N/A' }}
                                    </span>
                                </td>
                                <td style="color: var(--text-dark);">{{ $grade->student->last_name ?? '' }} {{ $grade->student->first_name ?? '' }}</td>
                                <td>
                                    @if($grade->grade_type == 'current')
                                        <span class="badge" style="background: var(--secondary-green); color: white;">Joriy</span>
                                    @elseif($grade->grade_type == 'midterm')
                                        <span class="badge" style="background: var(--accent-green); color: white;">Oraliq</span>
                                    @else
                                        <span class="badge" style="background: var(--primary-dark-green); color: white;">Yakuniy</span>
                                    @endif
                                </td>
                                <td>
                                    <strong style="color: var(--text-dark); font-size: 16px;">{{ $grade->score }}</strong>
                                </td>
                                <td>
                                    @if($grade->score >= 86)
                                        <span class="badge" style="background: linear-gradient(135deg, var(--primary-dark-green), var(--secondary-green)); color: white;">A'lo</span>
                                    @elseif($grade->score >= 71)
                                        <span class="badge" style="background: var(--accent-green); color: white;">Yaxshi</span>
                                    @elseif($grade->score >= 56)
                                        <span class="badge" style="background: #ffc107; color: #333;">Qoniqarli</span>
                                    @else
                                        <span class="badge" style="background: #dc3545; color: white;">Qoniqarsiz</span>
                                    @endif
                                </td>
                                <td style="color: #7f8c8d;">{{ $grade->graded_at ? \Carbon\Carbon::parse($grade->graded_at)->format('d.m.Y') : 'N/A' }}</td>
                                <td>
                                    <a href="{{ route('journal.show', $grade->journal_entry_id) }}" class="btn btn-sm"
                                       style="border: 1px solid var(--secondary-green); color: var(--secondary-green);"
                                       onmouseover="this.style.background='var(--light-green)'"
                                       onmouseout="this.style.background='transparent'">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="mt-3">
                    {{ $grades->links() }}
                </div>
            @else
                <div class="alert" style="background: var(--light-green); border: 1px solid var(--border-green); color: var(--text-dark);">
                    <i class="fas fa-info-circle me-2" style="color: var(--secondary-green);"></i>
                    Hozircha baho yozuvlari mavjud emas.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection