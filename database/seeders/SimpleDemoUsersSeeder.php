<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class SimpleDemoUsersSeeder extends Seeder
{
    public function run()
    {
        // Ensure roles exist
        $roles = [
            'SuperAdmin',
            'Admin',
            'Teacher',
            'Student',
            'HR',
            'Finance',
            'Marketing',
            'Rector',
            'Dean',
            'Department Head'
        ];

        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName]);
        }

        // Demo users data (basic fields only)
        $demoUsers = [
            [
                'name' => 'Super Admin Demo',
                'email' => 'superadmin@demo.uz',
                'password' => 'demo12345',
                'role' => 'SuperAdmin'
            ],
            [
                'name' => 'Admin Demo',
                'email' => 'admin@demo.uz',
                'password' => 'demo12345',
                'role' => 'Admin'
            ],
            [
                'name' => 'O\'qituvchi Rahimov',
                'email' => 'teacher@demo.uz',
                'password' => 'demo12345',
                'role' => 'Teacher'
            ],
            [
                'name' => 'Talaba Azimov',
                'email' => 'student@demo.uz',
                'password' => 'demo12345',
                'role' => 'Student'
            ],
            [
                'name' => 'HR Manager',
                'email' => 'hr@demo.uz',
                'password' => 'demo12345',
                'role' => 'HR'
            ],
            [
                'name' => 'Moliya Boshqarmasi',
                'email' => 'finance@demo.uz',
                'password' => 'demo12345',
                'role' => 'Finance'
            ],
            [
                'name' => 'Marketing Menedjer',
                'email' => 'marketing@demo.uz',
                'password' => 'demo12345',
                'role' => 'Marketing'
            ],
            [
                'name' => 'Rektor Karimov',
                'email' => 'rector@demo.uz',
                'password' => 'demo12345',
                'role' => 'Rector'
            ],
            [
                'name' => 'Dekan Saidova',
                'email' => 'dean@demo.uz',
                'password' => 'demo12345',
                'role' => 'Dean'
            ],
            [
                'name' => 'Kafedra Mudiri',
                'email' => 'department@demo.uz',
                'password' => 'demo12345',
                'role' => 'Department Head'
            ]
        ];

        foreach ($demoUsers as $userData) {
            // Check if user already exists
            $existingUser = User::where('email', $userData['email'])->first();

            if (!$existingUser) {
                // Create user
                $user = User::create([
                    'name' => $userData['name'],
                    'email' => $userData['email'],
                    'password' => Hash::make($userData['password']),
                    'email_verified_at' => now(),
                ]);

                // Assign role
                $user->assignRole($userData['role']);

                $this->command->info("Created user: {$userData['email']} with role: {$userData['role']}");
            } else {
                $this->command->warn("User already exists: {$userData['email']}");
            }
        }

        $this->command->info('');
        $this->command->info('=' . str_repeat('=', 58) . '=');
        $this->command->info('Demo foydalanuvchilar muvaffaqiyatli yaratildi!');
        $this->command->info('=' . str_repeat('=', 58) . '=');
        $this->command->info('');
        $this->command->table(
            ['Rol', 'Email', 'Parol'],
            collect($demoUsers)->map(function ($user) {
                return [
                    $user['role'],
                    $user['email'],
                    'demo12345'
                ];
            })
        );
        $this->command->info('');
        $this->command->info('Barcha demo foydalanuvchilar uchun parol: demo12345');
    }
}