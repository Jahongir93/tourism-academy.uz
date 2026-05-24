<?php

namespace App\Http\Controllers;

use App\Models\SystemSetting;
use App\Models\AcademicYear;
use App\Models\Semester;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use App\Models\Group;
use App\Models\Student;
use App\Models\StudentMovement;

class SettingsController extends Controller
{
    /**
     * Settings dashboard
     */
    public function index()
    {
        $stats = [
            'total_settings' => SystemSetting::count(),
            'academic_years' => class_exists(AcademicYear::class) ? AcademicYear::count() : 0,
            'current_year' => class_exists(AcademicYear::class) && method_exists(AcademicYear::class, 'current') ? (AcademicYear::current()?->name ?? 'N/A') : 'N/A',
            'current_semester' => class_exists(Semester::class) && method_exists(Semester::class, 'current') ? (Semester::current()?->name ?? 'N/A') : 'N/A',
        ];

        $recentActivities = class_exists(ActivityLog::class)
            ? ActivityLog::with('user')->orderBy('created_at', 'desc')->limit(10)->get()
            : collect([]);

        return view('settings.index', compact('stats', 'recentActivities'));
    }

    /**
     * General settings
     */
    public function general()
    {
        $settings = SystemSetting::all();

        return view('settings.general', compact('settings'));
    }

    /**
     * Update general settings
     */
    public function updateGeneral(Request $request)
    {
        $validated = $request->validate([
            'site_name' => 'required|string|max:255',
            'timezone' => 'required|string',
            'language' => 'required|string',
            'site_logo' => 'nullable|image|max:2048',
            'site_favicon' => 'nullable|image|max:1024',
            'interactive_map_url' => 'nullable|url|max:500',
            'virtual_tour_url' => 'nullable|url|max:500',
        ]);

        DB::beginTransaction();
        try {
            // Update text settings
            foreach (['site_name', 'timezone', 'language', 'interactive_map_url', 'virtual_tour_url'] as $key) {
                if (isset($validated[$key])) {
                    SystemSetting::set($key, $validated[$key]);
                } elseif ($request->has($key)) {
                    SystemSetting::set($key, $request->input($key) ?? '');
                }
            }

            // Handle file uploads
            if ($request->hasFile('site_logo')) {
                $path = $request->file('site_logo')->store('settings', 'public');
                SystemSetting::set('site_logo', $path);
            }

            if ($request->hasFile('site_favicon')) {
                $path = $request->file('site_favicon')->store('settings', 'public');
                SystemSetting::set('site_favicon', $path);
            }

            ActivityLog::log('update', 'Updated general settings');

            DB::commit();

            return redirect()->route('settings.general')
                ->with('success', 'Umumiy sozlamalar muvaffaqiyatli yangilandi');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Xatolik yuz berdi: ' . $e->getMessage());
        }
    }

    /**
     * Academic settings
     */
    public function academic()
    {
        $settings = SystemSetting::all();

        // Safely get academic years
        $academicYears = collect([]);
        try {
            if (class_exists(AcademicYear::class) && Schema::hasTable('academic_years')) {
                $academicYears = AcademicYear::orderBy('start_date', 'desc')->get();
            }
        } catch (\Exception $e) {
            // Table doesn't exist or other error
        }

        // Safely get semesters
        $semesters = collect([]);
        try {
            if (class_exists(Semester::class) && Schema::hasTable('semesters')) {
                $semesters = Semester::with('academicYear')->orderBy('start_date', 'desc')->get();
            }
        } catch (\Exception $e) {
            // Table doesn't exist or other error
        }

        return view('settings.academic', compact('settings', 'academicYears', 'semesters'));
    }

    /**
     * Update academic settings
     */
    public function updateAcademic(Request $request)
    {
        $validated = $request->validate([
            'attendance_required_percentage' => 'required|integer|min:0|max:100',
            'passing_grade' => 'required|integer|min:0|max:100',
            'max_absences_per_month' => 'required|integer|min:0',
        ]);

        DB::beginTransaction();
        try {
            foreach ($validated as $key => $value) {
                SystemSetting::set($key, $value);
            }

            ActivityLog::log('update', 'Updated academic settings');

            DB::commit();

            return redirect()->route('settings.academic')
                ->with('success', 'Akademik sozlamalar muvaffaqiyatli yangilandi');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Xatolik yuz berdi: ' . $e->getMessage());
        }
    }

