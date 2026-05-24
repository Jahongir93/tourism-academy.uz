<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\StudentPayment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StatisticsController extends Controller
{
    /**
     * Main statistics dashboard
     */
    public function index()
    {
        // Overall statistics
        $stats = [
            'students' => [
                'total' => Student::count(),
                'active' => Student::where('status', 'active')->count(),
                'graduated' => Student::where('status', 'graduated')->count(),
                'suspended' => Student::where('status', 'suspended')->count(),
            ],
            'groups' => [
                'total' => DB::table('student_groups')->count(),
                'active' => DB::table('student_groups')->where('is_active', true)->count(),
            ],
            'teachers' => [
                'total' => User::whereHas('roles', function($q) {
                    $q->where('name', 'Teacher');
                })->count(),
            ],
            'finance' => [
                'total_income_month' => StudentPayment::where('status', 'completed')
                    ->whereMonth('payment_date', now()->month)
                    ->sum('amount'),
                'total_income_year' => StudentPayment::where('status', 'completed')
                    ->whereYear('payment_date', now()->year)
                    ->sum('amount'),
            ]
        ];

        // Monthly enrollment trend (last 12 months)
        $enrollmentTrend = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $count = Student::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();

            $enrollmentTrend[] = [
                'month' => $month->format('M Y'),
                'count' => $count
            ];
        }

        // Gender distribution
        $genderDistribution = Student::select('gender', DB::raw('count(*) as total'))
            ->whereNotNull('gender')
            ->groupBy('gender')
            ->get();

        // Students by course
        $studentsByCourse = DB::table('students as s')
            ->join('student_groups as g', 's.group_id', '=', 'g.id')
            ->select('g.course', DB::raw('count(s.id) as total'))
            ->where('s.status', 'active')
            ->groupBy('g.course')
            ->orderBy('g.course')
            ->get();

        // Top 5 groups by student count
        $topGroups = DB::table('students as s')
            ->join('student_groups as g', 's.group_id', '=', 'g.id')
            ->select('g.name', DB::raw('count(s.id) as total'))
            ->where('s.status', 'active')
            ->groupBy('g.id', 'g.name')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        return view('statistics.index', compact(
            'stats',
            'enrollmentTrend',
            'genderDistribution',
            'studentsByCourse',
            'topGroups'
        ));
    }

    /**
     * Real-time statistics (AJAX endpoint)
     */
    public function realtime()
    {
        $stats = [
            'students_online' => 0, // Implement if you have online tracking
            'active_sessions' => 0, // Implement if needed
            'today_payments' => StudentPayment::whereDate('payment_date', today())
                ->where('status', 'completed')
                ->count(),
            'today_income' => StudentPayment::whereDate('payment_date', today())
                ->where('status', 'completed')
                ->sum('amount'),
        ];

        return response()->json($stats);
    }

    /**
     * Financial statistics
     */
    public function finance()
    {
        // Monthly income for current year
        $monthlyIncome = [];
        for ($month = 1; $month <= 12; $month++) {
            $income = StudentPayment::where('status', 'completed')
                ->whereYear('payment_date', now()->year)
                ->whereMonth('payment_date', $month)
                ->sum('amount');

            $monthlyIncome[] = [
                'month' => Carbon::create(now()->year, $month, 1)->format('M'),
                'income' => $income
            ];
        }

        // Payment methods distribution
        $paymentMethods = StudentPayment::select('payment_method', DB::raw('count(*) as count'), DB::raw('sum(amount) as total'))
            ->where('status', 'completed')
            ->whereYear('payment_date', now()->year)
            ->groupBy('payment_method')
            ->get();

        // Outstanding contracts
        $outstandingContracts = DB::table('student_contracts')
            ->where('status', 'active')
            ->select(
                DB::raw('count(*) as total_contracts'),
                DB::raw('sum(COALESCE(total_amount, 0)) as total_amount'),
                DB::raw('sum(COALESCE(paid_amount, 0)) as paid_amount'),
                DB::raw('sum(COALESCE(total_amount, 0) - COALESCE(paid_amount, 0) - COALESCE(discount_amount, 0)) as remaining')
            )
            ->first();

        // Top paying students (this month)
        $topPayingStudents = StudentPayment::with('student.user')
            ->select('student_id', DB::raw('sum(amount) as total_paid'))
            ->where('status', 'completed')
            ->whereMonth('payment_date', now()->month)
            ->groupBy('student_id')
            ->orderByDesc('total_paid')
            ->limit(10)
            ->get();

        return view('statistics.finance', compact(
            'monthlyIncome',
            'paymentMethods',
            'outstandingContracts',
            'topPayingStudents'
        ));
    }

    /**
     * Academic statistics
     */
    public function academic()
    {
        // Students by faculty (using two-step approach to avoid join issues)
        $studentsByFaculty = DB::table('students as s')
            ->join('student_groups as g', 's.group_id', '=', 'g.id')
            ->select('g.faculty_id', DB::raw('count(s.id) as total'))
            ->where('s.status', 'active')
            ->whereNotNull('g.faculty_id')
            ->groupBy('g.faculty_id')
            ->get()
            ->map(function($item) {
                $faculty = DB::table('faculties')->where('id', $item->faculty_id)->first();
                return (object)[
                    'faculty_name' => $faculty->name ?? 'N/A',
                    'id' => $item->faculty_id,
                    'total' => $item->total,
                    'groups' => DB::table('student_groups')
                        ->where('faculty_id', $item->faculty_id)
                        ->where('is_active', true)
                        ->count()
                ];
            });

        // Students by course
        $studentsByCourse = DB::table('students as s')
            ->join('student_groups as g', 's.group_id', '=', 'g.id')
            ->select('g.course', DB::raw('count(s.id) as total'))
            ->where('s.status', 'active')
            ->groupBy('g.course')
            ->orderBy('g.course')
            ->get()
            ->map(function($item) {
                return (object)[
                    'course' => $item->course,
                    'total' => $item->total,
                    'groups' => DB::table('student_groups')
                        ->where('course', $item->course)
                        ->where('is_active', true)
                        ->count()
                ];
            });

        // Overall statistics
        $totalStudents = Student::where('status', 'active')->count();
        $totalGroups = DB::table('student_groups')->where('is_active', true)->count();
        $totalSubjects = DB::table('subjects')->count();
        $totalTeachers = DB::table('users')
            ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->where('model_has_roles.model_type', 'App\\Models\\User')
            ->where('roles.name', 'Teacher')
            ->count();

        return view('statistics.academic', compact(
            'studentsByFaculty',
            'studentsByCourse',
            'totalStudents',
            'totalGroups',
            'totalSubjects',
            'totalTeachers'
        ));
    }

    /**
     * Comparison statistics
     */
    public function comparison(Request $request)
    {
        $year1 = $request->get('year1', now()->year);
        $year2 = $request->get('year2', now()->year - 1);

        // Compare enrollments
        $enrollments = [
            'year1' => Student::whereYear('created_at', $year1)->count(),
            'year2' => Student::whereYear('created_at', $year2)->count(),
        ];

        // Compare income
        $income = [
            'year1' => StudentPayment::where('status', 'completed')
                ->whereYear('payment_date', $year1)
                ->sum('amount'),
            'year2' => StudentPayment::where('status', 'completed')
                ->whereYear('payment_date', $year2)
                ->sum('amount'),
        ];

        // Monthly comparison
        $monthlyComparison = [];
        for ($month = 1; $month <= 12; $month++) {
            $monthlyComparison[] = [
                'month' => Carbon::create(2000, $month, 1)->format('M'),
                'year1' => StudentPayment::where('status', 'completed')
                    ->whereYear('payment_date', $year1)
                    ->whereMonth('payment_date', $month)
                    ->sum('amount'),
                'year2' => StudentPayment::where('status', 'completed')
                    ->whereYear('payment_date', $year2)
                    ->whereMonth('payment_date', $month)
                    ->sum('amount'),
            ];
        }

        return view('statistics.comparison', compact(
            'enrollments',
            'income',
            'monthlyComparison',
            'year1',
            'year2'
        ));
    }
}
