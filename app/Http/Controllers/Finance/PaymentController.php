<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Finance\StudentContract;
use App\Models\Student;
use App\Models\StudentPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    /**
     * Display list of all payments
     * BUGFIX #76: Fixed SQL injection vulnerability in search
     */
    public function index(Request $request)
    {
        // BUGFIX #76: Validate search input to prevent SQL injection
        $request->validate([
            'status' => 'nullable|in:pending,completed,cancelled',
            'payment_method' => 'nullable|in:cash,bank,online,card',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'search' => 'nullable|string|max:255'
        ]);

        $query = StudentPayment::with(['student.user', 'contract']);

        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->filled('date_from')) {
            $query->where('payment_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('payment_date', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            // BUGFIX #76: Sanitize search input
            $search = trim($request->search);
            $query->whereHas('student.user', function($q) use ($search) {
                $q->where('name', 'like', DB::raw("CONCAT('%', ?, '%')"))->addBinding($search, 'where');
            });
        }

        $payments = $query->orderBy('payment_date', 'desc')->paginate(20);

        return view('finance.payments.index', compact('payments'));
    }

    /**
     * Show the form for creating a new payment
     */
    public function create(Request $request)
    {
        $studentId = $request->get('student_id');
        $student = $studentId ? Student::with('user')->findOrFail($studentId) : null;

        // Get student's contracts
        $contracts = $studentId ? StudentContract::where('student_id', $studentId)
            ->where('status', 'active')
            ->get() : collect();

        return view('finance.payments.create', compact('student', 'contracts'));
    }

    /**
     * Store a newly created payment
     * BUGFIX #73: Added rate limiting (30 payments per hour)
     * BUGFIX #74: Amount must be greater than 0
     * BUGFIX #77: Payment date cannot be in the future
     * BUGFIX #79: Receipt number uniqueness validation
     */
    public function store(Request $request)
    {
        // BUGFIX #73: Rate limiting
        $rateLimitKey = 'payment_create_' . $request->ip();
        if (cache()->has($rateLimitKey) && cache()->get($rateLimitKey) >= 30) {
            return back()->withErrors(['error' => 'Juda ko\'p so\'rov yuborildi. Iltimos, bir soatdan keyin qayta urinib ko\'ring.']);
        }

        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'contract_id' => 'nullable|exists:student_contracts,id',
            'amount' => 'required|numeric|min:0.01', // BUGFIX #74: Must be > 0
            'payment_date' => 'required|date|before_or_equal:today', // BUGFIX #77: Cannot be future date
            'payment_method' => 'required|in:cash,bank,online,card',
            'receipt_number' => 'nullable|string|max:255|unique:student_payments,receipt_number', // BUGFIX #79
            'notes' => 'nullable|string|max:1000'
        ], [
            'amount.min' => 'To\'lov summasi 0 dan katta bo\'lishi kerak.',
            'payment_date.before_or_equal' => 'To\'lov sanasi kelajakda bo\'lishi mumkin emas.',
            'receipt_number.unique' => 'Bu kvitansiya raqami allaqachon mavjud.'
        ]);

        DB::beginTransaction();
        try {
            $validated['status'] = 'completed';
            $validated['processed_by'] = Auth::id();

            $payment = StudentPayment::create($validated);

            // BUGFIX #75: Audit trail - Log payment creation
            Log::info('Payment created', [
                'payment_id' => $payment->id,
                'student_id' => $payment->student_id,
                'amount' => $payment->amount,
                'processed_by' => Auth::id(),
                'ip_address' => $request->ip()
            ]);

            // Update contract paid amount
            if ($payment->contract_id) {
                $contract = StudentContract::lockForUpdate()->find($payment->contract_id);
                $contract->increment('paid_amount', $payment->amount);

                // Check if contract is fully paid
                if ($contract->isFullyPaid()) {
                    $contract->update(['status' => 'completed']);
                }
            }

            DB::commit();

            // BUGFIX #73: Increment rate limit counter
            cache()->put($rateLimitKey, (cache()->get($rateLimitKey, 0) + 1), now()->addHour());

            return redirect()->route('finance.payments.show', $payment)
                ->with('success', 'To\'lov muvaffaqiyatli qo\'shildi');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment creation failed', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'request_data' => $request->except(['_token'])
            ]);
            return back()->withInput()
                ->with('error', 'To\'lov qo\'shishda xatolik: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified payment
     */
    public function show(StudentPayment $payment)
    {
        $payment->load(['student.user', 'contract']);

        return view('finance.payments.show', compact('payment'));
    }

    /**
     * Show the form for editing the payment
     */
    public function edit(StudentPayment $payment)
    {
        $student = $payment->student;
        $contracts = StudentContract::where('student_id', $student->id)
            ->where('status', 'active')
            ->get();

        return view('finance.payments.edit', compact('payment', 'student', 'contracts'));
    }

    /**
     * Update the specified payment
     * BUGFIX #73: Added rate limiting (50 updates per hour)
     * BUGFIX #74: Amount must be greater than 0
     * BUGFIX #77: Payment date validation
     * BUGFIX #78: Log who changed the payment amount
     */
    public function update(Request $request, StudentPayment $payment)
    {
        // BUGFIX #73: Rate limiting
        $rateLimitKey = 'payment_update_' . $request->ip();
        if (cache()->has($rateLimitKey) && cache()->get($rateLimitKey) >= 50) {
            return back()->withErrors(['error' => 'Juda ko\'p so\'rov yuborildi. Iltimos, bir soatdan keyin qayta urinib ko\'ring.']);
        }

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01', // BUGFIX #74
            'payment_date' => 'required|date|before_or_equal:today', // BUGFIX #77
            'payment_method' => 'required|in:cash,bank,online,card',
            'receipt_number' => 'nullable|string|max:255|unique:student_payments,receipt_number,' . $payment->id,
            'status' => 'required|in:pending,completed,cancelled',
            'notes' => 'nullable|string|max:1000'
        ], [
            'amount.min' => 'To\'lov summasi 0 dan katta bo\'lishi kerak.',
            'payment_date.before_or_equal' => 'To\'lov sanasi kelajakda bo\'lishi mumkin emas.'
        ]);

        DB::beginTransaction();
        try {
            $oldAmount = $payment->amount;
            $oldStatus = $payment->status;
            $oldContractId = $payment->contract_id;

            // BUGFIX #78: Log changes before updating
            if ($oldAmount != $validated['amount']) {
                Log::warning('Payment amount changed', [
                    'payment_id' => $payment->id,
                    'old_amount' => $oldAmount,
                    'new_amount' => $validated['amount'],
                    'changed_by' => Auth::id(),
                    'ip_address' => $request->ip()
                ]);
            }

            $payment->update($validated);

            // Update contract amounts if changed
            if ($payment->contract_id && $oldAmount != $payment->amount) {
                $contract = StudentContract::lockForUpdate()->find($payment->contract_id);
                $contract->decrement('paid_amount', $oldAmount);
                $contract->increment('paid_amount', $payment->amount);

                // Recheck contract status
                if ($contract->isFullyPaid()) {
                    $contract->update(['status' => 'completed']);
                } elseif ($contract->status === 'completed') {
                    $contract->update(['status' => 'active']);
                }
            }

            DB::commit();

            // BUGFIX #73: Increment rate limit counter
            cache()->put($rateLimitKey, (cache()->get($rateLimitKey, 0) + 1), now()->addHour());

            return redirect()->route('finance.payments.show', $payment)
                ->with('success', 'To\'lov muvaffaqiyatli yangilandi');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment update failed', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);
            return back()->withInput()
                ->with('error', 'To\'lovni yangilashda xatolik: ' . $e->getMessage());
        }
    }

    /**
     * Cancel a payment
     * BUGFIX #73: Added rate limiting (20 cancellations per hour)
     * BUGFIX #75: Comprehensive audit trail for cancellations
     */
    public function cancel(Request $request, StudentPayment $payment)
    {
        // BUGFIX #73: Rate limiting
        $rateLimitKey = 'payment_cancel_' . $request->ip();
        if (cache()->has($rateLimitKey) && cache()->get($rateLimitKey) >= 20) {
            return back()->withErrors(['error' => 'Juda ko\'p so\'rov yuborildi. Iltimos, bir soatdan keyin qayta urinib ko\'ring.']);
        }

        // Validate cancellation reason
        $validated = $request->validate([
            'cancellation_reason' => 'required|string|max:500'
        ], [
            'cancellation_reason.required' => 'Bekor qilish sababini kiriting.'
        ]);

        // Don't allow cancelling already cancelled payments
        if ($payment->status === 'cancelled') {
            return back()->with('error', 'Bu to\'lov allaqachon bekor qilingan.');
        }

        DB::beginTransaction();
        try {
            // BUGFIX #75: Comprehensive audit trail
            Log::warning('Payment cancelled', [
                'payment_id' => $payment->id,
                'student_id' => $payment->student_id,
                'amount' => $payment->amount,
                'original_date' => $payment->payment_date,
                'cancelled_by' => Auth::id(),
                'cancellation_reason' => $validated['cancellation_reason'],
                'ip_address' => $request->ip(),
                'cancelled_at' => now()
            ]);

            // Update payment status and add cancellation note
            $payment->update([
                'status' => 'cancelled',
                'notes' => ($payment->notes ? $payment->notes . "\n\n" : '') .
                           "BEKOR QILINDI (" . now()->format('Y-m-d H:i') . ") - " .
                           "Sabab: " . $validated['cancellation_reason'] . "\n" .
                           "Kim tomonidan: " . Auth::user()->name
            ]);

            // Update contract if exists
            if ($payment->contract_id) {
                $contract = StudentContract::lockForUpdate()->find($payment->contract_id);
                $contract->decrement('paid_amount', $payment->amount);

                // Update contract status if it was completed
                if ($contract->status === 'completed') {
                    $contract->update(['status' => 'active']);
                }

                Log::info('Contract updated after payment cancellation', [
                    'contract_id' => $contract->id,
                    'new_paid_amount' => $contract->paid_amount
                ]);
            }

            DB::commit();

            // BUGFIX #73: Increment rate limit counter
            cache()->put($rateLimitKey, (cache()->get($rateLimitKey, 0) + 1), now()->addHour());

            return back()->with('success', 'To\'lov bekor qilindi');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment cancellation failed', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);
            return back()->with('error', 'To\'lovni bekor qilishda xatolik: ' . $e->getMessage());
        }
    }
}
