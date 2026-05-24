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
        Schema::create('vedomost_assessment_columns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vedomost_sheet_id')->constrained('vedomost_sheets')->onDelete('cascade');
            $table->string('name'); // e.g., "Oraliq nazorat 1", "Amaliy mashg'ulot"
            $table->string('column_type')->default('numeric'); // numeric, letter, text
            $table->integer('max_score')->default(100);
            $table->integer('order')->default(0); // Display order
            $table->boolean('is_final')->default(false); // Is this a final assessment column
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['vedomost_sheet_id', 'order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vedomost_assessment_columns');
    }
};
