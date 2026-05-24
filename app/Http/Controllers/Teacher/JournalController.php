<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\GroupSubject;
use App\Models\JournalEntry;
use App\Models\JournalGrade;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class JournalController extends Controller
{
    /**
     * Display list of teacher's journals (subjects/groups)
     */
    public function index()
    {
        $user = Auth::user();

        // Permission check
        if (!$user->can('view_journal') && !$user->hasAnyRole(['Teacher', 'teacher', 'superadmin'])) {
            abort(403, 'Sizda jurnal ko\'rish huquqi yo\'q');
        }

        // Teacher modelini olish
        $teacher = Teacher::where('user_id', $user->id)->first();

        // Employee modelini olish (yangi tizim)
        $employee = \App\Models\Employee::where('user_id', $user->id)->first();
        $employeeId = $employee ? $employee->id : null;
        $teacherId = $teacher ? $teacher->id : null;

        if (!$teacher && !$employee) {
            return view('teacher.journal.index', [
                'journalsData' => collect(),
                'message' => 'O\'qituvchi profili topilmadi. Administrator bilan bog\'laning.'
            ]);
        }

        // Get teacher's subjects with groups - check both Teacher ID and Employee ID
        $groupSubjects = GroupSubject::with(['subject', 'group.students', 'group.specialty', 'academicYear'])
            ->where(function($q) use ($teacherId, $employeeId) {
                if ($teacherId) {
                    $q->where('teacher_id', $teacherId);
                }
                if ($employeeId) {
                    $q->orWhere('teacher_id', $employeeId);
                }
            })
            ->where('is_active', true)
            ->get();

        // Also get journals from JournalEntry table
        $journalEntries = JournalEntry::with(['subject', 'group', 'academicYear'])
            ->where(function($q) use ($teacherId, $employeeId) {
                if ($teacherId) {
                    $q->where('teacher_id', $teacherId);
                }
                if ($employeeId) {
                    $q->orWhere('teacher_id', $employeeId);
                }
            })
            ->get();

        // Also check TeacherSubject assignments
        $teacherSubjects = \App\Models\TeacherSubject::with(['subject'])
            ->where(function($q) use ($teacherId, $employeeId) {
                if ($teacherId) {
                    $q->where('teacher_id', $teacherId);
                }
                if ($employeeId) {
                    $q->orWhere('teacher_id', $employeeId);
                }
            })
            ->where('status', 'active')
            ->get();

        // Pre-compute student counts for all groups — one query instead of N
        $allJournalGroupIds = $groupSubjects->pluck('group_id')
            ->merge($journalEntries->pluck('group_id'))
            ->merge(collect($teacherSubjects->flatMap(fn($ts) => $ts->group_ids ?? [])))
            ->filter()->unique()->toArray();

        $studentCountsByGroup = Student::whereIn('group_id', $allJournalGroupIds)
            ->where('status', 'active')
            ->selectRaw('group_id, COUNT(*) as cnt')
            ->groupBy('group_id')
            ->pluck('cnt', 'group_id');

        // Combine all journal data
        $journalsData = collect();

        // 1. From GroupSubject
        foreach ($groupSubjects as $gs) {
            if (!$gs->group || !$gs->subject) continue;

            $totalStudents = $studentCountsByGroup[$gs->group_id] ?? 0;

            // Get all journal entries for this group-subject
            $entriesCount = JournalEntry::where(function($q) use ($teacherId, $employeeId) {
                    if ($teacherId) $q->where('teacher_id', $teacherId);
                    if ($employeeId) $q->orWhere('teacher_id', $employeeId);
                })
                ->where('subject_id', $gs->subject_id)
                ->where('group_id', $gs->group_id)
                ->count();

            // Get all grades
            $grades = JournalGrade::whereHas('journalEntry', function($q) use ($teacherId, $employeeId, $gs) {
                $q->where(function($q2) use ($teacherId, $employeeId) {
                    if ($teacherId) $q2->where('teacher_id', $teacherId);
                    if ($employeeId) $q2->orWhere('teacher_id', $employeeId);
                })
                ->where('subject_id', $gs->subject_id)
                ->where('group_id', $gs->group_id);
            })->get();

            $journalsData->push([
                'id' => $gs->id,
                'type' => 'group_subject',
                'subject' => $gs->subject,
                'group' => $gs->group,
                'semester' => $gs->semester,
                'room' => $gs->room,
                'academic_year' => $gs->academicYear,
                'total_students' => $totalStudents,
                'entries_count' => $entriesCount,
                'avg_score' => $grades->whereNotNull('score')->avg('score') ?? 0,
                'total_grades' => $grades->count(),
                'joriy_count' => $grades->where('grade_type', 'joriy')->count(),
                'oraliq_count' => $grades->where('grade_type', 'oraliq')->count(),
                'yakuniy_count' => $grades->where('grade_type', 'yakuniy')->count(),
            ]);
        }

        // 2. From JournalEntry (if not already in GroupSubject)
        foreach ($journalEntries as $je) {
            // Skip if already added from GroupSubject
            $exists = $journalsData->first(function($item) use ($je) {
                return $item['subject']->id == $je->subject_id &&
                       isset($item['group']) && $item['group'] && $item['group']->id == $je->group_id;
            });

            if (!$exists && $je->subject && $je->group) {
                $totalStudents = $studentCountsByGroup[$je->group_id] ?? 0;

                $journalsData->push([
                    'id' => $je->id,
                    'type' => 'journal_entry',
                    'subject' => $je->subject,
                    'group' => $je->group,
                    'semester' => $je->semester_id,
                    'room' => null,
                    'academic_year' => $je->academicYear,
                    'total_students' => $totalStudents,
                    'entries_count' => 1,
                    'avg_score' => 0,
                    'total_grades' => 0,
                    'joriy_count' => 0,
                    'oraliq_count' => 0,
                    'yakuniy_count' => 0,
                    'attendance_rate' => 0,
                ]);
            }
        }

        // 3. From TeacherSubject (expand group_ids)
        foreach ($teacherSubjects as $ts) {
            $groupIds = $ts->group_ids ?? [];
            if (!is_array($groupIds)) continue;

            foreach ($groupIds as $groupId) {
                // Skip if already added
                $exists = $journalsData->first(function($item) use ($ts, $groupId) {
                    return $item['subject']->id == $ts->subject_id &&
                           isset($item['group']) && $item['group'] && $item['group']->id == $groupId;
                });

                if (!$exists && $ts->subject) {
                    $group = \App\Models\Group::find($groupId);
                    if (!$group) {
                        $group = \App\Models\StudentGroup::find($groupId);
                    }

                    if ($group) {
                        $totalStudents = $studentCountsByGroup[$groupId] ?? 0;

                        $journalsData->push([
                            'id' => $ts->id . '_' . $groupId,
                            'type' => 'teacher_subject',
                            'subject' => $ts->subject,
                            'group' => $group,
                            'semester' => $ts->semester_id,
                            'room' => null,
                            'academic_year' => $ts->academicYear ?? null,
                            'total_students' => $totalStudents,
                            'entries_count' => 0,
                            'avg_score' => 0,
                            'total_grades' => 0,
                            'joriy_count' => 0,
                            'oraliq_count' => 0,
                            'yakuniy_count' => 0,
                            'attendance_rate' => 0,
                        ]);
                    }
                }
            }
        }

        return view('teacher.journal.index', [
            'journalsData' => $journalsData,
            'teacherSubjects' => $teacherSubjects,
            'journalEntries' => $journalEntries,
            'teacher' => $teacher,
            'employee' => $employee,
        ]);
    }

    /**
     * Show detailed journal for specific group-subject or journal entry
     */
    public function show($id)
    {
        $user = Auth::user();
        $teacher = Teacher::where('user_id', $user->id)->first();
        $employee = \App\Models\Employee::where('user_id', $user->id)->first();

        $teacherId = $teacher ? $teacher->id : null;
        $employeeId = $employee ? $employee->id : null;

        if (!$teacher && !$employee) {
            abort(403, 'O\'qituvchi profili topilmadi');
        }

        // First try to find as GroupSubject
        $groupSubject = GroupSubject::with(['subject', 'group.students.user', 'group.specialty', 'academicYear'])
            ->where('id', $id)
            ->where(function($q) use ($teacherId, $employeeId) {
                if ($teacherId) $q->where('teacher_id', $teacherId);
                if ($employeeId) $q->orWhere('teacher_id', $employeeId);
            })
            ->first();

        // If not found as GroupSubject, try to find as JournalEntry
        if (!$groupSubject) {
            $journalEntry = JournalEntry::with(['subject', 'group', 'academicYear'])
                ->where('id', $id)
                ->where(function($q) use ($teacherId, $employeeId) {
                    if ($teacherId) $q->where('teacher_id', $teacherId);
                    if ($employeeId) $q->orWhere('teacher_id', $employeeId);
                })
                ->first();

            if (!$journalEntry) {
                abort(404, 'Jurnal topilmadi');
            }

            // Create a fake GroupSubject-like object from JournalEntry
            $groupSubject = (object)[
                'id' => $journalEntry->id,
                'subject_id' => $journalEntry->subject_id,
                'group_id' => $journalEntry->group_id,
                'subject' => $journalEntry->subject,
                'group' => $journalEntry->group,
                'academicYear' => $journalEntry->academicYear,
                'semester' => $journalEntry->semester_id,
                'room' => null,
            ];
        }

        // Get all students in the group
        $students = \App\Models\Student::where('group_id', $groupSubject->group_id ?? ($groupSubject->group ? $groupSubject->group->id : null))
            ->where('status', 'active')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        // Get all journal entries for this group-subject
        $journalEntries = JournalEntry::with(['grades'])
            ->where(function($q) use ($teacherId, $employeeId) {
                if ($teacherId) $q->where('teacher_id', $teacherId);
                if ($employeeId) $q->orWhere('teacher_id', $employeeId);
            })
            ->where('subject_id', $groupSubject->subject_id ?? ($groupSubject->subject ? $groupSubject->subject->id : null))
            ->where('group_id', $groupSubject->group_id ?? ($groupSubject->group ? $groupSubject->group->id : null))
            ->orderBy('created_at', 'asc')
            ->get();

        // Build journal matrix (students x entries)
        $journalMatrix = [];
        foreach ($students as $student) {
            $studentRow = [
                'student' => $student,
                'entries' => [],
            ];

            foreach ($journalEntries as $entry) {
                $grade = $entry->grades->where('student_id', $student->id)->first();
                $studentRow['entries'][$entry->id] = $grade;
            }

            // Calculate totals for this student
            $studentGrades = JournalGrade::whereHas('journalEntry', function($q) use ($teacherId, $employeeId, $groupSubject) {
                $q->where(function($q2) use ($teacherId, $employeeId) {
                    if ($teacherId) $q2->where('teacher_id', $teacherId);
                    if ($employeeId) $q2->orWhere('teacher_id', $employeeId);
                })
                ->where('subject_id', $groupSubject->subject_id)
                ->where('group_id', $groupSubject->group_id);
            })->where('student_id', $student->id)->get();

            $joriyGrades = $studentGrades->where('grade_type', 'joriy');
            $oraliqGrades = $studentGrades->where('grade_type', 'oraliq');
            $yakuniyGrades = $studentGrades->where('grade_type', 'yakuniy');

            $studentRow['joriy_avg'] = $joriyGrades->whereNotNull('score')->avg('score');
            $studentRow['oraliq_avg'] = $oraliqGrades->whereNotNull('score')->avg('score');
            $studentRow['yakuniy_avg'] = $yakuniyGrades->whereNotNull('score')->avg('score');
            $studentRow['total_avg'] = $studentGrades->whereNotNull('score')->avg('score');
            $studentRow['attendance_count'] = $studentGrades->count();
            $studentRow['attendance_rate'] = $journalEntries->count() > 0
                ? round(($studentGrades->count() / $journalEntries->count()) * 100, 1)
                : 0;

            $journalMatrix[] = $studentRow;
        }

        // Overall statistics
        $overallStats = [
            'total_students' => $students->count(),
            'total_entries' => $journalEntries->count(),
            'avg_score' => collect($journalMatrix)->avg('total_avg'),
            'avg_attendance' => collect($journalMatrix)->avg('attendance_rate'),
        ];

        // Get schedule for this subject-group using ScheduleSlot
        $schedules = collect();
        $subjectId = $groupSubject->subject_id ?? ($groupSubject->subject ? $groupSubject->subject->id : null);
        $groupId = $groupSubject->group_id ?? ($groupSubject->group ? $groupSubject->group->id : null);

        if ($subjectId && $groupId) {
            // First find the active schedule for this group
            $schedule = \App\Models\Schedule::where('group_id', $groupId)
                ->where('status', 'active')
                ->first();

            if ($schedule) {
                // Get slots for this subject and teacher
                $schedules = \App\Models\ScheduleSlot::with(['room', 'subject'])
                    ->where('schedule_id', $schedule->id)
                    ->where('subject_id', $subjectId)
                    ->where(function($q) use ($teacherId, $employeeId) {
                        if ($teacherId) $q->where('teacher_id', $teacherId);
                        if ($employeeId) $q->orWhere('teacher_id', $employeeId);
                    })
                    ->orderBy('day_of_week')
                    ->orderBy('time_slot')
                    ->get();
            }
        }

        // Format schedule with lesson times
        $lessonTimes = [
            1 => '08:30 - 09:50',
            2 => '10:10 - 11:30',
            3 => '12:00 - 13:20',
            4 => '14:00 - 15:20',
            5 => '15:40 - 17:00',
            6 => '17:20 - 18:40',
        ];

        $dayNames = [
            1 => 'Dushanba',
            2 => 'Seshanba',
            3 => 'Chorshanba',
            4 => 'Payshanba',
            5 => 'Juma',
            6 => 'Shanba',
            7 => 'Yakshanba',
        ];

        $formattedSchedule = $schedules->map(function($s) use ($lessonTimes, $dayNames) {
            return [
                'day' => $dayNames[$s->day_of_week] ?? 'N/A',
                'day_of_week' => $s->day_of_week,
                'time' => $lessonTimes[$s->time_slot] ?? '---',
                'lesson_number' => $s->time_slot,
                'room' => $s->room->name ?? 'N/A',
                'type' => $s->lesson_type ?? 'lecture',
            ];
        });

        return view('teacher.journal.show', [
            'groupSubject' => $groupSubject,
            'students' => $students,
            'journalEntries' => $journalEntries,
            'journalMatrix' => $journalMatrix,
            'overallStats' => $overallStats,
            'teacher' => $teacher,
            'schedules' => $formattedSchedule,
        ]);
    }

    /**
     * Export journal as PDF or Excel
     */
    public function export($groupSubjectId, $format = 'pdf')
    {
        $user = Auth::user();
        $teacher = Teacher::where('user_id', $user->id)->first();
        $employee = \App\Models\Employee::where('user_id', $user->id)->first();

        $teacherId = $teacher ? $teacher->id : null;
        $employeeId = $employee ? $employee->id : null;

        if (!$teacher && !$employee) {
            abort(403, 'O\'qituvchi profili topilmadi');
        }

        $groupSubject = GroupSubject::with(['subject', 'group', 'academicYear'])
            ->where('id', $groupSubjectId)
            ->where(function($q) use ($teacherId, $employeeId) {
                if ($teacherId) $q->where('teacher_id', $teacherId);
                if ($employeeId) $q->orWhere('teacher_id', $employeeId);
            })
            ->firstOrFail();

        // Get data (similar to show method)
        $students = $groupSubject->group->students()
            ->where('status', 'active')
            ->with(['user'])
            ->orderBy('student_id')
            ->get();

        $journalEntries = JournalEntry::with(['grades'])
            ->where(function($q) use ($teacherId, $employeeId) {
                if ($teacherId) $q->where('teacher_id', $teacherId);
                if ($employeeId) $q->orWhere('teacher_id', $employeeId);
            })
            ->where('subject_id', $groupSubject->subject_id)
            ->where('group_id', $groupSubject->group_id)
            ->orderBy('created_at', 'asc')
            ->get();

        // Build journal matrix
        $journalMatrix = [];
        foreach ($students as $student) {
            $studentRow = [
                'student' => $student,
                'entries' => [],
            ];

            foreach ($journalEntries as $entry) {
                $grade = $entry->grades->where('student_id', $student->id)->first();
                $studentRow['entries'][$entry->id] = $grade;
            }

            $studentGrades = JournalGrade::whereHas('journalEntry', function($q) use ($teacherId, $employeeId, $groupSubject) {
                $q->where(function($q2) use ($teacherId, $employeeId) {
                    if ($teacherId) $q2->where('teacher_id', $teacherId);
                    if ($employeeId) $q2->orWhere('teacher_id', $employeeId);
                })
                ->where('subject_id', $groupSubject->subject_id)
                ->where('group_id', $groupSubject->group_id);
            })->where('student_id', $student->id)->get();

            $studentRow['joriy_avg'] = $studentGrades->where('grade_type', 'joriy')->whereNotNull('score')->avg('score');
            $studentRow['oraliq_avg'] = $studentGrades->where('grade_type', 'oraliq')->whereNotNull('score')->avg('score');
            $studentRow['yakuniy_avg'] = $studentGrades->where('grade_type', 'yakuniy')->whereNotNull('score')->avg('score');
            $studentRow['total_avg'] = $studentGrades->whereNotNull('score')->avg('score');
            $studentRow['attendance_rate'] = $journalEntries->count() > 0
                ? round(($studentGrades->count() / $journalEntries->count()) * 100, 1)
                : 0;

            $journalMatrix[] = $studentRow;
        }

        if ($format === 'pdf') {
            $pdf = \PDF::loadView('teacher.journal.pdf', [
                'groupSubject' => $groupSubject,
                'journalEntries' => $journalEntries,
                'journalMatrix' => $journalMatrix,
                'teacher' => $teacher ?? $employee,
            ]);

            return $pdf->download('jurnal_' . $groupSubject->subject->name . '_' . $groupSubject->group->name . '.pdf');
        }

        // Excel export would go here
        return redirect()->back()->with('error', 'Excel eksport hozircha mavjud emas');
    }
}
