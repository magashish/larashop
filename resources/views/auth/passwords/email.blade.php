@extends('layouts.consumer')
@section('content')


<div class="hero-sec login-sec login-page mb-0">
    <div class="container-fluid g-0">
        <div class="row align-items-stretch banner-content g-0">
            <div class="col-xl-6 col-12">
                <div class="container">
                    <div class="hero-content">
                        <div class="bg-title">
                            <div class="yellow-bg">Xhale</div>
                            <div class="black-bg">{{ __('Reset Password') }}</div>
                        </div>
                        <div class="auth-box">
                         
                           <div class="form-wrapper mt-sm-0 mt-3">
                             @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    <form method="POST" action="{{ route('password.email') }}">
                        @csrf
                        <div class="position-relative mb-3">
                            <div class="position-relative mb-3">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="Email Address">
                                 <img src="{{ asset('images/unlock.png') }}" class="field-icon">

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                       <div class="position-relative mb-3">
                            <div class="">
                                <button type="submit" class="btn btn-primary w-100">
                                    {{ __('Reset Password') }}
                                </button>
                            </div>
                        </div>
                    </form>
                    </div>
                </div>


            </div>
        </div>
    </div>

    <div class="col-xl-6 col-12 position-relative d-xl-flex d-none">
        <div class="hero-img" style="background-image: url('{{ asset('images/login.png') }}');"></div>

         @include('global.offer-hero-slider')
</div>                    
</div>                    
</div>
</div>

@endsection
