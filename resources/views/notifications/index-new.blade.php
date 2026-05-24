@extends('layouts.dashboard-new')

@section('title', 'Bildirishnomalar')
@section('page-title', 'Barcha bildirishnomalar')

@section('content')
<div class="bg-white rounded-lg shadow-sm p-6">
    <div class="mb-4 flex justify-between items-center">
        <h3 class="text-xl font-semibold">Bildirishnomalar</h3>
        @if(Auth::user()->notifications()->unread()->count() > 0)
            <form action="{{ route('notifications.markAllRead') }}" method="POST">
                @csrf
                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                    Hammasini o'qilgan deb belgilash
                </button>
            </form>
        @endif
    </div>

    @php
        $notifications = Auth::user()->notifications()->orderBy('created_at', 'desc')->paginate(20);
    @endphp

    @if($notifications->count() > 0)
        <div class="space-y-3">
            @foreach($notifications as $notification)
                <div class="border rounded p-4 {{ !$notification->is_read ? 'bg-blue-50' : '' }}">
                    <div class="flex justify-between">
                        <div>
                            <h4 class="font-semibold">{{ $notification->title }}</h4>
                            <p class="text-gray-600 text-sm mt-1">{{ $notification->message }}</p>
                            <p class="text-xs text-gray-400 mt-2">{{ $notification->created_at->diffForHumans() }}</p>
                        </div>
                        <div class="flex space-x-2">
                            @if(!$notification->is_read)
                                <form action="{{ route('notifications.read', $notification->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="text-blue-500 hover:text-blue-700">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </button>
                                </form>
                            @endif
                            <form action="{{ route('notifications.destroy', $notification->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $notifications->links() }}
        </div>
    @else
        <div class="text-center py-8 text-gray-500">
            Bildirishnomalar yo'q
        </div>
    @endif
</div>
@endsection