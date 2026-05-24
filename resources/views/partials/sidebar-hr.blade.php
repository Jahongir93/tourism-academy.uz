<div class="w-64 bg-gray-900 min-h-screen flex flex-col">
    <!-- Logo -->
    <div class="px-4 py-5 border-b border-gray-800">
        <a href="{{ route('hr.dashboard') }}" class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-pink-600 rounded-lg flex items-center justify-center">
                <i class="fas fa-users-cog text-white text-lg"></i>
            </div>
            <div>
                <span class="text-white font-bold text-lg">HR Panel</span>
                <p class="text-gray-500 text-xs">Kadrlar bo'limi</p>
            </div>
        </a>
    </div>

    <!-- User Info -->
    <div class="px-4 py-4 border-b border-gray-800">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-gradient-to-br from-purple-400 to-pink-500 rounded-full flex items-center justify-center">
                <span class="text-white font-semibold">{{ substr(auth()->user()->name ?? 'HR', 0, 1) }}</span>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-white truncate">{{ auth()->user()->name ?? 'HR Manager' }}</p>
                <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email ?? '' }}</p>
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
        <!-- Dashboard -->
        <a href="{{ route('hr.dashboard') }}" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200
           {{ request()->routeIs('hr.dashboard') ? 'bg-gradient-to-r from-purple-600 to-pink-600 text-white shadow-lg' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            <span>Bosh sahifa</span>
        </a>

        <!-- Xodimlar Boshqaruvi -->
        <div x-data="{ open: {{ request()->routeIs('hr.employees.*') ? 'true' : 'false' }} }">
            <button @click="open = !open" class="flex items-center justify-between w-full px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200
                {{ request()->routeIs('hr.employees.*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    <span>Xodimlar</span>
                </div>
                <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div x-show="open" x-collapse class="mt-1 ml-4 pl-4 border-l border-gray-700 space-y-1">
                <a href="{{ route('hr.employees.index') }}" class="block px-3 py-2 text-sm rounded-lg transition-colors duration-200
                   {{ request()->routeIs('hr.employees.index') ? 'bg-purple-600 text-white' : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                    <i class="fas fa-list w-4 mr-2"></i> Xodimlar ro'yxati
                </a>
                <a href="{{ route('hr.employees.create') }}" class="block px-3 py-2 text-sm rounded-lg transition-colors duration-200
                   {{ request()->routeIs('hr.employees.create') ? 'bg-purple-600 text-white' : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                    <i class="fas fa-user-plus w-4 mr-2"></i> Yangi xodim
                </a>
                <a href="{{ route('hr.employees.contracts') }}" class="block px-3 py-2 text-sm rounded-lg transition-colors duration-200
                   {{ request()->routeIs('hr.employees.contracts') ? 'bg-purple-600 text-white' : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                    <i class="fas fa-file-contract w-4 mr-2"></i> Shartnomalar
                </a>
            </div>
        </div>

        <!-- Ishga qabul -->
        <div x-data="{ open: {{ request()->routeIs('hr.recruitment.*') ? 'true' : 'false' }} }">
            <button @click="open = !open" class="flex items-center justify-between w-full px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200
                {{ request()->routeIs('hr.recruitment.*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                    </svg>
                    <span>Ishga qabul</span>
                </div>
                <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div x-show="open" x-collapse class="mt-1 ml-4 pl-4 border-l border-gray-700 space-y-1">
                <a href="{{ route('hr.recruitment.vacancies') }}" class="block px-3 py-2 text-sm rounded-lg transition-colors duration-200
                   {{ request()->routeIs('hr.recruitment.vacancies') ? 'bg-green-600 text-white' : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                    <i class="fas fa-briefcase w-4 mr-2"></i> Vakansiyalar
                </a>
                <a href="{{ route('hr.recruitment.applications') }}" class="block px-3 py-2 text-sm rounded-lg transition-colors duration-200
                   {{ request()->routeIs('hr.recruitment.applications') ? 'bg-green-600 text-white' : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                    <i class="fas fa-file-alt w-4 mr-2"></i> Arizalar
                </a>
                <a href="{{ route('hr.recruitment.interviews') }}" class="block px-3 py-2 text-sm rounded-lg transition-colors duration-200
                   {{ request()->routeIs('hr.recruitment.interviews') ? 'bg-green-600 text-white' : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                    <i class="fas fa-comments w-4 mr-2"></i> Suhbatlar
                </a>
            </div>
        </div>

        <!-- Ta'til va Dam olish -->
        <div x-data="{ open: {{ request()->routeIs('hr.leave.*') ? 'true' : 'false' }} }">
            <button @click="open = !open" class="flex items-center justify-between w-full px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200
                {{ request()->routeIs('hr.leave.*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span>Ta'til/Dam olish</span>
                </div>
                <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div x-show="open" x-collapse class="mt-1 ml-4 pl-4 border-l border-gray-700 space-y-1">
                <a href="{{ route('hr.leave.requests') }}" class="block px-3 py-2 text-sm rounded-lg transition-colors duration-200
                   {{ request()->routeIs('hr.leave.requests') ? 'bg-blue-600 text-white' : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                    <i class="fas fa-envelope-open-text w-4 mr-2"></i> Arizalar
                </a>
                <a href="{{ route('hr.leave.calendar') }}" class="block px-3 py-2 text-sm rounded-lg transition-colors duration-200
                   {{ request()->routeIs('hr.leave.calendar') ? 'bg-blue-600 text-white' : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                    <i class="fas fa-calendar-alt w-4 mr-2"></i> Kalendar
                </a>
                <a href="{{ route('hr.leave.balances') }}" class="block px-3 py-2 text-sm rounded-lg transition-colors duration-200
                   {{ request()->routeIs('hr.leave.balances') ? 'bg-blue-600 text-white' : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                    <i class="fas fa-balance-scale w-4 mr-2"></i> Ta'til balansi
                </a>
            </div>
        </div>

        <!-- Davomat -->
        <a href="{{ route('hr.attendance.index') }}" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200
           {{ request()->routeIs('hr.attendance.*') ? 'bg-gradient-to-r from-orange-500 to-amber-500 text-white shadow-lg' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <svg class="w-5 h-5 mr-3 flex-shrink-0 {{ request()->routeIs('hr.attendance.*') ? 'text-white' : 'text-orange-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>Davomat</span>
        </a>

        <!-- Ish haqi -->
        <a href="{{ route('hr.payroll.index') }}" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200
           {{ request()->routeIs('hr.payroll.*') ? 'bg-gradient-to-r from-emerald-500 to-green-500 text-white shadow-lg' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <svg class="w-5 h-5 mr-3 flex-shrink-0 {{ request()->routeIs('hr.payroll.*') ? 'text-white' : 'text-emerald-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            <span>Ish haqi</span>
        </a>

        <!-- Treninglar -->
        <a href="{{ route('hr.training.index') }}" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200
           {{ request()->routeIs('hr.training.*') ? 'bg-gradient-to-r from-indigo-500 to-purple-500 text-white shadow-lg' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <svg class="w-5 h-5 mr-3 flex-shrink-0 {{ request()->routeIs('hr.training.*') ? 'text-white' : 'text-indigo-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
            </svg>
            <span>Treninglar</span>
        </a>

        <!-- Hujjatlar -->
        <a href="{{ route('hr.documents.index') }}" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200
           {{ request()->routeIs('hr.documents.*') ? 'bg-gradient-to-r from-cyan-500 to-blue-500 text-white shadow-lg' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <svg class="w-5 h-5 mr-3 flex-shrink-0 {{ request()->routeIs('hr.documents.*') ? 'text-white' : 'text-cyan-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <span>Hujjatlar</span>
        </a>

        <!-- Hisobotlar -->
        <div x-data="{ open: {{ request()->routeIs('hr.reports.*') ? 'true' : 'false' }} }">
            <button @click="open = !open" class="flex items-center justify-between w-full px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200
                {{ request()->routeIs('hr.reports.*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-3 flex-shrink-0 text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span>Hisobotlar</span>
                </div>
                <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div x-show="open" x-collapse class="mt-1 ml-4 pl-4 border-l border-gray-700 space-y-1">
                <a href="{{ route('hr.reports.employees') }}" class="block px-3 py-2 text-sm rounded-lg transition-colors duration-200
                   {{ request()->routeIs('hr.reports.employees') ? 'bg-pink-600 text-white' : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                    <i class="fas fa-users w-4 mr-2"></i> Xodimlar hisoboti
                </a>
                <a href="{{ route('hr.reports.attendance') }}" class="block px-3 py-2 text-sm rounded-lg transition-colors duration-200
                   {{ request()->routeIs('hr.reports.attendance') ? 'bg-pink-600 text-white' : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                    <i class="fas fa-clock w-4 mr-2"></i> Davomat hisoboti
                </a>
                <a href="{{ route('hr.reports.leave') }}" class="block px-3 py-2 text-sm rounded-lg transition-colors duration-200
                   {{ request()->routeIs('hr.reports.leave') ? 'bg-pink-600 text-white' : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                    <i class="fas fa-calendar-minus w-4 mr-2"></i> Ta'til hisoboti
                </a>
                <a href="{{ route('hr.reports.turnover') }}" class="block px-3 py-2 text-sm rounded-lg transition-colors duration-200
                   {{ request()->routeIs('hr.reports.turnover') ? 'bg-pink-600 text-white' : 'text-gray-400 hover:bg-gray-700 hover:text-white' }}">
                    <i class="fas fa-exchange-alt w-4 mr-2"></i> Kadrlar almashinuvi
                </a>
            </div>
        </div>

        <!-- Sozlamalar -->
        <a href="{{ route('hr.settings') }}" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all duration-200
           {{ request()->routeIs('hr.settings') ? 'bg-gradient-to-r from-gray-600 to-gray-700 text-white shadow-lg' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <span>Sozlamalar</span>
        </a>
    </nav>

    <!-- Footer -->
    <div class="px-4 py-3 border-t border-gray-800 flex-shrink-0">
        <p class="text-xs text-gray-500">HR Management System</p>
        <p class="text-xs text-gray-600">Tourism Academy Samarkand</p>
    </div>
</div>
