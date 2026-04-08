@extends('layouts.consumer')
@section('content')
<div class="hero-sec">
    <div class="container-fluid g-0">
        <div class="row align-items-center banner-content g-0">
            <div class="col-md-6 col-12">
                <div class="container">
                    <div class="hero-content">

                        <div class="bg-title">
                            <div class="yellow-bg">REAL REWARDS.</div>
                            <div class="black-bg">RIGHT NOW.</div>
                        </div>

                        <div class="action-btn">
                            @include('global.login-signup-btn')
                        </div>

                        <div class="rating-col">
                            <div class="rating-users-img">
                                <img src="images/rating-users.png">
                            </div>
                            <div class="rating-content">
                                <div class="rating-icon">
                                    <img src="images/rating.png"><span>1.5K +</span>
                                </div>
                                <p>Subscribers to date! <a href="#">View road map</a></p>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="col-md-6 col-12">
                <div class="hero-img">
                    <img src="{{ asset('images/hero-img.jpg') }}" alt="Hero Image 1">
                    <img src="{{ asset('images/hero-img.jpg') }}" alt="Hero Image 2">
                    <img src="{{ asset('images/hero-img.jpg') }}" alt="Hero Image 3">
                </div>
            </div>


            <div class="hero-slider-section">
                  <div class="hero-slider" id="hero-slider">
                    @foreach ($is_featured_offers as $offer)
                    <div class="slider-item">
                        <div class="innner-content">
                            <div class="item-content">
                                <div class="sec-tag d-flex align-item-center gap-2">
                                    @if ($offer->categories->isNotEmpty())
                                    @php
                                    $category = $offer->categories->first(); 
                                    @endphp
                                    <img src="images/dumble-icon.svg" class="img-fluid">
                                    <p class="mb-0">{{ $category->name }}</p>
                                    @endif
                                </div>
                                <h3 class="my-2">
                                    @if ($offer->organisation)
                                    {{ $offer->organisation->title }}
                                    @else
                                    Organization Name
                                    @endif
                                </h3>
                                <p>{{ $offer->title ?? 'Default Offer Title' }}</p> 
                                <a  class="btn btn-with-icon d-flex align-items-center justify-content-between offer-col login-check" data-id="{{ $offer->id }}">
                                    EXPLORE NOW
                                    <span>
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path d="M438.6 278.6c12.5-12.5 12.5-32.8 0-45.3l-160-160c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L338.8 224 32 224c-17.7 0-32 14.3-32 32s14.3 32 32 32l306.7 0L233.4 393.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0l160-160z"/></svg>
                                    </span>
                                </a>
                            </div>
                            <div class="item-img">
                              @php
                              $firstAttachment = $offer->attachments->first();
                              @endphp
                              @if ($firstAttachment) 
                              <img src="{{ Storage::url($firstAttachment->file_image) }}" class="img-thumbnail" alt="Offer Image">
                              @else
                              <img src="{{ asset('images/ubx.png') }}" class="img-fluid" alt="Default Offer Image">
                              @endif
                          </div>
                        </div>
                    </div>
                    @endforeach
                </div>
          </div>
      </div>                    
  </div>
</div>

<section class="cash-giveway">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6 col-12">
                <div class="cash-img">
                    <img src="images/australian-dollar.png">
                </div>
            </div>
            <div class="col-md-6 col-12">
                <div class="cash-content">

                    <div class="bg-title small-title">
                        <div class="yellow-bg">CASH GIVEAWAY</div>
                        <div class="black-bg">$5000.00</div>
                    </div>

                    <p>Big give away -23rd of June - Subscribe or buy a package to be in the chance to WIN!!</p>

                    <div class="action-btn">
                        @include('global.login-signup-btn')
                    </div>

                </div>
            </div>


            <div class="col-12">
                <div class="clock"></div>
            </div>

        </div>
    </div>
</section>


