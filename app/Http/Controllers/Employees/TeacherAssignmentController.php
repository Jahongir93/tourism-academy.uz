<?php

namespace App\Http\Controllers\Employees;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Teacher;
use App\Models\TeacherSubject;
use App\Models\TeacherGroup;
use App\Models\TeacherWorkload;
use App\Models\JournalEntry;
use App\Models\Subject;
use App\Models\Group;
use App\Models\Faculty;
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

            Log::info("Employees\TeacherAssignmentController: Created Teacher #{$teacher->id} for Employee #{$employeeId}");
            return $teacher->id;
        }

        return null;
    }
    public function assignSubjects($teacherId)
    {
        $teacher = Employee::where('employee_type', 'teacher')->findOrFail($teacherId);
        $subjects = Subject::all();
        $faculties = Faculty::with('departments')->get();
        $groups = Group::where('is_active', true)->orderBy('name')->get();
        $academicYears = \App\Models\AcademicYear::orderBy('id', 'desc')->get();

        $currentAssignments = TeacherSubject::where('teacher_id', $teacherId)
            ->where('status', 'active')
            ->with('subject')
            ->get();

        return view('employees.teachers.assign-subjects', compact(
            'teacher',
            'subjects',
            'faculties',
            'groups',
            'academicYears',
            'currentAssignments'
        ));
    }

    public function storeSubjectAssignment(Request $request, $teacherId)
    {
        $teacher = Employee::where('employee_type', 'teacher')->findOrFail($teacherId);

        $validated = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'academic_year_id' => 'required|integer',
            'semester_id' => 'required|integer|between:1,2',
            'group_ids' => 'required|array',
            'group_ids.*' => 'exists:groups,id',
            'lecture_hours' => 'required|integer|min:0',
            'practice_hours' => 'required|integer|min:0',
            'lab_hours' => 'required|integer|min:0',
            'language' => 'required|in:uz,ru,en'
        ]);

        DB::beginTransaction();
        try {
            // Ensure academic_year_id is a valid ID from academic_years table
            $academicYear = \App\Models\AcademicYear::find($validated['academic_year_id']);
            if (!$academicYear) {
                // Maybe it's a year value (like 2025), try to find by year
                $academicYear = \App\Models\AcademicYear::where('year', 'LIKE', $validated['academic_year_id'] . '%')->first();
                if (!$academicYear) {
                    $academicYear = \App\Models\AcademicYear::where('is_current', true)->first();
                }
                if (!$academicYear) {
                    throw new \Exception('O\'quv yili topilmadi');
                }
                $validated['academic_year_id'] = $academicYear->id;
            }

            // Check for conflicts
            $existingAssignment = TeacherSubject::where('teacher_id', $teacherId)
                ->where('subject_id', $validated['subject_id'])
                ->where('academic_year_id', $validated['academic_year_id'])
                ->where('semester_id', $validated['semester_id'])
                ->where('status', 'active')
                ->first();

            if ($existingAssignment) {
                throw new \Exception('Bu fan ushbu o\'qituvchiga allaqachon biriktirilgan!');
            }

            // Create assignment
            $validated['teacher_id'] = $teacherId;
            $assignment = TeacherSubject::create($validated);

            // Create JournalEntry for each group
            $this->createJournalEntries(
                $teacherId,
                $validated['subject_id'],
                $validated['group_ids'],
                $validated['academic_year_id'],
                $validated['semester_id']
            );

            // Update teacher workload
            $this->updateTeacherWorkload($teacherId, $validated['academic_year_id']);

            DB::commit();

            return redirect()->route('employees.teachers.subjects', $teacherId)
                ->with('success', 'Fan muvaffaqiyatli biriktirildi va jurnallar yaratildi!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Xatolik: ' . $e->getMessage());
        }
    }

    public function updateSubjectAssignment(Request $request, $teacherId, $assignmentId)
    {
        $teacher = Employee::where('employee_type', 'teacher')->findOrFail($teacherId);
        $assignment = TeacherSubject::where('teacher_id', $teacherId)->findOrFail($assignmentId);

        $validated = $request->validate([
            'group_ids' => 'required|array',
            'group_ids.*' => 'exists:groups,id',
            'lecture_hours' => 'required|integer|min:0',
            'practice_hours' => 'required|integer|min:0',
            'lab_hours' => 'required|integer|min:0',
            'language' => 'required|in:uz,ru,en',
            'status' => 'required|in:active,completed,cancelled'
        ]);

        DB::beginTransaction();
        try {
            $oldGroupIds = $assignment->group_ids ?? [];
            $newGroupIds = $validated['group_ids'];

            $assignment->update($validated);

            // Create journal entries for new groups
            $addedGroups = array_diff($newGroupIds, $oldGroupIds);
            if (!empty($addedGroups)) {
                $this->createJournalEntries(
                    $teacherId,
                    $assignment->subject_id,
                    $addedGroups,
                    $assignment->academic_year_id,
                    $assignment->semester_id
                );
            }

            // Update teacher workload
            $this->updateTeacherWorkload($teacherId, $assignment->academic_year_id);

            DB::commit();

            return redirect()->route('employees.teachers.subjects', $teacherId)
                ->with('success', 'Fan ma\'lumotlari yangilandi!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Xatolik: ' . $e->getMessage());
        }
    }

    public function removeSubjectAssignment($teacherId, $assignmentId)
    {
        $teacher = Employee::where('employee_type', 'teacher')->findOrFail($teacherId);
        $assignment = TeacherSubject::where('teacher_id', $teacherId)->findOrFail($assignmentId);

        DB::beginTransaction();
        try {
            $assignment->update(['status' => 'cancelled']);

            // Update teacher workload
            $this->updateTeacherWorkload($teacherId, $assignment->academic_year_id);

            DB::commit();

            return redirect()->route('employees.teachers.subjects', $teacherId)
                ->with('success', 'Fan biriktirilishi bekor qilindi!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Xatolik: ' . $e->getMessage());
        }
    }

    public function assignGroups($teacherId)
    {
        $teacher = Employee::where('employee_type', 'teacher')->findOrFail($teacherId);
        $groups = Group::with('faculty', 'department')->where('is_active', true)->orderBy('name')->get();
        
        $currentGroups = TeacherGroup::where('teacher_id', $teacherId)
            ->where('status', 'active')
            ->get();

        return view('employees.teachers.assign-groups', compact(
            'teacher',
            'groups',
            'currentGroups'
        ));
    }

    public function storeGroupAssignment(Request $request, $teacherId)
    {
        $teacher = Employee::where('employee_type', 'teacher')->findOrFail($teacherId);

        $validated = $request->validate([
            'group_id' => 'required|exists:groups,id',
            'academic_year_id' => 'required|integer',
            'role' => 'required|in:murabbiy,curator'
        ]);

        DB::beginTransaction();
        try {
            // Check if group already has a curator
            $existingCurator = TeacherGroup::where('group_id', $validated['group_id'])
                ->where('academic_year_id', $validated['academic_year_id'])
                ->where('status', 'active')
                ->first();

            if ($existingCurator) {
                throw new \Exception('Bu guruhga allaqachon murabbiy biriktirilgan!');
            }

            // Check teacher's group limit (max 3 groups)
            $teacherGroupCount = TeacherGroup::where('teacher_id', $teacherId)
                ->where('academic_year_id', $validated['academic_year_id'])
                ->where('status', 'active')
                ->count();

            if ($teacherGroupCount >= 3) {
                throw new \Exception('O\'qituvchi maksimal 3 ta guruhga murabbiy bo\'lishi mumkin!');
            }

            // Create assignment
            $validated['teacher_id'] = $teacherId;
            $validated['assigned_date'] = now();
            TeacherGroup::create($validated);

            DB::commit();

            return redirect()->route('employees.teachers.groups', $teacherId)
                ->with('success', 'Guruh muvaffaqiyatli biriktirildi!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Xatolik: ' . $e->getMessage());
        }
    }

    public function removeGroupAssignment($teacherId, $groupId)
    {
        $teacher = Employee::where('employee_type', 'teacher')->findOrFail($teacherId);
        $assignment = TeacherGroup::where('teacher_id', $teacherId)
            ->where('id', $groupId)
            ->firstOrFail();

        try {
            $assignment->update(['status' => 'inactive']);

            return redirect()->route('employees.teachers.groups', $teacherId)
                ->with('success', 'Guruh biriktirilishi bekor qilindi!');

        } catch (\Exception $e) {
            return back()->with('error', 'Xatolik: ' . $e->getMessage());
        }
    }

    public function workload($teacherId)
    {
        $teacher = Employee::where('employee_type', 'teacher')->findOrFail($teacherId);

        // Get current academic year from database
        $academicYear = \App\Models\AcademicYear::where('is_current', true)->first();
        if (!$academicYear) {
            $academicYear = \App\Models\AcademicYear::orderBy('id', 'desc')->first();
        }
        if (!$academicYear) {
            return back()->with('error', 'O\'quv yili topilmadi');
        }
        $currentYearId = $academicYear->id;

        $workload = TeacherWorkload::firstOrCreate(
            [
                'teacher_id' => $teacherId,
                'academic_year_id' => $currentYearId
            ],
            [
                'planned_hours' => 680,
                'status' => 'planned'
            ]
        );

        // Calculate current workload from assignments
        $this->updateTeacherWorkload($teacherId, $currentYearId);
        $workload->refresh();

        // Get subject assignments
        $assignments = TeacherSubject::where('teacher_id', $teacherId)
            ->where('academic_year_id', $currentYearId)
            ->where('status', 'active')
            ->with('subject')
            ->get();

        return view('employees.teachers.workload', compact(
            'teacher',
            'workload',
            'assignments'
        ));
    }

    /**
     * Create JournalEntry records for each group
     * Note: $employeeId is the Employee ID, we need to convert to Teacher ID
     */
    private function createJournalEntries($employeeId, $subjectId, $groupIds, $academicYearId, $semesterId)
    {
        // Convert Employee ID to Teacher ID (JournalEntry.teacher_id references teachers table)
        $teacherId = $this->getTeacherIdFromEmployee($employeeId);

        if (!$teacherId) {
            Log::warning("Employees\TeacherAssignmentController: Cannot create journal entries - Employee #{$employeeId} has no user account");
            return;
        }

        foreach ($groupIds as $groupId) {
            // Check if entry already exists
            $existingEntry = JournalEntry::where('teacher_id', $teacherId)
                ->where('subject_id', $subjectId)
                ->where('group_id', $groupId)
                ->where('academic_year_id', $academicYearId)
                ->where('semester_id', $semesterId)
                ->first();

            if (!$existingEntry) {
                JournalEntry::create([
                    'teacher_id' => $teacherId,
                    'subject_id' => $subjectId,
                    'group_id' => $groupId,
                    'academic_year_id' => $academicYearId,
                    'semester_id' => $semesterId
                ]);
            }
        }
    }

    private function updateTeacherWorkload($teacherId, $academicYearId)
    {
        $workload = TeacherWorkload::firstOrCreate(
            [
                'teacher_id' => $teacherId,
                'academic_year_id' => $academicYearId
            ],
            [
                'planned_hours' => 680,
                'status' => 'planned'
            ]
        );

        // Calculate teaching hours from subject assignments
        $teachingHours = TeacherSubject::where('teacher_id', $teacherId)
            ->where('academic_year_id', $academicYearId)
            ->where('status', 'active')
            ->sum('total_hours');

        // Calculate educational hours from group assignments
        $groupCount = TeacherGroup::where('teacher_id', $teacherId)
            ->where('academic_year_id', $academicYearId)
            ->where('status', 'active')
            ->count();
        $educationalHours = $groupCount * 50; // 50 hours per group

        // Update workload
        $workload->update([
            'teaching_hours' => $teachingHours,
            'educational_hours' => $educationalHours,
            'status' => 'in_progress'
        ]);

        return $workload;
    }
}