<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware untuk memastikan user yang sedang login tetap memiliki akun aktif.
 * 
 * Jika user sedang login namun akunnya dinonaktifkan oleh admin/RW,
 * middleware ini akan memaksa logout user tersebut.
 */
class CheckAccountActive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();

            // Jika akun user tidak aktif, logout paksa
            if (!$user->akun_aktif) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect('/login')
                    ->with('error', 'Akun Anda telah dinonaktifkan. Silakan hubungi pengurus RW.');
            }
        }

        return $next($request);
    }
}
