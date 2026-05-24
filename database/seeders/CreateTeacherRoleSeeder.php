<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class CreateTeacherRoleSeeder extends Seeder
{
    public function run()
    {
        // Create Teacher role if it doesn't exist
        Role::firstOrCreate(['name' => 'Teacher', 'guard_name' => 'web']);
        
        echo "Teacher role created successfully!\n";
    }
}