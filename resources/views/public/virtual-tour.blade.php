@extends(\App\Helpers\TemplateHelper::getLayout())

@section('title', 'Virtual Tur - Tourism Academy Samarkand')
@section('page-title', 'Kampus Virtual Turi')
@section('breadcrumb', 'Virtual Tur')

@section('content')
<div class="container mx-auto px-4 py-12">
    <div class="bg-white rounded-lg shadow-lg p-8">
        <div class="text-center mb-8">
            <i class="fas fa-vr-cardboard text-6xl text-green-500 mb-4"></i>
            <h2 class="text-3xl font-bold mb-4">360° Virtual Tur</h2>
            <p class="text-gray-600">Kampusimizni virtual muhitda ko'ring va tanishing</p>
        </div>

        <div class="bg-gray-100 rounded-lg p-12 text-center">
            <p class="text-gray-500 mb-4">Virtual tur tez kunda ishga tushiriladi</p>
            <button class="bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-lg transition">
                <i class="fas fa-play mr-2"></i> Turni boshlash
            </button>
        </div>

        <div class="grid md:grid-cols-3 gap-6 mt-8">
            <div class="text-center">
                <div class="bg-green-100 rounded-lg p-4 mb-3">
                    <i class="fas fa-building text-3xl text-green-600"></i>
                </div>
                <h3 class="font-bold">Bosh bino</h3>
                <p class="text-sm text-gray-600">Zamonaviy auditoriyalar</p>
            </div>
            <div class="text-center">
                <div class="bg-blue-100 rounded-lg p-4 mb-3">
                    <i class="fas fa-book text-3xl text-blue-600"></i>
                </div>
                <h3 class="font-bold">Kutubxona</h3>
                <p class="text-sm text-gray-600">10,000+ kitoblar</p>
            </div>
            <div class="text-center">
                <div class="bg-purple-100 rounded-lg p-4 mb-3">
                    <i class="fas fa-flask text-3xl text-purple-600"></i>
                </div>
                <h3 class="font-bold">Laboratoriyalar</h3>
                <p class="text-sm text-gray-600">Zamonaviy jihozlar</p>
            </div>
        </div>
    </div>
</div>
@endsection