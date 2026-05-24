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
        Schema::create('lms_forum_post_reactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained('lms_forum_posts')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('type', ['like', 'dislike']);
            $table->timestamps();

            // Har bir foydalanuvchi bitta postga faqat bir marta like yoki dislike qo'ya oladi
            $table->unique(['post_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lms_forum_post_reactions');
    }
};
