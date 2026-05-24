<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\FaceAttendanceService;
use App\Models\Attendance;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Universal dashboard
     */
    public function index()
    {
        $user = Auth::user();

        // Check for Admin or SuperAdmin role
        if ($user->hasRole(['Admin', 'admin', 'SuperAdmin', 'superadmin'])) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->hasRole(['Teacher', 'teacher'])) {
            return redirect()->route('teacher.dashboard');
        } elseif ($user->hasRole(['Student', 'student'])) {
            return redirect()->route('student.dashboard');
        } elseif ($user->hasRole(['PR', 'pr'])) {
            return redirect()->route('pr.dashboard');
        } elseif ($user->hasRole(['Marketing', 'marketing'])) {
            return redirect()->route('marketing.dashboard');
        } elseif ($user->hasRole(['HR', 'hr'])) {
            return redirect()->route('hr.dashboard');
        } elseif ($user->hasRole(['ChatAdmin', 'chatadmin', 'chat-admin'])) {
            return redirect()->route('chat-admin.dashboard');
        }

        return view('dashboard.default');
    }
    
    /**
     * Admin dashboard
     */
    public function adminDashboard()
    {
        $stats = \Illuminate\Support\Facades\Cache::remember('admin_dashboard_stats', 300, fn() => $this->getAdminStats());
        return view('dashboard.admin-simple', compact('stats'));
    }
    
    /**
     * Teacher dashboard
     */
    public function teacherDashboard()
    {
        $user = Auth::user();
        $teacher = \App\Models\Teacher::where('user_id', $user->id)->first();

        // Default values for empty dashboard
        $emptyData = [
            'teacher' => null,
            'subjects' => collect([]),
            'groupSubjects' => collect([]),
            'journalEntries' => collect([]),
            'teacherSubjects' => collect([]),
            'todaySchedule' => collect([]),
            'groups_count' => 0,
            'subjects_count' => 0,
            'journals_count' => 0,
            'total_students' => 0,
            'workload' => 0,
            'attendance_rate' => 0,
            'recentGrades' => collect([]),
            'pendingAssignments' => collect([]),
            'upcomingDeadlines' => collect([]),
            'teacherGroups' => collect([]),
            'subjectsByGroup' => collect([]),
            'lmsCourses' => collect([]),
            'lmsCourseStats' => ['total' => 0, 'published' => 0, 'draft' => 0, 'total_enrollments' => 0],
        ];

        if (!$teacher) {
            return view('dashboard.teacher-new', $emptyData);
        }

        // Get Employee record linked to this user
        $employee = \App\Models\Employee::where('user_id', $user->id)->first();
        $employeeId = $employee ? $employee->id : null;

        // ============ TEACHER'S GROUPS AND SUBJECTS ============

        // 1. From GroupSubject (teacher_id may link to User ID or Employee ID)
        $groupSubjects = \App\Models\GroupSubject::with(['subject', 'group.students', 'group.specialty'])
            ->where(function($q) use ($user, $employeeId) {
                $q->where('teacher_id', $user->id);
                if ($employeeId) {
                    $q->orWhere('teacher_id', $employeeId);
                }
            })
            ->where('is_active', true)
            ->get();

        // 2. From JournalEntry (teacher_id links to Teacher)
        $journalEntries = \App\Models\JournalEntry::with(['subject', 'group.students', 'group.specialty'])
            ->where('teacher_id', $teacher->id)
            ->get();

        // 3. From TeacherSubject (teacher_id may link to Teacher ID or Employee ID)
        $teacherSubjects = \App\Models\TeacherSubject::with(['subject'])
            ->where(function($q) use ($teacher, $employeeId) {
                $q->where('teacher_id', $teacher->id);
                if ($employeeId) {
                    $q->orWhere('teacher_id', $employeeId);
                }
            })
            ->where('status', 'active')
            ->get();

        // Combine unique groups from all sources
        $allGroupIds = $groupSubjects->pluck('group_id')
            ->merge($journalEntries->pluck('group_id'))
            ->unique()
            ->filter();

        // Combine unique subjects from all sources
        $allSubjectIds = $groupSubjects->pluck('subject_id')
            ->merge($journalEntries->pluck('subject_id'))
            ->merge($teacherSubjects->pluck('subject_id'))
            ->unique()
            ->filter();

        // Get all teacher's groups with student counts
        $teacherGroups = \App\Models\Group::with(['specialty', 'students' => function($q) {
                $q->where('status', 'active');
            }])
            ->whereIn('id', $allGroupIds)
            ->get()
            ->map(function($group) {
                return [
                    'id' => $group->id,
                    'name' => $group->name,
                    'specialty' => $group->specialty->name ?? 'N/A',
                    'course' => $group->course ?? '-',
                    'student_count' => $group->students->count(),
                ];
            });

        // Get today's schedule
        $todaySchedule = $this->getTeacherTodaySchedule($teacher->id);

        // ============ STATISTICS ============
        $groups_count = $allGroupIds->count();
        $subjects_count = $allSubjectIds->count();
        $journals_count = $journalEntries->count();

        // Total unique students — single query instead of per-group loop
        $total_students = \App\Models\Student::whereIn('group_id', $allGroupIds->toArray())
            ->where('status', 'active')
            ->count();

        // Calculate workload from GroupSubject and TeacherSubject
        $workload = $groupSubjects->sum(function($gs) {
            return ($gs->lecture_hours ?? 0) + ($gs->practice_hours ?? 0) + ($gs->lab_hours ?? 0);
        });

        // Add workload from TeacherSubject (already fetched above with employee_id check)
        $workload += $teacherSubjects->sum(function($ts) {
            return ($ts->lecture_hours ?? 0) + ($ts->practice_hours ?? 0) + ($ts->lab_hours ?? 0);
        });

        // ============ RECENT GRADES ============
        $recentGrades = \App\Models\JournalGrade::with(['journalEntry.subject', 'student.user', 'journalEntry.group'])
            ->whereHas('journalEntry', function($q) use ($teacher) {
                $q->where('teacher_id', $teacher->id);
            })
            ->latest('graded_date')
            ->take(10)
            ->get();

        // ============ PENDING ASSIGNMENTS ============
        $pendingAssignments = \App\Models\AssignmentSubmission::with(['assignment.subject', 'student.user'])
            ->whereHas('assignment', function($q) use ($teacher) {
                $q->where('teacher_id', $teacher->id);
            })
            ->whereNull('score')
            ->where('status', 'submitted')
            ->latest('submitted_at')
            ->take(10)
            ->get();

        // ============ UPCOMING ASSIGNMENT DEADLINES ============
        $upcomingDeadlines = \App\Models\Assignment::with(['subject'])
            ->where('teacher_id', $teacher->id)
            ->where('deadline', '>', now())
            ->where('deadline', '<', now()->addDays(14))
            ->orderBy('deadline')
            ->take(5)
            ->get()
            ->map(function($assignment) {
                $deadline = Carbon::parse($assignment->deadline);
                $daysLeft = now()->diffInDays($deadline, false);
                return [
                    'id' => $assignment->id,
                    'title' => $assignment->title,
                    'subject' => $assignment->subject->name ?? $assignment->subject->name_uz ?? '',
                    'deadline' => $deadline->format('d.m.Y H:i'),
                    'days_left' => $daysLeft,
                    'urgency' => $daysLeft <= 2 ? 'danger' : ($daysLeft <= 5 ? 'warning' : 'info'),
                ];
            });

        // ============ ATTENDANCE RATE ============
        $attendance_rate = $this->calculateTeacherAttendanceRate($teacher->id);

        // ============ LMS COURSES ============
        $lmsCourses = \App\Models\LmsCourse::with(['subject', 'enrollments'])
            ->where('teacher_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();

        $lmsStatsRow = \App\Models\LmsCourse::where('teacher_id', $user->id)
            ->selectRaw('COUNT(*) as total, SUM(is_published=1) as published, SUM(is_published=0) as draft, COALESCE(SUM(enrollment_count),0) as total_enrollments')
            ->first();
        $lmsCourseStats = [
            'total'             => (int) ($lmsStatsRow->total ?? 0),
            'published'         => (int) ($lmsStatsRow->published ?? 0),
            'draft'             => (int) ($lmsStatsRow->draft ?? 0),
            'total_enrollments' => (int) ($lmsStatsRow->total_enrollments ?? 0),
        ];

        // ============ SUBJECTS BY GROUP (for detailed view) ============
        $subjectsByGroup = collect([]);
        foreach ($teacherGroups as $group) {
            $groupId = $group['id'];

            // Get subjects for this group from both sources
            $subjectsForGroup = collect([]);

            // From GroupSubject
            $gsSubjects = $groupSubjects->where('group_id', $groupId);
            foreach ($gsSubjects as $gs) {
                if ($gs->subject && !$subjectsForGroup->contains('id', $gs->subject_id)) {
                    $subjectsForGroup->push([
                        'id' => $gs->subject_id,
                        'name' => $gs->subject->name ?? $gs->subject->name_uz ?? '',
                        'has_journal' => false,
                    ]);
                }
            }

            // From JournalEntry
            $jeSubjects = $journalEntries->where('group_id', $groupId);
            foreach ($jeSubjects as $je) {
                $existing = $subjectsForGroup->firstWhere('id', $je->subject_id);
                if ($existing) {
                    // Update has_journal flag
                    $subjectsForGroup = $subjectsForGroup->map(function($s) use ($je) {
                        if ($s['id'] == $je->subject_id) {
                            $s['has_journal'] = true;
                        }
                        return $s;
                    });
                } elseif ($je->subject) {
                    $subjectsForGroup->push([
                        'id' => $je->subject_id,
                        'name' => $je->subject->name ?? $je->subject->name_uz ?? '',
                        'has_journal' => true,
                    ]);
                }
            }

            if ($subjectsForGroup->isNotEmpty()) {
                $subjectsByGroup->push([
                    'group' => $group,
                    'subjects' => $subjectsForGroup,
                ]);
            }
        }

        return view('dashboard.teacher-new', [
            'teacher' => $teacher,
            'subjects' => $groupSubjects,
            'groupSubjects' => $groupSubjects,
            'journalEntries' => $journalEntries,
            'teacherSubjects' => $teacherSubjects,
            'todaySchedule' => $todaySchedule,
            'groups_count' => $groups_count,
            'subjects_count' => $subjects_count,
            'journals_count' => $journals_count,
            'total_students' => $total_students,
            'workload' => $workload,
            'attendance_rate' => $attendance_rate,
            'recentGrades' => $recentGrades,
            'pendingAssignments' => $pendingAssignments,
            'upcomingDeadlines' => $upcomingDeadlines,
            'teacherGroups' => $teacherGroups,
            'subjectsByGroup' => $subjectsByGroup,
            'lmsCourses' => $lmsCourses,
            'lmsCourseStats' => $lmsCourseStats,
        ]);
    }

    /**
     * Get teacher's schedule for today
     */
    private function getTeacherTodaySchedule($teacherId)
    {
        $today = Carbon::now()->dayOfWeek; // 0 = Sunday, 1 = Monday, etc.
        $todayName = Carbon::now()->format('l'); // Monday, Tuesday, etc.

        // Get teacher's subjects
        $groupSubjects = \App\Models\GroupSubject::with(['subject', 'group'])
            ->where('teacher_id', $teacherId)
            ->where('is_active', true)
            ->get();

        $scheduleItems = collect([]);

        foreach ($groupSubjects as $groupSubject) {
            // If schedule field has JSON data
            if ($groupSubject->schedule) {
                $scheduleData = is_string($groupSubject->schedule)
                    ? json_decode($groupSubject->schedule, true)
                    : $groupSubject->schedule;

                if (is_array($scheduleData)) {
                    foreach ($scheduleData as $scheduleItem) {
                        // Check if this schedule item is for today
                        if (isset($scheduleItem['day']) &&
                            (strtolower($scheduleItem['day']) == strtolower($todayName) ||
                             $scheduleItem['day'] == $today)) {

                            $scheduleItems->push((object)[
                                'subject' => $groupSubject->subject,
                                'group' => $groupSubject->group,
                                'room' => (object)['name' => $groupSubject->room ?? $scheduleItem['room'] ?? 'N/A'],
                                'start_time' => $scheduleItem['start_time'] ?? '09:00',
                                'end_time' => $scheduleItem['end_time'] ?? '10:30',
                            ]);
                        }
                    }
                }
            }
        }

        // Sort by start time
        return $scheduleItems->sortBy('start_time');
    }

    /**
     * Calculate average attendance rate for teacher's groups
     */
    private function calculateTeacherAttendanceRate($teacherId)
    {
        // Get total classes in last 30 days
        $totalClasses = \App\Models\JournalEntry::where('teacher_id', $teacherId)
            ->where('created_at', '>=', Carbon::now()->subMonth())
            ->count();

        if ($totalClasses == 0) {
            return 0;
        }

        // Get total attendance records
        $totalAttendance = \App\Models\JournalGrade::whereHas('journalEntry', function($q) use ($teacherId) {
            $q->where('teacher_id', $teacherId)
              ->where('created_at', '>=', Carbon::now()->subMonth());
        })->count();

        // Get expected attendance — preload counts to avoid N+1
        $entries = \App\Models\JournalEntry::where('teacher_id', $teacherId)
            ->where('created_at', '>=', Carbon::now()->subMonth())
            ->get();

        $entryGroupIds = $entries->pluck('group_id')->filter()->unique()->toArray();
        $groupStudentCounts = \App\Models\Student::whereIn('group_id', $entryGroupIds)
            ->where('status', 'active')
            ->selectRaw('group_id, COUNT(*) as cnt')
            ->groupBy('group_id')
            ->pluck('cnt', 'group_id');

        $totalExpected = $entries->sum(fn($entry) => $groupStudentCounts[$entry->group_id] ?? 0);

        return $totalExpected > 0 ? round(($totalAttendance / $totalExpected) * 100, 1) : 0;
    }
    
    /**
     * Student dashboard
     */
    public function studentDashboard()
    {
        $userId = Auth::id();
        $user = Auth::user();

        // Get student record
        $student = \App\Models\Student::where('user_id', $userId)->first();
        $group = null;
        $groupSubjects = collect([]);
        $studentGrades = collect([]);
        $todaySchedule = collect([]);
        $upcomingAssignments = [];

        if ($student && $student->group_id) {
            $group = \App\Models\Group::with(['department', 'specialty'])->find($student->group_id);

            // Get subjects and teachers from JournalEntry and TeacherSubject
            $groupSubjects = $this->getGroupSubjectsWithTeachers($student->group_id);

            // Get student grades
            $studentGrades = $this->getStudentGrades($student->id);

            // Get today's schedule
            $todaySchedule = $this->getStudentTodaySchedule($student->group_id);

            // Get upcoming assignments
            $upcomingAssignments = $this->getUpcomingAssignments($student);
        }

        // Calculate statistics
        $attendanceStats = $this->calculateStudentAttendance($student);
        $gradeStats = $this->calculateGradeStatistics($studentGrades);

        // Calculate student rank in group
        $studentRank = $this->calculateStudentRank($student, $group);

        return view('dashboard.student-new', [
            'student' => $student,
            'group' => $group,
            'groupSubjects' => $groupSubjects,
            'studentGrades' => $studentGrades,
            'todaySchedule' => $todaySchedule,
            'upcomingAssignments' => $upcomingAssignments,
            'gpa' => $gradeStats['gpa'],
            'total_courses' => $groupSubjects->count(),
            'total_credits' => $gradeStats['total_credits'],
            'student_rank' => $studentRank,
            'attendance_percentage' => $attendanceStats['percentage'],
            'attendance_present' => $attendanceStats['present'],
            'attendance_absent' => $attendanceStats['absent'],
            'attendance_total' => $attendanceStats['total'],
        ]);
    }

    /**
     * Get subjects with teachers for a group
     */
    private function getGroupSubjectsWithTeachers($groupId)
    {
        $groupSubjects = collect([]);
        $seenSubjects = [];

        // Get from JournalEntry
        $journalEntries = \App\Models\JournalEntry::where('group_id', $groupId)
            ->with(['subject', 'teacher'])
            ->get();

        foreach ($journalEntries as $entry) {
            if ($entry->subject_id && !in_array($entry->subject_id, $seenSubjects)) {
                $seenSubjects[] = $entry->subject_id;

                // Try to get teacher from Employee or Teacher model
                $teacher = $entry->teacher;
                $teacherName = 'Belgilanmagan';
                if ($teacher) {
                    $teacherName = $teacher->full_name ?? ($teacher->first_name . ' ' . $teacher->last_name);
                }

                $groupSubjects->push([
                    'id' => $entry->id,
                    'subject' => $entry->subject,
                    'teacher' => $teacher,
                    'teacher_name' => $teacherName,
                    'hours' => null
                ]);
            }
        }

        // Get from TeacherSubject
        $teacherSubjects = \App\Models\TeacherSubject::where('status', 'active')
            ->where(function($query) use ($groupId) {
                $query->whereJsonContains('group_ids', $groupId)
                      ->orWhereJsonContains('group_ids', (string)$groupId);
            })
            ->with(['teacher', 'subject'])
            ->get();

        foreach ($teacherSubjects as $ts) {
            if ($ts->subject_id && !in_array($ts->subject_id, $seenSubjects)) {
                $seenSubjects[] = $ts->subject_id;
                $groupSubjects->push([
                    'id' => $ts->id,
                    'subject' => $ts->subject,
                    'teacher' => $ts->teacher,
                    'teacher_name' => $ts->teacher ? $ts->teacher->full_name : 'Belgilanmagan',
                    'hours' => [
                        'lecture' => $ts->lecture_hours ?? 0,
                        'practice' => $ts->practice_hours ?? 0,
                        'lab' => $ts->lab_hours ?? 0,
                        'total' => ($ts->lecture_hours ?? 0) + ($ts->practice_hours ?? 0) + ($ts->lab_hours ?? 0)
                    ]
                ]);
            }
        }

        return $groupSubjects;
    }

    /**
     * Get student grades from JournalGrade
     */
    private function getStudentGrades($studentId)
    {
        return \App\Models\JournalGrade::with(['journalEntry.subject', 'journalEntry.teacher'])
            ->where('student_id', $studentId)
            ->orderBy('graded_date', 'desc')
            ->get()
            ->groupBy(function($grade) {
                return $grade->journalEntry->subject_id ?? 0;
            })
            ->map(function($grades, $subjectId) {
                $first = $grades->first();
                $subject = $first->journalEntry->subject ?? null;
                $teacher = $first->journalEntry->teacher ?? null;

                return [
                    'subject' => $subject,
                    'teacher' => $teacher,
                    'teacher_name' => $teacher ? ($teacher->full_name ?? '') : '',
                    'grades' => $grades,
                    'current_score' => $grades->where('grade_type', 'current')->sum('score'),
                    'midterm_score' => $grades->where('grade_type', 'midterm')->first()->score ?? 0,
                    'final_score' => $grades->where('grade_type', 'final')->first()->score ?? 0,
                    'total_score' => $grades->sum('score'),
                    'average' => round($grades->avg('score'), 1)
                ];
            });
    }

    /**
     * Get today's schedule for student's group
     */
    private function getStudentTodaySchedule($groupId)
    {
        $today = Carbon::now();

        // Convert Carbon dayOfWeek to day name (schedule_slots uses enum: monday, tuesday, etc.)
        $dayNames = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
        $dayOfWeek = $dayNames[$today->dayOfWeek];

        $lessonTypes = [
            'lecture' => "Ma'ruza",
            'practice' => 'Amaliy',
            'seminar' => 'Seminar',
            'lab' => 'Laboratoriya'
        ];

        // Time slots mapping
        $timeSlots = [
            '1' => ['start' => '08:30', 'end' => '09:50'],
            '2' => ['start' => '10:00', 'end' => '11:20'],
            '3' => ['start' => '11:30', 'end' => '12:50'],
            '4' => ['start' => '13:30', 'end' => '14:50'],
            '5' => ['start' => '15:00', 'end' => '16:20'],
            '6' => ['start' => '16:30', 'end' => '17:50'],
        ];

        try {
            // Try ScheduleSlot through Schedule model
            if (class_exists('\App\Models\ScheduleSlot')) {
                $activeSchedule = \App\Models\Schedule::where('group_id', $groupId)
                    ->where('status', 'active')
                    ->first();

                if ($activeSchedule) {
                    $slots = \App\Models\ScheduleSlot::with(['subject', 'teacher', 'room'])
                        ->where('schedule_id', $activeSchedule->id)
                        ->where('day_of_week', $dayOfWeek)
                        ->orderBy('time_slot')
                        ->get();

                    if ($slots->count() > 0) {
                        return $slots->map(function($slot) use ($lessonTypes, $timeSlots) {
                            $timeInfo = $timeSlots[$slot->time_slot] ?? $timeSlots['1'];

                            return [
                                'time' => $timeInfo['start'] . ' - ' . $timeInfo['end'],
                                'pair' => $slot->time_slot ?? '-',
                                'subject' => $slot->subject->name_uz ?? $slot->subject->name ?? 'Noma\'lum',
                                'teacher' => $slot->teacher->full_name ?? $slot->teacher->name ?? '',
                                'room' => $slot->room->name ?? 'N/A',
                                'type' => $lessonTypes[$slot->lesson_type] ?? $slot->lesson_type ?? "Ma'ruza",
                                'type_color' => $this->getLessonTypeColor($slot->lesson_type ?? '')
                            ];
                        });
                    }
                }
            }
        } catch (\Exception $e) {
            // Fall through to empty
        }

        return collect([]);
    }

    /**
     * Get lesson type badge color
     */
    private function getLessonTypeColor($type)
    {
        return match(strtolower($type)) {
            'ma\'ruza', 'lecture' => 'info',
            'amaliy', 'seminar', 'practice' => 'success',
            'laboratoriya', 'lab' => 'warning',
            default => 'secondary'
        };
    }

    /**
     * Calculate student attendance statistics
     */
    private function calculateStudentAttendance($student)
    {
        if (!$student) {
            return ['percentage' => 0, 'present' => 0, 'absent' => 0, 'total' => 0];
        }

        $thirtyDaysAgo = Carbon::now()->subDays(30);

        try {
            // Try AttendanceRecord model with lesson_date column
            if (class_exists('\App\Models\AttendanceRecord')) {
                $records = \App\Models\AttendanceRecord::where('student_id', $student->id)
                    ->where('lesson_date', '>=', $thirtyDaysAgo)
                    ->get();

                $total = $records->count();
                $present = $records->whereIn('status', ['present', 'late'])->count();
                $absent = $records->where('status', 'absent')->count();

                return [
                    'percentage' => $total > 0 ? round(($present / $total) * 100) : 0,
                    'present' => $present,
                    'absent' => $absent,
                    'total' => $total
                ];
            }
        } catch (\Exception $e) {
            // Fall through to default
        }

        try {
            // Try Attendance model with user_id
            $records = Attendance::where('user_id', $student->user_id)
                ->where('date', '>=', $thirtyDaysAgo)
                ->get();

            $total = $records->count();
            $present = $records->whereIn('status', ['present', 'late'])->count();
            $absent = $records->where('status', 'absent')->count();

            return [
                'percentage' => $total > 0 ? round(($present / $total) * 100) : 0,
                'present' => $present,
                'absent' => $absent,
                'total' => $total
            ];
        } catch (\Exception $e) {
            return ['percentage' => 0, 'present' => 0, 'absent' => 0, 'total' => 0];
        }
    }

    /**
     * Calculate grade statistics
     */
    private function calculateGradeStatistics($studentGrades)
    {
        if ($studentGrades->isEmpty()) {
            return ['gpa' => 0, 'total_credits' => 0];
        }

        $totalScore = 0;
        $count = 0;

        foreach ($studentGrades as $gradeGroup) {
            if (isset($gradeGroup['total_score'])) {
                $totalScore += $gradeGroup['total_score'];
                $count++;
            }
        }

        $gpa = $count > 0 ? round($totalScore / $count, 1) : 0;

        return [
            'gpa' => min($gpa, 100), // Cap at 100
            'total_credits' => $studentGrades->count() * 5 // Approximate credits
        ];
    }

    /**
     * Calculate student rank in group
     */
    private function calculateStudentRank($student, $group)
    {
        if (!$student || !$group) {
            return '-';
        }

        // Get all students in group with their grades
        $groupStudents = \App\Models\Student::where('group_id', $group->id)
            ->where('status', 'active')
            ->get();

        $studentScores = [];
        foreach ($groupStudents as $gs) {
            $grades = \App\Models\JournalGrade::where('student_id', $gs->id)->get();
            $avgScore = $grades->count() > 0 ? $grades->avg('score') : 0;
            $studentScores[$gs->id] = $avgScore;
        }

        // Sort by score descending
        arsort($studentScores);

        // Find current student's rank
        $rank = 1;
        foreach ($studentScores as $id => $score) {
            if ($id == $student->id) {
                return $rank . '/' . count($studentScores);
            }
            $rank++;
        }

        return '-';
    }

    /**
     * Get upcoming assignments for student
     */
    private function getUpcomingAssignments($student = null)
    {
        if (!$student) {
            $student = \App\Models\Student::where('user_id', Auth::id())->first();
        }

        if (!$student || !$student->group_id) {
            return [];
        }

        // Get submitted assignment IDs
        $submittedIds = [];
        if (method_exists($student, 'assignmentSubmissions')) {
            $submittedIds = $student->assignmentSubmissions()->pluck('assignment_id')->toArray();
        }

        $assignments = \App\Models\Assignment::with(['subject', 'teacher.user'])
            ->where(function($query) use ($student) {
                $query->whereJsonContains('group_ids', $student->group_id)
                      ->orWhereJsonContains('group_ids', (string)$student->group_id);
            })
            ->whereNotIn('id', $submittedIds)
            ->where('deadline', '>', now())
            ->orderBy('deadline', 'asc')
            ->limit(5)
            ->get();

        return $assignments->map(function($assignment) {
            $deadline = Carbon::parse($assignment->deadline);
            $daysUntil = now()->diffInDays($deadline, false);

            if ($daysUntil <= 2) {
                $urgency = 'urgent';
                $badgeColor = 'danger';
                $deadlineText = $daysUntil == 0 ? 'Bugun' : ($daysUntil == 1 ? '1 kun' : '2 kun');
            } elseif ($daysUntil <= 5) {
                $urgency = 'soon';
                $badgeColor = 'warning';
                $deadlineText = $daysUntil . ' kun';
            } else {
                $urgency = 'normal';
                $badgeColor = 'info';
                $deadlineText = $daysUntil <= 14 ? $daysUntil . ' kun' : floor($daysUntil / 7) . ' hafta';
            }

            return [
                'id' => $assignment->id,
                'title' => $assignment->title,
                'teacher' => $assignment->teacher->user->name ?? 'Noma\'lum',
                'subject' => $assignment->subject->name ?? $assignment->subject->name_uz ?? '',
                'deadline' => $deadline->format('d.m.Y H:i'),
                'deadline_text' => $deadlineText,
                'urgency' => $urgency,
                'badge_color' => $badgeColor,
            ];
        })->toArray();
    }
    
    /**
     * PR dashboard
     */
    public function prDashboard()
    {
        return view('dashboard.pr', [
            'news' => [],
            'announcements' => []
        ]);
    }
    
    /**
     * Marketing dashboard
     */
    public function marketingDashboard()
    {
        return view('dashboard.marketing', [
            'applications' => [],
            'statistics' => []
        ]);
    }
    
    /**
     * HR dashboard
     */
    public function hrDashboard()
    {
        return view('dashboard.hr', [
            'attendance' => [],
            'employees' => []
        ]);
    }
    
    /**
     * Chat Admin dashboard
     */
    public function chatAdminDashboard()
    {
        return view('dashboard.chat-admin', [
            'messages' => [],
            'botStats' => []
        ]);
    }
    
    /**
     * Get admin statistics
     */
    private function getAdminStats()
    {
        // User statistikalari
        $total_users = \App\Models\User::count();

        // Talabalar soni
        $total_students = 0;
        if (class_exists('\App\Models\Student')) {
            $total_students = \App\Models\Student::count();
        } elseif (class_exists('\App\Models\User')) {
            $total_students = \App\Models\User::whereHas('roles', function($q) {
                $q->where('name', 'Student');
            })->count();
        }

        // O'qituvchilar soni
        $total_teachers = 0;
        if (class_exists('\App\Models\Teacher')) {
            $total_teachers = \App\Models\Teacher::count();
        } elseif (class_exists('\App\Models\Employee')) {
            // Employee modelida employee_type ustuni yo'q, shunchaki barcha xodimlarni sanash
            $total_teachers = \App\Models\Employee::count();
        } else {
            $total_teachers = \App\Models\User::whereHas('roles', function($q) {
                $q->where('name', 'Teacher');
            })->count();
        }

        // Fakultetlar soni
        $total_faculties = 0;
        if (class_exists('\App\Models\Faculty')) {
            $total_faculties = \App\Models\Faculty::count();
        }

        // Fanlar soni
        $total_subjects = 0;
        if (class_exists('\App\Models\Subject')) {
            $total_subjects = \App\Models\Subject::count();
        }

        // Guruhlar soni
        $total_groups = 0;
        if (class_exists('\App\Models\AcademicGroup')) {
            $total_groups = \App\Models\AcademicGroup::count();
        } elseif (class_exists('\App\Models\StudentGroup')) {
            $total_groups = \App\Models\StudentGroup::count();
        }

        // Yo'nalishlar/Mutaxassisliklar soni
        $total_specialties = 0;
        if (class_exists('\App\Models\Specialty')) {
            $total_specialties = \App\Models\Specialty::count();
        }

        // Academic system statistics
        $total_grades = 0;
        $students_with_debts = 0;
        $average_gpa = 0;

        if (class_exists('\App\Models\Grade')) {
            $total_grades = \App\Models\Grade::count();

            // Students with academic debts (failing grades)
            $students_with_debts = \App\Models\Grade::where('is_final', true)
                ->where('grade', '<', 55)
                ->distinct('student_id')
                ->count('student_id');

            // Calculate average GPA
            $avgGradePoint = \App\Models\Grade::where('is_final', true)
                ->avg('grade_point');
            $average_gpa = $avgGradePoint ? round($avgGradePoint, 2) : 0;
        }

        return [
            'total_users' => $total_users,
            'total_students' => $total_students,
            'total_teachers' => $total_teachers,
            'total_faculties' => $total_faculties,
            'total_subjects' => $total_subjects,
            'total_groups' => $total_groups,
            'total_specialties' => $total_specialties,
            'recent_registrations' => \App\Models\User::with('roles')->latest()->limit(5)->get(),
            // Academic system stats
            'total_grades' => $total_grades,
            'students_with_debts' => $students_with_debts,
            'average_gpa' => $average_gpa,
        ];
    }

    /**
     * Get today's attendance statistics
     */
    private function getTodayAttendanceStats()
    {
        $today = Carbon::today();

        $present = Attendance::whereDate('date', $today)
            ->whereNotNull('check_in')
            ->count();

        $late = Attendance::whereDate('date', $today)
            ->where('status', 'late')
            ->count();

        $absent = \App\Models\User::whereHas('roles', function($q) {
                $q->whereIn('name', ['Student', 'Teacher']);
            })
            ->whereDoesntHave('attendances', function($q) use ($today) {
                $q->whereDate('date', $today);
            })
            ->count();

        return [
            'present' => $present,
            'late' => $late,
            'absent' => $absent,
            'total' => $present + $absent
        ];
    }

    /**
     * Get recent check-ins
     */
    private function getRecentCheckIns($limit = 5)
    {
        return Attendance::with('user')
            ->whereDate('date', Carbon::today())
            ->whereNotNull('check_in')
            ->orderBy('check_in', 'desc')
            ->limit($limit)
            ->get()
            ->map(function($attendance) {
                return [
                    'user_name' => $attendance->user->name ?? 'Unknown',
                    'check_in_time' => Carbon::parse($attendance->check_in)->format('H:i'),
                    'status' => $attendance->status,
                    'confidence' => $attendance->face_confidence_score
                ];
            });
    }

    /**
     * Get user's today attendance
     */
    private function getUserTodayAttendance($userId)
    {
        $attendance = Attendance::where('user_id', $userId)
            ->whereDate('date', Carbon::today())
            ->first();

        if (!$attendance) {
            return null;
        }

        return [
            'check_in' => $attendance->check_in ? Carbon::parse($attendance->check_in)->format('H:i') : null,
            'check_out' => $attendance->check_out ? Carbon::parse($attendance->check_out)->format('H:i') : null,
            'status' => $attendance->status,
            'working_hours' => $this->calculateWorkingHours($attendance->check_in, $attendance->check_out)
        ];
    }

    /**
     * Get user's attendance history
     */
    private function getUserAttendanceHistory($userId, $days = 7)
    {
        return Attendance::where('user_id', $userId)
            ->whereDate('date', '>=', Carbon::now()->subDays($days))
            ->orderBy('date', 'desc')
            ->get()
            ->map(function($attendance) {
                return [
                    'date' => Carbon::parse($attendance->date)->format('d/m'),
                    'day' => Carbon::parse($attendance->date)->format('D'),
                    'check_in' => $attendance->check_in ? Carbon::parse($attendance->check_in)->format('H:i') : '-',
                    'check_out' => $attendance->check_out ? Carbon::parse($attendance->check_out)->format('H:i') : '-',
                    'status' => $attendance->status
                ];
            });
    }

    /**
     * Calculate working hours
     */
    private function calculateWorkingHours($checkIn, $checkOut)
    {
        if (!$checkIn || !$checkOut) {
            return '0:00';
        }

        $start = Carbon::parse($checkIn);
        $end = Carbon::parse($checkOut);
        $diff = $end->diff($start);

        return sprintf('%d:%02d', $diff->h, $diff->i);
    }

}