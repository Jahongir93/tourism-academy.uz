<?php

namespace App\Services;

use App\Models\Student;
use App\Models\Grade;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class GPACalculatorService
{
    /**
     * Calculate GPA for a student
     *
     * @param int $studentId
     * @param string|null $academicYear Specific year or null for all
     * @param int|null $semester Specific semester or null for all
     * @return array
     */
    public function calculateGPA(int $studentId, ?string $academicYear = null, ?int $semester = null): array
    {
        $query = Grade::where('student_id', $studentId)
            ->where('is_final', true)
            ->with('subject');

        if ($academicYear) {
            $query->where('academic_year', $academicYear);
        }

        if ($semester) {
            $query->where('semester', $semester);
        }

        $grades = $query->get();

        if ($grades->isEmpty()) {
            return [
                'gpa' => 0.0,
                'total_credits' => 0,
                'earned_credits' => 0,
                'total_grade_points' => 0.0,
                'total_subjects' => 0,
                'passed_subjects' => 0,
                'failed_subjects' => 0,
                'grades' => [],
            ];
        }

        $totalCredits = $grades->sum('credits');
        $earnedCredits = $grades->where('grade', '>=', 55)->sum('credits');

        // Calculate weighted GPA
        $totalGradePoints = $grades->sum(function ($grade) {
            return $grade->grade_point * $grade->credits;
        });

        $gpa = $totalCredits > 0 ? round($totalGradePoints / $totalCredits, 2) : 0.0;

        $passedSubjects = $grades->where('grade', '>=', 55)->count();
        $failedSubjects = $grades->where('grade', '<', 55)->count();

        return [
            'gpa' => $gpa,
            'total_credits' => $totalCredits,
            'earned_credits' => $earnedCredits,
            'total_grade_points' => round($totalGradePoints, 2),
            'total_subjects' => $grades->count(),
            'passed_subjects' => $passedSubjects,
            'failed_subjects' => $failedSubjects,
            'pass_rate' => $grades->count() > 0 ? round(($passedSubjects / $grades->count()) * 100, 2) : 0,
            'grades' => $grades,
        ];
    }

    /**
     * Calculate cumulative GPA (all semesters)
     */
    public function calculateCumulativeGPA(int $studentId): array
    {
        return $this->calculateGPA($studentId);
    }

    /**
     * Calculate semester GPA
     */
    public function calculateSemesterGPA(int $studentId, string $academicYear, int $semester): array
    {
        return $this->calculateGPA($studentId, $academicYear, $semester);
    }

    /**
     * Calculate yearly GPA
     */
    public function calculateYearlyGPA(int $studentId, string $academicYear): array
    {
        return $this->calculateGPA($studentId, $academicYear);
    }

    /**
     * Get detailed GPA breakdown by semester
     */
    public function getGPABySemester(int $studentId): Collection
    {
        $grades = Grade::where('student_id', $studentId)
            ->where('is_final', true)
            ->select('academic_year', 'semester',
                DB::raw('SUM(grade_point * credits) as total_grade_points'),
                DB::raw('SUM(credits) as total_credits'),
                DB::raw('COUNT(*) as total_subjects'),
                DB::raw('SUM(CASE WHEN grade >= 55 THEN credits ELSE 0 END) as earned_credits'),
                DB::raw('SUM(CASE WHEN grade >= 55 THEN 1 ELSE 0 END) as passed_subjects')
            )
            ->groupBy('academic_year', 'semester')
            ->orderBy('academic_year')
            ->orderBy('semester')
            ->get()
            ->map(function ($item) {
                $gpa = $item->total_credits > 0
                    ? round($item->total_grade_points / $item->total_credits, 2)
                    : 0.0;

                return [
                    'academic_year' => $item->academic_year,
                    'semester' => $item->semester,
                    'gpa' => $gpa,
                    'total_credits' => $item->total_credits,
                    'earned_credits' => $item->earned_credits,
                    'total_subjects' => $item->total_subjects,
                    'passed_subjects' => $item->passed_subjects,
                    'failed_subjects' => $item->total_subjects - $item->passed_subjects,
                ];
            });

        return $grades;
    }

    /**
     * Get GPA trend (progression over semesters)
     */
    public function getGPATrend(int $studentId): array
    {
        $semesterGPAs = $this->getGPABySemester($studentId);

        return [
            'semesters' => $semesterGPAs,
            'highest_gpa' => $semesterGPAs->max('gpa') ?? 0,
            'lowest_gpa' => $semesterGPAs->min('gpa') ?? 0,
            'average_gpa' => $semesterGPAs->avg('gpa') ?? 0,
            'current_gpa' => $semesterGPAs->last()['gpa'] ?? 0,
            'trend' => $this->analyzeTrend($semesterGPAs),
        ];
    }

    /**
     * Analyze GPA trend
     */
    protected function analyzeTrend(Collection $semesterGPAs): string
    {
        if ($semesterGPAs->count() < 2) {
            return 'insufficient_data';
        }

        $last = $semesterGPAs->last();
        $previous = $semesterGPAs->slice(-2, 1)->first();

        $diff = $last['gpa'] - $previous['gpa'];

        if ($diff > 0.3) return 'improving';
        if ($diff < -0.3) return 'declining';
        return 'stable';
    }

    /**
     * Get academic standing based on GPA
     */
    public function getAcademicStanding(float $gpa): array
    {
        if ($gpa >= 3.7) {
            return [
                'standing' => 'excellent',
                'label' => 'A\'lo',
                'description' => 'O\'ta a\'lo ko\'rsatkich',
                'color' => 'success',
            ];
        } elseif ($gpa >= 3.0) {
            return [
                'standing' => 'good',
                'label' => 'Yaxshi',
                'description' => 'Yaxshi ko\'rsatkich',
                'color' => 'primary',
            ];
        } elseif ($gpa >= 2.0) {
            return [
                'standing' => 'satisfactory',
                'label' => 'Qoniqarli',
                'description' => 'Qoniqarli ko\'rsatkich',
                'color' => 'warning',
            ];
        } else {
            return [
                'standing' => 'unsatisfactory',
                'label' => 'Qoniqarsiz',
                'description' => 'Yaxshilash talab etiladi',
                'color' => 'danger',
            ];
        }
    }

    /**
     * Get subject-wise performance
     */
    public function getSubjectPerformance(int $studentId): Collection
    {
        return Grade::where('student_id', $studentId)
            ->where('is_final', true)
            ->with('subject')
            ->get()
            ->groupBy('subject_id')
            ->map(function ($grades, $subjectId) {
                $subject = $grades->first()->subject;
                $averageGrade = $grades->avg('grade');
                $averageGPA = $grades->avg('grade_point');

                return [
                    'subject_id' => $subjectId,
                    'subject_name' => $subject->name_uz ?? $subject->name,
                    'total_attempts' => $grades->count(),
                    'average_grade' => round($averageGrade, 2),
                    'average_gpa' => round($averageGPA, 2),
                    'highest_grade' => $grades->max('grade'),
                    'lowest_grade' => $grades->min('grade'),
                    'latest_grade' => $grades->sortByDesc('assessment_date')->first()->grade,
                    'total_credits' => $grades->sum('credits'),
                ];
            })
            ->sortByDesc('average_grade')
            ->values();
    }

    /**
     * Get failing subjects (qarzdorliklar)
     */
    public function getFailingSubjects(int $studentId): Collection
    {
        return Grade::where('student_id', $studentId)
            ->where('is_final', true)
            ->where('grade', '<', 55)
            ->with(['subject', 'teacher'])
            ->orderBy('assessment_date', 'desc')
            ->get()
            ->map(function ($grade) {
                return [
                    'grade_id' => $grade->id,
                    'subject_name' => $grade->subject->name_uz ?? $grade->subject->name,
                    'grade' => $grade->grade,
                    'grade_point' => $grade->grade_point,
                    'credits' => $grade->credits,
                    'academic_year' => $grade->academic_year,
                    'semester' => $grade->semester,
                    'assessment_date' => $grade->assessment_date,
                    'teacher_name' => $grade->teacher ? $grade->teacher->name : null,
                    'attempt_number' => $grade->attempt_number,
                ];
            });
    }

    /**
     * Calculate credits needed to graduate
     */
    public function getGraduationStatus(int $studentId, int $requiredCredits = 240): array
    {
        $gpaData = $this->calculateCumulativeGPA($studentId);
        $failingSubjects = $this->getFailingSubjects($studentId);

        $creditsRemaining = max(0, $requiredCredits - $gpaData['earned_credits']);
        $progressPercentage = ($gpaData['earned_credits'] / $requiredCredits) * 100;

        return [
            'earned_credits' => $gpaData['earned_credits'],
            'required_credits' => $requiredCredits,
            'credits_remaining' => $creditsRemaining,
            'progress_percentage' => round($progressPercentage, 2),
            'cumulative_gpa' => $gpaData['gpa'],
            'failing_subjects_count' => $failingSubjects->count(),
            'failing_subjects' => $failingSubjects,
            'is_eligible_for_graduation' => $creditsRemaining == 0 && $failingSubjects->isEmpty(),
            'academic_standing' => $this->getAcademicStanding($gpaData['gpa']),
        ];
    }

    /**
     * Generate transcript
     */
    public function generateTranscript(int $studentId): array
    {
        $student = Student::with(['user', 'faculty', 'group'])->findOrFail($studentId);
        $semesterGPAs = $this->getGPABySemester($studentId);
        $cumulativeGPA = $this->calculateCumulativeGPA($studentId);
        $graduationStatus = $this->getGraduationStatus($studentId);

        return [
            'student' => [
                'id' => $student->id,
                'student_id' => $student->student_id,
                'full_name' => $student->user->name,
                'faculty' => $student->faculty->name_uz ?? null,
                'group' => $student->group->name ?? null,
                'admission_date' => $student->admission_date,
            ],
            'semester_breakdown' => $semesterGPAs,
            'cumulative_gpa' => $cumulativeGPA,
            'graduation_status' => $graduationStatus,
            'generated_at' => now()->toDateTimeString(),
        ];
    }
}
