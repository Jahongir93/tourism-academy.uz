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
        Schema::table('departments', function (Blueprint $table) {
            // Add missing columns if they don't exist
            if (!Schema::hasColumn('departments', 'short_name')) {
                $table->string('short_name', 50)->nullable()->after('name_en');
            }
            if (!Schema::hasColumn('departments', 'type')) {
                $table->enum('type', ['umumkasbiy', 'umumtalim', 'ixtisoslik', 'boshqa'])->nullable()->after('short_name');
            }
            if (!Schema::hasColumn('departments', 'head_id')) {
                $table->foreignId('head_id')->nullable()->after('type')->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('departments', 'room_number')) {
                $table->string('room_number', 50)->nullable()->after('head_id');
            }
            if (!Schema::hasColumn('departments', 'phone')) {
                $table->string('phone', 50)->nullable()->after('room_number');
            }
            if (!Schema::hasColumn('departments', 'email')) {
                $table->string('email', 100)->nullable()->after('phone');
            }
            if (!Schema::hasColumn('departments', 'established_date')) {
                $table->date('established_date')->nullable()->after('email');
            }
            if (!Schema::hasColumn('departments', 'staff_capacity')) {
                $table->integer('staff_capacity')->nullable()->after('established_date');
            }
            if (!Schema::hasColumn('departments', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('staff_capacity');
            }
            if (!Schema::hasColumn('departments', 'code')) {
                $table->string('code', 20)->nullable()->after('faculty_id')->unique();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            // Drop foreign key constraint first if it exists
            $table->dropForeign(['head_id']);

            // Drop columns
            $table->dropColumn([
                'short_name',
                'type',
                'head_id',
                'room_number',
                'phone',
                'email',
                'established_date',
                'staff_capacity',
                'is_active',
                'code'
            ]);
        });
    }
};