<?php

namespace App\Http\Controllers;

use App\Models\Forum\ForumCategory;
use App\Models\Forum\ForumTopic;
use App\Models\Forum\ForumPost;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ForumController extends Controller
{
    public function index()
    {
        $categories = ForumCategory::active()
            ->ordered()
            ->withCount(['topics'])
            ->with(['latestTopic.user', 'latestTopic.latestPost.user'])
            ->get();

        $stats = [
            'total_topics' => ForumTopic::count(),
            'total_posts' => ForumPost::count(),
            'total_members' => \App\Models\User::count(),
            'newest_member' => \App\Models\User::latest()->first()
        ];

        $popularTopics = ForumTopic::popular()
            ->with(['user', 'category'])
            ->limit(5)
            ->get();

        $recentTopics = ForumTopic::recent()
            ->with(['user', 'category'])
            ->limit(5)
            ->get();

        return view('forum.index', compact('categories', 'stats', 'popularTopics', 'recentTopics'));
    }

    public function category($slug)
    {
        $category = ForumCategory::where('slug', $slug)
            ->active()
            ->firstOrFail();

        $topics = ForumTopic::where('category_id', $category->id)
            ->with(['user', 'latestPost.user'])
            ->withCount('posts')
            ->orderBy('is_pinned', 'desc')
            ->orderBy('last_reply_at', 'desc')
            ->paginate(20);

        return view('forum.category', compact('category', 'topics'));
    }

    public function topic($slug)
    {
        $topic = ForumTopic::where('slug', $slug)
            ->with(['user', 'category'])
            ->firstOrFail();

        // Increment views
        $topic->incrementViews();

        $posts = ForumPost::where('topic_id', $topic->id)
            ->with(['user', 'likes', 'replies.user'])
            ->rootPosts()
            ->paginate(20);

        return view('forum.topic', compact('topic', 'posts'));
    }

    public function createTopic(Request $request, $categorySlug = null)
    {
        $categories = ForumCategory::active()->ordered()->get();

        $selectedCategory = null;
        if ($categorySlug) {
            $selectedCategory = ForumCategory::where('slug', $categorySlug)->first();
        }

        return view('forum.create-topic', compact('categories', 'selectedCategory'));
    }

    public function storeTopic(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:forum_categories,id',
            'title' => 'required|string|max:255',
            'content' => 'required|string|min:10'
        ]);

        $topic = ForumTopic::create([
            'category_id' => $validated['category_id'],
            'user_id' => auth()->id(),
            'title' => $validated['title'],
            'slug' => Str::slug($validated['title']) . '-' . uniqid(),
            'content' => $validated['content'],
            'last_reply_at' => now()
        ]);

        return redirect()->route('forum.topic', $topic->slug)
            ->with('success', 'Mavzu muvaffaqiyatli yaratildi!');
    }

    public function storePost(Request $request, $topicSlug)
    {
        $topic = ForumTopic::where('slug', $topicSlug)->firstOrFail();

        if ($topic->is_locked) {
            return back()->with('error', 'Bu mavzu yopilgan!');
        }

        $validated = $request->validate([
            'content' => 'required|string|min:5',
            'parent_id' => 'nullable|exists:forum_posts,id'
        ]);

        $post = ForumPost::create([
            'topic_id' => $topic->id,
            'user_id' => auth()->id(),
            'parent_id' => $validated['parent_id'] ?? null,
            'content' => $validated['content']
        ]);

        // Update topic's last reply time
        $topic->update(['last_reply_at' => now()]);

        return back()->with('success', 'Javob muvaffaqiyatli yuborildi!');
    }

    public function like($type, $id)
    {
        $model = $type === 'topic'
            ? ForumTopic::findOrFail($id)
            : ForumPost::findOrFail($id);

        $existingLike = $model->likes()->where('user_id', auth()->id())->first();

        if ($existingLike) {
            $existingLike->delete();
            $liked = false;
        } else {
            $model->likes()->create(['user_id' => auth()->id()]);
            $liked = true;
        }

        return response()->json([
            'liked' => $liked,
            'likes_count' => $model->likes()->count()
        ]);
    }
}