@extends('layouts.app')
@section('content')
<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header fw-bold">{{ __('Category / Create') }}</div>

                <div class="card-body">
                    @if (session('status'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('status') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif

                    <div class="mb-3 text-end">
                        <a href="{{ route('categories.index') }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-arrow-left"></i> Back to Categories Admin
                        </a>
                    </div>
                    <form method="POST" action="{{ route('categories.store') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label fw-semibold"> Name</label>
                            <input type="text" name="name" id="name"
                            placeholder="Enter Name"
                            class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name') }}" required>
                            @error('name')
                            <div class="invalid-feedback d-block">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label fw-semibold">Description</label>
                            <textarea name="description" id="description"
                            placeholder="Enter Description"
                            class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>

                            @error('description')
                            <div class="invalid-feedback d-block">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Save Category
                        </button>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection

