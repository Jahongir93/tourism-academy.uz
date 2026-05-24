<?php

namespace App\Http\Controllers\LMS;

use App\Http\Controllers\Controller;
use App\Models\LmsExam;
use App\Models\LmsExamQuestion;
use App\Models\LmsExamAttempt;
use App\Models\LmsExamAnswer;
use App\Models\Subject;
use App\Models\Group;
use App\Models\Student;
use App\Models\Employee;
use App\Models\TeacherSubject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Http\Controllers\NotificationController;

class ExamController extends Controller
{
    /**
     * Imtihonlar ro'yxati (O'qituvchi va Admin uchun)
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $employee = Employee::where('user_id', $user->id)->first();
        $isAdmin = $user->hasRole('SuperAdmin') || $user->hasRole('admin');

        $query = LmsExam::with(['subject', 'teacher']);

        // Admin barcha imtihonlarni ko'radi, o'qituvchi faqat o'zini
        if (!$isAdmin && $employee) {
            $query->where('teacher_id', $employee->id);
        }

        // Filterlash
        if ($request->filled('exam_type')) {
            $query->where('exam_type', $request->exam_type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $exams = $query->orderBy('created_at', 'desc')->paginate(15);

        // Fanlar ro'yxati
        if ($isAdmin) {
            $subjects = Subject::where('is_active', true)->orderBy('name_uz')->get();
        } else {
            $employeeId = $employee->id ?? Auth::id();
            $subjectIds = TeacherSubject::where('teacher_id', $employeeId)->pluck('subject_id')->unique();
            $subjects = Subject::whereIn('id', $subjectIds)->orderBy('name_uz')->get();
        }

        return view('lms.exams.index', compact('exams', 'subjects', 'isAdmin'));
    }

    /**
     * Yangi imtihon yaratish formasi
     */
    public function create()
    {
        $user = Auth::user();
        $employee = Employee::where('user_id', $user->id)->first();
        $isAdmin = $user->hasRole('SuperAdmin') || $user->hasRole('admin');

        // Fanlar ro'yxati
        if ($isAdmin) {
            $subjects = Subject::where('is_active', true)->orderBy('name_uz')->get();
        } else {
            $employeeId = $employee->id ?? Auth::id();
            $subjectIds = TeacherSubject::where('teacher_id', $employeeId)->pluck('subject_id')->unique();
            $subjects = Subject::whereIn('id', $subjectIds)->orderBy('name_uz')->get();
        }

        // Guruhlar
        $groups = Group::where('is_active', true)->get();

        return view('lms.exams.create', compact('subjects', 'groups', 'isAdmin'));
    }

    /**
     * Yangi imtihon saqlash
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'instructions' => 'nullable|string',
            'subject_id' => 'required|exists:subjects,id',
            'group_ids' => 'nullable|array',
            'group_ids.*' => 'exists:groups,id',
            'exam_type' => 'required|in:joriy,oraliq,yakuniy,practice',
            'week_number' => 'nullable|integer|min:1|max:20',
            'duration_minutes' => 'required|integer|min:5|max:300',
            'start_time' => 'nullable|date',
            'end_time' => 'nullable|date|after:start_time',
            'max_score' => 'required|numeric|min:1|max:100',
            'passing_score' => 'required|numeric|min:0|max:100',
            'weight_percentage' => 'required|numeric|min:0|max:100',
            'max_attempts' => 'required|integer|min:1|max:10',
            'allow_retake' => 'boolean',
            'shuffle_questions' => 'boolean',
            'shuffle_answers' => 'boolean',
            'show_correct_answers' => 'boolean',
            'show_score_immediately' => 'boolean',
            'sync_to_journal' => 'boolean',
            'access_password' => 'nullable|string|max:50'
        ]);

        $user = Auth::user();

        // Employee orqali teacher_id olish
        $employee = Employee::where('user_id', $user->id)->first();
        $validated['teacher_id'] = $employee->id ?? $user->id;
        $validated['slug'] = Str::slug($validated['title']) . '-' . time();
        $validated['status'] = 'draft';

        // SECURITY FIX: Hash the access password before storing
        if (!empty($validated['access_password'])) {
            $validated['access_password_hash'] = Hash::make($validated['access_password']);
            unset($validated['access_password']); // Remove plain text
        }

        $exam = LmsExam::create($validated);

        return redirect()->route('lms.exams.questions', $exam)
            ->with('success', 'Imtihon yaratildi! Endi savollar qo\'shing.');
    }

    /**
     * Imtihon ko'rish
     */
    public function show(LmsExam $exam)
    {
        $this->authorizeExam($exam);

        $exam->load(['subject', 'teacher', 'questions', 'attempts' => function ($q) {
            $q->with('student')->latest();
        }]);

        $stats = [
            'total_attempts' => $exam->attempts()->count(),
            'completed_attempts' => $exam->attempts()->whereIn('status', ['submitted', 'graded'])->count(),
            'average_score' => $exam->getAverageScore(),
            'pass_rate' => $exam->getPassRate(),
            'total_questions' => $exam->questions()->count(),
            'total_points' => $exam->getTotalPoints()
        ];

        return view('lms.exams.show', compact('exam', 'stats'));
    }

