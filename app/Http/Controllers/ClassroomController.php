<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use Illuminate\Http\Request;

/**
 * Xonalar nazorati — classroom management (O'quv jarayoni).
 * Schedule (dars jadvali) xonalar shu yerdan boshqariladi.
 */
class ClassroomController extends Controller
{
    public function index(Request $request)
    {
        $query = Classroom::query()->orderBy('name');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('name', 'like', "%{$s}%")->orWhere('code', 'like', "%{$s}%"));
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $classrooms = $query->paginate(20)->withQueryString();
        $stats = [
            'total'    => Classroom::count(),
            'active'   => Classroom::where('is_active', true)->count(),
            'capacity' => (int) Classroom::sum('capacity'),
        ];

        return view('classrooms.index', compact('classrooms', 'stats'));
    }

    public function create()
    {
        return view('classrooms.form', ['classroom' => new Classroom(['is_active' => true])]);
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        Classroom::create($data);

        return redirect()->route('classrooms.index')->with('success', 'Xona muvaffaqiyatli qo\'shildi!');
    }

    public function edit(Classroom $classroom)
    {
        return view('classrooms.form', compact('classroom'));
    }

    public function update(Request $request, Classroom $classroom)
    {
        $data = $this->validateData($request, $classroom->id);
        $classroom->update($data);

        return redirect()->route('classrooms.index')->with('success', 'Xona ma\'lumotlari yangilandi!');
    }

    public function destroy(Classroom $classroom)
    {
        $classroom->delete();

        return redirect()->route('classrooms.index')->with('success', 'Xona o\'chirildi!');
    }

    private function validateData(Request $request, ?int $ignoreId = null): array
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'code'           => 'nullable|string|max:50|unique:classrooms,code' . ($ignoreId ? ",{$ignoreId}" : ''),
            'floor'          => 'nullable|integer|min:0|max:50',
            'capacity'       => 'nullable|integer|min:0|max:1000',
            'type'           => 'nullable|in:lecture,practice,lab,seminar,other',
            'notes'          => 'nullable|string',
            'has_projector'  => 'nullable|boolean',
            'has_computer'   => 'nullable|boolean',
            'has_whiteboard' => 'nullable|boolean',
            'is_active'      => 'nullable|boolean',
        ]);

        foreach (['has_projector', 'has_computer', 'has_whiteboard', 'is_active'] as $b) {
            $validated[$b] = $request->boolean($b);
        }

        return $validated;
    }
}
