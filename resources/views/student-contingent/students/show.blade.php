@extends('layouts.dashboard-new')

@section('title', 'Talaba ma\'lumotlari')
@section('page-title', 'Talaba ma\'lumotlari')

@section('content')
<div class="container-fluid px-4">
    <!-- Back Button & Actions -->
    <div class="flex flex-wrap justify-between items-center mb-4 gap-2">
        <a href="{{ route('students.index') }}" class="inline-flex items-center text-gray-600 hover:text-gray-900">
            <i class="fas fa-arrow-left mr-2"></i>
            <span>Orqaga qaytish</span>
        </a>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('students.id-card', $student->id) }}" class="bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium py-2 px-3 rounded">
                <i class="fas fa-id-card mr-1"></i>ID Card
            </a>
            <a href="{{ route('students.edit', $student->id) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-medium py-2 px-3 rounded">
                <i class="fas fa-edit mr-1"></i>Tahrirlash
            </a>
            <form action="{{ route('students.destroy', $student->id) }}" method="POST" class="inline" onsubmit="return confirm('Rostdan ham bu talabani o\'chirmoqchimisiz?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white text-sm font-medium py-2 px-3 rounded">
                    <i class="fas fa-trash mr-1"></i>O'chirish
                </button>
            </form>
        </div>
    </div>

    <!-- Student Card -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <!-- Header with Photo -->
        <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 text-white p-4 md:p-6">
            <div class="flex flex-col sm:flex-row items-center sm:items-start gap-4">
                <!-- Photo -->
                <div class="flex-shrink-0">
                    {{-- BUGFIX #56: Simplified photo display using accessor --}}
                    <img src="{{ $student->photo }}"
                         alt="{{ $student->full_name }}"
                         class="w-28 h-36 object-cover rounded-lg border-4 border-white/30 shadow-lg"
                         onerror="this.onerror=null; this.src='{{ asset('images/default-avatar.svg') }}';">
                </div>
                <!-- Info -->
                <div class="text-center sm:text-left">
                    <h2 class="text-xl md:text-2xl font-bold">{{ $student->last_name }} {{ $student->first_name }} {{ $student->middle_name ?? '' }}</h2>
                    <p class="text-indigo-200 mt-1">
                        <i class="fas fa-id-badge mr-1"></i>{{ $student->student_id }}
                    </p>
                    <p class="text-indigo-200">
                        <i class="fas fa-university mr-1"></i>{{ $student->faculty->name_uz ?? $student->faculty_name ?? 'Fakultet' }}
                    </p>
                    <p class="text-indigo-200">
                        <i class="fas fa-users mr-1"></i>{{ $student->group->name ?? 'Guruh' }}, {{ $student->course ?? 1 }}-kurs
                    </p>
                    <div class="mt-2">
                        @if($student->status == 'active')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <i class="fas fa-check-circle mr-1"></i>Faol
                            </span>
                        @elseif($student->status == 'graduated')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                <i class="fas fa-graduation-cap mr-1"></i>Bitirgan
                            </span>
                        @elseif($student->status == 'expelled')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                <i class="fas fa-times-circle mr-1"></i>Chetlatilgan
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                {{ $student->status }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="p-4 md:p-6">
            <!-- Grid Layout for Information Sections -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                <!-- Shaxsiy ma'lumotlar -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h3 class="text-base font-semibold text-gray-900 mb-3 flex items-center">
                        <i class="fas fa-user text-indigo-500 mr-2"></i>Shaxsiy ma'lumotlar
                    </h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Ism:</span>
                            <span class="text-gray-900 font-medium">{{ $student->first_name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Familiya:</span>
                            <span class="text-gray-900 font-medium">{{ $student->last_name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Otasining ismi:</span>
                            <span class="text-gray-900">{{ $student->middle_name ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Tug'ilgan sana:</span>
                            <span class="text-gray-900">{{ $student->birth_date ? date('d.m.Y', strtotime($student->birth_date)) : '-' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Jinsi:</span>
                            <span class="text-gray-900">
                                @if($student->gender == 'male' || $student->gender == 'erkak') Erkak
                                @elseif($student->gender == 'female' || $student->gender == 'ayol') Ayol
                                @else - @endif
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Pasport:</span>
                            <span class="text-gray-900">{{ $student->passport_series ?? '' }} {{ $student->passport_number ?? '-' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Aloqa ma'lumotlari -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h3 class="text-base font-semibold text-gray-900 mb-3 flex items-center">
                        <i class="fas fa-phone text-green-500 mr-2"></i>Aloqa ma'lumotlari
                    </h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Telefon:</span>
                            <span class="text-gray-900">{{ $student->phone ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Email:</span>
                            <span class="text-gray-900">{{ $student->email ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Manzil:</span>
                            <span class="text-gray-900 text-right max-w-xs">{{ $student->address ?? $student->permanent_address ?? '-' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Ta'lim ma'lumotlari -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h3 class="text-base font-semibold text-gray-900 mb-3 flex items-center">
                        <i class="fas fa-graduation-cap text-blue-500 mr-2"></i>Ta'lim ma'lumotlari
                    </h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Fakultet:</span>
                            <span class="text-gray-900">{{ $student->faculty->name_uz ?? $student->faculty_name ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Yo'nalish:</span>
                            <span class="text-gray-900">{{ $student->specialty->name_uz ?? $student->specialty_name ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Guruh:</span>
                            <span class="text-gray-900">{{ $student->group->name ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Kurs:</span>
                            <span class="text-gray-900">{{ $student->course ?? 1 }}-kurs</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Ta'lim shakli:</span>
                            <span class="text-gray-900">
                                @if($student->education_form == 'kunduzgi') Kunduzgi
                                @elseif($student->education_form == 'sirtqi') Sirtqi
                                @elseif($student->education_form == 'kechki') Kechki
                                @else Kunduzgi @endif
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Ta'lim turi:</span>
                            <span class="text-gray-900">{{ $student->education_type == 'byudjet' ? 'Byudjet' : 'Shartnoma' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Qo'shimcha -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h3 class="text-base font-semibold text-gray-900 mb-3 flex items-center">
                        <i class="fas fa-info-circle text-yellow-500 mr-2"></i>Qo'shimcha
                    </h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Qabul sanasi:</span>
                            <span class="text-gray-900">{{ $student->admission_date ? date('d.m.Y', strtotime($student->admission_date)) : ($student->admission_year ?? '-') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">GPA:</span>
                            <span class="text-gray-900">{{ $student->gpa ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Yotoqxona:</span>
                            <span class="text-gray-900">{{ $student->has_dormitory ? 'Ha' : 'Yo\'q' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">Yaratilgan:</span>
                            <span class="text-gray-900">{{ $student->created_at ? date('d.m.Y', strtotime($student->created_at)) : '-' }}</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
