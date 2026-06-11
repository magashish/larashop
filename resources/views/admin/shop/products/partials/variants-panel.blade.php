<div class="card shadow mb-4" id="variantsPanel">
    <div class="card-header fw-bold d-flex justify-content-between align-items-center">
        <span><i class="bi bi-grid-3x3-gap"></i> Colours & Sizes (Variants)</span>
        <button type="button" class="btn btn-success btn-sm" onclick="addVariantRow()">
            <i class="bi bi-plus-circle"></i> Add Row
        </button>
    </div>
    <div class="card-body">
        {{-- Sentinels: tell the controller the panel was present on submit --}}
        <input type="hidden" name="variants_managed" value="1">
        <input type="hidden" name="color_management_active" value="1">
            Each row = one Colour + Size combination. Stock is tracked per variant.
            Leave price adjustment at 0 unless this variant costs more/less.
        </p>

        {{-- Colour Images --}}
        @php
            // Merge: one group per unique variant colour (with or without uploaded images)
            $existingColorImages = isset($product)
                ? $product->colorImages->groupBy('color_name')
                : collect();
            $variantColors = isset($product)
                ? $product->variants->pluck('color_name')->unique()->filter()->values()
                : collect();
            $allColorGroups = collect();
            foreach ($variantColors as $cn) {
                $allColorGroups->push(['name' => $cn, 'images' => $existingColorImages->get($cn, collect())]);
            }
            // Include any orphaned colour-image groups not tied to a variant colour
            foreach ($existingColorImages as $cn => $imgs) {
                if (!$variantColors->contains($cn)) {
                    $allColorGroups->push(['name' => $cn, 'images' => $imgs]);
                }
            }
        @endphp

        {{-- Datalist kept in sync by JS --}}
        <datalist id="colourNameList">
            @foreach($allColorGroups as $cg)
            <option value="{{ $cg['name'] }}">
            @endforeach
        </datalist>

        <div class="mb-4">
            <h6 class="fw-semibold mb-2"><i class="bi bi-images"></i> Colour-Specific Image Galleries</h6>
            <p class="text-muted small mb-2">
                Upload images per colour — the gallery on the product page swaps automatically when a customer selects that colour.
            </p>

            <div id="colorImagesContainer">
                @forelse($allColorGroups as $gi => $cg)
                <div class="color-image-group card mb-3">
                    <div class="card-header py-2 d-flex align-items-center justify-content-between">
                        <span class="fw-semibold" style="font-size:13px">
                            <i class="bi bi-palette2 me-1"></i>
                            {{ $cg['name'] }}
                        </span>
                        <button type="button" class="btn btn-outline-danger btn-sm py-0"
                                onclick="this.closest('.color-image-group').remove()"
                                title="Remove this colour group">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                    <div class="card-body pb-2">
                        {{-- Retained marker: removed from DOM when the user deletes this group --}}
                        <input type="hidden" name="retained_color_groups[]" value="{{ $cg['name'] }}">
                        <input type="hidden"
                               name="color_image_groups[{{ $gi }}][color_name]"
                               value="{{ $cg['name'] }}">

                        {{-- Existing images --}}
                        @if($cg['images']->count())
                        <div class="d-flex flex-wrap gap-2 mb-2">
                            @foreach($cg['images'] as $img)
                            <div class="position-relative">
                                <img src="{{ asset('storage/' . $img->image_path) }}"
                                     class="rounded" style="width:80px;height:80px;object-fit:cover;border:1px solid #dee2e6">
                                <button type="button"
                                        class="btn btn-danger btn-sm p-0 px-1 position-absolute top-0 end-0 m-0"
                                        style="font-size:10px;line-height:1.4"
                                        onclick="shopDeleteItem('{{ route('admin.shop.products.delete-color-image', $img) }}')">×</button>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <p class="text-muted small mb-2">No images uploaded yet.</p>
                        @endif

                        {{-- Upload more --}}
                        <div>
                            <label class="form-label small fw-semibold mb-1">
                                {{ $cg['images']->count() ? 'Add more images' : 'Upload images' }}
                            </label>
                            <input type="file"
                                   name="color_image_groups[{{ $gi }}][images][]"
                                   class="form-control form-control-sm"
                                   accept="image/*" multiple>
                        </div>
                    </div>
                </div>
                @empty
                <p class="text-muted small" id="noColourGroupsMsg">
                    Add variants below first — a gallery group will appear here for each colour.
                </p>
                @endforelse
            </div>

            <button type="button" class="btn btn-outline-primary btn-sm mt-1" onclick="addColorImageGroup()">
                <i class="bi bi-plus-circle"></i> Add Colour Image Group
            </button>
        </div>

        <hr>

        {{-- SKU duplicate error banner --}}
        <div id="skuDuplicateError" class="alert alert-danger py-2 d-none" style="font-size:13px">
            <strong>Duplicate SKUs detected.</strong> Each variant must have a unique SKU, or leave the SKU field blank. Please fix the highlighted rows before saving.
        </div>

        {{-- Variant rows table --}}
        <div class="table-responsive">
            <table class="table table-bordered align-middle" id="variantsTable">
                <thead class="table-light">
                    <tr>
                        <th>Colour Name</th>
                        <th style="width:90px">Hex</th>
                        <th style="width:100px">Size</th>
                        <th style="width:70px">Sort #</th>
                        <th>SKU <small class="text-muted fw-normal">(unique per row, or leave blank)</small></th>
                        <th style="width:90px">Stock</th>
                        <th style="width:100px">+/- Price</th>
                        <th style="width:60px">Active</th>
                        <th style="width:110px" title="Photo shown when this variant is selected">Variant Photo</th>
                        <th style="width:40px"></th>
                    </tr>
                </thead>
                <tbody id="variantRows">
                    @if(isset($product))
                        @foreach($product->variants as $i => $variant)
                        <tr data-row="{{ $i }}">
                            <td><input type="hidden" name="variants[{{ $i }}][id]" value="{{ $variant->id }}">
                                <input type="text" name="variants[{{ $i }}][color_name]" list="colourNameList" class="form-control form-control-sm" value="{{ $variant->color_name }}" placeholder="Black Stone" required></td>
                            <td><input type="color" name="variants[{{ $i }}][color_hex]" class="form-control form-control-sm p-1" value="{{ $variant->color_hex ?? '#000000' }}" style="height:34px"></td>
                            <td><input type="text" name="variants[{{ $i }}][size]" class="form-control form-control-sm" value="{{ $variant->size }}" placeholder="M" required></td>
                            <td><input type="number" name="variants[{{ $i }}][size_order]" class="form-control form-control-sm" value="{{ $variant->size_order }}" min="0"></td>
                            <td><input type="text" name="variants[{{ $i }}][sku]" class="form-control form-control-sm" value="{{ $variant->sku }}"></td>
                            <td><input type="number" name="variants[{{ $i }}][stock_quantity]" class="form-control form-control-sm" value="{{ $variant->stock_quantity }}" min="0"></td>
                            <td><input type="number" name="variants[{{ $i }}][price_adjustment]" class="form-control form-control-sm" value="{{ $variant->price_adjustment }}" step="0.01"></td>
                            <td class="text-center"><input type="checkbox" name="variants[{{ $i }}][is_active]" value="1" class="form-check-input" @checked($variant->is_active)></td>
                            <td>
                                @if($variant->featured_image)
                                <div class="mb-1">
                                    <img src="{{ asset('storage/' . $variant->featured_image) }}"
                                         style="width:44px;height:44px;object-fit:cover;border-radius:4px;border:1px solid #dee2e6">
                                </div>
                                @endif
                                <input type="file" name="variant_images[{{ $i }}]"
                                       class="form-control form-control-sm p-0"
                                       accept="image/*"
                                       style="font-size:10px"
                                      >
                            </td>
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
let colorImageGroupIndex = {{ $allColorGroups->count() }};

