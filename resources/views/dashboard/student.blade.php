@extends('layouts.dashboard-new')

@section('title', 'Talaba Dashboard - HEMIS')
@section('page-title', 'Talaba Dashboard')

@section('content')
<div class="min-h-screen">
    <!-- Navigation -->
    <nav class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <h1 class="text-xl font-semibold">HEMIS - Talaba Paneli</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-gray-700">{{ Auth::user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-gray-500 hover:text-gray-700">
                            Chiqish
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Face Attendance Widget -->
            <div class="row mb-4">
                <div class="col-lg-6">
                    @include('components.attendance-widget', [
                        'hasRegisteredFace' => $hasRegisteredFace ?? false,
                        'todayAttendance' => $todayAttendance ?? null,
                        'attendanceHistory' => $attendanceHistory ?? []
                    ])
                </div>
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-chart-line"></i> Davomat statistikasi
                            </h5>
                        </div>
                        <div class="card-body">
                            @php
                                $history = $attendanceHistory ?? [];
                                $totalDays = count($history);
                                $presentDays = collect($history)->whereIn('status', ['present', 'late'])->count();
                                $percentage = $totalDays > 0 ? round(($presentDays / $totalDays) * 100) : 0;
                            @endphp
                            <div class="text-center">
                                <h2 class="display-4 text-primary">{{ $percentage }}%</h2>
                                <p class="text-muted">Davomat foizi</p>
                            </div>
                            <div class="row mt-4">
                                <div class="col-6 text-center">
                                    <h4>{{ $presentDays }}</h4>
                                    <small class="text-muted">Kelgan kunlar</small>
                                </div>
                                <div class="col-6 text-center">
                                    <h4>{{ $totalDays - $presentDays }}</h4>
                                    <small class="text-muted">Kelmagan kunlar</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Xush kelibsiz, {{ Auth::user()->name }}!</h2>
            
            <!-- Elektron jurnal tezkor kirish -->
            <div class="bg-gradient-to-r from-green-500 to-teal-600 text-white rounded-lg shadow-lg p-6 mb-8">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-xl font-bold mb-2">Elektron jurnal va dars jadvali</h2>
                        <p class="text-green-100 text-sm">Davomat, baholar va topshiriqlaringizni kuzating</p>
                    </div>
                    <div class="flex space-x-2">
                        <a href="{{ route('schedule.index') }}" class="bg-white text-green-600 px-4 py-2 rounded-lg text-sm font-semibold hover:bg-green-50 transition">
                            <i class="fas fa-calendar mr-1"></i> Dars jadvali
                        </a>
                        <a href="{{ route('journal.index') }}" class="bg-white text-teal-600 px-4 py-2 rounded-lg text-sm font-semibold hover:bg-teal-50 transition">
                            <i class="fas fa-star mr-1"></i> Baholarim
                        </a>
                        <a href="{{ route('assignments.index') }}" class="bg-white text-blue-600 px-4 py-2 rounded-lg text-sm font-semibold hover:bg-blue-50 transition">
                            <i class="fas fa-tasks mr-1"></i> Topshiriqlar
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-blue-500 bg-opacity-10 rounded-full">
                            <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-gray-500 text-sm">Faol fanlar</p>
                            <p class="text-2xl font-semibold text-gray-800">{{ count($enrolledCourses ?? []) }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-yellow-500 bg-opacity-10 rounded-full">
                            <svg class="w-8 h-8 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-gray-500 text-sm">Kutilayotgan vazifalar</p>
                            <p class="text-2xl font-semibold text-gray-800">{{ count($assignments ?? []) }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-green-500 bg-opacity-10 rounded-full">
                            <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-gray-500 text-sm">Topshirilgan vazifalar</p>
                            <p class="text-2xl font-semibold text-gray-800">0</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-3 bg-purple-500 bg-opacity-10 rounded-full">
                            <svg class="w-8 h-8 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-gray-500 text-sm">O'rtacha baho</p>
                            <p class="text-2xl font-semibold text-gray-800">0.0</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Schedule and Assignments -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-800">Bugungi darslar</h3>
                    </div>
                    <div class="p-6">
                        <p class="text-gray-500 text-center py-8">Bugun darslar yo'q</p>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-800">Yaqinlashayotgan vazifalar</h3>
                    </div>
                    <div class="p-6">
                        <p class="text-gray-500 text-center py-8">Hozircha vazifalar yo'q</p>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
@endsection