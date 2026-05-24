<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'provider')) {
                $table->string('provider', 32)->nullable()->after('password');
            }
            if (!Schema::hasColumn('users', 'provider_id')) {
                $table->string('provider_id', 191)->nullable()->after('provider');
            }
            if (!Schema::hasColumn('users', 'avatar')) {
                $table->string('avatar', 500)->nullable()->after('provider_id');
            }
        });

        if (Schema::hasColumn('users', 'provider') && Schema::hasColumn('users', 'provider_id')) {
            try {
                Schema::table('users', function (Blueprint $table) {
                    $table->unique(['provider', 'provider_id'], 'users_provider_provider_id_unique');
                });
            } catch (\Throwable $e) {
                // unique already exists — ignore
            }
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            try { $table->dropUnique('users_provider_provider_id_unique'); } catch (\Throwable $e) {}
            $table->dropColumn(['provider', 'provider_id', 'avatar']);
        });
    }
};
