<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->enum('type', ['permanent', 'temporary', 'dormitory']);
            $table->string('country')->default('Uzbekistan');
            $table->string('region')->nullable();
            $table->string('district')->nullable();
            $table->string('city')->nullable();
            $table->string('street')->nullable();
            $table->string('house_number')->nullable();
            $table->string('apartment_number')->nullable();
            $table->string('postal_code')->nullable();
            $table->text('full_address')->nullable();
            $table->boolean('is_current')->default(true);
            $table->timestamps();

            $table->index('student_id');
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_addresses');
    }
};