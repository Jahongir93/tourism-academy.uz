@extends(\App\Helpers\TemplateHelper::getLayout())

@section('title', 'Interaktiv Xarita - Tourism Academy Samarkand')
@section('page-title', 'Interaktiv Xarita')
@section('breadcrumb', 'Kampus Xaritasi')

@section('content')
<div class="container mx-auto px-4 py-12">
    <div class="bg-white rounded-lg shadow-lg p-8">
        <div class="text-center mb-8">
            <i class="fas fa-map-marked-alt text-6xl text-blue-500 mb-4"></i>
            <h2 class="text-3xl font-bold mb-4">Kampus Interaktiv Xaritasi</h2>
            <p class="text-gray-600">Kampus binolarini toping va yo'nalishlarni ko'ring</p>
        </div>

        <div class="bg-gray-100 rounded-lg p-4" style="height: 500px;">
            <iframe
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3071.8981!2d66.9757!3d39.6547!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMznCsDM5JzE3LjAiTiA2NsKwNTgnMzIuNSJF!5e0!3m2!1sen!2s!4v1234567890"
                width="100%"
                height="100%"
                style="border:0;"
                allowfullscreen=""
                loading="lazy">
            </iframe>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-4 mt-8">
            <div class="bg-gray-50 rounded-lg p-4">
                <h3 class="font-bold text-green-600 mb-2">Bosh bino</h3>
                <p class="text-sm text-gray-600">A-blok, 1-qavat</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-4">
                <h3 class="font-bold text-blue-600 mb-2">Kutubxona</h3>
                <p class="text-sm text-gray-600">B-blok, 2-qavat</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-4">
                <h3 class="font-bold text-purple-600 mb-2">Sport majmuasi</h3>
                <p class="text-sm text-gray-600">C-blok</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-4">
                <h3 class="font-bold text-orange-600 mb-2">Oshxona</h3>
                <p class="text-sm text-gray-600">D-blok, 1-qavat</p>
            </div>
        </div>
    </div>
</div>
@endsection