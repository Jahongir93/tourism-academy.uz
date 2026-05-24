<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Student;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class DocumentController extends Controller
{
    /**
     * Display documents page
     */
    public function index()
    {
        $user = Auth::user();
        $student = Student::with(['group.specialty', 'group.faculty'])
            ->where('user_id', $user->id)
            ->first();

        if (!$student) {
            return redirect()->route('student.dashboard')->with('error', 'Talaba ma\'lumotlari topilmadi.');
        }

        // Check document availability
        $hasCertificate = $this->checkCourseCompletion($student);
        $hasGraduated = $this->checkGraduation($student);

        return view('student.documents.index', compact('student', 'hasCertificate', 'hasGraduated'));
    }

    /**
     * Generate reference letter
     */
    public function generateReference()
    {
        $user = Auth::user();
        $student = Student::with(['group.specialty', 'group.faculty'])
            ->where('user_id', $user->id)
            ->first();

        if (!$student) {
            return redirect()->back()->with('error', 'Talaba ma\'lumotlari topilmadi.');
        }

        $data = [
            'student' => $student,
            'date' => Carbon::now()->format('d.m.Y'),
            'documentNumber' => 'REF-' . date('Y') . '-' . str_pad($student->id, 5, '0', STR_PAD_LEFT),
            'university' => 'Turizm Akademiyasi',
            'address' => 'Samarqand sh., Universitet xiyoboni',
            'rectorName' => '',
            'vicerectorName' => '',
        ];

        $pdf = PDF::loadView('student.documents.pdf.reference', $data);
        return $pdf->download('malumotnoma-' . $student->student_id . '.pdf');
    }

    /**
     * Generate academic transcript
     */
    public function generateTranscript()
    {
        $user = Auth::user();
        $student = Student::with(['group.specialty', 'group.faculty'])
            ->where('user_id', $user->id)
            ->first();

        if (!$student) {
            return redirect()->back()->with('error', 'Talaba ma\'lumotlari topilmadi.');
        }

        // Get all grades with subject information
        $gradesRaw = \App\Models\JournalGrade::with(['journalEntry.subject'])
            ->where('student_id', $student->id)
            ->where('grade_type', 'yakuniy')
            ->get();

        // Transform grades for PDF
        $grades = $gradesRaw->map(function($grade) {
            $score = $grade->score ?? 0;

            // Determine letter grade and GPA
            if ($score >= 90) {
                $letterGrade = 'A';
                $gradePoint = 4.0;
            } elseif ($score >= 80) {
                $letterGrade = 'B';
                $gradePoint = 3.0;
            } elseif ($score >= 70) {
                $letterGrade = 'C';
                $gradePoint = 2.0;
            } elseif ($score >= 60) {
                $letterGrade = 'D';
                $gradePoint = 1.0;
            } else {
                $letterGrade = 'F';
                $gradePoint = 0.0;
            }

            return (object)[
                'subject_name' => $grade->journalEntry->subject->name ?? 'N/A',
                'semester' => 1, // TODO: Get actual semester
                'credits' => 4,
                'score' => $score,
                'letter_grade' => $letterGrade,
                'grade_point' => $gradePoint,
                'graded_date' => $grade->graded_date ?? now(),
            ];
        });

        $data = [
            'student' => $student,
            'grades' => $grades,
            'date' => Carbon::now()->format('d.m.Y'),
            'gpa' => $this->calculateGPA($student->id),
            'totalCredits' => $grades->count() * 4,
            'completedCourses' => $grades->count(),
            'university' => 'Turizm Akademiyasi',
            'vicerectorName' => '',
        ];

        $pdf = PDF::loadView('student.documents.pdf.transcript', $data);
        return $pdf->download('akademik-malumotnoma-' . $student->student_id . '.pdf');
    }

    /**
     * Generate certificate
     */
    public function generateCertificate()
    {
        $user = Auth::user();
        $student = Student::with(['group.specialty', 'group.faculty'])
            ->where('user_id', $user->id)
            ->first();

        if (!$student) {
            return redirect()->back()->with('error', 'Talaba ma\'lumotlari topilmadi.');
        }

        if (!$this->checkCourseCompletion($student)) {
            return redirect()->back()->with('error', 'Siz hali kursni tugatmagansiz.');
        }

        $data = [
            'student' => $student,
            'date' => Carbon::now()->format('d.m.Y'),
            'certificateId' => 'CERT-' . date('Y') . '-' . str_pad($student->id, 5, '0', STR_PAD_LEFT),
            'courseName' => 'Turizm menejementi asoslari',
            'courseDuration' => 120,
            'finalScore' => 85,
            'completionDate' => Carbon::now()->format('d.m.Y'),
            'university' => 'Turizm Akademiyasi',
            'rectorName' => '',
            'vicerectorName' => '',
            'instructorName' => '',
            'verificationUrl' => 'hemis.uz/verify',
        ];

        $pdf = PDF::loadView('student.documents.pdf.certificate', $data);
        return $pdf->download('sertifikat-' . $student->student_id . '.pdf');
    }

    /**
     * Generate diploma
     */
    public function generateDiploma()
    {
        $user = Auth::user();
        $student = Student::with(['group.specialty', 'group.faculty'])
            ->where('user_id', $user->id)
            ->first();

        if (!$student) {
            return redirect()->back()->with('error', 'Talaba ma\'lumotlari topilmadi.');
        }

        if (!$this->checkGraduation($student)) {
            return redirect()->back()->with('error', 'Siz hali o\'qishni tugatmagansiz.');
        }

        $data = [
            'student' => $student,
            'issueDate' => Carbon::now()->format('d.m.Y'),
            'diplomaNumber' => 'DIP-' . date('Y') . '-' . str_pad($student->id, 5, '0', STR_PAD_LEFT),
            'gpa' => $this->calculateGPA($student->id),
            'studyPeriod' => '2020-2024',
            'university' => 'Turizm Akademiyasi',
            'rectorName' => '',
            'chairmanName' => '',
        ];

        $pdf = PDF::loadView('student.documents.pdf.diploma', $data);
        return $pdf->download('diplom-' . $student->student_id . '.pdf');
    }

    /**
     * Generate diploma supplement
     */
    public function generateDiplomaSupplement()
    {
        $user = Auth::user();
        $student = Student::with(['group.specialty', 'group.faculty'])
            ->where('user_id', $user->id)
            ->first();

        if (!$student) {
            return redirect()->back()->with('error', 'Talaba ma\'lumotlari topilmadi.');
        }

        if (!$this->checkGraduation($student)) {
            return redirect()->back()->with('error', 'Siz hali o\'qishni tugatmagansiz.');
        }

        // Get all grades with subject information
        $gradesRaw = \App\Models\JournalGrade::with(['journalEntry.subject'])
            ->where('student_id', $student->id)
            ->where('grade_type', 'yakuniy')
            ->get();

        // Transform grades for PDF
        $courses = $gradesRaw->map(function($grade) {
            $score = $grade->score ?? 0;

            // Determine letter grade and GPA
            if ($score >= 90) {
                $letterGrade = 'A';
                $gradePoint = 4.0;
            } elseif ($score >= 80) {
                $letterGrade = 'B';
                $gradePoint = 3.0;
            } elseif ($score >= 70) {
                $letterGrade = 'C';
                $gradePoint = 2.0;
            } elseif ($score >= 60) {
                $letterGrade = 'D';
                $gradePoint = 1.0;
            } else {
                $letterGrade = 'F';
                $gradePoint = 0.0;
            }

            return (object)[
                'name' => $grade->journalEntry->subject->name ?? 'N/A',
                'year' => '2023-2024', // TODO: Get actual year
                'credits' => 4,
                'score' => $score,
                'grade' => $letterGrade,
                'gpa' => $gradePoint,
                'date' => $grade->graded_date ? Carbon::parse($grade->graded_date)->format('d.m.Y') : Carbon::now()->format('d.m.Y'),
            ];
        });

        $data = [
            'student' => $student,
            'courses' => $courses,
            'issueDate' => Carbon::now()->format('d.m.Y'),
            'diplomaNumber' => 'DIP-' . date('Y') . '-' . str_pad($student->id, 5, '0', STR_PAD_LEFT),
            'supplementId' => 'SUP-' . date('Y') . '-' . str_pad($student->id, 5, '0', STR_PAD_LEFT),
            'gpa' => $this->calculateGPA($student->id),
            'totalCredits' => $courses->count() * 4,
            'totalCourses' => $courses->count(),
            'avgScore' => $courses->avg('score'),
            'studyPeriod' => '2020-2024',
            'duration' => '4 yil / 4 years',
            'qualification' => 'Turizm bo\'yicha bakalavr / Bachelor in Tourism',
            'university' => 'Turizm Akademiyasi',
            'vicerectorName' => '',
            'licenseNumber' => '',
            'accreditationNumber' => '',
            'advisor' => '-',
            'thesisTopic' => '-',
            'thesisGrade' => '-',
            'awards' => '-',
            'verificationUrl' => 'hemis.uz/verify',
        ];

        $pdf = PDF::loadView('student.documents.pdf.diploma-supplement', $data);
        return $pdf->download('diplom-ilovasi-' . $student->student_id . '.pdf');
    }

    /**
     * Check if course is completed
     */
    private function checkCourseCompletion($student)
    {
        // TODO: Implement actual logic
        // For now, check if student is in final year
        return $student->group && $student->group->course >= 3;
    }

    /**
     * Check if student has graduated
     */
    private function checkGraduation($student)
    {
        // TODO: Implement actual logic
        // For now, check if student status is graduated
        return $student->status == 'graduated';
    }

    /**
     * Calculate GPA
     */
    private function calculateGPA($studentId)
    {
        $grades = \App\Models\JournalGrade::where('student_id', $studentId)
            ->where('grade_type', 'yakuniy')
            ->get();

        if ($grades->isEmpty()) {
            return 0;
        }

        $totalPoints = 0;
        $totalCredits = 0;

        foreach ($grades as $grade) {
            $score = $grade->score;
            $credits = 3;

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
}
