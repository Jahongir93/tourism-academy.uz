@extends('layouts.dashboard-new')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="card border-0 shadow-sm mb-4 bg-gradient-primary text-white">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">
                        <i class="fas fa-clipboard-check me-2"></i>
                        Davomat qilish
                    </h4>
                    <p class="mb-0 opacity-75">
                        <i class="fas fa-book me-2"></i>{{ $groupSubject->subject->name }}
                        <span class="ms-3"><i class="fas fa-users me-1"></i>{{ $groupSubject->group->name }}</span>
                    </p>
                </div>
                <div>
                    <a href="{{ route('teacher.attendance.journal', $groupSubject->id) }}" class="btn btn-light btn-sm">
                        <i class="fas fa-arrow-left me-1"></i>Orqaga
                    </a>
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('teacher.attendance.store', $groupSubject->id) }}" method="POST">
        @csrf

        <div class="row">
            <!-- Left Column: Lesson Info -->
            <div class="col-lg-4 mb-4">
                <div class="card border-0 shadow-sm sticky-top" style="top: 20px;">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            Dars ma'lumotlari
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Date -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-calendar text-primary me-1"></i>Sana
                            </label>
                            <input type="date"
                                   name="date"
                                   class="form-control @error('date') is-invalid @enderror"
                                   value="{{ old('date', date('Y-m-d')) }}"
                                   required>
                            @error('date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Topic -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-bookmark text-primary me-1"></i>Mavzu
                            </label>
                            <input type="text"
                                   name="topic"
                                   class="form-control @error('topic') is-invalid @enderror"
                                   value="{{ old('topic') }}"
                                   placeholder="Dars mavzusini kiriting"
                                   required>
                            @error('topic')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Lesson Type -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-graduation-cap text-primary me-1"></i>Nazorat turi
                            </label>
                            <select name="lesson_type"
                                    class="form-select @error('lesson_type') is-invalid @enderror"
                                    required>
                                <option value="">Tanlang...</option>
                                <option value="joriy" {{ old('lesson_type') == 'joriy' ? 'selected' : '' }}>Joriy nazorat</option>
                                <option value="oraliq" {{ old('lesson_type') == 'oraliq' ? 'selected' : '' }}>Oraliq nazorat</option>
                                <option value="yakuniy" {{ old('lesson_type') == 'yakuniy' ? 'selected' : '' }}>Yakuniy nazorat</option>
                            </select>
                            @error('lesson_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Statistics -->
                        <div class="border-top pt-3">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Jami talabalar:</span>
                                <strong id="totalStudents">{{ $students->count() }}</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Ishtirok etdi:</span>
                                <strong class="text-success" id="presentCount">0</strong>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Kelmadi:</span>
                                <strong class="text-danger" id="absentCount">{{ $students->count() }}</strong>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-primary w-100 mt-4">
                            <i class="fas fa-save me-2"></i>Davomatni saqlash
                        </button>
                    </div>
                </div>
            </div>

            <!-- Right Column: Students List -->
            <div class="col-lg-8 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-users me-2"></i>
                                Talabalar ro'yxati ({{ $students->count() }})
                            </h5>
                            <div>
                                <button type="button" class="btn btn-sm btn-success" onclick="checkAll()">
                                    <i class="fas fa-check-double me-1"></i>Barchasini belgilash
                                </button>
                                <button type="button" class="btn btn-sm btn-danger" onclick="uncheckAll()">
                                    <i class="fas fa-times me-1"></i>Barchasini olib tashlash
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        @if($students->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="px-4" width="50">#</th>
                                        <th>F.I.Sh</th>
                                        <th>Talaba ID</th>
                                        <th width="100" class="text-center">Davomat</th>
                                        <th width="120">Ball (0-100)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($students as $index => $student)
                                    <tr class="student-row">
                                        <td class="px-4">{{ $index + 1 }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-circle me-2" style="width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">
                                                    {{ strtoupper(substr($student->user->name, 0, 2)) }}
                                                </div>
                                                <div>
                                                    <div class="fw-bold">{{ $student->user->name }}</div>
                                                    <small class="text-muted">{{ $student->user->email }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $student->student_id }}</span>
                                        </td>
                                        <td class="text-center">
                                            <div class="form-check form-switch d-flex justify-content-center">
                                                <input class="form-check-input attendance-checkbox"
                                                       type="checkbox"
                                                       name="attendance[{{ $student->id }}][present]"
                                                       value="1"
                                                       id="present_{{ $student->id }}"
                                                       onchange="updateCounts(); toggleScoreInput({{ $student->id }})">
                                            </div>
                                        </td>
                                        <td>
                                            <input type="number"
                                                   name="attendance[{{ $student->id }}][score]"
                                                   id="score_{{ $student->id }}"
                                                   class="form-control form-control-sm score-input"
                                                   min="0"
                                                   max="100"
                                                   step="0.01"
                                                   placeholder="Ball"
                                                   disabled>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <div class="text-center py-5">
                            <i class="fas fa-users-slash fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Bu guruhda talabalar yo'q</h5>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
.student-row:hover {
    background-color: #f8f9fa;
}
.form-check-input:checked {
    background-color: #28a745;
    border-color: #28a745;
}
.score-input:disabled {
    background-color: #e9ecef;
    cursor: not-allowed;
}
</style>

<script>
function updateCounts() {
    const checkboxes = document.querySelectorAll('.attendance-checkbox');
    let presentCount = 0;
    let absentCount = 0;

    checkboxes.forEach(checkbox => {
        if (checkbox.checked) {
            presentCount++;
        } else {
            absentCount++;
        }
    });

    document.getElementById('presentCount').textContent = presentCount;
    document.getElementById('absentCount').textContent = absentCount;
}

function toggleScoreInput(studentId) {
    const checkbox = document.getElementById('present_' + studentId);
    const scoreInput = document.getElementById('score_' + studentId);

    if (checkbox.checked) {
        scoreInput.disabled = false;
        scoreInput.focus();
    } else {
        scoreInput.disabled = true;
        scoreInput.value = '';
    }
}

function checkAll() {
    const checkboxes = document.querySelectorAll('.attendance-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = true;
        const studentId = checkbox.id.replace('present_', '');
        toggleScoreInput(studentId);
    });
    updateCounts();
}

function uncheckAll() {
    const checkboxes = document.querySelectorAll('.attendance-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = false;
        const studentId = checkbox.id.replace('present_', '');
        toggleScoreInput(studentId);
    });
    updateCounts();
}

// Initialize counts on page load
document.addEventListener('DOMContentLoaded', function() {
    updateCounts();
});
</script>
@endsection
