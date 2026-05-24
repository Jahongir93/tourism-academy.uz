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
        Schema::create('lms_forum_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('parent_id')->nullable()->constrained('lms_forum_posts')->onDelete('cascade');
            $table->string('title')->nullable();
            $table->text('content');
            $table->string('post_type'); // question, discussion, announcement
            $table->boolean('is_pinned')->default(false);
            $table->boolean('is_locked')->default(false);
            $table->integer('view_count')->default(0);
            $table->integer('reply_count')->default(0);
            $table->integer('like_count')->default(0);
            $table->boolean('is_answered')->default(false);
            $table->foreignId('best_answer_id')->nullable()->constrained('lms_forum_posts');
            $table->timestamps();
            
            $table->index(['subject_id', 'user_id']);
            $table->index('parent_id');
            $table->index('post_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lms_forum_posts');
    }
};