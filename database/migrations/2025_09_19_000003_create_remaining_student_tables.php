<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Student family members table
        if (!Schema::hasTable('student_family_members')) {
            Schema::create('student_family_members', function (Blueprint $table) {
                $table->id();
                $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
                $table->enum('relationship', ['father', 'mother', 'brother', 'sister', 'spouse', 'child', 'guardian', 'other']);
                $table->string('full_name');
                $table->date('birth_date')->nullable();
                $table->string('phone')->nullable();
                $table->string('occupation')->nullable();
                $table->string('workplace')->nullable();
                $table->text('address')->nullable();
                $table->boolean('is_guardian')->default(false);
                $table->boolean('is_deceased')->default(false);
                $table->timestamps();

                $table->index('student_id');
            });
        }

        // Student contracts table
        if (!Schema::hasTable('student_contracts')) {
            Schema::create('student_contracts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
                $table->string('contract_number')->unique();
                $table->date('contract_date');
                $table->string('academic_year');
                $table->integer('semester');
                $table->decimal('total_amount', 12, 2);
                $table->decimal('paid_amount', 12, 2)->default(0);
                $table->enum('payment_type', ['grant', 'contract', 'super_contract'])->default('contract');
                $table->enum('status', ['active', 'completed', 'cancelled', 'suspended'])->default('active');
                $table->date('start_date');
                $table->date('end_date');
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->index(['student_id', 'academic_year']);
            });
        }

        // Student payments table
        if (!Schema::hasTable('student_payments')) {
            Schema::create('student_payments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
                $table->foreignId('contract_id')->nullable()->constrained('student_contracts')->onDelete('set null');
                $table->string('payment_number')->unique();
                $table->decimal('amount', 12, 2);
                $table->date('payment_date');
                $table->enum('payment_method', ['cash', 'bank_transfer', 'card', 'online'])->default('cash');
                $table->string('receipt_number')->nullable();
                $table->string('academic_year');
                $table->integer('semester');
                $table->enum('status', ['pending', 'completed', 'cancelled', 'refunded'])->default('completed');
                $table->text('description')->nullable();
                $table->timestamps();

                $table->index(['student_id', 'payment_date']);
            });
        }

        // Student documents table
        if (!Schema::hasTable('student_documents')) {
            Schema::create('student_documents', function (Blueprint $table) {
                $table->id();
                $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
                $table->string('document_type');
                $table->string('document_name');
                $table->string('file_path')->nullable();
                $table->string('file_type')->nullable();
                $table->integer('file_size')->nullable();
                $table->date('issue_date')->nullable();
                $table->date('expiry_date')->nullable();
                $table->boolean('is_verified')->default(false);
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->index('student_id');
            });
        }

        // Student orders table
        if (!Schema::hasTable('student_orders')) {
            Schema::create('student_orders', function (Blueprint $table) {
                $table->id();
                $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
                $table->string('order_number')->unique();
                $table->date('order_date');
                $table->enum('order_type', [
                    'enrollment',
                    'expulsion',
                    'academic_leave',
                    'reinstatement',
                    'transfer',
                    'graduation',
                    'discipline',
                    'encouragement',
                    'other'
                ]);
                $table->text('content');
                $table->string('reason')->nullable();
                $table->date('effective_date');
                $table->enum('status', ['draft', 'active', 'cancelled'])->default('active');
                $table->timestamps();

                $table->index(['student_id', 'order_date']);
            });
        }

        // Student movements table
        if (!Schema::hasTable('student_movements')) {
            Schema::create('student_movements', function (Blueprint $table) {
                $table->id();
                $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
                $table->enum('movement_type', [
                    'admission',
                    'course_promotion',
                    'group_transfer',
                    'faculty_transfer',
                    'university_transfer',
                    'academic_leave',
                    'reinstatement',
                    'expulsion',
                    'graduation'
                ]);
                $table->date('movement_date');
                $table->string('from_group')->nullable();
                $table->string('to_group')->nullable();
                $table->integer('from_course')->nullable();
                $table->integer('to_course')->nullable();
                $table->string('from_faculty')->nullable();
                $table->string('to_faculty')->nullable();
                $table->string('order_number')->nullable();
                $table->text('reason')->nullable();
                $table->timestamps();

                $table->index(['student_id', 'movement_date']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('student_movements');
        Schema::dropIfExists('student_orders');
        Schema::dropIfExists('student_documents');
        Schema::dropIfExists('student_payments');
        Schema::dropIfExists('student_contracts');
        Schema::dropIfExists('student_family_members');
    }
};