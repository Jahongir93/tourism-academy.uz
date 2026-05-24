@extends('layouts.dashboard-new')

@section('title', 'Yangi Vedomost')
@section('page-title', 'Yangi Vedomost Yaratish')

@section('content')
<div class="container-fluid px-0">
    <div class="mb-4">
        <a href="{{ route('vedomost.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Orqaga
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Vedomost ma'lumotlari</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('vedomost.store') }}" method="POST" id="vedomostForm">
                @csrf

                <div class="row g-3">
                    <!-- Academic Year -->
                    <div class="col-md-4">
                        <label class="form-label">O'quv yili <span class="text-danger">*</span></label>
                        <select name="academic_year" id="academicYearSelect" class="form-select @error('academic_year') is-invalid @enderror" required>
                            <option value="">Tanlang</option>
                            @if(isset($academicYears))
                                @foreach($academicYears as $year)
                                    <option value="{{ $year->name }}" {{ old('academic_year') == $year->name ? 'selected' : '' }}>
                                        {{ $year->name }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        @error('academic_year')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Semester -->
                    <div class="col-md-4">
                        <label class="form-label">Semestr <span class="text-danger">*</span></label>
                        <select name="semester" class="form-select @error('semester') is-invalid @enderror" required>
                            <option value="">Tanlang</option>
                            <option value="1" {{ old('semester') == '1' ? 'selected' : '' }}>1-semestr</option>
                            <option value="2" {{ old('semester') == '2' ? 'selected' : '' }}>2-semestr</option>
                        </select>
                        @error('semester')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Credits -->
                    <div class="col-md-4">
                        <label class="form-label">Kreditlar <span class="text-danger">*</span></label>
                        <input type="number" name="credits" class="form-control @error('credits') is-invalid @enderror"
                               value="{{ old('credits', 3) }}" min="0" max="20" required>
                        @error('credits')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Group -->
                    <div class="col-md-6">
                        <label class="form-label">Guruh <span class="text-danger">*</span></label>
                        <select name="group_id" id="groupSelect" class="form-select @error('group_id') is-invalid @enderror" required>
                            <option value="">Tanlang</option>
                            @if(isset($groups))
                                @foreach($groups as $group)
                                    <option value="{{ $group->id }}" {{ old('group_id') == $group->id ? 'selected' : '' }}>
                                        {{ $group->name }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        @error('group_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Subject -->
                    <div class="col-md-6">
                        <label class="form-label">Fan <span class="text-danger">*</span></label>
                        <select name="subject_id" class="form-select @error('subject_id') is-invalid @enderror" required>
                            <option value="">Tanlang</option>
                            @if(isset($subjects))
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
                                        {{ $subject->name }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        @error('subject_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Assessment Type -->
                    <div class="col-md-6">
                        <label class="form-label">Nazorat turi <span class="text-danger">*</span></label>
                        <select name="assessment_type" class="form-select @error('assessment_type') is-invalid @enderror" required>
                            <option value="">Tanlang</option>
                            <option value="exam" {{ old('assessment_type') == 'exam' ? 'selected' : '' }}>Imtihon</option>
                            <option value="test" {{ old('assessment_type') == 'test' ? 'selected' : '' }}>Test</option>
                            <option value="coursework" {{ old('assessment_type') == 'coursework' ? 'selected' : '' }}>Kurs ishi</option>
                            <option value="project" {{ old('assessment_type') == 'project' ? 'selected' : '' }}>Loyiha</option>
                            <option value="practice" {{ old('assessment_type') == 'practice' ? 'selected' : '' }}>Amaliyot</option>
                            <option value="other" {{ old('assessment_type') == 'other' ? 'selected' : '' }}>Boshqa</option>
                        </select>
                        @error('assessment_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Assessment Date -->
                    <div class="col-md-6">
                        <label class="form-label">Nazorat sanasi <span class="text-danger">*</span></label>
                        <input type="date" name="assessment_date" class="form-control @error('assessment_date') is-invalid @enderror" value="{{ old('assessment_date', date('Y-m-d')) }}" required>
                        @error('assessment_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Students List -->
                <div id="studentsSection" class="mt-4" style="display: none;">
                    <h6 class="mb-3">Talabalar ro'yxati va baholar</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 50px;">#</th>
                                    <th>Talaba</th>
                                    <th class="text-center" style="width: 120px;">Baho (0-100)</th>
                                    <th>Izoh</th>
                                </tr>
                            </thead>
                            <tbody id="studentsTableBody">
                                <!-- Students will be loaded via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary" id="submitBtn" disabled>
                        <i class="fas fa-save me-2"></i>Saqlash
                    </button>
                    <a href="{{ route('vedomost.index') }}" class="btn btn-secondary">
                        Bekor qilish
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('groupSelect').addEventListener('change', function() {
    const groupId = this.value;
    if (groupId) {
        // Show loading
        const tbody = document.getElementById('studentsTableBody');
        tbody.innerHTML = '<tr><td colspan="4" class="text-center"><i class="fas fa-spinner fa-spin"></i> Yuklanmoqda...</td></tr>';
        document.getElementById('studentsSection').style.display = 'block';

        fetch(`{{ url('/academic/vedomost/group') }}/${groupId}/students`)
            .then(response => response.json())
            .then(data => {
                tbody.innerHTML = '';

                if (data.success && data.students && data.students.length > 0) {
                    data.students.forEach((student, index) => {
                        const studentName = student.name || student.full_name || 'Noma\'lum';
                        const row = `
                            <tr>
                                <td>${index + 1}</td>
                                <td>
                                    ${studentName}
                                    <input type="hidden" name="grades[${index}][student_id]" value="${student.id}">
                                </td>
                                <td>
                                    <input type="number" name="grades[${index}][grade]"
                                           class="form-control form-control-sm text-center"
                                           min="0" max="100" required placeholder="0-100">
                                </td>
                                <td>
                                    <input type="text" name="grades[${index}][comments]"
                                           class="form-control form-control-sm" placeholder="Izoh...">
                                </td>
                            </tr>
                        `;
                        tbody.innerHTML += row;
                    });
                    document.getElementById('submitBtn').disabled = false;
                } else {
                    tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted">Bu guruhda talabalar topilmadi</td></tr>';
                    document.getElementById('submitBtn').disabled = true;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                tbody.innerHTML = '<tr><td colspan="4" class="text-center text-danger">Xatolik yuz berdi</td></tr>';
                document.getElementById('submitBtn').disabled = true;
            });
    } else {
        document.getElementById('studentsSection').style.display = 'none';
        document.getElementById('submitBtn').disabled = true;
    }
});

// Form validation before submit
document.getElementById('vedomostForm').addEventListener('submit', function(e) {
    const gradeInputs = document.querySelectorAll('input[name^="grades"][name$="[grade]"]');
    let hasGrades = false;

    gradeInputs.forEach(input => {
        if (input.value && input.value >= 0 && input.value <= 100) {
            hasGrades = true;
        }
    });

    if (!hasGrades) {
        e.preventDefault();
        alert('Kamida bitta talabaga baho qo\'ying!');
        return false;
    }
});
</script>
@endpush
@endsection
