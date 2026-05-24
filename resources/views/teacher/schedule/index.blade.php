@extends('layouts.dashboard-new')

@section('title', 'Dars jadvali')
@section('page-title', 'Dars jadvali')

@section('content')
<div class="container-fluid px-4 py-3">
    <!-- Header -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1"><i class="fas fa-calendar-alt text-primary me-2"></i>Haftalik dars jadvali</h4>
                            <p class="text-muted mb-0">
                                @if($teacher && $teacher->user)
                                    {{ $teacher->user->name }} - {{ $teacher->position ?? 'O\'qituvchi' }}
                                @else
                                    {{ auth()->user()->name ?? 'O\'qituvchi' }}
                                @endif
                            </p>
                        </div>
                        <div>
                            <a href="{{ route('teacher.schedule.export') }}" class="btn btn-primary" target="_blank">
                                <i class="fas fa-file-pdf me-2"></i>PDF yuklash
                            </a>
                            <a href="{{ route('teacher.dashboard') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Orqaga
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics -->
    <div class="row mb-3">
        <div class="col-md-4 mb-2">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3">
                            <i class="fas fa-chalkboard-teacher fa-2x text-primary"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1 small">Haftalik darslar</h6>
                            <h3 class="mb-0 fw-bold">{{ $totalLessonsPerWeek }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-2">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-success bg-opacity-10 p-3 me-3">
                            <i class="fas fa-clock fa-2x text-success"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1 small">Haftalik soatlar</h6>
                            <h3 class="mb-0 fw-bold">{{ number_format($totalHoursPerWeek, 1) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-2">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-info bg-opacity-10 p-3 me-3">
                            <i class="fas fa-calendar-check fa-2x text-info"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1 small">Ish kunlari</h6>
                            <h3 class="mb-0 fw-bold">{{ $daysWithClasses }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Weekly Schedule Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered mb-0 schedule-table">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 100px;">Vaqt</th>
                            <th>Dushanba</th>
                            <th>Seshanba</th>
                            <th>Chorshanba</th>
                            <th>Payshanba</th>
                            <th>Juma</th>
                            <th>Shanba</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            // Get all unique time slots
                            $timeSlots = [];
                            foreach($weeklySchedule as $day => $lessons) {
                                foreach($lessons as $lesson) {
                                    $timeKey = $lesson['start_time'] . '-' . $lesson['end_time'];
                                    if (!isset($timeSlots[$timeKey])) {
                                        $timeSlots[$timeKey] = [
                                            'start' => $lesson['start_time'],
                                            'end' => $lesson['end_time']
                                        ];
                                    }
                                }
                            }
                            // Sort by start time
                            uasort($timeSlots, function($a, $b) {
                                return strcmp($a['start'], $b['start']);
                            });
                        @endphp

                        @forelse($timeSlots as $timeKey => $timeSlot)
                            <tr>
                                <td class="text-center align-middle bg-light">
                                    <strong>{{ \Carbon\Carbon::parse($timeSlot['start'])->format('H:i') }}</strong><br>
                                    <small class="text-muted">{{ \Carbon\Carbon::parse($timeSlot['end'])->format('H:i') }}</small>
                                </td>
                                @for($day = 1; $day <= 6; $day++)
                                    <td class="align-top p-2">
                                        @php
                                            $dayLessons = collect($weeklySchedule[$day] ?? [])->filter(function($lesson) use ($timeSlot) {
                                                return $lesson['start_time'] == $timeSlot['start'] &&
                                                       $lesson['end_time'] == $timeSlot['end'];
                                            });
                                        @endphp

                                        @foreach($dayLessons as $lesson)
                                            <div class="lesson-card mb-2">
                                                <div class="lesson-subject">
                                                    <i class="fas fa-book text-primary me-1"></i>
                                                    <strong>{{ $lesson['subject']->name_uz ?? $lesson['subject']->name ?? 'N/A' }}</strong>
                                                </div>
                                                <div class="lesson-group">
                                                    <i class="fas fa-users text-success me-1"></i>
                                                    <span class="badge bg-primary">{{ $lesson['group']->name ?? 'N/A' }}</span>
                                                </div>
                                                <div class="lesson-room">
                                                    <i class="fas fa-door-open text-warning me-1"></i>
                                                    {{ $lesson['room'] ?? 'N/A' }}
                                                </div>
                                                @if(isset($lesson['type']))
                                                    <div class="lesson-type">
                                                        <span class="badge bg-secondary">{{ $lesson['type'] }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </td>
                                @endfor
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">Dars jadvali mavjud emas</p>
                                    <small class="text-muted">Administratorga murojaat qiling</small>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Groups List -->
    @if(isset($groups) && $groups->count() > 0)
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 p-3">
                    <h5 class="mb-0"><i class="fas fa-users text-primary me-2"></i>Mening guruhlarim</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Guruh</th>
                                    <th>Kurs</th>
                                    <th>Mutaxassislik</th>
                                    <th>Talabalar</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($groups as $group)
                                <tr>
                                    <td><span class="badge bg-primary">{{ $group->name ?? 'N/A' }}</span></td>
                                    <td>{{ $group->course ?? '-' }}-kurs</td>
                                    <td>
                                        <small class="text-muted">
                                            {{ $group->specialty->name ?? $group->specialty_name ?? '-' }}
                                        </small>
                                    </td>
                                    <td>
                                        <i class="fas fa-users text-muted me-1"></i>
                                        {{ $group->current_students ?? $group->students_count ?? 0 }}
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
    @endif
</div>

<style>
.schedule-table {
    font-size: 0.9rem;
}

.schedule-table th {
    text-align: center;
    font-weight: 600;
    background-color: #f8f9fa;
    vertical-align: middle;
}

.schedule-table td {
    min-height: 80px;
    vertical-align: top;
}

.lesson-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 10px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: transform 0.2s;
}

.lesson-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

.lesson-subject {
    font-size: 0.95rem;
    margin-bottom: 6px;
}

.lesson-group {
    font-size: 0.85rem;
    margin-bottom: 4px;
}

.lesson-group .badge {
    background-color: rgba(255, 255, 255, 0.3) !important;
}

.lesson-room {
    font-size: 0.85rem;
    margin-bottom: 4px;
}

.lesson-type {
    margin-top: 6px;
}

.lesson-type .badge {
    background-color: rgba(255, 255, 255, 0.3) !important;
}

@media print {
    .btn, .card-header {
        display: none !important;
    }

    .schedule-table {
        page-break-inside: avoid;
    }
}
</style>
@endsection
