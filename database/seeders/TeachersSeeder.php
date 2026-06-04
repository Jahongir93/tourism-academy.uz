<?php

namespace Database\Seeders;

use App\Models\Employee;
use Illuminate\Database\Seeder;

/**
 * Seeds the previously-hardcoded public "Teachers" page entries into the
 * employees table (employee_type = teacher) so they are DYNAMIC and editable
 * via /employees and /cms/teachers, and shown on the public /teachers page.
 * Idempotent: matched by full_name.
 */
class TeachersSeeder extends Seeder
{
    public function run(): void
    {
        $teachers = [
            ['Odilov Akmaljon', 'Kafedra mudiri', 'Odilov Akmaljon.JPG',
                "Iqtisod fanlari bo'yicha falsafa doktori (PhD). London School of Business and Finance, Buyuk Britaniyada Business English o'qigan.",
                'Кандидат экономических наук (PhD). Изучал деловой английский в London School of Business and Finance, Великобритания.',
                'PhD in Economics. Studied Business English at London School of Business and Finance, UK.'],
            ['Klykov Artem', 'PhD, Dotsent', 'Klykov Artem.JPG',
                "Turizm kafedrasi shtatli o'qituvchisi (PhD). Shveytsariyaning Les Roches universitetida magistratura dasturini tugatgan.",
                'Штатный преподаватель кафедры туризма (PhD). Закончил магистратуру в университете Les Roches, Швейцария.',
                'Tourism department staff teacher (PhD). Completed masters program at Les Roches University, Switzerland.'],
            ['Xusen Ibragimov', 'PhD, Dotsent', 'Xusen Ibragimov.JPG',
                "Turizm kafedrasi shtatli o'qituvchisi (PhD). Ispaniyaning Alikante universitetida PhD dissertatsiyasini himoya qilgan.",
                'Штатный преподаватель кафедры туризма (PhD). Защитил диссертацию в университете Аликанте, Испания.',
                'Tourism department staff teacher (PhD). Defended PhD dissertation at University of Alicante, Spain.'],
            ['Nilufar Rakhimova', "PhD, Katta o'qituvchi", 'Nilufar Rakhimova.JPG',
                "Westminster universitetining Toshkent kampusida PhD dissertatsiyasini muvaffaqiyatli himoya qilgan.",
                'Успешно защитила диссертацию PhD в Ташкентском кампусе Вестминстерского университета.',
                'Successfully defended PhD dissertation at Westminster University Tashkent campus.'],
            ['Abdurasul Akhmadjonov', "Katta o'qituvchi", 'Abdurasul Akhmadjonov.JPG',
                "Turizm va mehmondo'stlik sohasida 10 yillik tajribaga ega mutaxassis.",
                'Специалист с 10-летним опытом в сфере туризма и гостеприимства.',
                'Specialist with 10 years of experience in tourism and hospitality.'],
            ['Botir Rakhmatullaev', 'Dotsent', 'Botir Rakhmatullaev.JPG',
                "Iqtisodiyot fanlari nomzodi. Turizm iqtisodiyoti va marketing bo'yicha mutaxassis.",
                'Кандидат экономических наук. Специалист по экономике туризма и маркетингу.',
                'Candidate of Economic Sciences. Specialist in tourism economics and marketing.'],
            ['Charos Makhmadieva', "O'qituvchi", 'Charos Makhmadieva.JPG',
                "Mehmondo'stlik menejment bo'yicha magistr. Xalqaro mehmonxonalarda amaliyot o'tagan.",
                'Магистр по менеджменту гостеприимства. Проходила практику в международных отелях.',
                'Master in Hospitality Management. Completed internship at international hotels.'],
            ['Dilnoza Muxidinova', "O'qituvchi", 'Dilnoza Muxidinova.JPG',
                "Turizm va xalqaro munosabatlar bo'yicha mutaxassis. Bir nechta xalqaro konferensiyalarda ishtirok etgan.",
                'Специалист по туризму и международным отношениям. Участвовала в нескольких международных конференциях.',
                'Specialist in tourism and international relations. Participated in several international conferences.'],
        ];

        foreach ($teachers as $i => [$fullName, $position, $image, $bioUz, $bioRu, $bioEn]) {
            $parts = preg_split('/\s+/', trim($fullName));
            $first = $parts[0] ?? $fullName;
            $last  = isset($parts[1]) ? implode(' ', array_slice($parts, 1)) : '';

            Employee::updateOrCreate(
                ['full_name' => $fullName, 'employee_type' => 'teacher'],
                [
                    'employee_code' => Employee::where('employee_code', 'like', 'TCH-%')->exists()
                        ? 'TCH-' . str_pad((string) ($i + 1), 4, '0', STR_PAD_LEFT)
                        : 'TCH-' . str_pad((string) ($i + 1), 4, '0', STR_PAD_LEFT),
                    'first_name'   => $first,
                    'last_name'    => $last,
                    'birth_date'        => '1985-01-01',
                    'gender'            => 'male',
                    'phone'             => '+99800000' . str_pad((string) ($i + 1), 4, '0', STR_PAD_LEFT),
                    'address_permanent' => 'Samarqand',
                    'position'          => $position,
                    'photo_url'    => 'assets/images/teachers/' . $image,
                    'bio_uz'       => $bioUz,
                    'bio_ru'       => $bioRu,
                    'bio_en'       => $bioEn,
                    'show_on_site' => true,
                    'public_order' => $i + 1,
                    'status'       => 'active',
                ]
            );
        }

        $this->command->info('TeachersSeeder: ' . count($teachers) . " o'qituvchi employees jadvaliga qo'shildi.");
    }
}
