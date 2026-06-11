@extends('layouts.consumer')

@push('styles')
<style>
/* ── Product Page ── */
#pdpWrap { background: #fff; }

/* ── Image Gallery ── */
#pdpGallery {
    display: grid !important;
    grid-template-columns: 80px 1fr !important;
    gap: 12px !important;
    align-items: start !important;
}
#pdpGallery.no-thumbs { grid-template-columns: 1fr !important; }
#pdpThumbStrip {
    display: flex !important;
    flex-direction: column !important;
    gap: 8px !important;
}
#pdpThumbStrip img {
    display: block !important;
    width: 80px !important;
    height: 80px !important;
    object-fit: cover !important;
    cursor: pointer;
    border: 2px solid transparent;
    border-radius: 2px;
    transition: border-color .2s;
    flex-shrink: 0 !important;
}
#pdpThumbStrip img.active,
#pdpThumbStrip img:hover { border-color: #111; }

#pdpMainWrap {
    position: relative;
    overflow: hidden;
    background: #f5f5f5;
    cursor: zoom-in;
    aspect-ratio: 3/4;
    max-height: 680px;
}
#pdpMainWrap #pdpMainImg {
    display: block !important;
    width: 100% !important;
    height: 100% !important;
    object-fit: cover !important;
    transition: transform .4s ease;
}
#pdpMainWrap:hover #pdpMainImg { transform: scale(1.06); }

/* ── Colour swatches ── */
#pdpColourSwatches {
    display: flex !important;
    flex-wrap: wrap !important;
    gap: 10px !important;
    align-items: center !important;
    padding: 4px 0;
}
.pdp-swatch-btn {
    display: inline-flex !important;
    flex-direction: column !important;
    align-items: center !important;
    gap: 5px !important;
    background: none !important;
    border: none !important;
    padding: 4px !important;
    cursor: pointer;
    text-decoration: none !important;
}
.pdp-swatch-circle {
    display: block !important;
    width: 32px !important;
    height: 32px !important;
    border-radius: 50% !important;
    border: 2px solid #d0d0d0 !important;
    transition: transform .15s;
    flex-shrink: 0 !important;
    overflow: hidden;
}
.pdp-swatch-btn:hover .pdp-swatch-circle { transform: scale(1.1); }
.pdp-swatch-btn.active .pdp-swatch-circle {
    outline: 3px solid #111 !important;
    outline-offset: 3px !important;
    border-color: #fff !important;
}
.pdp-swatch-label {
    font-size: 10px !important;
    color: #888 !important;
    max-width: 52px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    text-align: center;
    line-height: 1.2;
}
.pdp-swatch-btn.active .pdp-swatch-label { color: #111 !important; font-weight: 700 !important; }

/* ── Size pills ── */
.pdp-size-btn {
    min-width: 52px !important;
    height: 44px !important;
    border: 1.5px solid #ccc !important;
    background: #fff !important;
    color: #111 !important;
    font-size: 13px !important;
    font-weight: 600 !important;
    border-radius: 0 !important;
    cursor: pointer;
    transition: all .15s;
    position: relative;
    padding: 0 12px !important;
    text-transform: uppercase;
    letter-spacing: .3px;
}
.pdp-size-btn:hover:not(.oos):not(.sel) { border-color: #111 !important; }
.pdp-size-btn.sel { background: #111 !important; color: #fff !important; border-color: #111 !important; }
.pdp-size-btn.oos { color: #bbb !important; border-color: #e5e5e5 !important; cursor: not-allowed; }
.pdp-size-btn.oos::after {
    content: '';
    position: absolute;
    top: 50%; left: 0; right: 0;
    height: 1.5px;
    background: #d0d0d0;
    transform: rotate(-20deg);
}

/* ── Quantity selector ── */
.pdp-qty-wrap {
    display: flex;
    align-items: center;
    border: 1.5px solid #ccc;
    width: fit-content;
}
.pdp-qty-btn {
    width: 44px;
    height: 48px;
    background: none;
    border: none;
    font-size: 20px;
    font-weight: 300;
    cursor: pointer;
    color: #111;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background .15s;
    flex-shrink: 0;
}
.pdp-qty-btn:hover { background: #f5f5f5; }
.pdp-qty-input {
    width: 56px;
    height: 48px;
    border: none;
    border-left: 1.5px solid #ccc;
    border-right: 1.5px solid #ccc;
    text-align: center;
    font-size: 15px;
    font-weight: 600;
    color: #111;
    background: #fff;
    -moz-appearance: textfield;
}
.pdp-qty-input::-webkit-inner-spin-button,
.pdp-qty-input::-webkit-outer-spin-button { -webkit-appearance: none; }

/* ── Add to cart ── */
#pdpAddBtn {
    background: #111 !important;
    color: #fff !important;
    border: none !important;
    padding: 0 32px !important;
    height: 48px !important;
    font-size: 13px !important;
    font-weight: 700 !important;
    letter-spacing: 1.5px;
    text-transform: uppercase;
    width: 100% !important;
    cursor: pointer;
    transition: background .2s;
    display: block !important;
    border-radius: 0 !important;
}
#pdpAddBtn:hover:not(:disabled) { background: #333 !important; }
#pdpAddBtn:disabled { background: #999 !important; cursor: not-allowed; }

/* ── Accordion ── */
#pdpAccordion .accordion-button {
    font-weight: 700; font-size: 12px;
    text-transform: uppercase; letter-spacing: 1px;
    background: none !important; color: #111 !important;
    padding: 18px 0; border-bottom: 1px solid #e5e5e5; box-shadow: none !important;
}
#pdpAccordion .accordion-button::after { filter: brightness(0); }
#pdpAccordion .accordion-button:not(.collapsed) { color: #111 !important; background: none !important; }
#pdpAccordion .accordion-item { border: none !important; border-top: 1px solid #e5e5e5 !important; }
#pdpAccordion .accordion-body { padding: 16px 0 20px; color: #444; font-size: 13px; line-height: 1.8; }

/* ── Membership strip ── */
.pdp-member-strip {
    display: grid;
    grid-template-columns: 1fr 1fr;
    min-height: 320px;
    margin-top: 4rem;
}
.pdp-member-img {
    background-size: cover;
    background-position: center;
    min-height: 280px;
}
.pdp-member-text {
    background: #1a1a1a;
    color: #fff;
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    justify-content: center;
    padding: 3rem;
    gap: 1.5rem;
}
.pdp-member-text h2 {
    font-size: 1.6rem;
    font-weight: 900;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin: 0;
    line-height: 1.2;
}
.pdp-member-text p {
    font-size: 13px;
    color: #aaa;
    margin: 0;
    line-height: 1.7;
}
.pdp-member-login-btn {
    display: inline-block;
    border: 2px solid #fff;
    color: #fff;
    text-decoration: none;
    padding: 12px 36px;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 2px;
    text-transform: uppercase;
    transition: background .2s, color .2s;
}
.pdp-member-login-btn:hover { background: #fff; color: #111; }

/* ── Related products ── */
.pdp-related-card { border: none; overflow: hidden; transition: box-shadow .2s; background: transparent; }
.pdp-related-card:hover { box-shadow: 0 4px 20px rgba(0,0,0,.1); }
.pdp-related-card .card-img-wrap { aspect-ratio: 3/4; overflow: hidden; background: #f5f5f5; }
.pdp-related-card .card-img-wrap img { width: 100%; height: 100%; object-fit: cover; display: block; transition: transform .4s; }
.pdp-related-card:hover .card-img-wrap img { transform: scale(1.05); }

/* ── Section label ── */
.pdp-section-label {
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 2px;
    text-transform: uppercase;
    color: #111;
    margin-bottom: 8px;
    display: block;
}

/* ── Sticky ATC (mobile) ── */
#pdpStickyAtc {
    position: fixed; bottom: 0; left: 0; right: 0;
    z-index: 1050; background: #fff;
    border-top: 1px solid #e5e5e5;
    padding: 12px 20px; display: none;
}
#pdpStickyAtc .pdp-sticky-add {
    background: #111 !important; color: #fff !important;
    border: none !important; padding: 12px 24px !important;
    font-size: 12px !important; font-weight: 700 !important;
    letter-spacing: 1.5px; text-transform: uppercase;
    cursor: pointer;
}

@media (max-width: 991px) {
    .pdp-member-strip { grid-template-columns: 1fr; }
    .pdp-member-img { min-height: 240px; }
}
@media (max-width: 767px) {
    #pdpStickyAtc { display: flex; gap: 12px; align-items: center; }
    #pdpGallery { grid-template-columns: 1fr !important; }
    #pdpThumbStrip { flex-direction: row !important; overflow-x: auto; }
    #pdpThumbStrip img { width: 60px !important; height: 60px !important; }
    .product-detail-col { padding-bottom: 80px; }
    .pdp-member-text { padding: 2rem 1.5rem; }
    .pdp-member-text h2 { font-size: 1.2rem; }
}
</style>
@endpush

@section('content')
<div id="pdpWrap">
<div class="container py-4 py-lg-5">

    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb" style="font-size:12px;letter-spacing:.3px">
            <li class="breadcrumb-item"><a href="{{ route('consumer') }}" class="text-muted text-decoration-none">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('shop.merchandise.index') }}" class="text-muted text-decoration-none">Shop</a></li>
            @if($product->category)
                <li class="breadcrumb-item">
                    <a href="{{ route('shop.merchandise.index', ['category' => $product->category->slug]) }}" class="text-muted text-decoration-none">{{ $product->category->name }}</a>
                </li>
            @endif
            <li class="breadcrumb-item active text-dark">{{ $product->name }}</li>
        </ol>
    </nav>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    @php
        $colorImages = [];
        foreach ($product->colorImages->groupBy('color_name') as $cName => $imgs) {
            $colorImages[$cName] = $imgs->pluck('image_path')->toArray();
        }

        $colorVariants = [];
        $colorMeta = [];
        foreach ($product->variants->groupBy('color_name') as $cName => $variants) {
            $first = $variants->first();
            $colorMeta[$cName] = [
                'hex'    => $first->color_hex ?? '#cccccc',
                'swatch' => $first->color_swatch_image,
            ];
            foreach ($variants as $v) {
                $colorVariants[$cName][$v->size] = [
                    'id'    => $v->id,
                    'stock' => $v->stock_quantity,
                    'price' => $v->final_price,
                    'order' => $v->size_order,
                ];
            }
        }

        $hasVariants = !empty($colorVariants);
        $firstColor  = $hasVariants ? array_key_first($colorVariants) : null;

        $variantImages = [];
        foreach ($product->variants as $v) {
            if ($v->featured_image) $variantImages[$v->id] = $v->featured_image;
        }

        $defaultImages = [];
        if ($product->featured_image) $defaultImages[] = $product->featured_image;
        foreach ($product->images as $img) $defaultImages[] = $img->image_path;

        $initialImages = ($firstColor && !empty($colorImages[$firstColor]))
            ? $colorImages[$firstColor]
            : $defaultImages;
        if (empty($initialImages)) $initialImages = [];

        $baseDisplayPrice = $product->is_on_sale ? $product->sale_price : $product->price;
    @endphp

    <div class="row g-4 g-lg-5">

        {{-- ── LEFT: Image Gallery ── --}}
        <div class="col-lg-7">
            <div id="pdpGallery" class="{{ count($initialImages) <= 1 ? 'no-thumbs' : '' }}">

                <div id="pdpThumbStrip" style="{{ count($initialImages) <= 1 ? 'display:none!important' : '' }}">
                    @foreach($initialImages as $i => $img)
                    @php $thumbSrc = Str::startsWith($img, 'http') ? $img : asset('storage/' . $img); @endphp
                    <img src="{{ $thumbSrc }}"
                         class="{{ $i === 0 ? 'active' : '' }}"
                         alt="{{ $product->name }}"
                         onclick="pdpSwitchMain(this, '{{ $thumbSrc }}')"
                         loading="lazy">
                    @endforeach
                </div>

                <div id="pdpMainWrap">
                    @if(!empty($initialImages))
                        @php $mainSrc = Str::startsWith($initialImages[0], 'http') ? $initialImages[0] : asset('storage/' . $initialImages[0]); @endphp
                        <img src="{{ $mainSrc }}" id="pdpMainImg" alt="{{ $product->name }}">
                    @else
                        <div style="display:flex;align-items:center;justify-content:center;height:100%;min-height:400px;color:#bbb;">
                            <i class="bi bi-image" style="font-size:4rem"></i>
                        </div>
                    @endif
                    @if($product->is_on_sale)
                    <div class="position-absolute top-0 start-0 m-3">
                        <span class="badge bg-dark px-3 py-2" style="font-size:11px;letter-spacing:1px">SALE</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- ── RIGHT: Product Info ── --}}
        <div class="col-lg-5 product-detail-col">

            {{-- Category label --}}
            @if($product->category)
                <p class="mb-1" style="font-size:11px;letter-spacing:2px;text-transform:uppercase;color:#999">{{ $product->category->name }}</p>
            @endif

            {{-- Product name --}}
            <h1 style="font-size:1.65rem;font-weight:900;text-transform:uppercase;letter-spacing:.5px;line-height:1.15;margin-bottom:.5rem">{{ $product->name }}</h1>

            @if($product->sku)
                <p class="text-muted mb-3" style="font-size:11px;letter-spacing:.5px">SKU: {{ $product->sku }}</p>
            @endif

            {{-- Price --}}
            <div class="mb-4" id="pdpPriceBlock">
                @if($product->is_on_sale)
                    <span style="font-size:1.3rem;font-weight:700;color:#c0392b" id="pdpCurrentPrice">${{ number_format($baseDisplayPrice, 2) }} AUD</span>
                    <span class="text-muted text-decoration-line-through ms-2" style="font-size:1rem" id="pdpOriginalPrice">${{ number_format($product->price, 2) }} AUD</span>
                @else
                    <span style="font-size:1.3rem;font-weight:700;color:#111" id="pdpCurrentPrice">${{ number_format($baseDisplayPrice, 2) }} AUD</span>
                @endif
            </div>

            {{-- Description (inline) --}}
            @if($product->short_description || $product->description)
            <div class="mb-4">
                <span class="pdp-section-label">Description</span>
                @if($product->short_description)
                    <p style="font-size:13px;color:#555;line-height:1.75;margin-bottom:0">{{ $product->short_description }}</p>
                @elseif($product->description)
                    <div style="font-size:13px;color:#555;line-height:1.75">{!! nl2br(e(Str::limit($product->description, 280))) !!}</div>
                @endif
            </div>
            @endif

            {{-- ── COLOUR SELECTOR ── --}}
            @if($hasVariants && count($colorMeta) > 0)
            <div class="mb-4">
                <span class="pdp-section-label">
                    Colour: <span id="selectedColourLabel" style="font-weight:400;letter-spacing:0;text-transform:none;color:#666">{{ $firstColor }}</span>
                </span>
                <div id="pdpColourSwatches">
                    @foreach($colorMeta as $cName => $cData)
                    @php $hex = $cData['hex'] ?? '#cccccc'; @endphp
                    <button type="button"
                            class="pdp-swatch-btn {{ $loop->first ? 'active' : '' }}"
                            data-colour="{{ $cName }}"
                            onclick="pdpSelectColour('{{ $cName }}')"
                            title="{{ $cName }}">
                        <span class="pdp-swatch-circle" style="background-color:{{ $hex }};">
                            @if($cData['swatch'])
                            <img src="{{ asset('storage/' . $cData['swatch']) }}" alt="{{ $cName }}"
                                 style="width:100%;height:100%;object-fit:cover;border-radius:50%;display:block;">
                            @endif
                        </span>
                        <span class="pdp-swatch-label">{{ $cName }}</span>
                    </button>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- ── SIZE SELECTOR ── --}}
            @if($hasVariants)
            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="pdp-section-label" style="margin-bottom:0">
                        Size: <span id="selectedSizeLabel" style="font-weight:400;letter-spacing:0;text-transform:none;color:#666">Select a size</span>
                    </span>
                    <button type="button" class="btn btn-link p-0 text-muted" style="font-size:11px;letter-spacing:.5px;text-transform:uppercase"
                            data-bs-toggle="modal" data-bs-target="#sizeGuideModal">Size Guide</button>
                </div>
                <div style="display:flex;flex-wrap:wrap;gap:6px" id="pdpSizeButtons"></div>
                <div class="mt-2 small text-danger d-none" id="pdpSizeError">Please select a size.</div>
            </div>
            @endif

            {{-- Stock notice --}}
            <div id="pdpStockNotice" class="mb-3" style="font-size:12px"></div>

            {{-- ── QUANTITY + ADD TO CART ── --}}
            <form id="addToCartForm" action="{{ route('shop.cart.add') }}" method="POST" class="mb-4">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <input type="hidden" name="variant_id" id="pdpVariantId" value="">

                <div class="d-flex gap-3 align-items-stretch mb-3">
                    <div class="pdp-qty-wrap flex-shrink-0">
                        <button type="button" class="pdp-qty-btn" onclick="pdpQtyChange(-1)">−</button>
                        <input type="number" name="quantity" id="pdpQtyInput" class="pdp-qty-input" value="1" min="1" max="99">
                        <button type="button" class="pdp-qty-btn" onclick="pdpQtyChange(1)">+</button>
                    </div>
                </div>

                <button type="submit" id="pdpAddBtn"
                    {{ !$hasVariants && !$product->isInStock() ? 'disabled' : '' }}>
                    {{ !$hasVariants && !$product->isInStock() ? 'OUT OF STOCK' : 'ADD TO CART' }}
                </button>
            </form>

            {{-- ── ACCORDION ── --}}
            <div class="accordion" id="pdpAccordion">

                @if($product->description)
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDetails">
                            Details
                        </button>
                    </h2>
                    <div id="collapseDetails" class="accordion-collapse collapse" data-bs-parent="#pdpAccordion">
                        <div class="accordion-body">
                            {!! nl2br(e($product->description)) !!}
                        </div>
                    </div>
                </div>
                @endif

                @if($shippingReturns)
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseShipping">
                            Shipping &amp; Returns
                        </button>
                    </h2>
                    <div id="collapseShipping" class="accordion-collapse collapse" data-bs-parent="#pdpAccordion">
                        <div class="accordion-body">
                            {!! nl2br(e($shippingReturns)) !!}
                        </div>
                    </div>
                </div>
                @endif

                @php $careInstructions = ($product->meta ?? [])['care_instructions'] ?? null; @endphp
                @if($careInstructions)
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCare">
                            Care Instructions
                        </button>
                    </h2>
                    <div id="collapseCare" class="accordion-collapse collapse" data-bs-parent="#pdpAccordion">
                        <div class="accordion-body">
                            {!! nl2br(e($careInstructions)) !!}
                        </div>
                    </div>
                </div>
                @endif

            </div>
            {{-- /accordion --}}

        </div>
        {{-- /right col --}}
    </div>
    {{-- /product row --}}

</div>{{-- /container --}}

{{-- ── MEMBERSHIP PROMO STRIP ── --}}
@if($promoStrip['enabled'])
<div class="pdp-member-strip">
    <div class="pdp-member-img"
         style="background-color:#e5e5e5;{{ $promoStrip['image'] ? 'background-image:url(' . asset('storage/' . $promoStrip['image']) . ')' : '' }}">
    </div>
    <div class="pdp-member-text">
        @if($promoStrip['heading'])
            <h2>{{ $promoStrip['heading'] }}</h2>
        @endif
        @if($promoStrip['body'])
            <p>{{ $promoStrip['body'] }}</p>
        @endif
        @if($promoStrip['button_text'])
            <a href="{{ $promoStrip['button_url'] ?: '/login' }}" class="pdp-member-login-btn">{{ $promoStrip['button_text'] }}</a>
        @endif
    </div>
</div>
@endif

{{-- ── PRODUCTS YOU MIGHT LIKE ── --}}
@if($related->isNotEmpty())
<div class="container py-5">
    <h2 style="font-size:1rem;font-weight:900;text-transform:uppercase;letter-spacing:2px;margin-bottom:2rem">Products You Might Like</h2>
    <div class="row g-3 g-lg-4">
        @foreach($related as $rp)
        <div class="col-6 col-md-3">
            <a href="{{ route('shop.merchandise.show', $rp) }}" class="text-decoration-none text-dark">
                <div class="pdp-related-card">
                    <div class="card-img-wrap mb-3">
                        @php $rImg = $rp->featured_image ?? $rp->images->first()?->image_path; @endphp
                        @if($rImg)
                            <img src="{{ asset('storage/' . $rImg) }}" alt="{{ $rp->name }}" loading="lazy">
                        @else
                            <div class="bg-light d-flex align-items-center justify-content-center" style="aspect-ratio:3/4">
                                <i class="bi bi-image fs-1 text-muted"></i>
                            </div>
                        @endif
                    </div>
                    <p class="mb-1" style="font-size:10px;letter-spacing:1.5px;text-transform:uppercase;color:#999">{{ $rp->category?->name }}</p>
                    <p style="font-size:13px;font-weight:600;margin-bottom:4px;text-transform:uppercase;letter-spacing:.3px">{{ $rp->name }}</p>
                    <p style="font-size:13px;font-weight:700;margin-bottom:0;color:#111">
                        @if($rp->is_on_sale)
                            <span style="color:#c0392b">${{ number_format($rp->sale_price, 2) }} AUD</span>
                            <span class="text-muted text-decoration-line-through ms-1" style="font-size:11px">${{ number_format($rp->price, 2) }}</span>
                        @else
                            ${{ number_format($rp->price, 2) }} AUD
                        @endif
                    </p>
                </div>
            </a>
        </div>
        @endforeach
    </div>
