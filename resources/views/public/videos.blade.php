@extends(\App\Helpers\TemplateHelper::getLayout())

@section('title', 'Video Darslar - Tourism Academy Samarkand')
@section('page-title', 'Video Darslar')
@section('breadcrumb', 'Video Ta\'lim')

@section('content')
<div class="container mx-auto px-4 py-12">
    <div class="bg-white rounded-lg shadow-lg p-8">
        <div class="text-center mb-8">
            <i class="fas fa-video text-6xl text-red-500 mb-4"></i>
            <h2 class="text-3xl font-bold mb-4">Online Video Darslar</h2>
            <p class="text-gray-600">Professional video kurslar va ma'ruzalar</p>
        </div>

        <div class="grid md:grid-cols-3 gap-6">
            @for($i = 1; $i <= 9; $i++)
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition">
                <div class="relative">
                    <div class="h-48 bg-gray-300 flex items-center justify-center">
                        <i class="fas fa-play-circle text-6xl text-white opacity-80"></i>
                    </div>
                    <span class="absolute top-2 right-2 bg-black bg-opacity-75 text-white text-xs px-2 py-1 rounded">
                        45:{{ str_pad($i * 3, 2, '0', STR_PAD_LEFT) }}
                    </span>
                </div>
                <div class="p-4">
                    <h3 class="font-bold mb-2">Video dars #{{ $i }}: Turizm asoslari</h3>
                    <p class="text-sm text-gray-600 mb-3">O'qituvchi: Prof. A. Karimov</p>
                    <div class="flex justify-between items-center">
                        <span class="text-xs text-gray-500">
                            <i class="fas fa-eye mr-1"></i> 1.2K ko'rildi
                        </span>
                        <button class="text-green-600 hover:text-green-700">
                            <i class="fas fa-play mr-1"></i> Ko'rish
                        </button>
                    </div>
                </div>
            </div>
            @endfor
        </div>
    </div>
</div>
@endsection