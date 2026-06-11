<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CategoryController extends Controller
{
    public function index(Request $request): Response
    {
        $categories = Category::query()
            ->withCount(['items' => fn ($q) => $q->whereNull('deleted_at')])
            ->when($request->search, fn ($q, $s) => $q->where('name', 'like', "%{$s}%"))
            ->ordered()
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Admin/Categories/Index', [
            'categories' => $categories,
            'filters' => $request->only('search'),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Categories/Form', [
            'category' => null,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string|max:1000',
            'icon' => 'nullable|string|max:10',
            'color' => 'nullable|string|max:50',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $validated['is_active'] = $validated['is_active'] ?? true;
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        $category = Category::create($validated);

        ActivityLog::record(
            action: 'category_created',
            description: "Kategori '{$category->name}' ditambahkan",
            model: $category,
            newValues: $validated,
        );

        return redirect()->route('admin.categories.index')
            ->with('success', "Kategori '{$category->name}' berhasil ditambahkan.");
    }

    public function edit(Category $category): Response
    {
        return Inertia::render('Admin/Categories/Form', [
            'category' => $category,
        ]);
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $validated = $request->validate([
            'name' => "required|string|max:255|unique:categories,name,{$category->id}",
            'description' => 'nullable|string|max:1000',
            'icon' => 'nullable|string|max:10',
            'color' => 'nullable|string|max:50',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $oldValues = $category->only(['name', 'description', 'icon', 'color', 'is_active', 'sort_order']);
        $category->update($validated);

        ActivityLog::record(
            action: 'category_updated',
            description: "Kategori '{$category->name}' diperbarui",
            model: $category,
            oldValues: $oldValues,
            newValues: $validated,
        );

        return redirect()->route('admin.categories.index')
            ->with('success', "Kategori '{$category->name}' berhasil diperbarui.");
    }

    public function destroy(Category $category): RedirectResponse
    {
        // Prevent deletion if category has items
        if ($category->items()->exists()) {
            return redirect()->back()
                ->with('error', "Kategori '{$category->name}' tidak dapat dihapus karena masih memiliki {$category->items()->count()} barang.");
        }

        $name = $category->name;
        $category->delete();

        ActivityLog::record(
            action: 'category_deleted',
            description: "Kategori '{$name}' dihapus",
            model: $category,
        );

        return redirect()->route('admin.categories.index')
            ->with('success', "Kategori '{$name}' berhasil dihapus.");
    }
}
