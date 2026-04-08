@extends('layouts.businessapp')
@section('content')
<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header fw-bold">{{ __('My Details') }}</div>
                <div class="card-body">
                    @if (session('status'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('status') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif
                    <div class="mb-3 text-end">
                        <a href="{{ route('business.myaccount.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-arrow-left"></i> Back to My Account
                        </a>
                    </div>
                    <form method="POST" action="{{ route('business.myaccount.update', $user->id) }}">
                        @csrf
                        @method('PUT')


                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label fw-semibold">Email Address</label>
                                    <input id="email" type="email"
                                    class="form-control @error('email') is-invalid @enderror"
                                    name="email" value="{{ old('email', $user->email) }}"
                                    placeholder="Enter email" required> 
                                    @error('email')
                                    <div class="invalid-feedback d-block">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label fw-semibold">Password</label>
                                    <div>
                                        <a href="{{ route('business.password.edit', $user->id) }}" class="btn btn-primary">
                                    <i class="bi bi-key"></i> Change password
                                </a>
                                    </div>
                                    
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6"> 
                            <div class="mb-3">
                                <label for="first_name" class="form-label fw-semibold">First Name</label>
                                <input id="first_name" type="text"
                                class="form-control @error('first_name') is-invalid @enderror"
                                name="first_name" value="{{ old('first_name', $user->first_name) }}"
                                placeholder="Enter first name" required>
                                @error('first_name')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6"> 
                            <div class="mb-3">
                                <label for="last_name" class="form-label fw-semibold">Last Name</label>
                                <input id="last_name" type="text"
                                class="form-control @error('last_name') is-invalid @enderror"
                                name="last_name" value="{{ old('last_name', $user->last_name) }}"
                                placeholder="Enter last name" required>
                                @error('last_name')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                        </div>
                    </div>


                    <div class="row">
                        <div class="col-md-6"> 
                            <div class="mb-3">
                                <label for="mobile" class="form-label fw-semibold">Mobile</label>
                                <input id="mobile" type="text"
                                class="form-control @error('mobile') is-invalid @enderror"
                                name="mobile" value="{{ old('mobile', $user->mobile) }}"
                                placeholder="Enter mobile Number" required>
                                @error('mobile')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6"> 
                            <div class="mb-3">
                               
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Save Changes
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
</div>

@endsection
