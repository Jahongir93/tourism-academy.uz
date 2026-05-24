<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Services\GPACalculatorService;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GPAController extends Controller
{
    protected $gpaService;

    public function __construct(GPACalculatorService $gpaService)
    {
        $this->gpaService = $gpaService;
    }

    /**
     * Display GPA dashboard
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Determine student ID
        if ($user->hasRole('Student')) {
            $student = Student::where('user_id', $user->id)->first();
            if (!$student) {
                return redirect()->back()->with('error', 'Talaba profili topilmadi');
            }
            $studentId = $student->id;
        } else {
            // For teachers/admins, allow selecting student
            $studentId = $request->input('student_id') ?? $request->input('student');
            if (!$studentId) {
                // Build query with search and filters
                $query = Student::with(['user', 'group', 'faculty']);

                // Search by name or student ID
                if ($request->filled('name')) {
                    $searchTerm = $request->input('name');
                    $query->where(function($q) use ($searchTerm) {
                        $q->where('first_name', 'like', "%{$searchTerm}%")
                          ->orWhere('last_name', 'like', "%{$searchTerm}%")
                          ->orWhere('student_id_number', 'like', "%{$searchTerm}%")
                          ->orWhereHas('user', function($userQuery) use ($searchTerm) {
                              $userQuery->where('name', 'like', "%{$searchTerm}%");
                          });
                    });
                }

                // Filter by group
                if ($request->filled('group_id')) {
                    $query->where('group_id', $request->input('group_id'));
                }

                // Filter by faculty
                if ($request->filled('faculty_id')) {
                    $query->where('faculty_id', $request->input('faculty_id'));
                }

                $students = $query->orderBy('last_name', 'asc')
                                  ->orderBy('first_name', 'asc')
                                  ->paginate(24)
                                  ->withQueryString();

                // Get groups and faculties for filters
                $groups = \App\Models\Group::orderBy('name')->get();
                $faculties = \App\Models\Faculty::orderBy('name_uz')->get();

                return view('academic.gpa.select-student', compact('students', 'groups', 'faculties'));
            }
            // Cast to integer
            $studentId = (int) $studentId;
        }

        $cumulativeGPA = $this->gpaService->calculateCumulativeGPA($studentId);
        $gpaTrend = $this->gpaService->getGPATrend($studentId);
        $subjectPerformance = $this->gpaService->getSubjectPerformance($studentId);
        $failingSubjects = $this->gpaService->getFailingSubjects($studentId);
        $graduationStatus = $this->gpaService->getGraduationStatus($studentId);

        $student = Student::with(['user', 'faculty', 'group'])->findOrFail($studentId);

        return view('academic.gpa.index', compact(
            'student',
            'cumulativeGPA',
            'gpaTrend',
            'subjectPerformance',
            'failingSubjects',
            'graduationStatus'
        ));
    }

    /**
     * Calculate semester GPA
     */
    public function semesterGPA(Request $request, int $studentId)
    {
        $request->validate([
            'academic_year' => 'required|string',
            'semester' => 'required|integer|min:1|max:2',
        ]);

        $gpa = $this->gpaService->calculateSemesterGPA(
            $studentId,
            $request->academic_year,
            $request->semester
        );

        return response()->json([
            'success' => true,
            'data' => $gpa,
        ]);
    }

    /**
     * Get GPA trend
     */
    public function trend(int $studentId)
    {
        $trend = $this->gpaService->getGPATrend($studentId);

        return response()->json([
            'success' => true,
            'data' => $trend,
        ]);
    }

    /**
     * Get transcript
     */
    public function transcript(int $studentId)
    {
        $transcript = $this->gpaService->generateTranscript($studentId);

        return view('academic.gpa.transcript', compact('transcript'));
    }

    /**
     * Download transcript as PDF
     */
    public function downloadTranscript(int $studentId)
    {
        $transcript = $this->gpaService->generateTranscript($studentId);

        // TODO: Generate PDF using DomPDF or similar
        // For now, return JSON
        return response()->json($transcript);
    }

    /**
     * Get failing subjects
     */
    public function failingSubjects(int $studentId)
    {
        $failingSubjects = $this->gpaService->getFailingSubjects($studentId);

        return response()->json([
            'success' => true,
            'data' => $failingSubjects,
        ]);
    }

    /**
     * Get subject performance
     */
    public function subjectPerformance(int $studentId)
    {
        $performance = $this->gpaService->getSubjectPerformance($studentId);

        return response()->json([
            'success' => true,
            'data' => $performance,
        ]);
    }

    /**
     * Get graduation status
     */
    public function graduationStatus(int $studentId)
    {
        $status = $this->gpaService->getGraduationStatus($studentId);

        return response()->json([
            'success' => true,
            'data' => $status,
        ]);
    }
}
