@if ($organizations->isNotEmpty())
    @foreach ($organizations as $organization)
        @php
            // The offers relationship is constrained by the Controller, 
            // but an organization may be loaded even if its offers are empty (e.g., if matched by organization title).
            $offers = $organization->offers; 
            
            // Use ->first() on the Collection. If the collection is empty, $firstOffer will be null.
            $firstOffer = $offers->first(); 
            
            // **SAFETY FIXES (PHP 8.0+ Nullsafe Operator ?->)**
            // Safely access attachments and then the first one. Returns null if either is missing.
            $firstAttachment = $firstOffer?->attachments?->first(); 

            // Safely get the percentage_off.
            $offerPercentage = $firstOffer?->percentage_off; 

            // Organization Logo Logic
            $organizationLogo = asset('images/ubx.png');
            if ($organization->image && Storage::disk('public')->exists($organization->image)) {
                $organizationLogo = Storage::url($organization->image);
            }

            // Image URL Logic: Use offer attachment image if available, otherwise use organization logo.
            $imageUrl = $firstAttachment?->file_image ? Storage::url($firstAttachment->file_image) : $organizationLogo;
            
            // Get comma-separated list of offer IDs (safe even if $offers is empty)
            $offerIds = $offers->pluck('id')->implode(',');
            //$offerIds = $organization->pluck('id')->implode(',');
        @endphp

        {{-- Only render the item if we successfully found at least one offer to display (or if you want to display the organization even without an offer) --}}
        {{-- You may consider adding a check here: @if ($firstOffer) --}}

        <div class="col-md-4 offer-item-col">
            <div class="offer-col {{ $offerclass }}" data-id="{{ $organization->id }}" data-offer-ids="{{ $offerIds }}">
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
                    {{-- Safely display the first offer's title, falling back to organization title if no offer exists --}}
                    <div class="ubx-bottom">{{ $firstOffer?->title ?? $organization->title }}</div> 
                </div>
            </div>
        </div>
        
        {{-- @endif --}}

    @endforeach
@else
    <div class="col-12 text-center">
        <p>No organizations found.</p>
    </div>
@endif