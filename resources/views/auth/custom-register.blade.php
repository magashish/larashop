@extends('layouts.consumer')
@section('content')
<div class="hero-sec login-sec mb-0">
    <div class="container-fluid g-0">
        <div class="row align-items-stretch banner-content g-0">


         <div class="col-xl-6 col-12">
            <div class="container">
                <div class="hero-content signup-multistep-wrapper">
                    <div class="bg-title mx-0 mb-5">


                        @if($package_subscription_plan->type === 'package')
                        <div class="yellow-bg">READY TO WIN</div>
                        <div class="black-bg">LET'S GO</div>
                        @else
                        <div class="yellow-bg">SIGN UP TO</div>
                        <div class="black-bg">XHALE TODAY!</div>
                        <p>Become a loyal member and start unlocking exclusive discounts from hundreds of local gems and big-name brands. Plus, rack up points for your chance to win exciting cash and prize giveaways!</p>
                        @endif

                    </div>

                    @include('global.custom-register-form')

                </div>
            </div>
        </div>


        <div class="col-xl-6 col-12 position-relative">
            <div class="hero-img" style="background-image: url({{ asset('/images/Sarah-Mazda-half.webp') }});">
                <div class="container">
                    <div class="win-mazda">
                        <img src="/images/win.png" alt="Win">
                    </div>
                </div>
            </div>
            <div class="package-wrapper-slider d-xl-block d-none">
                @if($package_subscription_plan->type === 'package')
                @else
                @include('global.register-offer-hero-slider')
                @endif

                {{-- @include('global.register-offer-hero-slider') --}}
            </div>
        </div>

        <div class="col">
            <div class="package-wrapper-slider d-xl-none d-block">
                @if($package_subscription_plan->type === 'package')
                @else
                @include('global.register-offer-hero-slider')
                @endif

                {{-- @include('global.register-offer-hero-slider') --}}
            </div>
        </div>
    </div>                    
</div>                    
</div>
</div>


@include('global.stripe')
@endsection
