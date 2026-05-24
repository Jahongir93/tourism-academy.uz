<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Faculty;
use App\Models\Specialty;

class FacultySeeder extends Seeder
{
    public function run()
    {
        // Create faculties with correct column names
        $faculties = [
            [
                'name' => 'Turizm fakulteti',
                'code' => 'TUR',
                'dean_name' => 'Dr. Aziz Karimov'
            ],
            [
                'name' => 'Iqtisodiyot fakulteti',
                'code' => 'ECO',
                'dean_name' => 'Prof. Dilshod Rahimov'
            ],
            [
                'name' => 'Xorijiy tillar fakulteti',
                'code' => 'LANG',
                'dean_name' => 'Dr. Gulnora Saidova'
            ],
            [
                'name' => 'Axborot texnologiyalari fakulteti',
                'code' => 'IT',
                'dean_name' => 'Prof. Jamshid Toshev'
            ]
        ];

        foreach ($faculties as $facultyData) {
            $faculty = Faculty::create($facultyData);

            // Create specialties for each faculty
            if ($faculty->name == 'Turizm fakulteti') {
                $specialties = [
                    ['code' => 'TUR001', 'name_uz' => 'Turizm (turlar bo\'yicha)', 'name_en' => 'Tourism (by types)'],
                    ['code' => 'TUR002', 'name_uz' => 'Mehmonxona xo\'jaligi', 'name_en' => 'Hotel Management'],
                    ['code' => 'TUR003', 'name_uz' => 'Turizm menejmenti', 'name_en' => 'Tourism Management'],
                ];
            } elseif ($faculty->name == 'Iqtisodiyot fakulteti') {
                $specialties = [
                    ['code' => 'ECO001', 'name_uz' => 'Iqtisodiyot', 'name_en' => 'Economics'],
                    ['code' => 'ECO002', 'name_uz' => 'Buxgalteriya hisobi', 'name_en' => 'Accounting'],
                    ['code' => 'ECO003', 'name_uz' => 'Moliya va moliyaviy texnologiyalar', 'name_en' => 'Finance and Financial Technologies'],
                ];
            } elseif ($faculty->name == 'Xorijiy tillar fakulteti') {
                $specialties = [
                    ['code' => 'LAN001', 'name_uz' => 'Ingliz tili', 'name_en' => 'English Language'],
                    ['code' => 'LAN002', 'name_uz' => 'Nemis tili', 'name_en' => 'German Language'],
                    ['code' => 'LAN003', 'name_uz' => 'Fransuz tili', 'name_en' => 'French Language'],
                ];
            } else {
                $specialties = [
                    ['code' => 'IT001', 'name_uz' => 'Dasturiy injiniring', 'name_en' => 'Software Engineering'],
                    ['code' => 'IT002', 'name_uz' => 'Kompyuter ilmlari', 'name_en' => 'Computer Science'],
                    ['code' => 'IT003', 'name_uz' => 'Axborot tizimlari', 'name_en' => 'Information Systems'],
                ];
            }

            foreach ($specialties as $spec) {
                Specialty::create([
                    'faculty_id' => $faculty->id,
                    'code' => $spec['code'],
                    'name_uz' => $spec['name_uz'],
                    'name_ru' => $spec['name_uz'], // Same as uz for now
                    'name_en' => $spec['name_en'],
                    'direction_code' => substr($spec['code'], 0, 3),
                    'degree' => 'bachelor',
                    'education_form' => 'full_time',
                    'education_type' => 'traditional',
                    'duration_years' => 4,
                    'credits_required' => 240,
                    'tuition_fee' => 12000000,
                    'language' => 'uz',
                    'description' => $spec['name_uz'] . ' yo\'nalishi bo\'yicha ta\'lim',
                    'is_active' => true
                ]);
            }
        }
    }
}