<section class="pricing-plan gray-bg pt-0">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-12">
                <div class="bg-title small-title">
                    <div class="yellow-bg">CHOOSE YOUR PLAN</div>
                    <div class="black-bg">REAP THE REWARDS</div>
                </div>
            </div>
        </div>

        <div class="row align-items-end">
           @foreach($plans as $plan)
           <div class="col-xl-3 col-md-6 col-12">
            <div class="pricing-table {{ strtolower($plan->name) }} {{ $plan->type }} {{ $plan->type }}_{{ $plan->id }}">
                <div class="plan-name">{{ $plan->name }}</div>
                <div class="plan-content-col">
                    <div class="plan-price">
                        <sup>$</sup>{{ $plan->price }}<sup>.00</sup>
                        <p>ONE OFF PAYMENT</p>
                    </div>
                    <div class="plan-details">
                       {!! $plan->description !!}
                   </div>
                   <div class="pricing-button">
                     @php
                     $buttonText = 'READY TO WIN?';
                     $actionRoute = route('subscription.show', $plan->id);
                     @endphp
                     <a href="{{ $actionRoute }}">
                        {{ $buttonText }}
                    </a>
                </div>
                <div class="plicing-info"> {!! $plan->sub_description !!}</div>
            </div>
        </div>
    </div>
    @endforeach

    <div class="col-12 text-center mt-3">
        <div class="action-btn">
            @include('global.login-signup-btn')
        </div>
    </div>


</div>

</div>
</section>




<section class="categories-tabs-sec">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-12">
                <div class="highlight-title">
                    <h2>SHOP BY <span>CATEGORY</span></h2>
                    <a href="{{ route('consumer.allcategory') }}">EXPLORE ALL CATEGORIES</a>
                </div>
            </div>

            <div class="col-12">
                <nav class="categories-tabs">
                    <div class="nav nav-tabs" id="nav-tab" role="tablist">
                        @foreach ($categories->sortBy('position') as $index => $category)
                        <button class="nav-link {{ $index === 0 ? 'active' : '' }}"
                        id="nav-{{ $category->id }}-tab"
                        data-bs-toggle="tab"
                        data-bs-target="#nav-{{ $category->id }}"
                        type="button" role="tab"
                        aria-controls="nav-{{ $category->id }}"
                        aria-selected="{{ $index === 0 ? 'true' : 'false' }}">
                        @if ($category->icon)
                        <img src="{{ Storage::url($category->icon) }}" alt="{{ $category->name }} icon">
                        @endif
                        <p>{{ strtoupper($category->name) }}</p>
                        </button>
                        @endforeach
                    </div>
                </nav>

                <div class="tab-content" id="nav-tabContent">
                    @foreach ($categories->sortBy('position') as $index => $category)
                    <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}"
                    id="nav-{{ $category->id }}"
                    role="tabpanel"
                    aria-labelledby="nav-{{ $category->id }}-tab"
                    tabindex="0">
                        <div class="tab-content-inner">
                            <h3 class="ps-5">{{ strtoupper($category->name) }}</h3>

                            @php
                            $offersToShowInitially = 5;
                            $totalOffersInCurrentCategory = $category->offers->count();
                            @endphp

                            @if ($category->offers->isNotEmpty())
                            <div class="row offers-container">
                                @foreach ($category->offers as $offerIndex => $offer)
                                <div class="col offer-item-col {{ $offerIndex >= $offersToShowInitially ? 'd-none' : '' }}">
                                    <div class="offer-col offer-detail" data-id="{{ $offer->id }}">
                                        @if ($offer->percentage_off && $offer->percentage_off > 0)
                                        <div class="offer-percent">{{ round($offer->percentage_off) }}% Off</div>
                                        @endif
                                        <div class="offer-col-inner">
                                            <div class="ubx-col">
                                               @php
                                               $organisationLogo = asset('images/ubx.png'); 
                                               if ($offer->organisation && $offer->organisation->image) {
                                                if (Storage::disk('public')->exists($offer->organisation->image)) {
                                                    $organisationLogo = Storage::url($offer->organisation->image);
                                                }
                                            }
                                            @endphp
                                            <img src="{{ $organisationLogo }}" alt="UBX Logo">
                                            <span>{{ $offer->organisation->title ?? 'N/A' }}</span> 
                                        </div>
                                        <div class="offer-col-img">
                                            @php
                                            $firstAttachment = $offer->attachments->first();
                                            @endphp
                                            @if ($firstAttachment)
                                            <img src="{{ Storage::url($firstAttachment->file_image) }}" class="cat-offer-img" alt="Offer Image">
                                            @else
                                            <img src="{{ asset('images/ubx.png') }}" class="cat-offer-img" alt="Default Offer Image">
                                            @endif
                                        </div>

                                            <div class="ubx-bottom">{{ $offer->title }}</div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @else
                            <div class="no-offers-message-container">
                                <div class="col-12">
                                    <p class="ps-5">No offers available for this category yet.</p>
                                </div>
                            </div>
                            @endif
                            @if ($totalOffersInCurrentCategory > $offersToShowInitially)
                            <div class="action-btn text-center mt-4">
                                <a class="load-all-offers-btn signup"
                                data-target-tab="#nav-{{ $category->id }}">
                                View More
                                </a>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

{{--  --}}



<section class="second-tabs-sec pt-0">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-11 col-12">
                <div class="work-tabs-inner">
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link " id="work-tab" data-bs-toggle="tab" data-bs-target="#work-tab-pane" type="button" role="tab" aria-controls="work-tab-pane" aria-selected="false">HOW IT WORKS</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="rewards-tab" data-bs-toggle="tab" data-bs-target="#rewards-tab-pane" type="button" role="tab" aria-controls="rewards-tab-pane" aria-selected="false">REWARDS</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="community-tab" data-bs-toggle="tab" data-bs-target="#community-tab-pane" type="button" role="tab" aria-controls="community-tab-pane" aria-selected="true">COMMUNITY</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="lifestyle-tab" data-bs-toggle="tab" data-bs-target="#lifestyle-tab-pane" type="button" role="tab" aria-controls="lifestyle-tab-pane" aria-selected="false">LIFESTYLE</button>
                        </li>

                    </ul>
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade" id="work-tab-pane" role="tabpanel" aria-labelledby="work-tab" tabindex="0">
                            <div class="work-tab-content-col">
                                <div class="row align-items-center">
                                    <div class="col-md-6 col-12">
                                        <div class="work-tab-img">
                                            <img src="images/Mobile-App-Members-portal.png">
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="work-tab-content">
                                            <div class="highlight-title"><h3>Unlock Savings, Rewards on  <span>iOS!</span></h3></div>
                                            <p>Discover the ultimate app for exclusive deals, wellness rewards, and cash giveaways—right at your fingertips. Xhale is optimized for your iOS device, ensuring a smooth and seamless experience.</p>
                                            <p>Tap into a world of savings and exciting prizes with just one click. Start your journey to self-care and rewards today</p>

                                            <div class="action-btn">
                                                @include('global.login-signup-btn')
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="rewards-tab-pane" role="tabpanel" aria-labelledby="rewards-tab" tabindex="0">

                            <div class="work-tab-content-col">
                                <div class="row align-items-center">
                                    <div class="col-md-6 col-12">
                                        <div class="work-tab-img">
                                            <img src="images/Hand-Holding-Phone.png">
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="work-tab-content">
                                            <div class="highlight-title"><h3>What are the  <span>Rewards</span></h3></div>
                                            <p>Xhale is a modern day coupon offering discounts and a monthly 10k giveaway to subscribers. Businesses get free advertising on our platforms and are entered into a monthly 1k giveaway.</p>

                                            <div class="action-btn">
                                                @include('global.login-signup-btn')
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="tab-pane fade show active" id="community-tab-pane" role="tabpanel" aria-labelledby="community-tab" tabindex="0">

                            <div class="work-tab-content-col">
                                <div class="row align-items-center">
                                    <div class="col-md-6 col-12">
                                        <div class="work-tab-img">
                                            <img src="images/diverse-people.png">
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="work-tab-content">
                                            <div class="highlight-title"><h3>BUILDING A <span>COMMUNITY</span></h3></div>
                                            <p>Xhale connects small businesses with a network of engaged subscribers, helping businesses grow while offering users exclusive deals and rewards.</p>

                                            <div class="action-btn">
                                                @include('global.login-signup-btn')
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="tab-pane fade" id="lifestyle-tab-pane" role="tabpanel" aria-labelledby="lifestyle-tab" tabindex="0">

                            <div class="work-tab-content-col">
                                <div class="row align-items-center">
                                    <div class="col-md-6 col-12">
                                        <div class="work-tab-img">
                                            <img src="images/Happy-Couple.png">
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-12">
                                        <div class="work-tab-content">
                                            <div class="highlight-title"><h3>Financial Relief for  <span>YOU!</span></h3></div>
                                            <p>Xhale offers financial relief to everyday Australians by making self-care and treating yourself more affordable through exclusive discounts and rewards.</p>

                                            <div class="action-btn">
                                                @include('global.login-signup-btn')
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>


                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


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

    <div class="partner-slider-top partner-slider">
           @foreach ($organisations_part1 as $organisation)
               @if ($organisation->image)
               <div class="parner-logo">
                 @if ($organisation->website)
                <a href="{{ $organisation->website }}" target="_blank" rel="noopener noreferrer">
                    <img src="{{ Storage::url($organisation->image) }}" alt="{{ $organisation->name }} logo">
                </a>
            @else
                <a href="{{ Storage::url($organisation->image) }}" target="_blank" rel="noopener noreferrer">
                    <img src="{{ Storage::url($organisation->image) }}" alt="{{ $organisation->name }} logo">
                </a>
            @endif
               </div>
                @endif
        @endforeach
    </div>
    <div class="partner-slider-bottom partner-slider">
        @foreach ($organisations_part2 as $organisation)
         @if ($organisation->image)
                <div class="parner-logo">
                   @if ($organisation->website)
                <a href="{{ $organisation->website }}" target="_blank" rel="noopener noreferrer">
                    <img src="{{ Storage::url($organisation->image) }}" alt="{{ $organisation->name }} logo">
                </a>
            @else
                <a href="{{ Storage::url($organisation->image) }}" target="_blank" rel="noopener noreferrer">
                    <img src="{{ Storage::url($organisation->image) }}" alt="{{ $organisation->name }} logo">
                </a>
            @endif
                </div>
                 @endif
        @endforeach
    </div>

