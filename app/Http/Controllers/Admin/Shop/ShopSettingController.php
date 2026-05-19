<?php

namespace App\Http\Controllers\Admin\Shop;

use App\Http\Controllers\Controller;
use App\Models\ShopSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ShopSettingController extends Controller
{
    public function edit()
    {
        $settings = ShopSetting::pluck('value', 'key');
        return view('admin.shop.settings.edit', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'shipping_returns' => 'nullable|string|max:5000',
        ]);

        ShopSetting::set('shipping_returns', $request->input('shipping_returns'));
        Cache::forget('shop_setting_shipping_returns');

        return redirect()->route('admin.shop.settings.edit')
            ->with('success', 'Shop settings saved.');
    }
}
