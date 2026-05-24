<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Teacher;
use App\Models\Department;
use Spatie\Permission\Models\Role;

class DemoTeacherSeeder extends Seeder
{
    public function run(): void
    {
        // Create teacher role if not exists
        $teacherRole = Role::firstOrCreate(['name' => 'teacher']);

        // Create demo teacher user
        $user = User::firstOrCreate(
            ['email' => 'teacher@test.uz'],
            [
                'name' => 'Demo O\'qituvchi',
                'password' => bcrypt('password')
            ]
        );

        // Assign teacher role
        if (!$user->hasRole('teacher')) {
            $user->assignRole('teacher');
        }

        // Get first department or create one
        $department = Department::first();
        if (!$department) {
            $department = Department::create([
                'name' => 'Informatika kafedrasi',
                'code' => 'IT',
                'faculty_id' => 1
            ]);
        }

        // Create teacher record
        $teacher = Teacher::firstOrCreate(
            ['user_id' => $user->id],
            [
                'first_name' => 'Demo',
                'last_name' => 'O\'qituvchi',
                'middle_name' => 'Testovich',
                'email' => 'teacher@test.uz',
                'phone' => '+998901234567',
                'department_id' => $department->id,
                'degree' => 'Magistr',
                'position' => 'Katta o\'qituvchi'
            ]
        );

        $this->command->info("Demo teacher created successfully!");
        $this->command->info("Email: teacher@test.uz");
        $this->command->info("Password: password");
        $this->command->info("Teacher ID: {$teacher->id}");
    }
}
