<?php

namespace App\Http\Controllers\CMS;

use App\Http\Controllers\Controller;
use App\Models\CmsMedia;
use App\Models\CmsMediaFolder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    public function index(Request $request)
    {
        $query = CmsMedia::with(['folder', 'uploader']);
        
        if ($request->has('folder_id')) {
            $query->where('folder_id', $request->folder_id);
        }
        
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('file_name', 'like', "%{$search}%")
                  ->orWhere('alt_text', 'like', "%{$search}%");
            });
        }
        
        $media = $query->orderBy('created_at', 'desc')->paginate(24);
        $folders = CmsMediaFolder::orderBy('name')->get();
        
        return view('cms.media.index', compact('media', 'folders'));
    }

    public function upload(Request $request)
    {
        $validated = $request->validate([
            'files.*' => 'required|file|mimes:jpg,jpeg,png,gif,webp,svg,pdf,doc,docx|max:10240', // 10MB, images, PDF, Word
            'folder_id' => 'nullable|exists:cms_media_folders,id',
            'alt_text' => 'nullable|string|max:255',
            'caption' => 'nullable|string'
        ]);
        
        $uploadedFiles = [];
        
        foreach ($request->file('files') as $file) {
            $path = $file->store('cms/media/' . date('Y/m'), 'public');
            
            $media = CmsMedia::create([
                'name' => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
                'file_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'path' => $path,
                'disk' => 'public',
                'size' => $file->getSize(),
                'folder_id' => $validated['folder_id'] ?? null,
                'alt_text' => $validated['alt_text'] ?? null,
                'caption' => $validated['caption'] ?? null,
                'uploaded_by' => Auth::id()
            ]);
            
            // Generate thumbnails for images
            if (str_starts_with($media->mime_type, 'image/')) {
                // Add thumbnail generation logic here
            }
            
            $uploadedFiles[] = $media;
        }
        
        return response()->json([
            'success' => true,
            'files' => $uploadedFiles
        ]);
    }

    public function destroy(CmsMedia $media)
    {
        Storage::disk('public')->delete($media->path);

        if ($media->thumbnails) {
            foreach ($media->thumbnails as $thumbnail) {
                Storage::disk('public')->delete($thumbnail);
            }
        }

        $media->delete();

        return response()->json([
            'success' => true,
            'message' => 'Fayl muvaffaqiyatli o\'chirildi!'
        ]);
    }

    /**
     * TinyMCE uchun rasm yuklash
     */
    public function uploadImage(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:jpg,jpeg,png,gif,webp,svg,pdf,doc,docx|max:5120', // 5MB max
        ]);

        try {
            $file = $request->file('file');
            $path = $file->store('cms/content/' . date('Y/m'), 'public');

            // Media jadvaliga saqlash
            $media = CmsMedia::create([
                'name' => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
                'file_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'path' => $path,
                'disk' => 'public',
                'size' => $file->getSize(),
                'folder_id' => null,
                'alt_text' => null,
                'caption' => null,
                'uploaded_by' => Auth::id()
            ]);

            // TinyMCE kutayotgan formatda javob qaytarish
            return response()->json([
                'location' => asset('storage/' . $path)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Rasm yuklashda xatolik: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * WAF-safe TinyMCE rasm yuklash — rasm base64 JSON sifatida keladi,
     * shuning uchun firewall binary yuklashni bloklamaydi.
     * Kutiladi: { name, type, data(base64) }
     */
    public function uploadImageBase64(Request $request)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'type' => 'nullable|string|max:100',
            'data' => 'required|string',
        ]);

        try {
            $binary = base64_decode($validated['data'], true);
            if ($binary === false) {
                return response()->json(['error' => 'Noto\'g\'ri base64 ma\'lumot'], 422);
            }
            if (strlen($binary) > 5 * 1024 * 1024) {
                return response()->json(['error' => 'Fayl hajmi 5MB dan oshmasligi kerak'], 422);
            }

            $origName = $validated['name'] ?? 'image.png';
            $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION)) ?: 'png';
            $allowed = ['jpg','jpeg','png','gif','webp','svg'];
            if (!in_array($ext, $allowed, true)) {
                return response()->json(['error' => 'Ruxsat etilmagan fayl turi'], 422);
            }

            $dir = 'cms/content/' . date('Y/m');
            $fileName = \Illuminate\Support\Str::random(40) . '.' . $ext;
            $path = $dir . '/' . $fileName;
            Storage::disk('public')->put($path, $binary);

            CmsMedia::create([
                'name'        => pathinfo($origName, PATHINFO_FILENAME),
                'file_name'   => $origName,
                'mime_type'   => $validated['type'] ?? 'image/' . $ext,
                'path'        => $path,
                'disk'        => 'public',
                'size'        => strlen($binary),
                'folder_id'   => null,
                'alt_text'    => null,
                'caption'     => null,
                'uploaded_by' => Auth::id(),
            ]);

            return response()->json(['location' => asset('storage/' . $path)]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Rasm yuklashda xatolik: ' . $e->getMessage()], 500);
        }
    }
}