@extends('layouts.app')

@section('title', 'Profilni to\'ldirish')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">

        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-yellow-800">
                        Profilingizni to'ldiring
                    </h3>
                    <p class="text-sm text-yellow-700 mt-1">
                        Barcha imkoniyatlardan foydalanish uchun profilingizni to'liq to'ldiring
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4">
                <h2 class="text-2xl font-bold text-white">
                    <i class="fas fa-user-edit mr-2"></i>
                    Shaxsiy ma'lumotlar
                </h2>
                <p class="text-blue-100 text-sm mt-1">
                    Talaba ID: {{ $student->student_id }}
                </p>
            </div>

            <form action="{{ route('student.update-profile') }}" method="POST" class="p-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <div>
                        <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Ism <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="first_name" id="first_name" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            value="{{ old('first_name', $student->first_name) }}"
                            placeholder="Ismingiz">
                        @error('first_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Familiya <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="last_name" id="last_name" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            value="{{ old('last_name', $student->last_name) }}"
                            placeholder="Familiyangiz">
                        @error('last_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="middle_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Otasining ismi
                        </label>
                        <input type="text" name="middle_name" id="middle_name"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            value="{{ old('middle_name', $student->middle_name) }}"
                            placeholder="Otangizning ismi">
                        @error('middle_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="birth_date" class="block text-sm font-medium text-gray-700 mb-2">
                            Tug'ilgan sana <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="birth_date" id="birth_date" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            value="{{ old('birth_date', $student->birth_date) }}">
                        @error('birth_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="gender" class="block text-sm font-medium text-gray-700 mb-2">
                            Jinsi <span class="text-red-500">*</span>
                        </label>
                        <select name="gender" id="gender" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Tanlang</option>
                            <option value="male" {{ old('gender', $student->gender) == 'male' ? 'selected' : '' }}>
                                Erkak
                            </option>
                            <option value="female" {{ old('gender', $student->gender) == 'female' ? 'selected' : '' }}>
                                Ayol
                            </option>
                        </select>
                        @error('gender')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                            Telefon raqam <span class="text-red-500">*</span>
                        </label>
                        <input type="tel" name="phone" id="phone" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            value="{{ old('phone', $student->phone) }}"
                            placeholder="+998 90 123 45 67">
                        @error('phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            Email (ixtiyoriy)
                        </label>
                        <input type="email" name="email" id="email"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            value="{{ old('email', $student->email) }}"
                            placeholder="email@example.com">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="passport_series" class="block text-sm font-medium text-gray-700 mb-2">
                            Pasport seriyasi
                        </label>
                        <input type="text" name="passport_series" id="passport_series"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            value="{{ old('passport_series', $student->passport_series) }}"
                            placeholder="AA">
                        @error('passport_series')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="passport_number" class="block text-sm font-medium text-gray-700 mb-2">
                            Pasport raqami
                        </label>
                        <input type="text" name="passport_number" id="passport_number"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            value="{{ old('passport_number', $student->passport_number) }}"
                            placeholder="1234567">
                        @error('passport_number')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                            Yashash manzili
                        </label>
                        <textarea name="address" id="address" rows="3"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="To'liq manzilingiz">{{ old('address', $student->address) }}</textarea>
                        @error('address')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                </div>

                <div class="mt-8 flex justify-between">
                    <a href="{{ route('student.dashboard') }}"
                        class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition duration-200">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Keyinroq to'ldiraman
                    </a>
                    <button type="submit"
                        class="px-8 py-2 bg-gradient-to-r from-blue-500 to-blue-600 text-white font-medium rounded-lg hover:from-blue-600 hover:to-blue-700 transition duration-200 transform hover:scale-[1.02]">
                        <i class="fas fa-save mr-2"></i>
                        Saqlash
                    </button>
                </div>

            </form>
        </div>

        <div class="mt-6 bg-blue-50 border-l-4 border-blue-400 p-4 rounded-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-info-circle text-blue-400"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800">
                        Ma'lumot
                    </h3>
                    <p class="text-sm text-blue-700 mt-1">
                        * belgi bilan belgilangan maydonlar majburiy. Qolgan ma'lumotlarni keyinroq ham to'ldirishingiz mumkin.
                    </p>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection