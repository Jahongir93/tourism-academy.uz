<?php

namespace App\Observers;

use App\Models\TeacherSubject;
use App\Models\JournalEntry;
use App\Models\Group;
use App\Models\StudentGroup;
use App\Models\AcademicYear;
use App\Models\Employee;
use App\Models\Teacher;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TeacherSubjectObserver
{
    /**
     * Handle the TeacherSubject "created" event.
     * Auto-create JournalEntry records for each group assigned
     */
    public function created(TeacherSubject $teacherSubject): void
    {
        $this->createJournalEntries($teacherSubject);
    }

    /**
     * Handle the TeacherSubject "updated" event.
     * If group_ids changed, create new JournalEntry records
     */
    public function updated(TeacherSubject $teacherSubject): void
    {
        // Check if group_ids were changed
        if ($teacherSubject->isDirty('group_ids')) {
            $this->createJournalEntries($teacherSubject);
        }
    }

    /**
     * Get Teacher ID from Employee ID
     * TeacherSubject.teacher_id is Employee ID, but JournalEntry.teacher_id references teachers table
     */
    protected function getTeacherId(int $employeeId): ?int
    {
        $employee = Employee::find($employeeId);
        if (!$employee) {
            Log::warning("TeacherSubjectObserver: Employee #{$employeeId} not found");
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

            Log::info("TeacherSubjectObserver: Created Teacher #{$teacher->id} for Employee #{$employeeId}");
            return $teacher->id;
        }

        // If employee has no user_id, we can't create a proper teacher record
        Log::warning("TeacherSubjectObserver: Employee #{$employeeId} has no user_id, cannot create Teacher record");
        return null;
    }

    /**
     * Create JournalEntry records for teacher-subject assignment
     */
    protected function createJournalEntries(TeacherSubject $teacherSubject): void
    {
        try {
            $groupIds = $teacherSubject->group_ids;

            if (empty($groupIds) || !is_array($groupIds)) {
                Log::info('TeacherSubjectObserver: No group_ids for TeacherSubject', [
                    'teacher_subject_id' => $teacherSubject->id
                ]);
                return;
            }

            // Get Teacher ID from Employee ID
            $teacherId = $this->getTeacherId($teacherSubject->teacher_id);
            if (!$teacherId) {
                Log::warning("TeacherSubjectObserver: Could not get Teacher ID for TeacherSubject #{$teacherSubject->id}");
                return;
            }

            // Get academic year
            $academicYear = null;
            if ($teacherSubject->academic_year_id) {
                $academicYear = AcademicYear::find($teacherSubject->academic_year_id);
            }
            if (!$academicYear) {
                $academicYear = AcademicYear::where('is_current', true)->first();
            }

            if (!$academicYear) {
                Log::warning('TeacherSubjectObserver: No academic year found');
                return;
            }

            $semester = $teacherSubject->semester_id ?? (now()->month >= 9 ? 1 : 2);
            $createdCount = 0;

            foreach ($groupIds as $groupId) {
                // Try to find group in 'groups' table first
                $group = DB::table('groups')->where('id', $groupId)->first();
                $actualGroupId = $groupId;

                // If not found in 'groups', check if it's a StudentGroup
                if (!$group) {
                    $studentGroup = StudentGroup::find($groupId);
                    if ($studentGroup) {
                        // Create a corresponding entry in 'groups' table or use StudentGroup id
                        $existingGroup = DB::table('groups')->where('name', $studentGroup->name)->first();

                        if ($existingGroup) {
                            $actualGroupId = $existingGroup->id;
                        } else {
                            // Create group in 'groups' table
                            $actualGroupId = DB::table('groups')->insertGetId([
                                'name' => $studentGroup->name,
                                'code' => $studentGroup->code ?? $studentGroup->name,
                                'department_id' => $studentGroup->faculty_id ?? 1,
                                'course' => $studentGroup->course ?? 1,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }
                    }
                }

                // Check if journal entry already exists
                $existingEntry = JournalEntry::where('group_id', $actualGroupId)
                    ->where('subject_id', $teacherSubject->subject_id)
                    ->where('teacher_id', $teacherId)
                    ->where('academic_year_id', $academicYear->id)
                    ->where('semester_id', $semester)
                    ->first();

                if ($existingEntry) {
                    Log::info('TeacherSubjectObserver: JournalEntry already exists', [
                        'journal_entry_id' => $existingEntry->id
                    ]);
                    continue;
                }

                // Create journal entry
                $journalEntry = JournalEntry::create([
                    'subject_id' => $teacherSubject->subject_id,
                    'group_id' => $actualGroupId,
                    'teacher_id' => $teacherId,
                    'academic_year_id' => $academicYear->id,
                    'semester_id' => $semester,
                ]);

                $createdCount++;

                Log::info('TeacherSubjectObserver: JournalEntry created', [
                    'journal_entry_id' => $journalEntry->id,
                    'teacher_subject_id' => $teacherSubject->id,
                    'group_id' => $actualGroupId,
                    'subject_id' => $teacherSubject->subject_id,
                    'teacher_id' => $teacherId,
                ]);
            }

            Log::info("TeacherSubjectObserver: Created {$createdCount} journal entries for TeacherSubject #{$teacherSubject->id}");

        } catch (\Exception $e) {
            Log::error('TeacherSubjectObserver: Failed to create journal entries', [
                'teacher_subject_id' => $teacherSubject->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}
