@extends('layouts.consumer')

@push('styles')
<style>
/* ── Product Page Layout ─────────────────────────────── */
.product-page { background: #fff; }

/* Thumbnails: vertical strip on desktop, horizontal on mobile */
.thumb-strip {
    display: flex;
    flex-direction: column;
    gap: 8px;
    width: 80px;
    flex-shrink: 0;
}
.thumb-strip .thumb {
    width: 80px;
    height: 80px;
    object-fit: cover;
    cursor: pointer;
    border: 2px solid transparent;
    border-radius: 4px;
    transition: border-color .2s;
}
.thumb-strip .thumb.active,
.thumb-strip .thumb:hover {
    border-color: #111;
}

/* Main image */
.main-image-wrap {
    flex: 1;
    position: relative;
    overflow: hidden;
    background: #f5f5f5;
    border-radius: 4px;
    cursor: zoom-in;
    aspect-ratio: 3/4;
    max-height: 600px;
}
.main-image-wrap img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform .4s ease;
}
.main-image-wrap:hover img {
    transform: scale(1.08);
}

/* Colour swatches */
.colour-swatches-wrap {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    align-items: flex-end;
}
.swatch-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 4px;
    cursor: pointer;
}
.swatch-item .swatch-name {
    font-size: 10px;
    color: #888;
    text-align: center;
    max-width: 48px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    transition: color .15s;
}
.swatch-item.selected .swatch-name { color: #111; font-weight: 600; }
.colour-swatch {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    border: 2px solid #ddd;
    cursor: pointer;
    transition: transform .15s;
    position: relative;
    /* NO overflow:hidden here — it clips the outline */
}
.colour-swatch:hover { transform: scale(1.1); }
.colour-swatch.selected {
    outline: 3px solid #111;
    outline-offset: 2px;
    border-color: #fff;
}
.colour-swatch img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 50%;
    overflow: hidden;
    display: block;
}

/* Size buttons */
.size-btn {
    min-width: 52px;
    height: 44px;
    border: 1.5px solid #ccc;
    background: #fff;
    color: #111;
    font-size: 13px;
    font-weight: 600;
    border-radius: 4px;
    cursor: pointer;
    transition: all .15s;
    position: relative;
}
.size-btn:hover:not(.disabled):not(.selected) {
    border-color: #111;
}
.size-btn.selected {
    background: #111;
    color: #fff;
    border-color: #111;
}
.size-btn.disabled {
    color: #bbb;
    border-color: #e5e5e5;
    cursor: not-allowed;
}
.size-btn.disabled::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 0;
    right: 0;
    height: 1.5px;
    background: #ccc;
    transform: rotate(-30deg);
}

