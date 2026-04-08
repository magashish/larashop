<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\ShopProduct;
use App\Models\ShopCategory;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $query = ShopProduct::with(['category', 'images'])
            ->where('is_active', true);

        if ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('short_description', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        $sort = $request->get('sort', 'latest');
        match ($sort) {
            'price_asc'  => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            'name'       => $query->orderBy('name', 'asc'),
            'featured'   => $query->orderBy('is_featured', 'desc')->orderBy('created_at', 'desc'),
            default      => $query->latest(),
        };

        $products   = $query->paginate(12)->withQueryString();
        $categories = ShopCategory::where('is_active', true)
            ->withCount(['products' => fn($q) => $q->where('is_active', true)])
            ->orderBy('sort_order')->get();

        $featuredProducts = ShopProduct::with('images')
            ->where('is_active', true)
            ->where('is_featured', true)
            ->latest()->take(4)->get();

        return view('consumer.merchandise.index', compact('products', 'categories', 'featuredProducts'));
    }

    public function show(ShopProduct $product)
    {
        if (!$product->is_active) {
            abort(404);
        }

        $product->load(['category', 'images']);

        $related = ShopProduct::with('images')
            ->where('is_active', true)
            ->where('id', '!=', $product->id)
            ->where('shop_category_id', $product->shop_category_id)
            ->take(4)->get();

        return view('consumer.merchandise.show', compact('product', 'related'));
    }
}
