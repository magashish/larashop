@if ($organizations->isNotEmpty())
    @foreach ($organizations as $index => $organization)
        @php
            $offers = $organization->offers; 
            $firstOffer = $offers->first();
            $firstAttachment = optional($firstOffer)->attachments->first();
            $offerPercentage = optional($firstOffer)->percentage_off;

            // Organisation Logo
            $organizationLogo = asset('images/ubx.png');
            if ($organization->image && Storage::disk('public')->exists($organization->image)) {
                $organizationLogo = Storage::url($organization->image);
            }

            // Offer Image
            if (!empty($firstAttachment?->file_image)) {
                $imageUrl = Storage::url($firstAttachment->file_image);
            } elseif (!empty($organizationLogo)) {
                $imageUrl = $organizationLogo;
            } else {
                $imageUrl = asset('images/ubx.png');
            }
            $offerIds = $offers->pluck('id')->implode(',');
        @endphp
        <div class="{{ $index }} col offer-item-col {{ $index >= $organizationsToShowInitially ? 'd-none' : '' }}">
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
                    <div class="ubx-bottom">{{ $organization->title }}</div>
                </div>
            </div>
        </div>
    @endforeach
@endif
