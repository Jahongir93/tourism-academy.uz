<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\AdmissionApplication;
use App\Models\CmsNews;
use App\Models\CmsEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MarketingDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Get statistics
        $stats = [
            'total_applications' => AdmissionApplication::count(),
            'pending_applications' => AdmissionApplication::where('status', 'pending')->count(),
            'approved_applications' => AdmissionApplication::where('status', 'approved')->count(),
            'total_students' => Student::count(),
            'new_students_this_month' => Student::whereMonth('created_at', now()->month)->count(),
            'total_events' => CmsEvent::count(),
            'event_registrations' => DB::table('cms_event_registrations')->count(),
        ];

        // Applications by source/channel
        $applicationsBySource = AdmissionApplication::select(
                'source',
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('source')
            ->get();

        // Applications by month
        $applicationsByMonth = AdmissionApplication::select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->limit(12)
            ->get();

        // Student enrollment by faculty
        $enrollmentByFaculty = Student::select(
                'faculty_id',
                DB::raw('COUNT(*) as count')
            )
            ->with('faculty:id,name_uz')
            ->groupBy('faculty_id')
            ->get();

        // Recent applications
        $recentApplications = AdmissionApplication::orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Campaign performance
        $campaignStats = [
            'social_media_reach' => 15000,
            'website_visits' => 8500,
            'conversion_rate' => 12.5,
        ];

        return view('marketing.dashboard', compact(
            'user',
            'stats',
            'applicationsBySource',
            'applicationsByMonth',
            'enrollmentByFaculty',
            'recentApplications',
            'campaignStats'
        ));
    }
}
