<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\ShopProduct;
use App\Models\ShopProductVariant;
use App\Models\ShopCartItem;
use App\Models\ShopCoupon;
use Illuminate\Http\Request;

class CartController extends Controller
{
    private function getSessionId(): string
    {
        return session()->getId();
    }

    private function getCartQuery()
    {
        return ShopCartItem::where('session_id', $this->getSessionId());
    }

    public function index()
    {
        $items  = $this->getCartQuery()->with('product.images')->get();
        $coupon = session('shop_coupon');

        $subtotal       = $items->sum(fn($i) => $i->unit_price * $i->quantity);
        $discountAmount = 0;

        if ($coupon) {
            $couponModel = ShopCoupon::where('code', $coupon)->first();
            if ($couponModel && $couponModel->isValid($subtotal)) {
                $discountAmount = $couponModel->calculateDiscount($subtotal);
            } else {
                session()->forget('shop_coupon');
                $coupon = null;
            }
        }

        $bodyClass = 'ecommerce-page cart-page';

        return view('consumer.cart.index', compact('items', 'subtotal', 'discountAmount', 'coupon', 'bodyClass'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:shop_products,id',
            'variant_id' => 'nullable|exists:shop_product_variants,id',
            'quantity'   => 'required|integer|min:1|max:99',
        ]);

        $product = ShopProduct::with('variants')->findOrFail($request->product_id);

        if (!$product->is_active) {
            return back()->with('error', 'This product is not available.');
        }

        $variant     = null;
        $unitPrice   = $product->current_price;
        $variantColor = null;
        $variantSize  = null;

        if ($request->filled('variant_id')) {
            $variant = ShopProductVariant::where('id', $request->variant_id)
                ->where('shop_product_id', $product->id)->first();

            if (!$variant || !$variant->is_active) {
                return back()->with('error', 'Selected variant is not available.');
            }
            if ($variant->stock_quantity < $request->quantity) {
                return back()->with('error', 'Insufficient stock for the selected size.');
            }
            $unitPrice    = $variant->final_price;
            $variantColor = $variant->color_name;
            $variantSize  = $variant->size;
        } elseif ($product->variants->isNotEmpty()) {
            return back()->with('error', 'Please select a colour and size.');
        } else {
            if ($product->track_stock && $product->stock_quantity < $request->quantity && !$product->allow_backorders) {
                return back()->with('error', 'Insufficient stock.');
            }
        }

        $sessionId = $this->getSessionId();

        // Look for existing cart row with same product + variant
        $existing = ShopCartItem::where('session_id', $sessionId)
            ->where('shop_product_id', $product->id)
            ->where('shop_product_variant_id', $variant?->id)
            ->first();

        if ($existing) {
            $newQty = $existing->quantity + $request->quantity;
            if ($variant && $variant->stock_quantity < $newQty) {
                $newQty = $variant->stock_quantity;
            }
            $existing->update(['quantity' => $newQty]);
        } else {
            ShopCartItem::create([
                'session_id'               => $sessionId,
                'user_id'                  => auth()->id(),
                'shop_product_id'          => $product->id,
                'shop_product_variant_id'  => $variant?->id,
                'variant_color'            => $variantColor,
                'variant_size'             => $variantSize,
                'quantity'                 => $request->quantity,
                'unit_price'               => $unitPrice,
            ]);
        }

        if ($request->ajax() || $request->wantsJson()) {
            $count = $this->getCartQuery()->sum('quantity');
            return response()->json(['success' => true, 'cart_count' => $count]);
        }

        return back()->with('success', 'Item added to cart.');
    }

    public function update(Request $request, ShopCartItem $item)
    {
        $request->validate(['quantity' => 'required|integer|min:0|max:99']);

        if ($request->quantity == 0) {
            $item->delete();
        } else {
            $item->update(['quantity' => $request->quantity]);
        }

        return back()->with('success', 'Cart updated.');
    }

    public function remove(ShopCartItem $item)
    {
        $item->delete();
        return back()->with('success', 'Item removed from cart.');
    }

    public function applyCoupon(Request $request)
    {
        $request->validate(['coupon_code' => 'required|string']);

        $code   = strtoupper(trim($request->coupon_code));
        $coupon = ShopCoupon::where('code', $code)->first();
        $items  = $this->getCartQuery()->with('product')->get();
        $subtotal = $items->sum(fn($i) => $i->unit_price * $i->quantity);

        if (!$coupon || !$coupon->isValid($subtotal)) {
            return back()->with('error', 'Invalid or expired coupon code.');
        }

        session(['shop_coupon' => $code]);
        return back()->with('success', 'Coupon applied successfully.');
    }

    public function removeCoupon()
    {
        session()->forget('shop_coupon');
        return back()->with('success', 'Coupon removed.');
    }

    public function count()
    {
        $count = $this->getCartQuery()->sum('quantity');
        return response()->json(['count' => $count]);
    }
}
