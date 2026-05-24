<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Citizenship;

class CitizenshipSeeder extends Seeder
{
    public function run()
    {
        $citizenships = [
            ['code' => 'UZ', 'name_uz' => 'O\'zbekiston Respublikasi', 'name_ru' => 'Республика Узбекистан', 'name_en' => 'Republic of Uzbekistan'],
            ['code' => 'RU', 'name_uz' => 'Rossiya Federatsiyasi', 'name_ru' => 'Российская Федерация', 'name_en' => 'Russian Federation'],
            ['code' => 'KZ', 'name_uz' => 'Qozog\'iston Respublikasi', 'name_ru' => 'Республика Казахстан', 'name_en' => 'Republic of Kazakhstan'],
            ['code' => 'KG', 'name_uz' => 'Qirg\'iziston Respublikasi', 'name_ru' => 'Кыргызская Республика', 'name_en' => 'Kyrgyz Republic'],
            ['code' => 'TJ', 'name_uz' => 'Tojikiston Respublikasi', 'name_ru' => 'Республика Таджикистан', 'name_en' => 'Republic of Tajikistan'],
            ['code' => 'TM', 'name_uz' => 'Turkmaniston', 'name_ru' => 'Туркменистан', 'name_en' => 'Turkmenistan'],
            ['code' => 'AZ', 'name_uz' => 'Ozarbayjon Respublikasi', 'name_ru' => 'Азербайджанская Республика', 'name_en' => 'Republic of Azerbaijan'],
            ['code' => 'BY', 'name_uz' => 'Belarus Respublikasi', 'name_ru' => 'Республика Беларусь', 'name_en' => 'Republic of Belarus'],
            ['code' => 'UA', 'name_uz' => 'Ukraina', 'name_ru' => 'Украина', 'name_en' => 'Ukraine'],
            ['code' => 'GE', 'name_uz' => 'Gruziya', 'name_ru' => 'Грузия', 'name_en' => 'Georgia'],
            ['code' => 'AM', 'name_uz' => 'Armaniston Respublikasi', 'name_ru' => 'Республика Армения', 'name_en' => 'Republic of Armenia'],
            ['code' => 'CN', 'name_uz' => 'Xitoy Xalq Respublikasi', 'name_ru' => 'Китайская Народная Республика', 'name_en' => 'People\'s Republic of China'],
            ['code' => 'KR', 'name_uz' => 'Koreya Respublikasi', 'name_ru' => 'Республика Корея', 'name_en' => 'Republic of Korea'],
            ['code' => 'IN', 'name_uz' => 'Hindiston', 'name_ru' => 'Индия', 'name_en' => 'India'],
            ['code' => 'PK', 'name_uz' => 'Pokiston', 'name_ru' => 'Пакистан', 'name_en' => 'Pakistan'],
            ['code' => 'AF', 'name_uz' => 'Afg\'oniston', 'name_ru' => 'Афганистан', 'name_en' => 'Afghanistan'],
            ['code' => 'IR', 'name_uz' => 'Eron', 'name_ru' => 'Иран', 'name_en' => 'Iran'],
            ['code' => 'TR', 'name_uz' => 'Turkiya', 'name_ru' => 'Турция', 'name_en' => 'Turkey'],
            ['code' => 'US', 'name_uz' => 'AQSh', 'name_ru' => 'США', 'name_en' => 'USA'],
            ['code' => 'DE', 'name_uz' => 'Germaniya', 'name_ru' => 'Германия', 'name_en' => 'Germany'],
            ['code' => 'GB', 'name_uz' => 'Buyuk Britaniya', 'name_ru' => 'Великобритания', 'name_en' => 'United Kingdom'],
            ['code' => 'FR', 'name_uz' => 'Fransiya', 'name_ru' => 'Франция', 'name_en' => 'France'],
            ['code' => 'XX', 'name_uz' => 'Fuqaroligi yo\'q', 'name_ru' => 'Без гражданства', 'name_en' => 'Stateless'],
            ['code' => 'OT', 'name_uz' => 'Boshqa', 'name_ru' => 'Другое', 'name_en' => 'Other'],
        ];

        foreach ($citizenships as $citizenship) {
            Citizenship::create([
                'code' => $citizenship['code'],
                'name_uz' => $citizenship['name_uz'],
                'name_ru' => $citizenship['name_ru'],
                'name_en' => $citizenship['name_en'],
                'is_active' => true
            ]);
        }
    }
}