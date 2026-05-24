@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-blue-50">
    <!-- Header -->
    <div class="bg-white shadow-lg border-b-4" style="border-color: #0066CC;">
        <div class="container mx-auto px-4 py-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="p-3 rounded-xl shadow-lg" style="background: linear-gradient(135deg, #0066CC 0%, #0052A3 100%);">
                        <i class="fas fa-book-open text-3xl text-white"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-800">
                            Elektron kutubxona
                        </h1>
                        <p class="text-gray-600">O'quv materiallari va elektron resurslar</p>
                    </div>
                </div>
                <a href="{{ route('home') }}" class="bg-gradient-to-r from-gray-100 to-gray-200 hover:from-gray-200 hover:to-gray-300 px-6 py-3 rounded-xl text-gray-700 font-semibold transition-all transform hover:scale-105 shadow-md">
                    <i class="fas fa-arrow-left mr-2"></i>Bosh sahifa
                </a>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 py-8">
        <div class="grid lg:grid-cols-4 gap-6">
            <!-- Sidebar -->
            <div class="lg:col-span-1 space-y-4">
                <!-- Search -->
                <div class="bg-white rounded-2xl shadow-xl p-5 border border-blue-100">
                    <div class="flex items-center mb-4">
                        <div class="bg-blue-100 p-2 rounded-lg mr-3">
                            <i class="fas fa-search" style="color: #0066CC;"></i>
                        </div>
                        <h3 class="font-bold text-gray-800">Qidirish</h3>
                    </div>
                    <form method="GET" action="{{ route('public.library') }}">
                        <div class="relative">
                            <input type="text"
                                   name="search"
                                   value="{{ request('search') }}"
                                   placeholder="Kitob, muallif..."
                                   class="w-full pl-10 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:outline-none transition"
                                   style="focus:border-color: #0066CC;">
                            <i class="fas fa-search absolute left-3 top-4 text-gray-400"></i>
                        </div>
                        <button type="submit" class="w-full mt-3 text-white py-3 rounded-xl font-semibold hover:shadow-lg transform hover:scale-[1.02] transition-all" style="background: linear-gradient(135deg, #0066CC 0%, #0052A3 100%);">
                            Qidirish
                        </button>
                    </form>
                </div>

                <!-- Categories -->
                <div class="bg-white rounded-2xl shadow-xl p-5 border border-blue-100">
                    <div class="flex items-center mb-4">
                        <div class="bg-blue-100 p-2 rounded-lg mr-3">
                            <i class="fas fa-layer-group" style="color: #0066CC;"></i>
                        </div>
                        <h3 class="font-bold text-gray-800">Kategoriyalar</h3>
                    </div>
                    <div class="space-y-2 max-h-96 overflow-y-auto custom-scrollbar">
                        <a href="{{ route('public.library') }}"
                           class="flex items-center justify-between px-4 py-3 rounded-xl transition-all {{ !request('category_id') ? 'text-white shadow-lg' : 'hover:bg-blue-50 text-gray-700' }}"
                           style="{{ !request('category_id') ? 'background: linear-gradient(135deg, #0066CC 0%, #0052A3 100%);' : '' }}">
                            <span class="flex items-center">
                                <i class="fas fa-globe mr-3"></i>
                                Barcha kitoblar
                            </span>
                            @if(isset($stats['total_books']))
                                <span class="bg-white/20 px-2 py-1 rounded-lg text-xs font-bold">{{ $stats['total_books'] }}</span>
                            @endif
                        </a>

                        @if($categories->count() > 0)
                            @foreach($categories as $category)
                                <a href="{{ route('public.library', ['category_id' => $category->id]) }}"
                                   class="flex items-center justify-between px-4 py-3 rounded-xl transition-all {{ request('category_id') == $category->id ? 'text-white shadow-lg' : 'hover:bg-blue-50 text-gray-700' }}"
                                   style="{{ request('category_id') == $category->id ? 'background: linear-gradient(135deg, #0066CC 0%, #0052A3 100%);' : '' }}">
                                    <span class="flex items-center">
                                        <i class="fas fa-bookmark mr-3"></i>
                                        {{ $category->name }}
                                    </span>
                                    <span class="{{ request('category_id') == $category->id ? 'bg-white/20' : 'bg-blue-100' }} px-2 py-1 rounded-lg text-xs font-bold" style="{{ request('category_id') != $category->id ? 'color: #0066CC;' : '' }}">
                                        {{ $category->books_count ?? 0 }}
                                    </span>
                                </a>
                            @endforeach
                        @endif
                    </div>
                </div>

                <!-- Statistics -->
                @if(isset($stats) && $stats['total_books'] > 0)
                <div class="rounded-2xl shadow-xl p-5 text-white" style="background: linear-gradient(135deg, #0066CC 0%, #0052A3 100%);">
                    <div class="flex items-center mb-4">
                        <div class="bg-white/20 p-2 rounded-lg mr-3">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h3 class="font-bold">Statistika</h3>
                    </div>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center bg-white/10 rounded-lg px-3 py-2">
                            <span class="flex items-center">
                                <i class="fas fa-books mr-2 text-blue-200"></i>
                                Jami
                            </span>
                            <span class="font-bold text-lg">{{ $stats['total_books'] }}</span>
                        </div>
                        <div class="flex justify-between items-center bg-white/10 rounded-lg px-3 py-2">
                            <span class="flex items-center">
                                <i class="fas fa-sparkles mr-2 text-yellow-200"></i>
                                Yangi
                            </span>
                            <span class="font-bold text-lg">{{ $stats['new_books'] }}</span>
                        </div>
                        <div class="flex justify-between items-center bg-white/10 rounded-lg px-3 py-2">
                            <span class="flex items-center">
                                <i class="fas fa-download mr-2 text-blue-200"></i>
                                Yuklanmalar
                            </span>
                            <span class="font-bold text-lg">{{ number_format($stats['total_downloads']) }}</span>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Main Content -->
            <div class="lg:col-span-3">
                @if($books instanceof \Illuminate\Pagination\LengthAwarePaginator && $books->count() > 0)
                    <!-- Books Grid -->
                    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-5">
                        @foreach($books as $book)
                            <div class="bg-white rounded-2xl shadow-xl overflow-hidden hover:shadow-2xl transition-all transform hover:scale-[1.02] group">
                                <div class="relative h-72 bg-gradient-to-br from-blue-100 to-blue-100">
                                    @if($book->cover_image)
                                        <img src="{{ asset('storage/' . $book->cover_image) }}"
                                             alt="{{ $book->title }}"
                                             class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center">
                                            <div class="text-center">
                                                <i class="fas fa-book text-6xl text-blue-300 mb-3"></i>
                                                <p class="font-semibold" style="color: #0066CC;">{{ Str::limit($book->title, 20) }}</p>
                                            </div>
                                        </div>
                                    @endif

                                    @if($book->book_type)
                                        <div class="absolute top-3 right-3 text-white px-3 py-1 rounded-full text-xs font-bold shadow-lg" style="background: linear-gradient(135deg, #0066CC 0%, #0052A3 100%);">
                                            {{ strtoupper($book->book_type) }}
                                        </div>
                                    @endif

                                    <!-- Hover overlay -->
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-all flex items-end p-4">
                                        <div class="w-full space-y-2">
                                            <a href="{{ route('public.library.show', $book->id) }}"
                                               class="block w-full bg-white text-gray-800 py-2 rounded-xl font-semibold hover:bg-gray-100 text-center transition">
                                                <i class="fas fa-eye mr-2"></i>Ko'rish
                                            </a>
                                            @auth
                                                @if($book->allow_download)
                                                    <a href="{{ route('public.library.download', $book->id) }}"
                                                       class="block w-full text-white py-2 rounded-xl font-semibold hover:shadow-lg text-center transition"
                                                       style="background: linear-gradient(135deg, #0066CC 0%, #0052A3 100%);">
                                                        <i class="fas fa-download mr-2"></i>Yuklab olish
                                                    </a>
                                                @endif
                                            @else
                                                <a href="{{ route('login') }}"
                                                   class="block w-full bg-gray-600 text-white py-2 rounded-xl font-semibold hover:bg-gray-700 text-center transition">
                                                    <i class="fas fa-lock mr-2"></i>Kirish
                                                </a>
                                            @endauth
                                        </div>
                                    </div>
                                </div>

                                <div class="p-5">
                                    <h3 class="font-bold text-gray-800 text-lg mb-1 line-clamp-1">{{ $book->title }}</h3>
                                    <p class="text-gray-600 text-sm mb-3 flex items-center">
                                        <i class="fas fa-user-edit mr-2" style="color: #0066CC;"></i>
                                        {{ $book->author }}
                                    </p>

                                    <div class="flex items-center justify-between text-xs text-gray-500 mb-3">
                                        <span class="flex items-center">
                                            <i class="fas fa-calendar-alt mr-1 text-blue-400"></i>
                                            {{ $book->publication_year ?? date('Y') }}
                                        </span>
                                        @if($book->pages)
                                            <span class="flex items-center">
                                                <i class="fas fa-file-alt mr-1 text-blue-400"></i>
                                                {{ $book->pages }} bet
                                            </span>
                                        @endif
                                    </div>

                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            @if($book->rating)
                                                <div class="flex items-center bg-yellow-50 px-2 py-1 rounded-lg">
                                                    <i class="fas fa-star text-yellow-400 mr-1"></i>
                                                    <span class="text-sm font-bold text-gray-700">{{ number_format($book->rating, 1) }}</span>
                                                </div>
                                            @endif
                                        </div>
                                        @if($book->download_count > 0)
                                            <span class="flex items-center text-gray-600 bg-gray-100 px-2 py-1 rounded-lg">
                                                <i class="fas fa-download mr-1 text-xs" style="color: #0066CC;"></i>
                                                <span class="text-xs font-semibold">{{ number_format($book->download_count) }}</span>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="mt-8">
                        {{ $books->withQueryString()->links() }}
                    </div>

                @else
                    <!-- Empty State -->
                    <div class="bg-white rounded-2xl shadow-xl p-16">
                        <div class="text-center">
                            <div class="mb-8">
                                <div class="bg-gradient-to-br from-blue-100 to-blue-100 w-32 h-32 rounded-full mx-auto flex items-center justify-center">
                                    <i class="fas fa-book-open text-5xl" style="color: #0066CC;"></i>
                                </div>
                            </div>
                            <h2 class="text-3xl font-bold text-gray-800 mb-4">
                                Hozircha adabiyotlar mavjud emas
                            </h2>
                            <p class="text-gray-600 text-lg mb-8 max-w-md mx-auto">
                                Elektron kutubxonaga kitoblar tez orada yuklanadi.
                                Iltimos, keyinroq qayta tashrif buyuring.
                            </p>

                            @if(request('search'))
                                <div class="bg-gray-50 rounded-xl p-4 mb-6 max-w-md mx-auto">
                                    <p class="text-gray-700">
                                        <i class="fas fa-search mr-2 text-gray-400"></i>
                                        "<strong style="color: #0066CC;">{{ request('search') }}</strong>" bo'yicha natija topilmadi
                                    </p>
                                </div>
                                <a href="{{ route('public.library') }}"
                                   class="inline-flex items-center px-6 py-3 text-white rounded-xl font-semibold hover:shadow-lg transform hover:scale-105 transition-all"
                                   style="background: linear-gradient(135deg, #0066CC 0%, #0052A3 100%);">
                                    <i class="fas fa-times mr-2"></i>Tozalash
                                </a>
                            @else
                                <div class="flex justify-center space-x-4">
                                    <a href="{{ route('home') }}"
                                       class="inline-flex items-center px-6 py-3 bg-gray-200 text-gray-700 rounded-xl font-semibold hover:bg-gray-300 transform hover:scale-105 transition-all">
                                        <i class="fas fa-home mr-2"></i>Bosh sahifa
                                    </a>
                                    @auth
                                        @if(auth()->user()->hasRole(['Admin', 'SuperAdmin', 'Teacher']))
                                            <a href="{{ route('lms.library.create') }}"
                                               class="inline-flex items-center px-6 py-3 text-white rounded-xl font-semibold hover:shadow-lg transform hover:scale-105 transition-all"
                                               style="background: linear-gradient(135deg, #0066CC 0%, #0052A3 100%);">
                                                <i class="fas fa-plus mr-2"></i>Kitob qo'shish
                                            </a>
                                        @endif
                                    @endauth
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: #eff6ff;
        border-radius: 10px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: linear-gradient(to bottom, #0066CC, #0052A3);
        border-radius: 10px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(to bottom, #0052A3, #004080);
    }
    .line-clamp-1 {
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 1;
        -webkit-box-orient: vertical;
    }
</style>
@endpush