@extends(\App\Helpers\TemplateHelper::getLayout())

@section('title', 'Xalqaro Aloqalar - Tourism Academy Samarkand')
@section('page-title', 'Xalqaro Hamkorlik')
@section('breadcrumb', 'Xalqaro aloqalar')

@section('content')
<div class="container mx-auto px-4 py-12">
    <div class="bg-white rounded-lg shadow-lg p-8">
        <div class="text-center mb-8">
            <i class="fas fa-globe text-6xl text-blue-500 mb-4"></i>
            <h2 class="text-3xl font-bold mb-4">Xalqaro Hamkorlik</h2>
            <p class="text-gray-600">Dunyo universitetlari bilan aloqalar</p>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="border rounded-lg p-6 hover:shadow-lg transition">
                <div class="flex items-center mb-3">
                    <img src="https://flagcdn.com/48x36/gb.png" alt="UK" class="mr-3">
                    <h3 class="font-bold">Oxford University</h3>
                </div>
                <p class="text-sm text-gray-600">Talaba almashinuvi dasturi</p>
            </div>

            <div class="border rounded-lg p-6 hover:shadow-lg transition">
                <div class="flex items-center mb-3">
                    <img src="https://flagcdn.com/48x36/us.png" alt="USA" class="mr-3">
                    <h3 class="font-bold">Harvard University</h3>
                </div>
                <p class="text-sm text-gray-600">Qo'shma tadqiqot loyihalari</p>
            </div>

            <div class="border rounded-lg p-6 hover:shadow-lg transition">
                <div class="flex items-center mb-3">
                    <img src="https://flagcdn.com/48x36/de.png" alt="Germany" class="mr-3">
                    <h3 class="font-bold">Berlin University</h3>
                </div>
                <p class="text-sm text-gray-600">Magistratura dasturlari</p>
            </div>

            <div class="border rounded-lg p-6 hover:shadow-lg transition">
                <div class="flex items-center mb-3">
                    <img src="https://flagcdn.com/48x36/tr.png" alt="Turkey" class="mr-3">
                    <h3 class="font-bold">Istanbul University</h3>
                </div>
                <p class="text-sm text-gray-600">O'qituvchilar almashinuvi</p>
            </div>

            <div class="border rounded-lg p-6 hover:shadow-lg transition">
                <div class="flex items-center mb-3">
                    <img src="https://flagcdn.com/48x36/kr.png" alt="Korea" class="mr-3">
                    <h3 class="font-bold">Seoul National University</h3>
                </div>
                <p class="text-sm text-gray-600">Texnologiya transferi</p>
            </div>

            <div class="border rounded-lg p-6 hover:shadow-lg transition">
                <div class="flex items-center mb-3">
                    <img src="https://flagcdn.com/48x36/jp.png" alt="Japan" class="mr-3">
                    <h3 class="font-bold">Tokyo University</h3>
                </div>
                <p class="text-sm text-gray-600">Ilmiy konferensiyalar</p>
            </div>
        </div>

        <div class="mt-12">
            <h3 class="text-2xl font-bold mb-6 text-center">Xalqaro dasturlar</h3>
            <div class="grid md:grid-cols-2 gap-6">
                <div class="bg-blue-50 rounded-lg p-6">
                    <h4 class="font-bold text-blue-800 mb-3">
                        <i class="fas fa-exchange-alt mr-2"></i> Erasmus+ dasturi
                    </h4>
                    <p class="text-gray-600">Yevropa universitetlarida bir semestr o'qish imkoniyati</p>
                </div>

                <div class="bg-green-50 rounded-lg p-6">
                    <h4 class="font-bold text-green-800 mb-3">
                        <i class="fas fa-certificate mr-2"></i> Qo'sh diplom
                    </h4>
                    <p class="text-gray-600">Ikki davlat diplomini olish imkoniyati</p>
                </div>

                <div class="bg-purple-50 rounded-lg p-6">
                    <h4 class="font-bold text-purple-800 mb-3">
                        <i class="fas fa-briefcase mr-2"></i> Xalqaro amaliyot
                    </h4>
                    <p class="text-gray-600">Xorijiy kompaniyalarda amaliyot o'tash</p>
                </div>

                <div class="bg-orange-50 rounded-lg p-6">
                    <h4 class="font-bold text-orange-800 mb-3">
                        <i class="fas fa-language mr-2"></i> Til kurslari
                    </h4>
                    <p class="text-gray-600">Xorijda til o'rganish dasturlari</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection