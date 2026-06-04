@extends('layouts.dashboard-new')

@section('title', 'Talabani tahrirlash - HEMIS')
@section('page-title', 'Talabani tahrirlash')

@section('content')
<div class="container-fluid">
    <!-- Back Button -->
    <div class="mb-4">
        <a href="{{ route('students.index') }}" class="inline-flex items-center text-gray-600 hover:text-gray-900">
            <i class="fas fa-arrow-left mr-2"></i>
            <span>Orqaga qaytish</span>
        </a>
    </div>
    
    @if ($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        <strong>Xatolik!</strong>
                        <ul class="mt-2">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('students.update', $student->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                        <div class="bg-indigo-600 text-white p-4">
                            <h2 class="text-xl font-bold">Talaba ma'lumotlarini tahrirlash</h2>
                        </div>

                        <div class="p-6">
                            <!-- Photo Upload Section -->
                            <div class="mb-8">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4 border-b pb-2">Talaba rasmi</h3>
                                <div class="flex items-start gap-6">
                                    <div class="flex-shrink-0">
                                        <div class="w-32 h-32 rounded-lg border-2 border-gray-300 overflow-hidden bg-gray-100 flex items-center justify-center">
                                            @if($student->photo_url)
                                                <img id="photoPreview" src="{{ asset('storage/' . $student->photo_url) }}" alt="Student Photo" class="w-full h-full object-cover">
                                            @else
                                                <svg id="photoPreview" width="64" height="64" fill="#ccc" viewBox="0 0 16 16">
                                                    <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H3zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
                                                </svg>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex-1">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Rasm yuklash</label>
                                        <input type="file" name="photo" id="photoInput" accept="image/jpeg,image/png,image/jpg"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                        <p class="text-xs text-gray-500 mt-1">JPG, JPEG yoki PNG formatida, maksimal 2MB</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Personal Information -->
                            <div class="mb-8">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4 border-b pb-2">Shaxsiy ma'lumotlar</h3>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Talaba ID <span class="text-red-500">*</span></label>
                                        <input type="text" name="student_id" value="{{ old('student_id', $student->student_id) }}"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Ism <span class="text-red-500">*</span></label>
                                        <input type="text" name="first_name" value="{{ old('first_name', $student->first_name) }}"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Familiya <span class="text-red-500">*</span></label>
                                        <input type="text" name="last_name" value="{{ old('last_name', $student->last_name) }}"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Otasining ismi</label>
                                        <input type="text" name="middle_name" value="{{ old('middle_name', $student->middle_name) }}"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Tug'ilgan sana <span class="text-red-500">*</span></label>
                                        <input type="date" name="birth_date" value="{{ old('birth_date', date('Y-m-d', strtotime($student->birth_date))) }}"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Jinsi <span class="text-red-500">*</span></label>
                                        <select name="gender" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                            <option value="erkak" {{ old('gender', $student->gender) == 'erkak' ? 'selected' : '' }}>Erkak</option>
                                            <option value="ayol" {{ old('gender', $student->gender) == 'ayol' ? 'selected' : '' }}>Ayol</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">PINFL</label>
                                        <input type="text" name="pinfl" value="{{ old('pinfl', $student->pinfl) }}" maxlength="14"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Pasport seriya</label>
                                        <input type="text" name="passport_series" value="{{ old('passport_series', $student->passport_series) }}" maxlength="2"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Pasport raqam</label>
                                        <input type="text" name="passport_number" value="{{ old('passport_number', $student->passport_number) }}"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                </div>
                            </div>

                            <!-- Contact Information -->
                            <div class="mb-8">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4 border-b pb-2">Aloqa ma'lumotlari</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Telefon <span class="text-red-500">*</span></label>
                                        <input type="text" name="phone" value="{{ old('phone', $student->phone) }}"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Ota-ona telefoni</label>
                                        <input type="text" name="parent_phone" value="{{ old('parent_phone', $student->parent_phone) }}"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                                        <input type="email" name="email" value="{{ old('email', $student->email) }}"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Doimiy manzil</label>
                                        <input type="text" name="permanent_address" value="{{ old('permanent_address', $student->permanent_address) }}"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Vaqtinchalik manzil</label>
                                        <input type="text" name="temporary_address" value="{{ old('temporary_address', $student->temporary_address) }}"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                </div>
                            </div>

                            <!-- Login/Parol o'zgartirish -->
                            <div class="mb-8">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4 border-b pb-2">
                                    <i class="fas fa-key text-indigo-600 mr-2"></i>
                                    Tizimga kirish ma'lumotlari
                                </h3>
                                <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                                    @if($student->user)
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Joriy Login (Email)</label>
                                            <div class="flex items-center gap-2">
                                                <input type="text" value="{{ $student->user->email }}" readonly
                                                       class="flex-1 px-3 py-2 bg-gray-100 border border-gray-300 rounded-md text-gray-700">
                                                <button type="button" onclick="copyToClipboard('{{ $student->user->email }}')" class="px-3 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 text-sm">
                                                    <i class="fas fa-copy"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Yangi Login (ixtiyoriy)</label>
                                            <input type="email" name="new_login" value="{{ old('new_login') }}"
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                                   placeholder="Yangi email kiritish (ixtiyoriy)">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Yangi Parol</label>
                                            <div class="flex gap-2">
                                                <input type="text" name="new_password" id="new_password" value="{{ old('new_password') }}"
                                                       class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                                       placeholder="Yangi parol (ixtiyoriy)">
                                                <button type="button" onclick="generateNewPassword()" class="px-3 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm">
                                                    <i class="fas fa-sync-alt"></i>
                                                </button>
                                                <button type="button" onclick="copyNewPassword()" class="px-3 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 text-sm">
                                                    <i class="fas fa-copy"></i>
                                                </button>
                                            </div>
                                            <p class="text-xs text-gray-500 mt-1">Parolni o'zgartirish uchun yangi parol kiriting</p>
                                        </div>
                                        <div class="flex items-end">
                                            <div id="passwordGenerated" class="hidden p-2 bg-green-100 text-green-800 rounded text-sm">
                                                <i class="fas fa-check-circle mr-1"></i>
                                                Yangi parol: <strong id="generatedPasswordDisplay"></strong>
                                            </div>
                                        </div>
                                    </div>
                                    @else
                                    <div class="text-yellow-700 bg-yellow-100 p-3 rounded">
                                        <i class="fas fa-exclamation-triangle mr-2"></i>
                                        Bu talabaga user akkaunti biriktirilmagan.
                                        <button type="button" onclick="document.getElementById('createUserSection').classList.toggle('hidden')" class="ml-2 text-blue-600 underline">User yaratish</button>
                                    </div>
                                    <div id="createUserSection" class="hidden mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Login (Email)</label>
                                            <input type="email" name="create_user_email" value="{{ old('create_user_email', $student->email) }}"
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                                   placeholder="user@tas.uz">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Parol</label>
                                            <div class="flex gap-2">
                                                <input type="text" name="create_user_password" id="create_user_password" value="{{ old('create_user_password', 'password123') }}"
                                                       class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                                <button type="button" onclick="generateCreatePassword()" class="px-3 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm">
                                                    <i class="fas fa-sync-alt"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Academic Information -->
                            <div class="mb-8">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4 border-b pb-2">Ta'lim ma'lumotlari</h3>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Fakultet <span class="text-red-500">*</span></label>
                                        <select name="faculty_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                            @foreach($faculties as $faculty)
                                                <option value="{{ $faculty->id }}" {{ old('faculty_id', $student->faculty_id) == $faculty->id ? 'selected' : '' }}>
                                                    {{ $faculty->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Yo'nalish <span class="text-red-500">*</span></label>
                                        <select name="specialty_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                            @foreach($specialties as $specialty)
                                                <option value="{{ $specialty->id }}" {{ old('specialty_id', $student->specialty_id) == $specialty->id ? 'selected' : '' }}>
                                                    {{ $specialty->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Guruh</label>
                                        <select name="group_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                            <option value="">Tanlanmagan</option>
                                            @foreach($groups as $group)
                                                <option value="{{ $group->id }}" {{ old('group_id', $student->group_id) == $group->id ? 'selected' : '' }}>
                                                    {{ $group->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Kurs <span class="text-red-500">*</span></label>
                                        <select name="course" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                            @for($i = 1; $i <= 6; $i++)
                                                <option value="{{ $i }}" {{ old('course', $student->course) == $i ? 'selected' : '' }}>{{ $i }}-kurs</option>
                                            @endfor
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Ta'lim shakli <span class="text-red-500">*</span></label>
                                        <select name="education_form" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                            <option value="kunduzgi" {{ old('education_form', $student->education_form) == 'kunduzgi' ? 'selected' : '' }}>Kunduzgi</option>
                                            <option value="sirtqi" {{ old('education_form', $student->education_form) == 'sirtqi' ? 'selected' : '' }}>Sirtqi</option>
                                            <option value="kechki" {{ old('education_form', $student->education_form) == 'kechki' ? 'selected' : '' }}>Kechki</option>
                                            <option value="masofaviy" {{ old('education_form', $student->education_form) == 'masofaviy' ? 'selected' : '' }}>Masofaviy</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Ta'lim turi <span class="text-red-500">*</span></label>
                                        <select name="education_type" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                            <option value="byudjet" {{ old('education_type', $student->education_type) == 'byudjet' ? 'selected' : '' }}>Byudjet</option>
                                            <option value="shartnoma" {{ old('education_type', $student->education_type) == 'shartnoma' ? 'selected' : '' }}>Shartnoma</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                                        <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                            <option value="active" {{ old('status', $student->status) == 'active' ? 'selected' : '' }}>Faol</option>
                                            <option value="academic_leave" {{ old('status', $student->status) == 'academic_leave' ? 'selected' : '' }}>Akademik ta'til</option>
                                            <option value="expelled" {{ old('status', $student->status) == 'expelled' ? 'selected' : '' }}>Chetlatilgan</option>
                                            <option value="graduated" {{ old('status', $student->status) == 'graduated' ? 'selected' : '' }}>Bitirgan</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Form Actions -->
                            <div class="flex justify-end space-x-4">
                                <a href="{{ route('students.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                    Bekor qilish
                                </a>
                                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                    Saqlash
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
</div>

<script>
// Photo preview functionality
document.getElementById('photoInput').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(event) {
            const preview = document.getElementById('photoPreview');
            preview.src = event.target.result;
            preview.classList.remove('svg');
            preview.classList.add('w-full', 'h-full', 'object-cover');
        };
        reader.readAsDataURL(file);
    }
});

// Parol generatsiya funksiyalari
function generateNewPassword() {
    const chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    let password = '';
    for (let i = 0; i < 8; i++) {
        password += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    document.getElementById('new_password').value = password;
    document.getElementById('passwordGenerated').classList.remove('hidden');
    document.getElementById('generatedPasswordDisplay').textContent = password;
}

function generateCreatePassword() {
    const chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    let password = '';
    for (let i = 0; i < 8; i++) {
        password += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    document.getElementById('create_user_password').value = password;
}

function copyNewPassword() {
    const password = document.getElementById('new_password').value;
    if (password) {
        navigator.clipboard.writeText(password).then(() => {
            alert('Parol nusxalandi!');
        });
    } else {
        alert('Avval parol generatsiya qiling!');
    }
}

function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        alert('Nusxalandi: ' + text);
    });
}
</script>
@endsection