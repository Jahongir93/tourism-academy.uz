@extends('layouts.dashboard-new')

@section('title', 'Baholar - ' . ($groupSubject->subject->name_uz ?? 'HEMIS'))

@section('page-title', 'Baholar jurnali')

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

    .grade-input {
        width: 60px;
        text-align: center;
        border: 1px solid var(--border-green);
        border-radius: 4px;
        padding: 4px 8px;
        transition: all 0.3s ease;
    }

    .grade-input:focus {
        border-color: var(--secondary-green);
        box-shadow: 0 0 0 0.2rem rgba(22, 160, 133, 0.25);
        outline: none;
    }

    .grade-cell {
        position: relative;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .grade-cell:hover {
        background: var(--light-green) !important;
    }

    .grade-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 12px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .grade-badge:hover {
        transform: scale(1.1);
    }

    .grade-excellent {
        background: linear-gradient(135deg, var(--primary-dark-green), var(--secondary-green));
        color: white;
    }

    .grade-good {
        background: var(--accent-green);
        color: white;
    }

    .grade-satisfactory {
        background: #ffc107;
        color: #333;
    }

    .grade-unsatisfactory {
        background: #dc3545;
        color: white;
    }

    .progress-bar-animated {
        animation: progress-animation 2s ease-in-out;
    }

    @keyframes progress-animation {
        from { width: 0; }
    }

    .student-row:hover {
        background: var(--very-light-green) !important;
    }

    #gradesTable th {
        background: var(--very-light-green);
        color: var(--text-dark);
        font-weight: 600;
        position: sticky;
        top: 0;
        z-index: 10;
    }
</style>
@endsection

@section('content')
@php
    // Compatibility: map groupSubject to journal variable
    $journal = $groupSubject ?? null;
