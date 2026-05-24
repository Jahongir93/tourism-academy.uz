<a href="{{ route('dashboard') }}" class="flex items-center px-6 py-2 mt-4 text-gray-600 hover:bg-gray-200 {{ request()->routeIs('dashboard') ? 'bg-blue-600 bg-opacity-25 text-gray-100' : '' }}">
    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
    </svg>
    <span class="mx-3">Bosh sahifa</span>
</a>

<div class="px-6 py-2 mt-6">
    <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Sozlamalar</h3>
</div>

<a href="{{ route('admin.settings.users.index') }}" class="flex items-center px-6 py-2 mt-2 text-gray-600 hover:bg-gray-200 {{ request()->routeIs('admin.settings.users*') ? 'bg-blue-600 bg-opacity-25 text-gray-100' : '' }}">
    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
    </svg>
    <span class="mx-3">Foydalanuvchilar</span>
</a>

<a href="{{ route('admin.settings.roles.index') }}" class="flex items-center px-6 py-2 mt-2 text-gray-600 hover:bg-gray-200 {{ request()->routeIs('admin.settings.roles*') ? 'bg-blue-600 bg-opacity-25 text-gray-100' : '' }}">
    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
    </svg>
    <span class="mx-3">Rollar</span>
</a>

<a href="{{ route('admin.settings.permissions.index') }}" class="flex items-center px-6 py-2 mt-2 text-gray-600 hover:bg-gray-200 {{ request()->routeIs('admin.settings.permissions*') ? 'bg-blue-600 bg-opacity-25 text-gray-100' : '' }}">
    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
    </svg>
    <span class="mx-3">Ruxsatlar</span>
</a>

<a href="{{ route('admin.settings.modules.index') }}" class="flex items-center px-6 py-2 mt-2 text-gray-600 hover:bg-gray-200 {{ request()->routeIs('admin.settings.modules*') ? 'bg-blue-600 bg-opacity-25 text-gray-100' : '' }}">
    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
    </svg>
    <span class="mx-3">Modul ko'rinishi</span>
</a>

<a href="{{ route('admin.settings.otp.index') }}" class="flex items-center px-6 py-2 mt-2 text-gray-600 hover:bg-gray-200 {{ request()->routeIs('admin.settings.otp*') ? 'bg-blue-600 bg-opacity-25 text-gray-100' : '' }}">
    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
    </svg>
    <span class="mx-3">OTP Sozlamalari</span>
</a>

<div class="px-6 py-2 mt-6">
    <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Tizim</h3>
</div>

<a href="{{ route('students.index') }}" class="flex items-center px-6 py-2 mt-2 text-gray-600 hover:bg-gray-200 {{ request()->routeIs('students*') ? 'bg-blue-600 bg-opacity-25 text-gray-100' : '' }}">
    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
    </svg>
    <span class="mx-3">Talabalar</span>
</a>

<a href="{{ route('employees.teachers') }}" class="flex items-center px-6 py-2 mt-2 text-gray-600 hover:bg-gray-200 {{ request()->routeIs('employees.teachers*') ? 'bg-blue-600 bg-opacity-25 text-gray-100' : '' }}">
    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
    </svg>
    <span class="mx-3">O'qituvchilar</span>
</a>

<a href="{{ route('statistics.index') }}" class="flex items-center px-6 py-2 mt-2 text-gray-600 hover:bg-gray-200 {{ request()->routeIs('statistics*') ? 'bg-blue-600 bg-opacity-25 text-gray-100' : '' }}">
    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
    </svg>
    <span class="mx-3">Statistika</span>
</a>
