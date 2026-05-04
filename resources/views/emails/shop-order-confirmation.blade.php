@extends('layouts.xhale_mail')

@section('title', 'Order Confirmed – #' . $order->order_number)
@section('header_title', 'Order Confirmation')

@section('content')
<h1 style="color:#ffffff;font-size:26px;line-height:34px;margin:0 0 16px;">
    Thanks for your order, {{ $order->billing_first_name }}!
</h1>

<p style="color:#bbbbbb;font-size:16px;line-height:24px;margin:0 0 20px;">
    We've received your order and are processing it now. You'll receive another email when it ships.
</p>

<div style="background-color:#333333;padding:20px;border-radius:8px;margin-bottom:20px;">
    <h2 style="color:#ffffff;font-size:18px;line-height:26px;margin:0 0 14px;border-bottom:1px solid #444444;padding-bottom:10px;">
        Order #{{ $order->order_number }}
    </h2>

    <table style="width:100%;border-collapse:collapse;">
        <thead>
            <tr>
                <th style="color:#10B981;font-size:13px;text-align:left;padding:6px 0;border-bottom:1px solid #444;">Product</th>
                <th style="color:#10B981;font-size:13px;text-align:center;padding:6px 0;border-bottom:1px solid #444;">Qty</th>
                <th style="color:#10B981;font-size:13px;text-align:right;padding:6px 0;border-bottom:1px solid #444;">Price</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
            <tr>
                <td style="color:#ffffff;font-size:15px;padding:8px 0;border-bottom:1px solid #3a3a3a;">
                    {{ $item->product_name }}
                    @if($item->variant_color || $item->variant_size)
                    <br><small style="color:#999;">{{ implode(' / ', array_filter([$item->variant_color, $item->variant_size])) }}</small>
                    @endif
                </td>
                <td style="color:#ffffff;font-size:15px;text-align:center;padding:8px 0;border-bottom:1px solid #3a3a3a;">{{ $item->quantity }}</td>
                <td style="color:#ffffff;font-size:15px;text-align:right;padding:8px 0;border-bottom:1px solid #3a3a3a;">${{ number_format($item->subtotal, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <table style="width:100%;margin-top:12px;">
        <tr>
            <td style="color:#bbbbbb;font-size:14px;padding:4px 0;">Subtotal</td>
            <td style="color:#ffffff;font-size:14px;text-align:right;">${{ number_format($order->subtotal, 2) }}</td>
        </tr>
        @if($order->discount_amount > 0)
        <tr>
            <td style="color:#bbbbbb;font-size:14px;padding:4px 0;">Discount</td>
            <td style="color:#10B981;font-size:14px;text-align:right;">-${{ number_format($order->discount_amount, 2) }}</td>
        </tr>
        @endif
        @if($order->shipping_cost > 0)
        <tr>
            <td style="color:#bbbbbb;font-size:14px;padding:4px 0;">Shipping</td>
            <td style="color:#ffffff;font-size:14px;text-align:right;">${{ number_format($order->shipping_cost, 2) }}</td>
        </tr>
        @endif
        @if($order->tax_amount > 0)
        <tr>
            <td style="color:#bbbbbb;font-size:14px;padding:4px 0;">Tax (GST)</td>
            <td style="color:#ffffff;font-size:14px;text-align:right;">${{ number_format($order->tax_amount, 2) }}</td>
        </tr>
        @endif
        <tr>
            <td style="color:#ffffff;font-size:16px;font-weight:bold;padding:10px 0 0;">Total</td>
            <td style="color:#10B981;font-size:16px;font-weight:bold;text-align:right;padding:10px 0 0;">${{ number_format($order->total, 2) }}</td>
        </tr>
    </table>
</div>

<div style="background-color:#333333;padding:20px;border-radius:8px;margin-bottom:20px;">
    <h2 style="color:#ffffff;font-size:16px;margin:0 0 10px;">Shipping To</h2>
    <p style="color:#bbbbbb;font-size:14px;line-height:22px;margin:0;">
        {{ $order->shipping_first_name }} {{ $order->shipping_last_name }}<br>
        {{ $order->shipping_address }}<br>
        {{ $order->shipping_city }}, {{ $order->shipping_state }} {{ $order->shipping_postcode }}<br>
        {{ $order->shipping_country }}
    </p>
</div>

<p style="color:#bbbbbb;font-size:14px;line-height:22px;margin:0;">
    If you have any questions about your order, please contact our support team and reference order #{{ $order->order_number }}.
</p>
@endsection
