@extends('layouts.consumer')
@section('content')

<section class="py-5" style="min-height: 60vh;">
    <div class="container">

        <div class="row">
            {{-- Sidebar --}}
            <div class="col-lg-3 mb-4">
                @include('consumer.account._sidebar')
            </div>

            {{-- Main content --}}
            <div class="col-lg-9">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <h2 class="fw-bold mb-0">My Orders</h2>
                    <a href="{{ route('shop.merchandise.index') }}" class="btn btn-dark btn-sm rounded-pill px-4">
                        <i class="bi bi-bag me-1"></i>Shop Now
                    </a>
                </div>

                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                @forelse($orders as $order)
                <div class="card border-0 shadow-sm mb-3" style="border-radius: 12px; overflow: hidden;">
                    <div class="card-header bg-dark text-white py-3 px-4 d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-3 flex-wrap">
                            <span class="fw-semibold">#{{ $order->order_number }}</span>
                            <span class="badge bg-{{ $order->status_badge }} text-capitalize">{{ $order->status }}</span>
                            @if($order->payment_status === 'paid')
                                <span class="badge bg-success">Paid</span>
                            @elseif($order->payment_status === 'refunded')
                                <span class="badge bg-secondary">Refunded</span>
                            @endif
                        </div>
                        <small class="text-muted" style="color: #aaa !important;">
                            {{ $order->created_at->format('d M Y') }}
                        </small>
                    </div>
                    <div class="card-body px-4 py-3">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <div class="d-flex flex-column gap-1">
                                    @foreach($order->items->take(3) as $item)
                                    <div class="d-flex justify-content-between">
                                        <span class="text-dark">
                                            {{ $item->product_name }}
                                            @if(!empty($item->meta['variant_color']) || !empty($item->meta['variant_size']))
                                            <small class="text-muted">
                                                ({{ implode(' / ', array_filter([$item->meta['variant_color'] ?? null, $item->meta['variant_size'] ?? null])) }})
                                            </small>
                                            @endif
                                            <span class="text-muted"> × {{ $item->quantity }}</span>
                                        </span>
                                        <span class="text-muted">${{ number_format($item->subtotal, 2) }}</span>
                                    </div>
                                    @endforeach
                                    @if($order->items->count() > 3)
                                    <small class="text-muted">+ {{ $order->items->count() - 3 }} more item(s)</small>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                                <div class="fw-bold fs-5 mb-2">${{ number_format($order->total, 2) }}</div>
                                <div class="d-flex gap-2 justify-content-md-end flex-wrap">
                                    <a href="{{ route('shop.account.order', $order) }}"
                                       class="btn btn-outline-dark btn-sm rounded-pill px-3">
                                        <i class="bi bi-eye me-1"></i>View
                                    </a>
                                    @if(in_array($order->status, ['delivered', 'processing', 'shipped']))
                                    <form action="{{ route('shop.account.reorder', $order) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-dark btn-sm rounded-pill px-3">
                                            <i class="bi bi-arrow-repeat me-1"></i>Reorder
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </div>
                        </div>

                        @if($order->tracking_number)
                        <div class="mt-3 pt-2 border-top">
                            <small class="text-muted"><i class="bi bi-truck me-1"></i>Tracking: <strong>{{ $order->tracking_number }}</strong></small>
                        </div>
                        @endif
                    </div>
                </div>
                @empty
                <div class="card border-0 shadow-sm text-center py-5" style="border-radius: 12px;">
                    <div class="card-body">
                        <i class="bi bi-bag-x text-muted" style="font-size: 4rem;"></i>
                        <h5 class="mt-3 text-muted">No orders yet</h5>
                        <p class="text-muted mb-4">When you place an order, it will appear here.</p>
                        <a href="{{ route('shop.merchandise.index') }}" class="btn btn-dark rounded-pill px-5">
                            Start Shopping
                        </a>
                    </div>
                </div>
                @endforelse

                <div class="mt-3">
                    {{ $orders->links() }}
                </div>
            </div>
        </div>

    </div>
</section>

@endsection
