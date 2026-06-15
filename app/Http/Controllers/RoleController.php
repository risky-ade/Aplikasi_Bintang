<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::with('permissions')->get();
        return view('roles.index', compact('roles'));
    }

    public function editPermissions(Role $role)
    {
        $permissions = Permission::orderBy('name')->get();
        $rolePermIds = $role->permissions->pluck('id')->toArray();

        return view('roles.edit', compact('role', 'permissions', 'rolePermIds'));
    }

    public function updatePermissions(Request $request, Role $role)
    {
        if ($role->name === 'superadmin') {
            Log::channel('role')->warning('Update permission superadmin ditolak', [
                'role_id' => $role->id,
                'role_name' => $role->name,
                'user_id' => Auth::id(),
                'ip_address' => request()->ip(),
            ]);

            return back()->with('error', 'Permission Superadmin tidak boleh diubah (punya akses penuh).');
        }

        $permIds = $request->input('permissions', []);
        $before = $role->permissions()->pluck('permissions.name')->toArray();
        $role->permissions()->sync($permIds);
        $role->load('permissions');

        Log::channel('role')->info('Hak akses role berhasil diperbarui', [
            'role_id' => $role->id,
            'role_name' => $role->name,
            'before' => $before,
            'after' => $role->permissions->pluck('name')->toArray(),
            'user' => ['id' => Auth::id(), 'name' => Auth::user()->name ?? null],
            'ip_address' => request()->ip(),
            'waktu' => now()->toDateTimeString(),
        ]);

        return redirect()->route('roles.index')->with('success', 'Hak akses role berhasil diperbarui.');
    }
}
