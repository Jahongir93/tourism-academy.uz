<!DOCTYPE html>
<html lang="en" id="app">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Tourism Academy Samarkand 2026 - Application Form</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Flag Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/6.11.1/css/flag-icons.min.css">

    <style>
        * {
            font-family: 'Inter', sans-serif;
        }

        :root {
            --primary: #0066CC;
            --primary-dark: #0052A3;
            --primary-light: #e6f0fa;
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            padding: 0;
            margin: 0;
        }

        .form-container {
            max-width: 1000px;
            margin: 20px auto;
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .section-block {
            background: #f8f9fa;
            padding: 30px;
            margin: 20px 0;
            border-radius: 12px;
            border-left: 4px solid var(--primary);
        }

        .section-title {
            color: var(--primary);
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            color: #344054;
        }

        .form-label .required {
            color: #ef4444;
            margin-left: 2px;
        }

        .form-input,
        .form-select {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 15px;
            transition: all 0.3s ease;
            background: white;
        }

        .form-input:focus,
        .form-select:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(0, 102, 204, 0.1);
        }

        .form-input.error,
        .form-select.error {
            border-color: #ef4444;
        }

        .error-message {
            color: #ef4444;
            font-size: 13px;
            margin-top: 4px;
        }

        .two-column {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        @media (max-width: 768px) {
            .two-column {
                grid-template-columns: 1fr;
            }
        }

        .file-upload-box {
            border: 2px dashed #d1d5db;
            border-radius: 8px;
            padding: 30px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            background: white;
        }

        .file-upload-box:hover {
            border-color: var(--primary);
            background: var(--primary-light);
        }

        .file-upload-box.has-file {
            border-color: var(--primary);
            border-style: solid;
            background: var(--primary-light);
        }

        .checkbox-item {
            display: flex;
            align-items: center;
            padding: 12px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            margin-bottom: 12px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .checkbox-item:hover {
            border-color: var(--primary);
            background: var(--primary-light);
        }

        .checkbox-item.checked {
            border-color: var(--primary);
            background: var(--primary-light);
        }

        .checkbox-item input[type="checkbox"] {
            width: 20px;
            height: 20px;
            margin-right: 12px;
            cursor: pointer;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 16px 48px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 16px;
            border: none;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(0, 102, 204, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 102, 204, 0.4);
        }

        .btn-primary:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        /* Top Navbar */
        .top-navbar {
            background: white;
            padding: 12px 0;
            border-bottom: 2px solid var(--primary);
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .navbar-content {
            max-width: 1000px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar-left {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .home-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            background: transparent;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            color: #374151;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.2s;
        }

        .home-btn:hover {
            border-color: var(--primary);
            color: var(--primary);
            background: var(--primary-light);
        }

        .navbar-right {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        /* Accessibility Button */
        .accessibility-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 14px;
            background: transparent;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            color: #374151;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.2s;
        }

        .accessibility-btn:hover {
            border-color: var(--primary);
            color: var(--primary);
            background: var(--primary-light);
        }

        .accessibility-btn i {
            font-size: 16px;
        }

        /* Language Switcher - Matching Main Header */
        .lang-switcher {
            display: flex;
            gap: 0;
            background: transparent;
        }

        .lang-btn {
            padding: 8px 14px;
            border: none;
            background: transparent;
            cursor: pointer;
            font-weight: 600;
            font-size: 13px;
            transition: all 0.2s;
            color: #6b7280;
            position: relative;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .lang-btn .fi {
            font-size: 20px;
            width: 24px;
            height: 18px;
            border-radius: 3px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.15);
        }

        .lang-btn::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 2px;
            background: var(--primary);
            transition: width 0.3s;
        }

        .lang-btn:hover {
            color: var(--primary);
        }

        .lang-btn.active {
            color: var(--primary);
        }

        .lang-btn.active::after {
            width: 80%;
        }

        .lang-divider {
            width: 1px;
            height: 20px;
            background: #e5e7eb;
        }

        /* Accessibility Modal */
        .accessibility-modal {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 2000;
        }

        .accessibility-modal.show {
            display: flex;
        }

        .accessibility-content {
            background: white;
            border-radius: 16px;
            padding: 30px;
            max-width: 500px;
            width: 90%;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        }

        .accessibility-content h3 {
            color: var(--primary);
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 20px;
        }

        .accessibility-option {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            margin-bottom: 12px;
        }

        .accessibility-option label {
            font-weight: 600;
            color: #374151;
        }

        .toggle-switch {
            position: relative;
            width: 50px;
            height: 26px;
            background: #e5e7eb;
            border-radius: 50px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .toggle-switch.active {
            background: var(--primary);
        }

        .toggle-switch::after {
            content: '';
            position: absolute;
            top: 3px;
            left: 3px;
            width: 20px;
            height: 20px;
            background: white;
            border-radius: 50%;
            transition: transform 0.3s;
        }

        .toggle-switch.active::after {
            transform: translateX(24px);
        }

        .close-modal-btn {
            margin-top: 20px;
            width: 100%;
            padding: 12px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
        }

        .close-modal-btn:hover {
            background: var(--primary-dark);
        }

        /* Accessibility Features */
        body.large-text {
            font-size: 18px;
        }

        body.large-text .form-label,
        body.large-text .form-input,
        body.large-text .form-select {
            font-size: 18px;
        }

        body.high-contrast {
            filter: contrast(1.5);
        }

        body.grayscale {
            filter: grayscale(100%);
        }

        /* Header Section */
        .header-section {
            background: white;
            padding: 50px 30px;
            text-align: center;
            border-bottom: 1px solid #e5e7eb;
        }

        .header-section h1 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 25px;
            line-height: 1.3;
            color: #0A1F44;
        }

        .header-section p {
            font-size: 15px;
            line-height: 1.8;
            max-width: 800px;
            margin: 0 auto 12px;
            color: #4b5563;
        }

        .logos-row {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 50px;
            padding: 40px 0 20px;
            flex-wrap: wrap;
        }

        .logos-row img {
            height: 65px;
            object-fit: contain;
            filter: grayscale(20%);
            transition: all 0.3s;
        }

        .logos-row img:hover {
            filter: grayscale(0%);
            transform: scale(1.05);
        }

        @media (max-width: 768px) {
            .navbar-content {
                flex-direction: column;
                gap: 12px;
            }

            .navbar-left {
                width: 100%;
                justify-content: space-between;
            }

            .header-section {
                padding: 30px 20px;
            }

            .header-section h1 {
                font-size: 1.5rem;
            }

            .logos-row {
                gap: 30px;
            }

            .logos-row img {
                height: 50px;
            }
        }
    </style>
</head>
<body x-data="applicationForm()">
    <!-- Top Navbar -->
    <nav class="top-navbar">
        <div class="navbar-content">
            <div class="navbar-left">
                <a href="{{ route('home') }}" class="home-btn">
                    <i class="fas fa-home"></i>
                    <span x-text="t.backToHome || 'Bosh sahifa'"></span>
                </a>
            </div>

            <div class="navbar-right">
                <!-- Accessibility Button -->
                <button class="accessibility-btn" @click="showAccessibilityModal = true">
                    <i class="fas fa-universal-access"></i>
                    <span x-text="t.accessibility || 'Imkoniyatlar'"></span>
                </button>

                <!-- Language Switcher -->
                <div class="lang-switcher">
                    <button @click="setLanguage('uz')" :class="{ 'active': currentLang === 'uz' }" class="lang-btn">
                        <span class="fi fi-uz"></span>
                        <span>UZ</span>
                    </button>
                    <div class="lang-divider"></div>
                    <button @click="setLanguage('en')" :class="{ 'active': currentLang === 'en' }" class="lang-btn">
                        <span class="fi fi-gb"></span>
                        <span>EN</span>
                    </button>
                    <div class="lang-divider"></div>
                    <button @click="setLanguage('ru')" :class="{ 'active': currentLang === 'ru' }" class="lang-btn">
                        <span class="fi fi-ru"></span>
                        <span>RU</span>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Accessibility Modal -->
    <div class="accessibility-modal" :class="{ 'show': showAccessibilityModal }" @click.self="showAccessibilityModal = false">
        <div class="accessibility-content">
            <h3 x-text="t.accessibilitySettings || 'Imkoniyatlar sozlamalari'"></h3>

            <div class="accessibility-option">
                <label x-text="t.largeText || 'Katta matn'"></label>
                <div class="toggle-switch" :class="{ 'active': largeText }" @click="toggleLargeText()"></div>
            </div>

            <div class="accessibility-option">
                <label x-text="t.highContrast || 'Yuqori kontrast'"></label>
                <div class="toggle-switch" :class="{ 'active': highContrast }" @click="toggleHighContrast()"></div>
            </div>

            <div class="accessibility-option">
                <label x-text="t.grayscale || 'Oq-qora rejim'"></label>
                <div class="toggle-switch" :class="{ 'active': grayscale }" @click="toggleGrayscale()"></div>
            </div>

            <button class="close-modal-btn" @click="showAccessibilityModal = false" x-text="t.close || 'Yopish'"></button>
        </div>
    </div>

    <div class="form-container">
        <!-- Header Section -->
        <div class="header-section">
            <h1 x-text="t.headerTitle"></h1>
            <p x-text="t.headerDesc1"></p>
            <p x-text="t.headerDesc2"></p>
            <p x-text="t.headerDesc3"></p>
            <p style="margin-top: 15px; font-weight: 600; color: var(--primary);" x-text="t.headerThankYou"></p>

            <!-- Logos -->
            <div class="logos-row">
                <img src="{{ asset('images/logo.png') }}" alt="Tourism Academy Samarkand">
            </div>
        </div>

        <form @submit.prevent="submitForm" style="padding: 30px;">
            <!-- Section 1: Personal Information -->
            <div class="section-block">
                <h2 class="section-title" x-text="t.personalInfo"></h2>

                <div class="two-column">
                    <div>
                        <label class="form-label">
                            <span x-text="t.firstName"></span> <span class="required">*</span>
                        </label>
                        <input type="text" x-model="formData.firstName" class="form-input" :class="{ 'error': errors.firstName }" required>
                        <p class="error-message" x-show="errors.firstName" x-text="errors.firstName"></p>
                    </div>

                    <div>
                        <label class="form-label">
                            <span x-text="t.lastName"></span> <span class="required">*</span>
                        </label>
                        <input type="text" x-model="formData.lastName" class="form-input" :class="{ 'error': errors.lastName }" required>
                        <p class="error-message" x-show="errors.lastName" x-text="errors.lastName"></p>
                    </div>
                </div>

                <div class="two-column" style="margin-top: 20px;">
                    <div>
                        <label class="form-label">
                            <span x-text="t.dateOfBirth"></span> <span class="required">*</span>
                        </label>
                        <input type="date" x-model="formData.dateOfBirth" class="form-input" :class="{ 'error': errors.dateOfBirth }" required>
                        <p class="error-message" x-show="errors.dateOfBirth" x-text="errors.dateOfBirth"></p>
                    </div>

                    <div>
                        <label class="form-label">
                            <span x-text="t.gender"></span> <span class="required">*</span>
                        </label>
                        <select x-model="formData.gender" class="form-select" :class="{ 'error': errors.gender }" required>
                            <option value="" x-text="t.selectGender"></option>
                            <option value="male" x-text="t.male"></option>
                            <option value="female" x-text="t.female"></option>
                            <option value="other" x-text="t.other"></option>
                        </select>
                        <p class="error-message" x-show="errors.gender" x-text="errors.gender"></p>
                    </div>
                </div>

                <div class="two-column" style="margin-top: 20px;">
                    <div>
                        <label class="form-label">
                            <span x-text="t.phoneNumber"></span> <span class="required">*</span>
                        </label>
                        <input type="tel" x-model="formData.phone" class="form-input" :class="{ 'error': errors.phone }" placeholder="+998" required>
                        <p class="error-message" x-show="errors.phone" x-text="errors.phone"></p>
                    </div>

                    <div>
                        <label class="form-label">
                            <span x-text="t.email"></span> <span class="required">*</span>
                        </label>
                        <input type="email" x-model="formData.email" class="form-input" :class="{ 'error': errors.email }" required>
                        <p class="error-message" x-show="errors.email" x-text="errors.email"></p>
                    </div>
                </div>

                <div style="margin-top: 20px;">
                    <label class="form-label">
                        <span x-text="t.nationality"></span> <span class="required">*</span>
                    </label>
                    <select x-model="formData.nationality" class="form-select" :class="{ 'error': errors.nationality }" required>
                        <option value="" x-text="t.selectCountry"></option>
                        <template x-for="country in countries" :key="country.code">
                            <option :value="country.code" x-text="country.name"></option>
                        </template>
                    </select>
                    <p class="error-message" x-show="errors.nationality" x-text="errors.nationality"></p>
                </div>
            </div>

            <!-- Section 2: Professional Information -->
            <div class="section-block">
                <h2 class="section-title" x-text="t.professionalInfo"></h2>

                <div class="two-column">
                    <div>
                        <label class="form-label">
                            <span x-text="t.mainOccupation"></span> <span class="required">*</span>
                        </label>
                        <select x-model="formData.occupation" class="form-select" :class="{ 'error': errors.occupation }" required>
                            <option value="" x-text="t.selectOccupation"></option>
                            <option value="tourism_faculty" x-text="t.tourismFaculty"></option>
                            <option value="hospitality_faculty" x-text="t.hospitalityFaculty"></option>
                            <option value="public_sector" x-text="t.publicSector"></option>
                            <option value="private_sector" x-text="t.privateSector"></option>
                            <option value="other" x-text="t.other"></option>
                        </select>
                        <p class="error-message" x-show="errors.occupation" x-text="errors.occupation"></p>
                    </div>

                    <div>
                        <label class="form-label" x-text="t.jobPosition"></label>
                        <input type="text" x-model="formData.jobPosition" class="form-input">
                    </div>
                </div>

                <div style="margin-top: 20px;">
                    <label class="form-label" x-text="t.institutionName"></label>
                    <input type="text" x-model="formData.institution" class="form-input">
                </div>

                <div class="two-column" style="margin-top: 20px;">
                    <div>
                        <label class="form-label">
                            <span x-text="t.yearsOfExperience"></span> <span class="required">*</span>
                        </label>
                        <select x-model="formData.experience" class="form-select" :class="{ 'error': errors.experience }" required>
                            <option value="" x-text="t.selectExperience"></option>
                            <option value="1-9" x-text="t.exp1to9"></option>
                            <option value="10+" x-text="t.expMore10"></option>
                        </select>
                        <p class="error-message" x-show="errors.experience" x-text="errors.experience"></p>
                    </div>

                    <div>
                        <label class="form-label">
                            <span x-text="t.employmentType"></span> <span class="required">*</span>
                        </label>
                        <select x-model="formData.employmentType" class="form-select" :class="{ 'error': errors.employmentType }" required>
                            <option value="" x-text="t.selectEmployment"></option>
                            <option value="full_time" x-text="t.fullTime"></option>
                            <option value="part_time" x-text="t.partTime"></option>
                            <option value="self_employed" x-text="t.selfEmployed"></option>
                            <option value="other" x-text="t.other"></option>
                        </select>
                        <p class="error-message" x-show="errors.employmentType" x-text="errors.employmentType"></p>
                    </div>
                </div>
            </div>

            <!-- Section 3: Education Information -->
            <div class="section-block">
                <h2 class="section-title" x-text="t.educationInfo"></h2>

                <div class="two-column">
                    <div>
                        <label class="form-label">
                            <span x-text="t.highestDegree"></span> <span class="required">*</span>
                        </label>
                        <select x-model="formData.degree" class="form-select" :class="{ 'error': errors.degree }" required>
                            <option value="" x-text="t.selectDegree"></option>
                            <option value="bachelors" x-text="t.bachelors"></option>
                            <option value="masters" x-text="t.masters"></option>
                            <option value="doctorate" x-text="t.doctorate"></option>
                            <option value="other" x-text="t.other"></option>
                        </select>
                        <p class="error-message" x-show="errors.degree" x-text="errors.degree"></p>
                    </div>

                    <div>
                        <label class="form-label">
                            <span x-text="t.yearOfGraduation"></span> <span class="required">*</span>
                        </label>
                        <input type="number" x-model="formData.graduationYear" class="form-input" :class="{ 'error': errors.graduationYear }" min="1950" :max="new Date().getFullYear()" required>
                        <p class="error-message" x-show="errors.graduationYear" x-text="errors.graduationYear"></p>
                    </div>
                </div>

                <div style="margin-top: 20px;">
                    <label class="form-label" x-text="t.lastDegreeInstitution"></label>
                    <input type="text" x-model="formData.lastDegreeInstitution" class="form-input">
                </div>

                <div style="margin-top: 20px;">
                    <label class="form-label" x-text="t.fieldOfStudy"></label>
                    <input type="text" x-model="formData.fieldOfStudy" class="form-input">
                </div>
            </div>

            <!-- Section 4: Documents -->
            <div class="section-block">
                <h2 class="section-title" x-text="t.documents"></h2>

                <div class="two-column">
                    <div>
                        <label class="form-label">
                            <span x-text="t.uploadCV"></span> <span class="required">*</span>
                        </label>
                        <div class="file-upload-box" :class="{ 'has-file': formData.cv }" @click="$refs.cvInput.click()">
                            <i class="fas fa-file-pdf text-3xl mb-2" :class="formData.cv ? 'text-blue-600' : 'text-gray-400'"></i>
                            <p class="text-sm" x-text="formData.cv ? formData.cv.name : t.noFileChosen"></p>
                            <p class="text-xs text-gray-500 mt-2" x-text="t.cvFormat"></p>
                            <input type="file" x-ref="cvInput" @change="handleFileUpload($event, 'cv')" accept=".pdf,.doc,.docx" style="display: none;">
                        </div>
                        <p class="error-message" x-show="errors.cv" x-text="errors.cv"></p>
                    </div>

                    <div>
                        <label class="form-label">
                            <span x-text="t.uploadPassport"></span> <span class="required">*</span>
                        </label>
                        <div class="file-upload-box" :class="{ 'has-file': formData.passport }" @click="$refs.passportInput.click()">
                            <i class="fas fa-id-card text-3xl mb-2" :class="formData.passport ? 'text-blue-600' : 'text-gray-400'"></i>
                            <p class="text-sm" x-text="formData.passport ? formData.passport.name : t.noFileChosen"></p>
                            <p class="text-xs text-gray-500 mt-2" x-text="t.passportFormat"></p>
                            <input type="file" x-ref="passportInput" @change="handleFileUpload($event, 'passport')" accept=".jpg,.jpeg,.png,.pdf" style="display: none;">
                        </div>
                        <p class="error-message" x-show="errors.passport" x-text="errors.passport"></p>
                    </div>
                </div>
            </div>

            <!-- Section 5: Modules Selection -->
            <div class="section-block">
                <h2 class="section-title" x-text="t.modulesApplying"></h2>

                <template x-for="(module, index) in modules" :key="index">
                    <div class="checkbox-item" :class="{ 'checked': formData.selectedModules.includes(module.value) }">
                        <input type="checkbox" :value="module.value" x-model="formData.selectedModules" :id="'module-' + index">
                        <label :for="'module-' + index" style="cursor: pointer; flex: 1;" x-text="module[currentLang]"></label>
                    </div>
                </template>
                <p class="error-message" x-show="errors.selectedModules" x-text="errors.selectedModules"></p>
            </div>

            <!-- Section 6: Terms and Conditions -->
            <div class="section-block">
                <div class="checkbox-item" :class="{ 'checked': formData.termsAccepted }">
                    <input type="checkbox" x-model="formData.termsAccepted" id="terms" required>
                    <label for="terms" style="cursor: pointer; flex: 1;">
                        <span x-text="t.termsPrefix"></span>
                        <a href="https://docs.google.com/document/d/1XiCEe2lh4kx-5gdGdmYYFF1MnXYaG3xl4_tcppFsf-Y/edit?usp=sharing" target="_blank" class="text-blue-600 underline" x-text="t.termsLink"></a>
                        <span x-text="t.termsSuffix"></span>
                        <span class="required">*</span>
                    </label>
                </div>
                <p class="error-message" x-show="errors.termsAccepted" x-text="errors.termsAccepted"></p>
            </div>

            <!-- Submit Button -->
            <div style="text-align: center; margin-top: 40px;">
                <button type="submit" class="btn-primary" :disabled="isSubmitting">
                    <span x-show="!isSubmitting" x-text="t.applyNow"></span>
                    <span x-show="isSubmitting">
                        <i class="fas fa-spinner fa-spin mr-2"></i>
                        <span x-text="t.submitting"></span>
                    </span>
                </button>
            </div>
        </form>
    </div>

    <script>
        function applicationForm() {
            return {
                currentLang: 'en',
                isSubmitting: false,
                showAccessibilityModal: false,
                largeText: false,
                highContrast: false,
                grayscale: false,

                init() {
                    this.loadAccessibilitySettings();
                },

                formData: {
                    firstName: '',
                    lastName: '',
                    dateOfBirth: '',
                    gender: '',
                    phone: '',
                    email: '',
                    nationality: '',
                    occupation: '',
                    jobPosition: '',
                    institution: '',
                    experience: '',
                    employmentType: '',
                    degree: '',
                    graduationYear: '',
                    lastDegreeInstitution: '',
                    fieldOfStudy: '',
                    cv: null,
                    passport: null,
                    selectedModules: [],
                    termsAccepted: false
                },
                errors: {},

                translations: {
                    uz: {
                        backToHome: "Bosh sahifa",
                        accessibility: "Imkoniyatlar",
                        accessibilitySettings: "Imkoniyatlar sozlamalari",
                        largeText: "Katta matn",
                        highContrast: "Yuqori kontrast",
                        grayscale: "Oq-qora rejim",
                        close: "Yopish",
                        headerTitle: "Tourism Academy Samarkand 2026 – Ishtirokchilar uchun ariza shakli – MODUL(LAR) BO'YICHA ARIZA",
                        headerDesc1: "Tourism Academy Samarkand 2026 dasturiga qiziqish bildirganingizdan mamnunmiz.",
                        headerDesc2: "Ushbu ariza shakli Hospitality Management dasturi doirasida taklif etilayotgan modul(lar)ga ishtirok etish uchun mo'ljallangan.",
                        headerDesc3: "Ariza yakunida CV va shaxsni tasdiqlovchi hujjatni yuklash talab etiladi.",
                        headerThankYou: "Rahmat!",
                        personalInfo: "Shaxsiy Ma'lumotlar",
                        firstName: "Ism",
                        lastName: "Familiya",
                        dateOfBirth: "Tug'ilgan sana",
                        gender: "Jinsi",
                        selectGender: "Jinsni tanlang...",
                        male: "Erkak",
                        female: "Ayol",
                        other: "Boshqa",
                        phoneNumber: "Telefon raqami",
                        email: "Email",
                        nationality: "Millati/Fuqaroligi",
                        selectCountry: "Davlatni tanlang...",
                        professionalInfo: "Professional Ma'lumotlar",
                        mainOccupation: "Asosiy kasbi",
                        selectOccupation: "Kasbni tanlang...",
                        tourismFaculty: "Turizm fakulteti xodimi",
                        hospitalityFaculty: "Mehmondo'stlik fakulteti xodimi",
                        publicSector: "Davlat sektori",
                        privateSector: "Xususiy sektor",
                        jobPosition: "Lavozimi",
                        institutionName: "Muassasa nomi",
                        yearsOfExperience: "Ish tajribasi",
                        selectExperience: "Tajribani tanlang...",
                        exp1to9: "1-9 yil",
                        expMore10: "10 yildan ko'proq",
                        employmentType: "Bandlik turi",
                        selectEmployment: "Bandlik turini tanlang...",
                        fullTime: "To'liq stavka",
                        partTime: "Yarim stavka",
                        selfEmployed: "O'zini o'zi band qilgan",
                        educationInfo: "Ta'lim Ma'lumotlari",
                        highestDegree: "Eng yuqori daraja",
                        selectDegree: "Darajani tanlang...",
                        bachelors: "Bakalavr",
                        masters: "Magistr",
                        doctorate: "Doktorantura",
                        yearOfGraduation: "Bitirgan yili",
                        lastDegreeInstitution: "Oxirgi daraja va muassasa",
                        fieldOfStudy: "Ta'lim yo'nalishi",
                        documents: "Hujjatlar",
                        uploadCV: "CV yuklash",
                        uploadPassport: "Pasport/ID yuklash",
                        noFileChosen: "Fayl tanlanmagan",
                        cvFormat: "PDF, DOC, DOCX (max 2MB)",
                        passportFormat: "JPG, PNG, PDF (max 2MB)",
                        modulesApplying: "Ariza Topshirayotgan Modullar",
                        termsPrefix: "Men ",
                        termsLink: "Shartlar va Qoidalar",
                        termsSuffix: "ni o'qidim va roziman",
                        applyNow: "Ariza Yuborish",
                        submitting: "Yuborilmoqda...",
                    },
                    en: {
                        backToHome: "Home",
                        accessibility: "Accessibility",
                        accessibilitySettings: "Accessibility Settings",
                        largeText: "Large Text",
                        highContrast: "High Contrast",
                        grayscale: "Grayscale Mode",
                        close: "Close",
                        headerTitle: "Tourism Academy Samarkand 2026 – Application form for Participants – APPLICATION PER MODULE(S)",
                        headerDesc1: "Welcome to the Tourism Academy Samarkand 2026 program application.",
                        headerDesc2: "This application form is for participation in module(s) offered under the Hospitality Management Program.",
                        headerDesc3: "At the end of the application, you are required to upload your CV and proof of identity.",
                        headerThankYou: "Thank you!",
                        personalInfo: "Personal Information",
                        firstName: "First Name",
                        lastName: "Last Name",
                        dateOfBirth: "Date of Birth",
                        gender: "Gender",
                        selectGender: "Select Gender...",
                        male: "Male",
                        female: "Female",
                        other: "Other",
                        phoneNumber: "Phone Number",
                        email: "Email",
                        nationality: "Nationality",
                        selectCountry: "Select Country...",
                        professionalInfo: "Professional Information",
                        mainOccupation: "Main Occupation",
                        selectOccupation: "Select Occupation...",
                        tourismFaculty: "Tourism Faculty Member",
                        hospitalityFaculty: "Hospitality Faculty Member",
                        publicSector: "Public Sector",
                        privateSector: "Private Sector",
                        jobPosition: "Job Position",
                        institutionName: "Institution Name",
                        yearsOfExperience: "Years of Experience",
                        selectExperience: "Select Experience...",
                        exp1to9: "1-9 years",
                        expMore10: "More than 10 years",
                        employmentType: "Employment Type",
                        selectEmployment: "Select Employment...",
                        fullTime: "Full-time",
                        partTime: "Part-time",
                        selfEmployed: "Self-employed",
                        educationInfo: "Education Information",
                        highestDegree: "Highest Degree Obtained",
                        selectDegree: "Select Degree...",
                        bachelors: "Bachelor's",
                        masters: "Master's",
                        doctorate: "Doctorate",
                        yearOfGraduation: "Year of Graduation",
                        lastDegreeInstitution: "Last Degree and Institution",
                        fieldOfStudy: "Field of Study",
                        documents: "Documents",
                        uploadCV: "Upload CV",
                        uploadPassport: "Upload Passport/ID",
                        noFileChosen: "No file chosen",
                        cvFormat: "PDF, DOC, DOCX (max 2MB)",
                        passportFormat: "JPG, PNG, PDF (max 2MB)",
                        modulesApplying: "Modules You Are Applying To",
                        termsPrefix: "I have read and agree to the ",
                        termsLink: "Terms and Conditions",
                        termsSuffix: " of this programme",
                        applyNow: "Apply Now",
                        submitting: "Submitting...",
                    },
                    ru: {
                        backToHome: "Главная",
                        accessibility: "Доступность",
                        accessibilitySettings: "Настройки доступности",
                        largeText: "Крупный текст",
                        highContrast: "Высокая контрастность",
                        grayscale: "Черно-белый режим",
                        close: "Закрыть",
                        headerTitle: "Tourism Academy Samarkand 2026 – Форма заявки для участников – ЗАЯВКА ПО МОДУЛЮ(АМ)",
                        headerDesc1: "Добро пожаловать в программу Tourism Academy Samarkand 2026.",
                        headerDesc2: "Эта форма заявки предназначена для участия в модуле(ях), предлагаемых в рамках программы Hospitality Management.",
                        headerDesc3: "В конце заявки необходимо загрузить резюме и документ, удостоверяющий личность.",
                        headerThankYou: "Спасибо!",
                        personalInfo: "Личная Информация",
                        firstName: "Имя",
                        lastName: "Фамилия",
                        dateOfBirth: "Дата рождения",
                        gender: "Пол",
                        selectGender: "Выберите пол...",
                        male: "Мужской",
                        female: "Женский",
                        other: "Другое",
                        phoneNumber: "Номер телефона",
                        email: "Email",
                        nationality: "Национальность",
                        selectCountry: "Выберите страну...",
                        professionalInfo: "Профессиональная Информация",
                        mainOccupation: "Основная профессия",
                        selectOccupation: "Выберите профессию...",
                        tourismFaculty: "Преподаватель туризма",
                        hospitalityFaculty: "Преподаватель гостеприимства",
                        publicSector: "Государственный сектор",
                        privateSector: "Частный сектор",
                        jobPosition: "Должность",
                        institutionName: "Название учреждения",
                        yearsOfExperience: "Опыт работы",
                        selectExperience: "Выберите опыт...",
                        exp1to9: "1-9 лет",
                        expMore10: "Более 10 лет",
                        employmentType: "Тип занятости",
                        selectEmployment: "Выберите тип занятости...",
                        fullTime: "Полная занятость",
                        partTime: "Частичная занятость",
                        selfEmployed: "Самозанятый",
                        educationInfo: "Информация об Образовании",
                        highestDegree: "Высшая степень",
                        selectDegree: "Выберите степень...",
                        bachelors: "Бакалавр",
                        masters: "Магистр",
                        doctorate: "Докторантура",
                        yearOfGraduation: "Год окончания",
                        lastDegreeInstitution: "Последняя степень и учреждение",
                        fieldOfStudy: "Направление обучения",
                        documents: "Документы",
                        uploadCV: "Загрузить резюме",
                        uploadPassport: "Загрузить паспорт/ID",
                        noFileChosen: "Файл не выбран",
                        cvFormat: "PDF, DOC, DOCX (макс 2MB)",
                        passportFormat: "JPG, PNG, PDF (макс 2MB)",
                        modulesApplying: "Модули, на которые вы подаете заявку",
                        termsPrefix: "Я прочитал и согласен с ",
                        termsLink: "Условиями и Положениями",
                        termsSuffix: " этой программы",
                        applyNow: "Подать заявку",
                        submitting: "Отправка...",
                    }
                },

                countries: [
                    { code: 'UZ', name: 'Uzbekistan' },
                    { code: 'AF', name: 'Afghanistan' },
                    { code: 'AL', name: 'Albania' },
                    { code: 'DZ', name: 'Algeria' },
                    { code: 'AD', name: 'Andorra' },
                    { code: 'AO', name: 'Angola' },
                    { code: 'AR', name: 'Argentina' },
                    { code: 'AM', name: 'Armenia' },
                    { code: 'AU', name: 'Australia' },
                    { code: 'AT', name: 'Austria' },
                    { code: 'AZ', name: 'Azerbaijan' },
                    { code: 'BH', name: 'Bahrain' },
                    { code: 'BD', name: 'Bangladesh' },
                    { code: 'BY', name: 'Belarus' },
                    { code: 'BE', name: 'Belgium' },
                    { code: 'BT', name: 'Bhutan' },
                    { code: 'BO', name: 'Bolivia' },
                    { code: 'BA', name: 'Bosnia and Herzegovina' },
                    { code: 'BR', name: 'Brazil' },
                    { code: 'BG', name: 'Bulgaria' },
                    { code: 'KH', name: 'Cambodia' },
                    { code: 'CA', name: 'Canada' },
                    { code: 'CL', name: 'Chile' },
                    { code: 'CN', name: 'China' },
                    { code: 'CO', name: 'Colombia' },
                    { code: 'HR', name: 'Croatia' },
                    { code: 'CU', name: 'Cuba' },
                    { code: 'CY', name: 'Cyprus' },
                    { code: 'CZ', name: 'Czech Republic' },
                    { code: 'DK', name: 'Denmark' },
                    { code: 'EG', name: 'Egypt' },
                    { code: 'EE', name: 'Estonia' },
                    { code: 'ET', name: 'Ethiopia' },
                    { code: 'FI', name: 'Finland' },
                    { code: 'FR', name: 'France' },
                    { code: 'GE', name: 'Georgia' },
                    { code: 'DE', name: 'Germany' },
                    { code: 'GR', name: 'Greece' },
                    { code: 'HU', name: 'Hungary' },
                    { code: 'IS', name: 'Iceland' },
                    { code: 'IN', name: 'India' },
                    { code: 'ID', name: 'Indonesia' },
                    { code: 'IQ', name: 'Iraq' },
                    { code: 'IE', name: 'Ireland' },
                    { code: 'IL', name: 'Israel' },
                    { code: 'IT', name: 'Italy' },
                    { code: 'JP', name: 'Japan' },
                    { code: 'JO', name: 'Jordan' },
                    { code: 'KZ', name: 'Kazakhstan' },
                    { code: 'KE', name: 'Kenya' },
                    { code: 'KW', name: 'Kuwait' },
                    { code: 'KG', name: 'Kyrgyzstan' },
                    { code: 'LV', name: 'Latvia' },
                    { code: 'LB', name: 'Lebanon' },
                    { code: 'LT', name: 'Lithuania' },
                    { code: 'LU', name: 'Luxembourg' },
                    { code: 'MY', name: 'Malaysia' },
                    { code: 'MV', name: 'Maldives' },
                    { code: 'MX', name: 'Mexico' },
                    { code: 'MD', name: 'Moldova' },
                    { code: 'MN', name: 'Mongolia' },
                    { code: 'ME', name: 'Montenegro' },
                    { code: 'MA', name: 'Morocco' },
                    { code: 'NP', name: 'Nepal' },
                    { code: 'NL', name: 'Netherlands' },
                    { code: 'NZ', name: 'New Zealand' },
                    { code: 'NO', name: 'Norway' },
                    { code: 'OM', name: 'Oman' },
                    { code: 'PK', name: 'Pakistan' },
                    { code: 'PE', name: 'Peru' },
                    { code: 'PH', name: 'Philippines' },
                    { code: 'PL', name: 'Poland' },
                    { code: 'PT', name: 'Portugal' },
                    { code: 'QA', name: 'Qatar' },
                    { code: 'RO', name: 'Romania' },
                    { code: 'RU', name: 'Russia' },
                    { code: 'SA', name: 'Saudi Arabia' },
                    { code: 'RS', name: 'Serbia' },
                    { code: 'SG', name: 'Singapore' },
                    { code: 'SK', name: 'Slovakia' },
                    { code: 'SI', name: 'Slovenia' },
                    { code: 'ZA', name: 'South Africa' },
                    { code: 'KR', name: 'South Korea' },
                    { code: 'ES', name: 'Spain' },
                    { code: 'LK', name: 'Sri Lanka' },
                    { code: 'SE', name: 'Sweden' },
                    { code: 'CH', name: 'Switzerland' },
                    { code: 'TJ', name: 'Tajikistan' },
                    { code: 'TH', name: 'Thailand' },
                    { code: 'TN', name: 'Tunisia' },
                    { code: 'TR', name: 'Turkey' },
                    { code: 'TM', name: 'Turkmenistan' },
                    { code: 'UA', name: 'Ukraine' },
                    { code: 'AE', name: 'United Arab Emirates' },
                    { code: 'GB', name: 'United Kingdom' },
                    { code: 'US', name: 'United States' },
                    { code: 'VN', name: 'Vietnam' },
                    { code: 'YE', name: 'Yemen' }
                ],

                modules: [
                    {
                        value: 'housekeeping',
                        uz: 'Xo\'jalik xizmati va Gigiena standartlari',
                        en: 'Housekeeping & Hygiene Standards',
                        ru: 'Хозяйственная служба и Стандарты гигиены'
                    },
                    {
                        value: 'front_office',
                        uz: 'Front Office Professional xizmatlari',
                        en: 'Front Office Professional Services',
                        ru: 'Профессиональные услуги Front Office'
                    },
                    {
                        value: 'tourism_services',
                        uz: 'Chiqish turistik xizmatlarini tashkil etish va hamrohlik qilish',
                        en: 'Organization and Escorting of Outbound Tourist Services',
                        ru: 'Организация и сопровождение выездных туристических услуг'
                    },
                    {
                        value: 'service_marketing',
                        uz: 'Xizmatlar sohasida marketing: strategiya, savdo va o\'sish',
                        en: 'Marketing in Services: Strategy, Sales and Growth',
                        ru: 'Маркетинг в сфере услуг: стратегия, продажи и рост'
                    },
                    {
                        value: 'hotel_management',
                        uz: 'Mehmonxona operatsion boshqaruvi va xizmat sifati nazorati',
                        en: 'Hotel Operations Management and Service Quality Control',
                        ru: 'Операционное управление гостиницей и контроль качества обслуживания'
                    },
                    {
                        value: 'tour_operator',
                        uz: 'Turoperatorlik faoliyati texnologiyasi va uni tashkil etish',
                        en: 'Tour Operator Activity Technology and Organization',
                        ru: 'Технология и организация туроператорской деятельности'
                    },
                    {
                        value: 'guide_interpreter',
                        uz: 'Gid-tarjimon',
                        en: 'Guide-Interpreter',
                        ru: 'Гид-переводчик'
                    },
                    {
                        value: 'hospitality_management',
                        uz: 'Mehmondo\'stlik menejmenti (UN Tourism dasturi)',
                        en: 'Hospitality Management (UN Tourism program)',
                        ru: 'Управление гостеприимством (программа UN Tourism)'
                    },
                    {
                        value: 'hotel_operation_innovation',
                        uz: 'Mehmonxona operatsiyalari va innovatsiyalar (UN Tourism dasturi)',
                        en: 'Hotel Operation & Innovation (UN Tourism program)',
                        ru: 'Гостиничные операции и инновации (программа UN Tourism)'
                    }
                ],

                get t() {
                    return this.translations[this.currentLang];
                },

                setLanguage(lang) {
                    this.currentLang = lang;
                    document.documentElement.lang = lang;
                },

                toggleLargeText() {
                    this.largeText = !this.largeText;
                    if (this.largeText) {
                        document.body.classList.add('large-text');
                    } else {
                        document.body.classList.remove('large-text');
                    }
                    localStorage.setItem('largeText', this.largeText);
                },

                toggleHighContrast() {
                    this.highContrast = !this.highContrast;
                    if (this.highContrast) {
                        document.body.classList.add('high-contrast');
                    } else {
                        document.body.classList.remove('high-contrast');
                    }
                    localStorage.setItem('highContrast', this.highContrast);
                },

                toggleGrayscale() {
                    this.grayscale = !this.grayscale;
                    if (this.grayscale) {
                        document.body.classList.add('grayscale');
                    } else {
                        document.body.classList.remove('grayscale');
                    }
                    localStorage.setItem('grayscale', this.grayscale);
                },

                loadAccessibilitySettings() {
                    this.largeText = localStorage.getItem('largeText') === 'true';
                    this.highContrast = localStorage.getItem('highContrast') === 'true';
                    this.grayscale = localStorage.getItem('grayscale') === 'true';

                    if (this.largeText) document.body.classList.add('large-text');
                    if (this.highContrast) document.body.classList.add('high-contrast');
                    if (this.grayscale) document.body.classList.add('grayscale');
                },

                handleFileUpload(event, field) {
                    const file = event.target.files[0];
                    if (file) {
                        // Validate file size (2MB max)
                        if (file.size > 2 * 1024 * 1024) {
                            this.errors[field] = 'File size must be less than 2MB';
                            event.target.value = '';
                            return;
                        }
                        this.formData[field] = file;
                        delete this.errors[field];
                    }
                },

                validateForm() {
                    this.errors = {};
                    let isValid = true;

                    // Required fields validation
                    const requiredFields = [
                        'firstName', 'lastName', 'dateOfBirth', 'gender',
                        'phone', 'email', 'nationality', 'occupation',
                        'experience', 'employmentType', 'degree', 'graduationYear'
                    ];

                    requiredFields.forEach(field => {
                        if (!this.formData[field]) {
                            this.errors[field] = 'This field is required';
                            isValid = false;
                        }
                    });

                    // File uploads validation
                    if (!this.formData.cv) {
                        this.errors.cv = 'CV upload is required';
                        isValid = false;
                    }
                    if (!this.formData.passport) {
                        this.errors.passport = 'Passport/ID upload is required';
                        isValid = false;
                    }

                    // Modules validation
                    if (this.formData.selectedModules.length === 0) {
                        this.errors.selectedModules = 'Please select at least one module';
                        isValid = false;
                    }

                    // Terms validation
                    if (!this.formData.termsAccepted) {
                        this.errors.termsAccepted = 'You must accept the terms and conditions';
                        isValid = false;
                    }

                    return isValid;
                },

                async submitForm() {
                    if (!this.validateForm()) {
                        const errorMsg = this.currentLang === 'uz'
                            ? 'Iltimos, barcha majburiy maydonlarni to\'ldiring!'
                            : this.currentLang === 'ru'
                            ? 'Пожалуйста, заполните все обязательные поля!'
                            : 'Please fill in all required fields!';
                        alert(errorMsg);
                        return;
                    }

                    this.isSubmitting = true;

                    try {
                        const formDataToSend = new FormData();

                        // Append all form data
                        Object.keys(this.formData).forEach(key => {
                            if (key === 'selectedModules') {
                                formDataToSend.append(key, JSON.stringify(this.formData[key]));
                            } else if (this.formData[key] instanceof File) {
                                formDataToSend.append(key, this.formData[key]);
                            } else {
                                formDataToSend.append(key, this.formData[key]);
                            }
                        });

                        // Add CSRF token
                        formDataToSend.append('_token', document.querySelector('meta[name="csrf-token"]').content);

                        // Submit to server
                        const response = await fetch('{{ route("admission.store") }}', {
                            method: 'POST',
                            body: formDataToSend,
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            }
                        });

                        const result = await response.json();

                        if (result.success) {
                            // Redirect to success page directly (no alert)
                            window.location.href = '{{ route("admission.success", ["applicationNumber" => "__APP_NUM__"]) }}'.replace('__APP_NUM__', result.application_number);
                        } else {
                            // User-friendly error messages for duplicate email/phone
                            let errorTitle = '';
                            let errorMessage = result.message || '';

                            if (result.error_type === 'duplicate_email') {
                                errorTitle = this.currentLang === 'uz'
                                    ? '⚠️ Email allaqachon ro\'yxatdan o\'tgan!'
                                    : this.currentLang === 'ru'
                                    ? '⚠️ Email уже зарегистрирован!'
                                    : '⚠️ Email already registered!';
                                errorMessage = this.currentLang === 'uz'
                                    ? 'Bu email manzili bilan oldin ariza topshirilgan.\n\nIltimos, boshqa email manzilidan foydalaning yoki ariza holatini tekshiring.'
                                    : this.currentLang === 'ru'
                                    ? 'С этого email уже была подана заявка.\n\nПожалуйста, используйте другой email или проверьте статус вашей заявки.'
                                    : 'An application has already been submitted with this email.\n\nPlease use a different email or check your application status.';
                            } else if (result.error_type === 'duplicate_phone') {
                                errorTitle = this.currentLang === 'uz'
                                    ? '⚠️ Telefon raqami allaqachon ro\'yxatdan o\'tgan!'
                                    : this.currentLang === 'ru'
                                    ? '⚠️ Номер телефона уже зарегистрирован!'
                                    : '⚠️ Phone number already registered!';
                                errorMessage = this.currentLang === 'uz'
                                    ? 'Bu telefon raqami bilan oldin ariza topshirilgan.\n\nIltimos, boshqa telefon raqamidan foydalaning yoki ariza holatini tekshiring.'
                                    : this.currentLang === 'ru'
                                    ? 'С этого номера телефона уже была подана заявка.\n\nПожалуйста, используйте другой номер или проверьте статус вашей заявки.'
                                    : 'An application has already been submitted with this phone number.\n\nPlease use a different phone number or check your application status.';
                            } else {
                                errorTitle = this.currentLang === 'uz'
                                    ? 'Arizani yuborishda xatolik!'
                                    : this.currentLang === 'ru'
                                    ? 'Ошибка при отправке заявки!'
                                    : 'Error submitting application!';
                            }

                            alert(errorTitle + '\n\n' + errorMessage);
                        }
                    } catch (error) {
                        console.error('Submission error:', error);
                        const errorMsg = this.currentLang === 'uz'
                            ? 'Arizani yuborishda xatolik! Iltimos, qayta urinib ko\'ring.'
                            : this.currentLang === 'ru'
                            ? 'Ошибка при отправке! Пожалуйста, попробуйте еще раз.'
                            : 'Error submitting application. Please try again.';
                        alert(errorMsg);
                    } finally {
                        this.isSubmitting = false;
                    }
                }
            }
        }
    </script>
</body>
</html>