</div>
@endif

</div>{{-- /pdpWrap --}}
@include('consumer.blocks.social-feeds')


{{-- ── SIZE GUIDE MODAL ── --}}
<div class="modal fade" id="sizeGuideModal" tabindex="-1" aria-labelledby="sizeGuideLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content" style="border-radius:0">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" style="text-transform:uppercase;letter-spacing:1px;font-size:14px" id="sizeGuideLabel">Size Guide</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted small mb-3">All measurements in centimetres. Measure your body, not the garment.</p>
                <div class="table-responsive">
                    <table class="table table-bordered text-center" style="font-size:13px">
                        <thead class="table-dark">
                            <tr>
                                <th>Size</th><th>XS</th><th>S</th><th>M</th><th>L</th><th>XL</th><th>2XL</th><th>3XL</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td class="fw-semibold text-start">Chest</td><td>80–84</td><td>84–88</td><td>88–92</td><td>92–96</td><td>96–102</td><td>102–108</td><td>108–116</td></tr>
                            <tr><td class="fw-semibold text-start">Waist</td><td>64–68</td><td>68–72</td><td>72–76</td><td>76–82</td><td>82–88</td><td>88–96</td><td>96–104</td></tr>
                            <tr><td class="fw-semibold text-start">Hip</td><td>88–92</td><td>92–96</td><td>96–100</td><td>100–106</td><td>106–112</td><td>112–120</td><td>120–128</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── STICKY ADD TO BAG (mobile) ── --}}
