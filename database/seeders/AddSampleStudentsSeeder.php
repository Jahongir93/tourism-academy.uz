<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AddSampleStudentsSeeder extends Seeder
{
    public function run()
    {
        // Ensure Student role exists
        if (class_exists('\Spatie\Permission\Models\Role')) {
            \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Student']);
        }

        // Create or get faculty
        $faculty = DB::table('faculties')->first();
        if (!$faculty) {
            DB::table('faculties')->insert([
                'name' => 'Axborot texnologiyalari',
                'code' => 'IT',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $faculty = DB::table('faculties')->first();
        }

        // Create or get specialty
        $specialty = DB::table('specialties')->first();
        if (!$specialty) {
            DB::table('specialties')->insert([
                'name_uz' => 'Dasturiy injiniring',
                'name_ru' => 'Программная инженерия',
                'name_en' => 'Software Engineering',
                'code' => '5330100',
                'faculty_id' => $faculty->id,
                'duration_years' => 4,
                'degree' => 'bakalavr',
                'education_form' => 'kunduzgi',
                'education_type' => 'shartnoma',
                'language' => 'uz',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $specialty = DB::table('specialties')->first();
        }

        // Create academic group if not exists
        $group = DB::table('academic_groups')->first();
        if (!$group) {
            DB::table('academic_groups')->insert([
                'name' => 'IT-201',
                'specialty_id' => $specialty->id,
                'faculty_id' => $faculty->id,
                'course' => 2,
                'language' => 'uz',
                'academic_year' => date('Y'),
                'max_students' => 30,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $group = DB::table('academic_groups')->first();
        }

        // Sample students data
        $students = [
            [
                'name' => 'Abdullayev Jasur',
                'email' => 'jasur@student.uz',
                'password' => 'student123',
                'student_id' => 'S2024001',
                'first_name' => 'Jasur',
                'last_name' => 'Abdullayev',
                'middle_name' => 'Akmalovich',
                'phone' => '+998901234567',
                'gender' => 'male',
                'admission_date' => '2023-09-01',
            ],
            [
                'name' => 'Karimova Malika',
                'email' => 'malika@student.uz',
                'password' => 'student123',
                'student_id' => 'S2024002',
                'first_name' => 'Malika',
                'last_name' => 'Karimova',
                'middle_name' => 'Rustamovna',
                'phone' => '+998901234568',
                'gender' => 'female',
                'admission_date' => '2023-09-01',
            ],
            [
                'name' => 'Shodiyev Bekzod',
                'email' => 'bekzod@student.uz',
                'password' => 'student123',
                'student_id' => 'S2024003',
                'first_name' => 'Bekzod',
                'last_name' => 'Shodiyev',
                'middle_name' => 'Shavkatovich',
                'phone' => '+998901234569',
                'gender' => 'male',
                'admission_date' => '2023-09-01',
            ],
            [
                'name' => 'Rahimova Dilnoza',
                'email' => 'dilnoza@student.uz',
                'password' => 'student123',
                'student_id' => 'S2024004',
                'first_name' => 'Dilnoza',
                'last_name' => 'Rahimova',
                'middle_name' => 'Bahodirovna',
                'phone' => '+998901234570',
                'gender' => 'female',
                'admission_date' => '2024-09-01',
            ],
            [
                'name' => 'Tursunov Sardor',
                'email' => 'sardor@student.uz',
                'password' => 'student123',
                'student_id' => 'S2024005',
                'first_name' => 'Sardor',
                'last_name' => 'Tursunov',
                'middle_name' => 'Ilhomovich',
                'phone' => '+998901234571',
                'gender' => 'male',
                'admission_date' => '2024-09-01',
            ],
        ];

        foreach ($students as $studentData) {
            // Create user account
            $user = User::firstOrCreate(
                ['email' => $studentData['email']],
                [
                    'name' => $studentData['name'],
                    'password' => Hash::make($studentData['password']),
                    'email_verified_at' => now(),
                ]
            );

            // Assign Student role
            if (class_exists('\Spatie\Permission\Models\Role')) {
                $user->assignRole('Student');
            }

            // Check if student already exists
            $existingStudent = DB::table('students')
                ->where('student_id', $studentData['student_id'])
                ->first();

            if (!$existingStudent) {
                // Create student record
                DB::table('students')->insert([
                    'first_name' => $studentData['first_name'],
                    'last_name' => $studentData['last_name'],
                    'middle_name' => $studentData['middle_name'],
                    'student_id' => $studentData['student_id'],
                    'email' => $studentData['email'],
                    'phone' => $studentData['phone'],
                    'group_id' => $group->id,
                    'gender' => $studentData['gender'],
                    'admission_date' => $studentData['admission_date'],
                    'status' => 'active',
                    'user_id' => $user->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $student = DB::table('students')
                    ->where('student_id', $studentData['student_id'])
                    ->first();

                // Add passport information
                DB::table('student_passports')->insert([
                    'student_id' => $student->id,
                    'passport_series' => 'AA',
                    'passport_number' => rand(1000000, 9999999),
                    'issue_date' => '2020-01-01',
                    'issued_by' => 'Toshkent shahar IIB',
                    'birth_date' => '2005-' . rand(1, 12) . '-' . rand(1, 28),
                    'birth_place' => 'Toshkent',
                    'nationality' => 'Uzbekistan',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Add address information
                DB::table('student_addresses')->insert([
                    'student_id' => $student->id,
                    'type' => 'permanent',
                    'country' => 'Uzbekistan',
                    'region' => 'Toshkent',
                    'district' => 'Yunusobod',
                    'city' => 'Toshkent',
                    'street' => 'Amir Temur ko\'chasi',
                    'house_number' => rand(1, 100),
                    'is_current' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $this->command->info('Namunaviy talabalar muvaffaqiyatli qo\'shildi!');
        $this->command->info('Barcha talabalar uchun parol: student123');
        $this->command->info('');
        $this->command->info('Talabalar ro\'yxati:');
        $this->command->info('1. Jasur Abdullayev - jasur@student.uz');
        $this->command->info('2. Malika Karimova - malika@student.uz');
        $this->command->info('3. Bekzod Shodiyev - bekzod@student.uz');
        $this->command->info('4. Dilnoza Rahimova - dilnoza@student.uz');
        $this->command->info('5. Sardor Tursunov - sardor@student.uz');
    }
}