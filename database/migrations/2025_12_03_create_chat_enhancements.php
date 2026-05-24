<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add nickname to users table
        if (!Schema::hasColumn('users', 'nickname')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('nickname', 50)->unique()->nullable();
                $table->boolean('is_online')->default(false);
                $table->timestamp('last_seen_at')->nullable();
            });
        }

        // Telegram messages table for bot integration
        if (!Schema::hasTable('telegram_messages')) {
            Schema::create('telegram_messages', function (Blueprint $table) {
                $table->id();
                $table->bigInteger('telegram_chat_id');
                $table->bigInteger('telegram_message_id')->nullable();
                $table->string('telegram_username')->nullable();
                $table->string('telegram_first_name')->nullable();
                $table->string('telegram_last_name')->nullable();
                $table->text('message');
                $table->enum('direction', ['incoming', 'outgoing'])->default('incoming');
                $table->foreignId('replied_by')->nullable()->constrained('users')->onDelete('set null');
                $table->text('reply_message')->nullable();
                $table->timestamp('replied_at')->nullable();
                $table->enum('status', ['new', 'read', 'replied', 'closed'])->default('new');
                $table->timestamps();

                $table->index('telegram_chat_id');
                $table->index('status');
            });
        }

        // Telegram bot settings
        if (!Schema::hasTable('telegram_bot_settings')) {
            Schema::create('telegram_bot_settings', function (Blueprint $table) {
                $table->id();
                $table->string('bot_token')->nullable();
                $table->string('bot_username')->nullable();
                $table->string('webhook_url')->nullable();
                $table->boolean('is_active')->default(false);
                $table->text('welcome_message')->nullable();
                $table->json('auto_replies')->nullable();
                $table->timestamps();
            });
        }

        // User contact requests (for contacting admin/dean)
        if (!Schema::hasTable('chat_contact_requests')) {
            Schema::create('chat_contact_requests', function (Blueprint $table) {
                $table->id();
                $table->foreignId('from_user_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('to_user_id')->nullable()->constrained('users')->onDelete('cascade');
                $table->enum('to_role', ['admin', 'dean', 'prorector', 'teacher'])->nullable();
                $table->text('message');
                $table->enum('status', ['pending', 'accepted', 'rejected'])->default('pending');
                $table->foreignId('handled_by')->nullable()->constrained('users')->onDelete('set null');
                $table->text('response')->nullable();
                $table->timestamps();

                $table->index(['status', 'to_role']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_contact_requests');
        Schema::dropIfExists('telegram_bot_settings');
        Schema::dropIfExists('telegram_messages');

        if (Schema::hasColumn('users', 'nickname')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn(['nickname', 'is_online', 'last_seen_at']);
            });
        }
    }
};
