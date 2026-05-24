<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

// Private channel for each conversation
Broadcast::channel('chat.conversation.{conversationId}', function ($user, $conversationId) {
    // Check if user is a participant in this conversation
    return \App\Models\ChatParticipant::where('conversation_id', $conversationId)
        ->where('user_id', $user->id)
        ->exists();
});

// Presence channel for online users
Broadcast::channel('chat.online', function ($user) {
    if ($user) {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'avatar' => $user->avatar ?? strtoupper(substr($user->name, 0, 1)),
        ];
    }
});
