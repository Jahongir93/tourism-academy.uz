@extends('layouts.dashboard-new')

@section('title', 'Jadval tahrirlash')
@section('page-title', 'Dars jadvali tahrirlash')

@section('content')
<div class="container-fluid">
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm bg-gradient-primary text-white">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1"><i class="fas fa-edit me-2"></i>{{ $schedule->group?->name }} - Dars jadvali</h4>
                            <p class="mb-0 opacity-75">{{ $schedule->academicYear?->name ?? '' }} {{ $schedule->semester_id }}-semestr</p>
                        </div>
                        <div>
                            <a href="{{ route('dean.schedule.show', $schedule) }}" class="btn btn-light me-2">
                                <i class="fas fa-eye me-1"></i> Ko'rish
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

    <div class="row">
        <!-- Jadval status -->
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-cog text-primary me-2"></i>Jadval sozlamalari</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('dean.schedule.update', $schedule) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Status</label>
                            <select name="status" class="form-select">
                                <option value="draft" {{ $schedule->status == 'draft' ? 'selected' : '' }}>Qoralama</option>
                                <option value="active" {{ $schedule->status == 'active' ? 'selected' : '' }}>Faol</option>
                                <option value="archived" {{ $schedule->status == 'archived' ? 'selected' : '' }}>Arxiv</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-save me-1"></i> Saqlash
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Yangi dars qo'shish -->
        <div class="col-md-8 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-plus-circle text-success me-2"></i>Yangi dars qo'shish</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('dean.schedule.slots.store', $schedule) }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Kun</label>
                                <select name="day_of_week" class="form-select" required>
                                    @foreach($days as $num => $day)
                                    <option value="{{ $num }}">{{ $day }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Vaqt</label>
                                <select name="time_slot" class="form-select" required>
                                    @foreach($timeSlots as $num => $time)
                                    <option value="{{ $num }}">{{ $num }}-para ({{ $time }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Hafta turi</label>
                                <select name="week_type" class="form-select">
                                    <option value="all">Har hafta</option>
                                    <option value="odd">Toq hafta</option>
                                    <option value="even">Juft hafta</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Fan</label>
                                <select name="subject_id" class="form-select" required>
                                    <option value="">-- Fanni tanlang --</option>
                                    @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}">{{ $subject->name_uz }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">O'qituvchi</label>
                                <select name="teacher_id" class="form-select">
                                    <option value="">-- O'qituvchini tanlang --</option>
                                    @foreach($teachers as $teacher)
                                    <option value="{{ $teacher->id }}">{{ $teacher->last_name }} {{ $teacher->first_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Xona</label>
                                <select name="room_id" class="form-select">
                                    <option value="">-- Xonani tanlang --</option>
                                    @foreach($rooms as $room)
                                    <option value="{{ $room->id }}">{{ $room->name }} ({{ $room->capacity }} o'rin)</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Dars turi</label>
                                <select name="lesson_type" class="form-select">
                                    <option value="lecture">Ma'ruza</option>
                                    <option value="practice">Amaliy</option>
                                    <option value="lab">Laboratoriya</option>
                                    <option value="seminar">Seminar</option>
                                </select>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-plus me-1"></i> Qo'shish
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Mavjud darslar jadvali -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0"><i class="fas fa-table text-primary me-2"></i>Dars jadvali</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered mb-0">
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
                            @foreach($days as $dayNum => $dayName)
                            <td class="p-1">
                                @php
                                    $slot = $schedule->slots->first(function($s) use ($dayNum, $slotNum) {
                                        return $s->day_number == $dayNum && $s->time_slot == $slotNum;
                                    });
                                @endphp
                                @if($slot)
                                <div class="card border-0 bg-primary bg-opacity-10 h-100">
                                    <div class="card-body p-2">
                                        <div class="fw-semibold text-primary small">{{ $slot->subject?->name_uz ?? '-' }}</div>
                                        <div class="text-muted small">{{ $slot->teacher?->full_name ?? '-' }}</div>
                                        <div class="text-muted small">{{ $slot->room?->name ?? '-' }}</div>
                                        @if($slot->week_type != 'all')
                                        <span class="badge bg-warning text-dark small">{{ $slot->week_type == 'odd' ? 'Toq' : 'Juft' }}</span>
                                        @endif
                                        <form action="{{ route('dean.schedule.slots.destroy', $slot) }}" method="POST" class="mt-1"
                                              onsubmit="return confirm('O\'chirishni tasdiqlaysizmi?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger py-0 px-1">
                                                <i class="fas fa-trash fa-xs"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                @else
                                <div class="text-center text-muted py-3">
                                    <i class="fas fa-minus"></i>
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

<style>.bg-gradient-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }</style>
@endsection
