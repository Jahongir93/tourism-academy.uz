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
        Schema::create('scholarships', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Grant/Stipendiya nomi
            $table->enum('type', ['grant', 'scholarship', 'discount', 'financial_aid'])->default('scholarship');
            $table->text('description')->nullable();

            // Summa
            $table->decimal('amount', 15, 2)->default(0);
            $table->enum('amount_type', ['fixed', 'percentage', 'full'])->default('fixed');

            // Talablar
            $table->decimal('min_gpa', 3, 2)->nullable(); // Minimal GPA
            $table->integer('min_attendance')->nullable(); // Minimal davomat foizi
            $table->json('requirements')->nullable(); // Qo'shimcha talablar

            // Holat
            $table->enum('status', ['active', 'inactive', 'expired'])->default('active');
            $table->integer('available_slots')->nullable(); // Mavjud o'rinlar soni
            $table->integer('awarded_count')->default(0); // Berilgan grantlar soni

            // Muddatlar
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->date('application_deadline')->nullable();

            // Qo'shimcha ma'lumotlar
            $table->string('sponsor')->nullable(); // Homiy
            $table->json('eligible_programs')->nullable(); // Qaysi yo'nalishlarga tegishli
            $table->json('eligible_courses')->nullable(); // Qaysi kurslarga tegishli

            $table->timestamps();
            $table->softDeletes();

            $table->index('type');
            $table->index('status');
            $table->index(['start_date', 'end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scholarships');
    }
};
