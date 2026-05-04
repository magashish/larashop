@extends('layouts.xhale_mail')

@section('title', 'Order Update – #' . $order->order_number)
@section('header_title', 'Order Update')

@section('content')
@php
    $statusMessages = [
        'processing' => 'Your order is being processed and will be prepared for shipment soon.',
        'shipped'    => 'Great news — your order is on its way!',
        'delivered'  => 'Your order has been delivered. We hope you love it!',
        'cancelled'  => 'Your order has been cancelled. If you have questions, please contact us.',
        'refunded'   => 'Your refund has been processed. Please allow a few business days for it to appear.',
        'pending'    => 'Your order is pending. We will update you shortly.',
    ];
    $statusMessage = $statusMessages[$order->status] ?? 'Your order status has been updated.';
    $statusLabel   = ucfirst($order->status);
@endphp

<h1 style="color:#ffffff;font-size:26px;line-height:34px;margin:0 0 16px;">
    Hi {{ $order->billing_first_name }}, your order has been updated.
</h1>

<p style="color:#bbbbbb;font-size:16px;line-height:24px;margin:0 0 24px;">
    {{ $statusMessage }}
</p>

<div style="background-color:#333333;padding:20px;border-radius:8px;margin-bottom:20px;">
    <table style="width:100%;">
        <tr>
            <td style="color:#bbbbbb;font-size:14px;padding:6px 0;">Order Number</td>
            <td style="color:#ffffff;font-size:14px;text-align:right;">#{{ $order->order_number }}</td>
        </tr>
        <tr>
            <td style="color:#bbbbbb;font-size:14px;padding:6px 0;">Status</td>
            <td style="color:#10B981;font-size:14px;font-weight:bold;text-align:right;">{{ $statusLabel }}</td>
        </tr>
        @if($order->tracking_number)
        <tr>
            <td style="color:#bbbbbb;font-size:14px;padding:6px 0;">Tracking Number</td>
            <td style="color:#ffffff;font-size:14px;text-align:right;">{{ $order->tracking_number }}</td>
        </tr>
        @endif
        @if($order->shipped_at)
        <tr>
            <td style="color:#bbbbbb;font-size:14px;padding:6px 0;">Shipped</td>
            <td style="color:#ffffff;font-size:14px;text-align:right;">{{ $order->shipped_at->format('d M Y') }}</td>
        </tr>
        @endif
    </table>
</div>

<div style="background-color:#333333;padding:20px;border-radius:8px;margin-bottom:20px;">
    <h2 style="color:#ffffff;font-size:16px;margin:0 0 12px;">Your Items</h2>
    @foreach($order->items as $item)
    <div style="border-bottom:1px solid #3a3a3a;padding:8px 0;display:flex;justify-content:space-between;">
        <span style="color:#ffffff;font-size:14px;">{{ $item->product_name }} × {{ $item->quantity }}</span>
        <span style="color:#bbbbbb;font-size:14px;">${{ number_format($item->subtotal, 2) }}</span>
    </div>
    @endforeach
    <div style="margin-top:12px;text-align:right;">
        <span style="color:#bbbbbb;font-size:14px;">Total: </span>
        <span style="color:#10B981;font-size:16px;font-weight:bold;">${{ number_format($order->total, 2) }}</span>
    </div>
</div>

<p style="color:#bbbbbb;font-size:14px;line-height:22px;margin:0;">
    Questions? Reply to this email or contact our support team, quoting order #{{ $order->order_number }}.
</p>
@endsection
