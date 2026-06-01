<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('departments')) {
            return;
        }

        DB::statement("ALTER TABLE departments MODIFY COLUMN type ENUM('umumkasbiy','umumtalim','ixtisoslik','boshqa','kafedra') NULL");
    }

    public function down(): void
    {
        if (!Schema::hasTable('departments')) {
            return;
        }

        DB::statement("UPDATE departments SET type = 'boshqa' WHERE type = 'kafedra'");
        DB::statement("ALTER TABLE departments MODIFY COLUMN type ENUM('umumkasbiy','umumtalim','ixtisoslik','boshqa') NULL");
    }
};
