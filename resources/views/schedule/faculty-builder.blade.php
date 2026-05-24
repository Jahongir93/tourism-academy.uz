@extends('layouts.dashboard-new')

@section('title', 'Fakultet asosida dars jadvali tuzish')
@section('page-title', 'Fakultet asosida dars jadvali tuzish')

@section('styles')
<style>
    .schedule-grid {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.85rem;
    }
    .schedule-grid th,
    .schedule-grid td {
        border: 1px solid #dee2e6;
        padding: 8px;
        text-align: center;
    }
    .schedule-grid th {
        background: #0d6efd;
        color: white;
        font-weight: 600;
    }
    .time-slot-header {
        background: #6c757d !important;
        width: 100px;
    }
    .schedule-cell {
        height: 80px;
        cursor: pointer;
        transition: all 0.2s;
        position: relative;
        vertical-align: middle;
    }
    .schedule-cell:hover {
        background: #f8f9fa;
    }
    .schedule-cell.filled {
        background: #d1e7dd;
        cursor: default;
    }
    .schedule-cell.filled:hover {
        background: #c3e0cf;
    }
    .schedule-item {
        padding: 5px;
        min-height: 60px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }
    .schedule-item .text-muted {
        font-size: 1.5rem;
        color: #adb5bd;
    }
    .schedule-subject {
        font-weight: 600;
        color: #0d6efd;
        font-size: 0.9rem;
    }
    .schedule-details {
        font-size: 0.75rem;
        color: #6c757d;
    }
    .delete-slot-btn {
        position: absolute;
        top: 2px;
        right: 2px;
        padding: 2px 6px;
        font-size: 0.7rem;
    }
    .selection-panel {
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 4px;
        padding: 15px;
        margin-bottom: 15px;
    }
    .action-panel {
        background: #e7f3ff;
        border: 1px solid #b6d4fe;
        border-radius: 4px;
        padding: 15px;
        margin-bottom: 15px;
    }
    .loading {
        opacity: 0.5;
        pointer-events: none;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Selection Panel -->
    <div class="selection-panel">
        <div class="row g-3">
            <div class="col-md-4">
                <label for="facultySelect" class="form-label">Fakultet <span class="text-danger">*</span></label>
                <select id="facultySelect" class="form-select">
                    <option value="">Fakultetni tanlang</option>
                    @foreach($faculties as $faculty)
                        <option value="{{ $faculty->id }}">{{ $faculty->name_uz ?? $faculty->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label for="courseSelect" class="form-label">Kurs <span class="text-danger">*</span></label>
                <select id="courseSelect" class="form-select" disabled>
                    <option value="">Avval fakultetni tanlang</option>
                </select>
            </div>
            <div class="col-md-4">
                <label for="groupSelect" class="form-label">Guruh <span class="text-danger">*</span></label>
                <select id="groupSelect" class="form-select" disabled>
                    <option value="">Avval kursni tanlang</option>
                </select>
            </div>
        </div>

        <div class="mt-3" id="groupInfo" style="display: none;">
            <div class="alert alert-info mb-0">
                <strong>Tanlangan guruh:</strong> <span id="groupName"></span> |
                <strong>Talabalar soni:</strong> <span id="groupStudents"></span>
            </div>
        </div>
    </div>

    <!-- Action Panel -->
    <div class="action-panel" id="actionPanel" style="display: none;">
        <div class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Jadvalni qo'llash</label>
                <select id="periodType" class="form-select form-select-sm">
                    <option value="week">1 hafta</option>
                    <option value="month">1 oy</option>
                    <option value="semester">1 semestr</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Boshlanish sanasi</label>
                <input type="date" id="startDate" class="form-control form-control-sm" value="{{ date('Y-m-d') }}">
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-success btn-sm w-100" onclick="applySchedule()">
                    <i class="fas fa-check"></i> Joriy qilish
                </button>
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-warning btn-sm w-100" onclick="autoGenerate()">
                    <i class="fas fa-magic"></i> Avtomatik tuzish
                </button>
            </div>
            <div class="col-md-2">
                <a href="{{ route('schedule.index') }}" class="btn btn-secondary btn-sm w-100">
                    <i class="fas fa-arrow-left"></i> Orqaga
                </a>
            </div>
        </div>
    </div>

    <!-- Schedule Grid -->
    <div class="card" id="scheduleCard" style="display: none;">
        <div class="card-header">
            <h5 class="mb-0">Haftalik jadval - <span id="currentGroupName"></span></h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="schedule-grid">
                    <thead>
                        <tr>
                            <th class="time-slot-header">Vaqt</th>
                            @foreach($days as $dayNum => $dayName)
                                <th>{{ $dayName }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody id="scheduleTableBody">
                        @foreach($timeSlots as $timeSlot)
                            <tr>
                                <td class="time-slot-header">
                                    <div>{{ $timeSlot->slot_number }}-para</div>
                                    <small>{{ substr($timeSlot->start_time, 0, 5) }} - {{ substr($timeSlot->end_time, 0, 5) }}</small>
                                </td>
                                @foreach($days as $dayNum => $dayName)
                                    <td class="schedule-cell"
                                        data-day="{{ $dayNum }}"
                                        data-timeslot="{{ $timeSlot->id }}"
                                        onclick="openSlotModal(event, {{ $dayNum }}, {{ $timeSlot->id }}, '{{ $dayName }}', '{{ $timeSlot->slot_number }}-para ({{ substr($timeSlot->start_time, 0, 5) }}-{{ substr($timeSlot->end_time, 0, 5) }})')">
                                        <div class="schedule-item" id="cell-{{ $dayNum }}-{{ $timeSlot->id }}">
                                            <span class="text-muted">+</span>
                                        </div>
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

<!-- Slot Assignment Modal -->
<div class="modal fade" id="slotModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Dars birikitirish</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <div class="alert alert-info">
                        <strong>Kun:</strong> <span id="modalDay"></span> |
                        <strong>Vaqt:</strong> <span id="modalTime"></span>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="subjectSelect" class="form-label">Fan <span class="text-danger">*</span></label>
                    <select id="subjectSelect" class="form-select">
                        <option value="">Fanni tanlang</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="teacherSelect" class="form-label">O'qituvchi <span class="text-danger">*</span></label>
                    <select id="teacherSelect" class="form-select">
                        <option value="">O'qituvchini tanlang</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="classroomSelect" class="form-label">Xona <span class="text-danger">*</span></label>
                    <select id="classroomSelect" class="form-select">
                        <option value="">Xonani tanlang</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="lessonTypeSelect" class="form-label">Dars turi</label>
                    <select id="lessonTypeSelect" class="form-select">
                        <option value="lecture">Ma'ruza</option>
                        <option value="practice">Amaliyot</option>
                        <option value="lab">Laboratoriya</option>
                        <option value="seminar">Seminar</option>
                    </select>
                </div>

                <div id="conflictWarning" class="alert alert-warning" style="display: none;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bekor qilish</button>
                <button type="button" class="btn btn-primary" onclick="saveSlot()">Saqlash</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
let currentGroupId = null;
let currentDay = null;
let currentTimeSlot = null;
let scheduleData = {};
let subjects = [];
let teachers = [];
let classrooms = [];

// Faculty selection handler
document.getElementById('facultySelect').addEventListener('change', function() {
    const facultyId = this.value;
    const courseSelect = document.getElementById('courseSelect');

    courseSelect.innerHTML = '<option value="">Yuklanmoqda...</option>';
    courseSelect.disabled = true;
    document.getElementById('groupSelect').disabled = true;
    document.getElementById('groupSelect').innerHTML = '<option value="">Avval kursni tanlang</option>';
    document.getElementById('scheduleCard').style.display = 'none';
    document.getElementById('groupInfo').style.display = 'none';
    document.getElementById('actionPanel').style.display = 'none';

    if (!facultyId) {
        courseSelect.innerHTML = '<option value="">Avval fakultetni tanlang</option>';
        return;
    }

    fetch('{{ route("schedule.faculty.get-courses") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ faculty_id: facultyId })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        console.log('Courses loaded:', data);
        courseSelect.innerHTML = '<option value="">Kursni tanlang</option>';
        data.courses.forEach(course => {
            courseSelect.innerHTML += `<option value="${course}">${course}-kurs</option>`;
        });
        courseSelect.disabled = false;
    })
    .catch(error => {
        console.error('Error loading courses:', error);
        courseSelect.innerHTML = '<option value="">Xatolik yuz berdi</option>';
        alert('Kurslarni yuklashda xatolik: ' + error.message);
    });
});

// Course selection handler
document.getElementById('courseSelect').addEventListener('change', function() {
    const facultyId = document.getElementById('facultySelect').value;
    const course = this.value;
    const groupSelect = document.getElementById('groupSelect');

    groupSelect.innerHTML = '<option value="">Yuklanmoqda...</option>';
    groupSelect.disabled = true;
    document.getElementById('scheduleCard').style.display = 'none';
    document.getElementById('groupInfo').style.display = 'none';
    document.getElementById('actionPanel').style.display = 'none';

    if (!course) {
        groupSelect.innerHTML = '<option value="">Avval kursni tanlang</option>';
        return;
    }

    fetch('{{ route("schedule.faculty.get-groups") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ faculty_id: facultyId, course: course })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        console.log('Groups loaded:', data);
        groupSelect.innerHTML = '<option value="">Guruhni tanlang</option>';
        data.groups.forEach(group => {
            groupSelect.innerHTML += `<option value="${group.id}" data-students="${group.current_students}">${group.name}</option>`;
        });
        groupSelect.disabled = false;
    })
    .catch(error => {
        console.error('Error loading groups:', error);
        groupSelect.innerHTML = '<option value="">Xatolik yuz berdi</option>';
        alert('Guruhlarni yuklashda xatolik: ' + error.message);
    });
});

// Group selection handler
document.getElementById('groupSelect').addEventListener('change', function() {
    const groupId = this.value;

    if (!groupId) {
        document.getElementById('scheduleCard').style.display = 'none';
        document.getElementById('groupInfo').style.display = 'none';
        document.getElementById('actionPanel').style.display = 'none';
        return;
    }

    currentGroupId = groupId;
    const groupName = this.options[this.selectedIndex].text;
    const groupStudents = this.options[this.selectedIndex].dataset.students || '0';

    document.getElementById('groupName').textContent = groupName;
    document.getElementById('groupStudents').textContent = groupStudents;
    document.getElementById('currentGroupName').textContent = groupName;
    document.getElementById('groupInfo').style.display = 'block';
    document.getElementById('actionPanel').style.display = 'block';

    loadScheduleGrid(groupId);
});

function loadScheduleGrid(groupId) {
    console.log('Loading schedule grid for group:', groupId);
    document.getElementById('scheduleCard').classList.add('loading');
    document.getElementById('scheduleCard').style.display = 'block';

    fetch('{{ route("schedule.faculty.get-schedule-grid") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ group_id: groupId })
    })
    .then(response => {
        console.log('Schedule grid response status:', response.status);
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        console.log('Schedule grid data loaded:', data);
        scheduleData = data.schedules;
        subjects = data.subjects;
        teachers = data.teachers;
        classrooms = data.classrooms;

        renderScheduleGrid();
        document.getElementById('scheduleCard').classList.remove('loading');
    })
    .catch(error => {
        console.error('Error loading schedule grid:', error);
        document.getElementById('scheduleCard').classList.remove('loading');
        alert('Jadvalni yuklashda xatolik: ' + error.message);
    });
}

function renderScheduleGrid() {
    // Clear all cells first
    document.querySelectorAll('.schedule-cell').forEach(cell => {
        const day = cell.dataset.day;
        const timeslot = cell.dataset.timeslot;
        const cellId = `cell-${day}-${timeslot}`;
        document.getElementById(cellId).innerHTML = '<span class="text-muted">+</span>';
        cell.classList.remove('filled');
    });

    // Fill cells with schedule data
    Object.keys(scheduleData).forEach(key => {
        const schedules = scheduleData[key];
        if (schedules.length > 0) {
            const schedule = schedules[0];
            const cellId = `cell-${schedule.day_of_week}-${schedule.time_slot_id}`;
            const cell = document.getElementById(cellId);
            if (cell) {
                cell.innerHTML = `
                    <button class="delete-slot-btn btn btn-danger btn-sm" onclick="event.stopPropagation(); deleteSlot(${schedule.day_of_week}, ${schedule.time_slot_id})">
                        <i class="fas fa-times"></i>
                    </button>
                    <div class="schedule-subject">${schedule.subject.name_uz}</div>
                    <div class="schedule-details">
                        <div><i class="fas fa-user"></i> ${schedule.teacher.last_name} ${schedule.teacher.first_name}</div>
                        <div><i class="fas fa-door-open"></i> ${schedule.classroom.name}</div>
                    </div>
                `;
                cell.parentElement.classList.add('filled');
            }
        }
    });
}

function openSlotModal(event, day, timeslot, dayName, timeName) {
    console.log('Opening slot modal:', {day, timeslot, dayName, timeName});

    if (event.target.classList.contains('delete-slot-btn') || event.target.closest('.delete-slot-btn')) {
        console.log('Clicked on delete button, ignoring');
        return;
    }

    const cell = document.querySelector(`[data-day="${day}"][data-timeslot="${timeslot}"]`);
    if (cell.classList.contains('filled')) {
        console.log('Cell already filled, ignoring');
        return;
    }

    currentDay = day;
    currentTimeSlot = timeslot;

    document.getElementById('modalDay').textContent = dayName;
    document.getElementById('modalTime').textContent = timeName;

    console.log('Populating selects with data:', {
        subjectsCount: subjects.length,
        teachersCount: teachers.length,
        classroomsCount: classrooms.length
    });

    // Populate subject select
    const subjectSelect = document.getElementById('subjectSelect');
    subjectSelect.innerHTML = '<option value="">Fanni tanlang</option>';
    subjects.forEach(subject => {
        subjectSelect.innerHTML += `<option value="${subject.id}">${subject.name_uz} (${subject.code})</option>`;
    });

    // Populate teacher select
    const teacherSelect = document.getElementById('teacherSelect');
    teacherSelect.innerHTML = '<option value="">O\'qituvchini tanlang</option>';
    teachers.forEach(teacher => {
        teacherSelect.innerHTML += `<option value="${teacher.id}">${teacher.last_name} ${teacher.first_name}</option>`;
    });

    // Populate classroom select
    const classroomSelect = document.getElementById('classroomSelect');
    classroomSelect.innerHTML = '<option value="">Xonani tanlang</option>';
    classrooms.forEach(classroom => {
        const buildingName = classroom.building ? classroom.building.code + '-' : '';
        classroomSelect.innerHTML += `<option value="${classroom.id}">${buildingName}${classroom.name} (${classroom.capacity})</option>`;
    });

    document.getElementById('conflictWarning').style.display = 'none';

    console.log('Showing modal...');
    const modal = new bootstrap.Modal(document.getElementById('slotModal'));
    modal.show();
    console.log('Modal shown successfully');
}

function saveSlot() {
    const subjectId = document.getElementById('subjectSelect').value;
    const teacherId = document.getElementById('teacherSelect').value;
    const classroomId = document.getElementById('classroomSelect').value;
    const lessonType = document.getElementById('lessonTypeSelect').value;

    if (!subjectId || !teacherId || !classroomId) {
        alert('Iltimos, barcha maydonlarni to\'ldiring');
        return;
    }

    fetch('{{ route("schedule.faculty.store-slot") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            group_id: currentGroupId,
            subject_id: subjectId,
            teacher_id: teacherId,
            classroom_id: classroomId,
            day_of_week: currentDay,
            time_slot_id: currentTimeSlot,
            lesson_type: lessonType
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('slotModal')).hide();
            loadScheduleGrid(currentGroupId);
        } else if (data.conflicts) {
            const conflictWarning = document.getElementById('conflictWarning');
            conflictWarning.innerHTML = '<strong>Konfliktlar:</strong><ul class="mb-0">' +
                data.conflicts.map(c => `<li>${c.message}</li>`).join('') +
                '</ul>';
            conflictWarning.style.display = 'block';
        } else if (data.message) {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Xatolik yuz berdi');
    });
}

