<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Group;
use App\Models\Teacher;
use App\Models\Employee;
use App\Models\TeacherSubject;
use App\Models\JournalEntry;
use App\Models\Student;
use App\Models\Schedule;
use App\Models\AcademicYear;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TeacherDashboardController extends Controller
{
    /**
     * Teacher Dashboard ko'rsatish
     */
    public function index()
    {
        // Faqat teacher kirishi mumkin
        if (!auth()->user()->hasAnyRole(['teacher', 'Teacher', 'superadmin'])) {
            abort(403, 'Sizda bu sahifaga kirish huquqi yo\'q');
        }

        $user = auth()->user();

        // Teacher va Employee modellarini topish
        $teacher = Teacher::where('user_id', $user->id)->first();
        $employee = Employee::where('user_id', $user->id)->first();

        $teacherId = $teacher ? $teacher->id : null;
        $employeeId = $employee ? $employee->id : null;

        // Biriktirilgan fanlar va guruhlar
        $assignments = $this->getTeacherAssignments($teacherId, $employeeId);

        // Statistikalar
        $totalGroups = $assignments->pluck('groups')->flatten()->unique('id')->count();
        $totalStudents = $this->getTeacherStudentsCount($assignments);
        $todayLessons = $this->getTodayLessonsCount($teacherId, $employeeId);
        $pendingAssignments = 0; // Hozircha

        // Bugungi jadval
        $todaySchedule = $this->getTodaySchedule($teacherId, $employeeId);

        // Tekshirilmagan ishlar
        $uncheckedWorks = [];

        return view('teacher.dashboard', compact(
            'assignments',
            'totalGroups',
            'totalStudents',
            'todayLessons',
            'pendingAssignments',
            'todaySchedule',
            'uncheckedWorks',
            'teacher',
            'employee'
        ));
    }

    /**
     * O'qituvchiga biriktirilgan fanlar va guruhlarni olish
     */
    private function getTeacherAssignments($teacherId, $employeeId)
    {
        $assignments = collect();

        // JournalEntry dan olish (teacher_id orqali)
        if ($teacherId) {
            $journals = JournalEntry::with(['subject', 'group', 'academicYear'])
                ->where('teacher_id', $teacherId)
                ->get();

            foreach ($journals as $journal) {
                if (!$journal->subject) continue;

                $subjectId = $journal->subject_id;
                $existing = $assignments->firstWhere('subject_id', $subjectId);

                if ($existing) {
                    // Guruhni qo'shish
                    if ($journal->group && !$existing['groups']->contains('id', $journal->group_id)) {
                        $existing['groups']->push($journal->group);
                    }
                } else {
                    $assignments->push([
                        'subject_id' => $subjectId,
                        'subject' => $journal->subject,
                        'groups' => collect($journal->group ? [$journal->group] : []),
                        'academic_year' => $journal->academicYear,
                        'semester' => $journal->semester_id,
                        'source' => 'journal'
                    ]);
                }
            }
        }

        // TeacherSubject dan olish (teacher_id orqali - bu Employee ID)
        if ($employeeId) {
            $teacherSubjects = TeacherSubject::with(['subject', 'academicYear'])
                ->where('teacher_id', $employeeId)
                ->where('status', 'active')
                ->get();

            foreach ($teacherSubjects as $ts) {
                if (!$ts->subject) continue;

                $subjectId = $ts->subject_id;
                $existing = $assignments->firstWhere('subject_id', $subjectId);

                // Guruhlarni olish
                $groupIds = $ts->group_ids ?? [];
                $groups = Group::whereIn('id', $groupIds)->get();

                if ($existing) {
                    // Mavjud bo'lmagan guruhlarni qo'shish
                    foreach ($groups as $group) {
                        if (!$existing['groups']->contains('id', $group->id)) {
                            $existing['groups']->push($group);
                        }
                    }
                } else {
                    $assignments->push([
                        'subject_id' => $subjectId,
                        'subject' => $ts->subject,
                        'groups' => $groups,
                        'academic_year' => $ts->academicYear,
                        'semester' => $ts->semester_id,
                        'source' => 'teacher_subject'
                    ]);
                }
            }
        }

        return $assignments;
    }

    /**
     * Talabalar sonini hisoblash
     */
    private function getTeacherStudentsCount($assignments)
    {
        $groupIds = $assignments->pluck('groups')->flatten()->pluck('id')->unique()->toArray();

        if (empty($groupIds)) {
            return 0;
        }

        return Student::whereIn('group_id', $groupIds)
            ->where('status', 'active')
            ->count();
    }

    /**
     * Bugungi darslar sonini olish
     */
    private function getTodayLessonsCount($teacherId, $employeeId)
    {
        $today = Carbon::today()->dayOfWeek;
        // Carbon: 0=Yakshanba, 1=Dushanba...
        $dayOfWeek = $today == 0 ? 7 : $today;

        $count = 0;

        // ScheduleSlot dan olish
        $query = \App\Models\ScheduleSlot::where('day_of_week', $dayOfWeek)
            ->whereHas('schedule', function($q) {
                $q->where('status', 'active');
            });

        if ($teacherId) {
            $count = (clone $query)->where('teacher_id', $teacherId)->count();
        }

        if ($count == 0 && $employeeId) {
            $count = (clone $query)->where('teacher_id', $employeeId)->count();
        }

        return $count;
    }

    /**
     * Bugungi jadval
     */
    private function getTodaySchedule($teacherId, $employeeId)
    {
        $today = Carbon::today()->dayOfWeek;
        $dayOfWeek = $today == 0 ? 7 : $today;

        // ScheduleSlot dan olish
        $scheduleSlots = \App\Models\ScheduleSlot::with(['schedule.group', 'subject', 'room'])
            ->where('day_of_week', $dayOfWeek)
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
            ->orderBy('time_slot')
            ->get();

        // Formatlab qaytarish
        $result = [];
        $lessonTimes = [
            1 => '08:30',
            2 => '10:10',
            3 => '12:00',
            4 => '14:00',
            5 => '15:40',
            6 => '17:20',
        ];

        $lessonTypes = [
            'lecture' => 'Ma\'ruza',
            'practice' => 'Amaliyot',
            'seminar' => 'Seminar',
            'lab' => 'Laboratoriya',
        ];

        foreach ($scheduleSlots as $slot) {
            $result[] = [
                'time' => $lessonTimes[$slot->time_slot] ?? '---',
                'group' => $slot->schedule->group->name ?? 'N/A',
                'subject' => $slot->subject->name_uz ?? $slot->subject->name ?? 'N/A',
                'room' => $slot->room->name ?? 'N/A',
                'lesson_number' => $slot->time_slot,
                'type' => $slot->lesson_type ?? 'lecture'
            ];
        }

        return $result;
    }

    /**
     * Dashboard statistikalarini API orqali olish
     */
    public function getDashboardStats()
    {
        if (!auth()->user()->hasRole('teacher') && !auth()->user()->hasRole('superadmin')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $user = auth()->user();
        $teacher = Teacher::where('user_id', $user->id)->first();
        $employee = Employee::where('user_id', $user->id)->first();

        $teacherId = $teacher ? $teacher->id : null;
        $employeeId = $employee ? $employee->id : null;

        $assignments = $this->getTeacherAssignments($teacherId, $employeeId);

        $stats = [
            'totalGroups' => $assignments->pluck('groups')->flatten()->unique('id')->count(),
            'totalStudents' => $this->getTeacherStudentsCount($assignments),
            'todayLessons' => $this->getTodayLessonsCount($teacherId, $employeeId),
            'pendingAssignments' => 0,
            'timestamp' => now()->toIso8601String()
        ];

        return response()->json($stats);
    }
}
