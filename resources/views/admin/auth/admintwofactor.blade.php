@extends('layouts.app')
@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow-lg">
                <div class="card-header bg-dark text-white py-3">
                    <h4 class="mb-0">Two-Factor Verification</h4>
                </div>
                <div class="card-body">
                    @if(session()->has('message'))
                    <div class="alert alert-info text-center" role="alert">
                        {{ session()->get('message') }}
                    </div>
                    @endif

                    @if($errors->any())
                    <div class="alert alert-danger" role="alert">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <p class="text-muted mb-4">
                        You have received an email with your login token sent to
                        <strong>{{ $userEmail ?? 'your registered email address' }}</strong>. 
                        If you haven't received it, please click <a href="{{ route('admin.verify.resend') }}">here</a> to resend.
                    </p>

                    <form method="POST" action="{{ route('admin.verify.store') }}">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="two_factor_code" class="sr-only mb-3">Login Token:</label>
                            <div class="input-group">
                                <input name="two_factor_code" type="text"
                                class="form-control @error('two_factor_code') is-invalid @enderror"
                                id="two_factor_code" required autofocus
                                placeholder="Enter your 6-digit code">
                                @error('two_factor_code')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <button type="submit" class="btn btn-primary px-4">
                                Verify
                            </button>

                            <a class="btn btn-secondary px-4" href="#" onclick="event.preventDefault(); document.getElementById('logoutform').submit();">
                                {{ trans('Logout') ?? 'Logout' }}
                            </a>
                        </div>
                    </form>
                </div>
               
            </div>
        </div>
    </div>
</div>

<form id="logoutform" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
    @csrf
</form>
@endsection