<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Face attendances table (student-based)
        if (!Schema::hasTable('face_attendances')) {
            Schema::create('face_attendances', function (Blueprint $table) {
                $table->id();
                $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
                $table->foreignId('group_id')->nullable()->constrained('student_groups')->onDelete('set null');
                $table->date('date');
                $table->time('check_in_time')->nullable();
                $table->time('check_out_time')->nullable();
                $table->decimal('confidence_score', 5, 2)->nullable();
                $table->enum('status', ['early', 'present', 'late', 'very_late', 'absent'])->default('present');
                $table->string('method', 50)->default('face_recognition'); // face_recognition, manual, qr_code
                $table->string('location')->nullable();
                $table->text('notes')->nullable();
                $table->foreignId('marked_by')->nullable()->constrained('users')->onDelete('set null');
                $table->timestamps();

                $table->unique(['student_id', 'date'], 'unique_student_date');
                $table->index(['date', 'group_id']);
                $table->index(['student_id', 'date']);
            });
        }

        // Add student_id to face_encodings if not exists
        if (Schema::hasTable('face_encodings')) {
            if (!Schema::hasColumn('face_encodings', 'student_id')) {
                Schema::table('face_encodings', function (Blueprint $table) {
                    $table->foreignId('student_id')->nullable()->after('user_id');
                    $table->boolean('is_active')->default(true)->after('metadata');
                });
            }
        }

        // Face attendance logs
        if (!Schema::hasTable('face_attendance_logs')) {
            Schema::create('face_attendance_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('face_attendance_id')->constrained('face_attendances')->onDelete('cascade');
                $table->enum('action_type', ['check_in', 'check_out', 'manual_edit']);
                $table->string('image_path')->nullable();
                $table->decimal('confidence_score', 5, 2)->nullable();
                $table->timestamp('timestamp');
                $table->json('metadata')->nullable();
                $table->foreignId('performed_by')->nullable()->constrained('users')->onDelete('set null');
                $table->timestamps();

                $table->index(['face_attendance_id', 'action_type']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('face_attendance_logs');
        Schema::dropIfExists('face_attendances');

        if (Schema::hasTable('face_encodings')) {
            if (Schema::hasColumn('face_encodings', 'student_id')) {
                Schema::table('face_encodings', function (Blueprint $table) {
                    $table->dropColumn(['student_id', 'is_active']);
                });
            }
        }
    }
};
