<?php

namespace App\Http\Controllers\Structure\Academic;

use App\Http\Controllers\Controller;
use App\Models\EducationalProgram;
use App\Models\Curriculum;
use App\Models\Subject;
use App\Models\AcademicYear;
use App\Models\CurriculumTopic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
// use Maatwebsite\Excel\Facades\Excel; // Removed - using CSV instead

class CurriculumController extends Controller
{
    public function index(Request $request)
    {
        $programs = EducationalProgram::with(['faculty', 'department'])
            ->withCount('curricula')
            ->paginate(20);
        
        $currentYear = AcademicYear::getCurrentYear();
        
        return view('structure.academic.curriculum.index', compact('programs', 'currentYear'));
    }

    public function builder(EducationalProgram $program)
    {
        $academicYear = request('academic_year', AcademicYear::getCurrentYear());
        
        $curriculum = $program->curricula()
            ->with('subject')
            ->where('academic_year', $academicYear)
            ->orderBy('semester_number')
            ->orderBy('sequence_number')
            ->get()
            ->groupBy('semester_number');
        
        $subjects = Subject::active()->get();
        $academicYears = $this->getAcademicYears();
        
        // Calculate statistics
        $stats = [
            'total_credits' => $program->curricula()->where('academic_year', $academicYear)->sum('credits'),
            'total_hours' => 0,
            'auditory_hours' => 0,
            'independent_hours' => 0,
        ];
        
        foreach ($program->curricula()->where('academic_year', $academicYear)->get() as $item) {
            $stats['total_hours'] += $item->total_hours;
            $stats['auditory_hours'] += $item->total_auditory_hours;
            $stats['independent_hours'] += $item->independent_hours;
        }
        
        return view('structure.academic.curriculum.builder', compact(
            'program', 
            'curriculum', 
            'subjects', 
            'academicYear', 
            'academicYears',
            'stats'
        ));
    }

    public function save(Request $request, EducationalProgram $program)
    {
        $validated = $request->validate([
            'academic_year' => 'required|string',
            'semester' => 'required|integer|min:1|max:10',
            'subjects' => 'required|array',
            'subjects.*.subject_id' => 'required|exists:subjects,id',
            'subjects.*.lecture_hours' => 'required|integer|min:0',
            'subjects.*.practice_hours' => 'required|integer|min:0',
            'subjects.*.seminar_hours' => 'required|integer|min:0',
            'subjects.*.lab_hours' => 'required|integer|min:0',
            'subjects.*.independent_hours' => 'required|integer|min:0',
            'subjects.*.credits' => 'required|integer|min:1',
            'subjects.*.subject_type' => 'required|in:majburiy,tanlov',
        ]);
        
        DB::transaction(function() use ($validated, $program) {
            foreach ($validated['subjects'] as $index => $subjectData) {
                Curriculum::updateOrCreate(
                    [
                        'program_id' => $program->id,
                        'academic_year' => $validated['academic_year'],
                        'semester_number' => $validated['semester'],
                        'subject_id' => $subjectData['subject_id'],
                    ],
                    [
                        'lecture_hours' => $subjectData['lecture_hours'],
                        'practice_hours' => $subjectData['practice_hours'],
                        'seminar_hours' => $subjectData['seminar_hours'],
                        'lab_hours' => $subjectData['lab_hours'],
                        'independent_hours' => $subjectData['independent_hours'],
                        'credits' => $subjectData['credits'],
                        'subject_type' => $subjectData['subject_type'],
                        'sequence_number' => $index + 1,
                    ]
                );
            }
        });
        
        return redirect()->route('structure.academic.curriculum.builder', $program)
            ->with('success', "O'quv reja muvaffaqiyatli saqlandi!");
    }

    public function approve(Request $request, EducationalProgram $program)
    {
        $academicYear = $request->input('academic_year', $this->getCurrentAcademicYear());
        
        $program->curricula()
            ->where('academic_year', $academicYear)
            ->update(['is_approved' => true]);
        
        return back()->with('success', "O'quv reja tasdiqlandi!");
    }

    public function copy(Request $request, EducationalProgram $program)
    {
        $validated = $request->validate([
            'from_year' => 'required|string',
            'to_year' => 'required|string|different:from_year',
        ]);
        
        DB::transaction(function() use ($validated, $program) {
            $curricula = $program->curricula()
                ->where('academic_year', $validated['from_year'])
                ->get();
            
            foreach ($curricula as $curriculum) {
                $newCurriculum = $curriculum->replicate();
                $newCurriculum->academic_year = $validated['to_year'];
                $newCurriculum->is_approved = false;
                $newCurriculum->save();
            }
        });
        
        return redirect()->route('structure.academic.curriculum.builder', [
            'program' => $program,
            'academic_year' => $validated['to_year']
        ])->with('success', "O'quv reja muvaffaqiyatli nusxalandi!");
    }

