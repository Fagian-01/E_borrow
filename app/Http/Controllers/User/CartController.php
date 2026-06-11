<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Item;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class CartController extends Controller
{
    public function index(): Response
    {
        $carts = Cart::where('user_id', auth()->id())
            ->with(['item' => fn ($q) => $q->with('category:id,name,icon')])
            ->get();

        return Inertia::render('User/Cart/Index', [
            'carts' => $carts,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'item_id' => 'required|exists:items,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $item = Item::findOrFail($validated['item_id']);

        // Validate item is available and active
        if (!$item->is_active) {
            return back()->with('error', 'Barang ini tidak tersedia untuk dipinjam.');
        }

        if (!$item->hasStock($validated['quantity'])) {
            return back()->with('error', "Stok tidak cukup. Tersedia: {$item->available_stock}");
        }

        if ($validated['quantity'] > $item->max_qty_per_user) {
            return back()->with('error', "Maksimal peminjaman per user: {$item->max_qty_per_user}");
        }

        // Check if already in cart
        $existingCart = Cart::where('user_id', auth()->id())
            ->where('item_id', $validated['item_id'])
            ->first();

        if ($existingCart) {
            $newQty = $existingCart->quantity + $validated['quantity'];
            if ($newQty > $item->max_qty_per_user) {
                return back()->with('error', "Total di keranjang melebihi batas maksimal ({$item->max_qty_per_user}).");
            }
            $existingCart->update(['quantity' => $newQty]);
        } else {
            Cart::create([
                'user_id' => auth()->id(),
                'item_id' => $validated['item_id'],
                'quantity' => $validated['quantity'],
            ]);
        }

        return back()->with('success', "'{$item->name}' ditambahkan ke keranjang.");
    }

    public function update(Request $request, Cart $cart): RedirectResponse
    {
        abort_unless($cart->user_id === auth()->id(), 403);

        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $item = $cart->item;

        if ($validated['quantity'] > $item->max_qty_per_user) {
            return back()->with('error', "Maksimal: {$item->max_qty_per_user}");
        }

        if (!$item->hasStock($validated['quantity'])) {
            return back()->with('error', "Stok tidak cukup. Tersedia: {$item->available_stock}");
        }

        $cart->update(['quantity' => $validated['quantity']]);

        return back()->with('success', 'Jumlah berhasil diperbarui.');
    }

    public function destroy(Cart $cart): RedirectResponse
    {
        abort_unless($cart->user_id === auth()->id(), 403);

        $name = $cart->item->name;
        $cart->delete();

        return back()->with('success', "'{$name}' dihapus dari keranjang.");
    }

    public function clear(): RedirectResponse
    {
        Cart::where('user_id', auth()->id())->delete();

        return back()->with('success', 'Keranjang berhasil dikosongkan.');
    }
}
