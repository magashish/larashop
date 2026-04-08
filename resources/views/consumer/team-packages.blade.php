@extends('layouts.consumer')
@section('content')


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


<div class="access-column popup-wrapper">
            @include('consumer.blocks.pop-up-subscription') 
          </div> 

          
    <!-- Pricing Plan Section Start -->
    <section class="pricing-plan below-win gray-bg mt-0 pt-sm-5 pt-0">
        <div class="container">
            {{-- @include('consumer.blocks.subscription-package-list') --}}
            <div class="access-column">
                @include('consumer.blocks.pop-up-subscription-package-list') 
            </div>
           {{--  <div class="col-12 text-center mt-1 mb-3 d-lg-block d-none">
                <div class="action-btn">
                    @include('global.login-signup-btn')
                </div>
            </div> --}}
        </div>
    </section>
    <!-- Pricing Plan Section End -->

</div>










@endsection
