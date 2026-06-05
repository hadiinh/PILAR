<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Jika user wajib mengganti password (must_change_password=true),
 * paksa redirect ke halaman ganti password sebelum mengakses fitur lain.
 *
 * Route yang dibebaskan: halaman ganti password itu sendiri dan logout.
 */
class ForceChangePassword
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();
        if (!$user->must_change_password) {
            return $next($request);
        }

        $allowed = [
            'password.force-change.show',
            'password.force-change.submit',
            'logout',
        ];

        $routeName = optional($request->route())->getName();
        if (in_array($routeName, $allowed, true)) {
            return $next($request);
        }

        // Jangan redirect untuk request non-HTML (AJAX) supaya tidak loop.
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Anda harus mengganti kata sandi sebelum melanjutkan.',
            ], 423);
        }

        return redirect()->route('password.force-change.show')
            ->with('info', 'Silakan ubah kata sandi sebelum melanjutkan.');
    }
}
