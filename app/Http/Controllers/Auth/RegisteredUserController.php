<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle an incoming registration request.
     * - Input sanitized (strip_tags)
     * - Default role is 'user' (hardcoded, not from input)
     * - Audit logged
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:' . User::class,
            'phone' => 'nullable|string|max:20',
            'department' => 'nullable|string|max:100',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => strip_tags($request->name),
            'email' => $request->email,
            'phone' => $request->phone,
            'department' => strip_tags($request->department),
            'password' => Hash::make($request->password),
            'role' => 'user', // SECURITY: always force 'user' role — never accept from input
            'is_active' => true,
        ]);

        event(new Registered($user));

        // Audit trail
        ActivityLog::record(
            action: 'register',
            description: "User baru terdaftar: {$user->name} ({$user->email})",
            model: $user,
        );

        Auth::login($user);

        return redirect(route('user.dashboard', absolute: false));
    }
}
