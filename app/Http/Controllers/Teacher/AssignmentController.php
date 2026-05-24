<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\TeacherSubject;
use App\Models\Group;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AssignmentController extends Controller
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
     * Display list of all assignments
     */
    public function index(Request $request)
    {
        $ids = $this->getTeacherId();

        if (!$ids['teacher_id'] && !$ids['employee_id']) {
            return redirect()->route('teacher.dashboard')
                ->with('error', 'O\'qituvchi profili topilmadi. Admin bilan bog\'laning.');
        }

        $teacherId = $ids['teacher_id'] ?? $ids['employee_id'];

        $query = Assignment::with(['subject'])
            ->where('teacher_id', $teacherId);

        // Filter by status
        if ($request->has('status')) {
            if ($request->status === 'active') {
                $query->where('deadline', '>=', now());
            } elseif ($request->status === 'expired') {
                $query->where('deadline', '<', now());
            }
        }

        // Filter by subject
        if ($request->has('subject_id') && $request->subject_id != '') {
            $query->where('subject_id', $request->subject_id);
        }

        $assignments = $query->latest()->paginate(15);

        // Get assignments with submission statistics
        $assignmentsData = $assignments->map(function($assignment) {
            $totalSubmissions = AssignmentSubmission::where('assignment_id', $assignment->id)->count();
            $pendingSubmissions = AssignmentSubmission::where('assignment_id', $assignment->id)
                ->where('status', 'submitted')
                ->whereNull('score')
                ->count();
            $gradedSubmissions = AssignmentSubmission::where('assignment_id', $assignment->id)
                ->whereNotNull('score')
                ->count();

            // Get group IDs from JSON
            $groupIds = is_string($assignment->group_ids)
                ? json_decode($assignment->group_ids, true)
                : $assignment->group_ids;

            $totalStudents = 0;
            if (is_array($groupIds) && count($groupIds) > 0) {
                $totalStudents = \App\Models\Student::whereIn('group_id', $groupIds)
                    ->where('status', 'active')
                    ->count();
            }

            $isExpired = $assignment->deadline < now();

            return [
                'assignment' => $assignment,
                'total_submissions' => $totalSubmissions,
                'pending_submissions' => $pendingSubmissions,
                'graded_submissions' => $gradedSubmissions,
                'total_students' => $totalStudents,
                'submission_rate' => $totalStudents > 0 ? round(($totalSubmissions / $totalStudents) * 100, 1) : 0,
                'is_expired' => $isExpired,
            ];
        });

        // Get teacher's subjects from TeacherSubject
        $teacherSubjects = TeacherSubject::with('subject')
            ->where('teacher_id', $teacherId)
            ->active()
            ->get();
        $subjects = $teacherSubjects->pluck('subject')->unique('id')->filter();

        return view('teacher.assignments.index', [
            'assignmentsData' => $assignmentsData,
            'assignments' => $assignments,
            'subjects' => $subjects,
            'teacher' => $ids['teacher'] ?? $ids['employee'],
        ]);
    }

    /**
     * Show form to create new assignment
     */
    public function create()
    {
        $ids = $this->getTeacherId();
        $teacherId = $ids['teacher_id'] ?? $ids['employee_id'];

        // Get teacher's subjects with groups from TeacherSubject
        $teacherSubjects = TeacherSubject::with('subject')
            ->where('teacher_id', $teacherId)
            ->active()
            ->get();

        // Build subjects with groups
        $subjects = $teacherSubjects->groupBy('subject_id')->map(function($items) {
            $firstItem = $items->first();

            // Collect all groups from group_ids
            $allGroups = collect();
            foreach ($items as $item) {
                $groupIds = $item->group_ids ?? [];
                if (!is_array($groupIds)) {
                    $groupIds = json_decode($groupIds, true) ?? [];
                }
                foreach ($groupIds as $gid) {
                    $group = Group::find($gid);
                    if ($group) {
                        $allGroups->push($group);
                    }
                }
            }

            return [
                'subject' => $firstItem->subject,
                'groups' => $allGroups->unique('id')->values(),
            ];
        });

        return view('teacher.assignments.create', [
            'subjects' => $subjects,
            'teacher' => $ids['teacher'] ?? $ids['employee'],
        ]);
    }

    /**
     * Store new assignment
     */
    public function store(Request $request)
    {
        $ids = $this->getTeacherId();
        // Use teacher_id first (foreign key references teachers table)
        $teacherId = $ids['teacher_id'] ?? $ids['employee_id'];

        if (!$teacherId) {
            return redirect()->back()
                ->with('error', 'O\'qituvchi profili topilmadi. Admin bilan bog\'laning.');
        }

        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'deadline' => 'required|date|after:now',
            'max_score' => 'required|numeric|min:1|max:100',
            'group_ids' => 'required|array|min:1',
            'group_ids.*' => 'exists:groups,id',
            'file' => 'nullable|file|max:10240', // 10MB max
        ]);

        $data = [
            'teacher_id' => $teacherId,
            'subject_id' => $request->subject_id,
            'title' => $request->title,
            'description' => $request->description,
            'deadline' => $request->deadline,
            'max_score' => $request->max_score,
            'group_ids' => json_encode($request->group_ids),
        ];

        // Handle file upload
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('assignments', $filename, 'public');
            $data['file_path'] = $path;
        }

        Assignment::create($data);

        return redirect()
            ->route('teacher.assignments.index')
            ->with('success', 'Topshiriq muvaffaqiyatli yaratildi!');
    }

    /**
     * Show assignment details and submissions
     */
    public function show($id)
    {
        $ids = $this->getTeacherId();
        $teacherId = $ids['teacher_id'] ?? $ids['employee_id'];

        $assignment = Assignment::with(['subject'])
            ->where('id', $id)
            ->where('teacher_id', $teacherId)
            ->firstOrFail();

        // Get submissions with student info
        $submissions = AssignmentSubmission::with(['student.user', 'student.group'])
            ->where('assignment_id', $assignment->id)
            ->latest('submitted_at')
            ->get();

        // Get group IDs from JSON
        $groupIds = is_string($assignment->group_ids)
            ? json_decode($assignment->group_ids, true)
            : $assignment->group_ids;

        // Get all students who should submit
        $allStudents = \App\Models\Student::with(['user', 'group'])
            ->whereIn('group_id', $groupIds)
            ->where('status', 'active')
            ->get();

        // Find students who haven't submitted
        $submittedStudentIds = $submissions->pluck('student_id')->toArray();
        $notSubmittedStudents = $allStudents->filter(function($student) use ($submittedStudentIds) {
            return !in_array($student->id, $submittedStudentIds);
        });

        // Statistics
        $statistics = [
            'total_students' => $allStudents->count(),
            'submitted' => $submissions->where('status', 'submitted')->count(),
            'graded' => $submissions->whereNotNull('score')->count(),
            'pending' => $submissions->where('status', 'submitted')->whereNull('score')->count(),
            'not_submitted' => $notSubmittedStudents->count(),
            'avg_score' => $submissions->whereNotNull('score')->avg('score'),
            'submission_rate' => $allStudents->count() > 0 ? round(($submissions->count() / $allStudents->count()) * 100, 1) : 0,
        ];

        return view('teacher.assignments.show', [
            'assignment' => $assignment,
            'submissions' => $submissions,
            'notSubmittedStudents' => $notSubmittedStudents,
            'statistics' => $statistics,
            'teacher' => $ids['teacher'] ?? $ids['employee'],
        ]);
    }

    /**
     * Show form to grade a submission
     */
    public function gradeSubmission($submissionId)
    {
        $ids = $this->getTeacherId();
        $teacherId = $ids['teacher_id'] ?? $ids['employee_id'];

        $submission = AssignmentSubmission::with(['assignment', 'student.user'])
            ->whereHas('assignment', function($q) use ($teacherId) {
                $q->where('teacher_id', $teacherId);
            })
            ->findOrFail($submissionId);

        return view('teacher.assignments.grade', [
            'submission' => $submission,
            'teacher' => $ids['teacher'] ?? $ids['employee'],
        ]);
    }

    /**
     * Store grade for submission
     */
    public function storeGrade(Request $request, $submissionId)
    {
        $ids = $this->getTeacherId();
        $teacherId = $ids['teacher_id'] ?? $ids['employee_id'];

        $submission = AssignmentSubmission::whereHas('assignment', function($q) use ($teacherId) {
            $q->where('teacher_id', $teacherId);
        })->findOrFail($submissionId);

        $request->validate([
            'score' => 'required|numeric|min:0|max:' . $submission->assignment->max_score,
            'feedback' => 'nullable|string',
        ]);

        $submission->update([
            'score' => $request->score,
            'feedback' => $request->feedback,
            'graded_at' => now(),
        ]);

        return redirect()
            ->route('teacher.assignments.show', $submission->assignment_id)
            ->with('success', 'Baho muvaffaqiyatli qo\'yildi!');
    }

    /**
     * Edit assignment
     */
    public function edit($id)
    {
        $ids = $this->getTeacherId();
        $teacherId = $ids['teacher_id'] ?? $ids['employee_id'];

        $assignment = Assignment::where('id', $id)
            ->where('teacher_id', $teacherId)
            ->firstOrFail();

        // Get teacher's subjects with groups from TeacherSubject
        $teacherSubjects = TeacherSubject::with('subject')
            ->where('teacher_id', $teacherId)
            ->active()
            ->get();

        // Build subjects with groups
        $subjects = $teacherSubjects->groupBy('subject_id')->map(function($items) {
            $firstItem = $items->first();

            // Collect all groups from group_ids
            $allGroups = collect();
            foreach ($items as $item) {
                $groupIds = $item->group_ids ?? [];
                if (!is_array($groupIds)) {
                    $groupIds = json_decode($groupIds, true) ?? [];
                }
                foreach ($groupIds as $gid) {
                    $group = Group::find($gid);
                    if ($group) {
                        $allGroups->push($group);
                    }
                }
            }

            return [
                'subject' => $firstItem->subject,
                'groups' => $allGroups->unique('id')->values(),
            ];
        });

        // Decode group_ids
        $assignment->group_ids = is_string($assignment->group_ids)
            ? json_decode($assignment->group_ids, true)
            : $assignment->group_ids;

        return view('teacher.assignments.edit', [
            'assignment' => $assignment,
            'subjects' => $subjects,
            'teacher' => $ids['teacher'] ?? $ids['employee'],
        ]);
    }

    /**
     * Update assignment
     */
    public function update(Request $request, $id)
    {
        $ids = $this->getTeacherId();
        $teacherId = $ids['teacher_id'] ?? $ids['employee_id'];

        $assignment = Assignment::where('id', $id)
            ->where('teacher_id', $teacherId)
            ->firstOrFail();

        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'deadline' => 'required|date',
            'max_score' => 'required|numeric|min:1|max:100',
            'group_ids' => 'required|array|min:1',
            'group_ids.*' => 'exists:groups,id',
            'file' => 'nullable|file|max:10240',
        ]);

        $data = [
            'subject_id' => $request->subject_id,
            'title' => $request->title,
            'description' => $request->description,
            'deadline' => $request->deadline,
            'max_score' => $request->max_score,
            'group_ids' => json_encode($request->group_ids),
        ];

        // Handle file upload
        if ($request->hasFile('file')) {
            // Delete old file if exists
            if ($assignment->file_path) {
                Storage::disk('public')->delete($assignment->file_path);
            }

            $file = $request->file('file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('assignments', $filename, 'public');
            $data['file_path'] = $path;
        }

        $assignment->update($data);

        return redirect()
            ->route('teacher.assignments.show', $assignment->id)
            ->with('success', 'Topshiriq muvaffaqiyatli yangilandi!');
    }

    /**
     * Delete assignment
     */
    public function destroy($id)
    {
        $ids = $this->getTeacherId();
        $teacherId = $ids['teacher_id'] ?? $ids['employee_id'];

        $assignment = Assignment::where('id', $id)
            ->where('teacher_id', $teacherId)
            ->firstOrFail();

        // Delete file if exists
        if ($assignment->file_path) {
            Storage::disk('public')->delete($assignment->file_path);
        }

        $assignment->delete();

        return redirect()
            ->route('teacher.assignments.index')
            ->with('success', 'Topshiriq o\'chirildi!');
    }

    /**
     * Show pending submissions (need grading)
     */
    public function pending()
    {
        $ids = $this->getTeacherId();

        if (!$ids['teacher_id'] && !$ids['employee_id']) {
            return redirect()->route('teacher.dashboard')
                ->with('error', 'O\'qituvchi profili topilmadi. Admin bilan bog\'laning.');
        }

        $teacherId = $ids['teacher_id'] ?? $ids['employee_id'];

        $submissions = AssignmentSubmission::with(['assignment.subject', 'student.user', 'student.group'])
            ->whereHas('assignment', function($q) use ($teacherId) {
                $q->where('teacher_id', $teacherId);
            })
            ->where('status', 'submitted')
            ->whereNull('score')
            ->latest('submitted_at')
            ->paginate(20);

        return view('teacher.assignments.pending', [
            'submissions' => $submissions,
            'teacher' => $ids['teacher'] ?? $ids['employee'],
        ]);
    }
}
