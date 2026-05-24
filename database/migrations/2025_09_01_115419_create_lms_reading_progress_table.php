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
        Schema::create('lms_reading_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('book_id')->constrained('lms_library_books')->onDelete('cascade');
            $table->integer('current_page')->default(0);
            $table->integer('total_pages');
            $table->decimal('progress_percentage', 5, 2)->default(0);
            $table->timestamp('last_read_at')->nullable();
            $table->integer('reading_time')->default(0); // in minutes
            $table->json('bookmarks')->nullable(); // array of page numbers
            $table->text('notes')->nullable();
            $table->boolean('is_completed')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            
            $table->unique(['user_id', 'book_id']);
            $table->index('is_completed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lms_reading_progress');
    }
};