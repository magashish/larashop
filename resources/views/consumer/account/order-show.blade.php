@extends('layouts.consumer')
@section('content')

<section class="py-5" style="min-height: 60vh;">
    <div class="container">

        <div class="row">
            {{-- Sidebar --}}
            <div class="col-lg-3 mb-4">
                @include('consumer.account._sidebar')
            </div>

            {{-- Main --}}
            <div class="col-lg-9">

                <div class="d-flex align-items-center gap-3 mb-4 flex-wrap">
                    <a href="{{ route('shop.account.orders') }}" class="btn btn-outline-secondary btn-sm rounded-pill">
                        <i class="bi bi-arrow-left me-1"></i>Back to Orders
                    </a>
                    <h2 class="fw-bold mb-0">Order #{{ $order->order_number }}</h2>
                    <span class="badge bg-{{ $order->status_badge }} text-capitalize fs-6">{{ $order->status }}</span>
                </div>

                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                <div class="row g-4">
                    {{-- Left column --}}
                    <div class="col-lg-8">

                        {{-- Items --}}
                        <div class="card border-0 shadow-sm mb-4" style="border-radius:12px; overflow:hidden;">
                            <div class="card-header bg-dark text-white fw-semibold py-3 px-4">
                                Items Ordered
                            </div>
                            <div class="card-body p-0">
                                <table class="table mb-0 align-middle">
                                    <tbody>
                                        @foreach($order->items as $item)
                                        <tr>
                                            <td class="ps-4 py-3" style="width:60px;">
                                                @if($item->product && $item->product->featured_image)
                                                <img src="{{ asset('storage/' . $item->product->featured_image) }}"
                                                     class="rounded" style="width:50px;height:50px;object-fit:cover;"
                                                     alt="{{ $item->product_name }}">
                                                @else
                                                <div class="rounded bg-light d-flex align-items-center justify-content-center"
                                                     style="width:50px;height:50px;">
                                                    <i class="bi bi-image text-muted"></i>
                                                </div>
                                                @endif
                                            </td>
                                            <td class="py-3">
                                                <div class="fw-semibold">{{ $item->product_name }}</div>
                                                @if(!empty($item->meta['variant_color']) || !empty($item->meta['variant_size']))
                                                <small class="text-muted">
                                                    {{ implode(' / ', array_filter([$item->meta['variant_color'] ?? null, $item->meta['variant_size'] ?? null])) }}
                                                </small>
                                                @endif
                                                @if($item->product_sku)
                                                <br><small class="text-muted">SKU: {{ $item->product_sku }}</small>
                                                @endif
                                            </td>
                                            <td class="text-center py-3">
                                                <span class="text-muted">× {{ $item->quantity }}</span>
                                            </td>
                                            <td class="text-end pe-4 py-3 fw-semibold">
                                                ${{ number_format($item->subtotal, 2) }}
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="table-light">
                                        <tr>
                                            <td colspan="3" class="ps-4 text-muted">Subtotal</td>
                                            <td class="text-end pe-4">${{ number_format($order->subtotal, 2) }}</td>
                                        </tr>
                                        @if($order->discount_amount > 0)
                                        <tr class="text-success">
                                            <td colspan="3" class="ps-4">Discount</td>
                                            <td class="text-end pe-4">-${{ number_format($order->discount_amount, 2) }}</td>
                                        </tr>
                                        @endif
                                        <tr>
                                            <td colspan="3" class="ps-4 text-muted">Shipping</td>
                                            <td class="text-end pe-4">
                                                @if($order->shipping_cost == 0) Free
                                                @else ${{ number_format($order->shipping_cost, 2) }}
                                                @endif
                                            </td>
                                        </tr>
                                        @if($order->tax_amount > 0)
                                        <tr>
                                            <td colspan="3" class="ps-4 text-muted">Tax (GST)</td>
                                            <td class="text-end pe-4">${{ number_format($order->tax_amount, 2) }}</td>
                                        </tr>
                                        @endif
                                        <tr class="fw-bold">
                                            <td colspan="3" class="ps-4 fs-6">Total</td>
                                            <td class="text-end pe-4 fs-6">${{ number_format($order->total, 2) }}</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>

                        {{-- Shipping Address --}}
                        <div class="card border-0 shadow-sm mb-4" style="border-radius:12px;">
                            <div class="card-body px-4 py-3">
                                <h6 class="fw-semibold mb-3">Shipping Address</h6>
                                <p class="mb-0 text-muted">
                                    {{ $order->shipping_first_name }} {{ $order->shipping_last_name }}<br>
                                    {{ $order->shipping_address }}<br>
                                    {{ $order->shipping_city }}, {{ $order->shipping_state }} {{ $order->shipping_postcode }}<br>
                                    {{ $order->shipping_country }}
                                </p>
                            </div>
                        </div>

                        @if($order->notes)
                        <div class="card border-0 shadow-sm mb-4" style="border-radius:12px;">
                            <div class="card-body px-4 py-3">
                                <h6 class="fw-semibold mb-2">Order Notes</h6>
                                <p class="text-muted mb-0">{{ $order->notes }}</p>
                            </div>
                        </div>
                        @endif

                    </div>

                    {{-- Right column --}}
                    <div class="col-lg-4">

                        {{-- Order info --}}
                        <div class="card border-0 shadow-sm mb-4" style="border-radius:12px;">
                            <div class="card-body px-4 py-3">
                                <h6 class="fw-semibold mb-3">Order Info</h6>
                                <table class="table table-sm table-borderless mb-0">
                                    <tr>
                                        <td class="text-muted ps-0">Date</td>
                                        <td class="text-end">{{ $order->created_at->format('d M Y') }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted ps-0">Status</td>
                                        <td class="text-end">
                                            <span class="badge bg-{{ $order->status_badge }} text-capitalize">{{ $order->status }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted ps-0">Payment</td>
                                        <td class="text-end text-capitalize">{{ $order->payment_status }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted ps-0">Method</td>
                                        <td class="text-end text-capitalize">{{ $order->payment_method }}</td>
                                    </tr>
                                    @if($order->tracking_number)
                                    <tr>
                                        <td class="text-muted ps-0">Tracking</td>
                                        <td class="text-end"><strong>{{ $order->tracking_number }}</strong></td>
                                    </tr>
                                    @endif
                                    @if($order->shipped_at)
                                    <tr>
                                        <td class="text-muted ps-0">Shipped</td>
                                        <td class="text-end">{{ $order->shipped_at->format('d M Y') }}</td>
                                    </tr>
                                    @endif
                                </table>
                            </div>
                        </div>

                        {{-- Reorder --}}
                        @if(in_array($order->status, ['delivered', 'processing', 'shipped']))
                        <form action="{{ route('shop.account.reorder', $order) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-dark w-100 rounded-pill py-3">
                                <i class="bi bi-arrow-repeat me-2"></i>Reorder These Items
                            </button>
                        </form>
                        @endif

                        {{-- Order status timeline --}}
                        <div class="card border-0 shadow-sm mt-4" style="border-radius:12px;">
                            <div class="card-body px-4 py-3">
                                <h6 class="fw-semibold mb-3">Status Timeline</h6>
                                @php
                                    $steps = [
                                        'pending'    => ['icon' => 'clock',        'label' => 'Order Placed'],
                                        'processing' => ['icon' => 'gear',         'label' => 'Processing'],
                                        'shipped'    => ['icon' => 'truck',        'label' => 'Shipped'],
                                        'delivered'  => ['icon' => 'check-circle', 'label' => 'Delivered'],
                                    ];
                                    $statusOrder = array_keys($steps);
                                    $currentIdx  = array_search($order->status, $statusOrder);
                                @endphp
                                @foreach($steps as $key => $step)
                                @php
                                    $stepIdx = array_search($key, $statusOrder);
                                    $done    = ($currentIdx !== false && $stepIdx <= $currentIdx)
                                               || in_array($order->status, ['cancelled', 'refunded']);
                                    $active  = $key === $order->status;
                                @endphp
                                <div class="d-flex align-items-center mb-3 {{ !$loop->last ? 'pb-2' : '' }}"
                                     style="{{ !$loop->last ? 'border-bottom: 1px dashed #eee;' : '' }}">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center me-3 flex-shrink-0"
                                         style="width:34px;height:34px;background:{{ $active ? '#212529' : ($done ? '#d1fae5' : '#f3f4f6') }};">
                                        <i class="bi bi-{{ $step['icon'] }}"
                                           style="color:{{ $active ? '#fff' : ($done ? '#10B981' : '#9ca3af') }};font-size:14px;"></i>
                                    </div>
                                    <span class="{{ $active ? 'fw-semibold text-dark' : ($done ? 'text-muted' : 'text-muted') }}" style="font-size:14px;">
                                        {{ $step['label'] }}
                                    </span>
                                </div>
                                @endforeach
                                @if(in_array($order->status, ['cancelled', 'refunded']))
                                <div class="d-flex align-items-center mt-1">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center me-3 flex-shrink-0"
                                         style="width:34px;height:34px;background:#fee2e2;">
                                        <i class="bi bi-x-circle" style="color:#ef4444;font-size:14px;"></i>
                                    </div>
                                    <span class="fw-semibold text-capitalize" style="font-size:14px;color:#ef4444;">{{ $order->status }}</span>
                                </div>
                                @endif
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>

    </div>
</section>

@endsection
