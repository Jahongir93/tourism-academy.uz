<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $roles = [
            'SuperAdmin',
            'Teacher',
            'Student',
            'PR',
            'Marketing',
            'HR',
            'ChatAdmin',
        ];

        foreach ($roles as $role) {
            Role::create(['name' => $role]);
        }

        $superAdmin = User::create([
            'name' => 'Admin',
            'email' => 'admin@tourism.uz',
            'password' => Hash::make('password'),
            'user_type' => 'uzbek',
            'email_verified_at' => now(),
        ]);
        $superAdmin->assignRole('SuperAdmin');

        $this->command->info('Roles created successfully!');
        $this->command->info('SuperAdmin user created:');
        $this->command->info('Email: admin@tourism.uz');
        $this->command->info('Password: password');
    }
}