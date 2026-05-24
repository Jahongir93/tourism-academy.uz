<?php

namespace App\Http\Controllers\Structure;

use App\Http\Controllers\Controller;
use App\Models\Faculty;
use App\Models\Department;
use App\Models\Position;
use App\Models\OrgUnitPosition;
use App\Models\StaffAllocation;
use App\Models\User;
use App\Models\Specialty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FacultyManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = Faculty::with(['dean', 'departments', 'specialties']);
        
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name_uz', 'like', "%{$search}%")
                  ->orWhere('name_ru', 'like', "%{$search}%")
                  ->orWhere('name_en', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }
        
        $faculties = $query->orderBy('order_number')->paginate(15);
        
        // Safe statistics gathering
        $totalStudents = 0;
        try {
            $totalStudents = DB::table('student_enrollments')
                ->where('is_active', true)->count();
        } catch (\Exception $e) {
            // Table doesn't exist yet
        }

        $totalTeachers = 0;
        try {
            $totalTeachers = OrgUnitPosition::where('org_unit_type', 'faculty')
                ->where('is_active', true)->count();
        } catch (\Exception $e) {
            // Table doesn't exist yet
        }

        $statistics = [
            'total_faculties' => Faculty::count(),
            'total_departments' => Department::safeCount(),
            'total_students' => $totalStudents,
            'total_teachers' => $totalTeachers,
        ];
        
        return view('structure.faculties.index', compact('faculties', 'statistics'));
    }

    public function show(Faculty $faculty)
    {
        $faculty->load([
            'departments.head',
            'specialties',
            'positions.employee',
            'positions.position',
            'staffAllocations.position'
        ]);
        
        $stats = [
            'departments_count' => $faculty->departments->count(),
            'specialties_count' => $faculty->specialties->count(),
            'students_count' => $faculty->students()->count(),
            'staff_count' => $faculty->positions()->count(),
            'vacancy_count' => $faculty->staffAllocations->sum('vacancy_count'),
        ];
        
        return view('structure.faculties.show', compact('faculty', 'stats'));
    }

    public function create()
    {
        $deans = User::whereHas('roles', function($q) {
            $q->whereIn('name', ['Teacher', 'SuperAdmin']);
        })->get();
        return view('structure.faculties.create', compact('deans'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:faculties,code',
            'name_uz' => 'required|string|max:255',
            'name_ru' => 'nullable|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'abbreviation' => 'nullable|string|max:20',
            'dean_user_id' => 'nullable|exists:users,id',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'room' => 'nullable|string|max:50',
            'website' => 'nullable|url',
            'established_date' => 'nullable|date',
            'student_capacity' => 'nullable|integer|min:0',
            'teacher_capacity' => 'nullable|integer|min:0',
            'state_funded_places' => 'nullable|integer|min:0',
            'contract_places' => 'nullable|integer|min:0',
        ]);
        
        DB::beginTransaction();
        try {
            $faculty = Faculty::create($validated);
            
            // Create dean position if dean is selected
            if ($request->dean_user_id) {
                // Check if user has an employee record
                $user = User::find($request->dean_user_id);
                $employee = $user ? $user->employee : null;

                if ($employee) {
                    $deanPosition = Position::firstOrCreate(
                        ['code' => 'DEAN'],
                        [
                            'name_uz' => 'Dekan',
                            'name_ru' => 'Декан',
                            'name_en' => 'Dean',
                            'category' => 'leadership',
                            'level' => 1,
                        ]
                    );

                    OrgUnitPosition::create([
                        'org_unit_type' => 'faculty',
                        'org_unit_id' => $faculty->id,
                        'position_id' => $deanPosition->id,
                        'employee_id' => $employee->id,
                        'appointment_type' => 'main',
                        'appointment_date' => now(),
                        'workload_percentage' => 100,
                        'is_active' => true,
                    ]);
                }
            }
            
            DB::commit();
            
            return redirect()->route('structure.faculties.show', $faculty)
                ->with('success', 'Fakultet muvaffaqiyatli yaratildi!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Xatolik yuz berdi: ' . $e->getMessage());
        }
    }

    public function edit(Faculty $faculty)
    {
        $deans = User::whereHas('roles', function($q) {
            $q->whereIn('name', ['Teacher', 'SuperAdmin']);
        })->get();
        return view('structure.faculties.edit', compact('faculty', 'deans'));
    }

    public function update(Request $request, Faculty $faculty)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:faculties,code,' . $faculty->id,
            'name_uz' => 'required|string|max:255',
            'name_ru' => 'nullable|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'abbreviation' => 'nullable|string|max:20',
            'dean_user_id' => 'nullable|exists:users,id',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'room' => 'nullable|string|max:50',
            'website' => 'nullable|url',
            'established_date' => 'nullable|date',
            'student_capacity' => 'nullable|integer|min:0',
            'teacher_capacity' => 'nullable|integer|min:0',
            'state_funded_places' => 'nullable|integer|min:0',
            'contract_places' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);
        
        $faculty->update($validated);
        
        return redirect()->route('structure.faculties.show', $faculty)
            ->with('success', 'Fakultet ma\'lumotlari yangilandi!');
    }

    public function destroy(Faculty $faculty)
    {
        if ($faculty->departments()->exists() || $faculty->specialties()->exists()) {
            return back()->with('error', 'Bu fakultetda kafedra yoki yo\'nalishlar mavjud!');
        }
        
        $faculty->delete();
        
        return redirect()->route('structure.faculties.index')
            ->with('success', 'Fakultet o\'chirildi!');
    }

    public function departments(Faculty $faculty)
    {
        $departments = $faculty->departments()
            ->with(['head', 'specialties'])
            ->paginate(20);
            
        return view('structure.faculties.departments', compact('faculty', 'departments'));
    }

    public function createDepartment(Faculty $faculty)
    {
        $faculties = Faculty::all();
        $heads = User::whereHas('roles', function($q) {
            $q->whereIn('name', ['Teacher', 'SuperAdmin']);
        })->get();
        return view('structure.departments.create', compact('faculty', 'faculties', 'heads'));
    }

    public function staffing(Faculty $faculty)
    {
        $staffAllocations = $faculty->staffAllocations()
            ->with('position')
            ->get();
            
        $positions = $faculty->positions()
            ->with(['position', 'employee'])
            ->where('is_active', true)
            ->get();
            
        return view('structure.faculties.staffing', compact('faculty', 'staffAllocations', 'positions'));
    }

    public function allocateStaff(Request $request, Faculty $faculty)
    {
        $validated = $request->validate([
            'position_id' => 'required|exists:positions,id',
            'allocated_count' => 'required|integer|min:1',
            'budget_allocated' => 'nullable|numeric|min:0',
        ]);
        
        // Faculty division_id sifatida ishlatiladi
        $allocation = StaffAllocation::updateOrCreate(
            [
                'division_id' => $faculty->id,
                'position_id' => $validated['position_id'],
            ],
            [
                'allocated_count' => $validated['allocated_count'],
                'rate' => $validated['budget_allocated'] ?? 0,
                'status' => 'active',
                'effective_from' => now(),
            ]
        );

        $allocation->updateVacantCount();
        
        return redirect()->route('structure.faculties.staffing', $faculty)
            ->with('success', 'Shtat birligi muvaffaqiyatli belgilandi!');
    }
}