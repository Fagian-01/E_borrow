<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default with every Inertia response.
     * This is the single source of truth for frontend auth/role/permission data.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();

        return [
            ...parent::share($request),

            // ── Auth Data (always available in Vue via usePage) ───
            'auth' => [
                'user' => $user ? [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'phone' => $user->phone,
                    'department' => $user->department,
                    'employee_id' => $user->employee_id,
                    'avatar' => $user->avatar,
                    'is_active' => $user->is_active,
                    // Role helper flags for Vue templates
                    'is_super_admin' => $user->isSuperAdmin(),
                    'is_staff' => $user->isStaff(),
                    'is_user' => $user->isUser(),
                    'is_admin' => $user->isAdmin(),
                ] : null,
            ],

            // ── Flash Messages (for toast notifications) ─────────
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
                'warning' => fn () => $request->session()->get('warning'),
                'info' => fn () => $request->session()->get('info'),
            ],

            // ── Cart Count (for navbar badge) ────────────────────
            'cartCount' => fn () => $user
                ? \App\Models\Cart::where('user_id', $user->id)->sum('quantity')
                : 0,

            // ── Unread Notifications Count ───────────────────────
            'unreadNotifications' => fn () => $user
                ? $user->unreadNotifications()->count()
                : 0,

            // ── App Config ───────────────────────────────────────
            'appName' => config('app.name', 'E-Borrow'),
        ];
    }
}