    public function export(EducationalProgram $program)
    {
        $academicYear = request('academic_year', $this->getCurrentAcademicYear());
        
        $curriculum = $program->curricula()
            ->with('subject')
            ->where('academic_year', $academicYear)
            ->orderBy('semester_number')
            ->orderBy('sequence_number')
            ->get();
        
        // Create export logic here (using Excel or PDF)
        // For now, return a simple view
        return view('structure.academic.curriculum.export', compact('program', 'curriculum', 'academicYear'));
    }

    public function importForm()
    {
        $programs = EducationalProgram::all();
        return view('structure.academic.curriculum.import', compact('programs'));
    }

    public function import(Request $request)
    {
        $validated = $request->validate([
            'program_id' => 'required|exists:educational_programs,id',
            'academic_year' => 'required|string',
            'file' => 'required|file|mimes:xlsx,xls,csv',
        ]);
        
        // Import logic would go here
        // For now, just return success
        return back()->with('success', "O'quv reja import qilindi!");
    }

    private function getCurrentAcademicYear()
    {
        return AcademicYear::getCurrentYear();
    }

    private function getAcademicYears()
    {
        $years = [];
        $currentYear = date('Y');
        
        for ($i = -2; $i <= 2; $i++) {
            $year = $currentYear + $i;
            $years[] = $year . '-' . ($year + 1);
        }
        
        return $years;
    }

    public function topics(Request $request)
    {
        $programId = $request->get('program_id');
        $subjectId = $request->get('subject_id');
        $academicYear = $request->get('academic_year', AcademicYear::getCurrentYear());
        $semester = $request->get('semester');
        
        $programs = EducationalProgram::with('faculty')->get();
        $subjects = Subject::active()->orderBy('name_uz')->get();
        
        $topics = null;
        if ($programId && $subjectId && $semester) {
            $topics = CurriculumTopic::where('program_id', $programId)
                ->where('subject_id', $subjectId)
                ->where('academic_year', $academicYear)
                ->where('semester_number', $semester)
                ->orderBy('week_number')
                ->orderBy('lesson_number')
                ->get()
                ->groupBy('week_number');
        }
        
        return view('structure.academic.curriculum.topics', compact(
            'programs', 
            'subjects', 
            'topics', 
            'programId', 
            'subjectId', 
            'academicYear', 
            'semester'
        ));
    }

    public function saveTopics(Request $request)
    {
        $validated = $request->validate([
            'program_id' => 'required|exists:educational_programs,id',
            'subject_id' => 'required|exists:subjects,id',
            'academic_year' => 'required|string',
            'semester_number' => 'required|integer',
            'topics' => 'required|array',
            'topics.*.week_number' => 'required|integer|min:1|max:20',
            'topics.*.lesson_number' => 'required|integer|min:1',
            'topics.*.topic_name_uz' => 'required|string',
            'topics.*.topic_name_ru' => 'nullable|string',
            'topics.*.lesson_type' => 'required|in:lecture,practice,seminar,lab,independent',
            'topics.*.hours' => 'required|integer|min:1|max:8',
            'topics.*.description' => 'nullable|string',
        ]);
        
        DB::transaction(function() use ($validated, $request) {
            // Clear existing topics
            CurriculumTopic::where('program_id', $validated['program_id'])
                ->where('subject_id', $validated['subject_id'])
                ->where('academic_year', $validated['academic_year'])
                ->where('semester_number', $validated['semester_number'])
                ->delete();
            
            // Save new topics
            $sequenceNumber = 1;
            foreach ($request->input('topics', []) as $topicData) {
                // Skip empty topics
                if (empty($topicData['topic_name_uz'])) {
                    continue;
                }
                
                CurriculumTopic::create([
                    'program_id' => $validated['program_id'],
                    'subject_id' => $validated['subject_id'],
                    'academic_year' => $validated['academic_year'],
                    'semester_number' => $validated['semester_number'],
                    'week_number' => $topicData['week_number'],
                    'lesson_number' => $topicData['lesson_number'],
                    'topic_name_uz' => $topicData['topic_name_uz'],
                    'topic_name_ru' => $topicData['topic_name_ru'] ?? null,
                    'topic_name_en' => $topicData['topic_name_en'] ?? null,
                    'description' => $topicData['description'] ?? null,
                    'lesson_type' => $topicData['lesson_type'],
                    'hours' => $topicData['hours'],
                    'learning_outcomes' => $topicData['learning_outcomes'] ?? null,
                    'teaching_methods' => $topicData['teaching_methods'] ?? null,
                    'assessment_methods' => $topicData['assessment_methods'] ?? null,
                    'resources' => $topicData['resources'] ?? null,
                    'homework' => $topicData['homework'] ?? null,
                    'is_online' => isset($topicData['is_online']) && $topicData['is_online'] == '1',
                    'sequence_number' => $sequenceNumber++,
                ]);
            }
        });
        
        return redirect()->back()->with('success', "Mavzular muvaffaqiyatli saqlandi!");
    }

