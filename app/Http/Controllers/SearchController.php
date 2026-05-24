<?php

namespace App\Http\Controllers;

use App\Models\CmsNews;
use App\Models\CmsEvent;
use App\Models\Employee;
use App\Models\CmsPage;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->get('q', '');
        $type = $request->get('type', 'all');

        $results = [
            'news' => collect(),
            'events' => collect(),
            'teachers' => collect(),
            'pages' => collect(),
        ];

        $totalCount = 0;

        if (strlen($query) >= 2) {
            // Search News
            if ($type === 'all' || $type === 'news') {
                try {
                    $results['news'] = CmsNews::where('status', 'published')
                        ->where(function ($q) use ($query) {
                            $q->where('title_uz', 'LIKE', "%{$query}%")
                              ->orWhere('title_ru', 'LIKE', "%{$query}%")
                              ->orWhere('title_en', 'LIKE', "%{$query}%")
                              ->orWhere('content_uz', 'LIKE', "%{$query}%")
                              ->orWhere('content_ru', 'LIKE', "%{$query}%")
                              ->orWhere('content_en', 'LIKE', "%{$query}%")
                              ->orWhere('excerpt_uz', 'LIKE', "%{$query}%");
                        })
                        ->orderBy('published_at', 'desc')
                        ->limit(10)
                        ->get();
                    $totalCount += $results['news']->count();
                } catch (\Exception $e) {
                    $results['news'] = collect();
                }
            }

            // Search Events
            if ($type === 'all' || $type === 'events') {
                try {
                    $results['events'] = CmsEvent::where(function ($q) use ($query) {
                            $q->where('title_uz', 'LIKE', "%{$query}%")
                              ->orWhere('title_ru', 'LIKE', "%{$query}%")
                              ->orWhere('title_en', 'LIKE', "%{$query}%")
                              ->orWhere('description_uz', 'LIKE', "%{$query}%")
                              ->orWhere('description_ru', 'LIKE', "%{$query}%")
                              ->orWhere('location', 'LIKE', "%{$query}%");
                        })
                        ->orderBy('start_date', 'desc')
                        ->limit(10)
                        ->get();
                    $totalCount += $results['events']->count();
                } catch (\Exception $e) {
                    $results['events'] = collect();
                }
            }

            // Search Teachers/Employees
            if ($type === 'all' || $type === 'teachers') {
                try {
                    $results['teachers'] = Employee::where('status', 'active')
                        ->where('employee_type', 'teacher')
                        ->where(function ($q) use ($query) {
                            $q->where('first_name', 'LIKE', "%{$query}%")
                              ->orWhere('last_name', 'LIKE', "%{$query}%")
                              ->orWhere('middle_name', 'LIKE', "%{$query}%")
                              ->orWhereRaw("CONCAT(last_name, ' ', first_name) LIKE ?", ["%{$query}%"])
                              ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$query}%"]);
                        })
                        ->limit(10)
                        ->get();
                    $totalCount += $results['teachers']->count();
                } catch (\Exception $e) {
                    $results['teachers'] = collect();
                }
            }

            // Search Pages
            if ($type === 'all' || $type === 'pages') {
                try {
                    $results['pages'] = CmsPage::where('status', 'published')
                        ->where(function ($q) use ($query) {
                            $q->where('title_uz', 'LIKE', "%{$query}%")
                              ->orWhere('title_ru', 'LIKE', "%{$query}%")
                              ->orWhere('title_en', 'LIKE', "%{$query}%")
                              ->orWhere('content_uz', 'LIKE', "%{$query}%")
                              ->orWhere('content_ru', 'LIKE', "%{$query}%");
                        })
                        ->limit(10)
                        ->get();
                    $totalCount += $results['pages']->count();
                } catch (\Exception $e) {
                    $results['pages'] = collect();
                }
            }
        }

        return view('search.index', [
            'query' => $query,
            'type' => $type,
            'results' => $results,
            'totalCount' => $totalCount,
        ]);
    }

    // AJAX search for quick results
    public function quick(Request $request)
    {
        $query = $request->get('q', '');
        $results = [];

        if (strlen($query) >= 2) {
            // Quick search - limited results
            try {
                $news = CmsNews::where('status', 'published')
                    ->where(function ($q) use ($query) {
                        $q->where('title_uz', 'LIKE', "%{$query}%")
                          ->orWhere('title_ru', 'LIKE', "%{$query}%");
                    })
                    ->orderBy('published_at', 'desc')
                    ->limit(3)
                    ->get(['id', 'title_uz', 'title_ru', 'slug', 'featured_image']);

                foreach ($news as $item) {
                    $results[] = [
                        'type' => 'news',
                        'title' => $item->title_uz,
                        'url' => route('news.show', $item->slug),
                        'image' => $item->featured_image ? asset($item->featured_image) : null,
                        'icon' => 'fa-newspaper',
                    ];
                }
            } catch (\Exception $e) {
                // Silently ignore news search errors
            }

            try {
                $teachers = Employee::where('status', 'active')
                    ->where('employee_type', 'teacher')
                    ->where(function ($q) use ($query) {
                        $q->where('first_name', 'LIKE', "%{$query}%")
                          ->orWhere('last_name', 'LIKE', "%{$query}%")
                          ->orWhereRaw("CONCAT(last_name, ' ', first_name) LIKE ?", ["%{$query}%"]);
                    })
                    ->limit(3)
                    ->get(['id', 'first_name', 'last_name', 'middle_name', 'photo_url']);

                foreach ($teachers as $teacher) {
                    $results[] = [
                        'type' => 'teacher',
                        'title' => $teacher->full_name,
                        'subtitle' => 'O\'qituvchi',
                        'url' => route('teachers') . '#teacher-' . $teacher->id,
                        'image' => $teacher->photo_url,
                        'icon' => 'fa-user-tie',
                    ];
                }
            } catch (\Exception $e) {
                // Silently ignore teacher search errors
            }

            try {
                $events = CmsEvent::where(function ($q) use ($query) {
                        $q->where('title_uz', 'LIKE', "%{$query}%")
                          ->orWhere('title_ru', 'LIKE', "%{$query}%");
                    })
                    ->orderBy('start_date', 'desc')
                    ->limit(2)
                    ->get(['id', 'title_uz', 'slug', 'featured_image', 'start_date']);

                foreach ($events as $event) {
                    $results[] = [
                        'type' => 'event',
                        'title' => $event->title_uz,
                        'subtitle' => $event->start_date ? $event->start_date->format('d.m.Y') : null,
                        'url' => route('news.show', $event->slug ?? $event->id),
                        'image' => $event->featured_image ? asset('storage/' . $event->featured_image) : null,
                        'icon' => 'fa-calendar-alt',
                    ];
                }
            } catch (\Exception $e) {
                // Silently ignore event search errors
            }
        }

        return response()->json([
            'success' => true,
            'query' => $query,
            'results' => $results,
            'count' => count($results),
        ]);
    }
}
