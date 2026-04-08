@extends('layouts.consumer')
@section('content')



<div class="giveaways-banner-sec desktop">
    @php 
    $heroSlides = json_decode($metaData['landing_banner_slider'] ?? '[]', true); 
    @endphp
    <div class="giveaways-slider">
        @if(!empty($heroSlides) && is_array($heroSlides))
        @foreach($heroSlides as $slide)
        @if(!empty($slide) && Storage::disk('public')->exists($slide))
        <div class="giveaways-wrapper" style="background-image: url('{{ Storage::url($slide) }}'); "></div>
        @endif
        @endforeach
        @else
        {{-- Fallback if slider is empty --}}
        <div class="giveaways-wrapper" style="background-image: url('{{ asset('images/herolp-img.jpg') }}'); "></div>
        @endif
    </div>
    <div class="win-mazda">
        @php $winImg = $metaData['landing_hero_image'] ?? 'win.png'; @endphp
        @if(!empty($winImg) && (Storage::disk('public')->exists($winImg) || file_exists(public_path('images/'.$winImg))))
        <img src="{{ Str::contains($winImg, '/') ? Storage::url($winImg) : asset('images/'.$winImg) }}" alt="Win">
        @endif

        <a href="#supporting-community-sec" class="signup">Read More</a>
    </div>
</div>

<div class="giveaways-banner-sec for-mobile">
    @php 
    $heromobileSlides = json_decode($metaData['landing_banner_mobile_slider'] ?? '[]', true); 
    @endphp

    <div class="giveaways-slider">
     @if(!empty($heromobileSlides) && is_array($heromobileSlides))
     @foreach($heromobileSlides as $slide)
     @if(!empty($slide) && Storage::disk('public')->exists($slide))
     <div class="giveaways-wrapper" style="background-image: url('{{ Storage::url($slide) }}'); "></div>
     @endif
     @endforeach
     @else
     {{-- Fallback if slider is empty --}}
     <div class="giveaways-wrapper" style="background-image: url('{{ asset('images/mob-hero.jpg') }}'); "></div>
     @endif
 </div>
 <div class="win-mazda">
    @php $winImg = $metaData['landing_hero_image'] ?? 'win.png'; @endphp
    @if(!empty($winImg) && (Storage::disk('public')->exists($winImg) || file_exists(public_path('images/'.$winImg))))
    <img src="{{ Str::contains($winImg, '/') ? Storage::url($winImg) : asset('images/'.$winImg) }}" alt="Win">
    @endif
</div>
</div>

{{-- <div class="access-column">
    <div class="xhale-access">
        <div class="container">
            <div class="xhale-access-row">
                <div class="xhale-access-row">
                    <div class="jed">
                        @php $jedImg = $metaData['landing_package_image'] ?? 'jed.png'; @endphp
                        @if(!empty($jedImg))
                        <img src="{{ Str::contains($jedImg, '/') ? Storage::url($jedImg) : asset('images/jed.png') }}" alt="Jed">
                        @endif
                    </div>
                    <div class="sec-head">
                        <div class="bg-title mx-0 mb-5">
                            <div class="yellow-bg">{{ $metaData['landing_package_title'] ?? 'UNLOCK YOUR' }}</div>
                            <div class="black-bg">{{ $metaData['landing_package_subtitle'] ?? 'XHALE ACCESS' }}</div>
                        </div>
                    </div>
                </div> 
            </div>          
        </div>
    </div>

    <div class="package-plans">
        <div class="container">
            <div class="row">
                @foreach($plans as $plan)
                @php
                $price = number_format($plan->price, 2, '.', '');
                [$whole, $decimal] = explode('.', $price);

                $planName = strtolower($plan->name);
                switch ($planName) {
                    case 'bronze':
                    $buttonText = "Start with {$plan->name} - \${$whole}";
                    break;
                    case 'silver':
                    $buttonText = "Choose {$plan->name} - \${$whole}";
                    break;
                    case 'gold':
                    $buttonText = "Buy {$plan->name} - \${$whole}";
                    break;
                    case 'subscription':
                    $buttonText = "Go {$plan->name} - \${$whole}";
                    break;
                    default:
                    $buttonText = "Start with {$plan->name} - \${$whole}";
                }
                @endphp

                <div class="col-xl-3 col-6 {{ strtolower($plan->name) }} position-relative">
                    <img src="images/most-popular-img-2.png" class="popular-badge">
                    <p class="mb-0 popular-badge-text">Most Popular</p>
                    <div class="access-box {{ strtolower($plan->name) }}">
                        <div class="inner-wrapper">                            
                            <div>
                                <div class="access-title">
                                    <p class="mb-0">{{ $plan->name }}</p>
                                </div>

                                <div class="package-desc mt-3">
                                    <div class="plan-details py-2 px-3">
                                        {!! $plan->description !!}
                                    </div>

                                    @if(!empty($plan->read_more))
                                    <div class="plan-read-more smooth-toggle py-2 px-3"
                                        id="package-readmore-{{ $plan->id }}">
                                    {!! $plan->read_more !!}
                                    </div>

                                    <button type="button"
                                    class="read-more-circle"
                                    data-target="package-readmore-{{ $plan->id }}">
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                    class="arrow-icon"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="white"
                                    stroke-width="2">
                                    <path stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M19 9l-7 7-7-7" />
                                    </svg>
                                    </button>
                                    @endif
                                </div>
                            </div>

                            <div class="access-btn">
                                <a href="{{ auth()->check()
                                    ? route('subscription.show', $plan->id)
                                    : route('register') }}">
                                    {{ $buttonText }}
                                </a>
                            </div>
                         </div>
                    </div>
                </div>
    @endforeach
