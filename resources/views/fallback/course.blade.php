@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-6">
        <p class="font-bold">Ma'lumotlar bazasi mavjud emas</p>
        <p>Bu sahifa fallback rejimda ko'rsatilmoqda.</p>
    </div>

    <div class="bg-white rounded-lg shadow-md p-8">
        <h1 class="text-3xl font-bold mb-4">{{ $course['title'] ?? 'Kurs' }}</h1>

        <p class="text-gray-600 mb-6">{{ $course['description'] ?? 'Kurs tavsifi mavjud emas' }}</p>

        <div class="mb-6">
            <span class="text-3xl font-bold text-blue-600">
                {{ isset($course['price']) ? number_format($course['price']) . ' so\'m' : 'Bepul' }}
            </span>
        </div>

        <div class="bg-orange-100 border-l-4 border-orange-500 text-orange-700 p-4">
            <p class="font-bold">Demo rejim</p>
            <p>To'liq funksiyalar ma'lumotlar bazasi ulanganda mavjud bo'ladi.</p>
        </div>

        <div class="mt-6">
            <a href="{{ route('fallback.index') }}" class="bg-gray-500 text-white px-6 py-2 rounded hover:bg-gray-600">
                Orqaga qaytish
            </a>
        </div>
    </div>
</div>
@endsection