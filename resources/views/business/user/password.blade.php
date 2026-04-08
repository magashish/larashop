@extends('layouts.businessapp')
@section('content')
<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header fw-bold">{{ __('Change Password') }}</div>
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

                    <div class="mb-4 p-3 border rounded bg-light">
                        <h5 class="fw-bold mb-3">{{ __('Passwords must:') }}</h5>
                        <ul class="list-unstyled mb-0">
                            <li><i class="bi bi-circle-fill me-2"></i> Be at least 8 characters long</li>
                            <li><i class="bi bi-circle-fill me-2"></i> Be at max 32 characters</li>
                            <li><i class="bi bi-circle-fill me-2"></i> Include at least one UPPERCASE letter</li>
                            <li><i class="bi bi-circle-fill me-2"></i> Include at least one lowercase letter</li>
                            <li><i class="bi bi-circle-fill me-2"></i> Include at least one number</li>
                            <li><i class="bi bi-circle-fill me-2"></i> Include at least one special character, such as ~`!@#$%^&amp;*()?&lt;&gt;{}|[]\/-+</li>
                        </ul>
                    </div>


                    <form method="POST" action="{{ route('business.password.update', $user->id) }}">
                        @csrf
                        @method('PUT')

                         <div class="mb-3">
                            <label for="current_password" class="form-label fw-semibold">Current Password</label>
                            <input id="current_password" type="text"
                            class="form-control @error('current_password') is-invalid @enderror"
                            name="current_password"
                            placeholder="Enter current password"
                            value="{{ old('current_password') }}"
                            autocomplete="current_password" required> 
                            @error('current_password')
                            <div class="invalid-feedback d-block">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label fw-semibold">New Password</label>
                            <input id="password" type="text"
                            class="form-control @error('password') is-invalid @enderror"
                            name="password"
                            placeholder="Enter new password"
                            value="{{ old('password') }}"
                            autocomplete="new-password" required> 
                            @error('password')
                            <div class="invalid-feedback d-block">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="password-confirm" class="form-label fw-semibold">Confirm New Password</label>
                            <input id="password-confirm" type="text"
                            class="form-control"
                            name="password_confirmation"
                            value="{{ old('password_confirmation') }}"
                            placeholder="Re-enter new password"
                            autocomplete="new-password" required>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Update Password
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
