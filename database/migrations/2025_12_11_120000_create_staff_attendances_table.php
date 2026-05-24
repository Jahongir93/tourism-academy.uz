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
        // Staff/Employee attendances table
        if (!Schema::hasTable('staff_attendances')) {
            Schema::create('staff_attendances', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->date('date');
                $table->time('check_in_time')->nullable();
                $table->time('check_out_time')->nullable();
                $table->decimal('confidence_score', 5, 2)->nullable();
                $table->enum('status', ['early', 'present', 'late', 'very_late', 'absent'])->default('present');
                $table->string('method', 50)->default('face_recognition'); // face_recognition, manual, fingerprint
                $table->string('location')->nullable();
                $table->text('notes')->nullable();
                $table->foreignId('marked_by')->nullable()->constrained('users')->onDelete('set null');
                $table->timestamps();

                $table->unique(['user_id', 'date'], 'unique_staff_date');
                $table->index(['date', 'status']);
                $table->index(['user_id', 'date']);
            });
        }

        // Add department and position to users if not exists
        if (!Schema::hasColumn('users', 'department')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('department')->nullable();
                $table->string('position')->nullable();
            });
        }

        // Add role column if not exists
        if (!Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('role')->default('user')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff_attendances');

        // Don't drop user columns in down - they might be used elsewhere
    }
};
