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
        Schema::create('position_hierarchy', function (Blueprint $table) {
            $table->id();
            $table->foreignId('position_id')->constrained('positions')->onDelete('cascade');
            $table->foreignId('reports_to_position_id')->constrained('positions')->onDelete('cascade');
            $table->string('hierarchy_type')->default('direct'); // direct, functional, dotted
            $table->timestamps();

            // Composite unique key to prevent duplicate relationships
            $table->unique(['position_id', 'reports_to_position_id', 'hierarchy_type'], 'pos_hierarchy_unique');

            // Indexes for better query performance
            $table->index('position_id');
            $table->index('reports_to_position_id');
            $table->index('hierarchy_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('position_hierarchy');
    }
};