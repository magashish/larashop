@extends('layouts.consumer')
@section('content')

<!-- Page Banner Sec Start -->
<section class="image-banner-sec pb-0">
    <div class="container">
        <div class="inner-content">
            <img src="{{ asset('images/partner.png') }}" class="d-lg-block d-none" alt="Banner Image">
            <img src="{{ asset('images/partner-mob.png') }}" class="d-lg-none d-block" alt="Banner Image">
        </div>
        <div class="bg-title">
            <div class="yellow-bg text-uppercase">XHALE</div>
            <div class="black-bg text-uppercase">{{ $organisation->title }}</div>
        </div>
    </div>
</section>




<section class="offerModal business-details-sec">
    <div class="container ">
        <div class="row">

        <div class="modal-content rounded-0">

            @if($hasActiveSubscription)

            <div class="modal-body p-0">
                @php
                $modalBanner = 'images/offer-img.png'; 
                $organisationLogo = asset('images/ubx.png'); 

                $liveOffers = $organisation->offers->filter(function ($offer) {
                    $today = \Carbon\Carbon::now();
                    return $offer->start_date <= $today && $offer->end_date >= $today;
                });
                $firstOffer = $liveOffers->first();
                $firstAttachment = null;
                $categoryTitle = 'N/A'; 
                if ($firstOffer) {
                    $firstCategory = $firstOffer->categories->first();
                    if ($firstCategory) {
                      $categoryTitle = $firstCategory->name;
                  }
                  if ($firstOffer->attachments->isNotEmpty()) {
                     $firstAttachment = $firstOffer->attachments->first();
                     $modalBanner =  Storage::url($firstAttachment->file_image);
                 }
             }
             
             if ($organisation->image) {
                if (Storage::disk('public')->exists($organisation->image)) {
                    $organisationLogo = Storage::url($organisation->image);
                }
            }
            @endphp
            <div class="header-wrapper position-relative">
                <!-- <img src="{{ $modalBanner }}" class="modal-banner img-fluid w-100" alt="Offer Banner"> -->
                <img src="{{ $organisationLogo }}" class="modal-logo img-fluid" alt="Organisation Logo">
            </div>
            <div class="offer-wrapper">
                <div class="offer-inner rounded-4">
                 <div class="accordion" id="accordionExample">
                    @forelse ($organisation->offers as $key => $offer)
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading-{{ $offer->id }}">
                            <button class="accordion-button {{ $key === 0 ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-{{ $offer->id }}" aria-expanded="{{ $key === 0 ? 'true' : 'false' }}" aria-controls="collapse-{{ $offer->id }}">
                                {{ $offer->title }} @if ($offer->custom_redemption_code) - {{ $offer->custom_redemption_code }}  @endif   
                            </button>
                        </h2>
                        <div id="collapse-{{ $offer->id }}" class="accordion-collapse collapse {{ $key === 0 ? 'show' : '' }}" aria-labelledby="heading-{{ $offer->id }}" data-bs-parent="#accordionExample">
                            <div class="accordion-body">
                               
                                @if ($offer->custom_redemption_code)
                                <div class="text-center">
                                    <div class="heading-tag text-uppercase"><i class="bi bi-tags"></i> Discount Code</div>
                                </div>
                                <div class="discount-bar mt-2">
                                    <span class="discount-code text-uppercase" id="discountCode-{{ $offer->id }}">{{ $offer->custom_redemption_code }}</span>
                                    <button class="copy-btn" onclick="copyCode('discountCode-{{ $offer->id }}')" title="Copy code">
                                        <i class="bi bi-files"></i>
                                        <span class="tooltip" id="tooltip-{{ $offer->id }}">Copied!</span>
                                    </button>
                                </div>
                                @endif
                                <div class="row">
                                    <div class="col-12 mb-3">
                                        <div class="detail-wrapper">
                                            <div class="heading-tag text-uppercase"><i class="bi bi-chat-right-fill"></i> Offer</div>
                                            <p class="mb-0">{!! $offer->description ?? '' !!}</p>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 mb-3">
                                        <div class="detail-wrapper category-wrapper">
                                            <div class="heading-tag text-uppercase"><i class="bi bi-boxes"></i> Category</div>
                                            @forelse ($offer->categories as $category)
                                            <div class="category-box">{{ $category->name }}</div>
                                            @empty
                                            <div class="category-box">Uncategorized</div>
                                            @endforelse
                                        </div>
                                    </div>
                                    <div class="col-lg-6 mb-3">
                                        <div class="detail-wrapper business-detail-wrapper">
                                            <div class="heading-tag text-uppercase"><i class="bi bi-boxes"></i> Business Details</div>
                                            <ul class="list-unstyled">
                                                @if($offer->organisation->website ?? null)
                                                <li><a href="{{ $offer->organisation->website }}" target="_blank">{{ $offer->organisation->website }}</a></li>
                                                @endif
                                                @if($offer->organisation->mobile ?? null)
                                                <li><a href="tel:{{ $offer->organisation->mobile }}">{{ $offer->organisation->mobile }}</a></li>
                                                @endif
                                                @if($offer->organisation->full_address ?? null)
                                                <li>{{ $offer->organisation->full_address }}</li>
                                                @elseif(($offer->organisation->street_name ?? null) || ($offer->organisation->city ?? null))
                                                <li>
                                                    {{ $offer->organisation->street_name ?? '' }}{{ ($offer->organisation->street_name && $offer->organisation->city) ? ', ' : '' }}
                                                    {{ $offer->organisation->city ?? '' }}{{ ($offer->organisation->city && $offer->organisation->state) ? ', ' : '' }}
                                                    {{ $offer->organisation->state ?? '' }}{{ ($offer->organisation->state && $offer->organisation->postcode) ? ' ' : '' }}
                                                    {{ $offer->organisation->postcode ?? '' }}
                                                </li>
                                                @else
                                                <li>No business address available.</li>
                                                @endif
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="col-12 mb-3">
                                        <div class="detail-wrapper">
                                            <div class="heading-tag text-uppercase"><i class="bi bi-chat-right-fill"></i> Terms & Conditions</div>
                                            <div>{!! $offer->terms_conditions ?? '' !!}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="social-icon">
                                    <ul class="list-unstyled d-flex align-items-center justify-content-center gap-2">
                                        @if($offer->organisation->facebook ?? null)
                                        <li><a href="{{ $offer->organisation->facebook }}" target="_blank"><i class="bi bi-facebook"></i></a></li>
                                        @endif
                                        @if($offer->organisation->instagram ?? null)
                                        <li><a href="{{ $offer->organisation->instagram }}" target="_blank"><i class="bi bi-instagram"></i></a></li>
                                        @endif
                                        @if($offer->organisation->twitter ?? null)
                                        <li><a href="{{ $offer->organisation->twitter }}" target="_blank"><i class="bi bi-twitter"></i></a></li>
                                        @endif
                                        @if($offer->organisation->tiktok ?? null)
                                        <li><a href="{{ $offer->organisation->tiktok }}" target="_blank"><i class="bi bi-tiktok"></i></a></li>
                                        @endif
                                        @if($offer->organisation->youtube ?? null)
                                        <li><a href="{{ $offer->organisation->youtube }}" target="_blank"><i class="bi bi-youtube"></i></a></li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-muted">No offers are currently available from this organization.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

     @else

     <div class="modal-body p-0">
         <div class="text-center py-4">
            <p class="text-danger fw-bold">
                ⚠️ You currently don’t have an active subscription.  
                To continue enjoying all premium features without interruption, please purchase a subscription plan.  
                Choose the package that best fits your needs and unlock full access today!
            </p>
            <a href="{{ route('membership.dashboard') }}" class="btn btn-with-icon">
                View Subscription Plans
            </a>
        </div>
    </div>
    @endif



</div>

</div>
</div>
</section>


@include('consumer.blocks.social-feeds')


@endsection
