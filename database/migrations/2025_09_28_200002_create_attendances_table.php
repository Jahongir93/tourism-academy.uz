<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('attendances')) {
            Schema::create('attendances', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->date('date');
                $table->time('check_in_time')->nullable();
                $table->time('check_out_time')->nullable();
                $table->enum('status', ['present', 'absent', 'late', 'excused', 'holiday'])->default('absent');
                $table->string('check_in_method')->nullable(); // manual, face, card, fingerprint
                $table->string('check_out_method')->nullable();
                $table->float('face_confidence_score')->nullable();
                $table->string('location')->nullable();
                $table->string('ip_address')->nullable();
                $table->text('notes')->nullable();
                $table->json('metadata')->nullable();
                $table->foreignId('marked_by')->nullable()->constrained('users');
                $table->timestamps();

                $table->index(['user_id', 'date']);
                $table->index('date');
                $table->index('status');
                $table->unique(['user_id', 'date']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};