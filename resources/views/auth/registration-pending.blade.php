@extends('layouts.auth')

@section('title', 'Ro\'yxatdan o\'tish jarayoni')
@section('subtitle', 'Murojaatingiz qabul qilindi')

@section('content')
    <div class="text-center py-8">
        <div class="mb-6">
            <div class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-green-100">
                <i class="fas fa-check text-5xl text-green-500"></i>
            </div>
        </div>

        <h3 class="text-2xl font-bold text-gray-800 mb-4">
            Ro'yxatdan o'tish muvaffaqiyatli amalga oshirildi!
        </h3>

        @if(session('success'))
            <div class="bg-green-50 border-l-4 border-green-400 p-4 mb-6 mx-auto max-w-lg">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle text-green-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-green-700">
                            {{ session('success') }}
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6 mx-auto max-w-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-info-circle text-blue-400"></i>
                </div>
                <div class="ml-3 text-left">
                    <p class="text-sm text-blue-700 font-semibold mb-2">
                        Keyingi qadamlar:
                    </p>
                    <ul class="text-sm text-blue-700 list-disc list-inside space-y-1">
                        <li>Murojaatingiz Admin yoki HR tomonidan ko'rib chiqiladi</li>
                        <li>Tasdiqlangandan so'ng, tizimga kirish imkoniyati beriladi</li>
                        <li>Ko'rib chiqish jarayoni odatda 1-3 ish kuni davom etadi</li>
                        <li>Tasdiqlangach sizga telefon yoki email orqali xabar beriladi</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="space-y-3">
            <a href="{{ route('login') }}"
                class="inline-flex items-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-lg transition-colors">
                <i class="fas fa-sign-in-alt mr-2"></i>
                Tizimga kirish
            </a>

            <div class="block">
                <a href="{{ route('home') }}"
                    class="inline-flex items-center text-gray-600 hover:text-indigo-600 transition-colors">
                    <i class="fas fa-home mr-2"></i>
                    <span class="text-sm">Bosh sahifaga qaytish</span>
                </a>
            </div>
        </div>

        <div class="mt-8 p-4 bg-gray-50 rounded-lg mx-auto max-w-lg">
            <p class="text-sm text-gray-600">
                <i class="fas fa-question-circle mr-1"></i>
                Savol yoki muammolar bo'lsa,
                <a href="#" class="text-indigo-600 hover:text-indigo-800 font-semibold">
                    qo'llab-quvvatlash xizmatiga
                </a> murojaat qiling.
            </p>
        </div>
    </div>
@endsection
