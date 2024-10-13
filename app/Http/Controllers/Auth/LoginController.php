<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login()
    {
        return view('auth.login');
    }
    public function postlogin(Request $request)
    {
        if (Auth::attempt($request->only('email', 'password'))) {
            $user = Auth::user();
            return redirect('/dashboard')->with('success', 'Login success. Welcome, ' . $user->name);
        }

        // Gagal login, redirect ke halaman login
        return redirect('/login')->with('error', 'Login failed. Please check your credentials.');
    }
    public function logout()
    {
        Auth::logout();
        return redirect('/login');
    }
}
