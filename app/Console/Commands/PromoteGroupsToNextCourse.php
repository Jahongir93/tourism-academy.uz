<?php

namespace App\Console\Commands;

use App\Models\Group;
use App\Models\Student;
use App\Models\StudentMovement;
use App\Models\AcademicYear;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PromoteGroupsToNextCourse extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'academic:promote-groups
                            {--dry-run : Preview changes without applying them}
                            {--max-course=4 : Maximum course number (groups at this level will be marked as graduated)}
                            {--year= : Target academic year (e.g., 2025-2026)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Promote all active groups and their students to the next course for new academic year';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $maxCourse = (int) $this->option('max-course');
        $targetYear = $this->option('year');

        if ($dryRun) {
            $this->info('=== DRY RUN MODE - No changes will be made ===');
        }

        // Get current academic year
        $currentYear = AcademicYear::where('is_current', true)->first();
        if (!$currentYear) {
            $this->error('Joriy o\'quv yili topilmadi. Avval o\'quv yilini sozlang.');
            return 1;
        }

        $this->info("Joriy o'quv yili: {$currentYear->name}");

        // Determine target academic year
        if (!$targetYear) {
            // Auto-generate next year
            $parts = explode('-', $currentYear->name);
            if (count($parts) === 2) {
                $nextStart = (int)$parts[1];
                $nextEnd = $nextStart + 1;
                $targetYear = "{$nextStart}-{$nextEnd}";
            } else {
                $this->error('O\'quv yili formati noto\'g\'ri. Format: 2024-2025');
                return 1;
            }
        }

        $this->info("Yangi o'quv yili: {$targetYear}");
        $this->newLine();

        // Get all active groups
        $groups = Group::where('is_active', true)
            ->with(['students' => function($q) {
                $q->where('status', 'active');
            }])
            ->orderBy('course')
            ->orderBy('name')
            ->get();

        if ($groups->isEmpty()) {
            $this->warn('Faol guruhlar topilmadi.');
            return 0;
        }

        $this->info("Jami faol guruhlar: {$groups->count()}");
        $this->newLine();

        // Statistics
        $stats = [
            'promoted_groups' => 0,
            'promoted_students' => 0,
            'graduated_groups' => 0,
            'graduated_students' => 0,
            'errors' => 0,
        ];

        // Create table for display
        $tableData = [];

        DB::beginTransaction();

        try {
            foreach ($groups as $group) {
                $currentCourse = $group->course ?? 1;
                $studentCount = $group->students->count();

                if ($currentCourse >= $maxCourse) {
                    // Group is graduating
                    $tableData[] = [
                        $group->name,
                        $currentCourse,
                        'Bitiruvchi',
                        $studentCount,
                        'Guruh yakunlanadi'
                    ];

                    if (!$dryRun) {
                        // Mark group as inactive (graduated)
                        $group->update([
                            'is_active' => false,
                            'academic_year' => $targetYear,
                        ]);

                        // Graduate all students
                        foreach ($group->students as $student) {
                            // Record movement
                            StudentMovement::create([
                                'student_id' => $student->id,
                                'movement_type' => 'graduation',
                                'from_group_id' => $group->id,
                                'from_course' => $currentCourse,
                                'reason' => "O'quv yili yakunlandi - {$targetYear}",
                                'movement_date' => now(),
                            ]);

                            // Update student status
                            $student->update(['status' => 'graduated']);
                        }
                    }

                    $stats['graduated_groups']++;
                    $stats['graduated_students'] += $studentCount;

                } else {
                    // Promote to next course
                    $newCourse = $currentCourse + 1;

                    $tableData[] = [
                        $group->name,
                        $currentCourse,
                        $newCourse,
                        $studentCount,
                        'Ko\'tariladi'
                    ];

                    if (!$dryRun) {
                        // Update group course
                        $group->update([
                            'course' => $newCourse,
                            'academic_year' => $targetYear,
                            'semester' => 1, // Reset to first semester
                        ]);

                        // Update all students in the group
                        foreach ($group->students as $student) {
                            // Record movement
                            StudentMovement::create([
                                'student_id' => $student->id,
                                'movement_type' => 'course_promotion',
                                'from_group_id' => $group->id,
                                'to_group_id' => $group->id,
                                'from_course' => $currentCourse,
                                'to_course' => $newCourse,
                                'reason' => "Yangi o'quv yili - {$targetYear}",
                                'movement_date' => now(),
                            ]);

                            // Update student course
                            $student->update([
                                'course' => $newCourse,
                                'semester' => 1,
                            ]);
                        }
                    }

                    $stats['promoted_groups']++;
                    $stats['promoted_students'] += $studentCount;
                }
            }

            if (!$dryRun) {
                // Create or update new academic year
                $newAcademicYear = AcademicYear::firstOrCreate(
                    ['name' => $targetYear],
                    [
                        'start_date' => now()->setMonth(9)->setDay(1),
                        'end_date' => now()->addYear()->setMonth(6)->setDay(30),
                        'is_current' => false,
                    ]
                );

                // Set as current year
                AcademicYear::where('is_current', true)->update(['is_current' => false]);
                $newAcademicYear->update(['is_current' => true]);

                DB::commit();
                $this->info('O\'zgarishlar saqlandi.');
            } else {
                DB::rollBack();
                $this->warn('DRY RUN - O\'zgarishlar saqlanmadi.');
            }

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Xatolik yuz berdi: ' . $e->getMessage());
            Log::error('Group promotion error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }

        // Display results
        $this->newLine();
        $this->table(
            ['Guruh', 'Hozirgi kurs', 'Yangi kurs', 'Talabalar', 'Holat'],
            $tableData
        );

        $this->newLine();
        $this->info('=== NATIJALAR ===');
        $this->line("Ko'tarilgan guruhlar: {$stats['promoted_groups']}");
        $this->line("Ko'tarilgan talabalar: {$stats['promoted_students']}");
        $this->line("Bitiruvchi guruhlar: {$stats['graduated_groups']}");
        $this->line("Bitiruvchi talabalar: {$stats['graduated_students']}");

        return 0;
    }
}
