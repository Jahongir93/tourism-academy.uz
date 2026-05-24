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
        Schema::table('student_contracts', function (Blueprint $table) {
            // Add discount_amount if missing
            if (!Schema::hasColumn('student_contracts', 'discount_amount')) {
                $table->decimal('discount_amount', 10, 2)->default(0)->after('paid_amount');
            }

            // Add discount_reason if missing
            if (!Schema::hasColumn('student_contracts', 'discount_reason')) {
                $table->string('discount_reason')->nullable()->after('discount_amount');
            }

            // Add installment_count if missing
            if (!Schema::hasColumn('student_contracts', 'installment_count')) {
                $table->integer('installment_count')->default(1)->after('payment_type');
            }

            // Add created_by if missing
            if (!Schema::hasColumn('student_contracts', 'created_by')) {
                $table->foreignId('created_by')->nullable()->after('notes')->constrained('users');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_contracts', function (Blueprint $table) {
            if (Schema::hasColumn('student_contracts', 'discount_amount')) {
                $table->dropColumn('discount_amount');
            }
            if (Schema::hasColumn('student_contracts', 'discount_reason')) {
                $table->dropColumn('discount_reason');
            }
            if (Schema::hasColumn('student_contracts', 'installment_count')) {
                $table->dropColumn('installment_count');
            }
            if (Schema::hasColumn('student_contracts', 'created_by')) {
                $table->dropForeign(['created_by']);
                $table->dropColumn('created_by');
            }
        });
    }
};
