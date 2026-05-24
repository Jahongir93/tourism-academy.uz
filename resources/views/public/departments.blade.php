@extends(\App\Helpers\TemplateHelper::getLayout())

@section('title', 'Kafedralar - Tourism Academy Samarkand')
@section('page-title', 'Kafedralar')
@section('breadcrumb', 'Barcha kafedralar')

@section('content')
<div class="container mx-auto px-4 py-12">

    <!-- Filter by Faculty -->
    @if($faculties->isNotEmpty())
    <div class="mb-8">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex flex-wrap items-center gap-3">
                <span class="font-bold text-black">Fakultet bo'yicha filtrlash:</span>
                <a href="{{ route('departments') }}"
                   class="px-4 py-2 rounded-lg {{ !request('faculty') ? 'bg-green-600 text-white' : 'bg-white border border-green-600 hover:bg-green-50 text-black' }} transition">
                    Barchasi
                </a>
                @foreach($faculties as $fac)
                <a href="{{ route('departments') }}?faculty={{ $fac->id }}"
                   class="px-4 py-2 rounded-lg {{ request('faculty') == $fac->id ? 'bg-green-600 text-white' : 'bg-white border border-green-600 hover:bg-green-50 text-black' }} transition">
                    {{ $fac->name_uz ?: $fac->name }}
                </a>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Departments List -->
    @if($departments->isEmpty())
        <div class="bg-white rounded-lg shadow-lg p-8">
            <div class="text-center">
                <i class="fas fa-building text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-xl font-bold text-gray-600 mb-2">Kafedralar mavjud emas</h3>
                <p class="text-gray-500">Hozircha kafedralar ro'yxati bo'sh</p>
            </div>
        </div>
    @else
        @foreach($faculties as $faculty)
            @php
                $facultyDepartments = $departments->get($faculty->id, collect());
            @endphp

            @if($facultyDepartments->isNotEmpty())
            <div class="mb-8">
                <!-- Faculty Header -->
                <div class="bg-black text-white rounded-t-lg p-4 mb-4">
                    <h2 class="text-2xl font-bold">
                        <i class="fas fa-university mr-2"></i>
                        {{ $faculty->name_uz ?: $faculty->name }}
                    </h2>
                    <p class="text-sm opacity-90">{{ $facultyDepartments->count() }} ta kafedra</p>
                </div>

                <!-- Departments Grid -->
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($facultyDepartments as $department)
                    <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300">
                        <!-- Department Header -->
                        <div class="bg-green-600 p-5 text-white">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-lg font-bold mb-1">
                                        {{ $department->name_uz ?: $department->name }}
                                    </h3>
                                    @if($department->short_name)
                                        <p class="text-sm opacity-90">{{ $department->short_name }}</p>
                                    @endif
                                </div>
                                <i class="fas fa-building text-2xl opacity-50"></i>
                            </div>
                        </div>

                        <!-- Department Info -->
                        <div class="p-5">
                            <!-- Main Information -->
                            <div class="space-y-2 mb-4">
                                @if($department->head && $department->head->name)
                                <div class="flex items-center text-sm">
                                    <i class="fas fa-user-tie text-green-600 w-5"></i>
                                    <span class="text-black ml-2">Mudiri: {{ $department->head->name }}</span>
                                </div>
                                @endif

                                @if($department->type)
                                <div class="flex items-center text-sm">
                                    <i class="fas fa-tag text-green-600 w-5"></i>
                                    <span class="text-black ml-2">
                                        Turi:
                                        @switch($department->type)
                                            @case('general')
                                                Umumiy
                                                @break
                                            @case('major')
                                                Ixtisoslik
                                                @break
                                            @default
                                                {{ ucfirst($department->type) }}
                                        @endswitch
                                    </span>
                                </div>
                                @endif

                                @if($department->phone)
                                <div class="flex items-center text-sm">
                                    <i class="fas fa-phone text-green-600 w-5"></i>
                                    <span class="text-black ml-2">{{ $department->phone }}</span>
                                </div>
                                @endif

                                @if($department->email)
                                <div class="flex items-center text-sm">
                                    <i class="fas fa-envelope text-green-600 w-5"></i>
                                    <span class="text-black ml-2">{{ $department->email }}</span>
                                </div>
                                @endif

                                @if($department->room_number)
                                <div class="flex items-center text-sm">
                                    <i class="fas fa-door-open text-green-600 w-5"></i>
                                    <span class="text-black ml-2">Xona: {{ $department->room_number }}</span>
                                </div>
                                @endif
                            </div>

                            <!-- Statistics -->
                            <div class="grid grid-cols-2 gap-4 py-3 border-t border-gray-200">
                                <div class="text-center">
                                    <div class="text-xl font-bold text-green-600">
                                        {{ $department->specialties->count() ?? 0 }}
                                    </div>
                                    <div class="text-xs text-black">Yo'nalishlar</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-xl font-bold text-green-600">
                                        {{ $department->staff_capacity ?? 0 }}
                                    </div>
                                    <div class="text-xs text-black">Xodimlar</div>
                                </div>
                            </div>

                            <!-- Specialties -->
                            @if($department->specialties && $department->specialties->isNotEmpty())
                            <div class="mt-3 pt-3 border-t">
                                <h4 class="text-sm font-bold text-black mb-2">Yo'nalishlar:</h4>
                                <div class="space-y-1">
                                    @foreach($department->specialties->take(3) as $specialty)
                                    <div class="text-xs text-black pl-3 border-l-2 border-green-600">
                                        • {{ $specialty->name_uz ?: $specialty->name }}
                                    </div>
                                    @endforeach
                                    @if($department->specialties->count() > 3)
                                    <div class="text-xs text-gray-700 italic pl-3">
                                        va yana {{ $department->specialties->count() - 3 }} ta...
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @endif

                            <!-- View More Button -->
                            <div class="mt-4">
                                <button class="w-full bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded-lg transition duration-200 text-sm">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Batafsil ma'lumot
                                </button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        @endforeach
    @endif

    <!-- Additional Information -->
    <div class="mt-12 bg-white border-2 border-green-600 rounded-lg p-8">
        <div class="text-center">
            <i class="fas fa-info-circle text-4xl text-green-600 mb-4"></i>
            <h3 class="text-xl font-bold text-black mb-2">Kafedralar haqida</h3>
            <p class="text-black max-w-3xl mx-auto">
                Tourism Academy Samarkand kafedralari yuqori malakali professor-o'qituvchilar jamoasi bilan
                talabalarni zamonaviy bilim va ko'nikmalar bilan ta'minlaydi. Har bir kafedra o'z yo'nalishi bo'yicha
                ilmiy-tadqiqot ishlari olib boradi va xalqaro hamkorlik aloqalarini rivojlantiradi.
            </p>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Filter buttons hover effect */
    .filter-btn {
        transition: all 0.3s ease;
    }

    /* Custom scrollbar */
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