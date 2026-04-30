@extends('layouts.consumer')

@push('styles')
<style>
/* ── Product Page — scoped under #pdpWrap to avoid global CSS collisions ── */
#pdpWrap { background: #fff; }

/* ── Image Gallery: CSS grid so widths are guaranteed ── */
#pdpGallery {
    display: grid !important;
    grid-template-columns: 80px 1fr !important;
    gap: 12px !important;
    align-items: start !important;
}
#pdpGallery.no-thumbs {
    grid-template-columns: 1fr !important;
}
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
    border-radius: 4px;
    transition: border-color .2s;
    flex-shrink: 0 !important;
}
#pdpThumbStrip img.active,
#pdpThumbStrip img:hover { border-color: #111; }

#pdpMainWrap {
    position: relative;
    overflow: hidden;
    background: #f5f5f5;
    border-radius: 4px;
    cursor: zoom-in;
    aspect-ratio: 3/4;
    max-height: 620px;
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
    align-items: flex-end !important;
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
    width: 36px !important;
    height: 36px !important;
    border-radius: 50% !important;
    border: 2px solid #d0d0d0 !important;
    transition: transform .15s;
    flex-shrink: 0 !important;
}
.pdp-swatch-btn:hover .pdp-swatch-circle { transform: scale(1.12); }
.pdp-swatch-btn.active .pdp-swatch-circle {
    outline: 3px solid #111 !important;
    outline-offset: 3px !important;
    border-color: #fff !important;
}
.pdp-swatch-label {
    font-size: 10px !important;
    color: #888 !important;
    max-width: 50px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    text-align: center;
    line-height: 1.2;
}
.pdp-swatch-btn.active .pdp-swatch-label { color: #111 !important; font-weight: 700 !important; }

/* ── Size buttons ── */
.pdp-size-btn {
    min-width: 50px !important;
    height: 44px !important;
    border: 1.5px solid #ccc !important;
    background: #fff !important;
    color: #111 !important;
    font-size: 13px !important;
    font-weight: 600 !important;
    border-radius: 4px !important;
    cursor: pointer;
    transition: all .15s;
    position: relative;
    padding: 0 10px !important;
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
    transform: rotate(-25deg);
}

/* ── Add to bag ── */
#pdpAddBtn {
    background: #111 !important;
    color: #fff !important;
    border: none !important;
    padding: 16px 32px !important;
    font-size: 15px !important;
    font-weight: 700 !important;
    letter-spacing: .5px;
    border-radius: 4px !important;
    width: 100% !important;
    cursor: pointer;
    transition: background .2s;
    display: block !important;
}
#pdpAddBtn:hover:not(:disabled) { background: #333 !important; }
#pdpAddBtn:disabled { background: #999 !important; cursor: not-allowed; }

/* ── Accordion ── */
#pdpAccordion .accordion-button {
    font-weight: 600; font-size: 14px;
    text-transform: uppercase; letter-spacing: .5px;
    background: none !important; color: #111 !important;
    padding: 16px 0; border-bottom: 1px solid #e5e5e5; box-shadow: none !important;
}
#pdpAccordion .accordion-button:not(.collapsed) { color: #111 !important; background: none !important; }
#pdpAccordion .accordion-item { border: none !important; border-top: 1px solid #e5e5e5 !important; }
#pdpAccordion .accordion-body { padding: 16px 0; color: #444; font-size: 14px; line-height: 1.7; }

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
    font-size: 13px !important; font-weight: 700 !important;
    border-radius: 4px !important; cursor: pointer;
}

@media (max-width: 767px) {
    #pdpStickyAtc { display: flex; gap: 12px; align-items: center; }
    #pdpGallery { grid-template-columns: 1fr !important; }
    #pdpThumbStrip { flex-direction: row !important; }
    #pdpThumbStrip img { width: 60px !important; height: 60px !important; }
    .product-detail-col { padding-bottom: 80px; }
}

/* ── Related cards ── */
.pdp-related-card { border: none; border-radius: 4px; overflow: hidden; transition: box-shadow .2s; }
.pdp-related-card:hover { box-shadow: 0 4px 20px rgba(0,0,0,.12); }
.pdp-related-card img { aspect-ratio: 3/4; object-fit: cover; width: 100%; display: block; }
</style>
@endpush

