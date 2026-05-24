@extends('layouts.dashboard-new')

@section('title', 'Vedomost to\'ldirish')
@section('page-title', 'Vedomost to\'ldirish')

@section('content')
<div class="container-fluid px-0">
    <!-- Ministry Header -->
    <div class="text-center mb-4">
        <h5 class="fw-bold text-uppercase mb-1">O'zbekiston Respublikasi</h5>
        <h6 class="fw-bold text-uppercase">Oliy ta'lim, fan va innovatsiyalar vazirligi</h6>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Vedomost Info -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">
                <i class="fas fa-file-alt me-2"></i>Vedomost ma'lumotlari
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <strong class="text-muted d-block">Guruh</strong>
                    <p class="mb-3">{{ $vedomost->group->name }}</p>
                </div>
                <div class="col-md-3">
                    <strong class="text-muted d-block">Fan</strong>
                    <p class="mb-3">{{ $vedomost->subject->name }}</p>
                </div>
                <div class="col-md-3">
                    <strong class="text-muted d-block">O'qituvchi</strong>
                    <p class="mb-3">{{ $vedomost->teacher->name }}</p>
                </div>
                <div class="col-md-3">
                    <strong class="text-muted d-block">O'quv yili / Semestr</strong>
                    <p class="mb-3">{{ $vedomost->academicYear->name }} / {{ $vedomost->semester }}-semestr</p>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <strong class="text-muted d-block">Kreditlar</strong>
                    <p class="mb-0">{{ $vedomost->credits }}</p>
                </div>
                <div class="col-md-3">
                    <strong class="text-muted d-block">Nazorat turi</strong>
                    <p class="mb-0">
                        @if($vedomost->assessment_type == 'exam') Imtihon
                        @elseif($vedomost->assessment_type == 'test') Test
                        @elseif($vedomost->assessment_type == 'coursework') Kurs ishi
                        @else {{ $vedomost->assessment_type }}
                        @endif
                    </p>
                </div>
                <div class="col-md-3">
                    <strong class="text-muted d-block">To'ldirilgan</strong>
                    <div class="progress" style="height: 25px;">
                        <div class="progress-bar" role="progressbar" style="width: {{ $vedomost->getCompletionPercentage() }}%">
                            {{ $vedomost->getCompletionPercentage() }}%
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <strong class="text-muted d-block">Holat</strong>
                    <p class="mb-0">
                        @if($vedomost->status == 'approved')
                            <span class="badge bg-success">Tasdiqlangan</span>
                        @elseif($vedomost->status == 'submitted')
                            <span class="badge bg-info">Topshirilgan</span>
                        @elseif($vedomost->status == 'in_progress')
                            <span class="badge bg-warning">Jarayonda</span>
                        @else
                            <span class="badge bg-secondary">Qoralama</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Grades Form -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">
                <i class="fas fa-users me-2"></i>Talabalar ro'yxati va baholar
                <span class="badge bg-primary ms-2">{{ $students->count() }} talaba</span>
            </h5>
            <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#addColumnModal">
                <i class="fas fa-plus me-1"></i>Ustun qo'shish
            </button>
        </div>
        <div class="card-body">
            <form action="{{ route('vedomost.save-fill', $vedomost->id) }}" method="POST" id="gradesForm">
                @csrf
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 50px;" rowspan="2" class="align-middle">#</th>
                                <th rowspan="2" class="align-middle">Talaba</th>
                                <th colspan="2" class="text-center bg-info text-white">Joriy nazorat</th>
                                @foreach($vedomost->assessmentColumns as $column)
                                    <th colspan="2" class="text-center bg-warning text-dark">
                                        {{ $column->name }}
                                        <button type="button" class="btn btn-sm btn-danger btn-remove-column ms-1"
                                                data-column-id="{{ $column->id }}" title="O'chirish">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </th>
                                @endforeach
                                <th colspan="2" class="text-center bg-success text-white">Yakuniy nazorat</th>
                                <th rowspan="2" class="align-middle" style="width: 200px;">Izoh</th>
                            </tr>
                            <tr>
                                <th class="text-center" style="width: 100px;">Baho</th>
                                <th class="text-center" style="width: 60px;">Harf</th>
                                @foreach($vedomost->assessmentColumns as $column)
                                    <th class="text-center" style="width: 100px;">Baho</th>
                                    <th class="text-center" style="width: 60px;">Harf</th>
                                @endforeach
                                <th class="text-center" style="width: 100px;">Baho</th>
                                <th class="text-center" style="width: 60px;">Harf</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($students as $index => $student)
                            @php
                                // Find current and final grades for this student
                                $currentGradeObj = null;
                                $finalGradeObj = null;
                                $columnGrades = [];

                                foreach($vedomost->grades as $grade) {
                                    if ($grade->student_id == $student->id) {
                                        if ($grade->assessment_column_id) {
                                            $columnGrades[$grade->assessment_column_id] = $grade;
                                        } elseif ($grade->is_final) {
                                            $finalGradeObj = $grade;
                                        } else {
                                            $currentGradeObj = $grade;
                                        }
                                    }
                                }

                                $currentValue = $currentGradeObj ? $currentGradeObj->grade : '';
                                $finalValue = $finalGradeObj ? $finalGradeObj->grade : '';
                                $comments = $finalGradeObj ? $finalGradeObj->comments : ($currentGradeObj ? $currentGradeObj->comments : '');
                            @endphp
                            <tr>
                                <td class="text-center align-middle">{{ $index + 1 }}</td>
                                <td class="align-middle">
                                    <strong>{{ $student->full_name }}</strong>
                                    <input type="hidden" name="grades[{{ $index }}][student_id]" value="{{ $student->id }}">
                                </td>
                                <!-- Joriy nazorat -->
                                <td>
                                    <div class="position-relative">
                                        <input type="number"
                                               name="grades[{{ $index }}][current_grade]"
                                               class="form-control text-center grade-input"
                                               min="0"
                                               max="100"
                                               value="{{ $currentValue }}"
                                               data-letter-target="current-letter-{{ $index }}"
                                               placeholder="0-100"
                                               @if($currentGradeObj && str_contains($currentGradeObj->comments ?? '', 'LMS imtihon:')) readonly title="{{ $currentGradeObj->comments }}" @endif>
                                        @if($currentGradeObj && str_contains($currentGradeObj->comments ?? '', 'LMS imtihon:'))
                                            <span class="badge bg-info position-absolute top-0 end-0 m-1" style="font-size: 0.6rem;" title="LMS imtihonidan">
                                                <i class="fas fa-laptop"></i>
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="text-center align-middle">
                                    <span id="current-letter-{{ $index }}" class="badge bg-secondary">
                                        @if($currentValue)
                                            @php
                                                if ($currentValue >= 86) {
                                                    if ($currentValue >= 96) echo 'A+';
                                                    elseif ($currentValue >= 91) echo 'A';
                                                    else echo 'A-';
                                                } elseif ($currentValue >= 71) {
                                                    if ($currentValue >= 81) echo 'B+';
                                                    elseif ($currentValue >= 76) echo 'B';
                                                    else echo 'B-';
                                                } elseif ($currentValue >= 55) {
                                                    if ($currentValue >= 66) echo 'C+';
                                                    elseif ($currentValue >= 61) echo 'C';
                                                    else echo 'C-';
                                                } else {
                                                    echo 'F';
                                                }
                                            @endphp
                                        @else
                                            -
                                        @endif
                                    </span>
                                </td>
                                <!-- Dynamic assessment columns -->
                                @foreach($vedomost->assessmentColumns as $column)
                                    @php
                                        $columnValue = isset($columnGrades[$column->id]) ? $columnGrades[$column->id]->grade : '';
                                        $columnGradeObj = isset($columnGrades[$column->id]) ? $columnGrades[$column->id] : null;
                                    @endphp
                                    <td>
                                        <div class="position-relative">
                                            <input type="number"
                                                   name="grades[{{ $index }}][column_{{ $column->id }}]"
                                                   class="form-control text-center grade-input"
                                                   min="0"
                                                   max="{{ $column->max_score }}"
                                                   value="{{ $columnValue }}"
                                                   data-letter-target="column-letter-{{ $column->id }}-{{ $index }}"
                                                   placeholder="0-{{ $column->max_score }}"
                                                   @if($columnGradeObj && str_contains($columnGradeObj->comments ?? '', 'LMS imtihon:')) readonly title="{{ $columnGradeObj->comments }}" @endif>
                                            @if($columnGradeObj && str_contains($columnGradeObj->comments ?? '', 'LMS imtihon:'))
                                                <span class="badge bg-info position-absolute top-0 end-0 m-1" style="font-size: 0.6rem;" title="LMS imtihonidan">
                                                    <i class="fas fa-laptop"></i>
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="text-center align-middle">
                                        <span id="column-letter-{{ $column->id }}-{{ $index }}" class="badge bg-secondary">
                                            @if($columnValue)
                                                @php
                                                    if ($columnValue >= 86) {
                                                        if ($columnValue >= 96) echo 'A+';
                                                        elseif ($columnValue >= 91) echo 'A';
                                                        else echo 'A-';
                                                    } elseif ($columnValue >= 71) {
                                                        if ($columnValue >= 81) echo 'B+';
                                                        elseif ($columnValue >= 76) echo 'B';
                                                        else echo 'B-';
                                                    } elseif ($columnValue >= 55) {
                                                        if ($columnValue >= 66) echo 'C+';
                                                        elseif ($columnValue >= 61) echo 'C';
                                                        else echo 'C-';
                                                    } else {
                                                        echo 'F';
                                                    }
                                                @endphp
                                            @else
                                                -
                                            @endif
                                        </span>
                                    </td>
                                @endforeach
                                <!-- Yakuniy nazorat -->
                                <td>
                                    <div class="position-relative">
                                        <input type="number"
                                               name="grades[{{ $index }}][final_grade]"
                                               class="form-control text-center grade-input"
                                               min="0"
                                               max="100"
                                               value="{{ $finalValue }}"
                                               data-letter-target="final-letter-{{ $index }}"
                                               placeholder="0-100"
                                               @if($finalGradeObj && str_contains($finalGradeObj->comments ?? '', 'LMS imtihon:')) readonly title="{{ $finalGradeObj->comments }}" @endif>
                                        @if($finalGradeObj && str_contains($finalGradeObj->comments ?? '', 'LMS imtihon:'))
                                            <span class="badge bg-info position-absolute top-0 end-0 m-1" style="font-size: 0.6rem;" title="LMS imtihonidan">
                                                <i class="fas fa-laptop"></i>
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="text-center align-middle">
                                    <span id="final-letter-{{ $index }}" class="badge bg-secondary">
                                        @if($finalValue)
                                            @php
                                                if ($finalValue >= 86) {
                                                    if ($finalValue >= 96) echo 'A+';
                                                    elseif ($finalValue >= 91) echo 'A';
                                                    else echo 'A-';
                                                } elseif ($finalValue >= 71) {
                                                    if ($finalValue >= 81) echo 'B+';
                                                    elseif ($finalValue >= 76) echo 'B';
                                                    else echo 'B-';
                                                } elseif ($finalValue >= 55) {
                                                    if ($finalValue >= 66) echo 'C+';
                                                    elseif ($finalValue >= 61) echo 'C';
                                                    else echo 'C-';
                                                } else {
                                                    echo 'F';
                                                }
                                            @endphp
                                        @else
                                            -
                                        @endif
                                    </span>
                                </td>
                                <td>
                                    <input type="text"
                                           name="grades[{{ $index }}][comments]"
                                           class="form-control form-control-sm"
                                           value="{{ $comments }}"
                                           placeholder="Izoh...">
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Saqlash
                    </button>
                    <a href="{{ route('vedomost.export-word', $vedomost->id) }}" class="btn btn-success" target="_blank">
                        <i class="fas fa-file-word me-2"></i>Word eksport
                    </a>
                    <a href="{{ route('vedomost.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Orqaga
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Column Modal -->
<div class="modal fade" id="addColumnModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Yangi ustun qo'shish</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('vedomost.add-column', $vedomost->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Ustun nomi <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required
                               placeholder="Masalan: Oraliq nazorat 2, Amaliy mashg'ulot">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Maksimal ball <span class="text-danger">*</span></label>
                        <input type="number" name="max_score" class="form-control" value="100" min="1" max="100" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Turi <span class="text-danger">*</span></label>
                        <select name="column_type" class="form-select" required>
                            <option value="numeric">Raqamli (0-100)</option>
                            <option value="letter">Harfli (A-F)</option>
                            <option value="text">Matnli</option>
                        </select>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" name="is_final" value="1" class="form-check-input" id="isFinalCheck">
                        <label class="form-check-label" for="isFinalCheck">
                            Yakuniy nazorat sifatida belgilash
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bekor qilish</button>
                    <button type="submit" class="btn btn-primary">Qo'shish</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Auto-calculate letter grade
