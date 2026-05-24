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
            $table->foreignId('vedomost_sheet_id')->nullable()->after('id')->constrained('vedomost_sheets')->onDelete('set null');
            $table->index('vedomost_sheet_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('grades', function (Blueprint $table) {
            $table->dropForeign(['vedomost_sheet_id']);
            $table->dropColumn('vedomost_sheet_id');
        });
    }
};
