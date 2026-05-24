<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Student;
use App\Models\Faculty;
use App\Models\Specialty;
use App\Models\AcademicGroup;
use App\Models\University;

class TestStudentSeeder extends Seeder
{
    public function run()
    {
        // Create university if not exists
        $university = University::firstOrCreate(
            ['code' => 'TATU'],
            [
                'name_uz' => 'Muhammad al-Xorazmiy nomidagi Toshkent axborot texnologiyalari universiteti',
                'name_ru' => 'Ташкентский университет информационных технологий имени Мухаммада аль-Хорезми',
                'name_en' => 'Tashkent University of Information Technologies',
                'short_name' => 'TATU',
                'address' => 'Toshkent sh., Amir Temur ko\'chasi, 108',
                'phone' => '+998712381400',
                'email' => 'info@tuit.uz',
                'website' => 'https://tuit.uz',
                'rector_name' => 'Yuldashev Bahrom Atajanovich'
            ]
        );

        // Create test faculties if not exists
        $faculty1 = Faculty::firstOrCreate(
            ['code' => 'IT'],
            [
                'name_uz' => 'Axborot texnologiyalari fakulteti',
                'university_id' => $university->id
            ]
        );
        
        $faculty2 = Faculty::firstOrCreate(
            ['code' => 'ECO'],
            [
                'name_uz' => 'Iqtisodiyot fakulteti',
                'university_id' => $university->id
            ]
        );

        // Create test specialties
        $specialty1 = Specialty::firstOrCreate(
            ['code' => '60610100'],
            [
                'name_uz' => 'Dasturiy injiniring',
                'faculty_id' => $faculty1->id,
                'duration_years' => 4
            ]
        );
        
        $specialty2 = Specialty::firstOrCreate(
            ['code' => '60230100'],
            [
                'name_uz' => 'Iqtisodiyot',
                'faculty_id' => $faculty2->id,
                'duration_years' => 4
            ]
        );

        // Create test groups
        $group1 = AcademicGroup::firstOrCreate(
            ['name' => 'DI-101'],
            [
                'faculty_id' => $faculty1->id,
                'specialty_id' => $specialty1->id,
                'course' => 1,
                'language' => 'uz',
                'academic_year' => date('Y'),
                'max_students' => 30
            ]
        );
        
        $group2 = AcademicGroup::firstOrCreate(
            ['name' => 'IQ-201'],
            [
                'faculty_id' => $faculty2->id,
                'specialty_id' => $specialty2->id,
                'course' => 2,
                'language' => 'uz',
                'academic_year' => date('Y'),
                'max_students' => 30
            ]
        );

        // Create test students
        $students = [
            [
                'jshshir' => '12345678901234',
                'first_name_cyrillic' => 'Иван',
                'first_name_latin' => 'Ivan',
                'last_name_cyrillic' => 'Иванов',
                'last_name_latin' => 'Ivanov',
                'middle_name_cyrillic' => 'Иванович',
                'middle_name_latin' => 'Ivanovich',
                'birth_date' => '2000-05-15',
                'gender' => 'erkak',
                'phone_primary' => '+998901234567',
                'email' => 'ivan@example.com',
                'social_status' => 'oddiy',
                'faculty_id' => $faculty1->id,
                'specialty_id' => $specialty1->id,
                'group_id' => $group1->id,
            ],
            [
                'jshshir' => '98765432109876',
                'first_name_cyrillic' => 'Мария',
                'first_name_latin' => 'Mariya',
                'last_name_cyrillic' => 'Петрова',
                'last_name_latin' => 'Petrova',
                'middle_name_cyrillic' => 'Сергеевна',
                'middle_name_latin' => 'Sergeevna',
                'birth_date' => '2001-03-20',
                'gender' => 'ayol',
                'phone_primary' => '+998907654321',
                'email' => 'mariya@example.com',
                'social_status' => 'oddiy',
                'faculty_id' => $faculty2->id,
                'specialty_id' => $specialty2->id,
                'group_id' => $group2->id,
            ],
        ];

        foreach ($students as $studentData) {
            $enrollmentData = [
                'faculty_id' => $studentData['faculty_id'],
                'specialty_id' => $studentData['specialty_id'],
                'group_id' => $studentData['group_id'],
            ];
            unset($studentData['faculty_id'], $studentData['specialty_id'], $studentData['group_id']);

            $student = Student::create($studentData);

            // Create passport
            $student->passport()->create([
                'series' => 'AA',
                'number' => rand(1000000, 9999999),
                'issue_date' => '2018-01-01',
                'issued_by' => 'Toshkent shahar IIB',
            ]);

            // Create address
            $student->addresses()->create([
                'address_type' => 'permanent',
                'region_id' => 1,
                'district_id' => 1,
                'mahalla' => 'Chilonzor',
                'street' => 'Amir Temur',
                'house_number' => '123',
            ]);

            // Create enrollment
            $student->enrollments()->create(array_merge($enrollmentData, [
                'education_form' => 'kunduzgi',
                'education_type' => 'grant',
                'education_language' => 'uz',
                'enrollment_date' => now(),
                'expected_graduation_date' => now()->addYears(4),
                'current_course' => 1,
                'current_semester' => 1,
                'is_active' => true,
            ]));

            // Create education document
            $student->educationDocs()->create([
                'document_type' => 'attestat',
                'document_number' => 'A' . rand(100000, 999999),
                'institution_name' => rand(1, 50) . '-sonli maktab',
                'graduation_year' => 2023,
                'gpa' => rand(35, 50) / 10,
            ]);

            // Create family members
            $student->familyMembers()->create([
                'relationship_type' => 'ota',
                'full_name' => $student->last_name_latin . ' ' . fake()->firstNameMale(),
                'workplace' => fake()->company(),
                'position' => 'Menejer',
                'phone' => '+998' . rand(900000000, 999999999),
            ]);

            $student->familyMembers()->create([
                'relationship_type' => 'ona',
                'full_name' => $student->last_name_latin . 'a ' . fake()->firstNameFemale(),
                'workplace' => fake()->company(),
                'position' => 'O\'qituvchi',
                'phone' => '+998' . rand(900000000, 999999999),
            ]);
        }

        $this->command->info('Test talabalar muvaffaqiyatli yaratildi!');
    }
}