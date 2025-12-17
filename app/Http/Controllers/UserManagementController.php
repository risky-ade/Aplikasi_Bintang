<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserManagementController extends Controller
{
    public function index()
    {
        $users = User::with('role')->orderBy('name')->get();
        return view('users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::whereNotIn('name', ['superadmin'])->orderBy('label')->get();

        return view('users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'      => ['required','string','max:255'],
            'username'  => ['required','string','max:50','alpha_dash','unique:users,username'],
            'email'     => ['required','email','max:255','unique:users,email'],
            'role_id'   => ['required','exists:roles,id'],
            'photo'     => ['nullable','image','mimes:jpg,jpeg,png,webp','max:2048'],
            'password'  => ['required','min:6','confirmed'],
        ], [
            'password.confirmed' => 'Konfirmasi password tidak sama.',
            'username.alpha_dash' => 'Username hanya boleh huruf, angka, dash (-), underscore (_).',
        ]);
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('users', 'public');
        }

        User::create([
            'name'     => $request->name,
            'username' => $request->username,
            'email'    => $request->email,
            'role_id'  => $request->role_id,
            'photo'    => $photoPath,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan.');
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
                'password'  => ['required','min:6','confirmed'],
            ];
        } else {
            $rules = [
                'name'      => ['required','string','max:255'],
                'username'  => ['required','string','max:50','alpha_dash','unique:users,username,'.$user->id],
                'email'     => ['required','email','max:255','unique:users,email,'.$user->id],
                'role_id'   => ['required','exists:roles,id'],
                'photo'     => ['nullable','image','mimes:jpg,jpeg,png,webp','max:2048'],
                'password'  => ['nullable','min:6','confirmed'],
            ];
        }

        $data = $request->validate($rules);

        if ($request->hasFile('photo')) {
            if ($user->photo && Storage::disk('public')->exists($user->photo)) {
                Storage::disk('public')->delete($user->photo);
            }
            $user->photo = $request->file('photo')->store('users', 'public');
        }
        // password opsional
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }


        $user->update($data);

        return redirect()->route('users.index')->with('success','User berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        // proteksi: superadmin default (misal id=1) tidak bisa dihapus
        if ($user->id === 1) {
            return back()->with('error', 'Akun superadmin default tidak dapat dihapus.');
        }

        if ($user->photo && Storage::disk('public')->exists($user->photo)) {
            Storage::disk('public')->delete($user->photo);
        }

        $user->delete();

        return back()->with('success', 'User berhasil dihapus.');
    }
}
