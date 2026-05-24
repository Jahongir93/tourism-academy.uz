<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    protected $fillable = [
        'parent_id',
        'label_uz',
        'label_en',
        'label_ru',
        'url',
        'icon',
        'order',
        'is_active',
        'open_in_new_tab'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'open_in_new_tab' => 'boolean'
    ];

    public function parent()
    {
        return $this->belongsTo(MenuItem::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(MenuItem::class, 'parent_id')->orderBy('order');
    }

    public function getLabel($locale = null)
    {
        if ($locale === null) {
            $locale = app()->getLocale();
        }

        $column = "label_{$locale}";
        return $this->$column ?? $this->label_uz;
    }

    public static function getActiveMenuItems()
    {
        return self::where('is_active', true)
            ->whereNull('parent_id')
            ->with(['children' => function ($query) {
                $query->where('is_active', true)->orderBy('order');
            }])
            ->orderBy('order')
            ->get();
    }
}
