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
        Schema::table('buildings', function (Blueprint $table) {
            $table->renameColumn('floors', 'total_floors');
            $table->integer('total_rooms')->default(0)->after('total_floors');
            $table->string('type')->default('academic')->after('total_rooms');
            $table->boolean('is_active')->default(true)->after('type');
            $table->text('description')->nullable()->after('is_active');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('buildings', function (Blueprint $table) {
            $table->dropColumn(['total_rooms', 'type', 'is_active', 'description']);
            $table->dropIndex(['is_active']);
            $table->renameColumn('total_floors', 'floors');
        });
    }
};