    /**
     * Imtihon tahrirlash
     */
    public function edit(LmsExam $exam)
    {
        $this->authorizeExam($exam);

        $user = Auth::user();
        $employee = Employee::where('user_id', $user->id)->first();
        $isAdmin = $user->hasRole('SuperAdmin') || $user->hasRole('admin');

        // Fanlar ro'yxati
        if ($isAdmin) {
            $subjects = Subject::where('is_active', true)->orderBy('name_uz')->get();
        } else {
            $employeeId = $employee->id ?? Auth::id();
            $subjectIds = TeacherSubject::where('teacher_id', $employeeId)->pluck('subject_id')->unique();
            $subjects = Subject::whereIn('id', $subjectIds)->orderBy('name_uz')->get();
        }

        $groups = Group::where('is_active', true)->get();

        return view('lms.exams.edit', compact('exam', 'subjects', 'groups'));
    }

    /**
     * Imtihon yangilash
     */
    public function update(Request $request, LmsExam $exam)
    {
        $this->authorizeExam($exam);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'instructions' => 'nullable|string',
            'subject_id' => 'required|exists:subjects,id',
            'group_ids' => 'nullable|array',
            'group_ids.*' => 'exists:groups,id',
            'exam_type' => 'required|in:joriy,oraliq,yakuniy,practice',
            'week_number' => 'nullable|integer|min:1|max:20',
            'duration_minutes' => 'required|integer|min:5|max:300',
            'start_time' => 'nullable|date',
            'end_time' => 'nullable|date|after:start_time',
            'max_score' => 'required|numeric|min:1|max:100',
            'passing_score' => 'required|numeric|min:0|max:100',
            'weight_percentage' => 'required|numeric|min:0|max:100',
            'max_attempts' => 'required|integer|min:1|max:10',
            'allow_retake' => 'boolean',
            'shuffle_questions' => 'boolean',
            'shuffle_answers' => 'boolean',
            'show_correct_answers' => 'boolean',
            'show_score_immediately' => 'boolean',
            'sync_to_journal' => 'boolean',
            'access_password' => 'nullable|string|max:50'
        ]);

        // SECURITY FIX: Hash the access password if it's being updated
        if (!empty($validated['access_password'])) {
            $validated['access_password_hash'] = Hash::make($validated['access_password']);
            unset($validated['access_password']); // Remove plain text
        } else {
            // If password is empty, remove the hash too
            unset($validated['access_password']);
        }

        $exam->update($validated);

        return redirect()->route('lms.exams.show', $exam)
            ->with('success', 'Imtihon muvaffaqiyatli yangilandi!');
    }

    /**
     * Imtihon o'chirish
     */
    public function destroy(LmsExam $exam)
    {
        $this->authorizeExam($exam);

        // SECURITY FIX: Proper cascade delete with transaction (BUG #18)
        DB::beginTransaction();

        try {
            // Check if exam is published - extra safety
            if ($exam->is_published && $exam->status === 'active') {
                DB::rollBack();
                return back()->with('error', 'Faol imtihonni o\'chirib bo\'lmaydi. Avval to\'xtatib qo\'ying!');
            }

            // Delete all answers for all attempts
            foreach ($exam->attempts as $attempt) {
                $attempt->answers()->delete();
            }

            // Delete all attempts
            $exam->attempts()->delete();

            // Delete all questions
            $exam->questions()->delete();

            // Delete the exam itself
            $exam->delete();

            DB::commit();

            \Log::info('Exam deleted', [
                'exam_id' => $exam->id,
                'exam_title' => $exam->title,
                'deleted_by' => auth()->id()
            ]);

            return redirect()->route('lms.exams.index')
                ->with('success', 'Imtihon va barcha bog\'liq ma\'lumotlar o\'chirildi!');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error deleting exam: ' . $e->getMessage());
            return back()->with('error', 'Imtihonni o\'chirishda xatolik yuz berdi: ' . $e->getMessage());
        }
    }

    /**
     * Savollar boshqaruvi
     */
    public function questions(LmsExam $exam)
    {
        $this->authorizeExam($exam);

        $exam->load('questions');

        return view('lms.exams.questions', compact('exam'));
    }

    /**
     * Savol qo'shish
     */
    public function storeQuestion(Request $request, LmsExam $exam)
    {
        $this->authorizeExam($exam);

        $validated = $request->validate([
            'question_type' => 'required|in:single_choice,multiple_choice,true_false,text,essay',
            'question_text' => 'required|string',
            'question_hint' => 'nullable|string',
            'options' => 'required_if:question_type,single_choice,multiple_choice|array',
            'options.*' => 'string',
            'correct_answer' => 'required|array',
            'explanation' => 'nullable|string',
            'points' => 'required|numeric|min:0.5|max:100',
            'difficulty' => 'required|in:easy,medium,hard',
            'category' => 'nullable|string|max:100'
        ]);

        $maxOrder = $exam->questions()->max('order_number') ?? 0;

        $validated['exam_id'] = $exam->id;
        $validated['order_number'] = $maxOrder + 1;

        LmsExamQuestion::create($validated);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Savol qo\'shildi!']);
        }

        return back()->with('success', 'Savol muvaffaqiyatli qo\'shildi!');
    }

    /**
     * Savol yangilash
     */
    public function updateQuestion(Request $request, LmsExamQuestion $question)
    {
        $this->authorizeExam($question->exam);

        $validated = $request->validate([
            'question_type' => 'required|in:single_choice,multiple_choice,true_false,text,essay',
            'question_text' => 'required|string',
            'question_hint' => 'nullable|string',
            'options' => 'required_if:question_type,single_choice,multiple_choice|array',
            'options.*' => 'string',
            'correct_answer' => 'required|array',
            'explanation' => 'nullable|string',
            'points' => 'required|numeric|min:0.5|max:100',
            'difficulty' => 'required|in:easy,medium,hard',
            'category' => 'nullable|string|max:100'
        ]);

        $question->update($validated);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Savol yangilandi!']);
        }

        return back()->with('success', 'Savol muvaffaqiyatli yangilandi!');
    }

    /**
     * Savol o'chirish
     */
    public function destroyQuestion(LmsExamQuestion $question)
    {
        $this->authorizeExam($question->exam);

        $question->delete();

        return back()->with('success', 'Savol o\'chirildi!');
    }

    /**
     * Savol tartibini o'zgartirish
     */
    public function reorderQuestions(Request $request, LmsExam $exam)
    {
        $this->authorizeExam($exam);

        $validated = $request->validate([
            'questions' => 'required|array',
            'questions.*' => 'exists:lms_exam_questions,id'
        ]);

        foreach ($validated['questions'] as $index => $questionId) {
            LmsExamQuestion::where('id', $questionId)
                ->where('exam_id', $exam->id)
                ->update(['order_number' => $index + 1]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Imtihonni faollashtirish/nashr qilish
     */
    public function publish(LmsExam $exam)
    {
        $this->authorizeExam($exam);

        if ($exam->questions()->count() === 0) {
            return back()->with('error', 'Imtihonda savollar yo\'q. Avval savollar qo\'shing!');
        }

        $exam->is_published = true;
        $exam->status = 'active';
        $exam->save();

        // Send notification to students in the exam's groups
        $groupIds = $exam->group_ids ?? [];
        if (!empty($groupIds)) {
            NotificationController::notifyTestAssigned($exam, $groupIds);
        }

        return back()->with('success', 'Imtihon muvaffaqiyatli e\'lon qilindi!');
    }

    /**
     * Imtihonni to'xtatish
     */
    public function unpublish(LmsExam $exam)
    {
        $this->authorizeExam($exam);

        $exam->is_published = false;
        $exam->status = 'draft';
        $exam->save();

        return back()->with('success', 'Imtihon to\'xtatildi!');
    }

    /**
     * Natijalar
     */
    public function results(LmsExam $exam)
    {
        $this->authorizeExam($exam);

        $attempts = $exam->attempts()
            ->with(['student.group', 'answers'])
            ->whereIn('status', ['submitted', 'graded'])
            ->orderBy('score', 'desc')
            ->paginate(30);

        return view('lms.exams.results', compact('exam', 'attempts'));
    }

    /**
     * Talaba natijasini ko'rish
     */
    public function attemptDetails(LmsExamAttempt $attempt)
    {
        $this->authorizeExam($attempt->exam);

        $attempt->load(['exam', 'student', 'answers.question']);

        return view('lms.exams.attempt-details', compact('attempt'));
    }

    /**
     * Qo'lda baholash (essay uchun)
     */
    public function gradeAnswer(Request $request, LmsExamAnswer $answer)
    {
        $this->authorizeExam($answer->attempt->exam);

        $validated = $request->validate([
            'points' => 'required|numeric|min:0|max:' . $answer->question->points,
            'feedback' => 'nullable|string'
        ]);

        $answer->manualGrade($validated['points'], $validated['feedback'], Auth::id());

        // Check if all answers are graded
        $attempt = $answer->attempt;
        $ungraded = $attempt->answers()->whereNull('is_correct')->count();

        if ($ungraded === 0) {
            $attempt->status = 'graded';
            $attempt->save();

            // Sync to journal
            if ($attempt->exam->sync_to_journal) {
                $attempt->syncToJournal();
            }
        }

        return back()->with('success', 'Javob baholandi!');
    }

    /**
     * Natijalarni jurnalga sinxronlash
     */
    public function syncToJournal(LmsExam $exam)
    {
        $this->authorizeExam($exam);

        $attempts = $exam->attempts()
            ->where('status', 'graded')
            ->where('synced_to_journal', false)
            ->get();

        $synced = 0;
        foreach ($attempts as $attempt) {
            if ($attempt->syncToJournal()) {
                $synced++;
            }
        }

        return back()->with('success', "{$synced} ta natija jurnalga o'tkazildi!");
    }

    // ==================== TALABA UCHUN METODLAR ====================

    /**
     * Talabalar uchun imtihonlar ro'yxati
     */
    public function studentExams(Request $request)
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->first();

        if (!$student) {
            abort(403, 'Talaba topilmadi');
        }

        $query = LmsExam::published()
            ->available()
            ->where(function ($q) use ($student) {
                $q->whereNull('group_ids')
                    ->orWhereJsonContains('group_ids', $student->group_id);
            });

        // Filter by exam type
        if ($request->filled('exam_type')) {
            $query->where('exam_type', $request->exam_type);
        }

        $exams = $query->with(['subject', 'teacher'])
            ->orderBy('start_time', 'desc')
            ->paginate(12);

        // Get student's attempts
        $attemptCounts = LmsExamAttempt::where('student_id', $student->id)
            ->whereIn('exam_id', $exams->pluck('id'))
            ->selectRaw('exam_id, COUNT(*) as count, MAX(score) as best_score')
            ->groupBy('exam_id')
            ->pluck('count', 'exam_id');

        return view('lms.exams.student-list', compact('exams', 'student', 'attemptCounts'));
    }

    /**
     * Imtihon haqida ma'lumot (talaba uchun)
     */
    public function studentExamInfo(LmsExam $exam)
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->first();

        if (!$student) {
            abort(403, 'Talaba topilmadi');
        }

        $canAttempt = $exam->canStudentAttempt($student);

        $previousAttempts = $exam->attempts()
            ->where('student_id', $student->id)
            ->orderBy('attempt_number', 'desc')
            ->get();

        return view('lms.exams.student-info', compact('exam', 'student', 'canAttempt', 'previousAttempts'));
    }

    /**
     * Imtihonni boshlash
     */
    public function startExam(Request $request, LmsExam $exam)
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->first();

        if (!$student) {
            abort(403, 'Talaba topilmadi');
        }

        $canAttempt = $exam->canStudentAttempt($student);

        if (!$canAttempt['can_attempt']) {
            return back()->with('error', $canAttempt['reason']);
        }

        // SECURITY FIX: Check hashed password if required
        if ($exam->access_password_hash) {
            if (!$request->password || !Hash::check($request->password, $exam->access_password_hash)) {
                return back()->with('error', 'Noto\'g\'ri parol!');
            }
        }

        // SECURITY FIX: Use database transaction with locking to prevent race conditions (BUG #14)
        DB::beginTransaction();

        try {
            // Lock the exam to prevent concurrent access
            $exam = LmsExam::lockForUpdate()->find($exam->id);

            // Check for existing in-progress attempt with lock
            $inProgressAttempt = LmsExamAttempt::lockForUpdate()
                ->where('exam_id', $exam->id)
                ->where('student_id', $student->id)
                ->where('status', 'in_progress')
                ->first();

            if ($inProgressAttempt) {
                $attempt = $inProgressAttempt;
            } else {
                // Get the attempt count with lock
                $attemptCount = LmsExamAttempt::lockForUpdate()
                    ->where('exam_id', $exam->id)
                    ->where('student_id', $student->id)
                    ->whereIn('status', ['submitted', 'graded'])
                    ->count();

                // Double-check attempt limit
                if ($attemptCount >= $exam->max_attempts) {
                    DB::rollBack();
                    return back()->with('error', 'Maksimal urinishlar soni tugagan');
                }

                $attemptNumber = $attemptCount + 1;

                // Generate question order
                $questions = $exam->questions()->pluck('id')->toArray();
                if ($exam->shuffle_questions) {
                    shuffle($questions);
                }

                $attempt = LmsExamAttempt::create([
                    'exam_id' => $exam->id,
                    'student_id' => $student->id,
                    'attempt_number' => $attemptNumber,
                    'started_at' => now(),
                    'status' => 'in_progress',
                    'question_order' => $questions,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent()
                ]);

                $attempt->logActivity('started');
            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error starting exam: ' . $e->getMessage());
            return back()->with('error', 'Imtihonni boshlashda xatolik yuz berdi. Qaytadan urinib ko\'ring.');
        }

        return redirect()->route('lms.exams.take', $attempt);
    }

    /**
     * Imtihon topshirish sahifasi
     */
    public function takeExam(LmsExamAttempt $attempt)
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->first();

        if (!$student || $attempt->student_id !== $student->id) {
            abort(403, 'Ruxsat yo\'q');
        }

        if ($attempt->status !== 'in_progress') {
            return redirect()->route('lms.exams.result', $attempt);
        }

        // Check if expired
        if ($attempt->isExpired()) {
            $attempt->submit();
            return redirect()->route('lms.exams.result', $attempt)
                ->with('warning', 'Imtihon vaqti tugagan. Javoblaringiz avtomatik saqlandi.');
        }

        $exam = $attempt->exam;
        $questionIds = $attempt->question_order ?? $exam->questions()->pluck('id')->toArray();

        $questions = LmsExamQuestion::whereIn('id', $questionIds)
            ->get()
            ->sortBy(function ($q) use ($questionIds) {
                return array_search($q->id, $questionIds);
            });

        // Get existing answers
        $answers = $attempt->answers()->pluck('answer', 'question_id')->toArray();
        $textAnswers = $attempt->answers()->pluck('text_answer', 'question_id')->toArray();

        return view('lms.exams.take', compact('attempt', 'exam', 'questions', 'answers', 'textAnswers'));
    }

    /**
     * Javobni saqlash (auto-save)
     */
    public function saveAnswer(Request $request, LmsExamAttempt $attempt)
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->first();

        if (!$student || $attempt->student_id !== $student->id) {
            return response()->json(['error' => 'Ruxsat yo\'q'], 403);
        }

        if ($attempt->status !== 'in_progress') {
            return response()->json(['error' => 'Imtihon yakunlangan'], 400);
        }

        // SECURITY FIX: Server-side time validation on EVERY request (BUG #12)
        if ($attempt->isExpired()) {
            $attempt->submit();
            return response()->json([
                'error' => 'Imtihon vaqti tugagan',
                'expired' => true,
                'redirect' => route('lms.exams.result', $attempt)
            ], 410); // 410 Gone status
        }

        // SECURITY FIX: Validate question belongs to this exam (BUG #17)
        $question = LmsExamQuestion::where('id', $request->question_id)
            ->where('exam_id', $attempt->exam_id)
            ->first();

        if (!$question) {
            return response()->json(['error' => 'Savol topilmadi yoki bu imtihonga tegishli emas'], 404);
        }

        // SECURITY FIX: Validate answer based on question type (BUG #13)
        $validationRules = [
            'question_id' => 'required|exists:lms_exam_questions,id',
            'is_flagged' => 'nullable|boolean'
        ];

        // Add type-specific validation
        switch ($question->question_type) {
            case 'single_choice':
                $validationRules['answer'] = [
                    'required',
                    'integer',
                    'min:0',
                    function ($attribute, $value, $fail) use ($question) {
                        if (!isset($question->options[$value])) {
                            $fail('Noto\'g\'ri javob tanlandi');
                        }
                    }
                ];
                break;

            case 'multiple_choice':
                $validationRules['answer'] = [
                    'required',
                    'array',
                    'min:1',
                    function ($attribute, $value, $fail) use ($question) {
                        foreach ($value as $index) {
                            if (!is_int($index) || !isset($question->options[$index])) {
                                $fail('Noto\'g\'ri javob tanlandi');
                                break;
                            }
                        }
                    }
                ];
                break;

            case 'true_false':
                $validationRules['answer'] = 'required|boolean';
                break;

            case 'text':
                $validationRules['text_answer'] = 'required|string|max:500';
                break;

            case 'essay':
                $validationRules['text_answer'] = 'required|string|max:5000';
                break;

            default:
                return response()->json(['error' => 'Noma\'lum savol turi'], 400);
        }

        $validated = $request->validate($validationRules);

        $answer = LmsExamAnswer::updateOrCreate(
            [
                'attempt_id' => $attempt->id,
                'question_id' => $validated['question_id']
            ],
            [
                'answer' => isset($validated['answer'])
                    ? (is_array($validated['answer']) ? $validated['answer'] : [$validated['answer']])
                    : null,
                'text_answer' => $validated['text_answer'] ?? null,
                'answered_at' => now(),
                'is_flagged' => $validated['is_flagged'] ?? false
            ]
        );

        return response()->json(['success' => true, 'saved' => true]);
    }

    /**
     * Imtihonni yakunlash
     */
    public function submitExam(Request $request, LmsExamAttempt $attempt)
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->first();

        if (!$student || $attempt->student_id !== $student->id) {
            abort(403, 'Ruxsat yo\'q');
        }

        if ($attempt->status !== 'in_progress') {
            return redirect()->route('lms.exams.result', $attempt);
        }

        // SECURITY FIX: Server-side time validation (BUG #12)
        $isExpired = $attempt->isExpired();

        $attempt->submit();
        $attempt->logActivity($isExpired ? 'auto_submitted_expired' : 'submitted');

        $message = $isExpired
            ? 'Imtihon vaqti tugagan. Javoblaringiz avtomatik saqlandi.'
            : 'Imtihon muvaffaqiyatli topshirildi!';

        return redirect()->route('lms.exams.result', $attempt)
            ->with($isExpired ? 'warning' : 'success', $message);
    }

    /**
     * Natija sahifasi (talaba uchun)
     */
    public function examResult(LmsExamAttempt $attempt)
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->first();

        if (!$student || $attempt->student_id !== $student->id) {
            abort(403, 'Ruxsat yo\'q');
        }

        $exam = $attempt->exam;
        $showAnswers = $exam->show_correct_answers;

        $attempt->load(['answers.question']);

        return view('lms.exams.result', compact('attempt', 'exam', 'showAnswers'));
    }

    /**
     * Tab switch report
     */
    public function reportTabSwitch(Request $request, LmsExamAttempt $attempt)
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->first();

        if (!$student || $attempt->student_id !== $student->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $attempt->incrementTabSwitch();

        return response()->json(['success' => true, 'count' => $attempt->tab_switches]);
    }

    /**
     * Helper: Imtihon egaligini tekshirish
     */
    private function authorizeExam(LmsExam $exam): void
    {
        $user = Auth::user();

        // Admin har doim ruxsatga ega
        if ($user->hasRole(['SuperAdmin', 'admin'])) {
            return;
        }

        $employee = Employee::where('user_id', $user->id)->first();
        $employeeId = $employee->id ?? $user->id;

        if ($exam->teacher_id !== $employeeId) {
            abort(403, 'Bu imtihonga ruxsatingiz yo\'q');
        }
    }
}
