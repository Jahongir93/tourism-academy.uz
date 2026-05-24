<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Student;
use App\Models\Attendance;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $student = Student::where('user_id', $user->id)->first();

        if (!$student) {
            return redirect()->route('student.dashboard')
                ->with('error', 'Student ma\'lumotlari topilmadi');
        }

        // Get schedule slots for the student's group
        $schedule = \App\Models\Schedule::where('group_id', $student->group_id)
            ->where('status', 'active')
            ->first();

        if (!$schedule) {
            return view('student.attendance.index', [
                'student' => $student,
                'hasSchedule' => false,
                'message' => 'Guruhingiz uchun hali darslar biriktirilmagan',
                'attendanceData' => [],
                'totalHoursMissed' => 0
            ]);
        }

        // Get schedule slots with subject information
        $scheduleSlots = \App\Models\ScheduleSlot::where('schedule_id', $schedule->id)
            ->with(['subject'])
            ->get();

        // Get attendance records for this student
        $attendances = Attendance::where('student_id', $student->id)
            ->with(['subject', 'scheduleSlot'])
            ->get();

        // Calculate hours missed per subject
        $attendanceBySubject = [];
        $totalHoursMissed = 0;

        // Each lesson pair is 1 hour 20 minutes = 1.33 hours (80 minutes)
        $hoursPerLesson = 1.33;

        // Group schedule slots by subject
        $subjectSchedules = $scheduleSlots->groupBy('subject_id');

        foreach ($subjectSchedules as $subjectId => $slots) {
            $subject = $slots->first()->subject;

            if (!$subject) continue;

            // Count total lessons for this subject
            $totalLessons = $slots->count();

            // Count attendance for this subject
            $subjectAttendances = $attendances->where('subject_id', $subjectId);
            $presentCount = $subjectAttendances->where('status', 'present')->count();
            $absentCount = $subjectAttendances->where('status', 'absent')->count();
            $lateCount = $subjectAttendances->where('status', 'late')->count();

            // Calculate hours missed (absent + half for late)
            $hoursMissed = ($absentCount * $hoursPerLesson) + ($lateCount * $hoursPerLesson * 0.5);
            $totalHoursMissed += $hoursMissed;

            // Calculate attendance percentage
            $totalRecorded = $subjectAttendances->count();
            $attendancePercentage = $totalRecorded > 0 ? ($presentCount / $totalRecorded) * 100 : 0;

            $attendanceBySubject[] = [
                'subject_id' => $subjectId,
                'subject_name' => $subject->name,
                'subject_code' => $subject->code ?? 'N/A',
                'total_lessons' => $totalLessons,
                'present' => $presentCount,
                'absent' => $absentCount,
                'late' => $lateCount,
                'hours_missed' => round($hoursMissed, 2),
                'attendance_percentage' => round($attendancePercentage, 2),
                'weekly_lessons' => $slots->count()
            ];
        }

        // Sort by hours missed (descending)
        usort($attendanceBySubject, function($a, $b) {
            return $b['hours_missed'] <=> $a['hours_missed'];
        });

        return view('student.attendance.index', [
            'student' => $student,
            'hasSchedule' => true,
            'attendanceData' => $attendanceBySubject,
            'totalHoursMissed' => round($totalHoursMissed, 2),
            'hoursPerLesson' => $hoursPerLesson
        ]);
    }
}
