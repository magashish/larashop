@extends('layouts.app')
@section('content')
<div class="container-fluid mt-4">

    <form action="{{ route('admin.shop.products.update', $product) }}" method="POST" enctype="multipart/form-data" id="productForm">
    @csrf @method('PUT')

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header fw-bold">
                    <i class="bi bi-box-seam"></i> Edit Product: {{ $product->name }}
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
                    @endif
                    @if($errors->any())
                        <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
                    @endif

                    <h6 class="text-muted mb-3 border-bottom pb-2">Basic Information</h6>
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Product Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $product->name) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Category</label>
                            <select name="shop_category_id" class="form-select">
                                <option value="">— Uncategorised —</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" @selected(old('shop_category_id', $product->shop_category_id) == $cat->id)>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">SKU</label>
                            <input type="text" name="sku" class="form-control" value="{{ old('sku', $product->sku) }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Short Description</label>
                            <textarea name="short_description" class="form-control" rows="2">{{ old('short_description', $product->short_description) }}</textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Full Description</label>
                            <textarea name="description" class="form-control" rows="6">{{ old('description', $product->description) }}</textarea>
                        </div>
                    </div>

                    <h6 class="text-muted mb-3 mt-4 border-bottom pb-2">Pricing</h6>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Price <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" name="price" class="form-control" value="{{ old('price', $product->price) }}" step="0.01" min="0" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Sale Price</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" name="sale_price" class="form-control" value="{{ old('sale_price', $product->sale_price) }}" step="0.01" min="0">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Cost Price</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" name="cost_price" class="form-control" value="{{ old('cost_price', $product->cost_price) }}" step="0.01" min="0">
                            </div>
                        </div>
                    </div>

                    <h6 class="text-muted mb-3 mt-4 border-bottom pb-2">Inventory & Shipping</h6>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Stock Quantity</label>
                            <input type="number" name="stock_quantity" class="form-control" value="{{ old('stock_quantity', $product->stock_quantity) }}" min="0">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Weight (kg)</label>
                            <input type="number" name="weight" class="form-control" value="{{ old('weight', $product->weight) }}" step="0.001" min="0">
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="track_stock" value="1" id="track_stock" @checked(old('track_stock', $product->track_stock))>
                                <label class="form-check-label" for="track_stock">Track Stock</label>
                            </div>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="allow_backorders" value="1" id="allow_backorders" @checked(old('allow_backorders', $product->allow_backorders))>
                                <label class="form-check-label" for="allow_backorders">Backorders</label>
                            </div>
                        </div>
                    </div>

                    <hr>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-warning"><i class="bi bi-save"></i> Update Product</button>
                        <a href="{{ route('admin.shop.products.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow mb-3">
                <div class="card-header fw-semibold">Product Status</div>
                <div class="card-body">
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" @checked(old('is_active', $product->is_active))>
                        <label class="form-check-label" for="is_active">Active (visible in shop)</label>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_featured" value="1" id="is_featured" @checked(old('is_featured', $product->is_featured))>
                        <label class="form-check-label" for="is_featured">Featured Product</label>
                    </div>
                </div>
            </div>

            <div class="card shadow mb-3">
                <div class="card-header fw-semibold">Featured Image</div>
                <div class="card-body">
                    @if($product->featured_image)
                        <img src="{{ asset('storage/' . $product->featured_image) }}" class="img-fluid rounded mb-2">
                    @endif
                    <input type="file" name="featured_image" class="form-control" accept="image/*">
                </div>
            </div>

            <div class="card shadow mb-3">
                <div class="card-header fw-semibold">Gallery Images</div>
                <div class="card-body">
                    @if($product->images->count())
                        <div class="row g-2 mb-2">
                            @foreach($product->images as $image)
                            <div class="col-4 position-relative">
                                <img src="{{ asset('storage/' . $image->image_path) }}" class="img-fluid rounded">
                                <button type="button"
                                        class="btn btn-danger btn-sm p-0 px-1 position-absolute top-0 end-0 m-1"
                                        onclick="shopDeleteItem('{{ route('admin.shop.products.delete-image', $image) }}')">×</button>
                            </div>
                            @endforeach
                        </div>
                    @endif
                    <input type="file" name="images[]" class="form-control" accept="image/*" multiple>
                </div>
            </div>
        </div>
    </div>

    {{-- Variants Panel (full width — inside the form) --}}
    @php $formId = 'productForm'; @endphp
    @include('admin.shop.products.partials.variants-panel')

    </form>

</div>

@push('scripts')
<script>
function shopDeleteItem(url) {
    if (!confirm('Remove this?')) return;
    const f = document.createElement('form');
    f.method = 'POST';
    f.action = url;
    f.innerHTML = '<input name="_token" value="{{ csrf_token() }}"><input name="_method" value="DELETE">';
    document.body.appendChild(f);
    f.submit();
}
</script>
@endpush
@endsection
