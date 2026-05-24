@extends('layouts.auth')

@section('title', 'Talaba ro\'yxatdan o\'tish')
@section('subtitle', 'Tez va oson ro\'yxatdan o\'ting')

@section('content')
    <form action="{{ route('student.register') }}" method="POST">
        @csrf

        <div class="mb-6">
            <label for="full_name" class="block text-gray-700 text-sm font-bold mb-2">
                Ism va Familiya
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
            <label for="phone" class="block text-gray-700 text-sm font-bold mb-2">
                Telefon raqam (ixtiyoriy)
            </label>
            <input id="phone" name="phone" type="tel"
                class="shadow appearance-none border rounded w-full py-3 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-indigo-500 @error('phone') border-red-500 @enderror"
                placeholder="+998 90 123 45 67"
                value="{{ old('phone') }}">
            @error('phone')
                <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
            @enderror
            <p class="text-gray-600 text-xs mt-1">Keyinchalik qo'shishingiz mumkin</p>
        </div>

        <div class="mb-6">
            <label for="password" class="block text-gray-700 text-sm font-bold mb-2">
                Parol
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
                Parolni tasdiqlash
            </label>
            <input id="password_confirmation" name="password_confirmation" type="password" required
                class="shadow appearance-none border rounded w-full py-3 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:border-indigo-500"
                placeholder="Parolni qayta kiriting">
        </div>

        <div class="mb-4">
            <button type="submit"
                class="w-full bg-gradient-to-r from-green-500 to-green-600 text-white font-bold py-3 px-4 rounded-lg hover:from-green-600 hover:to-green-700 focus:outline-none focus:shadow-outline transition duration-200 transform hover:scale-[1.02]">
                <i class="fas fa-user-plus mr-2"></i>
                Ro'yxatdan o'tish
            </button>
        </div>
    </form>

    <div class="relative my-6">
        <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-gray-300"></div></div>
        <div class="relative flex justify-center text-sm"><span class="px-3 bg-white text-gray-500">yoki</span></div>
    </div>

    @includeIf('auth.partials.social-buttons')

    <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle text-blue-400"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm text-blue-700">
                    Ro'yxatdan o'tgandan keyin profilingizda qo'shimcha ma'lumotlarni to'ldirishingiz mumkin
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

    <div class="text-center mt-4">
        <a href="{{ route('home') }}" class="inline-flex items-center text-gray-600 hover:text-indigo-600 transition-colors">
            <i class="fas fa-home mr-2"></i>
            <span class="text-sm">Bosh sahifaga qaytish</span>
        </a>
    </div>
@endsection