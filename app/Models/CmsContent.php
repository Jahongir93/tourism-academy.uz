<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CmsContent extends Model
{
    protected $fillable = [
        'section',
        'key',
        'value_uz',
        'value_en',
        'value_ru',
        'type',
        'order',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function getValue($locale = null)
    {
        if ($locale === null) {
            $locale = app()->getLocale();
        }

        $column = "value_{$locale}";
        return $this->$column ?? $this->value_uz;
    }

    public static function getContent($section, $key, $locale = null)
    {
        $content = self::where('section', $section)
            ->where('key', $key)
            ->first();

        return $content ? $content->getValue($locale) : null;
    }

    public static function getSectionContent($section, $locale = null)
    {
        return self::where('section', $section)
            ->orderBy('order')
            ->get()
            ->mapWithKeys(function ($item) use ($locale) {
                return [$item->key => $item->getValue($locale)];
            });
    }
}
