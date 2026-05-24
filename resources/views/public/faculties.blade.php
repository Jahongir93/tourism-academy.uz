@extends(\App\Helpers\TemplateHelper::getLayout())

@section('title', 'Fakultetlar - Tourism Academy Samarkand')
@section('page-title', 'Fakultetlar')
@section('breadcrumb', 'Barcha fakultetlar')

@section('content')
<div class="container mx-auto px-4 py-12">
    <!-- Statistika -->
    @if(isset($stats))
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-lg shadow p-6 text-center border border-gray-200">
            <div class="text-3xl font-bold text-green-600">{{ $stats['total_faculties'] }}</div>
            <div class="text-black text-sm">Fakultetlar</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6 text-center border border-gray-200">
            <div class="text-3xl font-bold text-green-600">{{ $stats['total_departments'] }}</div>
            <div class="text-black text-sm">Kafedralar</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6 text-center border border-gray-200">
            <div class="text-3xl font-bold text-green-600">{{ $stats['total_students'] }}</div>
            <div class="text-black text-sm">Talabalar</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6 text-center border border-gray-200">
            <div class="text-3xl font-bold text-green-600">{{ $stats['total_teachers'] }}</div>
            <div class="text-black text-sm">O'qituvchilar</div>
        </div>
    </div>
    @endif

    <!-- Fakultetlar ro'yxati -->
    @if($faculties->isEmpty())
        <div class="bg-white rounded-lg shadow-lg p-8">
            <div class="text-center">
                <i class="fas fa-university text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-xl font-bold text-gray-600 mb-2">Fakultetlar mavjud emas</h3>
                <p class="text-gray-500">Hozircha fakultetlar ro'yxati bo'sh</p>
            </div>
        </div>
    @else
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($faculties as $faculty)
            <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300">
                <!-- Fakultet boshi -->
                <div class="bg-black p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-xl font-bold mb-1">
                                {{ $faculty->name_uz ?: $faculty->name }}
                            </h3>
                            @if($faculty->short_name)
                                <p class="text-sm opacity-90">{{ $faculty->short_name }}</p>
                            @endif
                        </div>
                        <i class="fas fa-university text-3xl opacity-50"></i>
                    </div>
                </div>

                <!-- Fakultet ma'lumotlari -->
                <div class="p-6">
                    <!-- Asosiy ma'lumotlar -->
                    <div class="space-y-3 mb-4">
                        @if($faculty->dean_name)
                        <div class="flex items-center text-sm">
                            <i class="fas fa-user-tie text-green-600 w-5"></i>
                            <span class="text-black ml-2">Dekan: {{ $faculty->dean_name }}</span>
                        </div>
                        @endif

                        @if($faculty->phone)
                        <div class="flex items-center text-sm">
                            <i class="fas fa-phone text-green-600 w-5"></i>
                            <span class="text-black ml-2">{{ $faculty->phone }}</span>
                        </div>
                        @endif

                        @if($faculty->email)
                        <div class="flex items-center text-sm">
                            <i class="fas fa-envelope text-green-600 w-5"></i>
                            <span class="text-black ml-2">{{ $faculty->email }}</span>
                        </div>
                        @endif

                        @if($faculty->room)
                        <div class="flex items-center text-sm">
                            <i class="fas fa-door-open text-green-600 w-5"></i>
                            <span class="text-black ml-2">Xona: {{ $faculty->room }}</span>
                        </div>
                        @endif
                    </div>

                    <!-- Statistika -->
                    <div class="grid grid-cols-2 gap-4 py-4 border-t border-b border-gray-200">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-green-600">
                                {{ $faculty->departments->count() }}
                            </div>
                            <div class="text-xs text-black">Kafedralar</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-green-600">
                                {{ $faculty->specialties->count() ?? 0 }}
                            </div>
                            <div class="text-xs text-black">Yo'nalishlar</div>
                        </div>
                    </div>

                    <!-- Kafedralar ro'yxati -->
                    @if($faculty->departments->isNotEmpty())
                    <div class="mt-4">
                        <h4 class="text-sm font-bold text-black mb-2">Kafedralar:</h4>
                        <div class="space-y-1 max-h-32 overflow-y-auto">
                            @foreach($faculty->departments->take(5) as $department)
                            <div class="text-sm text-black pl-3 border-l-2 border-green-600">
                                • {{ $department->name_uz ?: $department->name }}
                            </div>
                            @endforeach
                            @if($faculty->departments->count() > 5)
                            <div class="text-sm text-gray-700 italic pl-3">
                                va yana {{ $faculty->departments->count() - 5 }} ta...
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- Ko'proq ma'lumot tugmasi -->
                    <div class="mt-6">
                        <a href="{{ route('departments') }}?faculty={{ $faculty->id }}"
                           class="block text-center bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded-lg transition duration-200">
                            <i class="fas fa-arrow-right mr-2"></i>
                            Batafsil ko'rish
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @endif

    <!-- Qo'shimcha ma'lumot -->
    <div class="mt-12 bg-white border-2 border-green-600 rounded-lg p-8">
        <div class="text-center">
            <i class="fas fa-info-circle text-4xl text-green-600 mb-4"></i>
            <h3 class="text-xl font-bold text-black mb-2">Fakultetlar haqida</h3>
            <p class="text-black max-w-3xl mx-auto">
                Tourism Academy Samarkand fakultetlari zamonaviy ta'lim dasturlari va yuqori malakali professor-o'qituvchilar
                jamoasi bilan turizm sohasida professional kadrlar tayyorlashga ixtisoslashgan.
            </p>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Custom scrollbar for departments list */
    .overflow-y-auto::-webkit-scrollbar {
        width: 4px;
    }
    .overflow-y-auto::-webkit-scrollbar-track {
        background: #f1f1f1;
    }
    .overflow-y-auto::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 4px;
    }
    .overflow-y-auto::-webkit-scrollbar-thumb:hover {
        background: #555;
    }
</style>
@endpush