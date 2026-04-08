@extends('layouts.consumer')
@section('content')

<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="text-center mb-5">
                    <div class="rounded-circle bg-success d-inline-flex align-items-center justify-content-center mb-4"
                         style="width: 100px; height: 100px;">
                        <i class="bi bi-check-lg text-white" style="font-size: 3rem;"></i>
                    </div>
                    <h1 class="fw-bold">Order Confirmed!</h1>
                    <p class="text-muted lead">Thank you for your purchase. Your order has been received.</p>
                    <p>Order Number: <strong class="fs-5 text-dark">{{ $order->order_number }}</strong></p>
                </div>

                {{-- Order Items --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header fw-semibold bg-dark text-white">Order Summary</div>
                    <div class="card-body p-0">
                        <table class="table mb-0">
                            <tbody>
                                @foreach($order->items as $item)
                                <tr>
                                    <td class="ps-3">
                                        <strong>{{ $item->product_name }}</strong>
                                        <small class="text-muted d-block">× {{ $item->quantity }}</small>
                                    </td>
                                    <td class="text-end pe-3">${{ number_format($item->subtotal, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr><td class="ps-3">Subtotal</td><td class="text-end pe-3">${{ number_format($order->subtotal, 2) }}</td></tr>
                                @if($order->discount_amount > 0)
                                    <tr class="text-success"><td class="ps-3">Discount</td><td class="text-end pe-3">-${{ number_format($order->discount_amount, 2) }}</td></tr>
                                @endif
                                <tr><td class="ps-3">Shipping</td><td class="text-end pe-3">${{ number_format($order->shipping_cost, 2) }}</td></tr>
                                <tr><td class="ps-3">Tax</td><td class="text-end pe-3">${{ number_format($order->tax_amount, 2) }}</td></tr>
                                <tr class="fw-bold fs-6"><td class="ps-3">Total Paid</td><td class="text-end pe-3">${{ number_format($order->total, 2) }}</td></tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                {{-- Shipping Info --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header fw-semibold">Shipping To</div>
                    <div class="card-body">
                        <p class="mb-1">{{ $order->shipping_first_name }} {{ $order->shipping_last_name }}</p>
                        <p class="mb-1">{{ $order->shipping_address }}, {{ $order->shipping_city }}</p>
                        <p class="mb-0">{{ $order->shipping_state }} {{ $order->shipping_postcode }}, {{ $order->shipping_country }}</p>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4 bg-light">
                    <div class="card-body text-center">
                        <i class="bi bi-envelope-check text-muted fs-3 mb-2 d-block"></i>
                        <p class="text-muted mb-0">A confirmation email will be sent to <strong>{{ $order->billing_email }}</strong></p>
                    </div>
                </div>

                <div class="d-flex gap-3 justify-content-center">
                    <a href="{{ route('shop.merchandise.index') }}" class="btn btn-dark rounded-pill px-5">
                        <i class="bi bi-bag me-2"></i>Continue Shopping
                    </a>
                    @auth
                    <a href="{{ route('consumer') }}" class="btn btn-outline-secondary rounded-pill px-4">Go to Dashboard</a>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
