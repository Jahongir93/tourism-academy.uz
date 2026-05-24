<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Specialty;
use App\Models\Faculty;

class SpecialtiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get faculties
        $faculties = Faculty::all();

        if ($faculties->isEmpty()) {
            $this->command->error('Fakultetlar mavjud emas! Avval fakultetlarni qo\'shing.');
            return;
        }

        $specialties = [
            // Turizm fakulteti
            [
                'faculty_name' => 'Turizm',
                'specialties' => [
                    [
                        'code' => '60810100',
                        'name_uz' => 'Turizm',
                        'name_ru' => 'Туризм',
                        'name_en' => 'Tourism',
                        'direction_code' => '5A810100',
                        'degree' => 'bakalavr',
                        'education_form' => 'kunduzgi',
                        'education_type' => 'shartnoma',
                        'duration_years' => 4,
                        'credits_required' => 240,
                        'tuition_fee' => 8000000
                    ],
                    [
                        'code' => '60810200',
                        'name_uz' => 'Mehmondo\'stlik',
                        'name_ru' => 'Гостиничное дело',
                        'name_en' => 'Hospitality Management',
                        'direction_code' => '5A810200',
                        'degree' => 'bakalavr',
                        'education_form' => 'kunduzgi',
                        'education_type' => 'shartnoma',
                        'duration_years' => 4,
                        'credits_required' => 240,
                        'tuition_fee' => 7500000
                    ],
                ]
            ],
            // Iqtisodiyot fakulteti
            [
                'faculty_name' => 'Iqtisodiyot',
                'specialties' => [
                    [
                        'code' => '60310100',
                        'name_uz' => 'Iqtisodiyot',
                        'name_ru' => 'Экономика',
                        'name_en' => 'Economics',
                        'direction_code' => '5A310100',
                        'degree' => 'bakalavr',
                        'education_form' => 'kunduzgi',
                        'education_type' => 'shartnoma',
                        'duration_years' => 4,
                        'credits_required' => 240,
                        'tuition_fee' => 7000000
                    ],
                    [
                        'code' => '60310200',
                        'name_uz' => 'Boshqaruv',
                        'name_ru' => 'Менеджмент',
                        'name_en' => 'Management',
                        'direction_code' => '5A310200',
                        'degree' => 'bakalavr',
                        'education_form' => 'kunduzgi',
                        'education_type' => 'shartnoma',
                        'duration_years' => 4,
                        'credits_required' => 240,
                        'tuition_fee' => 7000000
                    ],
                ]
            ],
            // Muhandislik fakulteti
            [
                'faculty_name' => 'Muhandislik',
                'specialties' => [
                    [
                        'code' => '60230100',
                        'name_uz' => 'Filologiya (ingliz tili)',
                        'name_ru' => 'Филология (английский язык)',
                        'name_en' => 'Philology (English)',
                        'direction_code' => '5A230100',
                        'degree' => 'bakalavr',
                        'education_form' => 'kunduzgi',
                        'education_type' => 'shartnoma',
                        'duration_years' => 4,
                        'credits_required' => 240,
                        'tuition_fee' => 6500000
                    ],
                    [
                        'code' => '60230101',
                        'name_uz' => 'Tarjimashunoslik (ingliz tili)',
                        'name_ru' => 'Переводоведение (английский язык)',
                        'name_en' => 'Translation Studies (English)',
                        'direction_code' => '5A230101',
                        'degree' => 'bakalavr',
                        'education_form' => 'kunduzgi',
                        'education_type' => 'shartnoma',
                        'duration_years' => 4,
                        'credits_required' => 240,
                        'tuition_fee' => 6500000
                    ],
                ]
            ],
            // Axborot texnologiyalari fakulteti
            [
                'faculty_name' => 'Axborot texnologiyalari',
                'specialties' => [
                    [
                        'code' => '60610100',
                        'name_uz' => 'Axborot texnologiyalari',
                        'name_ru' => 'Информационные технологии',
                        'name_en' => 'Information Technology',
                        'direction_code' => '5A610100',
                        'degree' => 'bakalavr',
                        'education_form' => 'kunduzgi',
                        'education_type' => 'shartnoma',
                        'duration_years' => 4,
                        'credits_required' => 240,
                        'tuition_fee' => 9000000
                    ],
                    [
                        'code' => '60610200',
                        'name_uz' => 'Dasturiy injiniring',
                        'name_ru' => 'Программная инженерия',
                        'name_en' => 'Software Engineering',
                        'direction_code' => '5A610200',
                        'degree' => 'bakalavr',
                        'education_form' => 'kunduzgi',
                        'education_type' => 'shartnoma',
                        'duration_years' => 4,
                        'credits_required' => 240,
                        'tuition_fee' => 9000000
                    ],
                ]
            ],
        ];

        foreach ($specialties as $facultyData) {
            // Find faculty by name
            $faculty = $faculties->first(function($f) use ($facultyData) {
                return stripos($f->name_uz, $facultyData['faculty_name']) !== false ||
                       stripos($f->name_ru, $facultyData['faculty_name']) !== false;
            });

            if (!$faculty) {
                $this->command->warn("Fakultet topilmadi: {$facultyData['faculty_name']}");
                continue;
            }

            foreach ($facultyData['specialties'] as $specialtyData) {
                Specialty::updateOrCreate(
                    ['code' => $specialtyData['code']],
                    [
                        'faculty_id' => $faculty->id,
                        'name_uz' => $specialtyData['name_uz'],
                        'name_ru' => $specialtyData['name_ru'],
                        'name_en' => $specialtyData['name_en'],
                        'direction_code' => $specialtyData['direction_code'],
                        'degree' => $specialtyData['degree'],
                        'education_form' => $specialtyData['education_form'],
                        'education_type' => $specialtyData['education_type'],
                        'duration_years' => $specialtyData['duration_years'],
                        'credits_required' => $specialtyData['credits_required'],
                        'tuition_fee' => $specialtyData['tuition_fee'],
                    ]
                );

                $this->command->info("Yo'nalish qo'shildi/yangilandi: {$specialtyData['name_uz']} ({$faculty->name_uz})");
            }
        }

        $this->command->info('Barcha yo\'nalishlar muvaffaqiyatli qo\'shildi!');
    }
}
