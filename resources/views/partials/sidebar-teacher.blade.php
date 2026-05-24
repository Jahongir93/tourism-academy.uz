<!-- Teacher Sidebar -->
<div class="bg-gray-900 text-gray-100 w-72 flex flex-col h-screen">
    <!-- Logo (Fixed Header) -->
    <div class="flex items-center space-x-2 px-4 py-4 border-b border-gray-800 flex-shrink-0">
        <svg class="w-8 h-8 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14v7"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l-9-5v7l9 5 9-5v-7z"/>
        </svg>
        <div>
            <span class="text-xl font-bold text-white">HEMIS</span>
            <p class="text-xs text-gray-400">Tourism Academy</p>
        </div>
    </div>

    <!-- Navigation (Scrollable) -->
    <nav class="flex-1 overflow-y-auto px-2 py-4 space-y-1">
        <!-- Dashboard -->
        <a href="{{ route('teacher.dashboard') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors duration-200
           {{ request()->routeIs('teacher.dashboard') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            <span>Bosh sahifa</span>
        </a>

        <!-- Dars jadvali -->
        <a href="{{ route('teacher.schedule') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors duration-200
           {{ request()->routeIs('teacher.schedule*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <span>Dars jadvali</span>
        </a>

        <!-- Fanlarim -->
        <a href="{{ route('teacher.subjects.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors duration-200
           {{ request()->routeIs('teacher.subjects.*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
            <span>Mening fanlarim</span>
        </a>

        <!-- Davomat -->
        <a href="{{ route('teacher.attendance.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors duration-200
           {{ request()->routeIs('teacher.attendance.*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
            </svg>
            <span>Davomat</span>
        </a>

        <!-- Baholar -->
        <a href="{{ route('teacher.grades.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors duration-200
           {{ request()->routeIs('teacher.grades.*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
            </svg>
            <span>Baholar</span>
        </a>

        <!-- Topshiriqlar -->
        <div x-data="{ open: {{ request()->routeIs('teacher.assignments.*') ? 'true' : 'false' }} }">
            <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-lg transition-colors duration-200 {{ request()->routeIs('teacher.assignments.*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                    </svg>
                    <span>Topshiriqlar</span>
                </div>
                <svg class="w-4 h-4 transition-transform duration-200 flex-shrink-0" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div x-show="open" x-transition class="mt-1 space-y-1 pl-11">
                <a href="{{ route('teacher.assignments.index') }}" class="block px-3 py-2 text-sm rounded-lg transition-colors duration-200 {{ request()->routeIs('teacher.assignments.index') ? 'bg-gray-700 text-white' : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                    <i class="fas fa-list w-4 mr-2"></i> Barcha topshiriqlar
                </a>
                <a href="{{ route('teacher.assignments.create') }}" class="block px-3 py-2 text-sm rounded-lg transition-colors duration-200 {{ request()->routeIs('teacher.assignments.create') ? 'bg-gray-700 text-white' : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                    <i class="fas fa-plus w-4 mr-2"></i> Yangi topshiriq
                </a>
                <a href="{{ route('teacher.assignments.pending') }}" class="block px-3 py-2 text-sm rounded-lg transition-colors duration-200 {{ request()->routeIs('teacher.assignments.pending') ? 'bg-gray-700 text-white' : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                    <i class="fas fa-clock w-4 mr-2"></i> Tekshirish kerak
                </a>
            </div>
        </div>

        <!-- Journal -->
        <a href="{{ route('teacher.journal.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors duration-200
           {{ request()->routeIs('teacher.journal.*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
            <span>Jurnal</span>
        </a>

        <!-- LMS Kurslar -->
        <div x-data="{ open: {{ request()->is('lms/*') ? 'true' : 'false' }} }">
            <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-lg transition-colors duration-200 {{ request()->is('lms/*') ? 'bg-purple-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M12 14l9-5-9-5-9 5 9 5z"/>
                        <path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"/>
                    </svg>
                    <span>LMS Kurslar</span>
                </div>
                <svg class="w-4 h-4 transition-transform duration-200 flex-shrink-0" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div x-show="open" x-transition class="mt-1 space-y-1 pl-11">
                <a href="{{ route('lms.courses.index') }}" class="block px-3 py-2 text-sm rounded-lg transition-colors duration-200 {{ request()->routeIs('lms.courses.index') ? 'bg-gray-700 text-white' : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                    <i class="fas fa-book-open w-4 mr-2"></i> Mening kurslarim
                </a>
                <a href="{{ route('lms.courses.create') }}" class="block px-3 py-2 text-sm rounded-lg transition-colors duration-200 {{ request()->routeIs('lms.courses.create') ? 'bg-gray-700 text-white' : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                    <i class="fas fa-plus-circle w-4 mr-2"></i> Yangi kurs yaratish
                </a>
                <a href="{{ route('lms.exams.index') }}" class="block px-3 py-2 text-sm rounded-lg transition-colors duration-200 {{ request()->routeIs('lms.exams.*') ? 'bg-gray-700 text-white' : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                    <i class="fas fa-file-alt w-4 mr-2"></i> Imtihonlar
                </a>
                <a href="{{ route('lms.library.index') }}" class="block px-3 py-2 text-sm rounded-lg transition-colors duration-200 {{ request()->routeIs('lms.library.*') ? 'bg-gray-700 text-white' : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                    <i class="fas fa-book w-4 mr-2"></i> E-kutubxona
                </a>
                <a href="{{ route('lms.dashboard') }}" class="block px-3 py-2 text-sm rounded-lg transition-colors duration-200 {{ request()->routeIs('lms.dashboard') ? 'bg-gray-700 text-white' : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                    <i class="fas fa-tachometer-alt w-4 mr-2"></i> LMS Dashboard
                </a>
            </div>
        </div>

        <!-- Online Ta'lim -->
        <a href="{{ route('teacher.materials.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors duration-200
           {{ request()->routeIs('teacher.materials.*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
            <span>Online Ta'lim</span>
        </a>

        <!-- Kurs Mavzulari -->
        <a href="{{ route('teacher.topics.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors duration-200
           {{ request()->routeIs('teacher.topics.*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <span>Kurs Mavzulari</span>
        </a>

        <!-- Imtihonlar -->
        <a href="{{ route('lms.exams.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors duration-200
           {{ request()->routeIs('lms.exams.*') ? 'bg-green-700 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <svg class="w-5 h-5 mr-3 flex-shrink-0 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
            </svg>
            <span>Imtihonlar</span>
        </a>

        <!-- Hisobotlar -->
        <a href="{{ route('teacher.reports') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors duration-200
           {{ request()->routeIs('teacher.reports') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <span>Hisobotlar</span>
        </a>

        <!-- Kutubxona -->
        <a href="{{ route('lms.library.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors duration-200
           {{ request()->routeIs('lms.library.*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/>
            </svg>
            <span>Kutubxona</span>
        </a>

        <!-- Profilim -->
        <a href="{{ route('teacher.profile') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors duration-200
           {{ request()->routeIs('teacher.profile*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            <span>Mening profilim</span>
        </a>

        <!-- Yordam -->
        <a href="{{ route('teacher.help') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors duration-200
           {{ request()->routeIs('teacher.help') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>Yordam</span>
        </a>
    </nav>

    <!-- Footer (Fixed) -->
    <div class="px-4 py-3 border-t border-gray-800 flex-shrink-0">
        <p class="text-xs text-gray-500">© 2024 HEMIS</p>
        <p class="text-xs text-gray-600">Tourism Academy Samarkand</p>
    </div>
</div>
