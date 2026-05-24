<?php

namespace App\Http\Controllers\Structure;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\Position;
use App\Models\OrgUnitPosition;
use App\Models\StaffAllocation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DepartmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Department::with(['faculty', 'head']);
        
        if ($request->has('faculty_id')) {
            $query->where('faculty_id', $request->faculty_id);
        }
        
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }
        
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name_uz', 'like', "%{$search}%")
                  ->orWhere('name_ru', 'like', "%{$search}%")
                  ->orWhere('name_en', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }
        
        $departments = $query->orderBy('name_uz')->paginate(20);
        $faculties = Faculty::all();
        
        return view('structure.departments.index', compact('departments', 'faculties'));
    }

    public function show(Department $department)
    {
        $department->load([
            'faculty',
            'head',
            'specialties',
            'positions.employee',
            'positions.position',
            'staffAllocations.position'
        ]);
        
        $stats = [
            'specialties_count' => $department->specialties->count(),
            'staff_count' => $department->positions()->where('is_active', true)->count(),
            'vacancy_count' => $department->staffAllocations->sum('vacancy_count'),
        ];
        
        return view('structure.departments.show', compact('department', 'stats'));
    }

    public function create()
    {
        $faculties = Faculty::all();
        $heads = User::whereHas('roles', function($q) {
            $q->whereIn('name', ['Teacher', 'SuperAdmin']);
        })->get();
        
        return view('structure.departments.create', compact('faculties', 'heads'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'faculty_id' => 'required|exists:faculties,id',
            'code' => 'required|string|unique:departments,code',
            'name_uz' => 'required|string|max:255',
            'name_ru' => 'nullable|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'short_name' => 'nullable|string|max:50',
            'type' => 'nullable|in:umumkasbiy,ixtisoslik,umumtalim',
            'head_id' => 'nullable|exists:users,id',
            'room_number' => 'nullable|string|max:50',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'established_date' => 'nullable|date',
            'staff_capacity' => 'nullable|integer|min:0',
        ]);
        
        DB::beginTransaction();
        try {
            // Set name field from name_uz for backward compatibility
            $validated['name'] = $validated['name_uz'];

            $department = Department::create($validated);
            
            // Create head position if head is selected
            if ($request->head_id) {
                $headPosition = Position::firstOrCreate(
                    ['code' => 'DEPT_HEAD'],
                    [
                        'name' => 'Kafedra mudiri',  // For backward compatibility
                        'name_uz' => 'Kafedra mudiri',
                        'name_ru' => 'Заведующий кафедрой',
                        'name_en' => 'Department Head',
                        'category' => 'leadership',
                        'level' => 2,
                    ]
                );

                // employee_id ni null qilib qo'yamiz, chunki employees jadvali bo'sh
                OrgUnitPosition::create([
                    'org_unit_type' => 'department',
                    'org_unit_id' => $department->id,
                    'position_id' => $headPosition->id,
                    'position_name' => $headPosition->name_uz ?? 'Kafedra mudiri',
                    'employee_id' => null, // employees jadvali bo'sh
                    'appointment_type' => 'main',
                    'appointment_date' => now(),
                    'workload_percentage' => 100,
                    'is_active' => true,
                ]);
            }
            
            DB::commit();
            
            return redirect()->route('structure.departments.show', $department)
                ->with('success', 'Kafedra muvaffaqiyatli yaratildi!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Xatolik yuz berdi: ' . $e->getMessage());
        }
    }

    public function edit(Department $department)
    {
        $faculties = Faculty::all();
        $heads = User::whereHas('roles', function($q) {
            $q->whereIn('name', ['Teacher', 'SuperAdmin']);
        })->get();
        
        return view('structure.departments.edit', compact('department', 'faculties', 'heads'));
    }

    public function update(Request $request, Department $department)
    {
        $validated = $request->validate([
            'faculty_id' => 'required|exists:faculties,id',
            'code' => 'required|string|unique:departments,code,' . $department->id,
            'name_uz' => 'required|string|max:255',
            'name_ru' => 'nullable|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'short_name' => 'nullable|string|max:50',
            'type' => 'nullable|in:umumkasbiy,ixtisoslik,umumtalim',
            'head_id' => 'nullable|exists:users,id',
            'room_number' => 'nullable|string|max:50',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'established_date' => 'nullable|date',
            'staff_capacity' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        // Set name field from name_uz for backward compatibility
        $validated['name'] = $validated['name_uz'];

        $department->update($validated);
        
        return redirect()->route('structure.departments.show', $department)
            ->with('success', 'Kafedra ma\'lumotlari yangilandi!');
    }

    public function destroy(Department $department)
    {
        if ($department->specialties()->exists()) {
            return back()->with('error', 'Bu kafedrada yo\'nalishlar mavjud!');
        }
        
        $department->delete();
        
        return redirect()->route('structure.departments.index')
            ->with('success', 'Kafedra o\'chirildi!');
    }

    public function staffing(Department $department)
    {
        $staffAllocations = $department->staffAllocations()
            ->with('position')
            ->get();
            
        $positions = $department->positions()
            ->with(['position', 'employee'])
            ->where('is_active', true)
            ->get();
            
        $availablePositions = Position::whereIn('category', ['academic', 'administrative', 'support', 'leadership'])
            ->orderBy('category')
            ->orderBy('name_uz')
            ->get();
            
        return view('structure.departments.staffing', compact('department', 'staffAllocations', 'positions', 'availablePositions'));
    }

    public function allocateStaff(Request $request, Department $department)
    {
        $validated = $request->validate([
            'position_id' => 'required|exists:positions,id',
            'allocated_count' => 'required|integer|min:1',
            'budget_allocated' => 'nullable|numeric|min:0',
        ]);
        
        $allocation = StaffAllocation::updateOrCreate(
            [
                'org_unit_type' => 'department',
                'org_unit_id' => $department->id,
                'position_id' => $validated['position_id'],
            ],
            [
                'allocated_count' => $validated['allocated_count'],
                'budget_allocated' => $validated['budget_allocated'],
            ]
        );
        
        $allocation->updateVacancyCount();
        
        return redirect()->route('structure.departments.staffing', $department)
            ->with('success', 'Shtat birligi muvaffaqiyatli belgilandi!');
    }
}