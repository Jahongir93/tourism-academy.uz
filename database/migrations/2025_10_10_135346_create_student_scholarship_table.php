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
        Schema::create('student_scholarship', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('scholarship_id')->constrained('scholarships')->onDelete('cascade');

            // Ariza ma'lumotlari
            $table->date('applied_date')->nullable();
            $table->date('awarded_date')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();

            // Holat
            $table->enum('status', ['pending', 'approved', 'rejected', 'active', 'expired', 'cancelled'])->default('pending');
            $table->text('rejection_reason')->nullable();

            // Summa
            $table->decimal('amount', 15, 2)->default(0);
            $table->decimal('amount_paid', 15, 2)->default(0);

            // Baholash
            $table->decimal('gpa_at_application', 3, 2)->nullable();
            $table->integer('attendance_at_application')->nullable();
            $table->text('notes')->nullable();

            // Kim tasdiqladi
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['student_id', 'scholarship_id']);
            $table->index('status');
            $table->index(['start_date', 'end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_scholarship');
    }
};
