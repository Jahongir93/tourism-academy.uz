<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\TeacherSubject;
use App\Models\Material;
use App\Models\Teacher;
use App\Models\Employee;
use App\Models\LmsCourse;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MaterialController extends Controller
{
    /**
     * Get teacher/employee ID for current user
     */
    private function getTeacherId()
    {
        $user = Auth::user();
        $teacher = Teacher::where('user_id', $user->id)->first();
        $employee = Employee::where('user_id', $user->id)->first();

        return [
            'teacher_id' => $teacher ? $teacher->id : null,
            'employee_id' => $employee ? $employee->id : null,
            'user_id' => $user->id,
            'teacher' => $teacher,
            'employee' => $employee,
        ];
    }

    /**
     * Display list of all materials
     */
    public function index(Request $request)
    {
        $ids = $this->getTeacherId();

        if (!$ids['teacher_id'] && !$ids['employee_id']) {
            return redirect()->route('teacher.dashboard')
                ->with('error', 'O\'qituvchi profili topilmadi. Admin bilan bog\'laning.');
        }

        $teacherId = $ids['employee_id'] ?? $ids['teacher_id'];

        $query = Material::with(['subject'])
            ->where('teacher_id', $teacherId);

        // Filter by type
        if ($request->has('type') && $request->type != '') {
            $query->where('type', $request->type);
        }

        // Filter by subject
        if ($request->has('subject_id') && $request->subject_id != '') {
            $query->where('subject_id', $request->subject_id);
        }

        $materials = $query->latest()->paginate(15);

        // Get teacher's subjects from TeacherSubject
        $teacherSubjects = TeacherSubject::with('subject')
            ->where('teacher_id', $teacherId)
            ->active()
            ->get();
        $subjects = $teacherSubjects->pluck('subject')->unique('id')->filter();

        // Statistics
        $stats = [
            'total' => Material::where('teacher_id', $teacherId)->count(),
            'documents' => Material::where('teacher_id', $teacherId)->where('type', 'document')->count(),
            'videos' => Material::where('teacher_id', $teacherId)->where('type', 'video')->count(),
            'presentations' => Material::where('teacher_id', $teacherId)->where('type', 'presentation')->count(),
        ];

        // Get LMS courses count
        $coursesCount = LmsCourse::where('teacher_id', $ids['user_id'])->count();

        return view('teacher.materials.index', [
            'materials' => $materials,
            'subjects' => $subjects,
            'stats' => $stats,
            'coursesCount' => $coursesCount,
            'teacher' => $ids['teacher'] ?? $ids['employee'],
        ]);
    }

    /**
     * Show form to upload new material
     */
    public function create()
    {
        $ids = $this->getTeacherId();
        $teacherId = $ids['employee_id'] ?? $ids['teacher_id'];

        // Get teacher's subjects with groups from TeacherSubject
        $teacherSubjects = TeacherSubject::with('subject')
            ->where('teacher_id', $teacherId)
            ->active()
            ->get();

        // Build subjects with groups
        $subjects = $teacherSubjects->groupBy('subject_id')->map(function($items) {
            $firstItem = $items->first();

            // Collect all groups from group_ids
            $allGroups = collect();
            foreach ($items as $item) {
                $groupIds = $item->group_ids ?? [];
                if (!is_array($groupIds)) {
                    $groupIds = json_decode($groupIds, true) ?? [];
                }
                foreach ($groupIds as $gid) {
                    $group = Group::find($gid);
                    if ($group) {
                        $allGroups->push($group);
                    }
                }
            }

            return [
                'subject' => $firstItem->subject,
                'groups' => $allGroups->unique('id')->values(),
            ];
        });

        return view('teacher.materials.create', [
            'subjects' => $subjects,
            'teacher' => $ids['teacher'] ?? $ids['employee'],
        ]);
    }

    /**
     * Store new material
     */
    public function store(Request $request)
    {
        $ids = $this->getTeacherId();
        $teacherId = $ids['employee_id'] ?? $ids['teacher_id'];

        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:document,video,presentation,link',
            'file' => 'required_if:type,document,presentation|file|max:51200', // 50MB max
            'video_url' => 'required_if:type,video|nullable|url',
            'external_link' => 'required_if:type,link|nullable|url',
            'group_ids' => 'nullable|array',
            'group_ids.*' => 'exists:groups,id',
        ]);

        $data = [
            'teacher_id' => $teacherId,
            'subject_id' => $request->subject_id,
            'title' => $request->title,
            'description' => $request->description,
            'type' => $request->type,
            'group_ids' => $request->group_ids ? json_encode($request->group_ids) : null,
        ];

        // Handle file upload for documents and presentations
        if ($request->hasFile('file') && in_array($request->type, ['document', 'presentation'])) {
            $file = $request->file('file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('materials', $filename, 'public');
            $data['file_path'] = $path;
            $data['file_size'] = $file->getSize();
            $data['file_type'] = $file->getClientOriginalExtension();
        }

        // Handle video URL
        if ($request->type === 'video' && $request->video_url) {
            $data['video_url'] = $request->video_url;
        }

        // Handle external link
        if ($request->type === 'link' && $request->external_link) {
            $data['external_link'] = $request->external_link;
        }

        Material::create($data);

        return redirect()
            ->route('teacher.materials.index')
            ->with('success', 'Material muvaffaqiyatli yuklandi!');
    }

    /**
     * Show material details
     */
    public function show($id)
    {
        $ids = $this->getTeacherId();
        $teacherId = $ids['employee_id'] ?? $ids['teacher_id'];

        $material = Material::with(['subject'])
            ->where('id', $id)
            ->where('teacher_id', $teacherId)
            ->firstOrFail();

        // Get groups if specified
        $groups = [];
        if ($material->group_ids) {
            $groupIds = is_string($material->group_ids)
                ? json_decode($material->group_ids, true)
                : $material->group_ids;

            if (is_array($groupIds) && count($groupIds) > 0) {
                $groups = Group::whereIn('id', $groupIds)->get();
            }
        }

        // Track view
        $material->increment('views_count');

        return view('teacher.materials.show', [
            'material' => $material,
            'groups' => $groups,
            'teacher' => $ids['teacher'] ?? $ids['employee'],
        ]);
    }

    /**
     * Edit material
     */
    public function edit($id)
    {
        $ids = $this->getTeacherId();
        $teacherId = $ids['employee_id'] ?? $ids['teacher_id'];

        $material = Material::where('id', $id)
            ->where('teacher_id', $teacherId)
            ->firstOrFail();

        // Get teacher's subjects with groups from TeacherSubject
        $teacherSubjects = TeacherSubject::with('subject')
            ->where('teacher_id', $teacherId)
            ->active()
            ->get();

        // Build subjects with groups
        $subjects = $teacherSubjects->groupBy('subject_id')->map(function($items) {
            $firstItem = $items->first();

            // Collect all groups from group_ids
            $allGroups = collect();
            foreach ($items as $item) {
                $groupIds = $item->group_ids ?? [];
                if (!is_array($groupIds)) {
                    $groupIds = json_decode($groupIds, true) ?? [];
                }
                foreach ($groupIds as $gid) {
                    $group = Group::find($gid);
                    if ($group) {
                        $allGroups->push($group);
                    }
                }
            }

            return [
                'subject' => $firstItem->subject,
                'groups' => $allGroups->unique('id')->values(),
            ];
        });

        // Decode group_ids
        $material->group_ids = is_string($material->group_ids)
            ? json_decode($material->group_ids, true)
            : $material->group_ids;

        return view('teacher.materials.edit', [
            'material' => $material,
            'subjects' => $subjects,
            'teacher' => $ids['teacher'] ?? $ids['employee'],
        ]);
    }

    /**
     * Update material
     */
    public function update(Request $request, $id)
    {
        $ids = $this->getTeacherId();
        $teacherId = $ids['employee_id'] ?? $ids['teacher_id'];

        $material = Material::where('id', $id)
            ->where('teacher_id', $teacherId)
            ->firstOrFail();

        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:document,video,presentation,link',
            'file' => 'nullable|file|max:51200',
            'video_url' => 'required_if:type,video|nullable|url',
            'external_link' => 'required_if:type,link|nullable|url',
            'group_ids' => 'nullable|array',
            'group_ids.*' => 'exists:groups,id',
        ]);

        $data = [
            'subject_id' => $request->subject_id,
            'title' => $request->title,
            'description' => $request->description,
            'type' => $request->type,
            'group_ids' => $request->group_ids ? json_encode($request->group_ids) : null,
        ];

        // Handle new file upload
        if ($request->hasFile('file') && in_array($request->type, ['document', 'presentation'])) {
            // Delete old file
            if ($material->file_path) {
                Storage::disk('public')->delete($material->file_path);
            }

            $file = $request->file('file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('materials', $filename, 'public');
            $data['file_path'] = $path;
            $data['file_size'] = $file->getSize();
            $data['file_type'] = $file->getClientOriginalExtension();
        }

        // Handle video URL
        if ($request->type === 'video' && $request->video_url) {
            $data['video_url'] = $request->video_url;
            // Clear file path if changing to video
            if ($material->type != 'video') {
                $data['file_path'] = null;
                $data['external_link'] = null;
            }
        }

        // Handle external link
        if ($request->type === 'link' && $request->external_link) {
            $data['external_link'] = $request->external_link;
            // Clear file path if changing to link
            if ($material->type != 'link') {
                $data['file_path'] = null;
                $data['video_url'] = null;
            }
        }

        $material->update($data);

        return redirect()
            ->route('teacher.materials.show', $material->id)
            ->with('success', 'Material muvaffaqiyatli yangilandi!');
    }

    /**
     * Delete material
     */
    public function destroy($id)
    {
        $ids = $this->getTeacherId();
        $teacherId = $ids['employee_id'] ?? $ids['teacher_id'];

        $material = Material::where('id', $id)
            ->where('teacher_id', $teacherId)
            ->firstOrFail();

        // Delete file if exists
        if ($material->file_path) {
            Storage::disk('public')->delete($material->file_path);
        }

        $material->delete();

        return redirect()
            ->route('teacher.materials.index')
            ->with('success', 'Material o\'chirildi!');
    }

    /**
     * Download material file
     */
    public function download($id)
    {
        $ids = $this->getTeacherId();
        $teacherId = $ids['employee_id'] ?? $ids['teacher_id'];

        $material = Material::where('id', $id)
            ->where('teacher_id', $teacherId)
            ->firstOrFail();

        if (!$material->file_path || !Storage::disk('public')->exists($material->file_path)) {
            return redirect()->back()->with('error', 'Fayl topilmadi!');
        }

        // Increment download count
        $material->increment('downloads_count');

        return Storage::disk('public')->download($material->file_path, $material->title . '.' . $material->file_type);
    }
}
