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
        Schema::create('admission_form_fields', function (Blueprint $table) {
            $table->id();
            $table->string('field_key')->unique()->comment('Unique field identifier');
            $table->string('field_type')->comment('text, email, phone, date, select, radio, checkbox, textarea, file, heading');
            $table->string('label_uz')->comment('Uzbek label');
            $table->string('label_ru')->nullable()->comment('Russian label');
            $table->string('label_en')->nullable()->comment('English label');
            $table->string('placeholder')->nullable();
            $table->json('options')->nullable()->comment('Options for select/radio/checkbox');
            $table->json('validation_rules')->nullable()->comment('Custom Laravel validation rules');
            $table->boolean('is_required')->default(false);
            $table->unsignedTinyInteger('step')->default(1)->comment('Form step (1-4)');
            $table->unsignedInteger('sort_order')->default(0)->comment('Order within step');
            $table->json('file_config')->nullable()->comment('File upload config: max_size, allowed_extensions, storage_path');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['step', 'sort_order']);
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admission_form_fields');
    }
};
