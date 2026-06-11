<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Item;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CatalogController extends Controller
{
    public function index(Request $request): Response
    {
        $items = Item::query()
            ->active()
            ->with('category:id,name,icon,color')
            ->withCount(['assets' => fn ($q) => $q->available()])
            ->when($request->search, fn ($q, $s) => $q->search($s))
            ->when($request->category, fn ($q, $c) => $q->where('category_id', $c))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return Inertia::render('User/Catalog/Index', [
            'items' => $items,
            'categories' => Category::active()->ordered()->withCount(['items' => fn ($q) => $q->active()])->get(),
            'filters' => $request->only('search', 'category'),
        ]);
    }

    public function show(Item $item): Response
    {
        abort_unless($item->is_active, 404);

        $item->load(['category:id,name,icon,color']);
        $item->loadCount(['assets' => fn ($q) => $q->available()]);

        $relatedItems = Item::active()
            ->where('category_id', $item->category_id)
            ->where('id', '!=', $item->id)
            ->limit(4)
            ->get(['id', 'name', 'image', 'available_stock', 'total_stock']);

        return Inertia::render('User/Catalog/Show', [
            'item' => $item,
            'relatedItems' => $relatedItems,
            'inCart' => auth()->user()->carts()->where('item_id', $item->id)->exists(),
            'cartQuantity' => auth()->user()->carts()->where('item_id', $item->id)->value('quantity') ?? 0,
        ]);
    }
}
