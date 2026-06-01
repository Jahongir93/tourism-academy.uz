<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Hemis\University;
use App\Models\Hemis\Faculty;
use App\Models\Hemis\Department;
use App\Models\Hemis\Specialty;
use App\Models\Hemis\AcademicGroup;
use App\Models\Hemis\Student;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class HemisSeeder extends Seeder
{
    public function run(): void
    {
        // Create additional roles for HEMIS
        $roles = [
            'Rector',
            'Vice_Rector',
            'Dean',
            'Department_Head',
            'Academic_Affairs',
            'Student_Affairs',
            'Methodist',
            'Secretary',
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        // Create University
        $university = University::create([
            'code' => 'TAS',
            'name_uz' => 'Tourism Academy Samarkand',
            'name_ru' => 'Академия Туризма Самарканд',
            'name_en' => 'Tourism Academy Samarkand',
            'short_name' => 'TAS',
            'address' => 'Samarqand sh., Universitet xiyoboni 17',
            'phone' => '+998662377800',
            'email' => 'info@tourism.uz',
            'website' => 'www.tourism.uz',
            'rector_name' => 'Tursunov Akmal Rustamovich',
            'inn' => '123456789',
            'type' => 'state',
            'is_active' => true,
        ]);

        // Create Rector user
        $rector = User::create([
            'name' => 'Tursunov Akmal',
            'email' => 'rector@tourism.uz',
            'password' => Hash::make('rector123'),
            'user_type' => 'uzbek',
            'email_verified_at' => now(),
        ]);
        $rector->assignRole('Rector');

        // Create Faculty
        $faculty = Faculty::create([
            'university_id' => $university->id,
            'code' => 'TM',
            'name_uz' => 'Turizm va mehmonxona xo\'jaligi fakulteti',
            'name_ru' => 'Факультет туризма и гостиничного хозяйства',
            'name_en' => 'Faculty of Tourism and Hospitality',
            'short_name' => 'TMX',
            'dean_name' => 'Karimov Sardor',
            'phone' => '+998662377801',
            'email' => 'tmx@tourism.uz',
            'is_active' => true,
        ]);

        // Create Dean user
        $dean = User::create([
            'name' => 'Karimov Sardor',
            'email' => 'dean@tourism.uz',
            'password' => Hash::make('dean123'),
            'user_type' => 'uzbek',
            'email_verified_at' => now(),
        ]);
        $dean->assignRole('Dean');
        $faculty->update(['dean_user_id' => $dean->id]);

        // Create Department
        $department = Department::create([
            'faculty_id' => $faculty->id,
            'code' => 'TUR',
            'name_uz' => 'Turizm kafedrasi',
            'name_ru' => 'Кафедра туризма',
            'name_en' => 'Department of Tourism',
            'short_name' => 'TUR',
            'head_name' => 'Azimov Jasur',
            'phone' => '+998662377802',
            'email' => 'tourism@tourism.uz',
            'type' => 'kafedra',
            'is_active' => true,
        ]);

        // Create Teacher user
        $teacher = User::create([
            'name' => 'Azimov Jasur',
            'email' => 'teacher@tourism.uz',
            'password' => Hash::make('teacher123'),
            'user_type' => 'uzbek',
            'email_verified_at' => now(),
        ]);
        $teacher->assignRole('Teacher');
        $department->update(['head_id' => $teacher->id]);

        // Create Specialty
        $specialty = Specialty::create([
            'faculty_id' => $faculty->id,
            'department_id' => $department->id,
            'code' => '60610100',
            'name_uz' => 'Turizm (faoliyat turlari bo\'yicha)',
            'name_ru' => 'Туризм (по видам деятельности)',
            'name_en' => 'Tourism (by types of activities)',
            'direction_code' => '60610100',
            'degree' => 'bakalavr',
            'education_form' => 'kunduzgi',
            'education_type' => 'shartnoma',
            'duration_years' => 4,
            'credits_required' => 240,
            'tuition_fee' => 12000000,
            'language' => 'uz',
            'is_active' => true,
        ]);

        // Create Academic Group (academic_groups table)
        AcademicGroup::create([
            'specialty_id'    => $specialty->id,
            'faculty_id'      => $faculty->id,
            'name'            => 'TUR-21-01',
            'course'          => 3,
            'max_students'    => 30,
            'current_students'=> 25,
            'curator_id'      => $teacher->id,
            'curator_name'    => 'Azimov Jasur',
            'academic_year'   => 2024,
            'semester'        => '1',
            'language'        => 'uz',
            'is_active'       => true,
        ]);

        // Create group in the `groups` table — students.group_id FK references this
        $groupId = DB::table('groups')->insertGetId([
            'name'           => 'TUR-21-01',
            'code'           => 'TUR-21-01',
            'department_id'  => $department->id,
            'course'         => 3,
            'students_count' => 2,
            'education_type' => 'kunduzgi',
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);

        // Create Student users
        $studentUser1 = User::create([
            'name' => 'Aliyev Shoxrux',
            'email' => 'student1@tourism.uz',
            'phone' => '+998901234567',
            'password' => Hash::make('student123'),
            'user_type' => 'uzbek',
            'email_verified_at' => now(),
            'phone_verified_at' => now(),
        ]);
        $studentUser1->assignRole('Student');

        // Create Student record
        Student::create([
            'user_id'        => $studentUser1->id,
            'student_id'     => 'TAS2021001',
            'first_name'     => 'Shoxrux',
            'last_name'      => 'Aliyev',
            'middle_name'    => 'Bahromovich',
            'birth_date'     => '2003-05-15',
            'gender'         => 'male',
            'passport_series'=> 'AB',
            'passport_number'=> '1234567',
            'jshshir'        => '12345678901234',
            'phone'          => '+998901234567',
            'email'          => 'student1@tourism.uz',
            'address'        => 'Samarqand viloyati, Samarqand shahri',
            'faculty_id'     => $faculty->id,
            'specialty_id'   => $specialty->id,
            'group_id'       => $groupId,
            'course'         => 3,
            'education_form' => 'kunduzgi',
            'education_type' => 'shartnoma',
            'admission_date' => '2021-09-01',
            'status'         => 'active',
        ]);

        // Create another student
        $studentUser2 = User::create([
            'name' => 'Rahimova Dilnoza',
            'email' => 'student2@tourism.uz',
            'phone' => '+998901234568',
            'password' => Hash::make('student123'),
            'user_type' => 'uzbek',
            'email_verified_at' => now(),
            'phone_verified_at' => now(),
        ]);
        $studentUser2->assignRole('Student');

        Student::create([
            'user_id'        => $studentUser2->id,
            'student_id'     => 'TAS2021002',
            'first_name'     => 'Dilnoza',
            'last_name'      => 'Rahimova',
            'middle_name'    => 'Rustamovna',
            'birth_date'     => '2003-08-20',
            'gender'         => 'female',
            'passport_series'=> 'AC',
            'passport_number'=> '7654321',
            'jshshir'        => '98765432109876',
            'phone'          => '+998901234568',
            'email'          => 'student2@tourism.uz',
            'address'        => 'Samarqand viloyati, Kattaqo\'rg\'on shahri',
            'faculty_id'     => $faculty->id,
            'specialty_id'   => $specialty->id,
            'group_id'       => $groupId,
            'course'         => 3,
            'education_form' => 'kunduzgi',
            'education_type' => 'byudjet',
            'admission_date' => '2021-09-01',
            'status'         => 'active',
        ]);

        $this->command->info('HEMIS users created successfully!');
        $this->command->info('');
        $this->command->info('===== LOGIN CREDENTIALS =====');
        $this->command->info('');
        $this->command->info('SuperAdmin:');
        $this->command->info('Email: admin@tourism.uz');
        $this->command->info('Password: password');
        $this->command->info('');
        $this->command->info('Rector:');
        $this->command->info('Email: rector@tourism.uz');
        $this->command->info('Password: rector123');
        $this->command->info('');
        $this->command->info('Dean:');
        $this->command->info('Email: dean@tourism.uz');
        $this->command->info('Password: dean123');
        $this->command->info('');
        $this->command->info('Teacher:');
        $this->command->info('Email: teacher@tourism.uz');
        $this->command->info('Password: teacher123');
        $this->command->info('');
        $this->command->info('Student 1:');
        $this->command->info('Email: student1@tourism.uz');
        $this->command->info('Password: student123');
        $this->command->info('');
        $this->command->info('Student 2:');
        $this->command->info('Email: student2@tourism.uz');
        $this->command->info('Password: student123');
        $this->command->info('');
        $this->command->info('===========================');
    }
}