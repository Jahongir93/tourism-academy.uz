<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            // JSON column to track which users deleted this message
            // When both users in a private chat delete it, the message will be soft deleted
            $table->json('deleted_for')->nullable()->after('edited_at');
        });

        // Add deleted_at column to chat_participants for tracking deleted conversations
        Schema::table('chat_participants', function (Blueprint $table) {
            $table->timestamp('deleted_at')->nullable()->after('is_admin');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->dropColumn('deleted_for');
        });

        Schema::table('chat_participants', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
        });
    }
};
