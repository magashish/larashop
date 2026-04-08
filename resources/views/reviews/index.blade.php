@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header fw-bold d-flex justify-content-between align-items-center">
                    <span>{{ __('Google Reviews / Admin') }}</span>
                    <a href="{{ route('google-reviews.create') }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-plus-lg"></i> Add New Review
                    </a>
                </div>

                <div class="card-body">
                    @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle" id="reviews_table">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 80px;">Image</th>
                                    <th>Author Name</th>
                                    <th>Rating</th>
                                    <th>Review Text</th>
                                    <th>Date Added</th>
                                    <th>Type</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($reviews as $review)
                                <tr>
                                    <td>
                                        @if($review->author_img)
                                            <img src="{{ asset('storage/' . $review->author_img) }}" 
                                                 alt="Author" class="rounded-circle shadow-sm" 
                                                 style="width: 45px; height: 45px; object-fit: cover;">
                                        @else
                                            <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center text-white shadow-sm" 
                                                 style="width: 45px; height: 45px;">
                                                <i class="bi bi-person"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="fw-semibold">{{ $review->author_name }}</td>
                                    <td>
                                        <span class="badge bg-warning text-dark">
                                            {{ $review->rating }} <i class="bi bi-star-fill"></i>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="text-muted" style="max-width: 300px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                            {!! $review->review_text !!}
                                        </div>
                                    </td>
                                    <td>{{ $review->created_at->format('d M, Y') }}</td>
                                    <td class="fw-semibold">{{ $review->type }}</td>
                                    <td class="text-end">
                                            <a href="{{ route('google-reviews.edit', $review->id) }}" 
                                               class="btn btn-sm btn-outline-dark custom-action-btn me-2">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            
                                            <form action="{{ route('google-reviews.destroy', $review->id) }}" 
                                                  method="POST" 
                                                  onsubmit="return confirm('Are you sure you want to delete this review?');"
                                                  style="display: inline-block;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-dark custom-action-btn me-2">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                   
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        No reviews found. Click "Add New Review" to get started.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection