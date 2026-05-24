<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Group;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\Subject;
use App\Models\Schedule;
use App\Models\ScheduleSlot;
use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\LmsExam;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DekanController extends Controller
{
    protected $faculty;

    /**
     * Dekanning fakultetini olish
     */
    private function getDeanFaculty()
    {
        $user = auth()->user();

        // Dekan employee orqali fakultetni topish
        $employee = Employee::where('user_id', $user->id)->first();

        if ($employee) {
            // employment_details jadvalidan fakultetni olish
            $employmentDetail = DB::table('employment_details')
                ->where('employee_id', $employee->id)
                ->first();

            if ($employmentDetail && $employmentDetail->faculty_id) {
                return Faculty::find($employmentDetail->faculty_id);
            }
        }

        // Agar fakultet topilmasa, birinchi fakultetni qaytarish (test uchun)
        return Faculty::first();
    }

    /**
     * Dekan Dashboard
     */
    public function dashboard()
    {
        $faculty = $this->getDeanFaculty();
        $facultyId = $faculty?->id;

        // Asosiy statistikalar
        $teacherCount = $facultyId
            ? DB::table('employees')
                ->join('employment_details', 'employees.id', '=', 'employment_details.employee_id')
                ->where('employment_details.faculty_id', $facultyId)
                ->where('employees.employee_type', 'teacher')
                ->where('employees.status', 'active')
                ->count()
            : Employee::where('employee_type', 'teacher')->where('status', 'active')->count();

        $stats = [
            'total_students' => Student::when($facultyId, fn($q) => $q->where('faculty_id', $facultyId))
                ->where('status', 'active')->count(),
            'total_groups' => Group::when($facultyId, fn($q) => $q->where('faculty_id', $facultyId))
                ->where('is_active', true)->count(),
            'total_teachers' => $teacherCount,
            'total_departments' => Department::when($facultyId, fn($q) => $q->where('faculty_id', $facultyId))
                ->count(),
        ];

        // Kurs bo'yicha talabalar
        $studentsByCourse = Student::when($facultyId, fn($q) => $q->where('faculty_id', $facultyId))
            ->where('status', 'active')
            ->select('course', DB::raw('count(*) as count'))
            ->groupBy('course')
            ->orderBy('course')
            ->get();

        // Guruhlar bo'yicha talabalar soni
        $groupStats = Group::when($facultyId, fn($q) => $q->where('faculty_id', $facultyId))
            ->where('is_active', true)
            ->withCount(['students' => fn($q) => $q->where('status', 'active')])
            ->orderBy('name')
            ->limit(10)
            ->get();

        // Kafedralar statistikasi
        $departmentStats = Department::when($facultyId, fn($q) => $q->where('faculty_id', $facultyId))
            ->get()
            ->map(function ($department) {
                $department->employees_count = DB::table('employment_details')
                    ->join('employees', 'employment_details.employee_id', '=', 'employees.id')
                    ->where('employment_details.department_id', $department->id)
                    ->where('employees.status', 'active')
                    ->count();
                return $department;
            })
            ->sortByDesc('employees_count')
            ->values();

        // Eng yaxshi talabalar (so'nggi qo'shilgan)
        $topStudents = Student::when($facultyId, fn($q) => $q->where('faculty_id', $facultyId))
            ->where('status', 'active')
            ->with('group')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('dean.dashboard', compact(
            'faculty',
            'stats',
            'studentsByCourse',
            'groupStats',
            'departmentStats',
            'topStudents'
        ));
    }

    /**
     * Talabalar ro'yxati
     */
    public function studentsIndex(Request $request)
    {
        $faculty = $this->getDeanFaculty();
        $facultyId = $faculty?->id;

        $query = Student::with(['group', 'specialty'])
            ->when($facultyId, fn($q) => $q->where('faculty_id', $facultyId));

        // Qidiruv
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('student_id', 'like', "%{$search}%");
            });
        }

        // Guruh filtri
        if ($request->filled('group_id')) {
            $query->where('group_id', $request->group_id);
        }

        // Kurs filtri
        if ($request->filled('course')) {
            $query->where('course', $request->course);
        }

        // Status filtri
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $students = $query->orderBy('last_name')->paginate(20);
        $groups = Group::when($facultyId, fn($q) => $q->where('faculty_id', $facultyId))
            ->where('is_active', true)->orderBy('name')->get();

        return view('dean.students.index', compact('students', 'groups', 'faculty'));
    }

    /**
     * Talaba ko'chirishlar
     */
    public function studentsTransfers()
    {
        $faculty = $this->getDeanFaculty();
        return view('dean.students.transfers', compact('faculty'));
    }

    /**
     * Bitiruvchilar
     */
    public function studentsGraduates(Request $request)
    {
        $faculty = $this->getDeanFaculty();
        $facultyId = $faculty?->id;

        $graduates = Student::with(['group', 'specialty'])
            ->when($facultyId, fn($q) => $q->where('faculty_id', $facultyId))
            ->where('course', 4) // 4-kurs talabalar
            ->where('status', 'active')
            ->orderBy('last_name')
            ->paginate(20);

        return view('dean.students.graduates', compact('graduates', 'faculty'));
    }

    /**
     * Guruhlar ro'yxati
     */
    public function groupsIndex(Request $request)
    {
        $faculty = $this->getDeanFaculty();
        $facultyId = $faculty?->id;

        $query = Group::with(['specialty', 'curator'])
            ->when($facultyId, fn($q) => $q->where('faculty_id', $facultyId))
            ->withCount(['students' => fn($q) => $q->where('status', 'active')]);

        if ($request->filled('course')) {
            $query->where('course', $request->course);
        }

        $groups = $query->orderBy('name')->paginate(20);

        return view('dean.groups.index', compact('groups', 'faculty'));
    }

    /**
     * Kuratorlar
     */
    public function groupsCurators()
    {
        $faculty = $this->getDeanFaculty();
        $facultyId = $faculty?->id;

        // Barcha guruhlarni olish (kuratorsiz ham)
        $groups = Group::with(['curator', 'specialty'])
            ->when($facultyId, fn($q) => $q->where('faculty_id', $facultyId))
            ->where('is_active', true)
            ->withCount(['students' => fn($q) => $q->where('status', 'active')])
            ->orderBy('name')
            ->paginate(20);

        // O'qituvchilarni olish (kurator sifatida tayinlash uchun)
        // Faqat user_id bo'lgan o'qituvchilar (chunki curator_id users jadvaliga bog'langan)
        $teachers = Employee::where('employee_type', 'teacher')
            ->where('status', 'active')
            ->whereNotNull('user_id')
            ->when($facultyId, function($q) use ($facultyId) {
                $q->whereIn('id', function($subquery) use ($facultyId) {
                    $subquery->select('employee_id')
                        ->from('employment_details')
                        ->where('faculty_id', $facultyId);
                });
            })
            ->orderBy('last_name')
            ->get();

        return view('dean.groups.curators', compact('groups', 'faculty', 'teachers'));
    }

    /**
     * O'qituvchilar ro'yxati
     */
    public function teachersIndex(Request $request)
    {
        $faculty = $this->getDeanFaculty();
        $facultyId = $faculty?->id;

        // employment_details orqali o'qituvchilarni olish
        $query = Employee::with(['employmentDetail.department', 'employmentDetail.position'])
            ->where('employee_type', 'teacher');

        if ($facultyId) {
            $query->whereIn('id', function($subquery) use ($facultyId) {
                $subquery->select('employee_id')
                    ->from('employment_details')
                    ->where('faculty_id', $facultyId);
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('department_id')) {
            $query->whereIn('id', function($subquery) use ($request) {
                $subquery->select('employee_id')
                    ->from('employment_details')
                    ->where('department_id', $request->department_id);
            });
        }

        $teachers = $query->orderBy('last_name')->paginate(20);
        $departments = Department::when($facultyId, fn($q) => $q->where('faculty_id', $facultyId))
            ->orderBy('name')->get();

        return view('dean.teachers.index', compact('teachers', 'departments', 'faculty'));
    }

    /**
     * Kafedralar
     */
    public function departmentsIndex()
    {
        $faculty = $this->getDeanFaculty();
        $facultyId = $faculty?->id;

        $departments = Department::when($facultyId, fn($q) => $q->where('faculty_id', $facultyId))
            ->orderBy('name')
            ->paginate(20);

        // Har bir kafedra uchun xodimlar sonini hisoblash
        $departments->getCollection()->transform(function ($department) {
            $department->employees_count = DB::table('employment_details')
                ->join('employees', 'employment_details.employee_id', '=', 'employees.id')
                ->where('employment_details.department_id', $department->id)
                ->where('employees.status', 'active')
                ->count();
            return $department;
        });

        return view('dean.departments.index', compact('departments', 'faculty'));
    }

    /**
     * Dars jadvali
     */
    public function scheduleIndex(Request $request)
    {
        $faculty = $this->getDeanFaculty();
        $facultyId = $faculty?->id;

        $query = Schedule::with(['group', 'academicYear', 'createdBy'])
            ->when($facultyId, function($q) use ($facultyId) {
                $q->whereHas('group', fn($g) => $g->where('faculty_id', $facultyId));
            });

        // Guruh filtri
        if ($request->filled('group_id')) {
            $query->where('group_id', $request->group_id);
        }

        // Status filtri
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $schedules = $query->orderBy('created_at', 'desc')->paginate(20);

        $groups = Group::when($facultyId, fn($q) => $q->where('faculty_id', $facultyId))
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('dean.schedule.index', compact('schedules', 'groups', 'faculty'));
    }

    /**
     * Jadval yaratish formasi
     */
    public function scheduleCreate()
    {
        $faculty = $this->getDeanFaculty();
        $facultyId = $faculty?->id;

        $groups = Group::when($facultyId, fn($q) => $q->where('faculty_id', $facultyId))
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $academicYears = AcademicYear::orderBy('start_date', 'desc')->get();

        return view('dean.schedule.create', compact('groups', 'academicYears', 'faculty'));
    }

    /**
     * Jadval saqlash
     */
    public function scheduleStore(Request $request)
    {
        $request->validate([
            'group_id' => 'required|exists:groups,id',
            'academic_year_id' => 'nullable|exists:academic_years,id',
            'semester_id' => 'nullable|integer|min:1|max:2',
        ]);

        $faculty = $this->getDeanFaculty();
        $group = Group::findOrFail($request->group_id);

        // Fakultet tekshiruvi
        if ($faculty && $group->faculty_id !== $faculty->id) {
            return back()->with('error', 'Bu guruh sizning fakultetingizga tegishli emas.');
        }

        $schedule = Schedule::create([
            'group_id' => $request->group_id,
            'academic_year_id' => $request->academic_year_id ?? AcademicYear::current()?->id,
            'semester_id' => $request->semester_id ?? 1,
            'status' => 'draft',
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('dean.schedule.edit', $schedule)
            ->with('success', 'Jadval yaratildi. Endi dars vaqtlarini qo\'shing.');
    }

    /**
     * Jadvalni ko'rish
     */
    public function scheduleShow(Schedule $schedule)
    {
        $faculty = $this->getDeanFaculty();

        // Fakultet tekshiruvi
        if ($faculty && $schedule->group->faculty_id !== $faculty->id) {
            return back()->with('error', 'Bu jadval sizning fakultetingizga tegishli emas.');
        }

        $schedule->load(['group', 'academicYear', 'slots.subject', 'slots.teacher', 'slots.room']);

        // Kunlar bo'yicha slotlarni guruhlash
        $days = ['Dushanba', 'Seshanba', 'Chorshanba', 'Payshanba', 'Juma', 'Shanba'];
        $timeSlots = ['1' => '08:30-09:50', '2' => '10:00-11:20', '3' => '11:30-12:50', '4' => '14:00-15:20', '5' => '15:30-16:50', '6' => '17:00-18:20'];

        return view('dean.schedule.show', compact('schedule', 'faculty', 'days', 'timeSlots'));
    }

    /**
     * Jadvalni tahrirlash
     */
    public function scheduleEdit(Schedule $schedule)
    {
        $faculty = $this->getDeanFaculty();
        $facultyId = $faculty?->id;

        // Fakultet tekshiruvi
        if ($faculty && $schedule->group->faculty_id !== $faculty->id) {
            return back()->with('error', 'Bu jadval sizning fakultetingizga tegishli emas.');
        }

        $schedule->load(['group', 'academicYear', 'slots.subject', 'slots.teacher', 'slots.room']);

        $subjects = Subject::where('is_active', true)->orderBy('name_uz')->get();
        $teachers = Employee::where('employee_type', 'teacher')
            ->where('status', 'active')
            ->when($facultyId, function($q) use ($facultyId) {
                $q->whereIn('id', function($subquery) use ($facultyId) {
                    $subquery->select('employee_id')
                        ->from('employment_details')
                        ->where('faculty_id', $facultyId);
                });
            })
            ->orderBy('last_name')
            ->get();
        $rooms = Classroom::where('is_active', true)->orderBy('name')->get();

        $days = [1 => 'Dushanba', 2 => 'Seshanba', 3 => 'Chorshanba', 4 => 'Payshanba', 5 => 'Juma', 6 => 'Shanba'];
        $timeSlots = ['1' => '08:30-09:50', '2' => '10:00-11:20', '3' => '11:30-12:50', '4' => '14:00-15:20', '5' => '15:30-16:50', '6' => '17:00-18:20'];

        return view('dean.schedule.edit', compact('schedule', 'faculty', 'subjects', 'teachers', 'rooms', 'days', 'timeSlots'));
    }

    /**
     * Jadvalni yangilash
     */
    public function scheduleUpdate(Request $request, Schedule $schedule)
    {
        $faculty = $this->getDeanFaculty();

        if ($faculty && $schedule->group->faculty_id !== $faculty->id) {
            return back()->with('error', 'Bu jadval sizning fakultetingizga tegishli emas.');
        }

        $request->validate([
            'status' => 'nullable|in:draft,active,archived',
        ]);

        if ($request->filled('status')) {
            $schedule->status = $request->status;
            if ($request->status === 'active') {
                $schedule->approved_by = auth()->id();
            }
        }

        $schedule->save();

        return back()->with('success', 'Jadval muvaffaqiyatli yangilandi.');
    }

    /**
     * Jadvalni o'chirish
     */
    public function scheduleDestroy(Schedule $schedule)
    {
        $faculty = $this->getDeanFaculty();

        if ($faculty && $schedule->group->faculty_id !== $faculty->id) {
            return back()->with('error', 'Bu jadval sizning fakultetingizga tegishli emas.');
        }

        // Avval slotlarni o'chirish
        $schedule->slots()->delete();
        $schedule->delete();

        return redirect()->route('dean.schedule.index')
            ->with('success', 'Jadval muvaffaqiyatli o\'chirildi.');
    }

    /**
     * Jadval slotini qo'shish
     */
    public function scheduleSlotStore(Request $request, Schedule $schedule)
    {
        $faculty = $this->getDeanFaculty();

        if ($faculty && $schedule->group->faculty_id !== $faculty->id) {
            return back()->with('error', 'Bu jadval sizning fakultetingizga tegishli emas.');
        }

        $request->validate([
            'day_of_week' => 'required|integer|min:1|max:6',
            'time_slot' => 'required|string',
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'nullable|exists:employees,id',
            'room_id' => 'nullable|exists:classrooms,id',
            'lesson_type' => 'nullable|in:lecture,practice,lab,seminar',
            'week_type' => 'nullable|in:all,odd,even',
        ]);

        // Mavjud slot tekshiruvi
        $existingSlot = ScheduleSlot::where('schedule_id', $schedule->id)
            ->where('day_of_week', $request->day_of_week)
            ->where('time_slot', $request->time_slot)
            ->where('week_type', $request->week_type ?? 'all')
            ->first();

        if ($existingSlot) {
            return back()->with('error', 'Bu vaqt uchun allaqachon dars belgilangan.');
        }

        // Teacher ni employees dan Teachers jadvaliga moslashtirish
        $teacherId = null;
        if ($request->teacher_id) {
            $employee = Employee::find($request->teacher_id);
            if ($employee) {
                $teacher = Teacher::where('employee_id', $employee->id)->first();
                $teacherId = $teacher?->id ?? $employee->id;
            }
        }

        ScheduleSlot::create([
            'schedule_id' => $schedule->id,
            'day_of_week' => $request->day_of_week,
            'time_slot' => $request->time_slot,
            'subject_id' => $request->subject_id,
            'teacher_id' => $teacherId,
            'room_id' => $request->room_id,
            'lesson_type' => $request->lesson_type ?? 'lecture',
            'week_type' => $request->week_type ?? 'all',
        ]);

        return back()->with('success', 'Dars muvaffaqiyatli qo\'shildi.');
    }

    /**
     * Jadval slotini o'chirish
     */
    public function scheduleSlotDestroy(ScheduleSlot $slot)
    {
        $faculty = $this->getDeanFaculty();

        if ($faculty && $slot->schedule->group->faculty_id !== $faculty->id) {
            return back()->with('error', 'Bu slot sizning fakultetingizga tegishli emas.');
        }

        $slot->delete();

        return back()->with('success', 'Dars muvaffaqiyatli o\'chirildi.');
    }

    /**
     * Imtihon jadvali
     */
    public function scheduleExams(Request $request)
    {
        $faculty = $this->getDeanFaculty();
        $facultyId = $faculty?->id;

        // Fakultetga tegishli guruhlar
        $groupIds = Group::when($facultyId, fn($q) => $q->where('faculty_id', $facultyId))
            ->where('is_active', true)
            ->pluck('id')
            ->toArray();

        $query = LmsExam::with(['subject', 'teacher']);

        // Guruhlar bo'yicha filtrlash (string va integer qiymatlarni tekshirish)
        if (!empty($groupIds)) {
            $query->where(function($q) use ($groupIds) {
                foreach ($groupIds as $groupId) {
                    // Integer va string formatlarni qidirish
                    $q->orWhereJsonContains('group_ids', (int)$groupId)
                      ->orWhereJsonContains('group_ids', (string)$groupId);
                }
            });
        }

        // Status filtri
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Tur filtri
        if ($request->filled('exam_type')) {
            $query->where('exam_type', $request->exam_type);
        }

        $exams = $query->orderBy('start_time', 'desc')->paginate(20);

        $groups = Group::when($facultyId, fn($q) => $q->where('faculty_id', $facultyId))
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('dean.schedule.exams', compact('exams', 'groups', 'faculty'));
    }

    /**
     * Imtihon yaratish formasi
     */
    public function examCreate()
    {
        $faculty = $this->getDeanFaculty();
        $facultyId = $faculty?->id;

        $groups = Group::when($facultyId, fn($q) => $q->where('faculty_id', $facultyId))
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $subjects = Subject::where('is_active', true)->orderBy('name_uz')->get();

        $teachers = Employee::where('employee_type', 'teacher')
            ->where('status', 'active')
            ->when($facultyId, function($q) use ($facultyId) {
                $q->whereIn('id', function($subquery) use ($facultyId) {
                    $subquery->select('employee_id')
                        ->from('employment_details')
                        ->where('faculty_id', $facultyId);
                });
            })
            ->orderBy('last_name')
            ->get();

        return view('dean.schedule.exams-create', compact('groups', 'subjects', 'teachers', 'faculty'));
    }

    /**
     * Imtihon saqlash
     */
    public function examStore(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'subject_id' => 'required|exists:subjects,id',
            'group_ids' => 'required|array|min:1',
            'group_ids.*' => 'exists:groups,id',
            'exam_type' => 'required|in:joriy,oraliq,yakuniy,practice',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'duration_minutes' => 'required|integer|min:10|max:300',
            'max_score' => 'required|numeric|min:1|max:100',
            'teacher_id' => 'nullable|exists:employees,id',
        ]);

        $faculty = $this->getDeanFaculty();

        // Guruhlar fakultetga tegishliligini tekshirish
        if ($faculty) {
            $validGroups = Group::where('faculty_id', $faculty->id)
                ->whereIn('id', $request->group_ids)
                ->pluck('id')
                ->toArray();

            if (count($validGroups) !== count($request->group_ids)) {
                return back()->with('error', 'Ba\'zi guruhlar sizning fakultetingizga tegishli emas.');
            }
        }

        // group_ids ni integer arrayga aylantirish
        $groupIdsInt = array_map('intval', $request->group_ids);

        LmsExam::create([
            'title' => $request->title,
            'description' => $request->description,
            'subject_id' => $request->subject_id,
            'teacher_id' => $request->teacher_id,
            'group_ids' => $groupIdsInt,
            'exam_type' => $request->exam_type,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'duration_minutes' => $request->duration_minutes,
            'max_score' => $request->max_score,
            'passing_score' => $request->passing_score ?? ($request->max_score * 0.6),
            'max_attempts' => $request->max_attempts ?? 1,
            'status' => 'scheduled',
            'is_published' => false,
        ]);

        return redirect()->route('dean.schedule.exams')
            ->with('success', 'Imtihon muvaffaqiyatli yaratildi.');
    }

    /**
     * Imtihonni ko'rish
     */
    public function examShow(LmsExam $exam)
    {
        $faculty = $this->getDeanFaculty();
        $exam->load(['subject', 'teacher', 'questions', 'attempts']);

        // Guruhlar ma'lumotini olish
        $groupIds = $exam->group_ids ?? [];
        $groups = Group::whereIn('id', $groupIds)->get();

        return view('dean.schedule.exams-show', compact('exam', 'groups', 'faculty'));
    }

    /**
     * Imtihon tahrirlash
     */
    public function examEdit(LmsExam $exam)
    {
        $faculty = $this->getDeanFaculty();
        $facultyId = $faculty?->id;

        $groups = Group::when($facultyId, fn($q) => $q->where('faculty_id', $facultyId))
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $subjects = Subject::where('is_active', true)->orderBy('name_uz')->get();

        $teachers = Employee::where('employee_type', 'teacher')
            ->where('status', 'active')
            ->when($facultyId, function($q) use ($facultyId) {
                $q->whereIn('id', function($subquery) use ($facultyId) {
                    $subquery->select('employee_id')
                        ->from('employment_details')
                        ->where('faculty_id', $facultyId);
                });
            })
            ->orderBy('last_name')
            ->get();

        return view('dean.schedule.exams-edit', compact('exam', 'groups', 'subjects', 'teachers', 'faculty'));
    }

    /**
     * Imtihon yangilash
     */
    public function examUpdate(Request $request, LmsExam $exam)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'subject_id' => 'required|exists:subjects,id',
            'group_ids' => 'required|array|min:1',
            'exam_type' => 'required|in:joriy,oraliq,yakuniy,practice',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'duration_minutes' => 'required|integer|min:10|max:300',
        ]);

        // group_ids ni integer arrayga aylantirish
        $groupIdsInt = array_map('intval', $request->group_ids);

        $exam->update([
            'title' => $request->title,
            'description' => $request->description,
            'subject_id' => $request->subject_id,
            'teacher_id' => $request->teacher_id,
            'group_ids' => $groupIdsInt,
            'exam_type' => $request->exam_type,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'duration_minutes' => $request->duration_minutes,
            'max_score' => $request->max_score ?? $exam->max_score,
            'passing_score' => $request->passing_score ?? $exam->passing_score,
            'status' => $request->status ?? $exam->status,
            'is_published' => $request->has('is_published'),
        ]);

        return redirect()->route('dean.schedule.exams')
            ->with('success', 'Imtihon muvaffaqiyatli yangilandi.');
    }

    /**
     * Imtihon o'chirish
     */
    public function examDestroy(LmsExam $exam)
    {
        // Urinishlar borligini tekshirish
        if ($exam->attempts()->count() > 0) {
            return back()->with('error', 'Bu imtihonda urinishlar mavjud, o\'chirib bo\'lmaydi.');
        }

        $exam->questions()->delete();
        $exam->delete();

        return redirect()->route('dean.schedule.exams')
            ->with('success', 'Imtihon muvaffaqiyatli o\'chirildi.');
    }

    /**
     * Baholar
     */
    public function gradesIndex(Request $request)
    {
        $faculty = $this->getDeanFaculty();
        $facultyId = $faculty?->id;

        $groups = Group::when($facultyId, fn($q) => $q->where('faculty_id', $facultyId))
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('dean.grades.index', compact('groups', 'faculty'));
    }

    /**
     * GPA statistikasi
     */
    public function gradesGpa()
    {
        $faculty = $this->getDeanFaculty();
        $facultyId = $faculty?->id;

        // GPA ma'lumotlari hozircha mavjud emas
        $gpaStats = collect([]);

        return view('dean.grades.gpa', compact('gpaStats', 'faculty'));
    }

    /**
     * Qayta topshirish
     */
    public function gradesRetakes()
    {
        $faculty = $this->getDeanFaculty();
        return view('dean.grades.retakes', compact('faculty'));
    }

    /**
     * Davomat
     */
    public function attendanceIndex(Request $request)
    {
        $faculty = $this->getDeanFaculty();
        $facultyId = $faculty?->id;

        $groups = Group::when($facultyId, fn($q) => $q->where('faculty_id', $facultyId))
            ->where('is_active', true)
            ->withCount(['students' => fn($q) => $q->where('status', 'active')])
            ->orderBy('name')
            ->get();

        return view('dean.attendance.index', compact('groups', 'faculty'));
    }

    /**
     * Stipendiatlar
     */
    public function scholarshipIndex()
    {
        $faculty = $this->getDeanFaculty();
        $facultyId = $faculty?->id;

        // Stipendiya ma'lumotlari hozircha mavjud emas
        $scholars = Student::when($facultyId, fn($q) => $q->where('faculty_id', $facultyId))
            ->where('status', 'active')
            ->with('group')
            ->orderBy('last_name')
            ->paginate(20);

        return view('dean.scholarship.index', compact('scholars', 'faculty'));
    }

    /**
     * Stipendiya arizalari
     */
    public function scholarshipApplications()
    {
        $faculty = $this->getDeanFaculty();
        return view('dean.scholarship.applications', compact('faculty'));
    }

    /**
     * Talabalar hisoboti
     */
    public function reportsStudents()
    {
        $faculty = $this->getDeanFaculty();
        $facultyId = $faculty?->id;

        $stats = [
            'total' => Student::when($facultyId, fn($q) => $q->where('faculty_id', $facultyId))->count(),
            'active' => Student::when($facultyId, fn($q) => $q->where('faculty_id', $facultyId))
                ->where('status', 'active')->count(),
            'academic_leave' => Student::when($facultyId, fn($q) => $q->where('faculty_id', $facultyId))
                ->where('status', 'academic_leave')->count(),
            'expelled' => Student::when($facultyId, fn($q) => $q->where('faculty_id', $facultyId))
                ->where('status', 'expelled')->count(),
        ];

        $byCourse = Student::when($facultyId, fn($q) => $q->where('faculty_id', $facultyId))
            ->where('status', 'active')
            ->select('course', DB::raw('count(*) as count'))
            ->groupBy('course')
            ->orderBy('course')
            ->get();

        return view('dean.reports.students', compact('stats', 'byCourse', 'faculty'));
    }

    /**
     * O'zlashtirish hisoboti
     */
    public function reportsGrades()
    {
        $faculty = $this->getDeanFaculty();
        return view('dean.reports.grades', compact('faculty'));
    }

    /**
     * Davomat hisoboti
     */
    public function reportsAttendance()
    {
        $faculty = $this->getDeanFaculty();
        return view('dean.reports.attendance', compact('faculty'));
    }

    /**
     * E'lonlar
     */
    public function announcementsIndex()
    {
        $faculty = $this->getDeanFaculty();
        return view('dean.announcements.index', compact('faculty'));
    }

    /**
     * Sozlamalar
     */
    public function settings()
    {
        $faculty = $this->getDeanFaculty();
        return view('dean.settings', compact('faculty'));
    }

    /**
     * Guruhga kurator biriktirish
     */
    public function updateCurator(Request $request, Group $group)
    {
        $faculty = $this->getDeanFaculty();

        // Guruh fakultetga tegishli ekanligini tekshirish
        if ($faculty && $group->faculty_id !== $faculty->id) {
            return back()->with('error', 'Bu guruh sizning fakultetingizga tegishli emas.');
        }

        $request->validate([
            'curator_id' => 'nullable|exists:employees,id'
        ]);

        $curatorName = null;

        if ($request->curator_id) {
            $employee = Employee::find($request->curator_id);
            if ($employee) {
                // curator_id users jadvaliga bog'langan, shuning uchun user_id ishlatamiz
                if (!$employee->user_id) {
                    return back()->with('error', 'Bu xodimning tizimda foydalanuvchi hisobi yo\'q. Avval foydalanuvchi yaratish kerak.');
                }
                $group->curator_id = $employee->user_id;
                $curatorName = $employee->full_name;
            }
        } else {
            $group->curator_id = null;
        }

        $group->save();

        return back()->with('success',
            $curatorName
                ? "Kurator muvaffaqiyatli tayinlandi: {$curatorName}"
                : "Kurator olib tashlandi"
        );
    }
}
