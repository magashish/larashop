@extends('layouts.app')
@section('content')
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header fw-bold"><i class="bi bi-box-seam"></i> Add New Product</div>
                <div class="card-body">
                    <form action="{{ route('admin.shop.products.store') }}" method="POST" enctype="multipart/form-data" id="productForm">
                        @csrf

                        <h6 class="text-muted mb-3 border-bottom pb-2">Basic Information</h6>
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold">Product Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Category</label>
                                <select name="shop_category_id" class="form-select">
                                    <option value="">— Uncategorised —</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}" @selected(old('shop_category_id') == $cat->id)>{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">SKU</label>
                                <input type="text" name="sku" class="form-control @error('sku') is-invalid @enderror" value="{{ old('sku') }}" placeholder="Auto-generated if empty">
                                @error('sku')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Short Description</label>
                                <textarea name="short_description" class="form-control" rows="2" maxlength="500">{{ old('short_description') }}</textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Full Description</label>
                                <textarea name="description" class="form-control" rows="6">{{ old('description') }}</textarea>
                            </div>
                        </div>

                        <h6 class="text-muted mb-3 mt-4 border-bottom pb-2">Pricing</h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Price <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" name="price" class="form-control @error('price') is-invalid @enderror" value="{{ old('price') }}" step="0.01" min="0" required>
                                </div>
                                @error('price')<div class="text-danger small">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Sale Price</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" name="sale_price" class="form-control" value="{{ old('sale_price') }}" step="0.01" min="0" placeholder="Leave empty for no sale">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Cost Price</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" name="cost_price" class="form-control" value="{{ old('cost_price') }}" step="0.01" min="0">
                                </div>
                            </div>
                        </div>

                        <h6 class="text-muted mb-3 mt-4 border-bottom pb-2">Inventory & Shipping</h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Stock Quantity</label>
                                <input type="number" name="stock_quantity" class="form-control" value="{{ old('stock_quantity', 0) }}" min="0">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Weight (kg)</label>
                                <input type="number" name="weight" class="form-control" value="{{ old('weight') }}" step="0.001" min="0" placeholder="Optional">
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="track_stock" value="1" id="track_stock" @checked(old('track_stock', true))>
                                    <label class="form-check-label" for="track_stock">Track Stock</label>
                                </div>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="allow_backorders" value="1" id="allow_backorders" @checked(old('allow_backorders'))>
                                    <label class="form-check-label" for="allow_backorders">Backorders</label>
                                </div>
                            </div>
                        </div>

                        <hr>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success"><i class="bi bi-check-lg"></i> Create Product</button>
                            <a href="{{ route('admin.shop.products.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="col-md-4">
            <div class="card shadow mb-3">
                <div class="card-header fw-semibold">Product Status</div>
                <div class="card-body">
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" form="productForm" id="is_active" @checked(old('is_active', true))>
                        <label class="form-check-label" for="is_active">Active (visible in shop)</label>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_featured" value="1" form="productForm" id="is_featured" @checked(old('is_featured'))>
                        <label class="form-check-label" for="is_featured">Featured Product</label>
                    </div>
                </div>
            </div>

            <div class="card shadow mb-3">
                <div class="card-header fw-semibold">Featured Image</div>
                <div class="card-body">
                    <input type="file" name="featured_image" class="form-control" accept="image/*" form="productForm" id="featuredImageInput">
                    <div class="mt-2" id="featuredPreview"></div>
                </div>
            </div>

            <div class="card shadow">
                <div class="card-header fw-semibold">Gallery Images</div>
                <div class="card-body">
                    <input type="file" name="images[]" class="form-control" accept="image/*" multiple form="productForm">
                    <small class="text-muted">Select multiple images</small>
                </div>
            </div>
        </div>
    </div>

    @php $formId = 'productForm'; @endphp
    @include('admin.shop.products.partials.variants-panel')

</div>
@push('scripts')
<script>
document.getElementById('featuredImageInput').addEventListener('change', function(e) {
    const preview = document.getElementById('featuredPreview');
    preview.innerHTML = '';
    if (e.target.files[0]) {
        const img = document.createElement('img');
        img.src = URL.createObjectURL(e.target.files[0]);
        img.className = 'img-fluid rounded';
        preview.appendChild(img);
    }
});
</script>
@endpush
@endsection
