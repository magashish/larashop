@extends('layouts.consumer')
@section('content')

<div class="hero-sec login-sec thankyou-page mb-0">
    <div class="container-fluid g-0">
        <div class="row align-items-stretch banner-content g-0">
            <div class="col-xl-6 col-12">
                <div class="container">
                    <div class="hero-content position-relative">
                        <div class="bg-title">
                            <div class="yellow-bg">XHALE</div>
                            <div class="black-bg">THANK YOU</div>
                        </div>

                        <div class="auth-box">
                            <div class="thank-you-content">
                                <p>Welcome to the XHALE community — your membership gives you access to exclusive offers from our partners, member-only rewards, and the chance to win our amazing giveaways.</p>
                                <div class="actions-btns d-flex align-items-center gap-sm-4 gap-2">
                                    <a href="{{ route('shop') }}" class="btn btn-with-icon view-btn">
                                        <span class="btn-icon"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path d="M438.6 278.6c12.5-12.5 12.5-32.8 0-45.3l-160-160c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L338.8 224 32 224c-17.7 0-32 14.3-32 32s14.3 32 32 32l306.7 0L233.4 393.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0l160-160z"></path></svg></span>
                                        View Offers
                                    </a>
                                    <a href="{{ route('membership.dashboard') }}" class="btn btn-with-icon db-btn">
                                        My Dashboard 
                                        <span class="btn-icon"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path d="M438.6 278.6c12.5-12.5 12.5-32.8 0-45.3l-160-160c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L338.8 224 32 224c-17.7 0-32 14.3-32 32s14.3 32 32 32l306.7 0L233.4 393.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0l160-160z"></path></svg></span>                                        
                                    </a>
                                </div>
                            </div> 
                        </div>
                        <div class="sec-img">
                            <img src="{{ asset('images/notesImg.png') }}" class="img-fluid">
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-6 col-12 position-relative d-xl-flex d-none">
                <div class="hero-img" style="background-image: url({{ asset('images/car-prize-img.jpg') }});"></div>
                @include('global.offer-hero-slider')
            </div>

        </div>
    </div>
</div>


@endsection
