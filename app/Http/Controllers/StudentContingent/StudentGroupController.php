<?php

namespace App\Http\Controllers\StudentContingent;

use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Models\Faculty;
use App\Models\Department;
use App\Models\Specialty;
use App\Models\User;
use App\Models\Student;
use App\Models\TeacherSubject;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class StudentGroupController extends Controller
{
    public function index(Request $request)
    {
        $query = Group::with(['department.faculty', 'students', 'journalEntries.subject']);

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($request->has('department_id') && $request->department_id) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->has('course') && $request->course) {
            $query->where('course', $request->course);
        }

        if ($request->has('education_type') && $request->education_type) {
            $query->where('education_type', $request->education_type);
        }

        $groups = $query->orderBy('name')->paginate(20);

        $faculties = Faculty::all();
        $departments = Department::all();

        $statistics = [
            'total_groups' => Group::count(),
            'active_groups' => Group::count(), // All groups are active by default
            'course_1' => Group::where('course', 1)->count(),
            'course_2' => Group::where('course', 2)->count(),
            'course_3' => Group::where('course', 3)->count(),
            'course_4' => Group::where('course', 4)->count(),
            'total_students' => Student::whereNotNull('group_id')->count(),
            'max_capacity' => Group::sum('students_count') + 100, // Approximate capacity
        ];

        return view('student-contingent.groups.index', compact('groups', 'faculties', 'departments', 'statistics'));
    }

    public function create()
    {
        $faculties = Faculty::all();
        $departments = Department::all();
        $specialties = Specialty::all();

        // Get only teachers/curators - use role filtering
        $curators = User::whereHas('roles', function($query) {
            $query->whereIn('name', ['Teacher', 'Curator', 'Admin']);
        })->orderBy('name')->get();

        // Fallback if no users with roles
        if ($curators->isEmpty()) {
            $curators = User::orderBy('name')->get();
        }

        return view('student-contingent.groups.create', compact('faculties', 'departments', 'specialties', 'curators'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:groups',
                'code' => 'nullable|string|max:50|unique:groups',
                'faculty_id' => 'nullable|exists:faculties,id',
                'department_id' => 'nullable|exists:departments,id',
                'specialty_id' => 'nullable|exists:specialties,id',
                'course' => 'required|integer|min:1|max:6',
                'semester' => 'nullable|integer|min:1|max:12',
                'academic_year' => 'nullable|string|max:20',
                'education_type' => 'nullable|in:bakalavr,magistr,doktorantura',
                'education_form' => 'nullable|in:kunduzgi,sirtqi,kechki',
                'language' => 'nullable|in:uz,ru,en',
                'max_students' => 'nullable|integer|min:1|max:100',
                'curator_id' => 'nullable|exists:users,id',
                'description' => 'nullable|string',
            ], [
                'name.required' => 'Guruh nomi kiritilishi shart.',
                'name.unique' => 'Bu guruh nomi allaqachon mavjud.',
                'course.required' => 'Kurs tanlanishi shart.',
                'education_type.in' => 'Tanlangan ta\'lim turi noto\'g\'ri.',
                'education_form.in' => 'Tanlangan ta\'lim shakli noto\'g\'ri.',
            ]);

            // Auto-generate code if not provided
            if (empty($validated['code'])) {
                $validated['code'] = $this->generateGroupCode($validated['name'], $validated['course'] ?? 1);
            }

            $validated['students_count'] = 0;
            $validated['is_active'] = true;

            // Map education_form to education_type (database expects kunduzgi/sirtqi/kechki)
            if (isset($validated['education_form'])) {
                $validated['education_type'] = $validated['education_form'];
            }
            unset($validated['education_form']);

            $group = Group::create($validated);

            return redirect()->route('student-contingent.groups.index')
                ->with('success', 'Guruh muvaffaqiyatli yaratildi.');

        } catch (\Exception $e) {
            \Log::error('Group creation error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);

            return back()->withInput()
                ->withErrors(['error' => 'Guruh yaratishda xatolik: ' . $e->getMessage()]);
        }
    }

    /**
     * Generate group code from name
     */
    private function generateGroupCode($name, $course)
    {
        // Extract first letters or numbers from name
        $code = strtoupper(preg_replace('/[^A-Z0-9]/i', '', $name));

        // Limit to 10 characters
        $code = substr($code, 0, 10);

        // Add course number
        $code .= '-' . $course;

        // Make unique if needed
        $originalCode = $code;
        $counter = 1;
        while (Group::where('code', $code)->exists()) {
            $code = $originalCode . '-' . $counter;
            $counter++;
        }

        return $code;
    }

    public function show(Group $group)
    {
        $group->load(['department.faculty', 'students', 'journalEntries.subject', 'journalEntries.teacher']);

        $students = $group->students()
            ->orderBy('id')
            ->paginate(30);

        // Get assigned subjects with teachers from journal entries
        $assignedSubjects = $group->journalEntries()
            ->with(['subject', 'teacher'])
            ->get();

        // Get teacher-subject assignments from TeacherSubject where this group is included
        $teacherSubjectAssignments = TeacherSubject::where('status', 'active')
            ->where(function($query) use ($group) {
                $query->whereJsonContains('group_ids', $group->id)
                      ->orWhereJsonContains('group_ids', (string)$group->id);
            })
            ->with(['teacher', 'subject'])
            ->get();

        // Get available teachers (employees with teacher type)
        $availableTeachers = Employee::where('employee_type', 'teacher')
            ->where('status', 'active')
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        return view('student-contingent.groups.show', compact('group', 'students', 'assignedSubjects', 'teacherSubjectAssignments', 'availableTeachers'));
    }

    public function edit(Group $group)
    {
        $faculties = Faculty::all();
        $departments = Department::all();
        $specialties = Specialty::all();

        // Get only teachers/curators - use role filtering
        $curators = User::whereHas('roles', function($query) {
            $query->whereIn('name', ['Teacher', 'Curator', 'Admin']);
        })->orderBy('name')->get();

        // Fallback if no users with roles
        if ($curators->isEmpty()) {
            $curators = User::orderBy('name')->get();
        }

        $students = $group->students;

        return view('student-contingent.groups.edit', compact('group', 'faculties', 'departments', 'specialties', 'curators', 'students'));
    }

    public function update(Request $request, Group $group)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:groups,name,' . $group->id,
            'code' => 'nullable|string|max:50|unique:groups,code,' . $group->id,
            'faculty_id' => 'nullable|exists:faculties,id',
            'department_id' => 'nullable|exists:departments,id',
            'specialty_id' => 'nullable|exists:specialties,id',
            'course' => 'required|integer|min:1|max:6',
            'semester' => 'nullable|integer|min:1|max:12',
            'academic_year' => 'nullable|string|max:20',
            'education_type' => 'nullable|in:bakalavr,magistr,doktorantura',
            'education_form' => 'nullable|in:kunduzgi,sirtqi,kechki',
            'language' => 'nullable|in:uz,ru,en',
            'max_students' => 'nullable|integer|min:1|max:100',
            'curator_id' => 'nullable|exists:users,id',
            'description' => 'nullable|string',
        ], [
            'name.required' => 'Guruh nomi kiritilishi shart.',
            'name.unique' => 'Bu guruh nomi allaqachon mavjud.',
            'course.required' => 'Kurs tanlanishi shart.',
            'education_type.in' => 'Tanlangan ta\'lim turi noto\'g\'ri.',
            'education_form.in' => 'Tanlangan ta\'lim shakli noto\'g\'ri.',
        ]);

        // Auto-generate code if not provided
        if (empty($validated['code']) && empty($group->code)) {
            $validated['code'] = $this->generateGroupCode($validated['name'], $validated['course'] ?? 1);
        }

        // Map education_form to education_type (database expects kunduzgi/sirtqi/kechki)
        if (isset($validated['education_form'])) {
            $validated['education_type'] = $validated['education_form'];
        }
        unset($validated['education_form']);

        $group->update($validated);

        return redirect()->route('student-contingent.groups.show', $group)
            ->with('success', 'Guruh ma\'lumotlari yangilandi.');
    }

    public function destroy(Group $group)
    {
        if ($group->students()->count() > 0) {
            return back()->with('error', 'Bu guruhda talabalar mavjud. Avval talabalarni boshqa guruhga o\'tkazing.');
        }

        $group->delete();

        return redirect()->route('student-contingent.groups.index')
            ->with('success', 'Guruh o\'chirildi.');
    }

    public function addStudents(Request $request, Group $group)
    {
        $validated = $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:students,id'
        ]);

        DB::transaction(function () use ($group, $validated) {
            foreach ($validated['student_ids'] as $studentId) {
                $student = Student::find($studentId);
                $student->update(['group_id' => $group->id]);
            }

            $group->update(['students_count' => $group->students()->count()]);
        });

        return back()->with('success', 'Talabalar guruhga qo\'shildi.');
    }

    public function removeStudent(Group $group, Student $student)
    {
        if ($student->group_id !== $group->id) {
            return back()->with('error', 'Talaba bu guruhda emas.');
        }

        DB::transaction(function () use ($group, $student) {
            $student->update(['group_id' => null]);
            $group->update(['students_count' => $group->students()->count()]);
        });

        return back()->with('success', 'Talaba guruhdan chiqarildi.');
    }

    public function exportStudents(Group $group)
    {
        $students = $group->students()
            ->with(['passport', 'address'])
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        $csvData = "№,Talaba ID,F.I.O,JSHSHIR,Telefon,Email\n";

        foreach ($students as $index => $student) {
            $csvData .= ($index + 1) . ',';
            $csvData .= $student->student_id . ',';
            $csvData .= $student->full_name_latin . ',';
            $csvData .= $student->jshshir . ',';
            $csvData .= $student->phone_primary . ',';
            $csvData .= $student->email . "\n";
        }

        $fileName = 'guruh_' . $group->name . '_talabalar_' . date('Y-m-d') . '.csv';

        return response($csvData)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
    }
}