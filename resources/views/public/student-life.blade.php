@extends(\App\Helpers\TemplateHelper::getLayout())

@section('title', 'Talabalar Hayoti - Tourism Academy Samarkand')
@section('page-title', 'Talabalar Hayoti')
@section('breadcrumb', 'Kampus Hayoti')

@section('content')
<div class="container mx-auto px-4 py-12">
    <div class="bg-white rounded-lg shadow-lg p-8">
        <div class="text-center mb-8">
            <i class="fas fa-users text-6xl text-orange-500 mb-4"></i>
            <h2 class="text-3xl font-bold mb-4">Faol va Qiziqarli Talabalar Hayoti</h2>
            <p class="text-gray-600">Klub, to'garaklar va ijtimoiy faoliyatlar</p>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="bg-gradient-to-br from-blue-400 to-blue-600 rounded-lg p-6 text-white">
                <i class="fas fa-theater-masks text-4xl mb-3"></i>
                <h3 class="text-xl font-bold mb-2">San'at to'garagi</h3>
                <p>Teatr, raqs va musiqa</p>
            </div>

            <div class="bg-gradient-to-br from-green-400 to-green-600 rounded-lg p-6 text-white">
                <i class="fas fa-futbol text-4xl mb-3"></i>
                <h3 class="text-xl font-bold mb-2">Sport klublari</h3>
                <p>Futbol, voleybol, tennis</p>
            </div>

            <div class="bg-gradient-to-br from-purple-400 to-purple-600 rounded-lg p-6 text-white">
                <i class="fas fa-brain text-4xl mb-3"></i>
                <h3 class="text-xl font-bold mb-2">Intellektual klub</h3>
                <p>Zakovat, debat klubi</p>
            </div>

            <div class="bg-gradient-to-br from-red-400 to-red-600 rounded-lg p-6 text-white">
                <i class="fas fa-camera text-4xl mb-3"></i>
                <h3 class="text-xl font-bold mb-2">Media klub</h3>
                <p>Fotografiya va video</p>
            </div>

            <div class="bg-gradient-to-br from-yellow-400 to-yellow-600 rounded-lg p-6 text-white">
                <i class="fas fa-language text-4xl mb-3"></i>
                <h3 class="text-xl font-bold mb-2">Til klublari</h3>
                <p>Ingliz, nemis, fransuz tillari</p>
            </div>

            <div class="bg-gradient-to-br from-teal-400 to-teal-600 rounded-lg p-6 text-white">
                <i class="fas fa-hands-helping text-4xl mb-3"></i>
                <h3 class="text-xl font-bold mb-2">Volontyorlar</h3>
                <p>Ijtimoiy loyihalar</p>
            </div>
        </div>

        <div class="mt-12">
            <h3 class="text-2xl font-bold mb-6 text-center">Yillik tadbirlar</h3>
            <div class="grid md:grid-cols-2 gap-6">
                <div class="border-l-4 border-orange-500 pl-4">
                    <h4 class="font-bold mb-2">Bahor festivali</h4>
                    <p class="text-gray-600">Har yili mart oyida o'tkaziladi</p>
                </div>
                <div class="border-l-4 border-blue-500 pl-4">
                    <h4 class="font-bold mb-2">Sport musobaqalari</h4>
                    <p class="text-gray-600">Universitetlararo bellashuvlar</p>
                </div>
                <div class="border-l-4 border-green-500 pl-4">
                    <h4 class="font-bold mb-2">Ilmiy konferensiyalar</h4>
                    <p class="text-gray-600">Talabalar ilmiy ishlari</p>
                </div>
                <div class="border-l-4 border-purple-500 pl-4">
                    <h4 class="font-bold mb-2">Madaniyat kuni</h4>
                    <p class="text-gray-600">Xalqaro talabalar festivali</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection