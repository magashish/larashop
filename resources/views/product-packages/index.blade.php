@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header fw-bold">{{ __('Product Packages') }}</div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="mb-3 text-end">
                        <a href="{{ route('product-packages.create') }}" class="btn btn-success">
                            <i class="bi bi-plus-circle"></i> Create New Product Package
                        </a>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Price</th>
                                    <th>Entries Into Draw</th>
                                    <th>Access Duration (Weeks)</th>
                                    <th>Description</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($productPackages as $productPackage)
                                    <tr>
                                        <td>{{ $productPackage->name }}</td>
                                        <td>${{ number_format($productPackage->price, 2) }}</td>
                                        <td>{{ $productPackage->entries_into_draw }}</td>
                                        <td>{{ $productPackage->access_duration_weeks }}</td>
                                         <td>{{ Str::limit($productPackage->description, 50) ?? 'N/A' }}</td>
                                        <td class="text-end">
                                            <a href="{{ route('product-packages.edit', $productPackage->id) }}" class="btn btn-warning btn-sm me-2">
                                                <i class="bi bi-pencil"></i> Edit
                                            </a>
                                            <form action="{{ route('product-packages.destroy', $productPackage->id) }}" method="post" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this product package?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    <i class="bi bi-trash"></i> Delete
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">No product packages found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center">
                        {{ $productPackages->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection