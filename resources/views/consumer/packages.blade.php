@extends('layouts.consumer')
@section('content')

<!-- <div class="hero-sec packages-quote-sec">
    <div class="container ">
        <div class="row position-relative">
            <div class="banner">
                <img class="w-100 d-lg-block d-none" src="images/package-desktop-banner.jpg" alt="">
                <img class="w-100 d-lg-none d-block" src="images/pacakge-mob-banner.jpg" alt="">
            </div>
            <div class="hero-content-box">
                <div class="bg-title">
                    <div class="yellow-bg">REAL REWARDS.</div>
                    <div class="black-bg">YOUR WAY.</div>
                </div>
            </div>
            <div class="content-box d-lg-block d-none">

                <div class="image-box">
                    <img src="images/Screenshot_2025-06-26_125700-removebg-preview.png" alt="">
                </div>
                <p class="medium">Your wellness destination to access deals, support local and prioritise your
                    wellbeing and be
                in for the chance to win cash and prizes!</p>
            </div>
        </div>
    </div>
</div> -->

<div class="section-wrapped">

    <!-- Win Banner Sec Start -->
    <div class="banner-outer-wrapper">
        <div class="win-banner-sec cash-car-banner" style="background-image: url('../images/package-page-banner.jpg');">
            <div class="container">
                <div class="inner-content">
                    <div class="row align-items-end">
                        <div class="col-md-6 position-relative">
                            <div class="deserve-win-img">
                                <img src="../images/hero-banner-text.png">
                            </div>
                            <div>
                                <img src="../images/notesImg.png" class="money-img d-lg-block d-none">
                            </div>
                        </div>
                    </div>            
                </div>
            </div>
        </div>
    </div>
    <!-- Win Banner Sec End -->


    <!-- Pricing Plan Section Start -->
    <section class="pricing-plan below-win gray-bg mt-0 pt-sm-5 pt-0">
        <div class="container">
            {{-- @include('consumer.blocks.subscription-package-list') --}}
            <div class="access-column">
                @include('consumer.blocks.pop-up-subscription-package-list') 
            </div>
            <div class="col-12 text-center mt-1 mb-3 d-lg-block d-none">
                <div class="action-btn">
                    @include('global.login-signup-btn')
                </div>
            </div>
        </div>
    </section>
    <!-- Pricing Plan Section End -->

</div>



 @include('consumer.blocks.cash-giveaway')



<!-- Page Banner Sec Start -->
<section class="image-banner-sec pb-0 d-none">
    <div class="container">
        <div class="inner-content">
            <img src="{{ asset('images/package-desktop-banner.jpg') }}" class="d-lg-block d-none" alt="Banner Image">
            <img src="{{ asset('images/pacakge-mob-banner.jpg') }}" class="d-lg-none d-block" alt="Banner Image">
        </div>
        <div class="bg-title" style="max-width: fit-content;">
            <div class="yellow-bg text-uppercase">XHALE</div>
            <div class="black-bg text-uppercase d-block"><h1>YOUR WAY.</h1></div>
        </div>
    </div>
</section>
<!-- Page Banner Sec End -->

<!-- Text Banner Sec Start -->
<section class="packages-quote-sec pt-lg-0 pb-5 d-lg-none d-none">
    <div class="container ">
        <div class="row">
            <div class="content-box">
                <div class="image-box">
                    <img src="images/Screenshot_2025-06-26_125700-removebg-preview.png" alt="">
                </div>
                <p class="medium">Your wellness destination to access deals, support local and prioritise your
                    wellbeing and be in for the chance to win cash and prizes!</p>
            </div>
        </div>
    </div>
</section>
<!-- Text Banner Sec End -->


<!-- Video Review Section -->
<section class="video-review d-lg-block d-none">
    <div class="row g-0 align-items-center">
        <div class="col-md-6 col-12">
            <div class="video">
                <div class="ratio ratio-16x9">
                    @if(isset($metaData['hero_video_src']))
                    <video class="video" poster="{{ Storage::url($metaData['hero_video_poster']) ?? asset('images/hero-img.jpg') }}" class="w-full rounded-md shadow-md" src="{{ Storage::url($metaData['hero_video_src']) }}" controls="" preload="metadata" controlslist="nodownload"></video>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-6 col-12">
            @include('consumer.blocks.google-review')
        </div>
    </div>
</section>



{{-- @include('consumer.blocks.subscription') --}}
@include('consumer.blocks.social-feeds') 








@endsection
