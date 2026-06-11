<?php

use App\Http\Controllers\Admin\BorrowingController as AdminBorrowingController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ItemController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\User\BorrowingController as UserBorrowingController;
use App\Http\Controllers\User\CartController;
use App\Http\Controllers\User\CatalogController;
use App\Http\Controllers\User\DashboardController as UserDashboardController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    if (auth()->check()) {
        return auth()->user()->isAdmin()
            ? redirect()->route('admin.dashboard')
            : redirect()->route('user.dashboard');
    }

    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'appName' => config('app.name', 'E-Borrow'),
    ]);
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes (All Roles)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return auth()->user()->isAdmin()
            ? redirect()->route('admin.dashboard')
            : redirect()->route('user.dashboard');
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| Admin Routes (Super Admin & Staff)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified', 'role:superadmin,staff'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Category CRUD
        Route::resource('categories', CategoryController::class)->except('show');

        // Item CRUD
        Route::resource('items', ItemController::class);

        // Borrowing Management
        Route::get('/borrowings', [AdminBorrowingController::class, 'index'])->name('borrowings.index');
        Route::get('/borrowings/{borrowing}', [AdminBorrowingController::class, 'show'])->name('borrowings.show');
        Route::post('/borrowings/{borrowing}/approve', [AdminBorrowingController::class, 'approve'])->name('borrowings.approve');
        Route::post('/borrowings/{borrowing}/reject', [AdminBorrowingController::class, 'reject'])->name('borrowings.reject');
        Route::post('/borrowings/{borrowing}/handover', [AdminBorrowingController::class, 'handover'])->name('borrowings.handover');
        Route::post('/borrowings/{borrowing}/return', [AdminBorrowingController::class, 'returnItems'])->name('borrowings.return');

        // Activity Logs
        Route::get('/activity-logs', [\App\Http\Controllers\Admin\ActivityLogController::class, 'index'])->name('activity-logs.index');

        // Reports
        Route::get('/reports', [\App\Http\Controllers\Admin\ReportController::class, 'index'])->name('reports.index');
    });

/*
|--------------------------------------------------------------------------
| Super Admin Only Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified', 'role:superadmin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        // User Management (superadmin only)
        Route::resource('users', \App\Http\Controllers\Admin\UserController::class)->except('show', 'destroy');
        Route::post('/users/{user}/toggle-active', [\App\Http\Controllers\Admin\UserController::class, 'toggleActive'])->name('users.toggle-active');
    });

/*
|--------------------------------------------------------------------------
| User Routes (Regular Users)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified', 'role:user'])
    ->prefix('user')
    ->name('user.')
    ->group(function () {
        Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard');

        // Catalog
        Route::get('/catalog', [CatalogController::class, 'index'])->name('catalog.index');
        Route::get('/catalog/{item}', [CatalogController::class, 'show'])->name('catalog.show');

        // Cart
        Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
        Route::post('/cart', [CartController::class, 'store'])->name('cart.store');
        Route::patch('/cart/{cart}', [CartController::class, 'update'])->name('cart.update');
        Route::delete('/cart/{cart}', [CartController::class, 'destroy'])->name('cart.destroy');
        Route::delete('/cart', [CartController::class, 'clear'])->name('cart.clear');

        // Borrowings
        Route::get('/borrowings', [UserBorrowingController::class, 'index'])->name('borrowings.index');
        Route::post('/borrowings/checkout', [UserBorrowingController::class, 'checkout'])->name('borrowings.checkout');
        Route::get('/borrowings/{borrowing}', [UserBorrowingController::class, 'show'])->name('borrowings.show');
        Route::post('/borrowings/{borrowing}/cancel', [UserBorrowingController::class, 'cancel'])->name('borrowings.cancel');
    });

/*
|--------------------------------------------------------------------------
| Auth Routes (Breeze)
|--------------------------------------------------------------------------
*/

require __DIR__ . '/auth.php';
