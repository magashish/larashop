<div class="card h-100 border-0 shadow-sm product-card" style="border-radius:12px; overflow:hidden; transition: transform 0.2s;">
    <a href="{{ route('shop.merchandise.show', $product) }}" class="text-decoration-none">
        <div class="position-relative overflow-hidden" style="height: 220px; background:#f8f9fa;">
            @if($product->featured_image)
                <img src="{{ asset('storage/' . $product->featured_image) }}"
                     class="w-100 h-100 object-fit-cover"
                     alt="{{ $product->name }}" loading="lazy">
            @elseif($product->images->first())
                <img src="{{ asset('storage/' . $product->images->first()->image_path) }}"
                     class="w-100 h-100 object-fit-cover"
                     alt="{{ $product->name }}" loading="lazy">
            @else
                <div class="d-flex align-items-center justify-content-center h-100 text-muted">
                    <i class="bi bi-image fs-1"></i>
                </div>
            @endif
            @if($product->is_on_sale)
                <span class="position-absolute top-0 start-0 m-2 badge bg-danger">SALE</span>
            @endif
            @if($product->is_featured)
                <span class="position-absolute top-0 end-0 m-2 badge bg-warning text-dark">⭐ Featured</span>
            @endif
            @if(!$product->isInStock())
                <div class="position-absolute bottom-0 start-0 end-0 bg-dark bg-opacity-60 text-white text-center py-1 small">Out of Stock</div>
            @endif
        </div>
    </a>
    <div class="card-body d-flex flex-column p-3">
        <p class="text-muted small mb-1">{{ $product->category?->name }}</p>
        <h6 class="fw-semibold mb-2 text-dark" style="line-height:1.3">
            <a href="{{ route('shop.merchandise.show', $product) }}" class="text-decoration-none text-dark">{{ $product->name }}</a>
        </h6>
        <div class="mt-auto">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    @if($product->is_on_sale)
                        <span class="fw-bold text-danger">${{ number_format($product->sale_price, 2) }}</span>
                        <small class="text-muted text-decoration-line-through ms-1">${{ number_format($product->price, 2) }}</small>
                    @else
                        <span class="fw-bold">${{ number_format($product->price, 2) }}</span>
                    @endif
                </div>
                @if($product->isInStock())
                <form action="{{ route('shop.cart.add') }}" method="POST">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="quantity" value="1">
                    <button type="submit" class="btn btn-dark btn-sm rounded-pill px-3">
                        <i class="bi bi-bag-plus"></i>
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>
</div>
@once
@push('styles')
<style>.product-card:hover{transform:translateY(-4px);box-shadow:0 8px 25px rgba(0,0,0,.12)!important}</style>
@endpush
@endonce
