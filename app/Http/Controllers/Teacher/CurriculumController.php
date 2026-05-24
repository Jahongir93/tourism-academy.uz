<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CurriculumController extends Controller
{
    public function view()
    {
        $curriculum = [];
        return view('teacher.curriculum.view', compact('curriculum'));
    }

    public function create()
    {
        return view('teacher.curriculum.create');
    }

    public function store(Request $request)
    {
        // Store logic
        return redirect()->route('teacher.curriculum.view')
            ->with('success', 'O\'quv reja yaratildi!');
    }

    public function edit()
    {
        $curriculum = null;
        return view('teacher.curriculum.edit', compact('curriculum'));
    }

    public function update(Request $request, $id)
    {
        // Update logic
        return redirect()->route('teacher.curriculum.view')
            ->with('success', 'O\'quv reja yangilandi!');
    }

    public function materials()
    {
        $materials = [];
        return view('teacher.curriculum.materials', compact('materials'));
    }

    public function uploadMaterial(Request $request)
    {
        // Upload logic
        return redirect()->route('teacher.curriculum.materials')
            ->with('success', 'Material yuklandi!');
    }

    public function deleteMaterial($id)
    {
        // Delete logic
        return redirect()->route('teacher.curriculum.materials')
            ->with('success', 'Material o\'chirildi!');
    }
}