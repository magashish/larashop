@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header fw-bold">{{ __('Login') }}</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.login') }}">
                        @csrf
                        <div class="row mb-3">
                            <label for="email" class="col-form-label">{{ __('Email Address') }}</label>
                            <div class="">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                                @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="password" class="col-form-label">{{ __('Password') }}</label>
                            <div class="">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                                @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>


                        <div class="row mb-3">
                            <div>
                                <label class="checkbox style-b">
                                    <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }} />
                                    <div class="checkbox__checkmark"></div>
                                    <div class="checkbox__body">
                                        {{ __('Remember Me') }}
                                    </div>
                                </label>
                            </div>
                        </div>

                        
                        {{-- <div class="row mb-3">
                            <div class="coffset-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="remember">
                                        {{ __('Remember Me') }}
                                    </label>
                                </div>
                            </div>
                        </div> --}}

                        <div class="row mb-0">
                            <div class="">
                                <div class="d-flex justify-content-between align-items-center"> 
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('Login') }}
                                    </button>
                                    @if (Route::has('password.request'))
                                    <a class="btn btn-link" href="{{ route('admin.password.request') }}">
                                        {{ __('Forgot Your Password?') }}
                                    </a>
                                    @endif
                                </div>
                            </div>
                        </div>


                        {{-- <div class="row mb-0">
                            <div class="">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Login') }}
                                </button>

                                @if (Route::has('password.request'))
                                <a class="btn btn-link" href="{{ route('admin.password.request') }}">
                                    {{ __('Forgot Your Password?') }}
                                </a>
                                @endif
                            </div>
                        </div> --}}
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
