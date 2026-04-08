@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header fw-bold">{{ __('Google Reviews / Edit Review') }}</div>
                
                <div class="card-body">
                    <div class="mb-3 text-end">
                        <a href="{{ route('google-reviews.index') }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-arrow-left"></i> Back to Reviews Admin
                        </a>
                    </div>

                    <form action="{{ route('google-reviews.update', $googleReview->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        {{-- Author Name --}}
                        <div class="mb-3">
                            <label for="author_name" class="form-label fw-semibold">Author Name</label>
                            <input type="text" name="author_name" id="author_name"
                                class="form-control @error('author_name') is-invalid @enderror"
                                value="{{ old('author_name', $googleReview->author_name) }}" required>
                            @error('author_name')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Rating --}}
                        <div class="mb-3">
                            <label for="rating" class="form-label fw-semibold">Rating</label>
                            <select name="rating" id="rating" class="form-select @error('rating') is-invalid @enderror">
                                @for ($i = 5; $i >= 1; $i--)
                                    <option value="{{ $i }}" {{ old('rating', $googleReview->rating) == $i ? 'selected' : '' }}>
                                        {{ $i }} Stars
                                    </option>
                                @endfor
                            </select>
                            @error('rating')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Review Text --}}
                        <div class="mb-3">
                            <label for="review_text" class="form-label fw-semibold">Review Content</label>
                            <textarea name="review_text" id="review_text" rows="5"
                                class="form-control @error('review_text') is-invalid @enderror" >{{ old('review_text', $googleReview->review_text) }}</textarea>
                            @error('review_text')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="type" class="form-label fw-semibold">Review Type</label>

                            <select name="type" 
                            class="form-control @error('type') is-invalid @enderror">

                            <option value="google"
                            {{ old('type', $googleReview->type ?? '') == 'google' ? 'selected' : '' }}>
                            Google
                        </option>

                        <option value="facebook"
                        {{ old('type', $googleReview->type ?? '') == 'facebook' ? 'selected' : '' }}>
                        Facebook
                    </option>

                </select>

                @error('type')
                <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>


                        {{-- Profile Image Section --}}
                        <div class="mb-4">
                            <fieldset class="border rounded p-3">
                                <legend class="float-none w-auto px-2 fw-semibold">Profile Media</legend>
                                
                                @if($googleReview->author_img)
                                <div class="mb-3">
                                    <label class="d-block mb-2">Current Image:</label>
                                    <div class="position-relative d-inline-block">
                                        <img src="{{ asset('storage/' . $googleReview->author_img) }}" 
                                             class="img-thumbnail shadow-sm" 
                                             style="width: 120px; height: 120px; object-fit: cover;">
                                        <div class="mt-1 text-muted small">Format: WebP</div>
                                    </div>
                                </div>
                                @endif

                                <label for="author_img" class="form-label">Upload New Image (Optional)</label>
                                <input type="file" name="author_img" id="author_img"
                                    class="form-control @error('author_img') is-invalid @enderror">
                                <div class="form-text">Uploading a new image will replace the existing WebP file.</div>
                                
                                @error('author_img')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </fieldset>
                        </div>

                      

                        {{-- Action Buttons --}}
                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Update Review
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