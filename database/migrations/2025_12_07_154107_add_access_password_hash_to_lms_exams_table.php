<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // SECURITY FIX: Add hashed password column
        Schema::table('lms_exams', function (Blueprint $table) {
            $table->string('access_password_hash')->nullable()->after('access_password');
        });

        // Migrate existing plain-text passwords to hashed versions
        DB::table('lms_exams')
            ->whereNotNull('access_password')
            ->where('access_password', '!=', '')
            ->chunkById(100, function ($exams) {
                foreach ($exams as $exam) {
                    DB::table('lms_exams')
                        ->where('id', $exam->id)
                        ->update([
                            'access_password_hash' => Hash::make($exam->access_password),
                            'updated_at' => now()
                        ]);
                }
            });

        // Drop the old plain-text password column
        Schema::table('lms_exams', function (Blueprint $table) {
            $table->dropColumn('access_password');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add back the plain-text column
        Schema::table('lms_exams', function (Blueprint $table) {
            $table->string('access_password', 50)->nullable()->after('sync_to_journal');
        });

        // Note: Cannot reverse hashed passwords to plain text
        // New exams will need passwords to be re-set

        // Drop the hashed column
        Schema::table('lms_exams', function (Blueprint $table) {
            $table->dropColumn('access_password_hash');
        });
    }
};
