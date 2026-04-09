@extends('layouts.consumer')
@section('content')

<section class="py-5">
    <div class="container">
        <h2 class="fw-bold mb-4"><i class="bi bi-bag me-2"></i>Shopping Cart</h2>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif

        @if($items->isEmpty())
            <div class="text-center py-5">
                <i class="bi bi-bag-x fs-1 text-muted"></i>
                <h4 class="mt-3 text-muted">Your cart is empty</h4>
                <a href="{{ route('shop.merchandise.index') }}" class="btn btn-dark mt-3 rounded-pill px-5">Continue Shopping</a>
            </div>
        @else
        <div class="row g-4">
            {{-- Cart Items --}}
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-0">
                        <table class="table align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Product</th>
                                    <th class="text-center">Price</th>
                                    <th class="text-center" style="width:130px">Quantity</th>
                                    <th class="text-end">Subtotal</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($items as $item)
                                <tr>
                                    <td class="ps-3">
                                        <div class="d-flex align-items-center gap-3">
                                            @if($item->product->featured_image)
                                                <img src="{{ asset('storage/' . $item->product->featured_image) }}" width="60" height="60" class="rounded object-fit-cover">
                                            @else
                                                <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width:60px;height:60px">
                                                    <i class="bi bi-image text-muted"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <a href="{{ route('shop.merchandise.show', $item->product) }}" class="text-dark text-decoration-none fw-semibold">
                                                    {{ $item->product->name }}
                                                </a>
                                                @if($item->variant_color || $item->variant_size)
                                                    <small class="d-block text-muted">
                                                        {{ $item->variant_color }}{{ $item->variant_color && $item->variant_size ? ' / ' : '' }}{{ $item->variant_size }}
                                                    </small>
                                                @elseif($item->product->sku)
                                                    <small class="d-block text-muted">SKU: {{ $item->product->sku }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">${{ number_format($item->unit_price, 2) }}</td>
                                    <td class="text-center">
                                        <form action="{{ route('shop.cart.update', $item) }}" method="POST" class="d-flex align-items-center gap-1">
                                            @csrf @method('PATCH')
                                            <input type="number" name="quantity" value="{{ $item->quantity }}" min="0" max="99" class="form-control form-control-sm text-center" style="width:60px" onchange="this.form.submit()">
                                        </form>
                                    </td>
                                    <td class="text-end fw-bold">${{ number_format($item->unit_price * $item->quantity, 2) }}</td>
                                    <td class="text-end pe-3">
                                        <form action="{{ route('shop.cart.remove', $item) }}" method="POST">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger border-0"><i class="bi bi-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Coupon --}}
                <div class="card border-0 shadow-sm mt-3">
                    <div class="card-body">
                        <h6 class="fw-semibold mb-3">Have a coupon?</h6>
                        @if($coupon)
                            <div class="d-flex align-items-center gap-3">
                                <span class="badge bg-success fs-6 py-2 px-3">{{ $coupon }}</span>
                                <span class="text-success">Coupon applied! You save ${{ number_format($discountAmount, 2) }}</span>
                                <form action="{{ route('shop.cart.remove-coupon') }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Remove</button>
                                </form>
                            </div>
                        @else
                            <form action="{{ route('shop.cart.apply-coupon') }}" method="POST" class="d-flex gap-2">
                                @csrf
                                <input type="text" name="coupon_code" class="form-control" placeholder="Enter coupon code..." style="max-width:250px">
                                <button type="submit" class="btn btn-outline-dark">Apply</button>
                            </form>
                        @endif
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-3">
                    <a href="{{ route('shop.merchandise.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Continue Shopping
                    </a>
                </div>
            </div>

            {{-- Order Summary --}}
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-dark text-white fw-bold rounded-top">Order Summary</div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal</span><span>${{ number_format($subtotal, 2) }}</span>
                        </div>
                        @if($discountAmount > 0)
                        <div class="d-flex justify-content-between mb-2 text-success">
                            <span>Discount</span><span>-${{ number_format($discountAmount, 2) }}</span>
                        </div>
                        @endif
                        <div class="d-flex justify-content-between mb-2 text-muted small">
                            <span>Shipping</span><span>Calculated at checkout</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2 text-muted small">
                            <span>Tax</span><span>Calculated at checkout</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between fw-bold fs-5">
                            <span>Estimated Total</span>
                            <span>${{ number_format($subtotal - $discountAmount, 2) }}</span>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent border-0 pb-3">
                        <a href="{{ route('shop.checkout.index') }}" class="btn btn-dark w-100 btn-lg rounded-pill">
                            Proceed to Checkout <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</section>

@endsection
