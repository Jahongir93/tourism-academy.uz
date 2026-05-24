<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AcademicDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create academic years if not exists
        if (DB::table('academic_years')->count() == 0) {
            DB::table('academic_years')->insert([
                [
                    'name' => '2024-2025',
                    'start_date' => '2024-09-01',
                    'end_date' => '2025-06-30',
                    'is_current' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            ]);
        }

        // Create faculties if not exists
        if (DB::table('faculties')->count() == 0) {
            DB::table('faculties')->insert([
                [
                    'name' => 'Turizm fakulteti',
                    'code' => 'TF',
                    'dean_name' => 'Karimov A.B.',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'name' => 'Iqtisodiyot fakulteti',
                    'code' => 'IF',
                    'dean_name' => 'Rahimov D.S.',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }

        // Get faculty IDs
        $tourismFacultyId = DB::table('faculties')->where('code', 'TF')->first()->id;
        $economicsFacultyId = DB::table('faculties')->where('code', 'IF')->first()->id;

        // Create specialties
        if (DB::table('specialties')->count() == 0) {
            DB::table('specialties')->insert([
                [
                    'faculty_id' => $tourismFacultyId,
                    'code' => '5610100',
                    'name_uz' => 'Turizm (faoliyat turlari bo\'yicha)',
                    'name_ru' => 'Туризм (по видам деятельности)',
                    'degree' => 'bakalavr',
                    'education_form' => 'kunduzgi',
                    'education_type' => 'shartnoma',
                    'duration_years' => 4,
                    'credits_required' => 240,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'faculty_id' => $tourismFacultyId,
                    'code' => '5610200',
                    'name_uz' => 'Mehmonxona xo\'jaligi',
                    'name_ru' => 'Гостиничное хозяйство',
                    'degree' => 'bakalavr',
                    'education_form' => 'kunduzgi',
                    'education_type' => 'shartnoma',
                    'duration_years' => 4,
                    'credits_required' => 240,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'faculty_id' => $economicsFacultyId,
                    'code' => '5230100',
                    'name_uz' => 'Iqtisodiyot',
                    'name_ru' => 'Экономика',
                    'degree' => 'bakalavr',
                    'education_form' => 'kunduzgi',
                    'education_type' => 'shartnoma',
                    'duration_years' => 4,
                    'credits_required' => 240,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }

        // Get specialty IDs
        $tourismSpecialtyId = DB::table('specialties')->where('code', '5610100')->first()->id;
        $hotelSpecialtyId = DB::table('specialties')->where('code', '5610200')->first()->id;
        $economicsSpecialtyId = DB::table('specialties')->where('code', '5230100')->first()->id;

        // Create academic groups
        if (DB::table('academic_groups')->count() == 0) {
            DB::table('academic_groups')->insert([
                [
                    'specialty_id' => $tourismSpecialtyId,
                    'faculty_id' => $tourismFacultyId,
                    'name' => 'TUR-101',
                    'course' => 1,
                    'language' => 'uz',
                    'academic_year' => 2024,
                    'semester' => '1',
                    'max_students' => 30,
                    'current_students' => 25,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'specialty_id' => $tourismSpecialtyId,
                    'faculty_id' => $tourismFacultyId,
                    'name' => 'TUR-201',
                    'course' => 2,
                    'language' => 'uz',
                    'academic_year' => 2024,
                    'semester' => '1',
                    'max_students' => 30,
                    'current_students' => 28,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'specialty_id' => $hotelSpecialtyId,
                    'faculty_id' => $tourismFacultyId,
                    'name' => 'HOT-101',
                    'course' => 1,
                    'language' => 'uz',
                    'academic_year' => 2024,
                    'semester' => '1',
                    'max_students' => 25,
                    'current_students' => 22,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'specialty_id' => $economicsSpecialtyId,
                    'faculty_id' => $economicsFacultyId,
                    'name' => 'ECO-101',
                    'course' => 1,
                    'language' => 'uz',
                    'academic_year' => 2024,
                    'semester' => '1',
                    'max_students' => 35,
                    'current_students' => 32,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }

        // Create some test students
        $academicYearId = DB::table('academic_years')->where('is_current', true)->first()->id;
        $groupId = DB::table('academic_groups')->where('name', 'TUR-101')->first()->id;

        // Create test students if not exists
        if (DB::table('students')->count() == 0) {
            $students = [
                [
                    'first_name' => 'Aziz',
                    'last_name' => 'Karimov',
                    'middle_name' => 'Bahodirovich',
                    'student_id' => 'STU001',
                    'email' => 'aziz@student.uz',
                    'phone' => '+998901234501',
                    'group_id' => $groupId,
                    'gender' => 'male',
                    'admission_date' => '2024-09-01',
                    'status' => 'active',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'first_name' => 'Dilnoza',
                    'last_name' => 'Rahimova',
                    'middle_name' => 'Rustamovna',
                    'student_id' => 'STU002',
                    'email' => 'dilnoza@student.uz',
                    'phone' => '+998901234502',
                    'group_id' => $groupId,
                    'gender' => 'female',
                    'admission_date' => '2024-09-01',
                    'status' => 'active',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ];

            DB::table('students')->insert($students);
        }

        // Create student enrollments
        $students = DB::table('students')->get();
        foreach ($students as $student) {
            DB::table('student_enrollments')->insertOrIgnore([
                'student_id' => $student->id,
                'academic_year_id' => $academicYearId,
                'group_id' => $student->group_id,
                'specialty_id' => $tourismSpecialtyId,
                'enrollment_date' => '2024-09-01',
                'status' => 'active',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('Academic data seeded successfully!');
    }
}