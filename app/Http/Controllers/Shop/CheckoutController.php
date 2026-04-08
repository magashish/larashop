<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\ShopCartItem;
use App\Models\ShopCoupon;
use App\Models\ShopOrder;
use App\Models\ShopOrderItem;
use App\Models\ShopShippingRate;
use App\Models\ShopShippingZone;
use App\Models\ShopTaxRate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class CheckoutController extends Controller
{
    private function getCartItems()
    {
        return ShopCartItem::where('session_id', session()->getId())
            ->with('product')->get();
    }

    public function index()
    {
        $items = $this->getCartItems();
        if ($items->isEmpty()) {
            return redirect()->route('shop.merchandise.index')->with('error', 'Your cart is empty.');
        }

        $subtotal = $items->sum(fn($i) => $i->unit_price * $i->quantity);
        $coupon   = null;
        $discountAmount = 0;
        $couponCode = session('shop_coupon');

        if ($couponCode) {
            $coupon = ShopCoupon::where('code', $couponCode)->first();
            if ($coupon && $coupon->isValid($subtotal)) {
                $discountAmount = $coupon->calculateDiscount($subtotal);
            }
        }

        $shippingZones = ShopShippingZone::with('rates')->where('is_active', true)->get();
        $taxRate       = ShopTaxRate::where('is_default', true)->where('is_active', true)->first();
        $taxAmount     = $taxRate ? round(($subtotal - $discountAmount) * $taxRate->rate, 2) : 0;

        $user = auth()->user();

        return view('consumer.checkout.index', compact(
            'items', 'subtotal', 'discountAmount', 'coupon',
            'shippingZones', 'taxRate', 'taxAmount', 'user'
        ));
    }

    public function getShippingRates(Request $request)
    {
        $request->validate(['state' => 'required|string', 'country' => 'required|string']);

        $zones = ShopShippingZone::with(['rates' => fn($q) => $q->where('is_active', true)])
            ->where('is_active', true)->get();

        $applicableRates = collect();
        foreach ($zones as $zone) {
            $countries = $zone->countries ?? [];
            $states    = $zone->states ?? [];

            if (
                in_array($request->country, $countries) ||
                in_array($request->state, $states) ||
                (empty($countries) && empty($states))
            ) {
                foreach ($zone->rates as $rate) {
                    $applicableRates->push([
                        'id'   => $rate->id,
                        'name' => $rate->name,
                        'rate' => (float) $rate->rate,
                        'type' => $rate->type,
                    ]);
                }
            }
        }

        return response()->json($applicableRates);
    }

    public function createPaymentIntent(Request $request)
    {
        $request->validate([
            'shipping_rate_id' => 'nullable|exists:shop_shipping_rates,id',
        ]);

        $items    = $this->getCartItems();
        $subtotal = $items->sum(fn($i) => $i->unit_price * $i->quantity);

        $couponCode = session('shop_coupon');
        $discount   = 0;
        if ($couponCode) {
            $coupon = ShopCoupon::where('code', $couponCode)->first();
            if ($coupon && $coupon->isValid($subtotal)) {
                $discount = $coupon->calculateDiscount($subtotal);
            }
        }

        $shippingCost = 0;
        if ($request->shipping_rate_id) {
            $rate = ShopShippingRate::find($request->shipping_rate_id);
            if ($rate) {
                $shippingCost = $rate->type === 'free' ? 0 : (float) $rate->rate;
            }
        }

        $taxRate   = ShopTaxRate::where('is_default', true)->where('is_active', true)->first();
        $taxAmount = $taxRate ? round(($subtotal - $discount) * $taxRate->rate, 2) : 0;
        $total     = round($subtotal - $discount + $shippingCost + $taxAmount, 2);

        Stripe::setApiKey(config('services.stripe.secret'));

        $intent = PaymentIntent::create([
            'amount'   => (int) ($total * 100),
            'currency' => 'aud',
            'metadata' => ['session_id' => session()->getId()],
        ]);

        session(['shop_payment_intent' => $intent->id]);

        return response()->json([
            'client_secret' => $intent->client_secret,
            'total'         => $total,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'billing_first_name' => 'required|string|max:100',
            'billing_last_name'  => 'required|string|max:100',
            'billing_email'      => 'required|email|max:255',
            'billing_phone'      => 'nullable|string|max:20',
            'billing_address'    => 'required|string|max:255',
            'billing_city'       => 'required|string|max:100',
            'billing_state'      => 'required|string|max:50',
            'billing_postcode'   => 'required|string|max:20',
            'billing_country'    => 'required|string|size:2',
            'ship_to_billing'    => 'boolean',
            'shipping_first_name'=> 'required_if:ship_to_billing,0|nullable|string|max:100',
            'shipping_last_name' => 'required_if:ship_to_billing,0|nullable|string|max:100',
            'shipping_address'   => 'required_if:ship_to_billing,0|nullable|string|max:255',
            'shipping_city'      => 'required_if:ship_to_billing,0|nullable|string|max:100',
            'shipping_state'     => 'required_if:ship_to_billing,0|nullable|string|max:50',
            'shipping_postcode'  => 'required_if:ship_to_billing,0|nullable|string|max:20',
            'shipping_country'   => 'required_if:ship_to_billing,0|nullable|string|size:2',
            'shipping_rate_id'   => 'nullable|exists:shop_shipping_rates,id',
            'payment_intent_id'  => 'required|string',
            'notes'              => 'nullable|string|max:1000',
        ]);

        $items = $this->getCartItems();
        if ($items->isEmpty()) {
            return redirect()->route('shop.merchandise.index')->with('error', 'Your cart is empty.');
        }

        $subtotal = $items->sum(fn($i) => $i->unit_price * $i->quantity);

        $couponCode = session('shop_coupon');
        $discount   = 0;
        $couponId   = null;
        if ($couponCode) {
            $coupon = ShopCoupon::where('code', $couponCode)->first();
            if ($coupon && $coupon->isValid($subtotal)) {
                $discount = $coupon->calculateDiscount($subtotal);
                $couponId = $coupon->id;
            }
        }

        $shippingCost   = 0;
        $shippingRateId = null;
        if ($request->shipping_rate_id) {
            $rate = ShopShippingRate::find($request->shipping_rate_id);
            if ($rate) {
                $shippingCost   = $rate->type === 'free' ? 0 : (float) $rate->rate;
                $shippingRateId = $rate->id;
            }
        }

        $taxRate   = ShopTaxRate::where('is_default', true)->where('is_active', true)->first();
        $taxAmount = $taxRate ? round(($subtotal - $discount) * $taxRate->rate, 2) : 0;
        $total     = round($subtotal - $discount + $shippingCost + $taxAmount, 2);

        DB::beginTransaction();
        try {
            $shipToBilling = $request->boolean('ship_to_billing', true);

            $order = ShopOrder::create([
                'order_number'       => ShopOrder::generateOrderNumber(),
                'user_id'            => auth()->id(),
                'status'             => 'processing',
                'payment_status'     => 'paid',
                'payment_method'     => 'stripe',
                'payment_reference'  => $request->payment_intent_id,
                'billing_first_name' => $request->billing_first_name,
                'billing_last_name'  => $request->billing_last_name,
                'billing_email'      => $request->billing_email,
                'billing_phone'      => $request->billing_phone,
                'billing_address'    => $request->billing_address,
                'billing_city'       => $request->billing_city,
                'billing_state'      => $request->billing_state,
                'billing_postcode'   => $request->billing_postcode,
                'billing_country'    => $request->billing_country,
                'ship_to_billing'    => $shipToBilling,
                'shipping_first_name'=> $shipToBilling ? $request->billing_first_name : $request->shipping_first_name,
                'shipping_last_name' => $shipToBilling ? $request->billing_last_name  : $request->shipping_last_name,
                'shipping_address'   => $shipToBilling ? $request->billing_address    : $request->shipping_address,
                'shipping_city'      => $shipToBilling ? $request->billing_city       : $request->shipping_city,
                'shipping_state'     => $shipToBilling ? $request->billing_state      : $request->shipping_state,
                'shipping_postcode'  => $shipToBilling ? $request->billing_postcode   : $request->shipping_postcode,
                'shipping_country'   => $shipToBilling ? $request->billing_country    : $request->shipping_country,
                'subtotal'           => $subtotal,
                'shipping_cost'      => $shippingCost,
                'tax_amount'         => $taxAmount,
                'discount_amount'    => $discount,
                'total'              => $total,
                'shop_coupon_id'     => $couponId,
                'shop_shipping_rate_id' => $shippingRateId,
                'notes'              => $request->notes,
            ]);

            foreach ($items as $item) {
                ShopOrderItem::create([
                    'shop_order_id'   => $order->id,
                    'shop_product_id' => $item->shop_product_id,
                    'product_name'    => $item->product->name,
                    'product_sku'     => $item->product->sku,
                    'quantity'        => $item->quantity,
                    'unit_price'      => $item->unit_price,
                    'subtotal'        => $item->unit_price * $item->quantity,
                ]);

                // Decrement stock
                if ($item->product->track_stock) {
                    $item->product->decrement('stock_quantity', $item->quantity);
                }
            }

            // Increment coupon usage
            if ($couponId) {
                ShopCoupon::find($couponId)->increment('usage_count');
            }

            // Clear cart
            ShopCartItem::where('session_id', session()->getId())->delete();
            session()->forget(['shop_coupon', 'shop_payment_intent']);

            DB::commit();

            return redirect()->route('shop.checkout.success', $order->order_number)
                ->with('success', 'Order placed successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to place order. Please try again.');
        }
    }

    public function success(string $orderNumber)
    {
        $order = ShopOrder::where('order_number', $orderNumber)
            ->with('items.product')
            ->firstOrFail();

        return view('consumer.checkout.success', compact('order'));
    }
}
