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
        Schema::create('lms_content_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('viewable_type'); // polymorphic relation
            $table->unsignedBigInteger('viewable_id');
            $table->integer('view_duration')->default(0); // in seconds
            $table->decimal('completion_percentage', 5, 2)->default(0);
            $table->boolean('is_completed')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('last_viewed_at')->useCurrent();
            $table->integer('view_count')->default(1);
            $table->timestamps();
            
            $table->index(['user_id', 'viewable_type', 'viewable_id']);
            $table->index('is_completed');
            $table->index(['viewable_type', 'viewable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lms_content_views');
    }
};