<?php

namespace App\Http\Controllers\Admin\Shop;

use App\Http\Controllers\Controller;
use App\Models\ShopTaxRate;
use Illuminate\Http\Request;

class TaxController extends Controller
{
    public function index()
    {
        $taxes = ShopTaxRate::orderBy('priority')->orderBy('country')->orderBy('state')->get();
        return view('admin.shop.taxes.index', compact('taxes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'rate'       => 'required|numeric|min:0|max:100',
            'country'    => 'required|string|size:2',
            'state'      => 'nullable|string|max:10',
            'is_active'  => 'boolean',
            'is_default' => 'boolean',
            'priority'   => 'integer|min:0',
            'compound'   => 'boolean',
        ]);

        // Convert percentage to decimal (e.g., 10 => 0.1)
        $validated['rate']       = $validated['rate'] / 100;
        $validated['is_active']  = $request->boolean('is_active', true);
        $validated['is_default'] = $request->boolean('is_default');
        $validated['compound']   = $request->boolean('compound');

        if ($validated['is_default']) {
            ShopTaxRate::where('is_default', true)->update(['is_default' => false]);
        }

        ShopTaxRate::create($validated);

        return redirect()->route('admin.shop.taxes.index')
            ->with('success', 'Tax rate created.');
    }

    public function update(Request $request, ShopTaxRate $tax)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'rate'       => 'required|numeric|min:0|max:100',
            'country'    => 'required|string|size:2',
            'state'      => 'nullable|string|max:10',
            'is_active'  => 'boolean',
            'is_default' => 'boolean',
            'priority'   => 'integer|min:0',
            'compound'   => 'boolean',
        ]);

        $validated['rate']       = $validated['rate'] / 100;
        $validated['is_active']  = $request->boolean('is_active', true);
        $validated['is_default'] = $request->boolean('is_default');
        $validated['compound']   = $request->boolean('compound');

        if ($validated['is_default']) {
            ShopTaxRate::where('id', '!=', $tax->id)->update(['is_default' => false]);
        }

        $tax->update($validated);

        return redirect()->route('admin.shop.taxes.index')
            ->with('success', 'Tax rate updated.');
    }

    public function destroy(ShopTaxRate $tax)
    {
        $tax->delete();
        return redirect()->route('admin.shop.taxes.index')
            ->with('success', 'Tax rate deleted.');
    }
}
