<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\Borrowing;
use App\Models\Category;
use App\Models\Item;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'totalUsers' => User::where('role', 'user')->count(),
                'totalItems' => Item::count(),
                'totalAssets' => Asset::count(),
                'totalCategories' => Category::count(),
                'availableAssets' => Asset::where('status', 'available')->count(),
                'borrowedAssets' => Asset::where('status', 'borrowed')->count(),
                'maintenanceAssets' => Asset::where('status', 'maintenance')->count(),
                'pendingBorrowings' => Borrowing::where('status', 'pending')->count(),
                'activeBorrowings' => Borrowing::where('status', 'active')->count(),
                'overdueBorrowings' => Borrowing::where('status', 'active')
                    ->where('expected_return_date', '<', now()->toDateString())
                    ->count(),
            ],
            'recentBorrowings' => Borrowing::with(['user:id,name,email,department', 'details.item:id,name'])
                ->latest()
                ->take(5)
                ->get()
                ->map(fn (Borrowing $b) => [
                    'id' => $b->id,
                    'transaction_code' => $b->transaction_code,
                    'user' => $b->user,
                    'status' => $b->status,
                    'status_label' => $b->status_label,
                    'status_color' => $b->status_color,
                    'borrow_date' => $b->borrow_date->format('d M Y'),
                    'expected_return_date' => $b->expected_return_date->format('d M Y'),
                    'items_count' => $b->details->count(),
                ]),
        ]);
    }
}
