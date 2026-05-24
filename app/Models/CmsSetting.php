<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class CmsSetting extends Model
{
    protected $table = 'cms_settings';

    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'label',
        'description',
        'options',
        'order_position',
        'is_public',
    ];

    protected $casts = [
        'options' => 'array',
        'is_public' => 'boolean',
    ];

    /**
     * Get a setting value by key
     */
    public static function get(string $key, $default = null)
    {
        $setting = Cache::remember("cms_setting_{$key}", 3600, function () use ($key) {
            return static::where('key', $key)->first();
        });

        return $setting ? $setting->value : $default;
    }

    /**
     * Set a setting value
     */
    public static function set(string $key, $value): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );

        Cache::forget("cms_setting_{$key}");
    }

    /**
     * Get current theme
     */
    public static function getCurrentTheme(): string
    {
        return static::get('site_theme', 'theme1');
    }

    /**
     * Get available themes
     */
    public static function getAvailableThemes(): array
    {
        return [
            'theme1' => [
                'name' => 'Classic Blue',
                'name_uz' => 'Klassik Moviy',
                'name_ru' => 'Классический Синий',
                'primary' => '#4338CA',
                'secondary' => '#6366F1',
                'accent' => '#CCFF00',
                'description' => 'Professional indigo blue theme',
            ],
            'theme2' => [
                'name' => 'Ocean Green',
                'name_uz' => 'Okean Yashil',
                'name_ru' => 'Океанский Зелёный',
                'primary' => '#059669',
                'secondary' => '#10B981',
                'accent' => '#34D399',
                'description' => 'Fresh emerald green theme',
            ],
            'theme3' => [
                'name' => 'Sunset Orange',
                'name_uz' => 'Quyosh Botishi',
                'name_ru' => 'Закатный Оранжевый',
                'primary' => '#EA580C',
                'secondary' => '#F97316',
                'accent' => '#FBBF24',
                'description' => 'Warm sunset orange theme',
            ],
            'theme4' => [
                'name' => 'Royal Purple',
                'name_uz' => 'Qirollik Binafsha',
                'name_ru' => 'Королевский Пурпурный',
                'primary' => '#7C3AED',
                'secondary' => '#8B5CF6',
                'accent' => '#A78BFA',
                'description' => 'Elegant purple theme',
            ],
            'theme5' => [
                'name' => 'Midnight Dark',
                'name_uz' => 'Yarim Tun',
                'name_ru' => 'Полуночный Тёмный',
                'primary' => '#1E293B',
                'secondary' => '#334155',
                'accent' => '#38BDF8',
                'description' => 'Modern dark mode theme',
            ],
        ];
    }
}
