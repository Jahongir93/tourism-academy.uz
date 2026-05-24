@extends('layouts.dashboard-new')

@section('title', 'Yangi guruh yaratish - HEMIS')
@section('page-title', 'Yangi guruh yaratish')

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
    .form-section {
        background: white;
        border-radius: 12px;
        padding: 24px;
        margin-bottom: 24px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        border: 1px solid var(--border-green);
    }
    .section-title {
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 20px;
        padding-bottom: 12px;
        border-bottom: 2px solid var(--light-green);
        color: var(--text-dark);
    }
    .section-title i {
        color: var(--secondary-green) !important;
    }
    .form-label {
        font-weight: 500;
        color: var(--text-dark);
        margin-bottom: 8px;
        display: block;
    }
    .required-star {
        color: #e74c3c;
        margin-left: 4px;
    }
    .form-control {
        border: 1px solid var(--border-green);
        transition: all 0.3s;
    }
    .form-control:focus {
        border-color: var(--secondary-green);
        box-shadow: 0 0 0 3px rgba(22, 160, 133, 0.1);
    }
    .form-help {
        font-size: 12px;
        color: #7f8c8d;
        margin-top: 4px;
    }
    select.form-control {
        background-color: white;
        color: var(--text-dark);
    }
    select.form-control:focus {
        background-color: white;
    }
    select.form-control option {
        background-color: white;
        color: var(--text-dark);
    }
    input.form-control::placeholder,
    textarea.form-control::placeholder {
        color: #95a5a6;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Back Button -->
    <div class="mb-4">
        <a href="{{ route('student-contingent.groups.index') }}" class="inline-flex items-center transition-all"
           style="color: var(--secondary-green);"
           onmouseover="this.style.color='var(--primary-dark-green)'"
           onmouseout="this.style.color='var(--secondary-green)'">
            <i class="fas fa-arrow-left mr-2"></i>
            <span>Guruhlar ro'yxatiga qaytish</span>
        </a>
    </div>

    <form action="{{ route('student-contingent.groups.store') }}" method="POST" id="group-form">
        @csrf

        <!-- Asosiy ma'lumotlar -->
        <div class="form-section">
            <h3 class="section-title">
                <i class="fas fa-info-circle mr-2"></i>
                Guruh asosiy ma'lumotlari
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="name" class="form-label">
                        Guruh nomi <span class="required-star">*</span>
                    </label>
                    <input type="text"
                           class="form-control @error('name') is-invalid @enderror"
                           id="name"
                           name="name"
                           value="{{ old('name') }}"
                           placeholder="Masalan: TUR-21-01"
                           required>
                    <div class="form-help">Guruhning rasmiy nomini kiriting</div>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label for="code" class="form-label">
                        Guruh kodi
                    </label>
                    <input type="text"
                           class="form-control @error('code') is-invalid @enderror"
                           id="code"
                           name="code"
                           value="{{ old('code') }}"
                           placeholder="Avtomatik generatsiya qilinadi">
                    <div class="form-help">Bo'sh qoldirilsa avtomatik yaratiladi</div>
                    @error('code')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                <div>
                    <label for="academic_year" class="form-label">
                        O'quv yili
                    </label>
                    <input type="text"
                           class="form-control @error('academic_year') is-invalid @enderror"
                           id="academic_year"
                           name="academic_year"
                           value="{{ old('academic_year', '2024-2025') }}"
                           placeholder="2024-2025">
                    <div class="form-help">Format: YYYY-YYYY</div>
                    @error('academic_year')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Tuzilma ma'lumotlari -->
        <div class="form-section">
            <h3 class="section-title">
                <i class="fas fa-sitemap mr-2"></i>
                Tashkiliy tuzilma
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="faculty_id" class="form-label">
                        Fakultet <span class="required-star">*</span>
                    </label>
                    <select class="form-control @error('faculty_id') is-invalid @enderror"
                            id="faculty_id"
                            name="faculty_id"
                            required>
                        <option value="">-- Fakultetni tanlang --</option>
                        @foreach($faculties as $faculty)
                            <option value="{{ $faculty->id }}" {{ old('faculty_id') == $faculty->id ? 'selected' : '' }}>
                                {{ $faculty->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('faculty_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label for="department_id" class="form-label">
                        Kafedra
                    </label>
                    <select class="form-control @error('department_id') is-invalid @enderror"
                            id="department_id"
                            name="department_id">
                        <option value="">-- Kafedrani tanlang --</option>
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                {{ $department->name }}
                            </option>
                        @endforeach
                    </select>
                    <div class="form-help">Agar kerak bo'lsa, kafedrani tanlang</div>
                    @error('department_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label for="specialty_id" class="form-label">
                        Mutaxassislik
                    </label>
                    <select class="form-control @error('specialty_id') is-invalid @enderror"
                            id="specialty_id"
                            name="specialty_id">
                        <option value="">-- Mutaxassislikni tanlang --</option>
                        @foreach($specialties as $specialty)
                            <option value="{{ $specialty->id }}" {{ old('specialty_id') == $specialty->id ? 'selected' : '' }}>
                                {{ $specialty->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('specialty_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label for="curator_id" class="form-label">
                        Kurator
                    </label>
                    <select class="form-control @error('curator_id') is-invalid @enderror"
                            id="curator_id"
                            name="curator_id">
                        <option value="">-- Kuratorni tanlang --</option>
                        @foreach($curators as $curator)
                            <option value="{{ $curator->id }}" {{ old('curator_id') == $curator->id ? 'selected' : '' }}>
                                {{ $curator->name }}
                            </option>
                        @endforeach
                    </select>
                    <div class="form-help">Guruh kuratorini belgilash</div>
                    @error('curator_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Ta'lim parametrlari -->
        <div class="form-section">
            <h3 class="section-title">
                <i class="fas fa-graduation-cap mr-2"></i>
                Ta'lim parametrlari
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label for="course" class="form-label">
                        Kurs <span class="required-star">*</span>
                    </label>
                    <select class="form-control @error('course') is-invalid @enderror"
                            id="course"
                            name="course"
                            required>
                        <option value="">-- Tanlang --</option>
                        @for($i = 1; $i <= 6; $i++)
                            <option value="{{ $i }}" {{ old('course') == $i ? 'selected' : '' }}>
                                {{ $i }}-kurs
                            </option>
                        @endfor
                    </select>
                    @error('course')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label for="semester" class="form-label">
                        Semestr <span class="required-star">*</span>
                    </label>
                    <select class="form-control @error('semester') is-invalid @enderror"
                            id="semester"
                            name="semester"
                            required>
                        <option value="">-- Tanlang --</option>
                        @for($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ old('semester') == $i ? 'selected' : '' }}>
                                {{ $i }}-semestr
                            </option>
                        @endfor
                    </select>
                    @error('semester')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label for="education_type" class="form-label">
                        Ta'lim turi
                    </label>
                    <select class="form-control @error('education_type') is-invalid @enderror"
                            id="education_type"
                            name="education_type">
                        <option value="">-- Tanlang --</option>
                        <option value="bakalavr" {{ old('education_type') == 'bakalavr' ? 'selected' : '' }}>Bakalavr</option>
                        <option value="magistr" {{ old('education_type') == 'magistr' ? 'selected' : '' }}>Magistr</option>
                        <option value="doktorantura" {{ old('education_type') == 'doktorantura' ? 'selected' : '' }}>Doktorantura</option>
                    </select>
                    @error('education_type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label for="education_form" class="form-label">
                        Ta'lim shakli
                    </label>
                    <select class="form-control @error('education_form') is-invalid @enderror"
                            id="education_form"
                            name="education_form">
                        <option value="">-- Tanlang --</option>
                        <option value="kunduzgi" {{ old('education_form') == 'kunduzgi' ? 'selected' : '' }}>Kunduzgi</option>
                        <option value="kechki" {{ old('education_form') == 'kechki' ? 'selected' : '' }}>Kechki</option>
                        <option value="sirtqi" {{ old('education_form') == 'sirtqi' ? 'selected' : '' }}>Sirtqi</option>
                    </select>
                    @error('education_form')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                <div>
                    <label for="language" class="form-label">
                        Ta'lim tili <span class="required-star">*</span>
                    </label>
                    <select class="form-control @error('language') is-invalid @enderror"
                            id="language"
                            name="language"
                            required>
                        <option value="">-- Tanlang --</option>
                        <option value="uz" {{ old('language') == 'uz' ? 'selected' : '' }}>O'zbek</option>
                        <option value="ru" {{ old('language') == 'ru' ? 'selected' : '' }}>Rus</option>
                        <option value="en" {{ old('language') == 'en' ? 'selected' : '' }}>Ingliz</option>
                    </select>
                    @error('language')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label for="max_students" class="form-label">
                        Maksimal talabalar soni <span class="required-star">*</span>
                    </label>
                    <input type="number"
                           class="form-control @error('max_students') is-invalid @enderror"
                           id="max_students"
                           name="max_students"
                           value="{{ old('max_students', 30) }}"
                           min="1"
                           max="100"
                           required>
                    <div class="form-help">Guruhdagi maksimal talabalar soni (1-100)</div>
                    @error('max_students')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Qo'shimcha ma'lumotlar -->
        <div class="form-section">
            <h3 class="section-title">
                <i class="fas fa-comment-alt mr-2"></i>
                Qo'shimcha ma'lumotlar
            </h3>

            <div>
                <label for="description" class="form-label">
                    Guruh haqida tavsif
                </label>
                <textarea class="form-control @error('description') is-invalid @enderror"
                          id="description"
                          name="description"
                          rows="4"
                          placeholder="Guruh haqida qo'shimcha ma'lumotlar...">{{ old('description') }}</textarea>
                <div class="form-help">Guruh haqida qo'shimcha ma'lumotlar, eslatmalar yoki tavsiflar</div>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="bg-white rounded-lg shadow p-6 flex justify-between items-center" style="border: 1px solid var(--border-green);">
            <div class="text-sm" style="color: #7f8c8d;">
                <i class="fas fa-info-circle mr-1" style="color: var(--secondary-green);"></i>
                <span class="required-star">*</span> belgilangan maydonlar to'ldirilishi shart
            </div>
            <div class="flex gap-3">
                <a href="{{ route('student-contingent.groups.index') }}"
                   class="px-6 py-2.5 rounded-lg transition-all"
                   style="background: var(--light-green); color: var(--text-dark);"
                   onmouseover="this.style.background='#d1ebe3'"
                   onmouseout="this.style.background='var(--light-green)'">
                    <i class="fas fa-times mr-2"></i>Bekor qilish
                </a>
                <button type="submit"
                        class="px-6 py-2.5 text-white rounded-lg transition-all"
                        style="background: var(--primary-dark-green);"
                        onmouseover="this.style.background='var(--secondary-green)'"
                        onmouseout="this.style.background='var(--primary-dark-green)'">
                    <i class="fas fa-check mr-2"></i>Guruhni yaratish
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    // Auto-update semester based on course selection
    document.getElementById('course').addEventListener('change', function() {
        const course = parseInt(this.value);
        const semesterSelect = document.getElementById('semester');

        if (course) {
            const minSemester = (course - 1) * 2 + 1;
            const maxSemester = course * 2;

            // Clear current options
            semesterSelect.innerHTML = '<option value="">-- Tanlang --</option>';

            // Add relevant semester options
            for (let i = minSemester; i <= maxSemester; i++) {
                const option = document.createElement('option');
                option.value = i;
                option.textContent = i + '-semestr';
                semesterSelect.appendChild(option);
            }
        }
    });

    // Dynamic department loading based on faculty
    document.getElementById('faculty_id').addEventListener('change', function() {
        const facultyId = this.value;
        const departmentSelect = document.getElementById('department_id');

        if (facultyId) {
            // This would normally make an AJAX request to get departments
            // For now, we'll just enable the department select
            departmentSelect.disabled = false;
        } else {
            departmentSelect.disabled = true;
            departmentSelect.value = '';
        }
    });
</script>
@endsection