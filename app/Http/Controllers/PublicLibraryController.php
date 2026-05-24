<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LmsLibraryBook;
use App\Models\LmsLibraryCategory;
use Illuminate\Support\Facades\DB;

class PublicLibraryController extends Controller
{
    public function index(Request $request)
    {
        try {
            // Check database connection
            DB::connection()->getPdo();

            // Try to get books from database
            $query = LmsLibraryBook::where('is_active', true);

            // Filter by category
            if ($request->has('category_id') && $request->category_id) {
                $query->where('category_id', $request->category_id);
            }

            // Search
            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('author', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }

            $books = $query->orderBy('created_at', 'desc')->paginate(12);

            // Get categories from database
            $categories = LmsLibraryCategory::where('is_active', true)
                ->withCount(['books' => function ($query) {
                    $query->where('is_active', true);
                }])
                ->orderBy('order_number')
                ->get();

            // Statistics
            $stats = [
                'total_books' => LmsLibraryBook::where('is_active', true)->count(),
                'new_books' => LmsLibraryBook::where('is_active', true)
                    ->where('created_at', '>=', now()->subDays(30))
                    ->count(),
                'total_downloads' => LmsLibraryBook::sum('download_count') ?? 0
            ];

            return view('lms.library.public', compact('books', 'categories', 'stats'));

        } catch (\Exception $e) {
            // If database error or no data, return empty
            $books = collect([]);
            $categories = collect([]);
            $stats = [
                'total_books' => 0,
                'new_books' => 0,
                'total_downloads' => 0
            ];

            return view('lms.library.public', compact('books', 'categories', 'stats'));
        }
    }

    public function show($id)
    {
        try {
            $book = LmsLibraryBook::findOrFail($id);

            // Increment view count
            $book->increment('view_count');

            return view('lms.library.show', compact('book'));
        } catch (\Exception $e) {
            session()->flash('error', 'Kitob topilmadi.');
            return redirect()->route('public.library');
        }
    }

    public function download($id)
    {
        // Check if user is authenticated
        if (!auth()->check()) {
            session()->flash('info', 'Kitobni yuklab olish uchun tizimga kirishingiz kerak.');
            return redirect()->route('login');
        }

        try {
            $book = LmsLibraryBook::findOrFail($id);

            if (!$book->allow_download) {
                session()->flash('warning', 'Bu kitobni yuklab olish mumkin emas.');
                return redirect()->back();
            }

            // Increment download count
            $book->increment('download_count');

            // Return file download
            if ($book->file_path && file_exists(storage_path('app/' . $book->file_path))) {
                return response()->download(
                    storage_path('app/' . $book->file_path),
                    $book->file_name ?? $book->title . '.pdf'
                );
            }

            session()->flash('error', 'Kitob fayli topilmadi.');
            return redirect()->back();

        } catch (\Exception $e) {
            session()->flash('error', 'Xatolik yuz berdi.');
            return redirect()->route('public.library');
        }
    }
}