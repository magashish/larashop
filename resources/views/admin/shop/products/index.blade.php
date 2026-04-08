@extends('layouts.app')
@section('content')
<div class="container-fluid mt-4">
    <div class="card shadow">
        <div class="card-header d-flex justify-content-between align-items-center fw-bold">
            <span><i class="bi bi-box-seam"></i> Products</span>
            <a href="{{ route('admin.shop.products.create') }}" class="btn btn-success btn-sm">
                <i class="bi bi-plus-circle"></i> Add Product
            </a>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            @endif

            {{-- Filters --}}
            <form method="GET" class="row g-2 mb-3">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Search name or SKU..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="category" class="form-select">
                        <option value="">All Categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" @selected(request('category') == $cat->id)>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="active" @selected(request('status') === 'active')>Active</option>
                        <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary w-100"><i class="bi bi-search"></i> Filter</button>
                </div>
                <div class="col-md-1">
                    <a href="{{ route('admin.shop.products.index') }}" class="btn btn-outline-secondary w-100">Reset</a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle">
                    <thead>
                        <tr>
                            <th width="60">Image</th>
                            <th>Name</th>
                            <th>SKU</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                        <tr>
                            <td>
                                @if($product->featured_image)
                                    <img src="{{ asset('storage/' . $product->featured_image) }}" width="50" height="50" class="rounded object-fit-cover">
                                @else
                                    <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width:50px;height:50px">
                                        <i class="bi bi-image text-muted"></i>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <strong>{{ $product->name }}</strong>
                                @if($product->is_featured)<span class="badge bg-warning text-dark ms-1">Featured</span>@endif
                                @if($product->is_on_sale)<span class="badge bg-danger ms-1">Sale</span>@endif
                            </td>
                            <td><code>{{ $product->sku ?? '—' }}</code></td>
                            <td>{{ $product->category?->name ?? '—' }}</td>
                            <td>
                                @if($product->is_on_sale)
                                    <span class="text-danger fw-bold">${{ number_format($product->sale_price, 2) }}</span>
                                    <small class="text-muted text-decoration-line-through">${{ number_format($product->price, 2) }}</small>
                                @else
                                    ${{ number_format($product->price, 2) }}
                                @endif
                            </td>
                            <td>
                                @if(!$product->track_stock)
                                    <span class="text-muted">∞</span>
                                @elseif($product->stock_quantity > 0)
                                    <span class="badge bg-success">{{ $product->stock_quantity }}</span>
                                @else
                                    <span class="badge bg-danger">Out of stock</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $product->is_active ? 'success' : 'secondary' }}">
                                    {{ $product->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('admin.shop.products.edit', $product) }}" class="btn btn-warning btn-sm">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('admin.shop.products.destroy', $product) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this product?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-danger btn-sm"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="8" class="text-center text-muted py-4">No products found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center mt-3">{{ $products->links() }}</div>
        </div>
    </div>
</div>
@endsection
