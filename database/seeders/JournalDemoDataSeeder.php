<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Specialty;
use App\Models\Subject;
use App\Models\StudentGroup;
use App\Models\Teacher;
use App\Models\AcademicYear;
use App\Models\SpecialtySubject;
use App\Models\GroupSubject;

class JournalDemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Creating demo journal data...');

        // Get current academic year or create one
        $currentYear = AcademicYear::firstOrCreate(
            ['year' => '2024-2025'],
            ['is_current' => true, 'start_date' => '2024-09-01', 'end_date' => '2025-06-30']
        );

        // Get first specialty
        $specialty = Specialty::first();
        if (!$specialty) {
            $this->command->error('No specialty found! Please create a specialty first.');
            return;
        }

        // Get subjects
        $subjects = Subject::limit(5)->get();
        if ($subjects->isEmpty()) {
            $this->command->error('No subjects found! Please create subjects first.');
            return;
        }

        // Create specialty-subject relationships (curriculum)
        $this->command->info('Creating specialty curriculum...');
        foreach ($subjects as $index => $subject) {
            $semester = ($index % 2) + 1; // 1 or 2
            $courseYear = floor($index / 2) + 1; // 1, 2, etc.

            SpecialtySubject::firstOrCreate(
                [
                    'specialty_id' => $specialty->id,
                    'subject_id' => $subject->id,
                    'semester' => $semester
                ],
                [
                    'course_year' => $courseYear,
                    'is_required' => true,
                    'credits' => rand(3, 6),
                    'hours_total' => rand(60, 120)
                ]
            );
            $this->command->info("  - Added {$subject->name_uz} to semester {$semester}");
        }

        // Get or create student group
        $group = StudentGroup::first();
        if (!$group) {
            $group = StudentGroup::create([
                'name' => 'DEMO-101',
                'specialty_id' => $specialty->id,
                'academic_year_id' => $currentYear->id,
                'course_year' => 1
            ]);
            $this->command->info('Created demo group: DEMO-101');
        }

        // Get teacher
        $teacher = Teacher::first();

        // Create group subjects from specialty subjects
        $this->command->info('Creating group subjects...');
        $specialtySubjects = SpecialtySubject::where('specialty_id', $specialty->id)
            ->where('semester', 1) // Current semester
            ->get();

        foreach ($specialtySubjects as $ss) {
            GroupSubject::firstOrCreate(
                [
                    'student_group_id' => $group->id,
                    'subject_id' => $ss->subject_id,
                    'academic_year_id' => $currentYear->id,
                    'semester' => 1
                ],
                [
                    'teacher_id' => $teacher ? $teacher->id : null,
                    'is_active' => true
                ]
            );

            $teacherName = $teacher ? "{$teacher->first_name} {$teacher->last_name}" : 'Vakant';
            $this->command->info("  - {$ss->subject->name_uz} → {$group->name} ({$teacherName})");
        }

        $this->command->info('Demo journal data created successfully!');
        $this->command->info("Group: {$group->name}");
        $this->command->info("Specialty: {$specialty->name_uz}");
        $this->command->info("Academic Year: {$currentYear->year}");
        $this->command->info("Semester: 1");
    }
}
