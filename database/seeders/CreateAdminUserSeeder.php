<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CreateAdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin foydalanuvchini yaratish
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@tourism.uz',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        // Agar Spatie Permission paketi o'rnatilgan bo'lsa
        if (class_exists('\Spatie\Permission\Models\Role')) {
            // Admin rolini yaratish
            $adminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'SuperAdmin']);
            $admin->assignRole($adminRole);
        }

        // Boshqa test foydalanuvchilarni yaratish
        $users = [
            [
                'name' => 'Rektor',
                'email' => 'rector@tourism.uz',
                'password' => Hash::make('rector123'),
                'role' => 'Rector'
            ],
            [
                'name' => 'Dekan',
                'email' => 'dean@tourism.uz',
                'password' => Hash::make('dean123'),
                'role' => 'Dean'
            ],
            [
                'name' => 'O\'qituvchi',
                'email' => 'teacher@tourism.uz',
                'password' => Hash::make('teacher123'),
                'role' => 'Teacher'
            ],
            [
                'name' => 'Talaba',
                'email' => 'student1@tourism.uz',
                'password' => Hash::make('student123'),
                'role' => 'Student'
            ]
        ];

        foreach ($users as $userData) {
            $user = User::create([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'password' => $userData['password'],
                'email_verified_at' => now(),
            ]);

            if (class_exists('\Spatie\Permission\Models\Role')) {
                $role = \Spatie\Permission\Models\Role::firstOrCreate(['name' => $userData['role']]);
                $user->assignRole($role);
            }
        }

        $this->command->info('Admin va test foydalanuvchilar muvaffaqiyatli yaratildi!');
        $this->command->info('Admin: admin@tourism.uz / password');
        $this->command->info('O\'qituvchi: teacher@tourism.uz / teacher123');
        $this->command->info('Talaba: student1@tourism.uz / student123');
    }
}