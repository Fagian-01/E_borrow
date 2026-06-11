<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Asset;
use App\Models\Borrowing;
use App\Models\Item;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class BorrowingController extends Controller
{
    public function index(Request $request): Response
    {
        $borrowings = Borrowing::query()
            ->with(['user:id,name,email,department', 'details.item:id,name'])
            ->when($request->status, fn ($q, $s) => $q->byStatus($s))
            ->when($request->search, function ($q, $s) {
                $q->where(function ($sub) use ($s) {
                    $sub->where('transaction_code', 'like', "%{$s}%")
                        ->orWhereHas('user', fn ($uq) => $uq->where('name', 'like', "%{$s}%"));
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Admin/Borrowings/Index', [
            'borrowings' => $borrowings,
            'filters' => $request->only('status', 'search'),
            'statusCounts' => [
                'pending' => Borrowing::pending()->count(),
                'active' => Borrowing::active()->count(),
                'overdue' => Borrowing::overdue()->count(),
            ],
        ]);
    }

    public function show(Borrowing $borrowing): Response
    {
        $borrowing->load([
            'user:id,name,email,phone,department',
            'details.item:id,name,image,fine_per_day',
            'details.asset:id,asset_code,barcode,serial_number',
            'approver:id,name',
            'handler:id,name',
            'receiver:id,name',
        ]);

        return Inertia::render('Admin/Borrowings/Show', [
            'borrowing' => $borrowing,
        ]);
    }

    /**
     * Approve a pending borrowing.
     */
    public function approve(Borrowing $borrowing): RedirectResponse
    {
        if ($borrowing->status !== 'pending') {
            return back()->with('error', 'Peminjaman ini tidak dalam status pending.');
        }

        $borrowing->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        ActivityLog::record(
            action: 'borrowing_approved',
            description: "Peminjaman #{$borrowing->transaction_code} disetujui",
            model: $borrowing,
        );

        return back()->with('success', "Peminjaman #{$borrowing->transaction_code} telah disetujui.");
    }

    /**
     * Reject a pending borrowing.
     */
    public function reject(Request $request, Borrowing $borrowing): RedirectResponse
    {
        if ($borrowing->status !== 'pending') {
            return back()->with('error', 'Peminjaman ini tidak dalam status pending.');
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        DB::transaction(function () use ($borrowing, $validated) {
            // Restore stock
            foreach ($borrowing->details as $detail) {
                Item::where('id', $detail->item_id)->increment('available_stock', $detail->quantity);
            }

            $borrowing->update([
                'status' => 'rejected',
                'rejection_reason' => strip_tags($validated['rejection_reason']),
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

            ActivityLog::record(
                action: 'borrowing_rejected',
                description: "Peminjaman #{$borrowing->transaction_code} ditolak: {$validated['rejection_reason']}",
                model: $borrowing,
            );
        });

        return back()->with('success', "Peminjaman #{$borrowing->transaction_code} telah ditolak.");
    }

    /**
     * Handover: mark as active (items physically given).
     */
    public function handover(Borrowing $borrowing): RedirectResponse
    {
        if ($borrowing->status !== 'approved') {
            return back()->with('error', 'Peminjaman harus berstatus "Disetujui" untuk diserahkan.');
        }

        $borrowing->update([
            'status' => 'active',
            'handed_by' => auth()->id(),
            'handed_at' => now(),
        ]);

        ActivityLog::record(
            action: 'borrowing_handed_over',
            description: "Barang peminjaman #{$borrowing->transaction_code} diserahkan",
            model: $borrowing,
        );

        return back()->with('success', "Barang telah diserahkan. Peminjaman #{$borrowing->transaction_code} aktif.");
    }

    /**
     * Return: mark as returned + calculate fines.
     * Method name: returnItems (since "return" is a PHP reserved keyword)
     */
    public function returnItems(Request $request, Borrowing $borrowing): RedirectResponse
    {
        if (!in_array($borrowing->status, ['active', 'overdue'])) {
            return back()->with('error', 'Peminjaman harus berstatus aktif/terlambat untuk dikembalikan.');
        }

        DB::transaction(function () use ($borrowing) {
            $borrowing->load('details.item');

            $totalFine = 0;

            foreach ($borrowing->details as $detail) {
                // Mark as returned
                $detail->update([
                    'is_returned' => true,
                    'returned_at' => now(),
                    'returned_to' => auth()->id(),
                ]);

                // Restore stock
                Item::where('id', $detail->item_id)->increment('available_stock', $detail->quantity);

                // Release asset if assigned
                if ($detail->asset_id) {
                    Asset::where('id', $detail->asset_id)->update(['status' => 'available']);
                }

                // Calculate fine
                if ($borrowing->isOverdue()) {
                    $fine = $detail->item->fine_per_day * $borrowing->overdue_days * $detail->quantity;
                    $detail->update(['fine_amount' => $fine]);
                    $totalFine += $fine;
                }
            }

            $borrowing->update([
                'status' => 'returned',
                'actual_return_date' => now()->toDateString(),
                'total_fine' => $totalFine,
                'received_by' => auth()->id(),
                'received_at' => now(),
            ]);

            ActivityLog::record(
                action: 'borrowing_returned',
                description: "Peminjaman #{$borrowing->transaction_code} dikembalikan" .
                    ($totalFine > 0 ? " (denda: Rp " . number_format($totalFine, 0, ',', '.') . ")" : ''),
                model: $borrowing,
            );
        });

        return back()->with('success', "Peminjaman #{$borrowing->transaction_code} berhasil dikembalikan.");
    }
}
