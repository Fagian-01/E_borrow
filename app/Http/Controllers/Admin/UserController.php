<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function index(Request $request): Response
    {
        $users = User::query()
            ->withCount([
                'borrowings',
                'borrowings as active_borrowings_count' => fn ($q) => $q->whereIn('status', ['active', 'approved', 'pending']),
            ])
            ->when($request->search, function ($q, $s) {
                $q->where(function ($sub) use ($s) {
                    $sub->where('name', 'like', "%{$s}%")
                        ->orWhere('email', 'like', "%{$s}%")
                        ->orWhere('employee_id', 'like', "%{$s}%");
                });
            })
            ->when($request->role, fn ($q, $r) => $q->byRole($r))
            ->when($request->status === 'active', fn ($q) => $q->active())
            ->when($request->status === 'inactive', fn ($q) => $q->where('is_active', false))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
            'filters' => $request->only('search', 'role', 'status'),
            'roleCounts' => [
                'all' => User::count(),
                'superadmin' => User::byRole('superadmin')->count(),
                'staff' => User::byRole('staff')->count(),
                'user' => User::byRole('user')->count(),
            ],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Users/Form', [
            'user' => null,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Password::min(8)],
            'role' => 'required|in:superadmin,staff,user',
            'phone' => 'nullable|string|max:20',
            'department' => 'nullable|string|max:100',
            'employee_id' => 'nullable|string|max:50|unique:users',
            'is_active' => 'boolean',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['is_active'] = $validated['is_active'] ?? true;

        $user = User::create($validated);

        ActivityLog::record(
            action: 'user_created',
            description: "User '{$user->name}' ({$user->role}) ditambahkan oleh admin",
            model: $user,
        );

        return redirect()->route('admin.users.index')
            ->with('success', "User '{$user->name}' berhasil ditambahkan.");
    }

    public function edit(User $user): Response
    {
        return Inertia::render('Admin/Users/Form', [
            'user' => $user->only(['id', 'name', 'email', 'role', 'phone', 'department', 'employee_id', 'is_active']),
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => "required|string|email|max:255|unique:users,email,{$user->id}",
            'password' => ['nullable', 'confirmed', Password::min(8)],
            'role' => 'required|in:superadmin,staff,user',
            'phone' => 'nullable|string|max:20',
            'department' => 'nullable|string|max:100',
            'employee_id' => "nullable|string|max:50|unique:users,employee_id,{$user->id}",
            'is_active' => 'boolean',
        ]);

        // Only update password if provided
        if (empty($validated['password'])) {
            unset($validated['password']);
        } else {
            $validated['password'] = Hash::make($validated['password']);
        }

        $oldValues = $user->only(['name', 'email', 'role', 'is_active']);
        $user->update($validated);

        ActivityLog::record(
            action: 'user_updated',
            description: "User '{$user->name}' diperbarui oleh admin",
            model: $user,
            oldValues: $oldValues,
            newValues: $validated,
        );

        return redirect()->route('admin.users.index')
            ->with('success', "User '{$user->name}' berhasil diperbarui.");
    }

    /**
     * Toggle user active status.
     */
    public function toggleActive(User $user): RedirectResponse
    {
        // Prevent deactivating yourself
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri.');
        }

        $user->update(['is_active' => !$user->is_active]);

        $action = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';

        ActivityLog::record(
            action: 'user_status_toggled',
            description: "User '{$user->name}' {$action}",
            model: $user,
        );

        return back()->with('success', "User '{$user->name}' berhasil {$action}.");
    }
}
