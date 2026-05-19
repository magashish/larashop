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

    <form action="{{ route('admin.shop.settings.update') }}" method="POST">
        @csrf @method('PUT')

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
