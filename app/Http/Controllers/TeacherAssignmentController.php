<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Employee;
use App\Models\JournalEntry;
use App\Models\TeacherSubject;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TeacherAssignmentController extends Controller
{
    /**
     * Convert Employee ID to Teacher ID
     * JournalEntry.teacher_id has a foreign key to teachers table
     */
    protected function getTeacherIdFromEmployee(int $employeeId): ?int
    {
        $employee = Employee::find($employeeId);
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

            Log::info("TeacherAssignmentController: Created Teacher #{$teacher->id} for Employee #{$employeeId}");
            return $teacher->id;
        }

        return null;
    }

    /**
     * Assign a teacher to a subject for a specific group
     */
    public function assignTeacher(Request $request, Group $group)
    {
        $validated = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'required|integer',
        ]);

        try {
            DB::beginTransaction();

            // Check if teacher exists in Teacher table first
            $inputId = $validated['teacher_id'];
            $teacher = Teacher::find($inputId);
            $teacherIdForJournal = $inputId;

            if (!$teacher) {
                // Not in Teacher table, check Employee table
                $employee = Employee::where('employee_type', 'teacher')->find($inputId);

                if (!$employee) {
                    return back()->with('error', 'O\'qituvchi topilmadi');
                }

                // Convert Employee ID to Teacher ID for JournalEntry
                $teacherIdForJournal = $this->getTeacherIdFromEmployee($inputId);

                if (!$teacherIdForJournal) {
                    return back()->with('error', 'O\'qituvchi foydalanuvchi hisobiga ega emas. Avval foydalanuvchi yarating.');
                }
            }

            $currentYear = AcademicYear::where('is_current', true)->first();
            if (!$currentYear) {
                // Fallback to latest academic year if none is marked current
                $currentYear = AcademicYear::orderBy('id', 'desc')->first();
            }
            if (!$currentYear) {
                return back()->with('error', 'O\'quv yili topilmadi. Avval o\'quv yilini yarating.');
            }

            // Calculate current semester based on course
            $currentSemester = ($group->course * 2) - 1;

            // Check if this subject is already assigned in JournalEntry
            $existing = JournalEntry::where('group_id', $group->id)
                ->where('subject_id', $validated['subject_id'])
                ->where('academic_year_id', $currentYear->id)
                ->where('semester_id', $currentSemester)
                ->first();

            if ($existing) {
                // Update existing entry
                $existing->update([
                    'teacher_id' => $teacherIdForJournal
                ]);
            } else {
                // Create new journal entry
                JournalEntry::create([
                    'group_id' => $group->id,
                    'subject_id' => $validated['subject_id'],
                    'teacher_id' => $teacherIdForJournal,
                    'academic_year_id' => $currentYear->id,
                    'semester_id' => $currentSemester,
                ]);
            }

            // Also create/update TeacherSubject for bidirectional sync
            // Note: TeacherSubject.teacher_id uses Employee ID (inputId)
            $existingTeacherSubject = TeacherSubject::where('teacher_id', $inputId)
                ->where('subject_id', $validated['subject_id'])
                ->where('academic_year_id', $currentYear->id)
                ->where('semester_id', $currentSemester)
                ->where('status', 'active')
                ->first();

            if ($existingTeacherSubject) {
                // Add group to existing assignment if not already present
                $groupIds = $existingTeacherSubject->group_ids ?? [];
                if (!in_array($group->id, $groupIds)) {
                    $groupIds[] = $group->id;
                    $existingTeacherSubject->update(['group_ids' => $groupIds]);
                }
            } else {
                // Create new TeacherSubject entry
                TeacherSubject::create([
                    'teacher_id' => $inputId,
                    'subject_id' => $validated['subject_id'],
                    'academic_year_id' => $currentYear->id,
                    'semester_id' => $currentSemester,
                    'group_ids' => [$group->id],
                    'lecture_hours' => 0,
                    'practice_hours' => 0,
                    'lab_hours' => 0,
                    'language' => 'uz',
                    'status' => 'active'
                ]);
            }

            DB::commit();

            return back()->with('success', 'O\'qituvchi muvaffaqiyatli biriktirildi');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Xatolik yuz berdi: ' . $e->getMessage());
        }
    }

    /**
     * Remove teacher assignment
     */
    public function removeTeacher(JournalEntry $entry)
    {
        try {
            $entry->delete();
            return back()->with('success', 'O\'qituvchi o\'chirildi');
        } catch (\Exception $e) {
            return back()->with('error', 'Xatolik yuz berdi: ' . $e->getMessage());
        }
    }

    /**
     * Get available subjects for a group (from curriculum)
     */
    public function getAvailableSubjects(Group $group)
    {
        // This will return subjects from the curriculum for the group's program
        $subjects = Subject::all();
        return response()->json($subjects);
    }

    /**
     * Get available teachers
     */
    public function getAvailableTeachers()
    {
        $teachers = Teacher::with('department')->get()->map(function($teacher) {
            return [
                'id' => $teacher->id,
                'name' => $teacher->full_name,
                'department' => $teacher->department->name ?? '-'
            ];
        });

        return response()->json($teachers);
    }
}
