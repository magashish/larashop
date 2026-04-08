<?php

namespace App\Http\Controllers\Admin\Shop;

use App\Http\Controllers\Controller;
use App\Models\ShopOrder;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = ShopOrder::with('items');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('order_number', 'like', '%' . $request->search . '%')
                  ->orWhere('billing_email', 'like', '%' . $request->search . '%')
                  ->orWhere('billing_first_name', 'like', '%' . $request->search . '%')
                  ->orWhere('billing_last_name', 'like', '%' . $request->search . '%');
            });
        }

        $orders = $query->latest()->paginate(20)->withQueryString();

        return view('admin.shop.orders.index', compact('orders'));
    }

    public function show(ShopOrder $order)
    {
        $order->load('items.product', 'coupon', 'shippingRate.zone');
        return view('admin.shop.orders.show', compact('order'));
    }

    public function update(Request $request, ShopOrder $order)
    {
        $validated = $request->validate([
            'status'          => 'required|in:pending,processing,shipped,delivered,cancelled,refunded',
            'payment_status'  => 'required|in:unpaid,paid,refunded,failed',
            'tracking_number' => 'nullable|string|max:255',
            'notes'           => 'nullable|string',
        ]);

        if ($validated['status'] === 'shipped' && !$order->shipped_at) {
            $validated['shipped_at'] = now();
        }

        $order->update($validated);

        return back()->with('success', 'Order updated successfully.');
    }
}