@section('content')
<div id="pdpWrap">
<div class="container py-4 py-lg-5">

    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb" style="font-size:13px">
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
        // Build colour → images map
        $colorImages = [];
        foreach ($product->colorImages->groupBy('color_name') as $cName => $imgs) {
            $colorImages[$cName] = $imgs->pluck('image_path')->toArray();
        }

        // Build colour → sizes map with stock
        $colorVariants = [];
        $colorMeta = [];   // color_name => [hex, first_image]
        foreach ($product->variants->groupBy('color_name') as $cName => $variants) {
            $first = $variants->first();
            $colorMeta[$cName] = [
                'hex'   => $first->color_hex ?? '#cccccc',
                'swatch'=> $first->color_swatch_image,
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

        // Build variant ID → image map (for swapping on size select)
        $variantImages = [];
        foreach ($product->variants as $v) {
            if ($v->featured_image) {
                $variantImages[$v->id] = $v->featured_image;
            }
        }

        // Default gallery: featured image + gallery images
        $defaultImages = [];
        if ($product->featured_image) $defaultImages[] = $product->featured_image;
        foreach ($product->images as $img) $defaultImages[] = $img->image_path;

        // If first colour has images, use those
        $initialImages = ($firstColor && !empty($colorImages[$firstColor]))
            ? $colorImages[$firstColor]
            : $defaultImages;
        if (empty($initialImages)) $initialImages = [];
    @endphp

    <div class="row g-4">

        {{-- ── LEFT: Image Gallery ──────────────────────────────── --}}
        <div class="col-lg-7">
            <div id="pdpGallery" class="{{ count($initialImages) <= 1 ? 'no-thumbs' : '' }}">

                {{-- Vertical thumbs --}}
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

                {{-- Main image --}}
                <div id="pdpMainWrap">
                    @if(!empty($initialImages))
                        @php $mainSrc = Str::startsWith($initialImages[0], 'http') ? $initialImages[0] : asset('storage/' . $initialImages[0]); @endphp
                        <img src="{{ $mainSrc }}" id="pdpMainImg" alt="{{ $product->name }}">
                    @else
                        <div style="display:flex;align-items:center;justify-content:center;height:100%;min-height:300px;color:#bbb">
                            <i class="bi bi-image" style="font-size:4rem"></i>
                        </div>
                    @endif

                    {{-- Sale badge --}}
                    @if($product->is_on_sale)
                    <div class="position-absolute top-0 start-0 m-3">
                        <span class="badge bg-dark px-3 py-2" style="font-size:12px">SALE</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- ── RIGHT: Product Info ──────────────────────────────── --}}
        <div class="col-lg-5 product-detail-col">

            {{-- Name + code --}}
            <h1 class="fw-bold mb-1" style="font-size:1.6rem;line-height:1.2">{{ $product->name }}</h1>
            @if($product->sku)
                <p class="text-muted mb-3" style="font-size:13px">Product code: {{ $product->sku }}</p>
            @endif

            {{-- Price --}}
            @php $baseDisplayPrice = $product->is_on_sale ? $product->sale_price : $product->price; @endphp
            <div class="mb-4" id="pdpPriceBlock">
                <span class="fs-4 fw-bold {{ $product->is_on_sale ? 'text-danger' : '' }}" id="pdpCurrentPrice">
                    ${{ number_format($baseDisplayPrice, 2) }}
                </span>
                @if($product->is_on_sale)
                <span class="text-muted text-decoration-line-through ms-2" id="pdpOriginalPrice">
                    ${{ number_format($product->price, 2) }}
                </span>
                @endif
            </div>

            {{-- Short description --}}
            @if($product->short_description)
                <p class="text-muted mb-4" style="font-size:14px">{{ $product->short_description }}</p>
            @endif

            {{-- ── COLOUR SELECTOR ─────────────────────────────── --}}
            @if($hasVariants && count($colorMeta) > 0)
            <div class="mb-4">
                <p style="font-size:13px;font-weight:600;text-transform:uppercase;letter-spacing:.5px;margin-bottom:10px">
                    Colour: <span id="selectedColourLabel" style="font-weight:400;color:#666">{{ $firstColor }}</span>
                </p>
                <div id="pdpColourSwatches">
                    @foreach($colorMeta as $cName => $cData)
                    @php $hex = $cData['hex'] ?? '#cccccc'; @endphp
                    <button type="button"
                            class="pdp-swatch-btn {{ $loop->first ? 'active' : '' }}"
                            data-colour="{{ $cName }}"
                            onclick="pdpSelectColour('{{ $cName }}')"
                            title="{{ $cName }}">
                        <span class="pdp-swatch-circle"
                              style="background-color:{{ $hex }};">
                            @if($cData['swatch'])
                            <img src="{{ asset('storage/' . $cData['swatch']) }}"
                                 alt="{{ $cName }}"
                                 style="width:100%;height:100%;object-fit:cover;border-radius:50%;display:block;">
                            @endif
                        </span>
                        <span class="pdp-swatch-label">{{ $cName }}</span>
                    </button>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- ── SIZE SELECTOR ───────────────────────────────── --}}
            @if($hasVariants)
            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="fw-semibold" style="font-size:13px;text-transform:uppercase;letter-spacing:.5px">
                        Size: <span id="selectedSizeLabel" class="fw-normal text-muted">Select a size</span>
                    </span>
                    <button type="button" class="btn btn-link p-0 text-muted" style="font-size:13px" data-bs-toggle="modal" data-bs-target="#sizeGuideModal">
                        Size Guide
                    </button>
                </div>
                <div style="display:flex;flex-wrap:wrap;gap:8px" id="pdpSizeButtons">
                    {{-- Rendered by JS based on selected colour --}}
                </div>
                <div class="mt-2 small text-danger d-none" id="pdpSizeError">Please select a size.</div>
            </div>
            @endif

            {{-- Stock notice --}}
            <div id="pdpStockNotice" class="mb-3" style="font-size:13px"></div>

            {{-- ── ADD TO CART ──────────────────────────────────── --}}
            <form id="addToCartForm" action="{{ route('shop.cart.add') }}" method="POST" class="mb-4">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <input type="hidden" name="variant_id" id="pdpVariantId" value="">
                <input type="hidden" name="quantity" value="1">
                <button type="submit" id="pdpAddBtn"
                    {{ !$hasVariants && !$product->isInStock() ? 'disabled' : '' }}>
                    {{ !$hasVariants && !$product->isInStock() ? 'OUT OF STOCK' : 'ADD TO BAG' }}
                </button>
            </form>

            {{-- ── ACCORDION: Product Details ─────────────────── --}}
            <div class="accordion" id="pdpAccordion">

                @if($product->description)
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDesc">
                            Description
                        </button>
                    </h2>
                    <div id="collapseDesc" class="accordion-collapse collapse show" data-bs-parent="#pdpAccordion">
                        <div class="accordion-body">
                            {!! nl2br(e($product->description)) !!}
                        </div>
                    </div>
                </div>
                @endif

                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFabric">
                            Fabric & Care
                        </button>
                    </h2>
                    <div id="collapseFabric" class="accordion-collapse collapse" data-bs-parent="#pdpAccordion">
                        <div class="accordion-body">
                            @if($product->weight)
                                <p><strong>Weight:</strong> {{ $product->weight }} kg</p>
                            @endif
                            @php $meta = $product->meta ?? []; @endphp
                            @if(!empty($meta['fabric']))
                                <p><strong>Fabric:</strong> {{ $meta['fabric'] }}</p>
                            @endif
                            @if(!empty($meta['gsm']))
                                <p><strong>GSM:</strong> {{ $meta['gsm'] }}</p>
                            @endif

                            {{-- Wash care icons --}}
                            <div class="care-icons d-flex gap-3 mt-3 flex-wrap">
                                <div class="text-center">
                                    <span title="Machine wash cold">🌊</span>
                                    <div style="font-size:10px;color:#888;margin-top:3px">Cold wash</div>
                                </div>
                                <div class="text-center">
                                    <span title="Do not bleach">🚫</span>
                                    <div style="font-size:10px;color:#888;margin-top:3px">No bleach</div>
                                </div>
                                <div class="text-center">
                                    <span title="Tumble dry low">♨️</span>
                                    <div style="font-size:10px;color:#888;margin-top:3px">Low tumble</div>
                                </div>
                                <div class="text-center">
                                    <span title="Cool iron">🌡️</span>
                                    <div style="font-size:10px;color:#888;margin-top:3px">Cool iron</div>
                                </div>
                                <div class="text-center">
                                    <span title="Do not dry clean">🧺</span>
                                    <div style="font-size:10px;color:#888;margin-top:3px">No dry clean</div>
                                </div>
                            </div>

                            @if(!empty($meta['care_notes']))
                                <p class="mt-3">{{ $meta['care_notes'] }}</p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseShipping">
                            Shipping & Returns
                        </button>
                    </h2>
                    <div id="collapseShipping" class="accordion-collapse collapse" data-bs-parent="#pdpAccordion">
                        <div class="accordion-body">
                            <p>Orders are dispatched within 1–3 business days. Standard delivery 3–7 business days.</p>
                            <p>Free shipping on orders over $100.</p>
                            <p>Returns accepted within 30 days of purchase. Items must be unworn and in original condition.</p>
                        </div>
                    </div>
                </div>

            </div>
            {{-- End accordion --}}
        </div>
        {{-- End right col --}}
    </div>
    {{-- End row --}}

    {{-- ── Related Products ──────────────────────────────────────── --}}
    @if($related->isNotEmpty())
    <div class="mt-6 pt-5 border-top" style="margin-top:4rem">
        <h4 class="fw-bold mb-4" style="font-size:1.1rem;text-transform:uppercase;letter-spacing:.5px">You May Also Like</h4>
        <div class="row g-3">
            @foreach($related as $rp)
            <div class="col-6 col-md-3">
                <a href="{{ route('shop.merchandise.show', $rp) }}" class="text-decoration-none text-dark">
                    <div class="pdp-related-card card h-100">
                        @php $rImg = $rp->featured_image ?? $rp->images->first()?->image_path; @endphp
                        @if($rImg)
                            <img src="{{ asset('storage/' . $rImg) }}" class="card-img-top" alt="{{ $rp->name }}" loading="lazy">
                        @else
                            <div class="bg-light d-flex align-items-center justify-content-center" style="aspect-ratio:3/4">
                                <i class="bi bi-image fs-1 text-muted"></i>
                            </div>
                        @endif
                        <div class="card-body p-3">
                            <p class="mb-1" style="font-size:12px;color:#888">{{ $rp->category?->name }}</p>
                            <p class="fw-semibold mb-1" style="font-size:14px">{{ $rp->name }}</p>
                            <p class="fw-bold mb-0" style="font-size:14px">
                                @if($rp->is_on_sale)
                                    <span class="text-danger">${{ number_format($rp->sale_price, 2) }}</span>
                                    <span class="text-muted text-decoration-line-through small ms-1">${{ number_format($rp->price, 2) }}</span>
                                @else
                                    ${{ number_format($rp->price, 2) }}
                                @endif
                            </p>
                        </div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </div>
    @endif

