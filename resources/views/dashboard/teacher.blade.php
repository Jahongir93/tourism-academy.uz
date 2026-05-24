@extends('layouts.dashboard-new')

@section('title', 'O\'qituvchi Dashboard')

@section('navigation')
    <a href="{{ route('teacher.dashboard') }}" class="flex items-center px-6 py-2 mt-4 text-gray-100 bg-blue-600 bg-opacity-25">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
        </svg>
        <span class="mx-3">Bosh sahifa</span>
    </a>
    
    <a href="#" class="flex items-center px-6 py-2 mt-4 text-gray-600 hover:bg-gray-200">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
        </svg>
        <span class="mx-3">Mening kurslarim</span>
    </a>
    
    <a href="{{ route('journal.index') }}" class="flex items-center px-6 py-2 mt-4 text-gray-600 hover:bg-gray-200">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
        </svg>
        <span class="mx-3">Elektron jurnal</span>
    </a>
    
    <a href="{{ route('schedule.index') }}" class="flex items-center px-6 py-2 mt-4 text-gray-600 hover:bg-gray-200">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        <span class="mx-3">Dars jadvali</span>
    </a>
    
    <a href="{{ route('assignments.index') }}" class="flex items-center px-6 py-2 mt-4 text-gray-600 hover:bg-gray-200">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
        </svg>
        <span class="mx-3">Topshiriqlar</span>
    </a>
    
    <a href="{{ route('students.index') }}" class="flex items-center px-6 py-2 mt-4 text-gray-600 hover:bg-gray-200">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
        </svg>
        <span class="mx-3">Talabalar</span>
    </a>
    
    <a href="#" class="flex items-center px-6 py-2 mt-4 text-gray-600 hover:bg-gray-200">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/>
        </svg>
        <span class="mx-3">Baholar</span>
    </a>
@endsection

@section('content')
    <h3 class="text-3xl font-semibold text-gray-700">O'qituvchi Dashboard</h3>
    
    <!-- Elektron jurnal tezkor kirish -->
    <div class="mt-6 bg-gradient-to-r from-indigo-500 to-purple-600 text-white rounded-lg shadow-lg p-6 mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-xl font-bold mb-2">Elektron jurnal tizimi</h2>
                <p class="text-indigo-100 text-sm">Davomat belgilash, baholarni kiritish va topshiriqlar berish</p>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('journal.index') }}" class="bg-white text-indigo-600 px-4 py-2 rounded-lg text-sm font-semibold hover:bg-indigo-50 transition">
                    <i class="fas fa-book mr-1"></i> Jurnallar
                </a>
                <a href="{{ route('schedule.index') }}" class="bg-white text-purple-600 px-4 py-2 rounded-lg text-sm font-semibold hover:bg-purple-50 transition">
                    <i class="fas fa-calendar mr-1"></i> Jadval
                </a>
                <a href="{{ route('assignments.index') }}" class="bg-white text-pink-600 px-4 py-2 rounded-lg text-sm font-semibold hover:bg-pink-50 transition">
                    <i class="fas fa-tasks mr-1"></i> Topshiriqlar
                </a>
            </div>
        </div>
    </div>
    
    <div class="mt-8">
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
            <div class="px-6 py-6 bg-white rounded-lg shadow-sm">
                <div class="flex items-center">
                    <div class="p-3 bg-blue-600 bg-opacity-75 rounded-full">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <div class="mx-4">
                        <h4 class="text-2xl font-semibold text-gray-700">{{ \App\Models\JournalEntry::where('teacher_id', auth()->id())->count() ?? 0 }}</h4>
                        <div class="text-gray-500">Faol jurnallar</div>
                    </div>
                </div>
            </div>
            
            <div class="px-6 py-6 bg-white rounded-lg shadow-sm">
                <div class="flex items-center">
                    <div class="p-3 bg-green-600 bg-opacity-75 rounded-full">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <div class="mx-4">
                        <h4 class="text-2xl font-semibold text-gray-700">0</h4>
                        <div class="text-gray-500">Jami talabalar</div>
                    </div>
                </div>
            </div>
            
            <div class="px-6 py-6 bg-white rounded-lg shadow-sm">
                <div class="flex items-center">
                    <div class="p-3 bg-yellow-600 bg-opacity-75 rounded-full">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <div class="mx-4">
                        <h4 class="text-2xl font-semibold text-gray-700">0</h4>
                        <div class="text-gray-500">Topshiriqlar</div>
                    </div>
                </div>
            </div>
            
            <div class="px-6 py-6 bg-white rounded-lg shadow-sm">
                <div class="flex items-center">
                    <div class="p-3 bg-purple-600 bg-opacity-75 rounded-full">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div class="mx-4">
                        <h4 class="text-2xl font-semibold text-gray-700">0</h4>
                        <div class="text-gray-500">Bugungi darslar</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="mt-8 grid grid-cols-1 gap-6 lg:grid-cols-2">
        <div class="bg-white rounded-lg shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-700">Bugungi dars jadvali</h3>
            </div>
            <div class="p-6">
                <p class="text-gray-500 text-center py-8">Bugun darslar yo'q</p>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-700">So'nggi topshiriqlar</h3>
            </div>
            <div class="p-6">
                <p class="text-gray-500 text-center py-8">Hozircha topshiriqlar yo'q</p>
            </div>
        </div>
    </div>
@endsection