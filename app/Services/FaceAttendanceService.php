<?php

namespace App\Services;

use App\Models\Student;
use App\Models\AttendanceRecord;
use App\Models\JournalEntry;
use App\Models\Schedule;
use App\Models\ScheduleSlot;
use App\Models\Group;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use Exception;

class FaceAttendanceService
{
    protected $faceDataPath;
    protected $confidenceThreshold = 0.70;
    protected $useExternalApi = false;
    protected $apiUrl;

    public function __construct()
    {
        $this->faceDataPath = storage_path('app/face_data');
        $this->ensureDirectoriesExist();

        // Check if external API should be used
        $this->apiUrl = $this->getSetting('api_url', 'http://localhost:5000');
        $this->useExternalApi = $this->getSetting('use_external_api', false);
        $this->confidenceThreshold = $this->getSetting('min_confidence_score', 70) / 100;
    }

    /**
     * Get setting from database
     */
    protected function getSetting($key, $default = null)
    {
        return Cache::remember("face_setting_{$key}", 3600, function () use ($key, $default) {
            $setting = DB::table('face_recognition_settings')
                ->where('key', $key)
                ->first();

            if (!$setting) {
                return $default;
            }

            switch ($setting->type ?? 'string') {
                case 'integer':
                    return (int) $setting->value;
                case 'float':
                    return (float) $setting->value;
                case 'boolean':
                    return $setting->value === 'true' || $setting->value === '1';
                case 'json':
                    return json_decode($setting->value, true);
                default:
                    return $setting->value;
            }
        });
    }

    /**
     * Ensure required directories exist
     */
    protected function ensureDirectoriesExist()
    {
        $directories = [
            $this->faceDataPath,
            $this->faceDataPath . '/encodings',
            $this->faceDataPath . '/images',
            $this->faceDataPath . '/temp'
        ];

        foreach ($directories as $dir) {
            if (!file_exists($dir)) {
                mkdir($dir, 0755, true);
            }
        }
    }

