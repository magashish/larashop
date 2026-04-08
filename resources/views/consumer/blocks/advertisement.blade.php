<section class="advertisement-banner-sec pt-0">
    <div class="paid-banner-slider">
        @foreach (is_paid_advertisement_offers() as $offer)
        <div class="slider-item">
            @if ($offer->banner_image)
            <div class="item-bg position-relative" style="background-image: url({{ Storage::url($offer->banner_image) }});">
                @else
                <div class="item-bg position-relative" style="background-image: url({{ asset('images/Screenshot-2025-06-26-120836.png') }});">
                    @endif
                    <div class="container">
                        <div class="bg-title small-title">
                            <div class="yellow-bg text-uppercase" style="padding: 18px 65px 5px;">XHale</div>
                            <div class="black-bg text-uppercase" style="padding: 50px 100px 44px">Offers</div>
                        </div>
                        <div class="inner-content">
                            <div class="item-content pt-5">
                                <h3 class="my-2">
                                    @if ($offer->organisation)
                                    {{ $offer->organisation->title }}
                                    @else
                                    Organization Name
                                    @endif
                                </h3>
                                <p> {{ html_entity_decode($offer->title) }}</p>
                                @php
                                $exploreUrl = auth()->check()
                                ? route('discounts') . '?busness=' . $offer->organisation->id
                                : route('login');
                                @endphp

                                <a href="{{ $exploreUrl }}" 
                                class="btn btn-with-icon d-flex align-items-center justify-content-between">
                                EXPLORE NOW
                                <span>
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                                        <path d="M438.6 278.6c12.5-12.5 12.5-32.8 
                                        0-45.3l-160-160c-12.5-12.5-32.8-12.5-45.3 
                                        0s-12.5 32.8 0 45.3L338.8 224 32 
                                        224c-17.7 0-32 14.3-32 
                                        32s14.3 32 32 32l306.7 
                                        0L233.4 393.4c-12.5 
                                        12.5-12.5 32.8 
                                        0 45.3s32.8 12.5 
                                        45.3 0l160-160z"/>
                                    </svg>
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
            </div>
        </div>
        @endforeach
    </div>
</section>