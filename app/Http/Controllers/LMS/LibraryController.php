<?php

namespace App\Http\Controllers\LMS;

use App\Http\Controllers\Controller;
use App\Models\LmsLibraryBook;
use App\Models\LmsLibraryCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LibraryController extends Controller
{
    /**
     * Display library main page
     */
    public function index(Request $request)
    {
        // Check if user is authenticated
        if (!auth()->check()) {
            // Return public library view for non-authenticated users
            return view('lms.library.public');
        }

        $query = LmsLibraryBook::with(['libraryCategory', 'uploader'])
            ->active();

        // Filter by category
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by type
        if ($request->has('book_type')) {
            $query->where('book_type', $request->book_type);
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('author', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('isbn', 'like', "%{$search}%");
            });
        }

        $books = $query->orderBy('created_at', 'desc')->paginate(12);

        $categories = LmsLibraryCategory::active()
            ->root()
            ->orderBy('order_number')
            ->get();
        
        $featuredBooks = LmsLibraryBook::featured()
            ->active()
            ->limit(5)
            ->get();
        
        return view('lms.library.index', compact('books', 'categories', 'featuredBooks'));
    }
    
    /**
     * Show categories management page
     */
    public function categories()
    {
        $categories = LmsLibraryCategory::with('parent', 'children')
            ->orderBy('order_number')
            ->get();
        
        return view('lms.library.categories', compact('categories'));
    }
    
    /**
     * Store new category
     */
    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name_uz' => 'required|string|max:255',
            'name_ru' => 'nullable|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:lms_library_categories,id',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:20',
            'order_number' => 'nullable|integer|min:0',
            'is_active' => 'boolean'
        ]);
        
        $validated['slug'] = Str::slug($validated['name_uz']);
        
        LmsLibraryCategory::create($validated);
        
        return redirect()->route('lms.library.categories')
            ->with('success', 'Katalog muvaffaqiyatli yaratildi!');
    }
    
    /**
     * Update category
     */
    public function updateCategory(Request $request, LmsLibraryCategory $category)
    {
        $validated = $request->validate([
            'name_uz' => 'required|string|max:255',
            'name_ru' => 'nullable|string|max:255',
            'name_en' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:lms_library_categories,id',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:20',
            'order_number' => 'nullable|integer|min:0',
            'is_active' => 'boolean'
        ]);
        
        // Prevent setting self as parent
        if ($validated['parent_id'] == $category->id) {
            return back()->with('error', 'Katalog o\'zini ota katalog qilib belgilay olmaydi!');
        }
        
        $category->update($validated);
        
        return redirect()->route('lms.library.categories')
            ->with('success', 'Katalog muvaffaqiyatli yangilandi!');
    }
    
    /**
     * Delete category
     */
    public function destroyCategory(LmsLibraryCategory $category)
    {
        // Check if category has books
        if ($category->books()->exists()) {
            return back()->with('error', 'Bu katalogda kitoblar mavjud. Avval kitoblarni boshqa katalogga ko\'chiring!');
        }
        
        // Check if category has children
        if ($category->children()->exists()) {
            return back()->with('error', 'Bu katalogda ichki kataloglar mavjud. Avval ularni o\'chiring!');
        }
        
        $category->delete();
        
        return redirect()->route('lms.library.categories')
            ->with('success', 'Katalog muvaffaqiyatli o\'chirildi!');
    }
    
    /**
     * Show form for uploading new book
     */
    public function create()
    {
        $categories = LmsLibraryCategory::active()
            ->orderBy('name_uz')
            ->get();
        
        return view('lms.library.create', compact('categories'));
    }
    
    /**
     * Store newly uploaded book
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'isbn' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'publisher' => 'nullable|string|max:255',
            'publication_year' => 'nullable|integer|min:1900|max:' . date('Y'),
            'pages' => 'nullable|integer|min:1',
            'language' => 'required|in:uz,ru,en',
            'category_id' => 'required|exists:lms_library_categories,id',
            'edition' => 'nullable|string|max:50',
            'tags' => 'nullable|string',
            'keywords' => 'nullable|string',
            'file' => 'required|file|mimes:pdf,doc,docx,epub,txt,zip,rar|max:102400', // 100MB
            'cover_image' => 'nullable|image|max:2048',
            'allow_download' => 'boolean',
            'allow_online_reading' => 'boolean',
            'is_featured' => 'boolean'
        ]);
        
        // Handle file upload
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('lms/library/books', $fileName, 'public');
            
            $validated['file_path'] = $filePath;
            $validated['file_name'] = $file->getClientOriginalName();
            $validated['file_type'] = $file->getClientOriginalExtension();
            $validated['file_size'] = $file->getSize();
        }
        
        // Handle cover image upload
        if ($request->hasFile('cover_image')) {
            $cover = $request->file('cover_image');
            $coverPath = $cover->store('lms/library/covers', 'public');
            $validated['cover_image'] = $coverPath;
        }
        
        // Handle tags and keywords
        if (isset($validated['tags'])) {
            $validated['tags'] = array_map('trim', explode(',', $validated['tags']));
        }
        if (isset($validated['keywords'])) {
            $validated['keywords'] = array_map('trim', explode(',', $validated['keywords']));
        }
        
        $validated['slug'] = Str::slug($validated['title']);
        $validated['uploaded_by'] = Auth::id();
        $validated['is_active'] = true;
        
        $book = LmsLibraryBook::create($validated);

        // Update category books count
        $book->libraryCategory->updateBooksCount();

        // Handle AJAX requests
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Kitob muvaffaqiyatli yuklandi!',
                'redirect' => route('lms.library.show', $book)
            ]);
        }

        return redirect()->route('lms.library.show', $book)
            ->with('success', 'Kitob muvaffaqiyatli yuklandi!');
    }
    
    /**
     * Display book details
     */
    public function show(LmsLibraryBook $book)
    {
        if (!$book->is_active) {
            abort(404);
        }
        
        $book->load(['libraryCategory', 'uploader']);
        $book->incrementViewCount();
        
        // Get related books
        $relatedBooks = LmsLibraryBook::where('category_id', $book->category_id)
            ->where('id', '!=', $book->id)
            ->active()
            ->limit(5)
            ->get();
        
        return view('lms.library.show', compact('book', 'relatedBooks'));
    }
    
    /**
     * Show form for editing book
     */
    public function edit(LmsLibraryBook $book)
    {
        $categories = LmsLibraryCategory::active()
            ->orderBy('name_uz')
            ->get();
        
        return view('lms.library.edit', compact('book', 'categories'));
    }
    
    /**
     * Update book information
     */
    public function update(Request $request, LmsLibraryBook $book)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'isbn' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'publisher' => 'nullable|string|max:255',
            'publication_year' => 'nullable|integer|min:1900|max:' . date('Y'),
            'pages' => 'nullable|integer|min:1',
            'language' => 'required|in:uz,ru,en',
            'category_id' => 'required|exists:lms_library_categories,id',
            'edition' => 'nullable|string|max:50',
            'tags' => 'nullable|string',
            'keywords' => 'nullable|string',
            'file' => 'nullable|file|mimes:pdf,doc,docx,epub,txt,zip,rar|max:102400',
            'cover_image' => 'nullable|image|max:2048',
            'allow_download' => 'boolean',
            'allow_online_reading' => 'boolean',
            'is_featured' => 'boolean',
            'is_active' => 'boolean'
        ]);
        
        // Handle file upload if new file provided
        if ($request->hasFile('file')) {
            // Delete old file
            if ($book->file_path) {
                Storage::disk('public')->delete($book->file_path);
            }
            
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('lms/library/books', $fileName, 'public');
            
            $validated['file_path'] = $filePath;
            $validated['file_name'] = $file->getClientOriginalName();
            $validated['file_type'] = $file->getClientOriginalExtension();
            $validated['file_size'] = $file->getSize();
        }
        
        // Handle cover image upload
        if ($request->hasFile('cover_image')) {
            // Delete old cover
            if ($book->cover_image) {
                Storage::disk('public')->delete($book->cover_image);
            }
            
            $cover = $request->file('cover_image');
            $coverPath = $cover->store('lms/library/covers', 'public');
            $validated['cover_image'] = $coverPath;
        }
        
        // Handle tags and keywords
        if (isset($validated['tags'])) {
            $validated['tags'] = array_map('trim', explode(',', $validated['tags']));
        }
        if (isset($validated['keywords'])) {
            $validated['keywords'] = array_map('trim', explode(',', $validated['keywords']));
        }
        
        $oldCategoryId = $book->category_id;
        $book->update($validated);
        
        // Update category books count if category changed
        if ($oldCategoryId != $book->category_id) {
            if ($oldCategory = LmsLibraryCategory::find($oldCategoryId)) {
                $oldCategory->updateBooksCount();
            }
            $book->libraryCategory->updateBooksCount();
        }
        
        return redirect()->route('lms.library.show', $book)
            ->with('success', 'Kitob muvaffaqiyatli yangilandi!');
    }
    
    /**
     * Delete book
     */
    public function destroy(LmsLibraryBook $book)
    {
        // Delete files
        if ($book->file_path) {
            Storage::disk('public')->delete($book->file_path);
        }
        if ($book->cover_image) {
            Storage::disk('public')->delete($book->cover_image);
        }
        
        $categoryId = $book->category_id;
        $book->delete();
        
        // Update category books count
        if ($category = LmsLibraryCategory::find($categoryId)) {
            $category->updateBooksCount();
        }
        
        return redirect()->route('lms.library.index')
            ->with('success', 'Kitob muvaffaqiyatli o\'chirildi!');
    }
    
    /**
     * Download book file
     */
    public function download(LmsLibraryBook $book)
    {
        if (!$book->allow_download) {
            return back()->with('error', 'Bu kitobni yuklab olish mumkin emas!');
        }
        
        if (!$book->file_path || !Storage::disk('public')->exists($book->file_path)) {
            return back()->with('error', 'Fayl topilmadi!');
        }
        
        $book->incrementDownloadCount();
        
        return Storage::disk('public')->download($book->file_path, $book->file_name);
    }
    
    /**
     * Read book online
     */
    public function read(LmsLibraryBook $book)
    {
        if (!$book->allow_online_reading) {
            return back()->with('error', 'Bu kitobni online o\'qish mumkin emas!');
        }
        
        if (!in_array($book->file_type, ['pdf', 'txt'])) {
            return back()->with('error', 'Bu formatdagi faylni online o\'qib bo\'lmaydi!');
        }
        
        $book->incrementViewCount();
        
        return view('lms.library.read', compact('book'));
    }
}