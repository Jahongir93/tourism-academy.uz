<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Test o'qituvchilarni yaratish
        DB::table('employees')->insert([
            [
                'employee_code' => 'EMP001',
                'first_name' => 'Ahmadjon',
                'last_name' => 'Karimov',
                'middle_name' => 'Rustamovich',
                'birth_date' => '1985-05-15',
                'gender' => 'male',
                'phone' => '+998901234567',
                'email' => 'ahmadjon@tourism.uz',
                'address_permanent' => 'Samarqand sh., Rudaki ko\'chasi, 123-uy',
                'employee_type' => 'teacher',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'employee_code' => 'EMP002',
                'first_name' => 'Gulnora',
                'last_name' => 'Rahimova',
                'middle_name' => 'Bahodirovna',
                'birth_date' => '1990-08-22',
                'gender' => 'female',
                'phone' => '+998901234568',
                'email' => 'gulnora@tourism.uz',
                'address_permanent' => 'Samarqand sh., Gagarin ko\'chasi, 45-uy',
                'employee_type' => 'teacher',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'employee_code' => 'EMP003',
                'first_name' => 'Bobur',
                'last_name' => 'Saidov',
                'middle_name' => 'Ulug\'bekovich',
                'birth_date' => '1988-03-10',
                'gender' => 'male',
                'phone' => '+998901234569',
                'email' => 'bobur@tourism.uz',
                'address_permanent' => 'Samarqand sh., Mirzo Ulug\'bek ko\'chasi, 78-uy',
                'employee_type' => 'teacher',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'employee_code' => 'EMP004',
                'first_name' => 'Admin',
                'last_name' => 'User',
                'middle_name' => null,
                'birth_date' => '1980-01-01',
                'gender' => 'male',
                'phone' => '+998901234570',
                'email' => 'admin@tourism.uz',
                'address_permanent' => 'Samarqand sh., Mustaqillik ko\'chasi, 1-uy',
                'employee_type' => 'admin',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);

        $this->command->info('Xodimlar muvaffaqiyatli yaratildi!');
    }
}