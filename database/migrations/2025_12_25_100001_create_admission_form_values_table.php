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
        Schema::create('admission_form_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained('admission_applications')->onDelete('cascade');
            $table->string('field_key')->comment('References admission_form_fields.field_key');
            $table->text('value')->nullable()->comment('Field value (text or JSON for multi-select)');
            $table->string('file_path')->nullable()->comment('File path for file uploads');
            $table->timestamps();

            $table->unique(['application_id', 'field_key']);
            $table->index('field_key');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admission_form_values');
    }
};
