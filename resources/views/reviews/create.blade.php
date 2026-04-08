@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header fw-bold">{{ __('Google Reviews / Create') }}</div>
                
                <div class="card-body">
                    @if (session('status'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('status') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif

                    <div class="mb-3 text-end">
                        <a href="{{ route('google-reviews.index') }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-arrow-left"></i> Back to Reviews Admin
                        </a>
                    </div>

                    <form action="{{ route('google-reviews.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        {{-- Author Name --}}
                        <div class="mb-3">
                            <label for="author_name" class="form-label fw-semibold">Author Name</label>
                            <input type="text" name="author_name" id="author_name"
                                class="form-control @error('author_name') is-invalid @enderror"
                                value="{{ old('author_name') }}" placeholder="e.g. John Doe" required>

                            @error('author_name')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Rating Selection --}}
                        <div class="mb-3">
                            <label for="rating" class="form-label fw-semibold">Rating</label>
                            <select name="rating" id="rating" class="form-select @error('rating') is-invalid @enderror">
                                <option value="5" {{ old('rating') == 5 ? 'selected' : '' }}>5 Stars</option>
                                <option value="4" {{ old('rating') == 4 ? 'selected' : '' }}>4 Stars</option>
                                <option value="3" {{ old('rating') == 3 ? 'selected' : '' }}>3 Stars</option>
                                <option value="2" {{ old('rating') == 2 ? 'selected' : '' }}>2 Stars</option>
                                <option value="1" {{ old('rating') == 1 ? 'selected' : '' }}>1 Star</option>
                            </select>

                            @error('rating')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Review Text --}}
                        <div class="mb-3">
                            <label for="review_text" class="form-label fw-semibold">Review Content</label>
                            <textarea name="review_text" id="review_text" rows="5"
                                class="form-control @error('review_text') is-invalid @enderror">{{ old('review_text') }}</textarea>
                            @error('review_text')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                         <div class="mb-3">
                            <label for="review_text" class="form-label fw-semibold">Review Type</label>
                            <select name="type" class="form-control @error('review_text') is-invalid @enderror"">
                                <option value="google">Google</option>
                                <option value="facebook">Facebook</option>
                            </select>
                            @error('review_text')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        

                        {{-- Author Image --}}
                        <div class="mb-4">
                            <fieldset class="border rounded p-3">
                                <legend class="float-none w-auto px-2 fw-semibold">Profile Media</legend>
                                <label for="author_img" class="form-label">Author Profile Picture (Optional)</label>
                                <input type="file" name="author_img" id="author_img"
                                    class="form-control @error('author_img') is-invalid @enderror">
                                <div class="form-text">Recommended: Square image (e.g. 200x200px)</div>
                                
                                @error('author_img')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </fieldset>
                        </div>



                        {{-- Action Buttons --}}
                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Save Google Review
                            </button>
                            <a href="{{ route('google-reviews.index') }}" class="btn btn-outline-secondary">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection