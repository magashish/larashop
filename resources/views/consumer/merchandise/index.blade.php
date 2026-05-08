@extends('layouts.consumer')

@push('styles')
<style>

.shop-banner-sec {
    padding: 0px !important;
}

/* ── Breadcrumb ───────────────────────────────────────────────────────────── */
.shop-breadcrumb {
    font-size: .75rem;
    letter-spacing: .08em;
    color: #888;
    text-transform: uppercase;
    padding: 1rem 0 .25rem;
}
.shop-breadcrumb a { color: #888; text-decoration: none; }
.shop-breadcrumb a:hover { color: #111; }
.shop-breadcrumb .sep { margin: 0 .4em; }

/* ── Page title ───────────────────────────────────────────────────────────── */
.shop-page-title {
    font-size: clamp(1.2rem, 3vw, 1.75rem);
    font-weight: 800;
    letter-spacing: .1em;
    text-transform: uppercase;
    text-align: center;
    margin-bottom: 2rem;
    color: #111;
}

/* ── Sidebar / Accordion ──────────────────────────────────────────────────── */
.shop-sidebar { }
.shop-sidebar .accordion-item {
    border: none;
    border-bottom: 1px solid #ddd;
    border-radius: 0 !important;
}
.shop-sidebar .accordion-button {
    background: transparent;
    color: #111;
    font-weight: 700;
    font-size: .72rem;
    letter-spacing: .12em;
    text-transform: uppercase;
    padding: .9rem 0;
    box-shadow: none !important;
}
.shop-sidebar .accordion-button::after {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23111'%3e%3cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3e%3c/svg%3e");
}
.shop-sidebar .accordion-body { padding: 0 0 1rem; }

/* Checkbox items */
.shop-filter-check {
    display: flex; align-items: center; gap: .6rem;
    padding: .3rem 0; cursor: pointer;
    font-size: .82rem; color: #333;
    text-decoration: none;
    transition: color .15s;
}
.shop-filter-check:hover { color: #111; }
.shop-filter-check .check-box {
    width: 15px; height: 15px; min-width: 15px;
    border: 1.5px solid #aaa;
    display: flex; align-items: center; justify-content: center;
    transition: all .15s;
}
.shop-filter-check.active .check-box {
    background: #111; border-color: #111;
}
.shop-filter-check.active .check-box::after {
    content: '';
    display: block; width: 5px; height: 8px;
    border: 1.5px solid #fff; border-top: none; border-left: none;
    transform: rotate(40deg) translateY(-1px);
}
.shop-filter-check.active { color: #111; font-weight: 600; }

/* Price inputs */
.shop-price-input {
    border: none; border-bottom: 1px solid #aaa;
    border-radius: 0; padding: .25rem 0;
    font-size: .82rem; width: 100%;
    outline: none;
    background: transparent;
}
.shop-price-input:focus { border-color: #111; }
.shop-price-apply {
    font-size: .72rem; letter-spacing: .08em;
    text-transform: uppercase; font-weight: 700;
    background: #111; color: #fff;
    border: none; padding: .4rem 1rem;
    cursor: pointer; transition: opacity .2s;
}
.shop-price-apply:hover { opacity: .8; }

/* Sort select */
.shop-sort-select {
    border: none; border-bottom: 1px solid #aaa;
    border-radius: 0; padding: .25rem 0;
    font-size: .82rem; width: 100%;
    background: transparent; outline: none;
    -webkit-appearance: none; appearance: none;
    cursor: pointer;
}

/* ── Product grid ─────────────────────────────────────────────────────────── */
.shop-products-header {
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: 1.25rem;
    padding-bottom: .75rem;
    border-bottom: 1px solid #eee;
}
.shop-products-count { font-size: .78rem; color: #888; letter-spacing: .06em; text-transform: uppercase; }
.shop-clear-link { font-size: .75rem; color: #888; text-decoration: underline; }

/* ── Product Card ─────────────────────────────────────────────────────────── */
.shop-card { text-decoration: none; color: inherit; display: block; }
.shop-card__img-wrap {
    position: relative;
    aspect-ratio: 1 / 1;
    overflow: hidden;
    background: #f4f4f4;
}
.shop-card__img {
    width: 100%; height: 100%;
    object-fit: cover;
    transition: transform .4s ease;
}
.shop-card:hover .shop-card__img { transform: scale(1.05); }

.shop-card__badge {
    position: absolute; top: .6rem; left: .6rem;
    background: #e00;
    color: #fff; font-size: .62rem; font-weight: 700;
    letter-spacing: .08em; text-transform: uppercase;
    padding: .2em .55em;
}
.shop-card__featured-badge {
    position: absolute; top: .6rem; right: .6rem;
    background: #FFD700; color: #111;
    font-size: .62rem; font-weight: 700;
    letter-spacing: .06em; text-transform: uppercase;
    padding: .2em .55em;
}
.shop-card__oos {
    position: absolute; inset: 0;
    background: rgba(0,0,0,.45);
    display: flex; align-items: flex-end; justify-content: center;
    padding-bottom: .75rem;
}
.shop-card__oos-label {
    background: #111; color: #fff;
    font-size: .65rem; letter-spacing: .1em;
    text-transform: uppercase; font-weight: 700;
    padding: .25em .8em;
}
.shop-card__quick-add {
    position: absolute; bottom: 0; left: 0; right: 0;
    background: #111; color: #fff;
    text-align: center; font-size: .7rem; font-weight: 700;
    letter-spacing: .1em; text-transform: uppercase;
    padding: .5rem;
    transform: translateY(100%);
    transition: transform .25s ease;
    border: none; width: 100%; cursor: pointer;
}
.shop-card:hover .shop-card__quick-add { transform: translateY(0); }

.shop-card__info { padding: .65rem 0 .25rem; }
.shop-card__name {
    font-size: .75rem; font-weight: 700;
    letter-spacing: .06em; text-transform: uppercase;
    color: #111; margin-bottom: .2rem;
    line-height: 1.3;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.shop-card__price { font-size: .8rem; color: #444; }
.shop-card__price-sale { color: #cc0000; font-weight: 700; }
.shop-card__price-orig { text-decoration: line-through; color: #aaa; margin-left: .3em; font-size: .73rem; }

/* Colour swatch dot */
.colour-swatch {
    display: inline-block;
    width: 12px; height: 12px; min-width: 12px;
    border-radius: 50%;
    border: 1px solid rgba(0,0,0,.15);
    vertical-align: middle;
}

/* Size pills grid */
.size-grid {
    display: flex; flex-wrap: wrap; gap: .35rem;
}
.size-pill {
    display: inline-flex; align-items: center; justify-content: center;
    min-width: 34px; height: 30px; padding: 0 .4rem;
    border: 1.5px solid #ccc;
    font-size: .68rem; font-weight: 600; letter-spacing: .04em;
    text-decoration: none; color: #333;
    transition: all .15s;
}
.size-pill:hover { border-color: #111; color: #111; }
.size-pill.active { background: #111; border-color: #111; color: #fff; }

/* Filter clear link */
.shop-filter-clear {
    font-size: .68rem; color: #aaa; text-decoration: none;
    letter-spacing: .04em; margin-top: .5rem; display: inline-block;
}
.shop-filter-clear:hover { color: #111; }

/* "Select Options" hover label (variant products) */
.shop-card__quick-add--link {
    pointer-events: none;
}

/* Mobile adjustments */
@media (max-width: 575px) {
    .shop-hero { height: 220px; }
    .shop-hero__title-strip { font-size: 1.4rem; padding-right: 2em; }
    .shop-card__name { font-size: .7rem; }
}
</style>
@endpush

@section('content')

<!-- Page Banner Sec Start -->
<section class="image-banner-sec shop-banner-sec pb-0">
    <div class="container">
        <div class="inner-content">
            <img src="{{ asset('images/shop.webp') }}"alt="Banner Image">
        </div>
        <div class="bg-title" style="max-width: fit-content;">
            <div class="yellow-bg text-uppercase">GEAR UP WITH</div>
            <div class="black-bg text-uppercase d-block"><h1>XHALE MERCH</h1></div>
        </div>
    </div>
</section>

<div class="container">

    {{-- ── BREADCRUMB ───────────────────────────────────────────────────────── --}}
    <nav class="shop-breadcrumb">
        <a href="{{ route('consumer') }}">Home</a>
        <span class="sep">/</span>
        <a href="{{ route('shop.merchandise.index') }}">Merch</a>
        @if(request('category'))
            @php $activeCat = $categories->firstWhere('slug', request('category')); @endphp
            @if($activeCat)
            <span class="sep">/</span>
            <span>{{ $activeCat->name }}</span>
            @endif
        @endif
    </nav>

    {{-- ── PAGE TITLE ───────────────────────────────────────────────────────── --}}
    <h1 class="shop-page-title">
        @if(request('search'))
            Search: "{{ request('search') }}"
        @elseif(isset($activeCat))
            {{ $activeCat->name }}
        @else
            All Merch
        @endif
    </h1>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-3">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-3">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    <div class="row g-5">

        {{-- ── SIDEBAR ───────────────────────────────────────────────────────── --}}
        <div class="col-lg-2 col-md-3">
            <div class="shop-sidebar">
                <form method="GET" action="{{ route('shop.merchandise.index') }}" id="shopFilterForm">

                    {{-- Search (hidden input, triggered from navbar or top bar) --}}
                    @if(request('search'))
                        <input type="hidden" name="search" value="{{ request('search') }}">
                    @endif
                    @if(request('sort'))
                        <input type="hidden" name="sort" id="sortHidden" value="{{ request('sort') }}">
                    @endif

                    <div class="accordion accordion-flush" id="sidebarAccordion">

                        {{-- Product Type (Categories) --}}
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="hType">
                                <button class="accordion-button {{ request('category') || true ? '' : 'collapsed' }}"
                                        type="button" data-bs-toggle="collapse" data-bs-target="#colType">
                                    Product Type
                                </button>
                            </h2>
                            <div id="colType" class="accordion-collapse collapse show" data-bs-parent="#sidebarAccordion">
                                <div class="accordion-body">
                                    <a href="{{ route('shop.merchandise.index', array_merge(request()->except(['category','page']), [])) }}"
                                       class="shop-filter-check {{ !request('category') ? 'active' : '' }}">
                                        <span class="check-box"></span> All
                                    </a>
                                    @foreach($categories as $cat)
                                    <a href="{{ route('shop.merchandise.index', array_merge(request()->except(['category','page']), ['category' => $cat->slug])) }}"
                                       class="shop-filter-check {{ request('category') === $cat->slug ? 'active' : '' }}">
                                        <span class="check-box"></span> {{ $cat->name }}
                                    </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        {{-- Gender --}}
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="hGender">
                                <button class="accordion-button {{ request('gender') ? '' : 'collapsed' }}"
                                        type="button" data-bs-toggle="collapse" data-bs-target="#colGender">
                                    Gender
                                </button>
                            </h2>
                            <div id="colGender" class="accordion-collapse collapse {{ request('gender') ? 'show' : '' }}">
                                <div class="accordion-body">
                                    @php $genders = ['mens' => 'Mens', 'womens' => 'Womens', 'unisex' => 'Unisex', 'kids' => 'Kids']; @endphp
                                    @foreach($genders as $val => $label)
                                    <a href="{{ route('shop.merchandise.index', array_merge(request()->except(['gender','page']), ['gender' => $val])) }}"
                                       class="shop-filter-check {{ request('gender') === $val ? 'active' : '' }}">
                                        <span class="check-box"></span> {{ $label }}
                                    </a>
                                    @endforeach
                                    @if(request('gender'))
                                    <a href="{{ route('shop.merchandise.index', request()->except(['gender','page'])) }}"
                                       class="shop-filter-clear">✕ Clear</a>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Colour Range --}}
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="hColour">
                                <button class="accordion-button {{ request('colour') ? '' : 'collapsed' }}"
                                        type="button" data-bs-toggle="collapse" data-bs-target="#colColour">
                                    Colour Range
                                </button>
                            </h2>
                            <div id="colColour" class="accordion-collapse collapse {{ request('colour') ? 'show' : '' }}">
                                <div class="accordion-body">
                                    @forelse($availableColours as $colour)
                                    <a href="{{ route('shop.merchandise.index', array_merge(request()->except(['colour','page']), ['colour' => $colour->color_name])) }}"
                                       class="shop-filter-check {{ request('colour') === $colour->color_name ? 'active' : '' }}">
                                        <span class="check-box"></span>
                                        @if($colour->color_hex)
                                        <span class="colour-swatch" style="background:{{ $colour->color_hex }};"></span>
                                        @endif
                                        {{ $colour->color_name }}
                                    </a>
                                    @empty
                                    <span style="font-size:.75rem;color:#aaa;">No colours available</span>
                                    @endforelse
                                    @if(request('colour'))
                                    <a href="{{ route('shop.merchandise.index', request()->except(['colour','page'])) }}"
                                       class="shop-filter-clear">✕ Clear</a>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Size --}}
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="hSize">
                                <button class="accordion-button {{ request('size') ? '' : 'collapsed' }}"
                                        type="button" data-bs-toggle="collapse" data-bs-target="#colSize">
                                    Size
                                </button>
                            </h2>
                            <div id="colSize" class="accordion-collapse collapse {{ request('size') ? 'show' : '' }}">
                                <div class="accordion-body">
                                    <div class="size-grid">
                                        @forelse($availableSizes as $size)
                                        <a href="{{ route('shop.merchandise.index', array_merge(request()->except(['size','page']), ['size' => $size])) }}"
                                           class="size-pill {{ request('size') === $size ? 'active' : '' }}">
                                            {{ $size }}
                                        </a>
                                        @empty
                                        <span style="font-size:.75rem;color:#aaa;">No sizes available</span>
                                        @endforelse
                                    </div>
                                    @if(request('size'))
                                    <a href="{{ route('shop.merchandise.index', request()->except(['size','page'])) }}"
                                       class="shop-filter-clear mt-2 d-block">✕ Clear</a>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Price Range --}}
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="hPrice">
                                <button class="accordion-button {{ (request('min_price') || request('max_price')) ? '' : 'collapsed' }}"
                                        type="button" data-bs-toggle="collapse" data-bs-target="#colPrice">
                                    Price Range
                                </button>
                            </h2>
                            <div id="colPrice" class="accordion-collapse collapse {{ (request('min_price') || request('max_price')) ? 'show' : '' }}">
                                <div class="accordion-body">
                                    <div class="d-flex gap-2 mb-2">
                                        <div style="flex:1;">
                                            <label style="font-size:.65rem;color:#888;letter-spacing:.06em;text-transform:uppercase;">Min</label>
                                            <input type="number" name="min_price" class="shop-price-input"
                                                   placeholder="$0" value="{{ request('min_price') }}" min="0">
                                        </div>
                                        <div style="flex:1;">
                                            <label style="font-size:.65rem;color:#888;letter-spacing:.06em;text-transform:uppercase;">Max</label>
                                            <input type="number" name="max_price" class="shop-price-input"
                                                   placeholder="Any" value="{{ request('max_price') }}" min="0">
                                        </div>
                                    </div>
                                    <button type="submit" class="shop-price-apply mt-2">Apply</button>
                                </div>
                            </div>
                        </div>

                        {{-- Sort --}}
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="hSort">
                                <button class="accordion-button collapsed"
                                        type="button" data-bs-toggle="collapse" data-bs-target="#colSort">
                                    Sort By
                                </button>
                            </h2>
                            <div id="colSort" class="accordion-collapse collapse">
                                <div class="accordion-body">
                                    @php $sorts = ['latest'=>'Latest','price_asc'=>'Price: Low–High','price_desc'=>'Price: High–Low','name'=>'Name A–Z','featured'=>'Featured First']; @endphp
                                    @foreach($sorts as $val => $label)
                                    <a href="{{ route('shop.merchandise.index', array_merge(request()->except(['sort','page']), ['sort' => $val])) }}"
                                       class="shop-filter-check {{ request('sort','latest') === $val ? 'active' : '' }}">
                                        <span class="check-box"></span> {{ $label }}
                                    </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                    </div>{{-- /accordion --}}

                    @if(request()->except(['page']))
                    <div class="mt-3">
                        <a href="{{ route('shop.merchandise.index') }}"
                           style="font-size:.72rem;letter-spacing:.08em;text-transform:uppercase;color:#888;text-decoration:underline;">
                            Clear All Filters
                        </a>
                    </div>
                    @endif

                </form>
            </div>
        </div>

        {{-- ── PRODUCT GRID ──────────────────────────────────────────────────── --}}
        <div class="col-lg-10 col-md-9">

            <div class="shop-products-header">
                <span class="shop-products-count">{{ $products->total() }} product{{ $products->total() !== 1 ? 's' : '' }}</span>
                @if(request()->except(['page']))
                    <a href="{{ route('shop.merchandise.index') }}" class="shop-clear-link">Clear filters</a>
                @endif
            </div>

            @if($products->isEmpty())
                <div class="text-center py-5">
                    <p style="font-size:.85rem;color:#aaa;letter-spacing:.08em;text-transform:uppercase;">No products found</p>
                    <a href="{{ route('shop.merchandise.index') }}" style="font-size:.8rem;color:#111;text-decoration:underline;">Browse all products</a>
                </div>
            @else
            <div class="row g-3">
                @foreach($products as $product)
                <div class="col-6 col-sm-4 col-md-3">
                    @include('consumer.merchandise._product-card', ['product' => $product])
                </div>
                @endforeach
            </div>

            <div class="d-flex justify-content-center mt-5">
                {{ $products->links() }}
            </div>
            @endif

        </div>
    </div>

</div>{{-- /container --}}

<div style="margin-bottom: 3rem;"></div>


@include('consumer.blocks.social-feeds')


@endsection
