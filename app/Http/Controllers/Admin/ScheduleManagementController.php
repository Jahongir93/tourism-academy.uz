<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\StudentGroup;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;

class ScheduleManagementController extends Controller
{
    public function index()
    {
        $groups = StudentGroup::with('schedules')->get();
        return view('admin.schedule.index', compact('groups'));
    }

    public function create(Request $request)
    {
        $groupId = $request->get('group_id');
        $groups = StudentGroup::all();
        $subjects = Subject::all();
        $teachers = User::role(['Teacher', 'teacher'])->get();

        return view('admin.schedule.create', compact('groups', 'subjects', 'teachers', 'groupId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'group_id' => 'required|exists:student_groups,id',
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'required|exists:users,id',
            'day_of_week' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday',
            'pair_number' => 'required|integer|min:1|max:5',
            'start_time' => 'required',
            'end_time' => 'required',
            'lesson_type' => 'required|in:lecture,practice,seminar,lab',
            'room' => 'nullable|string',
            'building' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
        ]);

        Schedule::create($validated);

        return redirect()->route('admin.schedule.index')
            ->with('success', 'Dars jadvali muvaffaqiyatli qo\'shildi');
    }

    public function edit(Schedule $schedule)
    {
        $groups = StudentGroup::all();
        $subjects = Subject::all();
        $teachers = User::role(['Teacher', 'teacher'])->get();

        return view('admin.schedule.edit', compact('schedule', 'groups', 'subjects', 'teachers'));
    }

    public function update(Request $request, Schedule $schedule)
    {
        $validated = $request->validate([
            'group_id' => 'required|exists:student_groups,id',
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'required|exists:users,id',
            'day_of_week' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday',
            'pair_number' => 'required|integer|min:1|max:5',
            'start_time' => 'required',
            'end_time' => 'required',
            'lesson_type' => 'required|in:lecture,practice,seminar,lab',
            'room' => 'nullable|string',
            'building' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'status' => 'required|in:active,cancelled,rescheduled',
        ]);

        $schedule->update($validated);

        return redirect()->route('admin.schedule.index')
            ->with('success', 'Dars jadvali yangilandi');
    }

    public function destroy(Schedule $schedule)
    {
        $schedule->delete();

        return redirect()->route('admin.schedule.index')
            ->with('success', 'Dars jadvali o\'chirildi');
    }

    public function show($groupId)
    {
        $group = StudentGroup::with(['schedules.subject', 'schedules.teacher'])->findOrFail($groupId);

        $scheduleByDay = $group->schedules->groupBy('day_of_week');

        return view('admin.schedule.show', compact('group', 'scheduleByDay'));
    }
}
