@extends('layouts.dashboard-new')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="card border-0 shadow-sm mb-4 bg-gradient-primary text-white">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">
                        <i class="fas fa-plus-circle me-2"></i>
                        Yangi baho qo'shish
                    </h4>
                    <p class="mb-0 opacity-75">
                        <i class="fas fa-book me-2"></i>{{ $groupSubject->subject->name }}
                        <span class="ms-3"><i class="fas fa-users me-1"></i>{{ $groupSubject->group->name }}</span>
                    </p>
                </div>
                <div>
                    <a href="{{ route('teacher.grades.show', $groupSubject->id) }}" class="btn btn-light btn-sm">
                        <i class="fas fa-arrow-left me-1"></i>Orqaga
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i>
        <ul class="mb-0">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <form action="{{ route('teacher.grades.store', $groupSubject->id) }}" method="POST">
        @csrf

        <!-- Grade Info -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0">
                    <i class="fas fa-info-circle me-2 text-primary"></i>
                    Baho ma'lumotlari
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="date" class="form-label">Sana <span class="text-danger">*</span></label>
                        <input type="date"
                               class="form-control @error('date') is-invalid @enderror"
                               id="date"
                               name="date"
                               value="{{ old('date', date('Y-m-d')) }}"
                               required>
                        @error('date')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="grade_type" class="form-label">Nazorat turi <span class="text-danger">*</span></label>
                        <select class="form-select @error('grade_type') is-invalid @enderror"
                                id="grade_type"
                                name="grade_type"
                                required>
                            <option value="">Tanlang...</option>
                            <option value="joriy" {{ old('grade_type') == 'joriy' ? 'selected' : '' }}>Joriy nazorat</option>
                            <option value="oraliq" {{ old('grade_type') == 'oraliq' ? 'selected' : '' }}>Oraliq nazorat</option>
                            <option value="yakuniy" {{ old('grade_type') == 'yakuniy' ? 'selected' : '' }}>Yakuniy nazorat</option>
                        </select>
                        @error('grade_type')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="topic" class="form-label">Mavzu <span class="text-danger">*</span></label>
                        <input type="text"
                               class="form-control @error('topic') is-invalid @enderror"
                               id="topic"
                               name="topic"
                               value="{{ old('topic') }}"
                               placeholder="Dars mavzusi"
                               required>
                        @error('topic')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Students Grades -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-users me-2 text-success"></i>
                        Talabalar ro'yxati
                    </h5>
                    <div>
                        <span class="badge bg-primary">{{ $students->count() }} talaba</span>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                @if($students->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="px-4" style="width: 50px;">#</th>
                                <th>F.I.Sh</th>
                                <th>Talaba ID</th>
                                <th class="text-center" style="width: 150px;">Baho (0-100)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($students as $index => $student)
                            <tr>
                                <td class="px-4">{{ $index + 1 }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-circle me-2" style="width: 36px; height: 36px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 0.8rem;">
                                            {{ strtoupper(substr($student->user->name ?? 'U', 0, 2)) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold">{{ $student->user->name ?? 'Noma\'lum' }}</div>
                                            <small class="text-muted">{{ $student->user->email ?? '-' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $student->student_id }}</span>
                                </td>
                                <td class="text-center">
                                    <input type="number"
                                           class="form-control text-center grade-input"
                                           name="grades[{{ $student->id }}]"
                                           value="{{ old('grades.' . $student->id) }}"
                                           min="0"
                                           max="100"
                                           step="0.1"
                                           placeholder="-">
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

        <!-- Submit -->
        <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('teacher.grades.show', $groupSubject->id) }}" class="btn btn-secondary">
                <i class="fas fa-times me-1"></i>Bekor qilish
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-1"></i>Saqlash
            </button>
        </div>
    </form>
</div>

<style>
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
.grade-input {
    width: 80px;
    margin: 0 auto;
}
.grade-input:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-validate grade inputs
    const gradeInputs = document.querySelectorAll('.grade-input');
    gradeInputs.forEach(input => {
        input.addEventListener('input', function() {
            let value = parseFloat(this.value);
            if (value < 0) this.value = 0;
            if (value > 100) this.value = 100;
        });

        // Quick number keys
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const inputs = Array.from(gradeInputs);
                const currentIndex = inputs.indexOf(this);
                if (currentIndex < inputs.length - 1) {
                    inputs[currentIndex + 1].focus();
                }
            }
        });
    });
});
</script>
@endsection
