@extends('layouts.frontend')

@section('title', 'Kutubxona - Tourism Academy')

@section('content')
<section class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold gradient-text mb-4">Elektron Kutubxona</h1>
            <p class="text-gray-600 text-lg">Minglab kitob va resurslar bir joyda</p>
        </div>

        <!-- Search Panel -->
        <div class="max-w-2xl mx-auto mb-12">
            <div class="bg-white rounded-full shadow-xl p-2 flex">
                <input type="text" placeholder="Kitob, muallif yoki mavzu qidiring..."
                    class="flex-1 px-6 py-3 outline-none text-gray-700">
                <button class="bg-gradient-to-r from-emerald-500 to-teal-600 text-white px-8 py-3 rounded-full hover:from-emerald-600 hover:to-teal-700 transition">
                    <i class="fas fa-search mr-2"></i>Qidirish
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Book cards will be added here -->
            <div class="bg-white rounded-2xl shadow-xl p-8 col-span-full">
                <p class="text-gray-600 text-center py-12">Tez orada yangilanadi...</p>
            </div>
        </div>
    </div>
</section>
@endsection