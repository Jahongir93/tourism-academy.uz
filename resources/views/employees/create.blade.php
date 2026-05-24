@extends('layouts.dashboard-new')

@section('title', 'Yangi xodim qo\'shish')
@section('page-title', 'Yangi xodim qo\'shish')

@section('styles')
<style>
    :root {
        --primary-green: #0d4f3c;
        --secondary-green: #16a085;
        --light-green: #e8f5f0;
        --accent-green: #48c9b0;
    }
    .form-section {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        border: 1px solid #e5e7eb;
        overflow: hidden;
        transition: all 0.3s;
    }
    .form-section:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .section-header {
        background: linear-gradient(135deg, var(--light-green) 0%, #f0fdf4 100%);
        padding: 12px 16px;
        border-bottom: 1px solid #d1fae5;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .section-header i {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        font-size: 14px;
    }
    .section-body {
        padding: 16px;
    }
    .form-label {
        font-size: 13px;
        font-weight: 500;
        color: #374151;
        margin-bottom: 4px;
        display: block;
    }
    .form-label .required {
        color: #ef4444;
        margin-left: 2px;
    }
    .form-input {
        width: 100%;
        padding: 10px 12px;
        font-size: 14px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        transition: all 0.2s;
        background: #fafafa;
        color: #333;
    }
    .form-input option {
        color: #333;
        background: #fff;
    }
    .form-input:focus {
        outline: none;
        border-color: var(--secondary-green);
        box-shadow: 0 0 0 3px rgba(22, 160, 133, 0.1);
        background: white;
    }
    .form-input::placeholder {
        color: #9ca3af;
    }
    .photo-upload-area {
        border: 2px dashed #d1d5db;
        border-radius: 12px;
        padding: 20px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s;
        background: #fafafa;
    }
    .photo-upload-area:hover {
        border-color: var(--secondary-green);
        background: var(--light-green);
    }
    .photo-upload-area.has-image {
        border-style: solid;
        border-color: var(--secondary-green);
    }
    .photo-preview {
        width: 120px;
        height: 150px;
        object-fit: cover;
        border-radius: 8px;
        margin: 0 auto;
        display: none;
    }
    .photo-preview.show {
        display: block;
    }
    .document-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px;
        background: #f9fafb;
        border-radius: 8px;
        border: 1px solid #e5e7eb;
        margin-bottom: 10px;
    }
    .document-item:last-child {
        margin-bottom: 0;
    }
    .document-icon {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        font-size: 16px;
    }
    .document-info {
        flex: 1;
    }
    .document-info .name {
        font-size: 13px;
        font-weight: 500;
        color: #374151;
    }
    .document-info .hint {
        font-size: 11px;
        color: #9ca3af;
    }
    .document-input {
        flex: 1;
    }
    .btn-submit {
        background: linear-gradient(135deg, var(--primary-green) 0%, var(--secondary-green) 100%);
        color: white;
        padding: 12px 32px;
        border-radius: 8px;
        font-weight: 500;
        font-size: 14px;
        border: none;
        cursor: pointer;
        transition: all 0.3s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .btn-submit:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(13, 79, 60, 0.3);
    }
    .btn-cancel {
        background: #f3f4f6;
        color: #6b7280;
        padding: 12px 24px;
        border-radius: 8px;
        font-weight: 500;
        font-size: 14px;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .btn-cancel:hover {
        background: #e5e7eb;
        color: #374151;
    }
    .collapsible-header {
        cursor: pointer;
        user-select: none;
    }
    .collapsible-header .toggle-icon {
        transition: transform 0.3s;
    }
    .collapsible-header.collapsed .toggle-icon {
        transform: rotate(-90deg);
    }
    .collapsible-content {
        max-height: 1000px;
        overflow: hidden;
        transition: max-height 0.3s ease-out;
    }
    .collapsible-content.collapsed {
        max-height: 0;
    }
    .input-group {
        display: flex;
        gap: 8px;
    }
    .input-group .form-input {
        flex: 1;
    }
    .input-prefix {
        padding: 10px 12px;
        background: #f3f4f6;
        border: 1px solid #d1d5db;
        border-radius: 8px 0 0 8px;
        color: #6b7280;
        font-size: 14px;
    }
    .input-group .form-input:first-of-type {
        border-radius: 0 8px 8px 0;
    }
</style>
@endsection

@section('content')
<div class="container-fluid px-2 md:px-4 pb-6">
    <!-- Header -->
    <div class="flex flex-wrap items-center justify-between gap-3 mb-4 p-4 rounded-xl"
         style="background: linear-gradient(135deg, var(--primary-green) 0%, var(--secondary-green) 100%);">
        <div class="flex items-center gap-3">
            <a href="{{ route('employees.index') }}"
               class="w-10 h-10 flex items-center justify-center rounded-lg bg-white/20 text-white hover:bg-white/30 transition">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-lg md:text-xl font-bold text-white">Yangi xodim qo'shish</h1>
                <p class="text-white/80 text-sm hidden md:block">Barcha majburiy maydonlarni to'ldiring</p>
            </div>
        </div>
        <div class="flex items-center gap-2 text-white/90 text-sm">
            <i class="fas fa-info-circle"></i>
            <span class="hidden md:inline"><span class="text-red-300">*</span> majburiy maydonlar</span>
        </div>
    </div>

    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-4 flex items-start gap-3">
            <i class="fas fa-exclamation-circle text-red-500 mt-0.5"></i>
            <div>
                <p class="font-medium mb-1">Xatoliklar topildi:</p>
                <ul class="list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <form action="{{ route('employees.store') }}" method="POST" enctype="multipart/form-data" id="employeeForm">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            <!-- Left Column - Photo & Basic Info -->
            <div class="lg:col-span-1 space-y-4">
                <!-- Photo Upload -->
                <div class="form-section">
                    <div class="section-header">
                        <i class="fas fa-camera bg-purple-100 text-purple-600"></i>
                        <div>
                            <h3 class="font-semibold text-gray-800 text-sm">Xodim rasmi</h3>
                            <p class="text-xs text-gray-500">3x4 formatda</p>
                        </div>
                    </div>
                    <div class="section-body">
                        <div class="photo-upload-area" id="photoUploadArea" onclick="document.getElementById('photoInput').click()">
                            <img id="photoPreview" class="photo-preview" alt="Preview">
                            <div id="photoPlaceholder">
                                <i class="fas fa-cloud-upload-alt text-3xl text-gray-400 mb-2"></i>
                                <p class="text-sm text-gray-500">Rasmni tanlash uchun bosing</p>
                                <p class="text-xs text-gray-400 mt-1">JPG, PNG (max 2MB)</p>
                            </div>
                        </div>
                        <input type="file" id="photoInput" name="photo" accept="image/*" class="hidden" onchange="previewPhoto(this)">
                    </div>
                </div>

                <!-- Quick Info -->
                <div class="form-section">
                    <div class="section-header">
                        <i class="fas fa-id-card bg-blue-100 text-blue-600"></i>
                        <h3 class="font-semibold text-gray-800 text-sm">Identifikatsiya</h3>
                    </div>
                    <div class="section-body space-y-3">
                        <div>
                            <label class="form-label">JSHSHIR (PINFL)</label>
                            <input type="text" name="jshshir" value="{{ old('jshshir') }}"
                                   class="form-input" placeholder="14141414141414" maxlength="14"
                                   oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                        </div>
                        <div class="grid grid-cols-5 gap-2">
                            <div class="col-span-2">
                                <label class="form-label">Pasport seriya</label>
                                <input type="text" name="passport_series" value="{{ old('passport_series') }}"
                                       class="form-input text-center uppercase" placeholder="AA" maxlength="2"
                                       oninput="this.value = this.value.toUpperCase().replace(/[^A-Z]/g, '')">
                            </div>
                            <div class="col-span-3">
                                <label class="form-label">Raqami</label>
                                <input type="text" name="passport_number" value="{{ old('passport_number') }}"
                                       class="form-input" placeholder="1234567" maxlength="7"
                                       oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Middle Column - Personal & Contact -->
            <div class="lg:col-span-1 space-y-4">
                <!-- Personal Info -->
                <div class="form-section">
                    <div class="section-header">
                        <i class="fas fa-user bg-green-100 text-green-600"></i>
                        <h3 class="font-semibold text-gray-800 text-sm">Shaxsiy ma'lumotlar</h3>
                    </div>
                    <div class="section-body space-y-3">
                        <div>
                            <label class="form-label">Familiya <span class="required">*</span></label>
                            <input type="text" name="last_name" value="{{ old('last_name') }}"
                                   class="form-input" placeholder="Familiya" required>
                        </div>
                        <div>
                            <label class="form-label">Ism <span class="required">*</span></label>
                            <input type="text" name="first_name" value="{{ old('first_name') }}"
                                   class="form-input" placeholder="Ism" required>
                        </div>
                        <div>
                            <label class="form-label">Sharif (otasining ismi)</label>
                            <input type="text" name="middle_name" value="{{ old('middle_name') }}"
                                   class="form-input" placeholder="Sharif">
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="form-label">Tug'ilgan sana <span class="required">*</span></label>
                                <input type="date" name="birth_date" value="{{ old('birth_date') }}"
                                       class="form-input" required>
                            </div>
                            <div>
                                <label class="form-label">Jins <span class="required">*</span></label>
                                <select name="gender" class="form-input" required>
                                    <option value="">Tanlang</option>
                                    <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Erkak</option>
                                    <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Ayol</option>
                                </select>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="form-label">Millati</label>
                                <select name="nationality_id" class="form-input">
                                    <option value="">Tanlang</option>
                                    @foreach($nationalities as $nationality)
                                        <option value="{{ $nationality->id }}" {{ old('nationality_id') == $nationality->id ? 'selected' : '' }}>
                                            {{ $nationality->name_uz }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="form-label">Fuqaroligi</label>
                                <select name="citizenship_id" class="form-input">
                                    <option value="">Tanlang</option>
                                    @foreach($citizenships as $citizenship)
                                        <option value="{{ $citizenship->id }}" {{ old('citizenship_id') == $citizenship->id ? 'selected' : '' }}>
                                            {{ $citizenship->name_uz }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Info -->
                <div class="form-section">
                    <div class="section-header">
                        <i class="fas fa-phone-alt bg-yellow-100 text-yellow-600"></i>
                        <h3 class="font-semibold text-gray-800 text-sm">Aloqa ma'lumotlari</h3>
                    </div>
                    <div class="section-body space-y-3">
                        <div>
                            <label class="form-label">Telefon raqami <span class="required">*</span></label>
                            <input type="tel" name="phone" value="{{ old('phone') }}"
                                   class="form-input" placeholder="+998 90 123 45 67" required>
                        </div>
                        <div>
                            <label class="form-label">Email <span class="required">*</span></label>
                            <input type="email" name="email" value="{{ old('email') }}"
                                   class="form-input" placeholder="email@example.com" required>
                        </div>
                        <div>
                            <label class="form-label">Doimiy manzil <span class="required">*</span></label>
                            <textarea name="address_permanent" rows="2" class="form-input" required
                                      placeholder="Viloyat, tuman, ko'cha, uy...">{{ old('address_permanent') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Work & Education -->
            <div class="lg:col-span-1 space-y-4">
                <!-- Employment Info -->
                <div class="form-section">
                    <div class="section-header">
                        <i class="fas fa-briefcase bg-orange-100 text-orange-600"></i>
                        <h3 class="font-semibold text-gray-800 text-sm">Ish ma'lumotlari</h3>
                    </div>
                    <div class="section-body space-y-3">
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="form-label">Xodim turi <span class="required">*</span></label>
                                <select name="employee_type" class="form-input" required>
                                    <option value="">Tanlang</option>
                                    <option value="teacher" {{ old('employee_type') == 'teacher' ? 'selected' : '' }}>O'qituvchi</option>
                                    <option value="admin" {{ old('employee_type') == 'admin' ? 'selected' : '' }}>Ma'muriy</option>
                                    <option value="support" {{ old('employee_type') == 'support' ? 'selected' : '' }}>Yordamchi</option>
                                </select>
                            </div>
                            <div>
                                <label class="form-label">Lavozim <span class="required">*</span></label>
                                <select name="position_id" class="form-input" required>
                                    <option value="">Tanlang</option>
                                    @foreach($positions as $position)
                                        <option value="{{ $position->id }}" {{ old('position_id') == $position->id ? 'selected' : '' }}>
                                            {{ $position->name_uz ?: ($position->description ?: $position->code) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="form-label">Fakultet</label>
                                <select name="faculty_id" class="form-input">
                                    <option value="">Tanlang</option>
                                    @foreach($faculties as $faculty)
                                        <option value="{{ $faculty->id }}" {{ old('faculty_id') == $faculty->id ? 'selected' : '' }}>
                                            {{ $faculty->name_uz }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="form-label">Kafedra/Bo'lim</label>
                                <select name="department_id" class="form-input">
                                    <option value="">Tanlang</option>
                                    @foreach($departments as $department)
                                        <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                            {{ $department->name_uz }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="form-label">Ish turi <span class="required">*</span></label>
                                <select name="employment_type" class="form-input" required>
                                    <option value="">Tanlang</option>
                                    <option value="asosiy" {{ old('employment_type') == 'asosiy' ? 'selected' : '' }}>Asosiy</option>
                                    <option value="qoshimcha" {{ old('employment_type') == 'qoshimcha' ? 'selected' : '' }}>Qo'shimcha</option>
                                </select>
                            </div>
                            <div>
                                <label class="form-label">Shartnoma turi <span class="required">*</span></label>
                                <select name="contract_type" class="form-input" required>
                                    <option value="">Tanlang</option>
                                    <option value="muddatsiz" {{ old('contract_type') == 'muddatsiz' ? 'selected' : '' }}>Muddatsiz</option>
                                    <option value="muddatli" {{ old('contract_type') == 'muddatli' ? 'selected' : '' }}>Muddatli</option>
                                </select>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="form-label">Stavka <span class="required">*</span></label>
                                <select name="stavka" class="form-input" required>
                                    <option value="">Tanlang</option>
                                    <option value="1" {{ old('stavka') == '1' ? 'selected' : '' }}>1.0</option>
                                    <option value="0.75" {{ old('stavka') == '0.75' ? 'selected' : '' }}>0.75</option>
                                    <option value="0.5" {{ old('stavka') == '0.5' ? 'selected' : '' }}>0.5</option>
                                    <option value="0.25" {{ old('stavka') == '0.25' ? 'selected' : '' }}>0.25</option>
                                </select>
                            </div>
                            <div>
                                <label class="form-label">Ishga qabul <span class="required">*</span></label>
                                <input type="date" name="hire_date" value="{{ old('hire_date', date('Y-m-d')) }}"
                                       class="form-input" required>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Education Info -->
                <div class="form-section">
                    <div class="section-header">
                        <i class="fas fa-graduation-cap bg-indigo-100 text-indigo-600"></i>
                        <h3 class="font-semibold text-gray-800 text-sm">Ta'lim va malaka</h3>
                    </div>
                    <div class="section-body space-y-3">
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="form-label">Ta'lim darajasi <span class="required">*</span></label>
                                <select name="education_level" class="form-input" required>
                                    <option value="">Tanlang</option>
                                    <option value="secondary" {{ old('education_level') == 'secondary' ? 'selected' : '' }}>O'rta</option>
                                    <option value="vocational" {{ old('education_level') == 'vocational' ? 'selected' : '' }}>O'rta-maxsus</option>
                                    <option value="bachelor" {{ old('education_level') == 'bachelor' ? 'selected' : '' }}>Bakalavr</option>
                                    <option value="master" {{ old('education_level') == 'master' ? 'selected' : '' }}>Magistr</option>
                                    <option value="phd" {{ old('education_level') == 'phd' ? 'selected' : '' }}>PhD</option>
                                    <option value="dsc" {{ old('education_level') == 'dsc' ? 'selected' : '' }}>DSc</option>
                                </select>
                            </div>
                            <div>
                                <label class="form-label">Ilmiy/Unvon</label>
                                <select name="academic_degree" class="form-input">
                                    <option value="">Yo'q</option>
                                    <option value="phd" {{ old('academic_degree') == 'phd' ? 'selected' : '' }}>PhD</option>
                                    <option value="dsc" {{ old('academic_degree') == 'dsc' ? 'selected' : '' }}>DSc</option>
                                    <option value="docent" {{ old('academic_degree') == 'docent' ? 'selected' : '' }}>Dotsent</option>
                                    <option value="professor" {{ old('academic_degree') == 'professor' ? 'selected' : '' }}>Professor</option>
                                </select>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="form-label">Mutaxassisligi</label>
                                <input type="text" name="specialization" value="{{ old('specialization') }}"
                                       class="form-input" placeholder="Yo'nalish nomi">
                            </div>
                            <div>
                                <label class="form-label">Ish tajribasi (yil)</label>
                                <input type="number" name="experience_years" value="{{ old('experience_years', 0) }}"
                                       class="form-input" min="0" max="60">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Documents Section (Full Width, Collapsible) -->
        <div class="form-section mt-4">
            <div class="section-header collapsible-header" onclick="toggleSection('documentsSection')">
                <i class="fas fa-folder-open bg-teal-100 text-teal-600"></i>
                <div class="flex-1">
                    <h3 class="font-semibold text-gray-800 text-sm">Hujjatlar yuklash</h3>
                    <p class="text-xs text-gray-500">Ixtiyoriy - kerakli hujjatlarni yuklang</p>
                </div>
                <i class="fas fa-chevron-down toggle-icon text-gray-400"></i>
            </div>
            <div class="collapsible-content" id="documentsSection">
                <div class="section-body">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <!-- Passport Copy -->
                        <div class="document-item">
                            <div class="document-icon bg-blue-100 text-blue-600">
                                <i class="fas fa-id-card"></i>
                            </div>
                            <div class="document-info">
                                <div class="name">Pasport nusxasi</div>
                                <div class="hint">PDF, JPG (max 5MB)</div>
                            </div>
                            <input type="file" name="doc_passport" accept=".pdf,.jpg,.jpeg,.png"
                                   class="form-input document-input text-sm">
                        </div>

                        <!-- Diploma -->
                        <div class="document-item">
                            <div class="document-icon bg-purple-100 text-purple-600">
                                <i class="fas fa-certificate"></i>
                            </div>
                            <div class="document-info">
                                <div class="name">Diplom nusxasi</div>
                                <div class="hint">PDF, JPG (max 5MB)</div>
                            </div>
                            <input type="file" name="doc_diploma" accept=".pdf,.jpg,.jpeg,.png"
                                   class="form-input document-input text-sm">
                        </div>

                        <!-- Work Book -->
                        <div class="document-item">
                            <div class="document-icon bg-orange-100 text-orange-600">
                                <i class="fas fa-book"></i>
                            </div>
                            <div class="document-info">
                                <div class="name">Mehnat daftarchasi</div>
                                <div class="hint">PDF, JPG (max 5MB)</div>
                            </div>
                            <input type="file" name="doc_workbook" accept=".pdf,.jpg,.jpeg,.png"
                                   class="form-input document-input text-sm">
                        </div>

                        <!-- Medical Certificate -->
                        <div class="document-item">
                            <div class="document-icon bg-red-100 text-red-600">
                                <i class="fas fa-heartbeat"></i>
                            </div>
                            <div class="document-info">
                                <div class="name">Tibbiy ma'lumotnoma</div>
                                <div class="hint">PDF, JPG (max 5MB)</div>
                            </div>
                            <input type="file" name="doc_medical" accept=".pdf,.jpg,.jpeg,.png"
                                   class="form-input document-input text-sm">
                        </div>

                        <!-- Contract -->
                        <div class="document-item">
                            <div class="document-icon bg-green-100 text-green-600">
                                <i class="fas fa-file-contract"></i>
                            </div>
                            <div class="document-info">
                                <div class="name">Shartnoma</div>
                                <div class="hint">PDF (max 5MB)</div>
                            </div>
                            <input type="file" name="doc_contract" accept=".pdf"
                                   class="form-input document-input text-sm">
                        </div>

                        <!-- Other Documents -->
                        <div class="document-item">
                            <div class="document-icon bg-gray-100 text-gray-600">
                                <i class="fas fa-file-alt"></i>
                            </div>
                            <div class="document-info">
                                <div class="name">Boshqa hujjatlar</div>
                                <div class="hint">PDF, JPG (max 5MB)</div>
                            </div>
                            <input type="file" name="doc_other" accept=".pdf,.jpg,.jpeg,.png"
                                   class="form-input document-input text-sm">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Notes Section -->
        <div class="form-section mt-4">
            <div class="section-header">
                <i class="fas fa-sticky-note bg-pink-100 text-pink-600"></i>
                <h3 class="font-semibold text-gray-800 text-sm">Qo'shimcha eslatma</h3>
            </div>
            <div class="section-body">
                <textarea name="notes" rows="2" class="form-input"
                          placeholder="Xodim haqida qo'shimcha ma'lumotlar...">{{ old('notes') }}</textarea>
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="flex flex-wrap justify-between items-center gap-3 mt-6 p-4 bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="text-sm text-gray-500">
                <i class="fas fa-info-circle mr-1"></i>
                Barcha <span class="text-red-500">*</span> belgilangan maydonlarni to'ldiring
            </div>
            <div class="flex gap-3">
                <a href="{{ route('employees.index') }}" class="btn-cancel">
                    <i class="fas fa-times"></i>
                    <span>Bekor qilish</span>
                </a>
                <button type="submit" class="btn-submit">
                    <i class="fas fa-check"></i>
                    <span>Saqlash</span>
                </button>
            </div>
        </div>
    </form>
</div>

<script>
function previewPhoto(input) {
    const preview = document.getElementById('photoPreview');
    const placeholder = document.getElementById('photoPlaceholder');
    const uploadArea = document.getElementById('photoUploadArea');

    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.classList.add('show');
            placeholder.style.display = 'none';
            uploadArea.classList.add('has-image');
        }
        reader.readAsDataURL(input.files[0]);
    }
}

function toggleSection(sectionId) {
    const section = document.getElementById(sectionId);
    const header = section.previousElementSibling;

    section.classList.toggle('collapsed');
    header.classList.toggle('collapsed');
}

// Phone number formatting
document.querySelector('input[name="phone"]').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    if (value.startsWith('998')) {
        value = value.substring(3);
    }
    if (value.length > 0) {
        value = '+998 ' + value.substring(0, 2) + ' ' + value.substring(2, 5) + ' ' + value.substring(5, 7) + ' ' + value.substring(7, 9);
    }
    e.target.value = value.trim();
});
</script>
@endsection
