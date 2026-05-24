@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-6">
        <p class="font-bold">Ma'lumotlar bazasi mavjud emas</p>
        <p>Tizim demo rejimda ishlayapti. Ba'zi funksiyalar cheklangan.</p>
    </div>

    @if(config('database_fallback.demo_mode'))
    <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 mb-6">
        <p class="font-bold">Demo rejim</p>
        <p>Siz demo ma'lumotlar bilan ishlayapsiz.</p>
    </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @if(!empty($courses))
            @foreach($courses as $course)
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold mb-2">{{ $course['title'] ?? 'Nomsiz kurs' }}</h3>
                <p class="text-gray-600 mb-4">{{ $course['description'] ?? '' }}</p>
                <div class="flex justify-between items-center">
                    <span class="text-2xl font-bold text-blue-600">
                        {{ isset($course['price']) ? number_format($course['price']) . ' so\'m' : 'Bepul' }}
                    </span>
                    <button class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                        Ko'rish
                    </button>
                </div>
            </div>
            @endforeach
        @else
            <div class="col-span-3 text-center text-gray-500 py-8">
                <p>Hozircha kurslar mavjud emas</p>
            </div>
        @endif
    </div>

    <div class="mt-8 p-6 bg-gray-100 rounded-lg">
        <h2 class="text-xl font-semibold mb-4">Tizim holati</h2>
        <ul class="space-y-2">
            <li class="flex items-center">
                <span class="w-4 h-4 bg-red-500 rounded-full mr-2"></span>
                <span>Ma'lumotlar bazasi: Oflayn</span>
            </li>
            <li class="flex items-center">
                <span class="w-4 h-4 bg-green-500 rounded-full mr-2"></span>
                <span>Fallback rejim: Faol</span>
            </li>
            <li class="flex items-center">
                <span class="w-4 h-4 bg-yellow-500 rounded-full mr-2"></span>
                <span>Demo rejim: {{ config('database_fallback.demo_mode') ? 'Faol' : 'O\'chirilgan' }}</span>
            </li>
        </ul>
    </div>
</div>
@endsection