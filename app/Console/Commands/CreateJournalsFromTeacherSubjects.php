<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TeacherSubject;
use App\Models\JournalEntry;
use App\Models\Group;
use App\Models\StudentGroup;
use App\Models\AcademicYear;
use App\Models\Employee;
use App\Models\Teacher;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CreateJournalsFromTeacherSubjects extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'journals:create-from-teacher-subjects';

    /**
     * The console command description.
     */
    protected $description = 'Create journal entries from existing TeacherSubject records';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting journal creation from TeacherSubject records...');

        $teacherSubjects = TeacherSubject::where('status', 'active')->get();

        if ($teacherSubjects->isEmpty()) {
            $this->warn('No active TeacherSubject records found.');
            return 0;
        }

        $this->info("Found {$teacherSubjects->count()} TeacherSubject records.");

        $createdCount = 0;
        $skippedCount = 0;
        $errorCount = 0;

        $academicYear = AcademicYear::where('is_current', true)->first();

        if (!$academicYear) {
            $this->error('No current academic year found!');
            return 1;
        }

        foreach ($teacherSubjects as $ts) {
            $groupIds = $ts->group_ids;

            if (empty($groupIds) || !is_array($groupIds)) {
                $this->warn("TeacherSubject #{$ts->id}: No group_ids found, skipping.");
                $skippedCount++;
                continue;
            }

            // Convert Employee ID to Teacher ID
            $teacherId = $this->getTeacherId($ts->teacher_id);
            if (!$teacherId) {
                $this->warn("TeacherSubject #{$ts->id}: Could not get Teacher ID for Employee #{$ts->teacher_id}, skipping.");
                $skippedCount++;
                continue;
            }

            $semester = $ts->semester_id ?? (now()->month >= 9 ? 1 : 2);

            foreach ($groupIds as $groupId) {
                try {
                    // Find group
                    $group = DB::table('groups')->where('id', $groupId)->first();
                    $actualGroupId = $groupId;

                    if (!$group) {
                        // Try StudentGroup
                        $studentGroup = StudentGroup::find($groupId);
                        if ($studentGroup) {
                            // Check if exists in groups table
                            $existingGroup = DB::table('groups')->where('name', $studentGroup->name)->first();

                            if ($existingGroup) {
                                $actualGroupId = $existingGroup->id;
                            } else {
                                // Create in groups table
                                $actualGroupId = DB::table('groups')->insertGetId([
                                    'name' => $studentGroup->name,
                                    'code' => $studentGroup->code ?? $studentGroup->name,
                                    'department_id' => $studentGroup->faculty_id ?? 1,
                                    'course' => $studentGroup->course ?? 1,
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ]);
                                $this->info("  Created group '{$studentGroup->name}' in groups table.");
                            }
                        } else {
                            $this->warn("TeacherSubject #{$ts->id}: Group #{$groupId} not found, skipping.");
                            $skippedCount++;
                            continue;
                        }
                    }

                    // Check if journal entry already exists
                    $existingEntry = JournalEntry::where('group_id', $actualGroupId)
                        ->where('subject_id', $ts->subject_id)
                        ->where('teacher_id', $teacherId)
                        ->where('academic_year_id', $academicYear->id)
                        ->where('semester_id', $semester)
                        ->first();

                    if ($existingEntry) {
                        $this->line("  JournalEntry already exists for TeacherSubject #{$ts->id}, Group #{$actualGroupId}");
                        $skippedCount++;
                        continue;
                    }

                    // Create journal entry
                    $journalEntry = JournalEntry::create([
                        'subject_id' => $ts->subject_id,
                        'group_id' => $actualGroupId,
                        'teacher_id' => $teacherId,
                        'academic_year_id' => $academicYear->id,
                        'semester_id' => $semester,
                    ]);

                    $this->info("  Created JournalEntry #{$journalEntry->id} for TeacherSubject #{$ts->id}, Group #{$actualGroupId}, Teacher #{$teacherId}");
                    $createdCount++;

                } catch (\Exception $e) {
                    $this->error("  Error creating JournalEntry for TeacherSubject #{$ts->id}, Group #{$groupId}: {$e->getMessage()}");
                    Log::error('CreateJournalsFromTeacherSubjects error', [
                        'teacher_subject_id' => $ts->id,
                        'group_id' => $groupId,
                        'error' => $e->getMessage()
                    ]);
                    $errorCount++;
                }
            }
        }

        $this->newLine();
        $this->info('=== Summary ===');
        $this->info("Created: {$createdCount}");
        $this->info("Skipped: {$skippedCount}");
        $this->info("Errors: {$errorCount}");

        return 0;
    }

    /**
     * Get Teacher ID from Employee ID
     * TeacherSubject.teacher_id is Employee ID, but JournalEntry.teacher_id references teachers table
     */
    protected function getTeacherId(int $employeeId): ?int
    {
        $employee = Employee::find($employeeId);
        if (!$employee) {
            $this->warn("  Employee #{$employeeId} not found");
            return null;
        }

        // If employee has user_id, find Teacher by user_id
        if ($employee->user_id) {
            $teacher = Teacher::where('user_id', $employee->user_id)->first();
            if ($teacher) {
                return $teacher->id;
            }

            // If no Teacher exists, create one
            $teacher = Teacher::create([
                'user_id' => $employee->user_id,
                'first_name' => $employee->first_name ?? 'Unknown',
                'last_name' => $employee->last_name ?? 'Unknown',
                'middle_name' => $employee->middle_name,
                'email' => $employee->email ?? ($employee->user ? $employee->user->email : null),
                'phone' => $employee->phone,
                'position' => $employee->position ?? 'O\'qituvchi',
                'department_id' => $employee->department_id,
            ]);

            $this->info("  Created Teacher #{$teacher->id} for Employee #{$employeeId}");
            return $teacher->id;
        }

        // If employee has no user_id, we can't create a proper teacher record
        $this->warn("  Employee #{$employeeId} has no user_id, cannot create Teacher record");
        return null;
    }
}
