@extends('layouts.dashboard-new')

@section('title', 'Xodimni tahrirlash - ' . $employee->full_name)
@section('page-title', 'Xodimni tahrirlash')

@section('styles')
<style>
    :root {
        --primary-green: #0d4f3c;
        --secondary-green: #16a085;
        --light-green: #e8f5f0;
        --accent-green: #48c9b0;
    }
    .rotate-180 {
        transform: rotate(180deg);
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
    .form-input:disabled, .form-input[readonly] {
        background: #f3f4f6;
        color: #6b7280;
        cursor: not-allowed;
    }
    .photo-container {
        display: flex;
        align-items: center;
        gap: 16px;
    }
    .current-photo {
        width: 100px;
        height: 125px;
        object-fit: cover;
        border-radius: 8px;
        border: 2px solid var(--secondary-green);
    }
    .photo-placeholder {
        width: 100px;
        height: 125px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f3f4f6;
        border-radius: 8px;
        border: 2px dashed #d1d5db;
        color: #9ca3af;
    }
    .photo-upload-info {
        flex: 1;
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
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
    }
    .status-active { background: #d1fae5; color: #065f46; }
    .status-leave { background: #fef3c7; color: #92400e; }
    .status-inactive { background: #f3f4f6; color: #6b7280; }
    .status-terminated { background: #fee2e2; color: #991b1b; }
    .employee-code {
        background: linear-gradient(135deg, var(--light-green) 0%, #d1fae5 100%);
        padding: 8px 16px;
        border-radius: 8px;
        font-family: monospace;
        font-size: 16px;
        font-weight: 600;
        color: var(--primary-green);
        letter-spacing: 1px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid px-2 md:px-4 pb-6">
    <!-- Header -->
    <div class="flex flex-wrap items-center justify-between gap-3 mb-4 p-4 rounded-xl"
         style="background: linear-gradient(135deg, var(--primary-green) 0%, var(--secondary-green) 100%);">
        <div class="flex items-center gap-3">
            <a href="{{ route('employees.show', $employee) }}"
               class="w-10 h-10 flex items-center justify-center rounded-lg bg-white/20 text-white hover:bg-white/30 transition">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-lg md:text-xl font-bold text-white">{{ $employee->full_name }}</h1>
                <div class="flex items-center gap-3 mt-1">
                    <span class="employee-code bg-white/20 text-white px-3 py-1 rounded text-sm">
                        {{ $employee->employee_code }}
                    </span>
                    @if($employee->status == 'active')
                        <span class="status-badge status-active"><i class="fas fa-check-circle"></i> Faol</span>
                    @elseif($employee->status == 'leave')
                        <span class="status-badge status-leave"><i class="fas fa-clock"></i> Ta'tilda</span>
                    @elseif($employee->status == 'inactive')
                        <span class="status-badge status-inactive"><i class="fas fa-pause-circle"></i> Nofaol</span>
                    @else
                        <span class="status-badge status-terminated"><i class="fas fa-times-circle"></i> Bo'shagan</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="text-white/90 text-sm hidden md:block">
            <i class="fas fa-edit mr-1"></i> Tahrirlash rejimi
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

    <!-- Quick Actions Bar -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-4">
        <div class="flex flex-wrap items-center gap-3">
            <span class="text-sm font-medium text-gray-600"><i class="fas fa-bolt text-yellow-500 mr-2"></i>Tez harakatlar:</span>
            <button type="button" onclick="openPasswordSection()" class="inline-flex items-center gap-2 px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition text-sm font-medium">
                <i class="fas fa-key"></i> Parol o'zgartirish
            </button>
            <button type="button" onclick="document.getElementById('statusSelect').focus(); document.getElementById('statusSelect').scrollIntoView({behavior: 'smooth', block: 'center'})" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition text-sm font-medium">
                <i class="fas fa-user-cog"></i> Status o'zgartirish
            </button>
            <a href="{{ route('employees.show', $employee) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition text-sm font-medium">
                <i class="fas fa-eye"></i> Profilni ko'rish
            </a>
        </div>
    </div>

    <form action="{{ route('employees.update', $employee) }}" method="POST" enctype="multipart/form-data" id="employeeForm">
        @csrf
        @method('PUT')

        <!-- Password Change Section - NOW AT TOP -->
        <div class="form-section mb-4 border-2 border-red-200" id="passwordCard">
            <div class="section-header bg-red-50 cursor-pointer" onclick="toggleSection('passwordSection')">
                <i class="fas fa-key bg-red-100 text-red-600"></i>
                <div class="flex-1">
                    <h3 class="font-semibold text-gray-800 text-sm">Tizimga kirish paroli</h3>
                    <p class="text-xs text-gray-500">
                        @if($employee->user_id)
                            <span class="text-green-600"><i class="fas fa-check-circle mr-1"></i>Akkaunt mavjud</span>
                        @else
                            <span class="text-orange-600"><i class="fas fa-exclamation-circle mr-1"></i>Akkaunt yo'q - parol kiriting</span>
                        @endif
                    </p>
                </div>
                <i class="fas fa-chevron-down toggle-icon text-gray-400 transition-transform" id="passwordToggleIcon"></i>
            </div>
            <div class="section-body hidden" id="passwordSection">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Yangi parol</label>
                        <div class="flex gap-2">
                            <input type="text" name="password" id="passwordField" class="form-input flex-1" placeholder="Kamida 8 ta belgi">
                            <button type="button" onclick="generatePassword()" class="px-4 py-2 bg-gradient-to-r from-blue-500 to-purple-500 text-white rounded-lg hover:from-blue-600 hover:to-purple-600 transition font-medium" title="Avto parol yaratish">
                                <i class="fas fa-magic mr-1"></i> Avto
                            </button>
                        </div>
                        <p class="text-xs text-gray-400 mt-1">Bo'sh qoldiring agar o'zgartirmoqchi bo'lmasangiz</p>
                    </div>
                    <div>
                        <label class="form-label">Parolni tasdiqlash</label>
                        <input type="text" name="password_confirmation" id="passwordConfirmField" class="form-input" placeholder="Parolni qayta kiriting">
                    </div>
                </div>
                <div id="generatedPasswordInfo" class="mt-3 p-4 bg-green-50 border border-green-200 rounded-lg hidden">
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="text-green-700 text-sm"><i class="fas fa-check-circle mr-2"></i>Yangi parol yaratildi:</span>
                            <code class="ml-2 px-3 py-1 bg-white border rounded text-lg font-mono" id="generatedPasswordText"></code>
                        </div>
                        <button type="button" onclick="copyPassword()" class="px-3 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition">
                            <i class="fas fa-copy mr-1"></i> Nusxalash
                        </button>
                    </div>
                    <p class="text-xs text-green-600 mt-2"><i class="fas fa-info-circle mr-1"></i>Bu parolni xodimga yetkazing yoki nusxalab saqlang</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            <!-- Left Column - Photo & ID -->
            <div class="lg:col-span-1 space-y-4">
                <!-- Photo Section -->
                <div class="form-section">
                    <div class="section-header">
                        <i class="fas fa-camera bg-purple-100 text-purple-600"></i>
                        <div>
                            <h3 class="font-semibold text-gray-800 text-sm">Xodim rasmi</h3>
                            <p class="text-xs text-gray-500">3x4 formatda</p>
                        </div>
                    </div>
                    <div class="section-body">
                        <div class="photo-container">
                            @if($employee->photo_url)
                                <img src="{{ asset($employee->photo_url) }}" alt="{{ $employee->full_name }}" class="current-photo" id="photoPreview">
                            @else
                                <div class="photo-placeholder" id="photoPlaceholder">
                                    <i class="fas fa-user text-3xl"></i>
                                </div>
                                <img src="" alt="Preview" class="current-photo hidden" id="photoPreview">
                            @endif
                            <div class="photo-upload-info">
                                <p class="text-sm text-gray-600 mb-2">Yangi rasm yuklash</p>
                                <input type="file" name="photo" accept="image/*" id="photoInput"
                                       class="form-input text-sm" onchange="previewPhoto(this)">
                                <p class="text-xs text-gray-400 mt-1">JPG, PNG (max 2MB)</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Identification -->
                <div class="form-section">
                    <div class="section-header">
                        <i class="fas fa-id-card bg-blue-100 text-blue-600"></i>
                        <h3 class="font-semibold text-gray-800 text-sm">Identifikatsiya</h3>
                    </div>
                    <div class="section-body space-y-3">
                        <div>
                            <label class="form-label">Xodim kodi</label>
                            <input type="text" value="{{ $employee->employee_code }}"
                                   class="form-input text-center font-mono font-bold" readonly disabled>
                        </div>
                        <div>
                            <label class="form-label">JSHSHIR (PINFL) <span class="required">*</span></label>
                            <input type="text" name="jshshir" value="{{ old('jshshir', $employee->jshshir) }}"
                                   class="form-input" placeholder="14141414141414" maxlength="14" required
                                   oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                        </div>
                        <div class="grid grid-cols-5 gap-2">
                            <div class="col-span-2">
                                <label class="form-label">Pasport seriya <span class="required">*</span></label>
                                <input type="text" name="passport_series" value="{{ old('passport_series', $employee->passport_series) }}"
                                       class="form-input text-center uppercase" placeholder="AA" maxlength="2" required
                                       oninput="this.value = this.value.toUpperCase().replace(/[^A-Z]/g, '')">
                            </div>
                            <div class="col-span-3">
                                <label class="form-label">Raqami <span class="required">*</span></label>
                                <input type="text" name="passport_number" value="{{ old('passport_number', $employee->passport_number) }}"
                                       class="form-input" placeholder="1234567" maxlength="7" required
                                       oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="form-label">Berilgan sana</label>
                                <input type="date" name="passport_issued_date"
                                       value="{{ old('passport_issued_date', $employee->passport_issued_date?->format('Y-m-d')) }}"
                                       class="form-input">
                            </div>
                            <div>
                                <label class="form-label">Holat <span class="required">*</span></label>
                                <select name="status" id="statusSelect" class="form-input" required>
                                    <option value="active" {{ old('status', $employee->status) == 'active' ? 'selected' : '' }}>Faol</option>
                                    <option value="leave" {{ old('status', $employee->status) == 'leave' ? 'selected' : '' }}>Ta'tilda</option>
                                    <option value="inactive" {{ old('status', $employee->status) == 'inactive' ? 'selected' : '' }}>Nofaol</option>
                                    <option value="terminated" {{ old('status', $employee->status) == 'terminated' ? 'selected' : '' }}>Bo'shagan</option>
                                </select>
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
                            <input type="text" name="last_name" value="{{ old('last_name', $employee->last_name) }}"
                                   class="form-input" placeholder="Familiya" required>
                        </div>
                        <div>
                            <label class="form-label">Ism <span class="required">*</span></label>
                            <input type="text" name="first_name" value="{{ old('first_name', $employee->first_name) }}"
                                   class="form-input" placeholder="Ism" required>
                        </div>
                        <div>
                            <label class="form-label">Sharif (otasining ismi)</label>
                            <input type="text" name="middle_name" value="{{ old('middle_name', $employee->middle_name) }}"
                                   class="form-input" placeholder="Sharif">
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="form-label">Tug'ilgan sana <span class="required">*</span></label>
                                <input type="date" name="birth_date"
                                       value="{{ old('birth_date', $employee->birth_date?->format('Y-m-d')) }}"
                                       class="form-input" required>
                            </div>
                            <div>
                                <label class="form-label">Jins <span class="required">*</span></label>
                                <select name="gender" class="form-input" required>
                                    <option value="">Tanlang</option>
                                    <option value="male" {{ old('gender', $employee->gender) == 'male' ? 'selected' : '' }}>Erkak</option>
                                    <option value="female" {{ old('gender', $employee->gender) == 'female' ? 'selected' : '' }}>Ayol</option>
                                </select>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="form-label">Millati</label>
                                <select name="nationality_id" class="form-input">
                                    <option value="">Tanlang</option>
                                    @foreach($nationalities ?? [] as $nationality)
                                        <option value="{{ $nationality->id }}" {{ old('nationality_id', $employee->nationality_id) == $nationality->id ? 'selected' : '' }}>
                                            {{ $nationality->name_uz }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="form-label">Fuqaroligi</label>
                                <select name="citizenship_id" class="form-input">
                                    <option value="">Tanlang</option>
                                    @foreach($citizenships ?? [] as $citizenship)
                                        <option value="{{ $citizenship->id }}" {{ old('citizenship_id', $employee->citizenship_id) == $citizenship->id ? 'selected' : '' }}>
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
                            <input type="tel" name="phone" value="{{ old('phone', $employee->phone) }}"
                                   class="form-input" placeholder="+998 90 123 45 67" required>
                        </div>
                        <div>
                            <label class="form-label">Email <span class="required">*</span></label>
                            <input type="email" name="email" value="{{ old('email', $employee->email) }}"
                                   class="form-input" placeholder="email@example.com" required>
                        </div>
                        <div>
                            <label class="form-label">Telegram</label>
                            <input type="text" name="telegram" value="{{ old('telegram', $employee->telegram) }}"
                                   class="form-input" placeholder="@username">
                        </div>
                        <div>
                            <label class="form-label">Yashash manzili <span class="required">*</span></label>
                            <textarea name="address_permanent" rows="2" class="form-input" required
                                      placeholder="Viloyat, tuman, ko'cha, uy...">{{ old('address_permanent', $employee->address_permanent) }}</textarea>
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
                                    <option value="teacher" {{ old('employee_type', $employee->employee_type) == 'teacher' ? 'selected' : '' }}>O'qituvchi</option>
                                    <option value="admin" {{ old('employee_type', $employee->employee_type) == 'admin' ? 'selected' : '' }}>Ma'muriy</option>
                                    <option value="support" {{ old('employee_type', $employee->employee_type) == 'support' ? 'selected' : '' }}>Yordamchi</option>
                                </select>
                            </div>
                            <div>
                                <label class="form-label">Lavozim <span class="required">*</span></label>
                                <select name="position_id" class="form-input" required>
                                    <option value="">Tanlang</option>
                                    @foreach($positions ?? [] as $position)
                                        <option value="{{ $position->id }}" {{ old('position_id', $employee->employmentDetail?->position_id) == $position->id ? 'selected' : '' }}>
                                            {{ $position->name_uz ?? $position->name }}
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
                                    @foreach($faculties ?? [] as $faculty)
                                        <option value="{{ $faculty->id }}" {{ old('faculty_id', $employee->employmentDetail?->faculty_id) == $faculty->id ? 'selected' : '' }}>
                                            {{ $faculty->name_uz }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="form-label">Kafedra/Bo'lim</label>
                                <select name="department_id" class="form-input">
                                    <option value="">Tanlang</option>
                                    @foreach($departments ?? [] as $department)
                                        <option value="{{ $department->id }}" {{ old('department_id', $employee->employmentDetail?->department_id) == $department->id ? 'selected' : '' }}>
                                            {{ $department->name_uz }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="grid grid-cols-3 gap-3">
                            <div>
                                <label class="form-label">Stavka <span class="required">*</span></label>
                                <select name="stavka" class="form-input" required>
                                    <option value="">-</option>
                                    <option value="1" {{ old('stavka', $employee->employmentDetail?->stavka) == '1' ? 'selected' : '' }}>1.0</option>
                                    <option value="0.75" {{ old('stavka', $employee->employmentDetail?->stavka) == '0.75' ? 'selected' : '' }}>0.75</option>
                                    <option value="0.5" {{ old('stavka', $employee->employmentDetail?->stavka) == '0.5' ? 'selected' : '' }}>0.5</option>
                                    <option value="0.25" {{ old('stavka', $employee->employmentDetail?->stavka) == '0.25' ? 'selected' : '' }}>0.25</option>
                                </select>
                            </div>
                            <div>
                                <label class="form-label">Shartnoma <span class="required">*</span></label>
                                <select name="contract_type" class="form-input" required>
                                    <option value="">-</option>
                                    <option value="muddatsiz" {{ old('contract_type', $employee->employmentDetail?->contract_type) == 'muddatsiz' ? 'selected' : '' }}>Muddatsiz</option>
                                    <option value="muddatli" {{ old('contract_type', $employee->employmentDetail?->contract_type) == 'muddatli' ? 'selected' : '' }}>Muddatli</option>
                                </select>
                            </div>
                            <div>
                                <label class="form-label">Ishga qabul <span class="required">*</span></label>
                                <input type="date" name="hire_date"
                                       value="{{ old('hire_date', $employee->employmentDetail?->hire_date?->format('Y-m-d') ?? $employee->hire_date?->format('Y-m-d')) }}"
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
                                    <option value="secondary" {{ old('education_level', $employee->education_level) == 'secondary' ? 'selected' : '' }}>O'rta</option>
                                    <option value="vocational" {{ old('education_level', $employee->education_level) == 'vocational' ? 'selected' : '' }}>O'rta-maxsus</option>
                                    <option value="bachelor" {{ old('education_level', $employee->education_level) == 'bachelor' ? 'selected' : '' }}>Bakalavr</option>
                                    <option value="master" {{ old('education_level', $employee->education_level) == 'master' ? 'selected' : '' }}>Magistr</option>
                                    <option value="phd" {{ old('education_level', $employee->education_level) == 'phd' ? 'selected' : '' }}>PhD</option>
                                    <option value="dsc" {{ old('education_level', $employee->education_level) == 'dsc' ? 'selected' : '' }}>DSc</option>
                                </select>
                            </div>
                            <div>
                                <label class="form-label">Ilmiy/Unvon</label>
                                <select name="academic_degree" class="form-input">
                                    <option value="">Yo'q</option>
                                    <option value="phd" {{ old('academic_degree', $employee->academic_degree) == 'phd' ? 'selected' : '' }}>PhD</option>
                                    <option value="dsc" {{ old('academic_degree', $employee->academic_degree) == 'dsc' ? 'selected' : '' }}>DSc</option>
                                    <option value="docent" {{ old('academic_degree', $employee->academic_degree) == 'docent' ? 'selected' : '' }}>Dotsent</option>
                                    <option value="professor" {{ old('academic_degree', $employee->academic_degree) == 'professor' ? 'selected' : '' }}>Professor</option>
                                </select>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="form-label">Mutaxassisligi</label>
                                <input type="text" name="specialization" value="{{ old('specialization', $employee->specialization) }}"
                                       class="form-input" placeholder="Yo'nalish nomi">
                            </div>
                            <div>
                                <label class="form-label">Ish tajribasi (yil)</label>
                                <input type="number" name="experience_years" value="{{ old('experience_years', $employee->experience_years ?? 0) }}"
                                       class="form-input" min="0" max="60">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Documents Section (Collapsible) -->
        <div class="form-section mt-4">
            <div class="section-header collapsible-header collapsed" onclick="toggleSection('documentsSection')">
                <i class="fas fa-folder-open bg-teal-100 text-teal-600"></i>
                <div class="flex-1">
                    <h3 class="font-semibold text-gray-800 text-sm">Hujjatlar yuklash</h3>
                    <p class="text-xs text-gray-500">Ixtiyoriy - yangi hujjatlarni yuklang</p>
                </div>
                <i class="fas fa-chevron-down toggle-icon text-gray-400"></i>
            </div>
            <div class="collapsible-content collapsed" id="documentsSection">
                <div class="section-body">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
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
                          placeholder="Xodim haqida qo'shimcha ma'lumotlar...">{{ old('notes', $employee->notes) }}</textarea>
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="flex flex-wrap justify-between items-center gap-3 mt-6 p-4 bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="text-sm text-gray-500">
                <i class="fas fa-clock mr-1"></i>
                Oxirgi o'zgarish: {{ $employee->updated_at?->format('d.m.Y H:i') ?? 'Noma\'lum' }}
            </div>
            <div class="flex gap-3">
                <a href="{{ route('employees.show', $employee) }}" class="btn-cancel">
                    <i class="fas fa-times"></i>
                    <span>Bekor qilish</span>
                </a>
                <button type="submit" class="btn-submit">
                    <i class="fas fa-save"></i>
                    <span>O'zgarishlarni saqlash</span>
                </button>
            </div>
        </div>
    </form>
</div>

<script>
function previewPhoto(input) {
    const preview = document.getElementById('photoPreview');
    const placeholder = document.getElementById('photoPlaceholder');

    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.classList.remove('hidden');
            if (placeholder) {
                placeholder.classList.add('hidden');
            }
        }
        reader.readAsDataURL(input.files[0]);
    }
}

function toggleSection(sectionId) {
    const section = document.getElementById(sectionId);

    if (sectionId === 'passwordSection') {
        // Special handling for password section
        section.classList.toggle('hidden');
        const icon = document.getElementById('passwordToggleIcon');
        if (icon) {
            icon.classList.toggle('rotate-180');
        }
    } else {
        // Old behavior for other sections
        const header = section.previousElementSibling;
        section.classList.toggle('collapsed');
        header.classList.toggle('collapsed');
    }
}

function openPasswordSection() {
    const section = document.getElementById('passwordSection');
    const card = document.getElementById('passwordCard');

    // Open the section
    section.classList.remove('hidden');

    // Rotate icon
    const icon = document.getElementById('passwordToggleIcon');
    if (icon) {
        icon.classList.add('rotate-180');
    }

    // Scroll to it
    card.scrollIntoView({ behavior: 'smooth', block: 'start' });

    // Focus on password field
    setTimeout(() => {
        document.getElementById('passwordField').focus();
    }, 300);
}

// Phone number formatting
const phoneInput = document.querySelector('input[name="phone"]');
if (phoneInput) {
    phoneInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.startsWith('998')) {
            value = value.substring(3);
        }
        if (value.length > 0) {
            value = '+998 ' + value.substring(0, 2) + ' ' + value.substring(2, 5) + ' ' + value.substring(5, 7) + ' ' + value.substring(7, 9);
        }
        e.target.value = value.trim();
    });
}

// Password generation functions
function generatePassword() {
    const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789!@#$%';
    let password = '';
    for (let i = 0; i < 10; i++) {
        password += chars.charAt(Math.floor(Math.random() * chars.length));
    }

    document.getElementById('passwordField').value = password;
    document.getElementById('passwordConfirmField').value = password;
    document.getElementById('generatedPasswordText').textContent = password;
    document.getElementById('generatedPasswordInfo').classList.remove('hidden');

    // Open section if collapsed
    const section = document.getElementById('passwordSection');
    if (section.classList.contains('collapsed')) {
        toggleSection('passwordSection');
    }
}

function togglePasswordVisibility() {
    const passwordField = document.getElementById('passwordField');
    const confirmField = document.getElementById('passwordConfirmField');
    const icon = document.getElementById('toggleIcon');

    if (passwordField.type === 'password') {
        passwordField.type = 'text';
        confirmField.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        passwordField.type = 'password';
        confirmField.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

function copyPassword() {
    const password = document.getElementById('generatedPasswordText').textContent;
    navigator.clipboard.writeText(password).then(() => {
        // Show copied notification
        const btn = event.target.closest('button');
        const originalHTML = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-check"></i> Nusxalandi!';
        setTimeout(() => {
            btn.innerHTML = originalHTML;
        }, 2000);
    });
}
</script>
@endsection