// ── SKU duplicate validation ──────────────────────────────────────────────
function checkSkuDuplicates() {
    const inputs = document.querySelectorAll('#variantRows input[name$="[sku]"]');
    const seen   = {};
    let hasDupes = false;

    inputs.forEach(inp => { inp.classList.remove('is-invalid'); inp.removeAttribute('title'); });

    inputs.forEach(inp => {
        const val = inp.value.trim().toLowerCase();
        if (!val) return;
        if (seen[val]) {
            inp.classList.add('is-invalid');
            inp.title = 'Duplicate SKU — must be unique or left blank';
            seen[val].classList.add('is-invalid');
            seen[val].title = 'Duplicate SKU — must be unique or left blank';
            hasDupes = true;
        } else {
            seen[val] = inp;
        }
    });
    return hasDupes;
}

document.addEventListener('DOMContentLoaded', function () {
    // Run on page load so existing variants set the read-only state immediately
    syncMainStock();

    document.addEventListener('input', function (e) {
        // Live SKU duplicate check
        if (e.target.matches('#variantRows input[name$="[sku]"]')) {
            checkSkuDuplicates();
        }
        // Live stock sum
        if (e.target.matches('#variantRows input[name$="[stock_quantity]"]')) {
            syncMainStock();
        }
    });

    // Block form submission if duplicates remain
    const form = document.getElementById('{{ $formId ?? "productForm" }}');
    if (form) {
        form.addEventListener('submit', function (e) {
            if (checkSkuDuplicates()) {
                e.preventDefault();
                const errEl = document.getElementById('skuDuplicateError');
                if (errEl) {
                    errEl.classList.remove('d-none');
                    errEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
        });
    }
});

// ── Main stock auto-sum ───────────────────────────────────────────────────
function syncMainStock() {
    const stockInputs = document.querySelectorAll('#variantRows input[name$="[stock_quantity]"]');
    const mainStock   = document.getElementById('mainStockQty');
    const note        = document.getElementById('mainStockNote');
    if (!mainStock) return;

    if (stockInputs.length > 0) {
        let total = 0;
        stockInputs.forEach(inp => { total += parseInt(inp.value || 0); });
        mainStock.value    = total;
        mainStock.readOnly = true;
        mainStock.style.background = '#f8f9fa';
        mainStock.title    = 'Auto-calculated as the sum of all variant stock quantities';
        if (note) note.classList.remove('d-none');
    } else {
        mainStock.readOnly = false;
        mainStock.style.background = '';
        mainStock.title    = '';
        if (note) note.classList.add('d-none');
    }
}

// ── Datalist helpers ─────────────────────────────────────────────────────
function syncColourDatalist() {
    const dl = document.getElementById('colourNameList');
    if (!dl) return;
    const existing = new Set([...dl.options].map(o => o.value));
    document.querySelectorAll('#variantRows input[name$="[color_name]"]').forEach(inp => {
        const v = inp.value.trim();
        if (v && !existing.has(v)) {
            const opt = document.createElement('option');
            opt.value = v;
            dl.appendChild(opt);
            existing.add(v);
        }
    });
}

// Debounced live-sync as user types in variant colour fields
document.addEventListener('input', function (e) {
    if (e.target.matches('#variantRows input[name$="[color_name]"]')) {
        clearTimeout(window._colourSyncTimer);
        window._colourSyncTimer = setTimeout(syncColourDatalist, 400);
    }
});

function addVariantRow() {
    const i = variantRowIndex++;
    const row = `<tr data-row="${i}">
        <td><input type="text" name="variants[${i}][color_name]" class="form-control form-control-sm" list="colourNameList" placeholder="Black Stone" required></td>
        <td><input type="color" name="variants[${i}][color_hex]" class="form-control form-control-sm p-1" value="#000000" style="height:34px"></td>
        <td><input type="text" name="variants[${i}][size]" class="form-control form-control-sm" placeholder="M" required></td>
        <td><input type="number" name="variants[${i}][size_order]" class="form-control form-control-sm" value="0" min="0"></td>
        <td><input type="text" name="variants[${i}][sku]" class="form-control form-control-sm"></td>
        <td><input type="number" name="variants[${i}][stock_quantity]" class="form-control form-control-sm" value="0" min="0"></td>
        <td><input type="number" name="variants[${i}][price_adjustment]" class="form-control form-control-sm" value="0" step="0.01"></td>
        <td class="text-center"><input type="checkbox" name="variants[${i}][is_active]" value="1" class="form-check-input" checked></td>
        <td><input type="file" name="variant_images[${i}]" class="form-control form-control-sm p-0" accept="image/*" style="font-size:10px"></td>
        <td><button type="button" class="btn btn-outline-danger btn-sm" onclick="removeVariantRow(this)">×</button></td>
    </tr>`;
    document.getElementById('variantRows').insertAdjacentHTML('beforeend', row);
    syncMainStock();
}

function removeVariantRow(btn) {
    btn.closest('tr').remove();
    syncMainStock();
}

function addColorImageGroup() {
    syncColourDatalist();
    const noMsg = document.getElementById('noColourGroupsMsg');
    if (noMsg) noMsg.remove();
    const i = colorImageGroupIndex++;
    const html = `<div class="color-image-group card mb-3">
        <div class="card-header py-2 d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2 flex-grow-1">
                <i class="bi bi-palette2"></i>
                <input type="text" name="color_image_groups[${i}][color_name]"
                       list="colourNameList"
                       class="form-control form-control-sm colour-group-name"
                       style="max-width:200px" placeholder="Colour name (e.g. Black)">
            </div>
            <button type="button" class="btn btn-outline-danger btn-sm py-0"
                    onclick="this.closest('.color-image-group').remove()" title="Remove">
                <i class="bi bi-trash"></i>
            </button>
        </div>
        <div class="card-body pb-2">
            <p class="text-muted small mb-2">No images uploaded yet.</p>
            <div>
                <label class="form-label small fw-semibold mb-1">Upload images</label>
                <input type="file" name="color_image_groups[${i}][images][]"
                       class="form-control form-control-sm" accept="image/*" multiple>
            </div>
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
            <td><input type="text" name="variants[${i}][color_name]" class="form-control form-control-sm" value="${colorName}" required></td>
            <td><input type="color" name="variants[${i}][color_hex]" class="form-control form-control-sm p-1" value="${colorHex}" style="height:34px"></td>
            <td><input type="text" name="variants[${i}][size]" class="form-control form-control-sm" value="${cb.value}" required></td>
            <td><input type="number" name="variants[${i}][size_order]" class="form-control form-control-sm" value="${cb.dataset.order}" min="0"></td>
            <td><input type="text" name="variants[${i}][sku]" class="form-control form-control-sm"></td>
            <td><input type="number" name="variants[${i}][stock_quantity]" class="form-control form-control-sm" value="${stock}" min="0"></td>
            <td><input type="number" name="variants[${i}][price_adjustment]" class="form-control form-control-sm" value="0" step="0.01"></td>
            <td class="text-center"><input type="checkbox" name="variants[${i}][is_active]" value="1" class="form-check-input" checked></td>
            <td><button type="button" class="btn btn-outline-danger btn-sm" onclick="removeVariantRow(this)">×</button></td>
        </tr>`;
        document.getElementById('variantRows').insertAdjacentHTML('beforeend', row);
    });

    syncMainStock();
    bootstrap.Modal.getInstance(document.getElementById('bulkSizeModal')).hide();
}
</script>
