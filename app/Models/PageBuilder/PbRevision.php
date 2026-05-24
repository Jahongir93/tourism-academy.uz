<?php

namespace App\Models\PageBuilder;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class PbRevision extends Model
{
    protected $table = 'pb_revisions';

    protected $fillable = [
        'page_id',
        'content',
        'revision_type',
        'created_by'
    ];

    protected $casts = [
        'content' => 'array'
    ];

    public function page(): BelongsTo
    {
        return $this->belongsTo(PbPage::class, 'page_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}