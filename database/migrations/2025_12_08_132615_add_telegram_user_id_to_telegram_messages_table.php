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
        Schema::table('telegram_messages', function (Blueprint $table) {
            if (!Schema::hasColumn('telegram_messages', 'telegram_user_id')) {
                $table->bigInteger('telegram_user_id')->nullable()->after('telegram_message_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('telegram_messages', function (Blueprint $table) {
            if (Schema::hasColumn('telegram_messages', 'telegram_user_id')) {
                $table->dropColumn('telegram_user_id');
            }
        });
    }
};
