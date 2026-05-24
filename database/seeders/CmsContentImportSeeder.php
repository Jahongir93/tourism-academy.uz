<?php

namespace Database\Seeders;

use App\Models\CmsContent;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class CmsContentImportSeeder extends Seeder
{
    /**
     * JSON fayldan CMS kontentini import qilish
     *
     * Ishlatish:
     * 1. storage/import_templates/sahifalar_shablon.json faylini tahrirlang
     * 2. php artisan db:seed --class=CmsContentImportSeeder
     */
    public function run(): void
    {
        $jsonPath = storage_path('import_templates/sahifalar_shablon.json');

        if (!File::exists($jsonPath)) {
            $this->command->error("JSON fayl topilmadi: {$jsonPath}");
            return;
        }

        $data = json_decode(File::get($jsonPath), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->command->error("JSON xatosi: " . json_last_error_msg());
            return;
        }

        // Har bir bo'limni import qilish
        foreach ($data as $section => $items) {
            // Yo'riqnoma va social_links ni o'tkazib yuborish
            if ($section === '_yo\'riqnoma' || $section === 'social_links') {
                continue;
            }

            if (!is_array($items)) {
                continue;
            }

            foreach ($items as $key => $translations) {
                if (!is_array($translations)) {
                    continue;
                }

                // 3 tilli kontentni saqlash
                CmsContent::updateOrCreate(
                    ['section' => $section, 'key' => $key],
                    [
                        'value_uz' => $translations['uz'] ?? '',
                        'value_ru' => $translations['ru'] ?? '',
                        'value_en' => $translations['en'] ?? '',
                    ]
                );

                $this->command->info("Import qilindi: [{$section}] {$key}");
            }
        }

        // Social links ni alohida saqlash
        if (isset($data['social_links'])) {
            foreach ($data['social_links'] as $platform => $url) {
                CmsContent::updateOrCreate(
                    ['section' => 'social', 'key' => $platform],
                    [
                        'value_uz' => $url,
                        'value_ru' => $url,
                        'value_en' => $url,
                    ]
                );
            }
            $this->command->info("Ijtimoiy tarmoq havolalari saqlandi");
        }

        $this->command->info("✓ Barcha ma'lumotlar muvaffaqiyatli import qilindi!");
    }
}
