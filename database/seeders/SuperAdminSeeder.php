<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $role = Role::where('name', 'superadmin')->first();

        $user = User::firstOrCreate(
            ['email' => 'superadmin@example.com'],
            [
                'name'     => 'Super Admin',
                'username'     => 'superadmin',
                'password' => Hash::make('adminsu123'), 
                'role_id'  => $role?->id,
            ]
        );

        if (!$user->role_id && $role) {
            $user->role_id = $role->id;
            $user->save();
        }
    }
}
