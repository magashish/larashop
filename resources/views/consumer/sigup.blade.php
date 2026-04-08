@extends('layouts.consumer')
@section('content')
<div class="hero-sec login-sec mb-0">
    <div class="container-fluid g-0">
        <div class="row align-items-stretch banner-content g-0">
            <div class="col-xl-6 col-12">
                <div class="container">
                    <div class="hero-content">
                        <div class="bg-title">
                            <div class="yellow-bg">Register  TO</div>
                            <div class="black-bg">UNLOCK SAVINGS</div>
                        </div>
                        <div class="auth-box">
                         <div class="form-wrapper">
                            <form method="POST" action="{{ route('register') }}">
                             @csrf
                             <div class="position-relative mb-3">
                              <div class="">
                                <input id="first_name" type="text" class="form-control @error('first_name') is-invalid @enderror" name="first_name" value="{{ old('first_name') }}" required autocomplete="first_name" autofocus placeholder="First Name">

                                @error('first_name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            <div class="key-icon">
                                <i class="bi bi-person-fill"></i>
                            </div>
                        </div>


                          <div class="position-relative mb-3">
                              <div class="">
                                <input id="last_name" type="text" class="form-control @error('last_name') is-invalid @enderror" name="last_name" value="{{ old('last_name') }}" required autocomplete="last_name" autofocus placeholder="Last Name">

                                @error('last_name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            <div class="key-icon">
                                <i class="bi bi-person-fill"></i>
                            </div>
                        </div>


                        <div class="position-relative mb-3">
                          <div class="">
                           <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="Email Address">

                           @error('email')
                           <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <div class="key-icon">
                        <i class="bi bi-envelope"></i>
                    </div>
                </div>


                <div class="position-relative mb-3">
                    <div class="">
                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password" placeholder="Password">

                        @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                    </div>
                    <div class="key-icon">
                     <i class="bi bi-lock"></i>
                 </div>
             </div>

             <div class="position-relative mb-3">
                <div class="">
                    <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password" placeholder="Confirm Password">
                </div>
                <div class="key-icon">
                    <i class="bi bi-lock"></i>
                </div>
            </div>

            <button type="submit" class="btn btn-primary text-uppercase w-100">SIGN UP</button>
        </form>
        <div class="form-footer text-center mt-2">
          
            <p class="mb-0">Already have an account? <a href="{{ route('login') }}" class="text-decoration-underline">Log In Here</a></p>

        </div>
    </div>
</div>


</div>
</div>
</div>

<div class="col-xl-6 col-12 position-relative">
    <div class="hero-img" style="background-image: url(images/hero-img.jpg);"></div>
    <div class="hero-slider-section">
        <div class="hero-slider-login-sec">
          <img src="images/slide1.png">
          <img src="images/slide2.png">
          <img src="images/slide1.png">
          <img src="images/slide2.png">
          <img src="images/slide1.png">
          <img src="images/slide2.png">
          <img src="images/slide1.png">
          <img src="images/slide2.png">
      </div>
  </div>
</div>                    
</div>                    
</div>
</div>



@endsection