@endphp
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4 p-4 rounded-lg" style="background: linear-gradient(135deg, var(--primary-dark-green) 0%, var(--secondary-green) 100%);">
        <div class="col-md-8">
            <h1 class="h2 text-white">Baholar jurnali</h1>
            <p class="text-white opacity-90">
                <strong>{{ $journal->subject->name_uz ?? 'Fan' }}</strong> |
                {{ $journal->studentGroup->name ?? 'Guruh' }} |
                {{ $journal->semester ?? '1' }}-semestr
            </p>
        </div>
        <div class="col-md-4 text-end">
            <button class="btn text-white" onclick="openGradeSettings()"
                    style="background: rgba(255,255,255,0.2); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.3);"
                    onmouseover="this.style.background='rgba(255,255,255,0.3)'"
                    onmouseout="this.style.background='rgba(255,255,255,0.2)'">
                <i class="fas fa-cog"></i> Sozlamalar
            </button>
        </div>
    </div>

    <!-- Journal Info -->
    <div class="card border-0 shadow-sm mb-4" style="border: 1px solid var(--border-green) !important;">
        <div class="card-body" style="background: linear-gradient(135deg, var(--very-light-green), white);">
            <div class="row">
                <div class="col-md-3">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle p-2 me-2" style="background: var(--light-green);">
                            <i class="fas fa-book" style="color: var(--primary-dark-green);"></i>
                        </div>
                        <div>
                            <small style="color: #7f8c8d;">Fan</small>
                            <div style="color: var(--text-dark); font-weight: 600;">{{ $journal->subject->name ?? 'N/A' }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle p-2 me-2" style="background: var(--light-green);">
                            <i class="fas fa-users" style="color: var(--secondary-green);"></i>
                        </div>
                        <div>
                            <small style="color: #7f8c8d;">Guruh</small>
                            <div style="color: var(--text-dark); font-weight: 600;">{{ $journal->group->name ?? 'N/A' }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle p-2 me-2" style="background: var(--light-green);">
                            <i class="fas fa-chalkboard-teacher" style="color: var(--accent-green);"></i>
                        </div>
                        <div>
                            <small style="color: #7f8c8d;">O'qituvchi</small>
                            <div style="color: var(--text-dark); font-weight: 600;">
                                {{ $journal->teacher->first_name ?? '' }} {{ $journal->teacher->last_name ?? 'N/A' }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle p-2 me-2" style="background: var(--light-green);">
                            <i class="fas fa-percentage" style="color: var(--primary-dark-green);"></i>
                        </div>
                        <div>
                            <small style="color: #7f8c8d;">Ball taqsimoti</small>
                            <div style="color: var(--text-dark); font-weight: 600;">JB: 30% | ON: 30% | YN: 40%</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm" style="border: 1px solid var(--border-green) !important; background: linear-gradient(135deg, var(--primary-dark-green), var(--secondary-green));">
                <div class="card-body text-white">
                    <div class="d-flex align-items-center mb-2">
                        <div class="rounded-circle p-2" style="background: rgba(255,255,255,0.2);">
                            <i class="fas fa-trophy"></i>
                        </div>
                    </div>
                    <h3 class="mb-0">{{ $statistics['excellent_count'] ?? 0 }}</h3>
                    <p class="mb-0 opacity-90">A'lo (86-100)</p>
                    <div class="progress mt-2" style="height: 5px; background: rgba(255,255,255,0.2);">
                        <div class="progress-bar progress-bar-animated" role="progressbar"
                             style="width: {{ ($statistics['excellent_count'] ?? 0) / max(($statistics['total_students'] ?? 1), 1) * 100 }}%; background: white;">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm" style="border: 1px solid var(--border-green) !important; background: linear-gradient(135deg, var(--secondary-green), var(--accent-green));">
                <div class="card-body text-white">
                    <div class="d-flex align-items-center mb-2">
                        <div class="rounded-circle p-2" style="background: rgba(255,255,255,0.2);">
                            <i class="fas fa-star"></i>
                        </div>
                    </div>
                    <h3 class="mb-0">{{ $statistics['good_count'] ?? 0 }}</h3>
                    <p class="mb-0 opacity-90">Yaxshi (71-85)</p>
                    <div class="progress mt-2" style="height: 5px; background: rgba(255,255,255,0.2);">
                        <div class="progress-bar progress-bar-animated" role="progressbar"
                             style="width: {{ ($statistics['good_count'] ?? 0) / max(($statistics['total_students'] ?? 1), 1) * 100 }}%; background: white;">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm" style="border: 1px solid var(--border-green) !important;">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="rounded-circle p-2" style="background: #fff3cd;">
                            <i class="fas fa-check" style="color: #f39c12;"></i>
                        </div>
                    </div>
                    <h3 class="mb-0" style="color: #f39c12;">{{ $statistics['satisfactory_count'] ?? 0 }}</h3>
                    <p class="mb-0" style="color: #7f8c8d;">Qoniqarli (56-70)</p>
                    <div class="progress mt-2" style="height: 5px; background: var(--light-green);">
                        <div class="progress-bar progress-bar-animated" role="progressbar"
                             style="width: {{ ($statistics['satisfactory_count'] ?? 0) / max(($statistics['total_students'] ?? 1), 1) * 100 }}%; background: #f39c12;">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm" style="border: 1px solid var(--border-green) !important;">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="rounded-circle p-2" style="background: #fef0f0;">
                            <i class="fas fa-times" style="color: #dc3545;"></i>
                        </div>
                    </div>
                    <h3 class="mb-0" style="color: #dc3545;">{{ $statistics['unsatisfactory_count'] ?? 0 }}</h3>
                    <p class="mb-0" style="color: #7f8c8d;">Qoniqarsiz (<56)</p>
                    <div class="progress mt-2" style="height: 5px; background: var(--light-green);">
                        <div class="progress-bar progress-bar-animated" role="progressbar"
                             style="width: {{ ($statistics['unsatisfactory_count'] ?? 0) / max(($statistics['total_students'] ?? 1), 1) * 100 }}%; background: #dc3545;">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Grade Types Tabs -->
    <ul class="nav nav-tabs mb-3" style="border-bottom: 2px solid var(--border-green);">
        <li class="nav-item">
            <a class="nav-link active" data-bs-toggle="tab" href="#current"
               style="color: var(--text-dark); border-color: var(--border-green) var(--border-green) white;">
                <i class="fas fa-clipboard-check"></i> Joriy baholash (30 ball)
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#midterm"
               style="color: var(--text-dark);">
                <i class="fas fa-file-alt"></i> Oraliq nazorat (30 ball)
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#final"
               style="color: var(--text-dark);">
                <i class="fas fa-graduation-cap"></i> Yakuniy nazorat (40 ball)
            </a>
        </li>
        <li class="nav-item ms-auto">
            <a class="nav-link" data-bs-toggle="tab" href="#summary"
               style="color: var(--primary-dark-green); font-weight: 600;">
                <i class="fas fa-chart-bar"></i> Umumiy natijalar
            </a>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content">
        <!-- Current Grades Tab -->
        <div class="tab-pane fade show active" id="current">
            <div class="card border-0 shadow-sm" style="border: 1px solid var(--border-green) !important;">
                <div class="card-header" style="background: var(--light-green); border-bottom: 2px solid var(--border-green);">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0" style="color: var(--text-dark); font-weight: 600;">
                            <i class="fas fa-clipboard-check" style="color: var(--secondary-green);"></i>
                            Joriy baholash
                        </h5>
                        <div>
                            <button class="btn btn-sm text-white" onclick="addGradeColumn('current')"
                                    style="background: var(--primary-dark-green);">
                                <i class="fas fa-plus"></i> Ustun qo'shish
                            </button>
                            <button class="btn btn-sm text-white" onclick="saveAllGrades('current')"
                                    style="background: var(--secondary-green);">
                                <i class="fas fa-save"></i> Saqlash
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="gradesTable">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">№</th>
                                    <th style="min-width: 200px;">Talaba F.I.O</th>
                                    <th class="text-center">1-hafta</th>
                                    <th class="text-center">2-hafta</th>
                                    <th class="text-center">3-hafta</th>
                                    <th class="text-center">4-hafta</th>
                                    <th class="text-center">5-hafta</th>
                                    <th class="text-center" style="background: var(--accent-green); color: white;">Jami (30)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($students ?? [] as $index => $student)
                                <tr class="student-row">
                                    <td style="color: var(--text-dark);">{{ $index + 1 }}</td>
                                    <td style="color: var(--text-dark); font-weight: 600;">
                                        {{ $student->last_name }} {{ $student->first_name }}
                                        @if($student->is_monitor)
                                            <span class="badge ms-1" style="background: var(--secondary-green); color: white;">
                                                <i class="fas fa-crown"></i> Guruh boshlig'i
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center grade-cell">
                                        <input type="number" class="grade-input" min="0" max="6" value="{{ $grades[$student->id]['week1'] ?? '' }}"
                                               onchange="updateGrade({{ $student->id }}, 'week1', this.value)">
                                    </td>
                                    <td class="text-center grade-cell">
                                        <input type="number" class="grade-input" min="0" max="6" value="{{ $grades[$student->id]['week2'] ?? '' }}"
                                               onchange="updateGrade({{ $student->id }}, 'week2', this.value)">
                                    </td>
                                    <td class="text-center grade-cell">
                                        <input type="number" class="grade-input" min="0" max="6" value="{{ $grades[$student->id]['week3'] ?? '' }}"
                                               onchange="updateGrade({{ $student->id }}, 'week3', this.value)">
                                    </td>
                                    <td class="text-center grade-cell">
                                        <input type="number" class="grade-input" min="0" max="6" value="{{ $grades[$student->id]['week4'] ?? '' }}"
                                               onchange="updateGrade({{ $student->id }}, 'week4', this.value)">
                                    </td>
                                    <td class="text-center grade-cell">
                                        <input type="number" class="grade-input" min="0" max="6" value="{{ $grades[$student->id]['week5'] ?? '' }}"
                                               onchange="updateGrade({{ $student->id }}, 'week5', this.value)">
                                    </td>
                                    <td class="text-center" style="background: var(--very-light-green);">
                                        <strong id="total-current-{{ $student->id }}" style="color: var(--primary-dark-green);">
                                            {{ $grades[$student->id]['current_total'] ?? 0 }}
                                        </strong>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Midterm Tab -->
        <div class="tab-pane fade" id="midterm">
            <div class="card border-0 shadow-sm" style="border: 1px solid var(--border-green) !important;">
                <div class="card-header" style="background: var(--light-green); border-bottom: 2px solid var(--border-green);">
                    <h5 class="mb-0" style="color: var(--text-dark); font-weight: 600;">
                        <i class="fas fa-file-alt" style="color: var(--secondary-green);"></i>
                        Oraliq nazorat
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead style="background: var(--very-light-green);">
                                <tr>
                                    <th style="color: var(--text-dark); font-weight: 600;">№</th>
                                    <th style="color: var(--text-dark); font-weight: 600;">Talaba F.I.O</th>
                                    <th class="text-center" style="color: var(--text-dark); font-weight: 600;">Ball (30)</th>
                                    <th class="text-center" style="color: var(--text-dark); font-weight: 600;">Baho</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($students ?? [] as $index => $student)
                                <tr class="student-row">
                                    <td style="color: var(--text-dark);">{{ $index + 1 }}</td>
                                    <td style="color: var(--text-dark); font-weight: 600;">
                                        {{ $student->last_name }} {{ $student->first_name }}
                                    </td>
                                    <td class="text-center">
                                        <input type="number" class="grade-input" min="0" max="30"
                                               value="{{ $grades[$student->id]['midterm'] ?? '' }}"
                                               onchange="updateMidterm({{ $student->id }}, this.value)">
                                    </td>
                                    <td class="text-center">
                                        <span id="midterm-badge-{{ $student->id }}">
                                            @if(($grades[$student->id]['midterm'] ?? 0) >= 26)
                                                <span class="grade-badge grade-excellent">A'lo</span>
                                            @elseif(($grades[$student->id]['midterm'] ?? 0) >= 21)
                                                <span class="grade-badge grade-good">Yaxshi</span>
                                            @elseif(($grades[$student->id]['midterm'] ?? 0) >= 17)
                                                <span class="grade-badge grade-satisfactory">Qoniqarli</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Final Tab -->
        <div class="tab-pane fade" id="final">
            <div class="card border-0 shadow-sm" style="border: 1px solid var(--border-green) !important;">
                <div class="card-header" style="background: var(--light-green); border-bottom: 2px solid var(--border-green);">
                    <h5 class="mb-0" style="color: var(--text-dark); font-weight: 600;">
                        <i class="fas fa-graduation-cap" style="color: var(--secondary-green);"></i>
                        Yakuniy nazorat
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead style="background: var(--very-light-green);">
                                <tr>
                                    <th style="color: var(--text-dark); font-weight: 600;">№</th>
                                    <th style="color: var(--text-dark); font-weight: 600;">Talaba F.I.O</th>
                                    <th class="text-center" style="color: var(--text-dark); font-weight: 600;">Ball (40)</th>
                                    <th class="text-center" style="color: var(--text-dark); font-weight: 600;">Baho</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($students ?? [] as $index => $student)
                                <tr class="student-row">
                                    <td style="color: var(--text-dark);">{{ $index + 1 }}</td>
                                    <td style="color: var(--text-dark); font-weight: 600;">
                                        {{ $student->last_name }} {{ $student->first_name }}
                                    </td>
                                    <td class="text-center">
                                        <input type="number" class="grade-input" min="0" max="40"
                                               value="{{ $grades[$student->id]['final'] ?? '' }}"
                                               onchange="updateFinal({{ $student->id }}, this.value)">
                                    </td>
                                    <td class="text-center">
                                        <span id="final-badge-{{ $student->id }}">
                                            @if(($grades[$student->id]['final'] ?? 0) >= 34)
                                                <span class="grade-badge grade-excellent">A'lo</span>
                                            @elseif(($grades[$student->id]['final'] ?? 0) >= 28)
                                                <span class="grade-badge grade-good">Yaxshi</span>
                                            @elseif(($grades[$student->id]['final'] ?? 0) >= 22)
                                                <span class="grade-badge grade-satisfactory">Qoniqarli</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Summary Tab -->
        <div class="tab-pane fade" id="summary">
            <div class="card border-0 shadow-sm" style="border: 1px solid var(--border-green) !important;">
                <div class="card-header" style="background: linear-gradient(135deg, var(--primary-dark-green), var(--secondary-green)); color: white;">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-bar"></i>
                        Umumiy natijalar
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead style="background: var(--very-light-green);">
                                <tr>
                                    <th style="color: var(--text-dark); font-weight: 600;">№</th>
                                    <th style="color: var(--text-dark); font-weight: 600;">Talaba F.I.O</th>
                                    <th class="text-center" style="color: var(--text-dark); font-weight: 600;">JB (30)</th>
                                    <th class="text-center" style="color: var(--text-dark); font-weight: 600;">ON (30)</th>
                                    <th class="text-center" style="color: var(--text-dark); font-weight: 600;">YN (40)</th>
                                    <th class="text-center" style="color: var(--text-dark); font-weight: 600;">Jami (100)</th>
                                    <th class="text-center" style="color: var(--text-dark); font-weight: 600;">Baho</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($students ?? [] as $index => $student)
                                @php
                                    $current = $grades[$student->id]['current_total'] ?? 0;
                                    $midterm = $grades[$student->id]['midterm'] ?? 0;
                                    $final = $grades[$student->id]['final'] ?? 0;
                                    $total = $current + $midterm + $final;
                                @endphp
                                <tr class="student-row">
                                    <td style="color: var(--text-dark);">{{ $index + 1 }}</td>
                                    <td style="color: var(--text-dark); font-weight: 600;">
                                        {{ $student->last_name }} {{ $student->first_name }}
                                    </td>
                                    <td class="text-center">
                                        <span class="badge" style="background: var(--light-green); color: var(--primary-dark-green);">
                                            {{ $current }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge" style="background: var(--light-green); color: var(--primary-dark-green);">
                                            {{ $midterm }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge" style="background: var(--light-green); color: var(--primary-dark-green);">
                                            {{ $final }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <strong style="color: var(--primary-dark-green); font-size: 18px;">{{ $total }}</strong>
                                    </td>
                                    <td class="text-center">
                                        @if($total >= 86)
                                            <span class="grade-badge grade-excellent">A'lo (5)</span>
                                        @elseif($total >= 71)
                                            <span class="grade-badge grade-good">Yaxshi (4)</span>
                                        @elseif($total >= 56)
                                            <span class="grade-badge grade-satisfactory">Qoniqarli (3)</span>
                                        @else
                                            <span class="grade-badge grade-unsatisfactory">Qoniqarsiz (2)</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function updateGrade(studentId, week, value) {
    // Validate input
    if (value < 0 || value > 6) {
        alert('Ball 0 dan 6 gacha bo\'lishi kerak!');
        return;
    }

    // Update total for current grades
    calculateCurrentTotal(studentId);

    // Save to server (implementation would go here)
    saveGrade(studentId, 'current', week, value);
}

function calculateCurrentTotal(studentId) {
    let total = 0;
    const weeks = ['week1', 'week2', 'week3', 'week4', 'week5'];

    weeks.forEach(week => {
        const input = document.querySelector(`input[onchange*="${studentId}, '${week}'"]`);
        if (input && input.value) {
            total += parseInt(input.value);
        }
    });

    document.getElementById(`total-current-${studentId}`).textContent = total;
}

function updateMidterm(studentId, value) {
    if (value < 0 || value > 30) {
        alert('Ball 0 dan 30 gacha bo\'lishi kerak!');
        return;
    }

    // Update badge
    const badge = document.getElementById(`midterm-badge-${studentId}`);
    if (value >= 26) {
        badge.innerHTML = '<span class="grade-badge grade-excellent">A\'lo</span>';
    } else if (value >= 21) {
        badge.innerHTML = '<span class="grade-badge grade-good">Yaxshi</span>';
    } else if (value >= 17) {
        badge.innerHTML = '<span class="grade-badge grade-satisfactory">Qoniqarli</span>';
    } else {
        badge.innerHTML = '<span class="text-muted">-</span>';
    }

    saveGrade(studentId, 'midterm', null, value);
}

function updateFinal(studentId, value) {
    if (value < 0 || value > 40) {
        alert('Ball 0 dan 40 gacha bo\'lishi kerak!');
        return;
    }

    // Update badge
    const badge = document.getElementById(`final-badge-${studentId}`);
    if (value >= 34) {
        badge.innerHTML = '<span class="grade-badge grade-excellent">A\'lo</span>';
    } else if (value >= 28) {
        badge.innerHTML = '<span class="grade-badge grade-good">Yaxshi</span>';
    } else if (value >= 22) {
        badge.innerHTML = '<span class="grade-badge grade-satisfactory">Qoniqarli</span>';
    } else {
        badge.innerHTML = '<span class="text-muted">-</span>';
    }

    saveGrade(studentId, 'final', null, value);
}

function saveGrade(studentId, type, week, value) {
    // Implementation for saving grade to server
    fetch('/api/grades/save', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            journal_id: {{ $journal->id ?? 0 }},
            student_id: studentId,
            grade_type: type,
            week: week,
            score: value
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success indicator
            showSaveIndicator(true);
        }
    })
    .catch(error => {
        console.error('Error saving grade:', error);
    });
}

function showSaveIndicator(success) {
    // Visual feedback for save operation
    const indicator = document.createElement('div');
    indicator.className = 'position-fixed bottom-0 end-0 m-3 p-3 rounded';
    indicator.style.background = success ? 'var(--secondary-green)' : '#dc3545';
    indicator.style.color = 'white';
    indicator.style.zIndex = '9999';
    indicator.innerHTML = success ?
        '<i class="fas fa-check-circle"></i> Saqlandi' :
        '<i class="fas fa-times-circle"></i> Xatolik';

    document.body.appendChild(indicator);
    setTimeout(() => indicator.remove(), 2000);
}

function saveAllGrades(type) {
    alert(`${type} baholar saqlanmoqda...`);
}

function addGradeColumn(type) {
    alert('Yangi ustun qo\'shish funksiyasi tez orada qo\'shiladi!');
}

function openGradeSettings() {
    alert('Baholash sozlamalari oynasi ochilmoqda...');
}
</script>
@endpush