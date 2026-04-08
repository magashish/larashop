<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\ShopProduct;
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

        return view('consumer.cart.index', compact('items', 'subtotal', 'discountAmount', 'coupon'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:shop_products,id',
            'quantity'   => 'required|integer|min:1|max:99',
        ]);

        $product = ShopProduct::findOrFail($request->product_id);

        if (!$product->is_active) {
            return back()->with('error', 'This product is not available.');
        }
        if ($product->track_stock && $product->stock_quantity < $request->quantity && !$product->allow_backorders) {
            return back()->with('error', 'Insufficient stock.');
        }

        $sessionId = $this->getSessionId();
        $existing  = ShopCartItem::where('session_id', $sessionId)
            ->where('shop_product_id', $product->id)->first();

        if ($existing) {
            $newQty = $existing->quantity + $request->quantity;
            if ($product->track_stock && $product->stock_quantity < $newQty && !$product->allow_backorders) {
                $newQty = $product->stock_quantity;
            }
            $existing->update(['quantity' => $newQty]);
        } else {
            ShopCartItem::create([
                'session_id'      => $sessionId,
                'user_id'         => auth()->id(),
                'shop_product_id' => $product->id,
                'quantity'        => $request->quantity,
                'unit_price'      => $product->current_price,
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
