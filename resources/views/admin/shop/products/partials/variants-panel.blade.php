<div class="card shadow mb-4" id="variantsPanel">
    <div class="card-header fw-bold d-flex justify-content-between align-items-center">
        <span><i class="bi bi-grid-3x3-gap"></i> Colours & Sizes (Variants)</span>
        <button type="button" class="btn btn-success btn-sm" onclick="addVariantRow()">
            <i class="bi bi-plus-circle"></i> Add Row
        </button>
    </div>
    <div class="card-body">
        <p class="text-muted small mb-3">
            Each row = one Colour + Size combination. Stock is tracked per variant.
            Leave price adjustment at 0 unless this variant costs more/less.
        </p>

        {{-- Colour Images --}}
        <div class="mb-4">
            <h6 class="fw-semibold mb-2">Colour-Specific Image Galleries</h6>
            <p class="text-muted small">Upload images per colour. These swap when a customer selects that colour.</p>
            <div id="colorImagesContainer">
                @if(isset($product))
                    @foreach($product->colorImages->groupBy('color_name') as $colorName => $images)
                    <div class="color-image-group border rounded p-3 mb-3">
                        <div class="d-flex align-items-center gap-3 mb-2">
                            <input type="text" name="color_image_groups[{{ $loop->index }}][color_name]"
                                   class="form-control form-control-sm" style="max-width:180px"
                                   value="{{ $colorName }}" placeholder="Colour name" form="{{ $formId ?? 'productForm' }}">
                            <input type="file" name="color_image_groups[{{ $loop->index }}][images][]"
                                   class="form-control form-control-sm" accept="image/*" multiple form="{{ $formId ?? 'productForm' }}">
                            <button type="button" class="btn btn-outline-danger btn-sm" onclick="this.closest('.color-image-group').remove()">×</button>
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($images as $img)
                            <div class="position-relative">
                                <img src="{{ asset('storage/' . $img->image_path) }}" class="rounded" style="width:70px;height:70px;object-fit:cover;">
                                <form action="{{ route('admin.shop.products.delete-color-image', $img) }}" method="POST" class="position-absolute top-0 end-0 m-0">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-danger btn-sm p-0 px-1" style="font-size:10px" onclick="return confirm('Remove?')">×</button>
                                </form>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                @endif
            </div>
            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="addColorImageGroup()">
                <i class="bi bi-plus"></i> Add Colour Image Group
            </button>
        </div>

        <hr>

        {{-- Variant rows table --}}
        <div class="table-responsive">
            <table class="table table-bordered align-middle" id="variantsTable">
                <thead class="table-light">
                    <tr>
                        <th>Colour Name</th>
                        <th style="width:90px">Hex</th>
                        <th style="width:100px">Size</th>
                        <th style="width:70px">Sort #</th>
                        <th>SKU</th>
                        <th style="width:90px">Stock</th>
                        <th style="width:100px">+/- Price</th>
                        <th style="width:60px">Active</th>
                        <th style="width:40px"></th>
                    </tr>
                </thead>
                <tbody id="variantRows">
                    @if(isset($product))
                        @foreach($product->variants as $i => $variant)
                        <tr data-row="{{ $i }}">
                            <td><input type="hidden" name="variants[{{ $i }}][id]" value="{{ $variant->id }}">
                                <input type="text" name="variants[{{ $i }}][color_name]" class="form-control form-control-sm" value="{{ $variant->color_name }}" placeholder="Black Stone" required form="{{ $formId ?? 'productForm' }}"></td>
                            <td><input type="color" name="variants[{{ $i }}][color_hex]" class="form-control form-control-sm p-1" value="{{ $variant->color_hex ?? '#000000' }}" form="{{ $formId ?? 'productForm' }}" style="height:34px"></td>
                            <td><input type="text" name="variants[{{ $i }}][size]" class="form-control form-control-sm" value="{{ $variant->size }}" placeholder="M" required form="{{ $formId ?? 'productForm' }}"></td>
                            <td><input type="number" name="variants[{{ $i }}][size_order]" class="form-control form-control-sm" value="{{ $variant->size_order }}" min="0" form="{{ $formId ?? 'productForm' }}"></td>
                            <td><input type="text" name="variants[{{ $i }}][sku]" class="form-control form-control-sm" value="{{ $variant->sku }}" form="{{ $formId ?? 'productForm' }}"></td>
                            <td><input type="number" name="variants[{{ $i }}][stock_quantity]" class="form-control form-control-sm" value="{{ $variant->stock_quantity }}" min="0" form="{{ $formId ?? 'productForm' }}"></td>
                            <td><input type="number" name="variants[{{ $i }}][price_adjustment]" class="form-control form-control-sm" value="{{ $variant->price_adjustment }}" step="0.01" form="{{ $formId ?? 'productForm' }}"></td>
                            <td class="text-center"><input type="checkbox" name="variants[{{ $i }}][is_active]" value="1" class="form-check-input" @checked($variant->is_active) form="{{ $formId ?? 'productForm' }}"></td>
                            <td><button type="button" class="btn btn-outline-danger btn-sm" onclick="removeVariantRow(this)">×</button></td>
                        </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>

        <div class="mt-2">
            <button type="button" class="btn btn-outline-primary btn-sm" onclick="bulkAddSizes()">
                <i class="bi bi-lightning"></i> Quick-add standard sizes for a colour
            </button>
        </div>

        {{-- Quick-add modal --}}
        <div class="modal fade" id="bulkSizeModal" tabindex="-1">
            <div class="modal-dialog modal-sm">
                <div class="modal-content">
                    <div class="modal-header"><h6 class="modal-title">Quick Add Sizes</h6><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                    <div class="modal-body">
                        <div class="mb-2">
                            <label class="form-label small fw-semibold">Colour Name</label>
                            <input type="text" id="bulkColorName" class="form-control form-control-sm" placeholder="e.g. Black Stone">
                        </div>
                        <div class="mb-2">
                            <label class="form-label small fw-semibold">Colour Hex</label>
                            <input type="color" id="bulkColorHex" class="form-control form-control-sm p-1" style="height:34px" value="#000000">
                        </div>
                        <div class="mb-2">
                            <label class="form-label small fw-semibold">Sizes to add</label>
                            <div class="d-flex flex-wrap gap-2 mt-1" id="bulkSizeCheckboxes">
                                @foreach(['XS'=>0,'S'=>1,'M'=>2,'L'=>3,'XL'=>4,'2XL'=>5,'3XL'=>6] as $sz => $order)
                                <div class="form-check">
                                    <input class="form-check-input bulk-size-cb" type="checkbox" value="{{ $sz }}" data-order="{{ $order }}" id="bs_{{ $sz }}" checked>
                                    <label class="form-check-label small" for="bs_{{ $sz }}">{{ $sz }}</label>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="mb-2">
                            <label class="form-label small fw-semibold">Default Stock per Size</label>
                            <input type="number" id="bulkStock" class="form-control form-control-sm" value="10" min="0">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success btn-sm" onclick="confirmBulkAdd()">Add Rows</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let variantRowIndex = {{ isset($product) ? $product->variants->count() : 0 }};
