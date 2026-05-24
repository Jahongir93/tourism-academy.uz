<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HRDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Get statistics
        $stats = [
            'total_employees' => Employee::count(),
            'active_employees' => Employee::where('status', 'active')->count(),
            'teachers_count' => User::role('Teacher')->count(),
            'administrative_staff' => Employee::where('employee_type', 'administrative')
                ->orWhere('employee_type', 'support')
                ->count(),
            'new_hires_this_month' => Employee::whereMonth('created_at', now()->month)->count(),
            'pending_leave_requests' => 0, // Add when leave system is implemented
        ];

        // Recent employees
        $recentEmployees = Employee::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Employees by type
        $employeesByType = Employee::select(
                'employee_type',
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('employee_type')
            ->get();

        // Employees by status
        $employeesByStatus = Employee::select(
                'status',
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('status')
            ->get();

        return view('hr.dashboard', compact(
            'user',
            'stats',
            'recentEmployees',
            'employeesByType',
            'employeesByStatus'
        ));
    }
}
