@extends('layouts.consumer')
@section('content')

<section class="py-5">
    <div class="container">

        {{-- Breadcrumb --}}
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('consumer') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('shop.merchandise.index') }}">Shop</a></li>
                @if($product->category)
                    <li class="breadcrumb-item"><a href="{{ route('shop.merchandise.index', ['category' => $product->category->slug]) }}">{{ $product->category->name }}</a></li>
                @endif
                <li class="breadcrumb-item active">{{ $product->name }}</li>
            </ol>
        </nav>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif

        <div class="row g-5">
            {{-- Images --}}
            <div class="col-md-6">
                <div class="product-gallery">
                    @php
                        $mainImage = $product->featured_image ?? $product->images->first()?->image_path;
                    @endphp
                    <div class="main-image mb-3 rounded-3 overflow-hidden bg-light" style="height: 450px;">
                        @if($mainImage)
                            <img src="{{ asset('storage/' . $mainImage) }}" id="mainProductImage" class="w-100 h-100 object-fit-contain p-2" alt="{{ $product->name }}">
                        @else
                            <div class="d-flex align-items-center justify-content-center h-100 text-muted">
                                <i class="bi bi-image fs-1"></i>
                            </div>
                        @endif
                    </div>
                    @if($product->images->count() > 0)
                    <div class="d-flex gap-2 flex-wrap">
                        @if($product->featured_image)
                        <img src="{{ asset('storage/' . $product->featured_image) }}" class="thumbnail-img rounded cursor-pointer border border-2 border-dark"
                             style="width:70px;height:70px;object-fit:cover;cursor:pointer;"
                             onclick="document.getElementById('mainProductImage').src=this.src">
                        @endif
                        @foreach($product->images as $img)
                        <img src="{{ asset('storage/' . $img->image_path) }}" class="thumbnail-img rounded cursor-pointer border"
                             style="width:70px;height:70px;object-fit:cover;cursor:pointer;"
                             onclick="document.getElementById('mainProductImage').src=this.src">
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>

            {{-- Product Info --}}
            <div class="col-md-6">
                @if($product->category)
                    <a href="{{ route('shop.merchandise.index', ['category' => $product->category->slug]) }}"
                       class="badge bg-secondary text-decoration-none mb-2">{{ $product->category->name }}</a>
                @endif

                <h1 class="fw-bold mb-2">{{ $product->name }}</h1>

                @if($product->sku)
                    <p class="text-muted small">SKU: {{ $product->sku }}</p>
                @endif

                {{-- Price --}}
                <div class="mb-3">
                    @if($product->is_on_sale)
                        <span class="fs-2 fw-bold text-danger">${{ number_format($product->sale_price, 2) }}</span>
                        <span class="fs-5 text-muted text-decoration-line-through ms-2">${{ number_format($product->price, 2) }}</span>
                        <span class="badge bg-danger ms-2">
                            {{ round((1 - $product->sale_price / $product->price) * 100) }}% OFF
                        </span>
                    @else
                        <span class="fs-2 fw-bold">${{ number_format($product->price, 2) }}</span>
                    @endif
                </div>

                {{-- Stock --}}
                <div class="mb-3">
                    @if($product->isInStock())
                        <span class="text-success"><i class="bi bi-check-circle-fill"></i> In Stock</span>
                        @if($product->track_stock && $product->stock_quantity <= 5)
                            <span class="text-warning ms-2">(Only {{ $product->stock_quantity }} left)</span>
                        @endif
                    @else
                        <span class="text-danger"><i class="bi bi-x-circle-fill"></i> Out of Stock</span>
                    @endif
                </div>

                @if($product->short_description)
                    <p class="text-muted">{{ $product->short_description }}</p>
                @endif

                {{-- Add to Cart --}}
                @if($product->isInStock())
                <form action="{{ route('shop.cart.add') }}" method="POST" class="mb-4">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="input-group" style="width: 130px;">
                            <button type="button" class="btn btn-outline-secondary" onclick="adjustQty(-1)">−</button>
                            <input type="number" name="quantity" id="qtyInput" class="form-control text-center" value="1" min="1" max="{{ $product->track_stock ? $product->stock_quantity : 99 }}">
                            <button type="button" class="btn btn-outline-secondary" onclick="adjustQty(1)">+</button>
                        </div>
                    </div>
                    <div class="d-flex gap-3">
                        <button type="submit" class="btn btn-dark btn-lg px-5 rounded-pill">
                            <i class="bi bi-bag-plus me-2"></i>Add to Cart
                        </button>
                        <a href="{{ route('shop.cart.index') }}" class="btn btn-outline-dark btn-lg rounded-pill">
                            <i class="bi bi-bag"></i>
                        </a>
                    </div>
                </form>
                @else
                    <button class="btn btn-secondary btn-lg px-5 rounded-pill" disabled>Out of Stock</button>
                @endif

                {{-- Meta --}}
                @if($product->weight)
                    <p class="text-muted small mt-3"><i class="bi bi-box"></i> Weight: {{ $product->weight }} kg</p>
                @endif
            </div>
        </div>

        {{-- Description Tab --}}
        @if($product->description)
        <div class="mt-5">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent fw-bold">Product Description</div>
                <div class="card-body">
                    {!! nl2br(e($product->description)) !!}
                </div>
            </div>
        </div>
        @endif

        {{-- Related Products --}}
        @if($related->isNotEmpty())
        <div class="mt-5">
            <h5 class="fw-bold mb-4">Related Products</h5>
            <div class="row g-3">
                @foreach($related as $rp)
                <div class="col-md-3 col-6">
                    @include('consumer.merchandise._product-card', ['product' => $rp])
                </div>
                @endforeach
            </div>
        </div>
        @endif

    </div>
</section>

@push('scripts')
<script>
function adjustQty(delta) {
    const input = document.getElementById('qtyInput');
    const newVal = parseInt(input.value) + delta;
    input.value = Math.max(parseInt(input.min || 1), Math.min(parseInt(input.max || 99), newVal));
}
</script>
@endpush
@endsection
