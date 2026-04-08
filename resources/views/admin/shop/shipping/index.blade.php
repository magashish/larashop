@extends('layouts.app')
@section('content')
<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0"><i class="bi bi-truck"></i> Shipping Zones & Rates</h5>
        <a href="{{ route('admin.shop.shipping.create-zone') }}" class="btn btn-success btn-sm">
            <i class="bi bi-plus-circle"></i> Add Shipping Zone
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    @forelse($zones as $zone)
    <div class="card shadow mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <strong>{{ $zone->name }}</strong>
                @if($zone->description)<small class="text-muted ms-2">{{ $zone->description }}</small>@endif
                <span class="badge bg-{{ $zone->is_active ? 'success' : 'secondary' }} ms-2">{{ $zone->is_active ? 'Active' : 'Inactive' }}</span>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.shop.shipping.edit-zone', $zone) }}" class="btn btn-warning btn-sm"><i class="bi bi-pencil"></i> Edit Zone</a>
                <form action="{{ route('admin.shop.shipping.destroy-zone', $zone) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this zone?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-danger btn-sm"><i class="bi bi-trash"></i></button>
                </form>
            </div>
        </div>
        <div class="card-body">
            @if($zone->countries || $zone->states)
                <p class="text-muted small mb-2">
                    @if($zone->countries)<strong>Countries:</strong> {{ implode(', ', $zone->countries) }} @endif
                    @if($zone->states) | <strong>States:</strong> {{ implode(', ', $zone->states) }} @endif
                </p>
            @endif

            <table class="table table-sm table-striped mb-3">
                <thead><tr><th>Rate Name</th><th>Type</th><th>Rate</th><th>Min Order</th><th>Max Weight</th><th>Status</th><th></th></tr></thead>
                <tbody>
                    @forelse($zone->rates as $rate)
                    <tr>
                        <td>{{ $rate->name }}</td>
                        <td><span class="badge bg-secondary">{{ ucfirst(str_replace('_', ' ', $rate->type)) }}</span></td>
                        <td>{{ $rate->type === 'free' ? 'Free' : '$' . number_format($rate->rate, 2) }}</td>
                        <td>{{ $rate->min_order_amount ? '$' . number_format($rate->min_order_amount, 2) : '—' }}</td>
                        <td>{{ $rate->max_weight ? $rate->max_weight . ' kg' : '—' }}</td>
                        <td><span class="badge bg-{{ $rate->is_active ? 'success' : 'secondary' }}">{{ $rate->is_active ? 'Active' : 'Off' }}</span></td>
                        <td>
                            <form action="{{ route('admin.shop.shipping.destroy-rate', $rate) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete rate?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger btn-sm"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-muted text-center">No rates for this zone.</td></tr>
                    @endforelse
                </tbody>
            </table>

            {{-- Add rate form --}}
            <div class="border rounded p-3 bg-light">
                <strong class="d-block mb-2 small text-uppercase">Add Shipping Rate</strong>
                <form action="{{ route('admin.shop.shipping.store-rate', $zone) }}" method="POST">
                    @csrf
                    <div class="row g-2">
                        <div class="col-md-3">
                            <input type="text" name="name" class="form-control form-control-sm" placeholder="Rate name (e.g. Standard)" required>
                        </div>
                        <div class="col-md-2">
                            <select name="type" class="form-select form-select-sm">
                                <option value="flat">Flat Rate</option>
                                <option value="free">Free Shipping</option>
                                <option value="per_item">Per Item</option>
                                <option value="weight_based">Weight Based</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">$</span>
                                <input type="number" name="rate" class="form-control" placeholder="Rate" step="0.01" min="0" value="0" required>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">Min $</span>
                                <input type="number" name="min_order_amount" class="form-control" placeholder="Optional" step="0.01" min="0">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="input-group input-group-sm">
                                <input type="number" name="max_weight" class="form-control" placeholder="Max kg" step="0.001" min="0">
                                <span class="input-group-text">kg</span>
                            </div>
                        </div>
                        <div class="col-md-1">
                            <button type="submit" class="btn btn-success btn-sm w-100"><i class="bi bi-plus"></i></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @empty
    <div class="alert alert-info">No shipping zones configured yet. <a href="{{ route('admin.shop.shipping.create-zone') }}">Add your first zone.</a></div>
    @endforelse
</div>
@endsection
