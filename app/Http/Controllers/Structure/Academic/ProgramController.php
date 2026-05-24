<?php

namespace App\Http\Controllers\Structure\Academic;

use App\Http\Controllers\Controller;
use App\Models\EducationalProgram;
use App\Models\Specialty;
use App\Models\Faculty;
use App\Models\Department;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProgramController extends Controller
{
    public function index(Request $request)
    {
        // Get data from both EducationalProgram and Specialty tables
        $eduProgramsQuery = EducationalProgram::with(['faculty', 'department']);
        $specialtiesQuery = Specialty::with(['faculty', 'department']);

        // Apply faculty filter
        if ($request->has('faculty_id')) {
            $eduProgramsQuery->where('faculty_id', $request->faculty_id);
            $specialtiesQuery->where('faculty_id', $request->faculty_id);
        }

        // Apply level filter (degree for specialties)
        if ($request->has('level')) {
            $eduProgramsQuery->where('level', $request->level);
            $specialtiesQuery->where('degree', $request->level);
        }

        // Apply education form filter
        if ($request->has('education_form')) {
            $eduProgramsQuery->where('education_form', $request->education_form);
            $specialtiesQuery->where('education_form', $request->education_form);
        }

        // Apply search filter
        if ($request->has('search')) {
            $search = $request->search;
            $eduProgramsQuery->where(function($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('name_uz', 'like', "%{$search}%")
                  ->orWhere('name_ru', 'like', "%{$search}%");
            });
            $specialtiesQuery->where(function($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('name_uz', 'like', "%{$search}%")
                  ->orWhere('name_ru', 'like', "%{$search}%");
            });
        }

        // Get collections
        $eduPrograms = $eduProgramsQuery->orderBy('code')->get();
        $specialties = $specialtiesQuery->orderBy('code')->get();

        // Transform specialties to look like programs for display
        // Use actual specialty IDs with high offset to avoid conflicts
        $transformedSpecialties = $specialties->map(function($specialty) {
            // Create a new EducationalProgram instance dynamically
            $program = new EducationalProgram();
            $program->id = 10000 + $specialty->id; // Offset to avoid ID conflicts
            $program->code = $specialty->code;
            $program->name_uz = $specialty->name_uz;
            $program->name_ru = $specialty->name_ru;
            $program->name_en = $specialty->name_en;
            $program->level = $specialty->degree;
            $program->education_form = $specialty->education_form;
            $program->duration_years = $specialty->duration_years;
            $program->total_credits = $specialty->credits_required;
            $program->active = true;
            $program->exists = true; // Mark as existing so routes work
            $program->is_specialty = true;
            $program->specialty_id = $specialty->id;

            // Set relationships
            $program->setRelation('faculty', $specialty->faculty);
            $program->setRelation('department', $specialty->department);

            return $program;
        });

        // Merge and paginate
        $allPrograms = $eduPrograms->concat($transformedSpecialties)->sortBy('code');

        // Manual pagination
        $perPage = 20;
        $currentPage = $request->get('page', 1);
        $offset = ($currentPage - 1) * $perPage;
        $programs = new \Illuminate\Pagination\LengthAwarePaginator(
            $allPrograms->slice($offset, $perPage)->values(),
            $allPrograms->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $faculties = Faculty::all();

        // Calculate statistics from both tables
        $statistics = [
            'total_programs' => EducationalProgram::count() + Specialty::count(),
            'bakalavriat' => EducationalProgram::where('level', 'bakalavriat')->count()
                           + Specialty::where('degree', 'bakalavr')->count(),
            'magistratura' => EducationalProgram::where('level', 'magistratura')->count()
                            + Specialty::where('degree', 'magistr')->count(),
            'active' => EducationalProgram::where('active', true)->count()
                       + Specialty::where('is_active', true)->count(),
        ];

        return view('structure.academic.programs.index', compact('programs', 'faculties', 'statistics'));
    }

    public function show($id)
    {
        // Check if ID is from specialty (offset by 10000)
        if ($id >= 10000) {
            $specialtyId = $id - 10000;
            $specialty = Specialty::with(['faculty', 'department'])->findOrFail($specialtyId);

            // Transform specialty to program structure
            $program = new EducationalProgram();
            $program->id = $id;
            $program->code = $specialty->code;
            $program->name_uz = $specialty->name_uz;
            $program->name_ru = $specialty->name_ru;
            $program->name_en = $specialty->name_en;
            $program->level = $specialty->degree;
            $program->education_form = $specialty->education_form;
            $program->duration_years = $specialty->duration_years;
            $program->total_credits = $specialty->credits_required;
            $program->active = true;
            $program->exists = true;

            $program->setRelation('faculty', $specialty->faculty);
            $program->setRelation('department', $specialty->department);
        } else {
            $program = EducationalProgram::with(['faculty', 'department', 'curricula.subject'])->findOrFail($id);
        }

        // Get current academic year
        $currentYear = AcademicYear::getCurrentYear();

        // Get curriculum for current year (only for real programs)
        $curriculum = $program->id < 10000 ? $program->getCurrentCurriculum($currentYear) : collect();

        // Get credits by semester (may return null)
        $creditsBySemester = method_exists($program, 'getCreditsBySemester') && $program->id < 10000
            ? $program->getCreditsBySemester()
            : collect();

        // Calculate statistics
        $stats = [
            'total_subjects' => $curriculum->count(),
            'required_subjects' => $curriculum->where('subject_type', 'majburiy')->count(),
            'elective_subjects' => $curriculum->where('subject_type', 'tanlov')->count(),
            'total_hours' => $curriculum->sum('total_hours'),
        ];

        return view('structure.academic.programs.show', [
            'program' => $program,
            'curriculum' => $curriculum,
            'creditsBySemester' => $creditsBySemester,
            'stats' => $stats,
            'currentYear' => $currentYear
        ]);
    }

    public function create()
    {
        $faculties = Faculty::all();
        $departments = Department::all();
        
        return view('structure.academic.programs.create', compact('faculties', 'departments'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:educational_programs,code',
            'name_uz' => 'required|string|max:255',
            'name_ru' => 'nullable|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'level' => 'required|in:bakalavriat,magistratura,doktorantura,ordinatura',
            'education_form' => 'required|in:kunduzgi,kechki,sirtqi',
            'duration_years' => 'required|integer|min:1|max:6',
            'total_credits' => 'required|integer|min:60|max:300',
            'faculty_id' => 'required|exists:faculties,id',
            'department_id' => 'nullable|exists:departments,id',
            'qualification' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);
        
        $program = EducationalProgram::create($validated);
        
        return redirect()->route('structure.academic.programs.show', $program)
            ->with('success', "Ta'lim yo'nalishi muvaffaqiyatli yaratildi!");
    }

    public function edit($id)
    {
        // Check if ID is from specialty (offset by 10000)
        if ($id >= 10000) {
            return redirect()->route('structure.academic.programs.index')
                ->with('error', 'Specialtylarni bu yerdan tahrirlab bo\'lmaydi!');
        }

        $program = EducationalProgram::findOrFail($id);
        $faculties = Faculty::all();
        $departments = Department::all();

        return view('structure.academic.programs.edit', compact('program', 'faculties', 'departments'));
    }

    public function update(Request $request, EducationalProgram $program)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:educational_programs,code,' . $program->id,
            'name_uz' => 'required|string|max:255',
            'name_ru' => 'nullable|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'level' => 'required|in:bakalavriat,magistratura,doktorantura,ordinatura',
            'education_form' => 'required|in:kunduzgi,kechki,sirtqi',
            'duration_years' => 'required|integer|min:1|max:6',
            'total_credits' => 'required|integer|min:60|max:300',
            'faculty_id' => 'required|exists:faculties,id',
            'department_id' => 'nullable|exists:departments,id',
            'qualification' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'active' => 'boolean',
        ]);
        
        $program->update($validated);
        
        return redirect()->route('structure.academic.programs.show', $program)
            ->with('success', "Ta'lim yo'nalishi muvaffaqiyatli yangilandi!");
    }

    public function destroy(EducationalProgram $program)
    {
        if ($program->curricula()->exists()) {
            return back()->with('error', "Bu yo'nalishda o'quv reja mavjud!");
        }
        
        $program->delete();
        
        return redirect()->route('structure.academic.programs.index')
            ->with('success', "Ta'lim yo'nalishi o'chirildi!");
    }

    public function curriculum($id)
    {
        // Check if ID is from specialty (offset by 10000)
        if ($id >= 10000) {
            return redirect()->route('structure.academic.programs.index')
                ->with('error', 'Specialty uchun o\'quv reja hali mavjud emas!');
        }

        $program = EducationalProgram::findOrFail($id);

        // Get current academic year
        $academicYear = request('academic_year', AcademicYear::getCurrentYear());

        // Get curriculum data
        $curriculum = $program->curricula()
            ->with('subject')
            ->where('academic_year', $academicYear)
            ->orderBy('semester_number')
            ->orderBy('sequence_number')
            ->get()
            ->groupBy('semester_number');
        
        // Get available academic years
        $currentYear = date('Y');
        $academicYears = [];
        for ($i = -2; $i <= 2; $i++) {
            $year = $currentYear + $i;
            $academicYears[] = $year . '-' . ($year + 1);
        }
        
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
        
        return view('structure.academic.programs.curriculum', compact(
            'program', 
            'curriculum', 
            'academicYear', 
            'academicYears',
            'stats'
        ));
    }
}