<?php

namespace App\Http\Controllers\Admin\Shop;

use App\Http\Controllers\Controller;
use App\Models\ShopCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ShopCategoryController extends Controller
{
    public function index()
    {
        $categories = ShopCategory::withCount('products')->orderBy('sort_order')->get();
        return view('admin.shop.categories.index', compact('categories'));
    }

    public function create()
    {
        $parents = ShopCategory::whereNull('parent_id')->orderBy('name')->get();
        return view('admin.shop.categories.create', compact('parents'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'parent_id'   => 'nullable|exists:shop_categories,id',
            'sort_order'  => 'integer|min:0',
            'is_active'   => 'boolean',
            'image'       => 'nullable|image|max:1024',
        ]);

        $validated['slug']      = Str::slug($validated['name']);
        $validated['is_active'] = $request->boolean('is_active', true);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('shop/categories', 'public');
        }

        ShopCategory::create($validated);

        return redirect()->route('admin.shop.categories.index')
            ->with('success', 'Category created.');
    }

    public function edit(ShopCategory $category)
    {
        $parents = ShopCategory::whereNull('parent_id')
            ->where('id', '!=', $category->id)
            ->orderBy('name')->get();
        return view('admin.shop.categories.edit', compact('category', 'parents'));
    }

    public function update(Request $request, ShopCategory $category)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'parent_id'   => 'nullable|exists:shop_categories,id',
            'sort_order'  => 'integer|min:0',
            'is_active'   => 'boolean',
            'image'       => 'nullable|image|max:1024',
        ]);

        $validated['slug']      = Str::slug($validated['name']);
        $validated['is_active'] = $request->boolean('is_active', true);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('shop/categories', 'public');
        }

        $category->update($validated);

        return redirect()->route('admin.shop.categories.index')
            ->with('success', 'Category updated.');
    }

    public function destroy(ShopCategory $category)
    {
        $category->delete();
        return redirect()->route('admin.shop.categories.index')
            ->with('success', 'Category deleted.');
    }
}
