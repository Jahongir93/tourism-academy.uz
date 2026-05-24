@extends(\App\Helpers\TemplateHelper::getLayout())

@section('title', 'Tadbirlar - Tourism Academy Samarkand')
@section('page-title', 'Tadbirlar')
@section('breadcrumb', 'Yaqinlashayotgan tadbirlar')

@section('content')
<div class="container mx-auto px-4 py-12">
    <div class="bg-white rounded-lg shadow-lg p-8">
        <div class="text-center mb-8">
            <i class="fas fa-calendar-alt text-6xl text-pink-500 mb-4"></i>
            <h2 class="text-3xl font-bold mb-4">Tadbirlar kalendari</h2>
            <p class="text-gray-600">Konferensiyalar, seminarlar va boshqa tadbirlar</p>
        </div>

        <div class="space-y-6">
            @foreach($events as $event)
            <div class="border rounded-lg p-6 hover:shadow-lg transition">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                    <div class="flex-1">
                        <div class="flex items-center mb-2">
                            <span class="bg-pink-100 text-pink-600 px-3 py-1 rounded-full text-sm font-medium">
                                {{ $event['date']->format('d-M') }}
                            </span>
                            <h3 class="text-xl font-bold ml-4">{{ $event['title'] }}</h3>
                        </div>
                        <p class="text-gray-600 mb-2">{{ $event['description'] }}</p>
                        <div class="flex items-center text-sm text-gray-500">
                            <i class="fas fa-map-marker-alt mr-2"></i>
                            {{ $event['location'] }}
                            <span class="mx-3">•</span>
                            <i class="fas fa-clock mr-2"></i>
                            {{ $event['date']->format('H:i') }}
                        </div>
                    </div>
                    <div class="mt-4 md:mt-0">
                        <button class="bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded-lg transition">
                            Ro'yxatdan o'tish
                        </button>
                    </div>
                </div>
            </div>
            @endforeach

            @for($i = 3; $i <= 6; $i++)
            <div class="border rounded-lg p-6 hover:shadow-lg transition">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                    <div class="flex-1">
                        <div class="flex items-center mb-2">
                            <span class="bg-blue-100 text-blue-600 px-3 py-1 rounded-full text-sm font-medium">
                                {{ now()->addDays($i * 5)->format('d-M') }}
                            </span>
                            <h3 class="text-xl font-bold ml-4">Seminar: Turizm rivojlanishi</h3>
                        </div>
                        <p class="text-gray-600 mb-2">Zamonaviy turizm tendensiyalari haqida</p>
                        <div class="flex items-center text-sm text-gray-500">
                            <i class="fas fa-map-marker-alt mr-2"></i>
                            Konferensiya zali
                            <span class="mx-3">•</span>
                            <i class="fas fa-clock mr-2"></i>
                            14:00
                        </div>
                    </div>
                    <div class="mt-4 md:mt-0">
                        <button class="bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded-lg transition">
                            Ro'yxatdan o'tish
                        </button>
                    </div>
                </div>
            </div>
            @endfor
        </div>
    </div>
</div>
@endsection