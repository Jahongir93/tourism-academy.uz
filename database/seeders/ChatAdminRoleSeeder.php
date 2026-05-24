<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class ChatAdminRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create ChatAdmin role if not exists
        $role = Role::firstOrCreate(['name' => 'ChatAdmin']);

        // Create permissions for ChatAdmin
        $permissions = [
            'chat.manage',
            'chat.view_telegram',
            'chat.reply_telegram',
            'chat.view_contact_requests',
            'chat.handle_contact_requests',
            'chat.manage_bot_settings',
            'chat.manage_nicknames',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assign permissions to ChatAdmin role
        $role->syncPermissions($permissions);

        // Also give all these permissions to SuperAdmin
        $superAdmin = Role::findByName('SuperAdmin');
        if ($superAdmin) {
            $superAdmin->givePermissionTo($permissions);
        }

        $this->command->info('ChatAdmin role and permissions created successfully!');
    }
}