/* Add to bag button */
.btn-add-bag {
    background: #111;
    color: #fff;
    border: none;
    padding: 16px 32px;
    font-size: 15px;
    font-weight: 700;
    letter-spacing: .5px;
    border-radius: 4px;
    width: 100%;
    cursor: pointer;
    transition: background .2s;
}
.btn-add-bag:hover:not(:disabled) { background: #333; }
.btn-add-bag:disabled { background: #999; cursor: not-allowed; }

/* Accordion */
.product-accordion .accordion-button {
    font-weight: 600;
    font-size: 14px;
    text-transform: uppercase;
    letter-spacing: .5px;
    background: none;
    color: #111;
    padding: 16px 0;
    border-bottom: 1px solid #e5e5e5;
    box-shadow: none;
}
.product-accordion .accordion-button::after { filter: none; }
.product-accordion .accordion-button:not(.collapsed) { color: #111; background: none; }
.product-accordion .accordion-item { border: none; border-top: 1px solid #e5e5e5; }
.product-accordion .accordion-body { padding: 16px 0; color: #444; font-size: 14px; line-height: 1.7; }

/* Care icons */
.care-icons span { font-size: 22px; }

/* Sticky bar (mobile) */
.sticky-atc {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    z-index: 1050;
    background: #fff;
    border-top: 1px solid #e5e5e5;
    padding: 12px 20px;
    display: none;
}
@media (max-width: 768px) {
    .sticky-atc { display: flex; gap: 12px; align-items: center; }
    .thumb-strip { flex-direction: row; width: 100%; }
    .thumb-strip .thumb { width: 64px; height: 64px; }
    .product-detail-col { padding-bottom: 80px; }
}

/* Related products */
.related-card { border: none; border-radius: 4px; overflow: hidden; transition: box-shadow .2s; }
.related-card:hover { box-shadow: 0 4px 20px rgba(0,0,0,.1); }
.related-card .card-img-top { aspect-ratio: 3/4; object-fit: cover; }
</style>
@endpush

@section('content')
<div class="product-page">
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

    <div class="row g-5">

        {{-- ── LEFT: Image Gallery ──────────────────────────────── --}}
        <div class="col-lg-7">
            <div class="d-flex gap-3">

                {{-- Vertical thumbs --}}
                @if(count($initialImages) > 1)
                <div class="thumb-strip" id="thumbStrip">
                    @foreach($initialImages as $i => $img)
                    @php $thumbSrc = Str::startsWith($img, 'http') ? $img : asset('storage/' . $img); @endphp
                    <img src="{{ $thumbSrc }}"
                         class="thumb {{ $i === 0 ? 'active' : '' }}"
                         alt="{{ $product->name }}"
                         onclick="switchMainImage(this, '{{ $thumbSrc }}')"
                         loading="lazy">
                    @endforeach
                </div>
                @endif

                {{-- Main image --}}
                <div class="main-image-wrap" id="mainImageWrap">
                    @if(!empty($initialImages))
                        @php $mainSrc = Str::startsWith($initialImages[0], 'http') ? $initialImages[0] : asset('storage/' . $initialImages[0]); @endphp
                        <img src="{{ $mainSrc }}"
                             id="mainImage" alt="{{ $product->name }}">
                    @else
                        <div class="d-flex align-items-center justify-content-center h-100 text-muted" id="mainImage" style="min-height:300px">
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
            <div class="mb-4">
                @if($product->is_on_sale)
                    <span class="fs-4 fw-bold text-danger me-2">${{ number_format($product->sale_price, 2) }}</span>
                    <span class="text-muted text-decoration-line-through">${{ number_format($product->price, 2) }}</span>
                @else
                    <span class="fs-4 fw-bold" id="displayPrice">${{ number_format($product->price, 2) }}</span>
                @endif
            </div>

            {{-- Short description --}}
            @if($product->short_description)
                <p class="text-muted mb-4" style="font-size:14px">{{ $product->short_description }}</p>
            @endif

            {{-- ── COLOUR SELECTOR ─────────────────────────────── --}}
            @if($hasVariants && count($colorMeta) > 0)
            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="fw-semibold" style="font-size:13px;text-transform:uppercase;letter-spacing:.5px">
                        Colour: <span id="selectedColourLabel" class="fw-normal text-muted">{{ $firstColor }}</span>
                    </span>
                </div>
                <div class="colour-swatches-wrap" id="colourSwatches">
                    @foreach($colorMeta as $cName => $cData)
                    <div class="swatch-item {{ $loop->first ? 'selected' : '' }}"
                         data-colour="{{ $cName }}"
                         onclick="selectColour('{{ $cName }}')">
                        <div class="colour-swatch {{ $loop->first ? 'selected' : '' }}"
                             style="background-color: {{ $cData['hex'] ?? '#cccccc' }};"
                             title="{{ $cName }}">
                            @if($cData['swatch'])
                                <img src="{{ asset('storage/' . $cData['swatch']) }}" alt="{{ $cName }}">
                            @endif
                        </div>
                        <span class="swatch-name">{{ $cName }}</span>
                    </div>
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
                <div class="d-flex flex-wrap gap-2" id="sizeButtons">
                    {{-- Rendered by JS based on selected colour --}}
                </div>
                <div class="mt-2 small text-danger d-none" id="sizeError">Please select a size.</div>
            </div>
            @endif

            {{-- Stock notice --}}
            <div id="stockNotice" class="mb-3 small"></div>

            {{-- ── ADD TO CART ──────────────────────────────────── --}}
            <form id="addToCartForm" action="{{ route('shop.cart.add') }}" method="POST" class="mb-4">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <input type="hidden" name="variant_id" id="selectedVariantId" value="">
                <input type="hidden" name="quantity" value="1">
                <button type="submit" class="btn-add-bag" id="addToBagBtn"
                    {{ !$hasVariants && !$product->isInStock() ? 'disabled' : '' }}>
                    {{ !$hasVariants && !$product->isInStock() ? 'OUT OF STOCK' : 'ADD TO BAG' }}
                </button>
            </form>

            {{-- ── ACCORDION: Product Details ─────────────────── --}}
            <div class="accordion product-accordion" id="productAccordion">

                @if($product->description)
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDesc">
                            Description
                        </button>
                    </h2>
                    <div id="collapseDesc" class="accordion-collapse collapse show" data-bs-parent="#productAccordion">
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
                    <div id="collapseFabric" class="accordion-collapse collapse" data-bs-parent="#productAccordion">
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
                    <div id="collapseShipping" class="accordion-collapse collapse" data-bs-parent="#productAccordion">
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
                    <div class="related-card card h-100">
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
</div>{{-- /product-page --}}


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
<div class="sticky-atc" id="stickyAtc">
    <div class="flex-grow-1">
        <div class="fw-semibold" style="font-size:13px">{{ $product->name }}</div>
        <div id="stickyVariantLabel" class="text-muted" style="font-size:12px">Select colour & size</div>
    </div>
    <button class="btn-add-bag" style="width:auto;padding:12px 24px;font-size:13px" onclick="document.getElementById('addToCartForm').submit()">
        ADD TO BAG
    </button>
</div>

@push('scripts')
<script>
// ── Data from PHP ───────────────────────────────────────────────────────
const colorVariants = @json($colorVariants);
const colorImages   = @json($colorImages);
const defaultImages = @json($defaultImages);
const allImages     = { ...colorImages };  // colour → [paths]

let selectedColour = @json($firstColor);
let selectedSize   = null;
let selectedVariantId = null;

// ── Init ────────────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function() {
    if (selectedColour) {
        renderSizes(selectedColour);
    }
    setupStickyBar();
});

// ── Colour selection ────────────────────────────────────────────────────
function selectColour(colour) {
    selectedColour = colour;
    selectedSize   = null;
    selectedVariantId = null;

    // Update swatch UI — toggle both wrapper and inner circle
    document.querySelectorAll('.swatch-item').forEach(el => {
        const active = el.dataset.colour === colour;
        el.classList.toggle('selected', active);
        const circle = el.querySelector('.colour-swatch');
        if (circle) circle.classList.toggle('selected', active);
    });
    document.getElementById('selectedColourLabel').textContent = colour;

    // Swap gallery images
    const imgs = allImages[colour] || defaultImages;
    updateGallery(imgs);

    // Render sizes for this colour
    renderSizes(colour);
    resetAddBtn();
}

// ── Gallery swap ────────────────────────────────────────────────────────
function imgSrc(path) {
    // path may already be a full URL (from asset()) or a relative storage path
    if (path.startsWith('http') || path.startsWith('/storage/')) return path;
    return '/storage/' + path;
}

function updateGallery(images) {
    const mainWrap = document.getElementById('mainImageWrap');
    if (!mainWrap) return;

    let mainImg = document.getElementById('mainImage');

    if (images.length === 0) return;

    // Ensure main img element exists
    if (!mainImg) {
        mainImg = document.createElement('img');
        mainImg.id = 'mainImage';
        mainImg.alt = '';
        mainWrap.insertBefore(mainImg, mainWrap.firstChild);
    }
    mainImg.src = imgSrc(images[0]);

    // Create or get thumb strip
    let thumbStrip = document.getElementById('thumbStrip');
    if (images.length > 1) {
        if (!thumbStrip) {
            thumbStrip = document.createElement('div');
            thumbStrip.id = 'thumbStrip';
            thumbStrip.className = 'thumb-strip';
            mainWrap.parentNode.insertBefore(thumbStrip, mainWrap);
        }
        thumbStrip.innerHTML = '';
        images.forEach((path, i) => {
            const img = document.createElement('img');
            const src = imgSrc(path);
            img.src       = src;
            img.className = 'thumb' + (i === 0 ? ' active' : '');
            img.alt       = '';
            img.loading   = 'lazy';
            img.onclick   = () => switchMainImage(img, src);
            thumbStrip.appendChild(img);
        });
    } else if (thumbStrip) {
        thumbStrip.remove();
    }
}

function switchMainImage(thumb, src) {
    document.getElementById('mainImage').src = src;
    document.querySelectorAll('.thumb').forEach(t => t.classList.remove('active'));
    thumb.classList.add('active');
}

// ── Size rendering ──────────────────────────────────────────────────────
function renderSizes(colour) {
    const container = document.getElementById('sizeButtons');
    if (!container) return;

    const sizes = colorVariants[colour] || {};
    // Sort by size_order
    const sorted = Object.entries(sizes).sort((a, b) => a[1].order - b[1].order);

    container.innerHTML = '';
    sorted.forEach(([size, data]) => {
        const btn = document.createElement('button');
        btn.type      = 'button';
        btn.className = 'size-btn' + (data.stock === 0 ? ' disabled' : '');
        btn.textContent = size;
        btn.dataset.size      = size;
        btn.dataset.variantId = data.id;
        btn.dataset.stock     = data.stock;
        btn.dataset.price     = data.price;
        if (data.stock > 0) {
            btn.onclick = () => selectSize(btn);
        }
        container.appendChild(btn);
    });
}

// ── Size selection ──────────────────────────────────────────────────────
function selectSize(btn) {
    document.querySelectorAll('.size-btn').forEach(b => b.classList.remove('selected'));
    btn.classList.add('selected');

    selectedSize      = btn.dataset.size;
    selectedVariantId = btn.dataset.variantId;
    const stock       = parseInt(btn.dataset.stock);
    const price       = parseFloat(btn.dataset.price);

    document.getElementById('selectedSizeLabel').textContent = selectedSize;
    document.getElementById('selectedVariantId').value = selectedVariantId;
    document.getElementById('sizeError').classList.add('d-none');

    // Update price display
    const priceEl = document.getElementById('displayPrice');
    if (priceEl) priceEl.textContent = '$' + price.toFixed(2);

    // Stock notice
    const notice = document.getElementById('stockNotice');
    if (stock > 0 && stock <= 5) {
        notice.innerHTML = `<span class="text-warning">⚠ Only ${stock} left in this size</span>`;
    } else if (stock === 0) {
        notice.innerHTML = '<span class="text-danger">Out of stock in this size</span>';
    } else {
        notice.innerHTML = '<span class="text-success">✓ In stock</span>';
    }

    // Enable add to bag
    const addBtn = document.getElementById('addToBagBtn');
    addBtn.disabled    = stock === 0;
    addBtn.textContent = stock === 0 ? 'OUT OF STOCK' : 'ADD TO BAG';

    // Update sticky bar
    document.getElementById('stickyVariantLabel').textContent =
        selectedColour + ' / ' + selectedSize;
}

function resetAddBtn() {
    const addBtn = document.getElementById('addToBagBtn');
    if (addBtn) { addBtn.disabled = true; addBtn.textContent = 'SELECT A SIZE'; }
    const notice = document.getElementById('stockNotice');
    if (notice) notice.innerHTML = '';
    document.getElementById('selectedSizeLabel').textContent = 'Select a size';
    document.getElementById('selectedVariantId').value = '';
    document.getElementById('stickyVariantLabel').textContent = selectedColour ? selectedColour + ' / Select size' : 'Select colour & size';
}

// ── Form submit guard ───────────────────────────────────────────────────
document.getElementById('addToCartForm').addEventListener('submit', function(e) {
    @if($hasVariants)
    if (!selectedVariantId) {
        e.preventDefault();
        document.getElementById('sizeError').classList.remove('d-none');
        document.getElementById('sizeButtons').scrollIntoView({behavior: 'smooth', block: 'center'});
        return;
    }
    @endif
});

// ── Sticky bar on scroll (mobile) ───────────────────────────────────────
function setupStickyBar() {
    const stickyAtc = document.getElementById('stickyAtc');
    if (!stickyAtc) return;
    const addBtn = document.getElementById('addToBagBtn');
    const observer = new IntersectionObserver(([entry]) => {
        // Show sticky bar when the main add-to-bag button scrolls out of view
        if (window.innerWidth <= 768) {
            stickyAtc.style.display = entry.isIntersecting ? 'none' : 'flex';
        }
    }, { threshold: 0 });
    if (addBtn) observer.observe(addBtn);
}

// Init: if no variants, enable the button immediately
@if(!$hasVariants)
document.getElementById('addToBagBtn').disabled = false;
document.getElementById('addToBagBtn').textContent = 'ADD TO BAG';
@else
resetAddBtn();
@endif
</script>
@endpush
@endsection
