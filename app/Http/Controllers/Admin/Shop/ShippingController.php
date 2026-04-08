<?php

namespace App\Http\Controllers\Admin\Shop;

use App\Http\Controllers\Controller;
use App\Models\ShopShippingZone;
use App\Models\ShopShippingRate;
use Illuminate\Http\Request;

class ShippingController extends Controller
{
    public function index()
    {
        $zones = ShopShippingZone::with('rates')->orderBy('sort_order')->get();
        return view('admin.shop.shipping.index', compact('zones'));
    }

    public function createZone()
    {
        return view('admin.shop.shipping.create-zone');
    }

    public function storeZone(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'countries'   => 'nullable|array',
            'countries.*' => 'string|size:2',
            'states'      => 'nullable|string',
            'sort_order'  => 'integer|min:0',
            'is_active'   => 'boolean',
        ]);

        if (isset($validated['states']) && is_string($validated['states'])) {
            $validated['states'] = array_map('trim', explode(',', $validated['states']));
        }

        $validated['is_active'] = $request->boolean('is_active', true);

        ShopShippingZone::create($validated);

        return redirect()->route('admin.shop.shipping.index')
            ->with('success', 'Shipping zone created.');
    }

    public function editZone(ShopShippingZone $zone)
    {
        return view('admin.shop.shipping.edit-zone', compact('zone'));
    }

    public function updateZone(Request $request, ShopShippingZone $zone)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'countries'   => 'nullable|array',
            'states'      => 'nullable|string',
            'sort_order'  => 'integer|min:0',
            'is_active'   => 'boolean',
        ]);

        if (isset($validated['states']) && is_string($validated['states'])) {
            $validated['states'] = array_map('trim', explode(',', $validated['states']));
        }
        $validated['is_active'] = $request->boolean('is_active', true);

        $zone->update($validated);

        return redirect()->route('admin.shop.shipping.index')
            ->with('success', 'Shipping zone updated.');
    }

    public function destroyZone(ShopShippingZone $zone)
    {
        $zone->delete();
        return redirect()->route('admin.shop.shipping.index')
            ->with('success', 'Shipping zone deleted.');
    }

    public function storeRate(Request $request, ShopShippingZone $zone)
    {
        $validated = $request->validate([
            'name'             => 'required|string|max:255',
            'type'             => 'required|in:flat,free,per_item,weight_based',
            'rate'             => 'required|numeric|min:0',
            'min_order_amount' => 'nullable|numeric|min:0',
            'max_weight'       => 'nullable|numeric|min:0',
            'is_active'        => 'boolean',
        ]);

        $validated['shop_shipping_zone_id'] = $zone->id;
        $validated['is_active'] = $request->boolean('is_active', true);

        ShopShippingRate::create($validated);

        return redirect()->route('admin.shop.shipping.index')
            ->with('success', 'Shipping rate added.');
    }

    public function destroyRate(ShopShippingRate $rate)
    {
        $rate->delete();
        return redirect()->route('admin.shop.shipping.index')
            ->with('success', 'Shipping rate deleted.');
    }
}
