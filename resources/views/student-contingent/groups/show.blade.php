@extends('layouts.dashboard-new')

@section('title', 'Guruh ma\'lumotlari')
@section('page-title', 'Guruh ma\'lumotlari')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">{{ $group->name }} - Guruh ma'lumotlari</h6>
                        <div>
                            <a href="{{ route('student-contingent.groups.edit', $group) }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-edit"></i> Tahrirlash
                            </a>
                            <a href="{{ route('student-contingent.groups.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-left"></i> Orqaga
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-uppercase text-body text-xs font-weight-bolder">Asosiy ma'lumotlar</h6>
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <strong>Guruh nomi:</strong> {{ $group->name }}
                                </li>
                                <li class="mb-2">
                                    <strong>Fakultet:</strong> {{ $group->department->faculty->name ?? 'Belgilanmagan' }}
                                </li>
                                <li class="mb-2">
                                    <strong>Kafedra:</strong> {{ $group->department->name ?? 'Belgilanmagan' }}
                                </li>
                                <li class="mb-2">
                                    <strong>Kurs:</strong> {{ $group->course }}-kurs
                                </li>
                                <li class="mb-2">
                                    <strong>Guruh kodi:</strong> {{ $group->code }}
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-uppercase text-body text-xs font-weight-bolder">Qo'shimcha ma'lumotlar</h6>
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <strong>Ta'lim turi:</strong>
                                    @if($group->education_type == 'kunduzgi') Kunduzgi
                                    @elseif($group->education_type == 'sirtqi') Sirtqi
                                    @elseif($group->education_type == 'kechki') Kechki
                                    @else {{ $group->education_type }}
                                    @endif
                                </li>
                                <li class="mb-2">
                                    <strong>Talabalar soni:</strong> {{ $group->students_count ?? 0 }} ta
                                </li>
                                <li class="mb-2">
                                    <strong>Yaratilgan:</strong> {{ $group->created_at->format('d.m.Y') }}
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-12">
                            @php
                                $currentStudents = $group->students_count ?? $group->students->count();
                                $maxStudents = 30;
                                $percentage = $maxStudents > 0 ? ($currentStudents / $maxStudents) * 100 : 0;
                                $availableSlots = $maxStudents - $currentStudents;
                            @endphp
                            <div class="d-flex align-items-center mb-3">
                                <h6 class="mb-0">Talabalar sig'imi</h6>
                                <span class="ms-auto text-sm">{{ $currentStudents }} / {{ $maxStudents }}</span>
                            </div>
                            <div class="progress" style="height: 10px;">
                                <div class="progress-bar bg-gradient-primary" role="progressbar"
                                     style="width: {{ min($percentage, 100) }}%">
                                </div>
                            </div>
                            <div class="mt-2">
                                <span class="badge badge-sm bg-gradient-info">Bo'sh joylar: {{ max($availableSlots, 0) }}</span>
                            </div>
                        </div>
                    </div>

                    @if($group->description)
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <h6 class="text-uppercase text-body text-xs font-weight-bolder">Tavsif</h6>
                            <p>{{ $group->description }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Teacher Subject Assignments from TeacherSubject table --}}
            @if(isset($teacherSubjectAssignments) && $teacherSubjectAssignments->count() > 0)
            <div class="card mt-4">
                <div class="card-header pb-0 bg-gradient-info">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 text-white"><i class="fas fa-chalkboard-teacher me-2"></i>Biriktirilgan O'qituvchilar va Fanlar</h6>
                        <span class="badge bg-white text-info">{{ $teacherSubjectAssignments->count() }} ta</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">№</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Fan nomi</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">O'qituvchi</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Soatlar</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Til</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">O'qituvchi sahifasi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($teacherSubjectAssignments as $index => $assignment)
                                <tr>
                                    <td class="text-sm">{{ $index + 1 }}</td>
                                    <td class="text-sm font-weight-bold">
                                        {{ $assignment->subject->name_uz ?? $assignment->subject->name ?? 'Noma\'lum' }}
                                        @if($assignment->subject && $assignment->subject->code)
                                        <br><small class="text-muted">{{ $assignment->subject->code }}</small>
                                        @endif
                                    </td>
                                    <td class="text-sm">
                                        @if($assignment->teacher)
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-sm me-2 bg-gradient-primary rounded-circle">
                                                    <span class="text-white text-xs">{{ substr($assignment->teacher->first_name, 0, 1) }}{{ substr($assignment->teacher->last_name, 0, 1) }}</span>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0 text-sm">{{ $assignment->teacher->full_name }}</h6>
                                                    <p class="text-xs text-secondary mb-0">{{ $assignment->teacher->position ?? '' }}</p>
                                                </div>
                                            </div>
                                        @else
                                            <span class="badge badge-sm bg-gradient-warning">Noma'lum</span>
                                        @endif
                                    </td>
                                    <td class="text-sm">
                                        <span class="badge bg-gradient-success">Ma'ruza: {{ $assignment->lecture_hours ?? 0 }}</span>
                                        <span class="badge bg-gradient-info">Amaliy: {{ $assignment->practice_hours ?? 0 }}</span>
                                        <span class="badge bg-gradient-warning">Lab: {{ $assignment->lab_hours ?? 0 }}</span>
                                        <br><small class="text-muted">Jami: {{ $assignment->total_hours ?? 0 }} soat</small>
                                    </td>
                                    <td class="text-sm">
                                        @if($assignment->language == 'uz')
                                            <span class="badge bg-gradient-primary">O'zbek</span>
                                        @elseif($assignment->language == 'ru')
                                            <span class="badge bg-gradient-secondary">Rus</span>
                                        @elseif($assignment->language == 'en')
                                            <span class="badge bg-gradient-info">Ingliz</span>
                                        @else
                                            <span class="badge bg-secondary">{{ $assignment->language }}</span>
                                        @endif
                                    </td>
                                    <td class="text-sm">
                                        @if($assignment->teacher)
                                        <a href="{{ route('employees.teachers.subjects', $assignment->teacher_id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-external-link-alt me-1"></i> O'qituvchi fanlari
                                        </a>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            {{-- Subjects Section from JournalEntry --}}
            <div class="card mt-4">
                <div class="card-header pb-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0"><i class="fas fa-book me-2"></i>Journal Fanlar va O'qituvchilar</h6>
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#assignTeacherModal">
                            <i class="fas fa-plus"></i> O'qituvchi biriktirish
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">№</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Fan nomi</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Fan kodi</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">O'qituvchi</th>
                                    <th class="text-secondary opacity-7"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($assignedSubjects as $index => $entry)
                                <tr>
                                    <td class="text-sm">{{ $index + 1 }}</td>
                                    <td class="text-sm font-weight-bold">{{ $entry->subject->name_uz ?? $entry->subject->name }}</td>
                                    <td class="text-sm">{{ $entry->subject->code }}</td>
                                    <td class="text-sm">
                                        @if($entry->teacher)
                                            {{ $entry->teacher->full_name }}
                                        @else
                                            <span class="badge badge-sm bg-gradient-warning">O'qituvchi biriktirilmagan</span>
                                        @endif
                                    </td>
                                    <td class="text-sm">
                                        @if($entry->teacher)
                                        <button type="button" class="btn btn-link text-danger text-gradient px-3 mb-0"
                                                onclick="removeTeacher({{ $entry->id }})">
                                            <i class="far fa-trash-alt me-2"></i>O'chirish
                                        </button>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-sm">
                                        <p class="text-secondary mb-0">Hali fanlar biriktirilmagan</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-header pb-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Guruh talabalari</h6>
                        <div>
                            <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addStudentsModal">
                                <i class="fas fa-user-plus"></i> Talaba qo'shish
                            </button>
                            <a href="{{ route('student-contingent.groups.export', $group) }}" class="btn btn-info btn-sm">
                                <i class="fas fa-download"></i> Export
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">№</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Talaba ID</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">F.I.O</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">JSHSHIR</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Telefon</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Email</th>
                                    <th class="text-secondary opacity-7"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($students as $index => $student)
                                <tr>
                                    <td>
                                        <p class="text-sm mb-0">{{ ($students->currentPage() - 1) * $students->perPage() + $index + 1 }}</p>
                                    </td>
                                    <td>
                                        <p class="text-sm font-weight-bold mb-0">{{ $student->student_id }}</p>
                                    </td>
                                    <td>
                                        <div class="d-flex px-2 py-1">
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="mb-0 text-sm">{{ $student->full_name_latin }}</h6>
                                                <p class="text-xs text-secondary mb-0">{{ $student->full_name_cyrillic }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <p class="text-sm mb-0">{{ $student->jshshir }}</p>
                                    </td>
                                    <td>
                                        <p class="text-sm mb-0">{{ $student->phone_primary }}</p>
                                    </td>
                                    <td>
                                        <p class="text-sm mb-0">{{ $student->email }}</p>
                                    </td>
                                    <td class="align-middle">
                                        <div class="dropdown">
                                            <button class="btn btn-link text-secondary mb-0" data-bs-toggle="dropdown">
                                                <i class="fa fa-ellipsis-v text-xs"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('students.show', $student) }}">
                                                        <i class="fas fa-eye me-2"></i> Ko'rish
                                                    </a>
                                                </li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <form action="{{ route('student-contingent.groups.remove-student', [$group, $student]) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger"
                                                                onclick="return confirm('Talabani guruhdan chiqarishni xohlaysizmi?')">
                                                            <i class="fas fa-user-minus me-2"></i> Guruhdan chiqarish
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <p class="text-muted mb-0">Bu guruhda hozircha talabalar yo'q</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $students->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Students Modal -->
<div class="modal fade" id="addStudentsModal" tabindex="-1" aria-labelledby="addStudentsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addStudentsModalLabel">Guruhga talabalar qo'shish</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('student-contingent.groups.add-students', $group) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Bo'sh joylar soni: <strong>{{ $group->available_slots }}</strong>
                    </div>

                    <div class="form-group">
                        <label>Talabalarni tanlang:</label>
                        <div style="max-height: 300px; overflow-y: auto;">
                            @php
                                $availableStudents = App\Models\Student::whereNull('group_id')
                                    ->orderBy('last_name')
                                    ->get();
                            @endphp
                            @forelse($availableStudents as $student)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="student_ids[]"
                                       value="{{ $student->id }}" id="student_{{ $student->id }}">
                                <label class="form-check-label" for="student_{{ $student->id }}">
                                    {{ $student->student_id }} - {{ $student->full_name_latin }}
                                </label>
                            </div>
                            @empty
                            <p class="text-muted">Guruhga qo'shish uchun talabalar topilmadi</p>
                            @endforelse
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bekor qilish</button>
                    <button type="submit" class="btn btn-primary">Talabalarni qo'shish</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Assign Teacher Modal --}}
