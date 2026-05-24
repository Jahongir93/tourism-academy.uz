<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Student Contracts table
        if (!Schema::hasTable('student_contracts')) {
            Schema::create('student_contracts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('student_id')->constrained()->onDelete('cascade');
                $table->string('contract_number')->unique();
                $table->decimal('total_amount', 10, 2);
                $table->decimal('paid_amount', 10, 2)->default(0);
                $table->decimal('discount_amount', 10, 2)->default(0);
                $table->string('discount_reason')->nullable();
                $table->date('contract_date');
                $table->date('start_date');
                $table->date('end_date');
                $table->enum('payment_type', ['full', 'installment'])->default('installment');
                $table->integer('installment_count')->default(1);
                $table->enum('status', ['active', 'completed', 'cancelled', 'suspended'])->default('active');
                $table->text('notes')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users');
                $table->timestamps();

                $table->index('student_id');
                $table->index('status');
                $table->index('contract_date');
            });
        }

        // Student Payments table (if not exists)
        if (!Schema::hasTable('student_payments')) {
            Schema::create('student_payments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('student_id')->constrained()->onDelete('cascade');
                $table->foreignId('contract_id')->nullable()->constrained('student_contracts')->onDelete('set null');
                $table->decimal('amount', 10, 2);
                $table->date('payment_date');
                $table->enum('payment_method', ['cash', 'bank', 'online', 'card'])->default('bank');
                $table->string('receipt_number')->nullable();
                $table->string('receipt_file_url')->nullable();
                $table->enum('status', ['pending', 'completed', 'cancelled'])->default('completed');
                $table->text('notes')->nullable();
                $table->foreignId('processed_by')->nullable()->constrained('users');
                $table->timestamps();

                $table->index('student_id');
                $table->index('contract_id');
                $table->index('payment_date');
                $table->index('status');
            });
        }

        // Scholarships/Grants table - COMMENTED OUT (using separate migration)
        // Schema::create('scholarships', function (Blueprint $table) {
        //     $table->id();
        //     $table->string('name');
        //     $table->text('description')->nullable();
        //     $table->decimal('amount', 10, 2);
        //     $table->enum('type', ['monthly', 'one_time', 'annual'])->default('monthly');
        //     $table->enum('category', ['academic', 'social', 'sport', 'cultural', 'other'])->default('academic');
        //     $table->date('start_date');
        //     $table->date('end_date')->nullable();
        //     $table->integer('max_recipients')->nullable();
        //     $table->integer('current_recipients')->default(0);
        //     $table->text('eligibility_criteria')->nullable();
        //     $table->enum('status', ['active', 'inactive', 'expired'])->default('active');
        //     $table->timestamps();
        //
        //     $table->index('status');
        //     $table->index('category');
        // });

        // Student Scholarships (junction table) - COMMENTED OUT (using separate migration)
        // Schema::create('student_scholarships', function (Blueprint $table) {
        //     $table->id();
        //     $table->foreignId('student_id')->constrained()->onDelete('cascade');
        //     $table->foreignId('scholarship_id')->constrained()->onDelete('cascade');
        //     $table->date('awarded_date');
        //     $table->date('start_date');
        //     $table->date('end_date')->nullable();
        //     $table->decimal('amount', 10, 2);
        //     $table->enum('status', ['active', 'suspended', 'completed', 'cancelled'])->default('active');
        //     $table->text('reason')->nullable();
        //     $table->foreignId('approved_by')->nullable()->constrained('users');
        //     $table->timestamps();
        //
        //     $table->index('student_id');
        //     $table->index('scholarship_id');
        //     $table->index('status');
        // });

        // Scholarship Payments - COMMENTED OUT (not needed for now)
        // Schema::create('scholarship_payments', function (Blueprint $table) {
        //     $table->id();
        //     $table->foreignId('student_scholarship_id')->constrained()->onDelete('cascade');
        //     $table->decimal('amount', 10, 2);
        //     $table->date('payment_date');
        //     $table->string('payment_reference')->nullable();
        //     $table->enum('status', ['pending', 'paid', 'cancelled'])->default('pending');
        //     $table->text('notes')->nullable();
        //     $table->foreignId('processed_by')->nullable()->constrained('users');
        //     $table->timestamps();
        //
        //     $table->index('student_scholarship_id');
        //     $table->index('payment_date');
        //     $table->index('status');
        // });

        // Financial Reports/Transactions
        Schema::create('financial_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_number')->unique();
            $table->enum('type', ['income', 'expense'])->default('income');
            $table->enum('category', ['tuition', 'scholarship', 'grant', 'donation', 'salary', 'utility', 'other'])->default('tuition');
            $table->decimal('amount', 10, 2);
            $table->date('transaction_date');
            $table->string('description');
            $table->foreignId('related_student_id')->nullable()->constrained('students')->onDelete('set null');
            $table->foreignId('related_payment_id')->nullable()->constrained('student_payments')->onDelete('set null');
            $table->string('receipt_file_url')->nullable();
            $table->foreignId('processed_by')->nullable()->constrained('users');
            $table->timestamps();

            $table->index('type');
            $table->index('category');
            $table->index('transaction_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financial_transactions');
        // Schema::dropIfExists('scholarship_payments'); // Commented - using separate migration
        // Schema::dropIfExists('student_scholarships'); // Commented - using separate migration
        // Schema::dropIfExists('scholarships'); // Commented - using separate migration
        Schema::dropIfExists('student_payments');
        Schema::dropIfExists('student_contracts');
    }
};
