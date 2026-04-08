@extends('layouts.consumer')
@section('content')




<!-- Page Banner Sec Start -->
<section class="image-banner-sec pb-lg-4">
    <div class="container">
        <div class="inner-content">
             @php $pastWinnerJed = $metaData['grange_hero_image'] ?? 'jed.png'; @endphp
            <img src="{{ Str::contains($pastWinnerJed, '/') ? Storage::url($pastWinnerJed) : asset('images/Happy_winner_of_a_new_car.webp') }}">


            @php $grange_sub_hero_image = $metaData['grange_sub_hero_image'] ?? 'jed.png'; @endphp
            <img src="{{ Str::contains($grange_sub_hero_image, '/') ? Storage::url($grange_sub_hero_image) : asset('images/Happy_winner_of_a_new_car.webp') }}" class="side-img d-lg-block d-none">


        </div>
        <div class="bg-title">
            <div class="yellow-bg">{{ $metaData['grange_title'] ?? 'UNLOCK YOUR' }}</div>
            <div class="black-bg"><h1>{{ $metaData['grange_subtitle'] ?? 'UNLOCK YOUR' }}</h1></div>
        </div>
    </div>
</section>
<!-- Page Banner Sec End -->


@php
$offerIds = json_decode($metaData['grange_featured_offers'] ?? '[]', true);
@endphp
@php
$offers = get_all_offers()->whereIn('id', $offerIds);
@endphp
<section class="exclusive-offers-section">
    <div class="container">
        <div class="highlight-title d-lg-flex">
                    <h2 class="exclusive-title"> {!! $metaData['grange_exclusive_title'] ?? 'check their exclusive offers' !!}</h2>
         </div>
      
        <div class="offers-container desktop-view">
            @foreach($offers as $offer)
            @php
            $organization = $offer->organisation;
            $firstAttachment = optional($offer)->attachments->first();
            $offerPercentage = optional($offer)->percentage_off;

            $organizationLogo = asset('images/ubx.png');
            if (!empty($organization->image) && Storage::disk('public')->exists($organization->image)) {
                $organizationLogo = Storage::url($organization->image);
            }
            $imageUrl = !empty($firstAttachment?->file_image) ? Storage::url($firstAttachment->file_image) : $organizationLogo;
            @endphp

            <div class="offer-item-col">
                <div class="offer-col" >
                    @if ($offerPercentage > 0)
                    <div class="offer-percent">{{ round($offerPercentage) }}% Off</div>
                    @endif

                    <div class="offer-col-inner">
                        <div class="ubx-col">
                            <img src="{{ $organizationLogo }}" alt="{{ $organization->title }} Logo">
                            <span>{{ $organization->title }}</span>
                        </div>
                        <div class="offer-col-img">
                            <img data-src="{{ $imageUrl }}" class="cat-offer-img lazy lozad" alt="Offer Image"
                            onerror="this.onerror=null;this.src='{{ asset('images/ubx.png') }}'">
                        </div>
                        <div class="ubx-bottom">{{ $offer->title }}</div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <div class="exclusive-cta">
            <span>WANT THESE EXCLUSIVE OFFERS? <b>SIGN UP TODAY</b></span>
            <div class="col-12 text-center mt-1 mb-3 d-lg-block d-none">
                <div class="action-btn">
                    @include('global.login-signup-btn')
                </div>
            </div>
        </div>
    </div>
</section>



<div class="access-column">
    <div class="xhale-access">
        <div class="container">
            <div class="xhale-access-row">
                <div class="xhale-access-row">
                    <div class="sec-head">
                        <div class="bg-title mx-0 mb-5">
                            <div class="yellow-bg">{{ $metaData['grange_package_title'] ?? 'UNLOCK YOUR' }}</div>
                            <div class="black-bg">{{ $metaData['grange_package_subtitle'] ?? 'XHALE ACCESS' }}</div>
                        </div>
                    </div>
                </div> 
            </div>          
        </div>
    </div>

    @include('consumer.blocks.pop-up-subscription-package-list') 
</div>


<div class="access-column popup-wrapper">
            @include('consumer.blocks.pop-up-subscription') 
          </div> 







@include('consumer.blocks.cash-giveaway') 





