<?php

namespace App\Http\Controllers\Academic;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Group;
use App\Models\AcademicGroup;
use App\Models\AcademicYear;
use App\Models\VedomostSheet;
use App\Models\VedomostAssessmentColumn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Style\Font;
use PhpOffice\PhpWord\Style\Table;
use PhpOffice\PhpWord\SimpleType\Jc;

class VedomostController extends Controller
{
    public function __construct()
    {
        // Middleware applied at route level
    }

    /**
     * Display list of grade sheets
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Vedomost varaqlarini olish
        $query = VedomostSheet::with(['group', 'subject', 'teacher', 'academicYear'])
            ->where('is_active', true);

        // Filter by role - faqat Teacher rolida filter qo'llanadi
        if ($user && $user->hasRole('Teacher') && !$user->hasRole('Admin')) {
            $query->where('teacher_id', $user->id);
        }

        // Apply filters
        if ($request->filled('academic_year_id')) {
            $query->where('academic_year_id', $request->academic_year_id);
        }

        if ($request->filled('semester')) {
            $query->where('semester', $request->semester);
        }

        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        if ($request->filled('group_id')) {
            $query->where('group_id', $request->group_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $vedomosts = $query->orderBy('created_at', 'desc')->paginate(20);

        // Pre-compute student counts — one query instead of per-vedomost
        $vedomostGroupIds = $vedomosts->pluck('group_id')->filter()->unique()->toArray();
        $vedomostStudentCounts = Student::whereIn('group_id', $vedomostGroupIds)
            ->selectRaw('group_id, COUNT(*) as cnt')
            ->groupBy('group_id')
            ->pluck('cnt', 'group_id');

        foreach ($vedomosts as $vedomost) {
            $vedomost->completion_percentage = $vedomost->getCompletionPercentage();
            $vedomost->students_count = $vedomostStudentCounts[$vedomost->group_id] ?? 0;
        }

        // Get filter options
        $academicYears = AcademicYear::orderBy('name', 'desc')->get();

        $subjects = Subject::where('is_active', true)
            ->orderBy('name_uz')
            ->get();

        $groups = Group::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('academic.vedomost.index', compact('vedomosts', 'academicYears', 'subjects', 'groups'));
    }

    /**
     * Fill/Edit vedomost sheet
     */
    public function fill($id)
    {
        $vedomost = VedomostSheet::with(['group', 'subject', 'teacher', 'academicYear', 'assessmentColumns', 'grades'])
            ->findOrFail($id);

        // Get students for this group
        $students = $vedomost->getStudents();

        // Get existing grades
        $existingGrades = [];
        foreach ($vedomost->grades as $grade) {
            $existingGrades[$grade->student_id] = $grade;
        }

        return view('academic.vedomost.fill', compact('vedomost', 'students', 'existingGrades'));
    }

