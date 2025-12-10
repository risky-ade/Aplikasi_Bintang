<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;

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
            return back()->with('error', 'Permission Superadmin tidak boleh diubah (punya akses penuh).');
        }

        $permIds = $request->input('permissions', []);
        $role->permissions()->sync($permIds);

        return redirect()->route('roles.index')->with('success', 'Hak akses role berhasil diperbarui.');
    }
}
