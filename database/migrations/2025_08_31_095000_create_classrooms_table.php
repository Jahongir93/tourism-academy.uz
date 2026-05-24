<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('classrooms', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique()->nullable();
            $table->unsignedBigInteger('building_id')->nullable();
            $table->integer('floor')->nullable();
            $table->integer('capacity')->default(30);
            $table->enum('type', ['lecture', 'lab', 'seminar', 'computer', 'auditorium'])->default('lecture');
            $table->boolean('has_projector')->default(false);
            $table->boolean('has_computer')->default(false);
            $table->boolean('has_whiteboard')->default(true);
            $table->boolean('is_active')->default(true);
            $table->text('equipment')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('building_id');
            $table->index('type');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('classrooms');
    }
};