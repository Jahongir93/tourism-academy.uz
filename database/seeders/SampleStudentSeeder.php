<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Student;
use App\Models\Faculty;
use App\Models\Specialty;
use App\Models\AcademicGroup;
use App\Models\StudentEnrollment;
use App\Models\StudentPassport;
use App\Models\StudentAddress;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SampleStudentSeeder extends Seeder
{
    public function run()
    {
        // Create roles first
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Student']);
        
        // Create university first
        $university = \App\Models\University::firstOrCreate(
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
        
        // Create faculty
        $faculty = Faculty::create([
            'university_id' => $university->id,
            'code' => 'IT',
            'name_uz' => 'Axborot texnologiyalari fakulteti',
            'name_en' => 'Faculty of Information Technology',
            'dean_name' => 'Karimov A.A.',
            'phone' => '+998901234567',
            'email' => 'it@university.uz',
            'room' => '301',
            'is_active' => true
        ]);

        // Create specialty
        $specialty = Specialty::create([
            'code' => '5330100',
            'name_uz' => 'Dasturiy injiniring',
            'name_en' => 'Software Engineering',
            'faculty_id' => $faculty->id,
            'duration_years' => 4,
        ]);

        // Create academic group
        $group = AcademicGroup::create([
            'name' => 'DI-201',
            'specialty_id' => $specialty->id,
            'faculty_id' => $faculty->id,
            'course' => 2,
            'language' => 'uz',
            'academic_year' => date('Y'),
            'max_students' => 30
        ]);

        // Create user account
        $user = User::create([
            'name' => 'Jasurbek Karimov',
            'email' => 'jasurbek@test.com',
            'password' => Hash::make('password'),
            'phone' => '+998901234567',
        ]);
        $user->assignRole('Student');

        // Create test student
        $student = Student::create([
            'student_id' => '20240001',
            'jshshir' => '12345678901234',
            'first_name_cyrillic' => 'Жасурбек',
            'first_name_latin' => 'Jasurbek',
            'last_name_cyrillic' => 'Каримов',
            'last_name_latin' => 'Karimov',
            'middle_name_cyrillic' => 'Анварович',
            'middle_name_latin' => 'Anvarovich',
            'birth_date' => '2005-03-15',
            'gender' => 'erkak',
            'nationality_id' => 1,
            'citizenship_id' => 1,
            'phone_primary' => '+998901234567',
            'phone_secondary' => '+998911234567',
            'email' => 'jasurbek@test.com',
            'telegram_username' => '@jasurbek',
            'status' => 'active',
            'social_status' => 'oddiy',
            'user_id' => $user->id
        ]);

        // Create passport
        StudentPassport::create([
            'student_id' => $student->id,
            'series' => 'AA',
            'number' => '1234567',
            'issue_date' => '2021-01-15',
            'issued_by' => 'Toshkent shahar IIB',
            'expiry_date' => '2031-01-15',
        ]);

        // Create address
        StudentAddress::create([
            'student_id' => $student->id,
            'address_type' => 'permanent',
            'region_id' => 1,
            'district_id' => 1,
            'mahalla' => 'Chilonzor',
            'street' => 'Chilonzor ko\'chasi',
            'house_number' => '123',
        ]);

        // Create enrollment
        StudentEnrollment::create([
            'student_id' => $student->id,
            'faculty_id' => $faculty->id,
            'specialty_id' => $specialty->id,
            'group_id' => $group->id,
            'education_form' => 'kunduzgi',
            'education_type' => 'grant',
            'education_language' => 'uz',
            'enrollment_date' => '2023-09-01',
            'expected_graduation_date' => '2027-06-30',
            'current_course' => 2,
            'current_semester' => 3,
            'is_active' => true,
        ]);

        $this->command->info('Test student created successfully!');
        $this->command->info('Student ID: 20240001');
        $this->command->info('Email: jasurbek@test.com');
        $this->command->info('Password: password');
    }
}