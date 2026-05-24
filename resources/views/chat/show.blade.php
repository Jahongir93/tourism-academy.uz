<div id="chatContent">
    <!-- Chat header -->
    <div class="bg-white border-b px-6 py-4 flex items-center">
        <div class="flex items-center">
            <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center mr-3">
                @php
                    $otherUser = $conversation->users()->where('users.id', '!=', auth()->id())->first();
                @endphp
                @if($otherUser && $otherUser->avatar)
                    <img src="{{ $otherUser->avatar }}" alt="" class="w-full h-full rounded-full object-cover">
                @else
                    <i class="fas fa-user text-gray-600"></i>
                @endif
            </div>
            <div>
                <h2 class="font-semibold text-gray-800">
                    {{ $conversation->getNameForUser(auth()->id()) }}
                </h2>
                <p class="text-xs text-gray-500">
                    @if($otherUser && $otherUser->last_seen_at)
                        Oxirgi faollik: {{ $otherUser->last_seen_at->diffForHumans() }}
                    @else
                        Online
                    @endif
                </p>
            </div>
        </div>
        <div class="ml-auto flex space-x-2">
            <button class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-phone"></i>
            </button>
            <button class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-video"></i>
            </button>
            <button class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-ellipsis-v"></i>
            </button>
        </div>
    </div>

    <!-- Messages -->
    <div class="messages-container custom-scrollbar px-6 py-4 bg-gray-50" id="messagesContainer">
        @foreach($messages as $message)
            <div class="flex {{ $message->user_id == auth()->id() ? 'justify-end' : 'justify-start' }} mb-3"
                 data-message-id="{{ $message->id }}">
                <div class="message-bubble px-4 py-2 rounded-lg {{ $message->user_id == auth()->id() ? 'message-own' : 'message-other' }}">
                    @if($message->user_id != auth()->id())
                        <div class="text-xs font-semibold mb-1">{{ $message->user->name }}</div>
                    @endif
                    <div>{{ $message->message }}</div>
                    <div class="text-xs {{ $message->user_id == auth()->id() ? 'text-green-100' : 'text-gray-500' }} mt-1 flex items-center">
                        <span>{{ $message->formatted_time }}</span>
                        @if($message->user_id == auth()->id())
                            <i class="fas fa-check-double ml-2 {{ $message->is_read ? '' : 'opacity-50' }}"></i>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Message input -->
    <div class="bg-white border-t px-6 py-4">
        <div class="flex items-center space-x-2">
            <button class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-paperclip"></i>
            </button>
            <input type="text" id="messageInput"
                class="flex-1 px-4 py-2 border rounded-lg focus:outline-none focus:border-green-500"
                placeholder="Xabar yozing..."
                onkeypress="if(event.key === 'Enter') sendMessage()">
            <button onclick="sendMessage()"
                class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
                <i class="fas fa-paper-plane"></i>
            </button>
        </div>
    </div>
</div>