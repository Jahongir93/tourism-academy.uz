@extends('layouts.dashboard-new')

@section('title', 'Barcha bildirishnomalar')
@section('page-title', 'Barcha bildirishnomalar')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="bg-white rounded-lg shadow-sm">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-900">Bildirishnomalar</h3>
                        <div class="flex items-center space-x-4">
                            <span class="text-sm text-gray-600">
                                O'qilmagan: <span class="font-bold text-blue-600">{{ $notifications->where('is_read', false)->count() }}</span>
                            </span>
                            @if($notifications->where('is_read', false)->count() > 0)
                                <form action="{{ route('notifications.markAllRead') }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="px-4 py-2 bg-blue-500 text-white text-sm rounded hover:bg-blue-600">
                                        Hammasini o'qilgan deb belgilash
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    @if($notifications->count() > 0)
                        <div class="space-y-3">
                            @foreach($notifications as $notification)
                                <div class="notification-item {{ !$notification->is_read ? 'bg-blue-50' : 'bg-white' }} border rounded-lg p-4 hover:shadow-md transition-shadow">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <div class="flex items-center mb-2">
                                                @php
                                                    $iconClass = 'fa-bell';
                                                    $iconColor = 'text-gray-500';

                                                    switch($notification->type) {
                                                        case 'system':
                                                            $iconClass = 'fa-cog';
                                                            $iconColor = 'text-blue-500';
                                                            break;
                                                        case 'academic':
                                                            $iconClass = 'fa-graduation-cap';
                                                            $iconColor = 'text-green-500';
                                                            break;
                                                        case 'attendance':
                                                            $iconClass = 'fa-calendar-check';
                                                            $iconColor = 'text-orange-500';
                                                            break;
                                                        case 'grade':
                                                            $iconClass = 'fa-star';
                                                            $iconColor = 'text-yellow-500';
                                                            break;
                                                        case 'event':
                                                            $iconClass = 'fa-calendar';
                                                            $iconColor = 'text-purple-500';
                                                            break;
                                                        case 'message':
                                                            $iconClass = 'fa-envelope';
                                                            $iconColor = 'text-indigo-500';
                                                            break;
                                                    }
                                                @endphp
                                                <i class="fas {{ $iconClass }} {{ $iconColor }} mr-3"></i>
                                                <h4 class="font-semibold text-gray-900">{{ $notification->title }}</h4>
                                                @if(!$notification->is_read)
                                                    <span class="ml-2 px-2 py-1 text-xs bg-blue-500 text-white rounded">Yangi</span>
                                                @endif
                                            </div>
                                            <p class="text-gray-600 mb-2">{{ $notification->message }}</p>
                                            <div class="flex items-center text-xs text-gray-400">
                                                <i class="far fa-clock mr-1"></i>
                                                {{ $notification->created_at->diffForHumans() }}
                                                @if($notification->is_read && $notification->read_at)
                                                    <span class="ml-3">
                                                        <i class="far fa-check-circle mr-1"></i>
                                                        O'qilgan: {{ $notification->read_at->diffForHumans() }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="ml-4 flex items-center space-x-2">
                                            @if(!$notification->is_read)
                                                <form action="{{ route('notifications.read', $notification->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="text-blue-600 hover:text-blue-800" title="O'qilgan deb belgilash">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                            @endif
                                            <form action="{{ route('notifications.destroy', $notification->id) }}" method="POST" class="inline" onsubmit="return confirm('Bu bildirishnomani o\'chirmoqchimisiz?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-800" title="O'chirish">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        <div class="mt-6">
                            {{ $notifications->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <i class="fas fa-bell-slash text-6xl text-gray-300 mb-4"></i>
                            <p class="text-gray-500">Hozircha bildirishnomalar yo'q</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Auto-refresh every 30 seconds
    setInterval(function() {
        location.reload();
    }, 30000);
</script>
@endsection