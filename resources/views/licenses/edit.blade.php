@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <div class="row justify-content-center mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">{{ __('License / Edit') }}</div>
                <div class="card-body">
                    <div class="mb-3 text-end">
                        <a href="{{ route('licenses.index') }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-arrow-left"></i> Back to License Admin
                        </a>
                    </div>

                    <form method="POST" action="{{ route('licenses.update', $license->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="title" class="form-label">Title</label>
                            <input type="text"
                            class="form-control @error('title') is-invalid @enderror"
                            id="title"
                            name="title"
                            value="{{ old('title', $license->title) }}"
                            required>

                            @error('title')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="quantity" class="form-label">Quantity</label>
                            <input type="text"
                            class="form-control @error('quantity') is-invalid @enderror"
                            id="quantity"
                            name="quantity"
                            value="{{ old('quantity', $license->quantity) }}"
                            required>

                            @error('quantity')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="price" class="form-label">Price</label>
                            <input type="text"
                            class="form-control @error('price') is-invalid @enderror"
                            id="price"
                            name="price"
                            value="{{ old('price', $license->price) }}"
                            required>

                            @error('price')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="license_type" class="form-label">License Type</label>
                            <select class="form-control @error('license_type') is-invalid @enderror"
                            id="license_type"
                            name="license_type"
                            required>
                            <option value="unit" {{ old('license_type', $license->license_type) == 'unit' ? 'selected' : '' }}>Unit</option>
                            <option value="subscription" {{ old('license_type', $license->license_type) == 'subscription' ? 'selected' : '' }}>Subscription</option>
                        </select>

                        @error('license_type')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="is_active" class="form-label">Active?</label>
                        <select class="form-control @error('is_active') is-invalid @enderror"
                        id="is_active"
                        name="is_active"
                        required>
                        <option value="0" {{ old('is_active', $license->is_active) == 0 ? 'selected' : '' }}>No</option>
                        <option value="1" {{ old('is_active', $license->is_active) == 1 ? 'selected' : '' }}>Yes</option>
                    </select>

                    @error('is_active')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="is_public" class="form-label">Public?</label>
                    <select class="form-control @error('is_public') is-invalid @enderror"
                    id="is_public"
                    name="is_public"
                    required>
                    <option value="0" {{ old('is_public', $license->is_public) == 0 ? 'selected' : '' }}>No</option>
                    <option value="1" {{ old('is_public', $license->is_public) == 1 ? 'selected' : '' }}>Yes</option>
                </select>

                @error('is_public')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="bi bi-save"></i> Update License
            </button>
        </form>

    </div>

</div>
</div>
</div>
</div>
@endsection