</section>


<section class="video-review">
    <div class="row g-0 align-items-center">
        <div class="col-md-6 col-12">
            <div class="video">
                <div class="ratio ratio-16x9">
                    <video class="video" poster="{{ asset('images/hero-img.jpg') }}"src="https://www.xhale.com.au/wp-content/uploads/2025/01/Xhale-10K-Winner.mp4" controls="" preload="metadata" controlslist="nodownload"></video>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-12">
            <div class="review-slider">
                <div class="review-items">
                    <div class="author-img">
                        <img src="images/author1.png">
                        <img src="images/google-icon.svg" class="over-icon">
                    </div>
                    <div class="author-name">Kimberley Hamer</div>
                    <div class="review-date">2025-01-06</div>
                    <div class="rating-icon"><img src="images/rating.png"> </div>
                    <div class="review-content">
                        <p>I won a PS5 From an Xhale giveaway and it was shipped to me within 2 days . So grateful , it’s also an amazing company as it has lots and lots of brand discounts . Highly reccomend .</p>
                    </div>
                    <p><strong>Google</strong> rating score: <strong>5.0</strong> of 5, based on <strong>2 reviews</strong></p>
                </div>
                <div class="review-items">
                    <div class="author-img">
                        <img src="images/author1.png">
                        <img src="images/google-icon.svg" class="over-icon">
                    </div>
                    <div class="author-name">Kimberley Hamer</div>
                    <div class="review-date">2025-01-06</div>
                    <div class="rating-icon"><img src="images/rating.png"> </div>
                    <div class="review-content">
                        <p>I won a PS5 From an Xhale giveaway and it was shipped to me within 2 days . So grateful , it’s also an amazing company as it has lots and lots of brand discounts . Highly reccomend .</p>
                    </div>
                    <p><strong>Google</strong> rating score: <strong>5.0</strong> of 5, based on <strong>2 reviews</strong></p>
                </div>
            </div>
        </div>
    </div>
</section>


@include('consumer.blocks.social-feeds')


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $('.load-all-offers-btn').on('click', function() {
            var button = $(this);
            var targetTabId = button.data('target-tab'); 
            var hiddenOffers = $(targetTabId).find('.offer-item-col.d-none');
            hiddenOffers.removeClass('d-none');
            button.hide();
        });
    });
</script>
<script type="text/javascript">
    $(document).ready(function() {
        var dynamicEndDateString = {!! json_encode($countdownEndDate) !!};
        var clock = $('.clock').FlipClock({
            clockFace: 'DailyCounter',
            countdown: true
        });
        var date = new Date(dynamicEndDateString);
        var now = new Date();
        var diffMilliseconds = date.getTime() - now.getTime();
        var diffSeconds = Math.max(0, Math.floor(diffMilliseconds / 1000)); 

        clock.setTime(diffSeconds);
        clock.start();
    });
</script>

@endsection
