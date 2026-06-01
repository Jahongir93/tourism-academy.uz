<?php

namespace App\Helpers;

use App\Models\CmsSetting;
use Illuminate\Support\Facades\Cache;

class TemplateHelper
{
    const CACHE_KEY = 'active_template';
    const CACHE_TTL = 300; // 5 minutes

    /**
     * Get the active template name
     *
     * @return string
     */
    public static function getActiveTemplate(): string
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            try {
                $setting = CmsSetting::where('key', 'active_template')->first();
                return $setting ? $setting->value : 'classic';
            } catch (\Throwable $e) {
                // Table may not exist yet (e.g. during migrations)
                return 'classic';
            }
        });
    }

    /**
     * Clear the cached template so changes take effect immediately
     */
    public static function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * Get the layout file for the active template
     *
     * @return string
     */
    public static function getLayout(): string
    {
        $template = self::getActiveTemplate();

        return match ($template) {
            'academic' => 'layouts.public-academic',
            default => 'layouts.public',
        };
    }

    /**
     * Check if a specific template is active
     *
     * @param string $templateName
     * @return bool
     */
    public static function isTemplate(string $templateName): bool
    {
        return self::getActiveTemplate() === $templateName;
    }
}
