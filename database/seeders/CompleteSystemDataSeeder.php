<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class CompleteSystemDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🚀 Barcha tizim ma\'lumotlarini tiklamoqda...');

        // 1. Create Roles
        $this->createRoles();

        // 2. Create Faculties
        $facultyIds = $this->createFaculties();

        // 3. Create Specialties
        $specialtyIds = $this->createSpecialties($facultyIds);

        // 4. Create Student Groups
        $groupIds = $this->createStudentGroups($specialtyIds);

        // 5. Create Subjects
        $this->createSubjects();

        // 6. Create 300 Students
        $this->createStudents($groupIds);

        // 7. Create Teachers
        $this->createTeachers();

        $this->command->info('✅ Barcha ma\'lumotlar muvaffaqiyatli tiklandi!');
    }

    private function createRoles()
    {
        $this->command->info('📋 Rollarni yaratmoqda...');

        $roles = [
            ['name' => 'admin', 'guard_name' => 'web'],
            ['name' => 'teacher', 'guard_name' => 'web'],
            ['name' => 'student', 'guard_name' => 'web'],
            ['name' => 'dean', 'guard_name' => 'web'],
            ['name' => 'rector', 'guard_name' => 'web'],
        ];

        foreach ($roles as $role) {
            DB::table('roles')->updateOrInsert(
                ['name' => $role['name']],
                array_merge($role, ['created_at' => now(), 'updated_at' => now()])
            );
        }
    }

    private function createFaculties()
    {
        $this->command->info('🏛️  Fakultetlarni yaratmoqda...');

        $faculties = [
            ['name_uz' => 'Turizm fakulteti', 'name_ru' => 'Факультет туризма', 'name_en' => 'Faculty of Tourism', 'code' => 'TUR'],
            ['name_uz' => 'Mehmonxona xo\'jaligi fakulteti', 'name_ru' => 'Факультет гостиничного хозяйства', 'name_en' => 'Faculty of Hotel Management', 'code' => 'HTL'],
            ['name_uz' => 'Milliy taomlar fakulteti', 'name_ru' => 'Факультет национальной кухни', 'name_en' => 'Faculty of National Cuisine', 'code' => 'CUL'],
            ['name_uz' => 'Xizmat ko\'rsatish fakulteti', 'name_ru' => 'Факультет обслуживания', 'name_en' => 'Faculty of Service', 'code' => 'SRV'],
            ['name_uz' => 'Transport logistikasi fakulteti', 'name_ru' => 'Факультет транспортной логистики', 'name_en' => 'Faculty of Transport Logistics', 'code' => 'LOG'],
        ];

        $facultyIds = [];
        foreach ($faculties as $faculty) {
            DB::table('faculties')->updateOrInsert(
                ['code' => $faculty['code']],
                array_merge($faculty, ['created_at' => now(), 'updated_at' => now()])
            );
            $id = DB::table('faculties')->where('code', $faculty['code'])->value('id');
            $facultyIds[] = $id;
        }

        return $facultyIds;
    }

    private function createSpecialties($facultyIds)
    {
        $this->command->info('📚 Mutaxassisliklarni yaratmoqda...');

        $specialties = [
            // Tourism Faculty
            ['name_uz' => 'Turizm va sayohat', 'name_en' => 'Tourism and Travel', 'code' => 'TUR-001', 'faculty_id' => $facultyIds[0], 'duration_years' => 4],
            ['name_uz' => 'Ekologik turizm', 'name_en' => 'Ecological Tourism', 'code' => 'TUR-002', 'faculty_id' => $facultyIds[0], 'duration_years' => 4],

            // Hotel Management Faculty
            ['name_uz' => 'Mehmonxona xizmati', 'name_en' => 'Hotel Service', 'code' => 'HTL-001', 'faculty_id' => $facultyIds[1], 'duration_years' => 4],
            ['name_uz' => 'Restoran xizmati', 'name_en' => 'Restaurant Service', 'code' => 'HTL-002', 'faculty_id' => $facultyIds[1], 'duration_years' => 4],

            // Cuisine Faculty
            ['name_uz' => 'Oshpazlik san\'ati', 'name_en' => 'Culinary Arts', 'code' => 'CUL-001', 'faculty_id' => $facultyIds[2], 'duration_years' => 4],
            ['name_uz' => 'Konfetyer mahsulotlari', 'name_en' => 'Confectionery Products', 'code' => 'CUL-002', 'faculty_id' => $facultyIds[2], 'duration_years' => 4],

            // Service Faculty
            ['name_uz' => 'Xizmat ko\'rsatish', 'name_en' => 'Service Management', 'code' => 'SRV-001', 'faculty_id' => $facultyIds[3], 'duration_years' => 4],

            // Logistics Faculty
            ['name_uz' => 'Transport logistikasi', 'name_en' => 'Transport Logistics', 'code' => 'LOG-001', 'faculty_id' => $facultyIds[4], 'duration_years' => 4],
        ];

        $specialtyIds = [];
        foreach ($specialties as $specialty) {
            DB::table('specialties')->updateOrInsert(
                ['code' => $specialty['code']],
                array_merge($specialty, ['created_at' => now(), 'updated_at' => now()])
            );
            $id = DB::table('specialties')->where('code', $specialty['code'])->value('id');
            $specialtyIds[] = $id;
        }

        return $specialtyIds;
    }

    private function createStudentGroups($specialtyIds)
    {
        $this->command->info('👥 Guruhlarni yaratmoqda...');

        $groups = [];
        $groupNames = ['A', 'B', 'C'];

        // Create groups for each specialty and course
        foreach ($specialtyIds as $index => $specialtyId) {
            $specialty = DB::table('specialties')->find($specialtyId);
            $code = substr($specialty->code, 0, 3);

            // Create or get department for this specialty
            $departmentName = $specialty->name_uz . ' kafedrasi';
            DB::table('departments')->updateOrInsert(
                ['code' => $specialty->code],
                [
                    'name' => $departmentName,
                    'name_uz' => $departmentName,
                    'code' => $specialty->code,
                    'faculty_id' => $specialty->faculty_id,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );
            $departmentId = DB::table('departments')->where('code', $specialty->code)->value('id');

            for ($course = 1; $course <= 4; $course++) {
                foreach ($groupNames as $groupName) {
                    $groupCode = "{$code}-{$course}{$groupName}";
                    $groups[] = [
                        'name' => $groupCode,
                        'code' => $groupCode,
                        'course' => $course,
                        'department_id' => $departmentId,
                        'students_count' => 0,
                        'education_type' => 'kunduzgi',
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                }
            }
        }

        $groupIds = [];
        foreach ($groups as $group) {
            DB::table('groups')->updateOrInsert(
                ['code' => $group['code']],
                $group
            );
            $id = DB::table('groups')->where('code', $group['code'])->value('id');
            $groupIds[] = $id;
        }

        $this->command->info("✓ " . count($groupIds) . " ta guruh yaratildi");

        return $groupIds;
    }

    private function createSubjects()
    {
        $this->command->info('📖 Fanlarni yaratmoqda...');

        $subjects = [
            ['name_uz' => 'Turizm asoslari', 'code' => 'TUR101', 'credits' => 3],
            ['name_uz' => 'Mehmonxona menejment', 'code' => 'HTL101', 'credits' => 4],
            ['name_uz' => 'O\'zbek milliy taomlari', 'code' => 'CUL101', 'credits' => 3],
            ['name_uz' => 'Xorijiy tillar', 'code' => 'LANG101', 'credits' => 4],
            ['name_uz' => 'Marketing', 'code' => 'MKT101', 'credits' => 3],
            ['name_uz' => 'Iqtisodiyot', 'code' => 'ECO101', 'credits' => 3],
            ['name_uz' => 'Huquq asoslari', 'code' => 'LAW101', 'credits' => 2],
            ['name_uz' => 'Informatika', 'code' => 'IT101', 'credits' => 3],
            ['name_uz' => 'Psixologiya', 'code' => 'PSY101', 'credits' => 2],
            ['name_uz' => 'Ekologiya', 'code' => 'ECL101', 'credits' => 2],
        ];

        foreach ($subjects as $subject) {
            DB::table('subjects')->updateOrInsert(
                ['code' => $subject['code']],
                array_merge($subject, ['created_at' => now(), 'updated_at' => now()])
            );
        }
    }

    private function createStudents($groupIds)
    {
        $this->command->info('👨‍🎓 300 ta talabani yaratmoqda...');

        $firstNames = ['Aziz', 'Bobur', 'Davron', 'Eldor', 'Farrux', 'Gulnora', 'Hilola', 'Iroda', 'Jasur', 'Kamola',
                       'Laziz', 'Madina', 'Nodira', 'Otabek', 'Parvina', 'Quvonch', 'Rustam', 'Sabina', 'Temur', 'Umida',
                       'Vladislav', 'Xamza', 'Yulduz', 'Zarina', 'Anvar', 'Bekzod', 'Sardor', 'Dilshod', 'Jahongir'];

        $lastNames = ['Karimov', 'Rahimov', 'Alimov', 'Tursunov', 'Sharipov', 'Nazarov', 'Ismoilov', 'Abdullayev',
                      'Yusupov', 'Mahmudov', 'Rashidov', 'Umarov', 'Safarov', 'Qodirov', 'Normatov', 'Mirzayev'];

        $studentRole = DB::table('roles')->where('name', 'student')->first();

        for ($i = 1; $i <= 300; $i++) {
            $firstName = $firstNames[array_rand($firstNames)];
            $lastName = $lastNames[array_rand($lastNames)];
            $email = strtolower($firstName . '.' . $lastName . $i . '@student.tourism.uz');

            // Create user
            $user = User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => "{$firstName} {$lastName}",
                    'email' => $email,
                    'password' => Hash::make('student123'),
                    'email_verified_at' => now(),
                ]
            );

            // Assign student role
            if ($studentRole) {
                DB::table('model_has_roles')->updateOrInsert(
                    ['model_id' => $user->id, 'role_id' => $studentRole->id, 'model_type' => 'App\\Models\\User'],
                    ['model_id' => $user->id, 'role_id' => $studentRole->id, 'model_type' => 'App\\Models\\User']
                );
            }

            // Create student record
            $groupId = $groupIds[array_rand($groupIds)];
            $admissionDate = Carbon::now()->subYears(rand(0, 3))->subMonths(rand(0, 11));
            $studentId = 'STU' . str_pad($i, 6, '0', STR_PAD_LEFT);

            DB::table('students')->updateOrInsert(
                ['student_id' => $studentId],
                [
                    'user_id' => $user->id,
                    'student_id' => $studentId,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => $email,
                    'phone' => '+998' . rand(90, 99) . rand(1000000, 9999999),
                    'group_id' => $groupId,
                    'status' => 'active',
                    'gender' => rand(0, 1) ? 'male' : 'female',
                    'admission_date' => $admissionDate,
                    'birth_date' => Carbon::now()->subYears(rand(18, 25)),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

            if ($i % 50 == 0) {
                $this->command->info("  ✓ {$i} ta talaba yaratildi...");
            }
        }

        $this->command->info("✅ Jami 300 ta talaba yaratildi!");
    }

    private function createTeachers()
    {
        $this->command->info('👨‍🏫 O\'qituvchilarni yaratmoqda...');

        $teachers = [
            ['name' => 'Prof. Karimov Aziz', 'email' => 'a.karimov@teacher.uz'],
            ['name' => 'Dots. Rahimova Nodira', 'email' => 'n.rahimova@teacher.uz'],
            ['name' => 'Prof. Alimov Bobur', 'email' => 'b.alimov@teacher.uz'],
            ['name' => 'Dots. Tursunova Gulnora', 'email' => 'g.tursunova@teacher.uz'],
            ['name' => 'O\'qituvchi Sharipov Jasur', 'email' => 'j.sharipov@teacher.uz'],
            ['name' => 'O\'qituvchi Nazarova Madina', 'email' => 'm.nazarova@teacher.uz'],
            ['name' => 'Prof. Ismoilov Eldor', 'email' => 'e.ismoilov@teacher.uz'],
            ['name' => 'Dots. Abdullayeva Kamola', 'email' => 'k.abdullayeva@teacher.uz'],
            ['name' => 'O\'qituvchi Yusupov Temur', 'email' => 't.yusupov@teacher.uz'],
            ['name' => 'O\'qituvchi Mahmudova Sabina', 'email' => 's.mahmudova@teacher.uz'],
            ['name' => 'Prof. Rashidov Anvar', 'email' => 'a.rashidov@teacher.uz'],
            ['name' => 'Dots. Umarova Dilnoza', 'email' => 'd.umarova@teacher.uz'],
            ['name' => 'O\'qituvchi Safarov Sardor', 'email' => 's.safarov@teacher.uz'],
            ['name' => 'O\'qituvchi Qodirova Zarina', 'email' => 'z.qodirova@teacher.uz'],
            ['name' => 'Prof. Normatov Rustam', 'email' => 'r.normatov@teacher.uz'],
        ];

        $teacherRole = DB::table('roles')->where('name', 'teacher')->first();

        foreach ($teachers as $teacher) {
            $user = User::updateOrCreate(
                ['email' => $teacher['email']],
                [
                    'name' => $teacher['name'],
                    'email' => $teacher['email'],
                    'password' => Hash::make('teacher123'),
                    'email_verified_at' => now(),
                ]
            );

            if ($teacherRole) {
                DB::table('model_has_roles')->updateOrInsert(
                    ['model_id' => $user->id, 'role_id' => $teacherRole->id, 'model_type' => 'App\\Models\\User'],
                    ['model_id' => $user->id, 'role_id' => $teacherRole->id, 'model_type' => 'App\\Models\\User']
                );
            }
        }

        $this->command->info("✅ " . count($teachers) . " ta o'qituvchi yaratildi!");
    }
}
