<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Finance\FinancialTransaction;
use App\Models\Finance\Scholarship;
use App\Models\Finance\StudentContract;
use App\Models\StudentPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class FinanceController extends Controller
{
    /**
     * Show finance dashboard
     * BUGFIX #84: Added caching for expensive queries
     */
    public function index()
    {
        // BUGFIX #84: Cache dashboard stats for 5 minutes
        $stats = Cache::remember('finance_dashboard_stats', 300, function () {
            // Get current month statistics
            $currentMonth = now()->startOfMonth();
            $currentMonthEnd = now()->endOfMonth();

            return [
            // Income statistics
            'total_income_this_month' => StudentPayment::where('status', 'completed')
                ->whereBetween('payment_date', [$currentMonth, $currentMonthEnd])
                ->sum('amount'),

            'total_payments_count' => StudentPayment::where('status', 'completed')
                ->whereBetween('payment_date', [$currentMonth, $currentMonthEnd])
                ->count(),

            // Contract statistics
            'total_contracts' => StudentContract::where('status', 'active')->count(),
            'total_contract_value' => StudentContract::where('status', 'active')->sum('total_amount'),
            'total_paid' => StudentContract::where('status', 'active')->sum('paid_amount'),
            'total_remaining' => StudentContract::where('status', 'active')
                ->get()
                ->sum(function($contract) {
                    return $contract->remaining_amount;
                }),

            // Scholarship statistics
            'active_scholarships' => Scholarship::where('status', 'active')->count(),
            'scholarship_recipients' => DB::table('student_scholarship')
                ->where('status', 'active')
                ->count(),
            'total_scholarship_budget' => Scholarship::where('status', 'active')->sum('amount'),

            // Recent transactions
            'recent_income' => FinancialTransaction::where('type', 'income')
                ->whereBetween('transaction_date', [$currentMonth, $currentMonthEnd])
                ->sum('amount'),

            'recent_expense' => FinancialTransaction::where('type', 'expense')
                ->whereBetween('transaction_date', [$currentMonth, $currentMonthEnd])
                ->sum('amount'),
            ];
        });

        // Recent payments
        $recentPayments = StudentPayment::with(['student.user'])
            ->where('status', 'completed')
            ->orderBy('payment_date', 'desc')
            ->limit(10)
            ->get();

        // Payment by method chart data
        $paymentsByMethod = StudentPayment::select('payment_method', DB::raw('count(*) as count'), DB::raw('sum(amount) as total'))
            ->where('status', 'completed')
            ->whereBetween('payment_date', [$currentMonth, $currentMonthEnd])
            ->groupBy('payment_method')
            ->get();

        // Monthly income trend (last 6 months)
        $monthlyIncome = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthStart = $month->copy()->startOfMonth();
            $monthEnd = $month->copy()->endOfMonth();

            $monthlyIncome[] = [
                'month' => $month->format('M'),
                'income' => StudentPayment::where('status', 'completed')
                    ->whereBetween('payment_date', [$monthStart, $monthEnd])
                    ->sum('amount')
            ];
        }

        return view('finance.dashboard', compact('stats', 'recentPayments', 'paymentsByMethod', 'monthlyIncome'));
    }

    /**
     * Show all transactions
     */
    public function transactions(Request $request)
    {
        $query = FinancialTransaction::with(['student', 'processor']);

        // Filters
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('date_from')) {
            $query->where('transaction_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('transaction_date', '<=', $request->date_to);
        }

        $transactions = $query->orderBy('transaction_date', 'desc')->paginate(20);

        return view('finance.transactions', compact('transactions'));
    }

    /**
     * Show financial reports
     * BUGFIX #81: Added input validation for date parameters
     * BUGFIX #83: Added report size limits to prevent DOS
     */
    public function reports(Request $request)
    {
        // BUGFIX #81: Validate date inputs
        $validated = $request->validate([
            'start_date' => 'nullable|date|before_or_equal:today',
            'end_date' => 'nullable|date|after_or_equal:start_date|before_or_equal:today'
        ]);

        $startDate = $validated['start_date'] ?? now()->startOfMonth()->toDateString();
        $endDate = $validated['end_date'] ?? now()->endOfMonth()->toDateString();

        // BUGFIX #83: Limit report period to max 1 year to prevent DOS
        $daysDifference = \Carbon\Carbon::parse($startDate)->diffInDays(\Carbon\Carbon::parse($endDate));
        if ($daysDifference > 365) {
            return back()->with('error', 'Hisobot davri maksimum 1 yil bo\'lishi mumkin.');
        }

        $report = [
            'period' => [
                'start' => $startDate,
                'end' => $endDate
            ],
            'income' => [
                'tuition' => StudentPayment::where('status', 'completed')
                    ->whereBetween('payment_date', [$startDate, $endDate])
                    ->sum('amount'),
                'total' => FinancialTransaction::where('type', 'income')
                    ->whereBetween('transaction_date', [$startDate, $endDate])
                    ->sum('amount'),
            ],
            'expenses' => [
                'scholarships' => DB::table('scholarship_payments')
                    ->where('status', 'paid')
                    ->whereBetween('payment_date', [$startDate, $endDate])
                    ->sum('amount'),
                'total' => FinancialTransaction::where('type', 'expense')
                    ->whereBetween('transaction_date', [$startDate, $endDate])
                    ->sum('amount'),
            ]
        ];

        $report['net_income'] = $report['income']['total'] - $report['expenses']['total'];

        // Detailed breakdown
        $incomeByCategory = FinancialTransaction::select('category', DB::raw('sum(amount) as total'))
            ->where('type', 'income')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->groupBy('category')
            ->get();

        $expensesByCategory = FinancialTransaction::select('category', DB::raw('sum(amount) as total'))
            ->where('type', 'expense')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->groupBy('category')
            ->get();

        return view('finance.reports', compact('report', 'incomeByCategory', 'expensesByCategory'));
    }
}
