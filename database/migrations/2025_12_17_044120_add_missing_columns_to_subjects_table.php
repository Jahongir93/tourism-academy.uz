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
        Schema::table('subjects', function (Blueprint $table) {
            // Add missing columns only if they don't exist
            if (!Schema::hasColumn('subjects', 'department_id')) {
                $table->foreignId('department_id')->nullable()->after('id')->constrained('departments')->onDelete('set null');
            }
            if (!Schema::hasColumn('subjects', 'subject_type')) {
                $table->string('subject_type')->default('majburiy')->after('total_hours');
            }
            if (!Schema::hasColumn('subjects', 'description')) {
                $table->text('description')->nullable()->after('subject_type');
            }
            if (!Schema::hasColumn('subjects', 'objectives')) {
                $table->text('objectives')->nullable()->after('description');
            }
            if (!Schema::hasColumn('subjects', 'outcomes')) {
                $table->text('outcomes')->nullable()->after('objectives');
            }
            if (!Schema::hasColumn('subjects', 'prerequisites')) {
                $table->json('prerequisites')->nullable()->after('outcomes');
            }
        });

        // Migrate data from 'type' to 'subject_type' if 'type' column exists
        if (Schema::hasColumn('subjects', 'type') && Schema::hasColumn('subjects', 'subject_type')) {
            DB::statement('UPDATE subjects SET subject_type = type WHERE subject_type IS NULL OR subject_type = ""');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->dropForeign(['department_id']);
            $table->dropColumn([
                'department_id',
                'subject_type',
                'description',
                'objectives',
                'outcomes',
                'prerequisites'
            ]);
        });
    }
};
