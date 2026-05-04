@extends('layouts.xhale_mail')

@section('title', 'New Order – #' . $order->order_number)
@section('header_title', 'New Shop Order')

@section('content')
<h1 style="color:#ffffff;font-size:24px;line-height:32px;margin:0 0 16px;">
    New Order Received – #{{ $order->order_number }}
</h1>

<p style="color:#bbbbbb;font-size:16px;line-height:24px;margin:0 0 20px;">
    A new order has been placed. Details below.
</p>

<div style="background-color:#333333;padding:20px;border-radius:8px;margin-bottom:20px;">
    <h2 style="color:#10B981;font-size:16px;margin:0 0 12px;">Customer</h2>
    <p style="color:#ffffff;font-size:14px;line-height:22px;margin:0;">
        {{ $order->billing_first_name }} {{ $order->billing_last_name }}<br>
        {{ $order->billing_email }}<br>
        @if($order->billing_phone){{ $order->billing_phone }}<br>@endif
    </p>
</div>

<div style="background-color:#333333;padding:20px;border-radius:8px;margin-bottom:20px;">
    <h2 style="color:#10B981;font-size:16px;margin:0 0 12px;">Order Items</h2>
    <table style="width:100%;border-collapse:collapse;">
        <thead>
            <tr>
                <th style="color:#10B981;font-size:13px;text-align:left;padding:6px 0;border-bottom:1px solid #444;">Product</th>
                <th style="color:#10B981;font-size:13px;text-align:center;padding:6px 0;border-bottom:1px solid #444;">Qty</th>
                <th style="color:#10B981;font-size:13px;text-align:right;padding:6px 0;border-bottom:1px solid #444;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
            <tr>
                <td style="color:#ffffff;font-size:14px;padding:8px 0;border-bottom:1px solid #3a3a3a;">
                    {{ $item->product_name }}
                    @if($item->product_sku)<br><small style="color:#999;">SKU: {{ $item->product_sku }}</small>@endif
                </td>
                <td style="color:#ffffff;font-size:14px;text-align:center;padding:8px 0;border-bottom:1px solid #3a3a3a;">{{ $item->quantity }}</td>
                <td style="color:#ffffff;font-size:14px;text-align:right;padding:8px 0;border-bottom:1px solid #3a3a3a;">${{ number_format($item->subtotal, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <table style="width:100%;margin-top:12px;">
        @if($order->discount_amount > 0)
        <tr>
            <td style="color:#bbbbbb;font-size:13px;padding:3px 0;">Discount</td>
            <td style="color:#10B981;font-size:13px;text-align:right;">-${{ number_format($order->discount_amount, 2) }}</td>
        </tr>
        @endif
        @if($order->shipping_cost > 0)
        <tr>
            <td style="color:#bbbbbb;font-size:13px;padding:3px 0;">Shipping</td>
            <td style="color:#ffffff;font-size:13px;text-align:right;">${{ number_format($order->shipping_cost, 2) }}</td>
        </tr>
        @endif
        @if($order->tax_amount > 0)
        <tr>
            <td style="color:#bbbbbb;font-size:13px;padding:3px 0;">Tax</td>
            <td style="color:#ffffff;font-size:13px;text-align:right;">${{ number_format($order->tax_amount, 2) }}</td>
        </tr>
        @endif
        <tr>
            <td style="color:#ffffff;font-size:16px;font-weight:bold;padding:10px 0 0;">Order Total</td>
            <td style="color:#10B981;font-size:16px;font-weight:bold;text-align:right;padding:10px 0 0;">${{ number_format($order->total, 2) }}</td>
        </tr>
    </table>
</div>

<div style="background-color:#333333;padding:20px;border-radius:8px;margin-bottom:20px;">
    <h2 style="color:#10B981;font-size:16px;margin:0 0 12px;">Shipping Address</h2>
    <p style="color:#bbbbbb;font-size:14px;line-height:22px;margin:0;">
        {{ $order->shipping_first_name }} {{ $order->shipping_last_name }}<br>
        {{ $order->shipping_address }}<br>
        {{ $order->shipping_city }}, {{ $order->shipping_state }} {{ $order->shipping_postcode }}<br>
        {{ $order->shipping_country }}
    </p>
</div>

@if($order->notes)
<div style="background-color:#333333;padding:16px;border-radius:8px;margin-bottom:20px;">
    <h2 style="color:#10B981;font-size:16px;margin:0 0 8px;">Customer Notes</h2>
    <p style="color:#bbbbbb;font-size:14px;line-height:22px;margin:0;">{{ $order->notes }}</p>
</div>
@endif

<div style="text-align:center;margin-top:24px;">
    <a href="{{ route('admin.shop.orders.show', $order) }}"
       style="background-color:#10B981;color:#ffffff;text-decoration:none;padding:12px 28px;border-radius:6px;font-size:15px;font-weight:bold;display:inline-block;">
        View Order in Admin
    </a>
</div>
@endsection
