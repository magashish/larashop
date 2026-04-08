@extends('layouts.app')
@section('content')
<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card shadow">
                <div class="card-header fw-bold"><i class="bi bi-truck"></i> Create Shipping Zone</div>
                <div class="card-body">
                    <form action="{{ route('admin.shop.shipping.store-zone') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold">Zone Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="e.g. Australia Domestic" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Description</label>
                                <input type="text" name="description" class="form-control" value="{{ old('description') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Countries</label>
                                <input type="text" name="countries[]" class="form-control" value="{{ old('countries.0') }}" placeholder="e.g. AU">
                                <small class="text-muted">Country code (leave blank for all)</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">States (comma-separated)</label>
                                <input type="text" name="states" class="form-control" value="{{ old('states') }}" placeholder="e.g. NSW, VIC, QLD">
                                <small class="text-muted">Leave blank to match all states</small>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Sort Order</label>
                                <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', 0) }}" min="0">
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" @checked(old('is_active', true))>
                                    <label class="form-check-label" for="is_active">Active</label>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success"><i class="bi bi-check-lg"></i> Create Zone</button>
                            <a href="{{ route('admin.shop.shipping.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
