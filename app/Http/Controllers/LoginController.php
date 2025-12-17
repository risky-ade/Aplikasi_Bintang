<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function index()
    {
        return view('login.index');

        // $title = "Login";
        // $active = "login";
        // function index(){
        //     return view('login.index',compact('title','active'));
        // }
    }

    public function authenticate(Request $request)
    {
        $data = $request->validate([
            'login'=> 'required',
            'password'=> 'required'
        ]);


        // if(Auth::attempt($credentials)){
        //     $request->session()->regenerate();
        //     return redirect()->intended('/dashboard');
        // }
        $login = trim($data['login']);
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        if (Auth::attempt([$field => $login, 'password' => $data['password']])) {
            $request->session()->regenerate();
            return redirect()->intended('/');
        }

        return back()
        ->with(['login' => 'Login gagal, username atau password salah.'])
        ->withInput($request->only('login'));

        // return back()->with('loginError', 'Login failed!');
    }
    
    public function logout()
    {
        Auth::logout();
 
        request()->session()->invalidate();
    
        request()->session()->regenerateToken();
    
        return redirect('/login');
    }
}
