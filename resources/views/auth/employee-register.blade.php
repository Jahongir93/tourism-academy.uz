@extends('layouts.auth')

@section('title', 'Xodim ro\'yxatdan o\'tish')
@section('subtitle', 'Xodim sifatida ro\'yxatdan o\'ting')

@section('content')
    <form action="{{ route('employee.register') }}" method="POST" x-data="{ userType: 'uzbek' }">
        @csrf

        <div class="mb-6">
            <label for="full_name" class="block text-gray-700 text-sm font-bold mb-2">
                Ism va Familiya <span class="text-red-500">*</span>
            </label>
            <input id="full_name" name="full_name" type="text" required autofocus
                class="shadow appearance-none border rounded w-full py-3 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-indigo-500 @error('full_name') border-red-500 @enderror"
                placeholder="Masalan: Alisher Navoiy"
                value="{{ old('full_name') }}">
            @error('full_name')
                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label class="block text-gray-700 text-sm font-bold mb-2">
                Fuqarolik <span class="text-red-500">*</span>
            </label>
            <div style="display: flex; gap: 1rem; justify-content: center;">
                <label class="custom-checkbox" style="cursor: pointer;">
                    <input type="radio" name="user_type" value="uzbek" x-model="userType" checked>
                    <span class="checkmark" style="border-radius: 50%;"></span>
                    <span style="font-size: 0.9rem; color: #374151;">O'zbekiston fuqarosi</span>
                </label>
                <label class="custom-checkbox" style="cursor: pointer;">
                    <input type="radio" name="user_type" value="foreign" x-model="userType">
                    <span class="checkmark" style="border-radius: 50%;"></span>
                    <span style="font-size: 0.9rem; color: #374151;">Xorijiy fuqaro</span>
                </label>
            </div>
            @error('user_type')
                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div x-show="userType === 'uzbek'" class="mb-6">
            <label for="phone" class="block text-gray-700 text-sm font-bold mb-2">
                Telefon raqam
            </label>
            <input id="phone" name="phone" type="tel"
                class="shadow appearance-none border rounded w-full py-3 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-indigo-500 @error('phone') border-red-500 @enderror"
                placeholder="+998 90 123 45 67"
                value="{{ old('phone') }}">
            @error('phone')
                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div x-show="userType === 'foreign'" class="mb-6">
            <label for="email" class="block text-gray-700 text-sm font-bold mb-2">
                Email manzil
            </label>
            <input id="email" name="email" type="email"
                class="shadow appearance-none border rounded w-full py-3 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-indigo-500 @error('email') border-red-500 @enderror"
                placeholder="example@email.com"
                value="{{ old('email') }}">
            @error('email')
                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label for="position" class="block text-gray-700 text-sm font-bold mb-2">
                Lavozim <span class="text-red-500">*</span>
            </label>
            <input id="position" name="position" type="text" required
                class="shadow appearance-none border rounded w-full py-3 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-indigo-500 @error('position') border-red-500 @enderror"
                placeholder="Masalan: O'qituvchi, Laborant, Kutubxonachi"
                value="{{ old('position') }}">
            @error('position')
                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label for="additional_info" class="block text-gray-700 text-sm font-bold mb-2">
                Qo'shimcha ma'lumot (ixtiyoriy)
            </label>
            <textarea id="additional_info" name="additional_info" rows="3"
                class="shadow appearance-none border rounded w-full py-3 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-indigo-500 @error('additional_info') border-red-500 @enderror"
                placeholder="Ish tajribasi, malaka va boshqalar">{{ old('additional_info') }}</textarea>
            @error('additional_info')
                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label for="password" class="block text-gray-700 text-sm font-bold mb-2">
                Parol <span class="text-red-500">*</span>
            </label>
            <input id="password" name="password" type="password" required
                class="shadow appearance-none border rounded w-full py-3 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-indigo-500 @error('password') border-red-500 @enderror"
                placeholder="Kamida 6 ta belgi">
            @error('password')
                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label for="password_confirmation" class="block text-gray-700 text-sm font-bold mb-2">
                Parolni tasdiqlash <span class="text-red-500">*</span>
            </label>
            <input id="password_confirmation" name="password_confirmation" type="password" required
                class="shadow appearance-none border rounded w-full py-3 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-indigo-500"
                placeholder="Parolni qayta kiriting">
        </div>

        <div class="mb-6">
            <button type="submit"
                class="w-full bg-gradient-to-r from-blue-500 to-blue-600 text-white font-bold py-3 px-4 rounded-lg hover:from-blue-600 hover:to-blue-700 focus:outline-none focus:shadow-outline transition duration-200 transform hover:scale-[1.02]">
                <i class="fas fa-user-plus mr-2"></i>
                Ro'yxatdan o'tish
            </button>
        </div>

        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-info-circle text-yellow-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-yellow-700">
                        Ro'yxatdan o'tgandan keyin, admin yoki HR tomonidan tasdiqlanishingizni kutishingiz kerak.
                        Tasdiqlangandan so'ng, tizimga kirish imkoniyati beriladi.
                    </p>
                </div>
            </div>
        </div>

        <div class="text-center">
            <span class="text-gray-600 text-sm">Hisobingiz bormi?</span>
            <a href="{{ route('login') }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-bold">
                Tizimga kirish
            </a>
        </div>
    </form>

    <div class="text-center mt-4">
        <a href="{{ route('home') }}" class="inline-flex items-center text-gray-600 hover:text-indigo-600 transition-colors">
            <i class="fas fa-home mr-2"></i>
            <span class="text-sm">Bosh sahifaga qaytish</span>
        </a>
    </div>
@endsection
