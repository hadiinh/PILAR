<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (auth()->check()) {
            return redirect('/beranda');
        }
        return view('auth.login');
    }

    /**
     * Login berbasis NIK.
     * Rate limit: 5 percobaan per menit per (NIK + IP).
     */
    public function login(Request $request)
    {
        $validated = $request->validate([
            'nik'      => 'required|string|digits:16',
            'password' => 'required|string',
        ], [
            'nik.required' => 'NIK wajib diisi.',
            'nik.digits'   => 'NIK harus terdiri dari 16 angka.',
        ]);

        $key = 'login:' . $validated['nik'] . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            throw ValidationException::withMessages([
                'nik' => "Terlalu banyak percobaan. Coba lagi dalam {$seconds} detik.",
            ]);
        }

        $credentials = [
            'nik'        => $validated['nik'],
            'password'   => $validated['password'],
            'akun_aktif' => true,
        ];

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            RateLimiter::clear($key);
            $request->session()->regenerate();
            return redirect('/beranda')->with('success', 'Anda berhasil masuk.');
        }

        RateLimiter::hit($key, 60);

        return back()->withErrors([
            'nik' => 'NIK atau kata sandi salah, atau akun belum aktif.',
        ])->onlyInput('nik');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login')->with('success', 'Anda telah keluar.');
    }
}
