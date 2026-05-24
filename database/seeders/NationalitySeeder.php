<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Nationality;

class NationalitySeeder extends Seeder
{
    public function run()
    {
        $nationalities = [
            ['name_uz' => 'O\'zbek', 'name_ru' => 'Узбек', 'name_en' => 'Uzbek'],
            ['name_uz' => 'Qozoq', 'name_ru' => 'Казах', 'name_en' => 'Kazakh'],
            ['name_uz' => 'Rus', 'name_ru' => 'Русский', 'name_en' => 'Russian'],
            ['name_uz' => 'Tojik', 'name_ru' => 'Таджик', 'name_en' => 'Tajik'],
            ['name_uz' => 'Qirg\'iz', 'name_ru' => 'Киргиз', 'name_en' => 'Kyrgyz'],
            ['name_uz' => 'Turkman', 'name_ru' => 'Туркмен', 'name_en' => 'Turkmen'],
            ['name_uz' => 'Tatar', 'name_ru' => 'Татар', 'name_en' => 'Tatar'],
            ['name_uz' => 'Qoraqalpoq', 'name_ru' => 'Каракалпак', 'name_en' => 'Karakalpak'],
            ['name_uz' => 'Koreys', 'name_ru' => 'Кореец', 'name_en' => 'Korean'],
            ['name_uz' => 'Uyg\'ur', 'name_ru' => 'Уйгур', 'name_en' => 'Uyghur'],
            ['name_uz' => 'Ozarbayjon', 'name_ru' => 'Азербайджанец', 'name_en' => 'Azerbaijani'],
            ['name_uz' => 'Arman', 'name_ru' => 'Армянин', 'name_en' => 'Armenian'],
            ['name_uz' => 'Yahudiy', 'name_ru' => 'Еврей', 'name_en' => 'Jewish'],
            ['name_uz' => 'Nemis', 'name_ru' => 'Немец', 'name_en' => 'German'],
            ['name_uz' => 'Belarus', 'name_ru' => 'Белорус', 'name_en' => 'Belarusian'],
            ['name_uz' => 'Ukrain', 'name_ru' => 'Украинец', 'name_en' => 'Ukrainian'],
            ['name_uz' => 'Gruzin', 'name_ru' => 'Грузин', 'name_en' => 'Georgian'],
            ['name_uz' => 'Dungan', 'name_ru' => 'Дунган', 'name_en' => 'Dungan'],
            ['name_uz' => 'Kurd', 'name_ru' => 'Курд', 'name_en' => 'Kurdish'],
            ['name_uz' => 'Boshqa', 'name_ru' => 'Другая', 'name_en' => 'Other'],
        ];

        foreach ($nationalities as $nationality) {
            Nationality::create([
                'name_uz' => $nationality['name_uz'],
                'name_ru' => $nationality['name_ru'],
                'name_en' => $nationality['name_en'],
                'is_active' => true
            ]);
        }
    }
}