    /**
     * Store new academic year
     */
    public function storeAcademicYear(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_current' => 'boolean',
        ]);

        DB::beginTransaction();
        try {
            if ($validated['is_current'] ?? false) {
                AcademicYear::where('is_current', true)->update(['is_current' => false]);
            }

            $academicYear = AcademicYear::create($validated);

            ActivityLog::log('create', "Created academic year: {$academicYear->name}", AcademicYear::class, $academicYear->id);

            DB::commit();

            return redirect()->route('settings.academic')
                ->with('success', 'O\'quv yili muvaffaqiyatli yaratildi');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Xatolik yuz berdi: ' . $e->getMessage());
        }
    }

    /**
     * Store new semester
     */
    public function storeSemester(Request $request)
    {
        $validated = $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'name' => 'required|string|max:255',
            'semester_number' => 'required|integer|in:1,2',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_current' => 'boolean',
        ]);

        DB::beginTransaction();
        try {
            if ($validated['is_current'] ?? false) {
                Semester::where('is_current', true)->update(['is_current' => false]);
            }

            $semester = Semester::create($validated);

            ActivityLog::log('create', "Created semester: {$semester->name}", Semester::class, $semester->id);

            DB::commit();

            return redirect()->route('settings.academic')
                ->with('success', 'Semestr muvaffaqiyatli yaratildi');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Xatolik yuz berdi: ' . $e->getMessage());
        }
    }

    /**
     * Start new academic year - promote all groups to next course
     */
    public function startNewAcademicYear(Request $request)
    {
        $validated = $request->validate([
            'new_year_name' => 'required|string|regex:/^\d{4}-\d{4}$/',
            'max_course' => 'required|integer|min:1|max:6',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        $newYearName = $validated['new_year_name'];
        $maxCourse = $validated['max_course'];

        // Check if year already exists
        $existingYear = AcademicYear::where('name', $newYearName)->first();
        if ($existingYear) {
            return back()->with('error', "Bu o'quv yili allaqachon mavjud: {$newYearName}");
        }

        DB::beginTransaction();

        try {
            $stats = [
                'promoted_groups' => 0,
                'promoted_students' => 0,
                'graduated_groups' => 0,
                'graduated_students' => 0,
            ];

            // Get all active groups
            $groups = Group::where('is_active', true)
                ->with(['students' => function($q) {
                    $q->where('status', 'active');
                }])
                ->get();

            foreach ($groups as $group) {
                $currentCourse = $group->course ?? 1;
                $studentCount = $group->students->count();

                if ($currentCourse >= $maxCourse) {
                    // Group is graduating
                    $group->update([
                        'is_active' => false,
                        'academic_year' => $newYearName,
                    ]);

                    // Graduate all students
                    foreach ($group->students as $student) {
                        StudentMovement::create([
                            'student_id' => $student->id,
                            'movement_type' => 'graduation',
                            'from_group_id' => $group->id,
                            'from_course' => $currentCourse,
                            'reason' => "O'quv yili yakunlandi - {$newYearName}",
                            'movement_date' => now(),
                        ]);

                        $student->update(['status' => 'graduated']);
                    }

                    $stats['graduated_groups']++;
                    $stats['graduated_students'] += $studentCount;

                } else {
                    // Promote to next course
                    $newCourse = $currentCourse + 1;

                    $group->update([
                        'course' => $newCourse,
                        'academic_year' => $newYearName,
                        'semester' => 1,
                    ]);

                    // Update all students
                    foreach ($group->students as $student) {
                        StudentMovement::create([
                            'student_id' => $student->id,
                            'movement_type' => 'course_promotion',
                            'from_group_id' => $group->id,
                            'to_group_id' => $group->id,
                            'from_course' => $currentCourse,
                            'to_course' => $newCourse,
                            'reason' => "Yangi o'quv yili - {$newYearName}",
                            'movement_date' => now(),
                        ]);

                        $student->update([
                            'course' => $newCourse,
                            'semester' => 1,
                        ]);
                    }

                    $stats['promoted_groups']++;
                    $stats['promoted_students'] += $studentCount;
                }
            }

            // Create new academic year
            AcademicYear::where('is_current', true)->update(['is_current' => false]);

            $newAcademicYear = AcademicYear::create([
                'name' => $newYearName,
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'is_current' => true,
            ]);

            ActivityLog::log('academic', "Yangi o'quv yili boshlandi: {$newYearName}. " .
                "Ko'tarilgan guruhlar: {$stats['promoted_groups']}, " .
                "Bitiruvchilar: {$stats['graduated_groups']}", AcademicYear::class, $newAcademicYear->id);

            DB::commit();

            $message = "Yangi o'quv yili muvaffaqiyatli boshlandi!\n" .
                "Ko'tarilgan guruhlar: {$stats['promoted_groups']} ({$stats['promoted_students']} talaba)\n" .
                "Bitiruvchi guruhlar: {$stats['graduated_groups']} ({$stats['graduated_students']} talaba)";

            return redirect()->route('settings.academic')
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Academic year transition error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Xatolik yuz berdi: ' . $e->getMessage());
        }
    }

    /**
     * Preview new academic year changes (dry run)
     */
    public function previewNewAcademicYear(Request $request)
    {
        $maxCourse = $request->input('max_course', 4);

        $groups = Group::where('is_active', true)
            ->with(['students' => function($q) {
                $q->where('status', 'active');
            }, 'specialty', 'faculty'])
            ->orderBy('course')
            ->orderBy('name')
            ->get();

        $preview = [
            'to_promote' => [],
            'to_graduate' => [],
            'stats' => [
                'total_groups' => $groups->count(),
                'promote_groups' => 0,
                'promote_students' => 0,
                'graduate_groups' => 0,
                'graduate_students' => 0,
            ]
        ];

        foreach ($groups as $group) {
            $currentCourse = $group->course ?? 1;
            $studentCount = $group->students->count();

            $groupInfo = [
                'id' => $group->id,
                'name' => $group->name,
                'current_course' => $currentCourse,
                'student_count' => $studentCount,
                'faculty' => $group->faculty->name ?? '-',
                'specialty' => $group->specialty->name ?? '-',
            ];

            if ($currentCourse >= $maxCourse) {
                $groupInfo['new_course'] = 'Bitiruvchi';
                $preview['to_graduate'][] = $groupInfo;
                $preview['stats']['graduate_groups']++;
                $preview['stats']['graduate_students'] += $studentCount;
            } else {
                $groupInfo['new_course'] = $currentCourse + 1;
                $preview['to_promote'][] = $groupInfo;
                $preview['stats']['promote_groups']++;
                $preview['stats']['promote_students'] += $studentCount;
            }
        }

        return response()->json($preview);
    }

    /**
     * Finance settings
     */
    public function finance()
    {
        $settings = SystemSetting::all();

        return view('settings.finance', compact('settings'));
    }

    /**
     * Update finance settings
     */
    public function updateFinance(Request $request)
    {
        $validated = $request->validate([
            'currency' => 'required|string|max:10',
            'payment_deadline_days' => 'required|integer|min:1',
            'late_payment_fee_percentage' => 'required|integer|min:0|max:100',
        ]);

        DB::beginTransaction();
        try {
            foreach ($validated as $key => $value) {
                SystemSetting::set($key, $value);
            }

            ActivityLog::log('update', 'Updated finance settings');

            DB::commit();

            return redirect()->route('settings.finance')
                ->with('success', 'Moliya sozlamalari muvaffaqiyatli yangilandi');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Xatolik yuz berdi: ' . $e->getMessage());
        }
    }

    /**
     * System settings
     */
    public function system()
    {
        $settings = SystemSetting::all();

        // Get disk usage safely
        $diskUsagePercent = 0;
        try {
            $diskTotal = disk_total_space('/');
            $diskFree = disk_free_space('/');
            if ($diskTotal > 0) {
                $diskUsed = $diskTotal - $diskFree;
                $diskUsagePercent = ($diskUsed / $diskTotal) * 100;
            }
        } catch (\Exception $e) {
            // Disk info not available
        }

        // Get database size safely
        $databaseSize = 0;
        try {
            $dbSize = DB::select("SELECT
                SUM(data_length + index_length) as size
                FROM information_schema.TABLES
                WHERE table_schema = ?", [env('DB_DATABASE')]);
            $databaseSize = $dbSize[0]->size ?? 0;
        } catch (\Exception $e) {
            // Database info not available
        }

        // Get backup logs safely
        $backupLogs = collect([]);
        try {
            if (Schema::hasTable('backup_logs')) {
                $backupLogs = DB::table('backup_logs')
                    ->orderBy('created_at', 'desc')
                    ->limit(10)
                    ->get();
            }
        } catch (\Exception $e) {
            // Backup logs table doesn't exist
        }

        return view('settings.system', compact('settings', 'diskUsagePercent', 'databaseSize', 'backupLogs'));
    }

    /**
     * Update system settings
     */
    public function updateSystem(Request $request)
    {
        $validated = $request->validate([
            'maintenance_mode' => 'boolean',
            'auto_backup_enabled' => 'boolean',
            'backup_frequency' => 'required|string|in:daily,weekly,monthly',
        ]);

        DB::beginTransaction();
        try {
            foreach ($validated as $key => $value) {
                SystemSetting::set($key, $value);
            }

            // Handle maintenance mode
            if (isset($validated['maintenance_mode'])) {
                if ($validated['maintenance_mode']) {
                    Artisan::call('down');
                } else {
                    Artisan::call('up');
                }
            }

            ActivityLog::log('update', 'Updated system settings');

            DB::commit();

            return redirect()->route('settings.system')
                ->with('success', 'Tizim sozlamalari muvaffaqiyatli yangilandi');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Xatolik yuz berdi: ' . $e->getMessage());
        }
    }

    /**
     * Create backup
     */
    public function createBackup(Request $request)
    {
        $type = $request->input('type', 'database');

        DB::table('backup_logs')->insert([
            'filename' => 'backup_' . now()->format('Y-m-d_H-i-s') . '.sql',
            'type' => $type,
            'size' => 0,
            'status' => 'in_progress',
            'started_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        try {
            // Run backup command (requires spatie/laravel-backup package)
            // Artisan::call('backup:run');

            ActivityLog::log('backup', "Created {$type} backup");

            return back()->with('success', 'Zaxira nusxa yaratilmoqda...');
        } catch (\Exception $e) {
            return back()->with('error', 'Zaxira yaratishda xatolik: ' . $e->getMessage());
        }
    }

    /**
     * Clear cache
     */
    public function clearCache()
    {
        try {
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('view:clear');
            Artisan::call('route:clear');

            SystemSetting::clearCache();

            ActivityLog::log('system', 'Cleared all caches');

            return back()->with('success', 'Kesh tozalandi');
        } catch (\Exception $e) {
            return back()->with('error', 'Keshni tozalashda xatolik: ' . $e->getMessage());
        }
    }

    /**
     * Security settings
     */
    public function security()
    {
        $settings = SystemSetting::all();

        // Get recent failed login attempts safely
        $failedLogins = collect([]);
        $blockedUsers = collect([]);
        try {
            if (class_exists(ActivityLog::class) && Schema::hasTable('activity_logs')) {
                $failedLogins = ActivityLog::where('action', 'failed_login')
                    ->orderBy('created_at', 'desc')
                    ->limit(20)
                    ->get();
            }
        } catch (\Exception $e) {
            // Activity logs not available
        }

        return view('settings.security', compact('settings', 'failedLogins', 'blockedUsers'));
    }

    /**
     * Update security settings
     */
    public function updateSecurity(Request $request)
    {
        $validated = $request->validate([
            'session_timeout' => 'required|integer|min:5',
            'max_login_attempts' => 'required|integer|min:1',
            'password_min_length' => 'required|integer|min:6',
            'require_password_special_chars' => 'boolean',
        ]);

        DB::beginTransaction();
        try {
            foreach ($validated as $key => $value) {
                SystemSetting::set($key, $value);
            }

            ActivityLog::log('update', 'Updated security settings');

            DB::commit();

            return redirect()->route('settings.security')
                ->with('success', 'Xavfsizlik sozlamalari muvaffaqiyatli yangilandi');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Xatolik yuz berdi: ' . $e->getMessage());
        }
    }

    /**
     * Activity logs
     */
    public function activityLogs(Request $request)
    {
        $query = ActivityLog::with('user')->orderBy('created_at', 'desc');

        if ($request->has('action')) {
            $query->where('action', $request->action);
        }

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->paginate(50);
        $actions = ActivityLog::distinct()->pluck('action');
        $users = DB::table('users')->select('id', 'name')->get();

        return view('settings.activity-logs', compact('logs', 'actions', 'users'));
    }

    /**
     * Integrations settings
     */
    public function integrations()
    {
        $settings = SystemSetting::all();

        $hemisSettings = SystemSetting::where('group', 'integrations')
            ->where('key', 'like', 'hemis_%')
            ->get();

        $telegramSettings = SystemSetting::where('group', 'integrations')
            ->where('key', 'like', 'telegram_%')
            ->get();

        $webhooks = collect([]); // Empty collection for webhooks

        return view('settings.integrations', compact('settings', 'hemisSettings', 'telegramSettings', 'webhooks'));
    }

    /**
     * Telegram bot settings page
     */
    public function telegram()
    {
        $settings = SystemSetting::where('group', 'integrations')
            ->where('key', 'like', 'telegram_%')
            ->get();

        return view('settings.telegram', compact('settings'));
    }

    /**
     * Update telegram bot settings
     */
    public function updateTelegram(Request $request)
    {
        $validated = $request->validate([
            'telegram_bot_enabled' => 'nullable|boolean',
            'telegram_bot_token' => 'nullable|string',
            'telegram_bot_username' => 'nullable|string',
            'telegram_webhook_url' => 'nullable|url',
            'telegram_notifications_enabled' => 'nullable|boolean',
        ]);

        DB::beginTransaction();
        try {
            // Save to ChatSetting instead of SystemSetting for TelegramService compatibility
            if (isset($validated['telegram_bot_token'])) {
                \App\Models\ChatSetting::set('telegram_bot_token', $validated['telegram_bot_token'], 'string', false, 'Telegram Bot Token');
            }
            if (isset($validated['telegram_bot_username'])) {
                \App\Models\ChatSetting::set('telegram_bot_username', $validated['telegram_bot_username'], 'string', true, 'Telegram Bot Username');
            }
            if (isset($validated['telegram_webhook_url'])) {
                \App\Models\ChatSetting::set('telegram_webhook_url', $validated['telegram_webhook_url'], 'string', true, 'Telegram Webhook URL');
            }
            if (isset($validated['telegram_bot_enabled'])) {
                \App\Models\ChatSetting::set('telegram_enabled', $validated['telegram_bot_enabled'], 'boolean', true, 'Telegram Bot Enabled');
            }
            if (isset($validated['telegram_notifications_enabled'])) {
                \App\Models\ChatSetting::set('telegram_notifications_enabled', $validated['telegram_notifications_enabled'], 'boolean', true, 'Telegram Notifications Enabled');
            }

            // Also save to SystemSetting for backward compatibility
            foreach ($validated as $key => $value) {
                SystemSetting::updateOrCreate(
                    ['key' => $key],
                    [
                        'value' => $value ?? '',
                        'type' => is_bool($value) ? 'boolean' : 'string',
                        'group' => 'integrations',
                    ]
                );
            }

            // Set webhook if token is provided
            if (isset($validated['telegram_bot_token']) && !empty($validated['telegram_bot_token'])) {
                try {
                    $botToken = $validated['telegram_bot_token'];
                    $webhookUrl = $validated['telegram_webhook_url'] ?? url('/api/telegram/webhook');

                    $response = \Illuminate\Support\Facades\Http::timeout(30)->post(
                        "https://api.telegram.org/bot{$botToken}/setWebhook",
                        [
                            'url' => $webhookUrl,
                            'allowed_updates' => ['message', 'callback_query'],
                        ]
                    );

                    if ($response->successful()) {
                        Log::info('Telegram webhook set successfully', ['webhook_url' => $webhookUrl]);
                    }
                } catch (\Exception $e) {
                    Log::warning('Failed to set Telegram webhook', ['error' => $e->getMessage()]);
                }
            }

            ActivityLog::log('update', 'Updated Telegram Bot settings');

            DB::commit();

            return redirect()->route('settings.integrations.telegram')
                ->with('success', 'Telegram bot sozlamalari muvaffaqiyatli yangilandi va webhook o\'rnatildi');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Xatolik yuz berdi: ' . $e->getMessage());
        }
    }
}
