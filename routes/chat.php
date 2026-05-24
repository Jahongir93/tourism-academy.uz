<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatController;

/*
|--------------------------------------------------------------------------
| Chat Routes
|--------------------------------------------------------------------------
| All chat-related routes are defined here.
| These routes use 'web' middleware for session/cookie support.
|--------------------------------------------------------------------------
*/

// Public API endpoints (no auth required, but controller checks internally)
Route::get('/chat-users-api', [ChatController::class, 'getUsersList'])->name('chat.users.api');
Route::get('/chat-search-api', [ChatController::class, 'searchUsers'])->name('chat.search.api');

// Authenticated chat routes
Route::middleware(['auth'])->group(function () {

    // Main chat page - DISABLED (chat only works as popup now)
    Route::prefix('chat')->name('chat.')->group(function () {
        // Route::get('/', [ChatController::class, 'index'])->name('index');

        // Room routes
        Route::get('/room/{slug}', [ChatController::class, 'room'])->name('room');
        Route::post('/room/{roomId}/send', [ChatController::class, 'sendMessage'])->name('send');
        Route::post('/message/{messageId}/reaction', [ChatController::class, 'toggleReaction'])->name('reaction');
        Route::post('/join/{roomId}', [ChatController::class, 'joinRoom'])->name('join');
        Route::post('/leave/{roomId}', [ChatController::class, 'leaveRoom'])->name('leave');
        Route::delete('/message/{messageId}', [ChatController::class, 'deleteMessage'])->name('delete-message');

        // Conversation routes
        Route::get('/conversations', [ChatController::class, 'getConversations'])->name('conversations');
        Route::post('/create', [ChatController::class, 'createConversation'])->name('create');
        Route::get('/{conversationId}/messages', [ChatController::class, 'getMessages'])->name('messages');
        Route::post('/{conversationId}/send', [ChatController::class, 'sendDirectMessage'])->name('send-direct');
        Route::post('/{conversationId}/read', [ChatController::class, 'markAsRead'])->name('read');
        Route::get('/unread', [ChatController::class, 'getUnreadCount'])->name('unread');

        // Enhanced routes
        Route::get('/search-users', [ChatController::class, 'searchUsers'])->name('search-users');
        Route::post('/start-conversation', [ChatController::class, 'startConversation'])->name('start-conversation');
        Route::get('/conversation/{conversationId}', [ChatController::class, 'getConversationMessages'])->name('conversation.messages');
        Route::post('/conversation/{conversationId}/send', [ChatController::class, 'sendConversationMessage'])->name('conversation.send');
        Route::post('/conversation/{conversationId}/mark-read', [ChatController::class, 'markConversationAsRead'])->name('conversation.mark-read');
        Route::get('/total-unread', [ChatController::class, 'getTotalUnreadCount'])->name('total-unread');

        // Contact role (admin/dean/prorector)
        Route::post('/contact-role', [ChatController::class, 'contactRole'])->name('contact-role');
        Route::get('/contact-requests', [ChatController::class, 'getContactRequests'])->name('contact-requests');

        // Nickname management
        Route::post('/update-nickname', [ChatController::class, 'updateNickname'])->name('update-nickname');

        // File and voice uploads
        Route::post('/upload-voice', [ChatController::class, 'uploadVoice'])->name('upload-voice');
        Route::post('/upload-file', [ChatController::class, 'uploadFile'])->name('upload-file');
    });
});
