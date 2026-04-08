@extends('layouts.app')
@section('content')
<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header fw-bold">{{ __('License / Create') }}</div>
                <div class="card-body">
                    @if (session('status'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('status') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif

                    <div class="mb-3 text-end">
                        <a href="{{ route('licenses.index') }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-arrow-left"></i> Back to License Admin
                        </a>
                    </div>

                    <form action="{{ route('licenses.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label for="title" class="form-label fw-semibold">Title</label>
                            <input type="text" name="title" id="title"
                            value="{{ old('title') }}"
                            placeholder="Enter Title"
                            class="form-control @error('title') is-invalid @enderror">
                            @error('title')
                            <div class="invalid-feedback d-block">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="quantity" class="form-label fw-semibold">Quantity</label>
                            <input type="text" name="quantity" id="quantity"
                            value="{{ old('quantity') }}"
                            placeholder="Enter Quantity"
                            class="form-control @error('quantity') is-invalid @enderror">
                            @error('quantity')
                            <div class="invalid-feedback d-block">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="price" class="form-label fw-semibold">Price</label>
                            <input type="text" name="price" id="price"
                            value="{{ old('price') }}"
                            placeholder="Enter Price"
                            class="form-control @error('price') is-invalid @enderror">
                            @error('price')
                            <div class="invalid-feedback d-block">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="license_type" class="form-label fw-semibold">License Type</label>
                            <select name="license_type" id="license_type"
                            class="form-control @error('license_type') is-invalid @enderror">
                            <option value="">Select Type</option>
                            <option value="unit" {{ old('license_type') == 'unit' ? 'selected' : '' }}>Unit</option>
                            <option value="subscription" {{ old('license_type') == 'subscription' ? 'selected' : '' }}>Subscription</option>
                        </select>
                        @error('license_type')
                        <div class="invalid-feedback d-block">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="is_active" class="form-label fw-semibold">Active?</label>
                        <select name="is_active" id="is_active"
                        class="form-control @error('is_active') is-invalid @enderror">
                        <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>No</option>
                        <option value="1" {{ old('is_active') == '1' ? 'selected' : '' }}>Yes</option>
                    </select>
                    @error('is_active')
                    <div class="invalid-feedback d-block">
                        {{ $message }}
                    </div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="is_public" class="form-label fw-semibold">Public?</label>
                    <select name="is_public" id="is_public"
                    class="form-control @error('is_public') is-invalid @enderror">
                    <option value="0" {{ old('is_public') == '0' ? 'selected' : '' }}>No</option>
                    <option value="1" {{ old('is_public') == '1' ? 'selected' : '' }}>Yes</option>
                </select>
                @error('is_public')
                <div class="invalid-feedback d-block">
                    {{ $message }}
                </div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="bi bi-save"></i> Save License
            </button>
        </form>

    </div>
</div>
</div>
</div>
</div>
@endsection


