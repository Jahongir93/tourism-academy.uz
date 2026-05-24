<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vacancy;
use Illuminate\Http\Request;

class VacancyController extends Controller
{
    /**
     * Display a listing of vacancies.
     */
    public function index(Request $request)
    {
        $query = Vacancy::query()->withCount(['applications', 'applications as new_applications_count' => function ($q) {
            $q->where('status', 'new');
        }]);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('department', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->active();
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            } elseif ($request->status === 'expired') {
                $query->where('deadline', '<', now());
            }
        }

        // Filter by employment type
        if ($request->filled('employment_type')) {
            $query->where('employment_type', $request->employment_type);
        }

        $vacancies = $query->latest()->paginate(15)->withQueryString();

        return view('admin.vacancies.index', compact('vacancies'));
    }

    /**
     * Show the form for creating a new vacancy.
     */
    public function create()
    {
        return view('admin.vacancies.create');
    }

    /**
     * Store a newly created vacancy.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'title_ru' => 'nullable|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'description_ru' => 'nullable|string',
            'description_en' => 'nullable|string',
            'requirements' => 'nullable|string',
            'requirements_ru' => 'nullable|string',
            'requirements_en' => 'nullable|string',
            'responsibilities' => 'nullable|string',
            'responsibilities_ru' => 'nullable|string',
            'responsibilities_en' => 'nullable|string',
            'benefits' => 'nullable|string',
            'benefits_ru' => 'nullable|string',
            'benefits_en' => 'nullable|string',
            'employment_type' => 'required|in:full_time,part_time,contract,internship',
            'salary_range' => 'nullable|string|max:255',
            'experience_required' => 'nullable|string|max:255',
            'education_required' => 'nullable|string|max:255',
            'deadline' => 'nullable|date|after:today',
            'positions_count' => 'required|integer|min:1',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
        ]);

        $validated['created_by'] = auth()->id();
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['is_featured'] = $request->boolean('is_featured', false);

        $vacancy = Vacancy::create($validated);

        return redirect()->route('admin.vacancies.index')
            ->with('success', 'Vakansiya muvaffaqiyatli qo\'shildi!');
    }

    /**
     * Display the specified vacancy.
     */
    public function show(Vacancy $vacancy)
    {
        $vacancy->load(['applications' => function ($q) {
            $q->latest()->take(10);
        }]);

        return view('admin.vacancies.show', compact('vacancy'));
    }

    /**
     * Show the form for editing the specified vacancy.
     */
    public function edit(Vacancy $vacancy)
    {
        return view('admin.vacancies.edit', compact('vacancy'));
    }

    /**
     * Update the specified vacancy.
     */
    public function update(Request $request, Vacancy $vacancy)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'title_ru' => 'nullable|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'description_ru' => 'nullable|string',
            'description_en' => 'nullable|string',
            'requirements' => 'nullable|string',
            'requirements_ru' => 'nullable|string',
            'requirements_en' => 'nullable|string',
            'responsibilities' => 'nullable|string',
            'responsibilities_ru' => 'nullable|string',
            'responsibilities_en' => 'nullable|string',
            'benefits' => 'nullable|string',
            'benefits_ru' => 'nullable|string',
            'benefits_en' => 'nullable|string',
            'employment_type' => 'required|in:full_time,part_time,contract,internship',
            'salary_range' => 'nullable|string|max:255',
            'experience_required' => 'nullable|string|max:255',
            'education_required' => 'nullable|string|max:255',
            'deadline' => 'nullable|date',
            'positions_count' => 'required|integer|min:1',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['is_featured'] = $request->boolean('is_featured', false);

        $vacancy->update($validated);

        return redirect()->route('admin.vacancies.index')
            ->with('success', 'Vakansiya muvaffaqiyatli yangilandi!');
    }

    /**
     * Remove the specified vacancy.
     */
    public function destroy(Vacancy $vacancy)
    {
        $vacancy->delete();

        return redirect()->route('admin.vacancies.index')
            ->with('success', 'Vakansiya o\'chirildi!');
    }

    /**
     * Toggle vacancy status.
     */
    public function toggleStatus(Vacancy $vacancy)
    {
        $vacancy->update(['is_active' => !$vacancy->is_active]);

        return back()->with('success', 'Vakansiya holati o\'zgartirildi!');
    }
}
