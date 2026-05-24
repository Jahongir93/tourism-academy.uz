<?php

namespace App\Http\Controllers;

use App\Models\TelegramMessage;
use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class TelegramController extends Controller
{
    protected TelegramService $telegram;

    public function __construct(TelegramService $telegram)
    {
        $this->telegram = $telegram;
    }

    /**
     * Handle incoming webhook from Telegram
     */
    public function webhook(Request $request): JsonResponse
    {
        try {
            $update = $request->all();

            Log::info('Telegram webhook received', ['update' => $update]);

            // Process the update
            $this->telegram->processUpdate($update);

            return response()->json(['ok' => true]);
        } catch (\Exception $e) {
            Log::error('Telegram webhook error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json(['ok' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Send message from admin to user
     */
    public function sendMessage(Request $request): JsonResponse
    {
        $request->validate([
            'message_id' => 'required|exists:telegram_messages,id',
            'reply_text' => 'required|string|max:4000',
        ]);

        try {
            $message = TelegramMessage::findOrFail($request->message_id);

            // Send reply via Telegram
            $result = $this->telegram->sendMessage(
                $message->telegram_chat_id,
                "📬 <b>Javob:</b>\n\n" . $request->reply_text
            );

            if ($result['success']) {
                // Update message status
                $message->reply(
                    $request->reply_text,
                    auth()->id()
                );

                return response()->json([
                    'success' => true,
                    'message' => 'Xabar muvaffaqiyatli yuborildi!',
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $result['error'] ?? 'Xabar yuborishda xatolik yuz berdi',
            ], 400);
        } catch (\Exception $e) {
            Log::error('Telegram send message error', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Xatolik: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Send contact request from website
     */
    public function sendContactRequest(Request $request): JsonResponse
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        try {
            $user = auth()->user();

            // Note: We don't save to telegram_messages table for web contacts
            // because they don't have telegram_chat_id (which is required by DB)
            // Web contacts are sent directly to admins via Telegram bot

            // Try to send to admins via Telegram (if configured)
            try {
                $result = $this->telegram->sendContactRequestNotification($user, $request->message);
                if ($result && isset($result['success']) && $result['success']) {
                    Log::info('Telegram notification sent successfully to admins');
                } else {
                    Log::warning('Telegram notification not sent (bot may not be configured)');
                }
            } catch (\Exception $telegramError) {
                Log::warning('Telegram notification failed', [
                    'error' => $telegramError->getMessage(),
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Murojaatingiz yuborildi! Tez orada javob beramiz.',
            ]);
        } catch (\Exception $e) {
            Log::error('Contact request error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Xatolik yuz berdi. Iltimos qaytadan urinib ko\'ring.',
            ], 500);
        }
    }

    /**
     * Set webhook URL
     */
    public function setWebhook(Request $request): JsonResponse
    {
        try {
            $webhookUrl = url('/api/telegram/webhook');
            $result = $this->telegram->setWebhook($webhookUrl);

            if (isset($result['ok']) && $result['ok'] === true) {
                return response()->json([
                    'success' => true,
                    'description' => $result['description'] ?? 'Webhook o\'rnatildi',
                    'webhook_url' => $webhookUrl,
                ]);
            }

            return response()->json([
                'success' => false,
                'error' => $result['error'] ?? $result['description'] ?? 'Webhook o\'rnatishda xatolik',
            ], 400);

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('Telegram setWebhook connection error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'error' => 'Telegram API ga ulanib bo\'lmadi. Server firewall sozlamalarini tekshiring.',
            ], 500);
        } catch (\Exception $e) {
            Log::error('Telegram setWebhook error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'error' => 'Xatolik: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get webhook info
     */
    public function getWebhookInfo(): JsonResponse
    {
        try {
            $info = $this->telegram->getWebhookInfo();

            if (isset($info['ok']) && $info['ok'] === true && isset($info['result'])) {
                return response()->json([
                    'success' => true,
                    'webhook_info' => $info['result'],
                ]);
            }

            return response()->json([
                'success' => false,
                'error' => $info['error'] ?? $info['description'] ?? 'Ma\'lumot olishda xatolik',
            ], 400);

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('Telegram getWebhookInfo connection error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'error' => 'Telegram API ga ulanib bo\'lmadi. Server firewall sozlamalarini tekshiring.',
            ], 500);
        } catch (\Exception $e) {
            Log::error('Telegram getWebhookInfo error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'error' => 'Xatolik: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete webhook
     */
    public function deleteWebhook(): JsonResponse
    {
        try {
            $result = $this->telegram->deleteWebhook();

            if (isset($result['ok']) && $result['ok'] === true) {
                return response()->json([
                    'success' => true,
                    'description' => $result['description'] ?? 'Webhook o\'chirildi',
                ]);
            }

            return response()->json([
                'success' => false,
                'error' => $result['error'] ?? $result['description'] ?? 'Webhook o\'chirishda xatolik',
            ], 400);

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('Telegram deleteWebhook connection error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'error' => 'Telegram API ga ulanib bo\'lmadi. Server firewall sozlamalarini tekshiring.',
            ], 500);
        } catch (\Exception $e) {
            Log::error('Telegram deleteWebhook error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'error' => 'Xatolik: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get bot info
     */
    public function getBotInfo(): JsonResponse
    {
        try {
            $info = $this->telegram->getMe();

            // Check if result is valid
            if (isset($info['ok']) && $info['ok'] === false) {
                return response()->json([
                    'success' => false,
                    'error' => $info['error'] ?? $info['description'] ?? 'Telegram API xatosi',
                ], 400);
            }

            if (isset($info['result'])) {
                return response()->json([
                    'success' => true,
                    'bot' => $info['result'],
                ]);
            }

            return response()->json([
                'success' => false,
                'error' => 'Noto\'g\'ri javob formati',
            ], 400);

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('Telegram API connection error', [
                'error' => $e->getMessage(),
            ]);
            return response()->json([
                'success' => false,
                'error' => 'Telegram API ga ulanib bo\'lmadi. Server firewall yoki tarmoq muammosi bo\'lishi mumkin.',
            ], 500);
        } catch (\Exception $e) {
            Log::error('Telegram getBotInfo error', [
                'error' => $e->getMessage(),
            ]);
            return response()->json([
                'success' => false,
                'error' => 'Xatolik: ' . $e->getMessage(),
            ], 500);
        }
    }
}
