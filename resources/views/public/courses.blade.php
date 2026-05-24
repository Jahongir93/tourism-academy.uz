@extends(\App\Helpers\TemplateHelper::getLayout())

@section('title', 'Kurslar - Tourism Academy Samarkand')
@section('page-title', 'Ta\'lim Kurslari')
@section('breadcrumb', 'Kurslar')

@section('content')
<div class="container mx-auto px-4 py-12">
    <div class="grid md:grid-cols-3 gap-6">
        @for($i = 1; $i <= 6; $i++)
        <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition">
            <div class="h-48 bg-gradient-to-br from-green-400 to-green-600 flex items-center justify-center">
                <i class="fas fa-graduation-cap text-white text-5xl"></i>
            </div>
            <div class="p-6">
                <h3 class="text-xl font-bold mb-2">Turizm menejmenti {{ $i }}</h3>
                <p class="text-gray-600 mb-4">Professional turizm menejmenti kursi</p>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-500">
                        <i class="fas fa-clock mr-1"></i> 6 oy
                    </span>
                    <a href="#" class="text-green-600 hover:text-green-700 font-medium">
                        Batafsil <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
        </div>
        @endfor
    </div>
</div>
@endsection