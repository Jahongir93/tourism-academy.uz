<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Online Ariza Topshirish - Tourism Academy</title>

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

    <style>
        * {
            font-family: 'Inter', sans-serif;
        }

        :root {
            --primary: #0066CC;
            --primary-dark: #0052A3;
            --primary-light: #e6f0fa;
            --primary-lighter: #f0f7ff;
        }

        body {
            background: linear-gradient(135deg, var(--primary-lighter) 0%, var(--primary-light) 50%, #dbeafe 100%);
            min-height: 100vh;
        }

        /* Step Indicator Styles */
        .step-indicator.active .step-circle,
        .step-indicator.completed .step-circle {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
        }

        .step-indicator.active .step-title,
        .step-indicator.completed .step-title {
            color: var(--primary);
            font-weight: 600;
        }

        .step-indicator.completed .step-number {
            display: none;
        }

        .step-indicator.completed .step-check {
            display: block !important;
        }

        /* Form Input Styles */
        .form-input {
            width: 100%;
            padding: 0.875rem 1rem;
            border: 2px solid #e5e7eb;
            border-radius: 0.75rem;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: white;
        }

        .form-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(0, 102, 204, 0.1);
        }

        .form-input.error {
            border-color: #ef4444;
            animation: shake 0.5s ease-in-out;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            20%, 60% { transform: translateX(-5px); }
            40%, 80% { transform: translateX(5px); }
        }

        /* File Upload Styles */
        .file-upload-area {
            position: relative;
            border: 2px dashed #d1d5db;
            border-radius: 0.75rem;
            padding: 1.5rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            background: #f9fafb;
        }

        .file-upload-area:hover {
            border-color: var(--primary);
            background: var(--primary-lighter);
        }

        .file-upload-area.has-file {
            border-color: var(--primary);
            border-style: solid;
            background: var(--primary-lighter);
        }

        .file-upload-area input[type="file"] {
            position: absolute;
            inset: 0;
            opacity: 0;
            cursor: pointer;
        }

        /* Section Animation */
        .form-section {
            animation: fadeIn 0.4s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Select Styles */
        .form-select {
            appearance: none;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
            background-position: right 0.75rem center;
            background-repeat: no-repeat;
            background-size: 1.5em 1.5em;
            padding-right: 2.5rem;
        }

        /* Radio & Checkbox Styles */
        .custom-radio,
        .custom-checkbox {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            border: 2px solid #e5e7eb;
            border-radius: 0.5rem;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .custom-radio:hover,
        .custom-checkbox:hover {
            border-color: var(--primary);
            background: var(--primary-lighter);
        }

        .custom-radio.selected,
        .custom-checkbox.selected {
            border-color: var(--primary);
            background: var(--primary-lighter);
        }

        /* Submit Button Loading */
        #submitBtn.loading {
            pointer-events: none;
            opacity: 0.8;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Toast animation */
        .animate-fade-in {
            animation: fadeIn 0.3s ease-out;
        }
    </style>
</head>
<body>
    <!-- Hero Section -->
    <section class="relative py-12 overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-blue-600 via-blue-700 to-blue-800"></div>
        <div class="absolute inset-0 opacity-20">
            <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                <defs>
                    <pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse">
                        <path d="M 10 0 L 0 0 0 10" fill="none" stroke="white" stroke-width="0.5"/>
                    </pattern>
                </defs>
                <rect width="100%" height="100%" fill="url(#grid)"/>
            </svg>
        </div>
        <div class="relative z-10 max-w-4xl mx-auto text-center px-4">
            <a href="{{ url('/') }}" class="inline-flex items-center text-white/80 hover:text-white mb-4 transition">
                <i class="fas fa-arrow-left mr-2"></i> Bosh sahifaga qaytish
            </a>
            <h1 class="text-3xl md:text-4xl font-bold text-white mb-2">Tourism Academy Samarkand 2025</h1>
            <p class="text-xl text-blue-100">Application form for Participants</p>
            <p class="text-lg text-blue-200 mt-1">Application per Module(s)</p>
        </div>
    </section>

    <!-- Introduction Section -->
    <section class="py-8 bg-white">
        <div class="max-w-4xl mx-auto px-4">
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-2xl p-8 border border-blue-100">
                <!-- Logos -->
                <div class="flex items-center justify-center gap-8 mb-8">
                    <img src="{{ asset('images/logo.png') }}" alt="Tourism Academy Logo" class="h-16 md:h-20 object-contain">
                    <div class="w-px h-16 bg-gray-300"></div>
                    <img src="{{ asset('images/globallogo.jpg') }}" alt="UN Tourism Logo" class="h-16 md:h-20 object-contain">
                </div>

                <!-- Introduction Text -->
                <div class="text-gray-700 space-y-4 text-sm md:text-base leading-relaxed">
                    <p>Tourism Academy Samarkand 2025 dasturiga qiziqish bildirganingizdan mamnunmiz. Ushbu onlayn ariza shakli <strong>Hospitality Management Programme</strong> doirasida taklif etilayotgan alohida modul(lar) bo'yicha ishtirok etish uchun mo'ljallangan.</p>

                    <p>Mazkur dastur <strong>UN Tourism</strong> va <strong>UN Tourism Academy</strong> ko'magida, shuningdek <strong>Les Roches – Global Hospitality Education</strong> hamkorligida ishlab chiqilgan bo'lib, xalqaro darajadagi bilim va amaliy ko'nikmalarni taqdim etishga qaratilgan.</p>

                    <p>Nomzodlardan ushbu onlayn eFormni to'liq va aniq to'ldirish so'raladi. Ariza yakunida quyidagi hujjatlarni elektron shaklda yuklash talab etiladi:</p>

                    <ul class="list-disc list-inside ml-4 space-y-1">
                        <li>qisqacha tarjimai hol (CV),</li>
                        <li>shaxsni tasdiqlovchi hujjat (pasport yoki ID karta).</li>
                    </ul>

                    <p>Ariza har bir modul bo'yicha alohida topshiriladi. Siz qiziqqan bir yoki bir nechta modul(lar)ni tanlab, ularning har biri uchun mustaqil ariza yuborishingiz mumkin.</p>

                    <div class="bg-blue-100 rounded-xl p-4 mt-4">
                        <p class="flex items-start">
                            <i class="fas fa-info-circle text-blue-600 mr-2 mt-1"></i>
                            <span>Ushbu jarayon to'liq onlayn tarzda amalga oshiriladi va ariza topshirish uchun ofisga kelish talab etilmaydi. Arizangiz ko'rib chiqilgach, keyingi bosqichlar bo'yicha siz bilan bog'laniladi.</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-12">
        <div class="max-w-5xl mx-auto px-4">
            @if(session('error'))
                <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-lg mb-6 flex items-center">
                    <i class="fas fa-exclamation-circle text-2xl mr-3"></i>
                    <div>
                        <p class="font-semibold">Xatolik!</p>
                        <p>{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            <!-- Step Indicator -->
            <div class="bg-white rounded-2xl shadow-lg p-6 mb-8">
                <div class="flex items-center justify-between relative">
                    <!-- Progress Line -->
                    <div class="absolute top-6 left-0 right-0 h-1 bg-gray-200 mx-16 hidden md:block">
                        <div class="h-full bg-gradient-to-r from-blue-500 to-blue-600 transition-all duration-500" id="progressLine" style="width: 0%"></div>
                    </div>

                    @for($i = 1; $i <= $totalSteps; $i++)
                        <div class="step-indicator flex flex-col items-center relative z-10 {{ $i === 1 ? 'active' : '' }}" data-step="{{ $i }}">
                            <div class="step-circle w-12 h-12 rounded-full bg-gray-200 flex items-center justify-center text-gray-500 font-bold text-lg transition-all duration-300 border-4 border-white shadow-md">
                                <span class="step-number">{{ $i }}</span>
                                <i class="fas fa-check step-check hidden"></i>
                            </div>
                            <span class="step-title text-xs md:text-sm text-gray-500 mt-2 text-center font-medium max-w-[80px] md:max-w-none">
                                @if(isset($stepTitles[$i]))
                                    {{ Str::limit($stepTitles[$i], 15) }}
                                @else
                                    Bosqich {{ $i }}
                                @endif
                            </span>
                        </div>
                    @endfor
                </div>
            </div>

            <!-- Form -->
            <form action="{{ route('admission.store') }}" method="POST" enctype="multipart/form-data" id="admissionForm">
                @csrf

                @if($formFields->isEmpty())
                    <div class="bg-yellow-50 border-l-4 border-yellow-500 text-yellow-700 p-6 rounded-lg flex items-start">
                        <i class="fas fa-exclamation-triangle text-2xl mr-4 mt-1"></i>
                        <div>
                            <p class="font-bold text-lg">Forma sozlanmagan</p>
                            <p>Forma maydonlari hali sozlanmagan. Iltimos, administrator bilan bog'laning.</p>
                        </div>
                    </div>
                @else
                    @foreach($formFields as $step => $fields)
                        <div class="form-section {{ $loop->first ? 'active' : '' }}" id="section{{ $step }}" style="{{ !$loop->first ? 'display: none;' : '' }}">
                            <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-6">
                                <!-- Section Header -->
                                <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4">
                                    <h2 class="text-xl font-bold text-white flex items-center">
                                        @switch($step)
                                            @case(1)
                                                <i class="fas fa-user-circle mr-3 text-2xl"></i>
                                                @break
                                            @case(2)
                                                <i class="fas fa-graduation-cap mr-3 text-2xl"></i>
                                                @break
                                            @case(3)
                                                <i class="fas fa-university mr-3 text-2xl"></i>
                                                @break
                                            @case(4)
                                                <i class="fas fa-file-alt mr-3 text-2xl"></i>
                                                @break
                                            @default
                                                <i class="fas fa-list mr-3 text-2xl"></i>
                                        @endswitch
                                        {{ $stepTitles[$step] ?? 'Bosqich ' . $step }}
                                    </h2>
                                </div>

                                <!-- Section Content -->
                                <div class="p-6">
                                    <div class="grid md:grid-cols-2 gap-6">
                                        @foreach($fields as $field)
                                            @if($field->field_type === 'heading')
                                                <div class="md:col-span-2 mt-4 first:mt-0">
                                                    @include('admission.partials.field', ['field' => $field])
                                                </div>
                                            @elseif($field->field_type === 'textarea' || $field->field_key === 'address')
                                                <div class="md:col-span-2">
                                                    @include('admission.partials.field', ['field' => $field])
                                                </div>
                                            @else
                                                <div>
                                                    @include('admission.partials.field', ['field' => $field])
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>

                                    @if($step == $totalSteps)
                                        <!-- Agreement Section -->
                                        <div class="mt-8 p-5 bg-gradient-to-r from-amber-50 to-yellow-50 border border-amber-200 rounded-xl">
                                            <div class="flex items-start">
                                                <i class="fas fa-info-circle text-amber-500 text-xl mr-3 mt-0.5"></i>
                                                <div>
                                                    <p class="font-semibold text-amber-800 mb-2">Muhim eslatma</p>
                                                    <p class="text-amber-700 text-sm">Barcha hujjatlar aniq va o'qiladigan bo'lishi kerak. Noto'g'ri ma'lumotlar kiritilsa ariza rad etilishi mumkin.</p>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mt-6">
                                            <label class="flex items-start cursor-pointer group">
                                                <input type="checkbox" id="agreement" class="mt-1 w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500 transition" required>
                                                <span class="ml-3 text-gray-700 group-hover:text-gray-900 transition">
                                                    Men kiritilgan ma'lumotlarning to'g'riligini tasdiqlayman va
                                                    <a href="#" class="text-blue-600 hover:text-blue-700 underline">shaxsiy ma'lumotlarimning qayta ishlanishiga</a>
                                                    rozilik beraman.
                                                </span>
                                            </label>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Navigation Buttons -->
                            <div class="flex justify-between items-center">
                                @if($step > 1)
                                    <button type="button" onclick="prevSection()" class="inline-flex items-center px-6 py-3 bg-white border-2 border-gray-300 text-gray-700 rounded-xl font-semibold hover:bg-gray-50 hover:border-gray-400 transition group">
                                        <i class="fas fa-arrow-left mr-2 group-hover:-translate-x-1 transition-transform"></i> Orqaga
                                    </button>
                                @else
                                    <a href="{{ url('/') }}" class="inline-flex items-center px-6 py-3 bg-white border-2 border-gray-300 text-gray-700 rounded-xl font-semibold hover:bg-gray-50 hover:border-gray-400 transition">
                                        <i class="fas fa-home mr-2"></i> Bosh sahifa
                                    </a>
                                @endif

                                @if($step < $totalSteps)
                                    <button type="button" onclick="nextSection()" class="inline-flex items-center px-8 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl font-semibold hover:from-blue-600 hover:to-blue-700 transition shadow-lg hover:shadow-xl group">
                                        Keyingi <i class="fas fa-arrow-right ml-2 group-hover:translate-x-1 transition-transform"></i>
                                    </button>
                                @else
                                    <button type="submit" id="submitBtn" class="inline-flex items-center px-8 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl font-semibold hover:from-blue-600 hover:to-blue-700 transition shadow-lg hover:shadow-xl">
                                        <i class="fas fa-paper-plane mr-2"></i> Ariza yuborish
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endforeach
                @endif
            </form>

            <!-- Help Section -->
            @if($contactSettings['show_help_section'] ?? true)
            <div class="mt-12 bg-white rounded-2xl shadow-lg p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-question-circle text-blue-500 mr-2"></i>
                    Yordam kerakmi?
                </h3>
                <div class="grid md:grid-cols-3 gap-4">
                    @if(!empty($contactSettings['phone']))
                    <div class="flex items-center p-4 bg-blue-50 rounded-xl">
                        <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center text-white mr-4">
                            <i class="fas fa-phone"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Telefon</p>
                            <p class="font-semibold text-gray-800">{{ $contactSettings['phone'] }}</p>
                        </div>
                    </div>
                    @endif
                    @if(!empty($contactSettings['telegram']))
                    <div class="flex items-center p-4 bg-sky-50 rounded-xl">
                        <div class="w-12 h-12 bg-sky-500 rounded-full flex items-center justify-center text-white mr-4">
                            <i class="fab fa-telegram"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Telegram</p>
                            <p class="font-semibold text-gray-800">{{ $contactSettings['telegram'] }}</p>
                        </div>
                    </div>
                    @endif
                    @if(!empty($contactSettings['email']))
                    <div class="flex items-center p-4 bg-indigo-50 rounded-xl">
                        <div class="w-12 h-12 bg-indigo-500 rounded-full flex items-center justify-center text-white mr-4">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Email</p>
                            <p class="font-semibold text-gray-800">{{ $contactSettings['email'] }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Footer -->
            <div class="mt-8 text-center text-gray-500 text-sm pb-8">
                <p>&copy; {{ date('Y') }} Tourism Academy. Barcha huquqlar himoyalangan.</p>
            </div>
        </div>
    </section>

    <script>
        // Specialties data for dynamic filtering
        const specialties = @json($specialties ?? []);
        const totalSteps = {{ $totalSteps }};

        // Current section
        let currentSection = 1;

        // Initialize on DOM ready
        document.addEventListener('DOMContentLoaded', function() {
            initFacultySelect();
            initFileUploads();
            initInputFormatting();
            initFormValidation();
        });

        // Faculty select change handler
        function initFacultySelect() {
            const facultySelect = document.getElementById('facultySelect');
            if (!facultySelect) return;

            facultySelect.addEventListener('change', function() {
                const facultyId = this.value;
                const specialtySelect = document.getElementById('specialtySelect');

                if (!specialtySelect) return;

                // Clear and reset specialty select
                specialtySelect.innerHTML = '<option value="">Yo\'nalish tanlang...</option>';

                if (facultyId) {
                    const facultySpecialties = specialties.filter(s => s.faculty_id == facultyId);

                    if (facultySpecialties.length === 0) {
                        specialtySelect.innerHTML = '<option value="" disabled>Bu fakultet uchun yo\'nalishlar mavjud emas</option>';
                    } else {
                        facultySpecialties.forEach(specialty => {
                            const option = document.createElement('option');
                            option.value = specialty.id;
                            option.textContent = specialty.name_uz || specialty.name_ru || specialty.name_en;
                            specialtySelect.appendChild(option);
                        });
                    }
                }
            });

            // Trigger change event if old faculty_id exists
            @if(old('faculty_id'))
                facultySelect.value = '{{ old("faculty_id") }}';
                facultySelect.dispatchEvent(new Event('change'));
                @if(old('specialty_id'))
                    setTimeout(() => {
                        const specialtySelect = document.getElementById('specialtySelect');
                        if (specialtySelect) {
                            specialtySelect.value = '{{ old("specialty_id") }}';
                        }
                    }, 100);
                @endif
            @endif
        }

        // File upload handlers
        function initFileUploads() {
            document.querySelectorAll('.file-upload-area').forEach(area => {
                const input = area.querySelector('input[type="file"]');
                const label = area.querySelector('.file-label');
                const icon = area.querySelector('.file-icon');

                if (!input) return;

                input.addEventListener('change', function() {
                    if (this.files.length > 0) {
                        const file = this.files[0];
                        area.classList.add('has-file');
                        if (label) {
                            label.innerHTML = `<i class="fas fa-check-circle text-blue-500 mr-2"></i>${file.name}`;
                        }
                        if (icon) {
                            icon.classList.remove('text-gray-400');
                            icon.classList.add('text-blue-500');
                        }
                    } else {
                        area.classList.remove('has-file');
                        if (label) {
                            label.innerHTML = 'Fayl tanlang yoki bu yerga tashlang';
                        }
                        if (icon) {
                            icon.classList.add('text-gray-400');
                            icon.classList.remove('text-blue-500');
                        }
                    }
                });

                // Drag and drop
                ['dragenter', 'dragover'].forEach(eventName => {
                    area.addEventListener(eventName, (e) => {
                        e.preventDefault();
                        area.classList.add('has-file');
                    });
                });

                ['dragleave', 'drop'].forEach(eventName => {
                    area.addEventListener(eventName, (e) => {
                        e.preventDefault();
                        if (!input.files.length) {
                            area.classList.remove('has-file');
                        }
                    });
                });
            });
        }

        // Input formatting
        function initInputFormatting() {
            // JSHSHIR - only digits
            const jshshirInput = document.querySelector('input[name="jshshir"]');
            if (jshshirInput) {
                jshshirInput.addEventListener('input', function() {
                    this.value = this.value.replace(/\D/g, '').slice(0, 14);
                });
            }

            // Passport series - uppercase
            const passportSeriesInput = document.querySelector('input[name="passport_series"]');
            if (passportSeriesInput) {
                passportSeriesInput.addEventListener('input', function() {
                    this.value = this.value.toUpperCase().replace(/[^A-Z]/g, '').slice(0, 2);
                });
            }

            // Passport number - only digits
            const passportNumberInput = document.querySelector('input[name="passport_number"]');
            if (passportNumberInput) {
                passportNumberInput.addEventListener('input', function() {
                    this.value = this.value.replace(/\D/g, '').slice(0, 7);
                });
            }

            // Phone formatting
            const phoneInput = document.querySelector('input[name="phone"]');
            if (phoneInput) {
                phoneInput.addEventListener('input', function() {
                    let value = this.value.replace(/\D/g, '');
                    if (value.startsWith('998')) {
                        value = '+' + value;
                    } else if (!value.startsWith('+')) {
                        value = '+998' + value;
                    }
                    this.value = value.slice(0, 13);
                });
            }
        }

        // Form validation
        function initFormValidation() {
            const form = document.getElementById('admissionForm');
            if (!form) return;

            form.addEventListener('submit', function(e) {
                const submitBtn = document.getElementById('submitBtn');
                const agreement = document.getElementById('agreement');

                if (!agreement || !agreement.checked) {
                    e.preventDefault();
                    showToast('Iltimos, shartlarga rozilik bering!', 'error');
                    return;
                }

                submitBtn.classList.add('loading');
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Yuborilmoqda...';
            });
        }

        // Section navigation
        function showSection(sectionNumber) {
            // Hide all sections
            document.querySelectorAll('.form-section').forEach(section => {
                section.style.display = 'none';
                section.classList.remove('active');
            });

            // Show current section
            const targetSection = document.getElementById('section' + sectionNumber);
            if (targetSection) {
                targetSection.style.display = 'block';
                targetSection.classList.add('active');
            }

            // Update step indicators
            document.querySelectorAll('.step-indicator').forEach(step => {
                const stepNumber = parseInt(step.dataset.step);
                step.classList.remove('active', 'completed');

                if (stepNumber < sectionNumber) {
                    step.classList.add('completed');
                } else if (stepNumber === sectionNumber) {
                    step.classList.add('active');
                }
            });

            // Update progress line
            const progressLine = document.getElementById('progressLine');
            if (progressLine) {
                const progress = ((sectionNumber - 1) / (totalSteps - 1)) * 100;
                progressLine.style.width = progress + '%';
            }

            currentSection = sectionNumber;
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function nextSection() {
            if (validateCurrentSection()) {
                if (currentSection < totalSteps) {
                    showSection(currentSection + 1);
                }
            }
        }

        function prevSection() {
            if (currentSection > 1) {
                showSection(currentSection - 1);
            }
        }

        function validateCurrentSection() {
            const section = document.getElementById('section' + currentSection);
            if (!section) return true;

            const inputs = section.querySelectorAll('[required]');
            let valid = true;
            let firstInvalid = null;

            inputs.forEach(input => {
                // Reset state
                input.classList.remove('error');

                // Check validity
                if (!input.value || (input.type === 'checkbox' && !input.checked)) {
                    input.classList.add('error');
                    valid = false;
                    if (!firstInvalid) firstInvalid = input;
                }
            });

            if (!valid) {
                showToast('Barcha majburiy maydonlarni to\'ldiring!', 'error');

                // Focus first invalid
                if (firstInvalid) {
                    firstInvalid.focus();
                }
            }

            return valid;
        }

        function showToast(message, type = 'info') {
            const toast = document.createElement('div');
            const bgColor = type === 'error' ? 'bg-red-500' : 'bg-blue-500';
            toast.className = `fixed bottom-4 right-4 ${bgColor} text-white px-6 py-3 rounded-xl shadow-lg flex items-center z-50 animate-fade-in`;
            toast.innerHTML = `<i class="fas fa-${type === 'error' ? 'exclamation-circle' : 'check-circle'} mr-2"></i> ${message}`;
            document.body.appendChild(toast);

            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transition = 'opacity 0.3s';
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }
    </script>
</body>
</html>