<div id="pdpStickyAtc">
    <div style="flex:1;min-width:0">
        <div style="font-size:13px;font-weight:700;text-transform:uppercase;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $product->name }}</div>
        <div id="pdpStickyLabel" style="font-size:11px;color:#888;letter-spacing:.5px">Select colour &amp; size</div>
    </div>
    <button class="pdp-sticky-add" onclick="document.getElementById('addToCartForm').submit()">Add to Cart</button>
</div>

@push('scripts')
<script>
const pdpColorVariants = @json($colorVariants);
const pdpColorImages   = @json($colorImages);
const pdpDefaultImages = @json($defaultImages);
const pdpVariantImages = @json($variantImages);
const pdpBasePrice        = @json((float)$baseDisplayPrice);
const pdpIsOnSale         = @json((bool)$product->is_on_sale);
const pdpOrigPrice        = @json((float)$product->price);
const pdpAllowBackorders  = @json((bool)$product->allow_backorders);

let pdpColour    = @json($firstColor);
let pdpSize      = null;
let pdpVariantId = null;

function pdpImgSrc(path) {
    if (!path) return '';
    if (path.startsWith('http') || path.startsWith('/storage/')) return path;
    return '/storage/' + path;
}

function pdpFmtPrice(val) {
    return '$' + val.toFixed(2) + ' AUD';
}

