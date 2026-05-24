<?php

namespace App\Http\Controllers;

use App\Models\JournalEntry;
use App\Models\Subject;
use App\Models\Group;
use App\Models\StudentGroup;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class JournalController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $currentYear = \App\Models\AcademicYear::where('is_current', true)->first();
        $currentSemester = now()->month >= 9 ? 1 : 2;

        if ($user->hasRole('teacher') || $user->hasRole('Teacher')) {
            // Get Teacher record
            $teacher = Teacher::where('user_id', $user->id)->first();

            // Also get Employee record (for TeacherSubject compatibility)
            $employee = \App\Models\Employee::where('user_id', $user->id)->first();

            $teacherId = $teacher ? $teacher->id : null;
            $employeeId = $employee ? $employee->id : null;

            if ($teacherId || $employeeId) {
                // O'qituvchi o'z fanlarini ko'radi (JournalEntry dan)
                $journals = JournalEntry::with(['group', 'subject', 'academicYear', 'teacher'])
                    ->where(function($q) use ($teacherId, $employeeId) {
                        if ($teacherId) {
                            $q->where('teacher_id', $teacherId);
                        }
                        // Note: JournalEntry.teacher_id references teachers table, not employees
                        // So we don't need to check employeeId here directly
                    })
                    ->where('academic_year_id', $currentYear->id ?? 1)
                    ->paginate(10);
            } else {
                $journals = JournalEntry::query()->whereRaw('1=0')->paginate(10);
            }
        } elseif ($user->hasRole('student') || $user->hasRole('Student')) {
            // Talaba o'z guruhidagi barcha fanlarni ko'radi
            $student = Student::where('user_id', $user->id)->first();
            if ($student && $student->group_id) {
                $journals = JournalEntry::with(['group', 'subject', 'teacher', 'academicYear'])
                    ->where('group_id', $student->group_id)
                    ->where('academic_year_id', $currentYear->id ?? 1)
                    ->paginate(10);
            } else {
                $journals = JournalEntry::query()->whereRaw('1=0')->paginate(10);
            }
        } elseif ($user->hasRole('admin') || $user->hasRole('SuperAdmin') || $user->hasRole('dean') || $user->hasRole('Dean')) {
            // Admin va Dekan barcha jurnallarni ko'radi
            $journals = JournalEntry::with(['group', 'subject', 'teacher', 'academicYear'])
                ->where('academic_year_id', $currentYear->id ?? 1)
                ->paginate(10);
        } else {
            $journals = JournalEntry::query()->whereRaw('1=0')->paginate(10);
        }

        return view('journal.index', compact('journals', 'currentSemester', 'currentYear'));
    }

    public function create()
    {
        $subjects = Subject::where('is_active', true)->orderBy('name_uz')->get();

        // Groups jadvalidagi guruhlarni olish (journal_entries uchun)
        $groups = DB::table('groups')->orderBy('name')->get();

        // Agar groups bo'sh bo'lsa, StudentGroup-dan olish
        if ($groups->isEmpty()) {
            $studentGroups = StudentGroup::with('specialty')->orderBy('name')->get();
        } else {
            $studentGroups = collect();
        }

        $teachers = Teacher::all();
        $academicYears = AcademicYear::orderBy('year', 'desc')->get();

        return view('journal.create', compact('subjects', 'groups', 'studentGroups', 'teachers', 'academicYears'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'group_id' => 'required|integer',
            'teacher_id' => 'required|exists:teachers,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'semester_id' => 'required|integer|min:1|max:2'
        ]);

        // Agar group_id groups jadvalida bo'lmasa, student_group_id sifatida qabul qilamiz
        $groupExists = DB::table('groups')->where('id', $validated['group_id'])->exists();

        if (!$groupExists) {
            // StudentGroup dan groups jadvaliga ko'chirish
            $studentGroup = StudentGroup::find($validated['group_id']);

            if ($studentGroup) {
                $firstDepartment = \App\Models\Department::first();

                $groupId = DB::table('groups')->insertGetId([
                    'name' => $studentGroup->name,
                    'code' => $studentGroup->code ?? $studentGroup->name,
                    'department_id' => $firstDepartment->id ?? 1,
                    'course' => $studentGroup->course ?? 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $validated['group_id'] = $groupId;
            }
        }

        $journal = JournalEntry::create($validated);

        return redirect()->route('journal.show', $journal)
            ->with('success', 'Jurnal muvaffaqiyatli yaratildi');
    }

    public function show($id)
    {
        // First try to find as GroupSubject
        $groupSubject = \App\Models\GroupSubject::with(['studentGroup', 'subject', 'teacher', 'academicYear'])
            ->find($id);

        $groupId = null;

        if ($groupSubject) {
            $groupId = $groupSubject->student_group_id ?? $groupSubject->group_id;
        } else {
            // Try to find as JournalEntry
            $journalEntry = JournalEntry::with(['subject', 'group', 'teacher', 'academicYear'])
                ->find($id);

            if (!$journalEntry) {
                abort(404, 'Jurnal topilmadi');
            }

            // Create a GroupSubject-like object from JournalEntry
            $groupSubject = (object)[
                'id' => $journalEntry->id,
                'subject_id' => $journalEntry->subject_id,
                'group_id' => $journalEntry->group_id,
                'student_group_id' => $journalEntry->group_id,
                'subject' => $journalEntry->subject,
                'studentGroup' => $journalEntry->group,
                'group' => $journalEntry->group,
                'teacher' => $journalEntry->teacher,
                'academicYear' => $journalEntry->academicYear,
                'semester' => $journalEntry->semester_id,
            ];

            $groupId = $journalEntry->group_id;
        }

        // Get students from the group
        $students = Student::where('group_id', $groupId)
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        // Get attendance records
        $attendances = collect();

        // Get grades
        $grades = collect();

        return view('journal.show', compact('groupSubject', 'students', 'attendances', 'grades'));
    }

    public function edit(JournalEntry $journal)
    {
        $subjects = Subject::all();
        $groups = Group::all();
        $teachers = Teacher::all();
        $academicYears = AcademicYear::all();

        return view('journal.edit', compact('journal', 'subjects', 'groups', 'teachers', 'academicYears'));
    }

    public function update(Request $request, JournalEntry $journal)
    {
        $validated = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'group_id' => 'required|exists:groups,id',
            'teacher_id' => 'required|exists:teachers,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'semester_id' => 'required|integer|min:1|max:2'
        ]);

        $journal->update($validated);

        return redirect()->route('journal.show', $journal)
            ->with('success', 'Jurnal muvaffaqiyatli yangilandi');
    }

    public function destroy(JournalEntry $journal)
    {
        $journal->delete();

        return redirect()->route('journal.index')
            ->with('success', 'Jurnal muvaffaqiyatli o\'chirildi');
    }

    public function analytics($journalId)
    {
        $groupSubject = \App\Models\GroupSubject::with(['studentGroup', 'subject', 'teacher', 'academicYear'])
            ->findOrFail($journalId);

        // Statistika keyinroq qo'shiladi
        $attendanceStats = collect();
        $gradeStats = collect();

        $students = Student::where('group_id', $groupSubject->student_group_id)
            ->select('id', 'first_name', 'last_name', 'full_name')
            ->orderBy('last_name')
            ->get();

        $studentPerformance = $students->map(function($student) {
            return (object)[
                'id' => $student->id,
                'first_name' => $student->first_name,
                'last_name' => $student->last_name,
                'full_name' => $student->full_name,
                'average_score' => null
            ];
        });

        return view('journal.analytics', compact('groupSubject', 'attendanceStats', 'gradeStats', 'studentPerformance'));
    }
}