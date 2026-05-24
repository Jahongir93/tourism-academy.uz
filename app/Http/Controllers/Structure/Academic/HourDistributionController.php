<?php

namespace App\Http\Controllers\Structure\Academic;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\SubjectHourDistribution;
use App\Models\Curriculum;
use App\Models\EducationalProgram;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HourDistributionController extends Controller
{
    public function index(Request $request)
    {
        $query = Subject::with(['department', 'hourDistributions']);
        
        if ($request->has('department_id')) {
            $query->where('department_id', $request->department_id);
        }
        
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('name_uz', 'like', "%{$search}%");
            });
        }
        
        $subjects = $query->paginate(20);
        
        // Statistics
        $stats = [
            'total_subjects' => Subject::count(),
            'with_distribution' => Subject::whereHas('hourDistributions')->count(),
            'without_distribution' => Subject::whereDoesntHave('hourDistributions')->count(),
        ];
        
        return view('structure.academic.hours.index', compact('subjects', 'stats'));
    }

    public function distribution(Subject $subject)
    {
        $distributions = $subject->hourDistributions()
            ->with('program')
            ->get()
            ->groupBy('program_id');
        
        $programs = EducationalProgram::whereHas('curricula', function($q) use ($subject) {
            $q->where('subject_id', $subject->id);
        })->get();
        
        $defaultDistribution = $subject->getDefaultHourDistribution();
        
        return view('structure.academic.hours.distribution', compact('subject', 'distributions', 'programs', 'defaultDistribution'));
    }

    public function saveDistribution(Request $request, Subject $subject)
    {
        $validated = $request->validate([
            'program_id' => 'nullable|exists:educational_programs,id',
            'lecture_hours' => 'required|integer|min:0',
            'practice_hours' => 'required|integer|min:0',
            'seminar_hours' => 'required|integer|min:0',
            'lab_hours' => 'required|integer|min:0',
            'independent_hours' => 'required|integer|min:0',
            'course_work_hours' => 'nullable|integer|min:0',
        ]);
        
        // Validate total hours
        $totalHours = array_sum([
            $validated['lecture_hours'],
            $validated['practice_hours'],
            $validated['seminar_hours'],
            $validated['lab_hours'],
            $validated['independent_hours'],
            $validated['course_work_hours'] ?? 0,
        ]);
        
        if ($totalHours != $subject->total_hours) {
            return back()->withErrors([
                'total_hours' => "Jami soatlar {$subject->total_hours} ga teng bo'lishi kerak. Siz {$totalHours} soat kiritdingiz."
            ]);
        }
        
        // Check auditory hours - faqat ogohlantirish uchun, bloklash emas
        $auditoryHours = array_sum([
            $validated['lecture_hours'],
            $validated['practice_hours'],
            $validated['seminar_hours'],
            $validated['lab_hours'],
        ]);
        
        // Faqat session orqali ogohlantirish berish
        if ($auditoryHours > ($totalHours * 0.5)) {
            session()->flash('warning', "Diqqat! Auditoriya soatlari tavsiya etilgan 50% dan oshib ketdi. Auditoriya: {$auditoryHours} soat, Tavsiya: " . floor($totalHours * 0.5) . " soat");
        }
        
        SubjectHourDistribution::updateOrCreate(
            [
                'subject_id' => $subject->id,
                'program_id' => $validated['program_id'],
            ],
            $validated
        );
        
        // Update related curricula if program is specified
        if ($validated['program_id']) {
            Curriculum::where('subject_id', $subject->id)
                ->where('program_id', $validated['program_id'])
                ->update([
                    'lecture_hours' => $validated['lecture_hours'],
                    'practice_hours' => $validated['practice_hours'],
                    'seminar_hours' => $validated['seminar_hours'],
                    'lab_hours' => $validated['lab_hours'],
                    'independent_hours' => $validated['independent_hours'],
                ]);
        }
        
        return back()->with('success', 'Soatlar taqsimoti saqlandi!');
    }

    public function template()
    {
        $templates = DB::table('hour_distribution_templates')->get();
        
        return view('structure.academic.hours.template', compact('templates'));
    }

    public function saveTemplate(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subject_type' => 'required|in:majburiy,tanlov,umumkasbiy,mutaxassislik',
            'lecture_percent' => 'required|integer|min:0|max:100',
            'practice_percent' => 'required|integer|min:0|max:100',
            'seminar_percent' => 'required|integer|min:0|max:100',
            'lab_percent' => 'required|integer|min:0|max:100',
            'independent_percent' => 'required|integer|min:0|max:100',
        ]);
        
        // Validate that percentages sum to 100
        $total = array_sum([
            $validated['lecture_percent'],
            $validated['practice_percent'],
            $validated['seminar_percent'],
            $validated['lab_percent'],
            $validated['independent_percent'],
        ]);
        
        if ($total != 100) {
            return back()->withErrors([
                'total' => "Foizlar yig'indisi 100% ga teng bo'lishi kerak. Siz {$total}% kiritdingiz."
            ]);
        }
        
        DB::table('hour_distribution_templates')->insert([
            'name' => $validated['name'],
            'subject_type' => $validated['subject_type'],
            'lecture_percent' => $validated['lecture_percent'],
            'practice_percent' => $validated['practice_percent'],
            'seminar_percent' => $validated['seminar_percent'],
            'lab_percent' => $validated['lab_percent'],
            'independent_percent' => $validated['independent_percent'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        return back()->with('success', 'Shablon saqlandi!');
    }

    public function validate(Curriculum $curriculum)
    {
        $validation = $curriculum->validateHours();
        
        return response()->json($validation);
    }
}