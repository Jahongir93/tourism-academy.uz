<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('face_encodings')) {
            Schema::create('face_encodings', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->text('encoding'); // Face encoding data
                $table->string('image_path')->nullable();
                $table->float('quality_score')->nullable();
                $table->boolean('is_primary')->default(false);
                $table->enum('status', ['active', 'inactive', 'pending'])->default('active');
                $table->json('metadata')->nullable(); // Additional metadata
                $table->timestamps();

                $table->index('user_id');
                $table->index('is_primary');
                $table->index('status');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('face_encodings');
    }
};