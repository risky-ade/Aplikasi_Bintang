<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function index()
    {
        $title = "Login";
        $active = "login";
        function index(){
            return view('login.index',compact('title','active'));
        }
    }
    
    public function logout()
    {
        Auth::logout();
 
        request()->session()->invalidate();
    
        request()->session()->regenerateToken();
    
        return redirect('/');
    }
}
