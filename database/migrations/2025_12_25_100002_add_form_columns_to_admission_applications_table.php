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
        Schema::table('admission_applications', function (Blueprint $table) {
            $table->json('form_data')->nullable()->after('notes')->comment('Complete form snapshot for historical reference');
            $table->unsignedInteger('form_version')->default(1)->after('form_data')->comment('Form version used');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admission_applications', function (Blueprint $table) {
            $table->dropColumn(['form_data', 'form_version']);
        });
    }
};
