<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('centers', function (Blueprint $table) {
            $table->id();
            $table->string('name_uz');
            $table->string('name_ru')->nullable();
            $table->string('name_en')->nullable();
            $table->string('short_name')->nullable();
            $table->string('code')->unique();
            $table->enum('type', ['research', 'training', 'innovation', 'service', 'international', 'it', 'other'])->default('other');
            $table->foreignId('head_id')->nullable()->constrained('employees');
            $table->string('head_name')->nullable();
            $table->text('description')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->string('address')->nullable();
            $table->string('room_number')->nullable();
            $table->date('established_date')->nullable();
            $table->json('working_hours')->nullable();
            $table->json('services')->nullable();
            $table->integer('staff_count')->default(0);
            $table->decimal('budget', 15, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('order_number')->default(0);
            $table->timestamps();
            
            $table->index(['type', 'is_active']);
            $table->index('code');
        });

        // Seed initial centers data
        $this->seedInitialData();
    }

    private function seedInitialData()
    {
        $centers = [
            [
                'name_uz' => "Axborot resurs markazi",
                'name_ru' => "Информационно-ресурсный центр",
                'short_name' => 'ARM',
                'code' => 'IRC',
                'type' => 'it',
                'description' => "Universitet axborot tizimlarini boshqarish va rivojlantirish markazi",
                'is_active' => true,
                'order_number' => 1,
            ],
            [
                'name_uz' => "Ilmiy tadqiqot markazi",
                'name_ru' => "Научно-исследовательский центр",
                'short_name' => 'ITM',
                'code' => 'SRC',
                'type' => 'research',
                'description' => "Ilmiy tadqiqotlar va innovatsiyalar markazi",
                'is_active' => true,
                'order_number' => 2,
            ],
            [
                'name_uz' => "Xalqaro hamkorlik markazi",
                'name_ru' => "Центр международного сотрудничества",
                'short_name' => 'XHM',
                'code' => 'ICC',
                'type' => 'international',
                'description' => "Xalqaro aloqalar va hamkorlik markazi",
                'is_active' => true,
                'order_number' => 3,
            ],
            [
                'name_uz' => "Karera markazi",
                'name_ru' => "Центр карьеры",
                'short_name' => 'KM',
                'code' => 'CC',
                'type' => 'service',
                'description' => "Talabalar va bitiruvchilarni ish bilan ta'minlash markazi",
                'is_active' => true,
                'order_number' => 4,
            ],
            [
                'name_uz' => "Malaka oshirish markazi",
                'name_ru' => "Центр повышения квалификации",
                'short_name' => 'MOM',
                'code' => 'TDC',
                'type' => 'training',
                'description' => "O'qituvchilar va xodimlar malakasini oshirish markazi",
                'is_active' => true,
                'order_number' => 5,
            ],
            [
                'name_uz' => "Marketing va talabalar bilan ishlash markazi",
                'name_ru' => "Центр маркетинга и работы со студентами",
                'short_name' => 'MTIM',
                'code' => 'MSC',
                'type' => 'service',
                'description' => "Marketing va talabalar bilan ishlash markazi",
                'is_active' => true,
                'order_number' => 6,
            ],
        ];

        foreach ($centers as $center) {
            \DB::table('centers')->insert($center + [
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('centers');
    }
};