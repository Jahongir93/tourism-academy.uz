<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\FaceAttendanceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Student;
use Carbon\Carbon;

class FaceAttendanceController extends Controller
{
    protected $faceService;

    public function __construct()
    {
        $this->faceService = new FaceAttendanceService();
    }

    /**
     * Show face recognition page with attendance list
     */
    public function showFaceRecognition(Request $request)
    {
        // Get all active groups
        $groups = DB::table('groups')
            ->where('is_active', true)
            ->orderBy('course')
            ->orderBy('name')
            ->get();

        // Get today's attendance
        $selectedGroupId = $request->get('group_id');
        $todayAttendance = $this->faceService->getTodayAttendance($selectedGroupId);

        // Get registered students count
        $registeredCount = DB::table('face_encodings')
            ->where('is_active', true)
            ->count();

        return view('attendance.face-recognition', [
            'groups' => $groups,
            'selectedGroupId' => $selectedGroupId,
            'todayAttendance' => $todayAttendance['attendances'] ?? collect(),
            'stats' => $todayAttendance['stats'] ?? [],
            'registeredCount' => $registeredCount
        ]);
    }

    /**
     * Show staff attendance page
     */
    public function showStaffAttendance()
    {
        return view('attendance.staff-attendance');
    }

    /**
     * Get enrolled students for face recognition
     */
    public function getEnrolledStudents(Request $request)
    {
        try {
            $groupId = $request->get('group_id');
            $search = $request->get('search');
            $result = $this->faceService->getEnrolledStudents($groupId, $search);

            return response()->json($result);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Talabalar ma\'lumotini yuklashda xatolik: ' . $e->getMessage(),
                'students' => [],
                'count' => 0
            ], 500);
        }
    }

    /**
     * Register face for student (new method)
     */
    public function registerStudentFace(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'student_id' => 'required|exists:students,id',
            'images' => 'required|array|min:3|max:10',
            'images.*' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
                'message' => 'Kamida 3 ta rasm kerak'
            ], 422);
        }

        $result = $this->faceService->registerStudentFace(
            $request->student_id,
            $request->images
        );

        return response()->json($result, $result['success'] ? 200 : 400);
    }

    /**
     * Recognize face and mark attendance (new method)
     */
    public function recognizeAndMark(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|string',
            'group_id' => 'nullable|exists:groups,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $result = $this->faceService->recognizeAndMarkAttendance(
            $request->image,
            $request->group_id
        );

        return response()->json($result);
    }

    /**
     * Mark attendance for a specific student (when face is already recognized on client)
     */
    public function markStudentAttendance(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'student_id' => 'required|exists:students,id',
            'confidence' => 'nullable|numeric|min:0|max:1'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $student = Student::with(['group'])->find($request->student_id);

            if (!$student) {
                return response()->json([
                    'success' => false,
                    'message' => 'Talaba topilmadi'
                ], 404);
            }

            $today = Carbon::today()->toDateString();
            $now = Carbon::now();

            // Check if already marked today
            $existing = DB::table('face_attendances')
                ->where('student_id', $student->id)
                ->where('date', $today)
                ->first();

            if ($existing) {
                return response()->json([
                    'success' => true,
                    'message' => 'Davomat allaqachon belgilangan',
                    'already_marked' => true,
                    'attendance' => [
                        'id' => $existing->id,
                        'check_in_time' => $existing->check_in_time,
                        'status' => $existing->status
                    ]
                ]);
            }

            // Determine status based on time
            $hour = $now->hour;
            $status = 'present';
            if ($hour < 8) {
                $status = 'early';
            } elseif ($hour >= 9 && $hour < 10) {
                $status = 'late';
            } elseif ($hour >= 10) {
                $status = 'very_late';
            }

            // Insert attendance record
            $attendanceId = DB::table('face_attendances')->insertGetId([
                'student_id' => $student->id,
                'group_id' => $student->group_id,
                'date' => $today,
                'check_in_time' => $now->format('H:i:s'),
                'confidence_score' => $request->confidence ?? 0.85,
                'status' => $status,
                'method' => 'face_recognition',
                'created_at' => $now,
                'updated_at' => $now
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Davomat muvaffaqiyatli belgilandi',
                'attendance' => [
                    'id' => $attendanceId,
                    'student_id' => $student->id,
                    'student_name' => $student->last_name . ' ' . $student->first_name,
                    'group_name' => optional($student->group)->name,
                    'check_in_time' => $now->format('H:i:s'),
                    'status' => $status,
                    'date' => $today
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Xatolik: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete student face registration (new method)
     */
    public function deleteStudentFace(Request $request, $studentId)
    {
        $result = $this->faceService->deleteStudentFace($studentId);
        return response()->json($result, $result['success'] ? 200 : 400);
    }

    /**
     * Export attendance to Excel (new method)
     */
    public function exportToExcel(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'group_id' => 'nullable|exists:groups,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $this->faceService->exportAttendanceData(
            $request->group_id,
            $request->start_date ?? Carbon::now()->startOfMonth()->toDateString(),
            $request->end_date ?? Carbon::now()->toDateString()
        );

        // Generate CSV file
        $filename = 'davomat_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');

            // Add BOM for UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // Headers
            fputcsv($file, [
                'Sana',
                'Talaba ID',
                'F.I.O',
                'Guruh',
                'Kurs',
                'Kelish vaqti',
                'Ketish vaqti',
                'Holat',
                'Ishonchlilik (%)',
                'Usul'
            ], ';');

            // Data
            foreach ($data as $row) {
                $statusMap = [
                    'early' => 'Erta',
                    'present' => 'Keldi',
                    'late' => 'Kechikdi',
                    'very_late' => 'Juda kechikdi',
                    'absent' => 'Kelmadi'
                ];

                fputcsv($file, [
                    $row->date,
                    $row->student_code,
                    $row->student_name,
                    $row->group_name,
                    $row->course,
                    $row->check_in_time,
                    $row->check_out_time ?? '-',
                    $statusMap[$row->status] ?? $row->status,
                    round($row->confidence_score * 100, 1),
                    $row->method == 'face_recognition' ? 'Yuz orqali' : $row->method
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get all groups (new method)
     */
    public function getGroups()
    {
        $result = $this->faceService->getGroups();
        return response()->json($result);
    }

    /**
     * Check today's schedule for a group (new method)
     */
    public function checkSchedule(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'group_id' => 'required|exists:groups,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $result = $this->faceService->checkTodaySchedule($request->group_id);

        return response()->json([
            'success' => true,
            'schedule' => $result
        ]);
    }

    /**
     * Get attendance statistics (new method)
     */
    public function getStats(Request $request)
    {
        $result = $this->faceService->getAttendanceStats(
            $request->start_date,
            $request->end_date
        );

        return response()->json($result);
    }

    // ==========================================
    // Student Info Methods
    // ==========================================

    /**
     * Get student info by ID (for auto-fill)
     */
    public function getStudentInfo($studentId)
    {
        try {
            $student = Student::with(['group', 'faculty'])->find($studentId);

            if (!$student) {
                return response()->json([
                    'success' => false,
                    'message' => 'Talaba topilmadi'
                ], 404);
            }

            // Check if student has registered face
            $hasFace = DB::table('face_encodings')
                ->where('student_id', $studentId)
                ->where('is_active', true)
                ->exists();

            return response()->json([
                'success' => true,
                'student' => [
                    'id' => $student->id,
                    'student_id' => $student->student_id,
                    'first_name' => $student->first_name,
                    'last_name' => $student->last_name,
                    'middle_name' => $student->middle_name,
                    'full_name' => $student->last_name . ' ' . $student->first_name . ' ' . ($student->middle_name ?? ''),
                    'group_id' => $student->group_id,
                    'group_name' => optional($student->group)->name,
                    'course' => $student->course ?? optional($student->group)->course,
                    'faculty_id' => $student->faculty_id,
                    'faculty_name' => optional($student->faculty)->name_uz,
                    'photo' => $student->photo,
                    'status' => $student->status,
                    'has_face_registered' => $hasFace
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Xatolik: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get student's registered face photos
     */
    public function getStudentPhotos($studentId)
    {
        try {
            // Check if student has registered face
            $faceEncoding = DB::table('face_encodings')
                ->where('student_id', $studentId)
                ->where('is_active', true)
                ->first();

            if (!$faceEncoding) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bu talaba yuz ro\'yxatidan o\'tmagan',
                    'photos' => []
                ]);
            }

            // Get photos from storage
            $photoDir = storage_path('app/face_data/images/student_' . $studentId);
            $photos = [];

            if (file_exists($photoDir)) {
                $files = glob($photoDir . '/*.jpg');
                foreach ($files as $file) {
                    // Convert to base64 for display
                    $imageData = file_get_contents($file);
                    $base64 = base64_encode($imageData);
                    $photos[] = 'data:image/jpeg;base64,' . $base64;
                }
            }

            return response()->json([
                'success' => true,
                'photos' => $photos,
                'registered_at' => $faceEncoding->updated_at ? \Carbon\Carbon::parse($faceEncoding->updated_at)->format('d.m.Y H:i') : null,
                'count' => count($photos)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Xatolik: ' . $e->getMessage(),
                'photos' => []
            ], 500);
        }
    }

    // ==========================================
    // Staff/Employee Attendance Methods
    // ==========================================

    /**
     * Get staff info by ID
     */
    public function getStaffInfo($staffId)
    {
        try {
            $staff = User::find($staffId);

            if (!$staff) {
                return response()->json([
                    'success' => false,
                    'message' => 'Xodim topilmadi'
                ], 404);
            }

            // Check if staff has registered face
            $hasFace = DB::table('face_encodings')
                ->where('user_id', $staffId)
                ->where('is_active', true)
                ->exists();

            return response()->json([
                'success' => true,
                'staff' => [
                    'id' => $staff->id,
                    'name' => $staff->name,
                    'email' => $staff->email,
                    'role' => $staff->role ?? 'staff',
                    'department' => $staff->department ?? null,
                    'position' => $staff->position ?? null,
                    'has_face_registered' => $hasFace
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Xatolik: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get list of staff/employees
     */
    public function getStaffList(Request $request)
    {
        try {
            $query = User::query();

            // Filter by role if specified
            if ($request->has('role')) {
                $query->where('role', $request->role);
            } else {
                // Get teachers and staff (not students)
                $query->whereIn('role', ['admin', 'teacher', 'staff', 'dean', 'rector', 'accountant']);
            }

            $staff = $query->select('id', 'name', 'email', 'role', 'department', 'position')
                ->orderBy('name')
                ->get();

            // Add face registration status
            $staffIds = $staff->pluck('id');
            $registeredFaces = DB::table('face_encodings')
                ->whereIn('user_id', $staffIds)
                ->where('is_active', true)
                ->pluck('user_id')
                ->toArray();

            $staff = $staff->map(function($user) use ($registeredFaces) {
                $user->has_face_registered = in_array($user->id, $registeredFaces);
                return $user;
            });

            return response()->json([
                'success' => true,
                'staff' => $staff,
                'count' => $staff->count()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Xatolik: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Register face for staff
     */
    public function registerStaffFace(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'staff_id' => 'required|exists:users,id',
            'images' => 'required|array|min:3|max:10',
            'images.*' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
                'message' => 'Kamida 3 ta rasm kerak'
            ], 422);
        }

        try {
            $staff = User::find($request->staff_id);

            // Check if already registered
            $existing = DB::table('face_encodings')
                ->where('user_id', $request->staff_id)
                ->where('is_active', true)
                ->first();

            if ($existing) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bu xodim allaqachon ro\'yxatdan o\'tgan'
                ], 400);
            }

            // Save images and create face encoding record
            $imagePaths = [];
            foreach ($request->images as $index => $imageData) {
                $imagePath = $this->saveBase64Image($imageData, 'staff', $request->staff_id, $index);
                if ($imagePath) {
                    $imagePaths[] = $imagePath;
                }
            }

            // Create face encoding record
            DB::table('face_encodings')->insert([
                'user_id' => $request->staff_id,
                'student_id' => null,
                'encoding' => json_encode(['images' => $imagePaths]),
                'image_path' => $imagePaths[0] ?? null,
                'metadata' => json_encode([
                    'name' => $staff->name,
                    'role' => $staff->role,
                    'registered_at' => now()->toDateTimeString(),
                    'image_count' => count($imagePaths)
                ]),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Xodim muvaffaqiyatli ro\'yxatdan o\'tkazildi',
                'staff_id' => $request->staff_id,
                'images_saved' => count($imagePaths)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Xatolik: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete staff face registration
     */
    public function deleteStaffFace($staffId)
    {
        try {
            $deleted = DB::table('face_encodings')
                ->where('user_id', $staffId)
                ->update(['is_active' => false, 'updated_at' => now()]);

            if ($deleted) {
                return response()->json([
                    'success' => true,
                    'message' => 'Xodim yuz ma\'lumotlari o\'chirildi'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Xodim yuz ma\'lumotlari topilmadi'
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Xatolik: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check staff face status
     */
    public function checkStaffFaceStatus($staffId)
    {
        $hasFace = DB::table('face_encodings')
            ->where('user_id', $staffId)
            ->where('is_active', true)
            ->exists();

        return response()->json([
            'success' => true,
            'has_registered_face' => $hasFace,
            'staff_id' => $staffId
        ]);
    }

    /**
     * Recognize and mark staff attendance
     */
    public function recognizeAndMarkStaff(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|string',
            'staff_id' => 'nullable|exists:users,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // If staff_id provided, mark attendance directly
            if ($request->staff_id) {
                $result = $this->markStaffAttendance($request->staff_id, 0.95);
                return response()->json($result);
            }

            // TODO: Implement actual face recognition for staff
            // For now, return error if no staff_id provided
            return response()->json([
                'success' => false,
                'message' => 'Xodim ID si talab qilinadi'
            ], 400);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Xatolik: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark staff attendance
     */
    private function markStaffAttendance($staffId, $confidence = 0.95)
    {
        $today = Carbon::today()->toDateString();
        $now = Carbon::now();

        // Check if already checked in today
        $existing = DB::table('staff_attendances')
            ->where('user_id', $staffId)
            ->where('date', $today)
            ->first();

        if ($existing) {
            // Update check-out time
            DB::table('staff_attendances')
                ->where('id', $existing->id)
                ->update([
                    'check_out_time' => $now->toTimeString(),
                    'updated_at' => now()
                ]);

            return [
                'success' => true,
                'action' => 'check_out',
                'message' => 'Chiqish vaqti belgilandi',
                'time' => $now->format('H:i')
            ];
        }

        // Create check-in record
        DB::table('staff_attendances')->insert([
            'user_id' => $staffId,
            'date' => $today,
            'check_in_time' => $now->toTimeString(),
            'confidence_score' => $confidence,
            'status' => $this->getAttendanceStatus($now),
            'method' => 'face_recognition',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return [
            'success' => true,
            'action' => 'check_in',
            'message' => 'Kirish vaqti belgilandi',
            'time' => $now->format('H:i')
        ];
    }

    /**
     * Get staff today's attendance
     */
    public function getStaffTodayAttendance(Request $request)
    {
        try {
            $today = Carbon::today()->toDateString();

            $attendances = DB::table('staff_attendances')
                ->join('users', 'staff_attendances.user_id', '=', 'users.id')
                ->where('staff_attendances.date', $today)
                ->select(
                    'staff_attendances.*',
                    'users.name as staff_name',
                    'users.role',
                    'users.department'
                )
                ->orderBy('staff_attendances.check_in_time', 'desc')
                ->get();

            // Calculate stats
            $stats = [
                'total' => $attendances->count(),
                'early' => $attendances->where('status', 'early')->count(),
                'present' => $attendances->where('status', 'present')->count(),
                'late' => $attendances->where('status', 'late')->count(),
                'very_late' => $attendances->where('status', 'very_late')->count()
            ];

            return response()->json([
                'success' => true,
                'attendances' => $attendances,
                'stats' => $stats
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Xatolik: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get attendance status based on time
     */
    private function getAttendanceStatus($time)
    {
        $hour = $time->hour;
        $minute = $time->minute;

        // Work starts at 9:00
        if ($hour < 9 || ($hour == 9 && $minute == 0)) {
            return 'early';
        } elseif ($hour == 9 && $minute <= 15) {
            return 'present';
        } elseif ($hour == 9 && $minute <= 30) {
            return 'late';
        } else {
            return 'very_late';
        }
    }

    /**
     * Save base64 image to storage
     */
    private function saveBase64Image($base64Image, $type, $id, $index)
    {
        try {
            // Remove data:image/xxx;base64, prefix if exists
            if (strpos($base64Image, 'base64,') !== false) {
                $base64Image = explode('base64,', $base64Image)[1];
            }

            $imageData = base64_decode($base64Image);
            $filename = $type . '_' . $id . '_' . $index . '_' . time() . '.jpg';
            $path = 'face_images/' . $type . '/' . $filename;

            // Ensure directory exists
            $fullDir = storage_path('app/public/face_images/' . $type);
            if (!is_dir($fullDir)) {
                mkdir($fullDir, 0755, true);
            }

            file_put_contents(storage_path('app/public/' . $path), $imageData);

            return $path;
        } catch (\Exception $e) {
            \Log::error('Image save error: ' . $e->getMessage());
            return null;
        }
    }

    // ==========================================
    // Legacy methods (backward compatibility)
    // ==========================================

    /**
     * Register face for current user or specified user
     */
    public function registerFace(Request $request)
    {
        // Support both single image and multiple images
        $validator = Validator::make($request->all(), [
            'user_id' => 'sometimes|exists:users,id',
            'image' => 'required_without:images|string', // Single image
            'images' => 'required_without:image|array|min:3|max:10', // Multiple images
            'images.*' => 'required|string' // Base64 encoded images
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $userId = $request->user_id ?? Auth::id();
        $user = User::find($userId);

        // Check if user already has registered face
        if ($this->faceService->hasRegisteredFace($userId)) {
            return response()->json([
                'success' => false,
                'message' => 'Face already registered. Use update endpoint to modify.'
            ], 400);
        }

        // Handle single image or multiple images
        $images = $request->has('image') ? [$request->image] : $request->images;

        $result = $this->faceService->registerFace(
            $userId,
            $images,
            $user->name
        );

        return response()->json($result, $result['success'] ? 200 : 400);
    }

    /**
     * Update face for current user or specified user
     */
    public function updateFace(Request $request, $userId = null)
    {
        $validator = Validator::make($request->all(), [
            'images' => 'required|array|min:1|max:10',
            'images.*' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $userId = $userId ?? Auth::id();

        // Check if user has registered face
        if (!$this->faceService->hasRegisteredFace($userId)) {
            return response()->json([
                'success' => false,
                'message' => 'No face registered. Use register endpoint first.'
            ], 400);
        }

        $result = $this->faceService->updateFace($userId, $request->images);

        return response()->json($result, $result['success'] ? 200 : 400);
    }

    /**
     * Delete face for current user or specified user
     */
    public function deleteFace($userId = null)
    {
        $userId = $userId ?? Auth::id();

        if (!$this->faceService->hasRegisteredFace($userId)) {
            return response()->json([
                'success' => false,
                'message' => 'No face registered for this user.'
            ], 404);
        }

        $result = $this->faceService->deleteFace($userId);

        return response()->json($result, $result['success'] ? 200 : 400);
    }

    /**
     * Recognize face in image
     */
    public function recognizeFace(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $result = $this->faceService->recognizeFace($request->image);

        // If recognized, get user details
        if ($result['success'] && $result['recognized'] ?? false) {
            foreach ($result['users'] as &$recognizedUser) {
                $user = User::find($recognizedUser['user_id']);
                if ($user) {
                    $recognizedUser['user_details'] = [
                        'name' => $user->name,
                        'email' => $user->email
                    ];
                }
            }
        }

        return response()->json($result);
    }

    /**
     * Process check-in
     */
    public function checkIn(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|string',
            'location' => 'nullable|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $result = $this->faceService->checkIn(
            $request->image,
            $request->location
        );

        return response()->json($result, $result['success'] ? 200 : 400);
    }

    /**
     * Process check-out
     */
    public function checkOut(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|string',
            'location' => 'nullable|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $result = $this->faceService->checkOut(
            $request->image,
            $request->location
        );

        return response()->json($result, $result['success'] ? 200 : 400);
    }

    /**
     * Get attendance history
     */
    public function getAttendanceHistory(Request $request, $userId = null)
    {
        $userId = $userId ?? Auth::id();

        $validator = Validator::make($request->all(), [
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $result = $this->faceService->getAttendanceHistory(
            $userId,
            $request->start_date,
            $request->end_date
        );

        return response()->json($result);
    }

    /**
     * Get today's attendance
     */
    public function getTodayAttendance()
    {
        $result = $this->faceService->getTodayAttendance();
        return response()->json($result);
    }

    /**
     * Get attendance statistics
     */
    public function getAttendanceStats(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $result = $this->faceService->getAttendanceStats(
            $request->start_date,
            $request->end_date
        );

        return response()->json($result);
    }

    /**
     * Get users with registered faces
     */
    public function getUsersWithFaces()
    {
        $users = $this->faceService->getUsersWithFaces();

        return response()->json([
            'success' => true,
            'data' => $users,
            'total' => count($users)
        ]);
    }

    /**
     * Check if current user has registered face
     */
    public function checkFaceStatus($userId = null)
    {
        $userId = $userId ?? Auth::id();
        $hasface = $this->faceService->hasRegisteredFace($userId);

        return response()->json([
            'success' => true,
            'has_registered_face' => $hasface,
            'user_id' => $userId
        ]);
    }
}