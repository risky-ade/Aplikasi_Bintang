<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\PasswordResetRequest;
use Illuminate\Support\Facades\Mail;

class ForgotPasswordRequestController extends Controller
{
    public function create()
    {
        return view('login.forgot');
    }

    public function store(Request $request)
    {
        $request->validate([
            'login' => ['required','string'],
            'note'  => ['nullable','string','max:500'],
        ]);

        $login = trim($request->login);

        $user = User::where('username', $login)
            ->orWhere('email', $login)
            ->first();
        

        $ticket = PasswordResetRequest::create([
            'login'   => $login,
            'user_id' => $user?->id,
            'note'    => $request->note,
            'status'  => 'pending',
        ]);

        // kirim email ke semua superadmin
        $superadminRoleId = Role::where('name','superadmin')->value('id');
        $superadmins = User::where('role_id', $superadminRoleId)
        ->pluck('email')
        ->filter()
        ->unique()
        ->values()
        ->toArray();

        if (!empty($superadmins)) {
            Mail::to($superadmins)->send(new \App\Mail\PasswordResetRequestedMail($ticket));
        }

        return back()->with('success', 'Permintaan reset password sudah dikirim ke admin.');
    }
}
