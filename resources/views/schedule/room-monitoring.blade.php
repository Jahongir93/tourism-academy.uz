@extends('layouts.dashboard-new')

@section('title', 'Xonalar nazorati')
@section('page-title', 'Xonalar nazorati')

@section('styles')
<style>
    .room-card {
        border: 2px solid #dee2e6;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 10px;
        transition: all 0.3s;
    }
    .room-card.available {
        background: #d1e7dd;
        border-color: #198754;
    }
    .room-card.occupied {
        background: #f8d7da;
        border-color: #dc3545;
    }
    .room-card:hover {
        transform: translateX(5px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    .room-header {
        font-weight: 600;
        font-size: 1.1rem;
        margin-bottom: 5px;
    }
    .room-status {
        font-size: 0.85rem;
        padding: 3px 10px;
        border-radius: 4px;
        display: inline-block;
    }
    .status-available {
        background: #198754;
        color: white;
    }
    .status-occupied {
        background: #dc3545;
        color: white;
    }
    .room-details {
        font-size: 0.9rem;
        color: #6c757d;
        margin-top: 8px;
    }
    .building-section {
        margin-bottom: 30px;
    }
    .building-header {
        background: linear-gradient(135deg, #0d6efd 0%, #0d47a1 100%);
        color: white;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 15px;
    }
    .floor-section {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 15px;
    }
    .floor-title {
        font-weight: 600;
        color: #495057;
        margin-bottom: 10px;
        padding-bottom: 5px;
        border-bottom: 2px solid #dee2e6;
    }
    .filter-panel {
        background: #e7f3ff;
        border: 1px solid #b6d4fe;
        border-radius: 4px;
        padding: 15px;
        margin-bottom: 20px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Filter Panel -->
    <div class="filter-panel">
        <form method="GET" action="{{ route('schedule.room-monitoring') }}" class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Kun</label>
                <select name="day" class="form-select" onchange="this.form.submit()">
                    @foreach($days as $dayNum => $dayName)
                        <option value="{{ $dayNum }}" {{ $selectedDay == $dayNum ? 'selected' : '' }}>
                            {{ $dayName }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Vaqt oralig'i</label>
                <select name="time_slot" class="form-select" onchange="this.form.submit()">
                    @foreach($timeSlots as $timeSlot)
                        <option value="{{ $timeSlot->id }}" {{ $selectedTimeSlot == $timeSlot->id ? 'selected' : '' }}>
                            {{ $timeSlot->name }} ({{ substr($timeSlot->start_time, 0, 5) }} - {{ substr($timeSlot->end_time, 0, 5) }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">&nbsp;</label>
                <div>
                    <a href="{{ route('schedule.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Orqaga
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-success">
                <div class="card-body text-center">
                    <h3 class="text-success">{{ $buildings->sum(fn($b) => $b->classrooms->count()) - $occupiedRooms->count() }}</h3>
                    <p class="mb-0">Bo'sh xonalar</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-danger">
                <div class="card-body text-center">
                    <h3 class="text-danger">{{ $occupiedRooms->count() }}</h3>
                    <p class="mb-0">Band xonalar</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-primary">
                <div class="card-body text-center">
                    <h3 class="text-primary">{{ $buildings->sum(fn($b) => $b->classrooms->count()) }}</h3>
                    <p class="mb-0">Jami xonalar</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-info">
                <div class="card-body text-center">
                    <h3 class="text-info">{{ $buildings->count() }}</h3>
                    <p class="mb-0">Jami binolar</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Buildings and Rooms -->
    @foreach($buildings as $building)
        <div class="building-section">
            <div class="building-header">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h4 class="mb-1"><i class="fas fa-building"></i> {{ $building->name }}</h4>
                        <small>{{ $building->code }} | {{ $building->address }}</small>
                    </div>
                    <div class="col-md-4 text-end">
                        <span class="badge bg-light text-dark">{{ $building->total_floors }} ta etaj</span>
                        <span class="badge bg-light text-dark">{{ $building->classrooms->count() }} ta xona</span>
                    </div>
                </div>
            </div>

            @php
                $classroomsByFloor = $building->classrooms->groupBy('floor');
            @endphp

            @foreach($classroomsByFloor->sortKeys() as $floor => $classrooms)
                <div class="floor-section">
                    <div class="floor-title">
                        <i class="fas fa-layer-group"></i> {{ $floor }}-etaj
                        <span class="badge bg-secondary float-end">{{ $classrooms->count() }} ta xona</span>
                    </div>

                    <div class="row">
                        @foreach($classrooms as $classroom)
                            @php
                                $isOccupied = isset($occupiedRooms[$classroom->id]);
                                $schedule = $isOccupied ? $occupiedRooms[$classroom->id] : null;
                            @endphp

                            <div class="col-md-4">
                                <div class="room-card {{ $isOccupied ? 'occupied' : 'available' }}">
                                    <div class="room-header">
                                        {{ $classroom->name }}
                                        <span class="room-status {{ $isOccupied ? 'status-occupied' : 'status-available' }}">
                                            {{ $isOccupied ? 'Band' : 'Bo\'sh' }}
                                        </span>
                                    </div>

                                    <div class="room-details">
                                        <div>
                                            <i class="fas fa-chair"></i> Sig'im: {{ $classroom->capacity }} kishi
                                        </div>
                                        <div>
                                            <i class="fas fa-tag"></i> {{ $classroom->type_name }}
                                        </div>
                                        @if($classroom->has_projector)
                                            <span class="badge bg-info">Proyektor</span>
                                        @endif
                                        @if($classroom->has_computer)
                                            <span class="badge bg-info">Kompyuter</span>
                                        @endif
                                    </div>

                                    @if($isOccupied)
                                        <div class="mt-2 pt-2 border-top border-danger">
                                            <small>
                                                <strong>Fan:</strong> {{ $schedule->subject->name_uz }}<br>
                                                <strong>Guruh:</strong> {{ $schedule->group->name }}<br>
                                                <strong>O'qituvchi:</strong> {{ $schedule->teacher->full_name }}
                                            </small>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    @endforeach

    @if($buildings->isEmpty())
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> Binolar topilmadi. Iltimos, avval binolarni qo'shing.
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
    // Auto-refresh every 5 minutes
    setTimeout(function() {
        location.reload();
    }, 300000);
</script>
@endsection
