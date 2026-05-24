<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\TeacherSubject;
use App\Models\Group;
use App\Models\JournalEntry;
use App\Models\JournalGrade;
use App\Models\Teacher;
use App\Models\Employee;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\NotificationController;

class GradeController extends Controller
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
     * Display list of teacher's subjects/groups for grading
     */
    public function index()
    {
        $ids = $this->getTeacherId();

        if (!$ids['teacher_id'] && !$ids['employee_id']) {
            return redirect()->route('teacher.dashboard')
                ->with('error', 'O\'qituvchi profili topilmadi. Admin bilan bog\'laning.');
        }

        $teacherId = $ids['employee_id'] ?? $ids['teacher_id'];

        // Get teacher's subjects with groups from TeacherSubject
        $teacherSubjects = TeacherSubject::with('subject')
            ->where('teacher_id', $teacherId)
            ->active()
            ->get();

        // Build groups data from TeacherSubject.group_ids
        $groupsData = collect();

        foreach ($teacherSubjects as $ts) {
            $groupIds = $ts->group_ids ?? [];
            if (!is_array($groupIds)) {
                $groupIds = json_decode($groupIds, true) ?? [];
            }

            foreach ($groupIds as $gid) {
                $group = Group::with(['students', 'specialty'])->find($gid);
                if (!$group) continue;

                $totalStudents = $group->students()->where('status', 'active')->count();

                // Get all grades for this group-subject
                $grades = JournalGrade::whereHas('journalEntry', function($q) use ($teacherId, $ts, $gid) {
                    $q->where('teacher_id', $teacherId)
                      ->where('subject_id', $ts->subject_id)
                      ->where('group_id', $gid);
                })->get();

                $avgScore = $grades->whereNotNull('score')->avg('score');
                $totalGrades = $grades->count();

                // Count by grade type
                $joriyCount = $grades->where('grade_type', 'joriy')->count();
                $oraliqCount = $grades->where('grade_type', 'oraliq')->count();
                $yakuniyCount = $grades->where('grade_type', 'yakuniy')->count();

                $groupsData->push([
                    'id' => $ts->id . '_' . $gid, // composite key
                    'teacher_subject_id' => $ts->id,
                    'subject' => $ts->subject,
                    'group' => $group,
                    'group_id' => $gid,
                    'semester' => $ts->semester,
                    'room' => $ts->room ?? null,
                    'total_students' => $totalStudents,
                    'avg_score' => $avgScore ? round($avgScore, 1) : 0,
                    'total_grades' => $totalGrades,
                    'joriy_count' => $joriyCount,
                    'oraliq_count' => $oraliqCount,
                    'yakuniy_count' => $yakuniyCount,
                ]);
            }
        }

        return view('teacher.grades.index', [
            'groupsData' => $groupsData,
            'teacher' => $ids['teacher'] ?? $ids['employee'],
        ]);
    }

    /**
     * Show grades table for specific group-subject
     * Format: {teacherSubjectId}_{groupId}
     */
    public function show($id)
    {
        $ids = $this->getTeacherId();
        $teacherId = $ids['employee_id'] ?? $ids['teacher_id'];

        // Parse composite ID
        $parts = explode('_', $id);
        $teacherSubjectId = $parts[0] ?? null;
        $groupId = $parts[1] ?? null;

        if (!$teacherSubjectId || !$groupId) {
            return redirect()->route('teacher.grades.index')
                ->with('error', 'Noto\'g\'ri identifikator');
        }

        $teacherSubject = TeacherSubject::with('subject')
            ->where('id', $teacherSubjectId)
            ->where('teacher_id', $teacherId)
            ->firstOrFail();

        $group = Group::with(['students.user', 'specialty'])->findOrFail($groupId);

        // Get all students in the group
        $students = $group->students()
            ->where('status', 'active')
            ->with(['user', 'journalGrades' => function($query) use ($teacherId, $teacherSubject, $groupId) {
                $query->whereHas('journalEntry', function($q) use ($teacherId, $teacherSubject, $groupId) {
                    $q->where('teacher_id', $teacherId)
                      ->where('subject_id', $teacherSubject->subject_id)
                      ->where('group_id', $groupId);
                })->with('journalEntry')->orderBy('graded_date', 'asc');
            }])
            ->orderBy('student_id')
            ->get();

        // Get all journal entries for this group-subject
        $journalEntries = JournalEntry::with(['grades'])
            ->where('teacher_id', $teacherId)
            ->where('subject_id', $teacherSubject->subject_id)
            ->where('group_id', $groupId)
            ->orderBy('created_at', 'asc')
            ->get();

        // Calculate statistics for each student
        $studentsData = $students->map(function($student) use ($journalEntries) {
            $grades = $student->journalGrades;

            $joriyGrades = $grades->where('grade_type', 'joriy');
            $oraliqGrades = $grades->where('grade_type', 'oraliq');
            $yakuniyGrades = $grades->where('grade_type', 'yakuniy');

            $joriyAvg = $joriyGrades->whereNotNull('score')->avg('score');
            $oraliqAvg = $oraliqGrades->whereNotNull('score')->avg('score');
            $yakuniyAvg = $yakuniyGrades->whereNotNull('score')->avg('score');
            $totalAvg = $grades->whereNotNull('score')->avg('score');

            // Calculate attendance rate
            $totalEntries = $journalEntries->count();
            $attendedEntries = $grades->count();
            $attendanceRate = $totalEntries > 0 ? round(($attendedEntries / $totalEntries) * 100, 1) : 0;

            return [
                'student' => $student,
                'grades' => $grades,
                'joriy_avg' => $joriyAvg ? round($joriyAvg, 1) : null,
                'oraliq_avg' => $oraliqAvg ? round($oraliqAvg, 1) : null,
                'yakuniy_avg' => $yakuniyAvg ? round($yakuniyAvg, 1) : null,
                'total_avg' => $totalAvg ? round($totalAvg, 1) : 0,
                'attendance_rate' => $attendanceRate,
                'joriy_count' => $joriyGrades->count(),
                'oraliq_count' => $oraliqGrades->count(),
                'yakuniy_count' => $yakuniyGrades->count(),
            ];
        });

        // Overall statistics
        $overallStats = [
            'total_students' => $students->count(),
            'avg_score' => $studentsData->avg('total_avg'),
            'avg_attendance' => $studentsData->avg('attendance_rate'),
            'total_entries' => $journalEntries->count(),
        ];

        // Create a groupSubject-like object for view compatibility
        $groupSubject = (object) [
            'id' => $id,
            'subject' => $teacherSubject->subject,
            'group' => $group,
            'subject_id' => $teacherSubject->subject_id,
            'group_id' => $groupId,
            'semester' => $teacherSubject->semester,
        ];

        return view('teacher.grades.show', [
            'groupSubject' => $groupSubject,
            'studentsData' => $studentsData,
            'journalEntries' => $journalEntries,
            'overallStats' => $overallStats,
            'teacher' => $ids['teacher'] ?? $ids['employee'],
        ]);
    }

    /**
     * Show form to edit a specific grade
     */
    public function edit($gradeId)
    {
        $ids = $this->getTeacherId();
        $teacherId = $ids['employee_id'] ?? $ids['teacher_id'];

        $grade = JournalGrade::with(['journalEntry.subject', 'journalEntry.group', 'student.user'])
            ->whereHas('journalEntry', function($q) use ($teacherId) {
                $q->where('teacher_id', $teacherId);
            })
            ->findOrFail($gradeId);

        return view('teacher.grades.edit', [
            'grade' => $grade,
            'teacher' => $ids['teacher'] ?? $ids['employee'],
        ]);
    }

    /**
     * Update a specific grade
     */
    public function update(Request $request, $gradeId)
    {
        $ids = $this->getTeacherId();
        $teacherId = $ids['employee_id'] ?? $ids['teacher_id'];

        $grade = JournalGrade::whereHas('journalEntry', function($q) use ($teacherId) {
            $q->where('teacher_id', $teacherId);
        })->findOrFail($gradeId);

        $request->validate([
            'score' => 'required|numeric|min:0|max:100',
            'topic' => 'nullable|string|max:255',
            'grade_type' => 'required|in:joriy,oraliq,yakuniy',
        ]);

        $grade->update([
            'score' => $request->score,
            'topic' => $request->topic,
            'grade_type' => $request->grade_type,
        ]);

        // Send notification about updated grade
        $student = Student::find($grade->student_id);
        $subject = $grade->journalEntry->subject ?? null;
        if ($student && $subject) {
            NotificationController::notifyGradeGiven(
                $student,
                $subject,
                $request->score,
                $request->grade_type
            );
        }

        return redirect()
            ->back()
            ->with('success', 'Baho muvaffaqiyatli yangilandi!');
    }

    /**
     * Delete a specific grade
     */
    public function destroy($gradeId)
    {
        $ids = $this->getTeacherId();
        $teacherId = $ids['employee_id'] ?? $ids['teacher_id'];

        $grade = JournalGrade::whereHas('journalEntry', function($q) use ($teacherId) {
            $q->where('teacher_id', $teacherId);
        })->findOrFail($gradeId);

        $grade->delete();

        return redirect()
            ->back()
            ->with('success', 'Baho o\'chirildi!');
    }

    /**
     * Show form to create new grades
     * Format: {teacherSubjectId}_{groupId}
     */
    public function create($id)
    {
        $ids = $this->getTeacherId();
        $teacherId = $ids['employee_id'] ?? $ids['teacher_id'];

        // Parse composite ID
        $parts = explode('_', $id);
        $teacherSubjectId = $parts[0] ?? null;
        $groupId = $parts[1] ?? null;

        if (!$teacherSubjectId || !$groupId) {
            return redirect()->route('teacher.grades.index')
                ->with('error', 'Noto\'g\'ri identifikator');
        }

        $teacherSubject = TeacherSubject::with('subject')
            ->where('id', $teacherSubjectId)
            ->where('teacher_id', $teacherId)
            ->firstOrFail();

        $group = Group::with(['students.user', 'specialty'])->findOrFail($groupId);

        // Get students
        $students = $group->students()
            ->where('status', 'active')
            ->with('user')
            ->orderBy('student_id')
            ->get();

        // Create groupSubject-like object
        $groupSubject = (object) [
            'id' => $id,
            'subject' => $teacherSubject->subject,
            'group' => $group,
            'subject_id' => $teacherSubject->subject_id,
            'group_id' => $groupId,
            'semester' => $teacherSubject->semester,
            'academic_year_id' => $teacherSubject->academic_year_id ?? 1,
        ];

        return view('teacher.grades.create', [
            'groupSubject' => $groupSubject,
            'students' => $students,
            'teacher' => $ids['teacher'] ?? $ids['employee'],
        ]);
    }

    /**
     * Store new grades
     * Format: {teacherSubjectId}_{groupId}
     */
    public function store(Request $request, $id)
    {
        $request->validate([
            'date' => 'required|date',
            'topic' => 'required|string|max:255',
            'grade_type' => 'required|in:joriy,oraliq,yakuniy',
            'grades' => 'required|array',
        ]);

        $ids = $this->getTeacherId();
        $teacherId = $ids['employee_id'] ?? $ids['teacher_id'];

        // Parse composite ID
        $parts = explode('_', $id);
        $teacherSubjectId = $parts[0] ?? null;
        $groupId = $parts[1] ?? null;

        if (!$teacherSubjectId || !$groupId) {
            return redirect()->route('teacher.grades.index')
                ->with('error', 'Noto\'g\'ri identifikator');
        }

        $teacherSubject = TeacherSubject::where('id', $teacherSubjectId)
            ->where('teacher_id', $teacherId)
            ->firstOrFail();

        // Create journal entry
        $journalEntry = JournalEntry::create([
            'subject_id' => $teacherSubject->subject_id,
            'group_id' => $groupId,
            'teacher_id' => $teacherId,
            'academic_year_id' => $teacherSubject->academic_year_id ?? 1,
            'semester_id' => $teacherSubject->semester ?? 1,
        ]);

        // Save grades for each student and send notifications
        $subject = $teacherSubject->subject;
        foreach ($request->grades as $studentId => $score) {
            if ($score !== null && $score !== '') {
                JournalGrade::create([
                    'journal_entry_id' => $journalEntry->id,
                    'student_id' => $studentId,
                    'grade_type' => $request->grade_type,
                    'score' => $score,
                    'graded_date' => $request->date,
                    'topic' => $request->topic,
                ]);

                // Send notification to student about new grade
                $student = Student::find($studentId);
                if ($student && $subject) {
                    NotificationController::notifyGradeGiven(
                        $student,
                        $subject,
                        $score,
                        $request->grade_type
                    );
                }
            }
        }

        return redirect()
            ->route('teacher.grades.show', $id)
            ->with('success', 'Baholar muvaffaqiyatli saqlandi!');
    }

    /**
     * Show student's detailed grades
     * Format: {teacherSubjectId}_{groupId}
     */
    public function studentGrades($id, $studentId)
    {
        $ids = $this->getTeacherId();
        $teacherId = $ids['employee_id'] ?? $ids['teacher_id'];

        // Parse composite ID
        $parts = explode('_', $id);
        $teacherSubjectId = $parts[0] ?? null;
        $groupId = $parts[1] ?? null;

        if (!$teacherSubjectId || !$groupId) {
            return redirect()->route('teacher.grades.index')
                ->with('error', 'Noto\'g\'ri identifikator');
        }

        $teacherSubject = TeacherSubject::with('subject')
            ->where('id', $teacherSubjectId)
            ->where('teacher_id', $teacherId)
            ->firstOrFail();

        $group = Group::findOrFail($groupId);

        $student = Student::with('user')
            ->where('id', $studentId)
            ->where('group_id', $groupId)
            ->firstOrFail();

        // Get all grades for this student in this subject
        $grades = JournalGrade::with(['journalEntry'])
            ->whereHas('journalEntry', function($q) use ($teacherId, $teacherSubject, $groupId) {
                $q->where('teacher_id', $teacherId)
                  ->where('subject_id', $teacherSubject->subject_id)
                  ->where('group_id', $groupId);
            })
            ->where('student_id', $studentId)
            ->orderBy('graded_date', 'desc')
            ->get();

        // Group by grade type
        $joriyGrades = $grades->where('grade_type', 'joriy');
        $oraliqGrades = $grades->where('grade_type', 'oraliq');
        $yakuniyGrades = $grades->where('grade_type', 'yakuniy');

        $statistics = [
            'joriy_avg' => $joriyGrades->whereNotNull('score')->avg('score'),
            'oraliq_avg' => $oraliqGrades->whereNotNull('score')->avg('score'),
            'yakuniy_avg' => $yakuniyGrades->whereNotNull('score')->avg('score'),
            'total_avg' => $grades->whereNotNull('score')->avg('score'),
            'joriy_count' => $joriyGrades->count(),
            'oraliq_count' => $oraliqGrades->count(),
            'yakuniy_count' => $yakuniyGrades->count(),
            'total_count' => $grades->count(),
        ];

        // Create groupSubject-like object
        $groupSubject = (object) [
            'id' => $id,
            'subject' => $teacherSubject->subject,
            'group' => $group,
            'subject_id' => $teacherSubject->subject_id,
            'group_id' => $groupId,
        ];

        return view('teacher.grades.student', [
            'groupSubject' => $groupSubject,
            'student' => $student,
            'grades' => $grades,
            'joriyGrades' => $joriyGrades,
            'oraliqGrades' => $oraliqGrades,
            'yakuniyGrades' => $yakuniyGrades,
            'statistics' => $statistics,
            'teacher' => $ids['teacher'] ?? $ids['employee'],
        ]);
    }
}
