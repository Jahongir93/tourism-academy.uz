<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class SimpleStudentSeeder extends Seeder
{
    public function run()
    {
        // Avval student userlarni yaratamiz
        $user1 = User::firstOrCreate(
            ['email' => 'ahror@example.com'],
            [
                'name' => 'Ahror Ahmedov',
                'password' => bcrypt('password'),
                'phone' => '+998991111111',
            ]
        );
        $user1->assignRole('Student');

        $user2 = User::firstOrCreate(
            ['email' => 'malika@example.com'],
            [
                'name' => 'Malika Karimova',
                'password' => bcrypt('password'),
                'phone' => '+998992222222',
            ]
        );
        $user2->assignRole('Student');

        // Faculty va Specialty ID larni olish
        $facultyId = DB::table('faculties')->first()->id;
        $specialtyId = DB::table('specialties')->first()->id;

        // Agar talaba mavjud bo'lmasa qo'shamiz
        if (!DB::table('students')->where('student_id', '202500001')->exists()) {
            DB::table('students')->insert([
                'user_id' => $user1->id,
                'student_id' => '202500001',
                'first_name' => 'Ahror',
                'last_name' => 'Ahmedov',
                'birth_date' => '2000-05-15',
                'gender' => 'erkak',
                'nationality' => 'uzbek',
                'citizenship' => 'uzbekistan',
                'phone' => '+998991111111',
                'email' => 'ahror@example.com',
                'faculty_id' => $facultyId,
                'specialty_id' => $specialtyId,
                'course' => 2,
                'semester' => 3,
                'education_form' => 'kunduzgi',
                'education_type' => 'shartnoma',
                'education_language' => 'uz',
                'admission_year' => 2023,
                'admission_date' => '2023-09-01',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        if (!DB::table('students')->where('student_id', '202500002')->exists()) {
            DB::table('students')->insert([
                'user_id' => $user2->id,
                'student_id' => '202500002',
                'first_name' => 'Malika',
                'last_name' => 'Karimova',
                'birth_date' => '2001-03-20',
                'gender' => 'ayol',
                'nationality' => 'uzbek',
                'citizenship' => 'uzbekistan',
                'phone' => '+998992222222',
                'email' => 'malika@example.com',
                'faculty_id' => $facultyId,
                'specialty_id' => $specialtyId,
                'course' => 1,
                'semester' => 2,
                'education_form' => 'kunduzgi',
                'education_type' => 'byudjet',
                'education_language' => 'uz',
                'admission_year' => 2024,
                'admission_date' => '2024-09-01',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('2 ta test talaba yaratildi!');
    }
}