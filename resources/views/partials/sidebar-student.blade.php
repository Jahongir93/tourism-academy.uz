<!-- Student Sidebar -->
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
        <a href="{{ route('student.dashboard') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors duration-200
           {{ request()->routeIs('student.dashboard') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            <span>Bosh sahifa</span>
        </a>

        <!-- Dars jadvali -->
        <a href="{{ route('student.schedule') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors duration-200
           {{ request()->routeIs('student.schedule') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <span>Dars jadvali</span>
        </a>

        <!-- Davomat -->
        <a href="{{ route('student.attendance.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors duration-200
           {{ request()->routeIs('student.attendance.*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
            </svg>
            <span>Davomat</span>
        </a>

        <!-- Fanlar -->
        <a href="{{ route('lms.courses.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors duration-200
           {{ request()->routeIs('lms.courses.*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
            <span>Mening fanlarim</span>
        </a>

        <!-- Topshiriqlar -->
        <a href="{{ route('student.assignments.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors duration-200
           {{ request()->routeIs('student.assignments.*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <span>Topshiriqlar</span>
        </a>

        <!-- Baholar -->
        <a href="{{ route('grades.all') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors duration-200
           {{ request()->routeIs('grades.*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
            <span>Baholar</span>
        </a>

        <!-- Online Ta'lim (LMS) -->
        <div x-data="{ open: {{ request()->is('lms*') ? 'true' : 'false' }} }">
            <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-lg transition-colors duration-200
                    {{ request()->is('lms*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    <span>Online Ta'lim</span>
                </div>
                <svg class="w-4 h-4 transition-transform duration-200 flex-shrink-0" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div x-show="open" x-transition class="mt-1 space-y-1 pl-11">
                <a href="{{ route('lms.materials.index') }}" class="block px-3 py-2 text-sm rounded-lg transition-colors duration-200
                   {{ request()->routeIs('lms.materials.*') ? 'bg-gray-700 text-white' : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                    <i class="fas fa-file-alt w-4 mr-2"></i> O'quv materiallari
                </a>
                <a href="{{ route('lms.videos.index') }}" class="block px-3 py-2 text-sm rounded-lg transition-colors duration-200
                   {{ request()->routeIs('lms.videos.*') ? 'bg-gray-700 text-white' : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                    <i class="fas fa-video w-4 mr-2"></i> Video darslar
                </a>
                <a href="{{ route('lms.tests.index') }}" class="block px-3 py-2 text-sm rounded-lg transition-colors duration-200
                   {{ request()->routeIs('lms.tests.*') ? 'bg-gray-700 text-white' : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                    <i class="fas fa-tasks w-4 mr-2"></i> Testlar
                </a>
                <a href="{{ route('lms.forum.index') }}" class="block px-3 py-2 text-sm rounded-lg transition-colors duration-200
                   {{ request()->routeIs('lms.forum.*') ? 'bg-gray-700 text-white' : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                    <i class="fas fa-comments w-4 mr-2"></i> Forum
                </a>
                <a href="{{ route('lms.library.index') }}" class="block px-3 py-2 text-sm rounded-lg transition-colors duration-200
                   {{ request()->routeIs('lms.library.*') ? 'bg-gray-700 text-white' : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                    <i class="fas fa-book w-4 mr-2"></i> E-kutubxona
                </a>
                <a href="{{ route('lms.progress') }}" class="block px-3 py-2 text-sm rounded-lg transition-colors duration-200
                   {{ request()->routeIs('lms.progress') ? 'bg-gray-700 text-white' : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                    <i class="fas fa-chart-line w-4 mr-2"></i> Mening yutuqlarim
                </a>
            </div>
        </div>

        <!-- Moliya -->
        <div x-data="{ open: {{ request()->routeIs('finance.*') ? 'true' : 'false' }} }">
            <button @click="open = !open" class="w-full flex items-center justify-between px-3 py-2 text-sm font-medium rounded-lg transition-colors duration-200
                    {{ request()->routeIs('finance.*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>To'lovlar</span>
                </div>
                <svg class="w-4 h-4 transition-transform duration-200 flex-shrink-0" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div x-show="open" x-transition class="mt-1 space-y-1 pl-11">
                <a href="{{ route('finance.payments.index') }}" class="block px-3 py-2 text-sm rounded-lg transition-colors duration-200
                   {{ request()->routeIs('finance.payments.*') ? 'bg-gray-700 text-white' : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                    <i class="fas fa-money-bill-wave w-4 mr-2"></i> Mening to'lovlarim
                </a>
                <a href="{{ route('finance.scholarships.index') }}" class="block px-3 py-2 text-sm rounded-lg transition-colors duration-200
                   {{ request()->routeIs('finance.scholarships.*') ? 'bg-gray-700 text-white' : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                    <i class="fas fa-graduation-cap w-4 mr-2"></i> Grant/Stipendiya
                </a>
            </div>
        </div>

        <!-- Profil -->
        <a href="{{ route('student.profile.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors duration-200
           {{ request()->routeIs('student.profile.*') && !request()->routeIs('student.documents.*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            <span>Mening profilim</span>
        </a>

        <!-- Hujjatlar -->
        <a href="{{ route('student.documents.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors duration-200
           {{ request()->routeIs('student.documents.*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <span>Hujjatlar</span>
        </a>

        <!-- Yordam -->
        <a href="{{ route('student.help') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors duration-200
           {{ request()->routeIs('student.help') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
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
