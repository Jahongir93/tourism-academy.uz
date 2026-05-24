<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionManagementController extends Controller
{
    /**
     * Display a listing of permissions
     */
    public function index()
    {
        $permissions = Permission::all()->groupBy(function($permission) {
            return explode('_', $permission->name)[0];
        });
        return view('admin.settings.permissions.index', compact('permissions'));
    }

    /**
     * Show the form for creating a new permission
     */
    public function create()
    {
        return view('admin.settings.permissions.create');
    }

    /**
     * Store a newly created permission
     * BUGFIX #68: Added rate limiting (15 permissions per hour)
     * BUGFIX #70: Added permission name pattern validation (resource_action format)
     * BUGFIX #71: Added semantic uniqueness check
     */
    public function store(Request $request)
    {
        // BUGFIX #68: Rate limiting
        $rateLimitKey = 'permission_create_' . $request->ip();
        if (cache()->has($rateLimitKey) && cache()->get($rateLimitKey) >= 15) {
            return back()->withErrors(['error' => 'Juda ko\'p so\'rov yuborildi. Iltimos, bir soatdan keyin qayta urinib ko\'ring.']);
        }

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:permissions',
                'regex:/^[a-z_]+$/' // BUGFIX #70: Only lowercase letters and underscores (e.g., students_create)
            ],
        ], [
            'name.regex' => 'Ruxsat nomi faqat kichik harflar va _ belgisidan iborat bo\'lishi kerak (masalan: students_create).'
        ]);

        // BUGFIX #71: Check semantic uniqueness - ensure permission follows resource_action pattern
        $parts = explode('_', $validated['name']);
        if (count($parts) < 2) {
            return back()->withErrors(['name' => 'Ruxsat nomi "resurs_amal" formatida bo\'lishi kerak (masalan: students_create, teachers_edit).']);
        }

        try {
            Permission::create(['name' => $validated['name']]);

            // BUGFIX #68: Increment rate limit counter
            cache()->put($rateLimitKey, (cache()->get($rateLimitKey, 0) + 1), now()->addHour());

            return redirect()->route('admin.settings.permissions.index')
                ->with('success', 'Ruxsat muvaffaqiyatli yaratildi!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Ruxsat yaratishda xatolik: ' . $e->getMessage()]);
        }
    }

    /**
     * Show the form for editing a permission
     */
    public function edit(Permission $permission)
    {
        return view('admin.settings.permissions.edit', compact('permission'));
    }

    /**
     * Update the specified permission
     * BUGFIX #68: Added rate limiting (20 updates per hour)
     * BUGFIX #70: Added permission name pattern validation
     * BUGFIX #71: Added semantic format check
     */
    public function update(Request $request, Permission $permission)
    {
        // BUGFIX #68: Rate limiting
        $rateLimitKey = 'permission_update_' . $request->ip();
        if (cache()->has($rateLimitKey) && cache()->get($rateLimitKey) >= 20) {
            return back()->withErrors(['error' => 'Juda ko\'p so\'rov yuborildi. Iltimos, bir soatdan keyin qayta urinib ko\'ring.']);
        }

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:permissions,name,' . $permission->id,
                'regex:/^[a-z_]+$/' // BUGFIX #70
            ],
        ], [
            'name.regex' => 'Ruxsat nomi faqat kichik harflar va _ belgisidan iborat bo\'lishi kerak (masalan: students_create).'
        ]);

        // BUGFIX #71: Check semantic format
        $parts = explode('_', $validated['name']);
        if (count($parts) < 2) {
            return back()->withErrors(['name' => 'Ruxsat nomi "resurs_amal" formatida bo\'lishi kerak (masalan: students_create, teachers_edit).']);
        }

        try {
            DB::transaction(function () use ($validated, $permission) {
                $permission->update(['name' => $validated['name']]);
            });

            // BUGFIX #68: Increment rate limit counter
            cache()->put($rateLimitKey, (cache()->get($rateLimitKey, 0) + 1), now()->addHour());

            return redirect()->route('admin.settings.permissions.index')
                ->with('success', 'Ruxsat muvaffaqiyatli yangilandi!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Ruxsat yangilashda xatolik: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified permission
     * BUGFIX #68: Added rate limiting (10 deletions per hour)
     * BUGFIX #69: Check if permission is assigned to any role before deletion
     */
    public function destroy(Permission $permission)
    {
        // BUGFIX #68: Rate limiting
        $rateLimitKey = 'permission_delete_' . request()->ip();
        if (cache()->has($rateLimitKey) && cache()->get($rateLimitKey) >= 10) {
            return back()->withErrors(['error' => 'Juda ko\'p so\'rov yuborildi. Iltimos, bir soatdan keyin qayta urinib ko\'ring.']);
        }

        // BUGFIX #69: Check if permission is assigned to any role
        $rolesCount = Role::whereHas('permissions', function($query) use ($permission) {
            $query->where('permissions.id', $permission->id);
        })->count();

        if ($rolesCount > 0) {
            return redirect()->route('admin.settings.permissions.index')
                ->with('error', "Bu ruxsatni o\'chirib bo\'lmaydi, chunki {$rolesCount} ta rolga biriktirilgan!");
        }

        try {
            DB::transaction(function () use ($permission) {
                // First detach from all roles (as a precaution)
                $permission->roles()->detach();

                // Then delete the permission
                $permission->delete();
            });

            // BUGFIX #68: Increment rate limit counter
            cache()->put($rateLimitKey, (cache()->get($rateLimitKey, 0) + 1), now()->addHour());

            return redirect()->route('admin.settings.permissions.index')
                ->with('success', 'Ruxsat muvaffaqiyatli o\'chirildi!');
        } catch (\Exception $e) {
            return redirect()->route('admin.settings.permissions.index')
                ->with('error', 'Ruxsatni o\'chirishda xatolik: ' . $e->getMessage());
        }
    }
}
