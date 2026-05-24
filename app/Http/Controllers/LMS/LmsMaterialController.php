<?php

namespace App\Http\Controllers\LMS;

use App\Http\Controllers\Controller;
use App\Models\LmsMaterial;
use App\Models\Subject;
use App\Models\User;
use App\Models\LmsContentView;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class LmsMaterialController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = LmsMaterial::with(['subject', 'teacher'])
            ->where('is_active', true);
        
        // Filter by subject
        if ($request->has('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }
        
        // Filter by teacher
        if ($request->has('teacher_id')) {
            $query->where('teacher_id', $request->teacher_id);
        }
        
        // Filter by material type
        if ($request->has('material_type')) {
            $query->where('material_type', $request->material_type);
        }
        
        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        $materials = $query->orderBy('order_number')
                          ->orderBy('created_at', 'desc')
                          ->paginate(12);
        
        $subjects = Subject::where('is_active', true)->orderBy('name_uz')->get();
        $teachers = User::whereHas('roles', function($q) {
            $q->where('name', 'Teacher');
        })->orderBy('name')->get();
        
        return view('lms.materials.index', compact('materials', 'subjects', 'teachers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $subjects = Subject::where('is_active', true)->orderBy('name_uz')->get();
        
        return view('lms.materials.create', compact('subjects'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'material_type' => 'required|in:presentation,document,spreadsheet,pdf,other',
            'week_number' => 'nullable|integer|min:1|max:16',
            'order_number' => 'nullable|integer|min:0',
            'file' => 'required|file|max:51200', // 50MB max
        ]);
        
        $user = Auth::user();

        if (!$user->hasRole('Teacher') && !$user->hasRole('SuperAdmin') && !$user->hasRole('admin')) {
            return back()->with('error', 'Siz o\'qituvchi sifatida ro\'yxatdan o\'tmagan');
        }
        
        // Handle file upload
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('lms/materials', $fileName, 'public');
            
            $validated['file_path'] = $filePath;
            $validated['file_name'] = $file->getClientOriginalName();
            $validated['file_type'] = $file->getClientOriginalExtension();
            $validated['file_size'] = $file->getSize();
        }

        $validated['teacher_id'] = $user->id;
        
        LmsMaterial::create($validated);
        
        return redirect()->route('lms.materials.index')
                        ->with('success', 'Material muvaffaqiyatli yuklandi!');
    }

    /**
     * Display the specified resource.
     */
    public function show(LmsMaterial $material)
    {
        $material->load(['subject', 'teacher']);
        
        // Record view
        $user = Auth::user();
        $view = LmsContentView::firstOrCreate(
            [
                'user_id' => $user->id,
                'viewable_type' => LmsMaterial::class,
                'viewable_id' => $material->id,
            ],
            [
                'last_viewed_at' => now(),
                'view_count' => 0
            ]
        );
        
        $view->recordView();
        
        // Get related materials
        $relatedMaterials = LmsMaterial::where('subject_id', $material->subject_id)
            ->where('id', '!=', $material->id)
            ->where('is_active', true)
            ->limit(5)
            ->get();
        
        return view('lms.materials.show', compact('material', 'relatedMaterials'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(LmsMaterial $material)
    {
        $subjects = Subject::where('is_active', true)->orderBy('name_uz')->get();
        
        return view('lms.materials.edit', compact('material', 'subjects'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, LmsMaterial $material)
    {
        $validated = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'material_type' => 'required|in:presentation,document,spreadsheet,pdf,other',
            'week_number' => 'nullable|integer|min:1|max:16',
            'order_number' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
            'file' => 'nullable|file|max:51200', // 50MB max
        ]);
        
        // Handle file upload if new file provided
        if ($request->hasFile('file')) {
            // Delete old file
            if ($material->file_path) {
                Storage::disk('public')->delete($material->file_path);
            }
            
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('lms/materials', $fileName, 'public');
            
            $validated['file_path'] = $filePath;
            $validated['file_name'] = $file->getClientOriginalName();
            $validated['file_type'] = $file->getClientOriginalExtension();
            $validated['file_size'] = $file->getSize();
        }
        
        $material->update($validated);
        
        return redirect()->route('lms.materials.show', $material)
                        ->with('success', 'Material muvaffaqiyatli yangilandi!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LmsMaterial $material)
    {
        // Delete file
        if ($material->file_path) {
            Storage::disk('public')->delete($material->file_path);
        }
        
        $material->delete();
        
        return redirect()->route('lms.materials.index')
                        ->with('success', 'Material muvaffaqiyatli o\'chirildi!');
    }
    
    /**
     * Download the material file
     */
    public function download(LmsMaterial $material)
    {
        if (!$material->file_path || !Storage::disk('public')->exists($material->file_path)) {
            return back()->with('error', 'Fayl topilmadi!');
        }
        
        $material->incrementDownloadCount();
        
        return Storage::disk('public')->download($material->file_path, $material->file_name);
    }
}