<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleManagementController extends Controller
{
    /**
     * Display a listing of roles
     */
    public function index()
    {
        $roles = Role::with('permissions')->get();
        return view('admin.settings.roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new role
     */
    public function create()
    {
        $permissions = Permission::all()->groupBy(function($permission) {
            return explode('_', $permission->name)[0];
        });
        return view('admin.settings.roles.create', compact('permissions'));
    }

    /**
     * Store a newly created role
     * BUGFIX #62: Added rate limiting (10 roles per hour)
     * BUGFIX #66: Added regex validation for role name
     */
    public function store(Request $request)
    {
        // BUGFIX #62: Rate limiting
        $rateLimitKey = 'role_create_' . $request->ip();
        if (cache()->has($rateLimitKey) && cache()->get($rateLimitKey) >= 10) {
            return back()->withErrors(['error' => 'Juda ko\'p so\'rov yuborildi. Iltimos, bir soatdan keyin qayta urinib ko\'ring.']);
        }

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:roles',
                'regex:/^[a-zA-Z0-9_-]+$/' // BUGFIX #66: Only alphanumeric, underscores, hyphens
            ],
            'permissions' => 'array'
        ], [
            'name.regex' => 'Rol nomi faqat harflar, raqamlar, _ va - belgilaridan iborat bo\'lishi kerak.'
        ]);

        try {
            DB::transaction(function () use ($validated, $request, &$role) {
                $role = Role::create(['name' => $validated['name']]);

                if ($request->has('permissions')) {
                    $role->syncPermissions($request->permissions);
                }
            });

            // BUGFIX #62: Increment rate limit counter
            cache()->put($rateLimitKey, (cache()->get($rateLimitKey, 0) + 1), now()->addHour());

            return redirect()->route('admin.settings.roles.index')
                ->with('success', 'Rol muvaffaqiyatli yaratildi!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Rol yaratishda xatolik: ' . $e->getMessage()]);
        }
    }

    /**
     * Show the form for editing a role
     */
    public function edit(Role $role)
    {
        $permissions = Permission::all()->groupBy(function($permission) {
            return explode('_', $permission->name)[0];
        });
        $rolePermissions = $role->permissions->pluck('name')->toArray();
        return view('admin.settings.roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    /**
     * Update the specified role
     * BUGFIX #62: Added rate limiting (20 updates per hour)
     * BUGFIX #65: Enhanced system role protection
     * BUGFIX #66: Added regex validation for role name
     */
    public function update(Request $request, Role $role)
    {
        // BUGFIX #62: Rate limiting
        $rateLimitKey = 'role_update_' . $request->ip();
        if (cache()->has($rateLimitKey) && cache()->get($rateLimitKey) >= 20) {
            return back()->withErrors(['error' => 'Juda ko\'p so\'rov yuborildi. Iltimos, bir soatdan keyin qayta urinib ko\'ring.']);
        }

        // BUGFIX #65: Protect system roles from name changes
        $systemRoles = ['superadmin', 'admin', 'Teacher', 'Student', 'HR', 'Finance', 'Librarian'];
        if (in_array($role->name, $systemRoles)) {
            return back()->withErrors(['error' => 'Tizim rollarining nomini o\'zgartirib bo\'lmaydi!']);
        }

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:roles,name,' . $role->id,
                'regex:/^[a-zA-Z0-9_-]+$/' // BUGFIX #66
            ],
            'permissions' => 'array'
        ], [
            'name.regex' => 'Rol nomi faqat harflar, raqamlar, _ va - belgilaridan iborat bo\'lishi kerak.'
        ]);

        try {
            DB::transaction(function () use ($validated, $request, $role) {
                $role->update(['name' => $validated['name']]);
                $role->syncPermissions($request->permissions ?? []);
            });

            // BUGFIX #62: Increment rate limit counter
            cache()->put($rateLimitKey, (cache()->get($rateLimitKey, 0) + 1), now()->addHour());

            return redirect()->route('admin.settings.roles.index')
                ->with('success', 'Rol muvaffaqiyatli yangilandi!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Rol yangilashda xatolik: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified role
     * BUGFIX #62: Added rate limiting (10 deletions per hour)
     * BUGFIX #64: Check if users have this role before deletion
     * BUGFIX #65: Enhanced system role protection
     */
    public function destroy(Role $role)
    {
        // BUGFIX #62: Rate limiting
        $rateLimitKey = 'role_delete_' . request()->ip();
        if (cache()->has($rateLimitKey) && cache()->get($rateLimitKey) >= 10) {
            return back()->withErrors(['error' => 'Juda ko\'p so\'rov yuborildi. Iltimos, bir soatdan keyin qayta urinib ko\'ring.']);
        }

        // BUGFIX #65: Enhanced system role protection
        $systemRoles = ['superadmin', 'admin', 'Teacher', 'Student', 'HR', 'Finance', 'Librarian', 'Dean', 'Rector'];
        if (in_array($role->name, $systemRoles)) {
            return redirect()->route('admin.settings.roles.index')
                ->with('error', 'Tizim rollarini o\'chira olmaysiz!');
        }

        // BUGFIX #64: Check if any users have this role
        $usersCount = User::role($role->name)->count();
        if ($usersCount > 0) {
            return redirect()->route('admin.settings.roles.index')
                ->with('error', "Bu rolni o\'chirib bo\'lmaydi, chunki {$usersCount} ta foydalanuvchi bu rolga ega!");
        }

        try {
            DB::transaction(function () use ($role) {
                // First detach all permissions
                $role->permissions()->detach();

                // Then delete the role
                $role->delete();
            });

            // BUGFIX #62: Increment rate limit counter
            cache()->put($rateLimitKey, (cache()->get($rateLimitKey, 0) + 1), now()->addHour());

            return redirect()->route('admin.settings.roles.index')
                ->with('success', 'Rol muvaffaqiyatli o\'chirildi!');
        } catch (\Exception $e) {
            return redirect()->route('admin.settings.roles.index')
                ->with('error', 'Rolni o\'chirishda xatolik: ' . $e->getMessage());
        }
    }
}
