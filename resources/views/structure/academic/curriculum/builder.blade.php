@extends('layouts.dashboard-new')

@section('title', "O'quv reja tuzish - " . $program->name_uz)
@section('page-title', "O'quv reja tuzish")

@section('styles')
<style>
    :root {
        --primary-dark-green: #0d4f3c;
        --secondary-green: #16a085;
        --light-green: #e8f5f0;
        --accent-green: #48c9b0;
        --border-green: #c3e6d8;
        --text-dark: #2c3e50;
        --hover-green: #0a3d2e;
        --very-light-green: #f0f9f6;
    }

    .breadcrumb {
        background: var(--very-light-green);
        padding: 12px 20px;
        border-radius: 8px;
        border: 1px solid var(--border-green);
    }

    .breadcrumb-item a {
        color: var(--secondary-green);
        text-decoration: none;
    }

    .breadcrumb-item a:hover {
        color: var(--primary-dark-green);
    }

    .breadcrumb-item.active {
        color: var(--text-dark);
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Breadcrumb -->
    <div class="row mb-3">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('structure.academic.curriculum.index') }}">O'quv rejalar</a></li>
                    <li class="breadcrumb-item active">O'quv reja tuzish</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Program Info -->
    <div class="card border-0 shadow-sm mb-4" style="border: 1px solid var(--border-green) !important; background: linear-gradient(135deg, var(--very-light-green), white);">
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <h5 style="color: var(--primary-dark-green); font-weight: 600;">
                        <span class="badge" style="background: var(--secondary-green); color: white;">{{ $program->code }}</span>
                        {{ $program->name_uz }}
                    </h5>
                    <p class="mb-1" style="color: var(--text-dark);">
                        <strong>Fakultet:</strong>
                        <span class="badge" style="background: var(--light-green); color: var(--primary-dark-green);">
                            {{ $program->faculty->name_uz ?? 'N/A' }}
                        </span>
                    </p>
                    <p class="mb-1" style="color: var(--text-dark);">
                        <strong>Ta'lim shakli:</strong>
                        <span class="badge" style="background: var(--accent-green); color: white;">
                            {{ ucfirst($program->education_form ?? 'kunduzgi') }}
                        </span>
                    </p>
                </div>
                <div class="col-md-4 text-end">
                    <div class="mb-2">
                        <select class="form-select" style="border: 1px solid var(--border-green); background: var(--very-light-green);"
                                onchange="window.location.href='?academic_year=' + this.value">
                            @foreach($academicYears as $year)
                                <option value="{{ $year }}" {{ $academicYear == $year ? 'selected' : '' }}>
                                    {{ $year }} o'quv yili
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <a href="{{ route('structure.academic.curriculum.topics') }}?program_id={{ $program->id }}"
                       class="btn text-white"
                       style="background: var(--secondary-green);"
                       onmouseover="this.style.background='var(--primary-dark-green)'"
                       onmouseout="this.style.background='var(--secondary-green)'">
                        <i class="fas fa-list"></i> Mavzular
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm" style="border: 1px solid var(--border-green) !important; background: linear-gradient(135deg, var(--primary-dark-green), var(--secondary-green));">
                <div class="card-body text-white">
                    <div class="d-flex align-items-center mb-2">
                        <div class="rounded-circle p-2" style="background: rgba(255,255,255,0.2);">
                            <i class="fas fa-certificate"></i>
                        </div>
                    </div>
                    <h3 class="mb-0">{{ $stats['total_credits'] }}</h3>
                    <p class="mb-0 opacity-90">Jami kredit</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm" style="border: 1px solid var(--border-green) !important; background: linear-gradient(135deg, var(--secondary-green), var(--accent-green));">
                <div class="card-body text-white">
                    <div class="d-flex align-items-center mb-2">
                        <div class="rounded-circle p-2" style="background: rgba(255,255,255,0.2);">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                    <h3 class="mb-0">{{ $stats['total_hours'] }}</h3>
                    <p class="mb-0 opacity-90">Jami soat</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm" style="border: 1px solid var(--border-green) !important;">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="rounded-circle p-2" style="background: var(--light-green);">
                            <i class="fas fa-chalkboard-teacher" style="color: var(--primary-dark-green);"></i>
                        </div>
                    </div>
                    <h3 class="mb-0" style="color: var(--primary-dark-green);">{{ $stats['auditory_hours'] }}</h3>
                    <p class="mb-0" style="color: #7f8c8d;">Auditoriya soatlari</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm" style="border: 1px solid var(--border-green) !important;">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="rounded-circle p-2" style="background: var(--light-green);">
                            <i class="fas fa-book-reader" style="color: var(--secondary-green);"></i>
                        </div>
                    </div>
                    <h3 class="mb-0" style="color: var(--secondary-green);">{{ $stats['independent_hours'] }}</h3>
                    <p class="mb-0" style="color: #7f8c8d;">Mustaqil ta'lim</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Curriculum Builder by Semester -->
    @for($sem = 1; $sem <= ($program->duration ?? 4) * 2; $sem++)
        <div class="card border-0 shadow-sm mb-4" style="border: 1px solid var(--border-green) !important;">
            <div class="card-header" style="background: var(--light-green); border-bottom: 2px solid var(--border-green);">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0" style="color: var(--text-dark); font-weight: 600;">
                        <i class="fas fa-graduation-cap" style="color: var(--secondary-green);"></i>
                        {{ $sem }}-semestr
                    </h5>
                    <button type="button" class="btn text-white"
                            style="background: var(--primary-dark-green);"
                            onmouseover="this.style.background='var(--secondary-green)'"
                            onmouseout="this.style.background='var(--primary-dark-green)'"
                            onclick="addSubjectToSemester({{ $sem }})">
                        <i class="fas fa-plus"></i> Fan qo'shish
                    </button>
                </div>
            </div>
            <div class="card-body">
                @php
                    $semesterCurriculum = $curriculum->get($sem, collect());
                @endphp
                
                @if($semesterCurriculum->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead style="background: var(--very-light-green);">
                                <tr>
                                    <th width="50" style="color: var(--text-dark); font-weight: 600;">№</th>
                                    <th style="color: var(--text-dark); font-weight: 600;">Fan nomi</th>
                                    <th width="100" style="color: var(--text-dark); font-weight: 600;">Ma'ruza</th>
                                    <th width="100" style="color: var(--text-dark); font-weight: 600;">Amaliyot</th>
                                    <th width="100" style="color: var(--text-dark); font-weight: 600;">Seminar</th>
                                    <th width="100" style="color: var(--text-dark); font-weight: 600;">Laboratoriya</th>
                                    <th width="100" style="color: var(--text-dark); font-weight: 600;">Mustaqil</th>
                                    <th width="80" style="color: var(--text-dark); font-weight: 600;">Kredit</th>
                                    <th width="100" style="color: var(--text-dark); font-weight: 600;">Fan turi</th>
                                    <th width="100" style="color: var(--text-dark); font-weight: 600;">Amallar</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($semesterCurriculum as $item)
                                    <tr onmouseover="this.style.background='var(--very-light-green)'" onmouseout="this.style.background='white'">
                                        <td style="color: var(--text-dark);">{{ $loop->iteration }}</td>
                                        <td>
                                            <strong style="color: var(--text-dark);">{{ $item->subject->name_uz }}</strong>
                                        </td>
                                        <td style="color: var(--text-dark);">{{ $item->lecture_hours }}</td>
                                        <td style="color: var(--text-dark);">{{ $item->practice_hours }}</td>
                                        <td style="color: var(--text-dark);">{{ $item->seminar_hours }}</td>
                                        <td style="color: var(--text-dark);">{{ $item->lab_hours }}</td>
                                        <td style="color: var(--text-dark);">{{ $item->independent_hours }}</td>
                                        <td>
                                            <span class="badge" style="background: var(--accent-green); color: white;">
                                                {{ $item->credits }} kr
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge" style="background: {{ $item->subject_type == 'majburiy' ? 'var(--primary-dark-green)' : 'var(--secondary-green)' }}; color: white;">
                                                {{ ucfirst($item->subject_type) }}
                                            </span>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm"
                                                    style="border: 1px solid var(--secondary-green); color: var(--secondary-green);"
                                                    onmouseover="this.style.background='var(--light-green)'"
                                                    onmouseout="this.style.background='transparent'"
                                                    onclick="editCurriculumItem({{ $item->id }})">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm"
                                                    style="border: 1px solid #dc3545; color: #dc3545;"
                                                    onmouseover="this.style.background='#fef0f0'"
                                                    onmouseout="this.style.background='transparent'"
                                                    onclick="deleteCurriculumItem({{ $item->id }})">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5" style="color: #7f8c8d;">
                        <i class="fas fa-book-open fa-3x mb-3" style="color: var(--secondary-green);"></i>
                        <p>Bu semestrda hali fanlar qo'shilmagan</p>
                    </div>
                @endif
            </div>
        </div>
    @endfor
</div>

<!-- Add Subject Modal -->
<div class="modal fade" id="addSubjectModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="border: 2px solid var(--border-green);">
            <form action="{{ route('structure.academic.curriculum.save', $program) }}" method="POST">
                @csrf
                <input type="hidden" name="academic_year" value="{{ $academicYear }}">
                <input type="hidden" name="semester" id="modal_semester">

                <div class="modal-header" style="background: var(--light-green); border-bottom: 2px solid var(--border-green);">
                    <h5 class="modal-title" style="color: var(--text-dark); font-weight: 600;">
                        <i class="fas fa-plus-circle" style="color: var(--secondary-green);"></i>
                        Fan qo'shish - <span id="semester_title"></span>-semestr
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Fan <span class="text-danger">*</span></label>
                        <select name="subjects[0][subject_id]" class="form-select" required>
                            <option value="">Fanni tanlang</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}">
                                    {{ $subject->code }} - {{ $subject->name_uz }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-3">
                            <label class="form-label">Ma'ruza (soat)</label>
                            <input type="number" name="subjects[0][lecture_hours]" class="form-control" value="0" min="0">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Amaliyot (soat)</label>
                            <input type="number" name="subjects[0][practice_hours]" class="form-control" value="0" min="0">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Seminar (soat)</label>
                            <input type="number" name="subjects[0][seminar_hours]" class="form-control" value="0" min="0">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Laboratoriya (soat)</label>
                            <input type="number" name="subjects[0][lab_hours]" class="form-control" value="0" min="0">
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-md-4">
                            <label class="form-label">Mustaqil ta'lim (soat)</label>
                            <input type="number" name="subjects[0][independent_hours]" class="form-control" value="0" min="0">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Kredit <span class="text-danger">*</span></label>
                            <input type="number" name="subjects[0][credits]" class="form-control" value="2" min="1" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Fan turi <span class="text-danger">*</span></label>
                            <select name="subjects[0][subject_type]" class="form-select" required>
                                <option value="majburiy">Majburiy</option>
                                <option value="tanlov">Tanlov</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="background: var(--very-light-green); border-top: 1px solid var(--border-green);">
                    <button type="button" class="btn"
                            style="background: #6c757d; color: white;"
                            data-bs-dismiss="modal">Bekor qilish</button>
                    <button type="submit" class="btn text-white"
                            style="background: var(--primary-dark-green);"
                            onmouseover="this.style.background='var(--secondary-green)'"
                            onmouseout="this.style.background='var(--primary-dark-green)'">
                        <i class="fas fa-save"></i> Saqlash
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function addSubjectToSemester(semester) {
    document.getElementById('modal_semester').value = semester;
    document.getElementById('semester_title').textContent = semester;
    const modal = new bootstrap.Modal(document.getElementById('addSubjectModal'));
    modal.show();
}

function editCurriculumItem(id) {
    // Edit functionality
    alert('Tahrirlash funksiyasi tez orada qo\'shiladi');
}

function deleteCurriculumItem(id) {
    if (confirm('Fanni o\'chirishni xohlaysizmi?')) {
        // Delete functionality
        alert('O\'chirish funksiyasi tez orada qo\'shiladi');
    }
}
</script>
@endpush
@endsection