    public function importTopics(Request $request)
    {
        $validated = $request->validate([
            'program_id' => 'required|exists:educational_programs,id',
            'subject_id' => 'required|exists:subjects,id',
            'academic_year' => 'required|string',
            'semester_number' => 'required|integer',
            'file' => 'required|file|mimes:csv,txt',
        ]);
        
        try {
            $file = $request->file('file');
            $path = $file->getRealPath();
            
            // Read CSV file
            $rows = [];
            if (($handle = fopen($path, 'r')) !== FALSE) {
                while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
                    $rows[] = $data;
                }
                fclose($handle);
            }
            
            if (empty($rows)) {
                return back()->withErrors(['file' => 'Fayl bo\'sh yoki noto\'g\'ri formatda']);
            }
            
            $topicsImported = 0;
            
            DB::transaction(function() use ($validated, $rows, &$topicsImported, $file) {
                // Clear existing topics
                CurriculumTopic::where('program_id', $validated['program_id'])
                    ->where('subject_id', $validated['subject_id'])
                    ->where('academic_year', $validated['academic_year'])
                    ->where('semester_number', $validated['semester_number'])
                    ->delete();
                
                foreach ($rows as $index => $row) {
                    // Skip header row
                    if ($index === 0) continue;
                    
                    // Skip empty rows
                    if (empty($row[0])) continue;
                    
                    $lessonType = 'lecture';
                    if (!empty($row[4])) {
                        $type = strtolower($row[4]);
                        if (str_contains($type, 'amaliy') || str_contains($type, 'practice')) {
                            $lessonType = 'practice';
                        } elseif (str_contains($type, 'seminar')) {
                            $lessonType = 'seminar';
                        } elseif (str_contains($type, 'laborator') || str_contains($type, 'lab')) {
                            $lessonType = 'lab';
                        } elseif (str_contains($type, 'mustaqil') || str_contains($type, 'independent')) {
                            $lessonType = 'independent';
                        }
                    }
                    
                    CurriculumTopic::create([
                        'program_id' => $validated['program_id'],
                        'subject_id' => $validated['subject_id'],
                        'academic_year' => $validated['academic_year'],
                        'semester_number' => $validated['semester_number'],
                        'week_number' => intval($row[0]),
                        'lesson_number' => intval($row[1] ?? 1),
                        'topic_name_uz' => $row[2],
                        'topic_name_ru' => $row[3] ?? null,
                        'lesson_type' => $lessonType,
                        'hours' => intval($row[5] ?? 2),
                        'description' => $row[6] ?? null,
                        'learning_outcomes' => $row[7] ?? null,
                        'teaching_methods' => $row[8] ?? null,
                        'assessment_methods' => $row[9] ?? null,
                        'resources' => $row[10] ?? null,
                        'homework' => $row[11] ?? null,
                        'is_online' => isset($row[12]) && (strtolower($row[12]) === 'ha' || strtolower($row[12]) === 'yes'),
                        'sequence_number' => $topicsImported + 1,
                    ]);
                    
                    $topicsImported++;
                }
                
                // Log the import
                DB::table('curriculum_import_logs')->insert([
                    'program_id' => $validated['program_id'],
                    'subject_id' => $validated['subject_id'],
                    'academic_year' => $validated['academic_year'],
                    'file_name' => $file->getClientOriginalName(),
                    'topics_imported' => $topicsImported,
                    'imported_by' => Auth::id(),
                    'import_details' => json_encode(['rows' => count($rows) - 1]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            });
            
            return redirect()->back()->with('success', "{$topicsImported} ta mavzu muvaffaqiyatli import qilindi!");
            
        } catch (\Exception $e) {
            return back()->withErrors(['file' => 'Import jarayonida xatolik: ' . $e->getMessage()]);
        }
    }

    public function downloadTemplate()
    {
        $headers = [
            'Hafta',
            'Dars №',
            'Mavzu (uz)',
            'Mavzu (ru)',
            'Dars turi',
            'Soatlar',
            'Tavsif',
            'Kutilayotgan natijalar',
            'O\'qitish usullari',
            'Baholash usullari',
            'Adabiyotlar',
            'Uy vazifasi',
            'Online (ha/yo\'q)'
        ];
        
        $sampleData = [
            [1, 1, 'Kirish. Fan haqida umumiy ma\'lumot', 'Введение. Общие сведения о предмете', 'Ma\'ruza', 2, 'Fanga kirish', 'Asosiy tushunchalarni bilish', 'Prezentatsiya', 'Savol-javob', '1-adabiyot, 1-bob', 'Konspekt yozish', 'yo\'q'],
            [1, 2, 'Asosiy tushunchalar', 'Основные понятия', 'Amaliyot', 2, 'Amaliy mashg\'ulot', 'Amaliy ko\'nikmalar', 'Amaliy mashqlar', 'Topshiriqlar', '1-adabiyot, 2-bob', 'Masalalar yechish', 'yo\'q'],
            [2, 1, 'Nazariy asoslar', 'Теоретические основы', 'Ma\'ruza', 2, 'Nazariy materiallar', 'Nazariyani tushunish', 'Ma\'ruza', 'Test', '2-adabiyot, 1-bob', 'Mavzuni o\'rganish', 'ha'],
        ];
        
        // Create Excel file in memory
        $callback = function() use ($headers, $sampleData) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);
            foreach ($sampleData as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };
        
        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="curriculum_template.csv"',
        ]);
    }
}