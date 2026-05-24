<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use App\Models\User;
use App\Models\Attendance;
use Exception;

class LocalFaceRecognitionService
{
    protected $faceDataPath;
    protected $confidenceThreshold = 0.85;

    public function __construct()
    {
        $this->faceDataPath = storage_path('app/face_data');
        $this->ensureDirectoriesExist();
    }

    /**
     * Ensure required directories exist
     */
    protected function ensureDirectoriesExist()
    {
        if (!file_exists($this->faceDataPath)) {
            mkdir($this->faceDataPath, 0755, true);
        }

        if (!file_exists($this->faceDataPath . '/encodings')) {
            mkdir($this->faceDataPath . '/encodings', 0755, true);
        }

        if (!file_exists($this->faceDataPath . '/images')) {
            mkdir($this->faceDataPath . '/images', 0755, true);
        }
    }

    /**
     * Register face for user (stores images locally)
     */
    public function registerFace($userId, array $images, $userName = null)
    {
        try {
            $userDir = $this->faceDataPath . '/images/' . $userId;
            if (!file_exists($userDir)) {
                mkdir($userDir, 0755, true);
            }

            // Save images
            $savedImages = [];
            foreach ($images as $index => $imageBase64) {
                $imageData = base64_decode($imageBase64);
                $fileName = $userId . '_' . time() . '_' . $index . '.jpg';
                $filePath = $userDir . '/' . $fileName;

                file_put_contents($filePath, $imageData);
                $savedImages[] = $fileName;
            }

            // Store in database
            DB::table('face_encodings')->updateOrInsert(
                ['user_id' => $userId],
                [
                    'encoding_data' => json_encode([
                        'images' => $savedImages,
                        'user_name' => $userName,
                        'registered' => true
                    ]),
                    'metadata' => json_encode([
                        'images_count' => count($savedImages),
                        'registered_at' => now()
                    ]),
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );

            return [
                'success' => true,
                'message' => 'Face registered successfully',
                'images_saved' => count($savedImages)
            ];

        } catch (Exception $e) {
            Log::error('Face registration error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Face registration failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Simple face recognition simulation
     * In production, you would use actual face recognition algorithms
     */
    public function recognizeFace($imageBase64)
    {
        try {
            // For demonstration: randomly match with registered users
            // In production, use actual face recognition library

            $registeredUsers = DB::table('face_encodings')
                ->join('users', 'face_encodings.user_id', '=', 'users.id')
                ->select('users.id', 'users.name', 'face_encodings.encoding_data')
                ->get();

            if ($registeredUsers->isEmpty()) {
                return [
                    'success' => true,
                    'recognized' => false,
                    'message' => 'No registered faces found'
                ];
            }

            // Simulate recognition (in production, use actual face matching)
            $randomUser = $registeredUsers->random();
            $confidence = rand(85, 99) / 100;

            if ($confidence >= $this->confidenceThreshold) {
                return [
                    'success' => true,
                    'recognized' => true,
                    'users' => [[
                        'user_id' => $randomUser->id,
                        'name' => $randomUser->name,
                        'confidence' => $confidence
                    ]]
                ];
            }

            return [
                'success' => true,
                'recognized' => false,
                'message' => 'Face not recognized'
            ];

        } catch (Exception $e) {
            Log::error('Face recognition error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Recognition failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Process check-in locally
     */
    public function checkIn($imageBase64, $location = null)
    {
        try {
            // Recognize face
            $result = $this->recognizeFace($imageBase64);

            if (!$result['success'] || !$result['recognized']) {
                return [
                    'success' => false,
                    'message' => 'Face not recognized'
                ];
            }

            $userId = $result['users'][0]['user_id'];
            $confidence = $result['users'][0]['confidence'];
            $today = now()->toDateString();

            // Check if already checked in
            $attendance = Attendance::where('user_id', $userId)
                ->where('date', $today)
                ->first();

            if ($attendance && $attendance->check_in) {
                return [
                    'success' => false,
                    'message' => 'Already checked in today'
                ];
            }

            // Create attendance record
            if (!$attendance) {
                $attendance = new Attendance();
                $attendance->user_id = $userId;
                $attendance->date = $today;
            }

            $attendance->check_in = now();
            $attendance->status = 'present';
            $attendance->location = $location;
            $attendance->face_confidence = $confidence;
            $attendance->save();

            $user = User::find($userId);

            return [
                'success' => true,
                'message' => 'Check-in successful',
                'data' => [
                    'user_id' => $userId,
                    'user_name' => $user->name,
                    'check_in_time' => $attendance->check_in,
                    'confidence' => round($confidence * 100, 2)
                ]
            ];

        } catch (Exception $e) {
            Log::error('Check-in error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Check-in failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Process check-out locally
     */
    public function checkOut($imageBase64, $location = null)
    {
        try {
            // Recognize face
            $result = $this->recognizeFace($imageBase64);

            if (!$result['success'] || !$result['recognized']) {
                return [
                    'success' => false,
                    'message' => 'Face not recognized'
                ];
            }

            $userId = $result['users'][0]['user_id'];
            $today = now()->toDateString();

            // Find today's attendance
            $attendance = Attendance::where('user_id', $userId)
                ->where('date', $today)
                ->first();

            if (!$attendance || !$attendance->check_in) {
                return [
                    'success' => false,
                    'message' => 'No check-in found for today'
                ];
            }

            if ($attendance->check_out) {
                return [
                    'success' => false,
                    'message' => 'Already checked out today'
                ];
            }

            // Update check-out
            $checkInTime = \Carbon\Carbon::parse($attendance->check_in);
            $checkOutTime = now();

            $attendance->check_out = $checkOutTime;
            $attendance->total_hours = $checkInTime->diffInMinutes($checkOutTime) / 60;
            $attendance->save();

            $user = User::find($userId);

            return [
                'success' => true,
                'message' => 'Check-out successful',
                'data' => [
                    'user_id' => $userId,
                    'user_name' => $user->name,
                    'check_out_time' => $attendance->check_out,
                    'total_hours' => round($attendance->total_hours, 2)
                ]
            ];

        } catch (Exception $e) {
            Log::error('Check-out error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Check-out failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get attendance history
     */
    public function getAttendanceHistory($userId, $startDate = null, $endDate = null)
    {
        try {
            $query = Attendance::where('user_id', $userId);

            if ($startDate) {
                $query->where('date', '>=', $startDate);
            }

            if ($endDate) {
                $query->where('date', '<=', $endDate);
            }

            $attendances = $query->orderBy('date', 'desc')->get();

            return [
                'success' => true,
                'data' => $attendances,
                'total' => $attendances->count()
            ];

        } catch (Exception $e) {
            Log::error('Attendance history error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to fetch history: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get today's attendance
     */
    public function getTodayAttendance()
    {
        try {
            $today = now()->toDateString();

            $attendances = Attendance::where('date', $today)
                ->with('user:id,name,email')
                ->get();

            $stats = [
                'total_present' => $attendances->count(),
                'checked_in' => $attendances->whereNotNull('check_in')->count(),
                'checked_out' => $attendances->whereNotNull('check_out')->count()
            ];

            return [
                'success' => true,
                'data' => $attendances,
                'stats' => $stats
            ];

        } catch (Exception $e) {
            Log::error('Today attendance error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to fetch attendance: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get attendance statistics
     */
    public function getAttendanceStats($startDate = null, $endDate = null)
    {
        try {
            $query = Attendance::query();

            if ($startDate) {
                $query->where('date', '>=', $startDate);
            }

            if ($endDate) {
                $query->where('date', '<=', $endDate);
            }

            $stats = [
                'total_days' => $query->distinct('date')->count('date'),
                'total_attendances' => $query->count(),
                'average_confidence' => $query->avg('face_confidence'),
                'average_hours' => $query->avg('total_hours')
            ];

            return [
                'success' => true,
                'stats' => $stats
            ];

        } catch (Exception $e) {
            Log::error('Attendance stats error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to fetch statistics: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Check if user has registered face
     */
    public function hasRegisteredFace($userId)
    {
        return DB::table('face_encodings')
            ->where('user_id', $userId)
            ->exists();
    }

    /**
     * Update face for user
     */
    public function updateFace($userId, array $images)
    {
        // Delete old images
        $this->deleteFace($userId);

        // Register new images
        return $this->registerFace($userId, $images);
    }

    /**
     * Delete face for user
     */
    public function deleteFace($userId)
    {
        try {
            // Delete images
            $userDir = $this->faceDataPath . '/images/' . $userId;
            if (file_exists($userDir)) {
                $files = glob($userDir . '/*');
                foreach ($files as $file) {
                    unlink($file);
                }
                rmdir($userDir);
            }

            // Delete from database
            DB::table('face_encodings')
                ->where('user_id', $userId)
                ->delete();

            return [
                'success' => true,
                'message' => 'Face deleted successfully'
            ];

        } catch (Exception $e) {
            Log::error('Face deletion error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Face deletion failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get users with faces
     */
    public function getUsersWithFaces()
    {
        return DB::table('face_encodings')
            ->join('users', 'face_encodings.user_id', '=', 'users.id')
            ->select('users.id', 'users.name', 'users.email', 'face_encodings.created_at as registered_at')
            ->get();
    }
}