function deleteSlot(day, timeslot) {
    if (!confirm('Bu darsni o\'chirmoqchimisiz?')) {
        return;
    }

    fetch('{{ route("schedule.faculty.delete-slot") }}', {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            group_id: currentGroupId,
            day_of_week: day,
            time_slot_id: timeslot
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            loadScheduleGrid(currentGroupId);
        }
    });
}

function applySchedule() {
    const periodType = document.getElementById('periodType').value;
    const startDate = document.getElementById('startDate').value;

    if (!startDate) {
        alert('Iltimos, boshlanish sanasini tanlang');
        return;
    }

    if (!confirm(`Jadvalni ${periodType === 'week' ? '1 hafta' : periodType === 'month' ? '1 oy' : '1 semestr'} uchun joriy qilmoqchimisiz?`)) {
        return;
    }

    fetch('{{ route("schedule.faculty.apply-schedule") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            group_id: currentGroupId,
            period_type: periodType,
            start_date: startDate
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
        } else {
            alert(data.message || 'Xatolik yuz berdi');
        }
    });
}

function autoGenerate() {
    if (!currentGroupId) {
        alert('Iltimos, avval guruhni tanlang');
        return;
    }

    if (!confirm('Jadval avtomatik tuzilsinmi? Mavjud bo\'sh slotlarga darslar qo\'shiladi.')) {
        return;
    }

    fetch('{{ route("schedule.faculty.auto-generate") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            group_id: currentGroupId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            loadScheduleGrid(currentGroupId);
        } else {
            alert(data.message || 'Xatolik yuz berdi');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Xatolik yuz berdi');
    });
}
</script>
@endsection
