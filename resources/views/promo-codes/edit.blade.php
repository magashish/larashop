@extends('layouts.app') {{-- Adjust this to your main layout file, e.g., 'layouts.admin' for admin panel --}}
@section('content')
<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-md-12"> {{-- Adjusted column width for the form --}}
            <div class="card shadow">
                <div class="card-header fw-bold">{{ __('Promo Codes / Edit') }}</div>
                <div class="card-body">
                    @if (session('status'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('status') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif
                    <div class="mb-3 text-end">
                        <a href="{{ route('promo_codes.index') }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-arrow-left"></i> Back to Promo Codes Admin
                        </a>
                    </div>
                    <form action="{{ route('promo_codes.update', $promoCode->id) }}" method="POST">
                        @csrf
                        @method('PUT') {{-- Use PUT method for updates --}}

                        <div class="form-group mb-3">
                            <label for="influencer" class="form-label fw-semibold">Influencer:</label>
                            <input type="text" class="form-control @error('influencer') is-invalid @enderror" id="influencer" name="influencer" value="{{ old('influencer', $promoCode->influencer) }}">
                            @error('influencer')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="start_date" class="form-label fw-semibold">Start date:</label>
                            <input type="text" class="form-control datepicker @error('start_date') is-invalid @enderror" id="start_date" name="start_date" value="{{ old('start_date', $promoCode->start_date->format('Y-m-d')) }}" required>
                            @error('start_date')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="end_date" class="form-label fw-semibold">End date:</label>
                            <input type="text" class="form-control datepicker @error('end_date') is-invalid @enderror" id="end_date" name="end_date" value="{{ old('end_date', $promoCode->end_date->format('Y-m-d')) }}" required>
                            @error('end_date')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Update Promo Code
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
