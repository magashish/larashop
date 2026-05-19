@extends('layouts.app')
@section('content')
<div class="container-fluid mt-4" style="max-width:860px">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="mb-0 fw-bold"><i class="bi bi-gear"></i> Shop Settings</h4>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
    @endif

    <form action="{{ route('admin.shop.settings.update') }}" method="POST" enctype="multipart/form-data">
        @csrf @method('PUT')

        {{-- Shop Visibility --}}
        <div class="card shadow mb-4">
            <div class="card-header fw-semibold"><i class="bi bi-shop"></i> Shop Visibility</div>
            <div class="card-body">
                <div class="form-check form-switch mb-1">
                    <input class="form-check-input" type="checkbox" name="coming_soon" value="1" id="coming_soon"
                           @checked(old('coming_soon', $settings['coming_soon'] ?? '0') === '1')>
                    <label class="form-check-label fw-semibold" for="coming_soon">Enable Coming Soon mode</label>
                </div>
                <small class="text-muted">When enabled, the shop listing page shows a "Coming Soon" message instead of products. Individual product pages are unaffected.</small>
            </div>
        </div>

        {{-- Promo Strip --}}
        <div class="card shadow mb-4">
            <div class="card-header fw-semibold"><i class="bi bi-badge-ad"></i> Membership Promo Strip</div>
            <div class="card-body">
                <p class="text-muted small mb-3">This strip appears on every product page below the main product details.</p>

                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" name="promo_strip_enabled" value="1" id="promo_strip_enabled"
                           @checked(old('promo_strip_enabled', $settings['promo_strip_enabled'] ?? '1') === '1')>
                    <label class="form-check-label fw-semibold" for="promo_strip_enabled">Show promo strip on product pages</label>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Heading</label>
                        <input type="text" name="promo_strip_heading" class="form-control"
                               value="{{ old('promo_strip_heading', $settings['promo_strip_heading'] ?? 'Members get more') }}"
                               placeholder="Members get more">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Button Text</label>
                        <input type="text" name="promo_strip_button_text" class="form-control"
                               value="{{ old('promo_strip_button_text', $settings['promo_strip_button_text'] ?? 'Login') }}"
                               placeholder="Login">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Button URL</label>
                        <input type="text" name="promo_strip_button_url" class="form-control"
                               value="{{ old('promo_strip_button_url', $settings['promo_strip_button_url'] ?? '/login') }}"
                               placeholder="/login">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Body Text</label>
                        <textarea name="promo_strip_body" class="form-control" rows="3"
                                  placeholder="Describe the membership benefit…">{{ old('promo_strip_body', $settings['promo_strip_body'] ?? '') }}</textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Background Image</label>
                        @if(!empty($settings['promo_strip_image']))
                            <div class="mb-2">
                                <img src="{{ asset('storage/' . $settings['promo_strip_image']) }}"
                                     class="img-fluid rounded" style="max-height:160px;object-fit:cover">
                                <div class="mt-1"><small class="text-muted">Upload a new image to replace this one.</small></div>
                            </div>
                        @endif
                        <input type="file" name="promo_strip_image" class="form-control" accept="image/*">
                        <small class="text-muted">Recommended: landscape image, at least 800×600 px. Leave blank to keep the current image.</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Shipping & Returns --}}
        <div class="card shadow mb-4">
            <div class="card-header fw-semibold"><i class="bi bi-truck"></i> Shipping &amp; Returns</div>
            <div class="card-body">
                <p class="text-muted small mb-3">This text appears in the <strong>Shipping &amp; Returns</strong> accordion on every product page.</p>
                <textarea name="shipping_returns"
                          class="form-control @error('shipping_returns') is-invalid @enderror"
                          rows="8"
                          placeholder="Enter your shipping and returns policy…">{{ old('shipping_returns', $settings['shipping_returns'] ?? '') }}</textarea>
                @error('shipping_returns')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <small class="text-muted">Blank lines become paragraph breaks on the frontend.</small>
            </div>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-warning"><i class="bi bi-save"></i> Save Settings</button>
            <a href="{{ route('admin.shop.products.index') }}" class="btn btn-secondary">Cancel</a>
        </div>

    </form>
</div>
@endsection
