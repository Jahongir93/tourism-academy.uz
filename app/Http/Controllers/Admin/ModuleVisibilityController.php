<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class ModuleVisibilityController extends Controller
{
    /**
     * Access levels for modules
     */
    private $accessLevels = [
        'none' => [
            'name' => 'Ruxsat yo\'q',
            'description' => 'Bu modulga kirish taqiqlangan',
            'color' => 'secondary',
            'icon' => 'fas fa-ban'
        ],
        'view' => [
            'name' => 'Faqat ko\'rish',
            'description' => 'Ma\'lumotlarni faqat ko\'rish mumkin',
            'color' => 'info',
            'icon' => 'fas fa-eye'
        ],
        'edit' => [
            'name' => 'Ko\'rish va tahrirlash',
            'description' => 'Ma\'lumotlarni ko\'rish va tahrirlash mumkin',
            'color' => 'warning',
            'icon' => 'fas fa-edit'
        ],
        'manage' => [
            'name' => 'To\'liq boshqarish',
            'description' => 'Barcha amallarni bajarish mumkin',
            'color' => 'success',
            'icon' => 'fas fa-user-shield'
        ]
    ];

    /**
     * Define all available modules with descriptions
     */
    private function getModules()
    {
        return [
            'dashboard' => ['name' => 'Dashboard', 'icon' => 'fas fa-home', 'description' => 'Asosiy boshqaruv paneli'],
            'students' => ['name' => 'Talabalar', 'icon' => 'fas fa-user-graduate', 'description' => 'Talabalar ro\'yxati'],
            'teachers' => ['name' => 'O\'qituvchilar', 'icon' => 'fas fa-chalkboard-teacher', 'description' => 'O\'qituvchilar ro\'yxati'],
            'groups' => ['name' => 'Guruhlar', 'icon' => 'fas fa-users', 'description' => 'Talabalar guruhlari'],
            'subjects' => ['name' => 'Fanlar', 'icon' => 'fas fa-book', 'description' => 'O\'quv fanlari'],
            'schedule' => ['name' => 'Dars jadvali', 'icon' => 'fas fa-calendar-alt', 'description' => 'Dars jadvallari'],
            'attendance' => ['name' => 'Davomat', 'icon' => 'fas fa-clipboard-check', 'description' => 'Talabalar davomati'],
            'grades' => ['name' => 'Baholar', 'icon' => 'fas fa-star', 'description' => 'Talabalar baholari'],
            'assignments' => ['name' => 'Topshiriqlar', 'icon' => 'fas fa-tasks', 'description' => 'Vazifalar va topshiriqlar'],
            'exams' => ['name' => 'Imtihonlar', 'icon' => 'fas fa-file-alt', 'description' => 'Imtihonlar va natijalar'],
            'lms' => ['name' => 'LMS', 'icon' => 'fas fa-laptop', 'description' => 'Online ta\'lim tizimi'],
            'journal' => ['name' => 'Jurnal', 'icon' => 'fas fa-book-open', 'description' => 'Elektron jurnal'],
            'curriculum' => ['name' => 'O\'quv reja', 'icon' => 'fas fa-sitemap', 'description' => 'O\'quv rejalari'],
            'vedomost' => ['name' => 'Vedomost', 'icon' => 'fas fa-table', 'description' => 'Baholar vedomosti'],
            'employees' => ['name' => 'Xodimlar', 'icon' => 'fas fa-id-card', 'description' => 'Barcha xodimlar'],
            'structure' => ['name' => 'Tashkiliy tuzilma', 'icon' => 'fas fa-building', 'description' => 'Fakultet, kafedra, lavozimlar'],
            'hr_dashboard' => ['name' => 'HR Dashboard', 'icon' => 'fas fa-user-tie', 'description' => 'Kadrlar paneli'],
            'reports' => ['name' => 'Hisobotlar', 'icon' => 'fas fa-chart-bar', 'description' => 'Turli hisobotlar'],
            'hemis' => ['name' => 'HEMIS', 'icon' => 'fas fa-sync', 'description' => 'HEMIS integratsiyasi'],
            'settings' => ['name' => 'Sozlamalar', 'icon' => 'fas fa-cog', 'description' => 'Tizim sozlamalari'],
            'statistics' => ['name' => 'Statistika', 'icon' => 'fas fa-chart-line', 'description' => 'Statistik ma\'lumotlar'],
            'finance' => ['name' => 'Moliya', 'icon' => 'fas fa-dollar-sign', 'description' => 'Moliyaviy ma\'lumotlar'],
            'cms' => ['name' => 'CMS', 'icon' => 'fas fa-edit', 'description' => 'Kontent boshqaruvi'],
            'admission' => ['name' => 'Onlayn Qabul', 'icon' => 'fas fa-user-plus', 'description' => 'Abituriyentlar qabuli']
        ];
    }

    /**
     * Display module visibility settings
     */
    public function index()
    {
        $roles = Role::with('permissions')->get();
        $modules = $this->getModules();
        $accessLevels = $this->accessLevels;

        // Get current access levels for each role and module
        $roleModuleAccess = [];
        foreach ($roles as $role) {
            foreach ($modules as $moduleKey => $moduleData) {
                $roleModuleAccess[$role->id][$moduleKey] = $this->getRoleModuleAccessLevel($role, $moduleKey);
            }
        }

        return view('admin.settings.modules.index', compact('roles', 'modules', 'accessLevels', 'roleModuleAccess'));
    }

    /**
     * Get current access level for a role and module
     */
    private function getRoleModuleAccessLevel($role, $moduleKey)
    {
        $permissions = $role->permissions->pluck('name')->toArray();

        if (in_array("manage_{$moduleKey}", $permissions)) {
            return 'manage';
        }
        if (in_array("edit_{$moduleKey}", $permissions) || in_array("update_{$moduleKey}", $permissions)) {
            return 'edit';
        }
        if (in_array("view_{$moduleKey}", $permissions)) {
            return 'view';
        }
        return 'none';
    }

    /**
     * Update module access for a role via AJAX
     */
    public function updateModuleAccess(Request $request)
    {
        $validated = $request->validate([
            'role_id' => 'required|exists:roles,id',
            'module' => 'required|string',
            'access_level' => 'required|in:none,view,edit,manage'
        ]);

        $role = Role::findOrFail($validated['role_id']);
        $module = $validated['module'];
        $accessLevel = $validated['access_level'];

        // Get all current permissions
        $currentPermissions = $role->permissions->pluck('name')->toArray();

        // Remove all existing permissions for this module
        $modulePermissions = ["view_{$module}", "edit_{$module}", "create_{$module}", "delete_{$module}", "manage_{$module}", "update_{$module}"];
        $filteredPermissions = array_diff($currentPermissions, $modulePermissions);

        // Add new permissions based on access level
        $newPermissions = [];
        switch ($accessLevel) {
            case 'view':
                $newPermissions = ["view_{$module}"];
                break;
            case 'edit':
                $newPermissions = ["view_{$module}", "edit_{$module}", "update_{$module}"];
                break;
            case 'manage':
                $newPermissions = ["view_{$module}", "edit_{$module}", "create_{$module}", "delete_{$module}", "manage_{$module}", "update_{$module}"];
                break;
        }

        // Create permissions if they don't exist
        foreach ($newPermissions as $permName) {
            Permission::firstOrCreate(['name' => $permName, 'guard_name' => 'web']);
        }

        // Sync all permissions
        $allPermissions = array_unique(array_merge($filteredPermissions, $newPermissions));
        $role->syncPermissions($allPermissions);

        // Clear permission cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        return response()->json([
            'success' => true,
            'message' => "'{$role->name}' roli uchun '{$module}' moduli ruxsatlari yangilandi!",
            'access_level' => $accessLevel
        ]);
    }

    /**
     * Update module visibility for a role (form submit)
     */
    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'access_levels' => 'array'
        ]);

        // Get all current permissions
        $currentPermissions = $role->permissions->pluck('name')->toArray();

        // Remove all module-related permissions
        $modulePermissions = [];
        foreach ($this->getModules() as $moduleKey => $moduleData) {
            $modulePermissions = array_merge($modulePermissions, [
                "view_{$moduleKey}", "edit_{$moduleKey}", "create_{$moduleKey}",
                "delete_{$moduleKey}", "manage_{$moduleKey}", "update_{$moduleKey}"
            ]);
        }
        $filteredPermissions = array_diff($currentPermissions, $modulePermissions);

        // Add new permissions based on access levels
        $newPermissions = [];
        if ($request->has('access_levels')) {
            foreach ($request->access_levels as $module => $accessLevel) {
                switch ($accessLevel) {
                    case 'view':
                        $newPermissions[] = "view_{$module}";
                        break;
                    case 'edit':
                        $newPermissions[] = "view_{$module}";
                        $newPermissions[] = "edit_{$module}";
                        $newPermissions[] = "update_{$module}";
                        break;
                    case 'manage':
                        $newPermissions[] = "view_{$module}";
                        $newPermissions[] = "edit_{$module}";
                        $newPermissions[] = "create_{$module}";
                        $newPermissions[] = "delete_{$module}";
                        $newPermissions[] = "manage_{$module}";
                        $newPermissions[] = "update_{$module}";
                        break;
                }
            }
        }

        // Create permissions if they don't exist
        foreach ($newPermissions as $permName) {
            Permission::firstOrCreate(['name' => $permName, 'guard_name' => 'web']);
        }

        // Sync all permissions
        $allPermissions = array_unique(array_merge($filteredPermissions, $newPermissions));
        $role->syncPermissions($allPermissions);

        // Clear permission cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()->route('admin.settings.modules.index')
            ->with('success', "'{$role->name}' roli uchun modul ruxsatlari yangilandi!");
    }
}
