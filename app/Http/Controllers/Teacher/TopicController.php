<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\TeacherSubject;
use App\Models\Teacher;
use App\Models\Employee;
use App\Models\Topic;
use App\Models\TopicResource;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TopicController extends Controller
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
            'teacher' => $teacher,
            'employee' => $employee,
        ];
    }

    /**
     * Check if teacher has access to subject
     */
    private function hasAccessToSubject($subjectId)
    {
        $ids = $this->getTeacherId();

        return TeacherSubject::where('subject_id', $subjectId)
            ->where(function($q) use ($ids) {
                if ($ids['employee_id']) {
                    $q->where('teacher_id', $ids['employee_id']);
                }
                if ($ids['teacher_id']) {
                    $q->orWhere('teacher_id', $ids['teacher_id']);
                }
            })
            ->active()
            ->exists();
    }

    /**
     * Display list of subjects/courses assigned to teacher
     */
    public function index()
    {
        $ids = $this->getTeacherId();

        if (!$ids['teacher_id'] && !$ids['employee_id']) {
            return redirect()->route('teacher.dashboard')
                ->with('error', 'O\'qituvchi profili topilmadi. Admin bilan bog\'laning.');
        }

        // Get teacher's subjects from TeacherSubject
        $teacherSubjects = TeacherSubject::with(['subject'])
            ->where(function($q) use ($ids) {
                if ($ids['employee_id']) {
                    $q->where('teacher_id', $ids['employee_id']);
                }
                if ($ids['teacher_id']) {
                    $q->orWhere('teacher_id', $ids['teacher_id']);
                }
            })
            ->active()
            ->get();

        // Group by subject
        $subjects = $teacherSubjects->groupBy('subject_id')->map(function($items) {
            $firstItem = $items->first();
            $topicsCount = Topic::where('subject_id', $firstItem->subject_id)->count();

            // Count groups from group_ids
            $groupsCount = 0;
            foreach ($items as $item) {
                $groupIds = $item->group_ids ?? [];
                if (!is_array($groupIds)) {
                    $groupIds = json_decode($groupIds, true) ?? [];
                }
                $groupsCount += count($groupIds);
            }

            return [
                'subject' => $firstItem->subject,
                'groups_count' => $groupsCount,
                'topics_count' => $topicsCount,
            ];
        });

        return view('teacher.topics.index', [
            'subjects' => $subjects,
            'teacher' => $ids['teacher'] ?? $ids['employee'],
        ]);
    }

    /**
     * Show topics for specific subject
     */
    public function subjectTopics($subjectId)
    {
        $ids = $this->getTeacherId();

        // Verify teacher teaches this subject
        if (!$this->hasAccessToSubject($subjectId)) {
            abort(403, 'Bu fanga kirish huquqingiz yo\'q.');
        }

        $teacherSubject = TeacherSubject::with('subject')
            ->where('subject_id', $subjectId)
            ->where(function($q) use ($ids) {
                if ($ids['employee_id']) {
                    $q->where('teacher_id', $ids['employee_id']);
                }
                if ($ids['teacher_id']) {
                    $q->orWhere('teacher_id', $ids['teacher_id']);
                }
            })
            ->first();

        $subject = $teacherSubject->subject;

        // Get topics with resources count
        $topics = Topic::where('subject_id', $subjectId)
            ->withCount('resources')
            ->orderBy('order')
            ->get();

        return view('teacher.topics.subject-topics', [
            'subject' => $subject,
            'topics' => $topics,
            'teacher' => $ids['teacher'] ?? $ids['employee'],
        ]);
    }

    /**
     * Show form to create new topic
     */
    public function create($subjectId)
    {
        $ids = $this->getTeacherId();

        // Verify teacher teaches this subject
        if (!$this->hasAccessToSubject($subjectId)) {
            abort(403, 'Bu fanga kirish huquqingiz yo\'q.');
        }

        $teacherSubject = TeacherSubject::with('subject')
            ->where('subject_id', $subjectId)
            ->where(function($q) use ($ids) {
                if ($ids['employee_id']) {
                    $q->where('teacher_id', $ids['employee_id']);
                }
                if ($ids['teacher_id']) {
                    $q->orWhere('teacher_id', $ids['teacher_id']);
                }
            })
            ->first();

        $subject = $teacherSubject->subject;

        // Get max order
        $maxOrder = Topic::where('subject_id', $subjectId)->max('order') ?? 0;

        return view('teacher.topics.create', [
            'subject' => $subject,
            'maxOrder' => $maxOrder,
            'teacher' => $ids['teacher'] ?? $ids['employee'],
        ]);
    }

    /**
     * Store new topic
     */
    public function store(Request $request, $subjectId)
    {
        $ids = $this->getTeacherId();

        // Verify teacher teaches this subject
        if (!$this->hasAccessToSubject($subjectId)) {
            abort(403, 'Bu fanga kirish huquqingiz yo\'q.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'content' => 'nullable|string',
            'order' => 'required|integer|min:0',
            'duration_hours' => 'nullable|numeric|min:0',
        ]);

        Topic::create([
            'subject_id' => $subjectId,
            'teacher_id' => $ids['employee_id'] ?? $ids['teacher_id'],
            'title' => $request->title,
            'description' => $request->description,
            'content' => $request->content,
            'order' => $request->order,
            'duration_hours' => $request->duration_hours,
        ]);

        return redirect()
            ->route('teacher.topics.subject', $subjectId)
            ->with('success', 'Mavzu muvaffaqiyatli qo\'shildi!');
    }

    /**
     * Show topic details with resources
     */
    public function show($subjectId, $topicId)
    {
        $ids = $this->getTeacherId();

        // Verify teacher teaches this subject
        if (!$this->hasAccessToSubject($subjectId)) {
            abort(403, 'Bu fanga kirish huquqingiz yo\'q.');
        }

        $topic = Topic::with(['subject', 'resources'])
            ->where('id', $topicId)
            ->where('subject_id', $subjectId)
            ->firstOrFail();

        return view('teacher.topics.show', [
            'topic' => $topic,
            'teacher' => $ids['teacher'] ?? $ids['employee'],
        ]);
    }

    /**
     * Edit topic
     */
    public function edit($subjectId, $topicId)
    {
        $ids = $this->getTeacherId();

        // Verify teacher teaches this subject
        if (!$this->hasAccessToSubject($subjectId)) {
            abort(403, 'Bu fanga kirish huquqingiz yo\'q.');
        }

        $teacherSubject = TeacherSubject::with('subject')
            ->where('subject_id', $subjectId)
            ->where(function($q) use ($ids) {
                if ($ids['employee_id']) {
                    $q->where('teacher_id', $ids['employee_id']);
                }
                if ($ids['teacher_id']) {
                    $q->orWhere('teacher_id', $ids['teacher_id']);
                }
            })
            ->first();

        $topic = Topic::where('id', $topicId)
            ->where('subject_id', $subjectId)
            ->firstOrFail();

        return view('teacher.topics.edit', [
            'subject' => $teacherSubject->subject,
            'topic' => $topic,
            'teacher' => $ids['teacher'] ?? $ids['employee'],
        ]);
    }

    /**
     * Update topic
     */
    public function update(Request $request, $subjectId, $topicId)
    {
        // Verify teacher teaches this subject
        if (!$this->hasAccessToSubject($subjectId)) {
            abort(403, 'Bu fanga kirish huquqingiz yo\'q.');
        }

        $topic = Topic::where('id', $topicId)
            ->where('subject_id', $subjectId)
            ->firstOrFail();

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'content' => 'nullable|string',
            'order' => 'required|integer|min:0',
            'duration_hours' => 'nullable|numeric|min:0',
        ]);

        $topic->update([
            'title' => $request->title,
            'description' => $request->description,
            'content' => $request->content,
            'order' => $request->order,
            'duration_hours' => $request->duration_hours,
        ]);

        return redirect()
            ->route('teacher.topics.show', [$subjectId, $topicId])
            ->with('success', 'Mavzu yangilandi!');
    }

    /**
     * Delete topic
     */
    public function destroy($subjectId, $topicId)
    {
        // Verify teacher teaches this subject
        if (!$this->hasAccessToSubject($subjectId)) {
            abort(403, 'Bu fanga kirish huquqingiz yo\'q.');
        }

        $topic = Topic::where('id', $topicId)
            ->where('subject_id', $subjectId)
            ->firstOrFail();

        // Delete all resources first
        foreach ($topic->resources as $resource) {
            if ($resource->file_path) {
                Storage::disk('public')->delete($resource->file_path);
            }
            $resource->delete();
        }

        $topic->delete();

        return redirect()
            ->route('teacher.topics.subject', $subjectId)
            ->with('success', 'Mavzu o\'chirildi!');
    }

    /**
     * Add resource to topic
     */
    public function addResource(Request $request, $subjectId, $topicId)
    {
        // Verify teacher teaches this subject
        if (!$this->hasAccessToSubject($subjectId)) {
            abort(403, 'Bu fanga kirish huquqingiz yo\'q.');
        }

        $topic = Topic::where('id', $topicId)
            ->where('subject_id', $subjectId)
            ->firstOrFail();

        $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:file,video,link',
            'file' => 'required_if:type,file|file|max:102400', // 100MB
            'video_url' => 'required_if:type,video|nullable|url',
            'link_url' => 'required_if:type,link|nullable|url',
            'description' => 'nullable|string',
        ]);

        $data = [
            'topic_id' => $topic->id,
            'title' => $request->title,
            'type' => $request->type,
            'description' => $request->description,
        ];

        // Handle file upload
        if ($request->hasFile('file') && $request->type === 'file') {
            $file = $request->file('file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('topic-resources', $filename, 'public');
            $data['file_path'] = $path;
            $data['file_size'] = $file->getSize();
            $data['file_type'] = $file->getClientOriginalExtension();
        }

        // Handle video URL
        if ($request->type === 'video' && $request->video_url) {
            $data['url'] = $request->video_url;
        }

        // Handle link URL
        if ($request->type === 'link' && $request->link_url) {
            $data['url'] = $request->link_url;
        }

        TopicResource::create($data);

        return redirect()
            ->route('teacher.topics.show', [$subjectId, $topicId])
            ->with('success', 'Resurs qo\'shildi!');
    }

    /**
     * Delete resource
     */
    public function deleteResource($subjectId, $topicId, $resourceId)
    {
        // Verify teacher teaches this subject
        if (!$this->hasAccessToSubject($subjectId)) {
            abort(403, 'Bu fanga kirish huquqingiz yo\'q.');
        }

        $resource = TopicResource::whereHas('topic', function($q) use ($subjectId) {
            $q->where('subject_id', $subjectId);
        })->findOrFail($resourceId);

        // Delete file if exists
        if ($resource->file_path) {
            Storage::disk('public')->delete($resource->file_path);
        }

        $resource->delete();

        return redirect()
            ->back()
            ->with('success', 'Resurs o\'chirildi!');
    }
}
