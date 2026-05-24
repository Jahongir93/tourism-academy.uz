@extends('layouts.frontend')

@section('title', 'Fakultetlar - Tourism Academy')

@section('content')
<section class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold gradient-text mb-4">Fakultetlar</h1>
            <p class="text-gray-600 text-lg">Bizning ta'lim yo'nalishlarimiz</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Faculty cards will be added here -->
            <div class="bg-white rounded-2xl shadow-xl p-8 col-span-full">
                <p class="text-gray-600 text-center py-12">Tez orada yangilanadi...</p>
            </div>
        </div>
    </div>
</section>
@endsection