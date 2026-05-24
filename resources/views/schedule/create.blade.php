@extends('layouts.dashboard-new')

@section('title', 'Yangi dars qo\'shish')
@section('page-title', 'Yangi dars qo\'shish')

@section('styles')
<style>
    .smart-suggestion {
        background: #e7f3ff;
        border-left: 3px solid #0d6efd;
        padding: 10px;
        margin-bottom: 10px;
        border-radius: 4px;
        cursor: pointer;
        transition: all 0.2s;
    }
    .smart-suggestion:hover {
        background: #d0e7ff;
        transform: translateX(5px);
    }
    .conflict-warning {
        background: #fff3cd;
        border-left: 3px solid #ffc107;
        padding: 10px;
        margin-top: 10px;
    }
    .success-hint {
        background: #d1e7dd;
        border-left: 3px solid #198754;
        padding: 10px;
        margin-top: 10px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Form Section -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Dars ma'lumotlari</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('schedule.store') }}" method="POST" id="scheduleForm">
                        @csrf

                        <div class="row">
                            <!-- Group Selection -->
                            <div class="col-md-6 mb-3">
                                <label for="group_id" class="form-label">
                                    Guruh <span class="text-danger">*</span>
                                </label>
                                <select name="group_id" id="group_id" class="form-select @error('group_id') is-invalid @enderror" required>
                                    <option value="">Guruhni tanlang</option>
                                    @foreach($groups as $group)
                                        <option value="{{ $group->id }}"
                                                data-course="{{ $group->course }}"
                                                {{ old('group_id') == $group->id ? 'selected' : '' }}>
                                            {{ $group->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('group_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Subject Selection -->
                            <div class="col-md-6 mb-3">
                                <label for="subject_id" class="form-label">
                                    Fan <span class="text-danger">*</span>
                                </label>
                                <select name="subject_id" id="subject_id" class="form-select @error('subject_id') is-invalid @enderror" required>
                                    <option value="">Avval guruhni tanlang</option>
                                    @foreach($subjects as $subject)
                                        <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
                                            {{ $subject->name_uz ?? $subject->name }} ({{ $subject->code }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('subject_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div id="subjectHint" class="form-text"></div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Teacher Selection -->
                            <div class="col-md-6 mb-3">
                                <label for="teacher_id" class="form-label">
                                    O'qituvchi <span class="text-danger">*</span>
                                </label>
                                <select name="teacher_id" id="teacher_id" class="form-select @error('teacher_id') is-invalid @enderror" required>
                                    <option value="">O'qituvchini tanlang</option>
                                    @foreach($teachers as $teacher)
                                        <option value="{{ $teacher->id }}" {{ old('teacher_id') == $teacher->id ? 'selected' : '' }}>
                                            {{ $teacher->full_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('teacher_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Classroom Selection -->
                            <div class="col-md-6 mb-3">
                                <label for="classroom_id" class="form-label">
                                    Xona <span class="text-danger">*</span>
                                </label>
                                <select name="classroom_id" id="classroom_id" class="form-select @error('classroom_id') is-invalid @enderror" required>
                                    <option value="">Xonani tanlang</option>
                                    @foreach($classrooms as $classroom)
                                        <option value="{{ $classroom->id }}"
                                                data-capacity="{{ $classroom->capacity }}"
                                                {{ old('classroom_id') == $classroom->id ? 'selected' : '' }}>
                                            {{ $classroom->name }} ({{ $classroom->capacity }} o'rin)
                                        </option>
                                    @endforeach
                                </select>
                                @error('classroom_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div id="capacityHint" class="form-text"></div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Day of Week -->
                            <div class="col-md-6 mb-3">
                                <label for="day_of_week" class="form-label">
                                    Hafta kuni <span class="text-danger">*</span>
                                </label>
                                <select name="day_of_week" id="day_of_week" class="form-select @error('day_of_week') is-invalid @enderror" required>
                                    <option value="">Kunni tanlang</option>
                                    <option value="1" {{ old('day_of_week') == 1 ? 'selected' : '' }}>Dushanba</option>
                                    <option value="2" {{ old('day_of_week') == 2 ? 'selected' : '' }}>Seshanba</option>
                                    <option value="3" {{ old('day_of_week') == 3 ? 'selected' : '' }}>Chorshanba</option>
                                    <option value="4" {{ old('day_of_week') == 4 ? 'selected' : '' }}>Payshanba</option>
                                    <option value="5" {{ old('day_of_week') == 5 ? 'selected' : '' }}>Juma</option>
                                    <option value="6" {{ old('day_of_week') == 6 ? 'selected' : '' }}>Shanba</option>
                                </select>
                                @error('day_of_week')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Time Slot -->
                            <div class="col-md-6 mb-3">
                                <label for="time_slot_id" class="form-label">
                                    Dars vaqti <span class="text-danger">*</span>
                                </label>
                                <select name="time_slot_id" id="time_slot_id" class="form-select @error('time_slot_id') is-invalid @enderror" required>
                                    <option value="">Vaqtni tanlang</option>
                                    @foreach($timeSlots as $timeSlot)
                                        <option value="{{ $timeSlot->id }}" {{ old('time_slot_id') == $timeSlot->id ? 'selected' : '' }}>
                                            {{ $timeSlot->name ?? $timeSlot->slot_number . '-para' }} ({{ substr($timeSlot->start_time, 0, 5) }} - {{ substr($timeSlot->end_time, 0, 5) }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('time_slot_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <!-- Lesson Type -->
                            <div class="col-md-4 mb-3">
                                <label for="lesson_type" class="form-label">
                                    Dars turi <span class="text-danger">*</span>
                                </label>
                                <select name="lesson_type" id="lesson_type" class="form-select @error('lesson_type') is-invalid @enderror" required>
                                    <option value="lecture" {{ old('lesson_type') == 'lecture' ? 'selected' : '' }}>Ma'ruza</option>
                                    <option value="practice" {{ old('lesson_type') == 'practice' ? 'selected' : '' }}>Amaliyot</option>
                                    <option value="lab" {{ old('lesson_type') == 'lab' ? 'selected' : '' }}>Laboratoriya</option>
                                    <option value="seminar" {{ old('lesson_type') == 'seminar' ? 'selected' : '' }}>Seminar</option>
                                </select>
                                @error('lesson_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Academic Year -->
                            <div class="col-md-4 mb-3">
                                <label for="academic_year" class="form-label">
                                    O'quv yili <span class="text-danger">*</span>
                                </label>
                                <select name="academic_year" id="academic_year" class="form-select @error('academic_year') is-invalid @enderror" required>
                                    @php
                                        $currentYear = date('Y');
                                    @endphp
                                    <option value="{{ $currentYear }}-{{ $currentYear + 1 }}" {{ old('academic_year') == $currentYear.'-'.($currentYear + 1) ? 'selected' : '' }}>
                                        {{ $currentYear }}-{{ $currentYear + 1 }}
                                    </option>
                                    <option value="{{ $currentYear - 1 }}-{{ $currentYear }}" {{ old('academic_year') == ($currentYear - 1).'-'.$currentYear ? 'selected' : '' }}>
                                        {{ $currentYear - 1 }}-{{ $currentYear }}
                                    </option>
                                </select>
                                @error('academic_year')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Semester -->
                            <div class="col-md-4 mb-3">
                                <label for="semester" class="form-label">
                                    Semestr <span class="text-danger">*</span>
                                </label>
                                <select name="semester" id="semester" class="form-select @error('semester') is-invalid @enderror" required>
                                    @for($i = 1; $i <= 8; $i++)
                                        <option value="{{ $i }}" {{ old('semester', 1) == $i ? 'selected' : '' }}>{{ $i }}-semestr</option>
                                    @endfor
                                </select>
                                @error('semester')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Conflict Warning Area -->
                        <div id="conflictWarning"></div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('schedule.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Bekor qilish
                            </a>
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="fas fa-save"></i> Saqlash
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Helper Section -->
        <div class="col-md-4">
            <!-- Quick Tips -->
            <div class="card mb-3">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="fas fa-lightbulb"></i> Maslahatlar</h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success"></i> Avval guruhni tanlang
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success"></i> Xonaning sig'imi talabalar soniga mos kelishini tekshiring
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success"></i> O'qituvchi va xona bandligini tekshiring
                        </li>
                        <li>
                            <i class="fas fa-check-circle text-success"></i> Bir kunda ortiqcha darslar qo'ymang
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Current Selection Info -->
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0"><i class="fas fa-info-circle"></i> Tanlangan ma'lumotlar</h6>
                </div>
                <div class="card-body">
                    <div id="selectionInfo" class="text-muted">
                        Ma'lumotlarni tanlang
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('scheduleForm');
    const groupSelect = document.getElementById('group_id');
    const subjectSelect = document.getElementById('subject_id');
    const teacherSelect = document.getElementById('teacher_id');
    const classroomSelect = document.getElementById('classroom_id');
    const daySelect = document.getElementById('day_of_week');
    const timeSlotSelect = document.getElementById('time_slot_id');
    const conflictWarning = document.getElementById('conflictWarning');
    const selectionInfo = document.getElementById('selectionInfo');

    // Update selection info
    function updateSelectionInfo() {
        let info = '<small>';

        if (groupSelect.selectedIndex > 0) {
            info += '<strong>Guruh:</strong> ' + groupSelect.options[groupSelect.selectedIndex].text + '<br>';
        }

        if (subjectSelect.selectedIndex > 0) {
            info += '<strong>Fan:</strong> ' + subjectSelect.options[subjectSelect.selectedIndex].text + '<br>';
        }

        if (teacherSelect.selectedIndex > 0) {
            info += '<strong>O\'qituvchi:</strong> ' + teacherSelect.options[teacherSelect.selectedIndex].text + '<br>';
        }

        if (classroomSelect.selectedIndex > 0) {
            info += '<strong>Xona:</strong> ' + classroomSelect.options[classroomSelect.selectedIndex].text + '<br>';
        }

        if (daySelect.selectedIndex > 0 && timeSlotSelect.selectedIndex > 0) {
            info += '<strong>Vaqt:</strong> ' + daySelect.options[daySelect.selectedIndex].text + ', ' + timeSlotSelect.options[timeSlotSelect.selectedIndex].text;
        }

        info += '</small>';
        selectionInfo.innerHTML = info === '<small></small>' ? '<span class="text-muted">Ma\'lumotlarni tanlang</span>' : info;
    }

    // Add change listeners
    [groupSelect, subjectSelect, teacherSelect, classroomSelect, daySelect, timeSlotSelect].forEach(select => {
        select.addEventListener('change', updateSelectionInfo);
    });

    // Check classroom capacity
    groupSelect.addEventListener('change', function() {
        const capacityHint = document.getElementById('capacityHint');
        if (classroomSelect.selectedIndex > 0 && groupSelect.selectedIndex > 0) {
            const capacity = parseInt(classroomSelect.options[classroomSelect.selectedIndex].dataset.capacity);
            capacityHint.innerHTML = '<i class="fas fa-info-circle"></i> Guruh talabalar soni xona sig\'imiga mos kelishini tekshiring';
        }
    });
});
</script>
@endsection
