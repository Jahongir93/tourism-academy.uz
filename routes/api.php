<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\Api\ChatApiController;
use App\Http\Controllers\TelegramController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// ===== MODERN CHAT API ROUTES (FOR POPUP) =====
Route::middleware(['web', 'auth'])->prefix('chat')->group(function () {
    Route::get('/conversations', [ChatApiController::class, 'getConversations'])->name('api.chat.conversations');
    Route::get('/users', [ChatApiController::class, 'getUsers'])->name('api.chat.users');
    Route::post('/conversation', [ChatApiController::class, 'getOrCreateConversation'])->name('api.chat.conversation');
    Route::get('/conversation/{conversationId}/messages', [ChatApiController::class, 'getMessages'])->name('api.chat.messages');
    Route::post('/conversation/{conversationId}/send', [ChatApiController::class, 'sendMessage'])->name('api.chat.send');
    Route::post('/conversation/{conversationId}/upload', [ChatApiController::class, 'uploadFile'])->name('api.chat.upload');
    Route::get('/unread-count', [ChatApiController::class, 'getUnreadCount'])->name('api.chat.unread');
    Route::post('/conversation/{conversationId}/read', [ChatApiController::class, 'markAsRead'])->name('api.chat.read');

    // Delete routes
    Route::delete('/conversation/{conversationId}/message/{messageId}', [ChatApiController::class, 'deleteMessage'])->name('api.chat.message.delete');
    Route::delete('/conversation/{conversationId}/message/{messageId}/everyone', [ChatApiController::class, 'deleteMessageForEveryone'])->name('api.chat.message.delete.everyone');
    Route::delete('/conversation/{conversationId}', [ChatApiController::class, 'deleteConversation'])->name('api.chat.conversation.delete');

    // Request and Block routes
    Route::post('/conversation/{conversationId}/accept', [ChatApiController::class, 'acceptChatRequest'])->name('api.chat.request.accept');
    Route::post('/conversation/{conversationId}/reject', [ChatApiController::class, 'rejectChatRequest'])->name('api.chat.request.reject');
    Route::get('/pending-requests', [ChatApiController::class, 'getPendingRequests'])->name('api.chat.pending-requests');
    Route::post('/block', [ChatApiController::class, 'blockUser'])->name('api.chat.block');
    Route::post('/unblock', [ChatApiController::class, 'unblockUser'])->name('api.chat.unblock');

    // Settings routes (admin only)
    Route::middleware('role:ChatAdmin,SuperAdmin')->group(function () {
        Route::get('/settings', [ChatApiController::class, 'getSettings'])->name('api.chat.settings');
        Route::post('/settings/telegram', [ChatApiController::class, 'updateTelegramSettings'])->name('api.chat.settings.telegram');
        Route::get('/settings/telegram/test', [ChatApiController::class, 'testTelegramBot'])->name('api.chat.settings.telegram.test');
    });
});

// ===== LEGACY CHAT API ROUTES (BACKWARD COMPATIBILITY) =====
Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/chat-users-api', [ChatController::class, 'apiGetUsers']);
    Route::post('/chat-send-api', [ChatController::class, 'apiSendMessage']);
    Route::get('/chat-messages-api', [ChatController::class, 'apiGetMessages']);
    Route::post('/chat-create-conversation', [ChatController::class, 'apiCreateConversation']);
    Route::post('/chat-typing', [ChatController::class, 'apiTyping']);

    // Telegram - Contact Request (for authenticated users)
    Route::post('/telegram/contact', [TelegramController::class, 'sendContactRequest']);
});

// Telegram Webhook - no auth required (called by Telegram servers)
Route::post('/telegram/webhook', [TelegramController::class, 'webhook'])->name('telegram.webhook');

// Telegram Admin Routes - require ChatAdmin or SuperAdmin auth
Route::middleware(['web', 'auth', 'role:ChatAdmin,SuperAdmin'])->prefix('telegram')->group(function () {
    Route::post('/send-message', [TelegramController::class, 'sendMessage']);
    Route::post('/set-webhook', [TelegramController::class, 'setWebhook']);
    Route::get('/webhook-info', [TelegramController::class, 'getWebhookInfo']);
    Route::post('/delete-webhook', [TelegramController::class, 'deleteWebhook']);
    Route::get('/bot-info', [TelegramController::class, 'getBotInfo']);
    Route::get('/test-bot', [TelegramController::class, 'getBotInfo']); // Same as bot-info, for testing
});

// Frontend error logging - no auth required
Route::post('/log-frontend-error', function(Request $request) {
    \Log::channel('daily')->error('Frontend Error', [
        'message' => $request->input('message'),
        'url' => $request->input('url'),
        'line' => $request->input('line'),
        'user' => auth()->check() ? auth()->id() : 'guest'
    ]);
    return response()->json(['status' => 'logged']);
});

// ===== SUPPORT CHAT API ROUTES (PUBLIC - NO AUTH) =====
Route::prefix('support-chat')->group(function () {
    Route::post('/init', [App\Http\Controllers\PublicChatController::class, 'initSession']);
    Route::get('/messages', [App\Http\Controllers\PublicChatController::class, 'getMessages']);
    Route::post('/send', [App\Http\Controllers\PublicChatController::class, 'sendMessage']);
    Route::get('/unread', [App\Http\Controllers\PublicChatController::class, 'getUnreadCount']);
});

// Support Chat Admin routes
Route::middleware(['web', 'auth'])->prefix('support-chat/admin')->group(function () {
    Route::get('/sessions', [App\Http\Controllers\PublicChatController::class, 'adminGetSessions']);
    Route::get('/sessions/{sessionId}/messages', [App\Http\Controllers\PublicChatController::class, 'adminGetMessages']);
    Route::post('/sessions/{sessionId}/send', [App\Http\Controllers\PublicChatController::class, 'adminSendMessage']);
    Route::get('/unread', [App\Http\Controllers\PublicChatController::class, 'adminGetUnreadCount']);
});
