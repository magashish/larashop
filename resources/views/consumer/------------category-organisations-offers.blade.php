<div class="categories-tabs-sec mb-0">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-12 tabs-content-col">
                <nav class="categories-tabs">
                    <div class="scroll-wrapper">
                        <button class="scroll-btn left d-lg-flex d-none" aria-label="Scroll left">‹</button>
                        <div class="nav nav-tabs" id="nav-tab" role="tablist">
                            @php
                                $hasActiveCategory = false; 
                                $searchTerm = request('search');
                            @endphp

                            @foreach ($categories->sortBy('position') as $category)
                                @php
                                    $organizations = $category->offers->groupBy('organisation_id');
                                    $totalOrganizations = $organizations->count();
                                    $isActive = false;
                                    if (empty($searchTerm)) {
                                        if ($loop->first) {
                                            $isActive = true;
                                            $hasActiveCategory = true;
                                        }
                                    } else {
                                        if (!$hasActiveCategory && $totalOrganizations > 0) {
                                            $isActive = true;
                                            $hasActiveCategory = true;
                                        }
                                    }
                                @endphp
                                
                                <button 
                                    class="nav-link {{ $isActive ? 'active' : '' }} {{ !empty($searchTerm) && $totalOrganizations > 0 ? 'has-org' : '' }}"
                                    id="nav-{{ $category->id }}-tab"
                                    data-bs-toggle="tab"
                                    data-bs-target="#nav-{{ $category->id }}"
                                    type="button" 
                                    role="tab"
                                    aria-controls="nav-{{ $category->id }}"
                                    aria-selected="{{ $isActive ? 'true' : 'false' }}"
                                >
                                    @if ($category->icon)
                                        <img src="{{ Storage::url($category->icon) }}" alt="{{ $category->name }} icon">
                                    @endif
                                    <p>{{ strtoupper($category->name) }}</p>
                                </button>
                            @endforeach
                        </div>
                        <button class="scroll-btn right d-lg-flex d-none" aria-label="Scroll right">›</button>
                    </div>
                </nav>

                <div class="tab-content" id="nav-tabContent">
                    @php
                        $hasActiveCategory = false; 
                    @endphp

                    @forelse ($categories->sortBy('position') as $category)
                        @php
                            $organizations = $category->offers->groupBy('organisation_id');
                            $totalOrganizations = $organizations->count();
                            $organizationsToShowInitially = 5;
                            $isActive = false;
                            
                            if (empty($searchTerm)) {
                                if ($loop->first) {
                                    $isActive = true;
                                    $hasActiveCategory = true;
                                }
                            } else {
                                if (!$hasActiveCategory && $totalOrganizations > 0) {
                                    $isActive = true;
                                    $hasActiveCategory = true;
                                }
                            }

                            // Mobile slider logic (no change, as it's complex business logic)
                            $sliderCount = 0;
                            if ($totalOrganizations >= 8 && $totalOrganizations <= 9) {
                                $sliderCount = 1;
                            } elseif ($totalOrganizations >= 14 && $totalOrganizations <= 20) {
                                $sliderCount = 2;
                            } elseif ($totalOrganizations >= 21) {
                                $sliderCount = 3;
                            }

                            $organizationChunks = $sliderCount > 0
                                ? $organizations->chunk(ceil($totalOrganizations / $sliderCount))
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
                                    <img src="{{ asset('images/dumble-icon.svg') }}" class="head-icon" alt="Icon">
                                    {{ strtoupper($category->name) }}
                                </h2>

                                @if ($organizations->isNotEmpty())
                                    <div class="row offers-container desktop-view d-lg-grid d-none">
                                        @foreach ($organizations as $orgId => $offers)
                                            @php
                                                $offerIds = $offers->pluck('id')->implode(',');
                                                $organization = $offers->first()->organisation;
                                                $firstOffer = $offers->first();
                                                $firstAttachment = optional($firstOffer)->attachments->first();
                                                $offerPercentage = optional($firstOffer)->percentage_off;

                                                $organizationLogo = asset('images/ubx.png');
                                                if ($organization->image && Storage::disk('public')->exists($organization->image)) {
                                                    $organizationLogo = Storage::url($organization->image);
                                                }
                                                
                                                
                                                $imageUrl = $organizationLogo;
                                                if (!empty($firstAttachment?->file_image)) {
                                                    $imageUrl = Storage::url($firstAttachment->file_image);
                                                }

                                                $isHidden = $loop->iteration > $organizationsToShowInitially;
                                            @endphp
                                            
                                            <div class="col offer-item-col {{ $isHidden ? 'd-none' : '' }}">
                                                <div class="offer-col offer-detail" data-id="{{ $organization->id }}" data-offer-ids="{{ $offerIds }}">
                                                    @if ($offerPercentage > 0)
                                                        <div class="offer-percent">{{ round($offerPercentage) }}% Off</div>
                                                    @endif
                                                    <div class="offer-col-inner">
                                                        <div class="ubx-col">
                                                            <img src="{{ $organizationLogo }}" alt="{{ $organization->title }} Logo">
                                                            <span>{{ $organization->title }}</span>
                                                        </div>
                                                        <div class="offer-col-img">
                                                            <img src="{{ $imageUrl }}" class="cat-offer-img" alt="Offer Image"
                                                            onerror="this.onerror=null;this.src='{{ asset('images/ubx.png') }}'">
                                                        </div>
                                                        <div class="ubx-bottom">{{ $organization->title }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    {{-- Mobile Slider --}}
                                    <div class="mobile-slider d-lg-none">
                                        @foreach($organizationChunks as $chunkIndex => $chunk)
                                            <div class="offer-slide offer-slide-{{ $category->id }}-{{ $chunkIndex }}">
                                                @foreach($chunk as $orgId => $offers)
                                                    @php
                                                        $offerIds = $offers->pluck('id')->implode(',');
                                                        $organization = $offers->first()->organisation;
                                                        $firstOffer = $offers->first();
                                                        $firstAttachment = optional($firstOffer)->attachments->first();
                                                        $offerPercentage = optional($firstOffer)->percentage_off;
                                                        
                                                        $organizationLogo = asset('images/ubx.png');
                                                        if ($organization->image && Storage::disk('public')->exists($organization->image)) {
                                                            $organizationLogo = Storage::url($organization->image);
                                                        }

                                                        
                                                        $imageUrl = $organizationLogo;
                                                        if (!empty($firstAttachment?->file_image)) {
                                                            $imageUrl = Storage::url($firstAttachment->file_image);
                                                        }
                                                    @endphp
                                                    <div class="offer-item offer-detail" data-id="{{ $organization->id }}" data-offer-ids="{{ $offerIds }}">
                                                        @if ($offerPercentage > 0)
                                                            <div class="offer-percent">{{ round($offerPercentage) }}% Off</div>
                                                        @endif
                                                        <div class="offer-col-inner">
                                                            <div class="ubx-col">
                                                                <img src="{{ $organizationLogo }}" alt="{{ $organization->title }} Logo">
                                                                <span>{{ $organization->title }}</span>
                                                            </div>
                                                            <div class="offer-col-img">
                                                                <img src="{{ $imageUrl }}" class="cat-offer-img" alt="Offer Image"
                                                                onerror="this.onerror=null;this.src='{{ asset('images/ubx.png') }}'">
                                                            </div>
                                                            <div class="ubx-bottom">{{ $organization->title }}</div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endforeach
                                    </div>

                                    @if ($totalOrganizations > $organizationsToShowInitially)
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