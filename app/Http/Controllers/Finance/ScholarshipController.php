<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Finance\Scholarship;
use App\Models\Finance\ScholarshipPayment;
use App\Models\Finance\StudentScholarship;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ScholarshipController extends Controller
{
    /**
     * Display a listing of scholarships
     */
    public function index()
    {
        $scholarships = Scholarship::select('scholarships.*')
            ->selectSub(
                \DB::table('student_scholarship')
                    ->whereColumn('student_scholarship.scholarship_id', 'scholarships.id')
                    ->where('student_scholarship.status', 'active')
                    ->selectRaw('count(*)'),
                'active_recipients_count'
            )
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('finance.scholarships.index', compact('scholarships'));
    }

    /**
     * Show the form for creating a new scholarship
     */
    public function create()
    {
        return view('finance.scholarships.create');
    }

    /**
     * Store a newly created scholarship
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'type' => 'required|in:monthly,one_time,annual',
            'category' => 'required|in:academic,social,sport,cultural,other',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'max_recipients' => 'nullable|integer|min:1',
            'eligibility_criteria' => 'nullable|string',
            'status' => 'required|in:active,inactive'
        ]);

        $scholarship = Scholarship::create($validated);

        return redirect()->route('finance.scholarships.show', $scholarship)
            ->with('success', 'Grant muvaffaqiyatli yaratildi');
    }

    /**
     * Display the specified scholarship
     */
    public function show(Scholarship $scholarship)
    {
        $scholarship->load(['studentScholarships.student.user', 'studentScholarships.payments']);

        $stats = [
            'total_awarded' => $scholarship->studentScholarships()->sum('amount'),
            'total_paid' => ScholarshipPayment::whereHas('studentScholarship', function($q) use ($scholarship) {
                $q->where('scholarship_id', $scholarship->id);
            })->where('status', 'paid')->sum('amount'),
            'pending_payments' => ScholarshipPayment::whereHas('studentScholarship', function($q) use ($scholarship) {
                $q->where('scholarship_id', $scholarship->id);
            })->where('status', 'pending')->count()
        ];

        return view('finance.scholarships.show', compact('scholarship', 'stats'));
    }

    /**
     * Show the form for editing the scholarship
     */
    public function edit(Scholarship $scholarship)
    {
        return view('finance.scholarships.edit', compact('scholarship'));
    }

    /**
     * Update the specified scholarship
     */
    public function update(Request $request, Scholarship $scholarship)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'type' => 'required|in:monthly,one_time,annual',
            'category' => 'required|in:academic,social,sport,cultural,other',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'max_recipients' => 'nullable|integer|min:1',
            'eligibility_criteria' => 'nullable|string',
            'status' => 'required|in:active,inactive,expired'
        ]);

        $scholarship->update($validated);

        return redirect()->route('finance.scholarships.show', $scholarship)
            ->with('success', 'Grant muvaffaqiyatli yangilandi');
    }

    /**
     * Award scholarship to a student
     * BUGFIX #86: Added rate limiting (20 awards per hour)
     * BUGFIX #87: Fixed race condition with lockForUpdate()
     * BUGFIX #88: Validate awarded amount <= scholarship amount
     * BUGFIX #90: Check student eligibility
     */
    public function award(Request $request, Scholarship $scholarship)
    {
        // BUGFIX #86: Rate limiting
        $rateLimitKey = 'scholarship_award_' . $request->ip();
        if (cache()->has($rateLimitKey) && cache()->get($rateLimitKey) >= 20) {
            return back()->withErrors(['error' => 'Juda ko\'p so\'rov yuborildi. Iltimos, bir soatdan keyin qayta urinib ko\'ring.']);
        }

        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'amount' => 'required|numeric|min:0.01|max:' . $scholarship->amount, // BUGFIX #88
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'reason' => 'nullable|string|max:1000'
        ], [
            'amount.max' => 'Grant summasi asosiy grant summasidan oshmasligi kerak (' . number_format($scholarship->amount, 2) . ').'
        ]);

        // BUGFIX #90: Check student eligibility (basic validation)
        $student = Student::with('user')->findOrFail($validated['student_id']);
        if ($student->user->status !== 'active') {
            return back()->with('error', 'Talaba hisobi faol emas.');
        }

        DB::beginTransaction();
        try {
            // BUGFIX #87: Lock scholarship record to prevent race condition
            $scholarship = Scholarship::lockForUpdate()->findOrFail($scholarship->id);

            // Check if scholarship has available slots
            if (!$scholarship->hasAvailableSlots()) {
                return back()->with('error', 'Grant uchun bo\'sh joy yo\'q');
            }

            // Check if student already has this scholarship
            $exists = StudentScholarship::where('student_id', $validated['student_id'])
                ->where('scholarship_id', $scholarship->id)
                ->where('status', 'active')
                ->lockForUpdate()
                ->exists();

            if ($exists) {
                return back()->with('error', 'Talaba allaqachon ushbu grantga ega');
            }

            $studentScholarship = StudentScholarship::create([
                'student_id' => $validated['student_id'],
                'scholarship_id' => $scholarship->id,
                'awarded_date' => now(),
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'amount' => $validated['amount'],
                'status' => 'active',
                'reason' => $validated['reason'],
                'approved_by' => Auth::id()
            ]);

            $scholarship->increment('current_recipients');

            // Log the award
            Log::info('Scholarship awarded', [
                'scholarship_id' => $scholarship->id,
                'student_id' => $validated['student_id'],
                'amount' => $validated['amount'],
                'approved_by' => Auth::id(),
                'ip_address' => $request->ip()
            ]);

            DB::commit();

            // BUGFIX #86: Increment rate limit counter
            cache()->put($rateLimitKey, (cache()->get($rateLimitKey, 0) + 1), now()->addHour());

            return redirect()->route('finance.scholarships.show', $scholarship)
                ->with('success', 'Grant talabaga berildi');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Scholarship award failed', [
                'scholarship_id' => $scholarship->id,
                'student_id' => $validated['student_id'] ?? null,
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);
            return back()->with('error', 'Grant berishda xatolik: ' . $e->getMessage());
        }
    }

    /**
     * Revoke scholarship from a student
     * BUGFIX #89: Record revocation reason
     */
    public function revoke(Request $request, StudentScholarship $studentScholarship)
    {
        // BUGFIX #89: Require revocation reason
        $validated = $request->validate([
            'revocation_reason' => 'required|string|max:500'
        ], [
            'revocation_reason.required' => 'Bekor qilish sababini kiriting.'
        ]);

        DB::beginTransaction();
        try {
            // BUGFIX #89: Log revocation with reason
            Log::warning('Scholarship revoked', [
                'student_scholarship_id' => $studentScholarship->id,
                'student_id' => $studentScholarship->student_id,
                'scholarship_id' => $studentScholarship->scholarship_id,
                'amount' => $studentScholarship->amount,
                'revoked_by' => Auth::id(),
                'reason' => $validated['revocation_reason'],
                'ip_address' => $request->ip()
            ]);

            $studentScholarship->update([
                'status' => 'cancelled',
                'notes' => ($studentScholarship->notes ? $studentScholarship->notes . "\n\n" : '') .
                           "BEKOR QILINDI (" . now()->format('Y-m-d H:i') . ") - " .
                           "Sabab: " . $validated['revocation_reason'] . "\n" .
                           "Kim tomonidan: " . Auth::user()->name
            ]);

            $studentScholarship->scholarship->decrement('current_recipients');

            DB::commit();

            return back()->with('success', 'Grant bekor qilindi');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Scholarship revocation failed', [
                'student_scholarship_id' => $studentScholarship->id,
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);
            return back()->with('error', 'Grant bekor qilishda xatolik: ' . $e->getMessage());
        }
    }

    /**
     * Process scholarship payment
     * BUGFIX #91: Validate payment doesn't exceed awarded amount
     */
    public function processPayment(Request $request, StudentScholarship $studentScholarship)
    {
        // BUGFIX #91: Calculate total paid so far
        $totalPaid = ScholarshipPayment::where('student_scholarship_id', $studentScholarship->id)
            ->where('status', 'paid')
            ->sum('amount');

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01|max:' . ($studentScholarship->amount - $totalPaid), // BUGFIX #91
            'payment_date' => 'required|date|before_or_equal:today',
            'payment_reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:500'
        ], [
            'amount.max' => 'To\'lov summasi qolgan grant summasidan oshmasligi kerak (' . number_format($studentScholarship->amount - $totalPaid, 2) . ').'
        ]);

        try {
            $payment = ScholarshipPayment::create([
                'student_scholarship_id' => $studentScholarship->id,
                'amount' => $validated['amount'],
                'payment_date' => $validated['payment_date'],
                'payment_reference' => $validated['payment_reference'] ?? null,
                'status' => 'paid',
                'notes' => $validated['notes'] ?? null,
                'processed_by' => Auth::id()
            ]);

            Log::info('Scholarship payment processed', [
                'payment_id' => $payment->id,
                'student_scholarship_id' => $studentScholarship->id,
                'amount' => $validated['amount'],
                'processed_by' => Auth::id()
            ]);

            return back()->with('success', 'To\'lov qayd qilindi');
        } catch (\Exception $e) {
            Log::error('Scholarship payment failed', [
                'student_scholarship_id' => $studentScholarship->id,
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);
            return back()->with('error', 'To\'lovni qayd qilishda xatolik: ' . $e->getMessage());
        }
    }
}
