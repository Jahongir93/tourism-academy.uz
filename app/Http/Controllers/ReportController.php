<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\StudentPayment;
use App\Models\Finance\StudentContract;
use App\Models\Finance\Scholarship;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Main reports dashboard
     */
    public function index()
    {
        return view('reports.index');
    }

    /**
     * Student reports
     */
    public function students(Request $request)
    {
        // Get filters
        $facultyId = $request->get('faculty_id');
        $course = $request->get('course');
        $groupId = $request->get('group_id');
        $status = $request->get('status', 'active');

        // Build query
        $query = Student::with(['user', 'group']);

        if ($status) {
            $query->where('status', $status);
        }

        if ($groupId) {
            $query->where('group_id', $groupId);
        }

        if ($course) {
            $query->whereHas('group', function($q) use ($course) {
                $q->where('course', $course);
            });
        }

        if ($facultyId) {
            $query->whereHas('group', function($q) use ($facultyId) {
                $q->where('faculty_id', $facultyId);
            });
        }

        $students = $query->paginate(50);

        // Get faculties and groups for filters
        $faculties = DB::table('faculties')->orderBy('name_uz')->get();
        $groups = DB::table('student_groups')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        // Statistics
        $activeCount = Student::where('status', 'active')->count();
        $maleCount = Student::where('gender', 'male')->count();
        $femaleCount = Student::where('gender', 'female')->count();

        return view('reports.students', compact(
            'students',
            'faculties',
            'groups',
            'activeCount',
            'maleCount',
            'femaleCount'
        ));
    }

    /**
     * Finance reports
     */
    public function finance(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->endOfMonth()->toDateString());
        $paymentMethod = $request->get('payment_method');
        $status = $request->get('status');

        // Build payments query
        $query = DB::table('student_payments as p')
            ->join('student_contracts as c', 'p.contract_id', '=', 'c.id')
            ->join('students as s', 'c.student_id', '=', 's.id')
            ->join('users as u', 's.user_id', '=', 'u.id')
            ->select('p.*', 'c.contract_number', 'u.name as student_name')
            ->whereBetween('p.payment_date', [$startDate, $endDate]);

        if ($paymentMethod) {
            $query->where('p.payment_method', $paymentMethod);
        }

        if ($status) {
            $query->where('p.status', $status);
        }

        $payments = $query->orderBy('p.payment_date', 'desc')->paginate(50);

        // Statistics
        $totalIncome = DB::table('student_payments')
            ->where('status', 'completed')
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->sum('amount');

        $averagePayment = DB::table('student_payments')
            ->where('status', 'completed')
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->avg('amount') ?? 0;

        $totalDebt = DB::table('student_contracts')
            ->where('status', 'active')
            ->get()
            ->sum(function($contract) {
                return ($contract->total_amount ?? 0) - ($contract->paid_amount ?? 0) - ($contract->discount_amount ?? 0);
            });

        $paymentsByMethod = DB::table('student_payments')
            ->select('payment_method', DB::raw('count(*) as count'), DB::raw('sum(amount) as total'))
            ->where('status', 'completed')
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->groupBy('payment_method')
            ->get();

        $paymentsByStatus = DB::table('student_payments')
            ->select('status', DB::raw('count(*) as count'), DB::raw('sum(amount) as total'))
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->groupBy('status')
            ->get();

        return view('reports.finance', compact(
            'payments',
            'totalIncome',
            'averagePayment',
            'totalDebt',
            'paymentsByMethod',
            'paymentsByStatus'
        ));
    }

    /**
     * Academic reports
     */
    public function academic(Request $request)
    {
        // Get filters
        $academicYear = $request->get('academic_year');
        $semester = $request->get('semester');
        $facultyId = $request->get('faculty_id');
        $course = $request->get('course');

        // Get academic years (placeholder)
        $academicYears = collect([
            (object)['id' => '2024', 'year' => '2024-2025'],
            (object)['id' => '2023', 'year' => '2023-2024'],
        ]);

        // Get faculties for filter
        $faculties = DB::table('faculties')->orderBy('name_uz')->get();

        // Get groups with counts
        $groups = DB::table('student_groups as g')
            ->leftJoin('students as s', 'g.id', '=', 's.group_id')
            ->leftJoin('specialties as sp', 'g.specialty_id', '=', 'sp.id')
            ->select(
                'g.*',
                'sp.name_uz as specialty_name',
                DB::raw('count(distinct s.id) as students_count')
            )
            ->where('g.is_active', true)
            ->groupBy('g.id', 'g.name', 'g.code', 'g.course', 'g.academic_year', 'g.specialty_id', 'g.faculty_id', 'g.curator_id', 'g.max_students', 'g.current_students', 'g.education_form', 'g.education_type', 'g.is_active', 'g.description', 'g.created_at', 'g.updated_at', 'sp.name_uz')
            ->orderBy('g.course')
            ->get();

        // Add faculty info to groups
        $groups = $groups->map(function($group) {
            if ($group->specialty_name) {
                $specialty = DB::table('specialties')->where('id', $group->specialty_id)->first();
                if ($specialty && $specialty->faculty_id) {
                    $faculty = DB::table('faculties')->where('id', $specialty->faculty_id)->first();
                    $group->specialty = (object)[
                        'name' => $group->specialty_name,
                        'faculty' => $faculty
                    ];
                }
            }
            return $group;
        });

        // Statistics
        $totalGroups = $groups->count();
        $totalStudents = DB::table('students')->where('status', 'active')->count();
        $totalSubjects = DB::table('subjects')->count();
        $totalTeachers = DB::table('users')
            ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->where('model_has_roles.model_type', 'App\\Models\\User')
            ->where('roles.name', 'Teacher')
            ->count();

        // Faculty stats
        $facultyStats = DB::table('student_groups as g')
            ->leftJoin('students as s', 'g.id', '=', 's.group_id')
            ->leftJoin('specialties as sp', 'g.specialty_id', '=', 'sp.id')
            ->select(
                'sp.faculty_id',
                DB::raw('count(distinct g.id) as groups'),
                DB::raw('count(distinct s.id) as students')
            )
            ->where('g.is_active', true)
            ->whereNotNull('sp.faculty_id')
            ->groupBy('sp.faculty_id')
            ->get()
            ->map(function($item) {
                $faculty = DB::table('faculties')->where('id', $item->faculty_id)->first();
                $item->faculty_name = $faculty->name ?? 'N/A';
                $item->subjects = 0; // Placeholder
                $item->teachers = 0; // Placeholder
                return $item;
            });

        // Grade distribution (placeholder)
        $gradeDistribution = collect([
            (object)['grade' => 90, 'count' => 120],
            (object)['grade' => 75, 'count' => 250],
            (object)['grade' => 60, 'count' => 180],
            (object)['grade' => 45, 'count' => 50],
        ]);
        $totalGrades = $gradeDistribution->sum('count');

        // Academic indicators (placeholder)
        $averageGPA = 3.8;
        $successRate = 85;
        $excellentStudents = 120;
        $failedGrades = 50;
        $attendanceRate = 92;

        return view('reports.academic', compact(
            'groups',
            'academicYears',
            'faculties',
            'facultyStats',
            'totalGroups',
            'totalStudents',
            'totalSubjects',
            'totalTeachers',
            'gradeDistribution',
            'totalGrades',
            'averageGPA',
            'successRate',
            'excellentStudents',
            'failedGrades',
            'attendanceRate'
        ));
    }

    /**
     * Attendance reports
     */
    public function attendance(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->endOfMonth()->toDateString());
        $groupId = $request->get('group_id');
        $status = $request->get('status');

        // Get groups for filter
        $groups = DB::table('student_groups')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        // Build attendance query (placeholder - adjust based on your attendance table)
        $query = DB::table('attendances as a')
            ->join('students as s', 'a.student_id', '=', 's.id')
            ->join('users as u', 's.user_id', '=', 'u.id')
            ->join('student_groups as g', 's.group_id', '=', 'g.id')
            ->leftJoin('subjects as sub', 'a.subject_id', '=', 'sub.id')
            ->select('a.*', 'u.name as student_name', 'g.name as group_name', 'sub.name_uz as subject_name')
            ->whereBetween('a.date', [$startDate, $endDate]);

        if ($groupId) {
            $query->where('s.group_id', $groupId);
        }

        if ($status) {
            $query->where('a.status', $status);
        }

        $attendanceRecords = $query->orderBy('a.date', 'desc')->paginate(50);

        // Statistics
        $presentCount = DB::table('attendances')
            ->where('status', 'present')
            ->whereBetween('date', [$startDate, $endDate])
            ->count();

        $absentCount = DB::table('attendances')
            ->where('status', 'absent')
            ->whereBetween('date', [$startDate, $endDate])
            ->count();

        $lateCount = DB::table('attendances')
            ->where('status', 'late')
            ->whereBetween('date', [$startDate, $endDate])
            ->count();

        $totalRecords = $presentCount + $absentCount + $lateCount;
        $attendanceRate = $totalRecords > 0 ? ($presentCount / $totalRecords) * 100 : 0;

        // Student attendance summary
        $studentAttendance = DB::table('students as s')
            ->join('users as u', 's.user_id', '=', 'u.id')
            ->join('student_groups as g', 's.group_id', '=', 'g.id')
            ->leftJoin('attendances as a', function($join) use ($startDate, $endDate) {
                $join->on('s.id', '=', 'a.student_id')
                     ->whereBetween('a.date', [$startDate, $endDate]);
            })
            ->select(
                'u.name',
                'g.name as group_name',
                DB::raw('sum(case when a.status = "present" then 1 else 0 end) as present_count'),
                DB::raw('sum(case when a.status = "absent" then 1 else 0 end) as absent_count'),
                DB::raw('sum(case when a.status = "late" then 1 else 0 end) as late_count')
            )
            ->where('s.status', 'active')
            ->groupBy('s.id', 'u.name', 'g.name')
            ->limit(20)
            ->get();

        // Daily attendance for chart
        $dailyAttendance = DB::table('attendances')
            ->select(
                DB::raw('DATE(date) as date'),
                DB::raw('sum(case when status = "present" then 1 else 0 end) as present'),
                DB::raw('sum(case when status = "absent" then 1 else 0 end) as absent')
            )
            ->whereBetween('date', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('reports.attendance', compact(
            'attendanceRecords',
            'groups',
            'presentCount',
            'absentCount',
            'lateCount',
            'attendanceRate',
            'studentAttendance',
            'dailyAttendance'
        ));
    }

    /**
     * Export report to Excel
     */
    public function export(Request $request)
    {
        $type = $request->get('type', 'students');

        // Implement export logic here
        // You can use Laravel Excel package for this

        return response()->json([
            'success' => true,
            'message' => 'Hisobot eksport qilindi'
        ]);
    }

    /**
     * Print report
     */
    public function print(Request $request)
    {
        $type = $request->get('type', 'students');

        // Return printable view
        return view('reports.print.' . $type);
    }
}
