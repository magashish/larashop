@extends('layouts.consumer')
@section('content')

<section class="image-banner-sec shopfilter-banner">
    <div class="container">
        <div class="inner-content">
            <div class="bg-overlay" style="background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%); min-height: 200px; display:flex; align-items:center; justify-content:center; border-radius: 12px;">
                <div class="text-center text-white py-5">
                    <h1 class="fw-bold display-5 mb-2">MERCHANDISE SHOP</h1>
                    <p class="lead mb-0 opacity-75">Premium gear & apparel</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif

        <div class="row">
            {{-- Sidebar Filters --}}
            <div class="col-lg-3 mb-4">
                <div class="card shadow-sm border-0 rounded-3">
                    <div class="card-body">
                        <form method="GET" action="{{ route('shop.merchandise.index') }}" id="shopFilter">
                            <h6 class="fw-bold text-uppercase mb-3">Filter Products</h6>

                            <div class="mb-3">
                                <input type="text" name="search" class="form-control" placeholder="Search products..." value="{{ request('search') }}">
                            </div>

                            <h6 class="fw-semibold mb-2 small text-uppercase text-muted">Categories</h6>
                            <ul class="list-unstyled mb-3">
                                <li class="mb-1">
                                    <a href="{{ route('shop.merchandise.index', array_merge(request()->except('category'), [])) }}"
                                       class="text-decoration-none {{ !request('category') ? 'fw-bold text-dark' : 'text-secondary' }}">
                                        All Products <span class="text-muted small">({{ $products->total() }})</span>
                                    </a>
                                </li>
                                @foreach($categories as $cat)
                                <li class="mb-1">
                                    <a href="{{ route('shop.merchandise.index', array_merge(request()->except('category'), ['category' => $cat->slug])) }}"
                                       class="text-decoration-none {{ request('category') === $cat->slug ? 'fw-bold text-dark' : 'text-secondary' }}">
                                        {{ $cat->name }} <span class="text-muted small">({{ $cat->products_count }})</span>
                                    </a>
                                </li>
                                @endforeach
                            </ul>

                            <h6 class="fw-semibold mb-2 small text-uppercase text-muted">Price Range</h6>
                            <div class="row g-1 mb-3">
                                <div class="col-6">
                                    <input type="number" name="min_price" class="form-control form-control-sm" placeholder="Min $" value="{{ request('min_price') }}" min="0">
                                </div>
                                <div class="col-6">
                                    <input type="number" name="max_price" class="form-control form-control-sm" placeholder="Max $" value="{{ request('max_price') }}" min="0">
                                </div>
                            </div>

                            <h6 class="fw-semibold mb-2 small text-uppercase text-muted">Sort By</h6>
                            <select name="sort" class="form-select form-select-sm mb-3">
                                <option value="latest" @selected(request('sort','latest') === 'latest')>Latest</option>
                                <option value="price_asc" @selected(request('sort') === 'price_asc')>Price: Low to High</option>
                                <option value="price_desc" @selected(request('sort') === 'price_desc')>Price: High to Low</option>
                                <option value="name" @selected(request('sort') === 'name')>Name A-Z</option>
                                <option value="featured" @selected(request('sort') === 'featured')>Featured First</option>
                            </select>

                            <button type="submit" class="btn btn-dark w-100 btn-sm">Apply Filters</button>
                            @if(request()->except(['page']))
                                <a href="{{ route('shop.merchandise.index') }}" class="btn btn-outline-secondary w-100 btn-sm mt-2">Clear Filters</a>
                            @endif
                        </form>
                    </div>
                </div>
            </div>

            {{-- Product Grid --}}
            <div class="col-lg-9">
                @if($featuredProducts->isNotEmpty() && !request()->except(['page']))
                <div class="mb-4">
                    <h6 class="fw-bold text-uppercase text-muted mb-3">Featured Products</h6>
                    <div class="row g-3">
                        @foreach($featuredProducts as $fp)
                        <div class="col-md-3 col-6">
                            @include('consumer.merchandise._product-card', ['product' => $fp])
                        </div>
                        @endforeach
                    </div>
                    <hr class="my-4">
                </div>
                @endif

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted small">{{ $products->total() }} product(s)</span>
                </div>

                @if($products->isEmpty())
                    <div class="text-center py-5">
                        <i class="bi bi-bag-x fs-1 text-muted"></i>
                        <p class="mt-3 text-muted">No products found. <a href="{{ route('shop.merchandise.index') }}">Browse all products</a></p>
                    </div>
                @else
                <div class="row g-3">
                    @foreach($products as $product)
                    <div class="col-md-4 col-6">
                        @include('consumer.merchandise._product-card', ['product' => $product])
                    </div>
                    @endforeach
                </div>
                <div class="d-flex justify-content-center mt-4">{{ $products->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</section>

@endsection
