@extends('layouts.app')
@section('content')
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header fw-bold"><i class="bi bi-percent"></i> Tax Rates</div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
                    @endif
                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead>
                                <tr><th>Name</th><th>Rate</th><th>Country</th><th>State</th><th>Priority</th><th>Default</th><th>Status</th><th class="text-end">Actions</th></tr>
                            </thead>
                            <tbody>
                                @forelse($taxes as $tax)
                                <tr>
                                    <td>{{ $tax->name }}</td>
                                    <td>{{ number_format($tax->rate * 100, 2) }}%</td>
                                    <td>{{ $tax->country }}</td>
                                    <td>{{ $tax->state ?? '—' }}</td>
                                    <td>{{ $tax->priority }}</td>
                                    <td>@if($tax->is_default)<span class="badge bg-primary">Default</span>@endif</td>
                                    <td><span class="badge bg-{{ $tax->is_active ? 'success' : 'secondary' }}">{{ $tax->is_active ? 'Active' : 'Inactive' }}</span></td>
                                    <td class="text-end">
                                        <button class="btn btn-warning btn-sm" onclick="editTax({{ json_encode($tax) }})"><i class="bi bi-pencil"></i></button>
                                        <form action="{{ route('admin.shop.taxes.destroy', $tax) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this tax rate?')">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-danger btn-sm"><i class="bi bi-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="8" class="text-center text-muted">No tax rates configured.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow" id="taxFormCard">
                <div class="card-header fw-semibold" id="taxFormTitle"><i class="bi bi-plus-circle"></i> Add Tax Rate</div>
                <div class="card-body">
                    <form id="taxForm" action="{{ route('admin.shop.taxes.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="_method" id="formMethod" value="POST">
                        <input type="hidden" name="tax_id" id="taxId">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="taxName" class="form-control" placeholder="e.g. GST" required>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label fw-semibold">Rate (%) <span class="text-danger">*</span></label>
                                <input type="number" name="rate" id="taxRate" class="form-control" step="0.01" min="0" max="100" placeholder="10" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-semibold">Priority</label>
                                <input type="number" name="priority" id="taxPriority" class="form-control" value="0" min="0">
                            </div>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label fw-semibold">Country <span class="text-danger">*</span></label>
                                <input type="text" name="country" id="taxCountry" class="form-control" value="AU" maxlength="2" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-semibold">State</label>
                                <input type="text" name="state" id="taxState" class="form-control" placeholder="Leave blank for all">
                            </div>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-4">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="taxActive" checked>
                                    <label class="form-check-label" for="taxActive">Active</label>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_default" value="1" id="taxDefault">
                                    <label class="form-check-label" for="taxDefault">Default</label>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="compound" value="1" id="taxCompound">
                                    <label class="form-check-label" for="taxCompound">Compound</label>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success w-100" id="taxSubmitBtn"><i class="bi bi-check-lg"></i> Add Tax Rate</button>
                        </div>
                        <div class="mt-2 d-none" id="cancelEditBtn">
                            <button type="button" class="btn btn-outline-secondary w-100" onclick="resetForm()">Cancel Edit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function editTax(tax) {
    document.getElementById('taxFormTitle').innerHTML = '<i class="bi bi-pencil"></i> Edit Tax Rate';
    document.getElementById('taxForm').action = '/admin/shop/taxes/' + tax.id;
    document.getElementById('formMethod').value = 'PUT';
    document.getElementById('taxId').value = tax.id;
    document.getElementById('taxName').value = tax.name;
    document.getElementById('taxRate').value = parseFloat(tax.rate * 100).toFixed(2);
    document.getElementById('taxCountry').value = tax.country;
    document.getElementById('taxState').value = tax.state || '';
    document.getElementById('taxPriority').value = tax.priority;
    document.getElementById('taxActive').checked = tax.is_active;
    document.getElementById('taxDefault').checked = tax.is_default;
    document.getElementById('taxCompound').checked = tax.compound;
    document.getElementById('taxSubmitBtn').innerHTML = '<i class="bi bi-save"></i> Update Tax Rate';
    document.getElementById('taxSubmitBtn').className = 'btn btn-warning w-100';
    document.getElementById('cancelEditBtn').classList.remove('d-none');
    document.getElementById('taxFormCard').scrollIntoView({behavior:'smooth'});
}

function resetForm() {
    document.getElementById('taxFormTitle').innerHTML = '<i class="bi bi-plus-circle"></i> Add Tax Rate';
    document.getElementById('taxForm').action = '{{ route("admin.shop.taxes.store") }}';
    document.getElementById('formMethod').value = 'POST';
    document.getElementById('taxForm').reset();
    document.getElementById('taxSubmitBtn').innerHTML = '<i class="bi bi-check-lg"></i> Add Tax Rate';
    document.getElementById('taxSubmitBtn').className = 'btn btn-success w-100';
    document.getElementById('cancelEditBtn').classList.add('d-none');
}
</script>
@endpush
@endsection
