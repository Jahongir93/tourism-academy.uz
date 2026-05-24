<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LmsReadingProgress extends Model
{
    use HasFactory;

    protected $table = 'lms_reading_progress';

    protected $fillable = [
        'user_id',
        'book_id',
        'current_page',
        'total_pages',
        'progress_percentage',
        'last_read_at',
        'reading_time',
        'bookmarks',
        'notes',
        'is_completed',
        'completed_at'
    ];

    protected $casts = [
        'bookmarks' => 'array',
        'is_completed' => 'boolean',
        'current_page' => 'integer',
        'total_pages' => 'integer',
        'progress_percentage' => 'decimal:2',
        'reading_time' => 'integer',
        'last_read_at' => 'datetime',
        'completed_at' => 'datetime'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(LmsLibraryBook::class, 'book_id');
    }

    public function updateProgress(int $currentPage)
    {
        $this->current_page = $currentPage;
        $this->progress_percentage = ($currentPage / $this->total_pages) * 100;
        $this->last_read_at = now();
        
        if ($currentPage >= $this->total_pages) {
            $this->is_completed = true;
            $this->completed_at = now();
        }
        
        $this->save();
    }

    public function addBookmark(int $pageNumber)
    {
        $bookmarks = $this->bookmarks ?? [];
        if (!in_array($pageNumber, $bookmarks)) {
            $bookmarks[] = $pageNumber;
            sort($bookmarks);
            $this->bookmarks = $bookmarks;
            $this->save();
        }
    }

    public function removeBookmark(int $pageNumber)
    {
        $bookmarks = $this->bookmarks ?? [];
        $bookmarks = array_values(array_diff($bookmarks, [$pageNumber]));
        $this->bookmarks = $bookmarks;
        $this->save();
    }

    public function incrementReadingTime(int $minutes)
    {
        $this->increment('reading_time', $minutes);
        $this->last_read_at = now();
        $this->save();
    }
}