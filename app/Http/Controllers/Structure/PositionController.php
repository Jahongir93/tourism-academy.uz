<?php

namespace App\Http\Controllers\Structure;

use App\Http\Controllers\Controller;
use App\Models\Position;
use App\Models\OrgUnitPosition;
use App\Models\StaffAllocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PositionController extends Controller
{
    public function index(Request $request)
    {
        $query = Position::query();
        
        if ($request->has('category')) {
            $query->where('category', $request->category);
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
        
        $positions = $query->orderBy('category')
            ->orderBy('level')
            ->orderBy('name_uz')
            ->paginate(20);
            
        $statistics = [
            'total_positions' => Position::count(),
            'leadership' => Position::where('category', 'leadership')->count(),
            'academic' => Position::where('category', 'academic')->count(),
            'administrative' => Position::where('category', 'administrative')->count(),
            'support' => Position::where('category', 'support')->count(),
        ];
        
        return view('structure.positions.index', compact('positions', 'statistics'));
    }

    public function show(Position $position)
    {
        $position->load(['reportsTo', 'subordinates']);
        
        $assignments = OrgUnitPosition::where('position_id', $position->id)
            ->with(['employee', 'appointmentOrder'])
            ->where('is_active', true)
            ->get();
            
        $allocations = StaffAllocation::where('position_id', $position->id)
            ->get();
            
        $stats = [
            'total_allocated' => $allocations->sum('allocated_count'),
            'total_filled' => $allocations->sum('filled_count'),
            'total_vacancy' => $allocations->sum('vacancy_count'),
            'active_assignments' => $assignments->count(),
        ];
        
        return view('structure.positions.show', compact('position', 'assignments', 'allocations', 'stats'));
    }

    public function create()
    {
        $positions = Position::where('category', 'leadership')
            ->orWhere('level', '<', 3)
            ->get();
            
        return view('structure.positions.create', compact('positions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:positions,code',
            'name_uz' => 'required|string|max:255',
            'name_ru' => 'nullable|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'category' => 'required|in:leadership,academic,administrative,support',
            'level' => 'required|integer|min:1|max:10',
            'salary_grade' => 'nullable|string|max:50',
            'requirements' => 'nullable|array',
            'responsibilities' => 'nullable|array',
            'reports_to' => 'nullable|array',
            'reports_to.*' => 'exists:positions,id',
        ]);
        
        DB::beginTransaction();
        try {
            $position = Position::create($validated);
            
            // Set up reporting relationships
            if (!empty($validated['reports_to'])) {
                foreach ($validated['reports_to'] as $reportsToId) {
                    $position->reportsTo()->attach($reportsToId, [
                        'hierarchy_type' => 'direct'
                    ]);
                }
            }
            
            DB::commit();
            
            return redirect()->route('structure.positions.show', $position)
                ->with('success', 'Lavozim muvaffaqiyatli yaratildi!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Xatolik yuz berdi: ' . $e->getMessage());
        }
    }

    public function edit(Position $position)
    {
        $positions = Position::where('id', '!=', $position->id)
            ->where(function($q) use ($position) {
                $q->where('category', 'leadership')
                  ->orWhere('level', '<', $position->level);
            })
            ->get();
            
        $position->load('reportsTo');
        
        return view('structure.positions.edit', compact('position', 'positions'));
    }

    public function update(Request $request, Position $position)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:positions,code,' . $position->id,
            'name_uz' => 'required|string|max:255',
            'name_ru' => 'nullable|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'category' => 'required|in:leadership,academic,administrative,support',
            'level' => 'required|integer|min:1|max:10',
            'salary_grade' => 'nullable|string|max:50',
            'requirements' => 'nullable|array',
            'responsibilities' => 'nullable|array',
            'is_active' => 'boolean',
            'reports_to' => 'nullable|array',
            'reports_to.*' => 'exists:positions,id',
        ]);
        
        DB::beginTransaction();
        try {
            $position->update($validated);
            
            // Update reporting relationships
            if (isset($validated['reports_to'])) {
                $position->reportsTo()->sync([]);
                foreach ($validated['reports_to'] as $reportsToId) {
                    $position->reportsTo()->attach($reportsToId, [
                        'hierarchy_type' => 'direct'
                    ]);
                }
            }
            
            DB::commit();
            
            return redirect()->route('structure.positions.show', $position)
                ->with('success', 'Lavozim ma\'lumotlari yangilandi!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Xatolik yuz berdi: ' . $e->getMessage());
        }
    }

    public function destroy(Position $position)
    {
        if ($position->orgUnitPositions()->exists() || $position->staffAllocations()->exists()) {
            return back()->with('error', 'Bu lavozimda xodimlar yoki shtat birliklari mavjud!');
        }
        
        $position->delete();
        
        return redirect()->route('structure.positions.index')
            ->with('success', 'Lavozim o\'chirildi!');
    }

    public function hierarchy()
    {
        $positions = Position::with(['reportsTo', 'subordinates'])
            ->where('category', 'leadership')
            ->orWhere('level', '<=', 3)
            ->get();
            
        return view('structure.positions.hierarchy', compact('positions'));
    }
}