document.addEventListener('DOMContentLoaded', function () {
    if (pdpColour) pdpRenderSizes(pdpColour);
    @if($hasVariants) pdpResetBtn(); @endif
    pdpStickyInit();
});

// ── Quantity ─────────────────────────────────────────────────────────────────
function pdpQtyChange(delta) {
    const inp = document.getElementById('pdpQtyInput');
    if (!inp) return;
    let v = parseInt(inp.value) + delta;
    if (v < 1) v = 1;
    if (v > 99) v = 99;
    inp.value = v;
}

// ── Colour ───────────────────────────────────────────────────────────────────
function pdpSelectColour(colour) {
    pdpColour = colour; pdpSize = null; pdpVariantId = null;
    document.querySelectorAll('#pdpColourSwatches .pdp-swatch-btn').forEach(btn => {
        btn.classList.toggle('active', btn.dataset.colour === colour);
    });
    const lbl = document.getElementById('selectedColourLabel');
    if (lbl) lbl.textContent = colour;
    pdpUpdateGallery(pdpColorImages[colour] || pdpDefaultImages);
    pdpRenderSizes(colour);
    pdpResetBtn();
}

// ── Gallery ───────────────────────────────────────────────────────────────────
function pdpUpdateGallery(images) {
    const gallery    = document.getElementById('pdpGallery');
    const mainImg    = document.getElementById('pdpMainImg');
    const thumbStrip = document.getElementById('pdpThumbStrip');
    if (!gallery || !mainImg) return;
    const hasMulti = images.length > 1;
    if (images.length > 0) {
        mainImg.src = pdpImgSrc(images[0]);
        document.querySelectorAll('#pdpThumbStrip img').forEach((t, i) => t.classList.toggle('active', i === 0));
    }
    if (thumbStrip) {
        if (hasMulti) {
            thumbStrip.style.removeProperty('display');
            gallery.classList.remove('no-thumbs');
            thumbStrip.innerHTML = '';
            images.forEach((path, i) => {
                const src = pdpImgSrc(path);
                const img = document.createElement('img');
                img.src = src; img.alt = ''; img.loading = 'lazy';
                if (i === 0) img.classList.add('active');
                img.onclick = () => pdpSwitchMain(img, src);
                thumbStrip.appendChild(img);
            });
        } else {
            thumbStrip.style.display = 'none';
            gallery.classList.add('no-thumbs');
        }
    }
}

