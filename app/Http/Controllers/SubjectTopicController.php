<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Models\SubjectTopic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubjectTopicController extends Controller
{
    /**
     * Display topics for a subject
     */
    public function index(Subject $subject)
    {
        $topics = $subject->topics()
            ->ordered()
            ->paginate(50);

        $statistics = [
            'total_topics' => $subject->topics()->count(),
            'lecture_topics' => $subject->topics()->where('topic_type', 'lecture')->count(),
            'practice_topics' => $subject->topics()->where('topic_type', 'practice')->count(),
            'lab_topics' => $subject->topics()->where('topic_type', 'lab')->count(),
            'total_hours' => $subject->topics()->sum('hours'),
        ];

        return view('subjects.topics.index', compact('subject', 'topics', 'statistics'));
    }

    /**
     * Show the form for creating a new topic
     */
    public function create(Subject $subject)
    {
        $lastTopicNumber = $subject->topics()->max('topic_number') ?? 0;
        $nextTopicNumber = $lastTopicNumber + 1;

        return view('subjects.topics.create', compact('subject', 'nextTopicNumber'));
    }

    /**
     * Store a newly created topic
     */
    public function store(Request $request, Subject $subject)
    {
        $validated = $request->validate([
            'topic_number' => 'required|integer|min:1',
            'title_uz' => 'required|string|max:255',
            'title_ru' => 'nullable|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'description_uz' => 'nullable|string',
            'description_ru' => 'nullable|string',
            'description_en' => 'nullable|string',
            'topic_type' => 'required|in:lecture,practice,lab,seminar,independent',
            'hours' => 'required|integer|min:1|max:20',
            'week_number' => 'nullable|integer|min:1|max:52',
            'learning_outcomes' => 'nullable|string',
            'keywords' => 'nullable|string',
            'references' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['subject_id'] = $subject->id;
        $validated['order'] = $subject->topics()->max('order') + 1;

        $topic = SubjectTopic::create($validated);

        return redirect()->route('subjects.topics.index', $subject)
            ->with('success', 'Mavzu muvaffaqiyatli qo\'shildi.');
    }

    /**
     * Display the specified topic
     */
    public function show(Subject $subject, SubjectTopic $topic)
    {
        return view('subjects.topics.show', compact('subject', 'topic'));
    }

    /**
     * Show the form for editing the specified topic
     */
    public function edit(Subject $subject, SubjectTopic $topic)
    {
        return view('subjects.topics.edit', compact('subject', 'topic'));
    }

    /**
     * Update the specified topic
     */
    public function update(Request $request, Subject $subject, SubjectTopic $topic)
    {
        $validated = $request->validate([
            'topic_number' => 'required|integer|min:1',
            'title_uz' => 'required|string|max:255',
            'title_ru' => 'nullable|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'description_uz' => 'nullable|string',
            'description_ru' => 'nullable|string',
            'description_en' => 'nullable|string',
            'topic_type' => 'required|in:lecture,practice,lab,seminar,independent',
            'hours' => 'required|integer|min:1|max:20',
            'week_number' => 'nullable|integer|min:1|max:52',
            'learning_outcomes' => 'nullable|string',
            'keywords' => 'nullable|string',
            'references' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $topic->update($validated);

        return redirect()->route('subjects.topics.index', $subject)
            ->with('success', 'Mavzu yangilandi.');
    }

    /**
     * Remove the specified topic
     */
    public function destroy(Subject $subject, SubjectTopic $topic)
    {
        try {
            $topic->delete();
            return redirect()->route('subjects.topics.index', $subject)
                ->with('success', 'Mavzu o\'chirildi.');
        } catch (\Exception $e) {
            return back()->with('error', 'Xatolik yuz berdi: ' . $e->getMessage());
        }
    }

    /**
     * Reorder topics
     */
    public function reorder(Request $request, Subject $subject)
    {
        $validated = $request->validate([
            'topics' => 'required|array',
            'topics.*.id' => 'required|exists:subject_topics,id',
            'topics.*.order' => 'required|integer|min:0',
        ]);

        DB::transaction(function () use ($validated) {
            foreach ($validated['topics'] as $topicData) {
                SubjectTopic::where('id', $topicData['id'])
                    ->update(['order' => $topicData['order']]);
            }
        });

        return response()->json(['message' => 'Tartib yangilandi.']);
    }
}
