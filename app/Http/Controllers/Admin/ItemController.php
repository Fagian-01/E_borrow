<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Asset;
use App\Models\Category;
use App\Models\Item;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class ItemController extends Controller
{
    public function index(Request $request): Response
    {
        $items = Item::query()
            ->with('category:id,name,icon,color')
            ->withCount('assets')
            ->when($request->search, fn ($q, $s) => $q->search($s))
            ->when($request->category, fn ($q, $c) => $q->where('category_id', $c))
            ->when($request->status === 'active', fn ($q) => $q->where('is_active', true))
            ->when($request->status === 'inactive', fn ($q) => $q->where('is_active', false))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Admin/Items/Index', [
            'items' => $items,
            'categories' => Category::active()->ordered()->get(['id', 'name', 'icon']),
            'filters' => $request->only('search', 'category', 'status'),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Items/Form', [
            'item' => null,
            'categories' => Category::active()->ordered()->get(['id', 'name', 'icon']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'specifications' => 'nullable|string|max:2000',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'total_stock' => 'required|integer|min:0',
            'max_borrow_days' => 'required|integer|min:1|max:365',
            'max_qty_per_user' => 'required|integer|min:1',
            'fine_per_day' => 'required|numeric|min:0',
            'is_active' => 'boolean',
            'requires_approval' => 'boolean',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('items', 'public');
        }

        $validated['available_stock'] = $validated['total_stock'];
        $validated['is_active'] = $validated['is_active'] ?? true;
        $validated['requires_approval'] = $validated['requires_approval'] ?? true;

        $item = Item::create($validated);

        ActivityLog::record(
            action: 'item_created',
            description: "Barang '{$item->name}' ditambahkan",
            model: $item,
        );

        return redirect()->route('admin.items.index')
            ->with('success', "Barang '{$item->name}' berhasil ditambahkan.");
    }

    public function show(Item $item): Response
    {
        $item->load(['category:id,name,icon,color', 'assets' => fn ($q) => $q->orderBy('asset_code')]);

        return Inertia::render('Admin/Items/Show', [
            'item' => $item,
        ]);
    }

    public function edit(Item $item): Response
    {
        return Inertia::render('Admin/Items/Form', [
            'item' => $item,
            'categories' => Category::active()->ordered()->get(['id', 'name', 'icon']),
        ]);
    }

    public function update(Request $request, Item $item): RedirectResponse
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'specifications' => 'nullable|string|max:2000',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'total_stock' => 'required|integer|min:0',
            'max_borrow_days' => 'required|integer|min:1|max:365',
            'max_qty_per_user' => 'required|integer|min:1',
            'fine_per_day' => 'required|numeric|min:0',
            'is_active' => 'boolean',
            'requires_approval' => 'boolean',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($item->image) {
                Storage::disk('public')->delete($item->image);
            }
            $validated['image'] = $request->file('image')->store('items', 'public');
        }

        // Adjust available_stock proportionally if total_stock changed
        $stockDiff = $validated['total_stock'] - $item->total_stock;
        $validated['available_stock'] = max(0, $item->available_stock + $stockDiff);

        $oldValues = $item->only(['name', 'category_id', 'total_stock', 'is_active']);
        $item->update($validated);

        ActivityLog::record(
            action: 'item_updated',
            description: "Barang '{$item->name}' diperbarui",
            model: $item,
            oldValues: $oldValues,
            newValues: $validated,
        );

        return redirect()->route('admin.items.index')
            ->with('success', "Barang '{$item->name}' berhasil diperbarui.");
    }

    public function destroy(Item $item): RedirectResponse
    {
        // Check if item has active borrowings
        $activeBorrowings = $item->borrowingDetails()
            ->whereHas('borrowing', fn ($q) => $q->whereIn('status', ['pending', 'approved', 'active']))
            ->exists();

        if ($activeBorrowings) {
            return redirect()->back()
                ->with('error', "Barang '{$item->name}' tidak dapat dihapus karena memiliki peminjaman aktif.");
        }

        $name = $item->name;

        // Delete image
        if ($item->image) {
            Storage::disk('public')->delete($item->image);
        }

        $item->delete(); // Soft delete

        ActivityLog::record(
            action: 'item_deleted',
            description: "Barang '{$name}' dihapus",
            model: $item,
        );

        return redirect()->route('admin.items.index')
            ->with('success', "Barang '{$name}' berhasil dihapus.");
    }
}
