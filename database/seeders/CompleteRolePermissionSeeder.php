<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class CompleteRolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define all permissions
        $permissions = [
            // User Management
            'view_users',
            'create_users',
            'edit_users',
            'delete_users',

            // Student Management
            'view_students',
            'create_students',
            'edit_students',
            'delete_students',
            'manage_student_grades',

            // Teacher Management
            'view_teachers',
            'create_teachers',
            'edit_teachers',
            'delete_teachers',
            'assign_subjects',

            // Course Management
            'view_courses',
            'create_courses',
            'edit_courses',
            'delete_courses',
            'publish_courses',

            // LMS Management
            'view_lms',
            'manage_lms_materials',
            'manage_lms_videos',
            'manage_lms_tests',
            'manage_lms_library',
            'manage_lms_certificates',
            'moderate_forum',

            // HR Management
            'view_employees',
            'create_employees',
            'edit_employees',
            'delete_employees',
            'manage_payroll',
            'manage_leave',
            'view_reports',

            // PR Management
            'view_news',
            'create_news',
            'edit_news',
            'delete_news',
            'manage_media',
            'manage_events',
            'view_statistics',

            // Marketing Management
            'view_analytics',
            'manage_campaigns',
            'manage_social_media',
            'view_marketing_reports',

            // Chat Management
            'view_chat',
            'moderate_chat',
            'manage_chat_rooms',
            'ban_users',
            'delete_messages',

            // Finance
            'view_finance',
            'manage_payments',
            'manage_scholarships',
            'view_financial_reports',

            // Admission
            'view_applications',
            'process_applications',
            'approve_applications',

            // Schedule Management
            'view_schedule',
            'create_schedule',
            'edit_schedule',
            'delete_schedule',

            // Attendance
            'view_attendance',
            'mark_attendance',
            'edit_attendance',

            // System Settings
            'manage_settings',
            'manage_roles',
            'view_logs',
            'manage_system',
        ];

        // Create permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Define roles and their permissions
        $rolesData = [
            'SuperAdmin' => [
                'display_name' => 'Super Administrator',
                'description' => 'Tizimning to\'liq boshqaruvi',
                'permissions' => $permissions, // All permissions
                'dashboard_route' => '/admin/dashboard',
                'color' => 'red',
            ],
            'Teacher' => [
                'display_name' => 'O\'qituvchi',
                'description' => 'Darslar, baholar va materiallar boshqaruvi',
                'permissions' => [
                    'view_students',
                    'manage_student_grades',
                    'view_courses',
                    'create_courses',
                    'edit_courses',
                    'view_lms',
                    'manage_lms_materials',
                    'manage_lms_videos',
                    'manage_lms_tests',
                    'view_schedule',
                    'view_attendance',
                    'mark_attendance',
                    'view_chat',
                ],
                'dashboard_route' => '/teacher/dashboard',
                'color' => 'blue',
            ],
            'Student' => [
                'display_name' => 'Talaba',
                'description' => 'O\'quv jarayoni va o\'z ma\'lumotlarini ko\'rish',
                'permissions' => [
                    'view_courses',
                    'view_lms',
                    'view_schedule',
                    'view_attendance',
                    'view_chat',
                ],
                'dashboard_route' => '/student/dashboard',
                'color' => 'green',
            ],
            'PR' => [
                'display_name' => 'Jamoatchilik bilan aloqalar',
                'description' => 'Yangiliklar, tadbirlar va media boshqaruvi',
                'permissions' => [
                    'view_news',
                    'create_news',
                    'edit_news',
                    'delete_news',
                    'manage_media',
                    'manage_events',
                    'view_statistics',
                    'view_chat',
                ],
                'dashboard_route' => '/pr/dashboard',
                'color' => 'purple',
            ],
            'Marketing' => [
                'display_name' => 'Marketing',
                'description' => 'Marketing kampaniyalari va ijtimoiy tarmoqlar',
                'permissions' => [
                    'view_analytics',
                    'manage_campaigns',
                    'manage_social_media',
                    'view_marketing_reports',
                    'view_news',
                    'view_statistics',
                    'view_chat',
                ],
                'dashboard_route' => '/marketing/dashboard',
                'color' => 'orange',
            ],
            'HR' => [
                'display_name' => 'Kadrlar bo\'limi',
                'description' => 'Xodimlar va kadrlar boshqaruvi',
                'permissions' => [
                    'view_employees',
                    'create_employees',
                    'edit_employees',
                    'delete_employees',
                    'manage_payroll',
                    'manage_leave',
                    'view_reports',
                    'view_teachers',
                    'create_teachers',
                    'edit_teachers',
                    'view_chat',
                ],
                'dashboard_route' => '/hr/dashboard',
                'color' => 'teal',
            ],
            'ChatAdmin' => [
                'display_name' => 'Chat Administrator',
                'description' => 'Chat tizimi moderatsiyasi',
                'permissions' => [
                    'view_chat',
                    'moderate_chat',
                    'manage_chat_rooms',
                    'ban_users',
                    'delete_messages',
                    'view_users',
                ],
                'dashboard_route' => '/chat-admin/dashboard',
                'color' => 'indigo',
            ],
        ];

        // Create roles and assign permissions
        foreach ($rolesData as $roleName => $roleData) {
            // Create or update role
            $role = Role::firstOrCreate(['name' => $roleName]);

            // Sync permissions
            $role->syncPermissions($roleData['permissions']);

            $this->command->info("✓ {$roleName} roli yaratildi va " . count($roleData['permissions']) . " ta permission biriktirildi");
        }

        // Summary
        $this->command->info("\n=================================");
        $this->command->info("Rollar va permissionlar muvaffaqiyatli yaratildi!");
        $this->command->info("=================================\n");
        $this->command->table(
            ['Rol', 'Display Name', 'Dashboard Route', 'Permissions Count'],
            collect($rolesData)->map(function ($data, $name) {
                return [
                    $name,
                    $data['display_name'],
                    $data['dashboard_route'],
                    count($data['permissions']),
                ];
            })->toArray()
        );
    }
}
