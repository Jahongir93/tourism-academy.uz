<?php

namespace App\Services;

use App\Models\TelegramMessage;
use App\Models\User;
use App\Models\ChatSetting;
use App\Models\Notification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    protected ?string $botToken;
    protected ?string $apiUrl;

    public function __construct()
    {
        // Try to get from database first, then fall back to config
        $this->botToken = ChatSetting::getTelegramBotToken() ?: config('services.telegram.bot_token') ?: null;
        $this->apiUrl = $this->botToken ? "https://api.telegram.org/bot{$this->botToken}" : null;
    }

    /**
     * Check if Telegram is configured
     */
    protected function isConfigured(): bool
    {
        return !empty($this->botToken);
    }

    /**
     * Send message to Telegram
     */
    public function sendMessage(string $chatId, string $text, array $options = []): array
    {
        if (!$this->isConfigured()) {
            Log::warning('Telegram not configured, skipping message send');
            return [
                'success' => false,
                'error' => 'Telegram not configured',
            ];
        }

        try {
            $params = array_merge([
                'chat_id' => $chatId,
                'text' => $text,
                'parse_mode' => 'HTML',
            ], $options);

            $response = Http::timeout(30) // 30 seconds timeout
                ->retry(2, 100) // Retry 2 times with 100ms delay
                ->post("{$this->apiUrl}/sendMessage", $params);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                ];
            }

            Log::error('Telegram send message failed', [
                'response' => $response->json(),
                'chat_id' => $chatId,
            ]);

            return [
                'success' => false,
                'error' => $response->json()['description'] ?? 'Unknown error',
            ];
        } catch (\Exception $e) {
            Log::error('Telegram send message exception', [
                'error' => $e->getMessage(),
                'chat_id' => $chatId,
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Send message to all admins
     */
    public function sendToAdmins(string $message): array
    {
        $adminChatIds = $this->getAdminChatIds();
        $results = [];

        foreach ($adminChatIds as $chatId) {
            $results[$chatId] = $this->sendMessage($chatId, $message);
        }

        return $results;
    }

    /**
     * Process incoming webhook update
     */
    public function processUpdate(array $update): void
    {
        // Handle regular messages
        if (isset($update['message'])) {
            $this->handleMessage($update['message']);
        }

        // Handle callback queries (inline keyboard buttons)
        if (isset($update['callback_query'])) {
            $this->handleCallbackQuery($update['callback_query']);
        }
    }

    /**
     * Handle incoming message
     */
    protected function handleMessage(array $message): void
    {
        $chatId = $message['chat']['id'];
        $text = $message['text'] ?? '';
        $from = $message['from'] ?? [];

        // Handle commands (don't save to database)
        if (str_starts_with($text, '/')) {
            $this->handleCommand($chatId, $text, $from);
            return;
        }

        // Handle reply keyboard button presses (don't save to database)
        if ($this->handleReplyKeyboardButton($chatId, $text, $from)) {
            return;
        }

        // Only save actual user messages (not buttons/commands) to database
        TelegramMessage::create([
            'telegram_chat_id' => $chatId,
            'telegram_message_id' => $message['message_id'],
            'telegram_user_id' => $from['id'] ?? null,
            'telegram_username' => $from['username'] ?? null,
            'telegram_first_name' => $from['first_name'] ?? null,
            'telegram_last_name' => $from['last_name'] ?? null,
            'message' => $text,
            'direction' => 'incoming',
            'status' => 'new',
        ]);

        // Create notifications for ChatAdmin and SuperAdmin users
        $this->createNotificationForAdmins($from, $text);

        // Forward user message to admins
        $this->forwardMessageToAdmins($message, $from);

        // Send auto-reply to user with keyboard
        $keyboard = [
            'inline_keyboard' => [
                [
                    ['text' => '📚 Akademiya haqida', 'callback_data' => 'info'],
                    ['text' => '📞 Bog\'lanish', 'callback_data' => 'contact'],
                ],
                [
                    ['text' => '🏠 Asosiy menyu', 'callback_data' => 'back_to_menu'],
                ],
            ]
        ];

        $this->sendMessage($chatId,
            "✅ <b>Xabaringiz qabul qilindi!</b>\n\n" .
            "Tez orada sizga javob beramiz. Rahmat!\n\n" .
            "🕐 Kutish vaqti: 1-2 soat",
            [
                'reply_markup' => json_encode($keyboard)
            ]
        );
    }

    /**
     * Handle Telegram commands
     */
    protected function handleCommand(string $chatId, string $command, array $from): void
    {
        if (str_starts_with($command, '/start')) {
            $this->sendWelcomeMessage($chatId, $from);
        } elseif (str_starts_with($command, '/help')) {
            $this->sendHelpMessage($chatId);
        } elseif (str_starts_with($command, '/contact')) {
            $this->sendContactInfo($chatId);
        } elseif (str_starts_with($command, '/info')) {
            $this->sendAcademyInfo($chatId);
        }
    }

    /**
     * Handle reply keyboard button presses
     * Returns true if handled, false if not a keyboard button
     */
    protected function handleReplyKeyboardButton(string $chatId, string $text, array $from): bool
    {
        switch ($text) {
            case '📚 Akademiya haqida':
                $this->sendAcademyInfo($chatId);
                return true;

            case '📞 Bog\'lanish':
                $this->sendContactInfo($chatId);
                return true;

            case '❓ Yordam':
                $this->sendHelpMessage($chatId);
                return true;

            case '💬 Murojaat yuborish':
                $this->sendMessage($chatId,
                    "💬 <b>Murojaat yuborish</b>\n\n" .
                    "Iltimos, xabaringizni yuboring. Tez orada javob beramiz!\n\n" .
                    "Siz yozishingiz mumkin:\n" .
                    "• Savol\n" .
                    "• Murojaat\n" .
                    "• Takliflar"
                );
                return true;

            default:
                return false;
        }
    }

    /**
     * Send welcome message
     */
    protected function sendWelcomeMessage(string $chatId, array $from): void
    {
        $name = $from['first_name'] ?? 'Foydalanuvchi';

        $message = "👋 Assalomu alaykum, {$name}!\n\n";
        $message .= "🎓 <b>Tourism Academy</b> rasmiy botiga xush kelibsiz!\n\n";
        $message .= "📝 Men orqali quyidagilarni amalga oshirishingiz mumkin:\n\n";
        $message .= "• 💬 Murojaat yuborish\n";
        $message .= "• ❓ Savol berish\n";
        $message .= "• 📚 Ma'lumot olish\n";
        $message .= "• 📞 Bog'lanish\n\n";
        $message .= "Quyidagi tugmalardan foydalaning yoki menga xabar yuboring!";

        // Persistent reply keyboard (always visible at bottom)
        $keyboard = [
            'keyboard' => [
                [
                    ['text' => '📚 Akademiya haqida'],
                    ['text' => '📞 Bog\'lanish'],
                ],
                [
                    ['text' => '❓ Yordam'],
                    ['text' => '💬 Murojaat yuborish'],
                ],
            ],
            'resize_keyboard' => true,
            'one_time_keyboard' => false,
        ];

        $this->sendMessage($chatId, $message, [
            'reply_markup' => json_encode($keyboard)
        ]);
    }

    /**
     * Send help message
     */
    protected function sendHelpMessage(string $chatId): void
    {
        $message = "❓ <b>Yordam</b>\n\n";
        $message .= "Bot bilan ishlash bo'yicha qo'llanma:\n\n";
        $message .= "<b>Buyruqlar:</b>\n";
        $message .= "/start - Botni boshlash\n";
        $message .= "/help - Yordam ma'lumoti\n";
        $message .= "/info - Akademiya haqida\n";
        $message .= "/contact - Bog'lanish\n\n";
        $message .= "<b>Qanday foydalanish:</b>\n";
        $message .= "1️⃣ Tugmalardan birini tanlang\n";
        $message .= "2️⃣ Yoki oddiy xabar yuboring\n";
        $message .= "3️⃣ Admin tez orada javob beradi\n\n";
        $message .= "💡 Savol-murojaat bo'lsa, menga xabar yozing!";

        $keyboard = [
            'inline_keyboard' => [
                [
                    ['text' => '🏠 Asosiy menyu', 'callback_data' => 'back_to_menu'],
                ],
            ]
        ];

        $this->sendMessage($chatId, $message, [
            'reply_markup' => json_encode($keyboard)
        ]);
    }

    /**
     * Send contact information
     */
    protected function sendContactInfo(string $chatId): void
    {
        $message = "📞 <b>Bog'lanish</b>\n\n";
        $message .= "🎓 <b>Tourism Academy</b>\n\n";
        $message .= "📍 <b>Manzil:</b>\n";
        $message .= "Toshkent shahri\n\n";
        $message .= "📧 <b>Email:</b>\n";
        $message .= "info@tourismacademy.uz\n\n";
        $message .= "📱 <b>Telefon:</b>\n";
        $message .= "+998 XX XXX XX XX\n\n";
        $message .= "🌐 <b>Website:</b>\n";
        $message .= "www.tourismacademy.uz\n\n";
        $message .= "⏰ <b>Ish vaqti:</b>\n";
        $message .= "Dushanba - Juma: 9:00 - 18:00\n";
        $message .= "Shanba - Yakshanba: Dam olish\n\n";
        $message .= "💬 Menga xabar yuboring va tez orada javob beramiz!";

        $keyboard = [
            'inline_keyboard' => [
                [
                    ['text' => '💬 Murojaat yuborish', 'callback_data' => 'send_message'],
                ],
                [
                    ['text' => '🏠 Asosiy menyu', 'callback_data' => 'back_to_menu'],
                ],
            ]
        ];

        $this->sendMessage($chatId, $message, [
            'reply_markup' => json_encode($keyboard)
        ]);
    }

    /**
     * Send academy information
     */
    protected function sendAcademyInfo(string $chatId): void
    {
        $message = "📚 <b>Tourism Academy haqida</b>\n\n";
        $message .= "🎓 Tourism Academy - turizm sohasida yuqori malakali mutaxassislar tayyorlovchi yetakchi ta'lim muassasasi.\n\n";
        $message .= "<b>🎯 Bizning yo'nalishlar:</b>\n";
        $message .= "• Turizm va mehmonxona xo'jaligi\n";
        $message .= "• Sayohat biznesini boshqarish\n";
        $message .= "• Ekskursiya faoliyati\n";
        $message .= "• Restoranlar va ovqatlanish xizmati\n\n";
        $message .= "<b>✨ Afzalliklar:</b>\n";
        $message .= "✅ Zamonaviy o'quv dasturlari\n";
        $message .= "✅ Tajribali ustozlar\n";
        $message .= "✅ Amaliy mashg'ulotlar\n";
        $message .= "✅ Xalqaro sertifikatlar\n";
        $message .= "✅ Ish bilan ta'minlash\n\n";
        $message .= "📞 Qo'shimcha ma'lumot uchun biz bilan bog'laning!";

        $keyboard = [
            'inline_keyboard' => [
                [
                    ['text' => '📞 Bog\'lanish', 'callback_data' => 'contact'],
                    ['text' => '💬 Savol berish', 'callback_data' => 'send_message'],
                ],
                [
                    ['text' => '🏠 Asosiy menyu', 'callback_data' => 'back_to_menu'],
                ],
            ]
        ];

        $this->sendMessage($chatId, $message, [
            'reply_markup' => json_encode($keyboard)
        ]);
    }

    /**
     * Forward message to all admins
     */
    protected function forwardMessageToAdmins(array $message, array $from): void
    {
        $name = trim(($from['first_name'] ?? '') . ' ' . ($from['last_name'] ?? ''));
        $username = $from['username'] ?? 'username yo\'q';
        $text = $message['text'] ?? '';
        $chatId = $message['chat']['id'];

        $adminMessage = "📩 <b>Yangi murojaat!</b>\n\n";
        $adminMessage .= "👤 <b>Yuboruvchi:</b> {$name}\n";
        $adminMessage .= "📝 <b>Username:</b> @{$username}\n";
        $adminMessage .= "🆔 <b>Chat ID:</b> <code>{$chatId}</code>\n\n";
        $adminMessage .= "💬 <b>Xabar:</b>\n{$text}\n\n";
        $adminMessage .= "---\n";
        $adminMessage .= "Javob berish uchun ChatAdmin dashboard'dan foydalaning.";

        $this->sendToAdmins($adminMessage);
    }

    /**
     * Handle callback query (inline keyboard button clicks)
     */
    protected function handleCallbackQuery(array $callbackQuery): void
    {
        $callbackId = $callbackQuery['id'];
        $data = $callbackQuery['data'] ?? '';
        $chatId = $callbackQuery['message']['chat']['id'] ?? null;

        if (!$chatId) {
            return;
        }

        // Handle different callback actions
        switch ($data) {
            case 'info':
                $this->sendAcademyInfo($chatId);
                $this->answerCallbackQuery($callbackId, '📚 Ma\'lumot yuborildi');
                break;

            case 'help':
                $this->sendHelpMessage($chatId);
                $this->answerCallbackQuery($callbackId, '❓ Yordam ma\'lumoti yuborildi');
                break;

            case 'contact':
                $this->sendContactInfo($chatId);
                $this->answerCallbackQuery($callbackId, '📞 Bog\'lanish ma\'lumoti yuborildi');
                break;

            case 'send_message':
                $this->sendMessage($chatId,
                    "💬 <b>Murojaat yuborish</b>\n\n" .
                    "Iltimos, xabaringizni yuboring. Tez orada javob beramiz!\n\n" .
                    "Siz yozishingiz mumkin:\n" .
                    "• Savol\n" .
                    "• Murojaat\n" .
                    "• Takliflar"
                );
                $this->answerCallbackQuery($callbackId, '✍️ Xabaringizni yozing');
                break;

            case 'back_to_menu':
                $this->sendWelcomeMessage($chatId, $callbackQuery['from'] ?? []);
                $this->answerCallbackQuery($callbackId, '🏠 Asosiy menyu');
                break;

            default:
                $this->answerCallbackQuery($callbackId, 'Tugma bosildi!');
        }
    }

    /**
     * Answer callback query
     */
    public function answerCallbackQuery(string $callbackQueryId, string $text = ''): array
    {
        return Http::post("{$this->apiUrl}/answerCallbackQuery", [
            'callback_query_id' => $callbackQueryId,
            'text' => $text,
        ])->json();
    }

    /**
     * Set webhook
     */
    public function setWebhook(string $url): array
    {
        if (!$this->isConfigured()) {
            return ['ok' => false, 'error' => 'Telegram not configured'];
        }

        $response = Http::timeout(30)
            ->retry(2, 100)
            ->post("{$this->apiUrl}/setWebhook", [
                'url' => $url,
                'allowed_updates' => ['message', 'callback_query'],
            ]);

        return $response->json();
    }

    /**
     * Get webhook info
     */
    public function getWebhookInfo(): array
    {
        if (!$this->isConfigured()) {
            return ['ok' => false, 'error' => 'Telegram not configured'];
        }

        return Http::timeout(30)
            ->retry(2, 100)
            ->get("{$this->apiUrl}/getWebhookInfo")
            ->json();
    }

    /**
     * Delete webhook
     */
    public function deleteWebhook(): array
    {
        if (!$this->isConfigured()) {
            return ['ok' => false, 'error' => 'Telegram not configured'];
        }

        return Http::timeout(30)
            ->retry(2, 100)
            ->get("{$this->apiUrl}/deleteWebhook")
            ->json();
    }

    /**
     * Get bot info
     */
    public function getMe(): array
    {
        if (!$this->isConfigured()) {
            return ['ok' => false, 'error' => 'Telegram not configured'];
        }

        return Http::timeout(30)
            ->retry(2, 100)
            ->get("{$this->apiUrl}/getMe")
            ->json();
    }

    /**
     * Set bot commands (menu)
     */
    public function setBotCommands(): array
    {
        if (!$this->isConfigured()) {
            return ['ok' => false, 'error' => 'Telegram not configured'];
        }

        $commands = [
            ['command' => 'start', 'description' => 'Botni boshlash'],
            ['command' => 'help', 'description' => 'Yordam ma\'lumoti'],
            ['command' => 'info', 'description' => 'Akademiya haqida'],
            ['command' => 'contact', 'description' => 'Bog\'lanish ma\'lumotlari'],
        ];

        return Http::timeout(30)
            ->retry(2, 100)
            ->post("{$this->apiUrl}/setMyCommands", [
                'commands' => $commands
            ])
            ->json();
    }

    /**
     * Get admin chat IDs from config
     */
    protected function getAdminChatIds(): array
    {
        $chatIds = config('services.telegram.admin_chat_ids');

        if (empty($chatIds)) {
            return [];
        }

        if (is_string($chatIds)) {
            return array_filter(array_map('trim', explode(',', $chatIds)));
        }

        return $chatIds;
    }

    /**
     * Send contact request notification
     */
    public function sendContactRequestNotification(User $user, string $message): array
    {
        $text = "📨 <b>Yangi murojaat (Web)</b>\n\n";
        $text .= "👤 <b>Foydalanuvchi:</b> {$user->name}\n";
        $text .= "📧 <b>Email:</b> {$user->email}\n";
        $text .= "🆔 <b>ID:</b> {$user->id}\n\n";
        $text .= "💬 <b>Xabar:</b>\n{$message}";

        return $this->sendToAdmins($text);
    }

    /**
     * Create notification for ChatAdmin and SuperAdmin users
     */
    protected function createNotificationForAdmins(array $from, string $text): void
    {
        try {
            // Get users with ChatAdmin or SuperAdmin roles
            $adminUsers = User::whereHas('roles', function ($query) {
                $query->whereIn('name', ['ChatAdmin', 'SuperAdmin']);
            })->get();

            if ($adminUsers->isEmpty()) {
                return;
            }

            $name = trim(($from['first_name'] ?? '') . ' ' . ($from['last_name'] ?? ''));
            $username = $from['username'] ?? '';

            $title = 'Yangi Telegram xabari';
            $messagePreview = mb_strlen($text) > 50 ? mb_substr($text, 0, 50) . '...' : $text;
            $notificationMessage = $username
                ? "📱 {$name} (@{$username}): {$messagePreview}"
                : "📱 {$name}: {$messagePreview}";

            // Create notification for each admin
            foreach ($adminUsers as $admin) {
                Notification::create([
                    'user_id' => $admin->id,
                    'type' => 'telegram_message',
                    'title' => $title,
                    'message' => $notificationMessage,
                    'data' => json_encode([
                        'telegram_chat_id' => $from['id'] ?? null,
                        'telegram_username' => $username,
                        'telegram_first_name' => $from['first_name'] ?? null,
                        'telegram_last_name' => $from['last_name'] ?? null,
                        'message' => $text,
                    ]),
                ]);
            }

            Log::info('Telegram message notifications created', [
                'admin_count' => $adminUsers->count(),
                'from' => $name
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to create Telegram notifications', [
                'error' => $e->getMessage()
            ]);
        }
    }
}
