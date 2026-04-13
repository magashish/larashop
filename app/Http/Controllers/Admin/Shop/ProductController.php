<?php

namespace App\Http\Controllers\Admin\Shop;

use App\Http\Controllers\Controller;
use App\Models\ShopProduct;
use App\Models\ShopCategory;
use App\Models\ShopProductImage;
use App\Models\ShopProductVariant;
use App\Models\ShopProductColorImage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = ShopProduct::with('category');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('sku', 'like', '%' . $request->search . '%');
            });
        }
        if ($request->filled('category')) {
            $query->where('shop_category_id', $request->category);
        }
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $products   = $query->latest()->paginate(20)->withQueryString();
        $categories = ShopCategory::where('is_active', true)->orderBy('name')->get();

        return view('admin.shop.products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = ShopCategory::where('is_active', true)->orderBy('name')->get();
        return view('admin.shop.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'               => 'required|string|max:255',
            'shop_category_id'   => 'nullable|exists:shop_categories,id',
            'short_description'  => 'nullable|string|max:500',
            'description'        => 'nullable|string',
            'price'              => 'required|numeric|min:0',
            'sale_price'         => 'nullable|numeric|min:0',
            'cost_price'         => 'nullable|numeric|min:0',
            'sku'                => 'nullable|string|max:100|unique:shop_products,sku',
            'stock_quantity'     => 'required|integer|min:0',
            'track_stock'        => 'boolean',
            'allow_backorders'   => 'boolean',
            'weight'             => 'nullable|numeric|min:0',
            'featured_image'     => 'nullable|image|max:2048',
            'images.*'           => 'nullable|image|max:2048',
            'is_active'          => 'boolean',
            'is_featured'        => 'boolean',
        ]);

        $validated['slug']             = Str::slug($validated['name']) . '-' . Str::random(5);
        $validated['track_stock']      = $request->boolean('track_stock');
        $validated['allow_backorders'] = $request->boolean('allow_backorders');
        $validated['is_active']        = $request->boolean('is_active', true);
        $validated['is_featured']      = $request->boolean('is_featured');

        if ($request->hasFile('featured_image')) {
            $validated['featured_image'] = $request->file('featured_image')
                ->store('shop/products', 'public');
        }

        $product = ShopProduct::create($validated);

        $this->saveGalleryImages($request, $product);
        $this->saveVariants($request, $product);
        $this->saveColorImages($request, $product);

        return redirect()->route('admin.shop.products.index')
            ->with('success', 'Product created successfully.');
    }

    public function edit(ShopProduct $product)
    {
        $categories = ShopCategory::where('is_active', true)->orderBy('name')->get();
        $product->load('images', 'variants', 'colorImages');
        return view('admin.shop.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, ShopProduct $product)
    {
        $validated = $request->validate([
            'name'              => 'required|string|max:255',
            'shop_category_id'  => 'nullable|exists:shop_categories,id',
            'short_description' => 'nullable|string|max:500',
            'description'       => 'nullable|string',
            'price'             => 'required|numeric|min:0',
            'sale_price'        => 'nullable|numeric|min:0',
            'cost_price'        => 'nullable|numeric|min:0',
            'sku'               => 'nullable|string|max:100|unique:shop_products,sku,' . $product->id,
            'stock_quantity'    => 'required|integer|min:0',
            'track_stock'       => 'boolean',
            'allow_backorders'  => 'boolean',
            'weight'            => 'nullable|numeric|min:0',
            'featured_image'    => 'nullable|image|max:2048',
            'images.*'          => 'nullable|image|max:2048',
            'is_active'         => 'boolean',
            'is_featured'       => 'boolean',
        ]);

        $validated['track_stock']      = $request->boolean('track_stock');
        $validated['allow_backorders'] = $request->boolean('allow_backorders');
        $validated['is_active']        = $request->boolean('is_active', true);
        $validated['is_featured']      = $request->boolean('is_featured');

        if ($request->hasFile('featured_image')) {
            if ($product->featured_image) {
                Storage::disk('public')->delete($product->featured_image);
            }
            $validated['featured_image'] = $request->file('featured_image')
                ->store('shop/products', 'public');
        } else {
            // Don't overwrite the existing image with null
            unset($validated['featured_image']);
        }

        $product->update($validated);

        $this->saveGalleryImages($request, $product);
        $this->saveVariants($request, $product);
        $this->saveColorImages($request, $product);

        return redirect()->route('admin.shop.products.edit', $product)
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(ShopProduct $product)
    {
        $product->delete();
        return redirect()->route('admin.shop.products.index')
            ->with('success', 'Product deleted successfully.');
    }

    public function deleteImage(ShopProductImage $image)
    {
        Storage::disk('public')->delete($image->image_path);
        $image->delete();
        return back()->with('success', 'Image removed.');
    }

    public function deleteColorImage(ShopProductColorImage $image)
    {
        Storage::disk('public')->delete($image->image_path);
        $image->delete();
        return back()->with('success', 'Colour image removed.');
    }

    // ── Private helpers ────────────────────────────────────────────────────

    private function saveGalleryImages(Request $request, ShopProduct $product): void
    {
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $path = $image->store('shop/products/gallery', 'public');
                ShopProductImage::create([
                    'shop_product_id' => $product->id,
                    'image_path'      => $path,
                    'sort_order'      => $product->images()->count() + $index,
                ]);
            }
        }
    }

    private function saveVariants(Request $request, ShopProduct $product): void
    {
        if (!$request->has('variants')) {
            return;
        }

        $submitted = collect($request->input('variants', []));
        $submittedIds = $submitted->pluck('id')->filter()->values()->toArray();

        // Delete variants that were removed in the UI
        $product->variants()->whereNotIn('id', $submittedIds)->delete();

        foreach ($submitted as $index => $data) {
            $attrs = [
                'shop_product_id'  => $product->id,
                'color_name'       => $data['color_name'],
                'color_hex'        => $data['color_hex'] ?? null,
                'size'             => $data['size'],
                'size_order'       => (int) ($data['size_order'] ?? 0),
                'sku'              => $data['sku'] ?: null,
                'stock_quantity'   => (int) ($data['stock_quantity'] ?? 0),
                'price_adjustment' => (float) ($data['price_adjustment'] ?? 0),
                'is_active'        => isset($data['is_active']),
            ];

            // Handle per-variant image upload
            $fileKey = "variant_images.{$index}";
            if ($request->hasFile($fileKey)) {
                $attrs['featured_image'] = $request->file($fileKey)
                    ->store('shop/products/variants', 'public');
            }

            if (!empty($data['id'])) {
                $variant = ShopProductVariant::find($data['id']);
                if ($variant) {
                    // Delete old image if a new one is being uploaded
                    if (isset($attrs['featured_image']) && $variant->featured_image) {
                        Storage::disk('public')->delete($variant->featured_image);
                    }
                    $variant->update($attrs);
                }
            } else {
                ShopProductVariant::create($attrs);
            }
        }
    }

    private function saveColorImages(Request $request, ShopProduct $product): void
    {
        if (!$request->has('color_image_groups') || !$request->hasFile('color_image_groups')) {
            return;
        }

        foreach ($request->file('color_image_groups') as $index => $group) {
            if (empty($group['images'])) {
                continue;
            }
            $colorName = $request->input("color_image_groups.{$index}.color_name", '');
            if (!$colorName) {
                continue;
            }
            $existingCount = $product->colorImages()->where('color_name', $colorName)->count();
            foreach ($group['images'] as $i => $image) {
                $path = $image->store('shop/products/colors', 'public');
                ShopProductColorImage::create([
                    'shop_product_id' => $product->id,
                    'color_name'      => $colorName,
                    'image_path'      => $path,
                    'sort_order'      => $existingCount + $i,
                ]);
            }
        }
    }
}
