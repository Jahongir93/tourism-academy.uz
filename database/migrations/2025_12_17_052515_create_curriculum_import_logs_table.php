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
        Schema::create('curriculum_import_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained('educational_programs')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->string('academic_year');
            $table->string('file_name');
            $table->integer('topics_imported')->default(0);
            $table->foreignId('imported_by')->constrained('users')->onDelete('cascade');
            $table->json('import_details')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('curriculum_import_logs');
    }
};
