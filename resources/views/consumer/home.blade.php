@extends('layouts.consumer')
@section('content')

<div class="section-wrapped">



    <!-- Win Banner Sec Start -->
    <div class="banner-outer-wrapper">

     {{--    <div class="desktop-win-banner-slider win-banner-slider d-none d-md-block">
            @php
            $heroSliderPaths = json_decode($metaData['hero_slider'] ?? '[]', true);
            @endphp
            @if (!empty($heroSliderPaths))
            @foreach ($heroSliderPaths as $path)
            <div class="win-banner-sec"
            style="background-image: url('{{ Storage::url($path) }}');">
            <div class="container">
                <div class="win-mazda">
                    <img src="/images/deserve-win-img.png" alt="Win">
                </div>
            </div>
        </div>
        @endforeach
        @else
        <div class="win-banner-sec"
        style="background-image: url('{{ asset('images/sararh-with-car.webp') }}');">
        <div class="container">
            <div class="win-mazda">
                <img src="/images/deserve-win-img.png" alt="Win">
            </div>
        </div>
    </div>
    @endif
</div> --}}


<div class="desktop-win-banner-slider win-banner-slider d-none d-md-block">
    @php
        $dynamicSlides = json_decode($metaData['hero_dynamic_slider'] ?? '[]', true);
    @endphp
    
    @if (!empty($dynamicSlides))
        @foreach ($dynamicSlides as $key => $slide)
            <div class="win-banner-sec win-banner-sec-{{ $loop->iteration }}" 
                 @if(!empty($slide['bg_image']))
                 style="background-image: url('{{ Storage::url($slide['bg_image']) }}');"
                 @endif
            >
                <div class="container">
                    <div class="win-mazda">
                        @if(!empty($slide['fg_image']))
                            <img src="{{ Storage::url($slide['fg_image']) }}" alt="{{ $slide['text'] ?? 'Win' }}">
                        @endif
                    </div>

                    @if(!empty($slide['text']))
                            <div class="win-banner-text"> {!! $slide['text'] !!} </div>
                        @endif
                </div>
            </div>
        @endforeach
    @else
        {{-- Fallback if no slides exist --}}
        <div class="win-banner-sec"
             style="background-image: url('{{ asset('images/sararh-with-car.webp') }}');">
            <div class="container">
                <div class="win-mazda">
                    <img src="/images/deserve-win-img.png" alt="Win">
                </div>
            </div>
        </div>
    @endif
</div>

<div class="mobile-win-banner-slider win-banner-slider d-block d-md-none">
    @if (!empty($dynamicSlides))
        @foreach ($dynamicSlides as $key => $slide)

            @php
                $backgroundImage = null;

                if (!empty($slide['bg_mobile_image'])) {
                    $backgroundImage = Storage::url($slide['bg_mobile_image']);
                } elseif (!empty($slide['bg_image'])) {
                    $backgroundImage = Storage::url($slide['bg_image']);
                }
            @endphp

            <div class="win-banner-sec win-banner-sec-{{ $loop->iteration }}"
                @if($backgroundImage)
                    style="background-image: url('{{ $backgroundImage }}');"
                @endif
            >
                <div class="container">
                    <div class="win-mazda">
                        @if(!empty($slide['fg_image']))
                            <img src="{{ Storage::url($slide['fg_image']) }}" alt="{{ $slide['text'] ?? 'Win' }}">
                        @endif
                    </div>

                    @if(!empty($slide['text']))
                        <div class="win-banner-text">
                            {!! $slide['text'] !!}
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    @else
        {{-- Fallback if no slides exist --}}
        <div class="win-banner-sec"
             style="background-image: url('{{ asset('images/sararh-with-car.webp') }}');">
            <div class="container">
                <div class="win-mazda">
                    <img src="/images/deserve-win-img.png" alt="Win">
                </div>
            </div>
        </div>
    @endif
