<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\TeacherSubject;
use App\Models\Group;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GroupController extends Controller
{
    /**
     * Get teacher/employee ID for current user
     */
    private function getTeacherId()
    {
        $user = Auth::user();
        $teacher = Teacher::where('user_id', $user->id)->first();
        $employee = Employee::where('user_id', $user->id)->first();

        return [
            'teacher_id' => $teacher ? $teacher->id : null,
            'employee_id' => $employee ? $employee->id : null,
            'user_id' => $user->id,
            'teacher' => $teacher,
            'employee' => $employee,
        ];
    }

    /**
     * Display list of teacher's groups
     */
    public function index()
    {
        $ids = $this->getTeacherId();

        if (!$ids['teacher_id'] && !$ids['employee_id']) {
            return redirect()->route('teacher.dashboard')
                ->with('error', 'O\'qituvchi profili topilmadi.');
        }

        $teacherId = $ids['employee_id'] ?? $ids['teacher_id'];

        // Get all groups from TeacherSubject
        $teacherSubjects = TeacherSubject::with('subject')
            ->where('teacher_id', $teacherId)
            ->active()
            ->get();

        $groupsData = collect();

        foreach ($teacherSubjects as $ts) {
            $groupIds = $ts->group_ids ?? [];
            if (!is_array($groupIds)) {
                $groupIds = json_decode($groupIds, true) ?? [];
            }

            foreach ($groupIds as $gid) {
                // Skip if already added
                if ($groupsData->where('id', $gid)->isNotEmpty()) continue;

                $group = Group::with(['specialty', 'faculty', 'students'])->find($gid);
                if (!$group) continue;

                $studentCount = $group->students()->where('status', 'active')->count();

                // Get subjects for this group taught by this teacher
                $subjects = $teacherSubjects->filter(function($t) use ($gid) {
                    $ids = $t->group_ids ?? [];
                    if (!is_array($ids)) {
                        $ids = json_decode($ids, true) ?? [];
                    }
                    return in_array($gid, $ids);
                })->pluck('subject');

                $groupsData->push([
                    'id' => $group->id,
                    'group' => $group,
                    'subjects' => $subjects,
                    'student_count' => $studentCount,
                ]);
            }
        }

        return view('teacher.groups.index', [
            'groupsData' => $groupsData,
            'teacher' => $ids['teacher'] ?? $ids['employee'],
        ]);
    }

    /**
     * Display all students from teacher's groups
     */
    public function students()
    {
        $ids = $this->getTeacherId();
        $teacherId = $ids['employee_id'] ?? $ids['teacher_id'];

        // Get all groups from TeacherSubject
        $teacherSubjects = TeacherSubject::with('subject')
            ->where('teacher_id', $teacherId)
            ->active()
            ->get();

        $allGroupIds = collect();
        foreach ($teacherSubjects as $ts) {
            $groupIds = $ts->group_ids ?? [];
            if (!is_array($groupIds)) {
                $groupIds = json_decode($groupIds, true) ?? [];
            }
            $allGroupIds = $allGroupIds->merge($groupIds);
        }
        $allGroupIds = $allGroupIds->unique();

        $students = Student::with(['user', 'group.specialty'])
            ->whereIn('group_id', $allGroupIds)
            ->where('status', 'active')
            ->orderBy('group_id')
            ->paginate(50);

        return view('teacher.groups.students', [
            'students' => $students,
            'teacher' => $ids['teacher'] ?? $ids['employee'],
        ]);
    }

    /**
     * Messages page
     */
    public function messages()
    {
        $ids = $this->getTeacherId();
        $teacherId = $ids['employee_id'] ?? $ids['teacher_id'];

        // Get all groups
        $teacherSubjects = TeacherSubject::with('subject')
            ->where('teacher_id', $teacherId)
            ->active()
            ->get();

        $groups = collect();
        foreach ($teacherSubjects as $ts) {
            $groupIds = $ts->group_ids ?? [];
            if (!is_array($groupIds)) {
                $groupIds = json_decode($groupIds, true) ?? [];
            }
            foreach ($groupIds as $gid) {
                if ($groups->where('id', $gid)->isEmpty()) {
                    $group = Group::find($gid);
                    if ($group) $groups->push($group);
                }
            }
        }

        return view('teacher.groups.messages', [
            'groups' => $groups,
            'teacher' => $ids['teacher'] ?? $ids['employee'],
        ]);
    }

    /**
     * Send message to group
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'group_id' => 'required',
            'message' => 'required|string|max:1000',
        ]);

        // TODO: Implement message sending logic

        return redirect()->route('teacher.groups.messages')
            ->with('success', 'Xabar yuborildi!');
    }

    /**
     * Statistics page
     */
    public function statistics()
    {
        $ids = $this->getTeacherId();
        $teacherId = $ids['employee_id'] ?? $ids['teacher_id'];

        // Get all groups from TeacherSubject
        $teacherSubjects = TeacherSubject::with('subject')
            ->where('teacher_id', $teacherId)
            ->active()
            ->get();

        $allGroupIds = collect();
        foreach ($teacherSubjects as $ts) {
            $groupIds = $ts->group_ids ?? [];
            if (!is_array($groupIds)) {
                $groupIds = json_decode($groupIds, true) ?? [];
            }
            $allGroupIds = $allGroupIds->merge($groupIds);
        }
        $allGroupIds = $allGroupIds->unique();

        // Build statistics
        $totalGroups = $allGroupIds->count();
        $totalStudents = Student::whereIn('group_id', $allGroupIds)
            ->where('status', 'active')
            ->count();
        $totalSubjects = $teacherSubjects->pluck('subject_id')->unique()->count();

        $stats = [
            'total_groups' => $totalGroups,
            'total_students' => $totalStudents,
            'total_subjects' => $totalSubjects,
        ];

        return view('teacher.groups.statistics', [
            'stats' => $stats,
            'teacher' => $ids['teacher'] ?? $ids['employee'],
        ]);
    }

    /**
     * Show specific group
     */
    public function show($id)
    {
        $ids = $this->getTeacherId();
        $teacherId = $ids['employee_id'] ?? $ids['teacher_id'];

        $group = Group::with(['specialty', 'faculty', 'students.user'])->findOrFail($id);

        // Verify teacher has access to this group
        $hasAccess = TeacherSubject::where('teacher_id', $teacherId)
            ->active()
            ->get()
            ->contains(function($ts) use ($id) {
                $groupIds = $ts->group_ids ?? [];
                if (!is_array($groupIds)) {
                    $groupIds = json_decode($groupIds, true) ?? [];
                }
                return in_array($id, $groupIds);
            });

        if (!$hasAccess) {
            return redirect()->route('teacher.groups.index')
                ->with('error', 'Bu guruhga kirish huquqi yo\'q.');
        }

        $students = $group->students()
            ->where('status', 'active')
            ->with('user')
            ->orderBy('student_id')
            ->get();

        return view('teacher.groups.show', [
            'group' => $group,
            'students' => $students,
            'teacher' => $ids['teacher'] ?? $ids['employee'],
        ]);
    }

    /**
     * Show students of specific group
     */
    public function groupStudents($id)
    {
        return $this->show($id);
    }
}
