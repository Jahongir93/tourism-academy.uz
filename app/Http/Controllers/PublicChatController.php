<?php

namespace App\Http\Controllers;

use App\Models\SupportMessage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PublicChatController extends Controller
{
    /**
     * Get or create session ID for chat
     */
    private function getSessionId(Request $request)
    {
        $sessionId = $request->cookie('support_chat_session');

        if (!$sessionId) {
            $sessionId = 'support_' . Str::uuid();
        }

        return $sessionId;
    }

    /**
     * Initialize chat session
     */
    public function initSession(Request $request)
    {
        $sessionId = $this->getSessionId($request);

        // Check if there are existing messages
        $hasHistory = SupportMessage::forSession($sessionId)->exists();

        // Get user info if logged in
        $userInfo = null;
        if (auth()->check()) {
            $user = auth()->user();
            $userInfo = [
                'name' => $user->name,
                'email' => $user->email,
            ];
        }

        return response()->json([
            'success' => true,
            'session_id' => $sessionId,
            'has_history' => $hasHistory,
            'user' => $userInfo,
        ])->cookie('support_chat_session', $sessionId, 60 * 24 * 30); // 30 days
    }

    /**
     * Get messages for session
     */
    public function getMessages(Request $request)
    {
        $sessionId = $this->getSessionId($request);
        $after = $request->get('after', 0);

        $query = SupportMessage::forSession($sessionId);

        if ($after > 0) {
            $query->where('id', '>', $after);
        }

        $messages = $query->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($msg) {
                return [
                    'id' => $msg->id,
                    'message' => $msg->message,
                    'is_from_admin' => $msg->is_from_admin,
                    'sender_name' => $msg->sender_name,
                    'created_at' => $msg->created_at->format('H:i'),
                    'formatted_time' => $msg->created_at->format('d.m.Y H:i'),
                ];
            });

        // Mark messages from admin as read
        SupportMessage::forSession($sessionId)
            ->where('is_from_admin', true)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json([
            'success' => true,
            'messages' => $messages,
        ]);
    }

    /**
     * Send message
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:2000',
            'guest_name' => 'nullable|string|max:100',
            'guest_phone' => 'nullable|string|max:20',
        ]);

        $sessionId = $this->getSessionId($request);

        // Create message
        $messageData = [
            'session_id' => $sessionId,
            'message' => $request->message,
            'is_from_admin' => false,
            'ip_address' => $request->ip(),
        ];

        // If user is logged in
        if (auth()->check()) {
            $messageData['user_id'] = auth()->id();
        } else {
            // Guest info
            $messageData['guest_name'] = $request->guest_name;
            $messageData['guest_phone'] = $request->guest_phone;
        }

        $message = SupportMessage::create($messageData);

        // Check if this is the first message - send auto-reply
        $messageCount = SupportMessage::forSession($sessionId)->count();

        $response = [
            'success' => true,
            'message' => [
                'id' => $message->id,
                'message' => $message->message,
                'is_from_admin' => false,
                'sender_name' => $message->sender_name,
                'created_at' => $message->created_at->format('H:i'),
            ],
        ];

        // Send welcome message on first contact
        if ($messageCount === 1) {
            $welcomeMessage = SupportMessage::create([
                'session_id' => $sessionId,
                'message' => "Assalomu alaykum! Turizm akademiyasi qo'llab-quvvatlash xizmatiga xush kelibsiz. Operatorlarimiz tez orada javob berishadi. Iltimos, kutib turing.",
                'is_from_admin' => true,
                'admin_id' => null,
            ]);

            $response['auto_reply'] = [
                'id' => $welcomeMessage->id,
                'message' => $welcomeMessage->message,
                'is_from_admin' => true,
                'sender_name' => 'Qo\'llab-quvvatlash',
                'created_at' => $welcomeMessage->created_at->format('H:i'),
            ];
        }

        return response()->json($response)->cookie('support_chat_session', $sessionId, 60 * 24 * 30);
    }

    /**
     * Get unread count for user
     */
    public function getUnreadCount(Request $request)
    {
        $sessionId = $this->getSessionId($request);

        $count = SupportMessage::forSession($sessionId)
            ->where('is_from_admin', true)
            ->where('is_read', false)
            ->count();

        return response()->json([
            'success' => true,
            'count' => $count,
        ]);
    }

    // ==========================================
    // ADMIN METHODS
    // ==========================================

    /**
     * Get all chat sessions for admin
     */
    public function adminGetSessions(Request $request)
    {
        if (!auth()->check() || !in_array(auth()->user()->role, ['SuperAdmin', 'Admin', 'ChatAdmin'])) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $sessions = SupportMessage::select('session_id')
            ->selectRaw('MAX(created_at) as last_message_at')
            ->selectRaw('COUNT(*) as message_count')
            ->selectRaw('SUM(CASE WHEN is_from_admin = 0 AND is_read = 0 THEN 1 ELSE 0 END) as unread_count')
            ->groupBy('session_id')
            ->orderByDesc('last_message_at')
            ->get()
            ->map(function ($session) {
                $lastMessage = SupportMessage::forSession($session->session_id)
                    ->orderByDesc('created_at')
                    ->first();

                $firstMessage = SupportMessage::forSession($session->session_id)
                    ->where('is_from_admin', false)
                    ->orderBy('created_at')
                    ->first();

                return [
                    'session_id' => $session->session_id,
                    'name' => $firstMessage ? ($firstMessage->user ? $firstMessage->user->name : ($firstMessage->guest_name ?: 'Mehmon')) : 'Mehmon',
                    'phone' => $firstMessage ? ($firstMessage->user ? ($firstMessage->user->phone ?? null) : $firstMessage->guest_phone) : null,
                    'last_message' => $lastMessage ? Str::limit($lastMessage->message, 50) : '',
                    'last_message_at' => $lastMessage ? $lastMessage->created_at->diffForHumans() : '',
                    'message_count' => $session->message_count,
                    'unread_count' => $session->unread_count,
                ];
            });

        return response()->json([
            'success' => true,
            'sessions' => $sessions,
        ]);
    }

    /**
     * Get messages for a session (admin)
     */
    public function adminGetMessages(Request $request, $sessionId)
    {
        if (!auth()->check() || !in_array(auth()->user()->role, ['SuperAdmin', 'Admin', 'ChatAdmin'])) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $messages = SupportMessage::forSession($sessionId)
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($msg) {
                return [
                    'id' => $msg->id,
                    'message' => $msg->message,
                    'is_from_admin' => $msg->is_from_admin,
                    'sender_name' => $msg->sender_name,
                    'created_at' => $msg->created_at->format('H:i'),
                    'formatted_time' => $msg->created_at->format('d.m.Y H:i'),
                ];
            });

        // Mark user messages as read
        SupportMessage::forSession($sessionId)
            ->where('is_from_admin', false)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json([
            'success' => true,
            'messages' => $messages,
        ]);
    }

    /**
     * Send message as admin
     */
    public function adminSendMessage(Request $request, $sessionId)
    {
        if (!auth()->check() || !in_array(auth()->user()->role, ['SuperAdmin', 'Admin', 'ChatAdmin'])) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        $message = SupportMessage::create([
            'session_id' => $sessionId,
            'message' => $request->message,
            'is_from_admin' => true,
            'admin_id' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => [
                'id' => $message->id,
                'message' => $message->message,
                'is_from_admin' => true,
                'sender_name' => auth()->user()->name,
                'created_at' => $message->created_at->format('H:i'),
            ],
        ]);
    }

    /**
     * Get total unread count for admin
     */
    public function adminGetUnreadCount()
    {
        if (!auth()->check() || !in_array(auth()->user()->role, ['SuperAdmin', 'Admin', 'ChatAdmin'])) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $count = SupportMessage::where('is_from_admin', false)
            ->where('is_read', false)
            ->count();

        return response()->json([
            'success' => true,
            'count' => $count,
        ]);
    }
}
