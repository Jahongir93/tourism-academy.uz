@extends('layouts.dashboard-new')

@section('title', 'Xonalar bandligi')
@section('page-title', 'Xonalar bandligi')

@section('styles')
<style>
    .room-card { border:2px solid #dee2e6; border-radius:10px; padding:15px; transition:all .2s; height:100%; }
    .room-card.available { background:#d1e7dd; border-color:#198754; }
    .room-card.occupied  { background:#f8d7da; border-color:#dc3545; }
    .room-card:hover { transform:translateY(-3px); box-shadow:0 6px 14px rgba(0,0,0,.1); }
    .room-header { font-weight:700; font-size:1.05rem; margin-bottom:6px; }
    .room-status { font-size:.78rem; padding:3px 10px; border-radius:999px; display:inline-block; font-weight:600; }
    .status-available { background:#198754; color:#fff; }
    .status-occupied  { background:#dc3545; color:#fff; }
    .room-details { font-size:.85rem; color:#6c757d; margin-top:8px; }
    .filter-panel { background:#e7f3ff; border:1px solid #b6d4fe; border-radius:10px; padding:15px; margin-bottom:20px; }
</style>
@endsection

@section('content')
<div class="container-fluid">

    <!-- Filtr -->
    <div class="filter-panel">
        <form method="GET" action="{{ route('schedule.room-monitoring') }}" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label mb-1">Kun</label>
                <select name="day" class="form-select" onchange="this.form.submit()">
                    @foreach($days as $dayNum => $dayName)
                        <option value="{{ $dayNum }}" {{ $selectedDay == $dayNum ? 'selected' : '' }}>{{ $dayName }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label mb-1">Vaqt oralig'i</label>
                <select name="time_slot" class="form-select" onchange="this.form.submit()">
                    @foreach($timeSlots as $ts)
                        @php $tsVal = $ts->slot_number ?? $ts->id; @endphp
                        <option value="{{ $tsVal }}" {{ $selectedTimeSlot == $tsVal ? 'selected' : '' }}>
                            {{ $ts->name ?? ($ts->slot_number.'-para') }}
                            ({{ substr((string)$ts->start_time, 0, 5) }} - {{ substr((string)$ts->end_time, 0, 5) }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <a href="{{ route('schedule.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Orqaga</a>
                <a href="{{ route('classrooms.index') }}" class="btn btn-outline-primary"><i class="fas fa-door-open"></i> Xonalar</a>
            </div>
        </form>
    </div>

    <!-- Statistika -->
    @php
        $total = $classrooms->count();
        $occupied = $classrooms->filter(fn($r) => $occupiedRooms->has($r->id))->count();
        $free = $total - $occupied;
    @endphp
    <div class="row mb-4 g-3">
        <div class="col-md-4">
            <div class="card border-success"><div class="card-body text-center">
                <h3 class="text-success mb-0">{{ $free }}</h3><p class="mb-0">Bo'sh xonalar</p>
            </div></div>
        </div>
        <div class="col-md-4">
            <div class="card border-danger"><div class="card-body text-center">
                <h3 class="text-danger mb-0">{{ $occupied }}</h3><p class="mb-0">Band xonalar</p>
            </div></div>
        </div>
        <div class="col-md-4">
            <div class="card border-primary"><div class="card-body text-center">
                <h3 class="text-primary mb-0">{{ $total }}</h3><p class="mb-0">Jami xonalar</p>
            </div></div>
        </div>
    </div>

    <!-- Xonalar grid -->
    @if($classrooms->isEmpty())
        <div class="empty-state">
            <div class="empty-state-icon"><i class="fas fa-door-closed"></i></div>
            <div class="empty-state-title">Xonalar yo'q</div>
            <div class="empty-state-sub">"O'quv jarayoni → Xonalar nazorati" orqali xona qo'shing</div>
        </div>
    @else
        <div class="row g-3">
            @foreach($classrooms as $room)
                @php $busy = $occupiedRooms->get($room->id); @endphp
                <div class="col-md-4 col-lg-3">
                    <div class="room-card {{ $busy ? 'occupied' : 'available' }}">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="room-header">{{ $room->name }}</div>
                            <span class="room-status {{ $busy ? 'status-occupied' : 'status-available' }}">
                                {{ $busy ? 'Band' : "Bo'sh" }}
                            </span>
                        </div>
                        @if($room->capacity)<div style="font-size:.8rem;color:#6c757d">{{ $room->capacity }} o'rin</div>@endif
                        @if($busy)
                        <div class="room-details">
                            <div><i class="fas fa-book me-1"></i>{{ $busy->subject->name_uz ?? $busy->subject->name ?? '—' }}</div>
                            <div><i class="fas fa-users me-1"></i>{{ $busy->schedule->group->name ?? '—' }}</div>
                            <div><i class="fas fa-chalkboard-teacher me-1"></i>{{ $busy->teacher->full_name ?? '—' }}</div>
                        </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif

</div>
@endsection

@section('scripts')
<script>
    setTimeout(function () { location.reload(); }, 300000);
</script>
@endsection