    /**
     * Save grades for vedomost sheet
     */
    public function saveFill(Request $request, $id)
    {
        $vedomost = VedomostSheet::with('assessmentColumns')->findOrFail($id);

        $request->validate([
            'grades' => 'required|array',
            'grades.*.student_id' => 'required|integer',
            'grades.*.current_grade' => 'nullable|numeric|min:0|max:100',
            'grades.*.final_grade' => 'nullable|numeric|min:0|max:100',
            'grades.*.column_*' => 'nullable|numeric|min:0|max:100',
        ]);

        $savedCount = 0;

        foreach ($request->grades as $gradeData) {
            $studentId = $gradeData['student_id'];

            // Save current grade (joriy nazorat)
            if (!empty($gradeData['current_grade'])) {
                $gradeValue = $gradeData['current_grade'];
                $gradePoint = Grade::calculateGradePoint($gradeValue);
                $letterGrade = Grade::getLetterGrade($gradeValue);

                Grade::updateOrCreate(
                    [
                        'vedomost_sheet_id' => $vedomost->id,
                        'student_id' => $studentId,
                        'is_final' => false,
                        'assessment_column_id' => null,
                    ],
                    [
                        'subject_id' => $vedomost->subject_id,
                        'academic_year' => $vedomost->academicYear->name,
                        'semester' => $vedomost->semester,
                        'grade' => $gradeValue,
                        'grade_point' => $gradePoint,
                        'letter_grade' => $letterGrade,
                        'credits' => $vedomost->credits,
                        'assessment_type' => 'current',
                        'assessment_date' => now(),
                        'teacher_id' => $vedomost->teacher_id,
                        'is_retake' => false,
                        'course' => $vedomost->group->course ?? 1,
                        'attempt_number' => 1,
                        'comments' => $gradeData['comments'] ?? null,
                    ]
                );
                $savedCount++;
            }

            // Save final grade (yakuniy nazorat)
            if (!empty($gradeData['final_grade'])) {
                $gradeValue = $gradeData['final_grade'];
                $gradePoint = Grade::calculateGradePoint($gradeValue);
                $letterGrade = Grade::getLetterGrade($gradeValue);

                Grade::updateOrCreate(
                    [
                        'vedomost_sheet_id' => $vedomost->id,
                        'student_id' => $studentId,
                        'is_final' => true,
                        'assessment_column_id' => null,
                    ],
                    [
                        'subject_id' => $vedomost->subject_id,
                        'academic_year' => $vedomost->academicYear->name,
                        'semester' => $vedomost->semester,
                        'grade' => $gradeValue,
                        'grade_point' => $gradePoint,
                        'letter_grade' => $letterGrade,
                        'credits' => $vedomost->credits,
                        'assessment_type' => $vedomost->assessment_type,
                        'assessment_date' => $vedomost->assessment_date ?? now(),
                        'teacher_id' => $vedomost->teacher_id,
                        'is_retake' => false,
                        'course' => $vedomost->group->course ?? 1,
                        'attempt_number' => 1,
                        'comments' => $gradeData['comments'] ?? null,
                    ]
                );
                $savedCount++;
            }

            // Save grades for dynamic columns
            foreach ($vedomost->assessmentColumns as $column) {
                $columnKey = 'column_' . $column->id;
                if (!empty($gradeData[$columnKey])) {
                    $gradeValue = $gradeData[$columnKey];
                    $gradePoint = Grade::calculateGradePoint($gradeValue);
                    $letterGrade = Grade::getLetterGrade($gradeValue);

                    Grade::updateOrCreate(
                        [
                            'vedomost_sheet_id' => $vedomost->id,
                            'student_id' => $studentId,
                            'assessment_column_id' => $column->id,
                        ],
                        [
                            'subject_id' => $vedomost->subject_id,
                            'academic_year' => $vedomost->academicYear->name,
                            'semester' => $vedomost->semester,
                            'grade' => $gradeValue,
                            'grade_point' => $gradePoint,
                            'letter_grade' => $letterGrade,
                            'credits' => $vedomost->credits,
                            'assessment_type' => $column->name,
                            'assessment_date' => now(),
                            'teacher_id' => $vedomost->teacher_id,
                            'is_retake' => false,
                            'is_final' => $column->is_final,
                            'course' => $vedomost->group->course ?? 1,
                            'attempt_number' => 1,
                            'comments' => null,
                        ]
                    );
                    $savedCount++;
                }
            }
        }

        // Update vedomost status
        if ($vedomost->isComplete()) {
            $vedomost->update(['status' => 'in_progress']);
        }

        return redirect()->route('vedomost.fill', $vedomost->id)
            ->with('success', "{$savedCount} ta baho saqlandi!");
    }

    /**
     * Show grade sheet for a specific group and subject
     */
    public function show(Request $request)
    {
        $request->validate([
            'group_id' => 'required|integer',
            'subject_id' => 'required|integer',
            'academic_year' => 'nullable|string',
            'semester' => 'nullable|integer|min:1|max:2',
        ]);

        $group = Group::with(['students', 'faculty', 'specialty'])->find($request->group_id);
        if (!$group) {
            return redirect()->route('vedomost.index')->with('error', 'Guruh topilmadi');
        }

        $subject = Subject::find($request->subject_id);
        if (!$subject) {
            return redirect()->route('vedomost.index')->with('error', 'Fan topilmadi');
        }

        $students = Student::where('group_id', $group->id)->orderBy('last_name')->get();

        // Get grades for each student
        $gradesData = [];
        foreach ($students as $student) {
            $gradeQuery = Grade::where('student_id', $student->id)
                ->where('subject_id', $subject->id);

            if ($request->academic_year) {
                $gradeQuery->where('academic_year', $request->academic_year);
            }
            if ($request->semester) {
                $gradeQuery->where('semester', $request->semester);
            }

            $grade = $gradeQuery->where('is_final', true)->first();

            $gradesData[] = [
                'student' => $student,
                'grade' => $grade,
            ];
        }

        return view('academic.vedomost.show', compact(
            'group',
            'subject',
            'gradesData',
            'request'
        ));
    }