</div>{{-- /container --}}
</div>{{-- /pdpWrap --}}


{{-- ── SIZE GUIDE MODAL ──────────────────────────────────────────────── --}}
<div class="modal fade" id="sizeGuideModal" tabindex="-1" aria-labelledby="sizeGuideLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="sizeGuideLabel">Size Guide</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted small mb-3">All measurements are in centimetres (cm). Measure your body, not the garment.</p>
                <div class="table-responsive">
                    <table class="table table-bordered text-center">
                        <thead class="table-dark">
                            <tr>
                                <th>Size</th>
                                <th>XS</th>
                                <th>S</th>
                                <th>M</th>
                                <th>L</th>
                                <th>XL</th>
                                <th>2XL</th>
                                <th>3XL</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td class="fw-semibold text-start">Chest</td><td>80–84</td><td>84–88</td><td>88–92</td><td>92–96</td><td>96–102</td><td>102–108</td><td>108–116</td></tr>
                            <tr><td class="fw-semibold text-start">Waist</td><td>64–68</td><td>68–72</td><td>72–76</td><td>76–82</td><td>82–88</td><td>88–96</td><td>96–104</td></tr>
                            <tr><td class="fw-semibold text-start">Hip</td><td>88–92</td><td>92–96</td><td>96–100</td><td>100–106</td><td>106–112</td><td>112–120</td><td>120–128</td></tr>
                        </tbody>
                    </table>
                </div>
                <div class="mt-3 p-3 bg-light rounded small">
                    <strong>How to measure:</strong>
                    <ul class="mb-0 mt-1">
                        <li><strong>Chest:</strong> Measure around the fullest part of your chest, keeping the tape horizontal.</li>
                        <li><strong>Waist:</strong> Measure around your natural waistline at the narrowest point.</li>
                        <li><strong>Hip:</strong> Measure around the fullest part of your hips.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── STICKY ADD TO BAG (mobile) ────────────────────────────────────── --}}
