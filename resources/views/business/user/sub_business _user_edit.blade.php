@extends('layouts.businessapp')
@section('content')
<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header fw-bold">{{ __('User / Edit') }}</div>
                <div class="card-body">
                    @if (session('status'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('status') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif
                    <div class="mb-3 text-end">
                        <a href="{{ route('business.users.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-arrow-left"></i> Back to Users Admin
                        </a>
                    </div>
                    <form method="POST" action="{{ route('business.users.update', $user->id) }}">
                        @csrf
                        @method('PUT')
                    


                         <div class="row">
                            <div class="col-md-6"> {{-- Column for First Name --}}
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
                            <div class="col-md-6"> {{-- Column for Last Name --}}
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
                        
                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold">Email Address</label>
                            <input id="email" type="email"
                            class="form-control @error('email') is-invalid @enderror"
                            name="email" value="{{ old('email', $user->email) }}"
                            placeholder="Enter email" required readonly> 
                            @error('email')
                            <div class="invalid-feedback d-block">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label fw-semibold">New Password <small class="text-muted">(leave blank to keep current)</small></label>
                            <input id="password" type="password"
                            class="form-control @error('password') is-invalid @enderror"
                            name="password"
                            placeholder="Enter new password"
                            autocomplete="new-password"> {{-- Added autocomplete attribute --}}
                            @error('password')
                            <div class="invalid-feedback d-block">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="password-confirm" class="form-label fw-semibold">Confirm New Password</label>
                            <input id="password-confirm" type="password"
                            class="form-control"
                            name="password_confirmation"
                            placeholder="Re-enter new password"
                            autocomplete="new-password"> {{-- Added autocomplete attribute --}}
                        </div>

                        {{-- Level Field --}}



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





                        {{-- Status Field --}}
                        <div class="mb-4">
                            <label for="status" class="form-label fw-semibold">Status</label>
                            <select id="status" name="status" class="form-select @error('status') is-invalid @enderror" required>
                                <option value="active" {{ old('status', $user->status) == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="deactive" {{ old('status', $user->status) == 'deactive' ? 'selected' : '' }}>Deactive</option>
                            </select>
                            @error('status')
                            <div class="invalid-feedback d-block">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>


                     
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Update User
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection
