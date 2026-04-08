@php
    $searchTerm = request('search');
    $business_id = request('busness'); // UUID string
    $hasActiveCategory = false; // ensures only one active tab
@endphp

<!-- Categories & Offers Section -->
<div class="categories-tabs-sec mb-0">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-12 tabs-content-col">
                <nav class="categories-tabs">
                    <div class="scroll-wrapper">
                        <button class="scroll-btn left" aria-label="Scroll left">‹</button>
                        <div class="nav nav-tabs" id="nav-tab" role="tablist">

                            @foreach ($categories->sortBy('position') as $category)
                                @php
                                    $organizations = $category->offers->groupBy('organisation_id');
                                    $organizationIdsArray = $organizations->keys()->toArray();
                                    $totalOrganizationsInCurrentCategory = $organizations->count();

                                    // Active tab logic
                                    $isActive = false;
                                    if (!empty($business_id) && in_array($business_id, $organizationIdsArray) && !$hasActiveCategory) {
                                        $isActive = true;
                                        $hasActiveCategory = true;
                                    } elseif (empty($business_id) && empty($searchTerm) && !$hasActiveCategory && $loop->first) {
                                        $isActive = true;
                                        $hasActiveCategory = true;
                                    } elseif (!empty($searchTerm) && !$hasActiveCategory && $totalOrganizationsInCurrentCategory > 0) {
                                        $isActive = true;
                                        $hasActiveCategory = true;
                                    }

                                    $hasOrgClass = (!empty($searchTerm) && $totalOrganizationsInCurrentCategory > 0) ||
                                                   (!empty($business_id) && in_array($business_id, $organizationIdsArray));
                                @endphp

                                <button 
                                    class="nav-link {{ $isActive ? 'active' : '' }} {{ $hasOrgClass ? 'has-org' : '' }}"
                                    id="nav-{{ $category->id }}-tab"
                                    data-bs-toggle="tab"
                                    data-bs-target="#nav-{{ $category->id }}"
                                    type="button"
                                    role="tab"
                                    aria-controls="nav-{{ $category->id }}"
                                    aria-selected="{{ $isActive ? 'true' : 'false' }}"
                                >
                                    @if(!empty($category->icon))
                                        <img src="{{ Str::startsWith($category->icon, ['http://','https://']) ? $category->icon : Storage::url($category->icon) }}" alt="{{ $category->name }} icon">
                                    @endif
                                    <p>{{ strtoupper($category->name) }}</p>
                                </button>
                            @endforeach
                        </div>
                        <button class="scroll-btn right" aria-label="Scroll right">›</button>
                    </div>
                </nav>

                <div class="tab-content" id="nav-tabContent">
                    @php $hasActiveCategory = false; @endphp

                    @forelse ($categories->sortBy('position') as $category)
                        @php
                            $organizations = $category->offers->groupBy('organisation_id');
                            $organizationIdsArray = $organizations->keys()->toArray();
                            $totalOrganizationsInCurrentCategory = $organizations->count();
                            $organizationsToShowInitially = 15;

                            // Active tab logic
                            $isActive = false;
                            if (!empty($business_id) && in_array($business_id, $organizationIdsArray) && !$hasActiveCategory) {
                                $isActive = true;
                                $hasActiveCategory = true;
                            } elseif (empty($business_id) && empty($searchTerm) && !$hasActiveCategory && $loop->first) {
                                $isActive = true;
                                $hasActiveCategory = true;
                            } elseif (!empty($searchTerm) && !$hasActiveCategory && $totalOrganizationsInCurrentCategory > 0) {
                                $isActive = true;
                                $hasActiveCategory = true;
                            }

                            // Mobile slider calculation
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

                        <div 
                            class="tab-pane fade {{ $isActive ? 'show active' : '' }}" 
                            id="nav-{{ $category->id }}" 
                            role="tabpanel"
                            aria-labelledby="nav-{{ $category->id }}-tab" 
                            tabindex="0"
                        >
                            <div class="tab-content-inner">
                                <h2 class="d-flex align-items-center gap-2">
                                    <img src="{{ asset('images/dumble-icon.svg') }}" class="head-icon" alt="Category Icon">
                                    {{ strtoupper($category->name) }}
                                </h2>

                                {{-- Desktop View --}}
                                @if ($organizations->isNotEmpty())
                                    <div class="row offers-container desktop-view d-lg-grid d-none">
                                        @php $loopcheck = 0; @endphp
                                        @foreach ($organizations as $orgId => $offers)
                                            @php
                                                $offerIds = $offers->pluck('id')->implode(',');
                                                // $organization = $offers->first()->organisation;
                                                $organization = optional($offers->first())->organisation;
                                                if(!$organization){
                                                    continue;
                                                }

                                                $firstOffer = $offers->first();
                                                $firstAttachment = optional($firstOffer)->attachments->first();
                                                $offerPercentage = optional($firstOffer)->percentage_off;

                                                // Organization logo fallback
                                                $organizationLogo = asset('images/ubx.png');
                                                if (!empty($organization->image) && Storage::disk('public')->exists($organization->image)) {
                                                    $organizationLogo = Storage::url($organization->image);
                                                }

                                                // Offer image fallback
                                                $imageUrl = !empty($firstAttachment?->file_image) ? Storage::url($firstAttachment->file_image) : $organizationLogo;

                                                // Highlighted if business_id matches
                                                $isHighlighted = !empty($business_id) && $organization->id == $business_id;
                                            @endphp

                                            <div class="{{ $loopcheck }} col offer-item-col {{ $loopcheck >= $organizationsToShowInitially ? 'd-none' : '' }}">
                                                <div class="offer-col {{ $offerclass }} {{ $isHighlighted ? 'highlighted' : '' }}" data-id="{{ $organization->id }}" data-offer-ids="{{ $offerIds }}">
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
                                                        $offerIds = $offers->pluck('id')->implode(',');
                                                        $organization = optional($offers->first())->organisation;
                                                        if(!$organization){
                                                            continue;
                                                        }
                                                        $firstOffer = $offers->first();
                                                        $firstAttachment = optional($firstOffer)->attachments->first();
                                                        $offerPercentage = optional($firstOffer)->percentage_off;

                                                        $organizationLogo = asset('images/ubx.png');
                                                        if (!empty($organization->image) && Storage::disk('public')->exists($organization->image)) {
                                                            $organizationLogo = Storage::url($organization->image);
                                                        }

                                                        $imageUrl = !empty($firstAttachment?->file_image) ? Storage::url($firstAttachment->file_image) : $organizationLogo;
                                                        $isHighlighted = !empty($business_id) && $organization->id == $business_id;
                                                    @endphp

                                                    <div class="offer-item {{ $offerclass }} {{ $isHighlighted ? 'highlighted' : '' }}" data-id="{{ $organization->id }}" data-offer-ids="{{ $offerIds }}">
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
                        </div>
                    @empty
                        <div class="row">
                            <div class="col-lg-12 text-center my-5">
                                <p>No offers found that match your search.</p>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
