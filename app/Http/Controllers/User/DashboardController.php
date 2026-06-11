<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Borrowing;
use App\Models\Cart;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $user = Auth::user();

        return Inertia::render('User/Dashboard', [
            'stats' => [
                'activeBorrowings' => Borrowing::where('user_id', $user->id)
                    ->whereIn('status', ['active', 'approved'])
                    ->count(),
                'pendingBorrowings' => Borrowing::where('user_id', $user->id)
                    ->where('status', 'pending')
                    ->count(),
                'totalBorrowings' => Borrowing::where('user_id', $user->id)->count(),
                'cartItems' => Cart::where('user_id', $user->id)->sum('quantity'),
            ],
            'recentBorrowings' => Borrowing::with(['details.item:id,name,image'])
                ->where('user_id', $user->id)
                ->latest()
                ->take(5)
                ->get()
                ->map(fn (Borrowing $b) => [
                    'id' => $b->id,
                    'transaction_code' => $b->transaction_code,
                    'status' => $b->status,
                    'status_label' => $b->status_label,
                    'status_color' => $b->status_color,
                    'borrow_date' => $b->borrow_date->format('d M Y'),
                    'expected_return_date' => $b->expected_return_date->format('d M Y'),
                    'is_overdue' => $b->isOverdue(),
                    'items' => $b->details->map(fn ($d) => [
                        'name' => $d->item->name,
                        'quantity' => $d->quantity,
                    ]),
                ]),
            'categories' => Category::active()
                ->ordered()
                ->withCount(['items' => fn ($q) => $q->active()])
                ->get(['id', 'name', 'slug', 'icon', 'color']),
        ]);
    }
}
