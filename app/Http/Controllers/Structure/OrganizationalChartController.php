<?php

namespace App\Http\Controllers\Structure;

use App\Http\Controllers\Controller;
use App\Models\University;
use App\Models\Faculty;
use App\Models\Department;
use App\Models\Division;
use App\Models\Center;
use App\Models\Position;
use App\Models\OrgUnitPosition;
use Illuminate\Http\Request;

class OrganizationalChartController extends Controller
{
    public function index()
    {
        // Get university structure
        $university = University::first() ?? (object)[
            'id' => 1,
            'name_uz' => 'Toshkent Universiteti',
            'name_ru' => 'Ташкентский Университет',
            'name_en' => 'Tashkent University',
        ];
        
        $faculties = Faculty::with([
            'dean',
            'departments.head',
            'positions.employee',
        ])->active()->get();
        
        $divisions = Division::with([
            'head',
            'children',
            'positions.employee',
        ])->whereNull('parent_id')->active()->get();
        
        $centers = Center::with([
            'director',
            'positions.employee',
        ])->active()->get();
        
        $statistics = [
            'faculties' => Faculty::count(),
            'departments' => Department::count(),
            'divisions' => Division::count(),
            'centers' => Center::count(),
            'total_staff' => OrgUnitPosition::where('is_active', true)->count(),
            'leadership_positions' => OrgUnitPosition::whereHas('position', function($q) {
                $q->where('category', 'leadership');
            })->where('is_active', true)->count(),
        ];
        
        return view('structure.organizational-chart.index', compact(
            'university',
            'faculties',
            'divisions',
            'centers',
            'statistics'
        ));
    }

    public function facultyChart(Faculty $faculty)
    {
        $faculty->load([
            'dean',
            'departments' => function($q) {
                $q->with(['head', 'specialties', 'positions.employee']);
            },
            'positions' => function($q) {
                $q->with(['position', 'employee'])->where('is_active', true);
            },
        ]);
        
        return view('structure.organizational-chart.faculty', compact('faculty'));
    }

    public function divisionChart(Division $division)
    {
        $division->load([
            'head',
            'parent',
            'children' => function($q) {
                $q->with(['head', 'positions.employee']);
            },
            'positions' => function($q) {
                $q->with(['position', 'employee'])->where('is_active', true);
            },
        ]);
        
        return view('structure.organizational-chart.division', compact('division'));
    }

    public function exportChart(Request $request)
    {
        $type = $request->get('type', 'full');
        $format = $request->get('format', 'pdf');
        
        $data = $this->getChartData($type);
        
        if ($format === 'pdf') {
            $pdf = \PDF::loadView('structure.organizational-chart.export-pdf', $data);
            $pdf->setPaper('A3', 'landscape');
            return $pdf->download('org-chart-' . now()->format('Y-m-d') . '.pdf');
        } elseif ($format === 'image') {
            // Generate image using a chart library
            return response()->json(['message' => 'Image export will be implemented']);
        }
        
        return response()->json($data);
    }

    public function apiStructure()
    {
        $structure = [
            'university' => [
                'name' => 'Toshkent Universiteti',
                'rector' => $this->getRector(),
                'faculties' => $this->getFacultiesStructure(),
                'divisions' => $this->getDivisionsStructure(),
                'centers' => $this->getCentersStructure(),
            ]
        ];
        
        return response()->json($structure);
    }

    private function getRector()
    {
        $rectorPosition = OrgUnitPosition::where('org_unit_type', 'university')
            ->whereHas('position', function($q) {
                $q->where('code', 'RECTOR');
            })
            ->with('employee')
            ->where('is_active', true)
            ->first();
            
        return $rectorPosition ? [
            'name' => $rectorPosition->employee->name ?? 'Bo\'sh',
            'position' => 'Rektor',
        ] : null;
    }

    private function getFacultiesStructure()
    {
        return Faculty::with(['dean', 'departments.head'])
            ->active()
            ->get()
            ->map(function($faculty) {
                return [
                    'id' => $faculty->id,
                    'name' => $faculty->name,
                    'dean' => $faculty->dean->name ?? 'Bo\'sh',
                    'departments' => $faculty->departments->map(function($dept) {
                        return [
                            'id' => $dept->id,
                            'name' => $dept->name,
                            'head' => $dept->head->name ?? 'Bo\'sh',
                        ];
                    }),
                ];
            });
    }

    private function getDivisionsStructure()
    {
        return Division::with(['head', 'children.head'])
            ->whereNull('parent_id')
            ->active()
            ->get()
            ->map(function($division) {
                return [
                    'id' => $division->id,
                    'name' => $division->name,
                    'type' => $division->type,
                    'head' => $division->head->name ?? 'Bo\'sh',
                    'children' => $division->children->map(function($child) {
                        return [
                            'id' => $child->id,
                            'name' => $child->name,
                            'head' => $child->head->name ?? 'Bo\'sh',
                        ];
                    }),
                ];
            });
    }

    private function getCentersStructure()
    {
        return Center::with('director')
            ->active()
            ->get()
            ->map(function($center) {
                return [
                    'id' => $center->id,
                    'name' => $center->name,
                    'type' => $center->type,
                    'director' => $center->director->name ?? 'Bo\'sh',
                ];
            });
    }

    private function getChartData($type)
    {
        $data = ['title' => 'Tashkiliy tuzilma'];
        
        switch ($type) {
            case 'faculties':
                $data['items'] = $this->getFacultiesStructure();
                $data['type'] = 'faculties';
                break;
            case 'divisions':
                $data['items'] = $this->getDivisionsStructure();
                $data['type'] = 'divisions';
                break;
            case 'centers':
                $data['items'] = $this->getCentersStructure();
                $data['type'] = 'centers';
                break;
            default:
                $data['items'] = [
                    'faculties' => $this->getFacultiesStructure(),
                    'divisions' => $this->getDivisionsStructure(),
                    'centers' => $this->getCentersStructure(),
                ];
                $data['type'] = 'full';
                break;
        }
        
        return $data;
    }
}