<?php

namespace App\Http\Controllers\PR;

use App\Http\Controllers\Controller;
use App\Models\CmsNews;
use App\Models\CmsEvent;
use App\Models\CmsMedia;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PRDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Get statistics
        $stats = [
            'total_news' => CmsNews::count(),
            'published_news' => CmsNews::where('status', 'published')->count(),
            'draft_news' => CmsNews::where('status', 'draft')->count(),
            'total_events' => CmsEvent::count(),
            'upcoming_events' => CmsEvent::where('start_date', '>', now())->count(),
            'total_media' => CmsMedia::count(),
            'total_students' => Student::count(),
        ];

        // Recent news
        $recentNews = CmsNews::with('category')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Upcoming events
        $upcomingEvents = CmsEvent::where('start_date', '>', now())
            ->orderBy('start_date', 'asc')
            ->limit(5)
            ->get();

        // News statistics by month
        $newsStats = CmsNews::select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->limit(6)
            ->get();

        // Event registrations statistics
        $eventStats = CmsEvent::with('registrations')
            ->where('start_date', '>', now()->subMonths(3))
            ->get()
            ->map(function ($event) {
                return [
                    'name' => $event->title,
                    'registrations' => $event->registrations->count(),
                ];
            });

        return view('pr.dashboard', compact(
            'user',
            'stats',
            'recentNews',
            'upcomingEvents',
            'newsStats',
            'eventStats'
        ));
    }
}
