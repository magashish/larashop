@extends('layouts.app')
@section('content')
<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header fw-bold">{{ __('Editing ') }} "{{ $category->name}}"</div>

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

                    <form  action="{{ route('categories.update', $category->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="name" class="form-label fw-semibold">Name</label>
                            <input type="text" name="name" id="name"
                            placeholder="Enter Name"
                            class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name', $category->name) }}" required>
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
                            class="form-control @error('description') is-invalid @enderror">{{ old('description', $category->description) }}</textarea>
                            @error('description')
                            <div class="invalid-feedback d-block">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="icon" class="form-label fw-semibold">Category Icon</label>
                            <input type="file" name="icon" id="icon"
                            class="form-control @error('image') is-invalid @enderror">
                            @error('icon')
                            <div class="invalid-feedback d-block">
                                {{ $message }}
                            </div>
                            @enderror
                            @if ($category->icon)
                            <div class="mt-3 position-relative category-icon d-inline-block"> 
                                <img src="{{ Storage::url($category->icon) }}" alt="Current Image" class="img-thumbnail" style="max-width: 200px; height: auto;">

                                <button type="button" class="btn btn-danger btn-sm position-absolute top-0 start-100 translate-middle rounded-circle p-0"
                                style="width: 24px; height: 24px; line-height: 1; font-size: 1rem;"
                                id="removeImageBtn" title="Remove icon">
                                <i class="bi bi-x-circle-fill"></i>
                            </button>
                            <input type="checkbox" name="remove_image" id="remove_image_hidden" value="1" class="d-none">
                            <label class="d-none" for="remove_image_hidden">Remove current icon</label>
                        </div>
                        @endif
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Update Category
                    </button>
                </form>

            </div>
        </div>
    </div>
</div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script> 
<script>
    $(document).ready(function () {
        const $removeImageBtn = $('#removeImageBtn');
        const $removeImageHiddenCheckbox = $('#remove_image_hidden');
        const $currentImageDisplay = $removeImageBtn.closest('.category-icon');
        if ($removeImageBtn.length && $removeImageHiddenCheckbox.length && $currentImageDisplay.length) {
            $removeImageBtn.on('click', function () {
                if (confirm('Are you sure you want to remove this image?')) {
                    $removeImageHiddenCheckbox.prop('checked', true); 
                    $currentImageDisplay.hide(); 
                    $currentImageDisplay.removeClass('d-inline-block');
                    $('#image').val('');
                }
            });
        }
    });
</script>
@endsection
