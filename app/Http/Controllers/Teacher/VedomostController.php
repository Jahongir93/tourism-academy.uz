<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class VedomostController extends Controller
{
    public function create()
    {
        return view('teacher.vedomost.create');
    }

    public function store(Request $request)
    {
        // Store logic
        return redirect()->route('teacher.vedomost.list')
            ->with('success', 'Vedmost yaratildi!');
    }

    public function list()
    {
        $vedomosts = [];
        return view('teacher.vedomost.list', compact('vedomosts'));
    }

    public function fill($id)
    {
        $vedomost = null;
        return view('teacher.vedomost.fill', compact('vedomost'));
    }

    public function storeFill(Request $request, $id)
    {
        // Store fill logic
        return redirect()->route('teacher.vedomost.list')
            ->with('success', 'Vedmost to\'ldirildi!');
    }

    public function submit($id)
    {
        $vedomost = null;
        return view('teacher.vedomost.submit', compact('vedomost'));
    }

    public function processSubmit(Request $request, $id)
    {
        // Submit logic
        return redirect()->route('teacher.vedomost.list')
            ->with('success', 'Vedmost topshirildi!');
    }

    public function view($id)
    {
        $vedomost = null;
        return view('teacher.vedomost.view', compact('vedomost'));
    }

    public function export($id)
    {
        // Export logic
        return response()->download('vedomost.pdf');
    }
}