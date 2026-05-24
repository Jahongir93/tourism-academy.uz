@extends('layouts.frontend')

@section('title', 'Yangiliklar - Tourism Academy')

@section('content')
<section class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold gradient-text mb-4">Yangiliklar</h1>
            <p class="text-gray-600 text-lg">Eng so'nggi yangiliklar va e'lonlar</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- News cards will be added here -->
            <div class="bg-white rounded-2xl shadow-xl p-8 col-span-full">
                <p class="text-gray-600 text-center py-12">Tez orada yangilanadi...</p>
            </div>
        </div>

        <!-- Pagination -->
        <div class="flex justify-center mt-12">
            <nav class="flex space-x-2">
                <a href="#" class="px-4 py-2 bg-white rounded-lg shadow hover:shadow-lg transition">←</a>
                <a href="#" class="px-4 py-2 bg-emerald-600 text-white rounded-lg shadow">1</a>
                <a href="#" class="px-4 py-2 bg-white rounded-lg shadow hover:shadow-lg transition">2</a>
                <a href="#" class="px-4 py-2 bg-white rounded-lg shadow hover:shadow-lg transition">3</a>
                <a href="#" class="px-4 py-2 bg-white rounded-lg shadow hover:shadow-lg transition">→</a>
            </nav>
        </div>
    </div>
</section>
@endsection