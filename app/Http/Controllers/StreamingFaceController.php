<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\LocalFaceRecognitionService;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Attendance;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class StreamingFaceController extends Controller
{
    protected $faceService;

    public function __construct()
    {
        $this->faceService = new LocalFaceRecognitionService();
    }

    /**
     * Process real-time video stream frame
     * Avtomatik yuzni tanish va davomatni qayd qilish
     */
    public function processStreamFrame(Request $request)
    {
        $request->validate([
            'frame' => 'required|string', // Base64 encoded frame
            'camera_id' => 'required|string',
            'timestamp' => 'required|integer'
        ]);

        try {
            // Frameni process qilish
            $result = $this->faceService->recognizeFace($request->frame);

            if ($result['success'] && isset($result['users']) && count($result['users']) > 0) {
                $processedUsers = [];

                foreach ($result['users'] as $recognizedUser) {
                    $userId = $recognizedUser['user_id'];
                    $confidence = $recognizedUser['confidence'];

                    // Minimum confidence tekshirish (90%)
                    if ($confidence < 0.90) {
                        continue;
                    }

                    // Cache orqali takrorlanishni oldini olish (5 daqiqa)
                    $cacheKey = "face_detected_{$userId}_{$request->camera_id}";
                    if (Cache::has($cacheKey)) {
                        continue;
                    }

                    // Userning bugungi davomati
                    $user = User::find($userId);
                    if (!$user) {
                        continue;
                    }

                    $today = now()->toDateString();
                    $attendance = Attendance::where('user_id', $userId)
                        ->where('date', $today)
                        ->first();

                    $action = null;
                    $time = now();

                    if (!$attendance) {
                        // Yangi davomat (kirish)
                        $attendance = Attendance::create([
                            'user_id' => $userId,
                            'date' => $today,
                            'check_in' => $time,
                            'status' => 'present',
                            'location' => $request->camera_id,
                            'face_confidence' => $confidence
                        ]);
                        $action = 'check_in';
                    } elseif (!$attendance->check_out) {
                        // Chiqishni qayd qilish (agar oxirgi kirish 4 soatdan ko'p bo'lsa)
                        $checkInTime = \Carbon\Carbon::parse($attendance->check_in);
                        if ($checkInTime->diffInHours($time) >= 4) {
                            $attendance->update([
                                'check_out' => $time,
                                'total_hours' => $checkInTime->diffInMinutes($time) / 60
                            ]);
                            $action = 'check_out';
                        }
                    }

                    if ($action) {
                        // 5 daqiqalik cache
                        Cache::put($cacheKey, true, 300);

                        $processedUsers[] = [
                            'user_id' => $userId,
                            'name' => $user->name,
                            'action' => $action,
                            'time' => $time->format('H:i:s'),
                            'confidence' => round($confidence * 100, 2)
                        ];

                        // Real-time event trigger
                        broadcast(new \App\Events\FaceDetected($user, $action, $time, $request->camera_id));
                    }
                }

                return response()->json([
                    'success' => true,
                    'processed_users' => $processedUsers,
                    'timestamp' => now()->timestamp
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'No faces detected',
                'timestamp' => now()->timestamp
            ]);

        } catch (\Exception $e) {
            Log::error('Stream processing error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Processing failed'
            ], 500);
        }
    }

    /**
     * Batch process multiple frames
     */
    public function processBatchFrames(Request $request)
    {
        $request->validate([
            'frames' => 'required|array|min:1|max:10',
            'frames.*.frame' => 'required|string',
            'frames.*.timestamp' => 'required|integer',
            'camera_id' => 'required|string'
        ]);

        $results = [];

        foreach ($request->frames as $frameData) {
            $frameRequest = new Request([
                'frame' => $frameData['frame'],
                'timestamp' => $frameData['timestamp'],
                'camera_id' => $request->camera_id
            ]);

            $result = $this->processStreamFrame($frameRequest);
            $results[] = json_decode($result->getContent(), true);
        }

        return response()->json([
            'success' => true,
            'results' => $results
        ]);
    }

    /**
     * Get active monitoring dashboard data
     */
    public function getMonitoringData(Request $request)
    {
        $cameraId = $request->get('camera_id', 'all');

        // Bugungi statistikalar
        $today = now()->toDateString();

        $query = Attendance::where('date', $today);

        if ($cameraId !== 'all') {
            $query->where('location', $cameraId);
        }

        $stats = [
            'total_present' => $query->count(),
            'currently_in' => $query->whereNotNull('check_in')->whereNull('check_out')->count(),
            'checked_out' => $query->whereNotNull('check_out')->count(),
            'average_confidence' => $query->avg('face_confidence') * 100
        ];

        // Oxirgi 10 ta harakat
        $recentActivity = $query->with('user:id,name,email')
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($attendance) {
                return [
                    'user_name' => $attendance->user->name,
                    'check_in' => $attendance->check_in,
                    'check_out' => $attendance->check_out,
                    'status' => $attendance->check_out ? 'checked_out' : 'currently_in',
                    'location' => $attendance->location
                ];
            });

        // Soatlik taqsimot
        $hourlyDistribution = DB::table('attendances')
            ->where('date', $today)
            ->when($cameraId !== 'all', function ($q) use ($cameraId) {
                return $q->where('location', $cameraId);
            })
            ->selectRaw('HOUR(check_in) as hour, COUNT(*) as count')
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();

        return response()->json([
            'success' => true,
            'stats' => $stats,
            'recent_activity' => $recentActivity,
            'hourly_distribution' => $hourlyDistribution,
            'timestamp' => now()->timestamp
        ]);
    }

    /**
     * Get camera status and configuration
     */
    public function getCameraStatus(Request $request)
    {
        $cameras = [
            [
                'id' => 'entrance_main',
                'name' => 'Asosiy kirish',
                'status' => 'active',
                'last_detection' => Cache::get('camera_last_detection_entrance_main'),
                'total_detections_today' => Cache::get('camera_detections_today_entrance_main', 0)
            ],
            [
                'id' => 'entrance_secondary',
                'name' => 'Ikkinchi kirish',
                'status' => 'active',
                'last_detection' => Cache::get('camera_last_detection_entrance_secondary'),
                'total_detections_today' => Cache::get('camera_detections_today_entrance_secondary', 0)
            ]
        ];

        return response()->json([
            'success' => true,
            'cameras' => $cameras
        ]);
    }

    /**
     * Manual override for attendance
     */
    public function manualOverride(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'action' => 'required|in:check_in,check_out',
            'time' => 'nullable|date_format:Y-m-d H:i:s',
            'reason' => 'required|string|max:255'
        ]);

        $time = $request->time ? \Carbon\Carbon::parse($request->time) : now();
        $today = $time->toDateString();

        $attendance = Attendance::where('user_id', $request->user_id)
            ->where('date', $today)
            ->first();

        if ($request->action === 'check_in') {
            if (!$attendance) {
                $attendance = Attendance::create([
                    'user_id' => $request->user_id,
                    'date' => $today,
                    'check_in' => $time,
                    'status' => 'present',
                    'manual_override' => true,
                    'override_reason' => $request->reason,
                    'override_by' => auth()->id()
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'User already checked in today'
                ], 400);
            }
        } else {
            if ($attendance && !$attendance->check_out) {
                $checkInTime = \Carbon\Carbon::parse($attendance->check_in);
                $attendance->update([
                    'check_out' => $time,
                    'total_hours' => $checkInTime->diffInMinutes($time) / 60,
                    'manual_override' => true,
                    'override_reason' => $request->reason,
                    'override_by' => auth()->id()
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'No active check-in found for user'
                ], 400);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Manual override successful',
            'attendance' => $attendance
        ]);
    }
}