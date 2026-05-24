<?php

namespace App\Http\Controllers;

use App\Models\JournalGrade;
use App\Models\JournalEntry;
use App\Models\Student;
use Illuminate\Http\Request;

class GradeController extends Controller
{
    public function allGrades()
    {
        $user = auth()->user();

        // Check if user is a student
        $student = Student::where('user_id', $user->id)->first();

        if (!$student) {
            return redirect()->route('dashboard')->with('error', 'Talaba ma\'lumotlari topilmadi.');
        }

        // Get only this student's grades
        $grades = JournalGrade::with(['journalEntry.subject', 'journalEntry.group'])
            ->where('student_id', $student->id)
            ->latest('graded_date')
            ->paginate(20);

        // Get grades by subject with average
        $gradesBySubject = JournalGrade::where('student_id', $student->id)
            ->with(['journalEntry.subject'])
            ->get()
            ->groupBy(function($grade) {
                return $grade->journalEntry->subject->id ?? 'unknown';
            })
            ->map(function($subjectGrades) {
                $subject = $subjectGrades->first()->journalEntry->subject ?? null;
                return [
                    'subject' => $subject,
                    'grades' => $subjectGrades,
                    'average' => round($subjectGrades->avg('score'), 2),
                    'joriy' => $subjectGrades->where('grade_type', 'joriy')->avg('score'),
                    'oraliq' => $subjectGrades->where('grade_type', 'oraliq')->avg('score'),
                    'yakuniy' => $subjectGrades->where('grade_type', 'yakuniy')->avg('score'),
                    'total_count' => $subjectGrades->count(),
                ];
            });

        // Calculate statistics for this student only
        $statistics = [
            'total_grades' => $grades->total(),
            'average_score' => round(JournalGrade::where('student_id', $student->id)->avg('score') ?? 0, 2),
            'excellent_count' => JournalGrade::where('student_id', $student->id)->where('score', '>=', 86)->count(),
            'good_count' => JournalGrade::where('student_id', $student->id)->whereBetween('score', [71, 85])->count(),
            'satisfactory_count' => JournalGrade::where('student_id', $student->id)->whereBetween('score', [56, 70])->count(),
            'unsatisfactory_count' => JournalGrade::where('student_id', $student->id)->where('score', '<', 56)->count(),
            'gpa' => $this->calculateGPA($student->id),
        ];

        return view('grades.student', compact('grades', 'statistics', 'student', 'gradesBySubject'));
    }

    /**
     * Calculate GPA for student
     */
    private function calculateGPA($studentId)
    {
        $grades = JournalGrade::where('student_id', $studentId)
            ->where('grade_type', 'yakuniy')
            ->get();

        if ($grades->isEmpty()) {
            return 0;
        }

        $totalPoints = 0;
        $totalCredits = 0;

        foreach ($grades as $grade) {
            $score = $grade->score;
            $credits = 3; // Default, should get from subject

            // Convert score to GPA point (4.0 scale)
            if ($score >= 86) {
                $point = 4.0;
            } elseif ($score >= 71) {
                $point = 3.0;
            } elseif ($score >= 56) {
                $point = 2.0;
            } else {
                $point = 0;
            }

            $totalPoints += $point * $credits;
            $totalCredits += $credits;
        }

        return $totalCredits > 0 ? round($totalPoints / $totalCredits, 2) : 0;
    }
    
    public function index($journalId)
    {
        $groupSubject = \App\Models\GroupSubject::with(['studentGroup', 'subject', 'teacher', 'academicYear'])
            ->findOrFail($journalId);

        $students = Student::where('group_id', $groupSubject->student_group_id)
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        // Baholarni olish
        $grades = \App\Models\GroupSubjectGrade::where('group_subject_id', $journalId)
            ->with('student')
            ->get()
            ->keyBy('student_id');

        // Qo'shimcha ustunlar ro'yxati
        $additionalColumns = [];
        if ($grades->isNotEmpty()) {
            $firstGrade = $grades->first();
            if ($firstGrade && $firstGrade->additional_grades) {
                $additionalColumns = array_keys($firstGrade->additional_grades);
            }
        }

        return view('grades.index', compact('groupSubject', 'students', 'grades', 'additionalColumns'));
    }
    
    public function store(Request $request, $journalId)
    {
        $groupSubject = \App\Models\GroupSubject::findOrFail($journalId);

        $validated = $request->validate([
            'grades' => 'required|array',
            'grades.*.student_id' => 'required|exists:students,id',
            'grades.*.current_grade' => 'nullable|numeric|min:0|max:100',
            'grades.*.midterm_grade' => 'nullable|numeric|min:0|max:100',
            'grades.*.final_grade' => 'nullable|numeric|min:0|max:100',
            'grades.*.additional.*' => 'nullable|numeric|min:0|max:100',
            'additional_columns' => 'nullable|array',
        ]);

        foreach ($validated['grades'] as $gradeData) {
            $grade = \App\Models\GroupSubjectGrade::updateOrCreate(
                [
                    'group_subject_id' => $journalId,
                    'student_id' => $gradeData['student_id'],
                ],
                [
                    'current_grade' => $gradeData['current_grade'] ?? null,
                    'midterm_grade' => $gradeData['midterm_grade'] ?? null,
                    'final_grade' => $gradeData['final_grade'] ?? null,
                    'additional_grades' => $gradeData['additional'] ?? null,
                ]
            );

            // Umumiy ballni hisoblash
            $grade->calculateTotalScore();
            $grade->save();
        }

        return redirect()->route('grades.index', $journalId)
            ->with('success', 'Baholar muvaffaqiyatli saqlandi');
    }

    public function addColumn(Request $request, $journalId)
    {
        $validated = $request->validate([
            'column_name' => 'required|string|max:255',
        ]);

        // Barcha baholarni yangilash - yangi ustun qo'shish
        $grades = \App\Models\GroupSubjectGrade::where('group_subject_id', $journalId)->get();

        foreach ($grades as $grade) {
            $additional = $grade->additional_grades ?? [];
            $additional[$validated['column_name']] = null;
            $grade->additional_grades = $additional;
            $grade->save();
        }

        return redirect()->route('grades.index', $journalId)
            ->with('success', 'Yangi ustun qo\'shildi');
    }
}
