@extends('layouts.app')
@section('content')
<div class="container-fluid mt-4">
    <div class="card shadow">
        <div class="card-header d-flex justify-content-between align-items-center fw-bold">
            <span><i class="bi bi-ticket-perforated"></i> Discount Coupons</span>
            <a href="{{ route('admin.shop.coupons.create') }}" class="btn btn-success btn-sm">
                <i class="bi bi-plus-circle"></i> New Coupon
            </a>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            @endif
            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead>
                        <tr><th>Code</th><th>Type</th><th>Value</th><th>Min Order</th><th>Usage</th><th>Expires</th><th>Status</th><th class="text-end">Actions</th></tr>
                    </thead>
                    <tbody>
                        @forelse($coupons as $coupon)
                        <tr>
                            <td><code class="fs-6">{{ $coupon->code }}</code></td>
                            <td><span class="badge bg-{{ $coupon->type === 'percentage' ? 'info' : 'primary' }}">{{ ucfirst($coupon->type) }}</span></td>
                            <td>{{ $coupon->type === 'percentage' ? $coupon->value . '%' : '$' . number_format($coupon->value, 2) }}</td>
                            <td>{{ $coupon->min_order_amount ? '$' . number_format($coupon->min_order_amount, 2) : '—' }}</td>
                            <td>
                                {{ $coupon->usage_count }} / {{ $coupon->usage_limit ?? '∞' }}
                            </td>
                            <td>{{ $coupon->expires_at?->format('d M Y') ?? '—' }}</td>
                            <td>
                                <span class="badge bg-{{ $coupon->is_active ? 'success' : 'secondary' }}">{{ $coupon->is_active ? 'Active' : 'Inactive' }}</span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('admin.shop.coupons.edit', $coupon) }}" class="btn btn-warning btn-sm"><i class="bi bi-pencil"></i></a>
                                <form action="{{ route('admin.shop.coupons.destroy', $coupon) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this coupon?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-danger btn-sm"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="8" class="text-center text-muted">No coupons created yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center mt-3">{{ $coupons->links() }}</div>
        </div>
    </div>
</div>
@endsection
