@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50">
    <!-- Hero Header -->
    <div class="bg-gradient-to-r from-blue-600 to-purple-600 text-white">
        <div class="container mx-auto px-4 py-12">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-4xl font-bold mb-2">Forum</h1>
                    <p class="text-blue-100">Bilim almashish va muloqot platformasi</p>
                </div>
                @auth
                <a href="{{ route('forum.create-topic') }}"
                   class="bg-white text-blue-600 px-6 py-3 rounded-xl font-semibold hover:bg-blue-50 transition-all transform hover:scale-105 shadow-lg">
                    <i class="fas fa-plus mr-2"></i>Yangi mavzu
                </a>
                @endauth
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 py-8">
        <div class="grid lg:grid-cols-4 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-3 space-y-6">
                <!-- Categories -->
                @forelse($categories as $category)
                <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all overflow-hidden">
                    <div class="p-6">
                        <div class="flex items-start justify-between">
                            <div class="flex items-center space-x-4">
                                <div class="w-14 h-14 rounded-xl bg-gradient-to-br {{ $category->color ?? 'from-blue-500 to-purple-500' }} flex items-center justify-center text-white text-2xl">
                                    <i class="{{ $category->icon ?? 'fas fa-comments' }}"></i>
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-gray-800 hover:text-blue-600 transition">
                                        <a href="{{ route('forum.category', $category->slug) }}">
                                            {{ $category->name }}
                                        </a>
                                    </h3>
                                    <p class="text-gray-600 text-sm mt-1">{{ $category->description }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-2xl font-bold text-gray-800">{{ $category->topics_count }}</div>
                                <div class="text-xs text-gray-500">Mavzular</div>
                            </div>
                        </div>

                        @if($category->latestTopic)
                        <div class="mt-4 pt-4 border-t border-gray-100">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($category->latestTopic->user->name) }}&background=random"
                                         class="w-8 h-8 rounded-full">
                                    <div>
                                        <p class="text-sm">
                                            <a href="{{ route('forum.topic', $category->latestTopic->slug) }}"
                                               class="font-semibold text-gray-800 hover:text-blue-600">
                                                {{ Str::limit($category->latestTopic->title, 50) }}
                                            </a>
                                        </p>
                                        <p class="text-xs text-gray-500">
                                            {{ $category->latestTopic->user->name }} •
                                            {{ $category->latestTopic->created_at->diffForHumans() }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                @empty
                <div class="bg-white rounded-2xl shadow-lg p-12 text-center">
                    <i class="fas fa-folder-open text-6xl text-gray-300 mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-700">Hozircha kategoriyalar yo'q</h3>
                    <p class="text-gray-500 mt-2">Tez orada yangi kategoriyalar qo'shiladi</p>
                </div>
                @endforelse
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Statistics -->
                <div class="bg-white rounded-2xl shadow-lg p-6">
                    <h3 class="font-bold text-gray-800 mb-4">
                        <i class="fas fa-chart-bar mr-2 text-blue-500"></i>Forum statistikasi
                    </h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Mavzular:</span>
                            <span class="font-bold text-gray-800">{{ number_format($stats['total_topics']) }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Javoblar:</span>
                            <span class="font-bold text-gray-800">{{ number_format($stats['total_posts']) }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Foydalanuvchilar:</span>
                            <span class="font-bold text-gray-800">{{ number_format($stats['total_members']) }}</span>
                        </div>
                    </div>
                    @if($stats['newest_member'])
                    <div class="mt-4 pt-4 border-t">
                        <p class="text-sm text-gray-600">Yangi a'zo:</p>
                        <p class="font-semibold text-gray-800">{{ $stats['newest_member']->name }}</p>
                    </div>
                    @endif
                </div>

                <!-- Popular Topics -->
                @if($popularTopics->count() > 0)
                <div class="bg-white rounded-2xl shadow-lg p-6">
                    <h3 class="font-bold text-gray-800 mb-4">
                        <i class="fas fa-fire mr-2 text-orange-500"></i>Ommabop mavzular
                    </h3>
                    <div class="space-y-3">
                        @foreach($popularTopics as $topic)
                        <div>
                            <a href="{{ route('forum.topic', $topic->slug) }}"
                               class="text-sm font-medium text-gray-700 hover:text-blue-600 line-clamp-2">
                                {{ $topic->title }}
                            </a>
                            <div class="flex items-center space-x-3 text-xs text-gray-500 mt-1">
                                <span><i class="fas fa-eye mr-1"></i>{{ number_format($topic->views) }}</span>
                                <span><i class="fas fa-comment mr-1"></i>{{ $topic->posts_count ?? 0 }}</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Recent Topics -->
                @if($recentTopics->count() > 0)
                <div class="bg-white rounded-2xl shadow-lg p-6">
                    <h3 class="font-bold text-gray-800 mb-4">
                        <i class="fas fa-clock mr-2 text-green-500"></i>Oxirgi mavzular
                    </h3>
                    <div class="space-y-3">
                        @foreach($recentTopics as $topic)
                        <div>
                            <a href="{{ route('forum.topic', $topic->slug) }}"
                               class="text-sm font-medium text-gray-700 hover:text-blue-600 line-clamp-2">
                                {{ $topic->title }}
                            </a>
                            <p class="text-xs text-gray-500 mt-1">
                                {{ $topic->user->name }} • {{ $topic->created_at->diffForHumans() }}
                            </p>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Quick Links -->
                <div class="bg-gradient-to-br from-blue-500 to-purple-600 rounded-2xl shadow-lg p-6 text-white">
                    <h3 class="font-bold mb-4">
                        <i class="fas fa-link mr-2"></i>Tezkor havolalar
                    </h3>
                    <div class="space-y-2">
                        <a href="{{ route('forum.rules') }}"
                           class="block p-2 bg-white/10 rounded-lg hover:bg-white/20 transition">
                            <i class="fas fa-gavel mr-2"></i>Forum qoidalari
                        </a>
                        <a href="{{ route('forum.search') }}"
                           class="block p-2 bg-white/10 rounded-lg hover:bg-white/20 transition">
                            <i class="fas fa-search mr-2"></i>Qidiruv
                        </a>
                        <a href="{{ route('forum.members') }}"
                           class="block p-2 bg-white/10 rounded-lg hover:bg-white/20 transition">
                            <i class="fas fa-users mr-2"></i>A'zolar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .line-clamp-2 {
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
    }
</style>
@endpush