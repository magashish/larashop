<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\ShopProduct;
use App\Models\ShopCategory;
use App\Models\ShopProductVariant;
use App\Models\ShopSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $comingSoon = Cache::remember('shop_setting_coming_soon', 600, fn() =>
            ShopSetting::get('coming_soon', '0')
        );

        if ($comingSoon === '1') {
            return view('consumer.merchandise.coming-soon', ['bodyClass' => 'ecommerce-page shop-coming-soon-page']);
        }

        // Eager-load variants to prevent N+1 in isInStock() called from product cards
        $query = ShopProduct::with(['category', 'images', 'variants'])
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

        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        if ($request->filled('colour')) {
            $query->whereHas('variants', fn($q) => $q->where('color_name', $request->colour)->where('is_active', true));
        }

        if ($request->filled('size')) {
            $query->whereHas('variants', fn($q) => $q->where('size', $request->size)->where('is_active', true));
        }

        $sort = $request->get('sort', 'latest');
        match ($sort) {
            'price_asc'  => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            'name'       => $query->orderBy('name', 'asc'),
            'featured'   => $query->orderBy('is_featured', 'desc')->orderBy('created_at', 'desc'),
            default      => $query->latest(),
        };

        $products = $query->paginate(12)->withQueryString();

        // Cache categories for 10 minutes — they change rarely
        $categories = Cache::remember('shop_categories_nav', 600, fn() =>
            ShopCategory::where('is_active', true)
                ->withCount(['products' => fn($q) => $q->where('is_active', true)])
                ->orderBy('sort_order')->get()
        );

        // Cache featured products for 10 minutes
        $featuredProducts = Cache::remember('shop_featured_products', 600, fn() =>
            ShopProduct::with('images')
                ->where('is_active', true)
                ->where('is_featured', true)
                ->latest()->take(4)->get()
        );

        // Available colours and sizes for sidebar filters
        $availableColours = Cache::remember('shop_filter_colours', 300, fn() =>
            ShopProductVariant::join('shop_products', 'shop_product_variants.shop_product_id', '=', 'shop_products.id')
                ->where('shop_products.is_active', true)
                ->where('shop_product_variants.is_active', true)
                ->whereNotNull('shop_product_variants.color_name')
                ->where('shop_product_variants.color_name', '!=', '')
                ->select('shop_product_variants.color_name', 'shop_product_variants.color_hex')
                ->distinct()
                ->orderBy('shop_product_variants.color_name')
                ->get()
        );

        $availableSizes = Cache::remember('shop_filter_sizes', 300, fn() =>
            ShopProductVariant::join('shop_products', 'shop_product_variants.shop_product_id', '=', 'shop_products.id')
                ->where('shop_products.is_active', true)
                ->where('shop_product_variants.is_active', true)
                ->whereNotNull('shop_product_variants.size')
                ->where('shop_product_variants.size', '!=', '')
                ->select('shop_product_variants.size', 'shop_product_variants.size_order')
                ->distinct()
                ->orderBy('shop_product_variants.size_order')
                ->pluck('size')
        );

        $bodyClass = 'ecommerce-page shop-index-page';

        return view('consumer.merchandise.index', compact(
            'products', 'categories', 'featuredProducts',
            'availableColours', 'availableSizes', 'bodyClass'
        ));
    }

    public function show(ShopProduct $product)
    {
        if (!$product->is_active) {
            abort(404);
        }

        $product->load(['category', 'images', 'variants', 'colorImages']);

        $related = ShopProduct::with(['images', 'variants'])
            ->where('is_active', true)
            ->where('id', '!=', $product->id)
            ->where('shop_category_id', $product->shop_category_id)
            ->take(4)->get();

        $shippingReturns = Cache::remember('shop_setting_shipping_returns', 600, fn() =>
            ShopSetting::get('shipping_returns', '')
        );

        $promoStrip = Cache::remember('shop_setting_promo_strip', 600, fn() => [
            'enabled'     => ShopSetting::get('promo_strip_enabled', '1') === '1',
            'heading'     => ShopSetting::get('promo_strip_heading', 'Members get more'),
            'body'        => ShopSetting::get('promo_strip_body', ''),
            'button_text' => ShopSetting::get('promo_strip_button_text', 'Login'),
            'button_url'  => ShopSetting::get('promo_strip_button_url', '/login'),
            'image'       => ShopSetting::get('promo_strip_image', ''),
        ]);

        $bodyClass = 'ecommerce-page product-page';

        return view('consumer.merchandise.show', compact('product', 'related', 'shippingReturns', 'promoStrip', 'bodyClass'));
    }
}
