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
        Schema::create('chat_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique(); // Setting key (telegram_bot_token, telegram_bot_username, etc.)
            $table->text('value')->nullable(); // Setting value (encrypted for sensitive data)
            $table->string('type')->default('string'); // string, boolean, integer, json
            $table->boolean('is_public')->default(false); // Is visible to non-admins
            $table->text('description')->nullable(); // Description of the setting
            $table->timestamps();

            $table->index('key');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_settings');
    }
};
