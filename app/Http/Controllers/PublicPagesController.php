<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Faculty;
use App\Models\Department;
use App\Models\CmsNews;
use App\Models\CmsEvent;
use App\Models\Employee;

class PublicPagesController extends Controller
{
    public function faculties()
    {
        try {
            $faculties = Faculty::with(['departments' => function($q) {
                $q->where('is_active', true);
            }])
            ->where('is_active', true)
            ->orderBy('order_number')
            ->get();
        } catch (\Exception $e) {
            $faculties = collect();
        }

        // Statistika
        $stats = [
            'total_faculties' => $faculties->count(),
            'total_departments' => $faculties->sum(function($f) {
                return $f->departments->count();
            }),
            'total_students' => 2500, // Vaqtinchalik
            'total_teachers' => 120   // Vaqtinchalik
        ];

        return view('public.faculties', compact('faculties', 'stats'));
    }

    public function departments()
    {
        try {
            $departments = Department::with(['faculty', 'specialties'])
                ->where('is_active', true)
                ->get()
                ->groupBy('faculty_id');

            $faculties = Faculty::where('is_active', true)
                ->orderBy('order_number')
                ->get();
        } catch (\Exception $e) {
            $departments = collect();
            $faculties = collect();
        }

        return view('public.departments', compact('departments', 'faculties'));
    }
    
    public function news()
    {
        $news = CmsNews::where('status', 'published')
            ->orderBy('published_at', 'desc')
            ->get();

        $events = CmsEvent::whereIn('status', ['upcoming', 'ongoing'])
            ->orderBy('start_date', 'desc')
            ->get();

        // Combine news and events
        $allItems = collect([]);

        foreach ($news as $item) {
            $allItems->push([
                'type' => 'news',
                'id' => $item->id,
                'title' => html_entity_decode($item->title_uz, ENT_QUOTES | ENT_HTML5, 'UTF-8'),
                'excerpt' => html_entity_decode($item->excerpt_uz ?? Str::limit(strip_tags($item->content_uz), 150), ENT_QUOTES | ENT_HTML5, 'UTF-8'),
                'image' => $item->featured_image,
                'date' => $item->published_at,
                'category' => $item->category->name_uz ?? 'Yangilik',
                'views' => $item->views_count ?? 0,
                'is_featured' => $item->is_featured,
                'url' => route('news.show', $item->id)
            ]);
        }

        foreach ($events as $item) {
            $allItems->push([
                'type' => 'event',
                'id' => $item->id,
                'title' => html_entity_decode($item->title_uz, ENT_QUOTES | ENT_HTML5, 'UTF-8'),
                'excerpt' => html_entity_decode($item->description_uz ?? Str::limit(strip_tags($item->content_uz ?? ''), 150), ENT_QUOTES | ENT_HTML5, 'UTF-8'),
                'image' => $item->featured_image,
                'date' => $item->start_date,
                'category' => match($item->type) {
                    'conference' => 'Konferensiya',
                    'seminar' => 'Seminar',
                    'workshop' => 'Trening',
                    'meeting' => 'Yig\'ilish',
                    'ceremony' => 'Marosim',
                    default => 'Tadbir'
                },
                'views' => $item->views_count ?? 0,
                'is_featured' => $item->is_featured,
                'url' => route('news.show', $item->id) // Temporarily using news.show
            ]);
        }

        // Sort by date descending
        $allItems = $allItems->sortByDesc('date');

        // Get featured items
        $featuredItems = $allItems->where('is_featured', true)->take(3);

        // Paginate manually
        $perPage = 12;
        $currentPage = request()->get('page', 1);
        $pagedItems = new \Illuminate\Pagination\LengthAwarePaginator(
            $allItems->forPage($currentPage, $perPage),
            $allItems->count(),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('public.news', [
            'news' => $pagedItems,
            'featuredNews' => $featuredItems
        ]);
    }
    
    public function newsShow($slugOrId)
    {
        // Slug yoki ID orqali topish
        $article = CmsNews::where('status', 'published')
            ->where(function($q) use ($slugOrId) {
                $q->where('slug', $slugOrId)
                  ->orWhere('id', $slugOrId);
            })
            ->firstOrFail();

        // Ko'rishlar sonini oshirish
        $article->increment('views_count');

        $relatedNews = CmsNews::where('status', 'published')
            ->where('id', '!=', $article->id)
            ->when($article->category_id, function($q) use ($article) {
                $q->where('category_id', $article->category_id);
            })
            ->orderBy('published_at', 'desc')
            ->take(4)
            ->get();

        return view('public.news-show', compact('article', 'relatedNews'));
    }
    
    public function contact()
    {
        return view('public.contact');
    }
    
    public function contactSubmit(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|min:10'
        ]);

        // Save to database or send email
        // For now, just redirect with success message

        return redirect()->route('contact')->with('success', 'Xabaringiz muvaffaqiyatli yuborildi!');
    }

    // Yangi sahifalar metodlari
    public function about()
    {
        return view('public.about');
    }

    public function virtualTour()
    {
        return view('public.virtual-tour');
    }

    public function interactiveMap()
    {
        return view('public.interactive-map');
    }

    public function courses()
    {
        return view('public.courses');
    }

    public function programs()
    {
        return view('public.programs');
    }

    public function research()
    {
        return view('public.research');
    }

    public function library()
    {
        return view('public.library');
    }

    public function videos()
    {
        return view('public.videos');
    }

    public function studentLife()
    {
        return view('public.student-life');
    }

    public function events()
    {
        $events = collect([
            [
                'id' => 1,
                'title' => 'Ochiq eshiklar kuni',
                'date' => now()->addDays(5),
                'location' => 'Bosh bino',
                'description' => 'Abituriyentlar uchun ochiq eshiklar kuni'
            ],
            [
                'id' => 2,
                'title' => 'Ilmiy konferensiya',
                'date' => now()->addDays(10),
                'location' => 'Konferensiya zali',
                'description' => 'Turizm sohasida ilmiy konferensiya'
            ]
        ]);
        return view('public.events', compact('events'));
    }

    public function sports()
    {
        return view('public.sports');
    }

    public function dormitories()
    {
        return view('public.dormitories');
    }

    public function scholarships()
    {
        return view('public.scholarships');
    }

    public function internationalRelations()
    {
        return view('public.international-relations');
    }
}