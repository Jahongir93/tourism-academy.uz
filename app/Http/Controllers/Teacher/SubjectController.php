<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Teacher;
use App\Models\Employee;
use App\Models\TeacherSubject;
use App\Models\Group;
use App\Models\Subject;

class SubjectController extends Controller
{
    /**
     * Display teacher's subjects
     */
    public function index()
    {
        $user = Auth::user();
        $teacher = Teacher::where('user_id', $user->id)->first();
        $employee = Employee::where('user_id', $user->id)->first();

        // Get teacher ID (can be from Teacher or Employee table)
        $teacherId = $teacher ? $teacher->id : null;
        $employeeId = $employee ? $employee->id : null;

        if (!$teacherId && !$employeeId) {
            return redirect()->route('teacher.dashboard')->with('error', 'O\'qituvchi profili topilmadi.');
        }

        // Get teacher's subjects with related data from TeacherSubject table
        $teacherSubjects = TeacherSubject::with(['subject'])
            ->where(function($q) use ($teacherId, $employeeId) {
                if ($employeeId) {
                    $q->where('teacher_id', $employeeId);
                }
                if ($teacherId) {
                    $q->orWhere('teacher_id', $teacherId);
                }
            })
            ->active()
            ->get();

        // Group by subject and expand group_ids
        $subjects = $teacherSubjects->groupBy('subject_id')->map(function ($items, $subjectId) {
            $firstItem = $items->first();

            // Collect all groups from all teacher_subject entries for this subject
            $allGroups = collect();
            $totalStudents = 0;

            foreach ($items as $item) {
                $groupIds = $item->group_ids ?? [];
                if (!is_array($groupIds)) {
                    $groupIds = json_decode($groupIds, true) ?? [];
                }

                foreach ($groupIds as $groupId) {
                    $group = Group::find($groupId);
                    if ($group) {
                        $studentsCount = $group->students()->where('status', 'active')->count();
                        $totalStudents += $studentsCount;

                        $allGroups->push([
                            'id' => $item->id,
                            'group' => $group,
                            'semester' => $item->semester_id,
                            'room' => null,
                            'students_count' => $studentsCount,
                            'academic_year' => $item->academic_year_id,
                        ]);
                    }
                }
            }

            return [
                'subject' => $firstItem->subject,
                'groups' => $allGroups,
                'total_students' => $totalStudents,
                'groups_count' => $allGroups->count(),
            ];
        });

        // Statistics
        $totalSubjects = $subjects->count();
        $totalGroups = $subjects->sum('groups_count');
        $totalStudents = $subjects->sum('total_students');

        return view('teacher.subjects.index', compact(
            'teacher',
            'subjects',
            'totalSubjects',
            'totalGroups',
            'totalStudents'
        ));
    }

    /**
     * Show subject details with specific group
     */
    public function show($id, Request $request)
    {
        $user = Auth::user();
        $teacher = Teacher::where('user_id', $user->id)->first();
        $employee = Employee::where('user_id', $user->id)->first();

        $teacherId = $teacher ? $teacher->id : null;
        $employeeId = $employee ? $employee->id : null;

        // Get teacher subject
        $teacherSubject = TeacherSubject::with(['subject'])
            ->where('id', $id)
            ->where(function($q) use ($teacherId, $employeeId) {
                if ($employeeId) {
                    $q->where('teacher_id', $employeeId);
                }
                if ($teacherId) {
                    $q->orWhere('teacher_id', $teacherId);
                }
            })
            ->firstOrFail();

        // Get group_id from request or use first group
        $groupId = $request->get('group_id');
        $groupIds = $teacherSubject->group_ids ?? [];
        if (!is_array($groupIds)) {
            $groupIds = json_decode($groupIds, true) ?? [];
        }

        if (!$groupId && !empty($groupIds)) {
            $groupId = $groupIds[0];
        }

        $group = Group::with(['students.user', 'specialty'])->find($groupId);

        if (!$group) {
            return redirect()->route('teacher.subjects.index')->with('error', 'Guruh topilmadi.');
        }

        // Create a groupSubject-like object for view compatibility
        $groupSubject = (object)[
            'id' => $teacherSubject->id,
            'subject' => $teacherSubject->subject,
            'group' => $group,
            'semester' => $teacherSubject->semester_id,
            'teacher_id' => $teacherSubject->teacher_id,
            'subject_id' => $teacherSubject->subject_id,
            'group_id' => $group->id,
        ];

        // Get students with their grades
        $students = $group->students()
            ->where('status', 'active')
            ->with(['user', 'journalGrades' => function($query) use ($teacherSubject, $group) {
                $query->whereHas('journalEntry', function($q) use ($teacherSubject, $group) {
                    $q->where('subject_id', $teacherSubject->subject_id)
                      ->where('group_id', $group->id);
                });
            }])
            ->get();

        // Calculate statistics
        $studentsCount = $students->count();
        $maleCount = $students->where('gender', 'male')->count();
        $femaleCount = $students->where('gender', 'female')->count();

        // Get average grade
        $avgGrade = \App\Models\JournalGrade::whereHas('journalEntry', function($q) use ($teacherSubject, $group) {
            $q->where('subject_id', $teacherSubject->subject_id)
              ->where('group_id', $group->id)
              ->where('teacher_id', $teacherSubject->teacher_id);
        })->avg('score');

        return view('teacher.subjects.show', compact(
            'teacher',
            'groupSubject',
            'students',
            'studentsCount',
            'maleCount',
            'femaleCount',
            'avgGrade'
        ));
    }
}
