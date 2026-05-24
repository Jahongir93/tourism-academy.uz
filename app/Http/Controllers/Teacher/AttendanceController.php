<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Teacher;
use App\Models\Employee;
use App\Models\TeacherSubject;
use App\Models\Group;
use App\Models\JournalEntry;
use App\Models\JournalGrade;
use App\Models\Student;
use Carbon\Carbon;

class AttendanceController extends Controller
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
     * Display teacher's groups for attendance
     */
    public function index()
    {
        $ids = $this->getTeacherId();

        if (!$ids['teacher_id'] && !$ids['employee_id']) {
            return redirect()->route('teacher.dashboard')
                ->with('error', 'O\'qituvchi profili topilmadi. Admin bilan bog\'laning.');
        }

        $teacherId = $ids['employee_id'] ?? $ids['teacher_id'];

        // Get teacher's subjects from TeacherSubject
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

                // Get last journal entry date
                $lastEntry = JournalEntry::where('teacher_id', $teacherId)
                    ->where('subject_id', $ts->subject_id)
                    ->where('group_id', $gid)
                    ->latest('created_at')
                    ->first();

                // Calculate attendance rate
                $totalEntries = JournalEntry::where('teacher_id', $teacherId)
                    ->where('subject_id', $ts->subject_id)
                    ->where('group_id', $gid)
                    ->count();

                $totalAttendance = JournalGrade::whereHas('journalEntry', function($q) use ($teacherId, $ts, $gid) {
                    $q->where('teacher_id', $teacherId)
                      ->where('subject_id', $ts->subject_id)
                      ->where('group_id', $gid);
                })->count();

                $expectedAttendance = $totalEntries * $totalStudents;
                $attendanceRate = $expectedAttendance > 0 ? round(($totalAttendance / $expectedAttendance) * 100, 1) : 0;

                $groupsData->push([
                    'id' => $ts->id . '_' . $gid,
                    'teacher_subject_id' => $ts->id,
                    'subject' => $ts->subject,
                    'group' => $group,
                    'group_id' => $gid,
                    'room' => $ts->room ?? null,
                    'semester' => $ts->semester,
                    'total_students' => $totalStudents,
                    'total_entries' => $totalEntries,
                    'attendance_rate' => $attendanceRate,
                    'last_entry_date' => $lastEntry ? $lastEntry->created_at : null,
                ]);
            }
        }

        return view('teacher.attendance.index', [
            'teacher' => $ids['teacher'] ?? $ids['employee'],
            'groupsData' => $groupsData,
        ]);
    }

    /**
     * Show attendance journal for specific group-subject
     * Format: {teacherSubjectId}_{groupId}
     */
    public function journal($id)
    {
        $ids = $this->getTeacherId();
        $teacherId = $ids['employee_id'] ?? $ids['teacher_id'];

        // Parse composite ID
        $parts = explode('_', $id);
        $teacherSubjectId = $parts[0] ?? null;
        $groupId = $parts[1] ?? null;

        if (!$teacherSubjectId || !$groupId) {
            return redirect()->route('teacher.attendance.index')
                ->with('error', 'Noto\'g\'ri identifikator');
        }

        $teacherSubject = TeacherSubject::with('subject')
            ->where('id', $teacherSubjectId)
            ->where('teacher_id', $teacherId)
            ->firstOrFail();

        $group = Group::with(['students.user', 'specialty'])->findOrFail($groupId);

        // Get all journal entries for this group-subject
        $journalEntries = JournalEntry::with(['grades.student.user'])
            ->where('teacher_id', $teacherId)
            ->where('subject_id', $teacherSubject->subject_id)
            ->where('group_id', $groupId)
            ->latest('created_at')
            ->paginate(20);

        // Get students
        $students = $group->students()
            ->where('status', 'active')
            ->with('user')
            ->get();

        // Create groupSubject-like object for view compatibility
        $groupSubject = (object) [
            'id' => $id,
            'subject' => $teacherSubject->subject,
            'group' => $group,
            'subject_id' => $teacherSubject->subject_id,
            'group_id' => $groupId,
            'semester' => $teacherSubject->semester,
        ];

        return view('teacher.attendance.journal', compact(
            'groupSubject',
            'journalEntries',
            'students'
        ))->with('teacher', $ids['teacher'] ?? $ids['employee']);
    }

    /**
     * Show form to mark attendance
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
            return redirect()->route('teacher.attendance.index')
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

        return view('teacher.attendance.create', compact(
            'groupSubject',
            'students'
        ))->with('teacher', $ids['teacher'] ?? $ids['employee']);
    }

    /**
     * Store attendance
     * Format: {teacherSubjectId}_{groupId}
     */
    public function store(Request $request, $id)
    {
        $request->validate([
            'date' => 'required|date',
            'topic' => 'required|string|max:255',
            'lesson_type' => 'required|in:joriy,oraliq,yakuniy',
            'attendance' => 'required|array',
        ]);

        $ids = $this->getTeacherId();
        $teacherId = $ids['employee_id'] ?? $ids['teacher_id'];

        // Parse composite ID
        $parts = explode('_', $id);
        $teacherSubjectId = $parts[0] ?? null;
        $groupId = $parts[1] ?? null;

        if (!$teacherSubjectId || !$groupId) {
            return redirect()->route('teacher.attendance.index')
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

        // Save attendance and grades
        foreach ($request->attendance as $studentId => $data) {
            if (isset($data['present']) && $data['present'] == '1') {
                JournalGrade::create([
                    'journal_entry_id' => $journalEntry->id,
                    'student_id' => $studentId,
                    'grade_type' => $request->lesson_type,
                    'score' => $data['score'] ?? null,
                    'graded_date' => $request->date,
                    'topic' => $request->topic,
                ]);
            }
        }

        return redirect()
            ->route('teacher.attendance.journal', $id)
            ->with('success', 'Davomat muvaffaqiyatli saqlandi!');
    }

    /**
     * Show specific journal entry
     */
    public function show($journalEntryId)
    {
        $ids = $this->getTeacherId();
        $teacherId = $ids['employee_id'] ?? $ids['teacher_id'];

        $journalEntry = JournalEntry::with([
            'subject',
            'group.students.user',
            'grades.student.user'
        ])
            ->where('teacher_id', $teacherId)
            ->findOrFail($journalEntryId);

        return view('teacher.attendance.show', [
            'teacher' => $ids['teacher'] ?? $ids['employee'],
            'journalEntry' => $journalEntry,
        ]);
    }

    /**
     * Mark attendance page
     */
    public function mark()
    {
        return $this->index();
    }

    /**
     * Store mark
     */
    public function storeMark(Request $request)
    {
        $ids = $this->getTeacherId();
        $teacherId = $ids['employee_id'] ?? $ids['teacher_id'];

        $request->validate([
            'group_subject_id' => 'required',
            'date' => 'required|date',
            'topic' => 'required|string|max:255',
            'lesson_type' => 'required|in:joriy,oraliq,yakuniy',
            'attendance' => 'required|array',
        ]);

        return $this->store($request, $request->group_subject_id);
    }

    /**
     * Today's attendance
     */
    public function today()
    {
        $ids = $this->getTeacherId();
        $teacherId = $ids['employee_id'] ?? $ids['teacher_id'];

        $today = Carbon::today();

        $todayEntries = JournalEntry::with(['subject', 'group', 'grades'])
            ->where('teacher_id', $teacherId)
            ->whereDate('created_at', $today)
            ->latest()
            ->get();

        return view('teacher.attendance.today', [
            'teacher' => $ids['teacher'] ?? $ids['employee'],
            'todayEntries' => $todayEntries,
        ]);
    }

    /**
     * Attendance history
     */
    public function history(Request $request)
    {
        $ids = $this->getTeacherId();
        $teacherId = $ids['employee_id'] ?? $ids['teacher_id'];

        $query = JournalEntry::with(['subject', 'group', 'grades'])
            ->where('teacher_id', $teacherId);

        if ($request->has('from_date') && $request->from_date) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->has('to_date') && $request->to_date) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $entries = $query->latest()->paginate(30);

        return view('teacher.attendance.history', [
            'teacher' => $ids['teacher'] ?? $ids['employee'],
            'entries' => $entries,
        ]);
    }

    /**
     * Attendance report
     */
    public function report(Request $request)
    {
        $ids = $this->getTeacherId();
        $teacherId = $ids['employee_id'] ?? $ids['teacher_id'];

        // Get teacher's subjects from TeacherSubject
        $teacherSubjects = TeacherSubject::with('subject')
            ->where('teacher_id', $teacherId)
            ->active()
            ->get();

        // Build statistics
        $reportData = collect();

        foreach ($teacherSubjects as $ts) {
            $groupIds = $ts->group_ids ?? [];
            if (!is_array($groupIds)) {
                $groupIds = json_decode($groupIds, true) ?? [];
            }

            foreach ($groupIds as $gid) {
                $group = Group::with('students')->find($gid);
                if (!$group) continue;

                $totalStudents = $group->students()->where('status', 'active')->count();
                $totalEntries = JournalEntry::where('teacher_id', $teacherId)
                    ->where('subject_id', $ts->subject_id)
                    ->where('group_id', $gid)
                    ->count();

                $totalAttendance = JournalGrade::whereHas('journalEntry', function($q) use ($teacherId, $ts, $gid) {
                    $q->where('teacher_id', $teacherId)
                      ->where('subject_id', $ts->subject_id)
                      ->where('group_id', $gid);
                })->count();

                $expectedAttendance = $totalEntries * $totalStudents;
                $attendanceRate = $expectedAttendance > 0 ? round(($totalAttendance / $expectedAttendance) * 100, 1) : 0;

                $reportData->push([
                    'subject' => $ts->subject,
                    'group' => $group,
                    'total_students' => $totalStudents,
                    'total_entries' => $totalEntries,
                    'total_attendance' => $totalAttendance,
                    'attendance_rate' => $attendanceRate,
                ]);
            }
        }

        return view('teacher.attendance.report', [
            'teacher' => $ids['teacher'] ?? $ids['employee'],
            'reportData' => $reportData,
        ]);
    }

    /**
     * Export attendance for a group
     */
    public function exportAttendance($groupId)
    {
        $ids = $this->getTeacherId();
        $teacherId = $ids['employee_id'] ?? $ids['teacher_id'];

        // Parse composite ID
        $parts = explode('_', $groupId);
        $teacherSubjectId = $parts[0] ?? null;
        $gid = $parts[1] ?? null;

        if (!$teacherSubjectId || !$gid) {
            return redirect()->back()->with('error', 'Noto\'g\'ri identifikator');
        }

        $teacherSubject = TeacherSubject::with('subject')
            ->where('id', $teacherSubjectId)
            ->where('teacher_id', $teacherId)
            ->firstOrFail();

        $group = Group::with('students.user')->findOrFail($gid);

        $entries = JournalEntry::with(['grades.student.user'])
            ->where('teacher_id', $teacherId)
            ->where('subject_id', $teacherSubject->subject_id)
            ->where('group_id', $gid)
            ->orderBy('created_at', 'asc')
            ->get();

        // For now, just return a view (can be extended to PDF/Excel export)
        return view('teacher.attendance.export', [
            'teacher' => $ids['teacher'] ?? $ids['employee'],
            'subject' => $teacherSubject->subject,
            'group' => $group,
            'entries' => $entries,
        ]);
    }

    /**
     * Group attendance
     */
    public function groupAttendance($groupId)
    {
        return $this->journal($groupId);
    }
}
