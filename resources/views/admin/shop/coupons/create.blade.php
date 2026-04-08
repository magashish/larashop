@extends('layouts.app')
@section('content')
<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card shadow">
                <div class="card-header fw-bold"><i class="bi bi-ticket-perforated"></i> Create Coupon</div>
                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
                    @endif
                    <form action="{{ route('admin.shop.coupons.store') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label fw-semibold">Coupon Code <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="text" name="code" id="couponCode" class="form-control text-uppercase" value="{{ old('code') }}" placeholder="e.g. SAVE20" required>
                                    <button type="button" class="btn btn-outline-secondary" onclick="generateCode()"><i class="bi bi-shuffle"></i></button>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Type <span class="text-danger">*</span></label>
                                <select name="type" id="couponType" class="form-select" required>
                                    <option value="fixed" @selected(old('type') === 'fixed')>Fixed ($)</option>
                                    <option value="percentage" @selected(old('type') === 'percentage')>Percentage (%)</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Value <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text" id="valuePrefix">$</span>
                                    <input type="number" name="value" class="form-control" value="{{ old('value') }}" step="0.01" min="0" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Min Order Amount</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" name="min_order_amount" class="form-control" value="{{ old('min_order_amount') }}" step="0.01" min="0" placeholder="No minimum">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Total Usage Limit</label>
                                <input type="number" name="usage_limit" class="form-control" value="{{ old('usage_limit') }}" min="1" placeholder="Unlimited">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Per User Limit</label>
                                <input type="number" name="per_user_limit" class="form-control" value="{{ old('per_user_limit') }}" min="1" placeholder="Unlimited">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Start Date</label>
                                <input type="datetime-local" name="starts_at" class="form-control" value="{{ old('starts_at') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Expiry Date</label>
                                <input type="datetime-local" name="expires_at" class="form-control" value="{{ old('expires_at') }}">
                            </div>
                            <div class="col-12">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" @checked(old('is_active', true))>
                                    <label class="form-check-label" for="is_active">Active</label>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success"><i class="bi bi-check-lg"></i> Create Coupon</button>
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
function generateCode() {
    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    let code = '';
    for (let i = 0; i < 8; i++) code += chars.charAt(Math.floor(Math.random() * chars.length));
    document.getElementById('couponCode').value = code;
}
</script>
@endpush
@endsection
