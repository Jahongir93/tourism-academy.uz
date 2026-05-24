<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Chat rooms - only if doesn't exist
        if (!Schema::hasTable('chat_rooms')) {
            Schema::create('chat_rooms', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->enum('type', ['public', 'private', 'direct'])->default('public');
                $table->string('icon')->nullable();
                $table->string('color')->default('#10b981'); // Green theme
                $table->integer('max_members')->nullable();
                $table->boolean('is_active')->default(true);
                $table->integer('order_number')->default(0);
                $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
                $table->timestamps();

                $table->index(['slug', 'is_active']);
                $table->index('type');
            });
        }

        // Chat room members - only if doesn't exist
        if (!Schema::hasTable('chat_room_members')) {
            Schema::create('chat_room_members', function (Blueprint $table) {
                $table->id();
                $table->foreignId('room_id')->constrained('chat_rooms')->onDelete('cascade');
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->enum('role', ['admin', 'moderator', 'member'])->default('member');
                $table->boolean('is_muted')->default(false);
                $table->timestamp('muted_until')->nullable();
                $table->timestamp('last_seen_at')->nullable();
                $table->integer('unread_count')->default(0);
                $table->timestamps();

                $table->unique(['room_id', 'user_id']);
                $table->index('user_id');
            });
        }

        // Message reactions - only if doesn't exist
        if (!Schema::hasTable('chat_reactions')) {
            Schema::create('chat_reactions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('message_id')->constrained('chat_messages')->onDelete('cascade');
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->string('emoji', 10);
                $table->timestamps();

                $table->unique(['message_id', 'user_id', 'emoji']);
            });
        }

        // Online status tracking - only if doesn't exist
        if (!Schema::hasTable('chat_online_users')) {
            Schema::create('chat_online_users', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->unique()->constrained('users')->onDelete('cascade');
                $table->timestamp('last_activity');
                $table->string('status')->default('online'); // online, away, busy
                $table->string('status_message')->nullable();

                $table->index('last_activity');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('chat_online_users');
        Schema::dropIfExists('chat_reactions');
        Schema::dropIfExists('chat_room_members');
        Schema::dropIfExists('chat_rooms');
        // Note: Not dropping chat_messages as it's created by another migration
    }
};
