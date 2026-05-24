<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('specialties', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('faculty_id')->nullable();
            $table->unsignedBigInteger('department_id')->nullable();
            $table->string('code')->unique();
            $table->string('name_uz');
            $table->string('name_ru')->nullable();
            $table->string('name_en')->nullable();
            $table->string('direction_code')->nullable();
            $table->enum('degree', ['bakalavr', 'magistr', 'doktorantura', 'ordinatura'])->default('bakalavr');
            $table->enum('education_form', ['kunduzgi', 'sirtqi', 'kechki', 'masofaviy'])->default('kunduzgi');
            $table->enum('education_type', ['byudjet', 'shartnoma'])->default('shartnoma');
            $table->integer('duration_years');
            $table->integer('credits_required')->nullable();
            $table->decimal('tuition_fee', 12, 2)->nullable();
            $table->string('language')->default('uz');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['faculty_id', 'code']);
            $table->index('degree');
            $table->index('education_form');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('specialties');
    }
};