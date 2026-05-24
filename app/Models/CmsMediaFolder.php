<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class CmsMediaFolder extends Model
{
    protected $fillable = [
        'name', 'slug', 'parent_id', 'order_position'
    ];

    protected $casts = [
        'order_position' => 'integer'
    ];

    protected static function booted()
    {
        static::creating(function ($folder) {
            if (empty($folder->slug)) {
                $folder->slug = Str::slug($folder->name);
            }
        });
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(CmsMediaFolder::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(CmsMediaFolder::class, 'parent_id');
    }

    public function media(): HasMany
    {
        return $this->hasMany(CmsMedia::class, 'folder_id');
    }

    public function getFullPathAttribute()
    {
        $path = [$this->name];
        $parent = $this->parent;
        
        while ($parent) {
            array_unshift($path, $parent->name);
            $parent = $parent->parent;
        }
        
        return implode(' / ', $path);
    }
}