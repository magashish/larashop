@extends('layouts.consumer')
@section('content')

<div class="section-wrapped">

    <!-- Win Banner Sec Start -->
    <div class="banner-outer-wrapper">
        <div class="win-banner-sec" style="background-image: url('{{ !empty($metaData['hero_img_1']) ? asset('storage/' . $metaData['hero_img_1']) : asset('images/default-hero.jpg') }}');">
            <div class="container">
                <div class="inner-content">
                    <div class="row align-items-end">
                        <div class="col-md-6">
                            <div class="deserve-win-img">
                                <img src="{{ !empty($metaData['hero_img_2']) ? asset('storage/' . $metaData['hero_img_2']) : asset('images/default-hero2.jpg') }}" alt="Hero Image 2">
                            </div>                    
                        </div>
                        <div class="col-md-6">
                            <div class="notify-box">
                                <div class="icon-box">
                                    <img src="{{ asset('images/commbank.webp') }}" class="w-100" alt="CommBank">
                                </div>
                                <div>
                                    <h6 class="mb-0">CommBank</h6>
                                    <p class="notify-desc mb-0">You've been paid <span>$10,000</span> into your account ending 4563.</p>
                                </div>
                                <div class="timing text-end d-flex justify-content-end h-100">
                                    <p class="mb-0">now</p>
                                </div>
                            </div>  
                        </div>
                    </div>            
                </div>
            </div>
        </div>
    </div>

    <!-- Pricing Plan Section Start -->
    <section class="pricing-plan below-win gray-bg mt-0 pt-sm-5 pt-0">
        <div>
            <img src="{{ asset('images/notesImg.webp') }}" class="money-img d-lg-block d-none" alt="Money Notes">
            <img src="{{ asset('images/vegBasket-img.webp') }}" class="basket-img d-lg-block d-none" alt="Vegetable Basket">
        </div>
        <div class="container">
            <div class="col-12 text-center mt-1 mb-3 d-lg-block d-none">
                <div class="action-btn">
                    @include('global.login-signup-btn')
                </div>
            </div>
        </div>
    </section>
    <!-- Pricing Plan Section End -->

</div>

<!-- Mobile Google Review Section -->
<div class="reviw-mob-sec d-lg-none d-none">
    <div class="container-fluid px-0">
        @include('consumer.blocks.google-review')
    </div>
</div>

@include('consumer.blocks.slider-cash-giveaway')

<!-- Social Feeds & Stripe Sections -->
{{-- @include('global.stripe') --}}

@if(isset($countdownEndDate))
    @push('scripts')
        <script>
            window.COUNTDOWN_END_DATE = @json($countdownEndDate);
        </script>
    @endpush
@endif

@push('scripts')
    @vite(['resources/js/home.js'])
@endpush

@endsection
