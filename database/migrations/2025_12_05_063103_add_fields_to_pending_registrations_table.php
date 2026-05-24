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
        Schema::table('pending_registrations', function (Blueprint $table) {
            $table->string('full_name')->after('id');
            $table->string('email')->nullable()->after('full_name');
            $table->string('phone')->nullable()->after('email');
            $table->enum('type', ['student', 'employee'])->after('phone');
            $table->enum('user_type', ['uzbek', 'foreign'])->default('uzbek')->after('type');
            $table->string('position')->nullable()->after('user_type');
            $table->text('additional_info')->nullable()->after('position');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->after('additional_info');
            $table->text('rejection_reason')->nullable()->after('status');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null')->after('rejection_reason');
            $table->timestamp('reviewed_at')->nullable()->after('reviewed_by');

            $table->index(['type', 'status']);
            $table->index('reviewed_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pending_registrations', function (Blueprint $table) {
            $table->dropForeign(['reviewed_by']);
            $table->dropColumn([
                'full_name',
                'email',
                'phone',
                'type',
                'user_type',
                'position',
                'additional_info',
                'status',
                'rejection_reason',
                'reviewed_by',
                'reviewed_at'
            ]);
        });
    }
};
