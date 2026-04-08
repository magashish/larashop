@extends('layouts.app')
@section('content')
<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header fw-bold">{{ __('Admin / Register') }}</div>
                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('status') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    <div class="mb-3 text-end">
                        <a href="{{ route('admins.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-arrow-left"></i> Back to Admin Users Admin
                        </a>
                    </div>
                    <form method="POST" action="{{ route('admins.store') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label fw-semibold">Name</label>
                            <input id="name" type="text"
                                class="form-control @error('name') is-invalid @enderror"
                                name="name" value="{{ old('name') }}"
                                placeholder="Enter full name" required autofocus>
                            @error('name')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold">Email Address</label>
                            <input id="email" type="email"
                                class="form-control @error('email') is-invalid @enderror"
                                name="email" value="{{ old('email') }}"
                                placeholder="Enter email" required>
                            @error('email')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label fw-semibold">Password</label>
                            <input id="password" type="password"
                                class="form-control @error('password') is-invalid @enderror"
                                name="password"
                                placeholder="Enter password" required>
                            @error('password')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="password-confirm" class="form-label fw-semibold">Confirm Password</label>
                            <input id="password-confirm" type="password"
                                class="form-control"
                                name="password_confirmation"
                                placeholder="Re-enter password" required>
                        </div>

                        <div class="mb-3">
                            <label for="level" class="form-label fw-semibold">Level</label>
                            <select id="level" name="level" class="form-select @error('level') is-invalid @enderror" required>
                                @foreach($adminroles as $role)
                                <option value="{{ $role->name }}"
                                    {{ old('level', $admin->level ?? '') == $role->name ? 'selected' : '' }}>
                                    {{ Str::title(str_replace('-', ' ', $role->name)) }}
                                </option>
                                @endforeach
                            </select>
                            @error('level')
                            <div class="invalid-feedback d-block">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>


                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-person-plus-fill"></i> Register
                        </button>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
