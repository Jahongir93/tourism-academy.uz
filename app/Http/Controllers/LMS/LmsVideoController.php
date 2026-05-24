<?php

namespace App\Http\Controllers\LMS;

use App\Http\Controllers\Controller;
use App\Models\LmsVideo;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class LmsVideoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $videos = LmsVideo::with(['subject', 'teacher'])
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->paginate(12);
            
        return view('lms.videos.index', compact('videos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $subjects = Subject::where('is_active', true)->orderBy('name_uz')->get();
        return view('lms.videos.create', compact('subjects'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title'      => 'required|string|max:255',
            'subject_id' => 'required|exists:subjects,id',
            'video_type' => 'required|in:youtube,upload,external',
            'video_url'  => 'required_if:video_type,youtube,external|nullable|url',
            'video_file' => 'required_if:video_type,upload|nullable|file|mimes:mp4,avi,mov,mkv,webm|max:512000',
        ]);

        $data = $request->only(['title', 'description', 'subject_id', 'video_type', 'week_number', 'order_number', 'duration']);
        $data['teacher_id'] = Auth::id();
        $data['is_active']  = $request->boolean('is_active', true);
        $data['allow_download'] = $request->boolean('allow_download');

        if ($request->video_type === 'upload' && $request->hasFile('video_file')) {
            $data['video_path'] = $request->file('video_file')->store('lms/videos', 'public');
        } else {
            $data['video_url'] = $request->video_url;
        }

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail_path'] = $request->file('thumbnail')->store('lms/thumbnails', 'public');
        }

        LmsVideo::create($data);

        return redirect()->route('lms.videos.index')->with('success', 'Video muvaffaqiyatli qo\'shildi!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $video = LmsVideo::findOrFail($id);
        return view('lms.videos.show', compact('video'));
    }
    
    /**
     * Watch video
     */
    public function watch(string $id)
    {
        $video = LmsVideo::findOrFail($id);
        $video->incrementViewCount();
        return view('lms.videos.watch', compact('video'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $video = LmsVideo::findOrFail($id);
        return view('lms.videos.edit', compact('video'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}