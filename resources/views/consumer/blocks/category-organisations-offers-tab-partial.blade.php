@php
    $organizations = $category->offers->groupBy('organisation_id');
    $totalOrganizationsInCurrentCategory = $organizations->count();
    $organizationsToShowInitially = 15;

    $sliderCount = 0;
    if ($totalOrganizationsInCurrentCategory >= 8 && $totalOrganizationsInCurrentCategory <= 9) {
        $sliderCount = 1;
    } elseif ($totalOrganizationsInCurrentCategory >= 14 && $totalOrganizationsInCurrentCategory <= 20) {
        $sliderCount = 2;
    } elseif ($totalOrganizationsInCurrentCategory >= 21) {
        $sliderCount = 3;
    }

    $organizationChunks = $sliderCount > 0
        ? $organizations->chunk(ceil($totalOrganizationsInCurrentCategory / $sliderCount))
        : collect([$organizations]);
@endphp

<div class="tab-content-inner">
    <h2 class="d-flex align-items-center gap-2">
        <img src="{{ asset('images/dumble-icon.svg') }}" class="head-icon" alt="Category Icon">
        {{ strtoupper($category->name) }}
    </h2>

    @if ($organizations->isNotEmpty())

        {{-- Desktop View --}}
        <div class="row offers-container desktop-view d-lg-grid d-none">
            @php $loopcheck = 0; @endphp
            @foreach ($organizations as $orgId => $offers)
                @php
                    $organization = optional($offers->first())->organisation;
                    if (!$organization) continue;

                    $offerIds        = $offers->pluck('id')->implode(',');
                    $firstOffer      = $offers->first();
                    $firstAttachment = optional($firstOffer)->attachments->first();
                    $offerPercentage = optional($firstOffer)->percentage_off;

                    $organizationLogo = asset('images/ubx.png');
                    if (!empty($organization->image) && Storage::disk('public')->exists($organization->image)) {
                        $organizationLogo = Storage::url($organization->image);
                    }

                    $imageUrl      = !empty($firstAttachment?->file_image) ? Storage::url($firstAttachment->file_image) : $organizationLogo;
                    $isHighlighted = !empty($business_id) && $organization->id == $business_id;
                @endphp

                <div class="{{ $loopcheck }} col offer-item-col {{ $loopcheck >= $organizationsToShowInitially ? 'd-none' : '' }}">
                    <div class="offer-col {{ $offerclass ?? '' }} {{ $isHighlighted ? 'highlighted' : '' }}" data-id="{{ $organization->id }}" data-offer-ids="{{ $offerIds }}">
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
                            <div class="ubx-bottom">{{ $organization->title }}</div>
                        </div>
                    </div>
                </div>
                @php $loopcheck++; @endphp
            @endforeach
        </div>

        {{-- Mobile Slider --}}
        <div class="mobile-slider d-lg-none">
            @foreach($organizationChunks as $chunkIndex => $chunk)
                <div class="offer-slide offer-slide-{{ $category->id }}-{{ $chunkIndex }}">
                    @foreach($chunk as $orgId => $offers)
                        @php
                            $organization = optional($offers->first())->organisation;
                            if (!$organization) continue;

                            $offerIds        = $offers->pluck('id')->implode(',');
                            $firstOffer      = $offers->first();
                            $firstAttachment = optional($firstOffer)->attachments->first();
                            $offerPercentage = optional($firstOffer)->percentage_off;

                            $organizationLogo = asset('images/ubx.png');
                            if (!empty($organization->image) && Storage::disk('public')->exists($organization->image)) {
                                $organizationLogo = Storage::url($organization->image);
                            }

                            $imageUrl      = !empty($firstAttachment?->file_image) ? Storage::url($firstAttachment->file_image) : $organizationLogo;
                            $isHighlighted = !empty($business_id) && $organization->id == $business_id;
                        @endphp

                        <div class="offer-item {{ $offerclass ?? '' }} {{ $isHighlighted ? 'highlighted' : '' }}" data-id="{{ $organization->id }}" data-offer-ids="{{ $offerIds }}">
                            @if ($offerPercentage > 0)
                                <div class="offer-percent">{{ round($offerPercentage) }}% Off</div>
                            @endif
                            <div class="offer-col-inner">
                                <div class="ubx-col">
                                    <img src="{{ $organizationLogo }}" alt="{{ $organization->title }} Logo">
                                    <span>{{ $organization->title }}</span>
                                </div>
                                <div class="offer-col-img">
                                    <img src="{{ $imageUrl }}" class="cat-offer-img lazy" alt="Offer Image"
                                         onerror="this.onerror=null;this.src='{{ asset('images/ubx.png') }}'">
                                </div>
                                <div class="ubx-bottom">{{ $organization->title }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>

        @if ($totalOrganizationsInCurrentCategory > $organizationsToShowInitially)
            <div class="action-btn text-center mt-4 d-lg-inline-block d-none">
                <a class="load-all-offers-btn signup" data-target-tab="#nav-{{ $category->id }}">View More</a>
            </div>
        @endif

    @else
        <div class="no-offers-message-container">
            <div class="col-12">
                <p>No organizations available for this category yet.</p>
            </div>
        </div>
    @endif
</div>