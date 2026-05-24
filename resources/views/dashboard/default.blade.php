@extends('layouts.dashboard-new')

@section('title', 'Dashboard - HEMIS')
@section('page-title', 'Dashboard')

@section('content')
<div class="min-h-screen">
    <!-- Navigation -->
    <nav class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <h1 class="text-xl font-semibold">HEMIS - Tourism Academy Samarkand</h1>
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
            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h2 class="text-2xl font-bold mb-4">Xush kelibsiz!</h2>
                    <p class="text-gray-600">Siz HEMIS tizimiga muvaffaqiyatli kirdingiz.</p>
                    
                    @if(Auth::user()->roles->count() > 0)
                        <div class="mt-4">
                            <p class="text-sm text-gray-500">Sizning rolingiz: 
                                <span class="font-semibold">{{ Auth::user()->roles->first()->name }}</span>
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </main>
</div>
@endsection