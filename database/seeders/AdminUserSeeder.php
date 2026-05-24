<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin user yaratish yoki yangilash
        $admin = User::updateOrCreate(
            ['email' => 'admin@tourism.uz'],
            [
                'name' => 'Administrator',
                'email' => 'admin@tourism.uz',
                'password' => Hash::make('admin123456'),
                'email_verified_at' => now(),
            ]
        );

        // Super Admin user yaratish yoki yangilash
        $superAdmin = User::updateOrCreate(
            ['email' => 'superadmin@tourism.uz'],
            [
                'name' => 'Super Administrator',
                'email' => 'superadmin@tourism.uz',
                'password' => Hash::make('super123456'),
                'email_verified_at' => now(),
            ]
        );

        // Admin rolini topish va biriktrish
        $adminRole = DB::table('roles')->where('name', 'admin')->first();

        if ($adminRole) {
            // Admin uchun rol biriktirish
            DB::table('model_has_roles')->updateOrInsert(
                [
                    'model_id' => $admin->id,
                    'role_id' => $adminRole->id,
                    'model_type' => 'App\\Models\\User'
                ],
                [
                    'model_id' => $admin->id,
                    'role_id' => $adminRole->id,
                    'model_type' => 'App\\Models\\User'
                ]
            );

            // Super Admin uchun rol biriktirish
            DB::table('model_has_roles')->updateOrInsert(
                [
                    'model_id' => $superAdmin->id,
                    'role_id' => $adminRole->id,
                    'model_type' => 'App\\Models\\User'
                ],
                [
                    'model_id' => $superAdmin->id,
                    'role_id' => $adminRole->id,
                    'model_type' => 'App\\Models\\User'
                ]
            );
        }

        $this->command->info('✓ Admin foydalanuvchilar muvaffaqiyatli yaratildi!');
        $this->command->info('');
        $this->command->info('═══════════════════════════════════════');
        $this->command->info('📧 Email: admin@tourism.uz');
        $this->command->info('🔑 Parol: admin123456');
        $this->command->info('═══════════════════════════════════════');
        $this->command->info('📧 Email: superadmin@tourism.uz');
        $this->command->info('🔑 Parol: super123456');
        $this->command->info('═══════════════════════════════════════');
    }
}