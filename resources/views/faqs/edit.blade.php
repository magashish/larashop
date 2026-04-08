@extends('layouts.app')
@section('content')
<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header fw-bold">{{ __('Edit FAQ') }}</div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                   <div class="mb-3 text-end">
                        <a href="{{ route('faqs.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-arrow-left"></i> Back to FAQs Admin
                        </a>
                    </div>

                    <form action="{{ route('faqs.update', $faq->id) }}" method="post">
                        @csrf
                        @method('PUT') {{-- Use PUT method for updates --}}

                        <div class="form-group mb-3">
                            <label for="question" class="form-label">Question:</label>
                            <input id="question" name="question" type="text"
                                class="form-control @error('question') is-invalid @enderror"
                                value="{{ old('question', $faq->question) }}">
                            @error('question')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="answer" class="form-label">Answer:</label>
                            <textarea id="answer" name="answer"
                                class="form-control @error('answer') is-invalid @enderror"
                                rows="5">{{ old('answer', $faq->answer) }}</textarea>
                            @error('answer')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Save FAQ
                            </button>
                          
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
