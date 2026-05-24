<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CmsMenu extends Model
{
    protected $fillable = [
        'name', 'location', 'items', 'is_active'
    ];

    protected $casts = [
        'items' => 'array',
        'is_active' => 'boolean'
    ];

    public function menuItems(): HasMany
    {
        return $this->hasMany(CmsMenuItem::class, 'menu_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByLocation($query, $location)
    {
        return $query->where('location', $location);
    }

    public function getStructuredItemsAttribute()
    {
        $items = $this->menuItems()->orderBy('order_position')->get();
        return $this->buildTree($items);
    }

    private function buildTree($items, $parentId = null)
    {
        $branch = [];
        
        foreach ($items as $item) {
            if ($item->parent_id == $parentId) {
                $children = $this->buildTree($items, $item->id);
                if ($children) {
                    $item->children = $children;
                }
                $branch[] = $item;
            }
        }
        
        return $branch;
    }

    public static function getMenuByLocation($location)
    {
        return self::active()->byLocation($location)->first();
    }
}