    /**
     * Create/Edit grade sheet
     */
    public function create(Request $request)
    {
        // Use Group model (groups table) where students are linked
        $groups = Group::where('is_active', true)
            ->orderBy('name')
            ->get();

        $subjects = Subject::where('is_active', true)
            ->orderBy('name_uz')
            ->get();

        $academicYears = AcademicYear::orderBy('id', 'desc')->get();

        return view('academic.vedomost.create', compact('groups', 'subjects', 'academicYears'));
    }

    /**
     * Store or update grades
     */
    public function store(Request $request)
    {
        // Sodda validatsiya - exists tekshiruvisiz
        $request->validate([
            'group_id' => 'required|integer',
            'subject_id' => 'required|integer',
            'academic_year' => 'required|string',
            'semester' => 'required|integer|min:1|max:2',
            'assessment_type' => 'required|string',
            'assessment_date' => 'required|date',
            'credits' => 'required|integer|min:0',
            'grades' => 'required|array',
        ]);

        // Guruh va fan mavjudligini tekshirish
        $group = Group::find($request->group_id);
        if (!$group) {
            return redirect()->back()->withInput()->with('error', 'Guruh topilmadi');
        }

        $subject = Subject::find($request->subject_id);
        if (!$subject) {
            return redirect()->back()->withInput()->with('error', 'Fan topilmadi');
        }

        $user = Auth::user();

        DB::beginTransaction();
        try {
            $savedCount = 0;
            foreach ($request->grades as $gradeData) {
                if (empty($gradeData['grade'])) continue;

                $gradeValue = (float) $gradeData['grade'];
                $gradePoint = Grade::calculateGradePoint($gradeValue);
                $letterGrade = Grade::getLetterGrade($gradeValue);

                Grade::updateOrCreate(
                    [
                        'student_id' => $gradeData['student_id'],
                        'subject_id' => $request->subject_id,
                        'academic_year' => $request->academic_year,
                        'semester' => $request->semester,
                        'attempt_number' => 1,
                    ],
                    [
                        'grade' => $gradeValue,
                        'grade_point' => $gradePoint,
                        'letter_grade' => $letterGrade,
                        'credits' => $request->credits,
                        'assessment_type' => $request->assessment_type,
                        'assessment_date' => $request->assessment_date,
                        'teacher_id' => $user->id,
                        'comments' => $gradeData['comments'] ?? null,
                        'is_retake' => false,
                        'is_final' => true,
                        'course' => $group->course ?? 1,
                    ]
                );
                $savedCount++;
            }

            DB::commit();

            return redirect()->route('vedomost.index')
                ->with('success', "$savedCount ta baho muvaffaqiyatli saqlandi");

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Xatolik: ' . $e->getMessage());
        }
    }

    /**
     * Get students for a group via AJAX
     */
    public function getGroupStudents(int $groupId)
    {
        $students = Student::where('group_id', $groupId)
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get()
            ->map(function($student) {
                $fullName = $student->full_name
                    ?? trim($student->last_name . ' ' . $student->first_name . ' ' . ($student->middle_name ?? ''))
                    ?: ($student->user ? $student->user->name : 'Noma\'lum');

                return [
                    'id' => $student->id,
                    'student_id' => $student->student_id,
                    'name' => $fullName,
                    'full_name' => $fullName,
                    'email' => $student->email,
                ];
            });

        return response()->json([
            'success' => true,
            'students' => $students,
        ]);
    }