</div>




        {{-- <div class="win-banner-slider">
            <div class="win-banner-sec" style="background-image: url('{{ !empty($metaData['hero_img_1']) ? asset('storage/' . $metaData['hero_img_1']) : asset('images/default-hero.jpg') }}');">
            </div>
            <div class="win-banner-sec" style="background-image: url('images/sararh-with-car.webp');">
            </div>
            <div class="win-banner-sec" style="background-image: url('images/Fire-Brigade.jpg');">
            </div>
            <div class="win-banner-sec" style="background-image: url('images/win-banner-img-2.jpg');">
            </div>
        </div> --}}

        <!-- Slider Banner Content -->
        <!-- <div class="win-banner-slider-content">
            <div class="container">
                <div class="inner-content">
                    <div class="row align-items-end">
                        <div class="banner-outer-content">
                            <div class="container">
                                <div class="hero-content text-center" >
                                    <div class="bg-title">
                                        <img src="{{ !empty($metaData['hero_img_2']) ? asset('storage/' . $metaData['hero_img_2']) : asset('images/default-hero2.jpg') }}" alt="Hero Image 2">
                                        {{-- <div class="yellow-bg">WIN A NEW</div>
                                        <div class="black-bg">MAZDA</div> --}}
                                    </div>
                                    <div class="banner-auth-box">
                                        <p class="position-relative">
                                            Join the draw today and every dollar of profit goes directly to supporting the Harcourt community impacted by the recent Victorian bushfires.
                                            
                                        </p>

                                            {{-- <a href="{{ route('packages') }}" class="btn banner-auth-box-btn btn-with-icon d-flex align-items-center justify-content-end">
                                                 FIND OUT HOW
                                                 <span>
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                                                        <path d="M438.6 278.6c12.5-12.5 12.5-32.8 0-45.3l-160-160c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L338.8 224 32 224c-17.7 0-32 14.3-32 32s14.3 32 32 32l306.7 0L233.4 393.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0l160-160z"/>
                                                    </svg>
                                                </span>
                                            </a> --}}
                                  


                                    </div>
                                </div>
                            </div>
                        </div>


                        {{-- <div class="col-xl-12 col-12 banner-outer-content">
                            <div class="container">
                                <div class="hero-content w-50 text-center">
                                    <div class="bg-title">
                                        <div class="yellow-bg">WIN A NEW</div>
                                        <div class="black-bg">MAZDA</div>
                                    </div>
                                    <div class="banner-auth-box">
                                        <p class="position-relative mb-0">
                                            Join the draw today and every dollar of profit goes directly to supporting Harcourt communities impacted by the recent Victorian bushfires.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div> --}}


                        {{-- <div class="col-md-12 deserve-win-img-content">
                            <div class="deserve-win-img">
                                <img src="{{ !empty($metaData['hero_img_2']) ? asset('storage/' . $metaData['hero_img_2']) : asset('images/default-hero2.jpg') }}" alt="Hero Image 2">
                            </div>                    
                        </div> --}}

                        {{-- <div class="col-md-6 commbankcontent">
                            <div class="notify-box">
                                <div class="icon-box">
                                    <img src="{{ asset('images/commbank.webp') }}" class="w-100" alt="CommBank">
                                </div>
                                <div class="commbank">
                                    <h6 class="mb-0">CommBank</h6>
                                    <p class="notify-desc mb-0">You've been paid <span>$10,000</span> into your account ending 4563.</p>
                                </div>
                                <div class="timing text-end d-flex justify-content-end h-100">
                                    <p class="mb-0">now</p>
                                </div>
                            </div>  
                        </div> --}}
                    </div>            
                </div>
            </div>
        </div> -->
    </div>

    <!-- Pricing Plan Section Start -->
    <section class="pricing-plan below-win gray-bg mt-0 pt-sm-5 pt-0">
        <div>
            <img src="{{ asset('images/notesImg.webp') }}" class="money-img d-lg-block d-none" alt="Money Notes">
            <img src="{{ asset('images/vegBasket-img.webp') }}" class="basket-img d-lg-block d-none" alt="Vegetable Basket">
        </div>
        <div class="container">
            {{-- @include('consumer.blocks.subscription-package-list') --}}

           <div class="access-column pt-5 mt-lg-2 position-relative z-1">
            @include('consumer.blocks.pop-up-subscription-package-list', ['customRegister' => true])
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


<div class="access-column popup-wrapper">
    @include('consumer.blocks.pop-up-subscription', ['customRegister' => true])
</div> 

<!-- Mobile Google Review Section -->
<div class="reviw-mob-sec d-lg-none d-none">
    <div class="container-fluid px-0">
        @include('consumer.blocks.google-review')
    </div>
</div>

@include('consumer.blocks.cash-giveaway') 
{{-- @include('consumer.blocks.slider-cash-giveaway') --}}


<!-- Categories & Offers Section -->
<section class="categories-tabs-sec mb-0">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-12">
                <div class="highlight-title d-lg-flex d-none">
                    <h2>SHOP BY <span>CATEGORY</span></h2>
                    <a href="{{ route('consumer.allcategory') }}">EXPLORE ALL CATEGORIES</a>
                </div>
                <div class="bg-title small-title d-lg-none d-block">
                    <div class="yellow-bg">SHOP BY</div>
                    <div class="black-bg">CATEGORY</div>
                </div>
            </div>
            @include('consumer.blocks.category-organisations-offers-tab', ['categories' => $categories ,'offerclass' => 'home-offer-detail' ])
        </div>
    </div>
