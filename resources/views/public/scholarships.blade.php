@extends(\App\Helpers\TemplateHelper::getLayout())

@section('title', 'Stipendiyalar - Tourism Academy Samarkand')
@section('page-title', 'Stipendiyalar va Grantlar')
@section('breadcrumb', 'Moliyaviy yordam')

@section('content')
<div class="container mx-auto px-4 py-12">
    <div class="bg-white rounded-lg shadow-lg p-8">
        <div class="text-center mb-8">
            <i class="fas fa-graduation-cap text-6xl text-gold-500 mb-4" style="color: #FFD700;"></i>
            <h2 class="text-3xl font-bold mb-4">Stipendiya Dasturlari</h2>
            <p class="text-gray-600">Iqtidorli talabalar uchun moliyaviy yordam</p>
        </div>

        <div class="space-y-6">
            <div class="bg-gradient-to-r from-yellow-50 to-yellow-100 rounded-lg p-6">
                <h3 class="text-xl font-bold mb-3 text-yellow-800">
                    <i class="fas fa-star mr-2"></i> Prezident stipendiyasi
                </h3>
                <p class="text-gray-700 mb-3">A'lo o'qiyotgan va ilmiy faoliyat bilan shug'ullanuvchi talabalar uchun</p>
                <div class="flex justify-between items-center">
                    <span class="text-2xl font-bold text-yellow-600">1,500,000 so'm/oy</span>
                    <button class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg transition">
                        Ariza topshirish
                    </button>
                </div>
            </div>

            <div class="bg-gradient-to-r from-blue-50 to-blue-100 rounded-lg p-6">
                <h3 class="text-xl font-bold mb-3 text-blue-800">
                    <i class="fas fa-award mr-2"></i> Davlat stipendiyasi
                </h3>
                <p class="text-gray-700 mb-3">Yaxshi o'qiyotgan talabalar uchun</p>
                <div class="flex justify-between items-center">
                    <span class="text-2xl font-bold text-blue-600">800,000 so'm/oy</span>
                    <button class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition">
                        Ariza topshirish
                    </button>
                </div>
            </div>

            <div class="bg-gradient-to-r from-green-50 to-green-100 rounded-lg p-6">
                <h3 class="text-xl font-bold mb-3 text-green-800">
                    <i class="fas fa-university mr-2"></i> Akademiya stipendiyasi
                </h3>
                <p class="text-gray-700 mb-3">Faol talabalar va sport yutug'i egalari uchun</p>
                <div class="flex justify-between items-center">
                    <span class="text-2xl font-bold text-green-600">500,000 so'm/oy</span>
                    <button class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition">
                        Ariza topshirish
                    </button>
                </div>
            </div>

            <div class="bg-gradient-to-r from-purple-50 to-purple-100 rounded-lg p-6">
                <h3 class="text-xl font-bold mb-3 text-purple-800">
                    <i class="fas fa-globe mr-2"></i> Xalqaro grantlar
                </h3>
                <p class="text-gray-700 mb-3">Xorijda ta'lim olish imkoniyati</p>
                <div class="flex justify-between items-center">
                    <span class="text-lg font-bold text-purple-600">100% grant</span>
                    <button class="bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded-lg transition">
                        Batafsil ma'lumot
                    </button>
                </div>
            </div>
        </div>

        <div class="mt-8 border-t pt-8">
            <h3 class="text-xl font-bold mb-4">Stipendiya olish shartlari:</h3>
            <ul class="space-y-2 text-gray-600">
                <li><i class="fas fa-check-circle text-green-500 mr-2"></i> O'rtacha ball 80% dan yuqori</li>
                <li><i class="fas fa-check-circle text-green-500 mr-2"></i> Darslarni muntazam qatnash</li>
                <li><i class="fas fa-check-circle text-green-500 mr-2"></i> Intizomli va faol bo'lish</li>
                <li><i class="fas fa-check-circle text-green-500 mr-2"></i> Ilmiy yoki ijodiy faoliyat</li>
            </ul>
        </div>
    </div>
</div>
@endsection