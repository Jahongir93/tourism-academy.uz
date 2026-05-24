<?php

namespace App\Http\Controllers;

use App\Models\Chat\ChatRoom;
use App\Models\Chat\ChatMessage;
use App\Models\Chat\ChatRoomMember;
use App\Models\ChatConversation;
use App\Models\ChatMessage as ConversationMessage;
use App\Models\ChatParticipant;
use App\Models\User;
use App\Models\Student;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ChatController extends Controller
{
    /**
     * Chat main page
     */
    public function index()
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        try {
            $user->updateOnlineStatus();
        } catch (\Exception $e) {
            // Column might not exist
        }

        // Get user's conversations
        $conversations = $this->getUserConversations();

        // Get public rooms
        $rooms = collect([]);
        try {
            $rooms = ChatRoom::active()
                ->public()
                ->withCount('members')
                ->orderBy('order_number')
                ->orderBy('name')
                ->get();
        } catch (\Exception $e) {
            // Table might not exist
        }

        $joinedRooms = [];
        try {
            $joinedRooms = $user->chatRooms()
                ->active()
                ->pluck('chat_rooms.id')
                ->toArray();
        } catch (\Exception $e) {
            // Ignore
        }

        return view('chat.index', compact('conversations', 'rooms', 'joinedRooms'));
    }

    /**
     * Get user's conversations
     */
    private function getUserConversations()
    {
        $userId = auth()->id();

        try {
            return ChatConversation::whereHas('participants', function($q) use ($userId) {
                    $q->where('user_id', $userId);
                })
                ->with(['users' => function($q) use ($userId) {
                    $q->where('users.id', '!=', $userId);
                }, 'lastMessage.user'])
                ->withCount(['messages as unread_count' => function($q) use ($userId) {
                    $q->where('user_id', '!=', $userId)
                        ->whereDoesntHave('reads', function($r) use ($userId) {
                            $r->where('user_id', $userId);
                        });
                }])
                ->orderByDesc('last_message_at')
                ->get();
        } catch (\Exception $e) {
            return collect([]);
        }
    }

    /**
     * Search users by nickname, name, student_id, or employee_code
     */
    public function searchUsers(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['users' => []]);
        }

        $search = $request->get('query', $request->get('q', ''));

        if (strlen($search) < 2) {
            return response()->json(['users' => []]);
        }

        $userId = auth()->id();

        // Search by nickname (with or without @)
        $nickname = ltrim($search, '@');

        $users = User::where('id', '!=', $userId)
            ->where(function($q) use ($search, $nickname) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nickname', 'like', "%{$nickname}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })
            ->select('id', 'name', 'nickname', 'is_online', 'last_seen_at')
            ->limit(20)
            ->get();

        // Also search by student_id
        $studentUsers = Student::where('student_id', 'like', "%{$search}%")
            ->whereNotNull('user_id')
            ->where('user_id', '!=', $userId)
            ->with('user:id,name,nickname,is_online,last_seen_at')
            ->limit(10)
            ->get()
            ->pluck('user')
            ->filter();

        // Search by employee_code
        $employeeUsers = Employee::where('employee_code', 'like', "%{$search}%")
            ->whereNotNull('user_id')
            ->where('user_id', '!=', $userId)
            ->with('user:id,name,nickname,is_online,last_seen_at')
            ->limit(10)
            ->get()
            ->pluck('user')
            ->filter();

        $allUsers = $users->merge($studentUsers)->merge($employeeUsers)->unique('id');

        return response()->json([
            'users' => $allUsers->map(function($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'nickname' => $user->nickname,
                    'display_name' => $user->display_name ?? $user->name,
                    'mention' => $user->nickname ? '@' . $user->nickname : null,
                    'is_online' => method_exists($user, 'isCurrentlyOnline') ? $user->isCurrentlyOnline() : false,
                    'identifier' => $user->chat_identifier ?? null,
                    'roles' => $user->roles ? $user->roles->pluck('name')->toArray() : [],
                ];
            })->values()
        ]);
    }

    /**
     * Start or get existing conversation with a user
     */
    public function startConversation(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate([
            'user_id' => 'required|exists:users,id'
        ]);

        $otherUserId = $request->user_id;
        $currentUserId = auth()->id();

        if ($otherUserId == $currentUserId) {
            return response()->json(['error' => 'O\'zingiz bilan yozisha olmaysiz'], 400);
        }

        try {
            // Check if conversation already exists
            $existingConversation = ChatConversation::where('type', 'private')
                ->whereHas('participants', function($q) use ($currentUserId) {
                    $q->where('user_id', $currentUserId);
                })
                ->whereHas('participants', function($q) use ($otherUserId) {
                    $q->where('user_id', $otherUserId);
                })
                ->first();

            if ($existingConversation) {
                return response()->json([
                    'success' => true,
                    'conversation_id' => $existingConversation->id,
                    'is_new' => false
                ]);
            }

            // Create new conversation
            $conversation = ChatConversation::create([
                'type' => 'private',
                'created_by' => $currentUserId,
            ]);

            // Add participants
            ChatParticipant::create([
                'conversation_id' => $conversation->id,
                'user_id' => $currentUserId,
                'is_admin' => true,
            ]);

            ChatParticipant::create([
                'conversation_id' => $conversation->id,
                'user_id' => $otherUserId,
            ]);

            return response()->json([
                'success' => true,
                'conversation_id' => $conversation->id,
                'is_new' => true
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Xatolik yuz berdi: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get conversation messages
     */
    public function getConversationMessages($conversationId)
    {
        if (!auth()->check()) {
            return response()->json([]);
        }

        try {
            $conversation = ChatConversation::findOrFail($conversationId);
            $userId = auth()->id();

            // Check if user is participant
            if (!$conversation->participants()->where('user_id', $userId)->exists()) {
                return response()->json(['error' => 'Ruxsat yo\'q'], 403);
            }

            // Mark messages as read
            $conversation->markAsReadForUser($userId);

            // Support incremental loading (only new messages)
            $query = ConversationMessage::where('conversation_id', $conversationId);
            $after = request('after', 0);
            if ($after > 0) {
                $query->where('id', '>', (int) $after);
            }

            $messages = $query->with('user:id,name,nickname')
                ->orderBy('created_at', 'asc')
                ->get()
                ->map(function($msg) use ($userId) {
                    return [
                        'id'             => $msg->id,
                        'user_id'        => $msg->user_id,
                        'user_name'      => $msg->user->display_name ?? $msg->user->name,
                        'nickname'       => $msg->user->nickname,
                        'message'        => $msg->message,
                        'type'           => $msg->type ?? 'text',
                        'file_url'       => $msg->file_url,
                        'is_own'         => $msg->user_id == $userId,
                        'is_edited'      => $msg->is_edited ?? false,
                        'created_at'     => $msg->created_at->format('H:i'),
                        'created_at_raw' => $msg->created_at->toISOString(),
                    ];
                });

            return response()->json($messages);
        } catch (\Exception $e) {
            return response()->json([]);
        }
    }

    /**
     * Send message in conversation
     */
    public function sendConversationMessage(Request $request, $conversationId)
    {
        if (!auth()->check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        try {
            $conversation = ChatConversation::findOrFail($conversationId);
            $userId = auth()->id();

            // Check if user is participant
            if (!$conversation->participants()->where('user_id', $userId)->exists()) {
                return response()->json(['error' => 'Ruxsat yo\'q'], 403);
            }

            $message = ConversationMessage::create([
                'conversation_id' => $conversationId,
                'user_id' => $userId,
                'message' => $request->message,
                'type' => 'text',
            ]);

            // Update conversation
            $conversation->update(['last_message_at' => now()]);
            $conversation->incrementUnreadForOthers($userId);

            // Update online status
            auth()->user()->updateOnlineStatus();

            $message->load('user:id,name,nickname');

            return response()->json([
                'id' => $message->id,
                'user_id' => $message->user_id,
                'user_name' => $message->user->display_name ?? $message->user->name,
                'message' => $message->message,
                'is_own' => true,
                'created_at' => $message->created_at->format('H:i'),
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Xatolik: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Contact admin/dean/prorector
     */
    public function contactRole(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate([
            'role' => 'required|in:admin,dean,prorector',
            'message' => 'required|string|max:2000',
        ]);

        $user = auth()->user();

        try {
            // Create contact request
            DB::table('chat_contact_requests')->insert([
                'from_user_id' => $user->id,
                'to_role' => $request->role,
                'message' => $request->message,
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Murojaatingiz yuborildi. Tez orada javob olasiz.'
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Xatolik yuz berdi'], 500);
        }
    }

    /**
     * Get user's contact requests
     */
    public function getContactRequests()
    {
        if (!auth()->check()) {
            return response()->json([]);
        }

        try {
            $requests = DB::table('chat_contact_requests')
                ->where('from_user_id', auth()->id())
                ->orderByDesc('created_at')
                ->get();

            return response()->json($requests);
        } catch (\Exception $e) {
            return response()->json([]);
        }
    }

    /**
     * Update user's nickname
     */
    public function updateNickname(Request $request)
    {
        try {
            if (!auth()->check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tizimga kirish talab qilinadi'
                ], 401);
            }

            $validator = \Validator::make($request->all(), [
                'nickname' => 'required|string|min:3|max:30|regex:/^[a-zA-Z0-9_]+$/|unique:users,nickname,' . auth()->id(),
            ], [
                'nickname.required' => 'Nickname kiriting',
                'nickname.regex' => 'Nickname faqat harflar, raqamlar va pastki chiziqdan iborat bo\'lishi kerak',
                'nickname.unique' => 'Bu nickname allaqachon band',
                'nickname.min' => 'Nickname kamida 3 ta belgidan iborat bo\'lishi kerak',
                'nickname.max' => 'Nickname 30 ta belgidan oshmasligi kerak',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first(),
                    'errors' => $validator->errors()
                ], 422);
            }

            $user = auth()->user();
            $user->update(['nickname' => $request->nickname]);

            \Log::info('Nickname updated successfully', [
                'user_id' => $user->id,
                'old_nickname' => $user->getOriginal('nickname'),
                'new_nickname' => $request->nickname
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Nickname muvaffaqiyatli o\'zgartirildi',
                'nickname' => $request->nickname,
                'mention' => '@' . $request->nickname,
            ]);

        } catch (\Exception $e) {
            \Log::error('Error updating nickname: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Xatolik yuz berdi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get unread messages count for all conversations
     */
    public function getTotalUnreadCount()
    {
        if (!auth()->check()) {
            return response()->json(['count' => 0]);
        }

        try {
            $count = ChatParticipant::where('user_id', auth()->id())
                ->sum('unread_count');

            return response()->json(['count' => $count]);
        } catch (\Exception $e) {
            return response()->json(['count' => 0]);
        }
    }

    /**
     * Mark conversation as read
     */
    public function markConversationAsRead($conversationId)
    {
        if (!auth()->check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        try {
            $conversation = ChatConversation::findOrFail($conversationId);
            $conversation->markAsReadForUser(auth()->id());

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error'], 500);
        }
    }

    // ========== Legacy methods for ChatRoom system ==========

    public function room($slug)
    {
        try {
            $room = ChatRoom::where('slug', $slug)
                ->active()
                ->firstOrFail();

            if (!auth()->check()) {
                session()->flash('info', 'Chat xonasiga kirish uchun tizimga kirishingiz kerak.');
                return redirect()->route('login');
            }

            if ($room->type === 'public' && !$room->isMember()) {
                $room->join();
            }

            if ($room->type === 'private' && !$room->isMember()) {
                session()->flash('warning', 'Bu yopiq chat xonasi. Kirish uchun ruxsat kerak.');
                return redirect()->route('chat.index');
            }

            $member = $room->members()->where('user_id', auth()->id())->first();
            if ($member) {
                $member->updateLastSeen();
            }

            auth()->user()->updateOnlineStatus();

            $messages = $room->messages()
                ->with(['user', 'parent.user', 'reactions'])
                ->notDeleted()
                ->latest()
                ->paginate(50);

            $messages->setCollection($messages->getCollection()->reverse());

            $members = $room->users()
                ->withPivot('role', 'last_seen_at')
                ->orderBy('pivot_role')
                ->orderBy('name')
                ->get();

            return view('chat.room', compact('room', 'messages', 'members'));

        } catch (\Exception $e) {
            session()->flash('error', 'Chat xonasi topilmadi.');
            return redirect()->route('chat.index');
        }
    }

    public function sendMessage(Request $request, $roomId)
    {
        if (!auth()->check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate([
            'message' => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:chat_messages,id'
        ]);

        try {
            $room = ChatRoom::findOrFail($roomId);

            if (!$room->isMember()) {
                return response()->json(['error' => 'Not a member'], 403);
            }

            $member = $room->members()->where('user_id', auth()->id())->first();
            if ($member && $member->checkMuteStatus()) {
                return response()->json(['error' => 'You are muted'], 403);
            }

            $message = $room->messages()->create([
                'user_id' => auth()->id(),
                'message' => $request->message,
                'parent_id' => $request->parent_id
            ]);

            $room->members()
                ->where('user_id', '!=', auth()->id())
                ->increment('unread_count');

            $message->load(['user', 'parent.user']);

            auth()->user()->updateOnlineStatus();

            return response()->json([
                'success' => true,
                'message' => $message
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Server error'], 500);
        }
    }

    public function toggleReaction(Request $request, $messageId)
    {
        if (!auth()->check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate([
            'emoji' => 'required|string|max:10'
        ]);

        try {
            $message = ChatMessage::findOrFail($messageId);

            if (!$message->room->isMember()) {
                return response()->json(['error' => 'Not a member'], 403);
            }

            $added = $message->toggleReaction($request->emoji);

            return response()->json([
                'success' => true,
                'added' => $added,
                'reactions' => $message->getReactionSummary()
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Server error'], 500);
        }
    }

    public function joinRoom($roomId)
    {
        if (!auth()->check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        try {
            $room = ChatRoom::findOrFail($roomId);

            if ($room->type !== 'public') {
                return response()->json(['error' => 'Cannot join private room'], 403);
            }

            $joined = $room->join();

            if (!$joined) {
                return response()->json(['error' => 'Room is full'], 403);
            }

            return response()->json([
                'success' => true,
                'message' => 'Successfully joined'
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Server error'], 500);
        }
    }

    public function leaveRoom($roomId)
    {
        if (!auth()->check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        try {
            $room = ChatRoom::findOrFail($roomId);
            $room->leave();

            return response()->json([
                'success' => true,
                'message' => 'Successfully left'
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Server error'], 500);
        }
    }

    public function deleteMessage($messageId)
    {
        if (!auth()->check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        try {
            $message = ChatMessage::findOrFail($messageId);

            if ($message->user_id !== auth()->id() && !$message->room->isModerator()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $message->softDelete();

            return response()->json([
                'success' => true,
                'message' => 'Message deleted'
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Server error'], 500);
        }
    }

    public function getUsersList()
    {
        \Log::info('getUsersList called', ['auth' => auth()->check(), 'user_id' => auth()->id()]);

        if (!auth()->check()) {
            \Log::warning('getUsersList: User not authenticated');
            return response()->json([]);
        }

        try {
            $users = User::where('id', '!=', auth()->id())
                ->whereNotNull('name')
                ->orderBy('name')
                ->limit(100)
                ->get(['id', 'name', 'nickname', 'email', 'is_online', 'last_seen_at'])
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
                        'name' => $user->name ?? 'Unknown',
                        'nickname' => $user->nickname,
                        'email' => $user->email,
                        'is_online' => $isOnline,
                    ];
                })->values();

            \Log::info('getUsersList success', ['count' => count($users)]);
            return response()->json($users);
        } catch (\Exception $e) {
            \Log::error('Chat getUsersList error: ' . $e->getMessage());
            return response()->json([]);
        }
    }

    public function getConversations()
    {
        if (!auth()->check()) {
            return response()->json([]);
        }

        $conversations = $this->getUserConversations();

        return response()->json($conversations->map(function($conv) {
            $otherUser = $conv->users->first();
            $lastMsg   = $conv->lastMessage;
            return [
                'id'              => $conv->id,
                'name'            => $conv->getNameForUser(auth()->id()),
                'other_user_id'   => $otherUser?->id,
                'last_message'    => $lastMsg?->message,
                'last_message_at' => $lastMsg?->created_at?->toISOString(),
                'unread_count'    => $conv->unread_count ?? 0,
                'is_online'       => $otherUser ? (method_exists($otherUser,'isCurrentlyOnline') ? $otherUser->isCurrentlyOnline() : false) : false,
            ];
        }));
    }

    public function createConversation(Request $request)
    {
        return $this->startConversation($request);
    }

    public function getMessages($conversationId)
    {
        return $this->getConversationMessages($conversationId);
    }

    public function sendDirectMessage(Request $request, $conversationId)
    {
        return $this->sendConversationMessage($request, $conversationId);
    }

    public function markAsRead($conversationId)
    {
        return $this->markConversationAsRead($conversationId);
    }

    public function getUnreadCount()
    {
        return $this->getTotalUnreadCount();
    }

    // API Methods for AJAX calls
    public function apiGetUsers()
    {
        try {
            if (!auth()->check()) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $users = User::where('id', '!=', auth()->id())
                ->whereNotNull('name')
                ->select('id', 'name', 'email')
                ->orderBy('name')
                ->limit(100)
                ->get()
                ->map(function($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'avatar' => strtoupper(substr($user->name, 0, 1))
                    ];
                });

            return response()->json(['success' => true, 'users' => $users]);

        } catch (\Exception $e) {
            \Log::error('apiGetUsers error: ' . $e->getMessage());
            return response()->json(['error' => 'Server error'], 500);
        }
    }

    public function apiCreateConversation(Request $request)
    {
        try {
            if (!auth()->check()) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $userId = $request->input('user_id');

            if (!$userId) {
                return response()->json(['error' => 'User ID required'], 400);
            }

            // Find existing conversation between these two users
            $conversation = ChatConversation::where('type', 'private')
                ->whereHas('users', function($q) use ($userId) {
                    $q->where('user_id', $userId);
                })
                ->whereHas('users', function($q) {
                    $q->where('user_id', auth()->id());
                })
                ->first();

            if (!$conversation) {
                // Create new private conversation
                $conversation = ChatConversation::create([
                    'type' => 'private',
                    'created_by' => auth()->id(),
                ]);

                // Add both users as participants
                $conversation->users()->attach([
                    auth()->id() => ['is_admin' => false],
                    $userId => ['is_admin' => false],
                ]);
            }

            return response()->json([
                'success' => true,
                'conversation_id' => $conversation->id
            ]);

        } catch (\Exception $e) {
            \Log::error('apiCreateConversation error: ' . $e->getMessage(), [
                'user_id' => $request->input('user_id'),
                'auth_id' => auth()->id(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'error' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function apiGetMessages(Request $request)
    {
        try {
            if (!auth()->check()) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $conversationId = $request->input('conversation_id');

            $messages = ChatMessage::where('conversation_id', $conversationId)
                ->with('user:id,name')
                ->orderBy('created_at', 'asc')
                ->get()
                ->map(function($msg) {
                    return [
                        'id' => $msg->id,
                        'message' => $msg->message,
                        'user_id' => $msg->user_id,
                        'user_name' => $msg->user->name ?? 'Unknown',
                        'created_at' => $msg->created_at->format('H:i'),
                        'is_mine' => $msg->user_id == auth()->id()
                    ];
                });

            return response()->json(['success' => true, 'messages' => $messages]);

        } catch (\Exception $e) {
            \Log::error('apiGetMessages error: ' . $e->getMessage());
            return response()->json(['error' => 'Server error'], 500);
        }
    }

    public function apiSendMessage(Request $request)
    {
        try {
            if (!auth()->check()) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $request->validate([
                'conversation_id' => 'required|exists:chat_conversations,id',
                'message' => 'required|string|max:1000'
            ]);

            // Use ConversationMessage (App\Models\ChatMessage) not Chat\ChatMessage
            $message = ConversationMessage::create([
                'conversation_id' => $request->conversation_id,
                'user_id' => auth()->id(),
                'message' => $request->message
            ]);

            // Load user relationship for broadcasting
            $message->load('user');

            // Update conversation timestamp
            $conversation = ChatConversation::find($request->conversation_id);
            if ($conversation) {
                $conversation->update([
                    'updated_at' => now(),
                    'last_message_at' => now(),
                ]);
            }

            // Try to broadcast the new message event (will fail silently if WebSocket not available)
            try {
                broadcast(new \App\Events\NewMessage($message))->toOthers();
            } catch (\Exception $broadcastError) {
                \Log::warning('Broadcast failed (WebSocket might be unavailable): ' . $broadcastError->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => [
                    'id' => $message->id,
                    'message' => $message->message,
                    'user_id' => $message->user_id,
                    'user' => [
                        'id' => $message->user->id,
                        'name' => $message->user->name,
                    ],
                    'created_at' => $message->created_at->format('H:i'),
                    'formatted_time' => $message->created_at->format('d.m.Y H:i'),
                    'is_mine' => true
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('apiSendMessage error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            return response()->json([
                'error' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle typing indicator
     */
    public function apiTyping(Request $request)
    {
        try {
            if (!auth()->check()) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $request->validate([
                'conversation_id' => 'required|exists:chat_conversations,id',
                'is_typing' => 'required|boolean'
            ]);

            // Broadcast typing event
            broadcast(new \App\Events\UserTyping(
                auth()->id(),
                auth()->user()->name,
                $request->conversation_id,
                $request->is_typing
            ))->toOthers();

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            \Log::error('apiTyping error: ' . $e->getMessage());
            return response()->json(['error' => 'Server error'], 500);
        }
    }

    /**
     * Upload voice message
     */
    public function uploadVoice(Request $request)
    {
        try {
            if (!auth()->check()) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $request->validate([
                'voice' => 'required|file|mimes:webm,ogg,mp3,wav|max:10240', // 10MB max
                'conversation_id' => 'required|exists:chat_conversations,id'
            ]);

            $conversationId = $request->conversation_id;
            $conversation = ChatConversation::findOrFail($conversationId);
            $userId = auth()->id();

            // Check if user is participant
            if (!$conversation->participants()->where('user_id', $userId)->exists()) {
                return response()->json(['error' => 'Ruxsat yo\'q'], 403);
            }

            // Create directory if not exists
            $voiceDir = public_path('images/chat/voice');
            if (!file_exists($voiceDir)) {
                mkdir($voiceDir, 0755, true);
            }

            // Upload voice file
            $file = $request->file('voice');
            $filename = time() . '_' . $userId . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move($voiceDir, $filename);
            $voicePath = 'images/chat/voice/' . $filename;

            // Save message with voice path
            $message = ConversationMessage::create([
                'conversation_id' => $conversationId,
                'user_id' => $userId,
                'message' => '[Ovozli xabar]',
                'type' => 'voice',
                'file_url' => $voicePath,
            ]);

            // Update conversation
            $conversation->update(['last_message_at' => now()]);

            $message->load('user:id,name,nickname');

            return response()->json([
                'success' => true,
                'message' => [
                    'id' => $message->id,
                    'user_id' => $message->user_id,
                    'voice_path' => asset($voicePath),
                    'created_at' => $message->created_at->format('H:i'),
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Upload voice error: ' . $e->getMessage());
            return response()->json(['error' => 'Yuklashda xatolik'], 500);
        }
    }

    /**
     * Upload file (image, document, etc)
     */
    public function uploadFile(Request $request)
    {
        try {
            if (!auth()->check()) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $request->validate([
                'file' => 'required|file|max:10240', // 10MB max
                'conversation_id' => 'required|exists:chat_conversations,id'
            ]);

            $conversationId = $request->conversation_id;
            $conversation = ChatConversation::findOrFail($conversationId);
            $userId = auth()->id();

            // Check if user is participant
            if (!$conversation->participants()->where('user_id', $userId)->exists()) {
                return response()->json(['error' => 'Ruxsat yo\'q'], 403);
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
            $filename = time() . '_' . $userId . '_' . Str::slug($originalName) . '.' . $extension;
            $file->move($fileDir, $filename);
            $filePath = 'images/chat/files/' . $filename;

            // Determine file type
            $fileExt = strtolower($extension);
            $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
            $videoExtensions = ['mp4', 'webm', 'ogg', 'avi', 'mov'];

            if (in_array($fileExt, $imageExtensions)) {
                $fileType = 'image';
                $messageText = '[Rasm]';
            } elseif (in_array($fileExt, $videoExtensions)) {
                $fileType = 'video';
                $messageText = '[Video]';
            } else {
                $fileType = 'document';
                $messageText = '[Fayl: ' . $originalName . '.' . $extension . ']';
            }

            // Save message with file path
            $message = ConversationMessage::create([
                'conversation_id' => $conversationId,
                'user_id' => $userId,
                'message' => $messageText,
                'type' => $fileType,
                'file_url' => $filePath,
            ]);

            // Update conversation
            $conversation->update(['last_message_at' => now()]);

            $message->load('user:id,name,nickname');

            return response()->json([
                'success' => true,
                'message' => [
                    'id' => $message->id,
                    'user_id' => $message->user_id,
                    'message' => $messageText,
                    'file_path' => asset($filePath),
                    'file_type' => $fileType,
                    'created_at' => $message->created_at->format('H:i'),
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Upload file error: ' . $e->getMessage());
            return response()->json(['error' => 'Yuklashda xatolik: ' . $e->getMessage()], 500);
        }
    }
}
