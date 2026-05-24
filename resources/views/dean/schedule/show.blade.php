@extends('layouts.dashboard-new')

@section('title', 'Jadval ko\'rish')
@section('page-title', 'Dars jadvali')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm bg-gradient-info text-white">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1"><i class="fas fa-calendar-alt me-2"></i>{{ $schedule->group?->name }} - Dars jadvali</h4>
                            <p class="mb-0 opacity-75">
                                {{ $schedule->academicYear?->name ?? '' }} {{ $schedule->semester_id }}-semestr
                                @switch($schedule->status)
                                    @case('draft')
                                        <span class="badge bg-warning text-dark ms-2">Qoralama</span>
                                        @break
                                    @case('active')
                                        <span class="badge bg-success ms-2">Faol</span>
                                        @break
                                    @case('archived')
                                        <span class="badge bg-secondary ms-2">Arxiv</span>
                                        @break
                                @endswitch
                            </p>
                        </div>
                        <div>
                            <a href="{{ route('dean.schedule.edit', $schedule) }}" class="btn btn-light me-2">
                                <i class="fas fa-edit me-1"></i> Tahrirlash
                            </a>
                            <a href="{{ route('dean.schedule.index') }}" class="btn btn-outline-light">
                                <i class="fas fa-arrow-left me-1"></i> Orqaga
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistika -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100 bg-primary bg-opacity-10">
                <div class="card-body text-center">
                    <h3 class="mb-0 fw-bold text-primary">{{ $schedule->slots->count() }}</h3>
                    <p class="text-muted mb-0">Jami darslar</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100 bg-success bg-opacity-10">
                <div class="card-body text-center">
                    <h3 class="mb-0 fw-bold text-success">{{ $schedule->slots->where('lesson_type', 'lecture')->count() }}</h3>
                    <p class="text-muted mb-0">Ma'ruzalar</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100 bg-info bg-opacity-10">
                <div class="card-body text-center">
                    <h3 class="mb-0 fw-bold text-info">{{ $schedule->slots->where('lesson_type', 'practice')->count() }}</h3>
                    <p class="text-muted mb-0">Amaliyotlar</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100 bg-warning bg-opacity-10">
                <div class="card-body text-center">
                    <h3 class="mb-0 fw-bold text-warning">{{ $schedule->slots->where('lesson_type', 'lab')->count() }}</h3>
                    <p class="text-muted mb-0">Laboratoriyalar</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Jadval -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-table text-primary me-2"></i>Haftalik jadval</h5>
            <button class="btn btn-sm btn-outline-primary" onclick="window.print()">
                <i class="fas fa-print me-1"></i> Chop etish
            </button>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered mb-0" id="scheduleTable">
                    <thead class="bg-light">
                        <tr>
                            <th class="text-center" style="width: 100px;">Vaqt</th>
                            @foreach($days as $day)
                            <th class="text-center">{{ $day }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($timeSlots as $slotNum => $time)
                        <tr>
                            <td class="text-center align-middle bg-light">
                                <strong>{{ $slotNum }}-para</strong><br>
                                <small class="text-muted">{{ $time }}</small>
                            </td>
                            @foreach(range(1, 6) as $dayNum)
                            <td class="p-2">
                                @php
                                    $slot = $schedule->slots->first(function($s) use ($dayNum, $slotNum) {
                                        return $s->day_number == $dayNum && $s->time_slot == $slotNum;
                                    });
                                @endphp
                                @if($slot)
                                <div class="card border-0 {{ $slot->lesson_type == 'lecture' ? 'bg-primary' : ($slot->lesson_type == 'practice' ? 'bg-success' : 'bg-info') }} bg-opacity-10 h-100">
                                    <div class="card-body p-2">
                                        <div class="fw-semibold small">{{ $slot->subject?->name_uz ?? '-' }}</div>
                                        <div class="text-muted small">
                                            <i class="fas fa-user-tie"></i> {{ $slot->teacher?->full_name ?? '-' }}
                                        </div>
                                        <div class="text-muted small">
                                            <i class="fas fa-door-open"></i> {{ $slot->room?->name ?? '-' }}
                                        </div>
                                        <span class="badge bg-{{ $slot->lesson_type == 'lecture' ? 'primary' : ($slot->lesson_type == 'practice' ? 'success' : 'info') }} small">
                                            {{ $slot->lesson_type == 'lecture' ? 'Ma\'ruza' : ($slot->lesson_type == 'practice' ? 'Amaliy' : ($slot->lesson_type == 'lab' ? 'Lab' : 'Seminar')) }}
                                        </span>
                                        @if($slot->week_type != 'all')
                                        <span class="badge bg-warning text-dark small">{{ $slot->week_type == 'odd' ? 'Toq hafta' : 'Juft hafta' }}</span>
                                        @endif
                                    </div>
                                </div>
                                @else
                                <div class="text-center text-muted py-4">
                                    <i class="fas fa-minus opacity-25"></i>
                                </div>
                                @endif
                            </td>
                            @endforeach
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
.bg-gradient-info { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
@media print {
    .btn, .card-header .btn, nav, .sidebar, header { display: none !important; }
    .card { border: 1px solid #ddd !important; }
    .bg-gradient-info { background: #667eea !important; -webkit-print-color-adjust: exact; }
}
</style>
@endsection