<div class="modal fade" id="assignTeacherModal" tabindex="-1" aria-labelledby="assignTeacherModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('student-contingent.groups.assign-teacher', $group) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="assignTeacherModalLabel">Fanga o'qituvchi biriktirish</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="subject_id" class="form-label">Fan</label>
                        <select class="form-select" id="subject_id" name="subject_id" required>
                            <option value="">Fanni tanlang</option>
                            @foreach(\App\Models\Subject::all() as $subject)
                                <option value="{{ $subject->id }}">{{ $subject->code }} - {{ $subject->name_uz ?? $subject->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="teacher_id" class="form-label">O'qituvchi</label>
                        <select class="form-select" id="teacher_id" name="teacher_id" required>
                            <option value="">O'qituvchini tanlang</option>
                            @php
                                // Try to get teachers from Teacher model first, then Employee
                                $teachers = \App\Models\Teacher::all();
                                if($teachers->isEmpty()) {
                                    $teachers = \App\Models\Employee::where('employee_type', 'teacher')->where('status', 'active')->get();
                                }
                            @endphp
                            @foreach($teachers as $teacher)
                                <option value="{{ $teacher->id }}">{{ $teacher->full_name }} ({{ $teacher->department->name ?? '-' }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bekor qilish</button>
                    <button type="submit" class="btn btn-primary">Biriktirish</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function removeTeacher(entryId) {
    if (confirm('Rostdan ham o\'qituvchini o\'chirmoqchimisiz?')) {
        fetch(`/student-contingent/groups/journal-entry/${entryId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            location.reload();
        })
        .catch(error => {
            alert('Xatolik yuz berdi');
            console.error('Error:', error);
        });
    }
}
</script>
@endsection