<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }
            return redirect()->route('login')->with('error', 'Silakan masuk terlebih dahulu.');
        }

        $user = Auth::user();

        if (!$user->is_active) {
            Auth::logout();
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Akun Anda dinonaktifkan oleh administrator.'], 403);
            }
            return redirect()->route('login')->with('error', 'Akun Anda dinonaktifkan oleh administrator.');
        }

        // Superadmin always has bypass permission for all roles
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        if (!in_array($user->role, $roles, true)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Akses ditolak. Anda tidak memiliki izin untuk mengakses resource ini.',
                    'required_roles' => $roles,
                    'current_role' => $user->role,
                ], 403);
            }
            abort(403, 'Akses ditolak. Anda tidak memiliki kewenangan mengakses halaman ini.');
        }

        return $next($request);
    }
}
