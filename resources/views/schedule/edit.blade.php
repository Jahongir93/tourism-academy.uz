@extends('layouts.dashboard-new')

@section('title', 'Darsni tahrirlash')
@section('page-title', 'Darsni tahrirlash')

@section('content')
<div class="page-header">
    <div class="page-header-left">
        <h1 class="page-header-title">Darsni tahrirlash</h1>
        <p class="page-header-sub">Dars jadvali yozuvini o'zgartirish</p>
    </div>
    <div class="page-header-actions">
        <a href="{{ route('schedule.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left"></i> Orqaga</a>
    </div>
</div>

<div class="card">
    <div class="card-header"><i class="fas fa-edit" style="color:var(--c-primary)"></i> Dars ma'lumotlari</div>
    <div class="card-body">
        <form action="{{ route('schedule.update', $schedule->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row g-3">
                {{-- Fan --}}
                <div class="col-md-6">
                    <label class="form-label">Fan <span class="text-danger">*</span></label>
                    <select name="subject_id" class="form-select @error('subject_id') is-invalid @enderror" required>
                        <option value="">Tanlang</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}" {{ old('subject_id', $schedule->subject_id) == $subject->id ? 'selected' : '' }}>
                                {{ $subject->name_uz ?? $subject->name }} ({{ $subject->code }})
                            </option>
                        @endforeach
                    </select>
                    @error('subject_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- O'qituvchi --}}
                <div class="col-md-6">
                    <label class="form-label">O'qituvchi <span class="text-danger">*</span></label>
                    <select name="teacher_id" class="form-select @error('teacher_id') is-invalid @enderror" required>
                        <option value="">Tanlang</option>
                        @foreach($teachers as $teacher)
                            <option value="{{ $teacher->id }}" {{ old('teacher_id', $schedule->teacher_id) == $teacher->id ? 'selected' : '' }}>
                                {{ $teacher->full_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('teacher_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Xona --}}
                <div class="col-md-6">
                    <label class="form-label">Xona <span class="text-danger">*</span></label>
                    <select name="classroom_id" class="form-select @error('classroom_id') is-invalid @enderror" required>
                        <option value="">Tanlang</option>
                        @foreach($classrooms as $classroom)
                            <option value="{{ $classroom->id }}" {{ old('classroom_id', $schedule->classroom_id) == $classroom->id ? 'selected' : '' }}>
                                {{ $classroom->name }}{{ $classroom->capacity ? ' (' . $classroom->capacity . " o'rin)" : '' }}
                            </option>
                        @endforeach
                    </select>
                    @error('classroom_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Dars turi --}}
                <div class="col-md-6">
                    <label class="form-label">Dars turi <span class="text-danger">*</span></label>
                    <select name="lesson_type" class="form-select" required>
                        @foreach(['lecture'=>'Ma\'ruza','practice'=>'Amaliyot','lab'=>'Laboratoriya','seminar'=>'Seminar'] as $k=>$v)
                            <option value="{{ $k }}" {{ old('lesson_type', $schedule->lesson_type) == $k ? 'selected' : '' }}>{{ $v }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Hafta kuni --}}
                <div class="col-md-6">
                    <label class="form-label">Hafta kuni <span class="text-danger">*</span></label>
                    <select name="day_of_week" class="form-select @error('day_of_week') is-invalid @enderror" required>
                        @foreach([1=>'Dushanba',2=>'Seshanba',3=>'Chorshanba',4=>'Payshanba',5=>'Juma',6=>'Shanba'] as $k=>$v)
                            <option value="{{ $k }}" {{ old('day_of_week', $schedule->day_of_week) == $k ? 'selected' : '' }}>{{ $v }}</option>
                        @endforeach
                    </select>
                    @error('day_of_week')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- Dars vaqti --}}
                <div class="col-md-6">
                    <label class="form-label">Dars vaqti <span class="text-danger">*</span></label>
                    <select name="time_slot_id" class="form-select @error('time_slot_id') is-invalid @enderror" required>
                        @foreach($timeSlots as $timeSlot)
                            <option value="{{ $timeSlot->slot_number ?? $timeSlot->id }}"
                                {{ old('time_slot_id', $schedule->time_slot_id) == ($timeSlot->slot_number ?? $timeSlot->id) ? 'selected' : '' }}>
                                {{ $timeSlot->name ?? ($timeSlot->slot_number . '-para') }}
                                ({{ substr($timeSlot->start_time, 0, 5) }} - {{ substr($timeSlot->end_time, 0, 5) }})
                            </option>
                        @endforeach
                    </select>
                    @error('time_slot_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            @if($errors->has('conflict') || $errors->has('teacher_conflict') || $errors->has('room_conflict'))
            <div class="alert alert-warning mt-3">
                {{ $errors->first('conflict') }} {{ $errors->first('teacher_conflict') }} {{ $errors->first('room_conflict') }}
            </div>
            @endif

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Saqlash</button>
                <a href="{{ route('schedule.index') }}" class="btn btn-outline-secondary">Bekor qilish</a>
            </div>
        </form>
    </div>
</div>
@endsection
