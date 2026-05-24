<?php

namespace App\Http\Controllers\LMS;

use App\Http\Controllers\Controller;
use App\Models\LmsPracticeTest;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TestController extends Controller
{
    /**
     * Display listing of tests
     */
    public function index(Request $request)
    {
        $query = LmsPracticeTest::with(['subject', 'teacher']);
        
        // Filter by subject
        if ($request->has('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }
        
        // Filter by difficulty
        if ($request->has('difficulty')) {
            $query->where('difficulty', $request->difficulty);
        }
        
        // For teachers, show only their tests
        $user = Auth::user();
        if ($user->hasRole('Teacher')) {
            $query->where('teacher_id', $user->id);
        }
        
        $tests = $query->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->paginate(12);
            
        $subjects = Subject::where('is_active', true)->orderBy('name_uz')->get();
        
        return view('lms.tests.index', compact('tests', 'subjects'));
    }

    /**
     * Show form for creating new test
     */
    public function create()
    {
        $subjects = Subject::where('is_active', true)->orderBy('name_uz')->get();
        return view('lms.tests.create', compact('subjects'));
    }

    /**
     * Store new test
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'subject_id' => 'nullable|exists:subjects,id',
            'difficulty' => 'required|in:easy,medium,hard',
            'time_limit' => 'nullable|integer|min:1|max:300',
            'passing_score' => 'required|integer|min:0|max:100',
            'attempts_allowed' => 'nullable|integer|min:1|max:10',
            'available_from' => 'nullable|date',
            'available_until' => 'nullable|date|after:available_from',
            'shuffle_questions' => 'boolean',
            'shuffle_answers' => 'boolean',
            'show_correct_answers' => 'boolean',
            'allow_review' => 'boolean',
            'auto_grade' => 'boolean',
            'questions' => 'required|array|min:1',
            'questions.*.question' => 'required|string',
            'questions.*.type' => 'required|in:multiple_choice,true_false,short_answer',
            'questions.*.options' => 'required_if:questions.*.type,multiple_choice|array',
            'questions.*.correct_answer' => 'required',
            'questions.*.points' => 'required|integer|min:1',
            'questions.*.explanation' => 'nullable|string'
        ]);
        
        $user = Auth::user();
        $teacher = $user;
        
        if (!$teacher) {
            return back()->with('error', 'Siz o\'qituvchi sifatida ro\'yxatdan o\'tmagan');
        }
        
        // Create test
        $testData = $validated;
        unset($testData['questions']);
        $testData['teacher_id'] = $teacher->id;
        $testData['question_count'] = count($validated['questions']);
        $testData['total_points'] = collect($validated['questions'])->sum('points');
        
        $test = LmsPracticeTest::create($testData);
        
        // Create questions
        foreach ($validated['questions'] as $index => $questionData) {
            $questionData['test_id'] = $test->id;
            $questionData['order_number'] = $index + 1;
            
            // Convert options and correct_answer to JSON
            if (isset($questionData['options'])) {
                $questionData['options'] = json_encode($questionData['options']);
            }
            $questionData['correct_answer'] = json_encode($questionData['correct_answer']);
            
            DB::table('lms_test_questions')->insert(array_merge($questionData, [
                'created_at' => now(),
                'updated_at' => now()
            ]));
        }
        
        return redirect()->route('lms.tests.show', $test)
            ->with('success', 'Test muvaffaqiyatli yaratildi!');
    }

    /**
     * Display test details
     */
    public function show(LmsPracticeTest $test)
    {
        $test->load(['subject', 'teacher']);
        
        // Get questions if teacher or admin
        $user = Auth::user();
        $questions = null;
        if ($user->hasRole('admin') || $test->teacher_id == $user->id) {
            $questions = DB::table('lms_test_questions')
                ->where('test_id', $test->id)
                ->orderBy('order_number')
                ->get();
        }
        
        // Get user's attempts
        $attempts = DB::table('lms_test_attempts')
            ->where('test_id', $test->id)
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('lms.tests.show', compact('test', 'questions', 'attempts'));
    }

    /**
     * Show form for editing test
     */
    public function edit(LmsPracticeTest $test)
    {
        // Check permission
        $user = Auth::user();
        if (!$user->hasRole('admin') && $test->teacher_id != $user->id) {
            abort(403, 'Sizda bu testni tahrirlash huquqi yo\'q');
        }
        
        $subjects = Subject::where('is_active', true)->orderBy('name_uz')->get();
        $questions = DB::table('lms_test_questions')
            ->where('test_id', $test->id)
            ->orderBy('order_number')
            ->get()
            ->map(function ($q) {
                $q->options = json_decode($q->options);
                $q->correct_answer = json_decode($q->correct_answer);
                return $q;
            });
        
        return view('lms.tests.edit', compact('test', 'subjects', 'questions'));
    }

    /**
     * Update test
     */
    public function update(Request $request, LmsPracticeTest $test)
    {
        // Check permission
        $user = Auth::user();
        if (!$user->hasRole('admin') && $test->teacher_id != $user->id) {
            abort(403, 'Sizda bu testni tahrirlash huquqi yo\'q');
        }
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'subject_id' => 'nullable|exists:subjects,id',
            'difficulty' => 'required|in:easy,medium,hard',
            'time_limit' => 'nullable|integer|min:1|max:300',
            'passing_score' => 'required|integer|min:0|max:100',
            'attempts_allowed' => 'nullable|integer|min:1|max:10',
            'available_from' => 'nullable|date',
            'available_until' => 'nullable|date|after:available_from',
            'shuffle_questions' => 'boolean',
            'shuffle_answers' => 'boolean',
            'show_correct_answers' => 'boolean',
            'allow_review' => 'boolean',
            'auto_grade' => 'boolean',
            'is_active' => 'boolean'
        ]);
        
        $test->update($validated);
        
        return redirect()->route('lms.tests.show', $test)
            ->with('success', 'Test muvaffaqiyatli yangilandi!');
    }

    /**
     * Delete test
     */
    public function destroy(LmsPracticeTest $test)
    {
        // Check permission
        $user = Auth::user();
        if (!$user->hasRole('admin') && $test->teacher_id != $user->id) {
            abort(403, 'Sizda bu testni o\'chirish huquqi yo\'q');
        }
        
        $test->delete();
        
        return redirect()->route('lms.tests.index')
            ->with('success', 'Test muvaffaqiyatli o\'chirildi!');
    }

    /**
     * Start test attempt
     */
    public function start(LmsPracticeTest $test)
    {
        $user = Auth::user();
        
        // Check if test is available
        if (!$test->isAvailable()) {
            return back()->with('error', 'Bu test hozirda mavjud emas');
        }
        
        // Check attempts limit
        $attemptCount = DB::table('lms_test_attempts')
            ->where('test_id', $test->id)
            ->where('user_id', $user->id)
            ->count();
            
        if ($test->attempts_allowed && $attemptCount >= $test->attempts_allowed) {
            return back()->with('error', 'Sizning urinishlaringiz tugadi');
        }
        
        // Create new attempt
        $attemptId = DB::table('lms_test_attempts')->insertGetId([
            'test_id' => $test->id,
            'user_id' => $user->id,
            'attempt_number' => $attemptCount + 1,
            'started_at' => now(),
            'status' => 'in_progress',
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        // Get questions
        $query = DB::table('lms_test_questions')
            ->where('test_id', $test->id);
            
        if ($test->shuffle_questions) {
            $query->inRandomOrder();
        } else {
            $query->orderBy('order_number');
        }
        
        $questions = $query->get()->map(function ($q) use ($test) {
            $q->options = json_decode($q->options);
            if ($test->shuffle_answers && $q->options) {
                shuffle($q->options);
            }
            return $q;
        });
        
        return view('lms.tests.take', compact('test', 'questions', 'attemptId'));
    }

    /**
     * Submit test attempt
     */
    public function submit(Request $request, LmsPracticeTest $test)
    {
        $user = Auth::user();
        $attemptId = $request->input('attempt_id');
        
        // Verify attempt belongs to user
        $attempt = DB::table('lms_test_attempts')
            ->where('id', $attemptId)
            ->where('user_id', $user->id)
            ->where('status', 'in_progress')
            ->first();
            
        if (!$attempt) {
            return redirect()->route('lms.tests.show', $test)
                ->with('error', 'Test topilmadi yoki allaqachon yakunlangan');
        }
        
        $answers = $request->input('answers', []);
        $totalScore = 0;
        $totalPoints = 0;
        
        // Grade answers if auto-grade is enabled
        if ($test->auto_grade) {
            $questions = DB::table('lms_test_questions')
                ->where('test_id', $test->id)
                ->get();
                
            foreach ($questions as $question) {
                $totalPoints += $question->points;
                $userAnswer = $answers[$question->id] ?? null;
                $correctAnswer = json_decode($question->correct_answer);
                
                $isCorrect = false;
                $pointsEarned = 0;
                
                if ($question->type == 'multiple_choice' || $question->type == 'true_false') {
                    $isCorrect = $userAnswer == $correctAnswer;
                    if ($isCorrect) {
                        $pointsEarned = $question->points;
                        $totalScore += $pointsEarned;
                    }
                } elseif ($question->type == 'short_answer') {
                    // Simple string comparison for now
                    $isCorrect = strtolower(trim($userAnswer)) == strtolower(trim($correctAnswer));
                    if ($isCorrect) {
                        $pointsEarned = $question->points;
                        $totalScore += $pointsEarned;
                    }
                }
                
                // Save answer
                DB::table('lms_test_answers')->insert([
                    'attempt_id' => $attemptId,
                    'question_id' => $question->id,
                    'answer' => json_encode($userAnswer),
                    'is_correct' => $isCorrect,
                    'points_earned' => $pointsEarned,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }
        
        // Calculate percentage
        $percentage = $totalPoints > 0 ? ($totalScore / $totalPoints) * 100 : 0;
        
        // Update attempt
        DB::table('lms_test_attempts')
            ->where('id', $attemptId)
            ->update([
                'submitted_at' => now(),
                'time_spent' => now()->diffInSeconds($attempt->started_at),
                'score' => $totalScore,
                'percentage' => $percentage,
                'status' => $test->auto_grade ? 'graded' : 'submitted',
                'answers' => json_encode($answers),
                'updated_at' => now()
            ]);
        
        // Check if passed and issue certificate
        if ($percentage >= $test->passing_score) {
            $this->checkAndIssueCertificate($test, $user, $percentage);
        }
        
        return redirect()->route('lms.tests.result', [$test, $attemptId])
            ->with('success', 'Test muvaffaqiyatli topshirildi!');
    }

    /**
     * Show test result
     */
    public function result(LmsPracticeTest $test, $attemptId)
    {
        $user = Auth::user();
        
        $attempt = DB::table('lms_test_attempts')
            ->where('id', $attemptId)
            ->where('user_id', $user->id)
            ->first();
            
        if (!$attempt) {
            abort(404);
        }
        
        $answers = null;
        if ($test->allow_review) {
            $answers = DB::table('lms_test_answers as a')
                ->join('lms_test_questions as q', 'a.question_id', '=', 'q.id')
                ->where('a.attempt_id', $attemptId)
                ->select('a.*', 'q.question', 'q.type', 'q.options', 'q.correct_answer', 'q.explanation', 'q.points')
                ->get()
                ->map(function ($item) {
                    $item->options = json_decode($item->options);
                    $item->correct_answer = json_decode($item->correct_answer);
                    $item->answer = json_decode($item->answer);
                    return $item;
                });
        }
        
        return view('lms.tests.result', compact('test', 'attempt', 'answers'));
    }

    /**
     * Export test results
     */
    public function exportResults(LmsPracticeTest $test)
    {
        // Check permission
        $user = Auth::user();
        if (!$user->hasRole('admin') && $test->teacher_id != $user->id) {
            abort(403, 'Sizda bu test natijalarini ko\'rish huquqi yo\'q');
        }
        
        $attempts = DB::table('lms_test_attempts as a')
            ->join('users as u', 'a.user_id', '=', 'u.id')
            ->where('a.test_id', $test->id)
            ->select('a.*', 'u.name as user_name', 'u.email')
            ->orderBy('a.percentage', 'desc')
            ->get();
        
        // Generate CSV
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="test-results-' . $test->id . '.csv"',
        ];
        
        $callback = function() use ($attempts) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['User', 'Email', 'Attempt', 'Score', 'Percentage', 'Status', 'Date']);
            
            foreach ($attempts as $attempt) {
                fputcsv($file, [
                    $attempt->user_name,
                    $attempt->email,
                    $attempt->attempt_number,
                    $attempt->score,
                    $attempt->percentage . '%',
                    $attempt->status,
                    $attempt->submitted_at
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    /**
     * Check and issue certificate for test
     */
    private function checkAndIssueCertificate($test, $user, $score)
    {
        // Check if certificate already issued for this test
        $existingCert = \App\Models\LmsCertificate::where('user_id', $user->id)
            ->where('subject_id', $test->subject_id)
            ->where('grade', 'Test-' . $test->id)
            ->first();
            
        if (!$existingCert) {
            \App\Models\LmsCertificate::create([
                'user_id' => $user->id,
                'subject_id' => $test->subject_id,
                'certificate_number' => 'TEST-' . strtoupper(uniqid()),
                'issue_date' => now(),
                'grade' => 'Test-' . $test->id,
                'template' => 'test',
                'custom_fields' => json_encode([
                    'test_title' => $test->title,
                    'score' => $score
                ]),
                'status' => 'active'
            ]);
        }
    }
}