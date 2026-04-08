<?php

namespace App\Http\Controllers\Admin\Shop;

use App\Http\Controllers\Controller;
use App\Models\ShopCoupon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CouponController extends Controller
{
    public function index()
    {
        $coupons = ShopCoupon::latest()->paginate(20);
        return view('admin.shop.coupons.index', compact('coupons'));
    }

    public function create()
    {
        return view('admin.shop.coupons.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code'             => 'required|string|max:50|unique:shop_coupons,code',
            'type'             => 'required|in:fixed,percentage',
            'value'            => 'required|numeric|min:0',
            'min_order_amount' => 'nullable|numeric|min:0',
            'usage_limit'      => 'nullable|integer|min:1',
            'per_user_limit'   => 'nullable|integer|min:1',
            'starts_at'        => 'nullable|date',
            'expires_at'       => 'nullable|date|after_or_equal:starts_at',
            'is_active'        => 'boolean',
        ]);

        $validated['code']      = strtoupper($validated['code']);
        $validated['is_active'] = $request->boolean('is_active', true);

        ShopCoupon::create($validated);

        return redirect()->route('admin.shop.coupons.index')
            ->with('success', 'Coupon created.');
    }

    public function edit(ShopCoupon $coupon)
    {
        return view('admin.shop.coupons.edit', compact('coupon'));
    }

    public function update(Request $request, ShopCoupon $coupon)
    {
        $validated = $request->validate([
            'code'             => 'required|string|max:50|unique:shop_coupons,code,' . $coupon->id,
            'type'             => 'required|in:fixed,percentage',
            'value'            => 'required|numeric|min:0',
            'min_order_amount' => 'nullable|numeric|min:0',
            'usage_limit'      => 'nullable|integer|min:1',
            'per_user_limit'   => 'nullable|integer|min:1',
            'starts_at'        => 'nullable|date',
            'expires_at'       => 'nullable|date|after_or_equal:starts_at',
            'is_active'        => 'boolean',
        ]);

        $validated['code']      = strtoupper($validated['code']);
        $validated['is_active'] = $request->boolean('is_active', true);

        $coupon->update($validated);

        return redirect()->route('admin.shop.coupons.index')
            ->with('success', 'Coupon updated.');
    }

    public function destroy(ShopCoupon $coupon)
    {
        $coupon->delete();
        return redirect()->route('admin.shop.coupons.index')
            ->with('success', 'Coupon deleted.');
    }

    public function generate()
    {
        return response()->json(['code' => strtoupper(Str::random(8))]);
    }
}
