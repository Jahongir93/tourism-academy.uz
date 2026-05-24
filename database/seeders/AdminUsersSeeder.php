<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class AdminUsersSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Rollarni tekshirish
        $this->ensureRolesExist();

        // Admin foydalanuvchilar ma'lumotlari
        $users = [
            [
                'name' => 'Super Administrator',
                'email' => 'superadmin@tourism.uz',
                'password' => 'Super@2025!',
                'role' => 'SuperAdmin'
            ],
            [
                'name' => 'Administrator',
                'email' => 'admin@tourism.uz',
                'password' => 'Admin@2025!',
                'role' => 'Admin'
            ],
            [
                'name' => 'HR Manager',
                'email' => 'hr@tourism.uz',
                'password' => 'HR@2025!',
                'role' => 'HR'
            ],
            [
                'name' => 'Dekan',
                'email' => 'dekan@tourism.uz',
                'password' => 'Dekan@2025!',
                'role' => 'Dean'
            ],
            [
                'name' => 'Kafedra Mudiri',
                'email' => 'kafedra@tourism.uz',
                'password' => 'Kafedra@2025!',
                'role' => 'Department Head'
            ],
            [
                'name' => 'O\'qituvchi',
                'email' => 'teacher@tourism.uz',
                'password' => 'Teacher@2025!',
                'role' => 'Teacher'
            ],
            [
                'name' => 'Kurator',
                'email' => 'curator@tourism.uz',
                'password' => 'Curator@2025!',
                'role' => 'Curator'
            ],
            [
                'name' => 'Talaba',
                'email' => 'student@tourism.uz',
                'password' => 'Student@2025!',
                'role' => 'Student'
            ],
            [
                'name' => 'Moliya Xodimi',
                'email' => 'finance@tourism.uz',
                'password' => 'Finance@2025!',
                'role' => 'Finance Officer'
            ],
            [
                'name' => 'Stipendiya Manager',
                'email' => 'scholarship@tourism.uz',
                'password' => 'Scholar@2025!',
                'role' => 'Scholarship Manager'
            ],
            [
                'name' => 'Kutubxonachi',
                'email' => 'library@tourism.uz',
                'password' => 'Library@2025!',
                'role' => 'Librarian'
            ],
            [
                'name' => 'HEMIS Manager',
                'email' => 'hemis@tourism.uz',
                'password' => 'Hemis@2025!',
                'role' => 'HEMIS Manager'
            ],
        ];

        foreach ($users as $userData) {
            // Foydalanuvchi mavjudligini tekshirish
            $user = User::where('email', $userData['email'])->first();

            if (!$user) {
                // Yangi foydalanuvchi yaratish
                $user = User::create([
                    'name' => $userData['name'],
                    'email' => $userData['email'],
                    'password' => Hash::make($userData['password']),
                    'email_verified_at' => now(),
                ]);

                $this->command->info("✓ User created: {$userData['email']}");
            } else {
                // Parolni yangilash
                $user->password = Hash::make($userData['password']);
                $user->save();

                $this->command->info("✓ User updated: {$userData['email']}");
            }

            // Rolni biriktrish
            $role = Role::where('name', $userData['role'])->first();
            if ($role) {
                if (!$user->hasRole($userData['role'])) {
                    $user->assignRole($userData['role']);
                    $this->command->info("  → Role assigned: {$userData['role']}");
                }
            } else {
                $this->command->warn("  ! Role not found: {$userData['role']}");
            }
        }

        $this->command->info("\n" . str_repeat('=', 70));
        $this->command->info("ADMIN USERS CREATED SUCCESSFULLY!");
        $this->command->info(str_repeat('=', 70) . "\n");
    }

    /**
     * Rollarning mavjudligini tekshirish
     */
    protected function ensureRolesExist(): void
    {
        $roles = [
            'SuperAdmin',
            'Admin',
            'HR',
            'Dean',
            'Department Head',
            'Teacher',
            'Curator',
            'Student',
            'Finance Officer',
            'Scholarship Manager',
            'Librarian',
            'HEMIS Manager',
        ];

        foreach ($roles as $roleName) {
            if (!Role::where('name', $roleName)->exists()) {
                Role::create(['name' => $roleName, 'guard_name' => 'web']);
                $this->command->info("✓ Role created: {$roleName}");
            }
        }
    }
}
