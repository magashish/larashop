<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\ShopCartItem;
use App\Models\ShopOrder;
use App\Models\ShopProduct;
use App\Models\ShopProductVariant;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function index()
    {
        $orders = ShopOrder::where('user_id', auth()->id())
            ->with('items')
            ->latest()
            ->paginate(10);

        $bodyClass = 'ecommerce-page account-orders-page';

        return view('consumer.account.orders', compact('orders', 'bodyClass'));
    }

    public function show(ShopOrder $order)
    {
        abort_if($order->user_id !== auth()->id(), 403);

        $order->load('items.product', 'shippingRate.zone');

        $bodyClass = 'ecommerce-page account-order-page';

        return view('consumer.account.order-show', compact('order', 'bodyClass'));
    }

    public function reorder(ShopOrder $order)
    {
        abort_if($order->user_id !== auth()->id(), 403);

        $order->load('items.product.variants');

        $sessionId = session()->getId();
        $added     = 0;
        $skipped   = [];

        foreach ($order->items as $item) {
            $product = $item->product;

            if (!$product || !$product->is_active) {
                $skipped[] = $item->product_name;
                continue;
            }

            $variantId    = $item->meta['variant_id'] ?? null;
            $variantColor = $item->meta['variant_color'] ?? null;
            $variantSize  = $item->meta['variant_size'] ?? null;
            $variant      = null;

            if ($variantId) {
                $variant = ShopProductVariant::where('id', $variantId)
                    ->where('shop_product_id', $product->id)
                    ->where('is_active', true)
                    ->first();

                if (!$variant) {
                    // Try to find a matching active variant by colour + size
                    $variant = $product->variants()
                        ->where('color_name', $variantColor)
                        ->where('size', $variantSize)
                        ->where('is_active', true)
                        ->first();
                }

                if (!$variant) {
                    $skipped[] = $item->product_name;
                    continue;
                }

                if ($variant->stock_quantity < 1) {
                    $skipped[] = $item->product_name . ' (' . implode('/', array_filter([$variantColor, $variantSize])) . ')';
                    continue;
                }
            } else {
                // Simple product
                if ($product->track_stock && $product->stock_quantity < 1 && !$product->allow_backorders) {
                    $skipped[] = $item->product_name;
                    continue;
                }
            }

            $unitPrice = $variant ? $variant->final_price : $product->current_price;

            $existing = ShopCartItem::where('session_id', $sessionId)
                ->where('shop_product_id', $product->id)
                ->where('shop_product_variant_id', $variant?->id)
                ->first();

            if ($existing) {
                $existing->increment('quantity', $item->quantity);
            } else {
                ShopCartItem::create([
                    'session_id'              => $sessionId,
                    'user_id'                 => auth()->id(),
                    'shop_product_id'         => $product->id,
                    'shop_product_variant_id' => $variant?->id,
                    'variant_color'           => $variant?->color_name,
                    'variant_size'            => $variant?->size,
                    'quantity'                => $item->quantity,
                    'unit_price'              => $unitPrice,
                ]);
            }

            $added++;
        }

        if ($added === 0) {
            return redirect()->route('shop.cart.index')
                ->with('error', 'None of the items from this order could be added to your cart — they may no longer be available.');
        }

        $message = $added . ' ' . str_plural('item', $added) . ' added to your cart.';
        if ($skipped) {
            $message .= ' Unavailable: ' . implode(', ', $skipped) . '.';
        }

        return redirect()->route('shop.cart.index')->with('success', $message);
    }
}
