<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ChatConversation;
use App\Models\ChatMessage as ConversationMessage;
use App\Models\ChatParticipant;
use App\Models\ChatBlockedUser;
use App\Models\ChatSetting;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ChatApiController extends Controller
{
    /**
     * Get all users for chat (except current user)
     */
    public function getUsers(Request $request)
    {
        try {
            if (!auth()->check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }

            $query = $request->get('q', '');

            $users = User::where('id', '!=', auth()->id())
                ->whereNotNull('name')
                ->when($query, function($q) use ($query) {
                    $q->where(function($subQ) use ($query) {
                        $subQ->where('name', 'like', "%{$query}%")
                            ->orWhere('email', 'like', "%{$query}%")
                            ->orWhere('nickname', 'like', "%{$query}%");
                    });
                })
                ->select('id', 'name', 'email', 'nickname', 'is_online', 'last_seen_at')
                ->orderBy('name')
                ->limit(50)
                ->get()
                ->map(function($user) {
                    $isOnline = false;
                    try {
                        if ($user->is_online && $user->last_seen_at) {
                            $isOnline = $user->last_seen_at->diffInMinutes(now()) < 5;
                        }
                    } catch (\Exception $e) {
                        // Ignore
                    }

                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'nickname' => $user->nickname,
                        'avatar' => strtoupper(substr($user->name, 0, 1)),
                        'is_online' => $isOnline,
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $users
            ]);

        } catch (\Exception $e) {
            Log::error('Chat API - Get Users Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user conversations (only conversations with messages)
     */
    public function getConversations(Request $request)
    {
        try {
            if (!auth()->check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }

            $query = $request->get('q', '');

            // Get conversations where user is participant and has messages
            $conversations = ChatConversation::whereHas('participants', function($q) {
                    $q->where('user_id', auth()->id())
                      ->whereNull('deleted_at'); // Exclude deleted conversations
                })
                ->whereHas('messages') // Only conversations with messages
                ->with(['participants' => function($q) {
                    $q->where('user_id', '!=', auth()->id())
                      ->with('user:id,name,email,nickname,is_online,last_seen_at');
                }, 'messages' => function($q) {
                    $q->latest()->limit(1); // Get last message
                }])
                ->orderBy('last_message_at', 'desc')
                ->limit(50)
                ->get()
                ->map(function($conversation) use ($query) {
                    $otherParticipant = $conversation->participants->first();
                    if (!$otherParticipant || !$otherParticipant->user) {
                        return null;
                    }

                    $user = $otherParticipant->user;

                    // Apply search filter
                    if ($query && !str_contains(strtolower($user->name), strtolower($query))) {
                        return null;
                    }

                    $lastMessage = $conversation->messages->first();
                    $isOnline = false;

                    try {
                        if ($user->is_online && $user->last_seen_at) {
                            $isOnline = $user->last_seen_at->diffInMinutes(now()) < 5;
                        }
                    } catch (\Exception $e) {
                        // Ignore
                    }

                    // Get unread count for current user
                    $unreadCount = ChatParticipant::where('conversation_id', $conversation->id)
                        ->where('user_id', auth()->id())
                        ->value('unread_count') ?? 0;

                    return [
                        'conversation_id' => $conversation->id,
                        'user_id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'nickname' => $user->nickname,
                        'avatar' => strtoupper(substr($user->name, 0, 1)),
                        'is_online' => $isOnline,
                        'last_message' => $lastMessage ? $lastMessage->message : '',
                        'last_message_time' => $lastMessage ? $lastMessage->created_at->diffForHumans() : '',
                        'unread_count' => $unreadCount,
                    ];
                })
                ->filter() // Remove nulls
                ->values();

            return response()->json([
                'success' => true,
                'data' => $conversations
            ]);

        } catch (\Exception $e) {
            Log::error('Chat API - Get Conversations Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get or create conversation with user
     */
    public function getOrCreateConversation(Request $request)
    {
        try {
            if (!auth()->check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }

            $validator = Validator::make($request->all(), [
                'user_id' => 'required|exists:users,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ], 422);
            }

            $userId = $request->user_id;
            $currentUserId = auth()->id();

            if ($userId == $currentUserId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot chat with yourself'
                ], 400);
            }

            // Find existing conversation
            $conversation = ChatConversation::where('type', 'private')
                ->whereHas('participants', function($q) use ($userId) {
                    $q->where('user_id', $userId);
                })
                ->whereHas('participants', function($q) use ($currentUserId) {
                    $q->where('user_id', $currentUserId);
                })
                ->first();

            // Check if blocked
            if (ChatBlockedUser::hasBlockBetween($currentUserId, $userId)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bu foydalanuvchi bilan suhbatlasha olmaysiz'
                ], 403);
            }

            if (!$conversation) {
                // Create new conversation
                $conversation = ChatConversation::create([
                    'type' => 'private',
                    'created_by' => $currentUserId,
                ]);

                // Check if current user is a student
                $currentUser = auth()->user();
                $isStudent = $currentUser->role === 'Student' || $currentUser->role === 'student';

                // Add participants
                // If student is creating conversation, set request_status to 'pending'
                $conversation->participants()->createMany([
                    [
                        'user_id' => $currentUserId,
                        'is_admin' => false,
                        'request_status' => $isStudent ? 'pending' : 'accepted'
                    ],
                    [
                        'user_id' => $userId,
                        'is_admin' => false,
                        'request_status' => 'accepted' // Other user always accepted
                    ],
                ]);
            }

            // Get other user info
            $otherUser = User::find($userId);

            return response()->json([
                'success' => true,
                'data' => [
                    'conversation_id' => $conversation->id,
                    'user' => [
                        'id' => $otherUser->id,
                        'name' => $otherUser->name,
                        'email' => $otherUser->email,
                        'avatar' => strtoupper(substr($otherUser->name, 0, 1)),
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Chat API - Create Conversation Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get messages for conversation
     */
    public function getMessages(Request $request, $conversationId)
    {
        try {
            if (!auth()->check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }

            $conversation = ChatConversation::find($conversationId);

            if (!$conversation) {
                return response()->json([
                    'success' => false,
                    'message' => 'Conversation not found'
                ], 404);
            }

            // Check if user is participant
            if (!$conversation->participants()->where('user_id', auth()->id())->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied'
                ], 403);
            }

            $after = $request->get('after', 0);

            $query = ConversationMessage::where('conversation_id', $conversationId)
                ->notDeletedForUser(auth()->id()); // Filter deleted messages

            if ($after > 0) {
                $query->where('id', '>', $after);
            }

            $messages = $query->with('user:id,name,nickname')
                ->orderBy('created_at', 'asc')
                ->get()
                ->map(function($msg) {
                    return [
                        'id' => $msg->id,
                        'user_id' => $msg->user_id,
                        'message' => $msg->message,
                        'type' => $msg->type,
                        'file_url' => $msg->file_url ? asset($msg->file_url) : null,
                        'is_own' => $msg->user_id == auth()->id(),
                        'user_name' => $msg->user->name ?? 'Unknown',
                        'created_at' => $msg->created_at->format('H:i'),
                        'formatted_time' => $msg->created_at->format('d.m.Y H:i'),
                        'can_delete_for_everyone' => $msg->user_id == auth()->id() && $msg->created_at->diffInHours(now()) <= 48,
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $messages
            ]);

        } catch (\Exception $e) {
            Log::error('Chat API - Get Messages Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send message
     */
    public function sendMessage(Request $request, $conversationId)
    {
        try {
            if (!auth()->check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }

            $validator = Validator::make($request->all(), [
                'message' => 'required|string|max:2000',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ], 422);
            }

            $conversation = ChatConversation::find($conversationId);

            if (!$conversation) {
                return response()->json([
                    'success' => false,
                    'message' => 'Conversation not found'
                ], 404);
            }

            // Check if user is participant
            $currentParticipant = $conversation->participants()->where('user_id', auth()->id())->first();
            if (!$currentParticipant) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied'
                ], 403);
            }

            // Get other participant (for private chats)
            $otherParticipant = $conversation->participants()->where('user_id', '!=', auth()->id())->first();

            if ($otherParticipant) {
                // Check if blocked
                if (ChatBlockedUser::hasBlockBetween(auth()->id(), $otherParticipant->user_id)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Bu foydalanuvchi bilan suhbatlasha olmaysiz'
                    ], 403);
                }

                // Check request status for students
                $currentUser = auth()->user();
                $isStudent = $currentUser->role === 'Student' || $currentUser->role === 'student';

                if ($isStudent && $currentParticipant->request_status === 'pending') {
                    return response()->json([
                        'success' => false,
                        'message' => 'Suhbat so\'rovi kutilmoqda. Foydalanuvchi qabul qilguncha xabar yubora olmaysiz.',
                        'request_pending' => true
                    ], 403);
                }

                if ($isStudent && $currentParticipant->request_status === 'rejected') {
                    return response()->json([
                        'success' => false,
                        'message' => 'Sizning so\'rovingiz rad etilgan. Bu foydalanuvchiga xabar yubora olmaysiz.',
                        'request_rejected' => true
                    ], 403);
                }
            }

            // Create message
            $message = ConversationMessage::create([
                'conversation_id' => $conversationId,
                'user_id' => auth()->id(),
                'message' => $request->message,
                'type' => 'text',
            ]);

            // Update conversation
            $conversation->update(['last_message_at' => now()]);

            // Update unread count for other participants
            $conversation->participants()
                ->where('user_id', '!=', auth()->id())
                ->increment('unread_count');

            $message->load('user:id,name,nickname');

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $message->id,
                    'user_id' => $message->user_id,
                    'message' => $message->message,
                    'type' => $message->type,
                    'is_own' => true,
                    'user_name' => $message->user->name,
                    'created_at' => $message->created_at->format('H:i'),
                    'formatted_time' => $message->created_at->format('d.m.Y H:i'),
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Chat API - Send Message Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload file
     */
    public function uploadFile(Request $request, $conversationId)
    {
        try {
            if (!auth()->check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }

            $validator = Validator::make($request->all(), [
                'file' => 'required|file|max:10240', // 10MB
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ], 422);
            }

            $conversation = ChatConversation::find($conversationId);

            if (!$conversation) {
                return response()->json([
                    'success' => false,
                    'message' => 'Conversation not found'
                ], 404);
            }

            // Check if user is participant
            if (!$conversation->participants()->where('user_id', auth()->id())->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied'
                ], 403);
            }

            // Create directory if not exists
            $fileDir = public_path('images/chat/files');
            if (!file_exists($fileDir)) {
                mkdir($fileDir, 0755, true);
            }

            // Upload file
            $file = $request->file('file');
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();
            $filename = time() . '_' . auth()->id() . '_' . Str::slug($originalName) . '.' . $extension;
            $file->move($fileDir, $filename);
            $filePath = 'images/chat/files/' . $filename;

            // Determine file type (database only supports: text, file, image)
            $fileExt = strtolower($extension);
            $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];

            if (in_array($fileExt, $imageExtensions)) {
                $fileType = 'image';
                $messageText = '[Rasm]';
            } else {
                // All non-image files use 'file' type (videos, docs, etc.)
                $fileType = 'file';
                $messageText = '[Fayl: ' . $originalName . '.' . $extension . ']';
            }

            // Save message
            $message = ConversationMessage::create([
                'conversation_id' => $conversationId,
                'user_id' => auth()->id(),
                'message' => $messageText,
                'type' => $fileType,
                'file_url' => $filePath,
            ]);

            // Update conversation
            $conversation->update(['last_message_at' => now()]);

            $message->load('user:id,name,nickname');

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $message->id,
                    'user_id' => $message->user_id,
                    'message' => $messageText,
                    'type' => $fileType,
                    'file_url' => asset($filePath),
                    'is_own' => true,
                    'user_name' => $message->user->name,
                    'created_at' => $message->created_at->format('H:i'),
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Chat API - Upload File Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get unread count
     */
    public function getUnreadCount()
    {
        try {
            if (!auth()->check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }

            $count = ChatParticipant::where('user_id', auth()->id())
                ->sum('unread_count');

            return response()->json([
                'success' => true,
                'data' => ['count' => $count ?? 0]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => true,
                'data' => ['count' => 0]
            ]);
        }
    }

    /**
     * Mark conversation as read
     */
    public function markAsRead($conversationId)
    {
        try {
            if (!auth()->check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }

            ChatParticipant::where('conversation_id', $conversationId)
                ->where('user_id', auth()->id())
                ->update(['unread_count' => 0]);

            return response()->json([
                'success' => true,
                'message' => 'Marked as read'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Server error'
            ], 500);
        }
    }

    /**
     * Delete message for current user (one-sided delete)
     */
    public function deleteMessage($conversationId, $messageId)
    {
        try {
            if (!auth()->check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }

            $message = ConversationMessage::where('id', $messageId)
                ->where('conversation_id', $conversationId)
                ->first();

            if (!$message) {
                return response()->json([
                    'success' => false,
                    'message' => 'Xabar topilmadi'
                ], 404);
            }

            // Check if user is participant
            $conversation = ChatConversation::find($conversationId);
            $isParticipant = $conversation->users->contains(auth()->id());

            if (!$isParticipant) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }

            // Delete for current user
            $message->deleteForUser(auth()->id());

            return response()->json([
                'success' => true,
                'message' => 'Xabar o\'chirildi'
            ]);

        } catch (\Exception $e) {
            Log::error('Delete message error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete message for everyone (two-sided delete)
     */
    public function deleteMessageForEveryone($conversationId, $messageId)
    {
        try {
            if (!auth()->check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }

            $message = ConversationMessage::where('id', $messageId)
                ->where('conversation_id', $conversationId)
                ->where('user_id', auth()->id()) // Only sender can delete for everyone
                ->first();

            if (!$message) {
                return response()->json([
                    'success' => false,
                    'message' => 'Xabar topilmadi yoki sizda ruxsat yo\'q'
                ], 404);
            }

            // Check if message was sent recently (within 48 hours)
            $hoursSinceSent = $message->created_at->diffInHours(now());
            if ($hoursSinceSent > 48) {
                return response()->json([
                    'success' => false,
                    'message' => 'Eski xabarlarni hamma uchun o\'chirish mumkin emas (48 soat o\'tgan)'
                ], 403);
            }

            // Delete for everyone
            $message->deleteForEveryone();

            return response()->json([
                'success' => true,
                'message' => 'Xabar hamma uchun o\'chirildi'
            ]);

        } catch (\Exception $e) {
            Log::error('Delete message for everyone error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete conversation for current user
     */
    public function deleteConversation($conversationId)
    {
        try {
            if (!auth()->check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }

            $participant = ChatParticipant::where('conversation_id', $conversationId)
                ->where('user_id', auth()->id())
                ->first();

            if (!$participant) {
                return response()->json([
                    'success' => false,
                    'message' => 'Suhbat topilmadi'
                ], 404);
            }

            // Mark conversation as deleted for this user
            $participant->update(['deleted_at' => now()]);

            // Also mark all messages in this conversation as deleted for this user
            $messages = ConversationMessage::where('conversation_id', $conversationId)
                ->get();

            foreach ($messages as $message) {
                $message->deleteForUser(auth()->id());
            }

            return response()->json([
                'success' => true,
                'message' => 'Suhbat o\'chirildi'
            ]);

        } catch (\Exception $e) {
            Log::error('Delete conversation error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Accept chat request from student
     */
    public function acceptChatRequest($conversationId)
    {
        try {
            if (!auth()->check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }

            $conversation = ChatConversation::find($conversationId);

            if (!$conversation) {
                return response()->json([
                    'success' => false,
                    'message' => 'Suhbat topilmadi'
                ], 404);
            }

            // Get the student participant (the one with pending status)
            $studentParticipant = $conversation->participants()
                ->where('request_status', 'pending')
                ->first();

            if (!$studentParticipant) {
                return response()->json([
                    'success' => false,
                    'message' => 'So\'rov topilmadi'
                ], 404);
            }

            // Update request status to accepted
            $studentParticipant->update(['request_status' => 'accepted']);

            return response()->json([
                'success' => true,
                'message' => 'Suhbat qabul qilindi'
            ]);

        } catch (\Exception $e) {
            Log::error('Accept chat request error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reject chat request from student
     */
    public function rejectChatRequest($conversationId)
    {
        try {
            if (!auth()->check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }

            $conversation = ChatConversation::find($conversationId);

            if (!$conversation) {
                return response()->json([
                    'success' => false,
                    'message' => 'Suhbat topilmadi'
                ], 404);
            }

            // Get the student participant
            $studentParticipant = $conversation->participants()
                ->where('request_status', 'pending')
                ->first();

            if (!$studentParticipant) {
                return response()->json([
                    'success' => false,
                    'message' => 'So\'rov topilmadi'
                ], 404);
            }

            // Update request status to rejected
            $studentParticipant->update(['request_status' => 'rejected']);

            return response()->json([
                'success' => true,
                'message' => 'Suhbat rad etildi'
            ]);

        } catch (\Exception $e) {
            Log::error('Reject chat request error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Block user
     */
    public function blockUser(Request $request)
    {
        try {
            if (!auth()->check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }

            $validator = Validator::make($request->all(), [
                'user_id' => 'required|exists:users,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ], 422);
            }

            $blockedId = $request->user_id;

            if ($blockedId == auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'O\'zingizni bloklay olmaysiz'
                ], 400);
            }

            // Check if already blocked
            if (ChatBlockedUser::isBlocked(auth()->id(), $blockedId)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bu foydalanuvchi allaqachon bloklangan'
                ], 400);
            }

            // Block user
            ChatBlockedUser::create([
                'blocker_id' => auth()->id(),
                'blocked_id' => $blockedId,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Foydalanuvchi bloklandi'
            ]);

        } catch (\Exception $e) {
            Log::error('Block user error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Unblock user
     */
    public function unblockUser(Request $request)
    {
        try {
            if (!auth()->check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }

            $validator = Validator::make($request->all(), [
                'user_id' => 'required|exists:users,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ], 422);
            }

            $blockedId = $request->user_id;

            // Remove block
            ChatBlockedUser::where('blocker_id', auth()->id())
                ->where('blocked_id', $blockedId)
                ->delete();

            return response()->json([
                'success' => true,
                'message' => 'Foydalanuvchi blokdan chiqarildi'
            ]);

        } catch (\Exception $e) {
            Log::error('Unblock user error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get pending chat requests (for non-students receiving requests from students)
     */
    public function getPendingRequests()
    {
        try {
            if (!auth()->check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }

            // Get conversations where current user is participant and has pending requests
            $conversations = ChatConversation::whereHas('participants', function($q) {
                    $q->where('user_id', auth()->id())
                      ->where('request_status', 'accepted'); // Current user accepted
                })
                ->whereHas('participants', function($q) {
                    $q->where('user_id', '!=', auth()->id())
                      ->where('request_status', 'pending'); // Other user pending
                })
                ->with(['participants' => function($q) {
                    $q->where('user_id', '!=', auth()->id())
                      ->with('user:id,name,email,nickname');
                }, 'messages' => function($q) {
                    $q->latest()->limit(1);
                }])
                ->get();

            $requests = $conversations->map(function($conv) {
                $otherParticipant = $conv->participants->first();
                $lastMessage = $conv->messages->first();

                return [
                    'conversation_id' => $conv->id,
                    'user_id' => $otherParticipant->user->id,
                    'name' => $otherParticipant->user->name,
                    'email' => $otherParticipant->user->email,
                    'nickname' => $otherParticipant->user->nickname,
                    'avatar' => strtoupper(substr($otherParticipant->user->name, 0, 1)),
                    'last_message' => $lastMessage ? $lastMessage->message : '',
                    'created_at' => $conv->created_at->diffForHumans(),
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $requests
            ]);

        } catch (\Exception $e) {
            Log::error('Get pending requests error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get chat settings (admin only)
     */
    public function getSettings()
    {
        try {
            if (!auth()->check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }

            // Check if user has admin privileges (SuperAdmin or ChatAdmin)
            $user = auth()->user();
            if (!in_array($user->role, ['SuperAdmin', 'ChatAdmin'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ruxsat yo\'q'
                ], 403);
            }

            $settings = [
                'telegram_bot_token' => ChatSetting::getTelegramBotToken() ? '***' . substr(ChatSetting::getTelegramBotToken(), -8) : null,
                'telegram_bot_username' => ChatSetting::getTelegramBotUsername(),
                'telegram_webhook_url' => ChatSetting::getTelegramWebhookUrl(),
                'telegram_enabled' => ChatSetting::isTelegramEnabled(),
            ];

            return response()->json([
                'success' => true,
                'data' => $settings
            ]);

        } catch (\Exception $e) {
            Log::error('Get chat settings error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update Telegram bot settings (admin only)
     */
    public function updateTelegramSettings(Request $request)
    {
        try {
            if (!auth()->check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }

            // Check if user has admin privileges
            $user = auth()->user();
            if (!in_array($user->role, ['SuperAdmin', 'ChatAdmin'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ruxsat yo\'q'
                ], 403);
            }

            $validator = Validator::make($request->all(), [
                'telegram_bot_token' => 'required|string',
                'telegram_bot_username' => 'required|string|starts_with:@',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ], 422);
            }

            // Save settings
            ChatSetting::set('telegram_bot_token', $request->telegram_bot_token, 'string', false, 'Telegram Bot Token');
            ChatSetting::set('telegram_bot_username', $request->telegram_bot_username, 'string', true, 'Telegram Bot Username');

            // Auto-generate webhook URL from APP_URL
            $webhookUrl = url('/api/telegram/webhook');
            ChatSetting::set('telegram_webhook_url', $webhookUrl, 'string', true, 'Telegram Webhook URL');

            // Enable telegram
            ChatSetting::set('telegram_enabled', true, 'boolean', true, 'Telegram Enabled');

            // Try to set webhook and bot commands automatically using the new token
            try {
                $botToken = $request->telegram_bot_token;
                $apiUrl = "https://api.telegram.org/bot{$botToken}";

                // Set webhook
                $webhookResponse = Http::timeout(30)
                    ->retry(2, 100)
                    ->post("{$apiUrl}/setWebhook", [
                        'url' => $webhookUrl,
                        'allowed_updates' => ['message', 'callback_query'],
                    ]);

                $webhookResult = $webhookResponse->json();
                if (isset($webhookResult['ok']) && $webhookResult['ok'] === true) {
                    Log::info('Telegram webhook set successfully', ['url' => $webhookUrl]);
                } else {
                    Log::warning('Failed to set Telegram webhook automatically', ['result' => $webhookResult]);
                }

                // Set bot commands (menu)
                $commands = [
                    ['command' => 'start', 'description' => 'Botni boshlash'],
                    ['command' => 'help', 'description' => 'Yordam ma\'lumoti'],
                    ['command' => 'info', 'description' => 'Akademiya haqida'],
                    ['command' => 'contact', 'description' => 'Bog\'lanish ma\'lumotlari'],
                ];

                $commandsResponse = Http::timeout(30)
                    ->retry(2, 100)
                    ->post("{$apiUrl}/setMyCommands", [
                        'commands' => $commands
                    ]);

                $commandsResult = $commandsResponse->json();
                if (isset($commandsResult['ok']) && $commandsResult['ok'] === true) {
                    Log::info('Telegram bot commands set successfully');
                } else {
                    Log::warning('Failed to set Telegram bot commands', ['result' => $commandsResult]);
                }
            } catch (\Exception $webhookError) {
                Log::warning('Webhook/Commands setup warning: ' . $webhookError->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Sozlamalar saqlandi',
                'data' => [
                    'telegram_bot_username' => $request->telegram_bot_username,
                    'telegram_webhook_url' => $webhookUrl,
                    'telegram_enabled' => true,
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Update Telegram settings error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test Telegram bot connection (admin only)
     */
    public function testTelegramBot()
    {
        try {
            if (!auth()->check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }

            // Check if user has admin privileges
            $user = auth()->user();
            if (!in_array($user->role, ['SuperAdmin', 'ChatAdmin'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ruxsat yo\'q'
                ], 403);
            }

            $telegram = new TelegramService();
            $botInfo = $telegram->getMe();

            if (isset($botInfo['ok']) && $botInfo['ok'] === true && isset($botInfo['result'])) {
                return response()->json([
                    'success' => true,
                    'message' => 'Bot faol',
                    'data' => $botInfo['result']
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Bot bilan bog\'lanishda xatolik',
                'error' => $botInfo['error'] ?? $botInfo['description'] ?? 'Unknown error'
            ], 400);

        } catch (\Exception $e) {
            Log::error('Test Telegram bot error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }
}
