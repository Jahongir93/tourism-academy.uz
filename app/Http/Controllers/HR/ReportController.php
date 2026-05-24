<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Student;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index()
    {
        return view('hr.reports.index');
    }

    public function generate()
    {
        return view('hr.reports.generate');
    }

    public function processGenerate(Request $request)
    {
        $validated = $request->validate([
            'report_type' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $reportData = match($validated['report_type']) {
            'employee' => $this->generateEmployeeReport($validated['start_date'], $validated['end_date']),
            'student' => $this->generateStudentReport($validated['start_date'], $validated['end_date']),
            'attendance' => $this->generateAttendanceReport($validated['start_date'], $validated['end_date']),
            default => [],
        };

        return view('hr.reports.result', compact('reportData', 'validated'));
    }

    public function download($type)
    {
        // TODO: Implement download functionality
        return response()->download(storage_path("app/reports/{$type}.pdf"));
    }

    public function employeeSummary()
    {
        $totalEmployees = Employee::count();
        $employeesByType = Employee::selectRaw('employee_type, COUNT(*) as count')
            ->join('users', 'employees.user_id', '=', 'users.id')
            ->groupBy('employee_type')
            ->get();

        return view('hr.reports.employee-summary', compact('totalEmployees', 'employeesByType'));
    }

    public function studentSummary()
    {
        $totalStudents = Student::count();
        $studentsByGroup = Student::selectRaw('student_group_id, COUNT(*) as count')
            ->groupBy('student_group_id')
            ->with('group')
            ->get();

        return view('hr.reports.student-summary', compact('totalStudents', 'studentsByGroup'));
    }

    public function attendanceSummary()
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();

        $todayAttendance = Attendance::whereDate('date', $today)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get();

        $monthlyAttendance = Attendance::whereDate('date', '>=', $thisMonth)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get();

        return view('hr.reports.attendance-summary', compact('todayAttendance', 'monthlyAttendance'));
    }

    public function monthlyReport()
    {
        $month = request()->input('month', Carbon::now()->format('Y-m'));
        $startDate = Carbon::parse($month)->startOfMonth();
        $endDate = Carbon::parse($month)->endOfMonth();

        $attendances = Attendance::whereBetween('date', [$startDate, $endDate])
            ->with('user')
            ->get();

        $employees = Employee::count();
        $students = Student::count();

        return view('hr.reports.monthly', compact('attendances', 'employees', 'students', 'month'));
    }

    private function generateEmployeeReport($startDate, $endDate)
    {
        return Employee::with(['user'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();
    }

    private function generateStudentReport($startDate, $endDate)
    {
        return Student::with(['user', 'group'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();
    }

    private function generateAttendanceReport($startDate, $endDate)
    {
        return Attendance::with(['user'])
            ->whereBetween('date', [$startDate, $endDate])
            ->get();
    }
}
