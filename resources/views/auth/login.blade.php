@extends('layouts.consumer')
@section('content')

<div class="hero-sec login-sec login-page mb-0">
    <div class="container-fluid g-0">
        <div class="row align-items-stretch banner-content g-0">
            <div class="col-xl-6 col-12">
                <div class="container">
                    <div class="hero-content">
                        <div class="bg-title">
                            <div class="yellow-bg">LOG IN TO</div>
                            <div class="black-bg">UNLOCK SAVINGS</div>
                        </div>

                        <div class="auth-box">
                            <div class="social-icons">
                                @include('global.social-link')
                            </div>

                            <div class="social-icons-text text-center">
                                <p class="position-relative mb-0">
                                    <span>Or use email account</span>
                                </p>
                            </div>

                            <div class="form-wrapper mt-sm-0 mt-3">

                                {{-- 🔴 AJAX ERROR --}}
                                <div id="loginError" class="alert alert-danger d-none"></div>

                                <form id="loginForm" method="POST" action="{{ route('login') }}">
                                    @csrf
                                    <div class="position-relative mb-3">
                                       <div class="">
                                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email"  placeholder="Email address" autofocus>
                                        @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                    <img src="{{ asset('images/envelope.png') }}" class="field-icon">
                                </div>
                                <div class="position-relative mb-3">
                                    <div class="">
                                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="Password">

                                        @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                    <img src="{{ asset('images/unlock.png') }}" class="field-icon">
                                </div>

                                 <!-- reCAPTCHA Widget -->
                             {{--     <div class="position-relative mb-3">
                                    <div class="g-recaptcha" data-sitekey="{{ env('NOCAPTCHA_SITEKEY') }}"></div>
                                </div> --}}


                                <button type="submit" class="btn btn-primary text-uppercase w-100">
                                    SIGN IN
                                </button>
                            </form>

                            <div class="form-footer text-center mt-2">
                                <p class="mb-0">
                                    @if (Route::has('password.request'))
                                    <a class="btn btn-link" href="{{ route('password.request') }}">
                                        Forgot Your Password?
                                    </a>
                                    @endif
                                </p>

                                <p class="mb-0">
                                    Not signed up yet?
                                    <a href="{{ route('register') }}" class="text-decoration-underline">
                                        Sign Up Here
                                    </a>
                                </p>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="col-xl-6 col-12 position-relative d-xl-flex d-none">
            <div class="hero-img" style="background-image: url({{ asset('images/login.png') }});"></div>
            @include('global.offer-hero-slider')
        </div>

    </div>
</div>
</div>

{{-- jQuery --}}
{{-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://www.google.com/recaptcha/api.js" async defer></script>

<script>
   $(document).ready(function () {
    $('#loginForm').on('submit', function (e) {
        e.preventDefault();

        let form = $(this);
        let btn = form.find('button');
        btn.prop('disabled', true).text('Signing in...');
        $('#loginError').addClass('d-none').text('');

        // Get the reCAPTCHA response
        let recaptchaResponse = grecaptcha.getResponse();

        // Check if reCAPTCHA is solved
        if (recaptchaResponse.length === 0) {
            $('#loginError').removeClass('d-none').text('Please complete the reCAPTCHA.');
            btn.prop('disabled', false).text('SIGN IN');
            return;
        }

        // Proceed with form submission if reCAPTCHA is solved
        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            success: function (res) {
                if (res.two_factor) {
                    window.location.href = res.redirect;
                } else {
                    window.location.href = "/my-account";
                }
            },
           error: function (xhr) {
    btn.prop('disabled', false).text('SIGN IN');

    let message = 'Login failed. Please try again.';

    // Handle Laravel validation error (422)
    if (xhr.status === 422 && xhr.responseJSON?.errors?.email) {
        message = xhr.responseJSON.errors.email[0];
    }

    // Handle rate limit (429)
    else if (xhr.status === 429) {
        // If you return JSON
        if (xhr.responseJSON?.message) {
            message = xhr.responseJSON.message;
        } else if (xhr.responseText) {
            // Fallback for default “Too Many Attempts.” text
            message = xhr.responseText;
        } else {
            message = 'Too many attempts. Please try again later.';
        }
    }

    $('#loginError').removeClass('d-none').text(message);
}


        });
    });
});

</script> --}}
@endsection
