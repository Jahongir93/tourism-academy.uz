<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\ScheduleSlot;
use App\Models\Subject;
use App\Models\AcademicGroup;
use App\Models\Group;
use App\Models\Employee;
use App\Models\Teacher;
use App\Models\Classroom;
use App\Models\TimeSlot;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ScheduleController extends Controller
{
    /**
     * Display a listing of the schedules.
     */
    public function index(Request $request)
    {
        // Get schedule slots with relationships
        $query = ScheduleSlot::with(['schedule.group', 'subject', 'teacher', 'room']);

        // Filter by group if specified
        if ($request->filled('group_id')) {
            $query->whereHas('schedule', function($q) use ($request) {
                $q->where('group_id', $request->group_id);
            });
        }

        // Filter by teacher if specified
        if ($request->filled('teacher_id')) {
            $query->where('teacher_id', $request->teacher_id);
        }

        // Filter by day of week
        if ($request->filled('day_of_week')) {
            $query->where('day_of_week', $request->day_of_week);
        }

        // Get schedules
        $schedules = $query->orderBy('day_of_week')
                          ->orderBy('time_slot')
                          ->paginate(20);

        // Transform schedules to match view expectations
        $schedules->getCollection()->transform(function($slot) {
            // Create a virtual object that matches the view expectations
            $slot->group = $slot->schedule->group ?? null;
            $slot->classroom = $slot->room;

            // Normalize day_of_week to integer using day_number accessor
            $slot->day_of_week = $slot->day_number;

            // Time slot info
            $timeSlots = [
                1 => ['name' => '1-para', 'start_time' => '08:30', 'end_time' => '09:50'],
                2 => ['name' => '2-para', 'start_time' => '10:10', 'end_time' => '11:30'],
                3 => ['name' => '3-para', 'start_time' => '12:00', 'end_time' => '13:20'],
                4 => ['name' => '4-para', 'start_time' => '14:00', 'end_time' => '15:20'],
                5 => ['name' => '5-para', 'start_time' => '15:40', 'end_time' => '17:00'],
                6 => ['name' => '6-para', 'start_time' => '17:20', 'end_time' => '18:40'],
            ];

            $timeSlotNum = is_numeric($slot->time_slot) ? (int)$slot->time_slot : 1;
            $slot->timeSlot = (object)($timeSlots[$timeSlotNum] ?? ['name' => $timeSlotNum . '-para', 'start_time' => '--:--', 'end_time' => '--:--']);

            return $slot;
        });

        // Get filter data
        $groups = Group::orderBy('name')->get();
        if ($groups->isEmpty()) {
            $groups = AcademicGroup::orderBy('name')->get();
        }

        $teachers = Teacher::orderBy('last_name')->orderBy('first_name')->get();

        return view('schedule.index', compact('schedules', 'groups', 'teachers'));
    }

    /**
     * Show the form for creating a new schedule.
     */
    public function create()
    {
        $subjects = Subject::where(function($q) {
            $q->where('is_active', true)->orWhereNull('is_active');
        })->orderBy('name_uz')->get();

        $groups = Group::orderBy('name')->get();
        if ($groups->isEmpty()) {
            $groups = AcademicGroup::orderBy('name')->get();
        }

        $teachers = Teacher::orderBy('last_name')->orderBy('first_name')->get();

        $classrooms = Classroom::where(function($q) {
            $q->where('is_active', true)->orWhereNull('is_active');
        })->orderBy('name')->get();

        $timeSlots = TimeSlot::orderBy('slot_number')->get();

        // Create default time slots if empty
        if ($timeSlots->isEmpty()) {
            $this->createDefaultTimeSlots();
            $timeSlots = TimeSlot::orderBy('slot_number')->get();
        }

        return view('schedule.create', compact('subjects', 'groups', 'teachers', 'classrooms', 'timeSlots'));
    }

    protected function createDefaultTimeSlots()
    {
        $slots = [
            ['slot_number' => 1, 'name' => '1-para', 'start_time' => '08:30:00', 'end_time' => '09:50:00'],
            ['slot_number' => 2, 'name' => '2-para', 'start_time' => '10:10:00', 'end_time' => '11:30:00'],
            ['slot_number' => 3, 'name' => '3-para', 'start_time' => '12:00:00', 'end_time' => '13:20:00'],
            ['slot_number' => 4, 'name' => '4-para', 'start_time' => '14:00:00', 'end_time' => '15:20:00'],
            ['slot_number' => 5, 'name' => '5-para', 'start_time' => '15:40:00', 'end_time' => '17:00:00'],
            ['slot_number' => 6, 'name' => '6-para', 'start_time' => '17:20:00', 'end_time' => '18:40:00'],
        ];

        foreach ($slots as $slot) {
            TimeSlot::firstOrCreate(
                ['slot_number' => $slot['slot_number']],
                $slot
            );
        }
    }

    /**
     * Store a newly created schedule in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'group_id' => 'required|integer',
            'teacher_id' => 'required|integer',
            'classroom_id' => 'required|exists:classrooms,id',
            'time_slot_id' => 'required|integer|between:1,6',
            'day_of_week' => 'required|integer|between:1,6',
            'lesson_type' => 'required|in:lecture,practice,lab,seminar',
            'academic_year' => 'nullable|string',
            'semester' => 'nullable|integer|between:1,8',
        ]);

        DB::beginTransaction();
        try {
            // Get or create academic year
            $academicYear = AcademicYear::where('is_current', true)->first();
            if (!$academicYear) {
                $academicYear = AcademicYear::first();
                if (!$academicYear) {
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
                    'semester_id' => $validated['semester'] ?? 1,
                    'created_by' => auth()->id()
                ]
            );

            // Check for conflicts
            $existingSlot = ScheduleSlot::where('schedule_id', $schedule->id)
                ->where('day_of_week', $validated['day_of_week'])
                ->where('time_slot', $validated['time_slot_id'])
                ->first();

            if ($existingSlot) {
                DB::rollBack();
                return back()->withErrors(['conflict' => 'Bu vaqtda allaqachon dars mavjud.'])->withInput();
            }

            // Check teacher conflict
            $teacherConflict = ScheduleSlot::where('teacher_id', $validated['teacher_id'])
                ->where('day_of_week', $validated['day_of_week'])
                ->where('time_slot', $validated['time_slot_id'])
                ->whereHas('schedule', function($q) use ($academicYear) {
                    $q->where('academic_year_id', $academicYear->id)->where('status', 'active');
                })
                ->first();

            if ($teacherConflict) {
                DB::rollBack();
                return back()->withErrors(['teacher_conflict' => "O'qituvchi bu vaqtda boshqa guruhda band."])->withInput();
            }

            // Check classroom conflict
            $roomConflict = ScheduleSlot::where('room_id', $validated['classroom_id'])
                ->where('day_of_week', $validated['day_of_week'])
                ->where('time_slot', $validated['time_slot_id'])
                ->whereHas('schedule', function($q) use ($academicYear) {
                    $q->where('academic_year_id', $academicYear->id)->where('status', 'active');
                })
                ->first();

            if ($roomConflict) {
                DB::rollBack();
                return back()->withErrors(['room_conflict' => 'Xona bu vaqtda band.'])->withInput();
            }

            // Create schedule slot
            ScheduleSlot::create([
                'schedule_id' => $schedule->id,
                'day_of_week' => $validated['day_of_week'],
                'time_slot' => $validated['time_slot_id'],
                'subject_id' => $validated['subject_id'],
                'teacher_id' => $validated['teacher_id'],
                'room_id' => $validated['classroom_id'],
                'lesson_type' => $validated['lesson_type']
            ]);

            DB::commit();

            return redirect()->route('schedule.index')
                ->with('success', 'Dars jadvali muvaffaqiyatli qo\'shildi.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Xatolik: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Display the specified schedule.
     */
    public function show($id)
    {
        $slot = ScheduleSlot::with(['schedule.group', 'subject', 'teacher', 'room'])->findOrFail($id);

        // Prepare for view
        $slot->group = $slot->schedule->group ?? null;
        $slot->classroom = $slot->room;

        $timeSlots = [
            1 => ['name' => '1-para', 'start_time' => '08:30', 'end_time' => '09:50'],
            2 => ['name' => '2-para', 'start_time' => '10:10', 'end_time' => '11:30'],
            3 => ['name' => '3-para', 'start_time' => '12:00', 'end_time' => '13:20'],
            4 => ['name' => '4-para', 'start_time' => '14:00', 'end_time' => '15:20'],
            5 => ['name' => '5-para', 'start_time' => '15:40', 'end_time' => '17:00'],
            6 => ['name' => '6-para', 'start_time' => '17:20', 'end_time' => '18:40'],
        ];

        $slot->timeSlot = (object)($timeSlots[$slot->time_slot] ?? ['name' => $slot->time_slot . '-para', 'start_time' => '--:--', 'end_time' => '--:--']);

        $schedule = $slot;

        return view('schedule.show', compact('schedule'));
    }

    /**
     * Show the form for editing the specified schedule.
     */
    public function edit($id)
    {
        $slot = ScheduleSlot::with(['schedule.group', 'subject', 'teacher', 'room'])->findOrFail($id);

        // Prepare data for form
        $schedule = (object)[
            'id' => $slot->id,
            'subject_id' => $slot->subject_id,
            'group_id' => $slot->schedule->group_id ?? null,
            'teacher_id' => $slot->teacher_id,
            'classroom_id' => $slot->room_id,
            'time_slot_id' => $slot->time_slot,
            'day_of_week' => $slot->day_of_week,
            'lesson_type' => $slot->lesson_type
        ];

        $subjects = Subject::where(function($q) {
            $q->where('is_active', true)->orWhereNull('is_active');
        })->orderBy('name_uz')->get();

        $groups = Group::orderBy('name')->get();
        if ($groups->isEmpty()) {
            $groups = AcademicGroup::orderBy('name')->get();
        }

        $teachers = Teacher::orderBy('last_name')->orderBy('first_name')->get();

        $classrooms = Classroom::where(function($q) {
            $q->where('is_active', true)->orWhereNull('is_active');
        })->orderBy('name')->get();

        $timeSlots = TimeSlot::orderBy('slot_number')->get();
        if ($timeSlots->isEmpty()) {
            $this->createDefaultTimeSlots();
            $timeSlots = TimeSlot::orderBy('slot_number')->get();
        }

        return view('schedule.edit', compact('schedule', 'subjects', 'groups', 'teachers', 'classrooms', 'timeSlots'));
    }

    /**
     * Update the specified schedule in storage.
     */
    public function update(Request $request, $id)
    {
        $slot = ScheduleSlot::findOrFail($id);

        $validated = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'required|integer',
            'classroom_id' => 'required|exists:classrooms,id',
            'time_slot_id' => 'required|integer|between:1,6',
            'day_of_week' => 'required|integer|between:1,6',
            'lesson_type' => 'required|in:lecture,practice,lab,seminar',
        ]);

        $slot->update([
            'subject_id' => $validated['subject_id'],
            'teacher_id' => $validated['teacher_id'],
            'room_id' => $validated['classroom_id'],
            'time_slot' => $validated['time_slot_id'],
            'day_of_week' => $validated['day_of_week'],
            'lesson_type' => $validated['lesson_type']
        ]);

        return redirect()->route('schedule.index')
            ->with('success', 'Dars jadvali muvaffaqiyatli yangilandi.');
    }

    /**
     * Remove the specified schedule from storage.
     */
    public function destroy($id)
    {
        $slot = ScheduleSlot::findOrFail($id);
        $slot->delete();

        return redirect()->route('schedule.index')
            ->with('success', 'Dars jadvali muvaffaqiyatli o\'chirildi.');
    }

    /**
     * Display weekly schedule view
     */
    public function weekly(Request $request)
    {
        $groups = Group::orderBy('name')->get();
        if ($groups->isEmpty()) {
            $groups = AcademicGroup::orderBy('name')->get();
        }

        $teachers = Teacher::orderBy('last_name')->orderBy('first_name')->get();
        $classrooms = Classroom::where(function($q) {
            $q->where('is_active', true)->orWhereNull('is_active');
        })->orderBy('name')->get();

        $selectedGroupId = $request->get('group_id');
        $selectedTeacherId = $request->get('teacher_id');

        $schedules = collect();

        if ($selectedGroupId) {
            $schedule = Schedule::where('group_id', $selectedGroupId)
                ->where('status', 'active')
                ->first();

            if ($schedule) {
                $schedules = ScheduleSlot::with(['subject', 'teacher', 'room'])
                    ->where('schedule_id', $schedule->id)
                    ->orderBy('day_of_week')
                    ->orderBy('time_slot')
                    ->get();
            }
        } elseif ($selectedTeacherId) {
            $schedules = ScheduleSlot::with(['schedule.group', 'subject', 'room'])
                ->where('teacher_id', $selectedTeacherId)
                ->whereHas('schedule', function($q) {
                    $q->where('status', 'active');
                })
                ->orderBy('day_of_week')
                ->orderBy('time_slot')
                ->get();
        }

        $days = [
            1 => 'Dushanba',
            2 => 'Seshanba',
            3 => 'Chorshanba',
            4 => 'Payshanba',
            5 => 'Juma',
            6 => 'Shanba'
        ];

        $timeSlots = [
            1 => ['name' => '1-para', 'start_time' => '08:30', 'end_time' => '09:50'],
            2 => ['name' => '2-para', 'start_time' => '10:10', 'end_time' => '11:30'],
            3 => ['name' => '3-para', 'start_time' => '12:00', 'end_time' => '13:20'],
            4 => ['name' => '4-para', 'start_time' => '14:00', 'end_time' => '15:20'],
            5 => ['name' => '5-para', 'start_time' => '15:40', 'end_time' => '17:00'],
            6 => ['name' => '6-para', 'start_time' => '17:20', 'end_time' => '18:40'],
        ];

        return view('schedule.weekly', compact('groups', 'teachers', 'classrooms', 'schedules', 'days', 'timeSlots', 'selectedGroupId', 'selectedTeacherId'));
    }

    /**
     * Display room monitoring view
     */
    public function roomMonitoring(Request $request)
    {
        $classrooms = Classroom::where(function($q) {
            $q->where('is_active', true)->orWhereNull('is_active');
        })->with('building')->orderBy('name')->get();

        // Get current day and time slot
        $currentDay = now()->dayOfWeek;
        $currentDay = $currentDay == 0 ? 7 : $currentDay; // Sunday = 7

        $currentHour = now()->hour;
        $currentMinute = now()->minute;
        $currentTime = $currentHour * 60 + $currentMinute;

        // Determine current time slot
        $slotTimes = [
            1 => [510, 590],   // 08:30 - 09:50
            2 => [610, 690],   // 10:10 - 11:30
            3 => [720, 800],   // 12:00 - 13:20
            4 => [840, 920],   // 14:00 - 15:20
            5 => [940, 1020],  // 15:40 - 17:00
            6 => [1040, 1120], // 17:20 - 18:40
        ];

        $currentSlot = null;
        foreach ($slotTimes as $slot => $times) {
            if ($currentTime >= $times[0] && $currentTime <= $times[1]) {
                $currentSlot = $slot;
                break;
            }
        }

        // Get occupied rooms for current slot
        $occupiedRooms = [];
        if ($currentSlot && $currentDay <= 6) {
            $slots = ScheduleSlot::with(['schedule.group', 'subject', 'teacher'])
                ->where('day_of_week', $currentDay)
                ->where('time_slot', $currentSlot)
                ->whereHas('schedule', function($q) {
                    $q->where('status', 'active');
                })
                ->get();

            foreach ($slots as $slot) {
                $occupiedRooms[$slot->room_id] = [
                    'group' => $slot->schedule->group->name ?? 'N/A',
                    'subject' => $slot->subject->name_uz ?? $slot->subject->name ?? 'N/A',
                    'teacher' => $slot->teacher ? ($slot->teacher->last_name . ' ' . $slot->teacher->first_name) : 'N/A'
                ];
            }
        }

        $days = [
            1 => 'Dushanba',
            2 => 'Seshanba',
            3 => 'Chorshanba',
            4 => 'Payshanba',
            5 => 'Juma',
            6 => 'Shanba'
        ];

        return view('schedule.room-monitoring', compact('classrooms', 'occupiedRooms', 'currentDay', 'currentSlot', 'days'));
    }
}