function pdpSwitchMain(thumb, src) {
    const mainImg = document.getElementById('pdpMainImg');
    if (mainImg) mainImg.src = src;
    document.querySelectorAll('#pdpThumbStrip img').forEach(t => t.classList.remove('active'));
    thumb.classList.add('active');
}

// ── Sizes ─────────────────────────────────────────────────────────────────────
function pdpRenderSizes(colour) {
    const container = document.getElementById('pdpSizeButtons');
    if (!container) return;
    const sizes  = pdpColorVariants[colour] || {};
    const sorted = Object.entries(sizes).sort((a, b) => a[1].order - b[1].order);
    container.innerHTML = '';
    sorted.forEach(([size, data]) => {
        const btn = document.createElement('button');
        btn.type      = 'button';
        const isOos = data.stock === 0 && !pdpAllowBackorders;
        btn.className = 'pdp-size-btn' + (isOos ? ' oos' : '');
        btn.textContent      = size;
        btn.dataset.size      = size;
        btn.dataset.variantId = data.id;
        btn.dataset.stock     = data.stock;
        btn.dataset.price     = data.price;
        if (!isOos) btn.onclick = () => pdpSelectSize(btn);
        container.appendChild(btn);
    });
}

function pdpSelectSize(btn) {
    document.querySelectorAll('.pdp-size-btn').forEach(b => b.classList.remove('sel'));
    btn.classList.add('sel');
    pdpSize      = btn.dataset.size;
    pdpVariantId = btn.dataset.variantId;
    const stock  = parseInt(btn.dataset.stock);
    const price  = parseFloat(btn.dataset.price);

    const sizeLbl = document.getElementById('selectedSizeLabel');
    if (sizeLbl) sizeLbl.textContent = pdpSize;
    const varInput = document.getElementById('pdpVariantId');
    if (varInput) varInput.value = pdpVariantId;
    const errEl = document.getElementById('pdpSizeError');
    if (errEl) errEl.classList.add('d-none');

    const priceEl = document.getElementById('pdpCurrentPrice');
    if (priceEl) priceEl.textContent = pdpFmtPrice(price);

    const notice = document.getElementById('pdpStockNotice');
    if (notice) {
        if (stock > 0 && stock <= 5)
            notice.innerHTML = '<span style="color:#e67e22">Only ' + stock + ' left in this size</span>';
        else if (stock === 0 && pdpAllowBackorders)
            notice.innerHTML = '<span style="color:#2980b9">Available on backorder</span>';
        else if (stock === 0)
            notice.innerHTML = '<span style="color:#c0392b">Out of stock in this size</span>';
        else
            notice.innerHTML = '<span style="color:#27ae60">In stock</span>';
    }

    const addBtn = document.getElementById('pdpAddBtn');
    const outOfStock = stock === 0 && !pdpAllowBackorders;
    if (addBtn) { addBtn.disabled = outOfStock; addBtn.textContent = outOfStock ? 'OUT OF STOCK' : 'ADD TO CART'; }

    const stickyLbl = document.getElementById('pdpStickyLabel');
    if (stickyLbl) stickyLbl.textContent = (pdpColour || '') + ' / ' + pdpSize;

    if (pdpVariantImages && pdpVariantImages[pdpVariantId]) {
        const mainImg = document.getElementById('pdpMainImg');
        if (mainImg) {
            mainImg.src = pdpImgSrc(pdpVariantImages[pdpVariantId]);
            document.querySelectorAll('#pdpThumbStrip img').forEach(t => t.classList.remove('active'));
        }
    }
}

