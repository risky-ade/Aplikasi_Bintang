<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function index()
    {
        return view('login.index');

    }

    public function authenticate(Request $request)
    {
        $data = $request->validate([
            'login'=> 'required',
            'password'=> 'required'
        ]);
            
        $login = trim($data['login']);
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        if (Auth::attempt([$field => $login, 'password' => $data['password']])) {
            $request->session()->regenerate();
            
            $request->session()->regenerate();
            
            $user = Auth::user();
            
            Log::channel('auth')->info('Login berhasil', [
                'user_id'    => $user->id,
                'username'   => $user->username ?? null,
                'email'      => $user->email ?? null,
                'ip'         => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return redirect()->intended('/');
        }

        Log::channel('auth')->warning('Login gagal', [
            'login_input' => $login,
            'field'       => $field,
            'ip'          => $request->ip(),
            'user_agent'  => $request->userAgent(),
        ]);

        return back()
        ->with(['loginError' => 'Login gagal, username atau password salah.'])
        ->withInput($request->only('login'));
    }


    
    public function logout(Request $request)
    {
        $user = Auth::user();

        if ($user) {
            Log::channel('auth')->info('Logout', [
                'user_id'    => $user->id,
                'username'   => $user->username ?? null,
                'email'      => $user->email ?? null,
                'ip'         => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        }
        Auth::logout();
 
        request()->session()->invalidate();
        request()->session()->regenerateToken();
    
        return redirect('/login');
    }
}
