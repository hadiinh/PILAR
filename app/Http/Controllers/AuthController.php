<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Show login form
    public function showLogin()
    {
        if (auth()->check()) {
            return redirect('/beranda');
        }
        return view('auth.login');
    }

    // Show register form
    public function showRegister()
    {
        if (auth()->check()) {
            return redirect('/beranda');
        }
        return view('auth.register');
    }

    // Handle login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect('/beranda')->with('success', 'Anda berhasil masuk.');
        }

        return back()->withErrors([
            'email' => 'Email atau kata sandi salah.'
        ])->onlyInput('email');
    }

    // Handle register
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|min:3|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed'
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $user->role = 'user';
        $user->save();

        return redirect('/login')->with('success', 'Pendaftaran berhasil. Silakan masuk dengan akun Anda.');
    }

    // Handle logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login')->with('success', 'Anda telah keluar.');
    }
}
