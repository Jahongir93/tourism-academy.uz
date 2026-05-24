<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FaceEncoding extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'encoding_data',
        'image_path',
        'metadata'
    ];

    protected $casts = [
        'encoding_data' => 'array',
        'metadata' => 'array'
    ];

    /**
     * Get the user that owns the face encoding
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}