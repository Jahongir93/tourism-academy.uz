<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GroupSubjectsSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('📚 Guruh fanlarini yaratmoqda...');

        $academicYear = DB::table('academic_years')->first();

        if (!$academicYear) {
            $this->command->error('O\'quv yili topilmadi!');
            return;
        }

        $groups = DB::table('groups')->pluck('id')->toArray();
        $subjects = DB::table('subjects')->pluck('id')->toArray();
        $teachers = DB::table('users')
            ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->where('roles.name', 'teacher')
            ->pluck('users.id')
            ->toArray();

        if (empty($teachers)) {
            $teachers = [null];
        }

        $count = 0;
        foreach ($groups as $groupId) {
            // Har bir guruh uchun 5-8 ta fan biriktirish
            $randomSubjects = array_rand(array_flip($subjects), min(rand(5, 8), count($subjects)));
            if (!is_array($randomSubjects)) {
                $randomSubjects = [$randomSubjects];
            }

            foreach ($randomSubjects as $subjectId) {
                try {
                    DB::table('group_subjects')->insert([
                        'group_id' => $groupId,
                        'subject_id' => $subjectId,
                        'teacher_id' => $teachers[array_rand($teachers)],
                        'academic_year_id' => $academicYear->id,
                        'semester' => rand(1, 2),
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                    $count++;
                } catch (\Exception $e) {
                    // Skip duplicates
                }
            }
        }

        $this->command->info("✅ {$count} ta guruh-fan bog'lanishi yaratildi!");
    }
}
