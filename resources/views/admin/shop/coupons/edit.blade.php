@extends('layouts.app')
@section('content')
<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card shadow">
                <div class="card-header fw-bold"><i class="bi bi-ticket-perforated"></i> Edit Coupon: {{ $coupon->code }}</div>
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
                    @endif
                    <form action="{{ route('admin.shop.coupons.update', $coupon) }}" method="POST">
                        @csrf @method('PUT')
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label fw-semibold">Coupon Code</label>
                                <input type="text" name="code" class="form-control text-uppercase" value="{{ old('code', $coupon->code) }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Type</label>
                                <select name="type" id="couponType" class="form-select">
                                    <option value="fixed" @selected(old('type', $coupon->type) === 'fixed')>Fixed ($)</option>
                                    <option value="percentage" @selected(old('type', $coupon->type) === 'percentage')>Percentage (%)</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Value</label>
                                <div class="input-group">
                                    <span class="input-group-text" id="valuePrefix">{{ $coupon->type === 'percentage' ? '%' : '$' }}</span>
                                    <input type="number" name="value" class="form-control" value="{{ old('value', $coupon->value) }}" step="0.01" min="0" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Min Order Amount</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" name="min_order_amount" class="form-control" value="{{ old('min_order_amount', $coupon->min_order_amount) }}" step="0.01" min="0">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Usage Limit</label>
                                <input type="number" name="usage_limit" class="form-control" value="{{ old('usage_limit', $coupon->usage_limit) }}" min="1" placeholder="Unlimited">
                                <small class="text-muted">Used: {{ $coupon->usage_count }}</small>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Per User Limit</label>
                                <input type="number" name="per_user_limit" class="form-control" value="{{ old('per_user_limit', $coupon->per_user_limit) }}" min="1">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Start Date</label>
                                <input type="datetime-local" name="starts_at" class="form-control" value="{{ old('starts_at', $coupon->starts_at?->format('Y-m-d\TH:i')) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Expiry Date</label>
                                <input type="datetime-local" name="expires_at" class="form-control" value="{{ old('expires_at', $coupon->expires_at?->format('Y-m-d\TH:i')) }}">
                            </div>
                            <div class="col-12">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" @checked(old('is_active', $coupon->is_active))>
                                    <label class="form-check-label" for="is_active">Active</label>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-warning"><i class="bi bi-save"></i> Update Coupon</button>
                            <a href="{{ route('admin.shop.coupons.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script>
document.getElementById('couponType').addEventListener('change', function() {
    document.getElementById('valuePrefix').textContent = this.value === 'percentage' ? '%' : '$';
});
</script>
@endpush
@endsection
