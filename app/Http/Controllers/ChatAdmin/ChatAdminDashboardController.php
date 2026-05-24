<?php

namespace App\Http\Controllers\ChatAdmin;

use App\Http\Controllers\Controller;
use App\Models\ChatConversation;
use App\Models\ChatMessage;
use App\Models\Chat\ChatContactRequest;
use App\Models\TelegramMessage;
use App\Models\TelegramBotSetting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChatAdminDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Get statistics
        $stats = [
            'total_rooms' => ChatConversation::count(),
            'active_rooms' => ChatConversation::count(), // All conversations are considered active
            'total_messages' => ChatMessage::count(),
            'messages_today' => ChatMessage::whereDate('created_at', today())->count(),
            'total_users' => User::count(),
            'active_users_today' => ChatMessage::whereDate('created_at', today())
                ->distinct('user_id')
                ->count('user_id'),
            'telegram_new' => TelegramMessage::where('status', 'new')->count(),
            'contact_requests_pending' => ChatContactRequest::where('status', 'pending')->count(),
        ];

        // Most active conversations
        $activeRooms = ChatConversation::withCount('messages')
            ->orderBy('messages_count', 'desc')
            ->limit(10)
            ->get();

        // Recent messages
        $recentMessages = ChatMessage::with(['user', 'conversation'])
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        // Messages by hour (last 24 hours)
        $messagesByHour = ChatMessage::select(
                DB::raw('HOUR(created_at) as hour'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', now()->subDay())
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();

        // Most active users
        $activeUsers = User::withCount('chatMessages')
            ->orderBy('chat_messages_count', 'desc')
            ->limit(10)
            ->get();

        // Recent telegram messages
        $recentTelegramMessages = TelegramMessage::where('direction', 'incoming')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Recent contact requests
        $recentContactRequests = ChatContactRequest::with('fromUser')
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('chat-admin.dashboard', compact(
            'user',
            'stats',
            'activeRooms',
            'recentMessages',
            'messagesByHour',
            'activeUsers',
            'recentTelegramMessages',
            'recentContactRequests'
        ));
    }

    /**
     * Telegram xabarlar ro'yxati (conversation view)
     */
    public function telegramMessages(Request $request)
    {
        $selectedChatId = $request->get('chat_id');
        $status = $request->get('status');

        // Get conversations (grouped by user)
        $conversationsQuery = TelegramMessage::select('telegram_chat_id')
            ->selectRaw('MAX(telegram_first_name) as telegram_first_name')
            ->selectRaw('MAX(telegram_last_name) as telegram_last_name')
            ->selectRaw('MAX(telegram_username) as telegram_username')
            ->selectRaw('MAX(created_at) as last_message_at')
            ->selectRaw('COUNT(*) as message_count')
            ->selectRaw('SUM(CASE WHEN status = "new" THEN 1 ELSE 0 END) as unread_count')
            ->selectRaw('MAX(message) as last_message')
            ->groupBy('telegram_chat_id')
            ->orderBy('last_message_at', 'desc');

        if ($status) {
            $conversationsQuery->having(DB::raw('SUM(CASE WHEN status = "' . $status . '" THEN 1 ELSE 0 END)'), '>', 0);
        }

        $conversations = $conversationsQuery->get();

        // Get messages for selected conversation
        $messages = collect([]);
        if ($selectedChatId || $conversations->isNotEmpty()) {
            $chatId = $selectedChatId ?? $conversations->first()->telegram_chat_id;
            $messages = TelegramMessage::with('repliedByUser')
                ->where('telegram_chat_id', $chatId)
                ->orderBy('created_at', 'asc')
                ->get();

            // Mark messages as read
            TelegramMessage::where('telegram_chat_id', $chatId)
                ->where('status', 'new')
                ->update(['status' => 'read']);
        } else {
            $chatId = null;
        }

        $stats = [
            'total' => TelegramMessage::count(),
            'new' => TelegramMessage::where('status', 'new')->count(),
            'read' => TelegramMessage::where('status', 'read')->count(),
            'replied' => TelegramMessage::where('status', 'replied')->count(),
            'conversations' => $conversations->count(),
        ];

        return view('chat-admin.telegram.index', compact('conversations', 'messages', 'stats', 'status', 'chatId'));
    }

    /**
     * Telegram xabariga javob berish
     */
    public function replyTelegramMessage(Request $request, TelegramMessage $message)
    {
        $request->validate([
            'reply_message' => 'required|string|max:4096',
        ]);

        // Send reply to Telegram first
        $telegram = app(\App\Services\TelegramService::class);
        $result = $telegram->sendMessage(
            $message->telegram_chat_id,
            "📬 <b>Admin javobi:</b>\n\n" . $request->reply_message
        );

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => 'Telegram ga yuborishda xatolik: ' . ($result['error'] ?? 'Unknown error'),
            ], 400);
        }

        // Update incoming message status
        $message->reply($request->reply_message, Auth::id());

        // Create outgoing message record
        TelegramMessage::create([
            'telegram_chat_id' => $message->telegram_chat_id,
            'telegram_message_id' => $result['data']['result']['message_id'] ?? null,
            'telegram_user_id' => $message->telegram_user_id,
            'telegram_username' => $message->telegram_username,
            'telegram_first_name' => $message->telegram_first_name,
            'telegram_last_name' => $message->telegram_last_name,
            'reply_message' => $request->reply_message,
            'direction' => 'outgoing',
            'replied_by' => Auth::id(),
            'replied_at' => now(),
            'status' => 'replied',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Javob Telegramga yuborildi!',
        ]);
    }

    /**
     * Telegram xabar statusini yangilash
     */
    public function updateTelegramStatus(Request $request, TelegramMessage $message)
    {
        $request->validate([
            'status' => 'required|in:new,read,replied,closed',
        ]);

        $message->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => 'Status yangilandi',
        ]);
    }

    /**
     * Murojaat so'rovlari ro'yxati
     */
    public function contactRequests(Request $request)
    {
        $status = $request->get('status');
        $role = $request->get('role');

        $query = ChatContactRequest::with(['fromUser', 'toUser', 'handledBy'])
            ->orderBy('created_at', 'desc');

        if ($status) {
            $query->where('status', $status);
        }

        if ($role) {
            $query->where('to_role', $role);
        }

        $requests = $query->paginate(20);

        $stats = [
            'total' => ChatContactRequest::count(),
            'pending' => ChatContactRequest::where('status', 'pending')->count(),
            'accepted' => ChatContactRequest::where('status', 'accepted')->count(),
            'rejected' => ChatContactRequest::where('status', 'rejected')->count(),
        ];

        return view('chat-admin.contact-requests.index', compact('requests', 'stats', 'status', 'role'));
    }

    /**
     * Murojaat so'roviga javob berish
     */
    public function respondContactRequest(Request $request, ChatContactRequest $contactRequest)
    {
        $request->validate([
            'status' => 'required|in:accepted,rejected',
            'response' => 'nullable|string|max:1000',
        ]);

        $contactRequest->update([
            'status' => $request->status,
            'response' => $request->response,
            'handled_by' => Auth::id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Murojaat ' . ($request->status === 'accepted' ? 'qabul qilindi' : 'rad etildi'),
        ]);
    }

    /**
     * Telegram bot sozlamalari
     */
    public function telegramSettings()
    {
        $settings = TelegramBotSetting::first() ?? new TelegramBotSetting();

        return view('chat-admin.telegram.settings', compact('settings'));
    }

    /**
     * Telegram bot sozlamalarini yangilash
     */
    public function updateTelegramSettings(Request $request)
    {
        $request->validate([
            'bot_token' => 'nullable|string|max:100',
            'bot_username' => 'nullable|string|max:50',
            'webhook_url' => 'nullable|url|max:255',
            'is_active' => 'boolean',
            'welcome_message' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();
        try {
            // Save to ChatSetting for TelegramService compatibility
            if ($request->filled('bot_token')) {
                \App\Models\ChatSetting::set('telegram_bot_token', $request->bot_token, 'string', false, 'Telegram Bot Token');
            }
            if ($request->filled('bot_username')) {
                \App\Models\ChatSetting::set('telegram_bot_username', $request->bot_username, 'string', true, 'Telegram Bot Username');
            }
            if ($request->filled('webhook_url')) {
                \App\Models\ChatSetting::set('telegram_webhook_url', $request->webhook_url, 'string', true, 'Telegram Webhook URL');
            }
            if ($request->has('is_active')) {
                \App\Models\ChatSetting::set('telegram_enabled', $request->is_active, 'boolean', true, 'Telegram Bot Enabled');
            }
            if ($request->filled('welcome_message')) {
                \App\Models\ChatSetting::set('telegram_welcome_message', $request->welcome_message, 'string', true, 'Telegram Welcome Message');
            }

            // Also save to TelegramBotSetting for backward compatibility
            $settings = TelegramBotSetting::first();
            if (!$settings) {
                $settings = new TelegramBotSetting();
            }
            $settings->fill($request->only([
                'bot_token',
                'bot_username',
                'webhook_url',
                'is_active',
                'welcome_message',
            ]));
            $settings->save();

            // Set webhook if token is provided
            if ($request->filled('bot_token')) {
                try {
                    $botToken = $request->bot_token;
                    $webhookUrl = $request->webhook_url ?? url('/api/telegram/webhook');

                    $response = \Illuminate\Support\Facades\Http::timeout(30)->post(
                        "https://api.telegram.org/bot{$botToken}/setWebhook",
                        [
                            'url' => $webhookUrl,
                            'allowed_updates' => ['message', 'callback_query'],
                        ]
                    );

                    if ($response->successful()) {
                        \Illuminate\Support\Facades\Log::info('Telegram webhook set successfully', ['webhook_url' => $webhookUrl]);
                    }
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::warning('Failed to set Telegram webhook', ['error' => $e->getMessage()]);
                }
            }

            DB::commit();

            return redirect()->route('chat-admin.telegram-settings')
                ->with('success', 'Telegram bot sozlamalari saqlandi va webhook o\'rnatildi');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Xatolik yuz berdi: ' . $e->getMessage());
        }
    }

    /**
     * Foydalanuvchilar niknamlar ro'yxati
     */
    public function userNicknames(Request $request)
    {
        $search = $request->get('search');

        $query = User::query()
            ->select('id', 'name', 'nickname', 'email')
            ->orderBy('name');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('nickname', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->paginate(30);

        return view('chat-admin.nicknames.index', compact('users', 'search'));
    }

    /**
     * Foydalanuvchi niknamini yangilash
     */
    public function updateUserNickname(Request $request, User $user)
    {
        $request->validate([
            'nickname' => 'nullable|string|max:50|unique:users,nickname,' . $user->id,
        ]);

        $user->update(['nickname' => $request->nickname]);

        return response()->json([
            'success' => true,
            'message' => 'Nikname yangilandi',
            'nickname' => $user->nickname,
        ]);
    }

    /**
     * Support Chat - Barcha sessiyalar
     */
    public function supportChat(Request $request)
    {
        $sessions = \App\Models\SupportMessage::select('session_id')
            ->selectRaw('MAX(created_at) as last_message_at')
            ->selectRaw('COUNT(*) as message_count')
            ->selectRaw('SUM(CASE WHEN is_from_admin = 0 AND is_read = 0 THEN 1 ELSE 0 END) as unread_count')
            ->groupBy('session_id')
            ->orderByDesc('last_message_at')
            ->paginate(20);

        // Get additional info for each session
        $sessions->getCollection()->transform(function ($session) {
            $firstMessage = \App\Models\SupportMessage::where('session_id', $session->session_id)
                ->where('is_from_admin', false)
                ->orderBy('created_at')
                ->first();

            $lastMessage = \App\Models\SupportMessage::where('session_id', $session->session_id)
                ->orderByDesc('created_at')
                ->first();

            $session->name = $firstMessage ? ($firstMessage->user ? $firstMessage->user->name : ($firstMessage->guest_name ?: 'Mehmon')) : 'Mehmon';
            $session->phone = $firstMessage ? ($firstMessage->user ? ($firstMessage->user->phone ?? null) : $firstMessage->guest_phone) : null;
            $session->last_message = $lastMessage ? \Illuminate\Support\Str::limit($lastMessage->message, 50) : '';
            $session->last_message_time = $lastMessage ? $lastMessage->created_at->diffForHumans() : '';

            return $session;
        });

        $stats = [
            'total_sessions' => \App\Models\SupportMessage::distinct('session_id')->count('session_id'),
            'total_messages' => \App\Models\SupportMessage::count(),
            'unread_messages' => \App\Models\SupportMessage::where('is_from_admin', false)->where('is_read', false)->count(),
            'messages_today' => \App\Models\SupportMessage::whereDate('created_at', today())->count(),
        ];

        return view('chat-admin.support-chat.index', compact('sessions', 'stats'));
    }

    /**
     * Support Chat - Sessiya xabarlarini ko'rish
     */
    public function supportChatSession($sessionId)
    {
        $messages = \App\Models\SupportMessage::where('session_id', $sessionId)
            ->orderBy('created_at', 'asc')
            ->get();

        // Mark user messages as read
        \App\Models\SupportMessage::where('session_id', $sessionId)
            ->where('is_from_admin', false)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        // Get session info
        $firstMessage = $messages->where('is_from_admin', false)->first();
        $sessionInfo = [
            'session_id' => $sessionId,
            'name' => $firstMessage ? ($firstMessage->user ? $firstMessage->user->name : ($firstMessage->guest_name ?: 'Mehmon')) : 'Mehmon',
            'phone' => $firstMessage ? ($firstMessage->user ? ($firstMessage->user->phone ?? null) : $firstMessage->guest_phone) : null,
            'started_at' => $messages->first() ? $messages->first()->created_at : null,
        ];

        return view('chat-admin.support-chat.session', compact('messages', 'sessionInfo', 'sessionId'));
    }

    /**
     * Support Chat - Xabar yuborish
     */
    public function supportChatSend(Request $request, $sessionId)
    {
        $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        $message = \App\Models\SupportMessage::create([
            'session_id' => $sessionId,
            'message' => $request->message,
            'is_from_admin' => true,
            'admin_id' => Auth::id(),
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => [
                    'id' => $message->id,
                    'message' => $message->message,
                    'is_from_admin' => true,
                    'sender_name' => Auth::user()->name,
                    'created_at' => $message->created_at->format('H:i'),
                ],
            ]);
        }

        return redirect()->back()->with('success', 'Xabar yuborildi');
    }
}