<div id="pdpStickyAtc">
    <div style="flex:1;min-width:0">
        <div style="font-size:13px;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $product->name }}</div>
        <div id="pdpStickyLabel" style="font-size:12px;color:#888">Select colour &amp; size</div>
    </div>
    <button class="pdp-sticky-add" onclick="document.getElementById('addToCartForm').submit()">ADD TO BAG</button>
</div>

@push('scripts')
<script>
// ── Data injected from PHP ───────────────────────────────────────────────
const pdpColorVariants  = @json($colorVariants);
const pdpColorImages    = @json($colorImages);
const pdpDefaultImages  = @json($defaultImages);
const pdpVariantImages  = @json($variantImages);  // variant_id → image_path
const pdpBasePrice      = @json((float)$baseDisplayPrice);
const pdpIsOnSale       = @json((bool)$product->is_on_sale);
const pdpOrigPrice      = @json((float)$product->price);

let pdpColour    = @json($firstColor);
let pdpSize      = null;
let pdpVariantId = null;

// ── Helpers ──────────────────────────────────────────────────────────────
function pdpImgSrc(path) {
    if (!path) return '';
    if (path.startsWith('http') || path.startsWith('/storage/')) return path;
    return '/storage/' + path;
}

// ── Init ─────────────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function () {
    if (pdpColour) pdpRenderSizes(pdpColour);
    @if($hasVariants) pdpResetBtn(); @endif
    pdpStickyInit();
});

