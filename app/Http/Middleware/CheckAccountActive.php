<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware untuk memastikan user yang sedang login tetap memiliki akun aktif
 * dan session_version-nya selaras dengan record di DB.
 *
 * - Jika akun_aktif=false: logout paksa.
 * - Jika session_version di session != DB: paksa logout (mis. password baru saja direset).
 */
class CheckAccountActive
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();

            // Akun dinonaktifkan oleh admin
            if (!$user->akun_aktif) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect('/login')
                    ->with('error', 'Akun Anda telah dinonaktifkan. Silakan hubungi pengurus RW.');
            }

            // Force logout via session_version mismatch (mis. password baru saja direset oleh admin/lupa-password)
            $sessionVersion = $request->session()->get('session_version');
            if ($sessionVersion !== null && (int) $sessionVersion !== (int) $user->session_version) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect('/login')
                    ->with('error', 'Kata sandi Anda telah diperbarui. Silakan login ulang.');
            }
        }

        return $next($request);
    }
}
