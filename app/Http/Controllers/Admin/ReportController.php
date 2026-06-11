<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Borrowing;
use App\Models\BorrowingDetail;
use App\Models\Category;
use App\Models\Item;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class ReportController extends Controller
{
    public function index(): Response
    {
        // ── Summary Stats ───────────────────────
        $totalBorrowings = Borrowing::count();
        $activeBorrowings = Borrowing::whereIn('status', ['active', 'approved'])->count();
        $completedBorrowings = Borrowing::where('status', 'returned')->count();
        $totalFines = Borrowing::sum('total_fine');

        // ── Monthly Borrowing Trend (last 6 months) ─────
        $monthlyTrend = Borrowing::select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
                DB::raw('COUNT(*) as total'),
                DB::raw("SUM(CASE WHEN status = 'returned' THEN 1 ELSE 0 END) as returned"),
                DB::raw("SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected"),
            )
            ->where('created_at', '>=', now()->subMonths(6)->startOfMonth())
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->map(fn ($row) => [
                'month' => \Carbon\Carbon::parse($row->month . '-01')->format('M Y'),
                'total' => $row->total,
                'returned' => $row->returned,
                'rejected' => $row->rejected,
            ]);

        // ── Top Borrowed Items ──────────────────
        $topItems = BorrowingDetail::select('item_id', DB::raw('SUM(quantity) as total_qty'))
            ->groupBy('item_id')
            ->orderByDesc('total_qty')
            ->limit(10)
            ->with('item:id,name,image,category_id')
            ->get()
            ->map(fn ($d) => [
                'name' => $d->item?->name,
                'image' => $d->item?->image,
                'total_qty' => $d->total_qty,
            ]);

        // ── Borrowing by Category ───────────────
        $byCategory = Category::withCount(['items as borrowing_count' => function ($q) {
                $q->withCount('borrowingDetails');
            }])
            ->get()
            ->map(fn ($c) => [
                'name' => $c->name,
                'color' => $c->color ?? '#6366f1',
                'count' => $c->borrowing_count,
            ]);

        // ── Status Distribution ─────────────────
        $statusDistribution = Borrowing::select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get()
            ->map(fn ($row) => [
                'status' => $row->status,
                'count' => $row->count,
            ]);

        // ── Recent Fine Activity ────────────────
        $recentFines = Borrowing::where('total_fine', '>', 0)
            ->with('user:id,name,department')
            ->latest('received_at')
            ->limit(5)
            ->get()
            ->map(fn (Borrowing $b) => [
                'transaction_code' => $b->transaction_code,
                'user' => $b->user?->name,
                'department' => $b->user?->department,
                'fine' => $b->total_fine,
                'date' => $b->received_at?->format('d M Y'),
            ]);

        // ── Active Users This Month ─────────────
        $activeUsersThisMonth = Borrowing::where('created_at', '>=', now()->startOfMonth())
            ->distinct('user_id')
            ->count('user_id');

        return Inertia::render('Admin/Reports/Index', [
            'summary' => [
                'totalBorrowings' => $totalBorrowings,
                'activeBorrowings' => $activeBorrowings,
                'completedBorrowings' => $completedBorrowings,
                'totalFines' => $totalFines,
                'totalUsers' => User::where('role', 'user')->count(),
                'totalItems' => Item::count(),
                'activeUsersThisMonth' => $activeUsersThisMonth,
            ],
            'monthlyTrend' => $monthlyTrend,
            'topItems' => $topItems,
            'statusDistribution' => $statusDistribution,
            'recentFines' => $recentFines,
        ]);
    }
}
