<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class CreateUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create roles if they don't exist
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $studentRole = Role::firstOrCreate(['name' => 'Student']);
        $teacherRole = Role::firstOrCreate(['name' => 'Teacher']);

        // Create Admin User
        $admin = User::updateOrCreate(
            ['email' => 'admin@local.uz'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('admin123'),
                'email_verified_at' => now(),
            ]
        );
        $admin->assignRole($adminRole);

        // Create Student User
        $student = User::updateOrCreate(
            ['email' => 'student@local.uz'],
            [
                'name' => 'Aliyev Sardor',
                'password' => Hash::make('student123'),
                'email_verified_at' => now(),
            ]
        );
        $student->assignRole($studentRole);

        // Create Teacher User
        $teacher = User::updateOrCreate(
            ['email' => 'teacher@local.uz'],
            [
                'name' => 'Karimov Jamshid',
                'password' => Hash::make('teacher123'),
                'email_verified_at' => now(),
            ]
        );
        $teacher->assignRole($teacherRole);

        $this->command->info('Users created successfully!');
        $this->command->info('==================================');
        $this->command->info('Admin: admin@local.uz / admin123');
        $this->command->info('Student: student@local.uz / student123');
        $this->command->info('Teacher: teacher@local.uz / teacher123');
        $this->command->info('==================================');
    }
}