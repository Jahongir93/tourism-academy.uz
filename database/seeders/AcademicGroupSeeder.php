<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AcademicGroup;
use App\Models\Faculty;
use Illuminate\Support\Facades\DB;

class AcademicGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Oldingi guruhlarni o'chirish
        $this->command->info('Mavjud guruhlarni o\'chirish...');
        DB::table('academic_groups')->delete();

        $faculties = Faculty::all();

        if ($faculties->isEmpty()) {
            $this->command->warn('Fakultetlar topilmadi! Avval fakultetlarni yarating.');
            return;
        }

        $groupsData = [];

        foreach ($faculties as $index => $faculty) {
            $facultyCode = $faculty->code ?? substr($faculty->name_uz ?? $faculty->name, 0, 3);

            // Har bir fakultet uchun 1-4 kurs guruhlarini yaratamiz
            for ($course = 1; $course <= 4; $course++) {
                // Har kursda 2-3 ta guruh
                $groupsPerCourse = $course <= 2 ? 3 : 2; // 1-2 kurslarda ko'proq guruh

                for ($groupNum = 1; $groupNum <= $groupsPerCourse; $groupNum++) {
                    $groupsData[] = [
                        'faculty_id' => $faculty->id,
                        'name' => "{$facultyCode}-{$course}{$groupNum}",
                        'course' => $course,
                        'language' => 'uz',
                        'academic_year' => 2024,
                        'semester' => ($course % 2 == 0) ? '2' : '1', // Toq kurslar 1-semestr, juft kurslar 2-semestr
                        'current_students' => rand(25, 35),
                        'max_students' => 35,
                        'curator_id' => null,
                        'specialty_id' => null,
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
        }

        // Insert all groups using DB to bypass observers
        DB::table('academic_groups')->insert($groupsData);

        $this->command->info('Jami ' . count($groupsData) . ' ta guruh yaratildi!');
    }
}
