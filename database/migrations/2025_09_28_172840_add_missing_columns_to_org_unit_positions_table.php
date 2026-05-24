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
        Schema::table('org_unit_positions', function (Blueprint $table) {
            // Add missing columns if they don't exist
            if (!Schema::hasColumn('org_unit_positions', 'appointment_order_id')) {
                $table->unsignedBigInteger('appointment_order_id')->nullable()->after('appointment_date');
            }
            if (!Schema::hasColumn('org_unit_positions', 'salary')) {
                $table->decimal('salary', 12, 2)->nullable()->after('workload_percentage');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('org_unit_positions', function (Blueprint $table) {
            // Drop columns if they exist
            $columns = ['appointment_order_id', 'salary'];

            foreach ($columns as $column) {
                if (Schema::hasColumn('org_unit_positions', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
