<div class="modal offerModal fade" id="offerModal" tabindex="-1" aria-labelledby="offerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-0">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingOne">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                            {{ $offer->organisation->title ?? 'UBX Location' }} - {{ $offer->title }}
                        </button>
                    </h2>
                    <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                        <div class="accordion-body">


                          @if ($offer->custom_redemption_code)
                          <div class="text-center">
                            <div class="heading-tag text-uppercase">
                                <i class="bi bi-tags"></i> Discount Code
                            </div>
                        </div>
                        <div class="discount-bar mt-2">
                            <span class="discount-code text-uppercase">{{ $offer->custom_redemption_code }}</span>
                            <button class="copy-btn" onclick="copyCode(this)" title="Copy code">
                                <i class="bi bi-files"></i>
                                <span class="tooltip">Copied!</span>
                            </button>
                        </div>
                        @endif

                        <div class="row">
                            <div class="col-12 mb-3">
                                <div class="detail-wrapper">
                                    <div class="heading-tag text-uppercase"><i class="bi bi-chat-right-fill"></i> Offer</div>
                                    <p class="mb-0"> {!! $offer->description ?? '' !!}</p>
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
                                    <div>
                                     {!! $offer->terms_conditions ?? '' !!}

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
                </div>
            </div>
        </div>
    </div>
</div>
<button type="button" class="btn btn-secondary close-btn text-uppercase w-100 mt-2" data-bs-dismiss="modal">Back</button>
</div>
</div>
</div>
</div>
</div>