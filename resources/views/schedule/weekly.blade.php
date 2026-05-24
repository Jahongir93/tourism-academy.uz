@extends('layouts.dashboard-new')

@section('title', 'Haftalik dars jadvali')
@section('page-title', 'Haftalik dars jadvali')

@section('styles')
<style>
    .schedule-table {
        font-size: 0.85rem;
    }
    .schedule-cell {
        min-width: 150px;
        padding: 8px;
        vertical-align: top;
        border: 1px solid #dee2e6;
    }
    .schedule-item {
        background: #f8f9fa;
        border-left: 3px solid #0d6efd;
        padding: 8px;
        margin-bottom: 5px;
        border-radius: 4px;
    }
    .schedule-item:last-child {
        margin-bottom: 0;
    }
    .time-slot {
        font-weight: 600;
        background: #e9ecef;
        position: sticky;
        left: 0;
        z-index: 10;
    }
    .day-header {
        background: #0d6efd;
        color: white;
        font-weight: 600;
        text-align: center;
        padding: 12px;
    }
    .schedule-subject {
        font-weight: 600;
        color: #0d6efd;
        margin-bottom: 4px;
    }
    .schedule-info {
        font-size: 0.75rem;
        color: #6c757d;
    }
    .empty-cell {
        background: #fafafa;
        text-align: center;
        color: #adb5bd;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Filter Section -->
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('schedule.weekly') }}" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Guruh</label>
                    <select name="group_id" class="form-select">
                        <option value="">Barcha guruhlar</option>
                        @foreach($groups as $group)
                            <option value="{{ $group->id }}" {{ request('group_id') == $group->id ? 'selected' : '' }}>
                                {{ $group->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">O'qituvchi</label>
                    <select name="teacher_id" class="form-select">
                        <option value="">Barcha o'qituvchilar</option>
                        @foreach($teachers as $teacher)
                            <option value="{{ $teacher->id }}" {{ request('teacher_id') == $teacher->id ? 'selected' : '' }}>
                                {{ $teacher->full_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">&nbsp;</label>
                    <div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter"></i> Filterlash
                        </button>
                        <a href="{{ route('schedule.weekly') }}" class="btn btn-secondary">
                            <i class="fas fa-redo"></i> Tozalash
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Weekly Schedule Table -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Haftalik dars jadvali</h5>
            <div>
                <a href="{{ route('schedule.index') }}" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-list"></i> Ro'yxat
                </a>
                <a href="{{ route('schedule.create') }}" class="btn btn-sm btn-success">
                    <i class="fas fa-plus"></i> Yangi dars
                </a>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered schedule-table mb-0">
                    <thead>
                        <tr>
                            <th class="time-slot">Vaqt</th>
                            @foreach($days as $dayNum => $dayName)
                                <th class="day-header">{{ $dayName }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($timeSlots as $timeSlot)
                            <tr>
                                <td class="time-slot">
                                    <div>{{ $timeSlot->name }}</div>
                                    <small class="text-muted">
                                        {{ substr($timeSlot->start_time, 0, 5) }} - {{ substr($timeSlot->end_time, 0, 5) }}
                                    </small>
                                </td>
                                @foreach($days as $dayNum => $dayName)
                                    <td class="schedule-cell">
                                        @if(isset($schedules[$dayNum][$timeSlot->id]) && $schedules[$dayNum][$timeSlot->id]->count() > 0)
                                            @foreach($schedules[$dayNum][$timeSlot->id] as $schedule)
                                                <div class="schedule-item">
                                                    <div class="schedule-subject">
                                                        {{ $schedule->subject->name_uz ?? $schedule->subject->name }}
                                                    </div>
                                                    <div class="schedule-info">
                                                        <i class="fas fa-users"></i> {{ $schedule->group->name }}<br>
                                                        <i class="fas fa-chalkboard-teacher"></i> {{ $schedule->teacher->full_name }}<br>
                                                        <i class="fas fa-door-open"></i> {{ $schedule->classroom->name ?? '-' }}
                                                    </div>
                                                </div>
                                            @endforeach
                                        @else
                                            <div class="empty-cell">-</div>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <i class="fas fa-info-circle"></i> Vaqt oralig'i topilmadi
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
