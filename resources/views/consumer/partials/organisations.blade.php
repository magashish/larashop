@forelse ($organisations as $organisationIndex => $organisation)
@php
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
    }
}
@endphp
<div class="col-lg-3 col-md-6 offer-detail" data-id="{{ $organisation->id }}">
    <div class="offer-col">
        @if ($firstOffer->percentage_off && $firstOffer->percentage_off > 0)
        <div class="offer-percent">{{ round($firstOffer->percentage_off) }}% Off</div>
        @endif
        <div class="offer-col-inner">
            <div class="ubx-col">
                @php
                $organisationLogo = asset('images/ubx.png');
                if ($organisation->image) {
                    if (Storage::disk('public')->exists($organisation->image)) {
                        $organisationLogo = Storage::url($organisation->image);
                    }
                }
                @endphp
                <img src="{{ $organisationLogo }}" alt="UBX Logo">
                <span>{{ $categoryTitle }}</span>

            </div>
            <div class="offer-col-img">
                @if ($firstAttachment)
                <img src="{{ Storage::url($firstAttachment->file_image) }}" class="cat-offer-img" alt="Offer Image">
                @else
                <img src="{{ asset('images/ubx.png') }}" class="cat-offer-img" alt="Default Offer Image">
                @endif
            </div>
            <div class="ubx-bottom">{{ $organisation->title }}</div>
        </div>
    </div>
</div>
@empty
<div class="col-12"><p class="text-center">No offers found.</p></div>
@endforelse