    /**
     * Register student face for attendance system
     */
    public function registerStudentFace(int $studentId, array $images): array
    {
        try {
            $student = Student::with(['user', 'group'])->find($studentId);

            if (!$student) {
                return ['success' => false, 'message' => 'Talaba topilmadi'];
            }

            if (count($images) < 3) {
                return ['success' => false, 'message' => 'Kamida 3 ta rasm kerak'];
            }

            $userDir = $this->faceDataPath . '/images/student_' . $studentId;
            if (!file_exists($userDir)) {
                mkdir($userDir, 0755, true);
            }

            // Clear old images
            $oldFiles = glob($userDir . '/*');
            foreach ($oldFiles as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }

            // Save new images
            $savedImages = [];
            foreach ($images as $index => $imageBase64) {
                // Remove data URL prefix if present
                if (strpos($imageBase64, 'base64,') !== false) {
                    $imageBase64 = explode('base64,', $imageBase64)[1];
                }

                $imageData = base64_decode($imageBase64);
                if ($imageData === false) {
                    continue;
                }

                $fileName = 'student_' . $studentId . '_' . time() . '_' . $index . '.jpg';
                $filePath = $userDir . '/' . $fileName;

                file_put_contents($filePath, $imageData);
                $savedImages[] = $fileName;
            }

            if (empty($savedImages)) {
                return ['success' => false, 'message' => 'Rasmlar saqlanmadi'];
            }

            $studentName = $student->full_name_latin ?? ($student->user->name ?? 'Unknown');
            $groupName = $student->group->name ?? 'N/A';

            // Store in database
            DB::table('face_encodings')->updateOrInsert(
                ['student_id' => $studentId],
                [
                    'user_id' => $student->user_id,
                    'encoding_data' => json_encode([
                        'images' => $savedImages,
                        'student_name' => $studentName,
                        'group_name' => $groupName,
                        'registered' => true
                    ]),
                    'metadata' => json_encode([
                        'images_count' => count($savedImages),
                        'registered_at' => now()->toIso8601String(),
                        'group_id' => $student->group_id
                    ]),
                    'is_active' => true,
                    'created_at' => DB::raw('COALESCE(created_at, NOW())'),
                    'updated_at' => now()
                ]
            );

            return [
                'success' => true,
                'message' => 'Yuz muvaffaqiyatli ro\'yxatga olindi',
                'images_saved' => count($savedImages),
                'student' => [
                    'id' => $student->id,
                    'name' => $studentName,
                    'group' => $groupName
                ]
            ];

        } catch (Exception $e) {
            Log::error('Face registration error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Yuzni ro\'yxatga olishda xatolik: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Recognize face and mark attendance
     * Checks schedule and records to journal if class exists today
     */
    public function recognizeAndMarkAttendance(string $imageBase64, ?int $groupId = null): array
    {
        try {
            // Get registered students
            $query = DB::table('face_encodings')
                ->join('students', 'face_encodings.student_id', '=', 'students.id')
                ->leftJoin('users', 'students.user_id', '=', 'users.id')
                ->leftJoin('groups', 'students.group_id', '=', 'groups.id')
                ->select(
                    'face_encodings.*',
                    'students.id as student_id',
                    'students.group_id',
                    'students.student_id as student_code',
                    'students.photo_url',
                    'users.name as user_name',
                    'groups.name as group_name'
                )
                ->where('face_encodings.is_active', true);

            if ($groupId) {
                $query->where('students.group_id', $groupId);
            }

            $registeredStudents = $query->get();

            if ($registeredStudents->isEmpty()) {
                return [
                    'success' => false,
                    'recognized' => false,
                    'message' => 'Ro\'yxatga olingan talabalar topilmadi'
                ];
            }

            // Find matching student
            $matchedStudent = $this->findMatchingStudent($imageBase64, $registeredStudents);

            if (!$matchedStudent) {
                return [
                    'success' => true,
                    'recognized' => false,
                    'message' => 'Yuz tanilmadi'
                ];
            }

            // Check if group has class today
            $scheduleCheck = $this->checkTodaySchedule($matchedStudent->group_id);

            // Mark attendance in face_attendances table
            $attendanceResult = $this->markFaceAttendance($matchedStudent, $matchedStudent->confidence);

            // If group has class today and attendance was successful, also record to journal
            $journalResult = null;
            if ($scheduleCheck['has_class'] && $attendanceResult['success'] && isset($scheduleCheck['current_slot'])) {
                $journalResult = $this->recordToJournal(
                    $matchedStudent->student_id,
                    $matchedStudent->group_id,
                    $scheduleCheck['current_slot']
                );
            }

            return [
                'success' => true,
                'recognized' => true,
                'student' => [
                    'id' => $matchedStudent->student_id,
                    'name' => $matchedStudent->user_name ?? 'Unknown',
                    'student_code' => $matchedStudent->student_code,
                    'group' => $matchedStudent->group_name,
                    'confidence' => round($matchedStudent->confidence * 100, 1)
                ],
                'attendance' => $attendanceResult,
                'schedule' => $scheduleCheck,
                'journal' => $journalResult,
                'message' => $attendanceResult['success']
                    ? 'Davomat muvaffaqiyatli qayd qilindi'
                    : $attendanceResult['message']
            ];

        } catch (Exception $e) {
            Log::error('Face recognition error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Yuzni tanishda xatolik: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Find matching student from registered faces
     * In production with VPS, this would use actual face comparison
     */
    protected function findMatchingStudent(string $imageBase64, $registeredStudents)
    {
        if ($registeredStudents->isEmpty()) {
            return null;
        }

        // For demonstration/testing: simulate face matching
        // In production, implement actual face comparison using face_recognition library
        // or call external Python API

        $confidence = rand(75, 98) / 100;

        if ($confidence >= $this->confidenceThreshold) {
            $matched = $registeredStudents->random();
            $matched->confidence = $confidence;
            return $matched;
        }

        return null;
    }

    /**
     * Check if group has class today based on schedule
     */
    public function checkTodaySchedule(int $groupId): array
    {
        $today = Carbon::now();
        $dayOfWeek = $today->dayOfWeekIso; // 1 = Monday, 7 = Sunday
        $currentTime = $today->format('H:i');

        // Weekend check
        if ($dayOfWeek == 7) { // Sunday
            return [
                'has_class' => false,
                'reason' => 'Dam olish kuni (Yakshanba)',
                'current_slot' => null,
                'day_of_week' => $dayOfWeek
            ];
        }

        // Get active schedule for group
        $schedule = Schedule::where('group_id', $groupId)
            ->where('status', 'active')
            ->first();

        if (!$schedule) {
            return [
                'has_class' => false,
                'reason' => 'Faol jadval topilmadi',
                'current_slot' => null
            ];
        }

        // Get today's slots
        $todaySlots = ScheduleSlot::where('schedule_id', $schedule->id)
            ->byDay($dayOfWeek)
            ->with(['subject', 'teacher', 'room'])
            ->orderBy('time_slot')
            ->get();

        if ($todaySlots->isEmpty()) {
            return [
                'has_class' => false,
                'reason' => 'Bugun dars yo\'q',
                'current_slot' => null,
                'day_of_week' => $dayOfWeek
            ];
        }

        // Find current or upcoming slot
        $currentSlot = null;
        foreach ($todaySlots as $slot) {
            $slotTime = $this->parseTimeSlot($slot->time_slot);

            if ($slotTime && $currentTime >= $slotTime['start'] && $currentTime <= $slotTime['end']) {
                $currentSlot = $slot;
                break;
            }
        }

        // If no current slot, get the first slot of the day
        if (!$currentSlot) {
            $currentSlot = $todaySlots->first();
        }

        return [
            'has_class' => true,
            'current_slot' => $currentSlot ? [
                'id' => $currentSlot->id,
                'subject_id' => $currentSlot->subject_id,
                'subject_name' => $currentSlot->subject->name ?? 'N/A',
                'teacher_id' => $currentSlot->teacher_id,
                'teacher_name' => $currentSlot->teacher->full_name ?? ($currentSlot->teacher->first_name ?? 'N/A'),
                'room' => $currentSlot->room->name ?? 'N/A',
                'time_slot' => $currentSlot->time_slot,
                'lesson_type' => $currentSlot->lesson_type
            ] : null,
            'all_slots' => $todaySlots->map(fn($s) => [
                'time_slot' => $s->time_slot,
                'subject' => $s->subject->name ?? 'N/A',
                'lesson_type' => $s->lesson_type
            ])->toArray(),
            'day_of_week' => $dayOfWeek
        ];
    }

    /**
     * Parse time slot string to start/end times
     */
    protected function parseTimeSlot($timeSlot): ?array
    {
        // Handle format "08:00-09:30"
        if (strpos($timeSlot, '-') !== false) {
            list($start, $end) = explode('-', $timeSlot);
            return ['start' => trim($start), 'end' => trim($end)];
        }

        // Handle slot numbers (1-8)
        $slotTimes = [
            '1' => ['start' => '08:00', 'end' => '09:30'],
            '2' => ['start' => '09:40', 'end' => '11:10'],
            '3' => ['start' => '11:20', 'end' => '12:50'],
            '4' => ['start' => '13:30', 'end' => '15:00'],
            '5' => ['start' => '15:10', 'end' => '16:40'],
            '6' => ['start' => '16:50', 'end' => '18:20'],
            '7' => ['start' => '18:30', 'end' => '20:00'],
            '8' => ['start' => '20:10', 'end' => '21:40'],
        ];

        return $slotTimes[$timeSlot] ?? null;
    }

    /**
     * Mark attendance in face_attendances table
     */
    protected function markFaceAttendance($student, float $confidence): array
    {
        $today = Carbon::today()->toDateString();

        // Check if already marked today
        $existing = DB::table('face_attendances')
            ->where('student_id', $student->student_id)
            ->where('date', $today)
            ->first();

        if ($existing) {
            return [
                'success' => false,
                'message' => 'Bugun davomat allaqachon qayd qilingan',
                'existing_time' => $existing->check_in_time
            ];
        }

        $status = $this->calculateStatus(now());

        // Create new attendance record
        $id = DB::table('face_attendances')->insertGetId([
            'student_id' => $student->student_id,
            'group_id' => $student->group_id,
            'date' => $today,
            'check_in_time' => now()->format('H:i:s'),
            'confidence_score' => $confidence,
            'status' => $status,
            'method' => 'face_recognition',
            'marked_by' => auth()->id(),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Log the attendance
        DB::table('face_attendance_logs')->insert([
            'face_attendance_id' => $id,
            'action_type' => 'check_in',
            'confidence_score' => $confidence,
            'timestamp' => now(),
            'performed_by' => auth()->id(),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return [
            'success' => true,
            'attendance_id' => $id,
            'date' => $today,
            'check_in_time' => now()->format('H:i:s'),
            'status' => $status
        ];
    }

    /**
     * Record attendance to journal
     */
    protected function recordToJournal(int $studentId, int $groupId, ?array $currentSlot): ?array
    {
        if (!$currentSlot || !isset($currentSlot['subject_id'])) {
            return null;
        }

        try {
            // Find or create journal entry for this subject/group
            $journalEntry = JournalEntry::firstOrCreate(
                [
                    'subject_id' => $currentSlot['subject_id'],
                    'group_id' => $groupId,
                    'teacher_id' => $currentSlot['teacher_id']
                ],
                [
                    'academic_year_id' => $this->getCurrentAcademicYearId(),
                    'semester_id' => $this->getCurrentSemesterId()
                ]
            );

            // Check if attendance record already exists for today
            $existing = AttendanceRecord::where('journal_entry_id', $journalEntry->id)
                ->where('student_id', $studentId)
                ->where('lesson_date', Carbon::today())
                ->first();

            if ($existing) {
                return [
                    'success' => false,
                    'message' => 'Jurnalda bugungi davomat mavjud',
                    'existing_id' => $existing->id
                ];
            }

            // Create attendance record in journal
            $record = AttendanceRecord::create([
                'journal_entry_id' => $journalEntry->id,
                'student_id' => $studentId,
                'lesson_date' => Carbon::today(),
                'lesson_type' => $currentSlot['lesson_type'] ?? 'lecture',
                'time_slot' => $currentSlot['time_slot'],
                'status' => 'present',
                'marked_by' => auth()->id(),
                'marked_at' => now(),
                'notes' => 'Yuz orqali avtomatik qayd qilindi'
            ]);

            return [
                'success' => true,
                'record_id' => $record->id,
                'journal_entry_id' => $journalEntry->id,
                'subject' => $currentSlot['subject_name'],
                'message' => 'Jurnalga qayd qilindi'
            ];

        } catch (Exception $e) {
            Log::error('Journal record error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Jurnalga yozishda xatolik: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Calculate attendance status based on time
     */
    protected function calculateStatus(Carbon $time): string
    {
        $hour = $time->hour;
        $minute = $time->minute;

        // Office start at 8:00, late after 8:15, very late after 8:30
        if ($hour < 8 || ($hour == 8 && $minute <= 0)) {
            return 'early';
        } elseif ($hour == 8 && $minute <= 15) {
            return 'present';
        } elseif ($hour == 8 && $minute <= 30) {
            return 'late';
        } else {
            return 'very_late';
        }
    }

    /**
     * Get current academic year ID
     */
    protected function getCurrentAcademicYearId(): ?int
    {
        return DB::table('academic_years')
            ->where('is_active', true)
            ->value('id') ?? 1;
    }

    /**
     * Get current semester ID
     */
    protected function getCurrentSemesterId(): ?int
    {
        return DB::table('semesters')
            ->where('is_active', true)
            ->value('id') ?? 1;
    }

    /**
     * Get today's attendance list with filters
     */
    public function getTodayAttendance(?int $groupId = null): array
    {
        $query = DB::table('face_attendances as fa')
            ->join('students as s', 'fa.student_id', '=', 's.id')
            ->leftJoin('users as u', 's.user_id', '=', 'u.id')
            ->leftJoin('groups as g', 's.group_id', '=', 'g.id')
            ->select(
                'fa.*',
                's.student_id as student_code',
                's.first_name',
                's.last_name',
                's.photo_url',
                DB::raw("COALESCE(u.name, CONCAT(s.last_name, ' ', s.first_name)) as student_name"),
                'g.name as group_name',
                'g.course'
            )
            ->where('fa.date', Carbon::today()->toDateString())
            ->orderBy('fa.check_in_time', 'desc');

        if ($groupId) {
            $query->where('fa.group_id', $groupId);
        }

        $attendances = $query->get();

        // Get stats
        $stats = [
            'total' => $attendances->count(),
            'present' => $attendances->whereIn('status', ['present', 'early'])->count(),
            'late' => $attendances->where('status', 'late')->count(),
            'very_late' => $attendances->where('status', 'very_late')->count(),
            'early' => $attendances->where('status', 'early')->count()
        ];

        return [
            'success' => true,
            'date' => Carbon::today()->format('Y-m-d'),
            'attendances' => $attendances,
            'stats' => $stats
        ];
    }

    /**
     * Get attendance history with filters
     */
    public function getAttendanceHistory(
        ?int $groupId = null,
        ?string $startDate = null,
        ?string $endDate = null
    ): array {
        $query = DB::table('face_attendances as fa')
            ->join('students as s', 'fa.student_id', '=', 's.id')
            ->leftJoin('users as u', 's.user_id', '=', 'u.id')
            ->leftJoin('groups as g', 's.group_id', '=', 'g.id')
            ->select(
                'fa.*',
                's.student_id as student_code',
                's.photo_url',
                'u.name as student_name',
                'g.name as group_name',
                'g.course'
            )
            ->orderBy('fa.date', 'desc')
            ->orderBy('fa.check_in_time', 'desc');

        if ($groupId) {
            $query->where('fa.group_id', $groupId);
        }

        if ($startDate) {
            $query->where('fa.date', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('fa.date', '<=', $endDate);
        }

        return [
            'success' => true,
            'attendances' => $query->limit(500)->get()
        ];
    }

    /**
     * Get enrolled students (with face registration status)
     */
    public function getEnrolledStudents(?int $groupId = null, ?string $search = null): array
    {
        $query = DB::table('students as s')
            ->join('groups as g', 's.group_id', '=', 'g.id')
            ->join('face_encodings as fe', function($join) {
                $join->on('s.id', '=', 'fe.student_id')
                     ->where('fe.is_active', '=', true);
            })
            ->select(
                's.id',
                's.student_id as student_code',
                's.first_name',
                's.last_name',
                's.middle_name',
                's.photo_url',
                'g.id as group_id',
                'g.name as group_name',
                'g.course',
                'fe.id as face_encoding_id',
                'fe.updated_at as face_registered_at'
            )
            ->where('s.status', 'active')
            ->orderBy('g.course')
            ->orderBy('g.name')
            ->orderBy('s.last_name')
            ->orderBy('s.first_name');

        if ($groupId) {
            $query->where('s.group_id', $groupId);
        }

        // Search by name or student ID
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('s.first_name', 'like', "%{$search}%")
                  ->orWhere('s.last_name', 'like', "%{$search}%")
                  ->orWhere('s.middle_name', 'like', "%{$search}%")
                  ->orWhere('s.student_id', 'like', "%{$search}%")
                  ->orWhere(DB::raw("CONCAT(s.last_name, ' ', s.first_name)"), 'like', "%{$search}%");
            });
        }

        $students = $query->limit(100)->get();

        // Transform to format expected by JavaScript
        $students = $students->map(function($student) {
            $fullName = trim($student->last_name . ' ' . $student->first_name . ' ' . ($student->middle_name ?? ''));
            return [
                'id' => $student->id,
                // JavaScript expects these fields:
                'name' => $fullName,
                'student_id' => $student->student_code,
                'group' => $student->group_name,
                'course' => $student->course ?? 1,
                'enrolled_at' => $student->face_registered_at,
                'photo_data' => null,
                // Additional fields for other uses:
                'student_code' => $student->student_code,
                'first_name' => $student->first_name,
                'last_name' => $student->last_name,
                'middle_name' => $student->middle_name,
                'full_name' => $fullName,
                'photo' => $student->photo_url,
                'group_id' => $student->group_id,
                'group_name' => $student->group_name,
                'has_face_registered' => true,
                'face_registered_at' => $student->face_registered_at
            ];
        });

        return [
            'success' => true,
            'students' => $students,
            'count' => $students->count()
        ];
    }

    /**
     * Check if student has registered face
     */
    public function hasRegisteredFace(int $studentId): bool
    {
        return DB::table('face_encodings')
            ->where('student_id', $studentId)
            ->where('is_active', true)
            ->exists();
    }

    /**
     * Delete student face registration
     */
    public function deleteStudentFace(int $studentId): array
    {
        try {
            // Delete images
            $userDir = $this->faceDataPath . '/images/student_' . $studentId;
            if (file_exists($userDir)) {
                $files = glob($userDir . '/*');
                foreach ($files as $file) {
                    if (is_file($file)) {
                        unlink($file);
                    }
                }
                @rmdir($userDir);
            }

            // Delete from database
            DB::table('face_encodings')
                ->where('student_id', $studentId)
                ->delete();

            return [
                'success' => true,
                'message' => 'Yuz ma\'lumotlari o\'chirildi'
            ];

        } catch (Exception $e) {
            Log::error('Face deletion error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'O\'chirishda xatolik: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get all groups
     */
    public function getGroups(): array
    {
        $groups = DB::table('groups')
            ->where('is_active', true)
            ->orderBy('course')
            ->orderBy('name')
            ->get();

        return [
            'success' => true,
            'groups' => $groups
        ];
    }

    /**
     * Export attendance to array (for Excel)
     */
    public function exportAttendanceData(
        ?int $groupId = null,
        ?string $startDate = null,
        ?string $endDate = null
    ): array {
        $query = DB::table('face_attendances as fa')
            ->join('students as s', 'fa.student_id', '=', 's.id')
            ->leftJoin('users as u', 's.user_id', '=', 'u.id')
            ->leftJoin('groups as g', 's.group_id', '=', 'g.id')
            ->select(
                'fa.date',
                's.student_id as student_code',
                'u.name as student_name',
                'g.name as group_name',
                'g.course',
                'fa.check_in_time',
                'fa.check_out_time',
                'fa.status',
                'fa.confidence_score',
                'fa.method'
            )
            ->orderBy('fa.date', 'desc')
            ->orderBy('g.name')
            ->orderBy('u.name');

        if ($groupId) {
            $query->where('fa.group_id', $groupId);
        }

        if ($startDate) {
            $query->where('fa.date', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('fa.date', '<=', $endDate);
        }

        return $query->get()->toArray();
    }

    // ==========================================
    // Legacy methods for backward compatibility
    // ==========================================

    public function registerFace($userId, array $images, $userName = null)
    {
        // Find student by user_id
        $student = Student::where('user_id', $userId)->first();
        if ($student) {
            return $this->registerStudentFace($student->id, $images);
        }

        return ['success' => false, 'message' => 'Talaba topilmadi'];
    }

    public function recognizeFace($imageBase64)
    {
        return $this->recognizeAndMarkAttendance($imageBase64);
    }

    public function checkIn($imageBase64, $location = null)
    {
        return $this->recognizeAndMarkAttendance($imageBase64);
    }

    public function checkOut($imageBase64, $location = null)
    {
        // For now, just recognize - check-out logic can be added if needed
        return $this->recognizeAndMarkAttendance($imageBase64);
    }

    public function getAttendanceStats($startDate = null, $endDate = null)
    {
        $query = DB::table('face_attendances');

        if ($startDate) {
            $query->where('date', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('date', '<=', $endDate);
        }

        $total = $query->count();
        $byStatus = DB::table('face_attendances')
            ->selectRaw('status, COUNT(*) as count')
            ->when($startDate, fn($q) => $q->where('date', '>=', $startDate))
            ->when($endDate, fn($q) => $q->where('date', '<=', $endDate))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        return [
            'success' => true,
            'stats' => [
                'total' => $total,
                'by_status' => $byStatus
            ]
        ];
    }

    public function getUsersWithFaces()
    {
        return DB::table('face_encodings')
            ->join('students', 'face_encodings.student_id', '=', 'students.id')
            ->leftJoin('users', 'students.user_id', '=', 'users.id')
            ->leftJoin('groups', 'students.group_id', '=', 'groups.id')
            ->select(
                'students.id',
                'users.name',
                'users.email',
                'groups.name as group_name',
                'face_encodings.created_at as registered_at'
            )
            ->where('face_encodings.is_active', true)
            ->get();
    }

    public function updateFace($userId, array $images)
    {
        $student = Student::where('user_id', $userId)->first();
        if ($student) {
            return $this->registerStudentFace($student->id, $images);
        }
        return ['success' => false, 'message' => 'Talaba topilmadi'];
    }

    public function deleteFace($userId)
    {
        $student = Student::where('user_id', $userId)->first();
        if ($student) {
            return $this->deleteStudentFace($student->id);
        }
        return ['success' => false, 'message' => 'Talaba topilmadi'];
    }
}
