<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class LmsLibraryBook extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'author',
        'isbn',
        'description',
        'publisher',
        'publication_year',
        'language',
        'pages',
        'category',
        'category_id',
        'subjects',
        'file_path',
        'file_name',
        'file_type',
        'file_size',
        'cover_image',
        'book_type',
        'edition',
        'tags',
        'keywords',
        'is_featured',
        'is_active',
        'allow_download',
        'allow_online_reading',
        'view_count',
        'download_count',
        'rating',
        'rating_count',
        'uploaded_by'
    ];

    protected $casts = [
        'subjects' => 'array',
        'tags' => 'array',
        'keywords' => 'array',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'allow_download' => 'boolean',
        'allow_online_reading' => 'boolean',
        'publication_year' => 'integer',
        'pages' => 'integer',
        'file_size' => 'integer',
        'view_count' => 'integer',
        'download_count' => 'integer',
        'rating' => 'decimal:2',
        'rating_count' => 'integer'
    ];
    
    protected static function booted()
    {
        static::creating(function ($book) {
            if (empty($book->slug)) {
                $book->slug = Str::slug($book->title);
            }
        });
    }

    public function readingProgress(): HasMany
    {
        return $this->hasMany(LmsReadingProgress::class, 'book_id');
    }
    
    public function libraryCategory(): BelongsTo
    {
        return $this->belongsTo(LmsLibraryCategory::class, 'category_id');
    }
    
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function incrementViewCount()
    {
        $this->increment('view_count');
    }

    public function incrementDownloadCount()
    {
        $this->increment('download_count');
    }

    public function updateRating(float $newRating)
    {
        $totalRating = ($this->rating ?? 0) * $this->rating_count;
        $this->rating_count++;
        $this->rating = ($totalRating + $newRating) / $this->rating_count;
        $this->save();
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('book_type', $type);
    }
    
    public function scopeDownloadable($query)
    {
        return $query->where('allow_download', true);
    }
    
    public function scopeReadable($query)
    {
        return $query->where('allow_online_reading', true);
    }
    
    public function getFileSizeFormattedAttribute()
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }
    
    public function getFileIconAttribute()
    {
        $icons = [
            'pdf' => 'fa-file-pdf text-red-500',
            'doc' => 'fa-file-word text-blue-500',
            'docx' => 'fa-file-word text-blue-500',
            'epub' => 'fa-book text-green-500',
            'txt' => 'fa-file-text text-gray-500',
            'zip' => 'fa-file-archive text-purple-500',
            'rar' => 'fa-file-archive text-purple-500'
        ];
        
        return $icons[$this->file_type] ?? 'fa-file text-gray-500';
    }
}