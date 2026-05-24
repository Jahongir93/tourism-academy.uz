<?php

namespace App\Http\Controllers;

use App\Models\Vacancy;
use App\Models\VacancyApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VacancyFrontendController extends Controller
{
    /**
     * Display list of vacancies.
     */
    public function index(Request $request)
    {
        $query = Vacancy::query()->public();

        // Filter by employment type
        if ($request->filled('type')) {
            $query->where('employment_type', $request->type);
        }

        // Filter by department
        if ($request->filled('department')) {
            $query->where('department', $request->department);
        }

        // Search
        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('department', 'like', "%{$search}%");
            });
        }

        $vacancies = $query->latest()->paginate(12)->withQueryString();

        // Get departments for filter
        $departments = Vacancy::public()
            ->whereNotNull('department')
            ->distinct()
            ->pluck('department');

        $employmentTypes = Vacancy::EMPLOYMENT_TYPES;

        return view('vacancies.index', compact('vacancies', 'departments', 'employmentTypes'));
    }

    /**
     * Display a single vacancy.
     */
    public function show(Vacancy $vacancy)
    {
        // Only show active and not expired
        if (!$vacancy->is_active || $vacancy->is_expired) {
            abort(404);
        }

        $vacancy->incrementViews();

        // Related vacancies
        $relatedVacancies = Vacancy::public()
            ->where('id', '!=', $vacancy->id)
            ->where(function ($q) use ($vacancy) {
                $q->where('department', $vacancy->department)
                  ->orWhere('employment_type', $vacancy->employment_type);
            })
            ->take(3)
            ->get();

        return view('vacancies.show', compact('vacancy', 'relatedVacancies'));
    }

    /**
     * Show application form.
     */
    public function apply(Vacancy $vacancy)
    {
        // Only show active and not expired
        if (!$vacancy->is_active || $vacancy->is_expired) {
            abort(404);
        }

        $educationLevels = VacancyApplication::EDUCATION_LEVELS;

        return view('vacancies.apply', compact('vacancy', 'educationLevels'));
    }

    /**
     * Store application.
     */
    public function storeApplication(Request $request, Vacancy $vacancy)
    {
        // Only accept for active and not expired
        if (!$vacancy->is_active || $vacancy->is_expired) {
            abort(404);
        }

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'birth_date' => 'nullable|date|before:today',
            'gender' => 'nullable|in:male,female',
            'region' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
            'education_level' => 'nullable|in:' . implode(',', array_keys(VacancyApplication::EDUCATION_LEVELS)),
            'education_institution' => 'nullable|string|max:255',
            'education_specialty' => 'nullable|string|max:255',
            'graduation_year' => 'nullable|integer|min:1950|max:' . (date('Y') + 5),
            'experience_years' => 'nullable|integer|min:0|max:60',
            'work_experience' => 'nullable|string|max:2000',
            'skills' => 'nullable|string|max:1000',
            'languages' => 'nullable|string|max:500',
            'cover_letter' => 'nullable|string|max:3000',
            'resume' => 'nullable|file|mimes:pdf,doc,docx|max:5120', // 5MB
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048', // 2MB
        ], [
            'first_name.required' => 'Ism kiritish majburiy',
            'last_name.required' => 'Familiya kiritish majburiy',
            'email.required' => 'Email kiritish majburiy',
            'email.email' => 'Email formati noto\'g\'ri',
            'phone.required' => 'Telefon raqam kiritish majburiy',
            'resume.mimes' => 'Rezyume faqat PDF, DOC yoki DOCX formatida bo\'lishi kerak',
            'resume.max' => 'Rezyume fayl hajmi 5MB dan oshmasligi kerak',
            'photo.image' => 'Rasm noto\'g\'ri formatda',
            'photo.max' => 'Rasm hajmi 2MB dan oshmasligi kerak',
        ]);

        // Handle file uploads
        if ($request->hasFile('resume')) {
            $validated['resume_path'] = $request->file('resume')->store('vacancy-applications/resumes', 'public');
        }

        if ($request->hasFile('photo')) {
            $validated['photo_path'] = $request->file('photo')->store('vacancy-applications/photos', 'public');
        }

        // Add metadata
        $validated['vacancy_id'] = $vacancy->id;
        $validated['ip_address'] = $request->ip();
        $validated['user_agent'] = $request->userAgent();

        // Remove file inputs from validated data
        unset($validated['resume'], $validated['photo']);

        // Create application
        $application = VacancyApplication::create($validated);

        // Increment vacancy applications count
        $vacancy->incrementApplications();

        // TODO: Send notification email to HR

        return redirect()->route('vacancies.success', $application)
            ->with('success', 'Arizangiz muvaffaqiyatli yuborildi!');
    }

    /**
     * Show success page.
     */
    public function success(VacancyApplication $application)
    {
        return view('vacancies.success', compact('application'));
    }
}
