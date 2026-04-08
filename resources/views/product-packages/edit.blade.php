@extends('layouts.app')
@section('content')
<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header fw-bold">{{ __('Edit Product Package') }}</div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="mb-3 text-end">
                        <a href="{{ route('product-packages.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-arrow-left"></i> Back to Product Packages
                        </a>
                    </div>

                    <form action="{{ route('product-packages.update', $productPackage->id) }}" method="post">
                        @csrf
                        @method('PUT') {{-- Use PUT method for update --}}

                        <div class="form-group mb-3">
                            <label for="name" class="form-label">Product Package Name:</label>
                            <input id="name" name="name" type="text"
                                class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name', $productPackage->name) }}" placeholder="Enter product package name" required>
                            @error('name')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="price" class="form-label">Price ($):</label>
                            <input id="price" name="price" type="number" step="0.01"
                                class="form-control @error('price') is-invalid @enderror"
                                value="{{ old('price', $productPackage->price) }}" placeholder="Enter price" required min="0.01">
                            @error('price')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="entries_into_draw" class="form-label">Entries Into Draw:</label>
                            <input id="entries_into_draw" name="entries_into_draw" type="number"
                                class="form-control @error('entries_into_draw') is-invalid @enderror"
                                value="{{ old('entries_into_draw', $productPackage->entries_into_draw) }}" placeholder="Enter number of entries" required min="1">
                            @error('entries_into_draw')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="access_duration_weeks" class="form-label">Access Duration (Weeks):</label>
                            <input id="access_duration_weeks" name="access_duration_weeks" type="number"
                                class="form-control @error('access_duration_weeks') is-invalid @enderror"
                                value="{{ old('access_duration_weeks', $productPackage->access_duration_weeks) }}" placeholder="Enter access duration in weeks" required min="1">
                            @error('access_duration_weeks')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="description" class="form-label">Description (Each line will be a bullet point):</label>
                            <textarea id="description" name="description"
                                class="form-control @error('description') is-invalid @enderror"
                                rows="5" placeholder="Enter description for the package">{{ old('description', $productPackage->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-arrow-up-circle"></i> Update Product Package
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection