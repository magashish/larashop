@php $cardUrl = route('shop.merchandise.show', $product); @endphp

<div class="shop-card-wrap">
    <a href="{{ $cardUrl }}" class="shop-card">
        <div class="shop-card__img-wrap">
            @if($product->featured_image)
                <img src="{{ asset('storage/' . $product->featured_image) }}"
                     class="shop-card__img" alt="{{ $product->name }}" loading="lazy">
            @elseif($product->images->first())
                <img src="{{ asset('storage/' . $product->images->first()->image_path) }}"
                     class="shop-card__img" alt="{{ $product->name }}" loading="lazy">
            @else
                <div class="shop-card__img" style="display:flex;align-items:center;justify-content:center;">
                    <i class="bi bi-image text-muted" style="font-size:2rem;"></i>
                </div>
            @endif

            @if($product->is_on_sale)
                <span class="shop-card__badge">Sale</span>
            @endif
            @if($product->is_featured)
                <span class="shop-card__featured-badge">★ Featured</span>
            @endif

            @if(!$product->isInStock())
                <div class="shop-card__oos">
                    <span class="shop-card__oos-label">Out of Stock</span>
                </div>
            @elseif($product->variants->isEmpty())
                <form action="{{ route('shop.cart.add') }}" method="POST"
                      onclick="event.stopPropagation();"
                      style="position:absolute;bottom:0;left:0;right:0;">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="quantity" value="1">
                    <button type="submit" class="shop-card__quick-add">+ Add to Cart</button>
                </form>
            @else
                <span class="shop-card__quick-add shop-card__quick-add--link">Select Options</span>
            @endif
        </div>

        <div class="shop-card__info">
            <div class="shop-card__name" title="{{ $product->name }}">{{ $product->name }}</div>
            <div class="shop-card__price">
                @if($product->is_on_sale)
                    <span class="shop-card__price-sale">${{ number_format($product->sale_price, 2) }}</span>
                    <span class="shop-card__price-orig">${{ number_format($product->price, 2) }}</span>
                @else
                    ${{ number_format($product->price, 2) }}
                @endif
            </div>
        </div>
    </a>
</div>
