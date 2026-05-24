<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CmsTemplate extends Model
{
    protected $fillable = [
        'name', 'slug', 'description', 'type',
        'html_structure', 'css', 'js',
        'variables', 'sections', 'preview_image',
        'is_active', 'is_default'
    ];

    protected $casts = [
        'variables' => 'array',
        'sections' => 'array',
        'is_active' => 'boolean',
        'is_default' => 'boolean'
    ];

    protected static function booted()
    {
        static::creating(function ($template) {
            if ($template->is_default) {
                self::where('type', $template->type)
                    ->where('is_default', true)
                    ->update(['is_default' => false]);
            }
        });
        
        static::updating(function ($template) {
            if ($template->is_default) {
                self::where('type', $template->type)
                    ->where('id', '!=', $template->id)
                    ->where('is_default', true)
                    ->update(['is_default' => false]);
            }
        });
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    public static function getDefault($type)
    {
        return self::active()
            ->byType($type)
            ->default()
            ->first();
    }
}