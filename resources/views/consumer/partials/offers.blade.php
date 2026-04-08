@forelse ($offers as $offerIndex => $offer)
<div class="col-lg-4 col-md-6 offer-detail" data-id="{{ $offer->id }}">
    <div class="offer-col">
        @if ($offer->percentage_off && $offer->percentage_off > 0)
        <div class="offer-percent">{{ round($offer->percentage_off) }}% Off</div>
        @endif
        <div class="offer-col-inner">
            <div class="ubx-col">
             @php
             $organisationLogo = asset('images/ubx.png'); 
             if ($offer->organisation && $offer->organisation->image) {
                if (Storage::disk('public')->exists($offer->organisation->image)) {
                    $organisationLogo = Storage::url($offer->organisation->image);
                }
            }
            @endphp
            <img src="{{ $organisationLogo }}" alt="UBX Logo">
            <span>{{ $offer->organisation->title ?? 'N/A' }}</span> 
        </div>
        <div class="offer-col-img">
            @php
            $firstAttachment = $offer->attachments->first();
            @endphp
            @if ($firstAttachment)
            <img src="{{ Storage::url($firstAttachment->file_image) }}" class="cat-offer-img" alt="Offer Image">
            @else
            <img src="{{ asset('images/ubx.png') }}" class="cat-offer-img" alt="Default Offer Image">
            @endif
        </div>

        <div class="ubx-bottom">{{ $offer->title }}</div>
    </div>
</div>
</div>
@empty
<div class="col-12"><p class="text-center">No offers found.</p></div>
@endforelse
