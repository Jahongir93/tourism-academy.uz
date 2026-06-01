<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\CmsNews;
use App\Models\CmsEvent;
use App\Models\LmsCourse;
use App\Models\LmsLibraryBook;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        // Get locale from URL parameter or session or default to 'uz'
        $locale = $request->get('lang', Session::get('locale', 'uz'));

        // Validate locale
        if (!in_array($locale, ['uz', 'ru', 'en'])) {
            $locale = 'uz';
        }

        // Set locale
        App::setLocale($locale);
        Session::put('locale', $locale);

        try {
            // Cache ma'lumotlarni 10 daqiqaga
            $data = Cache::remember('home_page_data', 600, function () {
                return [
                    // Statistika
                    'stats' => [
                        'students' => Student::count(),
                        'faculties' => Faculty::where('is_active', true)->count(),
                        'departments' => Department::where('is_active', true)->count(),
                        'teachers' => \App\Models\User::whereHas('roles', function($q) {
                            $q->where('name', 'Teacher');
                        })->count(),
                        'courses' => class_exists(LmsCourse::class) ? LmsCourse::where('is_published', true)->count() : 0,
                        'books' => class_exists(LmsLibraryBook::class) ? LmsLibraryBook::where('is_active', true)->count() : 0,
                    ],

                    // So'nggi yangiliklar
                    'latest_news' => CmsNews::with('category')
                        ->where('status', 'published')
                        ->orderBy('published_at', 'desc')
                        ->limit(4)
                        ->get(),

                    // Yaqinlashib kelayotgan va davom etayotgan tadbirlar
                    'upcoming_events' => CmsEvent::whereIn('status', ['upcoming', 'ongoing'])
                        ->orderBy('start_date', 'desc')
                        ->limit(4)
                        ->get(),

                    // Fakultetlar
                    'faculties' => Faculty::where('is_active', true)
                        ->withCount('students')
                        ->orderBy('order_number')
                        ->get(),
                ];
            });

            $data['locale'] = $locale;

            return view('home', $data);
        } catch (\Exception $e) {
            // Agar xato bo'lsa, oddiy sahifa ko'rsatamiz
            \Log::error('Home page error: ' . $e->getMessage());
            return view('home', [
                'locale' => $locale,
                'stats' => [
                    'students' => 0,
                    'faculties' => 0,
                    'departments' => 0,
                    'teachers' => 0,
                    'courses' => 0,
                    'books' => 0,
                ],
                'latest_news' => collect([]),
                'upcoming_events' => collect([]),
                'faculties' => collect([]),
            ]);
        }
    }

    public function setLanguage(Request $request, $lang)
    {
        if (in_array($lang, ['uz', 'ru', 'en'])) {
            Session::put('locale', $lang);
            App::setLocale($lang);
        }

        return redirect()->back();
    }
    
    public function about(Request $request)
    {
        // Get locale from URL parameter or session or default to 'uz'
        $locale = $request->get('lang', Session::get('locale', 'uz'));

        // Validate locale
        if (!in_array($locale, ['uz', 'ru', 'en'])) {
            $locale = 'uz';
        }

        // Set locale
        App::setLocale($locale);
        Session::put('locale', $locale);

        return view('about', compact('locale'));
    }
    
    public function contact()
    {
        return view('pages.contact');
    }

    public function faq(Request $request)
    {
        $locale = $request->get('lang', Session::get('locale', 'uz'));
        if (!in_array($locale, ['uz', 'ru', 'en'])) {
            $locale = 'uz';
        }
        App::setLocale($locale);
        Session::put('locale', $locale);

        return view('pages.faq', compact('locale'));
    }

    public function programs(Request $request)
    {
        // Get locale from URL parameter or session or default to 'uz'
        $locale = $request->get('lang', Session::get('locale', 'uz'));

        // Validate locale
        if (!in_array($locale, ['uz', 'ru', 'en'])) {
            $locale = 'uz';
        }

        // Set locale
        App::setLocale($locale);
        Session::put('locale', $locale);

        return view('public.programs', compact('locale'));
    }

    public function teachers(Request $request)
    {
        // Get locale from URL parameter or session or default to 'uz'
        $locale = $request->get('lang', Session::get('locale', 'uz'));

        // Validate locale
        if (!in_array($locale, ['uz', 'ru', 'en'])) {
            $locale = 'uz';
        }

        // Set locale
        App::setLocale($locale);
        Session::put('locale', $locale);

        return view('teachers', compact('locale'));
    }

    public function statistics(Request $request)
    {
        // Get locale from URL parameter or session or default to 'uz'
        $locale = $request->get('lang', Session::get('locale', 'uz'));

        // Validate locale
        if (!in_array($locale, ['uz', 'ru', 'en'])) {
            $locale = 'uz';
        }

        // Set locale
        App::setLocale($locale);
        Session::put('locale', $locale);

        return view('statistics', compact('locale'));
    }

    public function blog(Request $request)
    {
        // Get locale from URL parameter or session or default to 'uz'
        $locale = $request->get('lang', Session::get('locale', 'uz'));

        // Validate locale
        if (!in_array($locale, ['uz', 'ru', 'en'])) {
            $locale = 'uz';
        }

        // Set locale
        App::setLocale($locale);
        Session::put('locale', $locale);

        // Get blog posts (using CmsNews as blog)
        $posts = CmsNews::with('category')
            ->where('status', 'published')
            ->orderBy('published_at', 'desc')
            ->paginate(12);

        return view('blog', compact('locale', 'posts'));
    }
}