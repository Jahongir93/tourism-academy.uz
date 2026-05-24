<?php

namespace App\Http\Controllers\Structure\Academic;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubjectController extends Controller
{
    public function index(Request $request)
    {
        $query = Subject::with('department');
        
        if ($request->has('department_id')) {
            $query->where('department_id', $request->department_id);
        }
        
        if ($request->has('subject_type')) {
            $query->where('subject_type', $request->subject_type);
        }
        
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('name_uz', 'like', "%{$search}%")
                  ->orWhere('name_ru', 'like', "%{$search}%");
            });
        }
        
        $subjects = $query->orderBy('code')->paginate(20);
        $departments = Department::all();
        
        $statistics = [
            'total_subjects' => Subject::count(),
            'majburiy' => Subject::where('subject_type', 'majburiy')->count(),
            'tanlov' => Subject::where('subject_type', 'tanlov')->count(),
            'active' => Subject::where('is_active', true)->count(),
        ];
        
        return view('structure.academic.subjects.index', compact('subjects', 'departments', 'statistics'));
    }

    public function show(Subject $subject)
    {
        $subject->load(['department', 'curricula.program', 'prerequisiteSubjects', 'dependentSubjects']);
        
        $programs = $subject->getPrograms();
        $hourDistributions = $subject->hourDistributions;
        
        return view('structure.academic.subjects.show', compact('subject', 'programs', 'hourDistributions'));
    }

    public function create()
    {
        $departments = Department::all();
        $subjects = Subject::where('is_active', true)->get();
        
        return view('structure.academic.subjects.create', compact('departments', 'subjects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:subjects,code',
            'name_uz' => 'required|string|max:255',
            'name_ru' => 'nullable|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'subject_type' => 'required|in:majburiy,tanlov,umumkasbiy,mutaxassislik',
            'department_id' => 'nullable|exists:departments,id',
            'credits' => 'required|integer|min:1|max:10',
            'total_hours' => 'required|integer|min:30|max:300',
            'description' => 'nullable|string',
            'objectives' => 'nullable|string',
            'outcomes' => 'nullable|string',
            'prerequisites' => 'nullable|array',
            'prerequisites.*' => 'exists:subjects,id',
        ]);
        
        $subject = DB::transaction(function() use ($validated, $request) {
            $prerequisites = $validated['prerequisites'] ?? [];
            unset($validated['prerequisites']);
            
            $subject = Subject::create($validated);
            
            if (!empty($prerequisites)) {
                foreach ($prerequisites as $prereqId) {
                    $subject->prerequisiteSubjects()->attach($prereqId, ['type' => 'required']);
                }
            }
            
            return $subject;
        });
        
        return redirect()->route('structure.academic.subjects.show', $subject)
            ->with('success', 'Fan muvaffaqiyatli yaratildi!');
    }

    public function edit(Subject $subject)
    {
        $departments = Department::all();
        $subjects = Subject::where('is_active', true)->where('id', '!=', $subject->id)->get();
        $subject->load('prerequisiteSubjects');
        
        return view('structure.academic.subjects.edit', compact('subject', 'departments', 'subjects'));
    }

    public function update(Request $request, Subject $subject)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:subjects,code,' . $subject->id,
            'name_uz' => 'required|string|max:255',
            'name_ru' => 'nullable|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'subject_type' => 'required|in:majburiy,tanlov,umumkasbiy,mutaxassislik',
            'department_id' => 'nullable|exists:departments,id',
            'credits' => 'required|integer|min:1|max:10',
            'total_hours' => 'required|integer|min:30|max:300',
            'description' => 'nullable|string',
            'objectives' => 'nullable|string',
            'outcomes' => 'nullable|string',
            'active' => 'boolean',
            'prerequisites' => 'nullable|array',
            'prerequisites.*' => 'exists:subjects,id',
        ]);
        
        DB::transaction(function() use ($subject, $validated, $request) {
            $prerequisites = $validated['prerequisites'] ?? [];
            unset($validated['prerequisites']);
            
            $subject->update($validated);
            
            // Update prerequisites
            $subject->prerequisiteSubjects()->sync([]);
            foreach ($prerequisites as $prereqId) {
                $subject->prerequisiteSubjects()->attach($prereqId, ['type' => 'required']);
            }
        });
        
        return redirect()->route('structure.academic.subjects.show', $subject)
            ->with('success', 'Fan muvaffaqiyatli yangilandi!');
    }

    public function destroy(Subject $subject)
    {
        if ($subject->curricula()->exists()) {
            return back()->with('error', "Bu fan o'quv rejada mavjud!");
        }
        
        $subject->delete();
        
        return redirect()->route('structure.academic.subjects.index')
            ->with('success', "Fan o'chirildi!");
    }

    public function prerequisites(Subject $subject)
    {
        // Load prerequisites if relationship exists
        try {
            $subject->load('prerequisites');
            $prerequisites = $subject->prerequisites ?? collect();
        } catch (\Exception $e) {
            $prerequisites = collect();
        }
        
        // Get available subjects
        $availableSubjects = Subject::where('id', '!=', $subject->id)
            ->where('is_active', true)
            ->orderBy('code')
            ->get();
        
        return view('structure.academic.subjects.prerequisites', compact('subject', 'availableSubjects'));
    }

    public function updatePrerequisites(Request $request, Subject $subject)
    {
        // Handle removal action
        if ($request->input('action') === 'remove') {
            $removeId = $request->input('remove_prerequisite_id');
            
            // Remove from prerequisites if relationship exists
            try {
                if (method_exists($subject, 'prerequisites')) {
                    $subject->prerequisites()->detach($removeId);
                }
            } catch (\Exception $e) {
                // Handle if relationship doesn't exist
            }
            
            return redirect()->route('structure.academic.subjects.prerequisites', $subject)
                ->with('success', "Fan muvaffaqiyatli o'chirildi!");
        }
        
        // Handle adding prerequisites
        $validated = $request->validate([
            'prerequisite_ids' => 'nullable|array',
            'prerequisite_ids.*' => 'exists:subjects,id|not_in:' . $subject->id,
        ]);
        
        // Add prerequisites if relationship exists
        try {
            if (method_exists($subject, 'prerequisites')) {
                foreach ($validated['prerequisite_ids'] ?? [] as $prereqId) {
                    $subject->prerequisites()->attach($prereqId);
                }
            }
        } catch (\Exception $e) {
            // Handle if relationship doesn't exist
        }
        
        return redirect()->route('structure.academic.subjects.prerequisites', $subject)
            ->with('success', "Oldindan talab qilinadigan fanlar qo'shildi!");
    }
}