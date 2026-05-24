@extends('layouts.dashboard-new')

@section('title', "O'quv Sozlamalari")
@section('page-title', "O'quv Sozlamalari")

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm bg-gradient-success text-white">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1"><i class="fas fa-graduation-cap me-2"></i>O'quv Sozlamalari</h4>
                            <p class="mb-0 opacity-75">O'quv yili, semestr, davomat va baho tizimi sozlamalari</p>
                        </div>
                        <a href="{{ route('settings.index') }}" class="btn btn-light">
                            <i class="fas fa-arrow-left me-1"></i> Orqaga
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

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

    <form action="{{ route('settings.academic.update') }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row">
            <!-- O'quv Yili Sozlamalari -->
            <div class="col-lg-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-calendar-alt text-primary me-2"></i>O'quv Yili</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Joriy o'quv yili <span class="text-danger">*</span></label>
                            <select name="current_academic_year" class="form-select" required>
                                @foreach($academicYears ?? [] as $year)
                                <option value="{{ $year->id }}" {{ $year->is_current ? 'selected' : '' }}>
                                    {{ $year->name }} {{ $year->is_current ? '(Joriy)' : '' }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">O'quv yili boshlanish sanasi</label>
                            <input type="date" name="academic_year_start" class="form-control"
                                   value="{{ old('academic_year_start', $settings->where('key', 'academic_year_start')->first()?->value ?? '') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">O'quv yili tugash sanasi</label>
                            <input type="date" name="academic_year_end" class="form-control"
                                   value="{{ old('academic_year_end', $settings->where('key', 'academic_year_end')->first()?->value ?? '') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Maksimal o'quv yillari soni</label>
                            <input type="number" name="max_study_years" class="form-control" min="1" max="10"
                                   value="{{ old('max_study_years', $settings->where('key', 'max_study_years')->first()?->value ?? '4') }}">
                            <small class="text-muted">Bakalavr uchun odatda 4 yil</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Semestr Sozlamalari -->
            <div class="col-lg-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-clock text-success me-2"></i>Semestr Sozlamalari</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Joriy semestr <span class="text-danger">*</span></label>
                            <select name="current_semester" class="form-select" required>
                                <option value="1" {{ old('current_semester', $settings->where('key', 'current_semester')->first()?->value ?? '1') == '1' ? 'selected' : '' }}>1-semestr (Kuz)</option>
                                <option value="2" {{ old('current_semester', $settings->where('key', 'current_semester')->first()?->value ?? '1') == '2' ? 'selected' : '' }}>2-semestr (Bahor)</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">1-semestr boshlanish sanasi</label>
                            <input type="date" name="semester1_start" class="form-control"
                                   value="{{ old('semester1_start', $settings->where('key', 'semester1_start')->first()?->value ?? '') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">1-semestr tugash sanasi</label>
                            <input type="date" name="semester1_end" class="form-control"
                                   value="{{ old('semester1_end', $settings->where('key', 'semester1_end')->first()?->value ?? '') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">2-semestr boshlanish sanasi</label>
                            <input type="date" name="semester2_start" class="form-control"
                                   value="{{ old('semester2_start', $settings->where('key', 'semester2_start')->first()?->value ?? '') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">2-semestr tugash sanasi</label>
                            <input type="date" name="semester2_end" class="form-control"
                                   value="{{ old('semester2_end', $settings->where('key', 'semester2_end')->first()?->value ?? '') }}">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Davomat Sozlamalari -->
            <div class="col-lg-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-user-check text-info me-2"></i>Davomat Sozlamalari</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Minimal davomat foizi (%)</label>
                            <input type="number" name="min_attendance_percent" class="form-control" min="0" max="100"
                                   value="{{ old('min_attendance_percent', $settings->where('key', 'min_attendance_percent')->first()?->value ?? '75') }}">
                            <small class="text-muted">Bu foizdan past bo'lsa ogohlantirish beriladi</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Sababsiz yo'qlama limiti</label>
                            <input type="number" name="max_unexcused_absences" class="form-control" min="0"
                                   value="{{ old('max_unexcused_absences', $settings->where('key', 'max_unexcused_absences')->first()?->value ?? '10') }}">
                            <small class="text-muted">Semestr davomida maksimal sababsiz yo'qlamalar</small>
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="attendance_notification" id="attendance_notification"
                                       {{ old('attendance_notification', $settings->where('key', 'attendance_notification')->first()?->value ?? '1') == '1' ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="attendance_notification">Davomat ogohlantirishlarini yoqish</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="late_attendance_allowed" id="late_attendance_allowed"
                                       {{ old('late_attendance_allowed', $settings->where('key', 'late_attendance_allowed')->first()?->value ?? '1') == '1' ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="late_attendance_allowed">Kechikish qaydini yoqish</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Baho Tizimi -->
            <div class="col-lg-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-star text-warning me-2"></i>Baho Tizimi</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Baholash tizimi</label>
                            <select name="grading_system" class="form-select">
                                <option value="100" {{ old('grading_system', $settings->where('key', 'grading_system')->first()?->value ?? '100') == '100' ? 'selected' : '' }}>100 ballik tizim</option>
                                <option value="5" {{ old('grading_system', $settings->where('key', 'grading_system')->first()?->value ?? '100') == '5' ? 'selected' : '' }}>5 ballik tizim</option>
                                <option value="gpa" {{ old('grading_system', $settings->where('key', 'grading_system')->first()?->value ?? '100') == 'gpa' ? 'selected' : '' }}>GPA tizimi</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">O'tish balli (100 ballik)</label>
                            <input type="number" name="passing_grade" class="form-control" min="0" max="100"
                                   value="{{ old('passing_grade', $settings->where('key', 'passing_grade')->first()?->value ?? '60') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">A'lo baho (dan)</label>
                            <input type="number" name="excellent_grade" class="form-control" min="0" max="100"
                                   value="{{ old('excellent_grade', $settings->where('key', 'excellent_grade')->first()?->value ?? '86') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Yaxshi baho (dan)</label>
                            <input type="number" name="good_grade" class="form-control" min="0" max="100"
                                   value="{{ old('good_grade', $settings->where('key', 'good_grade')->first()?->value ?? '71') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Qoniqarli baho (dan)</label>
                            <input type="number" name="satisfactory_grade" class="form-control" min="0" max="100"
                                   value="{{ old('satisfactory_grade', $settings->where('key', 'satisfactory_grade')->first()?->value ?? '60') }}">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dars Jadvali -->
            <div class="col-lg-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-table text-danger me-2"></i>Dars Jadvali</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Dars davomiyligi (daqiqa)</label>
                            <input type="number" name="lesson_duration" class="form-control" min="30" max="120"
                                   value="{{ old('lesson_duration', $settings->where('key', 'lesson_duration')->first()?->value ?? '80') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Tanaffus davomiyligi (daqiqa)</label>
                            <input type="number" name="break_duration" class="form-control" min="5" max="30"
                                   value="{{ old('break_duration', $settings->where('key', 'break_duration')->first()?->value ?? '10') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Kunlik maksimal dars soni</label>
                            <input type="number" name="max_lessons_per_day" class="form-control" min="1" max="10"
                                   value="{{ old('max_lessons_per_day', $settings->where('key', 'max_lessons_per_day')->first()?->value ?? '6') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Birinchi dars boshlanish vaqti</label>
                            <input type="time" name="first_lesson_time" class="form-control"
                                   value="{{ old('first_lesson_time', $settings->where('key', 'first_lesson_time')->first()?->value ?? '08:30') }}">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kredit Tizimi -->
            <div class="col-lg-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-coins text-secondary me-2"></i>Kredit Tizimi</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="credit_system_enabled" id="credit_system_enabled"
                                       {{ old('credit_system_enabled', $settings->where('key', 'credit_system_enabled')->first()?->value ?? '1') == '1' ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="credit_system_enabled">Kredit tizimini yoqish</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">1 kredit = soat</label>
                            <input type="number" name="credit_hours" class="form-control" min="1" max="50"
                                   value="{{ old('credit_hours', $settings->where('key', 'credit_hours')->first()?->value ?? '30') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Semestrlik minimal kredit</label>
                            <input type="number" name="min_credits_per_semester" class="form-control" min="1"
                                   value="{{ old('min_credits_per_semester', $settings->where('key', 'min_credits_per_semester')->first()?->value ?? '15') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Semestrlik maksimal kredit</label>
                            <input type="number" name="max_credits_per_semester" class="form-control" min="1"
                                   value="{{ old('max_credits_per_semester', $settings->where('key', 'max_credits_per_semester')->first()?->value ?? '30') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Bitirish uchun jami kredit</label>
                            <input type="number" name="total_credits_required" class="form-control" min="1"
                                   value="{{ old('total_credits_required', $settings->where('key', 'total_credits_required')->first()?->value ?? '240') }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- O'quv Yillari Jadvali -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-list text-primary me-2"></i>O'quv Yillari</h5>
                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addAcademicYearModal">
                            <i class="fas fa-plus me-1"></i> Yangi yil qo'shish
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>O'quv yili</th>
                                        <th>Boshlanish</th>
                                        <th>Tugash</th>
                                        <th>Holat</th>
                                        <th>Amallar</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($academicYears ?? [] as $index => $year)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td><strong>{{ $year->name }}</strong></td>
                                        <td>{{ $year->start_date ? \Carbon\Carbon::parse($year->start_date)->format('d.m.Y') : '-' }}</td>
                                        <td>{{ $year->end_date ? \Carbon\Carbon::parse($year->end_date)->format('d.m.Y') : '-' }}</td>
                                        <td>
                                            @if($year->is_current)
                                            <span class="badge bg-success"><i class="fas fa-check me-1"></i>Joriy</span>
                                            @else
                                            <span class="badge bg-secondary">Arxiv</span>
                                            @endif
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-outline-primary" title="Tahrirlash">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            @if(!$year->is_current)
                                            <button type="button" class="btn btn-sm btn-outline-success" title="Joriy qilish">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">
                                            <i class="fas fa-calendar-times fa-2x mb-2 d-block"></i>
                                            O'quv yillari topilmadi
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Submit -->
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-end">
                        <button type="reset" class="btn btn-outline-secondary me-2">
                            <i class="fas fa-undo me-1"></i> Bekor qilish
                        </button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save me-1"></i> Saqlash
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- Yangi O'quv Yilini Boshlash -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm border-warning">
                <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-rocket me-2"></i>Yangi O'quv Yilini Boshlash</h5>
                    <span class="badge bg-dark">Muhim amal</span>
                </div>
                <div class="card-body">
                    <div class="alert alert-info mb-4">
                        <h6 class="alert-heading"><i class="fas fa-info-circle me-2"></i>Bu amal quyidagilarni bajaradi:</h6>
                        <ul class="mb-0">
                            <li>Barcha faol guruhlar keyingi kursga o'tkaziladi (1→2, 2→3, ...)</li>
                            <li>Barcha talabalarning kursi yangilanadi</li>
                            <li>Oxirgi kursdagi guruhlar (bitiruvchilar) arxivlanadi</li>
                            <li>Yangi o'quv yili yaratiladi va joriy qilib belgilanadi</li>
                            <li>Barcha harakatlar StudentMovement jadvalida qayd etiladi</li>
                        </ul>
                    </div>

                    <button type="button" class="btn btn-warning btn-lg" data-bs-toggle="modal" data-bs-target="#startNewYearModal">
                        <i class="fas fa-play-circle me-2"></i>Yangi O'quv Yilini Boshlash
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Yangi o'quv yili qo'shish -->
<div class="modal fade" id="addAcademicYearModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-plus-circle me-2"></i>Yangi O'quv Yili</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('settings.academic.year.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">O'quv yili nomi <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="2024-2025" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Boshlanish sanasi <span class="text-danger">*</span></label>
                        <input type="date" name="start_date" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tugash sanasi <span class="text-danger">*</span></label>
                        <input type="date" name="end_date" class="form-control" required>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_current" id="is_current">
                        <label class="form-check-label" for="is_current">Joriy o'quv yili sifatida belgilash</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bekor qilish</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Saqlash</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Yangi o'quv yilini boshlash -->
<div class="modal fade" id="startNewYearModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title"><i class="fas fa-rocket me-2"></i>Yangi O'quv Yilini Boshlash</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('settings.academic.start-new-year') }}" method="POST" id="startNewYearForm">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-warning mb-4">
                        <strong><i class="fas fa-exclamation-triangle me-2"></i>Diqqat!</strong>
                        Bu amalni qaytarib bo'lmaydi. Iltimos, ma'lumotlarni tekshirib ko'ring.
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Yangi o'quv yili nomi <span class="text-danger">*</span></label>
                            @php
                                $currentYear = \App\Models\AcademicYear::where('is_current', true)->first();
                                $suggestedYear = '';
                                if ($currentYear) {
                                    $parts = explode('-', $currentYear->name);
                                    if (count($parts) === 2) {
                                        $suggestedYear = ((int)$parts[1]) . '-' . ((int)$parts[1] + 1);
                                    }
                                } else {
                                    $suggestedYear = date('Y') . '-' . (date('Y') + 1);
                                }
                            @endphp
                            <input type="text" name="new_year_name" class="form-control"
                                   value="{{ $suggestedYear }}"
                                   pattern="\d{4}-\d{4}"
                                   placeholder="2025-2026" required>
                            <small class="text-muted">Format: YYYY-YYYY (masalan: 2025-2026)</small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Maksimal kurs <span class="text-danger">*</span></label>
                            <select name="max_course" class="form-select" required>
                                <option value="4" selected>4-kurs (Bakalavr)</option>
                                <option value="2">2-kurs (Magistratura)</option>
                                <option value="3">3-kurs (Doktorantura)</option>
                                <option value="5">5-kurs</option>
                                <option value="6">6-kurs</option>
                            </select>
                            <small class="text-muted">Bu kursdagi guruhlar bitiruvchi sifatida arxivlanadi</small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Boshlanish sanasi <span class="text-danger">*</span></label>
                            <input type="date" name="start_date" class="form-control"
                                   value="{{ date('Y') }}-09-01" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Tugash sanasi <span class="text-danger">*</span></label>
                            <input type="date" name="end_date" class="form-control"
                                   value="{{ date('Y') + 1 }}-06-30" required>
                        </div>
                    </div>

                    <!-- Ko'rib chiqish -->
                    <div class="card bg-light mt-3">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="fas fa-eye me-2"></i>Ko'rib chiqish</h6>
                        </div>
                        <div class="card-body" id="previewContent">
                            <div class="text-center py-4">
                                <button type="button" class="btn btn-outline-primary" id="loadPreviewBtn">
                                    <i class="fas fa-sync-alt me-2"></i>O'zgarishlarni ko'rish
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bekor qilish</button>
                    <button type="submit" class="btn btn-warning" id="confirmStartBtn" disabled>
                        <i class="fas fa-rocket me-1"></i> Tasdiqlash va Boshlash
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.bg-gradient-success {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
}
.border-warning {
    border: 2px solid #ffc107 !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const loadPreviewBtn = document.getElementById('loadPreviewBtn');
    const previewContent = document.getElementById('previewContent');
    const confirmStartBtn = document.getElementById('confirmStartBtn');
    const maxCourseSelect = document.querySelector('[name="max_course"]');

    if (loadPreviewBtn) {
        loadPreviewBtn.addEventListener('click', function() {
            const maxCourse = maxCourseSelect.value;

            previewContent.innerHTML = '<div class="text-center py-4"><i class="fas fa-spinner fa-spin fa-2x"></i><p class="mt-2">Yuklanmoqda...</p></div>';

            fetch(`{{ route('settings.academic.preview-new-year') }}?max_course=${maxCourse}`)
                .then(response => response.json())
                .then(data => {
                    let html = '<div class="row">';

                    // Statistics
                    html += '<div class="col-12 mb-3">';
                    html += '<div class="row text-center">';
                    html += `<div class="col-3"><div class="border rounded p-2"><h4 class="text-primary mb-0">${data.stats.total_groups}</h4><small>Jami guruh</small></div></div>`;
                    html += `<div class="col-3"><div class="border rounded p-2"><h4 class="text-success mb-0">${data.stats.promote_groups}</h4><small>Ko'tariladi</small></div></div>`;
                    html += `<div class="col-3"><div class="border rounded p-2"><h4 class="text-info mb-0">${data.stats.promote_students}</h4><small>Talabalar</small></div></div>`;
                    html += `<div class="col-3"><div class="border rounded p-2"><h4 class="text-warning mb-0">${data.stats.graduate_groups}</h4><small>Bitiruvchi</small></div></div>`;
                    html += '</div></div>';

                    // Tables
                    if (data.to_promote.length > 0) {
                        html += '<div class="col-md-6"><h6 class="text-success"><i class="fas fa-arrow-up me-1"></i>Ko\'tariladigan guruhlar</h6>';
                        html += '<div class="table-responsive"><table class="table table-sm table-bordered"><thead><tr><th>Guruh</th><th>Kurs</th><th>Talabalar</th></tr></thead><tbody>';
                        data.to_promote.forEach(g => {
                            html += `<tr><td>${g.name}</td><td>${g.current_course} → ${g.new_course}</td><td>${g.student_count}</td></tr>`;
                        });
                        html += '</tbody></table></div></div>';
                    }

                    if (data.to_graduate.length > 0) {
                        html += '<div class="col-md-6"><h6 class="text-warning"><i class="fas fa-graduation-cap me-1"></i>Bitiruvchi guruhlar</h6>';
                        html += '<div class="table-responsive"><table class="table table-sm table-bordered"><thead><tr><th>Guruh</th><th>Kurs</th><th>Talabalar</th></tr></thead><tbody>';
                        data.to_graduate.forEach(g => {
                            html += `<tr><td>${g.name}</td><td>${g.current_course}</td><td>${g.student_count}</td></tr>`;
                        });
                        html += '</tbody></table></div></div>';
                    }

                    html += '</div>';

                    previewContent.innerHTML = html;
                    confirmStartBtn.disabled = false;
                })
                .catch(error => {
                    previewContent.innerHTML = '<div class="alert alert-danger">Xatolik yuz berdi: ' + error.message + '</div>';
                });
        });
    }

    // Form submission confirmation
    document.getElementById('startNewYearForm')?.addEventListener('submit', function(e) {
        if (!confirm('Haqiqatan ham yangi o\'quv yilini boshlamoqchimisiz? Bu amalni qaytarib bo\'lmaydi!')) {
            e.preventDefault();
        }
    });
});
</script>
@endsection
