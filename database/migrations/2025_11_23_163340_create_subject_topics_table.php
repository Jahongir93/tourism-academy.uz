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
        Schema::create('subject_topics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->integer('topic_number')->comment('Mavzu raqami');
            $table->string('title_uz')->comment('Mavzu nomi (o\'zbekcha)');
            $table->string('title_ru')->nullable()->comment('Mavzu nomi (ruscha)');
            $table->string('title_en')->nullable()->comment('Mavzu nomi (inglizcha)');
            $table->text('description_uz')->nullable()->comment('Tavsifi');
            $table->text('description_ru')->nullable();
            $table->text('description_en')->nullable();
            $table->enum('topic_type', ['lecture', 'practice', 'lab', 'seminar', 'independent'])->default('lecture');
            $table->integer('hours')->default(2)->comment('Soat miqdori');
            $table->integer('week_number')->nullable()->comment('Hafta raqami');
            $table->text('learning_outcomes')->nullable()->comment('O\'quv natijalari');
            $table->text('keywords')->nullable()->comment('Kalit so\'zlar');
            $table->text('references')->nullable()->comment('Adabiyotlar');
            $table->boolean('is_active')->default(true);
            $table->integer('order')->default(0)->comment('Tartib raqami');
            $table->timestamps();

            // Indexes
            $table->index(['subject_id', 'topic_number'], 'idx_subject_topic');
            $table->index(['subject_id', 'order'], 'idx_subject_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subject_topics');
    }
};