document.querySelectorAll('.grade-input').forEach(input => {
    input.addEventListener('input', function() {
        const grade = parseFloat(this.value);
        const letterTarget = document.getElementById(this.dataset.letterTarget);

        if (isNaN(grade) || grade < 0) {
            letterTarget.textContent = '-';
            letterTarget.className = 'badge bg-secondary';
            return;
        }

        let letter, badgeClass;

        if (grade >= 86) {
            letter = 'A';
            badgeClass = 'bg-success';
        } else if (grade >= 71) {
            letter = 'B';
            badgeClass = 'bg-info';
        } else if (grade >= 55) {
            letter = 'C';
            badgeClass = 'bg-warning';
        } else {
            letter = 'F';
            badgeClass = 'bg-danger';
        }

        // Add + or -
        if (grade >= 86) {
            if (grade >= 96) letter = 'A+';
            else if (grade >= 91) letter = 'A';
            else letter = 'A-';
        } else if (grade >= 71) {
            if (grade >= 81) letter = 'B+';
            else if (grade >= 76) letter = 'B';
            else letter = 'B-';
        } else if (grade >= 55) {
            if (grade >= 66) letter = 'C+';
            else if (grade >= 61) letter = 'C';
            else letter = 'C-';
        }

        letterTarget.textContent = letter;
        letterTarget.className = `badge ${badgeClass}`;
    });
});

// Form validation - removed strict requirement, now allows saving with any number of grades including zero
document.getElementById('gradesForm').addEventListener('submit', function(e) {
    // Allow saving even without grades (for initial save or partial completion)
    return true;
});

// Handle column removal
document.querySelectorAll('.btn-remove-column').forEach(button => {
    button.addEventListener('click', function(e) {
        e.preventDefault();
        const columnId = this.dataset.columnId;

        if (confirm('Ushbu ustunni o\'chirishni xohlaysizmi? Bu ustundagi barcha baholar o\'chiriladi!')) {
            // Create form and submit
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route('vedomost.remove-column', ['vedomostId' => $vedomost->id, 'columnId' => '__ID__']) }}'.replace('__ID__', columnId);

            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            form.appendChild(csrfToken);

            const methodField = document.createElement('input');
            methodField.type = 'hidden';
            methodField.name = '_method';
            methodField.value = 'DELETE';
            form.appendChild(methodField);

            document.body.appendChild(form);
            form.submit();
        }
    });
});
</script>
@endpush
@endsection
