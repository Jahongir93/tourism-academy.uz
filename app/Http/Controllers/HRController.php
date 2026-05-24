<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Department;
use App\Models\LeaveRequest;
use App\Models\Attendance;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HRController extends Controller
{
    /**
     * HR Dashboard
     */
    public function dashboard()
    {
        // Statistikalar
        $stats = [
            'total_employees' => Employee::where('status', 'active')->count(),
            'new_employees_month' => Employee::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)->count(),
            'pending_leave_requests' => LeaveRequest::where('status', 'pending')->count() ?? 0,
            'today_attendance' => Attendance::whereDate('date', today())->count() ?? 0,
        ];

        // Bo'limlar bo'yicha xodimlar
        $departmentStats = Department::withCount(['employees' => function($q) {
            $q->where('status', 'active');
        }])->orderBy('employees_count', 'desc')->limit(5)->get();

        // So'nggi ishga qabul qilinganlar
        $recentHires = Employee::with('department')
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Kutilayotgan ta'til arizalari
        $pendingLeaves = LeaveRequest::with(['employee', 'leaveType'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Oylik davomat statistikasi
        $monthlyAttendance = $this->getMonthlyAttendanceStats();

        return view('hr.dashboard', compact(
            'stats',
            'departmentStats',
            'recentHires',
            'pendingLeaves',
            'monthlyAttendance'
        ));
    }

    /**
     * Xodimlar ro'yxati
     */
    public function employeesIndex(Request $request)
    {
        $query = Employee::with(['department', 'position']);

        // Qidiruv
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('employee_id', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Bo'lim filtri
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        // Status filtri
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $employees = $query->orderBy('last_name')->paginate(20);
        $departments = Department::orderBy('name')->get();

        return view('hr.employees.index', compact('employees', 'departments'));
    }

    /**
     * Yangi xodim yaratish formasi
     */
    public function employeesCreate()
    {
        $departments = Department::orderBy('name')->get();
        return view('hr.employees.create', compact('departments'));
    }

    /**
     * Yangi xodim saqlash
     */
    public function employeesStore(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email',
            'phone' => 'nullable|string|max:20',
            'department_id' => 'required|exists:departments,id',
            'position' => 'nullable|string|max:255',
            'hire_date' => 'required|date',
            'birth_date' => 'nullable|date',
            'address' => 'nullable|string',
            'salary' => 'nullable|numeric|min:0',
        ]);

        $validated['status'] = 'active';
        $validated['employee_id'] = 'EMP' . str_pad(Employee::max('id') + 1, 5, '0', STR_PAD_LEFT);

        Employee::create($validated);

        return redirect()->route('hr.employees.index')
            ->with('success', 'Xodim muvaffaqiyatli qo\'shildi');
    }

    /**
     * Xodim ma'lumotlarini ko'rish
     */
    public function employeesShow(Employee $employee)
    {
        $employee->load(['department', 'leaveRequests', 'attendances' => function($q) {
            $q->orderBy('date', 'desc')->limit(30);
        }]);

        return view('hr.employees.show', compact('employee'));
    }

    /**
     * Xodimni tahrirlash
     */
    public function employeesEdit(Employee $employee)
    {
        $departments = Department::orderBy('name')->get();
        return view('hr.employees.edit', compact('employee', 'departments'));
    }

    /**
     * Xodimni yangilash
     */
    public function employeesUpdate(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email,' . $employee->id,
            'phone' => 'nullable|string|max:20',
            'department_id' => 'required|exists:departments,id',
            'position' => 'nullable|string|max:255',
            'hire_date' => 'required|date',
            'birth_date' => 'nullable|date',
            'address' => 'nullable|string',
            'salary' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,inactive,terminated',
        ]);

        $employee->update($validated);

        return redirect()->route('hr.employees.show', $employee)
            ->with('success', 'Xodim ma\'lumotlari yangilandi');
    }

    /**
     * Shartnomalar
     */
    public function employeesContracts()
    {
        $employees = Employee::with('department')
            ->whereNotNull('contract_end_date')
            ->orderBy('contract_end_date')
            ->paginate(20);

        return view('hr.employees.contracts', compact('employees'));
    }

    /**
     * Vakansiyalar
     */
    public function recruitmentVacancies()
    {
        return view('hr.recruitment.vacancies');
    }

    /**
     * Arizalar
     */
    public function recruitmentApplications()
    {
        return view('hr.recruitment.applications');
    }

    /**
     * Suhbatlar
     */
    public function recruitmentInterviews()
    {
        return view('hr.recruitment.interviews');
    }

    /**
     * Ta'til arizalari
     */
    public function leaveRequests(Request $request)
    {
        $query = LeaveRequest::with(['employee', 'leaveType']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $requests = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('hr.leave.requests', compact('requests'));
    }

    /**
     * Ta'til arizasini tasdiqlash
     */
    public function leaveApprove(LeaveRequest $leaveRequest)
    {
        $leaveRequest->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Ta\'til arizasi tasdiqlandi');
    }

    /**
     * Ta'til arizasini rad etish
     */
    public function leaveReject(LeaveRequest $leaveRequest, Request $request)
    {
        $leaveRequest->update([
            'status' => 'rejected',
            'rejection_reason' => $request->reason,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Ta\'til arizasi rad etildi');
    }

    /**
     * Ta'til kalendari
     */
    public function leaveCalendar()
    {
        $approvedLeaves = LeaveRequest::with('employee')
            ->where('status', 'approved')
            ->whereDate('start_date', '>=', now()->startOfMonth())
            ->whereDate('end_date', '<=', now()->endOfMonth()->addMonth())
            ->get();

        return view('hr.leave.calendar', compact('approvedLeaves'));
    }

    /**
     * Ta'til balanslari
     */
    public function leaveBalances()
    {
        $employees = Employee::with('leaveBalances')
            ->where('status', 'active')
            ->paginate(20);

        return view('hr.leave.balances', compact('employees'));
    }

    /**
     * Davomat
     */
    public function attendanceIndex(Request $request)
    {
        $date = $request->get('date', today()->format('Y-m-d'));

        $attendances = Attendance::with('employee')
            ->whereDate('date', $date)
            ->orderBy('check_in')
            ->paginate(50);

        $stats = [
            'present' => Attendance::whereDate('date', $date)->where('status', 'present')->count(),
            'absent' => Employee::where('status', 'active')->count() - Attendance::whereDate('date', $date)->count(),
            'late' => Attendance::whereDate('date', $date)->where('is_late', true)->count(),
        ];

        return view('hr.attendance.index', compact('attendances', 'date', 'stats'));
    }

    /**
     * Ish haqi
     */
    public function payrollIndex(Request $request)
    {
        $month = $request->get('month', now()->format('Y-m'));

        $employees = Employee::with('department')
            ->where('status', 'active')
            ->orderBy('last_name')
            ->paginate(20);

        return view('hr.payroll.index', compact('employees', 'month'));
    }

    /**
     * Treninglar
     */
    public function trainingIndex()
    {
        return view('hr.training.index');
    }

    /**
     * Hujjatlar
     */
    public function documentsIndex()
    {
        return view('hr.documents.index');
    }

    /**
     * Xodimlar hisoboti
     */
    public function reportsEmployees()
    {
        $stats = [
            'total' => Employee::count(),
            'active' => Employee::where('status', 'active')->count(),
            'inactive' => Employee::where('status', 'inactive')->count(),
            'terminated' => Employee::where('status', 'terminated')->count(),
        ];

        $byDepartment = Department::withCount('employees')->orderBy('employees_count', 'desc')->get();

        return view('hr.reports.employees', compact('stats', 'byDepartment'));
    }

    /**
     * Davomat hisoboti
     */
    public function reportsAttendance()
    {
        return view('hr.reports.attendance');
    }

    /**
     * Ta'til hisoboti
     */
    public function reportsLeave()
    {
        return view('hr.reports.leave');
    }

    /**
     * Kadrlar almashinuvi
     */
    public function reportsTurnover()
    {
        return view('hr.reports.turnover');
    }

    /**
     * Sozlamalar
     */
    public function settings()
    {
        return view('hr.settings');
    }

    /**
     * Oylik davomat statistikasi
     */
    private function getMonthlyAttendanceStats()
    {
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();

        $stats = [];
        $current = $startOfMonth->copy();

        while ($current <= $endOfMonth) {
            $stats[] = [
                'date' => $current->format('d'),
                'present' => Attendance::whereDate('date', $current)->where('status', 'present')->count(),
                'absent' => 0,
            ];
            $current->addDay();
        }

        return $stats;
    }
}