let colorImageGroupIndex = {{ isset($product) ? $product->colorImages->groupBy('color_name')->count() : 0 }};
const formId = '{{ $formId ?? "productForm" }}';

function addVariantRow() {
    const i = variantRowIndex++;
    const row = `<tr data-row="${i}">
        <td><input type="text" name="variants[${i}][color_name]" class="form-control form-control-sm" placeholder="Black Stone" required form="${formId}"></td>
        <td><input type="color" name="variants[${i}][color_hex]" class="form-control form-control-sm p-1" value="#000000" form="${formId}" style="height:34px"></td>
        <td><input type="text" name="variants[${i}][size]" class="form-control form-control-sm" placeholder="M" required form="${formId}"></td>
        <td><input type="number" name="variants[${i}][size_order]" class="form-control form-control-sm" value="0" min="0" form="${formId}"></td>
        <td><input type="text" name="variants[${i}][sku]" class="form-control form-control-sm" form="${formId}"></td>
        <td><input type="number" name="variants[${i}][stock_quantity]" class="form-control form-control-sm" value="0" min="0" form="${formId}"></td>
        <td><input type="number" name="variants[${i}][price_adjustment]" class="form-control form-control-sm" value="0" step="0.01" form="${formId}"></td>
        <td class="text-center"><input type="checkbox" name="variants[${i}][is_active]" value="1" class="form-check-input" checked form="${formId}"></td>
        <td><button type="button" class="btn btn-outline-danger btn-sm" onclick="removeVariantRow(this)">×</button></td>
    </tr>`;
    document.getElementById('variantRows').insertAdjacentHTML('beforeend', row);
}

