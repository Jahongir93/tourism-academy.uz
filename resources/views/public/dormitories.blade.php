@extends(\App\Helpers\TemplateHelper::getLayout())

@section('title', 'Yotoqxonalar - Tourism Academy Samarkand')
@section('page-title', 'Talabalar Yotoqxonalari')
@section('breadcrumb', 'Yotoqxonalar')

@section('content')
<div class="container mx-auto px-4 py-12">
    <div class="bg-white rounded-lg shadow-lg p-8">
        <div class="text-center mb-8">
            <i class="fas fa-bed text-6xl text-indigo-500 mb-4"></i>
            <h2 class="text-3xl font-bold mb-4">Zamonaviy Yotoqxonalar</h2>
            <p class="text-gray-600">Qulay va xavfsiz yashash sharoitlari</p>
        </div>

        <div class="grid md:grid-cols-2 gap-8">
            <div class="border rounded-lg overflow-hidden">
                <div class="h-48 bg-gray-200 flex items-center justify-center">
                    <i class="fas fa-building text-6xl text-gray-400"></i>
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold mb-3">1-Yotoqxona (Qizlar uchun)</h3>
                    <ul class="space-y-2 text-gray-600">
                        <li><i class="fas fa-check text-green-500 mr-2"></i> 500 o'rinli</li>
                        <li><i class="fas fa-check text-green-500 mr-2"></i> 2-3 kishilik xonalar</li>
                        <li><i class="fas fa-check text-green-500 mr-2"></i> Wi-Fi, issiq suv</li>
                        <li><i class="fas fa-check text-green-500 mr-2"></i> O'quv xonasi</li>
                        <li><i class="fas fa-check text-green-500 mr-2"></i> Oshxona va kir yuvish xonasi</li>
                    </ul>
                </div>
            </div>

            <div class="border rounded-lg overflow-hidden">
                <div class="h-48 bg-gray-200 flex items-center justify-center">
                    <i class="fas fa-building text-6xl text-gray-400"></i>
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold mb-3">2-Yotoqxona (Yigitlar uchun)</h3>
                    <ul class="space-y-2 text-gray-600">
                        <li><i class="fas fa-check text-green-500 mr-2"></i> 600 o'rinli</li>
                        <li><i class="fas fa-check text-green-500 mr-2"></i> 2-4 kishilik xonalar</li>
                        <li><i class="fas fa-check text-green-500 mr-2"></i> Wi-Fi, issiq suv</li>
                        <li><i class="fas fa-check text-green-500 mr-2"></i> Sport zali</li>
                        <li><i class="fas fa-check text-green-500 mr-2"></i> Kompyuter xonasi</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="mt-8 bg-blue-50 rounded-lg p-6">
            <h3 class="text-lg font-bold mb-3">Yotoqxona narxlari (oylik)</h3>
            <div class="grid md:grid-cols-3 gap-4">
                <div class="bg-white rounded p-4">
                    <h4 class="font-bold text-green-600">2 kishilik xona</h4>
                    <p class="text-2xl font-bold">200,000 so'm</p>
                </div>
                <div class="bg-white rounded p-4">
                    <h4 class="font-bold text-blue-600">3 kishilik xona</h4>
                    <p class="text-2xl font-bold">150,000 so'm</p>
                </div>
                <div class="bg-white rounded p-4">
                    <h4 class="font-bold text-purple-600">4 kishilik xona</h4>
                    <p class="text-2xl font-bold">100,000 so'm</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection