<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index()
    {
        $attendances = Attendance::with(['user'])
            ->orderBy('date', 'desc')
            ->paginate(20);

        return view('hr.attendance.index', compact('attendances'));
    }

    public function mark()
    {
        $users = User::all();
        return view('hr.attendance.mark', compact('users'));
    }

    public function storeMark(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'status' => 'required|in:present,absent,late,excused',
            'check_in' => 'nullable|date_format:H:i',
            'check_out' => 'nullable|date_format:H:i',
        ]);

        Attendance::updateOrCreate(
            [
                'user_id' => $validated['user_id'],
                'date' => $validated['date'],
            ],
            [
                'status' => $validated['status'],
                'check_in' => $validated['check_in'] ?? null,
                'check_out' => $validated['check_out'] ?? null,
            ]
        );

        return redirect()->route('hr.attendance.index')
            ->with('success', 'Davomat belgilandi');
    }

    public function today()
    {
        $attendances = Attendance::with(['user'])
            ->whereDate('date', Carbon::today())
            ->get();

        return view('hr.attendance.today', compact('attendances'));
    }

    public function report()
    {
        return view('hr.attendance.report');
    }

    public function exportReport(Request $request)
    {
        // TODO: Implement report export functionality
        return response()->download(storage_path('app/attendance_report.xlsx'));
    }

    public function userAttendance($userId)
    {
        $user = User::findOrFail($userId);
        $attendances = Attendance::where('user_id', $userId)
            ->orderBy('date', 'desc')
            ->paginate(30);

        return view('hr.attendance.user', compact('user', 'attendances'));
    }

    public function bulkMark(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'attendances' => 'required|array',
            'attendances.*.user_id' => 'required|exists:users,id',
            'attendances.*.status' => 'required|in:present,absent,late,excused',
        ]);

        foreach ($validated['attendances'] as $attendance) {
            Attendance::updateOrCreate(
                [
                    'user_id' => $attendance['user_id'],
                    'date' => $validated['date'],
                ],
                [
                    'status' => $attendance['status'],
                ]
            );
        }

        return redirect()->route('hr.attendance.index')
            ->with('success', 'Guruh davomati belgilandi');
    }

    public function calendar()
    {
        return view('hr.attendance.calendar');
    }

    public function calendarData(Request $request)
    {
        $start = $request->input('start');
        $end = $request->input('end');

        $attendances = Attendance::with(['user'])
            ->whereBetween('date', [$start, $end])
            ->get();

        $events = $attendances->map(function ($attendance) {
            return [
                'title' => $attendance->user->name . ' - ' . $attendance->status,
                'start' => $attendance->date,
                'color' => $this->getStatusColor($attendance->status),
            ];
        });

        return response()->json($events);
    }

    private function getStatusColor($status)
    {
        return match($status) {
            'present' => '#10b981',
            'absent' => '#ef4444',
            'late' => '#f59e0b',
            'excused' => '#6b7280',
            default => '#3b82f6',
        };
    }
}
