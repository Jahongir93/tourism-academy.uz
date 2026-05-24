<?php

namespace App\Http\Controllers\Hemis;

use App\Http\Controllers\Controller;
use App\Services\HemisDataSyncService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class HemisSyncController extends Controller
{
    protected $hemisSync;

    public function __construct(HemisDataSyncService $hemisSync)
    {
        $this->hemisSync = $hemisSync;
    }

    /**
     * Display sync dashboard
     */
    public function index()
    {
        // Get last sync information from logs or cache
        $lastSync = cache()->get('hemis_last_sync');

        return view('hemis.sync.index', compact('lastSync'));
    }

    /**
     * Sync students from HEMIS
     */
    public function syncStudents(Request $request)
    {
        $limit = $request->input('limit', 100);
        $offset = $request->input('offset', 0);

        try {
            $result = $this->hemisSync->syncStudents($limit, $offset);

            if ($result['success']) {
                cache()->put('hemis_last_sync_students', now(), 3600);

                return response()->json([
                    'success' => true,
                    'message' => "Sinxronlash muvaffaqiyatli: {$result['synced']} talaba",
                    'data' => $result,
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'] ?? 'Sinxronlashda xatolik',
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('Student sync error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Xatolik yuz berdi: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Sync teachers from HEMIS
     */
    public function syncTeachers(Request $request)
    {
        $limit = $request->input('limit', 100);
        $offset = $request->input('offset', 0);

        try {
            $result = $this->hemisSync->syncTeachers($limit, $offset);

            if ($result['success']) {
                cache()->put('hemis_last_sync_teachers', now(), 3600);

                return response()->json([
                    'success' => true,
                    'message' => "Sinxronlash muvaffaqiyatli: {$result['synced']} o'qituvchi",
                    'data' => $result,
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'] ?? 'Sinxronlashda xatolik',
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('Teacher sync error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Xatolik yuz berdi: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Sync student grades from HEMIS
     */
    public function syncGrades(Request $request)
    {
        $request->validate([
            'hemis_student_id' => 'required|string',
        ]);

        try {
            $result = $this->hemisSync->syncStudentGrades($request->hemis_student_id);

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => "Baholar sinxronlashtirildi: {$result['synced']} ta",
                    'data' => $result,
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'] ?? 'Sinxronlashda xatolik',
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('Grades sync error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Xatolik yuz berdi: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Full system sync
     */
    public function fullSync(Request $request)
    {
        try {
            set_time_limit(600); // 10 minutes

            $result = $this->hemisSync->fullSync();

            if ($result['success']) {
                cache()->put('hemis_last_full_sync', now(), 3600);

                return response()->json([
                    'success' => true,
                    'message' => 'To\'liq sinxronlash muvaffaqiyatli yakunlandi',
                    'data' => $result,
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'To\'liq sinxronlashda xatoliklar mavjud',
                    'data' => $result,
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('Full sync error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Xatolik yuz berdi: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get curriculum from HEMIS
     */
    public function getCurriculum(Request $request)
    {
        $request->validate([
            'specialty_code' => 'required|string',
        ]);

        try {
            $curriculum = $this->hemisSync->getCurriculum($request->specialty_code);

            if ($curriculum) {
                return response()->json([
                    'success' => true,
                    'data' => $curriculum,
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'O\'quv rejasi topilmadi',
                ], 404);
            }

        } catch (\Exception $e) {
            Log::error('Curriculum fetch error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Xatolik yuz berdi: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get schedule from HEMIS
     */
    public function getSchedule(Request $request)
    {
        $request->validate([
            'group_name' => 'required|string',
        ]);

        try {
            $schedule = $this->hemisSync->getSchedule($request->group_name);

            if ($schedule) {
                return response()->json([
                    'success' => true,
                    'data' => $schedule,
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Dars jadvali topilmadi',
                ], 404);
            }

        } catch (\Exception $e) {
            Log::error('Schedule fetch error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Xatolik yuz berdi: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get academic calendar from HEMIS
     */
    public function getAcademicCalendar()
    {
        try {
            $calendar = $this->hemisSync->getAcademicCalendar();

            if ($calendar) {
                return response()->json([
                    'success' => true,
                    'data' => $calendar,
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Akademik kalendar topilmadi',
                ], 404);
            }

        } catch (\Exception $e) {
            Log::error('Academic calendar fetch error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Xatolik yuz berdi: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display HEMIS settings page
     */
    public function settings()
    {
        $hemisUrl = config('services.hemis.base_url');
        $clientId = config('services.hemis.client_id');
        $isConfigured = !empty($hemisUrl) && !empty($clientId);

        return view('hemis.settings', compact('hemisUrl', 'clientId', 'isConfigured'));
    }

    /**
     * Update HEMIS settings
     */
    public function updateSettings(Request $request)
    {
        $request->validate([
            'hemis_url' => 'required|url',
            'client_id' => 'required|string',
            'client_secret' => 'required|string',
        ]);

        try {
            // Update .env file
            $this->updateEnvFile([
                'HEMIS_BASE_URL' => $request->hemis_url,
                'HEMIS_CLIENT_ID' => $request->client_id,
                'HEMIS_CLIENT_SECRET' => $request->client_secret,
            ]);

            // Clear config cache
            \Artisan::call('config:clear');

            return redirect()->back()->with('success', 'HEMIS sozlamalari muvaffaqiyatli yangilandi');
        } catch (\Exception $e) {
            Log::error('HEMIS settings update error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Xatolik yuz berdi: ' . $e->getMessage());
        }
    }

    /**
     * Update .env file
     */
    private function updateEnvFile(array $data)
    {
        $envFile = base_path('.env');
        $envContent = file_get_contents($envFile);

        foreach ($data as $key => $value) {
            $pattern = "/^{$key}=.*/m";
            $replacement = "{$key}={$value}";

            if (preg_match($pattern, $envContent)) {
                $envContent = preg_replace($pattern, $replacement, $envContent);
            } else {
                $envContent .= "\n{$replacement}";
            }
        }

        file_put_contents($envFile, $envContent);
    }

    /**
     * Test HEMIS connection
     */
    public function testConnection()
    {
        try {
            // Try to get service token
            $token = $this->hemisSync->getServiceToken();

            if ($token) {
                return response()->json([
                    'success' => true,
                    'message' => 'HEMIS bilan bog\'lanish muvaffaqiyatli',
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'HEMIS bilan bog\'lanishda xatolik',
                ], 500);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Xatolik: ' . $e->getMessage(),
            ], 500);
        }
    }
}
