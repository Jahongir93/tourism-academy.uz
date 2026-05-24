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
        // Add request status to chat_participants
        Schema::table('chat_participants', function (Blueprint $table) {
            // Request status: pending, accepted, rejected
            // For students messaging others, status is 'pending' until recipient accepts
            // For non-students, status is always 'accepted'
            $table->enum('request_status', ['pending', 'accepted', 'rejected'])->default('accepted')->after('is_admin');
        });

        // Create blocked_users table
        Schema::create('chat_blocked_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('blocker_id')->constrained('users')->onDelete('cascade'); // Who blocked
            $table->foreignId('blocked_id')->constrained('users')->onDelete('cascade'); // Who is blocked
            $table->timestamps();

            // Unique constraint: user can't block same person twice
            $table->unique(['blocker_id', 'blocked_id']);

            // Index for quick lookups
            $table->index('blocker_id');
            $table->index('blocked_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chat_participants', function (Blueprint $table) {
            $table->dropColumn('request_status');
        });

        Schema::dropIfExists('chat_blocked_users');
    }
};