</div>
</div>
</div>
</div> --}}

<div class="access-column popup-wrapper">
            @include('consumer.blocks.pop-up-subscription') 
          </div> 



<div class="access-column">
    <div class="xhale-access">
        <div class="container">
            <div class="xhale-access-row">
                <div class="xhale-access-row">
                    <div class="jed">
                        @php $jedImg = $metaData['landing_package_image'] ?? 'jed.png'; @endphp
                        @if(!empty($jedImg))
                        <img src="{{ Str::contains($jedImg, '/') ? Storage::url($jedImg) : asset('images/jed.png') }}" alt="Jed">
                        @endif
                    </div>
                    <div class="sec-head">
                        <div class="bg-title mx-0 mb-5">
                            <div class="yellow-bg">{{ $metaData['landing_package_title'] ?? 'UNLOCK YOUR' }}</div>
                            <div class="black-bg">{{ $metaData['landing_package_subtitle'] ?? 'XHALE ACCESS' }}</div>
                        </div>
                    </div>
                </div> 
            </div>          
        </div>
    </div>

    @include('consumer.blocks.pop-up-subscription-package-list') 


</div>











@php 
$worthSlider = json_decode($metaData['landing_worth_slider'] ?? '[]', true); 
@endphp
<div class="win-mazda-car">
    <div class="container">
        <div class="sec-head">
            <div class="bg-title">
                <div class="yellow-bg">{{ $metaData['landing_worth_title'] ?? 'WIN A NEW MAZDA CX5' }}</div>
                <div class="black-bg">{{ $metaData['landing_worth_subtitle'] ?? 'WORTH $55,000' }}</div>
            </div>
        </div>
        @if(!empty($worthSlider))
        <div class="gallery-wrapper position-relative">
            <div class="main-slider" id="win-mazda-main-slider">
                @foreach($worthSlider as $img)
                @if(Storage::disk('public')->exists($img))
                <div><img src="{{ Storage::url($img) }}" alt="Prize Image"></div>
                @endif
                @endforeach
            </div>
            @if(count($worthSlider) > 1)
            <div class="thumb-slider" id="win-mazda-thumb">
                @foreach($worthSlider as $img)
                @if(Storage::disk('public')->exists($img))
                <div><img src="{{ Storage::url($img) }}" alt="Thumb"></div>
                @endif
                @endforeach
            </div>
            @endif
        </div>
        @endif
    </div>
</div>


@include('consumer.blocks.cash-giveaway') 
@include('global.upcoming-draws-carousel')


@if(!empty($metaData['landing_real_video_src']) && Storage::disk('public')->exists($metaData['landing_real_video_src']))
<section class="real-giveaway-sec">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-12 mb-md-5 mb-2">
                <div class="bg-title small-title">
                    <div class="yellow-bg">{{ $metaData['landing_real_title'] ?? 'REAL GIVEAWAYS' }}</div>
                    <div class="black-bg">{{ $metaData['landing_real_subtitle'] ?? 'REAL WINNERS' }}</div>
                </div>
            </div>
            <div class="col-12">
                <div class="video">
                    <div class="ratio ratio-16x9">
                        <video class="video w-full rounded-md shadow-md" 
                        poster="{{ !empty($metaData['landing_real_video_poster']) ? Storage::url($metaData['landing_real_video_poster']) : '' }}" 
                        src="{{ Storage::url($metaData['landing_real_video_src']) }}" 
                        controls preload="metadata" controlslist="nodownload">
                    </video>
                </div>
            </div>
        </div>
    </div>
</div>
</section>
@endif






@include('consumer.blocks.google-review-slider')



<section class="past-winners">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8 offset-md-4">
                <div class="bg-title small-title">
                    <div class="yellow-bg">{{ $metaData['landing_past_winner_title'] ?? 'SOME OF OUR' }}</div>
                    <div class="black-bg">{{ $metaData['landing_past_winner_subtitle'] ?? 'PAST WINNERS' }}</div>
                </div>

                <div class="past-winner-slider">
                    @php $pastWinners = json_decode($metaData['landing_past_winner_slider'] ?? '[]', true); @endphp
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
            @php $pastWinnerJed = $metaData['landing_past_winner_image'] ?? 'jed.png'; @endphp
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
                            {{ $metaData['landing_harcourt_title'] ?? 'Supporting Harcourt' }} 
                            <span>{{ $metaData['landing_harcourt_subtitle'] ?? 'Community' }}</span>
                        </h2>

                        @php
                        $fullContent = $metaData['landing_harcourt_description'] ?? '';
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

                        {{-- @if(!empty($metaData['landing_harcourt_description']))
                        {!! $metaData['landing_harcourt_description'] !!}
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
                    @if(!empty($metaData['landing_harcourt_image_1']) && Storage::disk('public')->exists($metaData['landing_harcourt_image_1']))
                    <img src="{{ Storage::url($metaData['landing_harcourt_image_1']) }}" alt="Harcourt Support 1">
                    @else
                    <img src="{{ asset('images/harcourt-img-1.jpg') }}" alt="Default Support 1">
                    @endif
                </div>
                <div class="supporting-img2">
                    @if(!empty($metaData['landing_harcourt_image_2']) && Storage::disk('public')->exists($metaData['landing_harcourt_image_2']))
                    <img src="{{ Storage::url($metaData['landing_harcourt_image_2']) }}" alt="Harcourt Support 2">
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
