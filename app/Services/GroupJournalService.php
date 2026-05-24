<?php

namespace App\Services;

use App\Models\Group;
use App\Models\JournalEntry;
use App\Models\AcademicYear;
use App\Models\Specialty;
use App\Models\EducationalProgram;
use App\Models\Curriculum;
use App\Models\Teacher;
use App\Models\Employee;
use App\Models\Subject;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GroupJournalService
{
    /**
     * Convert Employee ID to Teacher ID if needed
     * Ensures we have a valid Teacher ID for JournalEntry
     */
    protected function ensureTeacherId(int $id): ?int
    {
        // First check if this ID exists in teachers table
        $teacher = Teacher::find($id);
        if ($teacher) {
            return $teacher->id;
        }

        // If not in teachers, check if it's an Employee ID
        $employee = Employee::find($id);
        if (!$employee) {
            return null;
        }

        // If employee has user_id, find or create Teacher
        if ($employee->user_id) {
            $teacher = Teacher::where('user_id', $employee->user_id)->first();
            if ($teacher) {
                return $teacher->id;
            }

            // Create Teacher record
            $teacher = Teacher::create([
                'user_id' => $employee->user_id,
                'first_name' => $employee->first_name ?? 'Unknown',
                'last_name' => $employee->last_name ?? 'Unknown',
                'middle_name' => $employee->middle_name,
                'email' => $employee->email ?? ($employee->user ? $employee->user->email : null),
                'phone' => $employee->phone,
                'position' => $employee->position ?? "O'qituvchi",
                'department_id' => $employee->department_id,
            ]);

            Log::info("GroupJournalService: Created Teacher #{$teacher->id} for Employee #{$id}");
            return $teacher->id;
        }

        Log::warning("GroupJournalService: Cannot convert Employee #{$id} to Teacher - no user_id");
        return null;
    }
    /**
     * Create journal entries for a group
     * This method is called automatically when a group is created
     */
    public function createJournalEntriesForGroup(Group $group): array
    {
        $created = [];
        $errors = [];

        try {
            DB::beginTransaction();

            // Get current academic year
            $currentAcademicYear = AcademicYear::where('is_current', true)->first();

            if (!$currentAcademicYear) {
                $errors[] = 'Joriy o\'quv yili topilmadi';
                DB::rollBack();
                return ['success' => false, 'errors' => $errors];
            }

            // Calculate current semester based on course
            // Course 1 = Semesters 1-2, Course 2 = Semesters 3-4, etc.
            $currentSemester = ($group->course * 2) - 1; // Start with odd semester

            // Get specialty for this group's department
            $specialty = Specialty::where('department_id', $group->department_id)->first();

            if (!$specialty) {
                $errors[] = "Kafedra uchun mutaxassislik topilmadi";
                DB::rollBack();
                return ['success' => false, 'errors' => $errors];
            }

            // Get educational program
            $program = EducationalProgram::where('code', $specialty->code)->first();

            if (!$program) {
                $errors[] = "Ta'lim yo'nalishi topilmadi: {$specialty->code}";
                DB::rollBack();
                return ['success' => false, 'errors' => $errors];
            }

            // Get subjects for current semester from curriculum
            $curriculumSubjects = Curriculum::where('program_id', $program->id)
                ->where('semester_number', $currentSemester)
                ->where('academic_year', $currentAcademicYear->name)
                ->with('subject')
                ->get();

            if ($curriculumSubjects->isEmpty()) {
                $errors[] = "Semestr {$currentSemester} uchun fanlar topilmadi";
                DB::rollBack();
                return ['success' => false, 'errors' => $errors];
            }

            // Create journal entries for each subject
            foreach ($curriculumSubjects as $curriculumItem) {
                // Find suitable teacher for this subject
                $teacher = $this->findTeacherForSubject($curriculumItem->subject_id, $group->department_id);

                if (!$teacher) {
                    $errors[] = "Fan uchun o'qituvchi topilmadi: {$curriculumItem->subject->name_uz}";
                    continue;
                }

                // Check if journal entry already exists
                $existingEntry = JournalEntry::where('group_id', $group->id)
                    ->where('subject_id', $curriculumItem->subject_id)
                    ->where('academic_year_id', $currentAcademicYear->id)
                    ->where('semester_id', $currentSemester)
                    ->first();

                if ($existingEntry) {
                    continue; // Skip if already exists
                }

                // Create journal entry
                $journalEntry = JournalEntry::create([
                    'subject_id' => $curriculumItem->subject_id,
                    'group_id' => $group->id,
                    'teacher_id' => $teacher->id,
                    'academic_year_id' => $currentAcademicYear->id,
                    'semester_id' => $currentSemester,
                ]);

                $created[] = [
                    'subject' => $curriculumItem->subject->name_uz,
                    'teacher' => $teacher->full_name,
                    'journal_id' => $journalEntry->id,
                ];
            }

            DB::commit();

            return [
                'success' => true,
                'created' => $created,
                'errors' => $errors,
                'summary' => [
                    'group' => $group->name,
                    'semester' => $currentSemester,
                    'academic_year' => $currentAcademicYear->name,
                    'journals_created' => count($created),
                ]
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Group journal creation failed', [
                'group_id' => $group->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'errors' => ['Xatolik yuz berdi: ' . $e->getMessage()]
            ];
        }
    }

    /**
     * Find a suitable teacher for a subject
     * Priority: 1) Teacher with subject assignment 2) Teacher from same department 3) Any teacher from department
     */
    private function findTeacherForSubject($subjectId, $departmentId): ?Teacher
    {
        // First, try to find teacher with subject assignment (if teacher_subjects table exists)
        $teacher = Teacher::whereHas('subjects', function($query) use ($subjectId) {
            $query->where('subject_id', $subjectId);
        })->first();

        if ($teacher) {
            return $teacher;
        }

        // Second, try to find teacher from the same department who teaches this subject
        $subject = Subject::find($subjectId);
        if ($subject) {
            $teacher = Teacher::where('department_id', $subject->department_id)->first();
            if ($teacher) {
                return $teacher;
            }
        }

        // Last resort: any teacher from the group's department
        return Teacher::where('department_id', $departmentId)->first();
    }

    /**
     * Assign a specific teacher to a subject for a group
     * Note: $teacherId can be either a Teacher ID or Employee ID - we convert as needed
     */
    public function assignTeacherToSubject(Group $group, $subjectId, $teacherId, $academicYearId = null, $semesterId = null): array
    {
        try {
            // Ensure we have a valid Teacher ID (convert from Employee ID if needed)
            $validTeacherId = $this->ensureTeacherId($teacherId);
            if (!$validTeacherId) {
                return ['success' => false, 'error' => 'O\'qituvchi topilmadi yoki foydalanuvchi hisobiga ega emas'];
            }

            $academicYear = $academicYearId
                ? AcademicYear::find($academicYearId)
                : AcademicYear::where('is_current', true)->first();

            if (!$academicYear) {
                return ['success' => false, 'error' => 'O\'quv yili topilmadi'];
            }

            $semester = $semesterId ?? (($group->course * 2) - 1);

            // Check if entry already exists
            $existing = JournalEntry::where('group_id', $group->id)
                ->where('subject_id', $subjectId)
                ->where('academic_year_id', $academicYear->id)
                ->where('semester_id', $semester)
                ->first();

            if ($existing) {
                // Update existing entry
                $existing->update(['teacher_id' => $validTeacherId]);
                return [
                    'success' => true,
                    'action' => 'updated',
                    'journal_entry' => $existing
                ];
            }

            // Create new entry
            $journalEntry = JournalEntry::create([
                'subject_id' => $subjectId,
                'group_id' => $group->id,
                'teacher_id' => $validTeacherId,
                'academic_year_id' => $academicYear->id,
                'semester_id' => $semester,
            ]);

            return [
                'success' => true,
                'action' => 'created',
                'journal_entry' => $journalEntry
            ];

        } catch (\Exception $e) {
            Log::error('Teacher assignment failed', [
                'group_id' => $group->id,
                'subject_id' => $subjectId,
                'teacher_id' => $teacherId,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get all journal entries for a group with details
     */
    public function getGroupJournals(Group $group, $academicYearId = null, $semesterId = null)
    {
        $query = JournalEntry::where('group_id', $group->id)
            ->with(['subject', 'teacher', 'academicYear']);

        if ($academicYearId) {
            $query->where('academic_year_id', $academicYearId);
        }

        if ($semesterId) {
            $query->where('semester_id', $semesterId);
        }

        return $query->get()->map(function($entry) {
            return [
                'id' => $entry->id,
                'subject' => [
                    'id' => $entry->subject->id,
                    'name' => $entry->subject->name_uz,
                    'code' => $entry->subject->code,
                    'credits' => $entry->subject->credits,
                ],
                'teacher' => [
                    'id' => $entry->teacher->id,
                    'name' => $entry->teacher->full_name,
                    'position' => $entry->teacher->position,
                ],
                'academic_year' => $entry->academicYear->name,
                'semester' => $entry->semester_id,
            ];
        });
    }

    /**
     * Sync all groups' journal entries for current academic year
     */
    public function syncAllGroupJournals(): array
    {
        $results = [
            'success' => 0,
            'failed' => 0,
            'skipped' => 0,
            'details' => []
        ];

        $groups = Group::all();

        foreach ($groups as $group) {
            $result = $this->createJournalEntriesForGroup($group);

            if ($result['success']) {
                $results['success']++;
                $results['details'][] = [
                    'group' => $group->name,
                    'status' => 'success',
                    'journals_created' => count($result['created'])
                ];
            } else {
                $results['failed']++;
                $results['details'][] = [
                    'group' => $group->name,
                    'status' => 'failed',
                    'errors' => $result['errors']
                ];
            }
        }

        return $results;
    }
}
