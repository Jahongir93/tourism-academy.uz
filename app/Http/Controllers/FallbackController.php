<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\UsesDatabaseFallback;
use App\Services\DatabaseFallbackService;

class FallbackController extends Controller
{
    use UsesDatabaseFallback;

    protected $fallbackService;

    public function __construct(DatabaseFallbackService $fallbackService)
    {
        $this->fallbackService = $fallbackService;
    }

    public function index()
    {
        if ($this->fallbackService->isConnected()) {
            return redirect('/');
        }

        $courses = $this->fallbackService->getData('courses');
        $users = $this->fallbackService->getData('users');

        return view('fallback.index', compact('courses', 'users'));
    }

    public function getCourse($id)
    {
        return $this->checkDatabaseAndExecute(
            function() use ($id) {
                $course = \App\Models\Course::findOrFail($id);
                return view('courses.show', compact('course'));
            },
            function($fallbackService) use ($id) {
                $course = $fallbackService->getData('courses', $id);
                if (!$course) {
                    abort(404, 'Kurs topilmadi');
                }
                return view('fallback.course', compact('course'));
            }
        );
    }

    public function storeCourse(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0'
        ]);

        return $this->checkDatabaseAndExecute(
            function() use ($data) {
                $course = \App\Models\Course::create($data);
                return response()->json($course, 201);
            },
            function($fallbackService) use ($data) {
                $course = $fallbackService->saveData('courses', $data);
                return response()->json($course, 201);
            }
        );
    }

    public function syncData()
    {
        if (!$this->fallbackService->isConnected()) {
            return response()->json([
                'error' => 'Database is not available for syncing'
            ], 503);
        }

        $result = $this->fallbackService->syncToDatabase();

        return response()->json([
            'success' => $result,
            'message' => $result ? 'Data synced successfully' : 'Sync failed'
        ]);
    }

    public function checkStatus()
    {
        $response = [
            'database_connected' => $this->fallbackService->isConnected(),
            'fallback_enabled' => config('database_fallback.fallback_enabled'),
            'demo_mode' => config('database_fallback.demo_mode'),
            'storage_path' => config('database_fallback.storage_path')
        ];

        // CSRF tokenni yangilash
        if (request()->session()) {
            $response['new_token'] = csrf_token();
        }

        return response()->json($response);
    }
}