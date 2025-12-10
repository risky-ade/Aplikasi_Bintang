<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class UserManagementController extends Controller
{
    public function index()
    {
        $users = User::with('role')->orderBy('name')->get();
        return view('users.index', compact('users'));
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        
        return view('users.edit', compact('user','roles'));
    }

    public function update(Request $request, User $user)
    {
        if ($user->isSuperAdmin() && $user->id === 1) {
            // superadmin default: role tidak boleh diganti
            $rules = [
                'name'  => ['required','string','max:255'],
                'email' => ['required','email','max:255','unique:users,email,'.$user->id],
            ];
        } else {
            $rules = [
                'name'   => ['required','string','max:255'],
                'email'  => ['required','email','max:255','unique:users,email,'.$user->id],
                'role_id'=> ['required','exists:roles,id'],
            ];
        }

        $data = $request->validate($rules);

        $user->update($data);

        return redirect()->route('users.index')->with('success','User berhasil diperbarui.');
    }
}
