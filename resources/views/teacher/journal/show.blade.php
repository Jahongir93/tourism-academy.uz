@extends('layouts.dashboard-new')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="card border-0 shadow-sm mb-4 bg-gradient-primary text-white">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">
                        <i class="fas fa-book me-2"></i>
                        {{ $groupSubject->subject->name }}
                    </h4>
                    <p class="mb-0 opacity-75">
                        <i class="fas fa-users me-2"></i>{{ $groupSubject->group->name }}
                        <span class="ms-3"><i class="fas fa-calendar me-1"></i>{{ $groupSubject->semester }}-semestr</span>
                        @if($groupSubject->academicYear)
                        <span class="ms-3"><i class="fas fa-graduation-cap me-1"></i>{{ $groupSubject->academicYear->year }}</span>
                        @endif
                    </p>
                </div>
                <div>
                    <a href="{{ route('teacher.journal.index') }}" class="btn btn-light btn-sm me-2">
                        <i class="fas fa-arrow-left me-1"></i>Orqaga
                    </a>
                    <a href="{{ route('teacher.journal.export', [$groupSubject->id, 'pdf']) }}"
                       class="btn btn-warning btn-sm">
                        <i class="fas fa-file-pdf me-1"></i>PDF
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3">
                            <i class="fas fa-users fa-lg text-primary"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1 small">Talabalar</h6>
                            <h3 class="mb-0 fw-bold">{{ $overallStats['total_students'] }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-success bg-opacity-10 p-3 me-3">
                            <i class="fas fa-chalkboard-teacher fa-lg text-success"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1 small">Darslar</h6>
                            <h3 class="mb-0 fw-bold">{{ $overallStats['total_entries'] }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-warning bg-opacity-10 p-3 me-3">
                            <i class="fas fa-star fa-lg text-warning"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1 small">O'rtacha ball</h6>
                            <h3 class="mb-0 fw-bold">{{ number_format($overallStats['avg_score'], 1) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-info bg-opacity-10 p-3 me-3">
                            <i class="fas fa-percentage fa-lg text-info"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1 small">Davomat</h6>
                            <h3 class="mb-0 fw-bold">{{ number_format($overallStats['avg_attendance'], 1) }}%</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Dars jadvali -->
    @if(isset($schedules) && $schedules->count() > 0)
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-light">
            <h6 class="mb-0">
                <i class="fas fa-calendar-alt me-2 text-primary"></i>Dars jadvali
            </h6>
        </div>
        <div class="card-body py-3">
            <div class="d-flex flex-wrap gap-3">
                @foreach($schedules as $schedule)
                <div class="border rounded p-2 px-3 bg-light">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <i class="fas fa-calendar-day text-primary fa-lg"></i>
                        </div>
                        <div>
                            <strong class="text-primary">{{ $schedule['day'] }}</strong>
                            <div class="small text-muted">
                                <i class="fas fa-clock me-1"></i>{{ $schedule['time'] }}
                            </div>
                            <div class="small">
                                <i class="fas fa-door-open me-1 text-secondary"></i>{{ $schedule['room'] }}
                                @if($schedule['type'] == 'lecture')
                                    <span class="badge bg-primary ms-1">Ma'ruza</span>
                                @elseif($schedule['type'] == 'practice')
                                    <span class="badge bg-success ms-1">Amaliyot</span>
                                @elseif($schedule['type'] == 'lab')
                                    <span class="badge bg-info ms-1">Laboratoriya</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @else
    <div class="alert alert-info mb-4">
        <i class="fas fa-info-circle me-2"></i>
        Bu fan uchun dars jadvali hali belgilanmagan.
    </div>
    @endif

    <!-- Journal Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0">
                <i class="fas fa-table me-2"></i>
                Jurnal jadvali
            </h5>
        </div>
        <div class="card-body p-0">
            @if($journalMatrix && count($journalMatrix) > 0)
            <div class="table-responsive" style="max-height: 600px; overflow-y: auto;">
                <table class="table table-bordered table-hover mb-0" style="font-size: 0.85rem;">
                    <thead class="bg-light sticky-top" style="top: 0; z-index: 10;">
                        <tr>
                            <th rowspan="2" class="text-center align-middle" style="min-width: 50px;">#</th>
                            <th rowspan="2" class="align-middle" style="min-width: 200px;">F.I.Sh</th>
                            <th rowspan="2" class="text-center align-middle" style="min-width: 100px;">ID</th>

                            @if($journalEntries->count() > 0)
                            <th colspan="{{ $journalEntries->count() }}" class="text-center bg-primary text-white">Darslar</th>
                            @endif

                            <th colspan="3" class="text-center bg-info text-white">Nazorat turlari</th>
                            <th rowspan="2" class="text-center align-middle bg-warning" style="min-width: 80px;">
                                <strong>Umumiy</strong>
                            </th>
                            <th rowspan="2" class="text-center align-middle" style="min-width: 80px;">Davomat</th>
                        </tr>
                        <tr>
                            @foreach($journalEntries as $entry)
                            <th class="text-center" style="min-width: 60px; font-size: 0.75rem;">
                                {{ $entry->created_at->format('d.m') }}
                            </th>
                            @endforeach

                            <th class="text-center bg-info bg-opacity-25" style="min-width: 60px;">Joriy</th>
                            <th class="text-center bg-info bg-opacity-25" style="min-width: 60px;">Oraliq</th>
                            <th class="text-center bg-info bg-opacity-25" style="min-width: 60px;">Yakuniy</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($journalMatrix as $index => $row)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>
                                <strong>{{ $row['student']->user->name }}</strong>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-secondary">{{ $row['student']->student_id }}</span>
                            </td>

                            @foreach($journalEntries as $entry)
                            <td class="text-center">
                                @if(isset($row['entries'][$entry->id]) && $row['entries'][$entry->id])
                                    @php $grade = $row['entries'][$entry->id]; @endphp
                                    @if($grade->score !== null)
                                    <span class="badge bg-{{ $grade->score >= 86 ? 'success' : ($grade->score >= 71 ? 'primary' : ($grade->score >= 56 ? 'warning' : 'danger')) }}">
                                        {{ number_format($grade->score, 0) }}
                                    </span>
                                    @else
                                    <i class="fas fa-check text-success" title="Ishtirok etdi"></i>
                                    @endif
                                @else
                                <span class="text-muted">-</span>
                                @endif
                            </td>
                            @endforeach

                            <td class="text-center bg-info bg-opacity-10">
                                @if($row['joriy_avg'])
                                <strong>{{ number_format($row['joriy_avg'], 1) }}</strong>
                                @else
                                <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-center bg-info bg-opacity-10">
                                @if($row['oraliq_avg'])
                                <strong>{{ number_format($row['oraliq_avg'], 1) }}</strong>
                                @else
                                <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-center bg-info bg-opacity-10">
                                @if($row['yakuniy_avg'])
                                <strong>{{ number_format($row['yakuniy_avg'], 1) }}</strong>
                                @else
                                <span class="text-muted">-</span>
                                @endif
                            </td>

                            <td class="text-center bg-warning bg-opacity-25">
                                @if($row['total_avg'])
                                <strong class="text-dark">{{ number_format($row['total_avg'], 1) }}</strong>
                                @else
                                <span class="text-muted">-</span>
                                @endif
                            </td>

                            <td class="text-center">
                                <small>{{ $row['attendance_rate'] }}%</small>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center py-5">
                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Hali darslar o'tilmagan</h5>
                <p class="text-muted mb-3">Birinchi davomatni qilish uchun "Davomat" bo'limiga o'ting</p>
                <a href="{{ route('teacher.attendance.create', $groupSubject->id) }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i>Davomat qilish
                </a>
            </div>
            @endif
        </div>
    </div>

    <!-- Legend -->
    @if($journalMatrix && count($journalMatrix) > 0)
    <div class="card border-0 shadow-sm mt-4">
        <div class="card-body">
            <h6 class="mb-3">
                <i class="fas fa-info-circle me-2"></i>
                Belgilar izohi
            </h6>
            <div class="row">
                <div class="col-md-6">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <span class="badge bg-success me-2">86-100</span> A (5) - A'lo
                        </li>
                        <li class="mb-2">
                            <span class="badge bg-primary me-2">71-85</span> B (4) - Yaxshi
                        </li>
                        <li class="mb-2">
                            <span class="badge bg-warning me-2">56-70</span> C (3) - Qoniqarli
                        </li>
                        <li class="mb-2">
                            <span class="badge bg-danger me-2">0-55</span> F (2) - Qoniqarsiz
                        </li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i> Ishtirok etdi (ball kiritilmagan)
                        </li>
                        <li class="mb-2">
                            <span class="text-muted me-2">-</span> Qatnashmadi
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<style>
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
.table thead.sticky-top {
    position: sticky;
    top: 0;
    z-index: 10;
}
</style>
@endsection