<section class="past-winners">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8 offset-md-4">
                <div class="bg-title small-title">
                    <div class="yellow-bg">{{ $metaData['grange_past_winner_title'] ?? 'SOME OF OUR' }}</div>
                    <div class="black-bg">{{ $metaData['grange_past_winner_subtitle'] ?? 'PAST WINNERS' }}</div>
                </div>

                <div class="past-winner-slider">
                    @php $pastWinners = json_decode($metaData['grange_past_winner_slider'] ?? '[]', true); @endphp
                    @if(!empty($pastWinners))
                    @foreach($pastWinners as $winnerImg)
                    @if(Storage::disk('public')->exists($winnerImg))
                    <div class="winner-review-wrapper">
                        <img src="{{ Storage::url($winnerImg) }}" alt="Winner" class="img-fluid rounded">
                    </div>
                    @endif
                    @endforeach
                    @else
                    {{-- Statics if no data --}}
                    <div><img src="{{ asset('images/winner.png') }}"></div>
                    <div><img src="{{ asset('images/winner.png') }}"></div>
                    @endif
                </div>
            </div>
        </div>

        <div class="jed">
            @php $pastWinnerJed = $metaData['grange_past_winner_image'] ?? 'jed.png'; @endphp
            <img src="{{ Str::contains($pastWinnerJed, '/') ? Storage::url($pastWinnerJed) : asset('images/jed.png') }}">
        </div>  
    </div>
</section>




<div class="supporting-community" id="supporting-community-sec">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 col-12 order-lg-1 order-2">
                <div class="supporting-community-contnet mb-lg-0 mb-5">                  
                    <div class="supporting-community-inner">
                        <h2>
                            {{ $metaData['grange_harcourt_title'] ?? 'Supporting Harcourt' }} 
                            <span>{{ $metaData['grange_harcourt_subtitle'] ?? 'Community' }}</span>
                        </h2>

                        @php
                        $fullContent = $metaData['grange_harcourt_description'] ?? '';
                        $plainText   = trim(strip_tags($fullContent));
                        $words       = preg_split('/\s+/', $plainText);

                        $wordLimit = 67;
                        $isLong = count($words) > $wordLimit;

                        $shortText = implode(' ', array_slice($words, 0, $wordLimit));
                        @endphp

                        <div class="harcourt-description" id="harcourtDesc">
                            {{-- Short content (mobile preview) --}}
                            <div class="harcourt-short">
                                {!! nl2br(e($shortText)) !!}{{ $isLong ? ' ' : '' }}
                            </div>

                            {{-- Full content --}}
                            <div class="harcourt-full">
                                {!! $fullContent !!}
                            </div>

                            @if($isLong)
                            <button type="button" class="read-toggle" id="readToggle">
                                Read more >
                            </button>
                            @endif
                        </div>

                        {{-- @if(!empty($metaData['grange_harcourt_description']))
                        {!! $metaData['grange_harcourt_description'] !!}
                        @else
                        <p>In early January, devastating bushfires tore through parts of Victoria... (Default text)</p>
                        @endif --}}
                        <div class="action-btn d-flex flex-wrap align-items-center gap-2">
                            <a class="login" href="{{ route('login') }}">Log In</a>
                            <a class="signup" data-bs-toggle="modal" data-bs-target="#SignUpModal">Sign Up</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-12 order-lg-2 order-1">
                <div class="supporting-img1">
                    @if(!empty($metaData['grange_harcourt_image_1']) && Storage::disk('public')->exists($metaData['grange_harcourt_image_1']))
                    <img src="{{ Storage::url($metaData['grange_harcourt_image_1']) }}" alt="Harcourt Support 1">
                    @else
                    <img src="{{ asset('images/harcourt-img-1.jpg') }}" alt="Default Support 1">
                    @endif
                </div>
                <div class="supporting-img2">
                    @if(!empty($metaData['grange_harcourt_image_2']) && Storage::disk('public')->exists($metaData['grange_harcourt_image_2']))
                    <img src="{{ Storage::url($metaData['grange_harcourt_image_2']) }}" alt="Harcourt Support 2">
                    @else
                    <img src="{{ asset('images/harcourt-img-2.jpg') }}" alt="Default Support 2">
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>



@include('consumer.blocks.social-feeds')

<script>
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.read-more-circle');
        if (!btn) return;
        const targetId = btn.dataset.target;
        const content = document.getElementById(targetId);
        if (!content) return;
        content.classList.toggle('active');
        btn.classList.toggle('active');
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const container = document.getElementById('harcourtDesc');
        const button = document.getElementById('readToggle');

        if (!container || !button) return;

        button.addEventListener('click', function () {
            const expanded = container.classList.toggle('expanded');
            button.textContent = expanded ? 'Read less <' : 'Read more >';
        });
    });
</script>



@endsection
