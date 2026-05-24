@extends(\App\Helpers\TemplateHelper::getLayout())

@section('title', 'Sport va Madaniyat - Tourism Academy Samarkand')
@section('page-title', 'Sport va Madaniyat')
@section('breadcrumb', 'Sport majmuasi')

@section('content')
<div class="container mx-auto px-4 py-12">
    <div class="bg-white rounded-lg shadow-lg p-8">
        <div class="text-center mb-8">
            <i class="fas fa-trophy text-6xl text-yellow-500 mb-4"></i>
            <h2 class="text-3xl font-bold mb-4">Sport va Madaniyat Markazi</h2>
            <p class="text-gray-600">Sog'lom turmush tarzi va faol dam olish</p>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="text-center">
                <div class="w-24 h-24 mx-auto mb-4 bg-blue-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-swimming-pool text-4xl text-blue-500"></i>
                </div>
                <h3 class="font-bold mb-2">Suzish basseyn</h3>
                <p class="text-sm text-gray-600">25m, 6 yo'lakli basseyn</p>
            </div>

            <div class="text-center">
                <div class="w-24 h-24 mx-auto mb-4 bg-green-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-futbol text-4xl text-green-500"></i>
                </div>
                <h3 class="font-bold mb-2">Futbol maydoni</h3>
                <p class="text-sm text-gray-600">Sun'iy o't qoplamali</p>
            </div>

            <div class="text-center">
                <div class="w-24 h-24 mx-auto mb-4 bg-orange-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-basketball-ball text-4xl text-orange-500"></i>
                </div>
                <h3 class="font-bold mb-2">Sport zali</h3>
                <p class="text-sm text-gray-600">Basketbol, voleybol</p>
            </div>

            <div class="text-center">
                <div class="w-24 h-24 mx-auto mb-4 bg-purple-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-dumbbell text-4xl text-purple-500"></i>
                </div>
                <h3 class="font-bold mb-2">Trenajyor zali</h3>
                <p class="text-sm text-gray-600">Zamonaviy uskunalar</p>
            </div>

            <div class="text-center">
                <div class="w-24 h-24 mx-auto mb-4 bg-red-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-table-tennis text-4xl text-red-500"></i>
                </div>
                <h3 class="font-bold mb-2">Tennis kortlari</h3>
                <p class="text-sm text-gray-600">4 ta ochiq kort</p>
            </div>

            <div class="text-center">
                <div class="w-24 h-24 mx-auto mb-4 bg-teal-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-running text-4xl text-teal-500"></i>
                </div>
                <h3 class="font-bold mb-2">Yugurish yo'lkalari</h3>
                <p class="text-sm text-gray-600">400m tartanli trek</p>
            </div>
        </div>
    </div>
</div>
@endsection