<?php

namespace App\Http\Controllers\LMS;

use App\Http\Controllers\Controller;
use App\Models\LmsMaterial;
use App\Models\LmsVideo;
use App\Models\LmsPracticeTest;
use App\Models\LmsForumPost;
use App\Models\LmsLibraryBook;
use App\Models\LmsCertificate;
use App\Models\LmsContentView;
use App\Models\LmsCourse;
use App\Models\LmsCourseEnrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LmsDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Cache statistics for 5 minutes
        $stats = \Cache::remember('lms_dashboard_stats', 300, function () {
            return [
                'total_courses' => LmsCourse::where('is_published', true)->count(),
                'total_materials' => LmsMaterial::where('is_active', true)->count(),
                'total_videos' => LmsVideo::where('is_active', true)->count(),
                'total_tests' => LmsPracticeTest::where('is_active', true)->count(),
                'total_books' => LmsLibraryBook::where('is_active', true)->count(),
                'materials_by_type' => [
                    'pdf' => LmsMaterial::where('is_active', true)->where('material_type', 'pdf')->count(),
                    'document' => LmsMaterial::where('is_active', true)->where('material_type', 'document')->count(),
                    'presentation' => LmsMaterial::where('is_active', true)->where('material_type', 'presentation')->count(),
                    'spreadsheet' => LmsMaterial::where('is_active', true)->where('material_type', 'spreadsheet')->count(),
                    'other' => LmsMaterial::where('is_active', true)->where('material_type', 'other')->count(),
                ],
                'total_downloads' => LmsMaterial::where('is_active', true)->sum('download_count'),
            ];
        });

        // User-specific stats (not cached)
        $stats['my_certificates'] = LmsCertificate::where('user_id', $user->id)->count();
        $stats['my_enrollments'] = LmsCourseEnrollment::where('user_id', $user->id)->count();
        $stats['forum_posts'] = LmsForumPost::where('user_id', $user->id)->count();

        // Cache recent materials
        $recentMaterials = \Cache::remember('lms_recent_materials', 600, function () {
            return LmsMaterial::with(['subject:id,name_uz', 'teacher:id,name,email'])
                ->select('id', 'title', 'description', 'material_type', 'subject_id', 'teacher_id', 'created_at')
                ->where('is_active', true)
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
        });

        // Cache recent videos
        $recentVideos = \Cache::remember('lms_recent_videos', 600, function () {
            return LmsVideo::with(['subject:id,name_uz', 'teacher:id,name,email'])
                ->select('id', 'title', 'description', 'duration', 'subject_id', 'teacher_id', 'created_at')
                ->where('is_active', true)
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
        });

        // Cache upcoming tests
        $upcomingTests = \Cache::remember('lms_upcoming_tests', 300, function () {
            return LmsPracticeTest::with(['subject:id,name_uz', 'teacher:id,name,email'])
                ->select('id', 'title', 'description', 'subject_id', 'teacher_id', 'time_limit', 'passing_score', 'available_from', 'available_until')
                ->where('is_active', true)
                ->where(function($query) {
                    $query->whereNull('available_from')
                          ->orWhere('available_from', '<=', now());
                })
                ->where(function($query) {
                    $query->whereNull('available_until')
                          ->orWhere('available_until', '>=', now());
                })
                ->orderBy('available_until')
                ->limit(5)
                ->get();
        });

        // Cache featured courses
        $featuredCourses = \Cache::remember('lms_featured_courses', 900, function () {
            $courses = LmsCourse::with(['subject:id,name_uz', 'teacher:id,name,email'])
                ->select('id', 'title', 'description', 'subject_id', 'teacher_id', 'thumbnail', 'level', 'enrollment_count', 'rating', 'slug')
                ->where('is_published', true)
                ->where('is_featured', true)
                ->orderBy('enrollment_count', 'desc')
                ->limit(4)
                ->get();

            if ($courses->isEmpty()) {
                $courses = LmsCourse::with(['subject:id,name_uz', 'teacher:id,name,email'])
                    ->select('id', 'title', 'description', 'subject_id', 'teacher_id', 'thumbnail', 'level', 'enrollment_count', 'rating', 'slug')
                    ->where('is_published', true)
                    ->orderBy('created_at', 'desc')
                    ->limit(4)
                    ->get();
            }

            return $courses;
        });

        // Cache featured books
        $featuredBooks = \Cache::remember('lms_featured_books', 900, function () {
            return LmsLibraryBook::select('id', 'title', 'author', 'cover_image', 'description', 'file_type', 'pages')
                ->where('is_active', true)
                ->where('is_featured', true)
                ->orderBy('created_at', 'desc')
                ->limit(4)
                ->get();
        });

        // Cache recent forum posts
        $recentForumPosts = \Cache::remember('lms_recent_forum_posts', 300, function () {
            return LmsForumPost::with(['subject:id,name_uz', 'user:id,name,email'])
                ->select('id', 'title', 'content', 'subject_id', 'user_id', 'reply_count', 'view_count', 'created_at')
                ->whereNull('parent_id')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
        });

        // Get user's progress (not cached)
        $userProgress = LmsContentView::where('user_id', $user->id)
            ->selectRaw('viewable_type, COUNT(*) as total, SUM(is_completed) as completed')
            ->groupBy('viewable_type')
            ->get();

        return view('lms.dashboard.index', compact(
            'stats',
            'recentMaterials',
            'recentVideos',
            'upcomingTests',
            'featuredCourses',
            'featuredBooks',
            'recentForumPosts',
            'userProgress'
        ));
    }

    public function myProgress()
    {
        $user = Auth::user();

        $contentViews = LmsContentView::with('viewable')
            ->where('user_id', $user->id)
            ->orderBy('last_viewed_at', 'desc')
            ->paginate(20);

        $certificates = LmsCertificate::with('subject')
            ->where('user_id', $user->id)
            ->orderBy('issue_date', 'desc')
            ->get();

        $overallStats = [
            'total_viewed' => LmsContentView::where('user_id', $user->id)->count(),
            'total_completed' => LmsContentView::where('user_id', $user->id)->where('is_completed', true)->count(),
            'total_time' => LmsContentView::where('user_id', $user->id)->sum('view_duration'),
            'certificates_earned' => $certificates->count(),
        ];

        return view('lms.dashboard.progress', compact('contentViews', 'certificates', 'overallStats'));
    }
}
