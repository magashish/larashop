<?php

namespace App\Http\Controllers\Admin\Shop;

use App\Http\Controllers\Controller;
use App\Models\ShopSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
            'shipping_returns'       => 'nullable|string|max:5000',
            'coming_soon'            => 'boolean',
            'promo_strip_enabled'    => 'boolean',
            'promo_strip_heading'    => 'nullable|string|max:255',
            'promo_strip_body'       => 'nullable|string|max:1000',
            'promo_strip_button_text'=> 'nullable|string|max:100',
            'promo_strip_button_url' => 'nullable|string|max:500',
            'promo_strip_image'      => 'nullable|image|max:3072',
        ]);

        ShopSetting::set('shipping_returns',        $request->input('shipping_returns'));
        ShopSetting::set('coming_soon',             $request->boolean('coming_soon') ? '1' : '0');
        ShopSetting::set('promo_strip_enabled',     $request->boolean('promo_strip_enabled') ? '1' : '0');
        ShopSetting::set('promo_strip_heading',     $request->input('promo_strip_heading'));
        ShopSetting::set('promo_strip_body',        $request->input('promo_strip_body'));
        ShopSetting::set('promo_strip_button_text', $request->input('promo_strip_button_text'));
        ShopSetting::set('promo_strip_button_url',  $request->input('promo_strip_button_url'));

        if ($request->hasFile('promo_strip_image')) {
            // Delete old image if one exists
            $old = ShopSetting::get('promo_strip_image');
            if ($old) {
                Storage::disk('public')->delete($old);
            }
            $file = $request->file('promo_strip_image');
            $path = 'shop/settings/' . Str::random(30) . '.' . $file->getClientOriginalExtension();
            Storage::disk('public')->put($path, file_get_contents($file->getRealPath()));
            ShopSetting::set('promo_strip_image', $path);
        }

        Cache::forget('shop_setting_shipping_returns');
        Cache::forget('shop_setting_coming_soon');
        Cache::forget('shop_setting_promo_strip');

        return redirect()->route('admin.shop.settings.edit')
            ->with('success', 'Shop settings saved.');
    }
}
