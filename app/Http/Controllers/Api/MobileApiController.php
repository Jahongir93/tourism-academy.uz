<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Student;
use App\Models\LmsCourse;
use App\Models\Assignment;
use App\Models\Grade;
use App\Models\Schedule;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;

class MobileApiController extends Controller
{
    /**
     * Mobile App Login
     */
    public function login(Request $request)
    {
        $request->validate([
            'phone' => 'required_without:email|string',
            'email' => 'required_without:phone|email',
            'password' => 'required|string|min:8',
        ]);

        $credentials = $request->only(['password']);

        if ($request->filled('phone')) {
            $credentials['phone'] = $request->phone;
            $user = User::where('phone', $request->phone)->first();
        } else {
            $credentials['email'] = $request->email;
            $user = User::where('email', $request->email)->first();
        }

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Login yoki parol noto\'g\'ri'
            ], 401);
        }

        // Check if phone verification is required
        if ($request->filled('phone') && !$user->phone_verified_at) {
            // Generate OTP
            $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            $user->otp_code = $otp;
            $user->otp_expires_at = now()->addMinutes(5);
            $user->save();

            // TODO: Send OTP via SMS

            return response()->json([
                'success' => true,
                'requires_otp' => true,
                'message' => 'Tasdiqlash kodi yuborildi'
            ]);
        }

        // Create token
        $token = $user->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'success' => true,
            'token' => $token,
            'user' => $this->formatUser($user),
        ]);
    }

    /**
     * Mobile App Register
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|unique:users',
            'email' => 'nullable|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'user_type' => 'required|in:uzbek,foreign',
        ]);

        if (empty($request->phone) && empty($request->email)) {
            return response()->json([
                'success' => false,
                'message' => 'Telefon yoki email kiritish majburiy'
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'user_type' => $request->user_type,
        ]);

        // Assign default role
        $user->assignRole('Student');

        // If phone provided, require OTP verification
        if ($request->filled('phone')) {
            $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            $user->otp_code = $otp;
            $user->otp_expires_at = now()->addMinutes(5);
            $user->save();

            // TODO: Send OTP via SMS

            return response()->json([
                'success' => true,
                'requires_otp' => true,
                'message' => 'Tasdiqlash kodi yuborildi'
            ]);
        }

        $token = $user->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'success' => true,
            'token' => $token,
            'user' => $this->formatUser($user),
        ]);
    }

    /**
     * Verify OTP
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'otp' => 'required|string|size:6',
        ]);

        $user = User::where('phone', $request->phone)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Foydalanuvchi topilmadi'
            ], 404);
        }

        if ($user->otp_code !== $request->otp) {
            return response()->json([
                'success' => false,
                'message' => 'OTP kod noto\'g\'ri'
            ], 422);
        }

        if ($user->otp_expires_at < now()) {
            return response()->json([
                'success' => false,
                'message' => 'OTP kod muddati tugagan'
            ], 422);
        }

        // Mark phone as verified
        $user->phone_verified_at = now();
        $user->otp_code = null;
        $user->otp_expires_at = null;
        $user->save();

        $token = $user->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'success' => true,
            'token' => $token,
            'user' => $this->formatUser($user),
        ]);
    }

    /**
     * Resend OTP
     */
    public function resendOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
        ]);

        $user = User::where('phone', $request->phone)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Foydalanuvchi topilmadi'
            ], 404);
        }

        // Rate limiting
        $cacheKey = 'otp_resend_' . $request->phone;
        $attempts = Cache::get($cacheKey, 0);

        if ($attempts >= 3) {
            return response()->json([
                'success' => false,
                'message' => 'Juda ko\'p so\'rov. 1 soatdan keyin qayta urinib ko\'ring.'
            ], 429);
        }

        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $user->otp_code = $otp;
        $user->otp_expires_at = now()->addMinutes(5);
        $user->save();

        Cache::put($cacheKey, $attempts + 1, now()->addHour());

        // TODO: Send OTP via SMS

        return response()->json([
            'success' => true,
            'message' => 'OTP kod yuborildi'
        ]);
    }

    /**
     * Get current user
     */
    public function getUser(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => $this->formatUser($request->user()),
        ]);
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Muvaffaqiyatli chiqildi'
        ]);
    }

    /**
     * Get student dashboard data
     */
    public function getDashboard(Request $request)
    {
        $user = $request->user();
        $student = Student::with(['faculty', 'specialty', 'group'])
            ->where('user_id', $user->id)
            ->first();

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Talaba ma\'lumotlari topilmadi'
            ], 404);
        }

        // Get today's schedule
        $today = now()->dayOfWeek;
        $todaySchedule = Schedule::with(['subject', 'teacher'])
            ->where('group_id', $student->group_id)
            ->where('day_of_week', $today)
            ->orderBy('start_time')
            ->get();

        // Get upcoming assignments
        $upcomingAssignments = Assignment::with(['subject', 'teacher'])
            ->whereJsonContains('group_ids', $student->group_id)
            ->where('due_date', '>=', now())
            ->orderBy('due_date')
            ->limit(5)
            ->get();

        // Get recent grades
        $recentGrades = Grade::with('subject')
            ->where('student_id', $student->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Get attendance stats for current semester
        $attendanceStats = Attendance::where('user_id', $user->id)
            ->whereMonth('date', now()->month)
            ->selectRaw('
                SUM(CASE WHEN status = "present" THEN 1 ELSE 0 END) as present,
                SUM(CASE WHEN status IN ("late", "very_late") THEN 1 ELSE 0 END) as late,
                SUM(CASE WHEN status = "absent" THEN 1 ELSE 0 END) as absent,
                COUNT(*) as total
            ')
            ->first();

        // Calculate GPA
        $gpaData = Grade::where('student_id', $student->id)
            ->where('is_final', true)
            ->selectRaw('
                AVG(grade_point) as cumulative_gpa,
                SUM(credits) as total_credits
            ')
            ->first();

        $semesterGpa = Grade::where('student_id', $student->id)
            ->where('semester', $student->semester)
            ->where('is_final', true)
            ->avg('grade_point');

        return response()->json([
            'success' => true,
            'data' => [
                'student' => [
                    'id' => $student->id,
                    'student_id' => $student->student_id,
                    'first_name' => $student->first_name,
                    'last_name' => $student->last_name,
                    'photo_url' => $student->photo_url ? asset('storage/' . $student->photo_url) : null,
                    'faculty' => $student->faculty ? [
                        'id' => $student->faculty->id,
                        'name_uz' => $student->faculty->name_uz,
                    ] : null,
                    'specialty' => $student->specialty ? [
                        'id' => $student->specialty->id,
                        'name_uz' => $student->specialty->name_uz,
                    ] : null,
                    'group' => $student->group ? [
                        'id' => $student->group->id,
                        'name' => $student->group->name,
                    ] : null,
                    'course' => $student->course,
                    'semester' => $student->semester,
                ],
                'today_schedule' => $todaySchedule->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'subject' => [
                            'id' => $item->subject->id,
                            'name' => $item->subject->name_uz ?? $item->subject->name,
                        ],
                        'teacher' => [
                            'id' => $item->teacher->id ?? null,
                            'name' => $item->teacher->name ?? 'N/A',
                        ],
                        'room' => $item->room,
                        'building' => $item->building,
                        'start_time' => $item->start_time,
                        'end_time' => $item->end_time,
                        'lesson_type' => $item->lesson_type,
                    ];
                }),
                'upcoming_assignments' => $upcomingAssignments->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'title' => $item->title,
                        'subject' => [
                            'id' => $item->subject->id ?? null,
                            'name' => $item->subject->name_uz ?? $item->subject->name ?? 'N/A',
                        ],
                        'due_date' => $item->due_date,
                    ];
                }),
                'recent_grades' => $recentGrades->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'subject' => [
                            'id' => $item->subject->id ?? null,
                            'name' => $item->subject->name_uz ?? $item->subject->name ?? 'N/A',
                        ],
                        'grade' => $item->grade,
                        'letter_grade' => $item->letter_grade,
                        'assessment_type' => $item->assessment_type,
                    ];
                }),
                'attendance_stats' => [
                    'present' => (int) ($attendanceStats->present ?? 0),
                    'late' => (int) ($attendanceStats->late ?? 0),
                    'absent' => (int) ($attendanceStats->absent ?? 0),
                    'total' => (int) ($attendanceStats->total ?? 0),
                ],
                'gpa_summary' => [
                    'semester_gpa' => round($semesterGpa ?? 0, 2),
                    'cumulative_gpa' => round($gpaData->cumulative_gpa ?? 0, 2),
                    'total_credits' => (int) ($gpaData->total_credits ?? 0),
                ],
                'notifications_count' => $user->unreadNotifications()->count(),
            ],
        ]);
    }

    /**
     * Get student profile
     */
    public function getProfile(Request $request)
    {
        $user = $request->user();
        $student = Student::with(['faculty', 'specialty', 'group'])
            ->where('user_id', $user->id)
            ->first();

        return response()->json([
            'success' => true,
            'data' => [
                'user' => $this->formatUser($user),
                'student' => $student,
            ],
        ]);
    }

    /**
     * Get courses list
     */
    public function getCourses(Request $request)
    {
        $user = $request->user();

        $courses = LmsCourse::with(['teacher', 'subject'])
            ->where('is_published', true)
            ->when($request->search, function ($query, $search) {
                $query->where('title', 'like', "%{$search}%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate($request->per_page ?? 15);

        return response()->json([
            'success' => true,
            'data' => $courses->items(),
            'current_page' => $courses->currentPage(),
            'last_page' => $courses->lastPage(),
            'total' => $courses->total(),
        ]);
    }

    /**
     * Get assignments
     */
    public function getAssignments(Request $request)
    {
        $user = $request->user();
        $student = Student::where('user_id', $user->id)->first();

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Talaba ma\'lumotlari topilmadi'
            ], 404);
        }

        $assignments = Assignment::with(['subject', 'teacher'])
            ->whereJsonContains('group_ids', $student->group_id)
            ->when($request->status === 'pending', function ($query) {
                $query->where('due_date', '>=', now());
            })
            ->when($request->status === 'overdue', function ($query) {
                $query->where('due_date', '<', now());
            })
            ->orderBy('due_date')
            ->paginate($request->per_page ?? 15);

        return response()->json([
            'success' => true,
            'data' => $assignments->items(),
            'current_page' => $assignments->currentPage(),
            'last_page' => $assignments->lastPage(),
            'total' => $assignments->total(),
        ]);
    }

    /**
     * Get grades
     */
    public function getGrades(Request $request)
    {
        $user = $request->user();
        $student = Student::where('user_id', $user->id)->first();

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Talaba ma\'lumotlari topilmadi'
            ], 404);
        }

        $grades = Grade::with('subject')
            ->where('student_id', $student->id)
            ->when($request->semester, function ($query, $semester) {
                $query->where('semester', $semester);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $grades,
        ]);
    }

    /**
     * Format user for API response
     */
    private function formatUser(User $user): array
    {
        $user->load('roles');

        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'user_type' => $user->user_type,
            'is_online' => $user->is_online,
            'roles' => $user->roles->pluck('name'),
        ];
    }
}
