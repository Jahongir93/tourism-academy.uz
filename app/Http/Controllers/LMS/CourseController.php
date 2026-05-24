<?php

namespace App\Http\Controllers\LMS;

use App\Http\Controllers\Controller;
use App\Models\LmsCourse;
use App\Models\LmsCourseResource;
use App\Models\LmsCourseEnrollment;
use App\Models\Subject;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CourseController extends Controller
{
    /**
     * Display a listing of courses
     */
    public function index(Request $request)
    {
        // Optimize query with eager loading and select specific columns
        $query = LmsCourse::with([
            'subject:id,name_uz,name_ru,name_en',
            'teacher:id,name,email'
        ])
        ->select('id', 'title', 'slug', 'description', 'subject_id', 'teacher_id', 'thumbnail', 'level', 'is_published', 'is_featured', 'is_archived', 'enrollment_count', 'rating', 'price', 'created_at');

        // Don't show archived courses by default
        if (!$request->has('show_archived')) {
            $query->where(function($q) {
                $q->whereNull('is_archived')
                  ->orWhere('is_archived', false);
            });
        }

        // Filter by search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('course_code', 'like', "%{$search}%");
            });
        }

        // Filter by subject
        if ($request->has('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        // Filter by level
        if ($request->has('level')) {
            $query->where('level', $request->level);
        }

        // Filter by status
        if ($request->has('status')) {
            if ($request->status == 'published') {
                $query->where('is_published', true);
            } elseif ($request->status == 'draft') {
                $query->where('is_published', false);
            }
        }

        // For teachers, show only their courses
        $user = Auth::user();
        if ($user->hasRole('Teacher')) {
            $query->where('teacher_id', $user->id);
        }

        $courses = $query->orderBy('created_at', 'desc')->paginate(12);

        // Cache subjects list for 1 hour
        $subjects = \Cache::remember('active_subjects_list', 3600, function () {
            return Subject::select('id', 'name_uz', 'name_ru', 'name_en')
                ->where('is_active', true)
                ->orderBy('name_uz')
                ->get();
        });

        return view('lms.courses.index', compact('courses', 'subjects'));
    }

    /**
     * Show form for creating new course
     */
    public function create()
    {
        // Check if user is teacher or admin
        $user = Auth::user();
        if (!$user->hasRole('Teacher') && !$user->hasRole('SuperAdmin') && !$user->hasRole('admin')) {
            return redirect()->route('lms.courses.index')
                ->with('error', 'Sizda kurs yaratish huquqi yo\'q');
        }
        
        $subjects = Subject::where('is_active', true)->orderBy('name_uz')->get();
        
        return view('lms.courses.create', compact('subjects'));
    }

    /**
     * Store newly created course
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'objectives' => 'nullable|string',
            'requirements' => 'nullable|string',
            'subject_id' => 'nullable|exists:subjects,id',
            'language' => 'required|in:uz,ru,en',
            'level' => 'required|in:beginner,intermediate,advanced',
            'duration_weeks' => 'nullable|integer|min:1|max:52',
            'hours_per_week' => 'nullable|integer|min:1|max:40',
            'credit_hours' => 'nullable|integer|min:1|max:10',
            'price' => 'nullable|numeric|min:0',
            'max_students' => 'nullable|integer|min:1',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
            'enrollment_start' => 'nullable|date',
            'enrollment_end' => 'nullable|date|after:enrollment_start',
            'passing_score' => 'nullable|integer|min:0|max:100',
            'certificate_available' => 'boolean',
            'auto_enrollment' => 'boolean',
            'thumbnail' => 'nullable|image|max:2048',
            'intro_video' => 'nullable|file|mimes:mp4,avi,mov|max:102400', // 100MB
            'tags' => 'nullable|string'
        ]);
        
        $user = Auth::user();
        
        // Get teacher_id
        if ($user) {
            $validated['teacher_id'] = $user->id;
        } else {
            // For admin without employee record, use first teacher or create a placeholder
            $firstTeacher = \App\Models\Employee::where('position', 'like', '%teacher%')
                ->orWhere('position', 'like', '%o\'qituvchi%')
                ->first();
            
            if ($firstTeacher) {
                $validated['teacher_id'] = $firstTeacher->id;
            } else {
                // Create a placeholder teacher if none exists
                $validated['teacher_id'] = 1; // Or handle differently
            }
        }
        $validated['slug'] = Str::slug($validated['title']);
        
        // Handle tags
        if (isset($validated['tags'])) {
            $validated['tags'] = array_map('trim', explode(',', $validated['tags']));
        }
        
        // Handle thumbnail upload
        if ($request->hasFile('thumbnail')) {
            try {
                $thumbnail = $request->file('thumbnail');
                $thumbnailPath = $thumbnail->store('lms/courses/thumbnails', 'public');
                $validated['thumbnail'] = $thumbnailPath;
            } catch (\Exception $e) {
                \Log::error('Thumbnail upload error: ' . $e->getMessage());
                return back()->withInput()->with('error', 'Rasmni yuklashda xatolik: ' . $e->getMessage());
            }
        }

        // Handle intro video upload
        if ($request->hasFile('intro_video')) {
            try {
                $video = $request->file('intro_video');
                $videoPath = $video->store('lms/courses/videos', 'public');
                $validated['intro_video'] = $videoPath;
            } catch (\Exception $e) {
                \Log::error('Video upload error: ' . $e->getMessage());
                return back()->withInput()->with('error', 'Videoni yuklashda xatolik: ' . $e->getMessage());
            }
        }

        try {
            $course = LmsCourse::create($validated);

            return redirect()->route('lms.courses.show', $course)
                ->with('success', 'Kurs muvaffaqiyatli yaratildi!');
        } catch (\Exception $e) {
            \Log::error('Course create error: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Kursni yaratishda xatolik: ' . $e->getMessage());
        }
    }

    /**
     * Display course details
     */
    public function show(LmsCourse $course)
    {
        $course->load(['subject', 'teacher', 'resources', 'enrollments.user']);
        $course->incrementViewCount();
        
        // Check if user is enrolled
        $user = Auth::user();
        $isEnrolled = false;
        $enrollment = null;
        
        if ($user) {
            $enrollment = LmsCourseEnrollment::where('course_id', $course->id)
                ->where('user_id', $user->id)
                ->first();
            $isEnrolled = $enrollment !== null;
        }
        
        // Get course resources grouped by week
        $resources = $course->resources()
            ->where('is_published', true)
            ->orderBy('week_number')
            ->orderBy('order_number')
            ->get()
            ->groupBy('week_number');
        
        return view('lms.courses.show', compact('course', 'resources', 'isEnrolled', 'enrollment'));
    }

    /**
     * Show form for editing course
     */
    public function edit(LmsCourse $course)
    {
        // Check permission
        $user = Auth::user();
        if (!$user->hasRole('admin') && $course->teacher_id != $user->id) {
            abort(403, 'Sizda bu kursni tahrirlash huquqi yo\'q');
        }
        
        $subjects = Subject::where('is_active', true)->orderBy('name_uz')->get();
        
        return view('lms.courses.edit', compact('course', 'subjects'));
    }

    /**
     * Update course
     */
    public function update(Request $request, LmsCourse $course)
    {
        // Check permission
        $user = Auth::user();
        if (!$user->hasRole('admin') && $course->teacher_id != $user->id) {
            abort(403, 'Sizda bu kursni tahrirlash huquqi yo\'q');
        }
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'objectives' => 'nullable|string',
            'requirements' => 'nullable|string',
            'subject_id' => 'nullable|exists:subjects,id',
            'language' => 'required|in:uz,ru,en',
            'level' => 'required|in:beginner,intermediate,advanced',
            'duration_weeks' => 'nullable|integer|min:1|max:52',
            'hours_per_week' => 'nullable|integer|min:1|max:40',
            'credit_hours' => 'nullable|integer|min:1|max:10',
            'price' => 'nullable|numeric|min:0',
            'max_students' => 'nullable|integer|min:1',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
            'enrollment_start' => 'nullable|date',
            'enrollment_end' => 'nullable|date|after:enrollment_start',
            'passing_score' => 'nullable|integer|min:0|max:100',
            'certificate_available' => 'boolean',
            'auto_enrollment' => 'boolean',
            'is_published' => 'boolean',
            'is_featured' => 'boolean',
            'thumbnail' => 'nullable|image|max:2048',
            'intro_video' => 'nullable|file|mimes:mp4,avi,mov|max:102400',
            'tags' => 'nullable|string'
        ]);
        
        // Update slug if title changed
        if (isset($validated['title']) && $validated['title'] != $course->title) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        // Handle tags
        if (isset($validated['tags'])) {
            $validated['tags'] = array_map('trim', explode(',', $validated['tags']));
        }

        // Handle thumbnail upload
        if ($request->hasFile('thumbnail')) {
            // Delete old thumbnail
            if ($course->thumbnail && Storage::disk('public')->exists($course->thumbnail)) {
                Storage::disk('public')->delete($course->thumbnail);
            }

            $thumbnail = $request->file('thumbnail');
            $thumbnailPath = $thumbnail->store('lms/courses/thumbnails', 'public');
            $validated['thumbnail'] = $thumbnailPath;
        }

        // Handle intro video upload
        if ($request->hasFile('intro_video')) {
            // Delete old video
            if ($course->intro_video && Storage::disk('public')->exists($course->intro_video)) {
                Storage::disk('public')->delete($course->intro_video);
            }

            $video = $request->file('intro_video');
            $videoPath = $video->store('lms/courses/videos', 'public');
            $validated['intro_video'] = $videoPath;
        }

        try {
            $course->update($validated);

            // Refresh model to ensure we have latest data
            $course->refresh();

            return redirect()->route('lms.courses.show', $course)
                ->with('success', 'Kurs muvaffaqiyatli yangilandi!');
        } catch (\Exception $e) {
            \Log::error('Course update error: ' . $e->getMessage());
            return back()
                ->withInput()
                ->with('error', 'Kursni yangilashda xatolik yuz berdi: ' . $e->getMessage());
        }
    }

    /**
     * Delete course thumbnail
     */
    public function deleteThumbnail(LmsCourse $course)
    {
        $user = Auth::user();
        if (!$user->hasRole('admin') && !$user->hasRole('SuperAdmin') && $course->teacher_id != $user->id) {
            abort(403, 'Sizda bu kurs rasmini o\'chirish huquqi yo\'q');
        }

        if ($course->thumbnail) {
            Storage::disk('public')->delete($course->thumbnail);
            $course->update(['thumbnail' => null]);
            return back()->with('success', 'Kurs rasmi o\'chirildi!');
        }

        return back()->with('error', 'Rasmni o\'chirish mumkin emas!');
    }

    /**
     * Delete course intro video
     */
    public function deleteIntroVideo(LmsCourse $course)
    {
        $user = Auth::user();
        if (!$user->hasRole('admin') && !$user->hasRole('SuperAdmin') && $course->teacher_id != $user->id) {
            abort(403, 'Sizda bu kurs videosini o\'chirish huquqi yo\'q');
        }

        if ($course->intro_video) {
            Storage::disk('public')->delete($course->intro_video);
            $course->update(['intro_video' => null]);
            return back()->with('success', 'Kurs videosi o\'chirildi!');
        }

        return back()->with('error', 'Videoni o\'chirish mumkin emas!');
    }

    /**
     * Archive course
     */
    public function archive(LmsCourse $course)
    {
        // Check permission
        $user = Auth::user();
        if (!$user->hasRole('admin') && $course->teacher_id != $user->id) {
            abort(403, 'Sizda bu kursni arxivlash huquqi yo\'q');
        }
        
        $course->update([
            'is_archived' => true,
            'archived_at' => now(),
            'is_published' => false
        ]);
        
        return back()->with('success', 'Kurs arxivlandi!');
    }

    /**
     * Restore archived course
     */
    public function restore(LmsCourse $course)
    {
        // Check permission
        $user = Auth::user();
        if (!$user->hasRole('admin') && $course->teacher_id != $user->id) {
            abort(403, 'Sizda bu kursni tiklash huquqi yo\'q');
        }
        
        $course->update([
            'is_archived' => false,
            'archived_at' => null
        ]);
        
        return back()->with('success', 'Kurs tiklandi!');
    }

    /**
     * Delete course
     */
    public function destroy(LmsCourse $course)
    {
        // Check permission
        $user = Auth::user();
        if (!$user->hasRole('admin') && $course->teacher_id != $user->id) {
            abort(403, 'Sizda bu kursni o\'chirish huquqi yo\'q');
        }
        
        // Delete associated files
        if ($course->thumbnail) {
            Storage::disk('public')->delete($course->thumbnail);
        }
        if ($course->intro_video) {
            Storage::disk('public')->delete($course->intro_video);
        }
        
        // Delete course resources files
        foreach ($course->resources as $resource) {
            if ($resource->file_path) {
                Storage::disk('public')->delete($resource->file_path);
            }
        }
        
        $course->delete();
        
        return redirect()->route('lms.courses.index')
            ->with('success', 'Kurs muvaffaqiyatli o\'chirildi!');
    }

    /**
     * Update course progress for a student
     */
    public function updateProgress(Request $request, LmsCourse $course)
    {
        $user = Auth::user();
        $resourceId = $request->input('resource_id');
        
        // Check if user is enrolled
        $enrollment = LmsCourseEnrollment::where('course_id', $course->id)
            ->where('user_id', $user->id)
            ->first();
            
        if (!$enrollment) {
            return response()->json(['error' => 'Not enrolled'], 403);
        }
        
        // Update or create resource progress
        \DB::table('lms_resource_progress')->updateOrInsert(
            [
                'user_id' => $user->id,
                'course_id' => $course->id,
                'resource_id' => $resourceId
            ],
            [
                'is_completed' => true,
                'completed_at' => now(),
                'progress_percentage' => 100,
                'updated_at' => now()
            ]
        );
        
        // Calculate overall course progress
        $totalResources = $course->resources()->where('is_published', true)->count();
        $completedResources = \DB::table('lms_resource_progress')
            ->where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->where('is_completed', true)
            ->count();
        
        $progressPercentage = $totalResources > 0 ? ($completedResources / $totalResources) * 100 : 0;
        
        // Update enrollment progress
        $enrollment->update([
            'progress_percentage' => $progressPercentage,
            'completed_resources' => $completedResources,
            'total_resources' => $totalResources,
            'last_accessed_at' => now()
        ]);
        
        // Check if course is completed
        if ($progressPercentage >= 100 && !$enrollment->completed_at) {
            $enrollment->update([
                'completed_at' => now(),
                'status' => 'completed'
            ]);
            
            // Auto-issue certificate if enabled
            $this->checkAndIssueCertificate($course, $user);
        }
        
        return response()->json([
            'success' => true,
            'progress' => $progressPercentage,
            'completed' => $completedResources,
            'total' => $totalResources
        ]);
    }

    /**
     * Check and issue certificate
     */
    private function checkAndIssueCertificate($course, $user)
    {
        // Check if certificate already issued
        $existingCert = \App\Models\LmsCertificate::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();
            
        if (!$existingCert && $course->certificate_available) {
            \App\Models\LmsCertificate::create([
                'user_id' => $user->id,
                'course_id' => $course->id,
                'subject_id' => $course->subject_id,
                'certificate_number' => 'CERT-' . strtoupper(uniqid()),
                'issue_date' => now(),
                'grade' => 'Completed',
                'template' => 'default',
                'status' => 'active'
            ]);
        }
    }

    /**
     * Enroll user to course
     */
    public function enroll(LmsCourse $course)
    {
        $user = Auth::user();
        
        // Check if already enrolled
        $existing = LmsCourseEnrollment::where('course_id', $course->id)
            ->where('user_id', $user->id)
            ->first();
        
        if ($existing) {
            return back()->with('info', 'Siz allaqachon bu kursga yozilgansiz');
        }
        
        // Check if enrollment is open
        if (!$course->isEnrollmentOpen()) {
            return back()->with('error', 'Bu kursga ro\'yxatdan o\'tish yopiq');
        }
        
        // Create enrollment
        LmsCourseEnrollment::create([
            'course_id' => $course->id,
            'user_id' => $user->id,
            'enrolled_at' => now(),
            'status' => 'enrolled'
        ]);
        
        // Increment enrollment count
        $course->increment('enrollment_count');
        
        return redirect()->route('lms.courses.learn', $course)
            ->with('success', 'Kursga muvaffaqiyatli yozildingiz!');
    }

    /**
     * Course learning page
     */
    public function learn(LmsCourse $course)
    {
        $user = Auth::user();
        
        // Check enrollment
        $enrollment = LmsCourseEnrollment::where('course_id', $course->id)
            ->where('user_id', $user->id)
            ->first();
        
        if (!$enrollment && $course->teacher_id != $user->id) {
            return redirect()->route('lms.courses.show', $course)
                ->with('error', 'Siz bu kursga yozilmagansiz');
        }
        
        // Get course resources
        $resources = $course->resources()
            ->where('is_published', true)
            ->orderBy('week_number')
            ->orderBy('order_number')
            ->get()
            ->groupBy('week_number');
        
        // Update last accessed
        if ($enrollment) {
            $enrollment->update([
                'last_accessed_at' => now(),
                'login_count' => $enrollment->login_count + 1
            ]);
        }
        
        return view('lms.courses.learn', compact('course', 'resources', 'enrollment'));
    }

    /**
     * Manage course resources
     */
    public function resources(LmsCourse $course)
    {
        // Check permission
        $user = Auth::user();
        if (!$user->hasRole('admin') && $course->teacher_id != $user->id) {
            abort(403, 'Sizda bu kurs resurslarini boshqarish huquqi yo\'q');
        }
        
        $resources = $course->resources()
            ->orderBy('week_number')
            ->orderBy('order_number')
            ->get();
        
        return view('lms.courses.resources', compact('course', 'resources'));
    }

    /**
     * Upload resource to course
     */
    public function uploadResource(Request $request, LmsCourse $course)
    {
        // Check permission
        $user = Auth::user();
        if (!$user->hasRole('admin') && $course->teacher_id != $user->id) {
            abort(403, 'Sizda bu kursga resurs yuklash huquqi yo\'q');
        }
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'resource_type' => 'required|in:video,document,presentation,audio,link,assignment,quiz',
            'week_number' => 'nullable|integer|min:1|max:52',
            'order_number' => 'nullable|integer|min:0',
            'is_mandatory' => 'boolean',
            'is_downloadable' => 'boolean',
            'is_published' => 'boolean',
            'available_from' => 'nullable|date',
            'available_until' => 'nullable|date|after:available_from',
            'file' => 'required_without:external_url|file|max:512000', // 500MB
            'external_url' => 'required_without:file|url'
        ]);
        
        $validated['course_id'] = $course->id;
        
        // Handle file upload
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('lms/courses/' . $course->id . '/resources', $fileName, 'public');
            
            $validated['file_path'] = $filePath;
            $validated['file_name'] = $file->getClientOriginalName();
            $validated['file_type'] = $file->getClientOriginalExtension();
            $validated['file_size'] = $file->getSize();
        }
        
        LmsCourseResource::create($validated);
        
        return back()->with('success', 'Resurs muvaffaqiyatli yuklandi!');
    }

    /**
     * Delete course resource
     */
    public function deleteResource(LmsCourse $course, LmsCourseResource $resource)
    {
        // Check permission
        $user = Auth::user();
        if (!$user->hasRole('admin') && $course->teacher_id != $user->id) {
            abort(403, 'Sizda bu resursni o\'chirish huquqi yo\'q');
        }
        
        // Check if resource belongs to course
        if ($resource->course_id != $course->id) {
            abort(404);
        }
        
        // Delete file
        if ($resource->file_path) {
            Storage::disk('public')->delete($resource->file_path);
        }
        
        $resource->delete();

        return back()->with('success', 'Resurs muvaffaqiyatli o\'chirildi!');
    }

    public function attachMaterial(Request $request, LmsCourse $course)
    {
        $user = Auth::user();

        if (!$user->hasRole('admin') && $course->teacher_id != $user->id) {
            abort(403, 'Sizda bu kursga material biriktirish huquqi yo\'q');
        }

        $validated = $request->validate([
            'material_id' => 'required|exists:lms_materials,id',
            'week_number' => 'nullable|integer|min:1|max:52',
            'order_number' => 'nullable|integer|min:1',
            'is_mandatory' => 'nullable|boolean'
        ]);

        // Get the next order number for this week if not provided
        if (!isset($validated['order_number'])) {
            $maxOrder = LmsCourseResource::where('course_id', $course->id)
                ->where('week_number', $validated['week_number'] ?? 1)
                ->max('order_number');
            $validated['order_number'] = ($maxOrder ?? 0) + 1;
        }

        LmsCourseResource::create([
            'course_id' => $course->id,
            'resource_type' => 'material',
            'resource_id' => $validated['material_id'],
            'week_number' => $validated['week_number'] ?? 1,
            'order_number' => $validated['order_number'],
            'is_mandatory' => $validated['is_mandatory'] ?? false,
            'is_published' => true
        ]);

        return back()->with('success', 'Material muvaffaqiyatli biriktirildi!');
    }

    public function attachVideo(Request $request, LmsCourse $course)
    {
        $user = Auth::user();

        if (!$user->hasRole('admin') && $course->teacher_id != $user->id) {
            abort(403, 'Sizda bu kursga video biriktirish huquqi yo\'q');
        }

        $validated = $request->validate([
            'video_id' => 'required|exists:lms_videos,id',
            'week_number' => 'nullable|integer|min:1|max:52',
            'order_number' => 'nullable|integer|min:1',
            'is_mandatory' => 'nullable|boolean'
        ]);

        // Get the next order number for this week if not provided
        if (!isset($validated['order_number'])) {
            $maxOrder = LmsCourseResource::where('course_id', $course->id)
                ->where('week_number', $validated['week_number'] ?? 1)
                ->max('order_number');
            $validated['order_number'] = ($maxOrder ?? 0) + 1;
        }

        LmsCourseResource::create([
            'course_id' => $course->id,
            'resource_type' => 'video',
            'resource_id' => $validated['video_id'],
            'week_number' => $validated['week_number'] ?? 1,
            'order_number' => $validated['order_number'],
            'is_mandatory' => $validated['is_mandatory'] ?? false,
            'is_published' => true
        ]);

        return back()->with('success', 'Video muvaffaqiyatli biriktirildi!');
    }

    public function attachTest(Request $request, LmsCourse $course)
    {
        $user = Auth::user();

        if (!$user->hasRole('admin') && $course->teacher_id != $user->id) {
            abort(403, 'Sizda bu kursga test biriktirish huquqi yo\'q');
        }

        $validated = $request->validate([
            'test_id' => 'required|exists:lms_tests,id',
            'week_number' => 'nullable|integer|min:1|max:52',
            'order_number' => 'nullable|integer|min:1',
            'is_mandatory' => 'nullable|boolean'
        ]);

        // Get the next order number for this week if not provided
        if (!isset($validated['order_number'])) {
            $maxOrder = LmsCourseResource::where('course_id', $course->id)
                ->where('week_number', $validated['week_number'] ?? 1)
                ->max('order_number');
            $validated['order_number'] = ($maxOrder ?? 0) + 1;
        }

        LmsCourseResource::create([
            'course_id' => $course->id,
            'resource_type' => 'test',
            'resource_id' => $validated['test_id'],
            'week_number' => $validated['week_number'] ?? 1,
            'order_number' => $validated['order_number'],
            'is_mandatory' => $validated['is_mandatory'] ?? false,
            'is_published' => true
        ]);

        return back()->with('success', 'Test muvaffaqiyatli biriktirildi!');
    }

    /**
     * Show curriculum management page
     */
    public function curriculum(LmsCourse $course)
    {
        $user = Auth::user();

        if (!$user->hasRole('admin') && !$user->hasRole('SuperAdmin') && $course->teacher_id != $user->id) {
            abort(403, 'Sizda bu kursga kirish huquqi yo\'q');
        }

        $course->load(['topics.resources', 'subject']);

        return view('lms.courses.curriculum', compact('course'));
    }

    /**
     * Store new topic
     */
    public function storeTopic(Request $request, LmsCourse $course)
    {
        $user = Auth::user();

        if (!$user->hasRole('admin') && !$user->hasRole('SuperAdmin') && $course->teacher_id != $user->id) {
            abort(403, 'Sizda bu kursga mavzu qo\'shish huquqi yo\'q');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'week_number' => 'required|integer|min:1|max:52',
            'order_number' => 'nullable|integer|min:1',
            'duration_minutes' => 'nullable|integer|min:1',
            'is_published' => 'boolean'
        ]);

        // Get next order number if not provided
        if (!isset($validated['order_number'])) {
            $maxOrder = $course->topics()
                ->where('week_number', $validated['week_number'])
                ->max('order_number');
            $validated['order_number'] = ($maxOrder ?? 0) + 1;
        }

        $validated['course_id'] = $course->id;
        $validated['is_published'] = $request->has('is_published');

        \App\Models\LmsCourseTopic::create($validated);

        return back()->with('success', 'Mavzu muvaffaqiyatli qo\'shildi!');
    }

    /**
     * Get topic data for editing
     */
    public function getTopic(LmsCourse $course, \App\Models\LmsCourseTopic $topic)
    {
        if ($topic->course_id != $course->id) {
            abort(404);
        }

        return response()->json($topic);
    }

    /**
     * Update topic
     */
    public function updateTopic(Request $request, LmsCourse $course, \App\Models\LmsCourseTopic $topic)
    {
        $user = Auth::user();

        if (!$user->hasRole('admin') && !$user->hasRole('SuperAdmin') && $course->teacher_id != $user->id) {
            abort(403, 'Sizda bu mavzuni tahrirlash huquqi yo\'q');
        }

        if ($topic->course_id != $course->id) {
            abort(404);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'week_number' => 'required|integer|min:1|max:52',
            'order_number' => 'nullable|integer|min:1',
            'duration_minutes' => 'nullable|integer|min:1',
            'is_published' => 'boolean'
        ]);

        $validated['is_published'] = $request->has('is_published');

        $topic->update($validated);

        return back()->with('success', 'Mavzu muvaffaqiyatli yangilandi!');
    }

    /**
     * Delete topic
     */
    public function destroyTopic(LmsCourse $course, \App\Models\LmsCourseTopic $topic)
    {
        $user = Auth::user();

        if (!$user->hasRole('admin') && !$user->hasRole('SuperAdmin') && $course->teacher_id != $user->id) {
            abort(403, 'Sizda bu mavzuni o\'chirish huquqi yo\'q');
        }

        if ($topic->course_id != $course->id) {
            abort(404);
        }

        $topic->delete();

        return back()->with('success', 'Mavzu muvaffaqiyatli o\'chirildi!');
    }

    /**
     * Store topic resource
     */
    public function storeTopicResource(Request $request, LmsCourse $course, \App\Models\LmsCourseTopic $topic)
    {
        $user = Auth::user();

        if (!$user->hasRole('admin') && !$user->hasRole('SuperAdmin') && $course->teacher_id != $user->id) {
            abort(403, 'Sizda bu mavzuga resurs biriktirish huquqi yo\'q');
        }

        if ($topic->course_id != $course->id) {
            abort(404);
        }

        $validated = $request->validate([
            'resource_type' => 'required|in:material,video,test,file,link,image',
            'material_id' => 'nullable|exists:lms_materials,id',
            'video_id' => 'nullable|exists:lms_videos,id',
            'test_id' => 'nullable|exists:lms_practice_tests,id',
            'file' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx|max:51200',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'external_link' => 'nullable|url',
            'description' => 'nullable|string',
            'is_mandatory' => 'boolean',
            'is_downloadable' => 'boolean'
        ]);

        $data = [
            'topic_id' => $topic->id,
            'resource_type' => $validated['resource_type'],
            'description' => $validated['description'] ?? null,
            'is_mandatory' => $request->has('is_mandatory'),
            'is_downloadable' => $request->has('is_downloadable'),
            'order_number' => $topic->resources()->max('order_number') + 1
        ];

        // Handle different resource types
        if ($validated['resource_type'] == 'material' && isset($validated['material_id'])) {
            $data['resource_id'] = $validated['material_id'];
        } elseif ($validated['resource_type'] == 'video' && isset($validated['video_id'])) {
            $data['resource_id'] = $validated['video_id'];
        } elseif ($validated['resource_type'] == 'test' && isset($validated['test_id'])) {
            $data['resource_id'] = $validated['test_id'];
        } elseif ($validated['resource_type'] == 'file' && $request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('lms/course-resources', $fileName, 'public');

            $data['file_path'] = $filePath;
            $data['file_name'] = $file->getClientOriginalName();
            $data['file_type'] = $file->getClientOriginalExtension();
            $data['file_size'] = $file->getSize();
        } elseif ($validated['resource_type'] == 'image' && $request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $imagePath = $image->storeAs('lms/course-images', $imageName, 'public');

            $data['file_path'] = $imagePath;
            $data['file_name'] = $image->getClientOriginalName();
            $data['file_type'] = $image->getClientOriginalExtension();
            $data['file_size'] = $image->getSize();
        } elseif ($validated['resource_type'] == 'link' && isset($validated['external_link'])) {
            $data['external_link'] = $validated['external_link'];
        }

        \App\Models\LmsTopicResource::create($data);

        return back()->with('success', 'Resurs muvaffaqiyatli biriktirildi!');
    }

    /**
     * Delete topic resource
     */
    public function destroyTopicResource(LmsCourse $course, \App\Models\LmsCourseTopic $topic, \App\Models\LmsTopicResource $resource)
    {
        $user = Auth::user();

        if (!$user->hasRole('admin') && !$user->hasRole('SuperAdmin') && $course->teacher_id != $user->id) {
            abort(403, 'Sizda bu resursni o\'chirish huquqi yo\'q');
        }

        if ($topic->course_id != $course->id || $resource->topic_id != $topic->id) {
            abort(404);
        }

        // Delete file if exists
        if ($resource->file_path) {
            Storage::disk('public')->delete($resource->file_path);
        }

        $resource->delete();

        return back()->with('success', 'Resurs muvaffaqiyatli o\'chirildi!');
    }
}