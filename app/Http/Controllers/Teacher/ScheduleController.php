<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Teacher;
use App\Models\Employee;
use App\Models\Schedule;
use App\Models\ScheduleSlot;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    /**
     * Display teacher's weekly schedule
     */
    public function index()
    {
        $user = Auth::user();
        $teacher = Teacher::where('user_id', $user->id)->first();
        $employee = Employee::where('user_id', $user->id)->first();

        $teacherId = $teacher ? $teacher->id : null;
        $employeeId = $employee ? $employee->id : null;

        // Get schedule slots for this teacher
        $query = ScheduleSlot::with(['schedule.group', 'subject', 'room', 'teacher'])
            ->whereHas('schedule', function($q) {
                $q->where('status', 'active');
            });

        // Filter by teacher ID if we have one
        if ($teacherId || $employeeId) {
            $query->where(function($q) use ($teacherId, $employeeId) {
                if ($teacherId) {
                    $q->where('teacher_id', $teacherId);
                }
                if ($employeeId) {
                    $q->orWhere('teacher_id', $employeeId);
                }
            });
        }

        $scheduleSlots = $query->orderBy('day_of_week')
            ->orderBy('time_slot')
            ->get();

        // Debug: if no slots found for this teacher, show message
        if ($scheduleSlots->isEmpty() && ($teacherId || $employeeId)) {
            // Check if there are any slots at all
            $allSlots = ScheduleSlot::whereHas('schedule', function($q) {
                $q->where('status', 'active');
            })->count();

            if ($allSlots > 0) {
                session()->flash('info', "Sizning ID: Teacher={$teacherId}, Employee={$employeeId}. Jadvaldagi teacher_id lar bilan mos kelmayapti.");
            }
        }

        // Build weekly schedule
        $weeklySchedule = $this->buildWeeklySchedule($scheduleSlots);

        // Get statistics
        $totalLessonsPerWeek = $scheduleSlots->count();
        $totalHoursPerWeek = $totalLessonsPerWeek * 1.5; // Each lesson is ~1.5 hours

        $daysWithClasses = collect($weeklySchedule)->filter(function($lessons) {
            return count($lessons) > 0;
        })->count();

        // Get unique groups
        $groups = $scheduleSlots->pluck('schedule.group')->filter()->unique('id');

        return view('teacher.schedule.index', compact(
            'teacher',
            'weeklySchedule',
            'totalLessonsPerWeek',
            'totalHoursPerWeek',
            'daysWithClasses',
            'groups'
        ));
    }

    /**
     * Build weekly schedule from schedule slots
     */
    private function buildWeeklySchedule($scheduleSlots)
    {
        $daysOfWeek = [
            1 => 'Dushanba',
            2 => 'Seshanba',
            3 => 'Chorshanba',
            4 => 'Payshanba',
            5 => 'Juma',
            6 => 'Shanba',
        ];

        $timeSlotInfo = [
            1 => ['start' => '08:30', 'end' => '09:50'],
            2 => ['start' => '10:10', 'end' => '11:30'],
            3 => ['start' => '12:00', 'end' => '13:20'],
            4 => ['start' => '14:00', 'end' => '15:20'],
            5 => ['start' => '15:40', 'end' => '17:00'],
            6 => ['start' => '17:20', 'end' => '18:40'],
        ];

        $lessonTypes = [
            'lecture' => 'Ma\'ruza',
            'practice' => 'Amaliyot',
            'seminar' => 'Seminar',
            'lab' => 'Laboratoriya',
        ];

        $weeklySchedule = [];

        // Initialize empty schedule
        foreach ($daysOfWeek as $dayNum => $dayName) {
            $weeklySchedule[$dayNum] = [];
        }

        foreach ($scheduleSlots as $slot) {
            // Use day_number attribute which normalizes string/int
            $dayNum = $slot->day_number;
            $timeSlot = is_numeric($slot->time_slot) ? (int)$slot->time_slot : 1;
            $timeInfo = $timeSlotInfo[$timeSlot] ?? ['start' => '00:00', 'end' => '00:00'];

            if (isset($daysOfWeek[$dayNum])) {
                $weeklySchedule[$dayNum][] = [
                    'id' => $slot->id,
                    'subject' => $slot->subject,
                    'group' => $slot->schedule->group ?? null,
                    'room' => $slot->room->name ?? 'N/A',
                    'start_time' => $timeInfo['start'],
                    'end_time' => $timeInfo['end'],
                    'type' => $lessonTypes[$slot->lesson_type] ?? $slot->lesson_type,
                    'type_code' => $slot->lesson_type,
                    'time_slot' => $timeSlot,
                ];
            }
        }

        // Sort each day's lessons by time slot
        foreach ($weeklySchedule as $day => $lessons) {
            usort($weeklySchedule[$day], function($a, $b) {
                return $a['time_slot'] - $b['time_slot'];
            });
        }

        return $weeklySchedule;
    }

    /**
     * Show today's schedule
     */
    public function today()
    {
        $user = Auth::user();
        $teacher = Teacher::where('user_id', $user->id)->first();
        $employee = Employee::where('user_id', $user->id)->first();

        $teacherId = $teacher ? $teacher->id : null;
        $employeeId = $employee ? $employee->id : null;

        $today = Carbon::today()->dayOfWeek;
        $dayOfWeek = $today == 0 ? 7 : $today; // Sunday = 7

        $todaySlots = ScheduleSlot::with(['schedule.group', 'subject', 'room'])
            ->where(function($q) use ($teacherId, $employeeId) {
                if ($teacherId) {
                    $q->where('teacher_id', $teacherId);
                }
                if ($employeeId) {
                    $q->orWhere('teacher_id', $employeeId);
                }
            })
            ->where('day_of_week', $dayOfWeek)
            ->whereHas('schedule', function($q) {
                $q->where('status', 'active');
            })
            ->orderBy('time_slot')
            ->get();

        $timeSlotInfo = [
            1 => ['start' => '08:30', 'end' => '09:50'],
            2 => ['start' => '10:10', 'end' => '11:30'],
            3 => ['start' => '12:00', 'end' => '13:20'],
            4 => ['start' => '14:00', 'end' => '15:20'],
            5 => ['start' => '15:40', 'end' => '17:00'],
            6 => ['start' => '17:20', 'end' => '18:40'],
        ];

        $lessonTypes = [
            'lecture' => 'Ma\'ruza',
            'practice' => 'Amaliyot',
            'seminar' => 'Seminar',
            'lab' => 'Laboratoriya',
        ];

        $todaySchedule = $todaySlots->map(function($slot) use ($timeSlotInfo, $lessonTypes) {
            $timeInfo = $timeSlotInfo[$slot->time_slot] ?? ['start' => '00:00', 'end' => '00:00'];
            return [
                'id' => $slot->id,
                'subject' => $slot->subject,
                'group' => $slot->schedule->group ?? null,
                'room' => $slot->room->name ?? 'N/A',
                'start_time' => $timeInfo['start'],
                'end_time' => $timeInfo['end'],
                'type' => $lessonTypes[$slot->lesson_type] ?? $slot->lesson_type,
                'time_slot' => $slot->time_slot,
            ];
        });

        $dayNames = [
            1 => 'Dushanba',
            2 => 'Seshanba',
            3 => 'Chorshanba',
            4 => 'Payshanba',
            5 => 'Juma',
            6 => 'Shanba',
            7 => 'Yakshanba',
        ];

        return view('teacher.schedule.today', compact('teacher', 'todaySchedule', 'dayOfWeek', 'dayNames'));
    }

    /**
     * Export schedule to PDF
     */
    public function exportPdf()
    {
        $user = Auth::user();
        $teacher = Teacher::where('user_id', $user->id)->first();
        $employee = Employee::where('user_id', $user->id)->first();

        $teacherId = $teacher ? $teacher->id : null;
        $employeeId = $employee ? $employee->id : null;

        $scheduleSlots = ScheduleSlot::with(['schedule.group', 'subject', 'room'])
            ->where(function($q) use ($teacherId, $employeeId) {
                if ($teacherId) {
                    $q->where('teacher_id', $teacherId);
                }
                if ($employeeId) {
                    $q->orWhere('teacher_id', $employeeId);
                }
            })
            ->whereHas('schedule', function($q) {
                $q->where('status', 'active');
            })
            ->orderBy('day_of_week')
            ->orderBy('time_slot')
            ->get();

        $weeklySchedule = $this->buildWeeklySchedule($scheduleSlots);

        $pdf = \PDF::loadView('teacher.schedule.pdf', compact('teacher', 'weeklySchedule'));
        return $pdf->download('dars-jadvali-' . date('Y-m-d') . '.pdf');
    }
}