</section>

<!-- How it Works Tabs Section -->
<section class="second-tabs-sec pt-lg-0">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-11 col-12">
                <div class="work-tabs-inner">
                    <!-- Tabs Navigation -->
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        @foreach($tabData as $tabKey => $tab)
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ $loop->first ? 'active' : '' }}"
                                id="{{ $tabKey }}-tab"
                                data-bs-toggle="tab"
                                data-bs-target="#{{ $tabKey }}-tab-pane"
                                type="button"
                                role="tab">
                                {{ $tab['title_line1'] ?? ucfirst($tabKey) }}
                            </button>
                        </li>
                        @endforeach
                    </ul>
                    <!-- Tabs Content -->
                    <div class="tab-content" id="myTabContent">
                        @foreach($tabData as $tabKey => $tab)
                        <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
                            id="{{ $tabKey }}-tab-pane"
                            role="tabpanel">
                            <div class="work-tab-content-col">
                                <div class="row align-items-center">
                                    <div class="col-lg-6 col-12 order-lg-1 order-2">
                                        <div class="work-tab-img">
                                            @if(!empty($tab['image']))
                                            <img class="lazy lozad" data-src="{{ Str::startsWith($tab['image'], ['http://','https://']) ? $tab['image'] : asset('storage/' . $tab['image']) }}" alt="{{ $tab['title_line1'] ?? 'Work Tab Image' }}">
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-12 order-lg-2 order-1">
                                        <div class="work-tab-content text-lg-start text-center">
                                            <div class="highlight-title">
                                                <h3>{!! $tab['title_line2'] ?? '' !!}</h3>
                                            </div>
                                            {!! $tab['paragraph'] ?? '' !!}
                                            <div class="action-btn d-flex flex-wrap align-items-center justify-content-center gap-2">
                                                @include('global.login-signup-btn')
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Partners Section with two sliders -->
<section class="partners-sec darkgray-bg">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-12">
                <div class="bg-title small-title">
                    <div class="yellow-bg">OUR BUSINESS</div>
                    <div class="black-bg">PARTNERS</div>
                </div>
            </div>
        </div>
    </div>
    <div class="partner-slider-top partner-slider" dir="rtl">
        @foreach ($organisations_part1 as $organisation)
        @if (!empty($organisation->image))
        <div class="parner-logo">
            <a href="{{ Auth::check() ? route('discounts', ['busness' => $organisation->id]) : route('login') }}" target="_blank">
                <img class="lazy " src="{{ Str::startsWith($organisation->image, ['http://','https://']) ? $organisation->image : Storage::url($organisation->image) }}" alt="{{ $organisation->name }} logo">
            </a>
        </div>
        @endif
        @endforeach
    </div>
    <div class="partner-slider-bottom partner-slider">
        @foreach ($organisations_part2 as $organisation)
        @if (!empty($organisation->image))
        <div class="parner-logo">
            <a href="{{ Auth::check() ? route('discounts', ['busness' => $organisation->id]) : route('login') }}" target="_blank">
                <img class="lazy " src="{{ Str::startsWith($organisation->image, ['http://','https://']) ? $organisation->image : Storage::url($organisation->image) }}" alt="{{ $organisation->name }} logo">
            </a>
        </div>
        @endif
        @endforeach
    </div>
</section>

<!-- Video Review Section -->
<section class="video-review d-lg-block d-none">
    <div class="row g-0 align-items-center">
        <div class="col-md-6 col-12">
            <div class="video">
                <div class="ratio ratio-16x9">
                    @if(!empty($metaData['hero_video_src']))
                    <video 
                    class="video w-full rounded-md shadow-md" 
                    poster="{{ !empty($metaData['hero_video_poster']) ? Storage::url($metaData['hero_video_poster']) : asset('images/hero-img.jpg') }}" 
                    src="{{ Storage::url($metaData['hero_video_src']) }}" 
                    controls preload="metadata" controlslist="nodownload">
                </video>
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-6 col-12">
        @include('consumer.blocks.google-review')
    </div>
</div>
</section>

<!-- Social Feeds & Stripe Sections -->
@include('consumer.blocks.social-feeds')
{{-- @include('global.stripe') --}}



@endsection