    /**
     * Export grade sheet to Excel
     */
    public function export(Request $request)
    {
        $request->validate([
            'group_id' => 'nullable|integer',
            'subject_id' => 'nullable|integer',
            'academic_year' => 'nullable|string',
            'semester' => 'nullable|integer',
        ]);

        // TODO: Implement Excel export using Laravel Excel package

        return response()->json([
            'success' => false,
            'message' => 'Excel export hali amalga oshirilmagan',
        ]);
    }

    /**
     * Print grade sheet
     */
    public function print(Request $request)
    {
        $request->validate([
            'group_id' => 'required|integer',
            'subject_id' => 'required|integer',
            'academic_year' => 'nullable|string',
            'semester' => 'nullable|integer',
        ]);

        $group = Group::with(['students', 'faculty', 'specialty'])->find($request->group_id);
        if (!$group) {
            return redirect()->route('vedomost.index')->with('error', 'Guruh topilmadi');
        }

        $subject = Subject::find($request->subject_id);
        if (!$subject) {
            return redirect()->route('vedomost.index')->with('error', 'Fan topilmadi');
        }

        $students = Student::where('group_id', $group->id)->orderBy('last_name')->get();

        $gradesData = [];
        foreach ($students as $student) {
            $gradeQuery = Grade::where('student_id', $student->id)
                ->where('subject_id', $subject->id);

            if ($request->academic_year) {
                $gradeQuery->where('academic_year', $request->academic_year);
            }
            if ($request->semester) {
                $gradeQuery->where('semester', $request->semester);
            }

            $grade = $gradeQuery->where('is_final', true)->first();

            $gradesData[] = [
                'student' => $student,
                'grade' => $grade,
            ];
        }

        return view('academic.vedomost.print', compact(
            'group',
            'subject',
            'gradesData',
            'request'
        ));
    }

    /**
     * Get statistics for vedomost
     */
    public function statistics(Request $request)
    {
        $request->validate([
            'subject_id' => 'nullable|integer',
            'academic_year' => 'nullable|string',
            'semester' => 'nullable|integer',
        ]);

        $query = Grade::where('is_final', true);

        if ($request->subject_id) {
            $query->where('subject_id', $request->subject_id);
        }
        if ($request->academic_year) {
            $query->where('academic_year', $request->academic_year);
        }
        if ($request->semester) {
            $query->where('semester', $request->semester);
        }

        $stats = $query
            ->select(
                DB::raw('COUNT(*) as total_students'),
                DB::raw('AVG(grade) as average_grade'),
                DB::raw('AVG(grade_point) as average_gpa'),
                DB::raw('MAX(grade) as highest_grade'),
                DB::raw('MIN(grade) as lowest_grade'),
                DB::raw('SUM(CASE WHEN grade >= 86 THEN 1 ELSE 0 END) as excellent_count'),
                DB::raw('SUM(CASE WHEN grade >= 71 AND grade < 86 THEN 1 ELSE 0 END) as good_count'),
                DB::raw('SUM(CASE WHEN grade >= 55 AND grade < 71 THEN 1 ELSE 0 END) as satisfactory_count'),
                DB::raw('SUM(CASE WHEN grade < 55 THEN 1 ELSE 0 END) as unsatisfactory_count')
            )
            ->first();

        return response()->json([
            'success' => true,
            'statistics' => $stats,
        ]);
    }

