<div class="modal offerModal fade" id="offerModal" tabindex="-1" aria-labelledby="offerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-0">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

            @if($hasActiveSubscription)

            <div class="modal-body p-0">

                @php
                $modalBanner = 'images/offer-img.png'; 
                $organisationLogo = asset('images/ubx.png'); 

                $liveOffers = $organisation->offers->filter(function ($offer) {
                    $today = \Carbon\Carbon::now();
                    return $offer->start_date <= $today && $offer->end_date >= $today;
                });

                if ($organisation->image) {
                    if (Storage::disk('public')->exists($organisation->image)) {
                        $organisationLogo = Storage::url($organisation->image);
                        $modalBanner = $organisationLogo; 
                    }
                }
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
             @endphp

             <div class="header-wrapper position-relative">
                <img src="{{ $modalBanner }}" class="modal-banner img-fluid w-100" alt="Offer Banner">
                <img src="{{ $organisationLogo }}" class="modal-logo img-fluid" alt="Organisation Logo">
            </div>
            <div class="offer-wrapper">
                <div class="offer-inner">
                 <div class="accordion" id="accordionExample">
                    @forelse ($organisation->offers as $key => $offer)
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading-{{ $offer->id }}">
                            <button class="accordion-button {{ $key === 0 ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-{{ $offer->id }}" aria-expanded="{{ $key === 0 ? 'true' : 'false' }}" aria-controls="collapse-{{ $offer->id }}">
                                {{ $offer->title }} 
                                {{-- @if ($offer->custom_redemption_code) - {{ $offer->custom_redemption_code }}  @endif    --}}
                            </button>
                        </h2>
                        <div id="collapse-{{ $offer->id }}" class="accordion-collapse collapse {{ $key === 0 ? 'show' : '' }}" aria-labelledby="heading-{{ $offer->id }}" data-bs-parent="#accordionExample">
                            <div class="accordion-body">


                                @if ($offer->offer_ongoing_subscription == 'yes' && !activeSubscription())
                                <div class="alert alert-warning d-flex align-items-start p-3 shadow-sm rounded-3" role="alert" style="background: #fff3cd; border-left: 5px solid #ffc107;">
                                    <div class="me-3">
                                        <i class="bi bi-exclamation-triangle-fill fs-3 text-warning"></i>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold mb-1">Subscription Required</h5>
                                        <p class="mb-0">
                                            This offer is available only with an active subscription.
                                            Please <a href="{{ route('membership.dashboard') }}" target="_blank" class="fw-semibold text-decoration-underline">subscribe</a> to access or redeem this offer.
                                        </p>
                                    </div>
                                </div>

                                @else
                                @if(!empty($offer->custom_redemption_url))
                                <div class="text-center custom-redemption-code-detail">
                                    <!-- <div class="mt-3 mb-4">
                                        <a href="{{ $offer->custom_redemption_url }}" target="_blank" rel="noopener"  class="heading-tag text-uppercase redeem_offer_site" data-id="{{ $offer->id }}">
                                            Redeem Offer Site
                                        </a>
                                    </div> -->
                                    @if ($offer->custom_redemption_code)
                                    <div class="custom-redemption-code">
                                        <div class="heading-tag text-uppercase">
                                            <a href="{{ route('shop')}}"><i class="bi bi-tags"></i></a> HOW TO REDEEM OFFER
                                        </div>
                                        <div class="custom-redemption-content mt-2">
                                            <p>Use this code at checkout, or mention your Xhale membership and offer code when contacting the business to redeem.</p>
                                        </div>
                                        <div class="discount-bar-outer d-flex align-items-center">
                                            <div class="discount-bar web-link">
                                                <a href="{{ $offer->custom_redemption_url }}" target="_blank" rel="noopener">
                                                    <span class="discount-code text-uppercase">Go to Website</span>
                                                    <button class="copy-btn">
                                                        <img src="{{ asset('/images/click-icon.png') }}">
                                                    </button>
                                                </a>
                                            </div>
                                            <div class="discount-bar">
                                                <span class="discount-code text-uppercase">{{ $offer->custom_redemption_code }}</span>
                                                <button class="copy-btn" onclick="copyCode(this)" title="Copy code">
                                                    <i class="bi bi-files"></i>
                                                    <span class="tooltip">Copied!</span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>                                    
                                    @endif
                                </div>
                                @endif

                                <div class="row">
                                    <div class="col-12">
                                        <div class="work-tabs-inner mb-3">
                                            <ul class="nav nav-tabs offerTabs" role="tablist">
                                                <li class="nav-item" role="presentation">
                                                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-pane1_{{ $offer->id }}" type="button" role="tab">
                                                        Offer
                                                    </button>
                                                </li>
                                                <li class="nav-item" role="presentation">
                                                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-pane2_{{ $offer->id }}" type="button" role="tab">
                                                        Category
                                                    </button>
                                                </li>
                                                <li class="nav-item" role="presentation">
                                                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-pane3_{{ $offer->id }}" type="button" role="tab">
                                                        Business Details
                                                    </button>
                                                </li>
                                                <li class="nav-item" role="presentation">
                                                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-pane4_{{ $offer->id }}" type="button" role="tab">
                                                        Terms & Conditions
                                                    </button>
                                                </li>
                                            </ul>

                                            <div class="tab-content">
                                                <div class="tab-pane fade show active" id="tab-pane1_{{ $offer->id }}" role="tabpanel">
                                                    <div class="work-tab-content-col">
                                                        <p class="mb-0">{!! $offer->description ?? '' !!}</p>
                                                    </div>
                                                </div>

                                                <div class="tab-pane fade" id="tab-pane2_{{ $offer->id }}" role="tabpanel">
                                                    <div class="work-tab-content-col">
                                                        @forelse ($offer->categories as $category)
                                                        <div class="category-box">{{ $category->name }}</div>
                                                        @empty
                                                        <div class="category-box">Uncategorized</div>
                                                        @endforelse
                                                    </div>
                                                </div>

                                                <div class="tab-pane fade" id="tab-pane3_{{ $offer->id }}" role="tabpanel">
                                                    <div class="work-tab-content-col">
                                                        <ul class="list-unstyled">

                                                            @php
                                                            $address = null;
                                                            $mapId = 'map-' . $offer->id;
                                                            $lat = $offer->latitude ?? 37.4221;
                                                            $lng = $offer->longitude ?? -122.0841;

                                                            if($offer->offer_address_type === 'physical_location') {
                                                                $address = $offer->full_address ?? 
                                                                (($offer->street_name ?? '') . ' ' . 
                                                                    ($offer->city ?? '') . ' ' . 
                                                                    ($offer->state ?? '') . ' ' . 
                                                                    ($offer->postcode ?? ''));
                                                            } elseif($offer->offer_address_type === 'use_business_address') {
                                                                if($offer->organisation->offer_address_type === 'physical_location') {
                                                                    $address = $offer->organisation->full_business_address ?? 
                                                                    (($offer->organisation->street_name ?? '') . ' ' . 
                                                                        ($offer->organisation->city ?? '') . ' ' . 
                                                                        ($offer->organisation->state ?? '') . ' ' . 
                                                                        ($offer->organisation->postcode ?? ''));
                                                                }
                                                            }

                                                            $address = trim($address);
                                                            @endphp

                                                            @if(!empty($address))
                                                            <div class="map-container" style="height: 300px; width: 100%; margin-top: 15px;">
                                                                <iframe 
                                                                width="100%" 
                                                                height="100%" 
                                                                frameborder="0" 
                                                                style="border:0"
                                                                src="https://www.google.com/maps?q={{ urlencode($address) }}&output=embed" 
                                                                allowfullscreen>
                                                            </iframe>
                                                        </div>

                                                     
    
                                                        @endif


                                                            @if($offer->organisation->website ?? null)
                                                            <li><a href="{{ $offer->organisation->website }}" target="_blank">{{ $offer->organisation->website }}</a></li>
                                                            @endif
                                                            @if($offer->organisation->mobile ?? null)
                                                            <li><a href="tel:{{ $offer->organisation->mobile }}">{{ $offer->organisation->mobile }}</a></li>
                                                            @endif

                                                            {{-- @if($offer->organisation->full_address ?? null)
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
                                                            @endif --}}
                                                            
                                                        </ul>
                                                    </div>
                                                </div>

                                                <div class="tab-pane fade" id="tab-pane4_{{ $offer->id }}" role="tabpanel">
                                                    <div class="work-tab-content-col">
                                                        <div>{!! $offer->terms_conditions ?? '' !!}</div>
                                                    </div>
                                                </div>
                                            </div>
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

                                @endif





                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-muted">No offers are currently available from this organization.</div>
                    @endforelse
                </div>
            </div>
            <button type="button" class="btn btn-secondary close-btn text-uppercase w-100 mt-2" data-bs-dismiss="modal">Back</button>
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