function pdpResetBtn() {
    const addBtn = document.getElementById('pdpAddBtn');
    if (addBtn) { addBtn.disabled = true; addBtn.textContent = 'SELECT A SIZE'; }
    const notice = document.getElementById('pdpStockNotice');
    if (notice) notice.innerHTML = '';
    const sizeLbl = document.getElementById('selectedSizeLabel');
    if (sizeLbl) sizeLbl.textContent = 'Select a size';
    const varInput = document.getElementById('pdpVariantId');
    if (varInput) varInput.value = '';
    const stickyLbl = document.getElementById('pdpStickyLabel');
    if (stickyLbl) stickyLbl.textContent = pdpColour ? pdpColour + ' / Select size' : 'Select colour & size';
    const priceEl = document.getElementById('pdpCurrentPrice');
    if (priceEl) priceEl.textContent = pdpFmtPrice(pdpBasePrice);
}

document.getElementById('addToCartForm').addEventListener('submit', function (e) {
    @if($hasVariants)
    if (!pdpVariantId) {
        e.preventDefault();
        const errEl = document.getElementById('pdpSizeError');
        if (errEl) { errEl.classList.remove('d-none'); errEl.scrollIntoView({behavior:'smooth',block:'center'}); }
    }
    @endif
});

function pdpStickyInit() {
    const sticky = document.getElementById('pdpStickyAtc');
    const addBtn = document.getElementById('pdpAddBtn');
    if (!sticky || !addBtn) return;
    new IntersectionObserver(([entry]) => {
        if (window.innerWidth <= 767)
            sticky.style.display = entry.isIntersecting ? 'none' : 'flex';
    }, { threshold: 0 }).observe(addBtn);
}

@if($hasVariants)
pdpResetBtn();
@endif
</script>
@endpush
@endsection
