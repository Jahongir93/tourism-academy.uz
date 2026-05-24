<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RestoreDashboardDataSeeder extends Seeder
{
    public function run(): void
    {
        // Create roles if not exist
        $roles = [
            ['name' => 'admin', 'guard_name' => 'web'],
            ['name' => 'teacher', 'guard_name' => 'web'],
            ['name' => 'student', 'guard_name' => 'web'],
        ];

        foreach ($roles as $role) {
            DB::table('roles')->updateOrInsert(
                ['name' => $role['name']],
                $role
            );
        }

        // Create sample faculties
        $faculties = [
            ['name_uz' => 'Turizm fakulteti', 'name_ru' => 'Факультет туризма', 'name_en' => 'Faculty of Tourism', 'code' => 'TUR', 'created_at' => now(), 'updated_at' => now()],
            ['name_uz' => 'Mehmonxona xo\'jaligi fakulteti', 'name_ru' => 'Факультет гостиничного хозяйства', 'name_en' => 'Faculty of Hotel Management', 'code' => 'HOT', 'created_at' => now(), 'updated_at' => now()],
            ['name_uz' => 'Milliy taomlar fakulteti', 'name_ru' => 'Факультет национальной кухни', 'name_en' => 'Faculty of National Cuisine', 'code' => 'CUL', 'created_at' => now(), 'updated_at' => now()],
        ];

        foreach ($faculties as $faculty) {
            DB::table('faculties')->updateOrInsert(
                ['code' => $faculty['code']],
                $faculty
            );
        }

        // Create sample student groups
        $groups = [
            ['name' => 'TUR-101', 'course' => 1, 'faculty_id' => 1, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'TUR-102', 'course' => 1, 'faculty_id' => 1, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'TUR-201', 'course' => 2, 'faculty_id' => 1, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'HOT-101', 'course' => 1, 'faculty_id' => 2, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'HOT-201', 'course' => 2, 'faculty_id' => 2, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'CUL-101', 'course' => 1, 'faculty_id' => 3, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ];

        foreach ($groups as $group) {
            DB::table('student_groups')->updateOrInsert(
                ['name' => $group['name']],
                $group
            );
        }

        // Create sample students
        for ($i = 1; $i <= 50; $i++) {
            $user = User::updateOrCreate(
                ['email' => "student{$i}@tourism.uz"],
                [
                    'name' => "Talaba {$i}",
                    'email' => "student{$i}@tourism.uz",
                    'password' => Hash::make('password123'),
                    'email_verified_at' => now(),
                ]
            );

            // Assign student role
            $studentRole = DB::table('roles')->where('name', 'student')->first();
            if ($studentRole) {
                DB::table('model_has_roles')->updateOrInsert(
                    ['model_id' => $user->id, 'role_id' => $studentRole->id, 'model_type' => 'App\\Models\\User'],
                    ['model_id' => $user->id, 'role_id' => $studentRole->id, 'model_type' => 'App\\Models\\User']
                );
            }

            // Create student record
            DB::table('students')->updateOrInsert(
                ['user_id' => $user->id],
                [
                    'user_id' => $user->id,
                    'student_id' => 'STU' . str_pad($i, 4, '0', STR_PAD_LEFT),
                    'first_name' => "Talaba",
                    'last_name' => "#{$i}",
                    'email' => "student{$i}@tourism.uz",
                    'group_id' => rand(1, 6),
                    'status' => 'active',
                    'gender' => $i % 2 == 0 ? 'male' : 'female',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        // Create sample teachers
        for ($i = 1; $i <= 10; $i++) {
            $user = User::updateOrCreate(
                ['email' => "teacher{$i}@tourism.uz"],
                [
                    'name' => "O'qituvchi {$i}",
                    'email' => "teacher{$i}@tourism.uz",
                    'password' => Hash::make('password123'),
                    'email_verified_at' => now(),
                ]
            );

            // Assign teacher role
            $teacherRole = DB::table('roles')->where('name', 'teacher')->first();
            if ($teacherRole) {
                DB::table('model_has_roles')->updateOrInsert(
                    ['model_id' => $user->id, 'role_id' => $teacherRole->id, 'model_type' => 'App\\Models\\User'],
                    ['model_id' => $user->id, 'role_id' => $teacherRole->id, 'model_type' => 'App\\Models\\User']
                );
            }
        }

        // Create sample subjects
        $subjects = [
            ['name' => 'Turizm asoslari', 'code' => 'TUR101', 'credits' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Mehmonxona menejment', 'code' => 'HOT101', 'credits' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'O\'zbek milliy taomlari', 'code' => 'CUL101', 'credits' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Xorijiy tillar', 'code' => 'LAN101', 'credits' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Marketing', 'code' => 'MKT101', 'credits' => 3, 'created_at' => now(), 'updated_at' => now()],
        ];

        foreach ($subjects as $subject) {
            DB::table('subjects')->updateOrInsert(
                ['code' => $subject['code']],
                $subject
            );
        }

        $this->command->info('✓ Dashboard ma\'lumotlari muvaffaqiyatli tiklandi!');
        $this->command->info('✓ 50 ta talaba yaratildi');
        $this->command->info('✓ 10 ta o\'qituvchi yaratildi');
        $this->command->info('✓ 6 ta guruh yaratildi');
        $this->command->info('✓ 3 ta fakultet yaratildi');
        $this->command->info('✓ 5 ta fan yaratildi');
    }
}
