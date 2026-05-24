<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\AcademicGroup;

class MigrateAcademicGroupsToGroupsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Academic_groups ma\'lumotlarini groups jadvaliga ko\'chirish...');

        // Eski department-based guruhlarni o'chirish
        $this->command->info('Eski guruhlarni o\'chirish...');
        DB::table('groups')->where('code', 'like', 'G-%')->delete();

        // academic_groups ma'lumotlarini olish
        $academicGroups = AcademicGroup::all();

        if ($academicGroups->isEmpty()) {
            $this->command->warn('academic_groups jadvalida ma\'lumot topilmadi!');
            return;
        }

        $groupsData = [];

        foreach ($academicGroups as $academicGroup) {
            // Get department_id from faculty (use first department of faculty as default)
            $department = DB::table('departments')
                ->where('faculty_id', $academicGroup->faculty_id)
                ->first();

            if (!$department) {
                $this->command->warn("Fakultet {$academicGroup->faculty_id} uchun kafedra topilmadi. Guruh {$academicGroup->name} o'tkazib yuborildi.");
                continue;
            }

            $groupsData[] = [
                'name' => $academicGroup->name,
                'code' => 'G-' . $academicGroup->name, // Generate unique code
                'department_id' => $department->id,
                'faculty_id' => $academicGroup->faculty_id,
                'specialty_id' => $academicGroup->specialty_id,
                'course' => $academicGroup->course,
                'academic_year' => $academicGroup->academic_year,
                'semester' => $academicGroup->semester,
                'students_count' => $academicGroup->current_students,
                'max_students' => $academicGroup->max_students,
                'current_students' => $academicGroup->current_students,
                'curator_id' => $academicGroup->curator_id,
                'language' => $academicGroup->language,
                'is_active' => $academicGroup->is_active,
                'education_type' => 'kunduzgi', // Default
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Insert using DB to avoid triggering journal entry creation
        DB::table('groups')->insert($groupsData);

        $this->command->info('Jami ' . count($groupsData) . ' ta guruh ko\'chirildi!');
    }
}