// ── Colour select ─────────────────────────────────────────────────────────
function pdpSelectColour(colour) {
    pdpColour    = colour;
    pdpSize      = null;
    pdpVariantId = null;

    // Update swatch buttons
    document.querySelectorAll('#pdpColourSwatches .pdp-swatch-btn').forEach(btn => {
        btn.classList.toggle('active', btn.dataset.colour === colour);
    });
    const lbl = document.getElementById('selectedColourLabel');
    if (lbl) lbl.textContent = colour;

    // Swap gallery
    const imgs = pdpColorImages[colour] || pdpDefaultImages;
    pdpUpdateGallery(imgs);

    pdpRenderSizes(colour);
    pdpResetBtn();
}

// ── Gallery ───────────────────────────────────────────────────────────────
function pdpUpdateGallery(images) {
    const gallery   = document.getElementById('pdpGallery');
    const mainImg   = document.getElementById('pdpMainImg');
    const thumbStrip = document.getElementById('pdpThumbStrip');
    if (!gallery || !mainImg) return;

    const hasMulti = images.length > 1;

    if (images.length > 0) {
        mainImg.src = pdpImgSrc(images[0]);
        // Reset active thumb to first
        document.querySelectorAll('#pdpThumbStrip img').forEach((t, i) => t.classList.toggle('active', i === 0));
    }

    // Show/hide thumb strip column
    if (thumbStrip) {
        if (hasMulti) {
            thumbStrip.style.removeProperty('display');
            gallery.classList.remove('no-thumbs');
            thumbStrip.innerHTML = '';
            images.forEach((path, i) => {
                const src = pdpImgSrc(path);
                const img = document.createElement('img');
                img.src     = src;
                img.alt     = '';
                img.loading = 'lazy';
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

// ── Size rendering ────────────────────────────────────────────────────────
function pdpRenderSizes(colour) {
    const container = document.getElementById('pdpSizeButtons');
    if (!container) return;
    const sizes  = pdpColorVariants[colour] || {};
    const sorted = Object.entries(sizes).sort((a, b) => a[1].order - b[1].order);
    container.innerHTML = '';
    sorted.forEach(([size, data]) => {
        const btn = document.createElement('button');
        btn.type        = 'button';
        btn.className   = 'pdp-size-btn' + (data.stock === 0 ? ' oos' : '');
        btn.textContent = size;
        btn.dataset.size      = size;
        btn.dataset.variantId = data.id;
        btn.dataset.stock     = data.stock;
        btn.dataset.price     = data.price;
        if (data.stock > 0) btn.onclick = () => pdpSelectSize(btn);
        container.appendChild(btn);
    });
}

// ── Size select ───────────────────────────────────────────────────────────
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

    // Price update
    const priceEl = document.getElementById('pdpCurrentPrice');
    if (priceEl) priceEl.textContent = '$' + price.toFixed(2);

    // Stock notice
    const notice = document.getElementById('pdpStockNotice');
    if (notice) {
        if (stock > 0 && stock <= 5)
            notice.innerHTML = '<span style="color:#e67e22">⚠ Only ' + stock + ' left in this size</span>';
        else if (stock === 0)
            notice.innerHTML = '<span style="color:#e74c3c">Out of stock in this size</span>';
        else
            notice.innerHTML = '<span style="color:#27ae60">✓ In stock</span>';
    }

    const addBtn = document.getElementById('pdpAddBtn');
    if (addBtn) { addBtn.disabled = stock === 0; addBtn.textContent = stock === 0 ? 'OUT OF STOCK' : 'ADD TO BAG'; }

    const stickyLbl = document.getElementById('pdpStickyLabel');
    if (stickyLbl) stickyLbl.textContent = (pdpColour || '') + ' / ' + pdpSize;

    // ── Swap main image to variant-specific photo if one exists ──────────
    if (pdpVariantImages && pdpVariantImages[pdpVariantId]) {
        const mainImg = document.getElementById('pdpMainImg');
        if (mainImg) {
            mainImg.src = pdpImgSrc(pdpVariantImages[pdpVariantId]);
            // Deselect all thumbs — variant image isn't in the strip
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
    // Reset price display back to base price
    const priceEl = document.getElementById('pdpCurrentPrice');
    if (priceEl) priceEl.textContent = '$' + pdpBasePrice.toFixed(2);
}

// ── Form guard ────────────────────────────────────────────────────────────
document.getElementById('addToCartForm').addEventListener('submit', function (e) {
    @if($hasVariants)
    if (!pdpVariantId) {
        e.preventDefault();
        const errEl = document.getElementById('pdpSizeError');
        if (errEl) { errEl.classList.remove('d-none'); errEl.scrollIntoView({behavior:'smooth',block:'center'}); }
        return;
    }
    @endif
});

// ── Sticky bar ────────────────────────────────────────────────────────────
function pdpStickyInit() {
    const sticky = document.getElementById('pdpStickyAtc');
    const addBtn = document.getElementById('pdpAddBtn');
    if (!sticky || !addBtn) return;
    new IntersectionObserver(([entry]) => {
        if (window.innerWidth <= 767)
            sticky.style.display = entry.isIntersecting ? 'none' : 'flex';
    }, { threshold: 0 }).observe(addBtn);
}

@if(!$hasVariants)
// No variants — enable button directly
const _ab = document.getElementById('pdpAddBtn');
if (_ab) { _ab.disabled = false; _ab.textContent = 'ADD TO BAG'; }
@else
pdpResetBtn();
@endif
</script>
@endpush
@endsection
