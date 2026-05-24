<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            // Make name column nullable if it exists
            if (Schema::hasColumn('departments', 'name')) {
                $table->string('name')->nullable()->change();
            }
        });

        // Update existing null names with name_uz value
        DB::statement('UPDATE departments SET name = name_uz WHERE name IS NULL OR name = ""');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            if (Schema::hasColumn('departments', 'name')) {
                $table->string('name')->nullable(false)->change();
            }
        });
    }
};