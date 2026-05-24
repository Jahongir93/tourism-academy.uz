<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use App\Models\Student;
use App\Models\Subject;
use App\Models\AcademicGroup;
use App\Models\Faculty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AcademicDebtController extends Controller
{
    public function __construct()
    {
        // Middleware applied at route level
    }

    /**
     * Display academic debts dashboard
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // For students, show their own debts
        if ($user->hasRole('Student')) {
            $student = Student::where('user_id', $user->id)->first();
            if (!$student) {
                return redirect()->back()->with('error', 'Talaba profili topilmadi');
            }
            return $this->studentDebts($student->id);
        }

        // For teachers/admins, show overview
        $query = Grade::where('grade', '<', 55)
            ->where('is_final', true)
            ->with(['student.user', 'student.faculty', 'student.group', 'subject']);

        // Apply filters
        if ($request->has('faculty_id')) {
            $query->whereHas('student', function($q) use ($request) {
                $q->where('faculty_id', $request->faculty_id);
            });
        }

        if ($request->has('group_id')) {
            $query->whereHas('student', function($q) use ($request) {
                $q->where('group_id', $request->group_id);
            });
        }

        if ($request->has('academic_year')) {
            $query->where('academic_year', $request->academic_year);
        }

        if ($request->has('semester')) {
            $query->where('semester', $request->semester);
        }

        $debts = $query->orderBy('assessment_date', 'desc')->paginate(50);

        // Statistics
        $stats = $this->getOverallStatistics($request);

        // Get filter options
        $faculties = Faculty::where('is_active', true)->orderBy('name_uz')->get();
        $groups = AcademicGroup::where('is_active', true)->orderBy('name')->get();
        $academicYears = Grade::select('academic_year')
            ->distinct()
            ->orderBy('academic_year', 'desc')
            ->pluck('academic_year');

        return view('academic.debt.index', compact(
            'debts',
            'stats',
            'faculties',
            'groups',
            'academicYears'
        ));
    }

    /**
     * Show debts for a specific student
     */
    public function studentDebts(int $studentId)
    {
        $student = Student::with(['user', 'faculty', 'group'])->findOrFail($studentId);

        $debts = Grade::where('student_id', $studentId)
            ->where('grade', '<', 55)
            ->where('is_final', true)
            ->with(['subject', 'teacher'])
            ->orderBy('academic_year', 'desc')
            ->orderBy('semester', 'desc')
            ->get();

        // Group debts by academic year and semester
        $debtsBySemester = $debts->groupBy(function($debt) {
            return $debt->academic_year . ' - Semester ' . $debt->semester;
        });

        // Statistics
        $stats = [
            'total_debts' => $debts->count(),
            'total_credits' => $debts->sum('credits'),
            'subjects' => $debts->pluck('subject.name_uz')->unique()->count(),
            'oldest_debt' => $debts->min('assessment_date'),
        ];

        // Retake history
        $retakeHistory = Grade::where('student_id', $studentId)
            ->where('is_retake', true)
            ->with(['subject'])
            ->orderBy('assessment_date', 'desc')
            ->get();

        return view('academic.debt.student', compact(
            'student',
            'debts',
            'debtsBySemester',
            'stats',
            'retakeHistory'
        ));
    }

    /**
     * Show debts by group
     */
    public function groupDebts(int $groupId)
    {
        $group = AcademicGroup::with(['faculty', 'specialty', 'students.user'])->findOrFail($groupId);

        $students = $group->students;
        $studentsData = [];

        foreach ($students as $student) {
            $debts = Grade::where('student_id', $student->id)
                ->where('grade', '<', 55)
                ->where('is_final', true)
                ->with('subject')
                ->get();

            if ($debts->count() > 0) {
                $studentsData[] = [
                    'student' => $student,
                    'debts_count' => $debts->count(),
                    'total_credits' => $debts->sum('credits'),
                    'debts' => $debts,
                ];
            }
        }

        // Sort by debts count
        usort($studentsData, function($a, $b) {
            return $b['debts_count'] - $a['debts_count'];
        });

        return view('academic.debt.group', compact('group', 'studentsData'));
    }

    /**
     * Show debts by faculty
     */
    public function facultyDebts(int $facultyId)
    {
        $faculty = Faculty::findOrFail($facultyId);

        $stats = Grade::where('grade', '<', 55)
            ->where('is_final', true)
            ->whereHas('student', function($q) use ($facultyId) {
                $q->where('faculty_id', $facultyId);
            })
            ->select(
                DB::raw('COUNT(DISTINCT student_id) as students_with_debts'),
                DB::raw('COUNT(*) as total_debts'),
                DB::raw('SUM(credits) as total_credits'),
                DB::raw('COUNT(DISTINCT subject_id) as subjects_count')
            )
            ->first();

        // Debts by subject
        $debtsBySubject = Grade::where('grade', '<', 55)
            ->where('is_final', true)
            ->whereHas('student', function($q) use ($facultyId) {
                $q->where('faculty_id', $facultyId);
            })
            ->with('subject')
            ->select('subject_id', DB::raw('COUNT(*) as count'))
            ->groupBy('subject_id')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();

        // Debts by group
        $debtsByGroup = Student::where('faculty_id', $facultyId)
            ->whereHas('grades', function($q) {
                $q->where('grade', '<', 55)->where('is_final', true);
            })
            ->with('group')
            ->select('group_id', DB::raw('COUNT(*) as students_count'))
            ->groupBy('group_id')
            ->orderBy('students_count', 'desc')
            ->get();

        return view('academic.debt.faculty', compact(
            'faculty',
            'stats',
            'debtsBySubject',
            'debtsByGroup'
        ));
    }

    /**
     * Register a retake attempt
     */
    public function registerRetake(Request $request)
    {
        $request->validate([
            'grade_id' => 'required|exists:grades,id',
            'retake_date' => 'required|date|after:today',
        ]);

        $originalGrade = Grade::findOrFail($request->grade_id);

        // Check if student is eligible for retake
        if ($originalGrade->grade >= 55) {
            return response()->json([
                'success' => false,
                'message' => 'Bu fan bo\'yicha qarzdorlik yo\'q',
            ], 400);
        }

        // Create retake record
        $retake = Grade::create([
            'student_id' => $originalGrade->student_id,
            'subject_id' => $originalGrade->subject_id,
            'journal_id' => $originalGrade->journal_id,
            'academic_year' => $originalGrade->academic_year,
            'semester' => $originalGrade->semester,
            'course' => $originalGrade->course,
            'grade' => 0, // To be filled later
            'grade_point' => 0,
            'letter_grade' => null,
            'credits' => $originalGrade->credits,
            'assessment_type' => 'exam',
            'assessment_date' => $request->retake_date,
            'teacher_id' => $originalGrade->teacher_id,
            'is_retake' => true,
            'attempt_number' => $originalGrade->attempt_number + 1,
            'is_final' => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Qayta topshirish ro\'yxatga olindi',
            'retake' => $retake,
        ]);
    }

    /**
     * Update retake grade
     */
    public function updateRetake(Request $request, int $retakeId)
    {
        $request->validate([
            'grade' => 'required|numeric|min:0|max:100',
            'comments' => 'nullable|string|max:500',
        ]);

        $retake = Grade::findOrFail($retakeId);

        if (!$retake->is_retake) {
            return response()->json([
                'success' => false,
                'message' => 'Bu qayta topshirish emas',
            ], 400);
        }

        $gradeValue = $request->grade;
        $retake->update([
            'grade' => $gradeValue,
            'grade_point' => Grade::calculateGradePoint($gradeValue),
            'letter_grade' => Grade::getLetterGrade($gradeValue),
            'comments' => $request->comments,
            'is_final' => true,
        ]);

        // If passed, update original grade as non-final
        if ($gradeValue >= 55) {
            Grade::where('student_id', $retake->student_id)
                ->where('subject_id', $retake->subject_id)
                ->where('academic_year', $retake->academic_year)
                ->where('semester', $retake->semester)
                ->where('is_retake', false)
                ->update(['is_final' => false]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Baho saqlandi',
            'retake' => $retake,
        ]);
    }

    /**
     * Get overall statistics
     */
    protected function getOverallStatistics(Request $request)
    {
        $query = Grade::where('grade', '<', 55)->where('is_final', true);

        if ($request->has('faculty_id')) {
            $query->whereHas('student', function($q) use ($request) {
                $q->where('faculty_id', $request->faculty_id);
            });
        }

        if ($request->has('academic_year')) {
            $query->where('academic_year', $request->academic_year);
        }

        return [
            'total_debts' => $query->count(),
            'total_students' => $query->distinct('student_id')->count('student_id'),
            'total_credits' => $query->sum('credits'),
            'total_subjects' => $query->distinct('subject_id')->count('subject_id'),
            'average_debts_per_student' => $query->count() > 0
                ? round($query->count() / $query->distinct('student_id')->count('student_id'), 2)
                : 0,
        ];
    }

    /**
     * Export debts report
     */
    public function export(Request $request)
    {
        // TODO: Implement Excel export

        return response()->json([
            'success' => false,
            'message' => 'Export funksiyasi hali ishlab chiqilmagan',
        ]);
    }

    /**
     * Send notification to students with debts
     */
    public function notifyStudents(Request $request)
    {
        $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:students,id',
            'message' => 'nullable|string|max:1000',
        ]);

        // TODO: Implement notification system

        return response()->json([
            'success' => true,
            'message' => count($request->student_ids) . ' ta talabaga xabar yuborildi',
        ]);
    }
}