function removeVariantRow(btn) {
    btn.closest('tr').remove();
}

function addColorImageGroup() {
    const i = colorImageGroupIndex++;
    const html = `<div class="color-image-group border rounded p-3 mb-3">
        <div class="d-flex align-items-center gap-3 mb-2">
            <input type="text" name="color_image_groups[${i}][color_name]" class="form-control form-control-sm" style="max-width:180px" placeholder="Colour name" form="${formId}">
            <input type="file" name="color_image_groups[${i}][images][]" class="form-control form-control-sm" accept="image/*" multiple form="${formId}">
            <button type="button" class="btn btn-outline-danger btn-sm" onclick="this.closest('.color-image-group').remove()">×</button>
        </div>
    </div>`;
    document.getElementById('colorImagesContainer').insertAdjacentHTML('beforeend', html);
}

function bulkAddSizes() {
    new bootstrap.Modal(document.getElementById('bulkSizeModal')).show();
}

function confirmBulkAdd() {
    const colorName = document.getElementById('bulkColorName').value.trim();
    const colorHex  = document.getElementById('bulkColorHex').value;
    const stock     = document.getElementById('bulkStock').value;
    if (!colorName) { alert('Please enter a colour name.'); return; }

    document.querySelectorAll('.bulk-size-cb:checked').forEach(cb => {
        const i = variantRowIndex++;
        const row = `<tr data-row="${i}">
            <td><input type="text" name="variants[${i}][color_name]" class="form-control form-control-sm" value="${colorName}" required form="${formId}"></td>
            <td><input type="color" name="variants[${i}][color_hex]" class="form-control form-control-sm p-1" value="${colorHex}" form="${formId}" style="height:34px"></td>
            <td><input type="text" name="variants[${i}][size]" class="form-control form-control-sm" value="${cb.value}" required form="${formId}"></td>
            <td><input type="number" name="variants[${i}][size_order]" class="form-control form-control-sm" value="${cb.dataset.order}" min="0" form="${formId}"></td>
            <td><input type="text" name="variants[${i}][sku]" class="form-control form-control-sm" form="${formId}"></td>
            <td><input type="number" name="variants[${i}][stock_quantity]" class="form-control form-control-sm" value="${stock}" min="0" form="${formId}"></td>
            <td><input type="number" name="variants[${i}][price_adjustment]" class="form-control form-control-sm" value="0" step="0.01" form="${formId}"></td>
            <td class="text-center"><input type="checkbox" name="variants[${i}][is_active]" value="1" class="form-check-input" checked form="${formId}"></td>
            <td><button type="button" class="btn btn-outline-danger btn-sm" onclick="removeVariantRow(this)">×</button></td>
        </tr>`;
        document.getElementById('variantRows').insertAdjacentHTML('beforeend', row);
    });

    bootstrap.Modal.getInstance(document.getElementById('bulkSizeModal')).hide();
}
</script>
