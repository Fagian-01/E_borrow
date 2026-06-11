<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware to enforce role-based access control.
 *
 * Usage in routes:
 *   ->middleware('role:superadmin')
 *   ->middleware('role:superadmin,staff')  // allow multiple roles
 */
class EnsureUserHasRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Check if user account is active
        if (!$user->is_active) {
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->withErrors(['email' => 'Akun Anda telah dinonaktifkan. Hubungi administrator.']);
        }

        // Check if user has one of the allowed roles
        if (!in_array($user->role, $roles)) {
            if ($request->inertia()) {
                return redirect()->back()->with('error', 'Anda tidak memiliki akses ke halaman ini.');
            }

            abort(403, 'Unauthorized. Required role: ' . implode(' or ', $roles));
        }

        return $next($request);
    }
}
