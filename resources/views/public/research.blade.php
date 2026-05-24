@extends(\App\Helpers\TemplateHelper::getLayout())

@section('title', 'Tadqiqotlar - Tourism Academy Samarkand')
@section('page-title', 'Ilmiy Tadqiqotlar')
@section('breadcrumb', 'Tadqiqotlar')

@section('content')
<div class="container mx-auto px-4 py-12">
    <div class="bg-white rounded-lg shadow-lg p-8">
        <div class="text-center mb-8">
            <i class="fas fa-microscope text-6xl text-purple-500 mb-4"></i>
            <h2 class="text-3xl font-bold mb-4">Ilmiy Tadqiqot Markazlari</h2>
            <p class="text-gray-600">Turizm sohasida ilmiy izlanishlar olib boramiz</p>
        </div>

        <div class="grid md:grid-cols-2 gap-8">
            <div class="border rounded-lg p-6">
                <h3 class="text-xl font-bold mb-3 text-purple-600">Turizm tadqiqotlari markazi</h3>
                <p class="text-gray-600 mb-4">Turizm industriyasini rivojlantirish bo'yicha tadqiqotlar</p>
                <ul class="list-disc list-inside text-gray-600">
                    <li>Turizm marketingi</li>
                    <li>Turistik destinatsiyalar</li>
                    <li>Madaniy turizm</li>
                </ul>
            </div>

            <div class="border rounded-lg p-6">
                <h3 class="text-xl font-bold mb-3 text-blue-600">Raqamli turizm laboratoriyasi</h3>
                <p class="text-gray-600 mb-4">Zamonaviy texnologiyalar va turizm</p>
                <ul class="list-disc list-inside text-gray-600">
                    <li>Virtual turizm</li>
                    <li>Smart turizm</li>
                    <li>Turizm analitikasi</li>
                </ul>
            </div>
        </div>

        <div class="mt-12">
            <h3 class="text-2xl font-bold mb-6 text-center">Ilmiy nashrlar</h3>
            <div class="grid md:grid-cols-3 gap-4">
                @for($i = 1; $i <= 3; $i++)
                <div class="bg-gray-50 rounded-lg p-4">
                    <i class="fas fa-file-alt text-2xl text-green-500 mb-2"></i>
                    <h4 class="font-bold mb-2">Turizm rivojlanishi {{ $i }}</h4>
                    <p class="text-sm text-gray-600">Ilmiy maqola, 2024</p>
                </div>
                @endfor
            </div>
        </div>
    </div>
</div>
@endsection