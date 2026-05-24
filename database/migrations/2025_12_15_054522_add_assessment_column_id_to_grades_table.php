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
        Schema::table('grades', function (Blueprint $table) {
            $table->foreignId('assessment_column_id')->nullable()->after('vedomost_sheet_id')
                  ->constrained('vedomost_assessment_columns')->onDelete('cascade');
            $table->index('assessment_column_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('grades', function (Blueprint $table) {
            $table->dropForeign(['assessment_column_id']);
            $table->dropColumn('assessment_column_id');
        });
    }
};
