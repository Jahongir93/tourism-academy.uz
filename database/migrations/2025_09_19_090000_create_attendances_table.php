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
        if (!Schema::hasTable('attendances')) {
            Schema::create('attendances', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->timestamp('check_in')->nullable();
                $table->timestamp('check_out')->nullable();
                $table->date('date');
                $table->enum('status', ['present', 'absent', 'late', 'very_late', 'holiday', 'leave'])->default('present');
                $table->decimal('face_confidence', 5, 2)->nullable();
                $table->string('location')->nullable();
                $table->decimal('total_hours', 5, 2)->nullable();
                $table->boolean('manual_override')->default(false);
                $table->string('override_reason')->nullable();
                $table->foreignId('override_by')->nullable()->constrained('users');
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->index(['user_id', 'date']);
                $table->index('date');
                $table->index('location');
                $table->unique(['user_id', 'date']); // One attendance per user per day
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};