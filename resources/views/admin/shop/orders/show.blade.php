@extends('layouts.app')
@section('content')
<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0"><i class="bi bi-bag-check"></i> Order {{ $order->order_number }}</h5>
        <a href="{{ route('admin.shop.orders.index') }}" class="btn btn-outline-secondary btn-sm">← Back to Orders</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    <div class="row g-4">
        {{-- Order Items --}}
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header fw-semibold">Order Items</div>
                <div class="card-body p-0">
                    <table class="table mb-0">
                        <thead class="table-light">
                            <tr><th>Product</th><th>SKU</th><th class="text-center">Qty</th><th class="text-end">Unit Price</th><th class="text-end">Subtotal</th></tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                            <tr>
                                <td>{{ $item->product_name }}</td>
                                <td><code>{{ $item->product_sku ?? '—' }}</code></td>
                                <td class="text-center">{{ $item->quantity }}</td>
                                <td class="text-end">${{ number_format($item->unit_price, 2) }}</td>
                                <td class="text-end">${{ number_format($item->subtotal, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light fw-semibold">
                            <tr><td colspan="4" class="text-end">Subtotal</td><td class="text-end">${{ number_format($order->subtotal, 2) }}</td></tr>
                            @if($order->discount_amount > 0)
                            <tr class="text-danger"><td colspan="4" class="text-end">Discount ({{ $order->coupon?->code }})</td><td class="text-end">-${{ number_format($order->discount_amount, 2) }}</td></tr>
                            @endif
                            <tr><td colspan="4" class="text-end">Shipping</td><td class="text-end">${{ number_format($order->shipping_cost, 2) }}</td></tr>
                            <tr><td colspan="4" class="text-end">Tax</td><td class="text-end">${{ number_format($order->tax_amount, 2) }}</td></tr>
                            <tr class="fs-5"><td colspan="4" class="text-end">Total</td><td class="text-end">${{ number_format($order->total, 2) }}</td></tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            {{-- Addresses --}}
            <div class="row g-3 mt-1">
                <div class="col-md-6">
                    <div class="card shadow h-100">
                        <div class="card-header fw-semibold">Billing Address</div>
                        <div class="card-body">
                            <p class="mb-1">{{ $order->billing_first_name }} {{ $order->billing_last_name }}</p>
                            <p class="mb-1">{{ $order->billing_email }}</p>
                            @if($order->billing_phone)<p class="mb-1">{{ $order->billing_phone }}</p>@endif
                            <p class="mb-1">{{ $order->billing_address }}</p>
                            <p class="mb-0">{{ $order->billing_city }}, {{ $order->billing_state }} {{ $order->billing_postcode }}, {{ $order->billing_country }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card shadow h-100">
                        <div class="card-header fw-semibold">Shipping Address</div>
                        <div class="card-body">
                            <p class="mb-1">{{ $order->shipping_first_name }} {{ $order->shipping_last_name }}</p>
                            <p class="mb-1">{{ $order->shipping_address }}</p>
                            <p class="mb-0">{{ $order->shipping_city }}, {{ $order->shipping_state }} {{ $order->shipping_postcode }}, {{ $order->shipping_country }}</p>
                            @if($order->tracking_number)
                                <hr>
                                <strong>Tracking:</strong> {{ $order->tracking_number }}
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            @if($order->notes)
            <div class="card shadow mt-3">
                <div class="card-header fw-semibold">Customer Notes</div>
                <div class="card-body"><p class="mb-0">{{ $order->notes }}</p></div>
            </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="col-md-4">
            <div class="card shadow mb-3">
                <div class="card-header fw-semibold">Update Order</div>
                <div class="card-body">
                    <form action="{{ route('admin.shop.orders.update', $order) }}" method="POST">
                        @csrf @method('PUT')
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Order Status</label>
                            <select name="status" class="form-select">
                                @foreach(['pending','processing','shipped','delivered','cancelled','refunded'] as $s)
                                    <option value="{{ $s }}" @selected($order->status === $s)>{{ ucfirst($s) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Payment Status</label>
                            <select name="payment_status" class="form-select">
                                @foreach(['unpaid','paid','refunded','failed'] as $s)
                                    <option value="{{ $s }}" @selected($order->payment_status === $s)>{{ ucfirst($s) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Tracking Number</label>
                            <input type="text" name="tracking_number" class="form-control" value="{{ $order->tracking_number }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Admin Notes</label>
                            <textarea name="notes" class="form-control" rows="3">{{ $order->notes }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-warning w-100"><i class="bi bi-save"></i> Update Order</button>
                    </form>
                </div>
            </div>

            <div class="card shadow">
                <div class="card-header fw-semibold">Order Summary</div>
                <div class="card-body">
                    <dl class="row mb-0 small">
                        <dt class="col-6">Order #</dt><dd class="col-6">{{ $order->order_number }}</dd>
                        <dt class="col-6">Date</dt><dd class="col-6">{{ $order->created_at->format('d M Y H:i') }}</dd>
                        <dt class="col-6">Payment</dt><dd class="col-6">{{ ucfirst($order->payment_method ?? 'N/A') }}</dd>
                        <dt class="col-6">Reference</dt><dd class="col-6 text-truncate">{{ $order->payment_reference ?? '—' }}</dd>
                        @if($order->shipped_at)<dt class="col-6">Shipped</dt><dd class="col-6">{{ $order->shipped_at->format('d M Y') }}</dd>@endif
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
