<?php

namespace App\Http\Controllers\LMS;

use App\Http\Controllers\Controller;
use App\Models\LmsForumPost;
use App\Models\LmsForumPostReaction;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LmsForumController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posts = LmsForumPost::with(['subject', 'user'])
            ->whereNull('parent_id')
            ->orderBy('is_pinned', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        return view('lms.forum.index', compact('posts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $subjects = Subject::where('is_active', true)->orderBy('name_uz')->get();
        return view('lms.forum.create', compact('subjects'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'subject_id' => 'nullable|exists:subjects,id',
            'attachments.*' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,zip,rar'
        ]);

        // Handle file uploads
        $attachments = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('forum/attachments', $filename, 'public');

                $attachments[] = [
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'size' => $file->getSize(),
                    'type' => $file->getMimeType(),
                    'extension' => $file->getClientOriginalExtension()
                ];
            }
        }

        $post = LmsForumPost::create([
            'user_id' => Auth::id(),
            'subject_id' => $validated['subject_id'],
            'title' => $validated['title'],
            'content' => $validated['content'],
            'attachments' => $attachments,
            'post_type' => 'question',
            'is_pinned' => false,
            'is_locked' => false,
            'is_answered' => false,
            'view_count' => 0,
            'reply_count' => 0,
            'like_count' => 0
        ]);

        return redirect()->route('lms.forum.show', $post)
            ->with('success', 'Forum mavzusi muvaffaqiyatli yaratildi!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $post = LmsForumPost::with(['subject', 'user', 'replies.user'])
            ->findOrFail($id);

        // Increment view count
        $post->incrementViewCount();

        return view('lms.forum.show', compact('post'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $post = LmsForumPost::findOrFail($id);
        $subjects = Subject::where('is_active', true)->orderBy('name_uz')->get();
        return view('lms.forum.edit', compact('post', 'subjects'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $post = LmsForumPost::findOrFail($id);

        // Check authorization
        if ($post->user_id !== Auth::id() && !Auth::user()->hasRole(['SuperAdmin', 'admin'])) {
            return back()->with('error', 'Sizda bu mavzuni tahrirlash huquqi yo\'q!');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'subject_id' => 'nullable|exists:subjects,id'
        ]);

        $post->update([
            'subject_id' => $validated['subject_id'],
            'title' => $validated['title'],
            'content' => $validated['content']
        ]);

        return redirect()->route('lms.forum.show', $post)
            ->with('success', 'Mavzu muvaffaqiyatli yangilandi!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $post = LmsForumPost::findOrFail($id);

        // Check authorization
        if ($post->user_id !== Auth::id() && !Auth::user()->hasRole(['SuperAdmin', 'admin'])) {
            return back()->with('error', 'Sizda bu mavzuni o\'chirish huquqi yo\'q!');
        }

        // Delete all replies first
        $post->replies()->delete();

        // Delete the post
        $post->delete();

        return redirect()->route('lms.forum.index')
            ->with('success', 'Mavzu va uning barcha javoblari o\'chirildi!');
    }

    /**
     * Reply to a post
     */
    public function reply(Request $request, string $id)
    {
        $post = LmsForumPost::findOrFail($id);

        // Check if post is locked
        if ($post->is_locked) {
            return back()->with('error', 'Bu mavzu yopilgan, javob qo\'shib bo\'lmaydi!');
        }

        $validated = $request->validate([
            'content' => 'required|string',
            'attachments.*' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,zip,rar'
        ]);

        // Handle file uploads
        $attachments = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('forum/attachments', $filename, 'public');

                $attachments[] = [
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'size' => $file->getSize(),
                    'type' => $file->getMimeType(),
                    'extension' => $file->getClientOriginalExtension()
                ];
            }
        }

        $reply = LmsForumPost::create([
            'user_id' => Auth::id(),
            'parent_id' => $post->id,
            'subject_id' => $post->subject_id,
            'title' => 'Re: ' . $post->title,
            'content' => $validated['content'],
            'attachments' => $attachments,
            'post_type' => 'reply',
            'is_pinned' => false,
            'is_locked' => false,
            'is_answered' => false,
            'view_count' => 0,
            'reply_count' => 0,
            'like_count' => 0
        ]);

        // Increment parent post reply count
        $post->incrementReplyCount();

        return back()->with('success', 'Javobingiz muvaffaqiyatli joylashtirildi!');
    }

    /**
     * Add or update like/dislike reaction
     */
    public function react(Request $request, string $id)
    {
        $post = LmsForumPost::findOrFail($id);

        $validated = $request->validate([
            'type' => 'required|in:like,dislike'
        ]);

        $userId = Auth::id();

        // Check if user already reacted
        $existingReaction = LmsForumPostReaction::where('post_id', $post->id)
            ->where('user_id', $userId)
            ->first();

        if ($existingReaction) {
            if ($existingReaction->type === $validated['type']) {
                // Remove reaction if same type
                $existingReaction->delete();
                return response()->json([
                    'success' => true,
                    'action' => 'removed',
                    'likes_count' => $post->likes()->count(),
                    'dislikes_count' => $post->dislikes()->count()
                ]);
            } else {
                // Update to new type
                $existingReaction->update(['type' => $validated['type']]);
                return response()->json([
                    'success' => true,
                    'action' => 'updated',
                    'type' => $validated['type'],
                    'likes_count' => $post->likes()->count(),
                    'dislikes_count' => $post->dislikes()->count()
                ]);
            }
        } else {
            // Create new reaction
            LmsForumPostReaction::create([
                'post_id' => $post->id,
                'user_id' => $userId,
                'type' => $validated['type']
            ]);

            return response()->json([
                'success' => true,
                'action' => 'added',
                'type' => $validated['type'],
                'likes_count' => $post->likes()->count(),
                'dislikes_count' => $post->dislikes()->count()
            ]);
        }
    }
}
