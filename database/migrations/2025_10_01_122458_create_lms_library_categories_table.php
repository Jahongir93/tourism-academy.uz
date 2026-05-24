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
        if (!Schema::hasTable('lms_library_categories')) {
            Schema::create('lms_library_categories', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->string('icon')->nullable();
                $table->string('color')->default('#10b981');
                $table->integer('order_number')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });

            // Insert default categories
            DB::table('lms_library_categories')->insert([
                [
                    'name' => 'Turizm',
                    'slug' => 'turizm',
                    'description' => 'Turizm sohasiga oid kitoblar',
                    'icon' => 'fa-plane',
                    'color' => '#10b981',
                    'order_number' => 1,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Mehmonxona xo\'jaligi',
                    'slug' => 'mehmonxona-xojaligi',
                    'description' => 'Mehmonxona menejmentiga oid adabiyotlar',
                    'icon' => 'fa-hotel',
                    'color' => '#3b82f6',
                    'order_number' => 2,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Umumiy',
                    'slug' => 'umumiy',
                    'description' => 'Umumiy bilim beruvchi kitoblar',
                    'icon' => 'fa-book',
                    'color' => '#8b5cf6',
                    'order_number' => 3,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lms_library_categories');
    }
};
