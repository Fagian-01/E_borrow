<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Borrowing;
use App\Models\BorrowingDetail;
use App\Models\Cart;
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
        $borrowings = Borrowing::forUser(auth()->id())
            ->with(['details.item:id,name,image'])
            ->when($request->status, fn ($q, $s) => $q->byStatus($s))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('User/Borrowings/Index', [
            'borrowings' => $borrowings,
            'filters' => $request->only('status'),
        ]);
    }

    /**
     * Checkout: convert cart to borrowing with pessimistic locking.
     */
    public function checkout(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'borrow_date' => 'required|date|after_or_equal:today',
            'expected_return_date' => 'required|date|after:borrow_date',
            'purpose' => 'nullable|string|max:1000',
            'notes' => 'nullable|string|max:500',
        ]);

        $userId = auth()->id();
        $cartItems = Cart::where('user_id', $userId)->with('item')->get();

        if ($cartItems->isEmpty()) {
            return back()->with('error', 'Keranjang kosong. Tambahkan barang terlebih dahulu.');
        }

        try {
            return DB::transaction(function () use ($validated, $userId, $cartItems) {
                // ── Pessimistic Locking ──────────────────────────
                $itemIds = $cartItems->pluck('item_id')->toArray();
                $items = Item::whereIn('id', $itemIds)
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');

                // Validate stock availability
                foreach ($cartItems as $cartItem) {
                    $item = $items->get($cartItem->item_id);

                    if (!$item || !$item->is_active) {
                        throw new \Exception("Barang '{$cartItem->item->name}' sudah tidak tersedia.");
                    }

                    if (!$item->hasStock($cartItem->quantity)) {
                        throw new \Exception("Stok '{$item->name}' tidak mencukupi. Tersedia: {$item->available_stock}");
                    }

                    // Validate max borrow days
                    $borrowDays = \Carbon\Carbon::parse($validated['borrow_date'])
                        ->diffInDays(\Carbon\Carbon::parse($validated['expected_return_date']));
                    if ($borrowDays > $item->max_borrow_days) {
                        throw new \Exception("Durasi pinjam '{$item->name}' melebihi batas ({$item->max_borrow_days} hari).");
                    }
                }

                // ── Create Borrowing ────────────────────────────
                $borrowing = Borrowing::create([
                    'user_id' => $userId,
                    'status' => 'pending',
                    'borrow_date' => $validated['borrow_date'],
                    'expected_return_date' => $validated['expected_return_date'],
                    'purpose' => strip_tags($validated['purpose'] ?? ''),
                    'notes' => strip_tags($validated['notes'] ?? ''),
                ]);

                // ── Create Borrowing Details & Decrement Stock ──
                foreach ($cartItems as $cartItem) {
                    $item = $items->get($cartItem->item_id);

                    BorrowingDetail::create([
                        'borrowing_id' => $borrowing->id,
                        'item_id' => $cartItem->item_id,
                        'quantity' => $cartItem->quantity,
                    ]);

                    // Decrement available stock
                    $item->decrement('available_stock', $cartItem->quantity);
                }

                // ── Clear cart ────────────────────────────────────
                Cart::where('user_id', $userId)->delete();

                // ── Audit log ─────────────────────────────────────
                ActivityLog::record(
                    action: 'borrowing_submitted',
                    description: "Pengajuan peminjaman #{$borrowing->transaction_code} diajukan",
                    model: $borrowing,
                );

                return redirect()->route('user.borrowings.index')
                    ->with('success', "Pengajuan peminjaman #{$borrowing->transaction_code} berhasil dikirim. Menunggu persetujuan admin.");
            });
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function show(Borrowing $borrowing): Response
    {
        abort_unless($borrowing->user_id === auth()->id(), 403);

        $borrowing->load([
            'details.item:id,name,image,fine_per_day',
            'details.asset:id,asset_code,barcode',
            'approver:id,name',
            'handler:id,name',
        ]);

        return Inertia::render('User/Borrowings/Show', [
            'borrowing' => $borrowing,
        ]);
    }

    public function cancel(Borrowing $borrowing): RedirectResponse
    {
        abort_unless($borrowing->user_id === auth()->id(), 403);

        if ($borrowing->status !== 'pending') {
            return back()->with('error', 'Hanya peminjaman dengan status pending yang dapat dibatalkan.');
        }

        DB::transaction(function () use ($borrowing) {
            // Restore stock
            foreach ($borrowing->details as $detail) {
                Item::where('id', $detail->item_id)->increment('available_stock', $detail->quantity);
            }

            $borrowing->update(['status' => 'cancelled']);

            ActivityLog::record(
                action: 'borrowing_cancelled',
                description: "Peminjaman #{$borrowing->transaction_code} dibatalkan oleh user",
                model: $borrowing,
            );
        });

        return back()->with('success', 'Peminjaman berhasil dibatalkan.');
    }
}
