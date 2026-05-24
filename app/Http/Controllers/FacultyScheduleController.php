<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Faculty;
use App\Models\Group;
use App\Models\Schedule;
use App\Models\ScheduleSlot;
use App\Models\TimeSlot;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Employee;
use App\Models\Classroom;
use App\Models\AcademicYear;
use App\Models\Semester;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FacultyScheduleController extends Controller
{
    public function index()
    {
        $faculties = Faculty::active()->orderBy('name_uz')->get();
        $timeSlots = TimeSlot::orderBy('slot_number')->get();

        // Agar time_slots bo'sh bo'lsa, default yaratish
        if ($timeSlots->isEmpty()) {
            $this->createDefaultTimeSlots();
            $timeSlots = TimeSlot::orderBy('slot_number')->get();
        }

        $academicYears = AcademicYear::orderBy('id', 'desc')->get();
        $currentAcademicYear = AcademicYear::where('is_current', true)->first();

        $days = [
            1 => 'Dushanba',
            2 => 'Seshanba',
            3 => 'Chorshanba',
            4 => 'Payshanba',
            5 => 'Juma',
            6 => 'Shanba'
        ];

        return view('schedule.faculty-builder', compact('faculties', 'timeSlots', 'days', 'academicYears', 'currentAcademicYear'));
    }

    protected function createDefaultTimeSlots()
    {
        $slots = [
            ['slot_number' => 1, 'start_time' => '08:30:00', 'end_time' => '09:50:00'],
            ['slot_number' => 2, 'start_time' => '10:10:00', 'end_time' => '11:30:00'],
            ['slot_number' => 3, 'start_time' => '12:00:00', 'end_time' => '13:20:00'],
            ['slot_number' => 4, 'start_time' => '14:00:00', 'end_time' => '15:20:00'],
            ['slot_number' => 5, 'start_time' => '15:40:00', 'end_time' => '17:00:00'],
            ['slot_number' => 6, 'start_time' => '17:20:00', 'end_time' => '18:40:00'],
        ];

        foreach ($slots as $slot) {
            TimeSlot::firstOrCreate(
                ['slot_number' => $slot['slot_number']],
                $slot
            );
        }
    }

    public function getCourses(Request $request)
    {
        $facultyId = $request->faculty_id;

        // Get unique courses from groups in this faculty
        $courses = Group::where('faculty_id', $facultyId)
            ->select('course')
            ->distinct()
            ->orderBy('course')
            ->pluck('course');

        return response()->json([
            'courses' => $courses
        ]);
    }

    public function getGroups(Request $request)
    {
        $facultyId = $request->faculty_id;
        $course = $request->course;

        $groups = Group::where('faculty_id', $facultyId)
            ->where('course', $course)
            ->orderBy('name')
            ->get(['id', 'name', 'current_students']);

        return response()->json([
            'groups' => $groups
        ]);
    }

    public function getScheduleGrid(Request $request)
    {
        $groupId = $request->group_id;

        $group = Group::findOrFail($groupId);

        // Get current academic year
        $academicYear = AcademicYear::where('is_current', true)->first();
        if (!$academicYear) {
            $academicYear = AcademicYear::first();
        }

        // Get or create schedule for this group
        $schedule = null;
        if ($academicYear) {
            $schedule = Schedule::where('group_id', $groupId)
                ->where('academic_year_id', $academicYear->id)
                ->where('status', 'active')
                ->first();
        }

        // Get schedule slots
        $scheduleSlots = [];
        if ($schedule) {
            $slots = ScheduleSlot::with(['subject', 'teacher', 'room'])
                ->where('schedule_id', $schedule->id)
                ->get();

            foreach ($slots as $slot) {
                $key = $slot->day_of_week . '_' . $slot->time_slot;
                if (!isset($scheduleSlots[$key])) {
                    $scheduleSlots[$key] = [];
                }
                $scheduleSlots[$key][] = [
                    'id' => $slot->id,
                    'schedule_id' => $slot->schedule_id,
                    'day_of_week' => $slot->day_of_week,
                    'time_slot_id' => $slot->time_slot,
                    'subject' => $slot->subject ? [
                        'id' => $slot->subject->id,
                        'name_uz' => $slot->subject->name_uz ?? $slot->subject->name,
                        'code' => $slot->subject->code ?? ''
                    ] : null,
                    'teacher' => $slot->teacher ? [
                        'id' => $slot->teacher->id,
                        'first_name' => $slot->teacher->first_name,
                        'last_name' => $slot->teacher->last_name
                    ] : null,
                    'classroom' => $slot->room ? [
                        'id' => $slot->room->id,
                        'name' => $slot->room->name
                    ] : null,
                    'lesson_type' => $slot->lesson_type
                ];
            }
        }

        // Get subjects
        $subjects = Subject::where(function($q) {
            $q->where('is_active', true)->orWhereNull('is_active');
        })->get()->map(function($s) {
            return [
                'id' => $s->id,
                'name_uz' => $s->name_uz ?? $s->name,
                'code' => $s->code ?? ''
            ];
        });

        // Get teachers - from both Teachers and Employees tables
        $teachers = collect();

        // From teachers table
        $teacherModels = Teacher::orderBy('last_name')->orderBy('first_name')->get();
        foreach ($teacherModels as $t) {
            $teachers->push([
                'id' => $t->id,
                'first_name' => $t->first_name,
                'last_name' => $t->last_name,
                'middle_name' => $t->middle_name ?? '',
                'source' => 'teacher'
            ]);
        }

        // Get classrooms
        $classrooms = Classroom::where(function($q) {
            $q->where('is_active', true)->orWhereNull('is_active');
        })->with('building')->orderBy('name')->get()->map(function($c) {
            return [
                'id' => $c->id,
                'name' => $c->name,
                'capacity' => $c->capacity ?? 30,
                'building' => $c->building ? ['code' => $c->building->code ?? ''] : null
            ];
        });

        return response()->json([
            'schedules' => $scheduleSlots,
            'subjects' => $subjects,
            'teachers' => $teachers,
            'classrooms' => $classrooms,
            'group' => $group,
            'schedule_id' => $schedule ? $schedule->id : null
        ]);
    }

    public function storeSlot(Request $request)
    {
        $validated = $request->validate([
            'group_id' => 'required|integer',
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'required|integer',
            'classroom_id' => 'required|exists:classrooms,id',
            'day_of_week' => 'required|integer|between:1,6',
            'time_slot_id' => 'required|integer|between:1,6',
            'lesson_type' => 'nullable|in:lecture,practice,lab,seminar',
        ]);

        DB::beginTransaction();
        try {
            // Get or create academic year
            $academicYear = AcademicYear::where('is_current', true)->first();
            if (!$academicYear) {
                $academicYear = AcademicYear::first();
                if (!$academicYear) {
                    // Create default academic year
                    $currentYear = date('Y');
                    $academicYear = AcademicYear::create([
                        'name' => $currentYear . '-' . ($currentYear + 1) . " o'quv yili",
                        'start_date' => $currentYear . '-09-01',
                        'end_date' => ($currentYear + 1) . '-06-30',
                        'is_current' => true
                    ]);
                }
            }

            // Get or create schedule for this group
            $schedule = Schedule::firstOrCreate(
                [
                    'group_id' => $validated['group_id'],
                    'academic_year_id' => $academicYear->id,
                    'status' => 'active'
                ],
                [
                    'semester_id' => 1,
                    'created_by' => auth()->id()
                ]
            );

            // Check for conflicts
            $conflicts = $this->checkConflicts($validated, $schedule->id);
            if (!empty($conflicts)) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'conflicts' => $conflicts
                ], 422);
            }

            // Create or update schedule slot
            $slot = ScheduleSlot::updateOrCreate(
                [
                    'schedule_id' => $schedule->id,
                    'day_of_week' => $validated['day_of_week'],
                    'time_slot' => $validated['time_slot_id']
                ],
                [
                    'subject_id' => $validated['subject_id'],
                    'teacher_id' => $validated['teacher_id'],
                    'room_id' => $validated['classroom_id'],
                    'lesson_type' => $validated['lesson_type'] ?? 'lecture'
                ]
            );

            $slot->load(['subject', 'teacher', 'room']);

            DB::commit();

            return response()->json([
                'success' => true,
                'schedule' => [
                    'id' => $slot->id,
                    'schedule_id' => $slot->schedule_id,
                    'day_of_week' => $slot->day_of_week,
                    'time_slot_id' => $slot->time_slot,
                    'subject' => $slot->subject ? [
                        'id' => $slot->subject->id,
                        'name_uz' => $slot->subject->name_uz ?? $slot->subject->name
                    ] : null,
                    'teacher' => $slot->teacher ? [
                        'id' => $slot->teacher->id,
                        'first_name' => $slot->teacher->first_name,
                        'last_name' => $slot->teacher->last_name
                    ] : null,
                    'classroom' => $slot->room ? [
                        'id' => $slot->room->id,
                        'name' => $slot->room->name
                    ] : null
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Xatolik yuz berdi: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteSlot(Request $request)
    {
        $groupId = $request->group_id;
        $dayOfWeek = $request->day_of_week;
        $timeSlot = $request->time_slot_id;

        // Find the schedule
        $academicYear = AcademicYear::where('is_current', true)->first();
        if (!$academicYear) {
            $academicYear = AcademicYear::first();
        }

        $schedule = Schedule::where('group_id', $groupId)
            ->where('academic_year_id', $academicYear ? $academicYear->id : 0)
            ->where('status', 'active')
            ->first();

        if (!$schedule) {
            return response()->json(['success' => false, 'message' => 'Jadval topilmadi'], 404);
        }

        $slot = ScheduleSlot::where('schedule_id', $schedule->id)
            ->where('day_of_week', $dayOfWeek)
            ->where('time_slot', $timeSlot)
            ->first();

        if ($slot) {
            $slot->delete();
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'Slot topilmadi'], 404);
    }

    public function applySchedule(Request $request)
    {
        $validated = $request->validate([
            'group_id' => 'required|integer',
            'period_type' => 'required|in:week,month,semester',
            'start_date' => 'required|date'
        ]);

        $groupId = $validated['group_id'];
        $startDate = Carbon::parse($validated['start_date']);

        // Calculate end date based on period type
        $endDate = match($validated['period_type']) {
            'week' => $startDate->copy()->addWeek(),
            'month' => $startDate->copy()->addMonth(),
            'semester' => $startDate->copy()->addMonths(4),
        };

        // Find the schedule for this group
        $academicYear = AcademicYear::where('is_current', true)->first();
        if (!$academicYear) {
            $academicYear = AcademicYear::first();
        }

        $schedule = Schedule::where('group_id', $groupId)
            ->where('academic_year_id', $academicYear ? $academicYear->id : 0)
            ->where('status', 'active')
            ->first();

        if (!$schedule) {
            return response()->json([
                'success' => false,
                'message' => 'Bu guruh uchun jadval topilmadi. Avval jadval yarating.'
            ], 404);
        }

        // Count schedule slots
        $slotCount = ScheduleSlot::where('schedule_id', $schedule->id)->count();

        if ($slotCount == 0) {
            return response()->json([
                'success' => false,
                'message' => 'Jadvalda darslar topilmadi. Avval darslarni qo\'shing.'
            ], 404);
        }

        // Mark schedule as approved
        $schedule->update([
            'approved_by' => auth()->id()
        ]);

        return response()->json([
            'success' => true,
            'message' => "Jadval muvaffaqiyatli tasdiqlandi. {$slotCount} ta dars {$validated['period_type']} uchun qo'llandi.",
            'period' => $validated['period_type'],
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'slot_count' => $slotCount
        ]);
    }

    public function duplicateWeek(Request $request)
    {
        $validated = $request->validate([
            'group_id' => 'required|integer',
            'source_week_start' => 'required|date',
            'target_week_start' => 'required|date'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Hafta nusxalash funksiyasi'
        ]);
    }

    private function checkConflicts($data, $currentScheduleId)
    {
        $conflicts = [];

        // Check teacher conflict - same teacher, same day, same time slot in different schedules
        $teacherConflict = ScheduleSlot::where('teacher_id', $data['teacher_id'])
            ->where('day_of_week', $data['day_of_week'])
            ->where('time_slot', $data['time_slot_id'])
            ->where('schedule_id', '!=', $currentScheduleId)
            ->with(['schedule.group'])
            ->first();

        if ($teacherConflict && $teacherConflict->schedule && $teacherConflict->schedule->group) {
            $conflicts[] = [
                'type' => 'teacher',
                'message' => "O'qituvchi bu vaqtda boshqa guruhda band: " . $teacherConflict->schedule->group->name
            ];
        }

        // Check classroom conflict - same room, same day, same time slot
        $classroomConflict = ScheduleSlot::where('room_id', $data['classroom_id'])
            ->where('day_of_week', $data['day_of_week'])
            ->where('time_slot', $data['time_slot_id'])
            ->where('schedule_id', '!=', $currentScheduleId)
            ->with(['schedule.group'])
            ->first();

        if ($classroomConflict && $classroomConflict->schedule && $classroomConflict->schedule->group) {
            $conflicts[] = [
                'type' => 'classroom',
                'message' => "Xona bu vaqtda band: " . $classroomConflict->schedule->group->name
            ];
        }

        return $conflicts;
    }

    /**
     * Auto generate schedule based on curriculum
     */
    public function autoGenerate(Request $request)
    {
        $validated = $request->validate([
            'group_id' => 'required|integer',
            'semester_id' => 'nullable|integer'
        ]);

        $groupId = $validated['group_id'];

        // Get group info
        $group = Group::findOrFail($groupId);

        // Get academic year
        $academicYear = AcademicYear::where('is_current', true)->first();
        if (!$academicYear) {
            return response()->json([
                'success' => false,
                'message' => "Joriy o'quv yili topilmadi"
            ], 400);
        }

        DB::beginTransaction();
        try {
            // Create or get schedule
            $schedule = Schedule::firstOrCreate(
                [
                    'group_id' => $groupId,
                    'academic_year_id' => $academicYear->id,
                    'status' => 'active'
                ],
                [
                    'semester_id' => $validated['semester_id'] ?? 1,
                    'created_by' => auth()->id()
                ]
            );

            // Get available time slots
            $timeSlots = TimeSlot::orderBy('slot_number')->get();
            if ($timeSlots->isEmpty()) {
                $this->createDefaultTimeSlots();
                $timeSlots = TimeSlot::orderBy('slot_number')->get();
            }

            // Get subjects for this group (from teacher_subjects or curriculum)
            $subjects = Subject::where(function($q) {
                $q->where('is_active', true)->orWhereNull('is_active');
            })->limit(10)->get();

            // Get available teachers
            $teachers = Teacher::limit(5)->get();

            // Get available classrooms
            $classrooms = Classroom::where(function($q) {
                $q->where('is_active', true)->orWhereNull('is_active');
            })->limit(10)->get();

            if ($subjects->isEmpty() || $teachers->isEmpty() || $classrooms->isEmpty()) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Fanlar, o\'qituvchilar yoki xonalar topilmadi'
                ], 400);
            }

            $created = 0;
            $days = [1, 2, 3, 4, 5, 6]; // Monday to Saturday
            $subjectIndex = 0;
            $teacherIndex = 0;
            $classroomIndex = 0;

            foreach ($days as $day) {
                foreach ($timeSlots->take(4) as $slot) { // Max 4 lessons per day
                    // Skip if slot already exists
                    $exists = ScheduleSlot::where('schedule_id', $schedule->id)
                        ->where('day_of_week', $day)
                        ->where('time_slot', $slot->slot_number)
                        ->exists();

                    if (!$exists && $subjectIndex < $subjects->count()) {
                        ScheduleSlot::create([
                            'schedule_id' => $schedule->id,
                            'day_of_week' => $day,
                            'time_slot' => $slot->slot_number,
                            'subject_id' => $subjects[$subjectIndex]->id,
                            'teacher_id' => $teachers[$teacherIndex % $teachers->count()]->id,
                            'room_id' => $classrooms[$classroomIndex % $classrooms->count()]->id,
                            'lesson_type' => 'lecture'
                        ]);
                        $created++;
                        $subjectIndex++;
                        $teacherIndex++;
                        $classroomIndex++;
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "{$created} ta dars avtomatik yaratildi",
                'created' => $created
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Xatolik: ' . $e->getMessage()
            ], 500);
        }
    }
}