    /**
     * Add new assessment column to vedomost
     */
    public function addColumn(Request $request, $id)
    {
        $vedomost = VedomostSheet::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'column_type' => 'required|in:numeric,letter,text',
            'max_score' => 'required|integer|min:1|max:100',
            'is_final' => 'boolean',
        ]);

        // Get max order for this vedomost
        $maxOrder = VedomostAssessmentColumn::where('vedomost_sheet_id', $id)->max('order') ?? 0;

        $column = VedomostAssessmentColumn::create([
            'vedomost_sheet_id' => $id,
            'name' => $request->name,
            'column_type' => $request->column_type,
            'max_score' => $request->max_score,
            'is_final' => $request->boolean('is_final', false),
            'order' => $maxOrder + 1,
            'is_active' => true,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Ustun qo\'shildi',
                'column' => $column,
            ]);
        }

        return redirect()->route('vedomost.fill', $id)
            ->with('success', 'Yangi ustun qo\'shildi!');
    }

    /**
     * Remove assessment column from vedomost
     */
    public function removeColumn($vedomostId, $columnId)
    {
        $column = VedomostAssessmentColumn::where('vedomost_sheet_id', $vedomostId)
            ->where('id', $columnId)
            ->firstOrFail();

        // Delete associated grades
        Grade::where('assessment_column_id', $columnId)->delete();

        $column->delete();

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Ustun o\'chirildi',
            ]);
        }

        return redirect()->route('vedomost.fill', $vedomostId)
            ->with('success', 'Ustun o\'chirildi!');
    }

    /**
     * Export vedomost to Word document
     */
    public function exportWord($id)
    {
        $vedomost = VedomostSheet::with(['group', 'subject', 'teacher', 'academicYear', 'assessmentColumns', 'grades'])
            ->findOrFail($id);

        $students = $vedomost->getStudents();

        // Create new PHPWord object
        $phpWord = new PhpWord();
        $section = $phpWord->addSection();

        // Set default font
        $phpWord->setDefaultFontName('Times New Roman');
        $phpWord->setDefaultFontSize(12);

        // Header
        $section->addText(
            'O\'ZBEKISTON RESPUBLIKASI',
            ['bold' => true, 'size' => 14],
            ['alignment' => Jc::CENTER]
        );
        $section->addText(
            'OLIY TA\'LIM, FAN VA INNOVATSIYALAR VAZIRLIGI',
            ['bold' => true, 'size' => 14],
            ['alignment' => Jc::CENTER]
        );
        $section->addText(
            strtoupper($vedomost->group->faculty->name ?? 'Turizm akademiyasi'),
            ['bold' => true, 'size' => 14],
            ['alignment' => Jc::CENTER]
        );
        $section->addTextBreak(1);

        // Title
        $section->addText(
            'BAHOLASH VEDOMOSTI',
            ['bold' => true, 'size' => 16],
            ['alignment' => Jc::CENTER]
        );
        $section->addTextBreak(1);

        // Info section
        $section->addText("Fan nomi: " . $vedomost->subject->name);
        $section->addText("Guruh: " . $vedomost->group->name);
        $section->addText("O'qituvchi: " . $vedomost->teacher->name);
        $section->addText("O'quv yili: " . $vedomost->academicYear->name . " / " . $vedomost->semester . "-semestr");
        $section->addText("Kreditlar: " . $vedomost->credits);
        $section->addText("Nazorat turi: " . ucfirst($vedomost->assessment_type));
        $section->addTextBreak(1);

        // Create table
        $tableStyle = [
            'borderSize' => 6,
            'borderColor' => '000000',
            'cellMargin' => 50,
            'alignment' => Jc::CENTER,
        ];

        $phpWord->addTableStyle('VedomostTable', $tableStyle);
        $table = $section->addTable('VedomostTable');

        // Table header
        $table->addRow(500);
        $table->addCell(800, ['vMerge' => 'restart', 'valign' => 'center'])->addText('№', ['bold' => true], ['alignment' => Jc::CENTER]);
        $table->addCell(4000, ['vMerge' => 'restart', 'valign' => 'center'])->addText('F.I.SH', ['bold' => true], ['alignment' => Jc::CENTER]);

        // Joriy nazorat
        $cell = $table->addCell(2000, ['gridSpan' => 2, 'valign' => 'center']);
        $cell->addText('Joriy nazorat', ['bold' => true], ['alignment' => Jc::CENTER]);

        // Dynamic columns
        foreach ($vedomost->assessmentColumns as $column) {
            $cell = $table->addCell(2000, ['gridSpan' => 2, 'valign' => 'center']);
            $cell->addText($column->name, ['bold' => true], ['alignment' => Jc::CENTER]);
        }

        // Yakuniy nazorat
        $cell = $table->addCell(2000, ['gridSpan' => 2, 'valign' => 'center']);
        $cell->addText('Yakuniy nazorat', ['bold' => true], ['alignment' => Jc::CENTER]);

        $table->addCell(2000, ['vMerge' => 'restart', 'valign' => 'center'])->addText('Izoh', ['bold' => true], ['alignment' => Jc::CENTER]);

        // Second header row
        $table->addRow(400);
        $table->addCell(null, ['vMerge' => 'continue']);
        $table->addCell(null, ['vMerge' => 'continue']);
        $table->addCell(1000)->addText('Baho', ['bold' => true], ['alignment' => Jc::CENTER]);
        $table->addCell(1000)->addText('Harf', ['bold' => true], ['alignment' => Jc::CENTER]);

        foreach ($vedomost->assessmentColumns as $column) {
            $table->addCell(1000)->addText('Baho', ['bold' => true], ['alignment' => Jc::CENTER]);
            $table->addCell(1000)->addText('Harf', ['bold' => true], ['alignment' => Jc::CENTER]);
        }

        $table->addCell(1000)->addText('Baho', ['bold' => true], ['alignment' => Jc::CENTER]);
        $table->addCell(1000)->addText('Harf', ['bold' => true], ['alignment' => Jc::CENTER]);
        $table->addCell(null, ['vMerge' => 'continue']);

        // Table data
        foreach ($students as $index => $student) {
            $table->addRow(400);
            $table->addCell(800)->addText($index + 1, [], ['alignment' => Jc::CENTER]);
            $table->addCell(4000)->addText($student->full_name);

            // Find grades for this student
            $currentGrade = null;
            $finalGrade = null;
            $columnGrades = [];

            foreach ($vedomost->grades as $grade) {
                if ($grade->student_id == $student->id) {
                    if ($grade->assessment_column_id) {
                        $columnGrades[$grade->assessment_column_id] = $grade;
                    } elseif ($grade->is_final) {
                        $finalGrade = $grade;
                    } else {
                        $currentGrade = $grade;
                    }
                }
            }

            // Current grade
            $table->addCell(1000)->addText(
                $currentGrade ? number_format($currentGrade->grade, 0) : '',
                [],
                ['alignment' => Jc::CENTER]
            );
            $table->addCell(1000)->addText(
                $currentGrade ? $currentGrade->letter_grade : '',
                [],
                ['alignment' => Jc::CENTER]
            );

            // Dynamic column grades
            foreach ($vedomost->assessmentColumns as $column) {
                $colGrade = isset($columnGrades[$column->id]) ? $columnGrades[$column->id] : null;
                $table->addCell(1000)->addText(
                    $colGrade ? number_format($colGrade->grade, 0) : '',
                    [],
                    ['alignment' => Jc::CENTER]
                );
                $table->addCell(1000)->addText(
                    $colGrade ? $colGrade->letter_grade : '',
                    [],
                    ['alignment' => Jc::CENTER]
                );
            }

            // Final grade
            $table->addCell(1000)->addText(
                $finalGrade ? number_format($finalGrade->grade, 0) : '',
                [],
                ['alignment' => Jc::CENTER]
            );
            $table->addCell(1000)->addText(
                $finalGrade ? $finalGrade->letter_grade : '',
                [],
                ['alignment' => Jc::CENTER]
            );

            // Comments
            $comments = $finalGrade ? $finalGrade->comments : ($currentGrade ? $currentGrade->comments : '');
            $table->addCell(2000)->addText($comments ?? '');
        }

        // Signature section
        $section->addTextBreak(2);

        $section->addText(
            'O\'qituvchi: _____________________ (' . $vedomost->teacher->name . ')',
            [],
            ['spaceAfter' => 200]
        );

        $section->addText(
            'Kafedra mudiri: _____________________ _____________________',
            [],
            ['spaceAfter' => 200]
        );

        $section->addText(
            'Dekan: _____________________ _____________________',
            [],
            ['spaceAfter' => 200]
        );

        $section->addTextBreak(1);
        $section->addText(
            'Sana: "___" __________ ' . date('Y') . ' yil',
            []
        );

        // Save document
        $fileName = 'Vedomost_' . $vedomost->group->name . '_' . str_replace(' ', '_', $vedomost->subject->name) . '.docx';
        $tempFile = tempnam(sys_get_temp_dir(), 'vedomost');

        $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
        $objWriter->save($tempFile);

        return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
    }
}
